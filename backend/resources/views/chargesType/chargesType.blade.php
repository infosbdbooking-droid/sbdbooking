@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <h1 class="text-xl font-bold text-gray-900">List of Charges Type</h1>
                <div class="flex flex-wrap gap-2">
                    <button type="button" id="addModalBtn"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md flex items-center transition-colors">
                        <i class="fas fa-plus mr-2"></i>Charges Type
                    </button>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white shadow border border-white rounded-lg">
            <div class="p-4">
                <div class="overflow-x-auto">
                    <table id="table" class="min-w-full bg-white text-black border-gray-200  text-center">
                        <thead>
                            <tr>
                                <th>SR No.</th>
                                <th>Charges Type</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200"></tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div id="Modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
            <div class="bg-white w-full max-w-3xl mx-auto p-6 rounded-lg shadow-lg relative">
                <button type="button"
                    class="absolute closeUserModal top-4 right-4 text-gray-500 hover:text-gray-700 text-xl">&times;</button>
                <h2 class="text-lg font-bold mb-4">Add Roles</h2>
                <form id="dataForm" action="{{ route('chargesType.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Charges Type<span
                                class="text-black-500">*</span></label>
                      <input type="text" name="charges_type" id="charges_type"
                        placeholder="e.g. Night Charges, Emergency Charges, Weekend Charges"
                        class="w-full mt-3 border border-gray-300 rounded px-3 py-2">
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button"
                            class="px-4 py-2 closeModal bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-md">Cancel</button>
                        <button type="submit"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white addBtn rounded-md"></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            let isEditing = false;
            let editingId = null;

            // Open modal
            $('#addModalBtn').on('click', function () {
                $('#Modal h2').text('Add Charges Type');
                $('.addBtn').text('Add Charges Type');
                $('#dataForm')[0].reset();
                isEditing = false;
                editingId = null;
                $('#Modal').removeClass('hidden').addClass('flex');
            });

            // Close modal
            $('.closeModal, .closeUserModal').on('click', function () {
                $('#Modal').removeClass('flex').addClass('hidden');
            });

            /* 
            $('#Modal').on('click', function (e) {
                if ($(e.target).is('#Modal')) {
                    $(this).removeClass('flex').addClass('hidden');
                }
            });
            */

            // Initialize DataTable
            // Toggle Status
            const table = $('#table').DataTable({
                processing: true,
                serverSide: true,
                scrollX: true,
                responsive: true,
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
                ajax: {
                    url: '{{ route("chargesType.data") }}',
                    type: 'GET',
                    dataType: 'json',
                    error: function (xhr) {
                        const response = xhr.responseJSON;
                        if (response && response.message) {
                            $.toastr.error(response.message);
                        } else {
                            $.toastr.error('Server error occurred while fetching Charges types.');
                        }
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
                    { data: 'charges_type', name: 'charges_type' },
                    {
                        data: 'status',
                        name: 'status',
                        render: function (data, type, row) {
                            const label = row.status === 1 ? 'Active' : 'Inactive';
                            const toggleClass = row.status === 1 ? 'bg-green-500' : 'bg-red-400';
                            return `
                                <button class="toggle-status px-3 py-1 text-white rounded ${toggleClass}" 
                                    data-id="${row.id}" data-status="${row.status}">
                                    ${label}
                                </button>
                            `;
                        }
                    },
                    {
                        data: 'id',
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        render: function (data, type, row) {
                            return `
                            <div class="flex space-x-2 justify-center">
                                <button type="button" class="edit-btn px-3 py-1 bg-blue-500 hover:bg-blue-600 text-white rounded text-sm" data-id="${row.id}">
                                    Edit
                                </button>
                                <button type="button" class="delete-btn px-3 py-1 bg-red-500 hover:bg-red-600 text-white rounded text-sm" data-id="${row.id}">
                                    Delete
                                </button>
                            </div>
                            `;
                        }
                    }
                ]
            });

            // Form Submit (Add or Update)
            $('#dataForm').on('submit', function (e) {
                e.preventDefault();
                const form = this;
                const formData = new FormData(form);
                const url = isEditing ? `chargesType/${editingId}` : $(form).attr('action');
                if (isEditing) formData.append('_method', 'PUT');

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    beforeSend: function () {
                        $(".addBtn").html('<span class="animate-spin inline-block w-4 h-4 border-t-2 border-white rounded-full mr-2"></span> Processing...').prop('disabled', true);
                    },
                    success: function (response) {
                        if (response.success) {
                            $.toastr.success(response.message);
                            handleSuccessResponse(form);
                        } else {
                            $.toastr.error(response.message);
                        }
                    },
                    error: function (xhr) {
                        handleValidationErrors(xhr);
                    },
                    complete: function () {
                        $(".addBtn").html(isEditing ? 'Update Charges Type' : 'Add Charges Type').prop('disabled', false);
                    }
                });
            });

            // Handle Validation Errors
            function handleValidationErrors(xhr) {
                $('.error-text').remove();
                $('input, select, textarea').removeClass('border-red-500');
                if (xhr.status === 422 && xhr.responseJSON.errors) {
                    $.each(xhr.responseJSON.errors, function (key, val) {
                        const input = $('[name="' + key + '"]');
                        input.addClass('border-red-500');
                        input.after(`<span class="text-red-500 text-sm error-text">${val[0]}</span>`);
                        $.toastr.error(val[0]);
                    });
                } else {
                    $.toastr.error("Something went wrong!");
                }
            }

            function handleSuccessResponse(form) {
                $('#Modal').removeClass('flex').addClass('hidden');
                form.reset();
                table.ajax.reload();
                isEditing = false;
                editingId = null;
            }

            // Edit
            $('#table tbody').on('click', '.edit-btn', function () {
                editingId = $(this).data('id');
                isEditing = true;

                $.get(`chargesType/${editingId}/edit`, function (data) {
                    $('#charges_type').val(data.charges_type);
                    $('#Modal h2').text('Edit Charges Type');
                    $('.addBtn').text('Update Charges Type');
                    $('#Modal').removeClass('hidden').addClass('flex');
                });
            });

            // Delete
            $('#table tbody').on('click', '.delete-btn', function () {
                const id = $(this).data('id');
                Swal.fire({
                    title: 'Confirm Delete',
                    html: '<p class="text-gray-700 text-sm">Are you sure you want to delete this Charges type?</p>',
                    icon: 'warning',
                    width: 350,
                    showCancelButton: true,
                    confirmButtonText: 'Delete',
                    cancelButtonText: 'Cancel',
                    buttonsStyling: false,
                    customClass: {
                        popup: 'rounded-lg p-4 shadow-md',
                        title: 'text-lg font-semibold text-gray-800',
                        confirmButton: 'bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded mr-2',
                        cancelButton: 'bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `chargesType/${id}`,
                            type: 'POST',
                            data: {
                                _method: 'DELETE',
                                _token: '{{ csrf_token() }}'
                            },
                            success: function (response) {
                                if (response.success) {
                                    $.toastr.success(response.message);
                                    table.ajax.reload();
                                } else {
                                    $.toastr.error(response.message);
                                }
                            },
                            error: function () {
                                $.toastr.error('Failed to delete.');
                            }
                        });
                    }
                });
            });



            $('#table tbody').on('click', '.toggle-status', function () {
                const button = $(this);
                const id = button.data('id');
                const currentStatus = button.data('status');
                const newStatus = currentStatus === 1 ? 0 : 1;
                Swal.fire({
                    title: 'Are you sure?',
                    html: `<p class="text-gray-700 text-sm">You are about to change the status to ${newStatus === 1 ? 'Active' : 'Inactive'}.</p>`,
                    icon: 'warning',
                    width: 350,
                    showCancelButton: true,
                    confirmButtonText: 'Yes, change it!',
                    cancelButtonText: 'Cancel',
                    buttonsStyling: false,
                    customClass: {
                        popup: 'rounded-lg p-4 shadow-md',
                        title: 'text-lg font-semibold text-gray-800',
                        confirmButton: 'bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded mr-2',
                        cancelButton: 'bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `chargesType/${id}/changeStatus`,
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                status: newStatus
                            },
                            success: function (response) {
                                if (response.success) {
                                    $.toastr.success(response.message);
                                    button.data('status', newStatus);
                                    const label = newStatus === 1 ? 'Active' : 'Inactive';
                                    const toggleClass = newStatus === 1 ? 'bg-green-500' : 'bg-red-400';
                                    button.text(label).removeClass('bg-green-500 bg-red-400').addClass(toggleClass);
                                } else {
                                    $.toastr.error(response.message);
                                }
                            },
                            error: function () {
                                $.toastr.error('Failed to change status.');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection