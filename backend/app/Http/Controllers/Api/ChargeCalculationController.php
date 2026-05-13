<?php
 
 namespace App\Http\Controllers\Api;
 
 use App\Http\Controllers\Controller;
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
                 'days' => 'nullable|integer|min:1',
                 'hours' => 'nullable|numeric|min:0',
                 'is_ac' => 'nullable|boolean',
                 'trip_type' => 'nullable|string',
             ]);
 
             if ($validator->fails()) {
                 return response()->json([
                     'success' => false,
                     'errors' => $validator->errors()
                 ], 422);
             }
 
             $carId = $request->car_id;
             $distance = (float)$request->distance_km;
             $days = (int)($request->days ?? 1);
             $hours = (float)($request->hours ?? 0);
             $isAc = $request->is_ac ? true : false;
             $tripType = $request->trip_type ?? 'one_way';
 
             // Fetch all charges for this vehicle
             $carCharges = CarCharge::where('car_id', $carId)
                 ->with('chargeType')
                 ->get();
 
             if ($carCharges->isEmpty()) {
                 return response()->json([
                     'success' => false,
                     'message' => 'No charges configured for this vehicle'
                 ], 404);
             }
 
             $breakdown = [];
             $totalAmount = 0;
 
            // 1. First, find all charges that could possibly apply to this distance
            $applicableCharges = $carCharges->filter(function ($charge) use ($distance) {
                $minKm = (float)($charge->min_km ?? 0);
                $maxKm = $charge->max_km === null ? null : (float)$charge->max_km;
                
                // Direct match: Distance is within the bracket
                if ($distance >= $minKm && ($maxKm === null || $distance <= $maxKm)) {
                    return true;
                }
                
                // Minimum billable match: Distance is below the minKm (we'll charge minKm)
                if ($distance < $minKm) {
                    return true;
                }
                
                return false;
            });

            // 2. Group by type to handle brackets (if 0-250 and 0-Unlimited both match, pick the best one)
            $groupedByTypeId = $applicableCharges->groupBy('charges_type_id');
            $finalCharges = [];

            foreach ($groupedByTypeId as $typeId => $typeCharges) {
                $bestMatch = null;

                // Priority 1: Direct matches (distance between min and max)
                $directMatches = $typeCharges->filter(function ($c) use ($distance) {
                    $min = (float)($c->min_km ?? 0);
                    $max = $c->max_km === null ? null : (float)$c->max_km;
                    return $distance >= $min && ($max === null || $distance <= $max);
                });

                if ($directMatches->isNotEmpty()) {
                    // Pick the most specific one (smallest max_km)
                    $bestMatch = $directMatches->sortBy(function($c) {
                        return $c->max_km === null ? 999999 : (float)$c->max_km;
                    })->first();
                } else {
                    // Priority 2: Fallback to the lowest "Minimum Billable" charge
                    $bestMatch = $typeCharges->sortBy(function($c) {
                        return (float)($c->min_km ?? 0);
                    })->first();
                }

                if ($bestMatch) {
                    $finalCharges[] = $bestMatch;
                }
            }

            foreach ($finalCharges as $charge) {
                $chargeName = $charge->title ?: ($charge->chargeType ? $charge->chargeType->charges_type : 'Unknown Charge');
                $typeId = (int)$charge->charges_type_id;
                
                // Special Case: AC Charges (Match by Name or ID 10)
                $isAcCharge = ($typeId === 10 || ($charge->chargeType && $charge->chargeType->charges_type === 'AC Charges'));
                if ($isAcCharge && !$isAc) {
                    continue;
                }

                // Special Case: Stay Charges (Match by Name or ID 8)
                $isStayCharge = ($typeId === 8 || ($charge->chargeType && $charge->chargeType->charges_type === 'Stay Charges'));
                if ($isStayCharge && $tripType === 'one_way') {
                    continue;
                }

                // Special Case: Per Hour charges (Unit 2) only for Round Trip (needs return time)
                if ($charge->charge_unit == 2 && $tripType === 'one_way') {
                    continue;
                }

                $amount = 0;
                $unitLabel = '';
                $rate = (float)$charge->amount;
                $quantity = 1;
                $minKm = (float)($charge->min_km ?? 0);

                // Updated Mapping: 0 - Flat , 1 - Per KM , 2 - Per Hour ,3 - Per Day
                switch ($charge->charge_unit) {
                    case 1: // Per KM
                        $quantity = max($distance, $minKm);
                        $amount = $rate * $quantity;
                        $unitLabel = 'per KM';
                        break;
                    case 2: // Per Hour
                        $quantity = $hours;
                        $amount = $rate * $quantity;
                        $unitLabel = 'per Hour';
                        break;
                    case 3: // Per Day
                        $quantity = $days;
                        $amount = $rate * $quantity;
                        $unitLabel = 'per Day';
                        break;
                    case 0: // Flat
                    default:
                        $quantity = 1;
                        $amount = $rate;
                        $unitLabel = 'Flat';
                        break;
                }

                $breakdown[] = [
                    'charge_title' => $chargeName,
                    'rate' => $rate,
                    'unit' => $unitLabel,
                    'quantity' => $quantity,
                    'amount' => round($amount, 2),
                    'is_minimum_applied' => ($charge->charge_unit == 1 && $distance < $minKm)
                ];
                $totalAmount += $amount;
            }
 
             return response()->json([
                 'success' => true,
                 'data' => [
                     'car_id' => $carId,
                     'distance_km' => $distance,
                     'days' => $days,
                     'is_ac' => $isAc,
                     'charges_breakdown' => $breakdown,
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
