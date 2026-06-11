<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice - {{ $order->order_number }}</title>
    <style>
        @page {
            size: a4 portrait;
            margin: 10mm 12mm 10mm 12mm;
        }

        body {
            font-family: 'DejaVu Sans', Arial, 'Helvetica Neue', Helvetica, sans-serif;
            color: #334155;
            background-color: #ffffff;
            line-height: 1.35;
            margin: 0;
            padding: 0;
            font-size: 11px;
        }

        .page {
            position: relative;
            page-break-after: always;
            clear: both;
        }

        .page:last-child {
            page-break-after: avoid;
        }

        .clear {
            clear: both;
        }

        /* Header Styles */
        .header-container {
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 2px solid #b59410;
        }

        .logo-box {
            padding: 0;
        }

        .logo-badge {
            background-color: #1e3a8a;
            color: #ffffff;
            font-size: 18px;
            font-weight: 800;
            width: 42px;
            height: 42px;
            line-height: 38px;
            text-align: center;
            border-radius: 6px;
            border: 2px solid #b59410;
        }

        .invoice-badge {
            display: inline-block;
            background-color: #eff6ff;
            color: #1e3a8a;
            border: 1px solid #bfdbfe;
            font-size: 9px;
            font-weight: 800;
            text-transform: uppercase;
            padding: 3px 8px;
            border-radius: 4px;
            letter-spacing: 0.5px;
        }

        /* Status row */
        .status-row {
            margin-top: 8px;
            margin-bottom: 10px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 6px 12px;
        }

        .status-chip {
            display: inline-block;
            font-size: 8.5px;
            font-weight: 700;
            text-transform: uppercase;
            padding: 2px 6px;
            border-radius: 4px;
            margin-left: 4px;
            letter-spacing: 0.3px;
        }

        .badge-confirmed { color: #1e3a8a; background-color: #eff6ff; border: 1px solid #bfdbfe; }
        .badge-completed { color: #166534; background-color: #f0fdf4; border: 1px solid #bbf7d0; }
        .badge-pending { color: #854d0e; background-color: #fef9c3; border: 1px solid #fef08a; }
        .badge-cancelled { color: #991b1b; background-color: #fee2e2; border: 1px solid #fca5a5; }
        .badge-active { color: #0369a1; background-color: #f0f9ff; border: 1px solid #bae6fd; }
        .badge-partial { color: #c2410c; background-color: #fff7ed; border: 1px solid #ffedd5; }
        .badge-refunded { color: #4338ca; background-color: #e0e7ff; border: 1px solid #c7d2fe; }

        /* Grid columns */
        .row {
            clear: both;
            width: 100%;
        }

        .col-left {
            width: 49%;
            float: left;
        }

        .col-right {
            width: 49%;
            float: right;
        }

        /* Cards */
        .card {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 10px 12px;
            margin-bottom: 8px;
        }

        .card-title {
            font-size: 9.5px;
            font-weight: 700;
            text-transform: uppercase;
            color: #1e3a8a;
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 4px;
            margin-top: 0;
            margin-bottom: 6px;
            letter-spacing: 0.5px;
        }

        /* Detail Tables */
        .details-table {
            width: 100%;
            border-collapse: collapse;
        }

        .details-table th, .details-table td {
            padding: 3px 0;
            font-size: 9.5px;
            vertical-align: top;
        }

        .details-table th {
            width: 38%;
            color: #64748b;
            font-weight: 600;
            text-align: left;
        }

        .details-table td {
            width: 62%;
            color: #0f172a;
            font-weight: 700;
            text-align: left;
        }

        /* Trip Table */
        .trip-table {
            width: 100%;
            border-collapse: collapse;
        }

        .trip-label {
            font-size: 8.5px;
            color: #64748b;
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 1px;
        }

        .trip-val {
            font-size: 10.5px;
            font-weight: 700;
            color: #0f172a;
        }

        /* Fare table */
        .fare-table {
            width: 100%;
            border-collapse: collapse;
        }

        .fare-table td {
            padding: 2.5px 0;
            font-size: 9.5px;
            color: #475569;
            font-weight: 600;
            font-family: 'DejaVu Sans', Arial, 'Helvetica Neue', Helvetica, sans-serif;
        }

        .fare-table td:last-child {
            text-align: right;
            color: #0f172a;
            font-weight: 700;
        }

        /* Timeline */
        .timeline-dot {
            width: 16px;
            height: 16px;
            line-height: 12px;
            border-radius: 50%;
            font-size: 8px;
            font-weight: 800;
            text-align: center;
            margin: 0 auto 3px auto;
            border: 2px solid #e2e8f0;
        }

        .dot-active {
            background-color: #1e3a8a;
            color: #ffffff;
            border-color: #b59410;
        }

        .dot-inactive {
            background-color: #f1f5f9;
            color: #94a3b8;
            border-color: #e2e8f0;
        }

        .timeline-line {
            height: 2px;
            width: 100%;
            margin-top: -10px;
        }

        .line-active {
            background-color: #b59410;
        }

        .line-inactive {
            background-color: #e2e8f0;
        }

        .timeline-label {
            font-size: 7.5px;
            font-weight: 700;
            line-height: 1.1;
        }

        .label-active {
            color: #1e3a8a;
        }

        .label-inactive {
            color: #94a3b8;
        }

        /* Notes List */
        .notes-list {
            margin: 0;
            padding-left: 12px;
            font-size: 9px;
            color: #475569;
            line-height: 1.4;
        }

        .notes-list li {
            margin-bottom: 3px;
        }

        /* Offer Table */
        .offer-table {
            width: 100%;
            border-collapse: collapse;
        }

        .offer-table th, .offer-table td {
            border: 1px solid #e2e8f0;
            padding: 3px 6px;
            font-size: 9px;
            text-align: left;
        }

        .offer-table th {
            background-color: #f1f5f9;
            color: #1e3a8a;
            font-weight: 700;
            text-transform: uppercase;
        }

        .offer-table td {
            background-color: #ffffff;
            color: #334155;
        }

        .reward-highlight {
            color: #b59410;
            font-weight: 700;
        }

        .currency, .rupee {
            font-family: 'DejaVu Sans', Arial, 'Helvetica Neue', Helvetica, sans-serif;
        }
    </style>
</head>
<body>
    @php
        $settings = \App\Models\Settings::first();
        $currencySymbol = trim($settings->currency ?? '₹');
        if ($currencySymbol === '') {
            $currencySymbol = '₹';
        }
        $booking_status = strtolower($order->booking_status);
        $payment_status = strtolower($order->payment_status);
        
        $booking_badge_class = 'badge-pending';
        $booking_status_text = 'Pending';
        if ($booking_status === 'confirmed' || $booking_status === 'accepted') {
            $booking_badge_class = 'badge-confirmed';
            $booking_status_text = 'Confirmed';
        } elseif ($booking_status === 'completed') {
            $booking_badge_class = 'badge-completed';
            $booking_status_text = 'Completed';
        } elseif ($booking_status === 'cancelled') {
            $booking_badge_class = 'badge-cancelled';
            $booking_status_text = 'Cancelled';
        } elseif ($booking_status === 'driver_assigned' || $booking_status === 'started' || $booking_status === 'on_the_way') {
            $booking_badge_class = 'badge-active';
            $booking_status_text = 'Confirmed';
        }

        $payment_badge_class = 'badge-pending';
        $payment_status_text = 'Pending';
        if ($payment_status === 'paid') {
            $payment_badge_class = 'badge-completed';
            $payment_status_text = 'Paid';
        } elseif ($payment_status === 'partial' || $payment_status === 'partially_paid') {
            $payment_badge_class = 'badge-partial';
            $payment_status_text = 'Partially Paid';
        } elseif ($payment_status === 'refunded') {
            $payment_badge_class = 'badge-refunded';
            $payment_status_text = 'Refunded';
        }
    @endphp

    <!-- Page 1 -->
    <div class="page">
        <div class="header-container">
            <div style="width: 55%; float: left;">
                <div class="logo-box">
                    <table style="width: 100%; border-collapse: collapse; border: none;">
                        <tr>
                            <td style="padding: 0; vertical-align: middle;">
                                @if($settings && $settings->logo && file_exists(public_path('images/logo/' . $settings->logo)))
                                    <img src="{{ public_path('images/logo/' . $settings->logo) }}" alt="Logo" style="height: 42px; max-width: 140px; display: block; margin-right: 8px;" />
                                @else
                                    <div class="logo-badge" style="margin-right: 8px;">SBD</div>
                                @endif
                            </td>
                            <td style="vertical-align: middle;">
                                <div style="font-size: 14px; font-weight: 800; color: #1e3a8a; line-height: 1.15; text-transform: uppercase;">Sampurna Bharat Darshan</div>
                                <div style="font-size: 9px; font-weight: 700; color: #b59410; letter-spacing: 0.3px; line-height: 1.15; text-transform: uppercase;">Maa Sharada Tour & Travels (SBD Group)</div>
                            </td>
                        </tr>
                    </table>
                </div>
                <div style="font-size: 8.5px; line-height: 1.35; color: #64748b; margin-top: 6px;">
                    {{ $settings->address ?? 'SECL Jamuna Colliery, Dist. Anuppur, Madhya Pradesh 484444' }}<br>
                    <strong>Support:</strong> {{ $settings->contact ?? '+91 82248 94319, +91 95756 67241' }} | <strong>GSTIN:</strong> 23AXXCSXXXXX1ZX<br>
                    <strong>Email:</strong> {{ $settings->email ?? 'infosbdbooking@gmail.com' }} | <strong>Website:</strong> sbdbooking.com
                </div>
            </div>
            <div style="width: 42%; float: right; text-align: right;">
                <div class="invoice-badge">Booking Confirmation</div>
                <div style="margin-top: 6px; font-size: 10px; color: #475569; line-height: 1.45;">
                    <strong>Invoice No:</strong> <span style="color: #0f172a; font-weight: 700;">{{ $order->order_number }}</span><br>
                    <strong>Booking ID:</strong> #{{ $order->id }}<br>
                    <strong>Booking Date:</strong> {{ $order->created_at->format('d M Y, h:i A') }}
                </div>
            </div>
            <div class="clear"></div>
        </div>

        <div class="status-row">
            <table style="width: 100%; border-collapse: collapse; border: none;">
                <tr>
                    <td style="width: 50%; padding: 0;">
                        <span style="color: #475569; font-size: 10px; font-weight: 600;">Booking Status:</span>
                        <span class="status-chip {{ $booking_badge_class }}">{{ $booking_status_text }}</span>
                    </td>
                    <td style="width: 50%; padding: 0; text-align: right;">
                        <span style="color: #475569; font-size: 10px; font-weight: 600;">Payment Status:</span>
                        <span class="status-chip {{ $payment_badge_class }}">{{ $payment_status_text }}</span>
                    </td>
                </tr>
            </table>
        </div>

        <div class="row">
            <!-- Customer Details Card -->
            <div class="col-left">
                <div class="card" style="min-height: 98px;">
                    <h3 class="card-title">Customer Details</h3>
                    <table class="details-table">
                        <tr>
                            <th>Customer Name</th>
                            <td>{{ $order->customer_name }}</td>
                        </tr>
                        <tr>
                            <th>Mobile Number</th>
                            <td>{{ $order->customer_mobile }}</td>
                        </tr>
                        <tr>
                            <th>Email Address</th>
                            <td>{{ optional($order->customer)->email ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Account Status</th>
                            <td>
                                @if($order->customer_id)
                                    <span style="color: #166534; font-weight: bold;">Registered User</span>
                                @else
                                    <span style="color: #64748b; font-weight: bold;">Guest User</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Vehicle & Driver Card -->
            <div class="col-right">
                <div class="card" style="min-height: 98px;">
                    <h3 class="card-title">Vehicle & Driver Details</h3>
                    <table class="details-table">
                        <tr>
                            <th>Vehicle Model</th>
                            <td>{{ strtoupper($order->car_name) }}</td>
                        </tr>
                        <tr>
                            <th>Vehicle Type</th>
                            <td>{{ $order->is_ac ? 'AC Cab' : 'Non-AC Cab' }}</td>
                        </tr>
                        <tr>
                            <th>Driver Name</th>
                            <td>{{ $order->driver_name ?? 'SBD Assigned Driver' }}</td>
                        </tr>
                        <tr>
                            <th>Driver Contact</th>
                            <td>{{ $order->driver_mobile ?? 'N/A' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="clear"></div>
        </div>

        <!-- Trip Details Card -->
        <div class="card" style="margin-top: 2px;">
            <h3 class="card-title">Trip Details</h3>
            <table class="trip-table">
                <tr>
                    <td style="width: 33.3%; vertical-align: top;">
                        <div class="trip-label">Trip Type</div>
                        <div class="trip-val">{{ str_replace('_', ' ', strtoupper($order->trip_type)) }}</div>
                    </td>
                    <td style="width: 33.3%; vertical-align: top;">
                        <div class="trip-label">Pickup Date & Time</div>
                        <div class="trip-val">
                            {{ \Carbon\Carbon::parse($order->pickup_date)->format('d M Y') }}<br>
                            <span style="font-size: 9px; color: #64748b; font-weight: 500;">
                                {{ strpos($order->pickup_time, ' to ') !== false ? $order->pickup_time : \Carbon\Carbon::parse($order->pickup_time)->format('h:i A') }}
                            </span>
                        </div>
                    </td>
                    <td style="width: 33.3%; vertical-align: top;">
                        <div class="trip-label">Total Distance</div>
                        <div class="trip-val">{{ $order->total_km }} KM</div>
                        <div style="font-size: 8px; color: #64748b; font-weight: 500; margin-top: 1px;">
                            ({{ $order->one_way_km }} km Run + {{ $order->return_km }} km Return)
                        </div>
                    </td>
                </tr>
                @if($order->trip_type === 'round_trip')
                <tr>
                    <td colspan="3" style="padding-top: 6px; border-top: 1px solid #f1f5f9; margin-top: 6px;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <tr>
                                <td style="width: 50%; padding: 0;">
                                    <div class="trip-label">Return Date & Time</div>
                                    <div class="trip-val" style="font-size: 10px;">
                                        {{ \Carbon\Carbon::parse($order->return_date)->format('d M Y') }} at 
                                        {{ strpos($order->return_time, ' to ') !== false ? $order->return_time : \Carbon\Carbon::parse($order->return_time)->format('h:i A') }}
                                    </div>
                                </td>
                                <td style="width: 50%; padding: 0;">
                                    <div class="trip-label">Passengers & Bags</div>
                                    <div class="trip-val" style="font-size: 10px;">
                                        {{ $order->passengers }} Passengers | {{ $order->bags }} Bags
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                @else
                <tr>
                    <td colspan="3" style="padding-top: 6px; border-top: 1px solid #f1f5f9; margin-top: 6px;">
                        <div class="trip-label">Passengers & Bags</div>
                        <div class="trip-val" style="font-size: 10px;">
                            {{ $order->passengers }} Passengers | {{ $order->bags }} Bags
                        </div>
                    </td>
                </tr>
                @endif
                <tr>
                    <td colspan="3" style="padding-top: 6px; border-top: 1px solid #f1f5f9; margin-top: 6px;">
                        <div class="trip-label">Route Details</div>
                        <div class="trip-val" style="font-size: 10px; line-height: 1.4; font-weight: normal;">
                            <strong style="color: #1e3a8a;">Pickup:</strong> {{ $order->pickup_address }}<br>
                            <strong style="color: #b59410;">Drop-off:</strong> {{ $order->drop_address }}
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="card w-full"    style="margin-top: 2px;">
            <!-- Fare Summary Card -->
            <div>
                <div style="min-height: 185px; padding-bottom: 5px;">
                    <h3 class="card-title">Fare Summary</h3>
                    <table class="fare-table">
                        @if(is_array($order->charges_breakdown) || is_object($order->charges_breakdown))
                            @foreach($order->charges_breakdown as $item)
                                @php
                                    $amount = isset($item['amount']) ? (float)$item['amount'] : 0;
                                    $item_name = isset($item['type']) ? $item['type'] : (isset($item['charge_type']) ? $item['charge_type'] : 'Charge');
                                @endphp
                                @if($amount > 0)
                                <tr>
                                    <td>
                                        {{ $item_name }}
                                        @if(isset($item['rate']) && $item['rate'] > 0 && isset($item['distance']) && $item['distance'] > 0)
                                            <span style="font-size: 7.5px; color: #64748b; font-weight: normal;">(<span class="currency">{{ $currencySymbol }}</span>{{ $item['rate'] }}/km)</span>
                                        @endif
                                    </td>
                                    <td><span class="currency">{{ $currencySymbol }}</span>{{ number_format($amount, 2) }}</td>
                                </tr>
                                @endif
                            @endforeach
                        @else
                            @if($order->per_km_amount > 0)
                            <tr>
                                <td>Base KM Fare</td>
                                <td><span class="currency">{{ $currencySymbol }}</span>{{ number_format($order->per_km_amount, 2) }}</td>
                            </tr>
                            @endif
                            @if($order->driver_allowance > 0)
                            <tr>
                                <td>Driver Allowance</td>
                                <td><span class="currency">{{ $currencySymbol }}</span>{{ number_format($order->driver_allowance, 2) }}</td>
                            </tr>
                            @endif
                            @if($order->ac_charges > 0)
                            <tr>
                                <td>AC Charges</td>
                                <td><span class="currency">{{ $currencySymbol }}</span>{{ number_format($order->ac_charges, 2) }}</td>
                            </tr>
                            @endif
                            @if($order->platform_charges > 0)
                            <tr>
                                <td>Platform Charges</td>
                                <td><span class="currency">{{ $currencySymbol }}</span>{{ number_format($order->platform_charges, 2) }}</td>
                            </tr>
                            @endif
                            @if($order->waiting_charges > 0)
                            <tr>
                                <td>Waiting Charges</td>
                                <td><span class="currency">{{ $currencySymbol }}</span>{{ number_format($order->waiting_charges, 2) }}</td>
                            </tr>
                            @endif
                            @if($order->estimated_toll > 0)
                            <tr>
                                <td>Toll & Parking (Est.)</td>
                                <td><span class="currency">{{ $currencySymbol }}</span>{{ number_format($order->estimated_toll, 2) }}</td>
                            </tr>
                            @endif
                        @endif
                        
                        <tr>
                            <td colspan="2" style="padding: 0;"><div style="border-top: 1px solid #f1f5f9; margin: 3px 0;"></div></td>
                        </tr>
                        
                        <tr>
                            <td style="font-weight: 700;">Sub Total</td>
                            <td style="font-weight: 700;"><span class="currency">{{ $currencySymbol }}</span>{{ number_format($order->subtotal, 2) }}</td>
                        </tr>
                        
                        @if($order->discount_amount > 0)
                        <tr style="color: #b91c1c;">
                            <td>Discount ({{ $order->coupon_code }})</td>
                            <td>- <span class="currency">{{ $currencySymbol }}</span>{{ number_format($order->discount_amount, 2) }}</td>
                        </tr>
                        @endif
                        
                        <tr>
                            <td style="font-size: 10.5px; font-weight: 800; color: #1e3a8a;">Final Amount</td>
                            <td><span class="currency"> {{ $currencySymbol }}</span>{{ number_format($order->total_amount, 2) }}</td>
                        </tr>
                        
                        <tr>
                            <td style="color: #166534; font-weight: 600;">Advance Paid</td>
                            <td>- <span class="currency">{{ $currencySymbol }}</span>{{ number_format($order->advance_payment ?? 0, 2) }}</td>
                        </tr>
                        
                        <tr class="fare-final">
                            <td>Remaining Amount</td>
                            <td><span class="currency">{{ $currencySymbol }}</span>{{ number_format($order->total_amount - ($order->advance_payment ?? 0), 2) }}</td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="clear"></div>
        </div>

        @php
            $step1 = true;
            $step2 = in_array($booking_status, ['confirmed', 'accepted', 'driver_assigned', 'started', 'completed']);
            $step3 = ($payment_status === 'paid' || $payment_status === 'partial' || $payment_status === 'partially_paid');
            $step4 = in_array($booking_status, ['driver_assigned', 'started', 'completed']);
            $step5 = in_array($booking_status, ['started', 'completed']);
            $step6 = ($booking_status === 'completed');
        @endphp

        <!-- Trip Timeline -->
        <div class="card" style="margin-top: 2px; padding: 10px 10px 8px 10px;">
            <h3 class="card-title" style="margin-bottom: 8px;">Trip Timeline</h3>
            <table style="width: 100%; border-collapse: collapse; border: none; table-layout: fixed;">
                <tr>
                    <td style="text-align: center; vertical-align: top; padding: 0;">
                        <div class="timeline-dot dot-active">1</div>
                        <div class="timeline-label label-active">Booking<br>Created</div>
                    </td>
                    
                    <td style="vertical-align: middle; padding: 0;">
                        <div class="timeline-line {{ $step2 ? 'line-active' : 'line-inactive' }}"></div>
                    </td>
                    
                    <td style="text-align: center; vertical-align: top; padding: 0;">
                        <div class="timeline-dot {{ $step2 ? 'dot-active' : 'dot-inactive' }}">2</div>
                        <div class="timeline-label {{ $step2 ? 'label-active' : 'label-inactive' }}">Booking<br>Confirmed</div>
                    </td>
                    
                    <td style="vertical-align: middle; padding: 0;">
                        <div class="timeline-line {{ $step3 ? 'line-active' : 'line-inactive' }}"></div>
                    </td>
                    
                    <td style="text-align: center; vertical-align: top; padding: 0;">
                        <div class="timeline-dot {{ $step3 ? 'dot-active' : 'dot-inactive' }}">3</div>
                        <div class="timeline-label {{ $step3 ? 'label-active' : 'label-inactive' }}">Payment<br>Received</div>
                    </td>
                    
                    <td style="vertical-align: middle; padding: 0;">
                        <div class="timeline-line {{ $step4 ? 'line-active' : 'line-inactive' }}"></div>
                    </td>
                    
                    <td style="text-align: center; vertical-align: top; padding: 0;">
                        <div class="timeline-dot {{ $step4 ? 'dot-active' : 'dot-inactive' }}">4</div>
                        <div class="timeline-label {{ $step4 ? 'label-active' : 'label-inactive' }}">Driver<br>Assigned</div>
                    </td>
                    
                    <td style="vertical-align: middle; padding: 0;">
                        <div class="timeline-line {{ $step5 ? 'line-active' : 'line-inactive' }}"></div>
                    </td>
                    
                    <td style="text-align: center; vertical-align: top; padding: 0;">
                        <div class="timeline-dot {{ $step5 ? 'dot-active' : 'dot-inactive' }}">5</div>
                        <div class="timeline-label {{ $step5 ? 'label-active' : 'label-inactive' }}">Trip<br>Started</div>
                    </td>
                    
                    <td style="vertical-align: middle; padding: 0;">
                        <div class="timeline-line {{ $step6 ? 'line-active' : 'line-inactive' }}"></div>
                    </td>
                    
                    <td style="text-align: center; vertical-align: top; padding: 0;">
                        <div class="timeline-dot {{ $step6 ? 'dot-active' : 'dot-inactive' }}">6</div>
                        <div class="timeline-label {{ $step6 ? 'label-active' : 'label-inactive' }}">Trip<br>Completed</div>
                    </td>
                </tr>
            </table>
        </div>

        <div style="margin-top: 10px; border-top: 1px solid #e2e8f0; padding-top: 6px; text-align: center; font-size: 8.5px; color: #64748b; font-weight: 600;">
            This is an electronically generated travel invoice document. No physical signature is required.<br>
            Please turn over for Terms & Conditions, Cancellation Policies, and Network Branch Contact Details.
        </div>
    </div>

    <!-- Page 2 -->
    <div class="page">
        <div class="header-container" style="margin-bottom: 15px;">
            <div style="width: 55%; float: left;">
                <div class="logo-box">
                    <table style="width: 100%; border-collapse: collapse; border: none;">
                        <tr>
                            <td style="padding: 0; vertical-align: middle;">
                                @if($settings && $settings->logo && file_exists(public_path('images/logo/' . $settings->logo)))
                                    <img src="{{ public_path('images/logo/' . $settings->logo) }}" alt="Logo" style="height: 34px; max-width: 120px; display: block; margin-right: 6px;" />
                                @else
                                    <div class="logo-badge" style="width: 34px; height: 34px; line-height: 30px; font-size: 15px; margin-right: 6px;">SBD</div>
                                @endif
                            </td>
                            <td style="vertical-align: middle;">
                                <div style="font-size: 12px; font-weight: 800; color: #1e3a8a; line-height: 1.15; text-transform: uppercase;">Sampurna Bharat Darshan</div>
                                <div style="font-size: 8px; font-weight: 700; color: #b59410; letter-spacing: 0.3px; line-height: 1.15; text-transform: uppercase;">Maa Sharada Tour & Travels (SBD Group)</div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <div style="width: 42%; float: right; text-align: right; padding-top: 6px;">
                <span style="font-size: 11px; font-weight: 800; color: #1e3a8a; text-transform: uppercase; letter-spacing: 0.8px;">Terms, Network & Offers</span>
            </div>
            <div class="clear"></div>
        </div>

        <div class="row">
            <!-- Important Notes Card -->
            <div class="col-left" style="width: 53%;">
                <div class="card" style="border-left: 4px solid #b59410; background-color: #fffdf9; min-height: 175px;">
                    <h3 class="card-title" style="color: #b59410;">Terms & Conditions</h3>
                    <ul class="notes-list">
                        <li><strong>Journey Date:</strong> {{ \Carbon\Carbon::parse($order->pickup_date)->format('d M Y') }}</li>
                        <li><strong>Departure:</strong> {{ strpos($order->pickup_time, ' to ') !== false ? $order->pickup_time : \Carbon\Carbon::parse($order->pickup_time)->format('h:i A') }}</li>
                        <li><strong>Vehicle Type:</strong> {{ $order->car_name }} ({{ $order->is_ac ? 'AC Cab' : 'Non-AC' }})</li>
                        <li><strong>Extra Distance:</strong> <span class="currency">{{ $currencySymbol }}</span>{{ $order->per_km_amount }}/KM applicable for any run exceeding the trip estimate.</li>
                        <li><strong>Toll & State Taxes:</strong> Toll charges, border tax, permit fees, and parking charges are charged separately or payable by customer directly.</li>
                        <li><strong>Driver Compliance:</strong> Guest safety is priority. Please do not force driver for overspeeding. Driver reserves right to halt if unsafe.</li>
                        <li><strong>Schedule Limit:</strong> Vehicle is committed only for route and destination listed on Page 1.</li>
                    </ul>
                </div>

                <div class="card" style="border-left: 4px solid #ef4444; background-color: #fef2f2; margin-top: 8px;">
                    <h3 class="card-title" style="color: #b91c1c;">Cancellation & Refund Policy</h3>
                    <ul class="notes-list" style="color: #991b1b;">
                        <li><strong>Date Changes:</strong> Booking journey date can be modified up to 24 hours prior to pickup, subject to vehicle availability.</li>
                        <li><strong>Cancellations:</strong> No cancellations or cash refunds are applicable post confirmation.</li>
                        <li><strong>Advance Forfeit:</strong> Booking advance is non-refundable in the event of customer cancellation or client no-show.</li>
                    </ul>
                </div>
            </div>

            <!-- Network Branch Card -->
            <div class="col-right" style="width: 44%;">
                <div class="card" style="min-height: 250px;">
                    <h3 class="card-title">Network & Booking Branches</h3>
                    <div style="font-size: 9px; color: #475569; line-height: 1.5;">
                        <strong style="color: #1e3a8a; text-transform: uppercase;">Jabalpur Division Area:</strong><br>
                        Active fleet and customer service network covering:<br>
                        • Anuppur • Shahdol • Umaria • Katni • Jabalpur
                        
                        <div style="border-top: 1px solid #e2e8f0; margin: 8px 0;"></div>
                        
                        <strong style="color: #1e3a8a; text-transform: uppercase;">Main Booking Branch:</strong><br>
                        Maa Sharada Tour & Travels (SBD Group)<br>
                        SECL J&K Area, Jamuna Colliery<br>
                        District Anuppur, Madhya Pradesh 484444
                        
                        <div style="border-top: 1px solid #e2e8f0; margin: 8px 0;"></div>
                        
                        <strong>Branch Phone Desk:</strong><br>
                        +91 82248 94319, +91 95756 67241<br>
                        <strong>Email Address:</strong> infosbdbooking@gmail.com<br>
                        <strong>Official Website:</strong> sbdbooking.com
                    </div>
                </div>
            </div>
            <div class="clear"></div>
        </div>

        <!-- Mega Loyalty Reward Program -->
        <div class="card" style="margin-top: 6px; background-color: #fffdf5; border: 1.5px dashed #b59410;">
            <table style="width: 100%; border-collapse: collapse; border: none;">
                <tr>
                    <td style="width: 65%; vertical-align: top; padding-right: 12px;">
                        <h3 style="font-size: 11px; font-weight: 800; color: #1e3a8a; margin: 0 0 3px 0; text-transform: uppercase; letter-spacing: 0.3px;">⭐ Mega Loyalty Reward Trips! ⭐</h3>
                        <p style="font-size: 8.5px; color: #475569; margin: 0 0 8px 0; font-weight: 500;">
                            Accumulate completed travel bookings with SBD Group and unlock complimentary tour packages!
                        </p>
                        
                        <table class="offer-table">
                            <thead>
                                <tr>
                                    <th style="width: 40%;">Completed Bookings</th>
                                    <th style="width: 60%;">Package Reward Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>20+ Bookings</strong></td>
                                    <td>Couple Tour to <span class="reward-highlight">Darjeeling & Sikkim (FREE TRIP)</span></td>
                                </tr>
                                <tr>
                                    <td><strong>15 - 20 Bookings</strong></td>
                                    <td>Couple Tour to <span class="reward-highlight">Darjeeling OR Sikkim (FREE TRIP)</span></td>
                                </tr>
                                <tr>
                                    <td><strong>10 - 15 Bookings</strong></td>
                                    <td>Couple Tour to <span class="reward-highlight">Goa Holiday (FREE TRIP)</span></td>
                                </tr>
                                <tr>
                                    <td><strong>5 - 10 Bookings</strong></td>
                                    <td>Couple Tour to <span class="reward-highlight">Varanasi Spiritual (FREE TRIP)</span></td>
                                </tr>
                                <tr>
                                    <td><strong>3 - 5 Bookings</strong></td>
                                    <td>Couple Tour to <span class="reward-highlight">Amarkantak Nature (FREE TRIP)</span></td>
                                </tr>
                                <tr>
                                    <td><strong>1 - 3 Bookings</strong></td>
                                    <td>Flat <strong style="color: #166534;">20% Discount</strong> on your very next booking</td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                    <td style="width: 35%; vertical-align: middle; background-color: #f8fafc; border-radius: 6px; padding: 10px; border: 1px solid #e2e8f0; text-align: center;">
                        <div style="font-size: 10px; font-weight: 800; color: #b59410; text-transform: uppercase; margin-bottom: 4px; letter-spacing: 0.3px;">New Customer Bonus</div>
                        <div style="font-size: 13px; font-weight: 800; color: #1e3a8a; margin-bottom: 6px;">FLAT 15% OFF</div>
                        <div style="font-size: 8.5px; color: #475569; line-height: 1.4; margin-bottom: 8px; font-weight: 500;">
                            Get a flat 15% discount on your first 3 bookings at any SBD network branch.
                        </div>
                        <div style="background-color: #25d366; color: #ffffff; padding: 5px 8px; border-radius: 4px; font-size: 8.5px; font-weight: 700; display: inline-block;">
                            WhatsApp Booking ID to Claim!
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <div style="margin-top: 15px; text-align: center; border-top: 1px solid #e2e8f0; padding-top: 12px;">
            <div style="font-size: 11.5px; font-weight: 700; color: #1e3a8a;">Thank you for choosing Maa Sharada Tour & Travels!</div>
            <div style="font-size: 9px; color: #64748b; margin-top: 4px; font-weight: 500;">
                For support, feedback, or bookings, visit <a href="https://sbdbooking.com" style="color: #b59410; font-weight: bold; text-decoration: none;">sbdbooking.com</a> or email <strong style="color: #0f172a;">{{ $settings->email ?? 'infosbdbooking@gmail.com' }}</strong>
            </div>
            <div style="margin-top: 6px; font-size: 9px; color: #475569; font-weight: 700;">
                Connect with us: 
                <span style="color: #3b5998;"><a href="{{ $settings->facebook ?? '#' }}" style="color: #3b5998; text-decoration: none; font-weight: bold;">Facebook</a></span> | 
                <span style="color: #e1306c;"><a href="{{ $settings->instagram ?? '#' }}" style="color: #e1306c; text-decoration: none; font-weight: bold;">Instagram</a></span> | 
                <span style="color: #0077b5;"><a href="{{ $settings->linkedin ?? '#' }}" style="color: #0077b5; text-decoration: none; font-weight: bold;">LinkedIn</a></span>
            </div>
        </div>
    </div>
</body>
</html>

