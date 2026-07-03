<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class VendorAccess
{
    /**
     * Routes that are always accessible to any authenticated vendor
     * (no permission check needed).
     */
    protected $alwaysAllowed = [
        'dashboard',
        'vendor.verify',
        'vendor.verify.submit',
        'logout',
        'vendor.wallet',
        'vendor.settlement.request',
    ];

    /**
     * Build a dynamic route-prefix → ability map from the permissions table.
     * Each permission row has a comma-separated "route_prefix" column, e.g.:
     *   manage_orders | cabOrders
     *   access        | access,users,vendors
     *
     * Returns: ['cabOrders' => 'manage_orders', 'users' => 'access', ...]
     */
    protected function buildRouteMap(): array
    {
        // Cache for 60 seconds so we don't hit the DB on every request
        return Cache::remember('vendor_route_ability_map', 60, function () {
            $map = [];
            $rows = DB::table('permissions')
                ->whereNotNull('route_prefix')
                ->where('route_prefix', '!=', '')
                ->get(['title', 'route_prefix']);

            foreach ($rows as $row) {
                // route_prefix can be comma-separated: "access,users,vendors"
                foreach (explode(',', $row->route_prefix) as $prefix) {
                    $prefix = trim($prefix);
                    if ($prefix !== '') {
                        $map[$prefix] = $row->title;
                    }
                }
            }

            return $map;
        });
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();

        if (!$user->isVendor()) {
            return $next($request);
        }

        $routeName = $request->route()?->getName() ?? '';

        // ── Unapproved vendor: only allow dashboard / profile / logout ──
        if ($user->profile_status !== 'Approved') {
            if (!in_array($routeName, $this->alwaysAllowed)) {
                return redirect()->route('dashboard')
                    ->with('warning', 'Please complete your profile verification first.');
            }
            return $next($request);
        }

        // ── Approved vendor: check permission dynamically ──

        // Always-allowed routes skip permission checks
        if (in_array($routeName, $this->alwaysAllowed)) {
            return $next($request);
        }

        // Extract first segment: "cabOrders.create" → "cabOrders"
        $segment = explode('.', $routeName)[0];

        // Look up the ability from the DB-driven route map
        $routeMap  = $this->buildRouteMap();
        $ability   = $routeMap[$segment] ?? null;

        // If no mapping exists for this route, allow it through
        // (unknown/unregistered modules are not blocked)
        if ($ability === null) {
            return $next($request);
        }

        // Gate reads vendor's permissions dynamically from the session
        if (!Gate::allows($ability)) {
            abort(403, 'You do not have permission to access this module.');
        }

        return $next($request);
    }
}
