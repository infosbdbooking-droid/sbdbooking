@extends('layouts.app')

@section('content')
<div class="container mx-auto py-6 space-y-6">
    <!-- Messages -->
    @if(session('success'))
        <div class="p-4 bg-green-50 border border-green-200 text-green-800 rounded-2xl flex items-center gap-2 shadow-sm">
            <i class="fas fa-check-circle text-lg text-green-500"></i>
            <div class="text-xs font-semibold">{{ session('success') }}</div>
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 bg-red-50 border border-red-200 text-red-800 rounded-2xl flex items-center gap-2 shadow-sm">
            <i class="fas fa-times-circle text-lg text-red-500"></i>
            <div class="text-xs font-semibold">{{ session('error') }}</div>
        </div>
    @endif

    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-2xl font-black text-gray-900 tracking-tight">My Wallet & Settlements</h1>
            <p class="text-xs text-gray-500">Manage your earnings, track commissions, and request payouts.</p>
        </div>
        
        <!-- Payout Button -->
        <button type="button" id="requestSettlementBtn" 
            class="inline-flex items-center gap-2 bg-[#d84e55] hover:bg-[#c04349] text-white px-5 py-2.5 rounded-xl shadow-lg hover:shadow font-bold text-xs uppercase tracking-wider transition-all duration-150">
            <i class="fas fa-paper-plane"></i>
            Request Payout
        </button>
    </div>

    <!-- Balance and Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Main Wallet Card -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex flex-col justify-between h-40 relative overflow-hidden">
            <div class="absolute right-0 top-0 h-16 w-16 bg-slate-50 rounded-bl-full flex items-center justify-end pr-3 pt-3">
                <i class="fas fa-wallet text-slate-300 text-xl"></i>
            </div>
            <div>
                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest block mb-0.5">Wallet Balance</span>
                <p class="text-3xl font-black {{ $wallet->balance >= 0 ? 'text-emerald-600' : 'text-red-500' }} leading-none mt-1">
                    ₹{{ number_format($wallet->balance, 2) }}
                </p>
            </div>
            <div class="text-[10px] text-slate-400 mt-4 flex items-center gap-1.5">
                <i class="fas fa-info-circle"></i>
                @if($wallet->balance >= 0)
                    <span>Your account is in good standing.</span>
                @else
                    <span class="text-red-400 font-semibold">Negative balance. Please recharge or clear your dues.</span>
                @endif
            </div>
        </div>
        
        <!-- Total Earnings Credit -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex flex-col justify-between h-40 relative overflow-hidden">
            <div class="absolute right-0 top-0 h-16 w-16 bg-emerald-50 rounded-bl-full flex items-center justify-end pr-3 pt-3">
                <i class="fas fa-arrow-trend-up text-emerald-300 text-xl"></i>
            </div>
            <div>
                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest block mb-0.5">Total Credited Fare</span>
                <p class="text-3xl font-black text-emerald-600 leading-none mt-1">
                    ₹{{ number_format($wallet->transactions()->where('type', 'credit')->sum('amount'), 2) }}
                </p>
            </div>
            <div class="text-[10px] text-slate-400 mt-4">
                Accumulated online ride fares credited to date.
            </div>
        </div>

        <!-- Total Commission Debited -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex flex-col justify-between h-40 relative overflow-hidden">
            <div class="absolute right-0 top-0 h-16 w-16 bg-red-50 rounded-bl-full flex items-center justify-end pr-3 pt-3">
                <i class="fas fa-percent text-red-300 text-xl"></i>
            </div>
            <div>
                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest block mb-0.5">Total Admin Commission Paid</span>
                <p class="text-3xl font-black text-red-500 leading-none mt-1">
                    ₹{{ number_format($wallet->transactions()->where('type', 'debit')->where('transaction_type', 'admin_commission')->sum('amount'), 2) }}
                </p>
            </div>
            <div class="text-[10px] text-slate-400 mt-4">
                Total commissions debited from your wallet.
            </div>
        </div>
    </div>

    <!-- Tables Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left 2 Cols: Transaction Log -->
        <div class="bg-white shadow rounded-2xl border border-slate-100 lg:col-span-2 overflow-hidden flex flex-col justify-between">
            <div>
                <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center">
                    <div>
                        <h2 class="text-sm font-bold text-slate-800">Transaction Statements</h2>
                        <p class="text-[10px] text-slate-400">Chronological history of all credits and debits.</p>
                    </div>
                    <i class="fas fa-file-invoice text-slate-300"></i>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-600">
                        <thead class="bg-slate-50 border-b border-slate-100 font-bold uppercase tracking-wider text-slate-500">
                            <tr>
                                <th class="px-6 py-3.5">Date</th>
                                <th class="px-6 py-3.5">Reference / Type</th>
                                <th class="px-6 py-3.5">Description</th>
                                <th class="px-6 py-3.5 text-right">Amount</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse($transactions as $tx)
                                <tr class="hover:bg-slate-50/50 transition">
                                    <td class="px-6 py-4 text-slate-400 font-medium">
                                        {{ $tx->created_at->format('d M Y, h:i A') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-slate-700 capitalize">{{ str_replace('_', ' ', $tx->transaction_type) }}</div>
                                        @if($tx->reference_id)
                                            <span class="px-1.5 py-0.5 bg-slate-100 text-slate-500 rounded text-[9px] font-bold">#{{ $tx->reference_id }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-slate-500 max-w-[200px] truncate" title="{{ $tx->description }}">
                                        {{ $tx->description ?: '—' }}
                                    </td>
                                    <td class="px-6 py-4 text-right font-black {{ $tx->type === 'credit' ? 'text-emerald-600' : 'text-red-500' }}">
                                        {{ $tx->type === 'credit' ? '+' : '-' }}₹{{ number_format($tx->amount, 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-slate-400">
                                        <i class="fas fa-receipt text-3xl text-slate-200 mb-2 block"></i>
                                        No transactions recorded yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            @if($transactions->hasPages())
                <div class="px-6 py-4 border-t border-slate-100">
                    {{ $transactions->appends(request()->except('tx_page'))->links() }}
                </div>
            @endif
        </div>

        <!-- Right 1 Col: Payout History -->
        <div class="bg-white shadow rounded-2xl border border-slate-100 overflow-hidden flex flex-col justify-between">
            <div>
                <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center">
                    <div>
                        <h2 class="text-sm font-bold text-slate-800">Settlements Requests</h2>
                        <p class="text-[10px] text-slate-400">Status of requested bank / UPI payouts.</p>
                    </div>
                    <i class="fas fa-money-bill-wave text-slate-300"></i>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse($settlements as $set)
                        <div class="p-5 hover:bg-slate-50/50 transition">
                            <div class="flex justify-between items-start">
                                <div>
                                    <div class="text-xs font-bold text-slate-700">₹{{ number_format($set->amount, 2) }}</div>
                                    <div class="text-[10px] text-slate-400 mt-0.5">{{ $set->created_at->format('d M Y') }}</div>
                                </div>
                                @if($set->status === 'pending')
                                    <span class="px-2 py-0.5 bg-yellow-50 text-yellow-700 border border-yellow-200 rounded-full text-[9px] font-bold">Pending</span>
                                @elseif($set->status === 'approved')
                                    <span class="px-2 py-0.5 bg-green-50 text-green-700 border border-green-200 rounded-full text-[9px] font-bold">Approved</span>
                                @else
                                    <span class="px-2 py-0.5 bg-red-50 text-red-700 border border-red-200 rounded-full text-[9px] font-bold" title="{{ $set->notes }}">Rejected</span>
                                @endif
                            </div>
                            <div class="mt-3 flex items-center justify-between text-[10px] text-slate-500">
                                <span>Method: <strong>{{ $set->payout_method }}</strong></span>
                                @if($set->transaction_reference)
                                    <span class="text-slate-400">Ref: {{ $set->transaction_reference }}</span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-12 text-center text-slate-400">
                            <i class="fas fa-hand-holding-dollar text-3xl text-slate-200 mb-2 block"></i>
                            No settlement requests made yet.
                        </div>
                    @endforelse
                </div>
            </div>

            @if($settlements->hasPages())
                <div class="px-6 py-4 border-t border-slate-100">
                    {{ $settlements->appends(request()->except('set_page'))->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- PAYOUT REQUEST MODAL -->
<div id="payoutModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden items-center justify-center z-50">
    <div class="bg-white w-full max-w-md mx-auto p-6 rounded-2xl shadow-2xl relative">
        <button type="button" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 text-xl closePayoutModalBtn focus:outline-none">&times;</button>
        <h2 class="text-base font-bold text-gray-900 mb-4 flex items-center gap-2 border-b pb-2">
            <i class="fas fa-paper-plane text-[#d84e55]"></i>
            Request Payout
        </h2>
        <form action="{{ route('vendor.settlement.request') }}" method="POST">
            @csrf
            <div class="space-y-4 mb-6">
                <!-- Amount -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Amount to Payout (₹) <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" min="1" name="amount" required 
                        placeholder="e.g. 500" 
                        class="w-full px-3 py-2 border border-slate-200 rounded-xl focus:outline-none focus:ring-1 focus:ring-[#d84e55] text-xs">
                    <p class="text-[10px] text-slate-400 mt-1">Available balance: <strong>₹{{ number_format($wallet->balance, 2) }}</strong></p>
                </div>
                
                <!-- Method -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Payout Method <span class="text-red-500">*</span></label>
                    <select name="payout_method" required 
                        class="w-full px-3 py-2 border border-slate-200 rounded-xl focus:outline-none focus:ring-1 focus:ring-[#d84e55] text-xs">
                        <option value="UPI">UPI Transfer</option>
                        <option value="Bank Transfer">Bank Account Transfer</option>
                        <option value="Cash">Cash Handover</option>
                    </select>
                </div>

                <!-- Notes -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Details / Notes</label>
                    <textarea name="notes" rows="3" placeholder="Enter UPI ID or bank account details..."
                        class="w-full px-3 py-2 border border-slate-200 rounded-xl focus:outline-none focus:ring-1 focus:ring-[#d84e55] text-xs"></textarea>
                </div>
            </div>
            
            <div class="flex justify-end gap-2">
                <button type="button" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-bold closePayoutModalBtn">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-[#d84e55] hover:bg-[#c04349] text-white rounded-xl text-xs font-bold shadow transition">Submit Request</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    const modal = $('#payoutModal');
    
    $('#requestSettlementBtn').on('click', function() {
        modal.removeClass('hidden').addClass('flex');
    });
    
    $('.closePayoutModalBtn').on('click', function() {
        modal.removeClass('flex').addClass('hidden');
    });
    
    $(window).on('click', function(e) {
        if ($(e.target).is('#payoutModal')) {
            modal.removeClass('flex').addClass('hidden');
        }
    });
});
</script>
@endsection
