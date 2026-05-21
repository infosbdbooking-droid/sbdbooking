@extends('layouts.app')

@section('content')
<div class="space-y-6 pb-10" x-data="{ search: '', statusFilter: 'all' }">
    
    <!-- 1. TOP SUMMARY CARDS (4-Column Grid) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Bookings -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 hover-premium transition flex flex-col justify-between h-36 group cursor-pointer relative overflow-hidden">
            <div class="absolute right-0 top-0 h-16 w-16 bg-[#d84e55]/5 rounded-bl-full flex items-center justify-end pr-3 pt-3 transition-transform group-hover:scale-105 duration-350">
                <i class="fas fa-calendar-check text-[#d84e55] text-lg opacity-60"></i>
            </div>
            <div class="flex items-center gap-3">
                <div class="p-3 bg-red-50 text-[#d84e55] rounded-xl group-hover:bg-[#d84e55] group-hover:text-white transition duration-300 shadow-sm">
                    <i class="fas fa-receipt text-lg"></i>
                </div>
                <div>
                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest block mb-0.5">Total Bookings</span>
                    <p class="text-2xl font-black text-slate-800 leading-none">{{ number_format($stats['total_bookings']) }}</p>
                </div>
            </div>
            <div class="mt-4">
                <div class="flex justify-between items-center text-[10px] text-slate-500 font-semibold mb-1">
                    <span>Target fulfillment</span>
                    <span class="text-[#d84e55] font-extrabold">+12% this week</span>
                </div>
                <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden">
                    <div class="bg-[#d84e55] h-full rounded-full transition-all duration-500" style="width: 88%"></div>
                </div>
            </div>
        </div>

        <!-- Total Revenue -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 hover-premium transition flex flex-col justify-between h-36 group cursor-pointer relative overflow-hidden">
            <div class="absolute right-0 top-0 h-16 w-16 bg-emerald-50 rounded-bl-full flex items-center justify-end pr-3 pt-3 transition-transform group-hover:scale-105 duration-350">
                <i class="fas fa-indian-rupee-sign text-emerald-600 text-lg opacity-60"></i>
            </div>
            <div class="flex items-center gap-3">
                <div class="p-3 bg-emerald-50 text-emerald-600 rounded-xl group-hover:bg-emerald-600 group-hover:text-white transition duration-300 shadow-sm">
                    <i class="fas fa-wallet text-lg"></i>
                </div>
                <div>
                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest block mb-0.5">Total Earnings</span>
                    <p class="text-2xl font-black text-slate-800 leading-none">₹{{ $stats['total_revenue'] >= 100000 ? number_format($stats['total_revenue'] / 100000, 1) . 'L' : number_format($stats['total_revenue']) }}</p>
                </div>
            </div>
            <div class="mt-4">
                <div class="flex justify-between items-center text-[10px] text-slate-500 font-semibold mb-1">
                    <span>Monthly Goal</span>
                    <span class="text-emerald-600 font-extrabold">+24% target</span>
                </div>
                <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden">
                    <div class="bg-emerald-500 h-full rounded-full transition-all duration-500" style="width: 92%"></div>
                </div>
            </div>
        </div>

        <!-- Completed Trips -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 hover-premium transition flex flex-col justify-between h-36 group cursor-pointer relative overflow-hidden">
            <div class="absolute right-0 top-0 h-16 w-16 bg-blue-50 rounded-bl-full flex items-center justify-end pr-3 pt-3 transition-transform group-hover:scale-105 duration-350">
                <i class="fas fa-check-circle text-blue-600 text-lg opacity-60"></i>
            </div>
            <div class="flex items-center gap-3">
                <div class="p-3 bg-blue-50 text-blue-600 rounded-xl group-hover:bg-blue-600 group-hover:text-white transition duration-300 shadow-sm">
                    <i class="fas fa-route text-lg"></i>
                </div>
                <div>
                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest block mb-0.5">Completed Rides</span>
                    <p class="text-2xl font-black text-slate-800 leading-none">{{ number_format($stats['completed_trips']) }}</p>
                </div>
            </div>
            <div class="mt-4">
                <div class="flex justify-between items-center text-[10px] text-slate-500 font-semibold mb-1">
                    <span>Completion Rate</span>
                    <span class="text-emerald-600 font-extrabold">86% active</span>
                </div>
                <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden">
                    <div class="bg-blue-500 h-full rounded-full transition-all duration-500" style="width: 86%"></div>
                </div>
            </div>
        </div>

        <!-- Available Cars -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 hover-premium transition flex flex-col justify-between h-36 group cursor-pointer relative overflow-hidden">
            <div class="absolute right-0 top-0 h-16 w-16 bg-amber-50 rounded-bl-full flex items-center justify-end pr-3 pt-3 transition-transform group-hover:scale-105 duration-350">
                <i class="fas fa-bus-alt text-amber-600 text-lg opacity-60"></i>
            </div>
            <div class="flex items-center gap-3">
                <div class="p-3 bg-amber-50 text-amber-600 rounded-xl group-hover:bg-amber-600 group-hover:text-white transition duration-300 shadow-sm">
                    <i class="fas fa-shuttle-van text-lg"></i>
                </div>
                <div>
                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest block mb-0.5">Active Fleet</span>
                    <p class="text-2xl font-black text-slate-800 leading-none">{{ number_format($stats['available_cars']) }}</p>
                </div>
            </div>
            <div class="mt-4">
                <div class="flex justify-between items-center text-[10px] text-slate-500 font-semibold mb-1">
                    <span>Fleet Availability</span>
                    <span class="text-slate-400 font-extrabold">90% available</span>
                </div>
                <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden">
                    <div class="bg-amber-500 h-full rounded-full transition-all duration-500" style="width: 90%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. SECONDARY METRIC GRID (2-Column Wide Cards) -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Active Trips Card -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex items-center justify-between hover-premium transition duration-300">
            <div class="flex items-center gap-4">
                <div class="p-3.5 bg-red-50 text-[#d84e55] rounded-2xl shadow-inner-sm">
                    <i class="fas fa-route text-xl"></i>
                </div>
                <div>
                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-0.5">In Progress</span>
                    <p class="text-base font-extrabold text-slate-800">{{ number_format($stats['active_trips']) }} Cabs / Buses on Roads</p>
                </div>
            </div>
            <a href="{{ route('cabOrders') }}" class="text-xs font-bold text-[#d84e55] hover:text-[#c6393f] bg-red-50 px-3.5 py-2 rounded-xl transition shadow-sm">Manage</a>
        </div>

        <!-- Pending Bookings Card -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex items-center justify-between hover-premium transition duration-300">
            <div class="flex items-center gap-4">
                <div class="p-3.5 bg-rose-50 text-rose-600 rounded-2xl shadow-inner-sm animate-pulse">
                    <i class="fas fa-exclamation-circle text-xl"></i>
                </div>
                <div>
                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-0.5">Awaiting Action</span>
                    <p class="text-base font-extrabold text-rose-600">{{ number_format($stats['pending_orders']) }} Inquiries Awaiting Review</p>
                </div>
            </div>
            <a href="{{ route('cabOrders') }}" class="text-xs font-bold text-rose-600 hover:text-rose-700 bg-rose-50 px-3.5 py-2 rounded-xl transition shadow-sm">Review Now</a>
        </div>
    </div>

    <!-- 3. TODAY'S PULSE & REVENUE CHART (1/3 and 2/3 Layout) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left 1/3 Column: Pulse & Info -->
        <div class="space-y-6 lg:col-span-1">
            <!-- Today's Pulse Panel -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 relative overflow-hidden">
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <h2 class="text-sm font-bold text-slate-800">Today's Pulse</h2>
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-wider block mt-1">Live booking flow metrics</span>
                    </div>
                    <div class="relative flex h-3.5 w-3.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#d84e55]/60 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3.5 w-3.5 bg-[#d84e55]"></span>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <!-- Today Orders -->
                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 hover:bg-white hover:border-red-100 transition duration-300">
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest block mb-1">Today's bookings</span>
                        <p class="text-2xl font-black text-slate-800">{{ $today_trips->count() }}</p>
                    </div>
                    <!-- Today Revenue -->
                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 hover:bg-white hover:border-emerald-100 transition duration-300">
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest block mb-1">Today's Revenue</span>
                        <p class="text-2xl font-black text-emerald-600">₹{{ number_format($today_trips->where('booking_status', 'completed')->sum('total_amount')) }}</p>
                    </div>
                    <!-- Pending Orders -->
                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 hover:bg-white hover:border-rose-100 transition duration-300">
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest block mb-1">Pending Orders</span>
                        <p class="text-2xl font-black text-rose-600">{{ number_format($stats['pending_orders']) }}</p>
                    </div>
                    <!-- Out for Delivery / Started Today -->
                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 hover:bg-white hover:border-red-100 transition duration-300">
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest block mb-1">Active Rides</span>
                        <p class="text-2xl font-black text-[#d84e55]">{{ $today_trips->whereIn('booking_status', ['started', 'on_the_way'])->count() }}</p>
                    </div>
                </div>
            </div>

            <!-- Quick Actions Panel -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
                <h2 class="text-sm font-bold text-slate-800 mb-4">Quick Fleet Actions</h2>
                <div class="grid grid-cols-2 gap-4">
                    <a href="{{ route('car') }}" class="flex flex-col items-center justify-center p-4 rounded-2xl border border-red-100 bg-red-50/20 text-[#d84e55] hover:bg-[#d84e55] hover:text-white transition duration-200 group">
                        <i class="fas fa-car-side text-2xl mb-2.5 group-hover:scale-110 transition duration-300"></i>
                        <span class="text-xs font-bold text-center">Add New Fleet</span>
                    </a>
                    <a href="{{ route('chargesType') }}" class="flex flex-col items-center justify-center p-4 rounded-2xl border border-amber-100 bg-amber-50/20 text-amber-600 hover:bg-amber-600 hover:text-white transition duration-200 group">
                        <i class="fas fa-file-invoice-dollar text-2xl mb-2.5 group-hover:scale-110 transition duration-300"></i>
                        <span class="text-xs font-bold text-center">Add Extra Charges</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Right 2/3 Column: Chart Panel -->
        <div class="lg:col-span-2 bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex flex-col justify-between relative overflow-hidden">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-sm font-bold text-slate-800">Booking Analytics Summary ({{ date('Y') }})</h2>
                    <p class="text-xs text-slate-400 font-medium mt-0.5">Yearly earnings summary & trend analysis</p>
                </div>
                <div class="flex gap-1 bg-slate-50 p-1 rounded-xl border border-slate-100">
                    <button class="px-3 py-1.5 text-[10px] font-extrabold bg-[#d84e55] text-white rounded-lg shadow-sm hover:bg-[#c6393f] transition">Monthly</button>
                    <button class="px-3 py-1.5 text-[10px] font-extrabold text-slate-500 hover:bg-slate-100 rounded-lg transition">Weekly</button>
                </div>
            </div>
            <div class="h-72 flex-1">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
    </div>

    <!-- 4. DYNAMIC OPERATION SEARCH & FILTERS HUB -->
    <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100/90 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <!-- Left Side: Live Operations Status -->
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 bg-red-50 text-[#d84e55] rounded-xl flex items-center justify-center shadow-inner-sm">
                <i class="fas fa-satellite-dish text-base animate-pulse"></i>
            </div>
            <div>
                <h3 class="text-xs font-black text-slate-800 leading-none">Live Travel Desk Control</h3>
                <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest mt-1">Real-time dynamic search and layout filter</p>
            </div>
        </div>

        <!-- Middle: Quick Search Input -->
        <div class="relative flex-1 max-w-md">
            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                <i class="fas fa-search text-xs"></i>
            </span>
            <input x-model="search" type="text" placeholder="Filter by customer, pickup/drop address, booking ID, or cab..." 
                class="w-full pl-9 pr-4 py-2.5 text-xs font-semibold bg-slate-50 border border-slate-100 rounded-xl focus:outline-none focus:border-[#d84e55] focus:ring-4 focus:ring-[#d84e55]/10 transition duration-200 placeholder-slate-400 text-slate-700">
        </div>

        <!-- Right Side: Filter Pills -->
        <div class="flex items-center gap-1.5 overflow-x-auto pb-1 md:pb-0 scrollbar-hide">
            <button @click="statusFilter = 'all'" :class="statusFilter === 'all' ? 'bg-[#d84e55] text-white shadow-sm' : 'bg-slate-50 text-slate-500 hover:bg-slate-100'"
                class="px-3.5 py-1.5 rounded-lg text-[9px] font-extrabold uppercase tracking-wider transition duration-150 whitespace-nowrap">
                All status
            </button>
            <button @click="statusFilter = 'pending'" :class="statusFilter === 'pending' ? 'bg-amber-500 text-white shadow-sm' : 'bg-slate-50 text-slate-500 hover:bg-slate-100'"
                class="px-3.5 py-1.5 rounded-lg text-[9px] font-extrabold uppercase tracking-wider transition duration-150 whitespace-nowrap">
                Pending
            </button>
            <button @click="statusFilter = 'started'" :class="statusFilter === 'started' ? 'bg-[#d84e55]/90 text-white shadow-sm' : 'bg-slate-50 text-slate-500 hover:bg-slate-100'"
                class="px-3.5 py-1.5 rounded-lg text-[9px] font-extrabold uppercase tracking-wider transition duration-150 whitespace-nowrap">
                Started
            </button>
            <button @click="statusFilter = 'completed'" :class="statusFilter === 'completed' ? 'bg-emerald-500 text-white shadow-sm' : 'bg-slate-50 text-slate-500 hover:bg-slate-100'"
                class="px-3.5 py-1.5 rounded-lg text-[9px] font-extrabold uppercase tracking-wider transition duration-150 whitespace-nowrap">
                Completed
            </button>
            <button @click="statusFilter = 'cancelled'" :class="statusFilter === 'cancelled' ? 'bg-rose-500 text-white shadow-sm' : 'bg-slate-50 text-slate-500 hover:bg-slate-100'"
                class="px-3.5 py-1.5 rounded-lg text-[9px] font-extrabold uppercase tracking-wider transition duration-150 whitespace-nowrap">
                Cancelled
            </button>
        </div>
    </div>

    <!-- 5. RECENT BOOKINGS & TODAY'S ACTIVE TRIPS GRID -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <!-- Recent Bookings Table (2/3 Column) -->
        <div class="xl:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden flex flex-col justify-between">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-slate-800">Recent Booking Requests</h2>
                    <p class="text-xs text-slate-400 font-medium mt-0.5">Customer reservations panel</p>
                </div>
                <a href="{{ route('cabOrders') }}" class="px-3.5 py-2 text-xs font-bold text-[#d84e55] bg-red-50 hover:bg-[#d84e55] hover:text-white rounded-xl transition">View Full History</a>
            </div>
            <div class="overflow-x-auto flex-1">
                <table class="w-full text-left text-xs border-collapse">
                    <thead class="bg-slate-50/70 text-slate-500 font-bold border-b border-slate-100 uppercase tracking-wider text-[10px]">
                        <tr>
                            <th class="px-6 py-4">Booking ID</th>
                            <th class="px-6 py-4">Customer Details</th>
                            <th class="px-6 py-4">Route Info</th>
                            <th class="px-6 py-4">Journey Date</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-center font-bold">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-600 text-xs">
                        @forelse($recent_bookings as $booking)
                        <tr class="hover:bg-slate-50/50 transition booking-row duration-150" 
                            x-show="(statusFilter === 'all' || '{{ $booking->booking_status }}' === statusFilter) && ('{{ strtolower($booking->order_number) }}'.includes(search.toLowerCase()) || '{{ strtolower($booking->customer_name) }}'.includes(search.toLowerCase()) || '{{ strtolower($booking->pickup_address) }}'.includes(search.toLowerCase()) || '{{ strtolower($booking->drop_address) }}'.includes(search.toLowerCase()))"
                            x-transition>
                            <td class="px-6 py-4 font-black text-[#d84e55] text-glow-red select-all">{{ $booking->order_number }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-full bg-slate-100 text-[#d84e55] flex items-center justify-center font-extrabold text-[10px] shadow-sm uppercase border border-slate-200/50">
                                        {{ substr($booking->customer_name, 0, 2) }}
                                    </div>
                                    <span class="font-bold text-slate-800 leading-snug">{{ $booking->customer_name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-0.5">
                                    <div class="flex items-center gap-1.5 text-slate-700 font-semibold">
                                        <span class="w-1.5 h-1.5 bg-[#d84e55] rounded-full"></span>
                                        <span>Pick: {{ Str::limit($booking->pickup_address, 20) }}</span>
                                    </div>
                                    <div class="flex items-center gap-1.5 text-slate-500">
                                        <span class="w-1.5 h-1.5 bg-emerald-500 rounded-sm"></span>
                                        <span>Drop: {{ Str::limit($booking->drop_address, 20) }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 font-semibold text-slate-500">{{ $booking->pickup_date->format('Y-m-d') }}</td>
                            <td class="px-6 py-4">
                                @php
                                    $statusColors = [
                                        'pending' => 'bg-amber-50 text-amber-700 border-amber-200/50',
                                        'confirmed' => 'bg-blue-50 text-blue-700 border-blue-200/50',
                                        'completed' => 'bg-emerald-50 text-emerald-700 border-emerald-200/50',
                                        'cancelled' => 'bg-rose-50 text-rose-700 border-rose-200/50',
                                        'started' => 'bg-red-50 text-[#d84e55] border-red-200/50',
                                    ];
                                    $color = $statusColors[$booking->booking_status] ?? 'bg-slate-50 text-slate-700 border-slate-200/50';
                                @endphp
                                <span class="px-3 py-1 border {{ $color }} rounded-full text-[9px] font-extrabold uppercase tracking-wider shadow-inner-sm">{{ $booking->booking_status }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex gap-2 justify-center">
                                    <a href="{{ route('cabOrders.show', $booking->id) }}" class="p-2 bg-slate-50 text-slate-400 hover:text-[#d84e55] rounded-xl hover:bg-red-50 border border-slate-100 transition shadow-sm" title="View details">
                                        <i class="fas fa-eye text-xs"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                <i class="fas fa-calendar-times text-2xl mb-2 text-slate-300"></i>
                                <p class="font-medium text-xs">No bookings found in database</p>
                            </td>
                        </tr>
                        @endforelse

                        <!-- Custom Alpine Client Side Empty Filter Indicator -->
                        <tr x-show="document.querySelectorAll('.booking-row:not([style*=\'display: none\'])').length === 0" class="hidden" :class="document.querySelectorAll('.booking-row:not([style*=\'display: none\'])').length === 0 ? '!table-row' : ''">
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                <i class="fas fa-filter text-2xl mb-2 text-slate-300 animate-bounce"></i>
                                <p class="font-bold text-xs text-slate-500">No matching reservations found</p>
                                <p class="text-[10px] text-slate-400 mt-1">Try adjusting your operations search or status pills filter</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Today's Active Trips Panel (1/3 Column) -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden flex flex-col justify-between">
            <div class="p-6 border-b border-slate-100">
                <h2 class="text-sm font-bold text-slate-800">Today's Active Timelines</h2>
                <p class="text-xs text-slate-400 font-medium mt-0.5">Live passenger routing paths</p>
            </div>
            <div class="p-6 space-y-4 overflow-y-auto max-h-[360px] card-scrollbar flex-1">
                @forelse($today_trips as $trip)
                <div class="p-4 rounded-2xl border border-slate-100 bg-slate-50/50 hover:bg-white hover:shadow-lg hover:border-red-100 transition duration-300 cursor-pointer group trip-card"
                     x-show="(statusFilter === 'all' || '{{ $trip->booking_status }}' === statusFilter) && ('{{ strtolower($trip->customer_name) }}'.includes(search.toLowerCase()) || '{{ strtolower($trip->pickup_address) }}'.includes(search.toLowerCase()) || '{{ strtolower($trip->drop_address) }}'.includes(search.toLowerCase()))"
                     x-transition>
                    <div class="flex justify-between items-start mb-3 gap-2">
                        <div class="flex-1">
                            <span class="px-2 py-0.5 bg-[#d84e55]/10 text-[#d84e55] text-[8px] font-black rounded uppercase tracking-wider">{{ $trip->trip_type }}</span>
                        </div>
                        <span class="px-2.5 py-0.5 bg-red-50 text-[#d84e55] border border-red-100 text-[8px] font-extrabold rounded uppercase tracking-wider">{{ $trip->booking_status }}</span>
                    </div>

                    <!-- Travel Routing Graphic Path -->
                    <div class="trip-route-line my-3">
                        <div class="trip-route-point text-xs font-bold text-slate-700 leading-snug">
                            {{ $trip->pickup_address }}
                        </div>
                        <div class="trip-route-point drop text-xs font-bold text-slate-700 leading-snug mt-3">
                            {{ $trip->drop_address }}
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2 text-[10px] pt-3 border-t border-slate-100/90 mt-2">
                        <div class="flex items-center gap-1.5 text-slate-600">
                            <i class="fas fa-user-circle text-[#d84e55]"></i>
                            <span class="font-bold text-slate-800">{{ $trip->customer_name }}</span>
                        </div>
                        <div class="flex items-center gap-1.5 text-slate-600">
                            <i class="fas fa-shuttle-van text-[#d84e55]"></i>
                            <span class="font-semibold text-slate-700 leading-none">{{ Str::limit($trip->car_name, 12) }}</span>
                        </div>
                        <div class="flex items-center gap-1.5 text-slate-500 font-semibold mt-1">
                            <i class="fas fa-clock text-[#d84e55]"></i>
                            <span>{{ $trip->pickup_time }}</span>
                        </div>
                        <a href="{{ route('cabOrders.show', $trip->id) }}" class="flex items-center gap-1.5 text-[#d84e55] font-black hover:underline justify-end mt-1">
                            <span>Details</span>
                            <i class="fas fa-chevron-right text-[8px]"></i>
                        </a>
                    </div>
                </div>
                @empty
                <div class="py-16 text-center text-slate-400 flex flex-col items-center justify-center">
                    <i class="fas fa-calendar-day text-4xl mb-3 text-slate-200"></i>
                    <p class="text-xs font-semibold">No trips scheduled for today</p>
                </div>
                @endforelse

                <!-- Custom Alpine Client Side Empty Filter Indicator -->
                <div x-show="document.querySelectorAll('.trip-card:not([style*=\'display: none\'])').length === 0" class="py-16 text-center text-slate-400 flex flex-col items-center justify-center hidden" :class="document.querySelectorAll('.trip-card:not([style*=\'display: none\'])').length === 0 ? '!flex' : ''">
                    <i class="fas fa-filter text-4xl mb-3 text-slate-200 animate-pulse"></i>
                    <p class="text-xs font-bold text-slate-500">No active itineraries match criteria</p>
                </div>
            </div>
        </div>
    </div>

    <!-- 6. RECENT ALERTS PANEL & SYSTEM STATISTICS -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- System Alerts List (2/3 Column) -->
        <div class="lg:col-span-2 bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex flex-col justify-between">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h2 class="text-sm font-bold text-slate-800">Operational System Notifications</h2>
                    <p class="text-xs text-slate-400 font-medium mt-0.5">Real-time system events list</p>
                </div>
                <a href="#" class="text-xs font-bold text-[#d84e55] hover:underline">View System Log</a>
            </div>
            <div class="space-y-3.5 flex-1">
                @forelse($alerts as $alert)
                <div class="flex gap-4 p-3.5 rounded-xl hover:bg-slate-50 transition border-l-4 border-{{ $alert['type'] }}-500 bg-slate-50/45">
                    <div class="h-10 w-10 bg-{{ $alert['type'] }}-50 rounded-xl flex items-center justify-center text-{{ $alert['type'] }}-600 flex-shrink-0">
                        <i class="fas {{ $alert['icon'] }} text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2">
                            <p class="text-xs font-bold text-slate-800 leading-snug truncate">{{ $alert['message'] }}</p>
                            <p class="text-[9px] text-slate-400 font-bold whitespace-nowrap pl-2 flex-shrink-0">{{ $alert['time'] }}</p>
                        </div>
                        <p class="text-[10px] text-slate-400 font-medium mt-0.5 truncate">{{ $alert['subtext'] }}</p>
                    </div>
                </div>
                @empty
                <p class="text-center text-slate-400 py-10 font-medium text-xs">No operational logs recorded</p>
                @endforelse
            </div>
        </div>

        <!-- System Stats Metrics (1/3 Column) -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex flex-col justify-between gap-5">
            <div>
                <h2 class="text-sm font-bold text-slate-800">Core Operator Metrics</h2>
                <p class="text-xs text-slate-400 font-medium mt-0.5">General system capacity status</p>
            </div>
            
            <div class="space-y-4">
                <div class="flex items-center gap-4 p-4 rounded-2xl border border-slate-100 bg-slate-50/50 hover:bg-white hover:shadow-md transition duration-200">
                    <div class="h-11 w-11 bg-red-50 rounded-xl flex items-center justify-center text-[#d84e55] flex-shrink-0 shadow-inner-sm">
                        <i class="fas fa-users-cog text-lg"></i>
                    </div>
                    <div>
                        <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest">Total Registered B2B Customers</p>
                        <p class="text-lg font-black text-slate-800 mt-0.5">{{ number_format($stats['total_users']) }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-4 p-4 rounded-2xl border border-slate-100 bg-slate-50/50 hover:bg-white hover:shadow-md transition duration-200">
                    <div class="h-11 w-11 bg-emerald-50 rounded-xl flex items-center justify-center text-emerald-600 flex-shrink-0 shadow-inner-sm">
                        <i class="fas fa-id-card text-lg"></i>
                    </div>
                    <div>
                        <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest">Authorized Drivers Active</p>
                        <p class="text-lg font-black text-slate-800 mt-0.5">{{ number_format($stats['active_drivers']) }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-4 p-4 rounded-2xl border border-slate-100 bg-slate-50/50 hover:bg-white hover:shadow-md transition duration-200">
                    <div class="h-11 w-11 bg-purple-50 rounded-xl flex items-center justify-center text-purple-600 flex-shrink-0 shadow-inner-sm">
                        <i class="fas fa-satellite text-lg"></i>
                    </div>
                    <div>
                        <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest">Real-time Gateway Status</p>
                        <p class="text-lg font-black text-slate-800 mt-0.5">99.9% Uptime</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Revenue Chart
        const ctx = document.getElementById('revenueChart').getContext('2d');
        
        // Gradient for line chart (Crimson Theme)
        const gradient = ctx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(216, 78, 85, 0.25)'); // RedBus Red glow
        gradient.addColorStop(1, 'rgba(216, 78, 85, 0)');
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($chart_labels) !!},
                datasets: [{
                    label: 'Revenue (₹)',
                    data: {!! json_encode($chart_values) !!},
                    borderColor: '#d84e55', // RedBus Red Border
                    borderWidth: 3,
                    fill: true,
                    backgroundColor: gradient,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#d84e55',
                    pointBorderWidth: 2,
                    pointHoverRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: '#1e293b',
                        padding: 12,
                        titleFont: { size: 13, weight: 'bold', family: "'Inter', sans-serif" },
                        bodyFont: { size: 12, family: "'Inter', sans-serif" },
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                return ' ₹' + context.parsed.y.toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            display: true,
                            color: '#f1f5f9'
                        },
                        ticks: {
                            callback: function(value) {
                                if (value >= 100000) return '₹' + (value/100000).toFixed(1) + 'L';
                                if (value >= 1000) return '₹' + (value/1000).toFixed(1) + 'K';
                                return '₹' + value;
                            },
                            color: '#94a3b8',
                            font: { size: 10, family: "'Inter', sans-serif" }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#94a3b8',
                            font: { size: 10, family: "'Inter', sans-serif" }
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
            }
        });
    });
</script>
@endsection