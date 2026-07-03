@extends('layouts.app')

@section('content')
<div class="container mx-auto py-6 space-y-6">
    <!-- Notifications -->
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

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Vendor Wallets</h1>
            <p class="text-xs text-slate-500">Overview of all active operators, wallet balances, and payout summaries.</p>
        </div>
        
        <!-- Export & Actions -->
        <div class="flex items-center gap-2 shrink-0">
            <button onclick="window.print()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition flex items-center gap-1.5">
                <i class="fas fa-print"></i> Print Statement
            </button>
            <button id="exportExcelBtn" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition flex items-center gap-1.5">
                <i class="fas fa-file-excel"></i> Export Excel
            </button>
        </div>
    </div>

    <!-- Search & Filter Card -->
    <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100">
        <form action="{{ route('finance.vendor-wallets.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <!-- Search field -->
            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wider">Search Vendor</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" 
                        placeholder="Search by name, email, or mobile..." 
                        class="w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-1 focus:ring-blue-500 text-xs">
                    <i class="fas fa-search absolute left-3.5 top-3 text-slate-400 text-xs"></i>
                </div>
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wider">Verification Status</label>
                <select name="status" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-1 focus:ring-blue-500 text-xs">
                    <option value="">All Statuses</option>
                    <option value="Approved" {{ request('status') === 'Approved' ? 'selected' : '' }}>Approved</option>
                    <option value="Pending" {{ request('status') === 'Pending' ? 'selected' : '' }}>Pending</option>
                    <option value="Rejected" {{ request('status') === 'Rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>

            <!-- Balance Filter -->
            <div>
                <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wider">Wallet Balance</label>
                <select name="balance_filter" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-1 focus:ring-blue-500 text-xs">
                    <option value="">All Balances</option>
                    <option value="positive" {{ request('balance_filter') === 'positive' ? 'selected' : '' }}>Positive (> ₹0)</option>
                    <option value="zero" {{ request('balance_filter') === 'zero' ? 'selected' : '' }}>Zero (= ₹0)</option>
                    <option value="negative" {{ request('balance_filter') === 'negative' ? 'selected' : '' }}>Negative (< ₹0)</option>
                </select>
            </div>

            <!-- Action buttons -->
            <div class="md:col-span-4 flex justify-end gap-2 mt-2">
                <a href="{{ route('finance.vendor-wallets.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition">
                    Clear Filters
                </a>
                <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow transition">
                    Apply Filters
                </button>
            </div>
        </form>
    </div>

    <!-- Vendor Table Card -->
    <div class="bg-white shadow rounded-2xl border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-bold uppercase tracking-wider sticky top-0 z-10">
                    <tr>
                        <th class="px-6 py-4">Photo</th>
                        <th class="px-6 py-4">Vendor Details</th>
                        <th class="px-6 py-4">Contact Info</th>
                        <th class="px-6 py-4 text-right">Total Earnings</th>
                        <th class="px-6 py-4 text-right">Wallet Balance</th>
                        <th class="px-6 py-4 text-right">Pending Settlement</th>
                        <th class="px-6 py-4 text-right">Total Paid</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($vendors as $vendor)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-6 py-4">
                                @if($vendor->photo)
                                    <img src="{{ asset('images/' . $vendor->photo) }}" alt="Vendor" class="w-10 h-10 rounded-full object-cover border border-slate-100 shadow-sm flex-shrink-0">
                                @else
                                    <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center border border-slate-100 text-xs text-slate-500 font-bold flex-shrink-0 uppercase">
                                        {{ substr($vendor->name, 0, 1) }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-800 text-sm">{{ $vendor->name }}</div>
                                <div class="text-[10px] text-slate-400 mt-0.5">{{ $vendor->email }}</div>
                            </td>
                            <td class="px-6 py-4 text-slate-500">
                                <div>{{ $vendor->mobile ?: 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 text-right font-semibold text-slate-700">
                                ₹{{ number_format($vendor->total_earnings, 2) }}
                            </td>
                            <td class="px-6 py-4 text-right font-bold {{ $vendor->wallet_balance >= 0 ? 'text-emerald-600' : 'text-red-500' }}">
                                ₹{{ number_format($vendor->wallet_balance, 2) }}
                            </td>
                            <td class="px-6 py-4 text-right text-slate-500">
                                ₹{{ number_format($vendor->pending_settlement, 2) }}
                            </td>
                            <td class="px-6 py-4 text-right text-slate-500 font-semibold">
                                ₹{{ number_format($vendor->total_paid, 2) }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($vendor->profile_status === 'Approved')
                                    <span class="px-2 py-0.5 bg-green-50 text-green-700 border border-green-200 rounded-full text-[9px] font-bold">Approved</span>
                                @elseif($vendor->profile_status === 'Rejected')
                                    <span class="px-2 py-0.5 bg-red-50 text-red-700 border border-red-200 rounded-full text-[9px] font-bold">Rejected</span>
                                @else
                                    <span class="px-2 py-0.5 bg-yellow-50 text-yellow-700 border border-yellow-200 rounded-full text-[9px] font-bold">Pending</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('finance.vendor-wallets.show', $vendor->id) }}" class="px-2.5 py-1.5 bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white rounded-lg font-bold text-[10px] uppercase tracking-wider transition">
                                        View Details
                                    </a>
                                    <button type="button" class="adjust-wallet-btn px-2.5 py-1.5 bg-slate-100 text-slate-700 hover:bg-slate-200 rounded-lg font-bold text-[10px] uppercase tracking-wider transition"
                                        data-id="{{ $vendor->id }}"
                                        data-name="{{ $vendor->name }}"
                                        data-balance="{{ $vendor->wallet_balance }}">
                                        Adjust
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-16 text-center text-slate-400">
                                <i class="fas fa-users-slash text-4xl text-slate-200 mb-2 block"></i>
                                No vendors found matching criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($vendors->hasPages())
        <div class="px-6 py-4 border-t border-slate-100">
            {{ $vendors->links() }}
        </div>
        @endif
    </div>
</div>

<!-- MANUAL ADJUST WALLET MODAL -->
<div id="adjustWalletModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden items-center justify-center z-50">
    <div class="bg-white w-full max-w-md mx-auto p-6 rounded-2xl shadow-2xl relative">
        <button type="button" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 text-xl closeAdjustModalBtn focus:outline-none">&times;</button>
        <h2 class="text-base font-bold text-gray-900 mb-4 flex items-center gap-2 border-b pb-2">
            <i class="fas fa-sliders-h text-blue-500"></i>
            Manual Wallet Adjustment
        </h2>
        <form id="adjustWalletForm" method="POST">
            @csrf
            
            <div class="space-y-4 mb-6">
                <!-- Target Vendor Info -->
                <div class="p-3 bg-slate-50 border border-slate-100 rounded-xl text-xs">
                    <div class="flex justify-between mb-1"><span class="text-slate-400">Vendor:</span> <strong id="adjust_vendor_name_val" class="text-slate-800">Name</strong></div>
                    <div class="flex justify-between"><span class="text-slate-400">Current Balance:</span> <strong id="adjust_vendor_balance_val" class="text-slate-800">₹0.00</strong></div>
                </div>

                <!-- Action (Credit / Debit) -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Adjustment Action <span class="text-red-500">*</span></label>
                    <select name="action" required 
                        class="w-full px-3 py-2 border border-slate-200 rounded-xl focus:outline-none focus:ring-1 focus:ring-blue-500 text-xs">
                        <option value="credit">Credit (Add Money)</option>
                        <option value="debit">Debit (Deduct Money)</option>
                    </select>
                </div>

                <!-- Amount -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Amount (₹) <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" min="0.01" name="amount" required placeholder="e.g. 1000"
                        class="w-full px-3 py-2 border border-slate-200 rounded-xl focus:outline-none focus:ring-1 focus:ring-blue-500 text-xs">
                </div>

                <!-- Type -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Adjustment Type <span class="text-red-500">*</span></label>
                    <select name="type" required 
                        class="w-full px-3 py-2 border border-slate-200 rounded-xl focus:outline-none focus:ring-1 focus:ring-blue-500 text-xs">
                        <option value="wallet_recharge">Wallet Recharge / Refill</option>
                        <option value="adjustment">Correction / Adjustment</option>
                    </select>
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Reason / Description <span class="text-red-500">*</span></label>
                    <textarea name="description" rows="3" required placeholder="Enter description for transaction log..."
                        class="w-full px-3 py-2 border border-slate-200 rounded-xl focus:outline-none focus:ring-1 focus:ring-blue-500 text-xs"></textarea>
                </div>
            </div>
            
            <div class="flex justify-end gap-2">
                <button type="button" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-bold closeAdjustModalBtn">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-xl text-xs font-bold shadow transition">Submit Adjustment</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    const adjustModal = $('#adjustWalletModal');

    // Close Adjust Modal
    $('.closeAdjustModalBtn').on('click', function() {
        adjustModal.removeClass('flex').addClass('hidden');
    });

    // Manual Adjust Button Trigger
    $('.adjust-wallet-btn').on('click', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        const balance = $(this).data('balance');

        const actionUrl = "{{ route('finance.vendor-wallets.adjust', ':id') }}".replace(':id', id);
        $('#adjustWalletForm').attr('action', actionUrl);

        $('#adjust_vendor_name_val').text(name);
        $('#adjust_vendor_balance_val').text('₹' + parseFloat(balance).toFixed(2));
        
        adjustModal.removeClass('hidden').addClass('flex');
    });

    // Export to Excel mockup
    $('#exportExcelBtn').on('click', function() {
        Swal.fire({
            title: 'Export to Excel',
            text: 'Your Excel file is being prepared and will start downloading automatically.',
            icon: 'success',
            confirmButtonText: 'OK',
            buttonsStyling: false,
            customClass: {
                confirmButton: 'bg-emerald-600 text-white px-4 py-2 rounded-xl text-xs font-bold'
            }
        });
    });
});
</script>
@endsection
