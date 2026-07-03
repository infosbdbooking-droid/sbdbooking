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

    <div>
        <h1 class="text-2xl font-black text-gray-900 tracking-tight">Settlement Requests</h1>
        <p class="text-xs text-gray-500">Manage vendor withdrawal and payout requests.</p>
    </div>

    <!-- Main Grid Layout -->
    <div class="grid grid-cols-1 gap-6">
        
        <!-- Pending Settlement Requests Card -->
        <div class="bg-white shadow rounded-2xl border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <div>
                    <h2 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-yellow-500 animate-pulse"></span>
                        Pending Requests
                    </h2>
                    <p class="text-[10px] text-slate-400">Vendor payout requests awaiting approval.</p>
                </div>
                <span class="px-2 py-0.5 bg-yellow-100 text-yellow-800 rounded-md text-[9px] font-bold">{{ count($pendingSettlements) }} Pending</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 border-b border-slate-100 font-bold uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="px-6 py-3.5">Vendor</th>
                            <th class="px-6 py-3.5">Amount</th>
                            <th class="px-6 py-3.5">Method</th>
                            <th class="px-6 py-3.5">Details</th>
                            <th class="px-6 py-3.5 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse($pendingSettlements as $set)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-800">{{ $set->vendor?->name ?? 'N/A' }}</div>
                                    <div class="text-[10px] text-slate-400">{{ $set->vendor?->email ?? '' }}</div>
                                </td>
                                <td class="px-6 py-4 font-black text-slate-900">
                                    ₹{{ number_format($set->amount, 2) }}
                                </td>
                                <td class="px-6 py-4 font-semibold text-slate-700">
                                    {{ $set->payout_method }}
                                </td>
                                <td class="px-6 py-4 text-slate-500">
                                    {{ $set->notes ?: '—' }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button type="button" class="approve-btn bg-emerald-500 hover:bg-emerald-600 text-white px-3 py-1.5 rounded-lg font-bold text-[10px] uppercase tracking-wider transition shadow-sm" data-id="{{ $set->id }}">
                                            Approve
                                        </button>
                                        <button type="button" class="reject-btn bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-lg font-bold text-[10px] uppercase tracking-wider transition shadow-sm" data-id="{{ $set->id }}">
                                            Reject
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                    <i class="fas fa-check-circle text-3xl text-slate-200 mb-2 block"></i>
                                    All payout requests are processed.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Settlement History Card -->
        <div class="bg-white shadow rounded-2xl border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <div>
                    <h2 class="text-sm font-bold text-slate-800">Payout History</h2>
                    <p class="text-[10px] text-slate-400">List of historically processed requests.</p>
                </div>
                <i class="fas fa-history text-slate-300"></i>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 border-b border-slate-100 font-bold uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="px-6 py-3.5">Date</th>
                            <th class="px-6 py-3.5">Vendor</th>
                            <th class="px-6 py-3.5">Amount</th>
                            <th class="px-6 py-3.5">Method</th>
                            <th class="px-6 py-3.5">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse($settlementsHistory as $set)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="px-6 py-4 text-slate-400 font-medium">
                                    {{ $set->created_at->format('d M Y') }}
                                    <div class="text-[9px] text-slate-300">{{ $set->created_at->format('h:i A') }}</div>
                                </td>
                                <td class="px-6 py-4 font-bold text-slate-800">
                                    {{ $set->vendor?->name ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 font-black text-slate-900">
                                    ₹{{ number_format($set->amount, 2) }}
                                </td>
                                <td class="px-6 py-4 text-slate-500">
                                    {{ $set->payout_method }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($set->status === 'approved')
                                        <span class="px-2 py-0.5 bg-green-50 text-green-700 border border-green-200 rounded-full text-[9px] font-bold">Approved</span>
                                    @else
                                        <span class="px-2 py-0.5 bg-red-50 text-red-700 border border-red-200 rounded-full text-[9px] font-bold" title="{{ $set->notes }}">Rejected</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                    <i class="fas fa-folder-open text-3xl text-slate-200 mb-2 block"></i>
                                    No historical payouts recorded.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($settlementsHistory->hasPages())
                <div class="px-6 py-4 border-t border-slate-100">
                    {{ $settlementsHistory->links() }}
                </div>
            @endif
        </div>

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
                <textarea name="rejection_reason" id="rejection_reason" rows="3" required placeholder="Describe the reason (e.g. invalid bank details, UPI ID does not exist)..."
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
    const rejectModal = $('#rejectSettlementModal');
    let activeRejectId = null;

    // Close Reject Modal
    $('.closeRejectModalBtn').on('click', function() {
        rejectModal.removeClass('flex').addClass('hidden');
        $('#rejectSettlementForm')[0].reset();
        activeRejectId = null;
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
});
</script>
@endsection
