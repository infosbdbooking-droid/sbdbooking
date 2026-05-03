@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <h1 class="text-xl font-bold text-gray-900">List of Roles</h1>
                <div class="flex flex-wrap gap-2">
                    <button type="button" id="addModalBtn"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md flex items-center transition-colors">
                        <i class="fas fa-plus mr-2"></i>Add Role
                    </button>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white shadow border border-white rounded-lg">
            <div class="p-4">
                <div class="overflow-x-auto">
                    <table id="table" class="min-w-full bg-white text-black border-gray-200">
                        <thead>
                            <tr>
                                <th>SR No.</th>
                                <th>Title</th>
                                <th>Permissions</th>
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
                <form id="dataForm" action="{{ route('access.roles.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Title <span
                                class="text-black-500">*</span></label>
                        <input type="text" name="title" id="title"
                            class="w-full px-3 py-2 border-b border-gray-300 focus:outline-none focus:border-blue-500">
                    </div>


                    <div class="mb-4">
                        <label for="permissions" class="block text-sm font-medium text-gray-700 mb-1">
                            Permissions
                            <button type="button"
                                class="select-all inline-block bg-blue-600 hover:bg-blue-700 text-white text-xs px-2 py-1 rounded ml-2 transition">
                                Select all
                            </button>
                            <button type="button"
                                class="deselect-all inline-block bg-blue-600 hover:bg-blue-700 text-white text-xs px-2 py-1 rounded ml-2 transition">
                                Deselect all
                            </button>
                        </label>
                        <select name="permissions[]" id="permissions" multiple
                            class="select2 w-full h-auto px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring focus:border-blue-500">
                        </select>
                    </div>

                    <div class="flex justify-end gap-2">
                        <button type="button"
                            class="px-4 py-2 closeModal bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-md">Cancel</button>
                        <button type="submit"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white addBtn rounded-md">Add
                            Role</button>
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
                $('#Modal h2').text('Add Roles');
                $('.addBtn').text('Add Role');
                $('#dataForm')[0].reset();
                isEditing = false;
                editingId = null;
                $('#Modal').removeClass('hidden').addClass('flex');
                fetchPermissions();
            });
            // Close modal
            $('.closeModal, .closeUserModal').on('click', function () {
                $('#Modal').removeClass('flex').addClass('hidden');
            });
            $('#Modal').on('click', function (e) {
                if ($(e.target).is('#Modal')) {
                    $(this).removeClass('flex').addClass('hidden');
                }
            });
            // Initialize select2
            $('#permissions').select2({
                width: '100%',
                placeholder: 'Select permissions',
            });
            function fetchPermissions() {
                return new Promise(function (resolve, reject) {
                    $.ajax({
                        url: "{{ route('access.roles.permissionsData') }}",
                        method: 'GET',
                        success: function (data) {
                            $('#permissions').empty();
                            $.each(data, function (id, name) {
                                const option = new Option(name, id, false, false);
                                $('#permissions').append(option);
                            });
                            $('#permissions').trigger('change');
                            resolve();
                        },
                        error: function () {
                            $.toastr.error("Something went wrong!");
                        }
                    });
                });
            }

            // Select all permissions
            $('.select-all').click(function () {
                $('#permissions > option').prop("selected", true);
                $('#permissions').trigger("change");
            });
            // Deselect all permissions
            $('.deselect-all').click(function () {
                $('#permissions > option').prop("selected", false);
                $('#permissions').trigger("change");
            });
            // DataTable
            const table = $('#table').DataTable({
                processing: true,
                serverSide: true,
                scrollX: true,
                responsive: true,
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
                ajax: {
                    url: '{{ route("access.roles.data") }}',
                    type: 'GET',
                    dataType: 'json',
                    error: function (xhr) {
                        const response = xhr.responseJSON;
                        if (response && response.message) {
                            $.toastr.error(response.message);
                        } else {
                            $.toastr.error('Server error occurred while fetching roles.');
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
                    {
                        data: 'title',
                        name: 'title'
                    },
                    {
                        data: 'permissions',
                        name: 'permissions',
                        render: function (data) {
                            if (!data) return '-';
                            const items = data.split(',').map(item => item.trim());
                            return items.map(name => {
                                return `<span class="inline-block bg-blue-600 text-white text-xs px-2 py-1 rounded mr-1 mb-1">${name}</span>`;
                            }).join(' ');
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


            //form submit
            $('#dataForm').on('submit', function (e) {
                e.preventDefault();
                const form = this;
                const formData = new FormData(form);
                const url = isEditing ? `{{ url('access/roles') }}/${editingId}` : $(form).attr('action');
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
                        $(".addBtn").html(isEditing ? 'Update Role' : 'Add Role').prop('disabled', false);
                    }
                });
            });

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

                $.get(`{{ url('access/roles') }}/${editingId}/edit`, function (data) {
                    $('#title').val(data.title);
                    $('#Modal h2').text('Edit Role');
                    $('.addBtn').text('Update Role');
                    $('#Modal').removeClass('hidden').addClass('flex');

                    fetchPermissions().then(() => {
                        $('#permissions').val(data.permissions).trigger('change');
                    });
                });
            });

            $('#table tbody').on('click', '.delete-btn', function () {
                const id = $(this).data('id');
                Swal.fire({
                    title: 'Confirm Delete',
                    html: '<p class="text-gray-700 text-sm">Are you sure you want to delete this permission? This action cannot be undone.</p>',
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
                            url: `{{ url('access/roles') }}/${id}`,
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
                                $.toastr.error('Failed to delete Rolse.');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection