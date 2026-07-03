@extends('layouts.app')

@section('content')
<div class="container mx-auto py-6 space-y-6">
    <div>
        <h1 class="text-2xl font-black text-gray-900 tracking-tight">Commission History</h1>
        <p class="text-xs text-gray-500">Log of all admin commission deductions from vendor ride bookings.</p>
    </div>

    <!-- Commissions History Card -->
    <div class="bg-white shadow rounded-2xl border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 border-b border-slate-100 font-bold uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-6 py-3.5">Ride ID</th>
                        <th class="px-6 py-3.5">Booking Number</th>
                        <th class="px-6 py-3.5">Vendor</th>
                        <th class="px-6 py-3.5 text-right">Ride Amount</th>
                        <th class="px-6 py-3.5">Commission Type</th>
                        <th class="px-6 py-3.5 text-right">Commission Rate/Value</th>
                        <th class="px-6 py-3.5 text-right">Deducted Commission</th>
                        <th class="px-6 py-3.5 text-right">Vendor Earnings</th>
                        <th class="px-6 py-3.5">Created Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($commissions as $com)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-6 py-4 font-bold text-slate-400">#{{ $com->id }}</td>
                            <td class="px-6 py-4 font-bold text-blue-600">
                                <a href="{{ route('cabOrders.show', $com->id) }}" class="hover:underline">{{ $com->order_number }}</a>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-800">{{ $com->vendor?->name ?? 'N/A' }}</div>
                                <div class="text-[10px] text-slate-400">{{ $com->vendor?->email ?? '' }}</div>
                            </td>
                            <td class="px-6 py-4 text-right font-semibold text-slate-700">₹{{ number_format($com->total_amount, 2) }}</td>
                            <td class="px-6 py-4 capitalize font-semibold text-slate-600">{{ $com->commission_type ?: 'Percentage' }}</td>
                            <td class="px-6 py-4 text-right">
                                {{ $com->commission_type === 'flat' ? '₹' . number_format($com->commission_rate, 2) : ($com->commission_rate ?: 10.0) . '%' }}
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-red-500">₹{{ number_format($com->commission_amount, 2) }}</td>
                            <td class="px-6 py-4 text-right font-bold text-emerald-600">₹{{ number_format($com->vendor_earnings, 2) }}</td>
                            <td class="px-6 py-4 text-slate-400">{{ $com->created_at->format('d M Y, h:i A') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center text-slate-400">
                                <i class="fas fa-percent text-3xl text-slate-200 mb-2 block"></i>
                                No commission deductions logged yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($commissions->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $commissions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
