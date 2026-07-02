<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SeoCity;
use App\Models\SeoState;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class SeoCityController extends Controller
{
    public function view()
    {
        $states = SeoState::where('status', 1)->get();
        return view('seo.cities', compact('states'));
    }

    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                $data = SeoCity::with('state')->orderBy('id', 'desc');
                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('state_name', function ($row) {
                        return $row->state ? $row->state->state_name : 'N/A';
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
                'state_id' => 'required|exists:seo_states,id',
                'city_name' => 'required|string|max:150',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            // Check unique slug globally
            $slug = Str::slug($request->city_name);
            $exists = SeoCity::where('slug', $slug)->exists();
            if ($exists) {
                return response()->json([
                    'success' => false,
                    'errors' => ['city_name' => ['This city name generates a duplicate slug. please use a different name.']]
                ], 422);
            }

            $city = new SeoCity();
            $city->state_id = $request->state_id;
            $city->city_name = $request->city_name;
            $city->slug = $slug;
            $city->status = $request->has('status') ? (int)$request->status : 1;
            $city->save();

            return response()->json([
                'success' => true,
                'message' => 'City added successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to store city.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        try {
            $city = SeoCity::findOrFail($id);
            return response()->json($city);
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
                'state_id' => 'required|exists:seo_states,id',
                'city_name' => 'required|string|max:150',
                'status' => 'required|in:0,1'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $slug = Str::slug($request->city_name);
            $exists = SeoCity::where('slug', $slug)->where('id', '!=', $id)->exists();
            if ($exists) {
                return response()->json([
                    'success' => false,
                    'errors' => ['city_name' => ['This city name generates a duplicate slug. please use a different name.']]
                ], 422);
            }

            $city = SeoCity::findOrFail($id);
            $city->state_id = $request->state_id;
            $city->city_name = $request->city_name;
            $city->slug = $slug;
            $city->status = $request->status;
            $city->save();

            return response()->json([
                'success' => true,
                'message' => 'City updated successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update city.'
            ], 500);
        }
    }

    public function changeStatus(Request $request, $id)
    {
        try {
            $city = SeoCity::findOrFail($id);
            $city->status = (int)$request->status;
            $city->save();

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
            $city = SeoCity::findOrFail($id);
            $city->delete();

            return response()->json([
                'success' => true,
                'message' => 'City deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete.'
            ], 500);
        }
    }

    // Helper for Ajax fetching of cities by state ID
    public function getCitiesByState($state_id)
    {
        $cities = SeoCity::where('state_id', $state_id)->where('status', 1)->orderBy('city_name', 'asc')->get();
        return response()->json($cities);
    }
}
