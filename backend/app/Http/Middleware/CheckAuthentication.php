<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use App\Models\Settings;
use App\Models\User;

class CheckAuthentication
{
    public function handle(Request $request, Closure $next)
    {
        $loggedIn = Session::get('logged_in');
        $loggedId = Session::get('logged_id');

        if (!$loggedIn || !$loggedId) {
            return redirect('/panel');
        }

        $user = User::find($loggedId);
        if (!$user) {
            Session::flush();
            return redirect('/panel');
        }


        if (!Session::has('permission_titles')) {
            $permissionTitles = DB::table('permission_role')
                ->join('permissions', 'permission_role.permission_id', '=', 'permissions.id')
                ->where('permission_role.role_id', $user->role_id)
                ->pluck('permissions.title')
                ->toArray();

            Session::put('permission_titles', $permissionTitles);
        }

        $setting = Settings::first();
        Session::put('logged_id', $user->id);
        Session::put('logged_name', $user->name);
        if ($setting && $setting->logo) {
            Session::put('logo', $setting->logo);
        }

        return $next($request);
    }
}


?>