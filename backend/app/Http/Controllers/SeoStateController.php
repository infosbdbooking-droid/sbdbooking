<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SeoState;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class SeoStateController extends Controller
{
    public function view()
    {
        return view('seo.states');
    }

    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                $data = SeoState::query()->orderBy('id', 'desc');
                return DataTables::of($data)
                    ->addIndexColumn()
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
                'state_name' => 'required|string|max:150|unique:seo_states,state_name',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $state = new SeoState();
            $state->state_name = $request->state_name;
            $state->slug = Str::slug($request->state_name);
            $state->status = $request->has('status') ? (int)$request->status : 1;
            $state->save();

            return response()->json([
                'success' => true,
                'message' => 'State added successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to store state.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        try {
            $state = SeoState::findOrFail($id);
            return response()->json($state);
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
                'state_name' => 'required|string|max:150|unique:seo_states,state_name,' . $id,
                'status' => 'required|in:0,1'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $state = SeoState::findOrFail($id);
            $state->state_name = $request->state_name;
            $state->slug = Str::slug($request->state_name);
            $state->status = $request->status;
            $state->save();

            return response()->json([
                'success' => true,
                'message' => 'State updated successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update state.'
            ], 500);
        }
    }

    public function changeStatus(Request $request, $id)
    {
        try {
            $state = SeoState::findOrFail($id);
            $state->status = (int)$request->status;
            $state->save();

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
            $state = SeoState::findOrFail($id);
            $state->delete();

            return response()->json([
                'success' => true,
                'message' => 'State deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete.'
            ], 500);
        }
    }
}
