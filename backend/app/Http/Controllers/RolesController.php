<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Permissions;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use App\Models\Roles;
class RolesController extends Controller
{
    #Index 
    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                $roles = Roles::with('permissions')->select('id', 'title');
                if ($roles->count() === 0) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No roles found.',
                        'data' => []
                    ], 200);
                }
                return DataTables::of($roles)
                    ->addColumn('permissions', function ($role) {
                        return $role->permissions->pluck('title')->join(', ');
                    })

                    ->addColumn('actions', function ($role) {
                        return '';
                    })
                    ->rawColumns(['permissions'])
                    ->make(true);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again later.',
            ], 500);
        }
    }
    #Get Permissions 
    public function getPermissionsList()
    {
        try {
            $permissions = Permissions::pluck('title', 'id');
            return response()->json($permissions, 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again later.',
            ], 500);
        }


    }
    #Store 
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'permissions' => 'required|array',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            $role = new Roles();
            $role->title = $request->title;
            $role->save();
            $role->permissions()->sync($request->permissions);
            return response()->json([
                'success' => true,
                'message' => 'Role created successfully!',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again later.',
            ], 500);
        }
    }
    #Edit 
    public function edit($id)
    {
        try {
            $role = Roles::with('permissions')->findOrFail($id);
            return response()->json([
                'id' => $role->id,
                'title' => $role->title,
                'permissions' => $role->permissions->pluck('id')->toArray(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again later.',
            ], 500);
        }
    }
    #Update 
    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'permissions' => 'required|array',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $role = Roles::findOrFail($id);
            $role->update(['title' => $request->title]);
            $role->permissions()->sync($request->permissions);

            return response()->json([
                'success' => true,
                'message' => 'Role updated successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again later.',
            ], 500);
        }
    }
    #Delete 
    public function destroy($id)
    {
        try {
            $role = Roles::findOrFail($id);
            $role->permissions()->detach();
            $role->delete();

            return response()->json([
                'success' => true,
                'message' => 'Role deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again later.',
            ], 500);
        }
    }

}
?>