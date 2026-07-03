<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CabOrder;
use App\Models\Car;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class CabOrderWebController extends Controller
{
    /**
     * Display a listing of cab orders.
     */
    public function index()
    {
        $user = Auth::user();
        $query = CabOrder::with(['customer', 'car'])
            ->orderBy('created_at', 'desc');

        // Vendors only see their own bookings
        if ($user && $user->isVendor()) {
            $query->where('vendor_id', $user->id);
        }

        $orders = $query->paginate(15);
            
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
            'stay_charges' => 'nullable|numeric|min:0',
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
                if ($request->filled('customer_name') && $request->customer_name != $customer->name) {
                    $customer->name = $request->customer_name;
                    $isUpdated = true;
                }
                if ($request->filled('customer_mobile') && $request->customer_mobile != $customer->mobile) {
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
            $stayCharges = (float)($request->stay_charges ?? 0);

            $subtotal = $perKmAmount + $driverAllowance + $platformCharges + $acCharges + $waitingCharges + $estimatedToll + $stayCharges;
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

            if ($stayCharges > 0) {
                $chargesBreakdown[] = [
                    'type' => 'Stay Charges',
                    'charge_type' => 'Stay Charges',
                    'amount' => $stayCharges,
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

            // Normalize payment_status for DB enum: unpaid | partial | paid
            $rawStatus = $request->payment_status;
            if ($rawStatus === 'partially_paid') {
                $paymentStatus = 'partial';
            } elseif (in_array($rawStatus, ['unpaid', 'partial', 'paid'])) {
                $paymentStatus = $rawStatus;
            } else {
                $paymentStatus = 'unpaid';
            }

            // payment_method is now VARCHAR — store the exact value from the form
            $paymentMethod = $request->payment_method ?? null;

            $order = CabOrder::create([
                'order_number' => CabOrder::generateOrderNumber(),
                'booking_status' => $request->booking_status,

                'customer_id' => $customer->id,
                'customer_name' => $customer->name,
                'customer_mobile' => $customer->mobile,

                'car_id' => $car->id,
                'car_name' => $car->car_name,
                'vendor_id' => $car->vendor_id,

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
                'payment_status'   => $paymentStatus,
                'payment_method'   => $paymentMethod,
                'advance_payment'  => 0,
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
        $order = CabOrder::with(['customer', 'car', 'payments', 'activities'])->findOrFail($id);
        
        return view('cabOrders.show', compact('order'));
    }

    /**
     * Accept and approve the cab booking.
     */
    public function acceptBooking($id)
    {
        $order = CabOrder::findOrFail($id);
        $order->update(['booking_status' => 'confirmed']);
        
        // Log Activity
        \App\Models\CabOrderActivity::create([
            'cab_order_id' => $order->id,
            'event' => 'Booking Approved',
            'description' => "Booking has been accepted and approved manually.",
            'performed_by' => 'Admin',
        ]);
        
        return redirect()->route('cabOrders.show', $id)->with('success', 'Cab booking accepted & approved successfully!');
    }

    /**
     * Approve the payment for the cab booking.
     */
    public function approvePayment($id)
    {
        $order = CabOrder::findOrFail($id);
        $totalFare = (float)$order->total_amount;

        // Generate a receipt number for this manual full-payment approval
        $date      = now()->format('Ymd');
        $todayCount = \App\Models\CabOrderPayment::whereDate('created_at', now()->toDateString())->count();
        $sequence  = str_pad($todayCount + 1, 3, '0', STR_PAD_LEFT);
        $receiptNumber = "RCPT-{$date}-{$sequence}";

        // Create a CabOrderPayment record so it appears in Payment History table
        \App\Models\CabOrderPayment::create([
            'cab_order_id'    => $order->id,
            'receipt_number'  => $receiptNumber,
            'amount'          => $totalFare,
            'payment_method'  => $order->payment_method ?? 'Cash',
            'transaction_id'  => null,
            'screenshot_path' => null,
            'payment_status'  => 'paid',
            'notes'           => 'Marked as fully paid manually by admin.',
            'added_by'        => 'Admin',
        ]);

        // Re-aggregate total paid from all receipts
        $totalPaid = \App\Models\CabOrderPayment::where('cab_order_id', $order->id)
            ->whereIn('payment_status', ['paid', 'partially_paid'])
            ->sum('amount');

        // Update order: payment_status = paid, advance_payment = full fare
        $order->update([
            'payment_status'  => 'paid',
            'advance_payment' => $totalPaid,
        ]);

        // Log payment receipt activity
        \App\Models\CabOrderActivity::create([
            'cab_order_id' => $order->id,
            'event'        => 'Final Payment Received',
            'description'  => "Full payment of ₹" . number_format($totalFare, 2) . " marked as received. Receipt: {$receiptNumber}.",
            'performed_by' => 'Admin',
        ]);

        // Log payment status change activity
        \App\Models\CabOrderActivity::create([
            'cab_order_id' => $order->id,
            'event'        => 'Payment Status Updated',
            'description'  => "Payment status changed to PAID manually by Admin.",
            'performed_by' => 'Admin',
        ]);

        return redirect()->route('cabOrders.show', $id)->with('success', 'Payment approved and marked as fully paid! Receipt ' . $receiptNumber . ' generated.');
    }

    /**
     * Cancel the cab booking.
     */
    public function cancelBooking($id)
    {
        $order = CabOrder::findOrFail($id);
        $order->update(['booking_status' => 'cancelled']);
        
        // Log Activity
        \App\Models\CabOrderActivity::create([
            'cab_order_id' => $order->id,
            'event' => 'Booking Cancelled',
            'description' => "Booking has been cancelled manually.",
            'performed_by' => 'Admin',
        ]);
        
        return redirect()->route('cabOrders.show', $id)->with('success', 'Cab booking cancelled successfully.');
    }

    /**
     * Approve booking and configure payment structure.
     */
    public function approveAndSetupPayment(Request $request, $id)
    {
        $request->validate([
            'payment_collection_type' => 'required|in:full,advance,pay_later',
            'advance_type' => 'nullable|required_if:payment_collection_type,advance|in:percentage,fixed',
            'advance_percentage' => 'nullable|required_if:advance_type,percentage|integer|min:1|max:100',
            'advance_amount_val' => 'nullable|required_if:advance_type,fixed|numeric|min:0',
            'payment_method' => 'nullable|required_unless:payment_collection_type,pay_later|string',
            'transaction_id' => 'nullable|string',
            'payment_screenshot' => 'nullable|image|max:2048',
            'notes' => 'nullable|string',
        ]);

        $order = CabOrder::findOrFail($id);
        $totalFare = (float)$order->total_amount;

        $amountToCollect = 0;
        $paymentStatus = 'unpaid';

        if ($request->payment_collection_type === 'full') {
            $amountToCollect = $totalFare;
            $paymentStatus = 'paid';
        } elseif ($request->payment_collection_type === 'advance') {
            if ($request->advance_type === 'percentage') {
                $percentage = (int)$request->advance_percentage;
                $amountToCollect = round(($totalFare * $percentage) / 100, 2);
            } else {
                $amountToCollect = (float)$request->advance_amount_val;
            }
            $paymentStatus = ($amountToCollect >= $totalFare) ? 'paid' : 'partial';
        }

        // Handle screenshot upload
        $screenshotPath = null;
        if ($request->hasFile('payment_screenshot')) {
            $file = $request->file('payment_screenshot');
            $filename = time() . '_' . $file->getClientOriginalName();
            $screenshotPath = $file->storeAs('payment_screenshots', $filename, 'public');
        }

        // Update booking details
        // advance_payment tracks total collected; add new amount on top of any prior collection
        $existingAdvance = (float)($order->advance_payment ?? 0);
        $newTotalAdvance = $existingAdvance + $amountToCollect;

        $order->update([
            'booking_status'  => 'confirmed',
            'payment_status'  => $paymentStatus,
            'payment_method'  => $request->payment_method ?: null,
            'advance_payment' => $newTotalAdvance,
        ]);

        // Log Approval Activity
        \App\Models\CabOrderActivity::create([
            'cab_order_id' => $order->id,
            'event' => 'Booking Approved',
            'description' => "Booking has been approved and set to Confirmed status. Payment setup: " . ucfirst($request->payment_collection_type) . " Payment.",
            'performed_by' => 'Admin',
        ]);

        // If payment collection is made, record payment and receipt
        if ($amountToCollect > 0) {
            // Generate receipt
            $date = now()->format('Ymd');
            $todayCount = \App\Models\CabOrderPayment::whereDate('created_at', now()->toDateString())->count();
            $sequence = str_pad($todayCount + 1, 3, '0', STR_PAD_LEFT);
            $receiptNumber = "RCPT-{$date}-{$sequence}";

            \App\Models\CabOrderPayment::create([
                'cab_order_id'   => $order->id,
                'receipt_number' => $receiptNumber,
                'amount'         => $amountToCollect,
                'payment_method' => $request->payment_method ?? 'Cash',
                'transaction_id' => $request->transaction_id,
                'screenshot_path'=> $screenshotPath,
                'payment_status' => $paymentStatus === 'partial' ? 'partially_paid' : $paymentStatus,
                'notes'          => $request->notes,
                'added_by'       => 'Admin',
            ]);

            \App\Models\CabOrderActivity::create([
                'cab_order_id' => $order->id,
                'event' => 'Advance Payment Received',
                'description' => "Advance payment of ₹" . number_format($amountToCollect, 2) . " received via " . ($request->payment_method ?? 'Cash') . ". Receipt: {$receiptNumber}.",
                'performed_by' => 'Admin',
            ]);
        }

        return back()->with('success', 'Booking approved and payment configuration set successfully!');
    }

    /**
     * Record a new manual transaction / payment collection.
     */
    public function collectPayment(Request $request, $id)
    {
        $request->validate([
            'payment_amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string',
            'transaction_id' => 'nullable|string',
            'payment_screenshot' => 'nullable|image|max:2048',
            'notes' => 'nullable|string',
            'payment_status' => 'required|in:pending,partially_paid,paid,failed,refunded',
        ]);

        $order = CabOrder::findOrFail($id);

        $screenshotPath = null;
        if ($request->hasFile('payment_screenshot')) {
            $file = $request->file('payment_screenshot');
            $filename = time() . '_' . $file->getClientOriginalName();
            $screenshotPath = $file->storeAs('payment_screenshots', $filename, 'public');
        }

        // Generate receipt
        $date = now()->format('Ymd');
        $todayCount = \App\Models\CabOrderPayment::whereDate('created_at', now()->toDateString())->count();
        $sequence = str_pad($todayCount + 1, 3, '0', STR_PAD_LEFT);
        $receiptNumber = "RCPT-{$date}-{$sequence}";

        \App\Models\CabOrderPayment::create([
            'cab_order_id' => $order->id,
            'receipt_number' => $receiptNumber,
            'amount' => $request->payment_amount,
            'payment_method' => $request->payment_method,
            'transaction_id' => $request->transaction_id,
            'screenshot_path' => $screenshotPath,
            'payment_status' => $request->payment_status,
            'notes' => $request->notes,
            'added_by' => 'Admin',
        ]);

        // Re-sum ALL paid / partially_paid receipts to get the definitive advance_payment total
        $totalPaid = \App\Models\CabOrderPayment::where('cab_order_id', $order->id)
            ->whereIn('payment_status', ['paid', 'partially_paid'])
            ->sum('amount');

        // Determine the order-level payment_status based on aggregated total
        $totalFare = (float)$order->total_amount;
        if ($totalPaid <= 0) {
            $orderPaymentStatus = 'unpaid';
        } elseif ($totalPaid >= $totalFare) {
            $orderPaymentStatus = 'paid';
        } else {
            $orderPaymentStatus = 'partial';
        }

        // If this specific record is failed/refunded, don't auto-upgrade the order status
        if (in_array($request->payment_status, ['failed', 'refunded'])) {
            // Re-compute excluding this new record (it was just created)
            $confirmedTotal = \App\Models\CabOrderPayment::where('cab_order_id', $order->id)
                ->whereIn('payment_status', ['paid', 'partially_paid'])
                ->sum('amount');
            $orderPaymentStatus = $confirmedTotal >= $totalFare ? 'paid'
                : ($confirmedTotal > 0 ? 'partial' : 'unpaid');
        }

        $order->update([
            'payment_status'  => $orderPaymentStatus,
            'advance_payment' => $totalPaid,
        ]);

        \App\Models\CabOrderActivity::create([
            'cab_order_id' => $order->id,
            'event' => 'Payment Received',
            'description' => "Amount of ₹" . number_format($request->payment_amount, 2) . " received via {$request->payment_method}. Status: " . ucfirst(str_replace('_', ' ', $request->payment_status)) . ". Receipt: {$receiptNumber}.",
            'performed_by' => 'Admin',
        ]);

        return back()->with('success', 'Payment of ₹' . number_format($request->payment_amount, 2) . ' recorded successfully!');
    }

    /**
     * Assign a driver to the cab booking.
     */
    public function assignDriver(Request $request, $id)
    {
        $request->validate([
            'driver_name' => 'required|string|max:255',
            'driver_mobile' => 'required|string|max:20',
        ]);

        $order = CabOrder::findOrFail($id);
        $order->update([
            'driver_name' => $request->driver_name,
            'driver_mobile' => $request->driver_mobile,
            'booking_status' => 'driver_assigned',
        ]);

        \App\Models\CabOrderActivity::create([
            'cab_order_id' => $order->id,
            'event' => 'Driver Assigned',
            'description' => "Driver {$request->driver_name} ({$request->driver_mobile}) has been assigned to this trip.",
            'performed_by' => 'Admin',
        ]);

        return back()->with('success', 'Driver assigned successfully!');
    }

    /**
     * Explicitly update the booking status.
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,accepted,confirmed,driver_assigned,started,completed,cancelled',
        ]);

        $order = CabOrder::findOrFail($id);
        $oldStatus = $order->booking_status;
        $order->update(['booking_status' => $request->status]);

        // Auto calculate vendor commission on booking completion
        if ($request->status === 'completed' && $order->vendor_id) {
            $vendor = \App\Models\User::find($order->vendor_id);
            if ($vendor && $vendor->isVendor()) {
                $totalAmount = (float)$order->total_amount;
                $commType = $vendor->commission_type ?: 'percentage';
                
                if ($commType === 'percentage') {
                    $commRate = (float)($vendor->commission_percentage !== null ? $vendor->commission_percentage : 10.00);
                    $commAmount = round(($totalAmount * $commRate) / 100, 2);
                } else {
                    $commRate = (float)($vendor->flat_commission !== null ? $vendor->flat_commission : 0.00);
                    $commAmount = round($commRate, 2);
                }
                
                $vendorEarnings = max(0, $totalAmount - $commAmount);

                $order->update([
                    'commission_type' => $commType,
                    'commission_rate' => $commRate,
                    'commission_amount' => $commAmount,
                    'vendor_earnings' => $vendorEarnings,
                ]);

                // Also log activity
                \App\Models\CabOrderActivity::create([
                    'cab_order_id' => $order->id,
                    'event' => 'Commission Calculated',
                    'description' => "Vendor commission calculated: " . ucfirst($commType) . " rate {$commRate}. Commission: ₹{$commAmount}, Vendor Earnings: ₹{$vendorEarnings}.",
                    'performed_by' => 'System',
                ]);
            }
        }

        // Map status to timeline event name
        $eventMap = [
            'pending' => 'Booking Set to Pending',
            'accepted' => 'Booking Accepted',
            'confirmed' => 'Booking Confirmed',
            'driver_assigned' => 'Driver Assigned',
            'started' => 'Trip Started',
            'completed' => 'Trip Completed',
            'cancelled' => 'Booking Cancelled',
        ];

        $eventName = $eventMap[$request->status] ?? 'Booking Status Updated';

        \App\Models\CabOrderActivity::create([
            'cab_order_id' => $order->id,
            'event' => $eventName,
            'description' => "Status changed from " . ucfirst(str_replace('_', ' ', $oldStatus)) . " to " . ucfirst(str_replace('_', ' ', $request->status)) . ".",
            'performed_by' => 'Admin',
        ]);

        return back()->with('success', 'Booking status updated successfully!');
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
