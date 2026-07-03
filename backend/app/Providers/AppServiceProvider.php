<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Share session data with all views
        View::composer('*', function ($view) {
            $view->with([
                'logged_id'   => Session::get('logged_id'),
                'logged_name' => Session::get('logged_name'),
                'logged_in'   => Session::get('logged_in'),
                'logged_role' => Session::get('logged_role'),
            ]);
        });

        // Define Gate::before to intercept all checks
        Gate::before(function (?User $user, $ability) {
            if ($user && $user->isVendor()) {
                // If they are not approved yet, they can ONLY access the dashboard
                if ($user->profile_status !== 'Approved') {
                    if ($ability === 'dashboard') {
                        return true;
                    }
                    return false;
                }
            }

            // Fallback to dynamic, role-assigned permissions from the session/database
            $permissions = Session::get('permission_titles', []);
            if (in_array($ability, $permissions)) {
                return true;
            }
            return null; // continue to other gate definitions if any
        });
    }
}

?>