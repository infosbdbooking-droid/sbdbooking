@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <nav class="flex text-xs text-gray-500 mb-2" aria-label="Breadcrumb">
                        <ol class="inline-flex items-center space-x-1 md:space-x-2">
                            <li class="inline-flex items-center">
                                <a href="{{ route('dashboard') }}" class="hover:text-gray-700">Dashboard</a>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <i class="fas fa-chevron-right text-[10px] mx-2 text-gray-400"></i>
                                    <span class="text-gray-900 font-medium">Cities</span>
                                </div>
                            </li>
                        </ol>
                    </nav>
                    <h1 class="text-xl font-bold text-gray-900">Cities Master</h1>
                </div>
                <div class="flex flex-wrap gap-2">
                    <button type="button" id="addModalBtn"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md flex items-center transition shadow font-semibold text-sm">
                        <i class="fas fa-plus mr-2"></i>Add City
                    </button>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white shadow border border-gray-100 rounded-lg">
            <div class="p-4">
                <div class="overflow-x-auto">
                    <table id="table" class="min-w-full bg-white text-black border-gray-200 text-center">
                        <thead>
                            <tr>
                                <th>SR No.</th>
                                <th>State Name</th>
                                <th>City Name</th>
                                <th>Slug</th>
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
            <div class="bg-white w-full max-w-lg mx-auto p-6 rounded-lg shadow-lg relative">
                <button type="button"
                    class="absolute closeUserModal top-4 right-4 text-gray-500 hover:text-gray-700 text-xl focus:outline-none">&times;</button>
                <h2 class="text-lg font-bold mb-4" id="modalTitle">Add City</h2>
                <form id="dataForm" action="{{ route('seoCities.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Select State <span class="text-red-500">*</span></label>
                        <select name="state_id" id="city_state_id" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-1 focus:ring-blue-500 focus:outline-none">
                            <option value="">Select State</option>
                            @foreach ($states as $state)
                                <option value="{{ $state->id }}">{{ $state->state_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4"> 
                        <label class="block text-sm font-medium text-gray-700 mb-1">City Name <span class="text-red-500">*</span></label>
                        <input type="text" name="city_name" id="city_name"
                            placeholder="e.g. Panaji, Mumbai, Pune..."
                            class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-1 focus:ring-blue-500 focus:outline-none">
                    </div>
                    <div class="mb-4"> 
                        <label class="block text-sm font-medium text-gray-700 mb-1">Slug (Auto-generated)</label>
                        <input type="text" name="slug" id="city_slug" readonly
                            class="w-full border border-gray-100 bg-gray-50 text-gray-500 rounded px-3 py-2 focus:outline-none">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" id="city_status" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-1 focus:ring-blue-500 focus:outline-none">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                    <div class="flex justify-end gap-2 mt-6">
                        <button type="button"
                            class="px-4 py-2 closeModal bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-md">Cancel</button>
                        <button type="submit"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white addBtn rounded-md shadow">Save</button>
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

            // Auto-generate slug
            $('#city_name').on('input', function() {
                let name = $(this).val();
                let slug = name.toLowerCase()
                    .replace(/[^a-z0-9 -]/g, '')
                    .replace(/\s+/g, '-')
                    .replace(/-+/g, '-');
                $('#city_slug').val(slug);
            });

            // Open modal
            $('#addModalBtn').on('click', function () {
                $('#modalTitle').text('Add City');
                $('.addBtn').text('Add City');
                $('#dataForm')[0].reset();
                isEditing = false;
                editingId = null;
                $('#Modal').removeClass('hidden').addClass('flex');
            });

            // Close modal
            $('.closeModal, .closeUserModal').on('click', function () {
                $('#Modal').removeClass('flex').addClass('hidden');
            });

            // Initialize DataTable
            const table = $('#table').DataTable({
                processing: true,
                serverSide: true,
                scrollX: true,
                responsive: true,
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
                ajax: {
                    url: '{{ route("seoCities.data") }}',
                    type: 'GET',
                    dataType: 'json',
                    error: function (xhr) {
                        $.toastr.error('Error fetching cities.');
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
                    { data: 'state_name', name: 'state.state_name' },
                    { data: 'city_name', name: 'city_name' },
                    { data: 'slug', name: 'slug' },
                    {
                        data: 'status',
                        name: 'status',
                        render: function (data, type, row) {
                            const label = row.status === 1 ? 'Active' : 'Inactive';
                            const toggleClass = row.status === 1 ? 'bg-green-500' : 'bg-red-400';
                            return `
                                <button class="toggle-status px-3 py-1 text-white rounded text-xs font-semibold ${toggleClass}" 
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
                                <button type="button" class="edit-btn px-3 py-1 bg-blue-500 hover:bg-blue-600 text-white rounded text-xs font-semibold transition" data-id="${row.id}">
                                    Edit
                                </button>
                                <button type="button" class="delete-btn px-3 py-1 bg-red-500 hover:bg-red-600 text-white rounded text-xs font-semibold transition" data-id="${row.id}">
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
                const url = isEditing ? `{{ url('panel/seo-cities') }}/${editingId}` : $(form).attr('action');
                if (isEditing) formData.append('_method', 'PUT');

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    beforeSend: function () {
                        $(".addBtn").html('<span class="animate-spin inline-block w-4 h-4 border-t-2 border-white rounded-full mr-2"></span> Saving...').prop('disabled', true);
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
                        $(".addBtn").html('Save').prop('disabled', false);
                    }
                });
            });

            function handleValidationErrors(xhr) {
                $('.error-text').remove();
                $('input, select').removeClass('border-red-500');
                if (xhr.status === 422 && xhr.responseJSON.errors) {
                    $.each(xhr.responseJSON.errors, function (key, val) {
                        const input = $('[name="' + key + '"]');
                        input.addClass('border-red-500');
                        input.after(`<span class="text-red-500 text-xs error-text mt-1 block">${val[0]}</span>`);
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

                $.get(`{{ url('panel/seo-cities') }}/${editingId}/edit`, function (data) {
                    $('#city_state_id').val(data.state_id);
                    $('#city_name').val(data.city_name);
                    $('#city_slug').val(data.slug);
                    $('#city_status').val(data.status);
                    $('#modalTitle').text('Edit City');
                    $('.addBtn').text('Update City');
                    $('#Modal').removeClass('hidden').addClass('flex');
                });
            });

            // Delete
            $('#table tbody').on('click', '.delete-btn', function () {
                const id = $(this).data('id');
                Swal.fire({
                    title: 'Confirm Delete',
                    html: '<p class="text-gray-700 text-sm">Are you sure you want to delete this city?</p>',
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
                            url: `{{ url('panel/seo-cities') }}/${id}`,
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
                                $.toastr.error('Failed to delete city.');
                            }
                        });
                    }
                });
            });

            // Toggle status click
            $('#table tbody').on('click', '.toggle-status', function () {
                const button = $(this);
                const id = button.data('id');
                const currentStatus = parseInt(button.data('status'));
                const newStatus = currentStatus === 1 ? 0 : 1;
                
                $.ajax({
                    url: `{{ url('panel/seo-cities') }}/${id}/changeStatus`,
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
            });
        });
    </script>
@endsection
