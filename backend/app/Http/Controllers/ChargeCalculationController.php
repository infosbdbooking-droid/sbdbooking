<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Car;
use App\Models\ChargesType;
use App\Models\CarCharge;
use Illuminate\Support\Facades\Validator;

class ChargeCalculationController extends Controller
{
    /**
     * Calculate charges for a trip
     * POST /api/calculate-charges
     */
    public function calculateCharges(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'car_id' => 'required|exists:car,id',
                'distance_km' => 'required|numeric|min:0',
                'trip_type' => 'required|in:one_way,round_trip', // one_way or round_trip
                'stay_duration' => 'nullable|in:short,day,night', // short=3-4hrs, day=day charges, night=overnight
                'is_ac' => 'nullable|boolean',
                'waiting_minutes' => 'nullable|numeric|min:0',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $carId = $request->car_id;
            $distance = $request->distance_km;
            $tripType = $request->trip_type;
            $stayDuration = $request->stay_duration ?? 'short';
            $isAc = $request->is_ac ? 1 : 0;
            $waitingMinutes = $request->waiting_minutes ?? 0;

            // Fetch car charges configured for this vehicle
            $carCharges = CarCharge::where('car_id', $carId)
                ->with('chargeType')
                ->get();

            if ($carCharges->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No charges configured for this vehicle'
                ], 404);
            }

            $charges = [];
            $totalAmount = 0;

            /* ========================
             * DETERMINE KM RANGE & RATE
             * ======================== */
            $kmCharges = $carCharges->filter(function ($ch) {
                return strpos(strtolower($ch->chargeType->charges_type), 'km') !== false;
            });

            $applicableKmCharge = null;
            foreach ($kmCharges as $charge) {
                $minKm = $charge->min_km ?? 0;
                $maxKm = $charge->max_km;

                if ($distance >= $minKm && ($maxKm === null || $distance <= $maxKm)) {
                    $applicableKmCharge = $charge;
                    break;
                }
            }

            if ($applicableKmCharge) {
                $kmRate = $applicableKmCharge->amount;
                $kmChargeAmount = $distance * $kmRate;
                $charges[] = [
                    'type' => 'Per KM',
                    'charge_type' => $applicableKmCharge->chargeType->charges_type,
                    'rate' => $kmRate,
                    'distance' => $distance,
                    'amount' => $kmChargeAmount
                ];
                $totalAmount += $kmChargeAmount;
            }

            /* ========================
             * DRIVER ALLOWANCE / DAY / NIGHT CHARGES
             * ======================== */
            $allowanceCharge = null;

            if ($stayDuration === 'short') {
                // 3-4 hours: Driver Allowance
                $allowanceCharge = $carCharges->firstWhere('chargeType.charges_type', 'Driver Allowance');
            } elseif ($stayDuration === 'day') {
                // Day charges
                $allowanceCharge = $carCharges->firstWhere('chargeType.charges_type', 'Day Charges');
            } elseif ($stayDuration === 'night') {
                // Night charges
                $allowanceCharge = $carCharges->firstWhere('chargeType.charges_type', 'Night Charges');
            }

            if ($allowanceCharge) {
                $charges[] = [
                    'type' => 'Driver Allowance',
                    'charge_type' => $allowanceCharge->chargeType->charges_type,
                    'amount' => $allowanceCharge->amount
                ];
                $totalAmount += $allowanceCharge->amount;
            }

            /* ========================
             * AC CHARGES (if applicable)
             * ======================== */
            if ($isAc) {
                $acCharge = $carCharges->firstWhere('chargeType.charges_type', 'AC Charges');
                if ($acCharge) {
                    $acAmount = $distance * $acCharge->amount;
                    $charges[] = [
                        'type' => 'AC Charges',
                        'charge_type' => $acCharge->chargeType->charges_type,
                        'rate' => $acCharge->amount,
                        'distance' => $distance,
                        'amount' => $acAmount
                    ];
                    $totalAmount += $acAmount;
                }
            }

            /* ========================
             * PLATFORM CHARGES
             * ======================== */
            $platformCharge = $carCharges->firstWhere('chargeType.charges_type', 'Platform Charges');
            if ($platformCharge) {
                $charges[] = [
                    'type' => 'Platform Charges',
                    'charge_type' => $platformCharge->chargeType->charges_type,
                    'amount' => $platformCharge->amount
                ];
                $totalAmount += $platformCharge->amount;
            }

            /* ========================
             * WAITING CHARGES (Optional)
             * ======================== */
            if ($waitingMinutes > 0) {
                $waitingCharge = $carCharges->firstWhere('chargeType.charges_type', 'Waiting Charges');
                if ($waitingCharge) {
                    // Assuming waiting charge is per minute
                    $waitingAmount = ($waitingMinutes / 60) * $waitingCharge->amount; // Convert to hours if per hour
                    $charges[] = [
                        'type' => 'Waiting Charges',
                        'charge_type' => $waitingCharge->chargeType->charges_type,
                        'minutes' => $waitingMinutes,
                        'amount' => $waitingAmount
                    ];
                    $totalAmount += $waitingAmount;
                }
            }

            /* ========================
             * TOLL, PARKING, TAXES (Usually Included)
             * ======================== */
            $charges[] = [
                'type' => 'Toll, Parking',
                'status' => 'Included'
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'car_id' => $carId,
                    'distance_km' => $distance,
                    'trip_type' => $tripType,
                    'stay_duration' => $stayDuration,
                    'is_ac' => $isAc,
                    'charges_breakdown' => $charges,
                    'total_amount' => round($totalAmount, 2),
                    'currency' => '₹'
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error calculating charges',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all charges for a specific car
     * GET /api/car/{id}/charges
     */
    public function getCarCharges($carId)
    {
        try {
            $car = Car::findOrFail($carId);
            $charges = CarCharge::where('car_id', $carId)
                ->with('chargeType')
                ->orderBy('min_km', 'ASC')
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'car' => [
                        'id' => $car->id,
                        'name' => $car->car_name,
                        'seats' => $car->car_seats,
                    ],
                    'charges' => $charges
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
}
