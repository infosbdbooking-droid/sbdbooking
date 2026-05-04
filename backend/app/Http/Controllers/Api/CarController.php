<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Car;

class CarController extends Controller
{
    // List all cars
    public function index()
    {
        $cars = Car::where('status', 1)->get();
        $cars->transform(function ($car) {
            $car->car_photos = $car->car_photos
                ? asset('storage/app/public/images/car/' . $car->car_photos)
                : null;
            return $car;
        });
        return response()->json([
            'status' => 1,
            'message' => $cars->isEmpty() ? 'No record found' : 'Cars fetched successfully',
            'data' => $cars
        ]);
    }


    public function carDetails(Request $request)
    {
        try {
            // ✅ API-safe validation
            $validator = Validator::make($request->all(), [
                'car_id' => 'required|integer|exists:car,id',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 0,
                    'message' => $validator->errors()->first(),
                    'data' => null
                ], 422);
            }
            //  Fetch car
            $car = Car::where('id', $request->car_id)
                ->where('status', 1)
                ->first();
            if (!$car) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Car not found',
                    'data' => null
                ], 200);
            }
            $car->car_photos = $car->car_photos
                ? asset('storage/app/public/images/car/' . $car->car_photos)
                : null;
            // FETCH CHARGES (GROUPED BY charges_type_id)
            $charges = \DB::table('car_charges')
                ->where('car_id', $request->car_id)
                ->where('status', 1)
                ->select(
                    'id',
                    'charges_type_id',
                    'title',
                    'amount',
                    'charge_unit',
                    'free_wait_minutes',
                    'wait_charge_unit',
                    'min_km',
                    'max_km'
                )
                ->orderBy('min_km', 'asc')
                ->get()
                ->groupBy('charges_type_id');
            // ADD charges INSIDE data (old format preserved)
            $car->charges = $charges;
            return response()->json([
                'status' => 1,
                'message' => 'Car details fetched successfully',
                'data' => $car
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }


}
