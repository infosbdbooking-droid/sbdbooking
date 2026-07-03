@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <h1 class="text-xl font-bold text-gray-900">Vendor Management</h1>
            <div class="flex items-center gap-3">
                <label class="text-sm font-semibold text-gray-600">Filter Status:</label>
                <select id="statusFilter" class="px-3 py-1.5 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-red-500">
                    <option value="">All Statuses</option>
                    <option value="Pending">Pending</option>
                    <option value="Approved">Approved</option>
                    <option value="Rejected">Rejected</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Vendors Table Card -->
    <div class="bg-white shadow border border-gray-150 rounded-xl overflow-hidden">
        <div class="p-5">
            <div class="overflow-x-auto">
                <table id="vendorsTable" class="min-w-full bg-white text-black border-collapse text-xs">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100 text-gray-700 font-bold uppercase">
                            <th class="py-3 px-4 text-left">SR No.</th>
                            <th class="py-3 px-4 text-left">Photo</th>
                            <th class="py-3 px-4 text-left">Vendor Info</th>
                            <th class="py-3 px-4 text-left">Contact Info</th>
                            <th class="py-3 px-4 text-left">Verification Status</th>
                            <th class="py-3 px-4 text-left">Commission Details</th>
                            <th class="py-3 px-4 text-center">Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <!-- VIEW DOCUMENTS MODAL -->
    <div id="viewDocumentsModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white w-full max-w-2xl mx-auto p-6 rounded-xl shadow-lg relative max-h-[85vh] overflow-y-auto">
            <button type="button" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 text-xl closeModalBtn">&times;</button>
            <h2 class="text-base font-bold text-gray-900 mb-5 border-b pb-2 flex items-center">
                <i class="fas fa-file-alt text-red-500 mr-2"></i> Verification Documents
            </h2>
            <div class="space-y-6">
                <!-- Photo -->
                <div class="flex flex-col md:flex-row gap-4 border-b pb-4">
                    <div class="w-24 h-24 rounded bg-gray-100 flex items-center justify-center overflow-hidden border">
                        <img id="docPhotoImg" src="" alt="Vendor Photo" class="w-full h-full object-cover">
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-gray-700 uppercase">Vendor Profile Photo</h4>
                        <p class="text-[10px] text-gray-400 mt-1">Official photo submitted by the vendor</p>
                        <a id="docPhotoLink" href="#" target="_blank" class="mt-2 inline-flex items-center gap-1 text-[11px] font-bold text-red-500 hover:underline">
                            <i class="fas fa-external-link-alt"></i> View Full Image
                        </a>
                    </div>
                </div>

                <!-- Company Logo -->
                <div class="flex flex-col md:flex-row gap-4 border-b pb-4">
                    <div class="w-24 h-24 rounded bg-gray-100 flex items-center justify-center overflow-hidden border">
                        <img id="docLogoImg" src="" alt="Company Logo" class="w-full h-full object-cover">
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-gray-700 uppercase">Vendor Company Logo</h4>
                        <p class="text-[10px] text-gray-400 mt-1">Official logo submitted by the vendor</p>
                        <a id="docLogoLink" href="#" target="_blank" class="mt-2 inline-flex items-center gap-1 text-[11px] font-bold text-red-500 hover:underline">
                            <i class="fas fa-external-link-alt"></i> View Full Image
                        </a>
                    </div>
                </div>

                <!-- Aadhaar & PAN details -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Aadhaar -->
                    <div class="border p-4 rounded-lg bg-slate-50/50">
                        <h4 class="text-xs font-bold text-gray-800 uppercase flex items-center justify-between border-b pb-1.5 mb-2">
                            <span>Aadhaar card</span>
                            <span id="docAadhaarNo" class="text-gray-500 font-normal">N/A</span>
                        </h4>
                        <div class="flex flex-col gap-2">
                            <a id="docAadhaarLink" href="#" target="_blank" class="inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-white border border-gray-300 rounded hover:bg-gray-50 text-[11px] font-bold text-gray-600 transition">
                                <i class="fas fa-eye text-red-500"></i> View Document
                            </a>
                        </div>
                    </div>
                    <!-- PAN -->
                    <div class="border p-4 rounded-lg bg-slate-50/50">
                        <h4 class="text-xs font-bold text-gray-800 uppercase flex items-center justify-between border-b pb-1.5 mb-2">
                            <span>PAN Card</span>
                            <span id="docPanNo" class="text-gray-500 font-normal">N/A</span>
                        </h4>
                        <div class="flex flex-col gap-2">
                            <a id="docPanLink" href="#" target="_blank" class="inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-white border border-gray-300 rounded hover:bg-gray-50 text-[11px] font-bold text-gray-600 transition">
                                <i class="fas fa-eye text-red-500"></i> View Document
                            </a>
                        </div>
                    </div>
                </div>

                <!-- GST details -->
                <div class="border p-4 rounded-lg bg-slate-50/50">
                    <h4 class="text-xs font-bold text-gray-800 uppercase border-b pb-1.5 mb-2 flex items-center gap-2">
                        <i class="fas fa-university text-gray-400"></i> GSTIN Details
                    </h4>
                    <p class="text-xs">
                        GST Number: <strong id="docGstNo" class="text-gray-700">N/A</strong>
                    </p>
                </div>
            </div>
            <div class="flex justify-end gap-2 mt-6">
                <button type="button" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md text-xs font-semibold closeModalBtn">Close</button>
            </div>
        </div>
    </div>

    <!-- REJECTION REASON MODAL -->
    <div id="rejectModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white w-full max-w-md mx-auto p-6 rounded-xl shadow-lg relative">
            <button type="button" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 text-xl closeRejectModalBtn">&times;</button>
            <h2 class="text-sm font-bold text-gray-900 mb-4">Reject Vendor Verification</h2>
            <form id="rejectForm" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Reason for Rejection <span class="text-red-500">*</span></label>
                    <textarea name="rejection_reason" id="rejection_reason" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-red-500 text-xs" placeholder="Describe the reason (e.g. Blurry photo, incorrect PAN number)..." required></textarea>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md text-xs font-semibold closeRejectModalBtn">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md text-xs font-semibold rejectSubmitBtn">Confirm Reject</button>
                </div>
            </form>
        </div>
    </div>

    <!-- EDIT COMMISSION MODAL -->
    <div id="commissionModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white w-full max-w-md mx-auto p-6 rounded-xl shadow-lg relative">
            <button type="button" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 text-xl closeCommModalBtn">&times;</button>
            <h2 class="text-sm font-bold text-gray-900 mb-4">Edit Vendor Commission Settings</h2>
            <form id="commissionForm" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Commission Type <span class="text-red-500">*</span></label>
                    <select name="commission_type" id="modal_commission_type" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-red-500 text-xs">
                        <option value="percentage">Percentage (%)</option>
                        <option value="flat">Flat Amount (₹)</option>
                    </select>
                </div>
                <div class="mb-4" id="modalPercentageField">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Commission Percentage (%) <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" name="commission_percentage" id="modal_commission_percentage" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-red-500 text-xs" placeholder="e.g. 10.00">
                </div>
                <div class="mb-4 hidden" id="modalFlatField">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Flat Commission Per Booking (₹) <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" name="flat_commission" id="modal_flat_commission" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-red-500 text-xs" placeholder="e.g. 500.00">
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md text-xs font-semibold closeCommModalBtn">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md text-xs font-semibold commSubmitBtn">Save Settings</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function () {
    const viewDocumentsModal = $('#viewDocumentsModal');
    const rejectModal = $('#rejectModal');
    const commissionModal = $('#commissionModal');

    // Close Modals
    $('.closeModalBtn').on('click', () => viewDocumentsModal.removeClass('flex').addClass('hidden'));
    $('.closeRejectModalBtn').on('click', () => rejectModal.removeClass('flex').addClass('hidden'));
    $('.closeCommModalBtn').on('click', () => commissionModal.removeClass('flex').addClass('hidden'));

    // Toggle Commission Fields in Modal
    $('#modal_commission_type').on('change', function () {
        if ($(this).val() === 'percentage') {
            $('#modalPercentageField').removeClass('hidden');
            $('#modalFlatField').addClass('hidden');
        } else {
            $('#modalPercentageField').addClass('hidden');
            $('#modalFlatField').removeClass('hidden');
        }
    });

    // Datatable
    const table = $('#vendorsTable').DataTable({
        processing: true,
        serverSide: true,
        scrollX: true,
        responsive: true,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
        ajax: {
            url: '{{ route("vendors.data") }}',
            type: 'GET',
            data: function (d) {
                d.status = $('#statusFilter').val();
            },
            error: function (xhr) {
                const response = xhr.responseJSON;
                $.toastr.error(response?.message || 'Server error occurred while fetching vendors.');
            }
        },
        columns: [
            {
                data: null,
                name: 'id',
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            {
                data: 'photo',
                name: 'photo',
                orderable: false,
                searchable: false,
                render: function (data) {
                    const src = data ? `/storage/${data}` : 'https://placehold.co/100x100?text=No+Photo';
                    return `<div class="w-10 h-10 rounded-full overflow-hidden border bg-gray-50 flex items-center justify-center">
                                <img src="${src}" alt="Vendor" class="w-full h-full object-cover">
                            </div>`;
                }
            },
            {
                data: null,
                name: 'name',
                render: function (data, type, row) {
                    return `<div>
                                <div class="font-bold text-gray-900 text-sm">${row.name}</div>
                                <div class="text-gray-400 text-[10px]">${row.email}</div>
                            </div>`;
                }
            },
            {
                data: null,
                name: 'mobile',
                render: function (data, type, row) {
                    return `<div>
                                <div class="font-medium text-gray-800">${row.mobile || 'N/A'}</div>
                                <div class="text-gray-400 text-[10px]">Alt: ${row.alternate_mobile || 'N/A'}</div>
                            </div>`;
                }
            },
            {
                data: 'profile_status',
                name: 'profile_status',
                render: function (data, type, row) {
                    let badgeClass = 'bg-yellow-100 text-yellow-800 border-yellow-200';
                    if (data === 'Approved') badgeClass = 'bg-green-100 text-green-800 border-green-200';
                    if (data === 'Rejected') badgeClass = 'bg-red-100 text-red-800 border-red-200';
                    
                    let rejectionHtml = '';
                    if (data === 'Rejected' && row.rejection_reason) {
                        rejectionHtml = `<div class="text-red-400 text-[9px] mt-1 max-w-[150px] truncate" title="${row.rejection_reason}">Reason: ${row.rejection_reason}</div>`;
                    }

                    let verifiedHtml = '';
                    if (data === 'Approved' && row.profile_verified_at) {
                        const verifiedDate = new Date(row.profile_verified_at).toLocaleDateString();
                        verifiedHtml = `<div class="text-gray-400 text-[9px] mt-1">Verified: ${verifiedDate}<br>By: ${row.approved_by_name}</div>`;
                    }

                    return `<div>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border ${badgeClass}">${data}</span>
                                ${rejectionHtml}
                                ${verifiedHtml}
                            </div>`;
                }
            },
            {
                data: null,
                name: 'commission_type',
                render: function (data, type, row) {
                    const typeText = row.commission_type === 'flat' ? 'Flat Fee' : 'Percentage';
                    const valueText = row.commission_type === 'flat' 
                        ? `₹${parseFloat(row.flat_commission || 0).toFixed(2)}` 
                        : `${parseFloat(row.commission_percentage || 0).toFixed(2)}%`;

                    return `<div>
                                <div class="font-semibold text-gray-800">${typeText}</div>
                                <div class="text-red-500 font-bold text-[11px]">${valueText}</div>
                            </div>`;
                }
            },
            {
                data: null,
                name: 'actions',
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    let verifyButtons = '';
                    if (row.profile_status !== 'Approved') {
                        verifyButtons += `<button type="button" class="approve-btn px-2 py-1 bg-green-500 hover:bg-green-600 text-white rounded text-[10px] font-bold" data-id="${row.id}">Approve</button>`;
                    }
                    if (row.profile_status !== 'Rejected') {
                        verifyButtons += `<button type="button" class="reject-btn px-2 py-1 bg-red-500 hover:bg-red-600 text-white rounded text-[10px] font-bold" data-id="${row.id}">Reject</button>`;
                    }

                    return `<div class="flex flex-wrap gap-1.5 justify-center items-center">
                                <button type="button" class="view-docs-btn px-2 py-1 bg-gray-500 hover:bg-gray-600 text-white rounded text-[10px] font-bold" 
                                    data-id="${row.id}"
                                    data-aadhaar-no="${row.aadhaar_number || ''}"
                                    data-aadhaar-file="${row.aadhaar_file || ''}"
                                    data-pan-no="${row.pan_number || ''}"
                                    data-pan-file="${row.pan_file || ''}"
                                    data-gst-no="${row.gst_number || 'N/A'}"
                                    data-photo="${row.photo || ''}"
                                    data-company-logo="${row.company_logo || ''}">Docs</button>
                                <button type="button" class="comm-btn px-2 py-1 bg-blue-500 hover:bg-blue-600 text-white rounded text-[10px] font-bold" 
                                    data-id="${row.id}"
                                    data-comm-type="${row.commission_type || 'percentage'}"
                                    data-comm-percent="${row.commission_percentage || ''}"
                                    data-flat="${row.flat_commission || ''}">Commission</button>
                                ${verifyButtons}
                            </div>`;
                }
            }
        ]
    });

    // Refresh datatable when status changes
    $('#statusFilter').on('change', function () {
        table.ajax.reload();
    });

    // View Documents Modal Open
    $('#vendorsTable tbody').on('click', '.view-docs-btn', function () {
        const btn = $(this);
        const photoSrc = btn.data('photo') ? `/storage/${btn.data('photo')}` : 'https://placehold.co/100x100?text=No+Photo';
        
        $('#docPhotoImg').attr('src', photoSrc);
        $('#docPhotoLink').attr('href', photoSrc);

        const logoSrc = btn.data('company-logo') ? `/storage/${btn.data('company-logo')}` : 'https://placehold.co/100x100?text=No+Logo';
        $('#docLogoImg').attr('src', logoSrc);
        $('#docLogoLink').attr('href', logoSrc);
        
        $('#docAadhaarNo').text(btn.data('aadhaar-no') || 'N/A');
        if (btn.data('aadhaar-file')) {
            $('#docAadhaarLink').attr('href', `/storage/${btn.data('aadhaar-file')}`).removeClass('opacity-50 pointer-events-none');
        } else {
            $('#docAadhaarLink').attr('href', '#').addClass('opacity-50 pointer-events-none');
        }

        $('#docPanNo').text(btn.data('pan-no') || 'N/A');
        if (btn.data('pan-file')) {
            $('#docPanLink').attr('href', `/storage/${btn.data('pan-file')}`).removeClass('opacity-50 pointer-events-none');
        } else {
            $('#docPanLink').attr('href', '#').addClass('opacity-50 pointer-events-none');
        }

        $('#docGstNo').text(btn.data('gst-no') || 'N/A');

        viewDocumentsModal.removeClass('hidden').addClass('flex');
    });

    // Approve action
    $('#vendorsTable tbody').on('click', '.approve-btn', function () {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Approve Vendor Profile?',
            html: '<p class="text-gray-700 text-xs">Are you sure you want to approve this vendor? This will enable their access to all backend booking modules.</p>',
            icon: 'question',
            width: 350,
            showCancelButton: true,
            confirmButtonText: 'Approve',
            cancelButtonText: 'Cancel',
            buttonsStyling: false,
            customClass: {
                popup: 'rounded-xl p-4 shadow-lg border border-gray-100',
                title: 'text-sm font-bold text-gray-800',
                confirmButton: 'bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-xs font-semibold mr-2',
                cancelButton: 'bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-md text-xs font-semibold'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/panel/vendors/${id}/approve`,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (res) {
                        if (res.success) {
                            $.toastr.success(res.message);
                            table.ajax.reload();
                        } else {
                            $.toastr.error(res.message);
                        }
                    },
                    error: function (xhr) {
                        $.toastr.error(xhr.responseJSON?.message || 'Approval request failed.');
                    }
                });
            }
        });
    });

    // Reject action click
    $('#vendorsTable tbody').on('click', '.reject-btn', function () {
        const id = $(this).data('id');
        $('#rejectForm').attr('action', `/panel/vendors/${id}/reject`);
        $('#rejection_reason').val('');
        rejectModal.removeClass('hidden').addClass('flex');
    });

    // Reject Form submit
    $('#rejectForm').on('submit', function (e) {
        e.preventDefault();
        const form = $(this);
        const submitBtn = $('.rejectSubmitBtn');
        const originalText = submitBtn.text();

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            beforeSend: function () {
                submitBtn.html('<span class="animate-spin inline-block w-3.5 h-3.5 border-t-2 border-white rounded-full mr-1"></span> Processing...').prop('disabled', true);
            },
            success: function (res) {
                if (res.success) {
                    $.toastr.success(res.message);
                    table.ajax.reload();
                    rejectModal.removeClass('flex').addClass('hidden');
                } else {
                    $.toastr.error(res.message);
                }
            },
            error: function (xhr) {
                if (xhr.status === 422 && xhr.responseJSON.errors) {
                    $.toastr.error(xhr.responseJSON.errors.rejection_reason[0]);
                } else {
                    $.toastr.error(xhr.responseJSON?.message || 'Rejection request failed.');
                }
            },
            complete: function () {
                submitBtn.html(originalText).prop('disabled', false);
            }
        });
    });

    // Edit Commission modal open
    $('#vendorsTable tbody').on('click', '.comm-btn', function () {
        const btn = $(this);
        const id = btn.data('id');
        $('#commissionForm').attr('action', `/panel/vendors/${id}/commission`);
        
        const commType = btn.data('comm-type');
        $('#modal_commission_type').val(commType).trigger('change');
        
        $('#modal_commission_percentage').val(btn.data('comm-percent'));
        $('#modal_flat_commission').val(btn.data('flat'));

        commissionModal.removeClass('hidden').addClass('flex');
    });

    // Edit Commission submit
    $('#commissionForm').on('submit', function (e) {
        e.preventDefault();
        const form = $(this);
        const submitBtn = $('.commSubmitBtn');
        const originalText = submitBtn.text();

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            beforeSend: function () {
                submitBtn.html('<span class="animate-spin inline-block w-3.5 h-3.5 border-t-2 border-white rounded-full mr-1"></span> Processing...').prop('disabled', true);
            },
            success: function (res) {
                if (res.success) {
                    $.toastr.success(res.message);
                    table.ajax.reload();
                    commissionModal.removeClass('flex').addClass('hidden');
                } else {
                    $.toastr.error(res.message);
                }
            },
            error: function (xhr) {
                if (xhr.status === 422 && xhr.responseJSON.errors) {
                    $.each(xhr.responseJSON.errors, function(k, v) {
                        $.toastr.error(v[0]);
                    });
                } else {
                    $.toastr.error(xhr.responseJSON?.message || 'Update commission request failed.');
                }
            },
            complete: function () {
                submitBtn.html(originalText).prop('disabled', false);
            }
        });
    });
});
</script>
@endsection
