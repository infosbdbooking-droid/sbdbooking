<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Car;

class CarController extends Controller
{
    // Search and Filter Cars
    public function carFilter(Request $request)
    {
        $query = Car::with(['carType', 'charges.chargeType'])->where('status', 1);

        // Filter by Car Type (supports single ID or array)
        if ($request->filled('car_type_id')) {
            $typeIds = is_array($request->car_type_id) ? $request->car_type_id : [$request->car_type_id];
            $query->whereIn('car_type_id', $typeIds);
        }

        // Filter by Seating Capacity
        if ($request->filled('seats')) {
            $query->where('car_seats', $request->seats);
        }

        // Sort Logic
        if ($request->sort_by === 'price') {
            $query->orderBy('min_trip_amount', 'asc');
        } elseif ($request->sort_by === 'rating') {
            $query->orderBy('rating_value', 'desc');
        } else {
            $query->orderBy('id', 'desc'); // Popularity / Default
        }

        $cars = $query->get();

        $cars->transform(function ($car) {
            $car->car_photos = $car->car_photos
                ? asset('images/car/' . $car->car_photos)
                : null;
            
            // Find all Per KM Charges
            $perKmCharges = $car->charges->filter(function($charge) {
                return $charge->chargeType && $charge->chargeType->charges_type === 'Per KM Charges';
            });
            
            // Find Driver Allowance
            $driverAllowance = $car->charges->first(function($charge) {
                return $charge->chargeType && $charge->chargeType->charges_type === 'Driver Allowance';
            });
            
            if ($perKmCharges->isNotEmpty()) {
                $min = $perKmCharges->min('amount');
                $max = $perKmCharges->max('amount');
                
                $minFormatted = (int)round($min);
                $maxFormatted = (int)round($max);
                
                if ($minFormatted != $maxFormatted) {
                    $car->per_km_fare = $minFormatted . ' - ' . $maxFormatted;
                } else {
                    $car->per_km_fare = $minFormatted;
                }
            } else {
                $car->per_km_fare = (int)round($car->min_trip_amount ?: 12);
            }
            $car->driver_allowance = $driverAllowance ? $driverAllowance->amount : '0';
            
            return $car;
        });

        return response()->json([
            'status' => 1,
            'message' => 'Cars filtered successfully',
            'count' => $cars->count(),
            'data' => $cars
        ]);
    }

    public function index(Request $request)
    {
        return $this->carFilter($request);
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
                ? asset('images/car/' . $car->car_photos)
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

    public function carTypes()
    {
        $types = \App\Models\CarType::where('status', 1)->get();
        return response()->json([
            'status' => 1,
            'message' => 'Car types fetched successfully',
            'data' => $types
        ]);
    }
}
