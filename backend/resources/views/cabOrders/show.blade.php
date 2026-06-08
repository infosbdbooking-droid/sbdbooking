@extends('layouts.app')

@section('content')
<!-- Bootstrap 5 CSS (scoped to CRM wrapper) -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
{{-- DataTables CSS is loaded globally via footer (dist/Datatable2.18) --}}

{{-- ── Validation Errors ─────────────────────────────────────────── --}}
@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show mx-4 mt-3 rounded-3 shadow-sm border-0" role="alert" style="border-left: 4px solid #dc3545 !important;">
    <div class="d-flex align-items-start gap-2">
        <i class="fas fa-exclamation-circle text-danger mt-1"></i>
        <div>
            <strong class="d-block mb-1">Please fix the following errors:</strong>
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li class="small">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

{{-- ── Inline Session Flash (fallback if JS Swal fails) ─────────── --}}
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show mx-4 mt-3 rounded-3 shadow-sm border-0" role="alert" style="border-left: 4px solid #198754 !important;" id="flash-success-bar">
    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show mx-4 mt-3 rounded-3 shadow-sm border-0" role="alert" style="border-left: 4px solid #dc3545 !important;" id="flash-error-bar">
    <i class="fas fa-times-circle me-2"></i> {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif


<style>
    /* Scope Bootstrap styles to prevent conflicts with global layout */
    .crm-wrapper {
        font-family: 'Inter', sans-serif;
    }
    .crm-wrapper .card {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05), 0 2px 4px -1px rgba(0,0,0,0.03);
        margin-bottom: 1.5rem;
        background: #ffffff;
    }
    .crm-wrapper .card-header {
        background-color: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        border-top-left-radius: 12px !important;
        border-top-right-radius: 12px !important;
        padding: 1rem 1.25rem;
    }
    /* Timeline styles */
    .timeline {
        position: relative;
        padding-left: 30px;
        list-style: none;
    }
    .timeline::before {
        content: "";
        position: absolute;
        left: 9px;
        top: 5px;
        bottom: 5px;
        width: 2px;
        background: #e2e8f0;
    }
    .timeline-item {
        position: relative;
        margin-bottom: 1.5rem;
    }
    .timeline-badge {
        position: absolute;
        left: -30px;
        top: 3px;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: #cbd5e1;
        border: 4px solid #fff;
        box-shadow: 0 0 0 2px #cbd5e1;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .timeline-item.active .timeline-badge {
        background: #3b82f6;
        box-shadow: 0 0 0 2px #3b82f6;
    }
    .timeline-item.completed .timeline-badge {
        background: #10b981;
        box-shadow: 0 0 0 2px #10b981;
    }
    .timeline-item.cancelled .timeline-badge {
        background: #ef4444;
        box-shadow: 0 0 0 2px #ef4444;
    }
    .timeline-date {
        font-size: 0.75rem;
        color: #64748b;
        margin-bottom: 0.25rem;
    }
    .timeline-title {
        font-size: 0.875rem;
        font-weight: 700;
        color: #1e293b;
    }
    .timeline-desc {
        font-size: 0.75rem;
        color: #475569;
        margin-top: 0.125rem;
    }
    /* Payment progress bar wrapper */
    .payment-progress-wrapper {
        background: #f1f5f9;
        border-radius: 8px;
        padding: 10px;
        border: 1px solid #e2e8f0;
    }
    /* Badge Customizations */
    .status-badge {
        font-size: 0.75rem;
        font-weight: 700;
        padding: 0.35em 0.65em;
        border-radius: 30px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    
    /* Professional Table styling overrides */
    #payments-table thead th {
        background-color: #f8fafc !important;
        color: #475569 !important;
        font-weight: 600 !important;
        text-transform: uppercase !important;
        font-size: 11px !important;
        letter-spacing: 0.05em !important;
        border-bottom: 1px solid #e2e8f0 !important;
        padding: 10px 12px !important;
        text-align: left !important;
    }
    #payments-table thead th.text-end {
        text-align: right !important;
    }
    #payments-table tbody td {
        padding: 10px 12px !important;
        border-bottom: 1px solid #f1f5f9 !important;
        color: #334155 !important;
    }
    
    /* Tailwind utility simulation classes */
    .text-slate-800 {
        color: #1e293b !important;
    }
    .text-slate-700 {
        color: #334155 !important;
    }
    .text-slate-600 {
        color: #475569 !important;
    }
    .text-slate-500 {
        color: #64748b !important;
    }
    .text-indigo-200 {
        color: #c7d2fe !important;
    }
    .border-slate-100 {
        border-color: #f1f5f9 !important;
    }
    .border-slate-200 {
        border-color: #e2e8f0 !important;
    }
    .border-slate-300 {
        border-color: #cbd5e1 !important;
    }
    .bg-slate-50 {
        background-color: #f8fafc !important;
    }
    .bg-slate-50\/50 {
        background-color: rgba(248, 250, 252, 0.5) !important;
    }
    .bg-gradient-to-br.from-slate-900.to-indigo-950 {
        background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%) !important;
    }
    .bg-white\/5 {
        background-color: rgba(255, 255, 255, 0.05) !important;
    }
    .border-white\/10 {
        border-color: rgba(255, 255, 255, 0.1) !important;
    }
    .border-b {
        border-bottom-width: 1px !important;
        border-bottom-style: solid !important;
    }
    .badge.bg-light {
        color: #1e293b !important;
        background-color: #f8fafc !important;
        border: 1px solid #cbd5e1 !important;
    }
</style>

@php
    $paidAmount = (float)$order->advance_payment;
    $totalFare = (float)$order->total_amount;
    $remainingAmount = max(0, $totalFare - $paidAmount);
    $payProgress = $totalFare > 0 ? round(($paidAmount / $totalFare) * 100) : 0;
    
    // Format coordinates & routes
    $navOrigin = $order->pickup_lat . ',' . $order->pickup_lng;
    $navDest = $order->drop_lat . ',' . $order->drop_lng;
    $navWaypoints = '';
    
    if($order->trip_type === 'round_trip') {
        if($order->return_pickup_lat) {
            $navWaypoints = $order->drop_lat . ',' . $order->drop_lng . '|' . $order->return_pickup_lat . ',' . $order->return_pickup_lng;
            $navDest = ($order->return_drop_lat ?? $order->pickup_lat) . ',' . ($order->return_drop_lng ?? $order->pickup_lng);
        } else {
            $navWaypoints = $order->drop_lat . ',' . $order->drop_lng;
            $navDest = $order->pickup_lat . ',' . $order->pickup_lng;
        }
    }
    
    $navigationUrl = "https://www.google.com/maps/dir/?api=1&origin=" . urlencode($navOrigin) . "&destination=" . urlencode($navDest) . "&travelmode=driving";
    if($navWaypoints) {
        $navigationUrl .= "&waypoints=" . urlencode($navWaypoints);
    }
@endphp

<div class="crm-wrapper container-fluid py-4">
    <!-- Breadcrumb & Page title -->
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 text-xs text-muted">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('cabOrders') }}" class="text-decoration-none text-muted">Cab Bookings</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $order->order_number }}</li>
                </ol>
            </nav>
            <h1 class="h3 font-bold text-slate-800 mb-0 d-flex align-items-center gap-2">
                Booking CRM Details 
                <span class="fs-6 text-muted">#{{ $order->order_number }}</span>
            </h1>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <a href="{{ route('cabOrders') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="fas fa-arrow-left me-1.5"></i> Back to List
            </a>
            <a href="{{ route('cabOrders.invoice', $order->id) }}" target="_blank" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm">
                <i class="fas fa-file-invoice me-1.5"></i> Print Invoice
            </a>
        </div>
    </div>

    <!-- Sticky action buttons panel -->
    <div class="card border-slate-200 shadow-sm sticky-top z-3 mb-4" style="top: 15px;">
        <div class="card-body py-3">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <!-- Status Badges -->
                <div class="d-flex align-items-center gap-2">
                    <div class="d-flex flex-col">
                        <span class="text-xxs text-muted uppercase tracking-wider font-semibold">Booking Status</span>
                        @if($order->booking_status === 'pending')
                            <span class="badge bg-warning text-dark status-badge">Pending</span>
                        @elseif($order->booking_status === 'accepted')
                            <span class="badge bg-info text-white status-badge">Accepted</span>
                        @elseif($order->booking_status === 'confirmed')
                            <span class="badge bg-success text-white status-badge">Confirmed</span>
                        @elseif($order->booking_status === 'driver_assigned')
                            <span class="badge bg-teal text-white status-badge" style="background-color: #20c997;">Driver Assigned</span>
                        @elseif($order->booking_status === 'started')
                            <span class="badge bg-primary text-white status-badge">Started</span>
                        @elseif($order->booking_status === 'completed')
                            <span class="badge bg-success text-white status-badge">Completed</span>
                        @elseif($order->booking_status === 'cancelled')
                            <span class="badge bg-danger text-white status-badge">Cancelled</span>
                        @else
                            <span class="badge bg-secondary text-white status-badge">{{ $order->booking_status }}</span>
                        @endif
                    </div>
                    <div class="vr mx-2 align-self-stretch"></div>
                    <div class="d-flex flex-col">
                        <span class="text-xxs text-muted uppercase tracking-wider font-semibold">Payment Status</span>
                        @if($order->payment_status === 'unpaid')
                            <span class="badge bg-warning text-dark status-badge">Pending</span>
                        @elseif($order->payment_status === 'partial')
                            <span class="badge status-badge text-white" style="background-color: #fd7e14;">Partially Paid</span>
                        @elseif($order->payment_status === 'paid')
                            <span class="badge bg-success text-white status-badge">Paid</span>
                        @elseif($order->payment_status === 'failed')
                            <span class="badge bg-danger text-white status-badge">Failed</span>
                        @elseif($order->payment_status === 'refunded')
                            <span class="badge bg-secondary text-white status-badge">Refunded</span>
                        @else
                            <span class="badge bg-secondary text-white status-badge">{{ $order->payment_status }}</span>
                        @endif
                    </div>
                </div>

                <!-- Primary Operational Actions -->
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    @if($order->booking_status === 'pending')
                        <button type="button" class="btn btn-emerald btn-success text-white btn-sm font-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#approvalModal">
                            <i class="fas fa-check-circle me-1.5"></i> Accept & Approve
                        </button>
                    @endif

                    @if($order->payment_status !== 'paid')
                        <button type="button" class="btn btn-warning text-dark btn-sm font-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#collectPaymentModal">
                            <i class="fas fa-wallet me-1.5"></i> Collect Payment
                        </button>
                        <form id="approve-payment-form" action="{{ route('cabOrders.approvePayment', $order->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="button" class="btn btn-success text-white btn-sm font-bold shadow-sm" onclick="confirmApprovePayment()">
                                <i class="fas fa-check-double me-1.5"></i> Mark Fully Paid
                            </button>
                        </form>
                    @endif

                    @if(in_array($order->booking_status, ['pending', 'accepted', 'confirmed']))
                        <button type="button" class="btn btn-info text-white btn-sm font-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#assignDriverModal">
                            <i class="fas fa-steering-wheel fa-car me-1.5"></i> Assign Driver
                        </button>
                    @endif

                    @if($order->booking_status === 'driver_assigned')
                        <form action="{{ route('cabOrders.updateStatus', $order->id) }}" method="POST" class="d-inline status-update-form">
                            @csrf
                            <input type="hidden" name="status" value="started">
                            <button type="submit" class="btn btn-primary text-white btn-sm font-bold shadow-sm">
                                <i class="fas fa-play me-1.5"></i> Start Trip
                            </button>
                        </form>
                    @endif

                    @if($order->booking_status === 'started')
                        <form action="{{ route('cabOrders.updateStatus', $order->id) }}" method="POST" class="d-inline status-update-form">
                            @csrf
                            <input type="hidden" name="status" value="completed">
                            <button type="submit" class="btn btn-success text-white btn-sm font-bold shadow-sm">
                                <i class="fas fa-flag-checkered me-1.5"></i> Complete Trip
                            </button>
                        </form>
                    @endif

                    @if(in_array($order->booking_status, ['pending', 'accepted', 'confirmed', 'driver_assigned']))
                        <form id="cancel-booking-form" action="{{ route('cabOrders.cancel', $order->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="button" class="btn btn-outline-danger btn-sm font-bold" onclick="confirmCancelBooking()">
                                <i class="fas fa-times-circle me-1.5"></i> Cancel Booking
                            </button>
                        </form>
                    @endif
                </div>

                <!-- Sharing & Integrations -->
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-success btn-sm font-semibold rounded-pill px-3" onclick="sendWhatsAppConfirmation()">
                        <i class="fab fa-whatsapp me-1.5"></i> Send Confirmation
                    </button>
                    <button type="button" class="btn btn-outline-primary btn-sm font-semibold rounded-pill px-3" onclick="generatePaymentLink()">
                        <i class="fas fa-link me-1.5"></i> Payment Link
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main CRM grid -->
    <div class="row">
        <!-- LEFT COLUMN: Fare breakdown, route details, history table -->
        <div class="col-lg-8">
            <!-- Invoice breakdown card -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-slate-800 font-bold fs-6"><i class="fas fa-file-invoice-dollar text-primary me-2"></i>Invoice Details & Breakdown</h5>
                    <span class="badge bg-light text-slate-700 font-bold px-2.5 py-1 border text-uppercase" style="font-size: 10px;">{{ str_replace('_', ' ', $order->trip_type) }}</span>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <!-- Left inside column: Breakdown list -->
                        <div class="col-md-6">
                            <ul class="list-group list-group-flush fs-7">
                                @php
                                    $breakdownList = is_string($order->charges_breakdown) ? json_decode($order->charges_breakdown, true) : $order->charges_breakdown;
                                    $isBreakdownArray = is_array($breakdownList);
                                    
                                    // Extract helper values
                                    $baseKmRate = 0;
                                    $totalKmDistance = $order->total_km;
                                    $kmChargesVal = $order->per_km_amount;
                                    
                                    if ($isBreakdownArray) {
                                        foreach ($breakdownList as $item) {
                                            $name = strtolower($item['charge_type'] ?? $item['type'] ?? '');
                                            if (str_contains($name, 'km') || str_contains($name, 'distance')) {
                                                $baseKmRate = $item['rate'] ?? 0;
                                            }
                                        }
                                    }
                                @endphp
                                <li class="list-group-item d-flex justify-content-between align-items-center py-2.5 px-0 border-top-0">
                                    <span class="text-muted">Base Rate Limit (Min Charge)</span>
                                    <span class="font-bold text-slate-800">₹{{ number_format($order->car->min_trip_amount ?? 0, 2) }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center py-2.5 px-0">
                                    <div>
                                        <span class="text-muted d-block">Distance Fare / KM Charges</span>
                                        <small class="text-xxs text-muted-flat font-normal">({{ $totalKmDistance }} KM × ₹{{ $baseKmRate }}/KM)</small>
                                    </div>
                                    <span class="font-bold text-slate-800">₹{{ number_format($kmChargesVal, 2) }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center py-2.5 px-0">
                                    <span class="text-muted">Driver Allowance</span>
                                    <span class="font-bold text-slate-800">₹{{ number_format($order->driver_allowance, 2) }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center py-2.5 px-0">
                                    <span class="text-muted">AC Charges</span>
                                    <span class="font-bold text-slate-800">₹{{ number_format($order->ac_charges, 2) }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center py-2.5 px-0">
                                    <span class="text-muted">Toll, Parking & Border Taxes</span>
                                    <span class="font-bold text-slate-800">₹{{ number_format($order->estimated_toll, 2) }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center py-2.5 px-0">
                                    <span class="text-muted">Overnight/Night Stay Charges</span>
                                    @php
                                        $stayAmount = 0;
                                        if ($isBreakdownArray) {
                                            foreach ($breakdownList as $item) {
                                                $name = strtolower($item['charge_type'] ?? $item['type'] ?? '');
                                                if (str_contains($name, 'stay')) {
                                                    $stayAmount = $item['amount'] ?? 0;
                                                }
                                            }
                                        }
                                    @endphp
                                    <span class="font-bold text-slate-800">₹{{ number_format($stayAmount, 2) }}</span>
                                </li>
                            </ul>
                        </div>
                        <!-- Right inside column: Summary math -->
                        <div class="col-md-6 bg-slate-50/50 p-4 rounded-xl border border-dashed border-slate-200">
                            <div class="d-flex justify-content-between text-slate-600 mb-2.5 fs-7">
                                <span>Subtotal (Base + Allowances)</span>
                                <span class="font-semibold">₹{{ number_format($order->subtotal, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between text-success mb-2.5 fs-7">
                                <span>Promo Discount Applied</span>
                                <span class="font-semibold">- ₹{{ number_format($order->discount_amount, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between text-slate-600 mb-2.5 fs-7">
                                <span>GST (5% Included)</span>
                                <span class="font-semibold">₹{{ number_format($order->total_amount * 0.05, 2) }}</span>
                            </div>
                            <hr class="my-2.5 border-slate-300">
                            <div class="d-flex justify-content-between align-items-center text-slate-800 mb-3">
                                <span class="font-bold fs-6">Net Final Fare</span>
                                <span class="font-extrabold fs-4 text-primary">₹{{ number_format($order->total_amount, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between text-slate-600 mb-2 fs-7">
                                <span>Total Paid (Advance)</span>
                                <span class="font-semibold text-success">₹{{ number_format($paidAmount, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center text-slate-800">
                                <span class="font-semibold text-danger fs-7">Balance Outstanding</span>
                                <span class="font-bold text-danger fs-6">₹{{ number_format($remainingAmount, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Route Details & map -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 text-slate-800 font-bold fs-6"><i class="fas fa-map-marked-alt text-primary me-2"></i>Map Route & Distance Details</h5>
                </div>
                <div class="card-body p-0">
                    <!-- Embedded Google Map -->
                    @if($order->pickup_lat && $order->pickup_lng && $order->drop_lat && $order->drop_lng)
                        <div id="route-map" style="width: 100%; height: 320px; border-bottom: 1px solid #e2e8f0; background: #f8fafc;"></div>
                    @endif

                    <div class="p-4">
                        <div class="row g-4">
                            <div class="col-md-6 border-end border-slate-100">
                                <div class="relative pl-4 border-l-2 border-dashed border-primary/50 py-1">
                                    <div class="mb-4 relative">
                                        <div class="position-absolute rounded-circle bg-primary" style="width: 10px; height: 10px; left: -21px; top: 6px;"></div>
                                        <span class="text-xxs uppercase tracking-wider text-muted font-bold block mb-0.5">Pickup Location</span>
                                        <span class="fs-7 font-bold text-slate-800">{{ $order->pickup_address }}</span>
                                    </div>
                                    <div class="relative">
                                        <div class="position-absolute rounded-circle bg-success" style="width: 10px; height: 10px; left: -21px; top: 6px;"></div>
                                        <span class="text-xxs uppercase tracking-wider text-muted font-bold block mb-0.5">Drop Destination</span>
                                        <span class="fs-7 font-bold text-slate-800">{{ $order->drop_address }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <span class="text-xxs uppercase tracking-wider text-muted font-bold block mb-2">Distance Details</span>
                                <div class="d-flex justify-content-between mb-2 fs-7">
                                    <span class="text-slate-600">Pickup to Drop-off</span>
                                    <strong class="text-slate-800">{{ $order->one_way_km }} KM</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-2 fs-7">
                                    <span class="text-slate-600">Return to Pickup (Back Running)</span>
                                    <strong class="text-slate-800">{{ $order->return_km }} KM</strong>
                                </div>
                                <hr class="my-2 border-slate-200">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-slate-800 font-bold fs-7">Total Distance</span>
                                    <strong class="text-primary font-black fs-6">{{ $order->total_km }} KM</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment History Section -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-slate-800 font-bold fs-6"><i class="fas fa-history text-primary me-2"></i>Payment Collection History</h5>
                    <button type="button" class="btn btn-outline-primary btn-xs" data-bs-toggle="modal" data-bs-target="#collectPaymentModal">
                        <i class="fas fa-plus me-1"></i> Record New Payment
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="payments-table" class="table table-striped table-hover align-middle fs-7">
                            <thead class="table-light text-slate-700">
                                <tr>
                                    <th>Date</th>
                                    <th>Receipt No</th>
                                    <th>Amount</th>
                                    <th>Method</th>
                                    <th>Transaction ID</th>
                                    <th>Status</th>
                                    <th>Added By</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->payments as $payment)
                                    <tr>
                                        <td>{{ $payment->created_at->format('d M Y | h:i A') }}</td>
                                        <td><code class="text-primary font-semibold">{{ $payment->receipt_number }}</code></td>
                                        <td class="font-bold text-slate-800">₹{{ number_format($payment->amount, 2) }}</td>
                                        <td><span class="badge bg-light text-slate-700 border font-semibold">{{ $payment->payment_method }}</span></td>
                                        <td><small class="text-muted font-mono">{{ $payment->transaction_id ?: 'N/A' }}</small></td>
                                        <td>
                                            @if($payment->payment_status === 'paid')
                                                <span class="badge bg-success-subtle text-success border border-success-subtle">Paid</span>
                                            @elseif($payment->payment_status === 'partial')
                                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle">Partial</span>
                                            @elseif($payment->payment_status === 'failed')
                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Failed</span>
                                            @else
                                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">{{ $payment->payment_status }}</span>
                                            @endif
                                        </td>
                                        <td><small class="text-slate-600">{{ $payment->added_by }}</small></td>
                                        <td class="text-end">
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-outline-secondary btn-xs" onclick="viewPaymentDetails({{ json_encode($payment) }})">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button type="button" class="btn btn-outline-primary btn-xs" onclick="printReceipt({{ json_encode($payment) }}, {{ json_encode($order) }})">
                                                    <i class="fas fa-print"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Activity timeline section -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 text-slate-800 font-bold fs-6"><i class="fas fa-stream text-primary me-2"></i>Trip Activity & CRM Timeline</h5>
                </div>
                <div class="card-body">
                    <ul class="timeline">
                        @forelse($order->activities as $act)
                            <li class="timeline-item {{ str_contains(strtolower($act->event), 'complete') ? 'completed' : (str_contains(strtolower($act->event), 'cancel') ? 'cancelled' : 'active') }}">
                                <div class="timeline-badge">
                                    @if(str_contains(strtolower($act->event), 'create'))
                                        <i class="fas fa-plus text-xxs text-white" style="font-size: 8px;"></i>
                                    @elseif(str_contains(strtolower($act->event), 'payment'))
                                        <i class="fas fa-indian-rupee-sign text-xxs text-white" style="font-size: 8px;"></i>
                                    @elseif(str_contains(strtolower($act->event), 'driver'))
                                        <i class="fas fa-user text-xxs text-white" style="font-size: 8px;"></i>
                                    @elseif(str_contains(strtolower($act->event), 'complete'))
                                        <i class="fas fa-check text-xxs text-white" style="font-size: 8px;"></i>
                                    @else
                                        <i class="fas fa-dot-circle text-xxs text-white" style="font-size: 8px;"></i>
                                    @endif
                                </div>
                                <div class="timeline-date">{{ $act->created_at->format('d M Y | h:i A') }}</div>
                                <div class="timeline-title">{{ $act->event }}</div>
                                @if($act->description)
                                    <div class="timeline-desc">{{ $act->description }}</div>
                                @endif
                            </li>
                        @empty
                            <!-- Default Fallbacks based on order state -->
                            <li class="timeline-item completed">
                                <div class="timeline-badge"><i class="fas fa-plus text-xxs text-white" style="font-size: 8px;"></i></div>
                                <div class="timeline-date">{{ $order->created_at->format('d M Y | h:i A') }}</div>
                                <div class="timeline-title">Booking Created</div>
                                <div class="timeline-desc">Manual cab booking initiated by Admin. Status set to Pending Approval.</div>
                            </li>

                            @if(in_array($order->booking_status, ['accepted', 'confirmed', 'driver_assigned', 'started', 'completed']))
                                <li class="timeline-item active">
                                    <div class="timeline-badge"><i class="fas fa-check text-xxs text-white" style="font-size: 8px;"></i></div>
                                    <div class="timeline-date">{{ $order->updated_at->format('d M Y | h:i A') }}</div>
                                    <div class="timeline-title">Booking Approved</div>
                                    <div class="timeline-desc">Booking has been approved and moved to Confirmed state.</div>
                                </li>
                            @endif

                            @if(in_array($order->booking_status, ['driver_assigned', 'started', 'completed']))
                                <li class="timeline-item active">
                                    <div class="timeline-badge"><i class="fas fa-car text-xxs text-white" style="font-size: 8px;"></i></div>
                                    <div class="timeline-date">{{ $order->updated_at->format('d M Y | h:i A') }}</div>
                                    <div class="timeline-title">Driver Assigned</div>
                                    <div class="timeline-desc">Driver assigned: {{ $order->driver_name ?: ' Rahul' }} ({{ $order->driver_mobile ?: '96444 52399' }}).</div>
                                </li>
                            @endif

                            @if(in_array($order->booking_status, ['started', 'completed']))
                                <li class="timeline-item active">
                                    <div class="timeline-badge"><i class="fas fa-play text-xxs text-white" style="font-size: 8px;"></i></div>
                                    <div class="timeline-date">{{ $order->updated_at->format('d M Y') }}</div>
                                    <div class="timeline-title">Trip Started</div>
                                    <div class="timeline-desc">The trip is currently on route. GPS tracking live.</div>
                                </li>
                            @endif

                            @if($order->booking_status === 'completed')
                                <li class="timeline-item completed">
                                    <div class="timeline-badge"><i class="fas fa-check-double text-xxs text-white" style="font-size: 8px;"></i></div>
                                    <div class="timeline-date">{{ $order->updated_at->format('d M Y') }}</div>
                                    <div class="timeline-title">Trip Completed</div>
                                    <div class="timeline-desc">Vehicle successfully reached drop address. Feedback collected.</div>
                                </li>
                            @endif

                            @if($order->booking_status === 'cancelled')
                                <li class="timeline-item cancelled">
                                    <div class="timeline-badge"><i class="fas fa-times text-xxs text-white" style="font-size: 8px;"></i></div>
                                    <div class="timeline-date">{{ $order->updated_at->format('d M Y | h:i A') }}</div>
                                    <div class="timeline-title">Booking Cancelled</div>
                                    <div class="timeline-desc">Order has been cancelled by administrator.</div>
                                </li>
                            @endif
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: Payment Summary Card, Customer Info, Schedule -->
        <div class="col-lg-4">
            <!-- Payment summary card -->
            <div class="card bg-gradient-to-br from-slate-900 to-indigo-950 text-white border-0 shadow-lg overflow-hidden relative">
                <!-- Decorative background elements -->
                <div class="position-absolute rounded-circle bg-white/5" style="width: 150px; height: 150px; right: -50px; top: -50px;"></div>
                <div class="position-absolute rounded-circle bg-white/5" style="width: 100px; height: 100px; left: -30px; bottom: -30px;"></div>
                
                <div class="card-body p-4 relative z-1">
                    <h5 class="font-bold text-white fs-6 mb-3 d-flex align-items-center gap-2">
                        <i class="fas fa-wallet text-warning"></i> Payment Status Summary
                    </h5>

                    <!-- Progress Bar -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between text-xs font-semibold mb-1">
                            <span class="text-indigo-200">Collection Progress</span>
                            <span class="text-white">{{ $payProgress }}% Collected</span>
                        </div>
                        <div class="progress" style="height: 8px; background-color: rgba(255,255,255,0.15);">
                            <div class="progress-bar bg-warning progress-bar-striped progress-bar-animated" role="progressbar" style="width: {{ $payProgress }}%;" aria-valuenow="{{ $payProgress }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>

                    <!-- Ledger details -->
                    <div class="space-y-3 fs-7">
                        <div class="d-flex justify-content-between border-b border-white/10 pb-2">
                            <span class="text-indigo-200">Total Booking Fare</span>
                            <strong class="fs-6">₹{{ number_format($totalFare, 2) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between border-b border-white/10 pb-2">
                            <span class="text-indigo-200">Total Advance Paid</span>
                            <strong class="text-success fs-6">₹{{ number_format($paidAmount, 2) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between border-b border-white/10 pb-2">
                            <span class="text-indigo-200">Outstanding Balance</span>
                            <strong class="text-warning fs-6">₹{{ number_format($remainingAmount, 2) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between border-b border-white/10 pb-2">
                            <span class="text-indigo-200">Payment Method</span>
                            <strong class="text-white text-uppercase">{{ $order->payment_method ?: 'N/A' }}</strong>
                        </div>
                        <div class="d-flex justify-content-between border-b border-white/10 pb-2">
                            <span class="text-indigo-200">Latest Receipt No</span>
                            @php
                                $lastPayment = $order->payments->first();
                            @endphp
                            <strong class="text-white font-mono">{{ $lastPayment ? $lastPayment->receipt_number : 'N/A' }}</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-indigo-200">Last Payment Date</span>
                            <strong class="text-white">{{ $lastPayment ? $lastPayment->created_at->format('d M Y') : 'N/A' }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Customer Details card -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 text-slate-800 font-bold fs-6"><i class="fas fa-id-card text-primary me-2"></i>Customer Information</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div class="w-12 h-12 bg-primary/10 rounded-circle text-primary d-flex align-items-center justify-center font-bold text-lg">
                            {{ substr($order->customer_name, 0, 1) }}
                        </div>
                        <div>
                            <h6 class="font-bold text-slate-800 mb-0.5">{{ $order->customer_name }}</h6>
                            <span class="text-xs text-muted d-flex align-items-center gap-1">
                                <i class="fas fa-phone text-[9px]"></i> {{ $order->customer_mobile }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="space-y-2.5 fs-7 border-t border-slate-100 pt-3">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Customer Type:</span>
                            <strong class="text-slate-800">{!! $order->customer_id ? '<span class="text-success">Registered Member</span>' : '<span class="text-muted">Guest Checkout</span>' !!}</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Bags Capacity:</span>
                            <strong class="text-slate-800">{{ $order->bags ?? 0 }} Bags</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Passengers Capacity:</span>
                            <strong class="text-slate-800">{{ $order->passengers }} Passengers</strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Vehicle & Schedule Card -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 text-slate-800 font-bold fs-6"><i class="fas fa-clock text-primary me-2"></i>Schedule & Vehicle</h5>
                </div>
                <div class="card-body">
                    <div class="p-3 bg-slate-50 border rounded-xl mb-4 text-center">
                        <i class="fas fa-car-side text-primary fs-3 mb-1.5"></i>
                        <h6 class="font-bold text-slate-800 mb-0.5">{{ strtoupper($order->car_name) }}</h6>
                        <span class="text-xxs text-muted font-bold uppercase tracking-wider">{{ $order->is_ac ? 'Air Conditioned Cabin (AC)' : 'Non-AC Standard' }}</span>
                    </div>

                    <div class="space-y-3 fs-7">
                        <div class="d-flex justify-content-between border-b border-slate-100 pb-2">
                            <span class="text-muted">Journey Date</span>
                            <strong class="text-slate-800">{{ $order->pickup_date ? \Carbon\Carbon::parse($order->pickup_date)->format('d M, Y') : '-' }}</strong>
                        </div>
                        <div class="d-flex justify-content-between border-b border-slate-100 pb-2">
                            <span class="text-muted">Pickup Departure Time</span>
                            <strong class="text-slate-800">{{ $order->pickup_time ? (strpos($order->pickup_time, ' to ') !== false ? $order->pickup_time : \Carbon\Carbon::parse($order->pickup_time)->format('h:i A')) : '-' }}</strong>
                        </div>
                        @if($order->trip_type === 'round_trip')
                            <div class="d-flex justify-content-between border-b border-slate-100 pb-2">
                                <span class="text-muted">Return Date</span>
                                <strong class="text-slate-800">{{ $order->return_date ? \Carbon\Carbon::parse($order->return_date)->format('d M, Y') : '-' }}</strong>
                            </div>
                            <div class="d-flex justify-content-between border-b border-slate-100 pb-2">
                                <span class="text-muted">Return Pickup Time</span>
                                <strong class="text-slate-800">{{ $order->return_time ? (strpos($order->return_time, ' to ') !== false ? $order->return_time : \Carbon\Carbon::parse($order->return_time)->format('h:i A')) : '-' }}</strong>
                            </div>
                        @endif
                        <div class="d-flex justify-content-between border-b border-slate-100 pb-2">
                            <span class="text-muted">Driver Details:</span>
                            <strong class="text-slate-800">
                                @if($order->driver_name)
                                    {{ $order->driver_name }} ({{ $order->driver_mobile }})
                                @else
                                    <span class="text-muted font-semibold">Not Assigned</span>
                                @endif
                            </strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================
     MODAL 1: BOOKING APPROVAL & PAYMENT SETUP
     ======================================================== -->
<div class="modal fade" id="approvalModal" tabindex="-1" aria-labelledby="approvalModalLabel" aria-hidden="true" x-data="approvalSetup()">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="approval-setup-form" action="{{ route('cabOrders.approveSetup', $order->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header bg-slate-900 text-white py-3">
                    <h5 class="modal-title font-bold fs-6" id="approvalModalLabel"><i class="fas fa-check-circle text-success me-2"></i>Booking Approval & Payment Setup</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <!-- Summary info grid -->
                    <div class="p-3 bg-slate-50 border rounded-xl mb-4">
                        <h6 class="text-xxs uppercase tracking-wider text-muted font-bold mb-2">Booking Info Quick Summary</h6>
                        <div class="row g-2 fs-7">
                            <div class="col-6 col-sm-3"><strong>Booking ID:</strong> {{ $order->order_number }}</div>
                            <div class="col-6 col-sm-3"><strong>Customer Name:</strong> {{ $order->customer_name }}</div>
                            <div class="col-6 col-sm-3"><strong>Mobile:</strong> {{ $order->customer_mobile }}</div>
                            <div class="col-6 col-sm-3"><strong>Vehicle Type:</strong> {{ $order->car_name }}</div>
                            <div class="col-12 col-sm-6 mt-1"><strong>Route:</strong> {{ $order->pickup_address }} &rarr; {{ $order->drop_address }}</div>
                            <div class="col-6 col-sm-3 mt-1"><strong>Travel Date:</strong> {{ $order->pickup_date ? \Carbon\Carbon::parse($order->pickup_date)->format('d M, Y') : '-' }}</div>
                            <div class="col-6 col-sm-3 mt-1"><strong>Total Fare:</strong> <strong class="text-primary">₹{{ number_format($totalFare, 2) }}</strong></div>
                        </div>
                    </div>

                    <!-- Payment setup controls -->
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Payment Collection Type</label>
                            <div class="d-flex gap-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_collection_type" id="coll_full" value="full" x-model="collectType" @change="recalculate()">
                                    <label class="form-check-label fs-7 font-bold text-slate-700" for="coll_full">Full Payment</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_collection_type" id="coll_adv" value="advance" x-model="collectType" @change="recalculate()">
                                    <label class="form-check-label fs-7 font-bold text-slate-700" for="coll_adv">Advance Payment</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_collection_type" id="coll_later" value="pay_later" x-model="collectType" @change="recalculate()">
                                    <label class="form-check-label fs-7 font-bold text-slate-700" for="coll_later">Pay Later</label>
                                </div>
                            </div>
                        </div>

                        <!-- Advance payment specific sub-options -->
                        <div class="col-12" x-show="collectType === 'advance'" x-transition>
                            <div class="card border-slate-200 bg-slate-50/50 p-3 mb-0">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Advance Type</label>
                                        <div class="d-flex gap-3">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="advance_type" id="adv_perc" value="percentage" x-model="advanceType" @change="recalculate()">
                                                <label class="form-check-label fs-7" for="adv_perc">Percentage (%)</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="advance_type" id="adv_fixed" value="fixed" x-model="advanceType" @change="recalculate()">
                                                <label class="form-check-label fs-7" for="adv_fixed">Fixed Amount (₹)</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6" x-show="advanceType === 'percentage'">
                                        <label class="block text-xs font-semibold text-slate-600 mb-1">Advance Percentage</label>
                                        <select class="form-select form-select-sm" name="advance_percentage" x-model="advancePercentage" @change="recalculate()">
                                            <option value="10">10%</option>
                                            <option value="20">20%</option>
                                            <option value="25">25%</option>
                                            <option value="30">30%</option>
                                            <option value="40">40%</option>
                                            <option value="50">50%</option>
                                            <option value="75">75%</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6" x-show="advanceType === 'fixed'">
                                        <label class="block text-xs font-semibold text-slate-600 mb-1">Enter Advance Amount (₹)</label>
                                        <input type="number" step="0.01" class="form-control form-control-sm" name="advance_amount_val" x-model="fixedAdvanceAmount" @input="recalculate()" placeholder="e.g. 2000">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Auto calculator section (visible if collecting payment now) -->
                        <div class="col-12" x-show="collectType !== 'pay_later'" x-transition>
                            <div class="p-3 bg-blue-50 border border-blue-100 rounded-xl">
                                <div class="row g-2 fs-7">
                                    <div class="col-sm-4">Total Fare: <strong>₹<span x-text="totalFare.toFixed(2)"></span></strong></div>
                                    <div class="col-sm-4 text-success">Advance Collected: <strong>₹<span x-text="advanceToPay.toFixed(2)"></span></strong></div>
                                    <div class="col-sm-4 text-danger">Remaining Balance: <strong>₹<span x-text="remainingBalance.toFixed(2)"></span></strong></div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment metadata (visible if collecting payment now) -->
                        <template x-if="collectType !== 'pay_later'">
                            <div class="row g-3 mt-1">
                                <div class="col-md-6">
                                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Payment Method <span class="text-danger">*</span></label>
                                    <select class="form-select form-select-sm" name="payment_method">
                                        <option value="Cash">Cash</option>
                                        <option value="UPI">UPI</option>
                                        <option value="PhonePe">PhonePe</option>
                                        <option value="Google Pay">Google Pay</option>
                                        <option value="Paytm">Paytm</option>
                                        <option value="Credit Card">Credit Card</option>
                                        <option value="Debit Card">Debit Card</option>
                                        <option value="Net Banking">Net Banking</option>
                                        <option value="Bank Transfer">Bank Transfer</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Transaction ID / UTR Number</label>
                                    <input type="text" class="form-control form-control-sm" name="transaction_id" placeholder="Enter transaction reference UTR">
                                </div>
                                <div class="col-md-6">
                                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Payment Screenshot</label>
                                    <input type="file" class="form-control form-control-sm" name="payment_screenshot" accept="image/*">
                                </div>
                                <div class="col-md-6 d-flex align-items-end">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="generate_receipt" id="gen_rec_chk" value="1" checked>
                                        <label class="form-check-label fs-7 font-bold text-slate-700" for="gen_rec_chk">
                                            Generate Payment Receipt <code class="text-primary font-semibold">RCPT-{{ now()->format('Ymd') }}-XXX</code>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <div class="col-12">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Internal Admin Notes</label>
                            <textarea class="form-control" name="notes" rows="2" placeholder="Write any payment or approval instructions here..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top p-3 bg-slate-50">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success text-white btn-sm px-4 font-bold shadow-sm">Confirm Approval & Record</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================
     MODAL 2: COLLECT PAYMENT
     ======================================================== -->
<div class="modal fade" id="collectPaymentModal" tabindex="-1" aria-labelledby="collectPaymentModalLabel" aria-hidden="true" x-data="collectPaymentApp()">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <form id="collect-payment-form" action="{{ route('cabOrders.collectPayment', $order->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header bg-slate-900 text-white py-3">
                    <h5 class="modal-title font-bold fs-6" id="collectPaymentModalLabel"><i class="fas fa-wallet text-warning me-2"></i>Record Payment Collection</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="p-3 bg-slate-50 border rounded-xl mb-4">
                        <div class="row text-center g-2 fs-7">
                            <div class="col-6 border-end">Total Booking Fare: <strong class="d-block text-slate-800">₹{{ number_format($totalFare, 2) }}</strong></div>
                            <div class="col-6">Outstanding Balance: <strong class="d-block text-danger">₹{{ number_format($remainingAmount, 2) }}</strong></div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Collection Amount (₹) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control font-bold text-slate-800" name="payment_amount" x-model="collectAmount" @input="recalculateStatus()" min="0.01" max="{{ $remainingAmount }}" placeholder="Enter collection amount">
                        </div>

                        <div class="col-md-6">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Payment Method <span class="text-danger">*</span></label>
                            <select class="form-select" name="payment_method">
                                <option value="Cash">Cash</option>
                                <option value="UPI">UPI</option>
                                <option value="PhonePe">PhonePe</option>
                                <option value="Google Pay">Google Pay</option>
                                <option value="Paytm">Paytm</option>
                                <option value="Credit Card">Credit Card</option>
                                <option value="Debit Card">Debit Card</option>
                                <option value="Net Banking">Net Banking</option>
                                <option value="Bank Transfer">Bank Transfer</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Transaction ID / UTR Number</label>
                            <input type="text" class="form-control" name="transaction_id" placeholder="Enter UTR reference">
                        </div>

                        <div class="col-md-6">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Transaction screenshot</label>
                            <input type="file" class="form-control" name="payment_screenshot" accept="image/*">
                        </div>

                        <div class="col-md-6">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Payment Status <span class="text-danger">*</span></label>
                            <select class="form-select font-bold" name="payment_status" x-model="paymentStatus">
                                <option value="paid" class="text-success">Paid (Fully Collected)</option>
                                <option value="partially_paid" class="text-warning">Partially Paid</option>
                                <option value="pending" class="text-muted">Pending Clearance</option>
                                <option value="failed" class="text-danger">Failed</option>
                                <option value="refunded" class="text-secondary">Refunded</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="generate_receipt" id="gen_rec_chk_coll" value="1" checked>
                                <label class="form-check-label fs-7 font-bold text-slate-700" for="gen_rec_chk_coll">
                                    Generate Payment Receipt <code class="text-primary font-semibold">RCPT-{{ now()->format('Ymd') }}-XXX</code>
                                </label>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Internal Admin Notes</label>
                            <textarea class="form-control" name="notes" rows="2" placeholder="Write any specific notes..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top p-3 bg-slate-50">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-warning text-dark btn-sm px-4 font-bold shadow-sm">Record Payment Collection</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================
     MODAL 3: ASSIGN DRIVER
     ======================================================== -->
<div class="modal fade" id="assignDriverModal" tabindex="-1" aria-labelledby="assignDriverModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <form id="assign-driver-form" action="{{ route('cabOrders.assignDriver', $order->id) }}" method="POST">
                @csrf
                <div class="modal-header bg-slate-900 text-white py-3">
                    <h5 class="modal-title font-bold fs-6" id="assignDriverModalLabel"><i class="fas fa-steering-wheel fa-car text-info me-2"></i>Assign Driver & Fleet Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Driver Name <span class="text-danger">*</span></label>
                            <div class="relative">
                                <input type="text" class="form-control" name="driver_name" value="{{ $order->driver_name }}" placeholder="e.g. Rahul Kumar" required>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Driver Mobile <span class="text-danger">*</span></label>
                            <div class="relative">
                                <input type="text" class="form-control" name="driver_mobile" value="{{ $order->driver_mobile }}" placeholder="e.g. 9876543210" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top p-3 bg-slate-50">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-info text-white btn-sm px-4 font-bold shadow-sm">Assign Driver & Notify</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================================
     MODAL 4: VIEW TRANSACTION RECEIPT DETAILS
     ======================================================== -->
<div class="modal fade" id="viewReceiptModal" tabindex="-1" aria-labelledby="viewReceiptModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-slate-900 text-white py-3">
                <h5 class="modal-title font-bold fs-6" id="viewReceiptModalLabel"><i class="fas fa-receipt text-primary me-2"></i>Payment Receipt Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="receipt-details-body">
                <!-- Dynamically Populated -->
            </div>
            <div class="modal-footer border-top p-3 bg-slate-50">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary btn-sm" id="print-modal-receipt-btn">Print Receipt</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<!-- Bootstrap 5 JS Bundle (needed for modals on this page) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
{{-- DataTables JS is loaded globally via footer (dist/Datatable2.18) - no CDN duplicate needed --}}

<script>
    // ── SweetAlert2 Flash Message Auto-Fire ─────────────────────────
    document.addEventListener('DOMContentLoaded', function () {
        @if(session('success'))
            // Hide the inline fallback bar (Swal takes over)
            const successBar = document.getElementById('flash-success-bar');
            if (successBar) successBar.style.display = 'none';

            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: @json(session('success')),
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true,
                customClass: { popup: 'shadow-lg' }
            });
        @endif

        @if(session('error'))
            const errorBar = document.getElementById('flash-error-bar');
            if (errorBar) errorBar.style.display = 'none';

            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: @json(session('error')),
                showConfirmButton: true,
                confirmButtonText: 'OK',
                confirmButtonColor: '#dc3545',
            });
        @endif
    });

    // Initialize DataTables
    $(document).ready(function() {
        $('#payments-table').DataTable({
            "order": [[ 0, "desc" ]],
            "paging": false,
            "info": false,
            "searching": false,
            "responsive": true,
            "language": {
                "emptyTable": '<div class="text-center text-muted fs-7 py-2"><i class="fas fa-info-circle text-primary me-1.5"></i> No payment collections recorded yet.</div>'
            }
        });
    });

    // ----------------------------------------------------
    // GOOGLE MAPS LOADER & ROUTE DISPLAY
    // ----------------------------------------------------
    function initRouteMap() {
        if (typeof google === 'undefined') return;

        const directionsService = new google.maps.DirectionsService();
        const directionsRenderer = new google.maps.DirectionsRenderer({
            suppressMarkers: false,
            polylineOptions: {
                strokeColor: "#3B82F6",
                strokeOpacity: 0.8,
                strokeWeight: 5
            }
        });

        const mapElement = document.getElementById("route-map");
        if (!mapElement) return;

        const map = new google.maps.Map(mapElement, {
            zoom: 12,
            center: { lat: {{ (float)($order->pickup_lat ?? 28.6139) }}, lng: {{ (float)($order->pickup_lng ?? 77.2090) }} },
            mapTypeControl: false,
            streetViewControl: false,
            fullscreenControl: true,
            styles: [{ "featureType": "poi", "stylers": [{ "visibility": "off" }] }]
        });

        directionsRenderer.setMap(map);

        const pickup = { lat: {{ (float)($order->pickup_lat ?? 0) }}, lng: {{ (float)($order->pickup_lng ?? 0) }} };
        const drop = { lat: {{ (float)($order->drop_lat ?? 0) }}, lng: {{ (float)($order->drop_lng ?? 0) }} };
        
        if (!pickup.lat || !drop.lat) return;

        let origin = pickup;
        let destination = drop;
        let waypoints = [];

        @if($order->trip_type === 'round_trip')
            waypoints.push({
                location: drop,
                stopover: true
            });

            @if($order->return_pickup_lat && $order->return_pickup_lng)
                waypoints.push({
                    location: { lat: {{ (float)$order->return_pickup_lat }}, lng: {{ (float)$order->return_pickup_lng }} },
                    stopover: true
                });
            @endif

            @if($order->return_drop_lat && $order->return_drop_lng)
                destination = { lat: {{ (float)$order->return_drop_lat }}, lng: {{ (float)$order->return_drop_lng }} };
            @else
                destination = pickup;
            @endif
        @endif

        directionsService.route(
            {
                origin: origin,
                destination: destination,
                waypoints: waypoints,
                optimizeWaypoints: false,
                travelMode: google.maps.TravelMode.DRIVING,
            },
            (response, status) => {
                if (status === "OK") {
                    directionsRenderer.setDirections(response);
                } else {
                    console.error("Directions request failed due to " + status);
                    new google.maps.Marker({ position: pickup, map: map, title: "Pickup" });
                    new google.maps.Marker({ position: drop, map: map, title: "Drop-off" });
                }
            }
        );
    }

    // Call map renderer on window load
    window.addEventListener('load', () => {
        initRouteMap();
    });

    // ----------------------------------------------------
    // CRM INTERACTIVE HELPERS (WhatsApp, payment links, print receipts)
    // ----------------------------------------------------
    function sendWhatsAppConfirmation() {
        const orderNumber = "{{ $order->order_number }}";
        const customerName = "{{ $order->customer_name }}";
        const mobile = "{{ $order->customer_mobile }}";
        const vehicle = "{{ $order->car_name }}";
        const route = "{{ $order->pickup_address }} to {{ $order->drop_address }}";
        const date = "{{ $order->pickup_date ? \Carbon\Carbon::parse($order->pickup_date)->format('d-m-Y') : '-' }}";
        const time = "{{ $order->pickup_time }}";
        const status = "{{ ucfirst($order->booking_status) }}";
        const payStatus = "{{ ucfirst($order->payment_status) }}";
        const fare = "₹" + parseFloat("{{ $order->total_amount }}").toLocaleString('en-IN');
        const advance = "₹" + parseFloat("{{ $order->advance_payment }}").toLocaleString('en-IN');
        const remaining = "₹" + (parseFloat("{{ $order->total_amount }}") - parseFloat("{{ $order->advance_payment }}")).toLocaleString('en-IN');
        
        let driverStr = "Not Assigned Yet";
        @if($order->driver_name)
            driverStr = "{{ $order->driver_name }} ({{ $order->driver_mobile }})";
        @endif

        const message = `*SAMPURNA BHARAT DARSHAN (SBD GROUP)*\n` +
                        `*Booking Confirmation*\n\n` +
                        `*Order Number:* ${orderNumber}\n` +
                        `*Customer Name:* ${customerName}\n` +
                        `*Mobile:* ${mobile}\n` +
                        `*Vehicle Type:* ${vehicle}\n` +
                        `*Trip Route:* ${route}\n` +
                        `*Trip Date & Time:* ${date} at ${time}\n` +
                        `*Driver Details:* ${driverStr}\n\n` +
                        `*Booking Status:* ${status}\n` +
                        `*Payment Status:* ${payStatus}\n` +
                        `*Total Fare:* ${fare}\n` +
                        `*Advance Paid:* ${advance}\n` +
                        `*Remaining Amount:* ${remaining}\n\n` +
                        `Thank you for choosing SBD Tours & Travels!`;

        const whatsappUrl = `https://api.whatsapp.com/send?phone=91${mobile.trim()}&text=${encodeURIComponent(message)}`;
        window.open(whatsappUrl, '_blank');
    }

    function generatePaymentLink() {
        const payUrl = `http://sbdbooking.com/payment/pay/{{ $order->order_number }}`;
        
        Swal.fire({
            title: 'Shareable Payment Link',
            html: `<div class="mb-3">Use the link below to request customer payment:</div>
                   <input type="text" id="payment-link-val" class="form-control text-center text-sm font-semibold mb-2" value="${payUrl}" readonly>`,
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-copy me-1"></i> Copy Link',
            cancelButtonText: 'Close',
            customClass: {
                confirmButton: 'btn btn-primary rounded-pill px-4',
                cancelButton: 'btn btn-outline-secondary rounded-pill px-4'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                const copyText = document.getElementById("payment-link-val");
                copyText.select();
                copyText.setSelectionRange(0, 99999);
                navigator.clipboard.writeText(copyText.value);
                
                Swal.fire({
                    icon: 'success',
                    title: 'Copied!',
                    text: 'Payment link copied to clipboard.',
                    timer: 1500,
                    showConfirmButton: false
                });
            }
        });
    }

    // ----------------------------------------------------
    // SWEETALERT CONFIRMATIONS FOR DELETES/STATUSES
    // ----------------------------------------------------
    function confirmApprovePayment() {
        Swal.fire({
            title: 'Mark Order Fully Paid?',
            text: 'Are you sure you want to change the payment status of this booking to PAID?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Yes, Approve Payment',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('approve-payment-form').submit();
            }
        });
    }

    function confirmCancelBooking() {
        Swal.fire({
            title: 'Cancel Cab Booking?',
            text: 'Are you sure you want to cancel this booking? This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Yes, Cancel Trip',
            cancelButtonText: 'Go Back'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('cancel-booking-form').submit();
            }
        });
    }

    // ----------------------------------------------------
    // ALPINEJS CONTROLLERS FOR CALCULATORS
    // ----------------------------------------------------
    function approvalSetup() {
        return {
            collectType: 'pay_later',
            advanceType: 'percentage',
            advancePercentage: 30,
            fixedAdvanceAmount: 0,
            totalFare: {{ $totalFare }},
            advanceToPay: 0,
            remainingBalance: {{ $totalFare }},

            init() {
                this.recalculate();
            },

            recalculate() {
                if (this.collectType === 'pay_later') {
                    this.advanceToPay = 0;
                    this.remainingBalance = this.totalFare;
                } else if (this.collectType === 'full') {
                    this.advanceToPay = this.totalFare;
                    this.remainingBalance = 0;
                } else if (this.collectType === 'advance') {
                    if (this.advanceType === 'percentage') {
                        this.advanceToPay = (this.totalFare * parseInt(this.advancePercentage)) / 100;
                    } else {
                        this.advanceToPay = parseFloat(this.fixedAdvanceAmount || 0);
                    }
                    this.remainingBalance = Math.max(0, this.totalFare - this.advanceToPay);
                }
            }
        };
    }

    function collectPaymentApp() {
        return {
            collectAmount: {{ $remainingAmount }},
            remainingBalance: {{ $remainingAmount }},
            paymentStatus: 'paid',

            init() {
                this.recalculateStatus();
            },

            recalculateStatus() {
                const amt = parseFloat(this.collectAmount || 0);
                if (amt >= this.remainingBalance) {
                    this.paymentStatus = 'paid';
                } else if (amt > 0) {
                    this.paymentStatus = 'partially_paid';
                } else {
                    this.paymentStatus = 'pending';
                }
            }
        };
    }

    // ----------------------------------------------------
    // RECEIPT DETAILS VIEWING & PRINTING (HTML DOM)
    // ----------------------------------------------------
    let activeReceiptData = null;
    let activeOrderData = null;

    function viewPaymentDetails(payment) {
        activeReceiptData = payment;
        
        let screenshotHtml = '<span class="text-muted">No upload provided</span>';
        if (payment.screenshot_path) {
            screenshotHtml = `<a href="/storage/${payment.screenshot_path}" target="_blank" class="btn btn-outline-primary btn-xs mt-1 d-inline-flex align-items-center gap-1">
                                <i class="fas fa-image"></i> View Attachment
                             </a>`;
        }

        const html = `
            <div class="space-y-3 fs-7">
                <div class="row">
                    <div class="col-sm-5 text-muted">Receipt Number:</div>
                    <div class="col-sm-7"><strong>${payment.receipt_number}</strong></div>
                </div>
                <div class="row">
                    <div class="col-sm-5 text-muted">Payment Date:</div>
                    <div class="col-sm-7"><strong>${new Date(payment.created_at).toLocaleString()}</strong></div>
                </div>
                <div class="row">
                    <div class="col-sm-5 text-muted">Amount Received:</div>
                    <div class="col-sm-7"><strong class="text-primary fs-6">₹${parseFloat(payment.amount).toFixed(2)}</strong></div>
                </div>
                <div class="row">
                    <div class="col-sm-5 text-muted">Payment Method:</div>
                    <div class="col-sm-7"><span class="badge bg-light text-slate-800 border">${payment.payment_method}</span></div>
                </div>
                <div class="row">
                    <div class="col-sm-5 text-muted">Transaction/UTR ID:</div>
                    <div class="col-sm-7"><code class="text-muted font-mono">${payment.transaction_id || 'N/A'}</code></div>
                </div>
                <div class="row">
                    <div class="col-sm-5 text-muted">Screenshot / Proof:</div>
                    <div class="col-sm-7">${screenshotHtml}</div>
                </div>
                <div class="row">
                    <div class="col-sm-5 text-muted">Collected By:</div>
                    <div class="col-sm-7"><strong>${payment.added_by || 'Admin'}</strong></div>
                </div>
                <div class="row">
                    <div class="col-sm-5 text-muted">Internal Notes:</div>
                    <div class="col-sm-7"><p class="mb-0 text-slate-600">${payment.notes || 'No notes added.'}</p></div>
                </div>
            </div>
        `;

        document.getElementById('receipt-details-body').innerHTML = html;
        
        // Setup print button
        document.getElementById('print-modal-receipt-btn').onclick = function() {
            printReceipt(payment, {
                order_number: "{{ $order->order_number }}",
                customer_name: "{{ $order->customer_name }}",
                customer_mobile: "{{ $order->customer_mobile }}",
                car_name: "{{ $order->car_name }}"
            });
        };

        const modal = new bootstrap.Modal(document.getElementById('viewReceiptModal'));
        modal.show();
    }

    function printReceipt(payment, order) {
        const printWindow = window.open('', '_blank', 'width=800,height=600');
        
        const content = `
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="utf-8">
                <title>Receipt - ${payment.receipt_number}</title>
                <style>
                    body {
                        font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
                        color: #333;
                        padding: 30px;
                        line-height: 1.5;
                    }
                    .header {
                        text-align: center;
                        border-bottom: 2px solid #333;
                        padding-bottom: 10px;
                        margin-bottom: 30px;
                    }
                    .company-name {
                        font-size: 22px;
                        font-weight: bold;
                        color: #A67C00;
                        margin: 0;
                    }
                    .company-subtitle {
                        font-size: 14px;
                        margin: 5px 0 0 0;
                        color: #666;
                    }
                    .title {
                        text-align: center;
                        font-size: 18px;
                        font-weight: bold;
                        margin-bottom: 20px;
                        text-decoration: underline;
                    }
                    table {
                        width: 100%;
                        border-collapse: collapse;
                        margin-bottom: 30px;
                    }
                    th, td {
                        padding: 10px 12px;
                        text-align: left;
                        border-bottom: 1px solid #ddd;
                        font-size: 14px;
                    }
                    th {
                        background-color: #f9f9f9;
                        width: 35%;
                        font-weight: 600;
                    }
                    .amount-row {
                        background-color: #fdfdfd;
                    }
                    .amount {
                        font-size: 18px;
                        font-weight: bold;
                        color: #0047AB;
                    }
                    .footer {
                        margin-top: 50px;
                        display: flex;
                        justify-content: space-between;
                    }
                    .signature {
                        border-top: 1px solid #999;
                        width: 200px;
                        text-align: center;
                        padding-top: 8px;
                        font-size: 12px;
                        font-weight: bold;
                    }
                    @media print {
                        body { padding: 10px; }
                        button { display: none; }
                    }
                </style>
            </head>
            <body>
                <div class="header">
                    <h1 class="company-name">SAMPURNA BHARAT DARSHAN</h1>
                    <h2 class="company-subtitle">MAA SHARADA TOUR AND TRAVELS - SBD GROUP</h2>
                    <div style="font-size: 11px; margin-top: 5px;">Anuppur, Shahdol, Umaria, Katni, Jabalpur District Area</div>
                </div>

                <div class="title">PAYMENT RECEIPT</div>

                <table>
                    <tr>
                        <th>Receipt Number</th>
                        <td><strong>${payment.receipt_number}</strong></td>
                    </tr>
                    <tr>
                        <th>Payment Date & Time</th>
                        <td>${new Date(payment.created_at).toLocaleString()}</td>
                    </tr>
                    <tr>
                        <th>Booking / Order Reference</th>
                        <td>${order.order_number}</td>
                    </tr>
                    <tr>
                        <th>Customer Details</th>
                        <td>${order.customer_name} (${order.customer_mobile})</td>
                    </tr>
                    <tr>
                        <th>Vehicle Assigned</th>
                        <td>${order.car_name.toUpperCase()}</td>
                    </tr>
                    <tr>
                        <th>Payment Method</th>
                        <td>${payment.payment_method}</td>
                    </tr>
                    <tr>
                        <th>UTR / Transaction Reference</th>
                        <td><code>${payment.transaction_id || 'N/A'}</code></td>
                    </tr>
                    <tr class="amount-row">
                        <th>Amount Received</th>
                        <td><span class="amount">₹${parseFloat(payment.amount).toFixed(2)}</span></td>
                    </tr>
                </table>

                <div class="footer">
                    <div>
                        <p style="font-size: 11px; color:#777; margin:0;">Receipt issued electronically.</p>
                        <p style="font-size: 11px; color:#777; margin:0;">Status: APPROVED / RECEIVED</p>
                    </div>
                    <div class="signature">
                        Authorized Signatory<br>
                        (SBD Tours & Travels)
                    </div>
                </div>

                <div style="text-align:center; margin-top: 40px;">
                    <button onclick="window.print()" style="padding: 10px 24px; font-weight: bold; background: #0047AB; color: white; border: none; border-radius: 5px; cursor: pointer;">Print Receipt</button>
                </div>
            </body>
            </html>
        `;

        printWindow.document.write(content);
        printWindow.document.close();
    }
</script>
@endsection
