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
                                    <span class="text-gray-900 font-medium">All SEO Pages</span>
                                </div>
                            </li>
                        </ol>
                    </nav>
                    <h1 class="text-xl font-bold text-gray-900">SEO Landing Pages</h1>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('seoPages.create') }}"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md flex items-center transition shadow font-semibold text-sm">
                        <i class="fas fa-plus mr-2"></i>Add SEO Page
                    </a>
                </div>
            </div>
        </div>

        <!-- Filters Block -->
        <div class="bg-white shadow-sm border border-gray-100 rounded-xl p-5 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Service Category Filter -->
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Service Category</label>
                    <select id="filterCategory" class="w-full border border-gray-300 rounded px-3 py-2 text-xs focus:ring-1 focus:ring-blue-500">
                        <option value="">All Categories</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->category_name }}</option>
                        @endforeach
                    </select>
                </div>
                <!-- State Filter -->
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">State</label>
                    <select id="filterState" class="w-full border border-gray-300 rounded px-3 py-2 text-xs focus:ring-1 focus:ring-blue-500">
                        <option value="">All States</option>
                        @foreach ($states as $st)
                            <option value="{{ $st->id }}">{{ $st->state_name }}</option>
                        @endforeach
                    </select>
                </div>
                <!-- City Filter -->
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">City</label>
                    <select id="filterCity" class="w-full border border-gray-300 rounded px-3 py-2 text-xs focus:ring-1 focus:ring-blue-500">
                        <option value="">All Cities</option>
                        @foreach ($cities as $city)
                            <option value="{{ $city->id }}">{{ $city->city_name }}</option>
                        @endforeach
                    </select>
                </div>
                <!-- Status Filter -->
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Status</label>
                    <select id="filterStatus" class="w-full border border-gray-300 rounded px-3 py-2 text-xs focus:ring-1 focus:ring-blue-500">
                        <option value="">All Statuses</option>
                        <option value="Published">Published</option>
                        <option value="Draft">Draft</option>
                    </select>
                </div>
            </div>
            <div class="flex justify-end gap-2 mt-4">
                <button type="button" id="resetFilters" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded transition">Reset</button>
                <button type="button" id="applyFilters" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded transition">Apply Filters</button>
            </div>
        </div>

        <!-- DataTable -->
        <div class="bg-white shadow border border-gray-100 rounded-lg">
            <div class="p-4 border-b border-gray-100 flex flex-wrap gap-2 items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-semibold text-gray-500 hidden id-selected-count bg-gray-100 px-2 py-1 rounded">0 Selected</span>
                </div>
                <div class="flex flex-wrap gap-2 items-center">
                    <button type="button" id="bulkPublishBtn" disabled class="bulk-btn px-2.5 py-1.5 bg-green-600 hover:bg-green-700 text-white rounded text-xs font-semibold opacity-50 cursor-not-allowed transition shadow-sm">Bulk Publish</button>
                    <button type="button" id="bulkDraftBtn" disabled class="bulk-btn px-2.5 py-1.5 bg-yellow-500 hover:bg-yellow-600 text-white rounded text-xs font-semibold opacity-50 cursor-not-allowed transition shadow-sm">Bulk Draft</button>
                    <button type="button" id="bulkFeatureBtn" disabled class="bulk-btn px-2.5 py-1.5 bg-purple-600 hover:bg-purple-700 text-white rounded text-xs font-semibold opacity-50 cursor-not-allowed transition shadow-sm">Bulk Feature</button>
                    <button type="button" id="bulkUnfeatureBtn" disabled class="bulk-btn px-2.5 py-1.5 bg-gray-600 hover:bg-gray-700 text-white rounded text-xs font-semibold opacity-50 cursor-not-allowed transition shadow-sm">Bulk Unfeature</button>
                    <button type="button" id="bulkDeleteBtn" disabled class="bulk-btn px-2.5 py-1.5 bg-red-600 hover:bg-red-700 text-white rounded text-xs font-semibold opacity-50 cursor-not-allowed transition shadow-sm">Bulk Delete</button>
                </div>
            </div>
            <div class="p-4">
                <div class="overflow-x-auto">
                    <table id="table" class="min-w-full bg-white text-black border-gray-200 text-center">
                        <thead>
                            <tr>
                                <th>
                                    <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                </th>
                                <th>SR No.</th>
                                <th>Image</th>
                                <th>Title</th>
                                <th>Service Category</th>
                                <th>State</th>
                                <th>City</th>
                                <th>Route</th>
                                <th>Views</th>
                                <th>Status</th>
                                <th>Featured</th>
                                <th>Published Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            // State-city dynamic linkage inside filters
            $('#filterState').on('change', function() {
                const stateId = $(this).val();
                $('#filterCity').html('<option value="">All Cities</option>');
                if (stateId) {
                    $.get(`{{ url('panel/seo-cities/by-state') }}/${stateId}`, function(cities) {
                        $.each(cities, function(i, city) {
                            $('#filterCity').append(`<option value="${city.id}">${city.city_name}</option>`);
                        });
                    });
                }
            });

            // Select/Deselect checkboxes
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
                const count = $('.row-checkbox:checked').length;
                if (count > 0) {
                    $('.bulk-btn').prop('disabled', false).removeClass('opacity-50 cursor-not-allowed');
                    $('.id-selected-count').text(count + ' Selected').removeClass('hidden');
                } else {
                    $('.bulk-btn').prop('disabled', true).addClass('opacity-50 cursor-not-allowed');
                    $('.id-selected-count').addClass('hidden');
                }
            }

            // DataTable init
            const table = $('#table').DataTable({
                processing: true,
                serverSide: true,
                scrollX: true,
                responsive: true,
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
                ajax: {
                    url: '{{ route("seoPages.data") }}',
                    type: 'GET',
                    data: function (d) {
                        d.category_id = $('#filterCategory').val();
                        d.state_id = $('#filterState').val();
                        d.city_id = $('#filterCity').val();
                        d.status = $('#filterStatus').val();
                    },
                    dataType: 'json',
                    error: function (xhr) {
                        $.toastr.error('Error fetching SEO Pages.');
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
                    {
                        data: 'featured_image',
                        name: 'featured_image',
                        orderable: false,
                        searchable: false,
                        render: function (data) {
                            return data
                                ? `<img src="{{ asset('images/seo/featured') }}/${data}" class="w-10 h-10 object-cover rounded shadow-sm mx-auto" />`
                                : '<div class="w-10 h-10 bg-gray-100 text-gray-400 text-[9px] flex items-center justify-center rounded mx-auto">No Cover</div>';
                        }
                    },
                    {
                        data: 'title',
                        name: 'title',
                        render: function (data, type, row) {
                            return `<div class="text-left font-semibold max-w-xs truncate" title="${data}">${data}</div>`;
                        }
                    },
                    { data: 'category_name', name: 'category.category_name' },
                    { data: 'state_name', name: 'state.state_name' },
                    { data: 'city_name', name: 'city.city_name' },
                    { data: 'route_name', name: 'route.id', defaultContent: '-' },
                    { data: 'view_count', name: 'view_count', defaultContent: 0 },
                    {
                        data: 'status',
                        name: 'status',
                        render: function (data, type, row) {
                            let badgeClass = 'bg-yellow-100 text-yellow-800';
                            if (data === 'Published') badgeClass = 'bg-green-100 text-green-800';
                            return `
                                <button class="change-status-btn px-2.5 py-0.5 rounded-full text-[10px] font-bold ${badgeClass}" 
                                    data-id="${row.id}" data-status="${row.status}">
                                    ${data}
                                </button>
                            `;
                        }
                    },
                    {
                        data: 'featured',
                        name: 'featured',
                        render: function (data, type, row) {
                            const isFeat = parseInt(row.featured) === 1;
                            const label = isFeat ? 'Featured' : 'Regular';
                            const badge = isFeat ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-600';
                            return `
                                <button class="change-featured-btn px-2.5 py-0.5 rounded-full text-[10px] font-bold ${badge}" 
                                    data-id="${row.id}" data-featured="${row.featured}">
                                    ${label}
                                </button>
                            `;
                        }
                    },
                    { data: 'formatted_publish_date', name: 'published_at' },
                    {
                        data: 'id',
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        render: function (data, type, row) {
                            return `
                            <div class="flex space-x-1.5 justify-center">
                                <a href="{{ url('panel/seo') }}/${row.id}" class="px-2 py-1 bg-gray-500 hover:bg-gray-600 text-white rounded text-xs font-semibold transition" title="View Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ url('panel/seo') }}/${row.id}/edit" class="px-2 py-1 bg-blue-500 hover:bg-blue-600 text-white rounded text-xs font-semibold transition" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="duplicate-btn px-2 py-1 bg-yellow-500 hover:bg-yellow-600 text-white rounded text-xs font-semibold transition" data-id="${row.id}" title="Duplicate">
                                    <i class="fas fa-clone"></i>
                                </button>
                                <button type="button" class="delete-btn px-2 py-1 bg-red-500 hover:bg-red-600 text-white rounded text-xs font-semibold transition" data-id="${row.id}" title="Delete">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                            `;
                        }
                    }
                ]
            });

            function reloadTable() {
                table.ajax.reload(null, false);
                $('#selectAll').prop('checked', false);
                toggleBulkButtons();
            }

            // Apply filter
            $('#applyFilters').on('click', function () {
                reloadTable();
            });

            // Reset filters
            $('#resetFilters').on('click', function () {
                $('#filterCategory').val('');
                $('#filterState').val('').trigger('change');
                $('#filterCity').val('');
                $('#filterStatus').val('');
                reloadTable();
            });

            // Toggle Featured Click
            $('#table tbody').on('click', '.change-featured-btn', function () {
                const btn = $(this);
                const id = btn.data('id');
                const curr = parseInt(btn.data('featured'));
                const next = curr === 1 ? 0 : 1;

                $.post(`{{ url('panel/seo') }}/${id}/changeFeatured`, {
                    _token: '{{ csrf_token() }}',
                    featured: next
                }, function (res) {
                    if (res.success) {
                        $.toastr.success(res.message);
                        reloadTable();
                    } else {
                        $.toastr.error(res.message);
                    }
                });
            });

            // Toggle Status Click
            $('#table tbody').on('click', '.change-status-btn', function () {
                const btn = $(this);
                const id = btn.data('id');
                const curr = btn.data('status');
                const next = curr === 'Published' ? 'Draft' : 'Published';

                $.post(`{{ url('panel/seo') }}/${id}/changeStatus`, {
                    _token: '{{ csrf_token() }}',
                    status: next
                }, function (res) {
                    if (res.success) {
                        $.toastr.success(res.message);
                        reloadTable();
                    } else {
                        $.toastr.error(res.message);
                    }
                });
            });

            // Duplicate trigger
            $('#table tbody').on('click', '.duplicate-btn', function () {
                const id = $(this).data('id');
                $.post(`{{ url('panel/seo') }}/${id}/duplicate`, { _token: '{{ csrf_token() }}' }, function (res) {
                    if (res.success) {
                        $.toastr.success(res.message);
                        reloadTable();
                    } else {
                        $.toastr.error(res.message);
                    }
                });
            });

            // Delete trigger
            $('#table tbody').on('click', '.delete-btn', function () {
                const id = $(this).data('id');
                Swal.fire({
                    title: 'Confirm Delete',
                    html: '<p class="text-gray-700 text-sm">Are you sure you want to delete this SEO landing page?</p>',
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
                            url: `{{ url('panel/seo') }}/${id}`,
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

            // Bulk actions
            function runBulkAction(action, text) {
                const selectedIds = $('.row-checkbox:checked').map(function () {
                    return $(this).val();
                }).get();

                Swal.fire({
                    title: `Bulk ${text}`,
                    html: `<p class="text-gray-700 text-sm">Are you sure you want to execute bulk ${text.toLowerCase()} on the ${selectedIds.length} selected pages?</p>`,
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
                            url: `{{ route('seoPages.bulkAction') }}`,
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

            $('#bulkPublishBtn').on('click', function () { runBulkAction('publish', 'Publish'); });
            $('#bulkDraftBtn').on('click', function () { runBulkAction('draft', 'Draft'); });
            $('#bulkFeatureBtn').on('click', function () { runBulkAction('feature', 'Feature'); });
            $('#bulkUnfeatureBtn').on('click', function () { runBulkAction('unfeature', 'Unfeature'); });
            $('#bulkDeleteBtn').on('click', function () { runBulkAction('delete', 'Delete'); });
        });
    </script>
@endsection
