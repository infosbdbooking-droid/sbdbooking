@extends('layouts.app')
@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex flex-col md:flex-row  justify-between items-start md:items-center gap-4">
                <h1 class="text-xl font-bold text-gray-900 ">List of Users</h1>
                <div class="flex flex-wrap gap-2">
                    <button type="button" id="addUser"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700   text-white rounded-md flex items-center transition-colors"><i
                            class="fas fa-plus mr-2"></i>Add User
                    </button>
                </div>
            </div>
        </div>
        <!-- EMPLOYEES TABLE -->
        <div class="bg-white  shadow border border-white  rounded-lg">
            <div class="p-4">
                <div class="overflow-x-auto">
                    <table id="table" class="min-w-full bg-white text-black border-gray-200">
                        <thead>
                            <tr>
                                <th>SR No.</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
        <!-- ADD USER MODAL -->
        <div id="userModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 ">
            <div class="bg-white w-full max-w-3xl mx-auto p-6 rounded-lg shadow-lg relative">
                <button type="button"
                    class="absolute closeUserModal top-4 right-4 text-gray-500 hover:text-gray-700 text-xl">
                    &times;
                </button>
                <h2 id="userModalTitle" class="text-lg font-bold mb-4">Add New User</h2>
                <form id="userForm" action="{{ route('access.user.store')}}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Name<span
                                class="text-black-500">*</span></label>
                        <input type="text" name="name" id="name"
                            class="w-full px-3 py-2 border-b border-gray-300 focus:outline-none focus:border-blue-500"
                            placeholder="e.g. Mr.Suraj">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email<span
                                class="text-black-500">*</span></label>
                        <input type="email" name="email" id="email"
                            class="w-full px-3 py-2 border-b border-gray-300 focus:outline-none focus:border-blue-500"
                            placeholder="e.g. surajgupta3118@gmail.com">
                    </div>
                    <div class="mb-4">
                        <label for="permissions" class="block text-sm font-medium text-gray-700 mb-1">
                            Role
                        </label>
                        <select name="role" id="role"
                            class="select2 w-full h-auto px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring focus:border-blue-500">
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password<span
                                class="text-black-500">*</span></label>
                        <input type="password" name="password" id="password"
                            class="w-full px-3 py-2 border-b border-gray-300 focus:outline-none focus:border-blue-500"
                            placeholder="Enter your password">
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button"
                            class="px-4 py-2 closeUserModal bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-md">Cancel</button>
                        <button type="submit"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white addBtn rounded-md">Add User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
    $(document).ready(function () {
        const userModal = $('#userModal');
        const userForm = $('#userForm');
        const passwordField = $('#password').closest('.mb-4');
        const submitBtn = $('.addBtn');

        // Open modal for new user
        $('#addUser').on('click', function () {
            userForm[0].reset();
            userForm.find('input[name="_method"]').remove();
            userForm.attr('action', '{{ route("access.user.store") }}');
            passwordField.show();
            fetchRole().then(() => $('#role').val('').trigger('change'));
            submitBtn.text('Add User');
            $('#userModalTitle').text('Add New User');
            userModal.removeClass('hidden').addClass('flex');
        });

        // Close modal
        $('.closeUserModal').on('click', () => userModal.removeClass('flex').addClass('hidden'));
        userModal.on('click', e => { if ($(e.target).is('#userModal')) userModal.removeClass('flex').addClass('hidden'); });

        // Get Roles
        function fetchRole() {
            return $.ajax({
                url: "{{ route('access.user.getRole') }}",
                method: 'GET',
                success: function (data) {
                    $('#role').empty().append(new Option("Select Role", ""));
                    $.each(data, function (id, name) {
                        $('#role').append(new Option(name, id));
                    });
                },
                error: function () {
                    toastr.error("Failed to load roles.");
                }
            });
        }

        // DataTable
         const table = $('#table').DataTable({
                processing: true,
                serverSide: true,
                scrollX: true,
                responsive: true,
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
                ajax: {
                    url: '{{ route("access.user.data") }}',
                    type: 'GET',
                    dataType: 'json',
                    error: function (xhr) {
                        const response = xhr.responseJSON;
                        if (response && response.message) {
                            $.toastr.error(response.message);
                        } else {
                            $.toastr.error('Server error occurred while fetching users.');
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
                    { data: 'name', name: 'name' },
                    { data: 'email', name: 'email' },
                    { data: 'roles.title', name: 'roles.title' },
                    {
                        data: null,
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

        // jQuery Validate
        userForm.validate({
            submitHandler: function (form, event) {
                event.preventDefault();
                $(form).ajaxSubmit({
                    beforeSend: function () {
                        submitBtn.html('<span class="animate-spin inline-block w-4 h-4 border-t-2 border-white rounded-full mr-2"></span> Processing...')
                            .prop('disabled', true);
                    },
                    success: function (response) {
                        if (response.success) {
                            $.toastr.success(response.message);
                            table.ajax.reload();
                            userModal.removeClass('flex').addClass('hidden');
                            form.reset();
                            
                        } else {
                            $.toastr.error(response.message);
                        }
                    },
                    error: function (xhr) {
                      handleValidationErrors(xhr)
                    },
                    complete: function () {
                        submitBtn.html('Add User').prop('disabled', false);
                    }
                });
            }
        });

        // Edit user
        $('#table tbody').on('click', '.edit-btn', function () {
            const id = $(this).data('id');
            $.ajax({
                url: `{{ url('access/user') }}/${id}/edit`,
                type: 'GET',
                success: function (data) {
                    userForm[0].reset();
                    $('#name').val(data.name);
                    $('#email').val(data.email);
                    $('#password').val('');
                    userForm.attr('action', `{{ url('access/user') }}/${data.id}`);
                    if (!userForm.find('input[name="_method"]').length) {
                        userForm.append('<input type="hidden" name="_method" value="PUT">');
                    }
                    fetchRole().then(() => {
                        $('#role').val(data.role_id).trigger('change');
                    });
                    submitBtn.text('Update User');
                    $('#userModalTitle').text('Edit User');
                    userModal.removeClass('hidden').addClass('flex');
                },
                error: function () {
                    toastr.error('Failed to fetch user data.');
                }
            });
        });
         function handleValidationErrors(xhr) {
                $('.error-text').remove();
                $('input, select, textarea').removeClass('border-red-500');
                $('.select2').next('.select2').removeClass('border-red-500');
                if (xhr.status === 422 && xhr.responseJSON.errors) {
                    $.each(xhr.responseJSON.errors, function (key, val) {
                        const input = $('[name="' + key + '"]');
                        input.addClass('border-red-500');
                        if (input.hasClass('select2')) {
                            const container = input.next('.select2');
                            container.addClass('border-red-500');
                            if (!container.next('.error-text').length) {
                                container.after(`<span class="text-red-500 text-sm error-text">${val[0]}</span>`);
                            }
                        } else {
                            if (!input.next('.error-text').length) {
                                input.after(`<span class="text-red-500 text-sm error-text">${val[0]}</span>`);
                            }
                        }
                        $.toastr.error(val[0]);
                    });
                } else {
                    $.toastr.error("Something went wrong!");
                }
            }

        // Delete user
        $('#table tbody').on('click', '.delete-btn', function () {
            const id = $(this).data('id');
             Swal.fire({
                    title: 'Confirm Delete',
                    html: '<p class="text-gray-700 text-sm">Are you sure you want to delete this service frequency? This action cannot be undone.</p>',
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
                        url: `{{ url('access/user') }}/${id}`,
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
                                toastr.error(response.message);
                            }
                        },
                        error: function () {
                            $.toastr.error('Delete request failed.');
                        }
                    });
                }
            });
        });
    });
</script>

@endsection