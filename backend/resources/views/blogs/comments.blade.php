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
                                    <span class="text-gray-900 font-medium">Comments</span>
                                </div>
                            </li>
                        </ol>
                    </nav>
                    <h1 class="text-xl font-bold text-gray-900">Blog Comments</h1>
                </div>
                <!-- Bulk Actions -->
                <div class="flex flex-wrap gap-2 items-center">
                    <span class="text-xs font-semibold text-gray-500 hidden id-selected-count bg-gray-100 px-2 py-1 rounded">0 Selected</span>
                    <button type="button" id="bulkApproveBtn" disabled
                        class="bulk-btn px-3 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md text-xs font-semibold flex items-center transition shadow opacity-50 cursor-not-allowed">
                        <i class="fas fa-check mr-1.5"></i>Approve
                    </button>
                    <button type="button" id="bulkRejectBtn" disabled
                        class="bulk-btn px-3 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-md text-xs font-semibold flex items-center transition shadow opacity-50 cursor-not-allowed">
                        <i class="fas fa-ban mr-1.5"></i>Reject
                    </button>
                    <button type="button" id="bulkDeleteBtn" disabled
                        class="bulk-btn px-3 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md text-xs font-semibold flex items-center transition shadow opacity-50 cursor-not-allowed">
                        <i class="fas fa-trash-alt mr-1.5"></i>Delete
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
                                <th>
                                    <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                </th>
                                <th>SR No.</th>
                                <th>Blog</th>
                                <th>User Name</th>
                                <th>Email</th>
                                <th>Comment</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200"></tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- View Comment Modal -->
        <div id="viewModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
            <div class="bg-white w-full max-w-lg mx-auto p-6 rounded-lg shadow-lg relative">
                <button type="button" id="closeViewModal"
                    class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 text-xl focus:outline-none">&times;</button>
                <h2 class="text-lg font-bold mb-4">View Comment</h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">Blog Title</label>
                        <p class="text-sm font-medium text-gray-800 mt-0.5" id="viewBlogTitle"></p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">User Name</label>
                            <p class="text-sm font-medium text-gray-800 mt-0.5" id="viewUserName"></p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">Email</label>
                            <p class="text-sm font-medium text-gray-800 mt-0.5" id="viewEmail"></p>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">Comment</label>
                        <div class="text-sm text-gray-700 bg-gray-50 border border-gray-100 p-3 rounded-lg mt-1 whitespace-pre-wrap" id="viewCommentBody"></div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</label>
                            <span class="inline-block px-2.5 py-1 text-xs font-bold rounded-full mt-1" id="viewStatus"></span>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">Submitted On</label>
                            <p class="text-sm text-gray-600 mt-0.5" id="viewDate"></p>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end gap-2 mt-6">
                    <button type="button" id="closeViewModalBtn"
                        class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-md text-sm font-medium">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            // Select/Deselect All Checkboxes
            $('#selectAll').on('change', function () {
                $('.row-checkbox').prop('checked', this.checked);
                toggleBulkButtons();
            });

            $(document).on('change', '.row-checkbox', function () {
                if ($('.row-checkbox:checked').length === $('.row-checkbox').length) {
                    $('#selectAll').prop('checked', true);
                } else {
                    $('#selectAll').prop('checked', false);
                }
                toggleBulkButtons();
            });

            function toggleBulkButtons() {
                const checkedCount = $('.row-checkbox:checked').length;
                if (checkedCount > 0) {
                    $('.bulk-btn').prop('disabled', false).removeClass('opacity-50 cursor-not-allowed');
                    $('.id-selected-count').text(checkedCount + ' Selected').removeClass('hidden');
                } else {
                    $('.bulk-btn').prop('disabled', true).addClass('opacity-50 cursor-not-allowed');
                    $('.id-selected-count').addClass('hidden');
                }
            }

            // Initialize DataTable
            const table = $('#table').DataTable({
                processing: true,
                serverSide: true,
                scrollX: true,
                responsive: true,
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
                ajax: {
                    url: '{{ route("blogComments.data") }}',
                    type: 'GET',
                    dataType: 'json',
                    error: function (xhr) {
                        const response = xhr.responseJSON;
                        if (response && response.message) {
                            $.toastr.error(response.message);
                        } else {
                            $.toastr.error('Error fetching comments.');
                        }
                    }
                },
                columns: [
                    {
                        data: 'id',
                        name: 'checkbox',
                        orderable: false,
                        searchable: false,
                        render: function (data) {
                            return `<input type="checkbox" class="row-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500" value="${data}">`;
                        }
                    },
                    {
                        data: null,
                        name: 'id',
                        render: function (data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    { data: 'blog_title', name: 'blog.title' },
                    { data: 'name', name: 'name' },
                    { data: 'email', name: 'email' },
                    {
                        data: 'comment',
                        name: 'comment',
                        render: function (data) {
                            return data.length > 50 ? data.substr(0, 50) + '...' : data;
                        }
                    },
                    {
                        data: 'status',
                        name: 'status',
                        render: function (data) {
                            let badgeClass = 'bg-yellow-100 text-yellow-800';
                            if (data === 'Approved') badgeClass = 'bg-green-100 text-green-800';
                            if (data === 'Rejected') badgeClass = 'bg-red-100 text-red-800';
                            return `<span class="px-2.5 py-0.5 text-xs font-bold rounded-full ${badgeClass}">${data}</span>`;
                        }
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        render: function (data) {
                            if (!data) return '-';
                            // Extract just YYYY-MM-DD
                            return data.substring(0, 10);
                        }
                    },
                    {
                        data: 'id',
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        render: function (data, type, row) {
                            let approveBtn = '';
                            let rejectBtn = '';
                            
                            if (row.status !== 'Approved') {
                                approveBtn = `
                                    <button type="button" class="approve-btn px-2 py-1 bg-green-500 hover:bg-green-600 text-white rounded text-xs font-semibold" data-id="${row.id}" title="Approve">
                                        <i class="fas fa-check"></i>
                                    </button>
                                `;
                            }
                            if (row.status !== 'Rejected') {
                                rejectBtn = `
                                    <button type="button" class="reject-btn px-2 py-1 bg-yellow-500 hover:bg-yellow-600 text-white rounded text-xs font-semibold" data-id="${row.id}" title="Reject">
                                        <i class="fas fa-ban"></i>
                                    </button>
                                `;
                            }

                            return `
                            <div class="flex space-x-1.5 justify-center">
                                <button type="button" class="view-btn px-2 py-1 bg-blue-500 hover:bg-blue-600 text-white rounded text-xs font-semibold" 
                                    data-id="${row.id}" 
                                    data-blog="${row.blog_title}" 
                                    data-name="${row.name}" 
                                    data-email="${row.email || 'N/A'}" 
                                    data-comment="${row.comment.replace(/"/g, '&quot;')}" 
                                    data-status="${row.status}" 
                                    data-date="${row.created_at || 'N/A'}" 
                                    title="View">
                                    <i class="fas fa-eye"></i>
                                </button>
                                ${approveBtn}
                                ${rejectBtn}
                                <button type="button" class="delete-btn px-2 py-1 bg-red-500 hover:bg-red-600 text-white rounded text-xs font-semibold" data-id="${row.id}" title="Delete">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                            `;
                        }
                    }
                ]
            });

            // Reload table and clear checkboxes
            function reloadTable() {
                table.ajax.reload(null, false);
                $('#selectAll').prop('checked', false);
                toggleBulkButtons();
            }

            // View modal trigger
            $('#table tbody').on('click', '.view-btn', function () {
                const btn = $(this);
                $('#viewBlogTitle').text(btn.data('blog'));
                $('#viewUserName').text(btn.data('name'));
                $('#viewEmail').text(btn.data('email'));
                $('#viewCommentBody').text(btn.data('comment'));
                $('#viewDate').text(btn.data('date').substring(0, 19).replace('T', ' '));

                const status = btn.data('status');
                let badgeClass = 'bg-yellow-100 text-yellow-800';
                if (status === 'Approved') badgeClass = 'bg-green-100 text-green-800';
                if (status === 'Rejected') badgeClass = 'bg-red-100 text-red-800';

                $('#viewStatus').text(status).removeClass().addClass(`inline-block px-2.5 py-1 text-xs font-bold rounded-full ${badgeClass}`);
                $('#viewModal').removeClass('hidden').addClass('flex');
            });

            $('#closeViewModal, #closeViewModalBtn').on('click', function () {
                $('#viewModal').removeClass('flex').addClass('hidden');
            });

            // Single Actions
            $('#table tbody').on('click', '.approve-btn', function () {
                const id = $(this).data('id');
                $.post(`{{ url('panel/blog-comments') }}/${id}/approve`, { _token: '{{ csrf_token() }}' }, function (res) {
                    if (res.success) {
                        $.toastr.success(res.message);
                        reloadTable();
                    } else {
                        $.toastr.error(res.message);
                    }
                });
            });

            $('#table tbody').on('click', '.reject-btn', function () {
                const id = $(this).data('id');
                $.post(`{{ url('panel/blog-comments') }}/${id}/reject`, { _token: '{{ csrf_token() }}' }, function (res) {
                    if (res.success) {
                        $.toastr.success(res.message);
                        reloadTable();
                    } else {
                        $.toastr.error(res.message);
                    }
                });
            });

            $('#table tbody').on('click', '.delete-btn', function () {
                const id = $(this).data('id');
                Swal.fire({
                    title: 'Confirm Delete',
                    html: '<p class="text-gray-700 text-sm">Are you sure you want to delete this comment?</p>',
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
                            url: `{{ url('panel/blog-comments') }}/${id}`,
                            type: 'POST',
                            data: {
                                _method: 'DELETE',
                                _token: '{{ csrf_token() }}'
                            },
                            success: function (res) {
                                if (res.success) {
                                    $.toastr.success(res.message);
                                    reloadTable();
                                } else {
                                    $.toastr.error(res.message);
                                }
                            }
                        });
                    }
                });
            });

            // Bulk Actions Handler
            function runBulkAction(action, text) {
                const selectedIds = $('.row-checkbox:checked').map(function () {
                    return $(this).val();
                }).get();

                Swal.fire({
                    title: `Bulk ${text}`,
                    html: `<p class="text-gray-700 text-sm">Are you sure you want to ${text.toLowerCase()} the ${selectedIds.length} selected comments?</p>`,
                    icon: 'warning',
                    width: 380,
                    showCancelButton: true,
                    confirmButtonText: text,
                    cancelButtonText: 'Cancel',
                    buttonsStyling: false,
                    customClass: {
                        popup: 'rounded-lg p-4 shadow-md',
                        title: 'text-lg font-semibold text-gray-800',
                        confirmButton: action === 'delete' ? 'bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded mr-2' : 'bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded mr-2',
                        cancelButton: 'bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `{{ route('blogComments.bulkAction') }}`,
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                ids: selectedIds,
                                action: action
                            },
                            success: function (res) {
                                if (res.success) {
                                    $.toastr.success(res.message);
                                    reloadTable();
                                } else {
                                    $.toastr.error(res.message);
                                }
                            }
                        });
                    }
                });
            }

            $('#bulkApproveBtn').on('click', function () {
                runBulkAction('approve', 'Approve');
            });
            $('#bulkRejectBtn').on('click', function () {
                runBulkAction('reject', 'Reject');
            });
            $('#bulkDeleteBtn').on('click', function () {
                runBulkAction('delete', 'Delete');
            });
        });
    </script>
@endsection
