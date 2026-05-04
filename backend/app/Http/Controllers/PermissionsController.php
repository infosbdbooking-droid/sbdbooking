<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Permissions;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
class PermissionsController extends Controller
{
    // Get all permissions
    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                $data = Permissions::query();
                if ($data->count() === 0) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No permissions found.',
                        'data' => []
                    ], 201);
                }
                return DataTables::of($data)->make(true);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again later.',
            ], 500);
        }
    }


    // Update Permissions
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            $permissions = new Permissions();
            $permissions->title = $request->title;

            $permissions->save();
            return response()->json([
                'success' => true,
                'message' => 'Permissions Add successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again later.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function edit($id)
    {
        try {
            $permission = Permissions::findOrFail($id);
            return response()->json($permission);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again later.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            $permission = Permissions::findOrFail($id);
            $permission->update([
                'title' => $request->title
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Permission updated successfully.'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again later.',
                'error' => $e->getMessage()
            ], 500);
        }

    }

    public function destroy($id)
    {
        try {
            $permission = Permissions::findOrFail($id);
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