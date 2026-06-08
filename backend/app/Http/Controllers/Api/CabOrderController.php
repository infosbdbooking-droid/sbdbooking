<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CabOrder;
use App\Models\Car;
use App\Models\CarCharge;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class CabOrderController extends Controller
{
    /* =============================================================
     * POST /api/v1/cab-orders
     * Place a new cab order.
     *
     * Required fields:
     *   car_id, trip_type, pickup_date, pickup_time,
     *   pickup_address, pickup_lat, pickup_lng,
     *   drop_address, drop_lat, drop_lng,
     *   distance_km (one-way km),
     *   customer_name, customer_mobile
     *
     * Optional:
     *   stay_duration (short|day|night)   → default: short
     *   is_ac                             → default: false
     *   passengers, bags
     *   return_date, return_time
     *   return_pickup_address/lat/lng
     *   return_drop_address/lat/lng
     *   return_km                         → for round trips
     *   notes_for_driver
     *   coupon_code
     *   waiting_minutes
     *   estimated_toll
     * ============================================================= */
    public function placeOrder(Request $request)
    {

        // ─── Validation ───────────────────────────────────────────
        $validator = Validator::make($request->all(), [
            'car_id' => 'required|exists:car,id',
            'trip_type' => 'required|in:one_way,round_trip',
            'stay_duration' => 'nullable|in:short,day,night',
            'is_ac' => 'nullable|boolean',

            // Pickup
            'pickup_address' => 'required|string',
            'pickup_lat' => 'required|numeric|between:-90,90',
            'pickup_lng' => 'required|numeric|between:-180,180',

            // Drop
            'drop_address' => 'required|string',
            'drop_lat' => 'required|numeric|between:-90,90',
            'drop_lng' => 'required|numeric|between:-180,180',

            // Return (only for round_trip)
            'return_pickup_address' => 'nullable|string',
            'return_pickup_lat' => 'nullable|numeric|between:-90,90',
            'return_pickup_lng' => 'nullable|numeric|between:-180,180',
            'return_drop_address' => 'nullable|string',
            'return_drop_lat' => 'nullable|numeric|between:-90,90',
            'return_drop_lng' => 'nullable|numeric|between:-180,180',

            // Distance
            'distance_km' => 'required|numeric|min:0',
            'return_km' => 'nullable|numeric|min:0',

            // Schedule
            'pickup_date' => 'required|date',
            'pickup_time' => 'required|string',
            'return_date' => 'nullable|date',
            'return_time' => 'nullable|string',

            // Passengers
            'passengers' => 'nullable|integer|min:1',
            'bags' => 'nullable|integer|min:0',
            'notes_for_driver' => 'nullable|string|max:500',

            // Customer (if not logged in)
            'customer_name' => 'required_without:customer_id|string',
            'customer_mobile' => 'required_without:customer_id|string|max:20',

            // Coupon
            'coupon_code' => 'nullable|string',

            // Extras
            'waiting_minutes' => 'nullable|numeric|min:0',
            'estimated_toll' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 0,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        // Custom validation: Round trip pickup and return schedule validation
        if ($request->trip_type === 'round_trip') {
            if ($request->pickup_date === $request->return_date) {
                if ($request->pickup_time === $request->return_time) {
                    return response()->json([
                        'status' => 0,
                        'message' => "We can't do this. Return date and time cannot be the same as pickup date and time.",
                    ], 422);
                }

                $parseMinutes = function ($timeStr) {
                    if (!$timeStr) return 0;
                    $parts = explode(' ', $timeStr);
                    $timePart = $parts[0] ?? '';
                    $ampm = $parts[1] ?? '';
                    $timeSubParts = explode(':', $timePart);
                    $hours = (int)($timeSubParts[0] ?? 0);
                    $minutes = (int)($timeSubParts[1] ?? 0);
                    
                    if (strtoupper($ampm) === 'PM' && $hours < 12) {
                        $hours += 12;
                    }
                    if (strtoupper($ampm) === 'AM' && $hours === 12) {
                        $hours = 0;
                    }
                    return $hours * 60 + $minutes;
                };

                $pickupMins = $parseMinutes($request->pickup_time);
                $returnMins = $parseMinutes($request->return_time);

                if ($returnMins < $pickupMins) {
                    return response()->json([
                        'status' => 0,
                        'message' => "We can't do this. Return time cannot be earlier than pickup time.",
                    ], 422);
                }
            }
        }

        try {
            // ─── Auth: optional sanctum customer ──────────────────────
            $customer = null;
            if ($request->bearerToken()) {
                $customer = auth('sanctum')->user();
            }

            DB::beginTransaction();

            // ─── Resolve customer details ──────────────────────────
            $customerId = $customer?->id ?? null;
            // Prioritize name/mobile from the form (for booking on behalf of others)
            $customerName = $request->customer_name ?: ($customer?->name ?? 'Guest');
            $customerMobile = $request->customer_mobile ?: ($customer?->mobile ?? 'N/A');

            // ─── Fetch car snapshot ────────────────────────────────
            $car = Car::findOrFail($request->car_id);

            $oneWayKm = (float)$request->distance_km;
            // If return_km is not provided or is 0, default to oneWayKm (car return)
            $returnKm = ($request->return_km && (float)$request->return_km > 0) ? (float)$request->return_km : $oneWayKm;
            $totalKmForCalc = $oneWayKm + $returnKm;

            // ─── Calculate Charges ─────────────────────────────────
            $chargesResult = $this->computeCharges(
                carId: $request->car_id,
                distanceKm: $totalKmForCalc,
                tripType: $request->trip_type,
                stayDuration: $request->stay_duration ?? 'short',
                isAc: (bool) ($request->is_ac ?? false),
                waitingMinutes: $request->waiting_minutes ?? 0
            );

            if (!$chargesResult['success']) {
                return response()->json([
                    'status' => 0,
                    'message' => $chargesResult['message'],
                ], 422);
            }

            $chargesData = $chargesResult['data'];
            $chargesBreakdown = $chargesData['charges_breakdown'];

            // ─── Extract individual charge amounts ─────────────────
            $perKmAmount = $this->extractCharge($chargesBreakdown, 'Per KM');
            $driverAllowance = $this->extractCharge($chargesBreakdown, 'Driver Allowance');
            $platformCharges = $this->extractCharge($chargesBreakdown, 'Platform Charges');
            $acCharges = $this->extractCharge($chargesBreakdown, 'AC Charges');
            $waitingCharges = $this->extractCharge($chargesBreakdown, 'Waiting Charges');

            $subtotal = $chargesData['total_amount'];

            // ─── Coupon Discount ───────────────────────────────────
            $discountAmount = 0;
            $couponCode = null;
            if ($request->coupon_code) {
                [$discountAmount, $couponCode] = $this->applyCoupon(
                    $request->coupon_code,
                    $subtotal
                );
            }
            $totalAmount = max(0, $subtotal - $discountAmount);

            // ─── Distance totals ───────────────────────────────────
            // $oneWayKm and $returnKm already calculated above
            $totalKm = $oneWayKm + $returnKm;

            // ─── Create order ──────────────────────────────────────
            $order = CabOrder::create([
                'order_number' => CabOrder::generateOrderNumber(),
                'booking_status' => 'pending',

                'customer_id' => $customerId,
                'customer_name' => $customerName,
                'customer_mobile' => $customerMobile,

                'car_id' => $car->id,
                'car_name' => $car->car_name,

                'trip_type' => $request->trip_type,
                'stay_duration' => $request->stay_duration ?? 'short',
                'is_ac' => $request->is_ac ?? false,

                // Pickup
                'pickup_address' => $request->pickup_address,
                'pickup_lat' => $request->pickup_lat,
                'pickup_lng' => $request->pickup_lng,

                // Drop
                'drop_address' => $request->drop_address,
                'drop_lat' => $request->drop_lat,
                'drop_lng' => $request->drop_lng,

                // Return
                'return_pickup_address' => $request->return_pickup_address,
                'return_pickup_lat' => $request->return_pickup_lat,
                'return_pickup_lng' => $request->return_pickup_lng,
                'return_drop_address' => $request->return_drop_address,
                'return_drop_lat' => $request->return_drop_lat,
                'return_drop_lng' => $request->return_drop_lng,

                // Distance
                'one_way_km' => $oneWayKm,
                'return_km' => $returnKm,
                'total_km' => $totalKm,

                // Schedule
                'pickup_date' => $request->pickup_date,
                'pickup_time' => $request->pickup_time,
                'return_date' => $request->return_date,
                'return_time' => $request->return_time,

                // Passengers
                'passengers' => $request->passengers ?? 1,
                'bags' => $request->bags ?? 0,
                'notes_for_driver' => $request->notes_for_driver,

                // Charges
                'per_km_amount' => $perKmAmount,
                'driver_allowance' => $driverAllowance,
                'platform_charges' => $platformCharges,
                'ac_charges' => $acCharges,
                'waiting_charges' => $waitingCharges,
                'toll_tax' => 0,
                'estimated_toll' => $request->estimated_toll ?? 0,
                'charges_breakdown' => $chargesBreakdown,

                // Coupon
                'coupon_code' => $couponCode,
                'discount_amount' => $discountAmount,

                // Totals
                'subtotal' => $subtotal,
                'total_amount' => $totalAmount,

                // Payment
                'payment_status' => 'unpaid',
            ]);

            DB::commit();

            return response()->json([
                'status' => 1,
                'message' => 'Booking confirmed! Your order has been placed.',
                'data' => $this->formatOrder($order),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 0,
                'message' => 'Something went wrong while placing the order.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /* =============================================================
     * GET /api/v1/cab-orders                (auth required)
     * List orders for the authenticated customer.
     * ============================================================= */
    public function myOrders(Request $request)
    {
        try {
            $customer = auth('sanctum')->user();

            if (!$customer) {
                return response()->json(['status' => 0, 'message' => 'Unauthenticated'], 401);
            }

            $perPage = $request->input('per_page', 10);
            $search = $request->input('search');

            $query = CabOrder::where('customer_id', $customer->id);

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('order_number', 'like', "%{$search}%")
                      ->orWhere('pickup_address', 'like', "%{$search}%")
                      ->orWhere('drop_address', 'like', "%{$search}%")
                      ->orWhere('car_name', 'like', "%{$search}%");
                });
            }

            $orders = $query->orderByDesc('created_at')->paginate($perPage);

            return response()->json([
                'status' => 1,
                'data' => $orders->map(fn($o) => $this->formatOrder($o)),
                'meta' => [
                    'current_page' => $orders->currentPage(),
                    'last_page' => $orders->lastPage(),
                    'total' => $orders->total(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 0, 'message' => $e->getMessage()], 500);
        }
    }

    /* =============================================================
     * GET /api/v1/cab-orders/{orderNumber}
     * Get a single order detail.
     * ============================================================= */
    public function orderDetail(Request $request, string $orderNumber)
    {
        try {
            $order = CabOrder::where('order_number', $orderNumber)->firstOrFail();

            // Optional: ensure customer owns this order
            if ($request->bearerToken()) {
                $customer = auth('sanctum')->user();
                if ($customer && $order->customer_id && $order->customer_id !== $customer->id) {
                    return response()->json(['status' => 0, 'message' => 'Forbidden'], 403);
                }
            }

            return response()->json([
                'status' => 1,
                'data' => $this->formatOrder($order),
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 0, 'message' => 'Order not found'], 404);
        }
    }

    /* =============================================================
     * POST /api/v1/cab-orders/{orderNumber}/cancel
     * Cancel a pending order.
     * ============================================================= */
    public function cancelOrder(Request $request, string $orderNumber)
    {
        try {
            $order = CabOrder::where('order_number', $orderNumber)->firstOrFail();

            if (!in_array($order->booking_status, ['pending', 'confirmed'])) {
                return response()->json([
                    'status' => 0,
                    'message' => 'This order cannot be cancelled at this stage.',
                ], 422);
            }

            $order->update(['booking_status' => 'cancelled']);

            return response()->json([
                'status' => 1,
                'message' => 'Order cancelled successfully.',
                'data' => $this->formatOrder($order->fresh()),
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 0, 'message' => 'Order not found'], 404);
        }
    }

    /* ─────────────────────────────────────────────────────────────
     *  PRIVATE HELPERS
     * ───────────────────────────────────────────────────────────── */

    /**
     * Re-uses the ChargeCalculation logic internally.
     */
    private function computeCharges(
        int $carId,
        float $distanceKm,
        string $tripType,
        string $stayDuration = 'short',
        bool $isAc = false,
        float $waitingMinutes = 0
    ): array {
        $carCharges = CarCharge::where('car_id', $carId)->with('chargeType')->get();

        if ($carCharges->isEmpty()) {
            return ['success' => false, 'message' => 'No charges configured for this vehicle'];
        }

        $charges = [];
        $totalAmount = 0;

        // Per KM
        $kmCharges = $carCharges->filter(
            fn($ch) =>
            strpos(strtolower($ch->chargeType->charges_type), 'km') !== false
        );

        $applicableKmCharge = null;
        foreach ($kmCharges as $charge) {
            $minKm = $charge->min_km ?? 0;
            $maxKm = $charge->max_km;
            if ($distanceKm >= $minKm && ($maxKm === null || $distanceKm <= $maxKm)) {
                $applicableKmCharge = $charge;
                break;
            }
        }

        if ($applicableKmCharge) {
            $kmAmount = $distanceKm * $applicableKmCharge->amount;
            $charges[] = [
                'type' => 'Per KM',
                'charge_type' => $applicableKmCharge->chargeType->charges_type,
                'rate' => $applicableKmCharge->amount,
                'distance' => $distanceKm,
                'amount' => $kmAmount,
            ];
            $totalAmount += $kmAmount;
        }

        // Driver Allowance / Day / Night
        $allowanceKey = match ($stayDuration) {
            'day' => 'Day Charges',
            'night' => 'Night Charges',
            default => 'Driver Allowance',
        };
        $allowanceCharge = $carCharges->firstWhere('chargeType.charges_type', $allowanceKey);
        if ($allowanceCharge) {
            // Stay Charges only for Round Trip
            if (($allowanceCharge->charges_type_id == 8 || strpos($allowanceCharge->chargeType->charges_type, 'Stay') !== false) && $tripType === 'one_way') {
                // skip
            } else {
                $charges[] = [
                    'type' => 'Driver Allowance',
                    'charge_type' => $allowanceCharge->chargeType->charges_type,
                    'amount' => $allowanceCharge->amount,
                ];
                $totalAmount += $allowanceCharge->amount;
            }
        }

        // AC Charges (Match by name or type)
        if ($isAc) {
            $acCharge = $carCharges->first(function($ch) {
                return (int)$ch->charges_type_id === 10 || 
                       ($ch->chargeType && $ch->chargeType->charges_type === 'AC Charges');
            });
            if ($acCharge) {
                $acAmount = $distanceKm * $acCharge->amount;
                $charges[] = [
                    'type' => 'AC Charges',
                    'charge_type' => $acCharge->chargeType->charges_type,
                    'rate' => $acCharge->amount,
                    'distance' => $distanceKm,
                    'amount' => $acAmount,
                ];
                $totalAmount += $acAmount;
            }
        }

        // Platform Charges
        $platformCharge = $carCharges->firstWhere('chargeType.charges_type', 'Platform Charges');
        if ($platformCharge) {
            $charges[] = [
                'type' => 'Platform Charges',
                'charge_type' => $platformCharge->chargeType->charges_type,
                'amount' => $platformCharge->amount,
            ];
            $totalAmount += $platformCharge->amount;
        }

        // Waiting Charges
        if ($waitingMinutes > 0) {
            $waitingCharge = $carCharges->firstWhere('chargeType.charges_type', 'Waiting Charges');
            if ($waitingCharge) {
                $waitingAmount = ($waitingMinutes / 60) * $waitingCharge->amount;
                $charges[] = [
                    'type' => 'Waiting Charges',
                    'charge_type' => $waitingCharge->chargeType->charges_type,
                    'minutes' => $waitingMinutes,
                    'amount' => $waitingAmount,
                ];
                $totalAmount += $waitingAmount;
            }
        }

        // Toll / Parking / Taxes
        $charges[] = ['type' => 'Toll, Parking & Taxes', 'status' => 'Included'];

        return [
            'success' => true,
            'data' => [
                'charges_breakdown' => $charges,
                'total_amount' => round($totalAmount, 2),
            ],
        ];
    }

    /**
     * Extract a specific charge amount from breakdown array.
     */
    private function extractCharge(array $breakdown, string $type): float
    {
        foreach ($breakdown as $charge) {
            if (isset($charge['type']) && $charge['type'] === $type) {
                return (float) ($charge['amount'] ?? 0);
            }
        }
        return 0.0;
    }

    /**
     * Apply coupon code. Extend this logic as needed.
     * Returns [discountAmount, validatedCouponCode]
     */
    private function applyCoupon(string $code, float $total): array
    {
        // Hardcoded demo coupons — replace with DB lookup later
        $coupons = [
            'WELCOME10' => ['type' => 'percent', 'value' => 10],
            'FLAT100' => ['type' => 'fixed', 'value' => 100],
            'SBD50' => ['type' => 'percent', 'value' => 50, 'max' => 300],
        ];

        $code = strtoupper(trim($code));
        if (!isset($coupons[$code])) {
            return [0, null];
        }

        $coupon = $coupons[$code];
        $discount = 0;

        if ($coupon['type'] === 'percent') {
            $discount = ($total * $coupon['value']) / 100;
            if (isset($coupon['max'])) {
                $discount = min($discount, $coupon['max']);
            }
        } else {
            $discount = $coupon['value'];
        }

        return [round($discount, 2), $code];
    }

    /**
     * Format a CabOrder for API response.
     */
    private function formatOrder(CabOrder $order): array
    {
        return [
            'id' => $order->id,
            'order_number' => $order->order_number,
            'booking_status' => $order->booking_status,

            'customer' => [
                'id' => $order->customer_id,
                'name' => $order->customer_name,
                'mobile' => $order->customer_mobile,
            ],

            'car' => [
                'id' => $order->car_id,
                'name' => $order->car_name,
            ],

            'trip' => [
                'type' => $order->trip_type,
                'stay_duration' => $order->stay_duration,
                'is_ac' => $order->is_ac,
                'passengers' => $order->passengers,
                'bags' => $order->bags,
            ],

            'pickup' => [
                'address' => $order->pickup_address,
                'lat' => $order->pickup_lat,
                'lng' => $order->pickup_lng,
            ],

            'drop' => [
                'address' => $order->drop_address,
                'lat' => $order->drop_lat,
                'lng' => $order->drop_lng,
            ],

            'return' => $order->trip_type === 'round_trip' ? [
                'pickup' => [
                    'address' => $order->return_pickup_address,
                    'lat' => $order->return_pickup_lat,
                    'lng' => $order->return_pickup_lng,
                ],
                'drop' => [
                    'address' => $order->return_drop_address,
                    'lat' => $order->return_drop_lat,
                    'lng' => $order->return_drop_lng,
                ],
                'return_date' => $order->return_date,
                'return_time' => $order->return_time,
            ] : null,

            'distance' => [
                'one_way_km' => $order->one_way_km,
                'return_km' => $order->return_km,
                'total_km' => $order->total_km,
            ],

            'schedule' => [
                'pickup_date' => $order->pickup_date,
                'pickup_time' => $order->pickup_time,
            ],

            'notes_for_driver' => $order->notes_for_driver,

            'charges' => [
                'per_km' => $order->per_km_amount,
                'driver_allowance' => $order->driver_allowance,
                'platform_charges' => $order->platform_charges,
                'ac_charges' => $order->ac_charges,
                'waiting_charges' => $order->waiting_charges,
                'estimated_toll' => $order->estimated_toll,
                'breakdown' => $order->charges_breakdown,
            ],

            'coupon' => [
                'code' => $order->coupon_code,
                'discount_amount' => $order->discount_amount,
            ],

            'payment' => [
                'subtotal' => $order->subtotal,
                'total_amount' => $order->total_amount,
                'payment_status' => $order->payment_status,
                'payment_method' => $order->payment_method,
                'currency' => '₹',
            ],

            'created_at' => $order->created_at?->toISOString(),
            'updated_at' => $order->updated_at?->toISOString(),
        ];
    }
}
