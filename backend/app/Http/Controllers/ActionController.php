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

            $user = User::where('email', $email)
                ->where('status', '1')
                ->first();

            if (!$user || !Hash::check($password, $user->password)) {

                $msg = 'Invalid credentials';

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $msg
                    ], 401);
                }

                return back()->withErrors(['message' => $msg]);
            }

            Auth::login($user);
            $this->storeSession($request, $user);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'redirect' => route('dashboard')
                ]);
            }

            return redirect()->route('dashboard');

        } catch (\Exception $e) {

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Server error'
                ], 500);
            }

            return back()->withErrors(['message' => 'Server error']);
        }
    }

    # STORE USER SESSION DATA
    protected function storeSession(Request $request, user $user)
    {
        try {
            # Store session data
            $sessionData = [
                'logged_id' => $user->id,
                'logged_in' => true,
                'last_activity' => time()
            ];
            foreach ($sessionData as $key => $value) {
                $request->session()->put($key, $value);
            }
        } catch (\Exception $e) {
            \Log::error('Session storage error: ' . $e->getMessage());
            throw $e;
        }
    }
    # LOGOUT
    public function logout()
    {
        Session::flush();
        return redirect('/');
    }
}
?>