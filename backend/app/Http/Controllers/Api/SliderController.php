<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Slider;

class SliderController extends Controller
{
    public function getActiveSliders(Request $request)
    {
        try {
            $sliders = Slider::where('status', 1)->orderBy('order', 'asc')->orderBy('id', 'desc')->paginate(10);
            
            // Append full URL for image_path
            $sliders->getCollection()->transform(function ($slider) {
                if ($slider->image_path) {
                    $slider->image_url = asset('images/sliders/' . $slider->image_path);
                }
                return $slider;
            });

            return response()->json([
                'status' => 1,
                'message' => $sliders->isEmpty() ? 'No record found' : 'Sliders fetched successfully',
                'data' => $sliders
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'Something went wrong. Please try again later.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
