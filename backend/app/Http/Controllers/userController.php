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
use App\Models\User;
use App\Models\roles;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;
class UserController extends Controller
{
    #Get Data
    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                $data = User::with('roles');
                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('roles.title', function ($user) {
                        return $user->roles ? $user->roles->title : 'N/A';
                    })
                    ->toJson();
            }

            return view('access.users');
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong. Please try again later.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    # Get Role 
    public function getRole()
    {
        try {
            $roles = roles::pluck('title', 'id');
            return response()->json($roles, 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again later.',
            ], 500);
        }


    }
    ## Add
    public function store(Request $request)
    { 
        try {
            $validated = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email',
                'role' =>  'required|exists:roles,id',
                'password' => 'required|string|min:6',
            ]);

            if ($validated->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validated->errors()
                ], 422);
            }

            if (User::where('email', $request->email)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User already exists with this email.',
                ], 201);
            }

            $validatedData = $validated->validated();

            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'role_id' => $validatedData['role'],
                'password' => Hash::make($validatedData['password']),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User registered successfully.',
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again later.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    #Edit 
    public function edit($id)
    {
        try {
            $user = User::findOrFail($id);
            return response()->json($user);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again later.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    #Update 
   public function update(Request $request, $id)
    {
        try {
                $validator = Validator::make($request->all(), [
                    'name' => 'required|string|max:255',
                    'email' => 'required|email|unique:users,email,' . $id,
                    'role' => 'required|exists:roles,id',
                    'password' => 'nullable|min:6'
                ]);
                if ($validator->fails()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $validator->errors()
                    ], 422);
                }
                $data = $validator->validated(); 
                $user = User::findOrFail($id);
                $user->name = $data['name'];
                $user->email = $data['email'];
                $user->role_id = $data['role'];
                if (!empty($data['password'])) {
                    $user->password = Hash::make($data['password']);
                }
                $user->save();
                return response()->json([
                    'success' => true,
                    'message' => 'User updated successfully.'
                ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again later.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    #Delete
    public function destroy($id)
    {
        try {
            $permission = User::findOrFail($id);
            $permission->delete();
            return response()->json([
                'success' => true,
                'message' => 'Permission deleted successfully.'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again later.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
?>