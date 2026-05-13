@extends('layouts.app')

@section('content')
<div class="container mx-auto py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Cab Bookings</h1>
    </div>

    <div class="bg-white shadow rounded-lg overflow-hidden border border-gray-200">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 font-semibold text-gray-700">Order Number</th>
                        <th class="px-6 py-4 font-semibold text-gray-700">Customer Details</th>
                        <th class="px-6 py-4 font-semibold text-gray-700">Pickup Date/Time</th>
                        <th class="px-6 py-4 font-semibold text-gray-700">Trip Type</th>
                        <th class="px-6 py-4 font-semibold text-gray-700">Amount</th>
                        <th class="px-6 py-4 font-semibold text-gray-700">Status</th>
                        <th class="px-6 py-4 font-semibold text-gray-700 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($orders as $order)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-medium text-blue-600">
                                {{ $order->order_number }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-800">{{ $order->customer_name }}</div>
                                <div class="text-xs text-gray-500">{{ $order->customer_mobile }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div>{{ $order->pickup_date ? \Carbon\Carbon::parse($order->pickup_date)->format('d M, Y') : '-' }}</div>
                                <div class="text-xs text-gray-500">{{ $order->pickup_time ? (strpos($order->pickup_time, ' to ') !== false ? $order->pickup_time : \Carbon\Carbon::parse($order->pickup_time)->format('h:i A')) : '-' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="capitalize">{{ str_replace('_', ' ', $order->trip_type) }}</div>
                                <div class="text-xs text-gray-500">{{ $order->total_km }} km</div>
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-900">
                                ₹{{ number_format($order->total_amount, 2) }}
                            </td>
                            <td class="px-6 py-4">
                                @if($order->booking_status === 'pending')
                                    <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-semibold">Pending</span>
                                @elseif($order->booking_status === 'confirmed')
                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">Confirmed</span>
                                @elseif($order->booking_status === 'cancelled')
                                    <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-semibold">Cancelled</span>
                                @else
                                    <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-semibold capitalize">{{ str_replace('_', ' ', $order->booking_status) }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('cabOrders.show', $order->id) }}" class="inline-flex items-center justify-center p-2 text-blue-600 hover:text-white hover:bg-blue-600 rounded transition border border-transparent hover:border-blue-600" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                <i class="fas fa-box-open text-3xl mb-3 text-gray-300 block"></i>
                                No cab bookings found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($orders->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $orders->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
