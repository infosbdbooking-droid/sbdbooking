@extends('layouts.app')

@section('content')
<div class="container mx-auto py-6 space-y-6">
    @php
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

    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Booking Details</h1>
            <p class="text-gray-500">Order {{ $order->order_number }}</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('cabOrders') }}" class="px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50 transition">
                <i class="fas fa-arrow-left mr-2"></i> Back to List
            </a>
            <a href="{{ $navigationUrl }}" target="_blank" class="px-4 py-2 bg-green-600 text-white rounded shadow hover:bg-green-700 transition">
                <i class="fas fa-play mr-2"></i> Start Trip
            </a>
            <a href="{{ route('cabOrders.invoice', $order->id) }}" class="px-4 py-2 bg-blue-600 text-white rounded shadow hover:bg-blue-700 transition">
                Print Invoice
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-green-100 border border-green-200 text-green-800 rounded-xl flex items-center gap-2 shadow-sm">
            <i class="fas fa-check-circle text-lg text-green-600 animate-pulse"></i>
            <div class="font-semibold">{{ session('success') }}</div>
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 bg-red-100 border border-red-200 text-red-800 rounded-xl flex items-center gap-2 shadow-sm">
            <i class="fas fa-times-circle text-lg text-red-600"></i>
            <div class="font-semibold">{{ session('error') }}</div>
        </div>
    @endif

    <!-- Booking Actions Panel -->
    <div class="bg-gradient-to-r from-slate-800 to-slate-900 text-white rounded-xl shadow-lg border border-slate-700 p-6 flex flex-col md:flex-row justify-between items-center gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-blue-500/20 border border-blue-500/30 rounded-xl flex items-center justify-center text-blue-400 text-xl shadow-inner">
                <i class="fas fa-tasks"></i>
            </div>
            <div>
                <h4 class="font-bold text-base leading-tight">Order & Payment Action Control</h4>
                <p class="text-xs text-gray-400 mt-0.5">Approve bookings and register received payments instantly.</p>
            </div>
        </div>
        
        <div class="flex flex-wrap gap-3 items-center w-full md:w-auto justify-end">
            <!-- Accept Button -->
            @if($order->booking_status === 'pending')
                <form action="{{ route('cabOrders.accept', $order->id) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="w-full md:w-auto px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg shadow hover:shadow-lg transition-all duration-150 flex items-center justify-center gap-2 text-sm uppercase tracking-wide">
                        <i class="fas fa-check-circle text-base"></i>
                        Accept & Approve Order
                    </button>
                </form>
            @endif

            <!-- Approve Payment Button -->
            @if($order->payment_status !== 'paid')
                <form action="{{ route('cabOrders.approvePayment', $order->id) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="w-full md:w-auto px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-lg shadow hover:shadow-lg transition-all duration-150 flex items-center justify-center gap-2 text-sm uppercase tracking-wide">
                        <i class="fas fa-receipt text-base"></i>
                        Approve Payment
                    </button>
                </form>
            @endif

            <!-- Cancel Button -->
            @if($order->booking_status === 'pending' || $order->booking_status === 'confirmed')
                <form action="{{ route('cabOrders.cancel', $order->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to cancel this booking?')">
                    @csrf
                    <button type="submit" class="w-full md:w-auto px-4 py-2.5 bg-rose-600/20 hover:bg-rose-600 text-rose-300 hover:text-white border border-rose-500/30 rounded-lg transition-all duration-150 flex items-center justify-center gap-2 text-sm">
                        <i class="fas fa-times-circle"></i>
                        Cancel Booking
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left Column: Details -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Route Details -->
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 border-b pb-3 mb-4"><i class="fas fa-route text-blue-500 mr-2"></i> Route Details</h3>
                
                <!-- Interactive Map -->
                @if($order->pickup_lat && $order->pickup_lng && $order->drop_lat && $order->drop_lng)
                    <div id="route-map" class="w-full h-72 rounded-lg mb-6 border border-gray-200 bg-gray-50 shadow-inner"></div>
                @endif

                <div class="relative pl-6 border-l-2 border-dashed border-gray-300 ml-3 space-y-8">
                    <!-- Pickup -->
                    <div class="relative">
                        <div class="absolute -left-9 top-1 w-5 h-5 rounded-full border-4 border-white bg-blue-500"></div>
                        <h4 class="font-medium text-gray-800">Pickup</h4>
                        <p class="text-sm text-gray-600 mt-1">{{ $order->pickup_address }}</p>
                        @if($order->pickup_lat && $order->pickup_lng)
                        <a href="https://maps.google.com/?q={{ $order->pickup_lat }},{{ $order->pickup_lng }}" target="_blank" class="inline-flex mt-2 items-center text-xs text-blue-600 hover:underline">
                            <i class="fas fa-map-marker-alt mr-1"></i> View on Map ({{ $order->pickup_lat }}, {{ $order->pickup_lng }})
                        </a>
                        @endif
                    </div>

                    <!-- Drop -->
                    <div class="relative">
                        <div class="absolute -left-9 top-1 w-5 h-5 rounded-full border-4 border-white bg-green-500"></div>
                        <h4 class="font-medium text-gray-800">Drop-off</h4>
                        <p class="text-sm text-gray-600 mt-1">{{ $order->drop_address }}</p>
                        @if($order->drop_lat && $order->drop_lng)
                        <a href="https://maps.google.com/?q={{ $order->drop_lat }},{{ $order->drop_lng }}" target="_blank" class="inline-flex mt-2 items-center text-xs text-blue-600 hover:underline">
                            <i class="fas fa-map-marker-alt mr-1"></i> View on Map ({{ $order->drop_lat }}, {{ $order->drop_lng }})
                        </a>
                        @endif
                    </div>
                </div>

                @if($order->trip_type === 'round_trip')
                <div class="mt-6 pt-6 border-t border-gray-100">
                    <h4 class="font-medium text-gray-800 mb-3 text-sm">Return Trip Address</h4>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div class="bg-gray-50 p-3 rounded">
                            <span class="text-xs text-gray-500 block mb-1">Return Pickup</span>
                            <p class="text-sm">{{ $order->return_pickup_address ?? 'Same as Drop-off' }}</p>
                        </div>
                        <div class="bg-gray-50 p-3 rounded">
                            <span class="text-xs text-gray-500 block mb-1">Return Drop-off</span>
                            <p class="text-sm">{{ $order->return_drop_address ?? 'Same as Pickup' }}</p>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Customer & Trip Info -->
            <div class="grid md:grid-cols-2 gap-6">
                <!-- Customer -->
                <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-800 border-b pb-3 mb-4"><i class="fas fa-user text-blue-500 mr-2"></i> Customer Info</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Name</span>
                            <span class="font-medium text-gray-800">{{ $order->customer_name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Mobile</span>
                            <span class="font-medium text-gray-800">{{ $order->customer_mobile }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Registered User</span>
                            <span class="font-medium text-gray-800">{!! $order->customer_id ? '<span class="text-green-600"><i class="fas fa-check-circle"></i> Yes</span>' : '<span class="text-gray-400">Guest</span>' !!}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Passengers / Bags</span>
                            <span class="font-medium text-gray-800">{{ $order->passengers }} Pax / {{ $order->bags }} Bags</span>
                        </div>
                    </div>
                </div>

                <!-- Schedule -->
                <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-800 border-b pb-3 mb-4"><i class="fas fa-clock text-blue-500 mr-2"></i> Schedule</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Pickup Date</span>
                            <span class="font-medium text-gray-800">{{ $order->pickup_date ? \Carbon\Carbon::parse($order->pickup_date)->format('d M, Y') : '-' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Pickup Time</span>
                            <span class="font-medium text-gray-800">{{ $order->pickup_time ? (strpos($order->pickup_time, ' to ') !== false ? $order->pickup_time : \Carbon\Carbon::parse($order->pickup_time)->format('h:i A')) : '-' }}</span>
                        </div>
                        @if($order->trip_type === 'round_trip')
                        <div class="flex justify-between">
                            <span class="text-gray-500">Return Date</span>
                            <span class="font-medium text-gray-800">{{ $order->return_date ? \Carbon\Carbon::parse($order->return_date)->format('d M, Y') : '-' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Return Time</span>
                            <span class="font-medium text-gray-800">{{ $order->return_time ? (strpos($order->return_time, ' to ') !== false ? $order->return_time : \Carbon\Carbon::parse($order->return_time)->format('h:i A')) : '-' }}</span>
                        </div>
                        @endif
                        <div class="flex justify-between">
                            <span class="text-gray-500">Pickup to Drop-off</span>
                            <span class="font-medium text-gray-800">{{ $order->one_way_km }} km</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Return to Pickup</span>
                            <span class="font-medium text-gray-800">{{ $order->return_km }} km</span>
                        </div>
                        <div class="flex justify-between pt-2 border-t border-dashed">
                            <span class="text-gray-500 font-bold">Total Billed Distance</span>
                            <span class="font-bold text-blue-600">{{ $order->total_km }} km</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            @if($order->notes_for_driver)
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg shadow-sm p-4 text-sm">
                <strong class="text-yellow-800 block mb-1"><i class="fas fa-sticky-note mr-1"></i> Notes for Driver:</strong>
                <p class="text-yellow-900">{{ $order->notes_for_driver }}</p>
            </div>
            @endif

        </div>

        <!-- Right Column: Charges & Status -->
        <div class="space-y-6">
            <!-- Payment & Status -->
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 border-b pb-3 mb-4">Summary</h3>
                
                <div class="space-y-4">
                    <div>
                        <span class="text-xs text-gray-500 uppercase tracking-wider">Booking Status</span>
                        <div class="mt-1">
                            @if($order->booking_status === 'pending')
                                <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm font-semibold inline-block">Pending</span>
                            @elseif($order->booking_status === 'confirmed')
                                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-semibold inline-block">Confirmed</span>
                            @elseif($order->booking_status === 'cancelled')
                                <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm font-semibold inline-block">Cancelled</span>
                            @else
                                <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-semibold inline-block capitalize">{{ str_replace('_', ' ', $order->booking_status) }}</span>
                            @endif
                        </div>
                    </div>

                    <div>
                        <span class="text-xs text-gray-500 uppercase tracking-wider">Payment Status</span>
                        <div class="mt-1 font-medium">
                            <span class="text-gray-800 capitalize">{{ $order->payment_status }}</span>
                            @if($order->payment_method)
                                <span class="text-gray-400 text-sm ml-2">via {{ $order->payment_method }}</span>
                            @endif
                        </div>
                    </div>

                    <div>
                        <span class="text-xs text-gray-500 uppercase tracking-wider">Car Selection</span>
                        <div class="mt-1 font-medium text-gray-800">
                            {{ $order->car_name }}
                            @if($order->is_ac) <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded ml-2">AC</span> @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Price Breakdown -->
            <div class="bg-gray-50 border border-gray-200 rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 border-b border-gray-300 pb-3 mb-4">Price Breakdown</h3>
                
                <div class="space-y-3 text-sm">
                    @php 
                        $breakdown = is_string($order->charges_breakdown) ? json_decode($order->charges_breakdown, true) : $order->charges_breakdown;
                    @endphp

                    @if($breakdown && is_array($breakdown))
                        @foreach($breakdown as $item)
                            <div class="flex justify-between items-start">
                                <div>
                                    <span class="text-gray-700">{{ $item['charge_type'] ?? $item['type'] }}</span>
                                    @if(isset($item['distance']))
                                        <div class="text-xs text-gray-500">({{ $item['distance'] }} km × ₹{{ $item['rate'] }})</div>
                                    @endif
                                </div>
                                <span class="font-medium text-gray-900">
                                    @if(isset($item['amount']))
                                        ₹{{ number_format((float)$item['amount'], 2) }}
                                    @elseif(isset($item['status']))
                                        <span class="text-green-600 text-xs">{{ $item['status'] }}</span>
                                    @endif
                                </span>
                            </div>
                        @endforeach
                    @else
                        <!-- Fallback if JSON is missing -->
                        <div class="flex justify-between">
                            <span class="text-gray-700">Per KM Charges</span>
                            <span class="font-medium text-gray-900">₹{{ number_format($order->per_km_amount, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-700">Driver Allowance</span>
                            <span class="font-medium text-gray-900">₹{{ number_format($order->driver_allowance, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-700">Platform Fees</span>
                            <span class="font-medium text-gray-900">₹{{ number_format($order->platform_charges, 2) }}</span>
                        </div>
                    @endif

                    <!-- Coupon -->
                    @if($order->discount_amount > 0)
                    <div class="flex justify-between text-green-600 pt-2 border-t border-dashed border-gray-300">
                        <span>Discount ({{ $order->coupon_code }})</span>
                        <span class="font-medium">-₹{{ number_format($order->discount_amount, 2) }}</span>
                    </div>
                    @endif

                    <!-- Total -->
                    <div class="flex justify-between items-center pt-3 border-t border-gray-300 mt-3">
                        <span class="text-base font-bold text-gray-900">Total Amount</span>
                        <span class="text-xl font-bold text-blue-600">₹{{ number_format($order->total_amount, 2) }}</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
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

        const map = new google.maps.Map(document.getElementById("route-map"), {
            zoom: 12,
            center: { lat: {{ (float)$order->pickup_lat }}, lng: {{ (float)$order->pickup_lng }} },
            mapTypeControl: false,
            streetViewControl: false,
            fullscreenControl: true,
            styles: [{ "featureType": "poi", "stylers": [{ "visibility": "off" }] }]
        });

        directionsRenderer.setMap(map);

        const pickup = { lat: {{ (float)$order->pickup_lat }}, lng: {{ (float)$order->pickup_lng }} };
        const drop = { lat: {{ (float)$order->drop_lat }}, lng: {{ (float)$order->drop_lng }} };
        
        let origin = pickup;
        let destination = drop;
        let waypoints = [];

        @if($order->trip_type === 'round_trip')
            // For round trip, we go Pickup -> Drop -> Return Pickup (if any) -> Return Drop
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
                destination = pickup; // Default return to pickup location
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
                    
                    // Fallback to simple markers if directions fail
                    new google.maps.Marker({ position: pickup, map: map, title: "Pickup" });
                    new google.maps.Marker({ position: drop, map: map, title: "Drop-off" });
                    
                    const bounds = new google.maps.LatLngBounds();
                    bounds.extend(pickup);
                    bounds.extend(drop);
                    map.fitBounds(bounds);
                }
            }
        );
    }

    window.onload = function() {
        initRouteMap();
    };
</script>
@endsection

