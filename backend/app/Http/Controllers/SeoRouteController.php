<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SeoRoute;
use App\Models\SeoCity;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class SeoRouteController extends Controller
{
    public function view()
    {
        $cities = SeoCity::where('status', 1)->orderBy('city_name', 'asc')->get();
        return view('seo.routes', compact('cities'));
    }

    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                $data = SeoRoute::with(['fromCity', 'toCity'])->orderBy('id', 'desc');
                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('from_city_name', function ($row) {
                        return $row->fromCity ? $row->fromCity->city_name : 'N/A';
                    })
                    ->addColumn('to_city_name', function ($row) {
                        return $row->toCity ? $row->toCity->city_name : 'N/A';
                    })
                    ->make(true);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'from_city_id' => 'required|exists:seo_cities,id',
                'to_city_id' => 'required|exists:seo_cities,id|different:from_city_id',
                'distance' => 'nullable|string|max:50',
                'estimated_time' => 'nullable|string|max:50',
                'starting_price' => 'nullable|numeric|min:0',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            // Check if route already exists
            $exists = SeoRoute::where('from_city_id', $request->from_city_id)
                ->where('to_city_id', $request->to_city_id)
                ->exists();
            if ($exists) {
                return response()->json([
                    'success' => false,
                    'errors' => ['to_city_id' => ['This route already exists.']]
                ], 422);
            }

            $route = new SeoRoute();
            $route->from_city_id = $request->from_city_id;
            $route->to_city_id = $request->to_city_id;
            $route->distance = $request->distance;
            $route->estimated_time = $request->estimated_time;
            $route->starting_price = $request->starting_price ?? 0.00;
            $route->status = $request->has('status') ? (int)$request->status : 1;
            $route->save();

            return response()->json([
                'success' => true,
                'message' => 'Popular Route added successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to store route.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        try {
            $route = SeoRoute::findOrFail($id);
            return response()->json($route);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Not found.'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'from_city_id' => 'required|exists:seo_cities,id',
                'to_city_id' => 'required|exists:seo_cities,id|different:from_city_id',
                'distance' => 'nullable|string|max:50',
                'estimated_time' => 'nullable|string|max:50',
                'starting_price' => 'nullable|numeric|min:0',
                'status' => 'required|in:0,1'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            // Check if another route already exists
            $exists = SeoRoute::where('from_city_id', $request->from_city_id)
                ->where('to_city_id', $request->to_city_id)
                ->where('id', '!=', $id)
                ->exists();
            if ($exists) {
                return response()->json([
                    'success' => false,
                    'errors' => ['to_city_id' => ['This route already exists.']]
                ], 422);
            }

            $route = SeoRoute::findOrFail($id);
            $route->from_city_id = $request->from_city_id;
            $route->to_city_id = $request->to_city_id;
            $route->distance = $request->distance;
            $route->estimated_time = $request->estimated_time;
            $route->starting_price = $request->starting_price ?? 0.00;
            $route->status = $request->status;
            $route->save();

            return response()->json([
                'success' => true,
                'message' => 'Route updated successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update route.'
            ], 500);
        }
    }

    public function changeStatus(Request $request, $id)
    {
        try {
            $route = SeoRoute::findOrFail($id);
            $route->status = (int)$request->status;
            $route->save();

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status.'
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $route = SeoRoute::findOrFail($id);
            $route->delete();

            return response()->json([
                'success' => true,
                'message' => 'Route deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete.'
            ], 500);
        }
    }
}
