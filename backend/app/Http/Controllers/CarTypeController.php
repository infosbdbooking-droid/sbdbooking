<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\permissions;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use App\Models\carType;
use Illuminate\Support\Facades\Storage;

class CarTypeController extends Controller
{
    // Get all categories
    public function index(Request $request)
    {
        try {  
            if ($request->ajax()) {
                $data = carType::query()
                ->orderBy('id', 'desc');
                if ($data->count() === 0) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No Car Type found.',
                        'data' => []
                    ], 201);
                }
                return DataTables::of($data)
                    ->addIndexColumn()
                    ->make(true);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again later.',
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try { 
            $validator = Validator::make(request()->all(), [
                'car_type' => 'required|string|max:255|unique:car_type',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $category = new carType();
            $category->car_type = request('car_type');
            $category->save();

            return response()->json([
                'success' => true,
                'message' => 'Car Type added successfully'
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
            $category = carType::findOrFail($id);
            return response()->json($category);
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
                'car_type' => 'required|string|max:255|unique:car_type,car_type,' . $id,
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $carType = carType::findOrFail($id);
            $carType->car_type = $request->car_type;
            $carType->save();

            return response()->json([
                'success' => true,
                'message' => 'Car type updated successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again later.'
            ], 500);
        }
    }

    public function changeStatus(Request $request, $id)
    {
        try {
            $carType = carType::findOrFail($id);
            $carType->status = $request->status;
            $carType->save();

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again later.',
            ], 500);
        }
    }



    public function destroy($id)
    {
        try {
            $carType = carType::findOrFail($id);
            $carType->delete();
            return response()->json([
                'success' => true,
                'message' => 'Car Type deleted successfully.'
            ]);
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