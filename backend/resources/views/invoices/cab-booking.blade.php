<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice - {{ $order->order_number }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #333;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }
        .page {
            padding: 20px;
            page-break-after: always;
        }
        .page:last-child {
            page-break-after: avoid;
        }
        .header {
            text-align: center;
            border-top: 5px solid #A67C00;
            padding-top: 10px;
            margin-bottom: 20px;
        }
        .company-name {
            color: #A67C00;
            font-size: 24px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
        }
        .company-subtitle {
            color: #A67C00;
            font-size: 20px;
            font-weight: bold;
            margin: 5px 0;
        }
        .slogan {
            font-style: italic;
            color: #666;
            font-size: 14px;
            margin-bottom: 10px;
        }
        .divider {
            border-top: 2px solid #A67C00;
            margin: 10px 0;
        }
        .booking-type {
            color: #0047AB;
            font-size: 18px;
            font-weight: bold;
            text-decoration: underline;
            margin: 15px 0;
            text-align: center;
        }
        .main-info {
            margin-bottom: 20px;
        }
        .logo-section {
            float: left;
            width: 150px;
        }
        .trip-header {
            float: left;
            width: 500px;
            padding-left: 20px;
        }
        .trip-title {
            color: #A67C00;
            font-size: 28px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
        }
        .quotation-ref {
            font-size: 14px;
            margin-top: 5px;
        }
        .clear {
            clear: both;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .info-table th, .info-table td {
            border: 1px solid #A67C00;
            padding: 8px;
            text-align: left;
            font-size: 12px;
        }
        .info-table th {
            background-color: #FFF9E6;
            width: 30%;
            color: #555;
        }
        .info-table td {
            font-weight: bold;
            text-transform: uppercase;
        }
        .charges-table th, .charges-table td {
            border: 1px solid #DDD;
            padding: 8px;
            font-size: 13px;
        }
        .charges-table th {
            background-color: #FFF9E6;
            text-align: left;
            width: 70%;
        }
        .charges-table td {
            text-align: right;
            font-weight: bold;
        }
        .total-row {
            background-color: #F9F9F9;
        }
        .terms-section {
            font-size: 9px;
            border: 1px solid #A67C00;
            padding: 10px;
            margin-top: 10px;
        }
        .terms-title {
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 5px;
        }
        .terms-list {
            padding-left: 15px;
            margin: 0;
        }
        .footer-note {
            text-align: center;
            color: red;
            font-weight: bold;
            font-size: 12px;
            margin-top: 10px;
        }
        .qr-section {
            float: right;
            width: 150px;
            text-align: center;
            border: 1px solid #A67C00;
            padding: 5px;
            margin-top: -100px;
        }
        .qr-title {
            font-size: 10px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .qr-placeholder {
            width: 100px;
            height: 100px;
            background-color: #EEE;
            margin: 0 auto;
            border: 1px solid #DDD;
            position: relative;
        }
        .qr-placeholder:after {
            content: 'UPI QR';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 10px;
            color: #999;
        }
        
        /* Page 2 Styles */
        .branch-header {
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 10px;
            text-transform: uppercase;
        }
        .branch-info {
            font-size: 9px;
            margin-bottom: 5px;
        }
        .mega-offer {
            background-color: #FFF9E6;
            border: 2px dashed #A67C00;
            padding: 20px;
            text-align: center;
            margin-top: 20px;
        }
        .offer-title {
            color: #A67C00;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .offer-table {
            width: 100%;
            margin-top: 10px;
        }
        .offer-table td {
            padding: 5px;
            border-bottom: 1px solid #EEE;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="header">
            <h1 class="company-name">SAMPURNA BHARAT DARSHAN</h1>
            <h2 class="company-subtitle">MAA SHARADA TOUR AND TRAVELS - SBD GROUP</h2>
            <p class="slogan">"हर यात्रा का समाधान - हर यात्रा को बनाए खास"</p>
            <div class="divider"></div>
        </div>

        <div class="booking-type">
            BOOKING CONFIRMATION {{ strtoupper($order->car_name) }} {{ $order->is_ac ? 'AC' : 'NON-AC' }}
        </div>

        <div class="main-info">
            <div class="logo-section">
                <!-- Fallback to text logo if image fails -->
                <div style="font-weight: bold; font-size: 40px; border: 4px solid #333; padding: 10px; display: inline-block;">SBD</div>
            </div>
            <div class="trip-header">
                <h2 class="trip-title">{{ str_replace('_', ' ', strtoupper($order->trip_type)) }}</h2>
                <div class="quotation-ref">
                    <strong>QUOTATION REFERENCE:</strong> INVOICE NO - {{ $order->order_number }}<br>
                    <strong>Prepared By:</strong> MAA SHARADA TOUR AND TRAVELS (SBD GROUP)<br>
                    <strong>Booking Date:</strong> {{ $order->created_at->format('d M Y | h:i A') }}
                </div>
            </div>
            <div class="clear"></div>
        </div>

        <table class="info-table">
            <tr>
                <th>Customer Name</th>
                <td>{{ $order->customer_name }} - {{ $order->customer_mobile }}</td>
            </tr>
            <tr>
                <th>Pickup Address</th>
                <td>{{ $order->pickup_address }}</td>
            </tr>
            <tr>
                <th>Vehicle</th>
                <td>{{ strtoupper($order->car_name) }} {{ $order->is_ac ? 'AC' : '' }}</td>
            </tr>
            <tr>
                <th>Trip Route</th>
                <td>{{ strtoupper($order->pickup_address) }} TO {{ strtoupper($order->drop_address) }} - {{ str_replace('_', ' ', strtoupper($order->trip_type)) }}</td>
            </tr>
            <tr>
                <th>Trip Date & Time</th>
                <td>
                    {{ \Carbon\Carbon::parse($order->pickup_date)->format('d-m-Y') }} {{ \Carbon\Carbon::parse($order->pickup_time)->format('h:i A') }}
                    @if($order->trip_type === 'round_trip')
                        RETURN {{ \Carbon\Carbon::parse($order->return_date)->format('d-m-Y') }} {{ \Carbon\Carbon::parse($order->return_time)->format('h:i A') }}
                    @endif
                </td>
            </tr>
        </table>

        <table class="charges-table">
            <tr>
                <th>Base Charges</th>
                <td>₹{{ number_format($order->subtotal, 2) }}</td>
            </tr>
            @if($order->discount_amount > 0)
            <tr>
                <th>Discount ({{ $order->coupon_code }})</th>
                <td>- ₹{{ number_format($order->discount_amount, 2) }}</td>
            </tr>
            @endif
            <tr>
                <th>Advance Received</th>
                <td>₹{{ number_format($order->advance_payment ?? 0, 2) }}</td>
            </tr>
            <tr class="total-row">
                <th style="font-size: 16px;">Total Payable</th>
                <td style="font-size: 16px; color: #A67C00;">₹{{ number_format($order->total_amount - ($order->advance_payment ?? 0), 2) }}</td>
            </tr>
        </table>

        <div class="qr-section">
            <div class="qr-title">Payment Barcode</div>
            <div class="qr-placeholder"></div>
            <div style="font-size: 8px; margin-top: 5px; font-weight: bold;">SBD BOOKING UPI</div>
        </div>

        <div class="terms-section">
            <div class="terms-title">TERMS AND CONDITIONS:-</div>
            <ul class="terms-list">
                <li>Terms & Conditions - SBD GROUP</li>
                <li>Journey Date: {{ \Carbon\Carbon::parse($order->pickup_date)->format('d/m/Y') }}</li>
                <li>Departure Time: {{ \Carbon\Carbon::parse($order->pickup_time)->format('h:i A') }}</li>
                <li>Vehicle: {{ $order->car_name }} ({{ $order->is_ac ? 'AC' : 'Non-AC' }})</li>
                <li>Fare Type: Package Based / KM Based</li>
                <li>Local Use: Vehicle will remain available for the fixed trip schedule only.</li>
                <li>Extra Running Charges: ₹{{ $order->per_km_amount }} per KM applicable for any extra distance.</li>
                <li>Additional Charges: Toll Tax, Border Tax, Permit Charges, Parking Charges separately by customer.</li>
                <li>Safety: Do not force the driver for overspeeding. Driver has full right to stop if safety is at risk.</li>
                <li>Note: All terms are final and binding after booking confirmation.</li>
            </ul>
            <div style="margin-top: 10px; font-weight: bold;">🚗 Happy Journey!</div>
        </div>

        <div class="footer-note">
            CANCELLATION POLICY<br>
            <span style="font-size: 10px;">ONLY BOOKING JOURNEY DATE CHANGE NO CANCELLATION OR REFUND APPLICABLE.</span>
        </div>

        <div style="text-align: center; margin-top: 20px; font-weight: bold; font-size: 14px; border-top: 1px solid #CCC; padding-top: 10px;">
            VEHICLE DRIVER CONTACT NUMBER - {{ $order->driver_mobile ?? '96444 52399' }}
        </div>
    </div>

    <!-- Page 2 -->
    <div class="page">
        <div class="branch-header">
            CONTACT & BOOKING OFFICE BRANCH
        </div>
        <div class="branch-info">
            <strong>ANUPPUR, SHAHDOL, UMARIA, KATNI, JABALPUR, DISTRICT AREA SHAHDOL, JABALPUR DIVISION</strong><br>
            Sampurn Bharat Darshan - Maa Sharda Tour & Travels (SBD GROUP)<br>
            SECL J&K Area Jamuna Colliery District Anuppur Madhya Pradesh 484444<br>
            Phone: +91 82248 94319 | +91 95756 67241<br>
            Email: infosbdbooking@gmail.com | Website: https://sbdbooking.wuaze.com
        </div>

        <div class="divider"></div>
        <div style="text-align: center; font-weight: bold; color: #A67C00; margin-bottom: 20px;">
            THANK'S FOR CHOOSING SAMPURN BHARAT DARSHAN (SBD GROUP)
        </div>

        <div class="mega-offer">
            <div class="offer-title">MEGA OFFER FOR OUR VALUED CUSTOMERS! ⭐⭐</div>
            <div style="color: red; font-weight: bold;">Book More, Get a FREE TRIP!</div>
            
            <table class="offer-table">
                <tr>
                    <td><strong>20+ Bookings</strong></td>
                    <td>Couple Trip to <strong>Darjeeling - Sikkim</strong> <span style="color: red;">FREE TRIP</span></td>
                </tr>
                <tr>
                    <td><strong>15-20 Bookings</strong></td>
                    <td>Darjeeling OR Sikkim <span style="color: red;">FREE TRIP</span></td>
                </tr>
                <tr>
                    <td><strong>10-15 Bookings</strong></td>
                    <td>Goa <span style="color: red;">FREE TRIP</span></td>
                </tr>
                <tr>
                    <td><strong>5-10 Bookings</strong></td>
                    <td>Varanasi <span style="color: red;">FREE TRIP</span></td>
                </tr>
                <tr>
                    <td><strong>3-5 Bookings</strong></td>
                    <td>Amarkantak <span style="color: red;">FREE TRIP</span></td>
                </tr>
                <tr>
                    <td><strong>1-3 Bookings</strong></td>
                    <td><strong>20% OFF</strong> on Next Booking</td>
                </tr>
            </table>

            <div style="margin-top: 20px; font-weight: bold;">
                NEW CUSTOMERS: <span style="color: blue;">15% OFF</span> on First 3 Bookings!<br><br>
                <span style="background-color: #25D366; color: white; padding: 5px 10px; border-radius: 5px;">
                    Send Your Booking Number & Date to WhatsApp to Avail This Offer!
                </span>
            </div>
            <div style="margin-top: 10px; font-style: italic; font-size: 11px;">Offer Valid at All Our Branches</div>
        </div>
    </div>
</body>
</html>
