<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
class ActionController extends Controller
{

    public function signIn(Request $request)
    {
        try {
            $email = $request->input('email');
            $password = $request->input('password');
            $user = user::where(function ($query) use ($email) {
                $query->where('email', $email);
            })
                ->where('status', '1')
                ->first();
            if (!$user) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'The provided credentials do not match our records.'
                    ], 201);
                }
                return back()->withErrors(['message' => 'The provided credentials do not match our records.'])->withInput();
            }

            # Password Verification
            if (!Hash::check($password, $user->password)) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'The provided credentials do not match our records.'
                    ], 201);
                }
                return back()->withErrors(['message' => 'The provided credentials do not match our records.'])->withInput();
            }
            $this->storeSession($request, $user);

            $permissionTitles = DB::table('permission_role')
                ->join('permissions', 'permission_role.permission_id', '=', 'permissions.id')
                ->where('permission_role.role_id', $user->role_id)
                ->pluck('permissions.title')
                ->toArray();
          

            $request->session()->put('permission_titles', $permissionTitles);

            DB::table('login_logs')->insert([
                'user_id'    => $user->id,
                'ip_address' => $request->ip(),
                'logged_in_at' => now()
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success'  => true,
                    'message'  => 'Authentication successful',
                    'redirect' => route('dashboard'),
                    'user'     => $user
                ]);
            }

            return redirect()->route('dashboard')->with('success', 'Authentication successful');

        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Something went wrong. Please try again later.',
                    'error' => $e->getMessage()
                ], 500);
            }
            return back()->withErrors(['message' => 'Something went wrong. Please try again later.'])->withInput();
        }
    }

      # STORE USER SESSION DATA
      protected function storeSession(Request $request, user $user){
        try{
            # Store session data
            $sessionData = [
                'logged_id'         => $user->id,
                'logged_in'         => true,
                'last_activity'     => time()
            ];
            foreach($sessionData as $key => $value){
                $request->session()->put($key, $value);
            }
        }catch(\Exception $e){
            \Log::error('Session storage error: ' . $e->getMessage());
            throw $e;
        }
    }
      # LOGOUT
      public function logout(){
        Session::flush();
        return redirect('/');
    }
}
?>