<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BlogCategory;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class BlogCategoryController extends Controller
{
    // Return view
    public function view()
    {
        return view('blogs.categories');
    }

    // Ajax data for Datatables
    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                $data = BlogCategory::query()->orderBy('id', 'desc');
                return DataTables::of($data)
                    ->addIndexColumn()
                    ->make(true);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again later.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Store new category
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'category_name' => 'required|string|max:150|unique:blog_categories,category_name',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $category = new BlogCategory();
            $category->category_name = $request->category_name;
            $category->slug = Str::slug($request->category_name);
            $category->status = $request->has('status') ? (int)$request->status : 1;
            $category->save();

            return response()->json([
                'success' => true,
                'message' => 'Category added successfully.'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again later.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Edit view data
    public function edit($id)
    {
        try {
            $category = BlogCategory::findOrFail($id);
            return response()->json($category);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found.',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    // Update category
    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'category_name' => 'required|string|max:150|unique:blog_categories,category_name,' . $id,
                'status' => 'required|in:0,1'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $category = BlogCategory::findOrFail($id);
            $category->category_name = $request->category_name;
            $category->slug = Str::slug($request->category_name);
            $category->status = $request->status;
            $category->save();

            return response()->json([
                'success' => true,
                'message' => 'Category updated successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again later.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Change status toggle
    public function changeStatus(Request $request, $id)
    {
        try {
            $category = BlogCategory::findOrFail($id);
            $category->status = (int)$request->status;
            $category->save();

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again later.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Delete category
    public function destroy($id)
    {
        try {
            $category = BlogCategory::findOrFail($id);
            $category->delete();

            return response()->json([
                'success' => true,
                'message' => 'Category deleted successfully.'
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
