<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BlogTag;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class BlogTagController extends Controller
{
    // Return view
    public function view()
    {
        return view('blogs.tags');
    }

    // Ajax data for Datatables
    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                $data = BlogTag::query()->orderBy('id', 'desc');
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

    // Store new tag
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'tag_name' => 'required|string|max:100|unique:blog_tags,tag_name',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $tag = new BlogTag();
            $tag->tag_name = $request->tag_name;
            $tag->slug = Str::slug($request->tag_name);
            $tag->save();

            return response()->json([
                'success' => true,
                'message' => 'Tag added successfully.'
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
            $tag = BlogTag::findOrFail($id);
            return response()->json($tag);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tag not found.',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    // Update tag
    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'tag_name' => 'required|string|max:100|unique:blog_tags,tag_name,' . $id,
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $tag = BlogTag::findOrFail($id);
            $tag->tag_name = $request->tag_name;
            $tag->slug = Str::slug($request->tag_name);
            $tag->save();

            return response()->json([
                'success' => true,
                'message' => 'Tag updated successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again later.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Delete tag
    public function destroy($id)
    {
        try {
            $tag = BlogTag::findOrFail($id);
            $tag->delete();

            return response()->json([
                'success' => true,
                'message' => 'Tag deleted successfully.'
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
