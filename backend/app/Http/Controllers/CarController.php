<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Car;
use App\Models\CarType;
use App\Models\ChargesType;
use App\Models\CarCharge;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

use Illuminate\Support\Facades\Storage;

class CarController extends Controller
{
    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                $data = Car::join('car_type', 'car.car_type_id', '=', 'car_type.id')
                    ->select('car.*', 'car_type.car_type')
                    ->orderBy('car.id', 'DESC');

                return DataTables::of($data)
                    ->addIndexColumn()
                    ->filterColumn('car_type', function ($query, $keyword) {
                        $query->where('car_type.car_type', 'like', "%{$keyword}%");
                    })
                    ->make(true);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getCarTypes()
    {
        try {
            $types = CarType::where('status', 1)->get();
            return response()->json($types, 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch car types.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function chargesType()
    {
        try {
            $types = ChargesType::where('status', 1)->get();
            return response()->json($types, 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch car types.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {

            /* =======================
             * VALIDATION
             * ======================= */
            $validator = Validator::make($request->all(), [
                'car_seats' => 'required|integer|min:1',
                'car_name' => 'required|string|max:100',
                'car_ac' => 'required|in:0,1',
                'car_type_id' => 'required|exists:car_type,id',
                'car_photos' => 'required|image|mimes:jpeg,png,jpg|max:2048',
                'max_passengers' => 'required|integer|min:1',
                'max_bags' => 'required|integer|min:0',
                'rating_summary' => 'required|numeric|min:0|max:5',
                'rating_value' => 'required|numeric|min:0|max:5',
                'rating_count' => 'required|integer|min:1',
                'min_trip_amount' => 'nullable|numeric|min:0',
                'booking_includes' => 'required|array|min:1',
                'booking_includes.*' => 'required|string|min:2',
                'why_book_us' => 'required|array|min:1',
                'why_book_us.*' => 'required|string|min:2',
                'trip_policies' => 'required|array|min:1',
                'trip_policies.*.question' => 'required|string|min:3',
                'trip_policies.*.answer' => 'required|string|min:3',
                'recent_reviews' => 'required|array|min:1',
                'recent_reviews.*.name' => 'required|string|min:2',
                'recent_reviews.*.rating' => 'required|numeric|min:1|max:5',
                'recent_reviews.*.comment' => 'required|string|min:5',
                'car_charges' => 'required|array|min:1',
                'car_charges.charges_type_id.*' => 'required|exists:charges_type,id',
                'car_charges.title.*' => 'required|string|max:100',
                'car_charges.amount.*' => 'required|numeric|min:0',
                'car_charges.charge_unit.*' => 'required|in:0,1,2,3',
                'car_charges.free_wait_minutes.*' => 'nullable|integer|min:0',
                'car_charges.wait_charge_unit.*' => 'nullable|in:0,1',
                'car_charges.min_km.*' => 'nullable|integer|min:0',
                'car_charges.max_km.*' => 'nullable|integer|min:0',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            /* =======================
             * SAVE CAR
             * ======================= */
            $car = new Car();
            $car->car_seats = $request->car_seats;
            $car->car_name = $request->car_name;
            $car->is_ac = $request->car_ac;
            $car->car_type_id = $request->car_type_id;
            $car->max_passengers = $request->max_passengers;
            $car->max_bags = $request->max_bags;

            $car->rating_summary = $request->rating_summary;
            $car->rating_value = $request->rating_value;
            $car->rating_count = $request->rating_count;
            $car->min_trip_amount = $request->min_trip_amount;

            // JSON save
            $car->booking_includes = $request->booking_includes
                ? json_encode($request->booking_includes)
                : null;

            $car->why_book_us = $request->why_book_us
                ? json_encode($request->why_book_us)
                : null;

            $car->trip_policies = $request->trip_policies
                ? json_encode($request->trip_policies)
                : null;

            $car->recent_reviews = $request->recent_reviews
                ? json_encode($request->recent_reviews)
                : null;

            // upload image
            if ($request->hasFile('car_photos')) {
                $photo = $request->file('car_photos');
                $photoName = 'car-' . uniqid() . '.' . $photo->getClientOriginalExtension();
                $photo->move(public_path('images/car'), $photoName);
                $car->car_photos = $photoName;
            }
            $car->save();

            /* =======================
             * SAVE CAR CHARGES
             * ======================= */
            if ($request->filled('car_charges')) {

                foreach ($request->car_charges['charges_type_id'] as $i => $typeId) {

                    CarCharge::create([
                        'car_id' => $car->id,
                        'charges_type_id' => $typeId,
                        'title' => $request->car_charges['title'][$i] ?? null,
                        'amount' => $request->car_charges['amount'][$i],
                        'charge_unit' => $request->car_charges['charge_unit'][$i],
                        'free_wait_minutes' => $request->car_charges['free_wait_minutes'][$i] ?? 0,
                        'wait_charge_unit' => $request->car_charges['wait_charge_unit'][$i] ?? 0,
                        'min_km' => $request->car_charges['min_km'][$i] ?? 0,
                        'max_km' => $request->car_charges['max_km'][$i] ?? null,
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Car added successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong while saving car.',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    public function edit($id)
    {
        try {
            $car = Car::with([
                'charges' => function ($q) {
                    $q->orderBy('id', 'ASC');
                }
            ])->findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $car->id,
                    'car_type_id' => $car->car_type_id,
                    'car_name' => $car->car_name,
                    'car_seats' => $car->car_seats,
                    'is_ac' => $car->is_ac,
                    'max_passengers' => $car->max_passengers,
                    'max_bags' => $car->max_bags,
                    'rating_summary' => $car->rating_summary,
                    'rating_value' => $car->rating_value,
                    'rating_count' => $car->rating_count,
                    'min_trip_amount' => $car->min_trip_amount,
                    'car_photos' => $car->car_photos,
                    'booking_includes' => $car->booking_includes ? json_decode($car->booking_includes, true) : [],
                    'why_book_us' => $car->why_book_us ? json_decode($car->why_book_us, true) : [],
                    'trip_policies' => $car->trip_policies ? json_decode($car->trip_policies, true) : [],
                    'recent_reviews' => $car->recent_reviews ? json_decode($car->recent_reviews, true) : [],
                    'charges' => $car->charges
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Car not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }


    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                /* =======================
                 * CAR BASIC (REQUIRED)
                 * ======================= */
                'car_seats' => 'required|integer|min:1',
                'car_name' => 'required|string|max:100',
                'car_ac' => 'required|in:0,1',
                'car_type_id' => 'required|exists:car_type,id',
                'car_photos' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'max_passengers' => 'required|integer|min:1',
                'max_bags' => 'required|integer|min:0',
                'rating_summary' => 'required|numeric|min:0|max:5',
                'rating_value' => 'required|numeric|min:0|max:5',
                'rating_count' => 'required|integer|min:1',
                'min_trip_amount' => 'nullable|numeric|min:0',
                'booking_includes' => 'required|array|min:1',
                'booking_includes.*' => 'required|string|min:2',
                'why_book_us' => 'required|array|min:1',
                'why_book_us.*' => 'required|string|min:2',
                'trip_policies' => 'required|array|min:1',
                'trip_policies.*.question' => 'required|string|min:3',
                'trip_policies.*.answer' => 'required|string|min:3',
                'recent_reviews' => 'required|array|min:1',
                'recent_reviews.*.name' => 'required|string|min:2',
                'recent_reviews.*.rating' => 'required|numeric|min:1|max:5',
                'recent_reviews.*.comment' => 'required|string|min:5',
                'car_charges' => 'required|array|min:1',
                'car_charges.charges_type_id.*' => 'required|exists:charges_type,id',
                'car_charges.title.*' => 'required|string|max:100',
                'car_charges.amount.*' => 'required|numeric|min:0',
                'car_charges.charge_unit.*' => 'required|in:0,1,2,3',
                'car_charges.free_wait_minutes.*' => 'nullable|integer|min:0',
                'car_charges.wait_charge_unit.*' => 'nullable|in:0,1',
                'car_charges.min_km.*' => 'nullable|integer|min:0',
                'car_charges.max_km.*' => 'nullable|integer|min:0',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            $car = Car::findOrFail($id);
            /* ================= BASIC UPDATE ================= */
            $car->car_seats = $request->car_seats;
            $car->car_name = $request->car_name;
            $car->is_ac = $request->car_ac;
            $car->car_type_id = $request->car_type_id;
            $car->max_passengers = $request->max_passengers;
            $car->max_bags = $request->max_bags;
            $car->rating_summary = $request->rating_summary;
            $car->rating_value = $request->rating_value;
            $car->rating_count = $request->rating_count;
            $car->min_trip_amount = $request->min_trip_amount;
            /* ================= JSON UPDATE ================= */
            $car->booking_includes = json_encode($request->booking_includes);
            $car->why_book_us = json_encode($request->why_book_us);
            $car->trip_policies = json_encode($request->trip_policies);
            $car->recent_reviews = json_encode($request->recent_reviews);
            /* ================= IMAGE UPDATE ================= */
            if ($request->hasFile('car_photos')) {
                if ($car->car_photos && file_exists(public_path('images/car/' . $car->car_photos))) {
                    unlink(public_path('images/car/' . $car->car_photos));
                }
                $photo = $request->file('car_photos');
                $photoName = 'car-' . uniqid() . '.' . $photo->getClientOriginalExtension();
                $photo->move(public_path('images/car'), $photoName);
                $car->car_photos = $photoName;
            }
            $car->save();
            CarCharge::where('car_id', $car->id)->delete();
            foreach ($request->car_charges['charges_type_id'] as $i => $typeId) {
                CarCharge::create([
                    'car_id' => $car->id,
                    'charges_type_id' => $typeId,
                    'title' => $request->car_charges['title'][$i] ?? null,
                    'amount' => $request->car_charges['amount'][$i],
                    'charge_unit' => $request->car_charges['charge_unit'][$i],
                    'free_wait_minutes' => $request->car_charges['free_wait_minutes'][$i] ?? 0,
                    'wait_charge_unit' => $request->car_charges['wait_charge_unit'][$i] ?? 0,
                    'min_km' => $request->car_charges['min_km'][$i] ?? 0,
                    'max_km' => $request->car_charges['max_km'][$i] ?? null,
                ]);
            }
            return response()->json([
                'success' => true,
                'message' => 'Car updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    public function changeStatus(Request $request, $id)
    {
        try {
            $car = car::findOrFail($id);
            $car->status = $request->status;
            $car->save();

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
            $car = Car::findOrFail($id);
            if ($car->car_photos && file_exists(public_path('storage/app/public/images/car/' . $car->car_photos))) {
                unlink(public_path('storage/app/public/images/car/' . $car->car_photos));
            }
            $car->delete();
            return response()->json([
                'success' => true,
                'message' => 'Car deleted successfully.'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting car.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
