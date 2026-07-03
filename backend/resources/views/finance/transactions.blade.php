@extends('layouts.app')

@section('content')
<div class="container mx-auto py-6 space-y-6">
    <div>
        <h1 class="text-2xl font-black text-gray-900 tracking-tight">Payment Transactions Ledger</h1>
        <p class="text-xs text-gray-500">Global double-entry audit ledger of credits and debits across all vendor wallets.</p>
    </div>

    <!-- Global Ledger Card -->
    <div class="bg-white shadow rounded-2xl border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 border-b border-slate-100 font-bold uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-6 py-3.5">Date</th>
                        <th class="px-6 py-3.5">Vendor</th>
                        <th class="px-6 py-3.5">Transaction Type</th>
                        <th class="px-6 py-3.5 text-right">Credit</th>
                        <th class="px-6 py-3.5 text-right">Debit</th>
                        <th class="px-6 py-3.5">Reference ID</th>
                        <th class="px-6 py-3.5">Description</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($transactions as $tx)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-6 py-4 text-slate-400 font-medium">{{ $tx->created_at->format('d M Y, h:i A') }}</td>
                            <td class="px-6 py-4">
                                @if($tx->wallet && $tx->wallet->user)
                                    <div class="font-bold text-slate-800">{{ $tx->wallet->user->name }}</div>
                                    <div class="text-[10px] text-slate-400">{{ $tx->wallet->user->email }}</div>
                                @else
                                    <span class="text-slate-400">Unknown Vendor</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 capitalize font-bold text-slate-700">{{ str_replace('_', ' ', $tx->transaction_type) }}</td>
                            <td class="px-6 py-4 text-right font-semibold text-emerald-600">
                                {{ $tx->type === 'credit' ? '₹' . number_format($tx->amount, 2) : '—' }}
                            </td>
                            <td class="px-6 py-4 text-right font-semibold text-red-500">
                                {{ $tx->type === 'debit' ? '₹' . number_format($tx->amount, 2) : '—' }}
                            </td>
                            <td class="px-6 py-4 text-slate-400 font-bold">#{{ $tx->reference_id ?: 'N/A' }}</td>
                            <td class="px-6 py-4 text-slate-500 max-w-[250px] truncate" title="{{ $tx->description }}">{{ $tx->description ?: '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                <i class="fas fa-receipt text-3xl text-slate-200 mb-2 block"></i>
                                No ledger entries found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($transactions->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
