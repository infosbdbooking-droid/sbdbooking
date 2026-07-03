@extends('layouts.app')

@section('content')
<div class="container mx-auto py-6 space-y-6" x-data="{ activeTab: 'overview' }">
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

    <!-- Header Breadcrumb & Top Action Buttons -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border-b border-slate-200 pb-5">
        <div>
            <!-- Breadcrumbs -->
            <nav class="flex text-xs font-bold text-slate-400 uppercase tracking-wider mb-2 gap-2" aria-label="Breadcrumb">
                <a href="{{ route('dashboard') }}" class="hover:text-slate-600">Dashboard</a>
                <span>/</span>
                <span class="text-slate-400">Finance</span>
                <span>/</span>
                <a href="{{ route('finance.vendor-wallets.index') }}" class="hover:text-slate-600">Vendor Wallets</a>
                <span>/</span>
                <span class="text-[#d84e55] font-black">Vendor Details</span>
            </nav>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Vendor Finance Dashboard</h1>
        </div>

        <!-- Top Right Action Buttons -->
        <div class="flex flex-wrap items-center gap-2">
            <button type="button" class="adjust-trigger-btn px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-sm transition" data-action="credit">
                <i class="fas fa-plus mr-1"></i> Manual Credit
            </button>
            <button type="button" class="adjust-trigger-btn px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold shadow-sm transition" data-action="debit">
                <i class="fas fa-minus mr-1"></i> Manual Debit
            </button>
            <button type="button" onclick="window.print()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition">
                <i class="fas fa-file-pdf mr-1"></i> Download PDF
            </button>
        </div>
    </div>

    <!-- Section 1 : Vendor Information Header Card -->
    <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm flex flex-col md:flex-row items-center gap-6">
        <div class="relative group shrink-0">
            <div class="w-24 h-24 rounded-2xl overflow-hidden bg-slate-50 border-2 border-slate-100 shadow flex items-center justify-center">
                @if($vendor->company_logo)
                    <img src="{{ asset('images/' . $vendor->company_logo) }}" alt="Company Logo" class="w-full h-full object-cover">
                @elseif($vendor->photo)
                    <img src="{{ asset('images/' . $vendor->photo) }}" alt="Vendor Photo" class="w-full h-full object-cover">
                @else
                    <i class="fas fa-store text-4xl text-slate-300"></i>
                @endif
            </div>
        </div>
        <div class="text-center md:text-left flex-1 space-y-2">
            <div class="flex flex-col md:flex-row md:items-center gap-3">
                <h2 class="text-xl font-black text-slate-800">{{ $vendor->name }}</h2>
                <div class="flex items-center gap-2 justify-center">
                    <!-- Vendor Status -->
                    <span class="px-2.5 py-0.5 rounded-full text-[9px] font-black tracking-wider uppercase border 
                        {{ $vendor->profile_status === 'Approved' ? 'bg-green-50 text-green-700 border-green-200' : 'bg-yellow-50 text-yellow-700 border-yellow-200' }}">
                        {{ $vendor->profile_status }}
                    </span>
                    <!-- Wallet Status -->
                    <span class="px-2.5 py-0.5 rounded-full text-[9px] font-black tracking-wider uppercase border 
                        {{ $wallet->status === 'active' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-red-50 text-red-700 border-red-200 animate-pulse' }}">
                        Wallet {{ ucfirst($wallet->status) }}
                    </span>
                </div>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-y-1 gap-x-4 text-xs text-slate-500">
                <div><i class="fas fa-envelope text-[#d84e55] mr-1.5"></i> {{ $vendor->email }}</div>
                <div><i class="fas fa-phone text-[#d84e55] mr-1.5"></i> {{ $vendor->mobile ?: 'No Phone Registered' }}</div>
                <div><i class="fas fa-calendar-alt text-[#d84e55] mr-1.5"></i> Joined: {{ $vendor->created_at->format('d M Y') }}</div>
            </div>
        </div>
    </div>

    <!-- Section 2 : Finance Summary Cards -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        <!-- Balance -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 relative overflow-hidden">
            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Wallet Balance</span>
            <p class="text-xl font-black {{ $walletBalance >= 0 ? 'text-emerald-600' : 'text-red-500' }} mt-1">
                ₹{{ number_format($walletBalance, 2) }}
            </p>
        </div>
        
        <!-- Pending -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 relative overflow-hidden">
            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Pending Payouts</span>
            <p class="text-xl font-black text-amber-500 mt-1">
                ₹{{ number_format($pendingSettlement, 2) }}
            </p>
        </div>

        <!-- Paid -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 relative overflow-hidden">
            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Total Paid Out</span>
            <p class="text-xl font-black text-blue-600 mt-1">
                ₹{{ number_format($totalPaid, 2) }}
            </p>
        </div>

        <!-- Total Earnings -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 relative overflow-hidden">
            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Total Earnings</span>
            <p class="text-xl font-black text-slate-800 mt-1">
                ₹{{ number_format($totalEarnings, 2) }}
            </p>
        </div>

        <!-- Commission -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 relative overflow-hidden">
            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Admin Commission</span>
            <p class="text-xl font-black text-red-500 mt-1">
                ₹{{ number_format($adminCommission, 2) }}
            </p>
        </div>

        <!-- Today -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 relative overflow-hidden">
            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Today's Earnings</span>
            <p class="text-xl font-black text-emerald-500 mt-1">
                ₹{{ number_format($todayEarnings, 2) }}
            </p>
        </div>
    </div>

    <!-- Navigation Tabs (Fintech Style) -->
    <div class="flex border-b border-slate-200 text-xs font-bold uppercase tracking-wider text-slate-400 gap-6">
        <button @click="activeTab = 'overview'" :class="activeTab === 'overview' ? 'text-[#d84e55] border-b-2 border-[#d84e55] pb-3' : 'hover:text-slate-700 pb-3'">Overview</button>
        <button @click="activeTab = 'ledger'" :class="activeTab === 'ledger' ? 'text-[#d84e55] border-b-2 border-[#d84e55] pb-3' : 'hover:text-slate-700 pb-3'">Wallet Ledger</button>
        <button @click="activeTab = 'rides'" :class="activeTab === 'rides' ? 'text-[#d84e55] border-b-2 border-[#d84e55] pb-3' : 'hover:text-slate-700 pb-3'">Rides & Commissions</button>
        <button @click="activeTab = 'settlements'" :class="activeTab === 'settlements' ? 'text-[#d84e55] border-b-2 border-[#d84e55] pb-3' : 'hover:text-slate-700 pb-3'">Settlements & Payouts</button>
        <button @click="activeTab = 'reports'" :class="activeTab === 'reports' ? 'text-[#d84e55] border-b-2 border-[#d84e55] pb-3' : 'hover:text-slate-700 pb-3'">Statement / Reports</button>
    </div>

    <!-- TAB CONTENTS -->
    
    <!-- Tab 1: Overview & Controls -->
    <div x-show="activeTab === 'overview'" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Section 8: Wallet status adjustments / Controls -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 space-y-4">
                <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider border-b pb-2 flex items-center gap-2">
                    <i class="fas fa-sliders-h text-slate-400"></i> Wallet Control Panel
                </h3>
                
                <div class="space-y-3">
                    <!-- Freeze / Activate Wallet forms -->
                    <form action="{{ route('finance.vendor-wallets.status', $vendor->id) }}" method="POST" class="w-full">
                        @csrf
                        @if($wallet->status === 'active')
                            <input type="hidden" name="action" value="freeze">
                            <button type="submit" class="w-full py-2 bg-red-100 hover:bg-red-200 text-red-700 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5">
                                <i class="fas fa-lock"></i> Freeze Wallet
                            </button>
                        @else
                            <input type="hidden" name="action" value="activate">
                            <button type="submit" class="w-full py-2 bg-emerald-100 hover:bg-emerald-200 text-emerald-700 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5">
                                <i class="fas fa-unlock"></i> Activate Wallet
                            </button>
                        @endif
                    </form>

                    <!-- Reset balance form -->
                    <form action="{{ route('finance.vendor-wallets.status', $vendor->id) }}" method="POST" class="w-full" onsubmit="return confirm('WARNING: Are you sure you want to reset this vendor\'s wallet balance to zero? This action is recorded in the transaction ledger.');">
                        @csrf
                        <input type="hidden" name="action" value="reset">
                        <button type="submit" class="w-full py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5">
                            <i class="fas fa-rotate-right"></i> Reset Balance to Zero
                        </button>
                    </form>
                </div>
            </div>

            <!-- Profile Summary Card -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 md:col-span-2 space-y-3 text-xs">
                <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider border-b pb-2 flex items-center gap-2">
                    <i class="fas fa-info-circle text-slate-400"></i> Finance Health Details
                </h3>
                <div class="grid grid-cols-2 gap-4">
                    <div><span class="text-slate-400 block">Total Ledger Entries:</span> <strong class="text-slate-800">{{ $ledger->total() }} Transactions</strong></div>
                    <div><span class="text-slate-400 block">Total Completed Rides:</span> <strong class="text-slate-800">{{ $rides->total() }} Trips</strong></div>
                    <div><span class="text-slate-400 block">Commission Settings:</span> <strong class="text-slate-800">{{ $vendor->commission_type === 'flat' ? 'Flat Fee' : 'Percentage' }}</strong></div>
                    <div><span class="text-slate-400 block">Commission Value:</span> <strong class="text-slate-800">{{ $vendor->commission_type === 'flat' ? '₹' . number_format($vendor->flat_commission, 2) : ($vendor->commission_percentage ?: 10.0) . '%' }}</strong></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab 2: Wallet Ledger -->
    <div x-show="activeTab === 'ledger'" class="bg-white shadow rounded-2xl border border-slate-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100">
            <h2 class="text-sm font-bold text-slate-800">Section 4: Wallet Ledger</h2>
            <p class="text-[10px] text-slate-400">Transaction logs including credit, debit, and running wallet balances.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 border-b border-slate-100 font-bold uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-6 py-3.5">Date</th>
                        <th class="px-6 py-3.5">Transaction Type</th>
                        <th class="px-6 py-3.5 text-right">Credit</th>
                        <th class="px-6 py-3.5 text-right">Debit</th>
                        <th class="px-6 py-3.5 text-right">Running Balance</th>
                        <th class="px-6 py-3.5">Reference</th>
                        <th class="px-6 py-3.5">Description</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($ledger as $tx)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-6 py-4 text-slate-400 font-medium">{{ $tx->created_at->format('d M Y, h:i A') }}</td>
                            <td class="px-6 py-4 capitalize font-bold text-slate-700">{{ str_replace('_', ' ', $tx->transaction_type) }}</td>
                            <td class="px-6 py-4 text-right font-semibold text-emerald-600">{{ $tx->type === 'credit' ? '₹' . number_format($tx->amount, 2) : '—' }}</td>
                            <td class="px-6 py-4 text-right font-semibold text-red-500">{{ $tx->type === 'debit' ? '₹' . number_format($tx->amount, 2) : '—' }}</td>
                            <td class="px-6 py-4 text-right font-bold text-slate-800">₹{{ number_format($tx->running_balance, 2) }}</td>
                            <td class="px-6 py-4 text-slate-400 font-bold">#{{ $tx->reference_id ?: 'N/A' }}</td>
                            <td class="px-6 py-4 text-slate-500" title="{{ $tx->description }}">{{ $tx->description ?: '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                <i class="fas fa-receipt text-3xl text-slate-200 mb-2 block"></i>
                                Ledger is empty.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($ledger->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $ledger->links() }}
            </div>
        @endif
    </div>

    <!-- Tab 3: Rides & Commissions -->
    <div x-show="activeTab === 'rides'" class="space-y-6">
        <!-- Section 3: Recent Ride Earnings -->
        <div class="bg-white shadow rounded-2xl border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100">
                <h2 class="text-sm font-bold text-slate-800">Section 3: Recent Ride Earnings</h2>
                <p class="text-[10px] text-slate-400">History of ride booking details completed by this vendor.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 border-b border-slate-100 font-bold uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="px-6 py-3.5">Ride ID</th>
                            <th class="px-6 py-3.5">Booking Number</th>
                            <th class="px-6 py-3.5">Customer</th>
                            <th class="px-6 py-3.5 text-right">Ride Amount</th>
                            <th class="px-6 py-3.5 text-right">Commission</th>
                            <th class="px-6 py-3.5 text-right">Vendor Earnings</th>
                            <th class="px-6 py-3.5">Payment Method</th>
                            <th class="px-6 py-3.5 text-center">Ride Status</th>
                            <th class="px-6 py-3.5">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse($rides as $ride)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="px-6 py-4 font-bold text-slate-400">#{{ $ride->id }}</td>
                                <td class="px-6 py-4 font-bold text-blue-600">{{ $ride->order_number }}</td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-800">{{ $ride->customer_name }}</div>
                                    <div class="text-[10px] text-slate-400">{{ $ride->customer_mobile }}</div>
                                </td>
                                <td class="px-6 py-4 text-right text-slate-900 font-semibold">₹{{ number_format($ride->total_amount, 2) }}</td>
                                <td class="px-6 py-4 text-right text-red-500">₹{{ number_format($ride->commission_amount, 2) }}</td>
                                <td class="px-6 py-4 text-right text-emerald-600 font-bold">₹{{ number_format($ride->vendor_earnings, 2) }}</td>
                                <td class="px-6 py-4 capitalize font-semibold text-slate-600">{{ $ride->payment_method ?: 'Cash' }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-2 py-0.5 bg-green-50 text-green-700 border border-green-200 rounded-full text-[9px] font-bold capitalize">
                                        {{ $ride->booking_status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-slate-400">{{ $ride->created_at->format('d M Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-12 text-center text-slate-400">
                                    No completed rides found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($rides->hasPages())
                <div class="px-6 py-4 border-t border-slate-100">
                    {{ $rides->links() }}
                </div>
            @endif
        </div>

        <!-- Section 7: Commission History -->
        <div class="bg-white shadow rounded-2xl border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100">
                <h2 class="text-sm font-bold text-slate-800">Section 7: Commission History</h2>
                <p class="text-[10px] text-slate-400">Breakdown of calculated commissions per trip.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 border-b border-slate-100 font-bold uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="px-6 py-3.5">Ride ID</th>
                            <th class="px-6 py-3.5">Booking Number</th>
                            <th class="px-6 py-3.5 text-right">Ride Amount</th>
                            <th class="px-6 py-3.5">Commission Type</th>
                            <th class="px-6 py-3.5 text-right">Commission Rate/Value</th>
                            <th class="px-6 py-3.5 text-right">Deducted Commission</th>
                            <th class="px-6 py-3.5 text-right">Vendor Net Earnings</th>
                            <th class="px-6 py-3.5">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse($commissions as $com)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="px-6 py-4 font-bold text-slate-400">#{{ $com->id }}</td>
                                <td class="px-6 py-4 font-bold text-blue-600">{{ $com->order_number }}</td>
                                <td class="px-6 py-4 text-right font-semibold text-slate-700">₹{{ number_format($com->total_amount, 2) }}</td>
                                <td class="px-6 py-4 capitalize font-semibold text-slate-600">{{ $com->commission_type ?: 'Percentage' }}</td>
                                <td class="px-6 py-4 text-right">
                                    {{ $com->commission_type === 'flat' ? '₹' . number_format($com->commission_rate, 2) : ($com->commission_rate ?: 10.0) . '%' }}
                                </td>
                                <td class="px-6 py-4 text-right font-bold text-red-500">₹{{ number_format($com->commission_amount, 2) }}</td>
                                <td class="px-6 py-4 text-right font-bold text-emerald-600">₹{{ number_format($com->vendor_earnings, 2) }}</td>
                                <td class="px-6 py-4 text-slate-400">{{ $com->created_at->format('d M Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-slate-400">
                                    No commission logs found.
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

    <!-- Tab 4: Settlements & Payments -->
    <div x-show="activeTab === 'settlements'" class="space-y-6">
        <!-- Section 5: Settlement Requests -->
        <div class="bg-white shadow rounded-2xl border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center">
                <div>
                    <h2 class="text-sm font-bold text-slate-800">Section 5: Settlement Requests</h2>
                    <p class="text-[10px] text-slate-400">Vendor payout request status logs.</p>
                </div>
                <button type="button" id="adminCreateSettlementBtn" class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-sm transition">
                    <i class="fas fa-plus mr-1"></i> Create Settlement
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 border-b border-slate-100 font-bold uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="px-6 py-3.5">Settlement ID</th>
                            <th class="px-6 py-3.5">Request Date</th>
                            <th class="px-6 py-3.5 text-right">Requested Amount</th>
                            <th class="px-6 py-3.5">Payment Method</th>
                            <th class="px-6 py-3.5">Transaction Reference</th>
                            <th class="px-6 py-3.5">Status</th>
                            <th class="px-6 py-3.5 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse($settlementRequests as $sr)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="px-6 py-4 font-bold text-slate-400">#SR-{{ $sr->id }}</td>
                                <td class="px-6 py-4">{{ $sr->created_at->format('d M Y, h:i A') }}</td>
                                <td class="px-6 py-4 text-right font-black text-slate-800">₹{{ number_format($sr->amount, 2) }}</td>
                                <td class="px-6 py-4 font-semibold text-slate-600">{{ $sr->payout_method }}</td>
                                <td class="px-6 py-4 text-slate-400">{{ $sr->transaction_reference ?: '—' }}</td>
                                <td class="px-6 py-4">
                                    @if($sr->status === 'pending')
                                        <span class="px-2.5 py-0.5 bg-yellow-50 text-yellow-700 border border-yellow-200 rounded-full text-[9px] font-bold">Pending</span>
                                    @elseif($sr->status === 'approved')
                                        <span class="px-2.5 py-0.5 bg-green-50 text-green-700 border border-green-200 rounded-full text-[9px] font-bold">Approved</span>
                                    @else
                                        <span class="px-2.5 py-0.5 bg-red-50 text-red-700 border border-red-200 rounded-full text-[9px] font-bold" title="{{ $sr->notes }}">Rejected</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($sr->status === 'pending')
                                        <div class="flex items-center justify-center gap-1.5">
                                            <button type="button" class="approve-btn bg-emerald-500 hover:bg-emerald-600 text-white px-2.5 py-1 rounded font-bold text-[9px] uppercase tracking-wider transition" data-id="{{ $sr->id }}">Approve</button>
                                            <button type="button" class="reject-btn bg-red-500 hover:bg-red-600 text-white px-2.5 py-1 rounded font-bold text-[9px] uppercase tracking-wider transition" data-id="{{ $sr->id }}">Reject</button>
                                        </div>
                                    @else
                                        <span class="text-slate-400">Processed</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                    No payouts requested yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($settlementRequests->hasPages())
                <div class="px-6 py-4 border-t border-slate-100">
                    {{ $settlementRequests->links() }}
                </div>
            @endif
        </div>

        <!-- Section 6: Payment History -->
        <div class="bg-white shadow rounded-2xl border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100">
                <h2 class="text-sm font-bold text-slate-800">Section 6: Payment History</h2>
                <p class="text-[10px] text-slate-400">History of all completed payouts.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 border-b border-slate-100 font-bold uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="px-6 py-3.5">Settlement ID</th>
                            <th class="px-6 py-3.5 text-right">Amount</th>
                            <th class="px-6 py-3.5">Payment Mode</th>
                            <th class="px-6 py-3.5">Transaction ID</th>
                            <th class="px-6 py-3.5">Paid By</th>
                            <th class="px-6 py-3.5">Paid Date</th>
                            <th class="px-6 py-3.5">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse($paymentHistory as $pay)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="px-6 py-4 font-bold text-slate-400">#SR-{{ $pay->id }}</td>
                                <td class="px-6 py-4 text-right font-black text-slate-800">₹{{ number_format($pay->amount, 2) }}</td>
                                <td class="px-6 py-4 font-semibold text-slate-600">{{ $pay->payout_method }}</td>
                                <td class="px-6 py-4 text-slate-500">{{ $pay->transaction_reference ?: 'Bank Transfer' }}</td>
                                <td class="px-6 py-4 text-slate-500 font-bold">{{ $pay->processedBy ? $pay->processedBy->name : 'System' }}</td>
                                <td class="px-6 py-4 text-slate-400">{{ $pay->processed_at ? $pay->processed_at->format('d M Y') : '—' }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-0.5 bg-green-50 text-green-700 border border-green-200 rounded-full text-[9px] font-bold">Approved</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                    No completed payout transactions found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($paymentHistory->hasPages())
                <div class="px-6 py-4 border-t border-slate-100">
                    {{ $paymentHistory->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Tab 5: Reports & Statement Downloads -->
    <div x-show="activeTab === 'reports'" class="bg-white shadow rounded-2xl border border-slate-100 p-6 space-y-6">
        <div>
            <h2 class="text-sm font-bold text-slate-800">Section 9: Download Account Statement</h2>
            <p class="text-[10px] text-slate-400 font-semibold mt-0.5">Generate statements, transaction ledgers, and payout logs.</p>
        </div>
        
        <form action="#" class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end max-w-xl text-xs">
            <div>
                <label class="block text-xs font-bold text-slate-400 mb-1.5 uppercase">Select Date Range</label>
                <select id="reportRange" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-1 focus:ring-red-500">
                    <option value="today">Today</option>
                    <option value="week">This Week</option>
                    <option value="month">This Month</option>
                    <option value="custom">Custom Date Range</option>
                </select>
            </div>
            
            <div class="sm:col-span-2 grid grid-cols-2 gap-2" id="customDateFields" style="display: none;">
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 mb-1">Start Date</label>
                    <input type="date" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-1">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 mb-1">End Date</label>
                    <input type="date" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none">
                </div>
            </div>
        </form>

        <div class="flex items-center gap-2 pt-4 border-t">
            <button type="button" class="exportBtn px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-sm transition">
                <i class="fas fa-file-excel mr-1"></i> Export Excel
            </button>
            <button type="button" class="exportBtn px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold shadow-sm transition">
                <i class="fas fa-file-pdf mr-1"></i> Export PDF
            </button>
            <button type="button" onclick="window.print()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition">
                <i class="fas fa-print mr-1"></i> Print Statement
            </button>
        </div>
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
        <form action="{{ route('finance.vendor-wallets.adjust', $vendor->id) }}" method="POST">
            @csrf
            
            <div class="space-y-4 mb-6">
                <!-- Target Vendor Info -->
                <div class="p-3 bg-slate-50 border border-slate-100 rounded-xl text-xs">
                    <div class="flex justify-between mb-1"><span class="text-slate-400">Vendor:</span> <strong class="text-slate-800">{{ $vendor->name }}</strong></div>
                    <div class="flex justify-between"><span class="text-slate-400">Current Balance:</span> <strong class="text-slate-800">₹{{ number_format($walletBalance, 2) }}</strong></div>
                </div>

                <!-- Action (Credit / Debit) -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Adjustment Action <span class="text-red-500">*</span></label>
                    <select name="action" id="adjustActionSelect" required 
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

<!-- PAYOUT / SETTLEMENT CREATION MODAL (Admin side) -->
<div id="adminSettlementModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden items-center justify-center z-50">
    <div class="bg-white w-full max-w-md mx-auto p-6 rounded-2xl shadow-2xl relative">
        <button type="button" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 text-xl closePayoutModalBtn focus:outline-none">&times;</button>
        <h2 class="text-base font-bold text-gray-900 mb-4 flex items-center gap-2 border-b pb-2">
            <i class="fas fa-money-bill-transfer text-emerald-500"></i>
            Create Manual Settlement
        </h2>
        <form action="{{ route('vendor.settlement.request') }}" method="POST">
            @csrf
            <!-- Simulate Vendor's payout request directly -->
            <div class="space-y-4 mb-6">
                <!-- Current Balance -->
                <div class="p-3 bg-slate-50 border border-slate-100 rounded-xl text-xs flex justify-between">
                    <span class="text-slate-400">Available Wallet Balance:</span>
                    <strong class="text-slate-800">₹{{ number_format($walletBalance, 2) }}</strong>
                </div>

                <!-- Amount -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Amount to Payout (₹) <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" min="1" max="{{ $walletBalance }}" name="amount" required placeholder="e.g. 500" 
                        class="w-full px-3 py-2 border border-slate-200 rounded-xl focus:outline-none focus:ring-1 focus:ring-emerald-500 text-xs">
                </div>
                
                <!-- Method -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Payment Method <span class="text-red-500">*</span></label>
                    <select name="payout_method" required 
                        class="w-full px-3 py-2 border border-slate-200 rounded-xl focus:outline-none focus:ring-1 focus:ring-emerald-500 text-xs">
                        <option value="UPI">UPI Transfer</option>
                        <option value="Bank Transfer">Bank Account Transfer</option>
                        <option value="Cash">Cash Handover</option>
                    </select>
                </div>

                <!-- Notes -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Details / Notes</label>
                    <textarea name="notes" rows="3" placeholder="Reference ID or bank details..."
                        class="w-full px-3 py-2 border border-slate-200 rounded-xl focus:outline-none focus:ring-1 focus:ring-emerald-500 text-xs"></textarea>
                </div>
            </div>
            
            <div class="flex justify-end gap-2">
                <button type="button" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-bold closePayoutModalBtn">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow transition">Create Payout</button>
            </div>
        </form>
    </div>
</div>

<!-- REJECT SETTLEMENT MODAL -->
<div id="rejectSettlementModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden items-center justify-center z-50">
    <div class="bg-white w-full max-w-md mx-auto p-6 rounded-2xl shadow-2xl relative">
        <button type="button" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 text-xl closeRejectModalBtn focus:outline-none">&times;</button>
        <h2 class="text-base font-bold text-gray-900 mb-4 flex items-center gap-2 border-b pb-2">
            <i class="fas fa-ban text-red-500"></i>
            Reject Settlement Payout
        </h2>
        <form id="rejectSettlementForm">
            @csrf
            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-700 mb-1">Reason for Rejection <span class="text-red-500">*</span></label>
                <textarea name="rejection_reason" id="rejection_reason" rows="3" required placeholder="Reason for rejecting this payout..."
                    class="w-full px-3 py-2 border border-slate-200 rounded-xl focus:outline-none focus:ring-1 focus:ring-red-500 text-xs"></textarea>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-bold closeRejectModalBtn">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-red-500 hover:bg-red-600 text-white rounded-xl text-xs font-bold shadow transition">Confirm Rejection</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    const adjustModal = $('#adjustWalletModal');
    const payoutModal = $('#adminSettlementModal');
    const rejectModal = $('#rejectSettlementModal');
    let activeRejectId = null;

    // Toggle custom date range fields in reports tab
    $('#reportRange').on('change', function() {
        if ($(this).val() === 'custom') {
            $('#customDateFields').show();
        } else {
            $('#customDateFields').hide();
        }
    });

    // Close Modals
    $('.closeAdjustModalBtn').on('click', () => adjustModal.removeClass('flex').addClass('hidden'));
    $('.closePayoutModalBtn').on('click', () => payoutModal.removeClass('flex').addClass('hidden'));
    
    $('.closeRejectModalBtn').on('click', function() {
        rejectModal.removeClass('flex').addClass('hidden');
        $('#rejectSettlementForm')[0].reset();
        activeRejectId = null;
    });

    // Trigger Adjust Modals
    $('.adjust-trigger-btn').on('click', function() {
        const action = $(this).data('action');
        $('#adjustActionSelect').val(action);
        adjustModal.removeClass('hidden').addClass('flex');
    });

    // Trigger Adjust from overview
    $('.adjust-wallet-btn').on('click', function() {
        adjustModal.removeClass('hidden').addClass('flex');
    });

    // Trigger Create Payout
    $('#adminCreateSettlementBtn').on('click', function() {
        payoutModal.removeClass('hidden').addClass('flex');
    });

    // Approve Request handler
    $('.approve-btn').on('click', function() {
        const id = $(this).data('id');
        const confirmUrl = "{{ route('finance.settlements.approve', ':id') }}".replace(':id', id);
        
        Swal.fire({
            title: 'Confirm Approval',
            html: '<p class="text-gray-700 text-sm">Are you sure you want to approve this settlement payout request?</p>',
            icon: 'question',
            width: 350,
            showCancelButton: true,
            confirmButtonText: 'Approve',
            cancelButtonText: 'Cancel',
            buttonsStyling: false,
            customClass: {
                popup: 'rounded-lg p-4 shadow-md',
                title: 'text-lg font-semibold text-gray-800',
                confirmButton: 'bg-emerald-500 hover:bg-emerald-600 text-white px-4 py-2 rounded mr-2',
                cancelButton: 'bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: confirmUrl,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            $.toastr.success(response.message);
                            location.reload();
                        } else {
                            $.toastr.error(response.message);
                        }
                    },
                    error: function() {
                        $.toastr.error('Server error occurred during approval.');
                    }
                });
            }
        });
    });

    // Reject Button Trigger
    $('.reject-btn').on('click', function() {
        activeRejectId = $(this).data('id');
        rejectModal.removeClass('hidden').addClass('flex');
    });

    // Reject Form Submission
    $('#rejectSettlementForm').on('submit', function(e) {
        e.preventDefault();
        if (!activeRejectId) return;

        const rejectUrl = "{{ route('finance.settlements.reject', ':id') }}".replace(':id', activeRejectId);
        
        $.ajax({
            url: rejectUrl,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                rejection_reason: $('#rejection_reason').val()
            },
            success: function(response) {
                if (response.success) {
                    $.toastr.success(response.message);
                    location.reload();
                } else {
                    $.toastr.error(response.message);
                }
            },
            error: function() {
                $.toastr.error('Server error occurred during rejection.');
            }
        });
    });

    // Mock reports export
    $('.exportBtn').on('click', function() {
        Swal.fire({
            title: 'Reports Export',
            text: 'Preparing statement export, your download will begin shortly.',
            icon: 'info',
            confirmButtonText: 'Done',
            buttonsStyling: false,
            customClass: {
                confirmButton: 'bg-[#d84e55] text-white px-4 py-2 rounded-xl text-xs font-bold'
            }
        });
    });
});
</script>
@endsection
