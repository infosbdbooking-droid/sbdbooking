@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- 1. TOP SUMMARY CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
        <!-- Total Bookings -->
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition cursor-pointer group">
            <div class="flex items-center justify-between mb-3">
                <div class="p-2 bg-blue-50 text-blue-600 rounded-lg group-hover:bg-blue-600 group-hover:text-white transition">
                    <i class="fas fa-calendar-check text-xl"></i>
                </div>
                <span class="text-xs font-medium text-green-600 bg-green-50 px-2 py-1 rounded-full">+12%</span>
            </div>
            <h3 class="text-sm text-gray-500 font-medium">Total Bookings</h3>
            <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['total_bookings']) }}</p>
        </div>

        <!-- Active Trips -->
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition cursor-pointer group">
            <div class="flex items-center justify-between mb-3">
                <div class="p-2 bg-green-50 text-green-600 rounded-lg group-hover:bg-green-600 group-hover:text-white transition">
                    <i class="fas fa-route text-xl"></i>
                </div>
                <span class="text-xs font-medium text-green-600 bg-green-50 px-2 py-1 rounded-full">+5%</span>
            </div>
            <h3 class="text-sm text-gray-500 font-medium">Active Trips</h3>
            <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['active_trips']) }}</p>
        </div>

        <!-- Completed Trips -->
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition cursor-pointer group">
            <div class="flex items-center justify-between mb-3">
                <div class="p-2 bg-purple-50 text-purple-600 rounded-lg group-hover:bg-purple-600 group-hover:text-white transition">
                    <i class="fas fa-check-circle text-xl"></i>
                </div>
                <span class="text-xs font-medium text-green-600 bg-green-50 px-2 py-1 rounded-full">+18%</span>
            </div>
            <h3 class="text-sm text-gray-500 font-medium">Completed Trips</h3>
            <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['completed_trips']) }}</p>
        </div>

        <!-- Pending Orders -->
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition cursor-pointer group">
            <div class="flex items-center justify-between mb-3">
                <div class="p-2 bg-orange-50 text-orange-600 rounded-lg group-hover:bg-orange-600 group-hover:text-white transition">
                    <i class="fas fa-clock text-xl"></i>
                </div>
                <span class="text-xs font-medium text-red-600 bg-red-50 px-2 py-1 rounded-full">-3%</span>
            </div>
            <h3 class="text-sm text-gray-500 font-medium">Pending Orders</h3>
            <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['pending_orders']) }}</p>
        </div>

        <!-- Total Revenue -->
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition cursor-pointer group">
            <div class="flex items-center justify-between mb-3">
                <div class="p-2 bg-teal-50 text-teal-600 rounded-lg group-hover:bg-teal-600 group-hover:text-white transition">
                    <i class="fas fa-indian-rupee-sign text-xl"></i>
                </div>
                <span class="text-xs font-medium text-green-600 bg-green-50 px-2 py-1 rounded-full">+24%</span>
            </div>
            <h3 class="text-sm text-gray-500 font-medium">Total Revenue</h3>
            <p class="text-2xl font-bold text-gray-800">₹{{ $stats['total_revenue'] >= 100000 ? number_format($stats['total_revenue'] / 100000, 1) . 'L' : number_format($stats['total_revenue']) }}</p>
        </div>

        <!-- Available Cars -->
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition cursor-pointer group">
            <div class="flex items-center justify-between mb-3">
                <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg group-hover:bg-indigo-600 group-hover:text-white transition">
                    <i class="fas fa-car text-xl"></i>
                </div>
                <span class="text-xs font-medium text-gray-400 bg-gray-50 px-2 py-1 rounded-full">Stable</span>
            </div>
            <h3 class="text-sm text-gray-500 font-medium">Available Cars</h3>
            <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['available_cars']) }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- 4. REVENUE ANALYTICS -->
        <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-bold text-gray-800">Revenue Analytics ({{ date('Y') }})</h2>
                <div class="flex gap-2">
                    <button class="px-3 py-1 text-xs font-medium bg-blue-600 text-white rounded-md">Monthly</button>
                    <button class="px-3 py-1 text-xs font-medium bg-gray-50 text-gray-600 rounded-md hover:bg-gray-100">Weekly</button>
                </div>
            </div>
            <div class="h-80">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <!-- 6. NOTIFICATIONS PANEL -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-bold text-gray-800">Recent Alerts</h2>
                <a href="#" class="text-sm text-blue-600 hover:underline">View all</a>
            </div>
            <div class="space-y-4">
                @forelse($alerts as $alert)
                <div class="flex gap-4 p-3 rounded-lg hover:bg-gray-50 transition border-l-4 border-{{ $alert['type'] }}-500">
                    <div class="h-10 w-10 bg-{{ $alert['type'] }}-50 rounded-full flex items-center justify-center text-{{ $alert['type'] }}-600 flex-shrink-0">
                        <i class="fas {{ $alert['icon'] }} text-sm"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-800">{{ $alert['message'] }}</p>
                        <p class="text-xs text-gray-500">{{ $alert['subtext'] }}</p>
                        <p class="text-[10px] text-gray-400 mt-1">{{ $alert['time'] }}</p>
                    </div>
                </div>
                @empty
                <p class="text-center text-gray-400 py-10">No recent alerts</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- 5. QUICK ACTION BUTTONS -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <h2 class="text-lg font-bold text-gray-800 mb-6">Quick Actions</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="{{ route('orders') }}" class="flex flex-col items-center justify-center p-4 rounded-xl border border-blue-100 bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition group">
                <i class="fas fa-plus-circle text-2xl mb-2"></i>
                <span class="text-sm font-semibold">Create Booking</span>
            </a>
            <a href="{{ route('kmPrice') }}" class="flex flex-col items-center justify-center p-4 rounded-xl border border-green-100 bg-green-50 text-green-600 hover:bg-green-600 hover:text-white transition group">
                <i class="fas fa-route text-2xl mb-2"></i>
                <span class="text-sm font-semibold">Add New Trip</span>
            </a>
            <a href="{{ route('car') }}" class="flex flex-col items-center justify-center p-4 rounded-xl border border-purple-100 bg-purple-50 text-purple-600 hover:bg-purple-600 hover:text-white transition group">
                <i class="fas fa-car-side text-2xl mb-2"></i>
                <span class="text-sm font-semibold">Add New Car</span>
            </a>
            <a href="{{ route('chargesType') }}" class="flex flex-col items-center justify-center p-4 rounded-xl border border-orange-100 bg-orange-50 text-orange-600 hover:bg-orange-600 hover:text-white transition group">
                <i class="fas fa-file-invoice-dollar text-2xl mb-2"></i>
                <span class="text-sm font-semibold">Add Charges</span>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <!-- 2. RECENT BOOKINGS TABLE -->
        <div class="xl:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-lg font-bold text-gray-800">Recent Bookings</h2>
                <a href="{{ route('cabOrders') }}" class="px-4 py-2 text-sm font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition">View All</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 text-gray-500 font-medium border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4">Booking ID</th>
                            <th class="px-6 py-4">Customer</th>
                            <th class="px-6 py-4">Route</th>
                            <th class="px-6 py-4">Date</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($recent_bookings as $booking)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-medium text-blue-600">{{ $booking->order_number }}</td>
                            <td class="px-6 py-4">{{ $booking->customer_name }}</td>
                            <td class="px-6 py-4">
                                <p class="text-xs text-gray-500">Pick: {{ Str::limit($booking->pickup_address, 30) }}</p>
                                <p class="text-xs text-gray-500">Drop: {{ Str::limit($booking->drop_address, 30) }}</p>
                            </td>
                            <td class="px-6 py-4 text-gray-600">{{ $booking->pickup_date->format('Y-m-d') }}</td>
                            <td class="px-6 py-4">
                                @php
                                    $statusColors = [
                                        'pending' => 'bg-yellow-100 text-yellow-700',
                                        'confirmed' => 'bg-blue-100 text-blue-700',
                                        'completed' => 'bg-green-100 text-green-700',
                                        'cancelled' => 'bg-red-100 text-red-700',
                                        'started' => 'bg-indigo-100 text-indigo-700',
                                    ];
                                    $color = $statusColors[$booking->booking_status] ?? 'bg-gray-100 text-gray-700';
                                @endphp
                                <span class="px-2 py-1 {{ $color }} rounded-full text-[10px] font-bold uppercase">{{ $booking->booking_status }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex gap-2">
                                    <a href="{{ route('cabOrders.show', $booking->id) }}" class="p-1.5 text-gray-400 hover:text-blue-600 transition"><i class="fas fa-eye"></i></a>
                                    <button class="p-1.5 text-gray-400 hover:text-orange-600 transition"><i class="fas fa-edit"></i></button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-gray-400">No bookings found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 3. TODAY’S TRIPS / UPCOMING TRIPS -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100">
                <h2 class="text-lg font-bold text-gray-800">Today's Active Trips</h2>
            </div>
            <div class="p-6 space-y-4">
                @forelse($today_trips as $trip)
                <div class="p-4 rounded-xl border border-gray-100 bg-gray-50 hover:bg-white hover:shadow-md transition cursor-pointer group">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <h4 class="font-bold text-gray-800">{{ $trip->pickup_address }} to {{ $trip->drop_address }}</h4>
                            <p class="text-xs text-gray-500">{{ ucfirst(str_replace('_', ' ', $trip->trip_type)) }}</p>
                        </div>
                        <span class="px-2 py-0.5 bg-blue-100 text-blue-600 text-[10px] font-bold rounded-md uppercase">{{ $trip->booking_status }}</span>
                    </div>
                    <div class="grid grid-cols-2 gap-4 text-xs">
                        <div class="flex items-center gap-2 text-gray-600">
                            <i class="fas fa-user text-blue-400"></i>
                            <span>{{ $trip->customer_name }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-gray-600">
                            <i class="fas fa-car text-blue-400"></i>
                            <span>{{ $trip->car_name }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-gray-600">
                            <i class="fas fa-clock text-blue-400"></i>
                            <span>{{ $trip->pickup_time }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-blue-600 font-medium">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>Details</span>
                        </div>
                    </div>
                </div>
                @empty
                <div class="py-10 text-center text-gray-400">
                    <i class="fas fa-calendar-day text-3xl mb-2"></i>
                    <p>No trips scheduled for today</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- 7. SYSTEM STATUS / INFO -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="h-12 w-12 bg-blue-50 rounded-full flex items-center justify-center text-blue-600">
                <i class="fas fa-users text-xl"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">Total Users</p>
                <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['total_users']) }}</p>
            </div>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="h-12 w-12 bg-green-50 rounded-full flex items-center justify-center text-green-600">
                <i class="fas fa-id-card text-xl"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">Active Drivers</p>
                <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['active_drivers']) }}</p>
            </div>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="h-12 w-12 bg-purple-50 rounded-full flex items-center justify-center text-purple-600">
                <i class="fas fa-server text-xl"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">System Uptime</p>
                <p class="text-2xl font-bold text-gray-800">99.9%</p>
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
        
        // Gradient for line chart
        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(37, 99, 235, 0.2)');
        gradient.addColorStop(1, 'rgba(37, 99, 235, 0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($chart_labels) !!},
                datasets: [{
                    label: 'Revenue (₹)',
                    data: {!! json_encode($chart_values) !!},
                    borderColor: '#2563eb',
                    borderWidth: 3,
                    fill: true,
                    backgroundColor: gradient,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#2563eb',
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
                        titleFont: { size: 14, weight: 'bold' },
                        bodyFont: { size: 13 },
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
                            color: '#64748b'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#64748b'
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