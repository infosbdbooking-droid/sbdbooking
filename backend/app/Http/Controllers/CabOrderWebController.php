<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CabOrder;
use App\Models\Car;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Barryvdh\DomPDF\Facade\Pdf;

class CabOrderWebController extends Controller
{
    /**
     * Display a listing of cab orders.
     */
    public function index()
    {
        $orders = CabOrder::with(['customer', 'car'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('cabOrders.index', compact('orders'));
    }

    /**
     * Show form to create a manual cab booking.
     */
    public function create()
    {
        $cars = Car::where('status', 1)->get();
        $customers = Customer::where('status', 1)->orderBy('name', 'asc')->get();
        
        return view('cabOrders.create', compact('cars', 'customers'));
    }

    /**
     * Store a newly created cab booking in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'customer_type' => 'required|in:existing,new',
            'car_id' => 'required|exists:car,id',
            'trip_type' => 'required|in:one_way,round_trip',
            'stay_duration' => 'nullable|in:short,day,night',
            'is_ac' => 'nullable',
            'pickup_address' => 'required|string',
            'drop_address' => 'required|string',
            'pickup_date' => 'required|date',
            'pickup_time' => 'required|string',
            'passengers' => 'required|integer|min:1',
            'bags' => 'nullable|integer|min:0',
            'notes_for_driver' => 'nullable|string|max:500',
            'booking_status' => 'required|in:pending,confirmed,cancelled,completed',
            'payment_status' => 'required|in:unpaid,paid,partially_paid',
            'payment_method' => 'required|string',
        ];

        if ($request->customer_type === 'existing') {
            $rules['customer_id'] = 'required|exists:customers,id';
        } else {
            $rules['customer_name'] = 'required|string|max:255';
            $rules['customer_mobile'] = 'required|string|max:20';
            $rules['customer_email'] = 'nullable|email|max:255';
        }

        if ($request->trip_type === 'round_trip') {
            $rules['return_date'] = 'required|date';
            $rules['return_time'] = 'required|string';
        }

        $request->validate($rules);

        try {
            DB::beginTransaction();

            // Resolve Customer
            if ($request->customer_type === 'existing') {
                $customer = Customer::findOrFail($request->customer_id);
                $isUpdated = false;
                if ($request->has('customer_name') && $request->customer_name != $customer->name) {
                    $customer->name = $request->customer_name;
                    $isUpdated = true;
                }
                if ($request->has('customer_mobile') && $request->customer_mobile != $customer->mobile) {
                    $customer->mobile = $request->customer_mobile;
                    $isUpdated = true;
                }
                if ($isUpdated) {
                    $customer->save();
                }
            } else {
                // Check if customer already exists by mobile to avoid duplicates
                $customer = Customer::where('mobile', $request->customer_mobile)->first();
                if (!$customer) {
                    $customer = Customer::create([
                        'name' => $request->customer_name,
                        'mobile' => $request->customer_mobile,
                        'email' => $request->customer_email,
                        'password' => Hash::make($request->customer_mobile), // Default password is mobile number
                        'status' => 1,
                    ]);
                }
            }

            $car = Car::findOrFail($request->car_id);

            // Distance
            $oneWayKm = (float)($request->one_way_km ?? 0);
            $returnKm = (float)($request->return_km ?? 0);
            $totalKm = $oneWayKm + $returnKm;

            // Charges and breakdown
            $perKmAmount = (float)($request->per_km_amount ?? 0);
            $driverAllowance = (float)($request->driver_allowance ?? 0);
            $platformCharges = (float)($request->platform_charges ?? 0);
            $acCharges = (float)($request->ac_charges ?? 0);
            $waitingCharges = (float)($request->waiting_charges ?? 0);
            $estimatedToll = (float)($request->estimated_toll ?? 0);
            $discountAmount = (float)($request->discount_amount ?? 0);

            $subtotal = $perKmAmount + $driverAllowance + $platformCharges + $acCharges + $waitingCharges + $estimatedToll;
            $totalAmount = max(0, $subtotal - $discountAmount);

            // Construct breakdown
            $chargesBreakdown = [
                [
                    'type' => 'Per KM',
                    'charge_type' => 'Per KM Rate',
                    'rate' => (float)($request->per_km_rate_unit ?? 0),
                    'distance' => $totalKm,
                    'amount' => $perKmAmount,
                ],
                [
                    'type' => 'Driver Allowance',
                    'charge_type' => 'Driver Allowance',
                    'amount' => $driverAllowance,
                ],
                [
                    'type' => 'Platform Charges',
                    'charge_type' => 'Platform Charges',
                    'amount' => $platformCharges,
                ]
            ];

            if ($request->has('is_ac')) {
                $chargesBreakdown[] = [
                    'type' => 'AC Charges',
                    'charge_type' => 'AC Charges',
                    'rate' => (float)($request->ac_rate_unit ?? 0),
                    'distance' => $totalKm,
                    'amount' => $acCharges,
                ];
            }

            if ($waitingCharges > 0) {
                $chargesBreakdown[] = [
                    'type' => 'Waiting Charges',
                    'charge_type' => 'Waiting Charges',
                    'minutes' => (float)($request->waiting_minutes ?? 0),
                    'amount' => $waitingCharges,
                ];
            }

            $chargesBreakdown[] = [
                'type' => 'Toll, Parking & Taxes',
                'status' => 'Included',
                'amount' => $estimatedToll,
            ];

            $order = CabOrder::create([
                'order_number' => CabOrder::generateOrderNumber(),
                'booking_status' => $request->booking_status,

                'customer_id' => $customer->id,
                'customer_name' => $customer->name,
                'customer_mobile' => $customer->mobile,

                'car_id' => $car->id,
                'car_name' => $car->car_name,

                'trip_type' => $request->trip_type,
                'stay_duration' => $request->stay_duration ?? 'short',
                'is_ac' => $request->has('is_ac'),

                // Pickup
                'pickup_address' => $request->pickup_address,
                'pickup_lat' => (float)($request->pickup_lat ?? 0),
                'pickup_lng' => (float)($request->pickup_lng ?? 0),

                // Drop
                'drop_address' => $request->drop_address,
                'drop_lat' => (float)($request->drop_lat ?? 0),
                'drop_lng' => (float)($request->drop_lng ?? 0),

                // Return
                'return_pickup_address' => $request->return_pickup_address,
                'return_pickup_lat' => (float)($request->return_pickup_lat ?? 0),
                'return_pickup_lng' => (float)($request->return_pickup_lng ?? 0),
                'return_drop_address' => $request->return_drop_address,
                'return_drop_lat' => (float)($request->return_drop_lat ?? 0),
                'return_drop_lng' => (float)($request->return_drop_lng ?? 0),

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
                'passengers' => $request->passengers,
                'bags' => $request->bags ?? 0,
                'notes_for_driver' => $request->notes_for_driver,

                // Charges
                'per_km_amount' => $perKmAmount,
                'driver_allowance' => $driverAllowance,
                'platform_charges' => $platformCharges,
                'ac_charges' => $acCharges,
                'waiting_charges' => $waitingCharges,
                'toll_tax' => 0,
                'estimated_toll' => $estimatedToll,
                'charges_breakdown' => $chargesBreakdown,

                // Coupon
                'coupon_code' => $request->coupon_code,
                'discount_amount' => $discountAmount,

                // Totals
                'subtotal' => $subtotal,
                'total_amount' => $totalAmount,

                // Payment
                'payment_status' => $request->payment_status,
                'payment_method' => $request->payment_method,
            ]);

            DB::commit();

            return redirect()->route('cabOrders')->with('success', 'Manual booking placed successfully! Order Number: ' . $order->order_number);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error placing manual booking: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified cab order details.
     */
    public function show($id)
    {
        $order = CabOrder::with(['customer', 'car'])->findOrFail($id);
        
        return view('cabOrders.show', compact('order'));
    }

    /**
     * Accept and approve the cab booking.
     */
    public function acceptBooking($id)
    {
        $order = CabOrder::findOrFail($id);
        $order->update(['booking_status' => 'confirmed']);
        
        return redirect()->route('cabOrders.show', $id)->with('success', 'Cab booking accepted & approved successfully!');
    }

    /**
     * Approve the payment for the cab booking.
     */
    public function approvePayment($id)
    {
        $order = CabOrder::findOrFail($id);
        $order->update(['payment_status' => 'paid']);
        
        return redirect()->route('cabOrders.show', $id)->with('success', 'Payment approved and marked as fully paid!');
    }

    /**
     * Cancel the cab booking.
     */
    public function cancelBooking($id)
    {
        $order = CabOrder::findOrFail($id);
        $order->update(['booking_status' => 'cancelled']);
        
        return redirect()->route('cabOrders.show', $id)->with('success', 'Cab booking cancelled successfully.');
    }

    /**
     * Generate and download the invoice PDF.
     */
    public function downloadInvoice($id)
    {
        $order = CabOrder::with(['customer', 'car'])->findOrFail($id);
        
        // Using fully qualified class name to ensure it resolves
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('invoices.cab-booking', compact('order'));
        
        return $pdf->download('Invoice-' . $order->order_number . '.pdf');
    }
}
