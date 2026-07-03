@extends('layouts.app')

@section('content')
<div class="container mx-auto py-6 space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border-b border-slate-200 pb-5">
        <div>
            <h1 class="text-2xl font-black text-gray-900 tracking-tight">Financial Reports</h1>
            <p class="text-xs text-gray-500">Filter, summarize, and download detailed balance sheets and settlement logs.</p>
        </div>
    </div>

    <!-- Date Filter Form -->
    <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100">
        <form action="{{ route('finance.reports.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
            <!-- Start Date -->
            <div>
                <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase">Start Date</label>
                <input type="date" name="start_date" value="{{ request('start_date', $startDate->format('Y-m-d')) }}" 
                    class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-1 focus:ring-red-500 text-xs">
            </div>

            <!-- End Date -->
            <div>
                <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase">End Date</label>
                <input type="date" name="end_date" value="{{ request('end_date', $endDate->format('Y-m-d')) }}" 
                    class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-1 focus:ring-red-500 text-xs">
            </div>

            <!-- Buttons -->
            <div class="flex gap-2">
                <a href="{{ route('finance.reports.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition w-1/2 text-center">
                    Reset Date
                </a>
                <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow transition w-1/2">
                    Filter Reports
                </button>
            </div>
        </form>
    </div>

    <!-- Summary Statistics Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Total Credited -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex flex-col justify-between h-36">
            <div>
                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest block mb-0.5">Total Fare Credited</span>
                <p class="text-3xl font-black text-emerald-600 mt-1">₹{{ number_format($totalCredited, 2) }}</p>
            </div>
            <div class="text-[10px] text-slate-400">
                Sum of credit transactions within specified dates.
            </div>
        </div>

        <!-- Total Commission -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex flex-col justify-between h-36">
            <div>
                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest block mb-0.5">Total Admin Commission Earned</span>
                <p class="text-3xl font-black text-rose-500 mt-1">₹{{ number_format($totalCommissions, 2) }}</p>
            </div>
            <div class="text-[10px] text-slate-400">
                Total commissions collected from operators.
            </div>
        </div>

        <!-- Total Payouts -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex flex-col justify-between h-36">
            <div>
                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest block mb-0.5">Total Payouts Settled</span>
                <p class="text-3xl font-black text-blue-600 mt-1">₹{{ number_format($totalPayouts, 2) }}</p>
            </div>
            <div class="text-[10px] text-slate-400">
                Total payout amounts released to vendors.
            </div>
        </div>
    </div>

    <!-- Statement Export Options -->
    <div class="bg-white shadow rounded-2xl border border-slate-100 p-6 space-y-4">
        <div>
            <h2 class="text-sm font-bold text-slate-800">Export Global Finance Sheets</h2>
            <p class="text-[10px] text-slate-400">Select formats to export global statistics for selected dates.</p>
        </div>

        <div class="flex items-center gap-2 pt-2">
            <button type="button" class="exportBtn px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-sm transition">
                <i class="fas fa-file-excel mr-1"></i> Download Global Excel
            </button>
            <button type="button" class="exportBtn px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold shadow-sm transition">
                <i class="fas fa-file-pdf mr-1"></i> Download Global PDF
            </button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $('.exportBtn').on('click', function() {
        Swal.fire({
            title: 'Reports Export',
            text: 'Your global financial sheet is being exported and download will begin shortly.',
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
