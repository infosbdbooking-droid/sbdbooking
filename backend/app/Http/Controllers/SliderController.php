<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Slider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Yajra\DataTables\DataTables;

class SliderController extends Controller
{
    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                $sliders = Slider::query()->orderBy('order', 'asc')->orderBy('id', 'desc')->paginate(10);
                
                if ($sliders->isEmpty()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No Slider found.',
                        'data' => []
                    ], 200); // Usually 200 is better here
                }
                
                return response()->json($sliders);
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
            $validator = Validator::make($request->all(), [
                'slider_id' => 'nullable|string|max:255',
                'title' => 'nullable|string|max:255',
                'alt' => 'nullable|string|max:255',
                'meta_title' => 'nullable|string|max:255',
                'order' => 'nullable|integer',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $slider = new Slider();
            $slider->slider_id = $request->slider_id;
            $slider->title = $request->title;
            $slider->alt = $request->alt;
            $slider->meta_title = $request->meta_title;
            $slider->order = $request->order ?? 1;

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $name = time() . '.' . $image->getClientOriginalExtension();
                $destinationPath = public_path('/images/sliders');
                $image->move($destinationPath, $name);
                $slider->image_path = $name;
            }

            $slider->status = ($request->status === 'Active' || $request->status == 1) ? 1 : 0;
            $slider->save();

            return response()->json([
                'success' => true,
                'message' => 'Slider added successfully'
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
            $slider = Slider::findOrFail($id);
            return response()->json($slider);
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
                'slider_id' => 'nullable|string|max:255',
                'title' => 'nullable|string|max:255',
                'alt' => 'nullable|string|max:255',
                'meta_title' => 'nullable|string|max:255',
                'order' => 'nullable|integer',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $slider = Slider::findOrFail($id);
            $slider->slider_id = $request->slider_id ?? $slider->slider_id;
            $slider->title = $request->title;
            $slider->alt = $request->alt;
            $slider->meta_title = $request->meta_title;
            $slider->order = $request->order ?? $slider->order;

            if ($request->hasFile('image')) {
                // remove old image
                if ($slider->image_path && File::exists(public_path('images/sliders/' . $slider->image_path))) {
                    File::delete(public_path('images/sliders/' . $slider->image_path));
                }
                $image = $request->file('image');
                $name = time() . '.' . $image->getClientOriginalExtension();
                $destinationPath = public_path('/images/sliders');
                $image->move($destinationPath, $name);
                $slider->image_path = $name;
            }

            if ($request->has('status')) {
                $slider->status = ($request->status === 'Active' || $request->status == 1) ? 1 : 0;
            }
            $slider->save();

            return response()->json([
                'success' => true,
                'message' => 'Slider updated successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again later.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function changeStatus(Request $request, $id)
    {
        try {
            $slider = Slider::findOrFail($id);
            $slider->status = $request->status;
            $slider->save();

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
            $slider = Slider::findOrFail($id);
            if ($slider->image_path && File::exists(public_path('images/sliders/' . $slider->image_path))) {
                File::delete(public_path('images/sliders/' . $slider->image_path));
            }
            $slider->delete();
            return response()->json([
                'success' => true,
                'message' => 'Slider deleted successfully.'
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
