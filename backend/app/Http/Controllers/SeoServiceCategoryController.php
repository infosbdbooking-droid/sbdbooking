<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SeoServiceCategory;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class SeoServiceCategoryController extends Controller
{
    public function view()
    {
        return view('seo.service_categories');
    }

    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                $data = SeoServiceCategory::query()->orderBy('id', 'desc');
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
                'category_name' => 'required|string|max:150|unique:seo_service_categories,category_name',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $cat = new SeoServiceCategory();
            $cat->category_name = $request->category_name;
            $cat->slug = Str::slug($request->category_name);
            $cat->status = $request->has('status') ? (int)$request->status : 1;
            $cat->save();

            return response()->json([
                'success' => true,
                'message' => 'Service Category added successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to store category.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        try {
            $category = SeoServiceCategory::findOrFail($id);
            return response()->json($category);
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
                'category_name' => 'required|string|max:150|unique:seo_service_categories,category_name,' . $id,
                'status' => 'required|in:0,1'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $cat = SeoServiceCategory::findOrFail($id);
            $cat->category_name = $request->category_name;
            $cat->slug = Str::slug($request->category_name);
            $cat->status = $request->status;
            $cat->save();

            return response()->json([
                'success' => true,
                'message' => 'Service Category updated successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update category.'
            ], 500);
        }
    }

    public function changeStatus(Request $request, $id)
    {
        try {
            $cat = SeoServiceCategory::findOrFail($id);
            $cat->status = (int)$request->status;
            $cat->save();

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
            $cat = SeoServiceCategory::findOrFail($id);
            $cat->delete();

            return response()->json([
                'success' => true,
                'message' => 'Category deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete.'
            ], 500);
        }
    }
}
