@extends('layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="mb-6">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <h1 class="text-xl font-bold text-gray-900">List of Cars</h1>
                <div class="flex flex-wrap gap-2">
                    <button type="button" id="addModalBtn"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md flex items-center transition-colors">
                        <i class="fas fa-plus mr-2"></i>Add Car
                    </button>
                </div>
            </div>
        </div>
        <div class="bg-white shadow border border-white rounded-lg">
            <div class="p-4">
                <div class="overflow-x-auto">
                    <table id="table" class="min-w-full bg-white text-black text-center border-gray-200">
                        <thead>
                            <tr>
                                <th>SR No.</th>
                                <th>Car Name</th>
                                <th>Car Type</th>
                                <th>Seats</th>
                                <th>AC</th>
                                <th>Photo</th>
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
        <div id="Modal"
            class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 overflow-y-auto">
            <div role="dialog" aria-modal="true" aria-labelledby="modalTitle"
                class="bg-white w-full max-w-3xl mx-auto p-6 rounded-lg shadow-lg relative max-h-[90vh] overflow-y-auto">
                <button type="button" aria-label="Close modal"
                    class="absolute closeUserModal top-4 right-4 text-gray-500 hover:text-gray-700 text-xl focus:outline-none">&times;</button>
                <h2 id="modalTitle" class="text-lg font-bold mb-4"></h2>
                <form id="dataForm" action="{{ route('car.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4 flex space-x-4">
                        <div class="w-1/2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Car Name <span
                                    class="text-black-500">*</span></label>
                            <input type="text" name="car_name" id="car_name"
                                class="w-full border border-gray-300 rounded px-3 py-2" placeholder="Enter car name">
                        </div>
                        <div class="w-1/2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Car Type <span
                                    class="text-black-500">*</span></label>
                            <select name="car_type_id" id="cat_id"
                                class="w-full border select2 border-gray-300 rounded px-3 py-2">
                            </select>
                        </div>
                        <div class="w-1/2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Car Photo</label>
                            <div class="flex items-center space-x-2">
                                <input type="file" name="car_photos" id="icon"
                                    class="w-full border border-gray-300 border-r-0 rounded-l px-3 py-2">
                                <div
                                    class="flex items-center justify-center w-20 h-11 bg-gray-100 rounded-r overflow-hidden">
                                    <img id="iconPreview" src="" class="hidden w-full h-full object-contain"
                                        alt="Car photo preview" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-4 flex space-x-4">
                        <div class="w-1/2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Seats <span
                                    class="text-black-500">*</span></label>
                            <input type="number" name="car_seats" id="car_seats"
                                class="w-full border border-gray-300 rounded px-3 py-2" placeholder="Number of seats">
                        </div>
                        <div class="w-1/2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">AC Available? <span
                                    class="text-black-500">*</span></label>
                            <select name="car_ac" id="car_ac" class="w-full border border-gray-300 rounded px-3 py-2">
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-4 flex space-x-4">
                        <div class="w-1/2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Max Passengers</label>
                            <input type="number" name="max_passengers" id="max_passengers"
                                class="w-full border border-gray-300 rounded px-3 py-2" placeholder="Max passengers">
                        </div>
                        <div class="w-1/2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Max Bags</label>
                            <input type="number" name="max_bags" id="max_bags"
                                class="w-full border border-gray-300 rounded px-3 py-2" placeholder="Max bags">
                        </div>
                    </div>
                    <div class="mb-4 flex space-x-4">
                        <div class="w-1/2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Rating Summary</label>
                            <input type="number" name="rating_summary" id="rating_summary"
                                class="w-full border border-gray-300 rounded px-3 py-2" placeholder="Rating summary">
                        </div>
                        <div class="w-1/2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Rating Value</label>
                            <input type="number" name="rating_value" id="rating_value"
                                class="w-full border border-gray-300 rounded px-3 py-2" placeholder="Rating value">
                        </div>
                        <div class="w-1/2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Rating Count</label>
                            <input type="number" name="rating_count" id="rating_count"
                                class="w-full border border-gray-300 rounded px-3 py-2" placeholder="Rating count">
                        </div>
                    </div>
                    <hr class="my-4">
                    <div class="mb-4">
                        <h3 class="text-sm font-semibold mb-2">
                            Booking Includes
                            <button type="button" id="addBookingInclude"
                                class="text-sm px-3 py-1 float-right bg-blue-500 text-white rounded">
                                + Add Include
                            </button>
                        </h3>
                        <div id="bookingIncludesWrap">
                            <input type="text" name="booking_includes[]"
                                class="w-full mt-2 border border-gray-300 rounded px-3 py-2"
                                placeholder="e.g. Professional driver">
                        </div>
                    </div>
                    <hr class="my-4">
                    <div class="mb-4">
                        <h3 class="text-sm font-semibold mb-2">
                            Why Book With Us
                            <button type="button" id="addWhyBook"
                                class="text-sm px-3 py-1 float-right bg-blue-500 text-white rounded">
                                + Add Reason
                            </button>
                        </h3>
                        <div id="whyBookWrap">
                            <input type="text" name="why_book_us[]"
                                class="w-full mt-2 border border-gray-300 rounded px-3 py-2"
                                placeholder="e.g. Assured on-time pickup">
                        </div>
                    </div>
                    <!-- ================= TRIP POLICIES ================= -->
                    <hr class="my-4">
                    <div class="mb-4">
                        <h3 class="text-sm font-semibold mb-2">
                            Trip Policies
                            <button type="button" id="addTripPolicy"
                                class="text-sm px-3 py-1 float-right bg-blue-500 text-white rounded">
                                + Add Policy
                            </button>
                        </h3>
                        <div id="tripPoliciesWrap">
                            <div class="policy-item border rounded p-3 mb-3 bg-gray-50">
                                <input type="text" name="trip_policies[0][question]"
                                    class="w-full border border-gray-300 rounded px-3 py-2 mb-2" placeholder="Question">
                                <textarea name="trip_policies[0][answer]"
                                    class="w-full border border-gray-300 rounded px-3 py-2" rows="2"
                                    placeholder="Answer"></textarea>
                                <button type="button" class="removeRow text-sm text-red-600 mt-2">
                                    Remove
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- ================= RECENT REVIEWS ================= -->
                    <hr class="my-4">
                    <div class="mb-4">
                        <h3 class="text-sm font-semibold mb-2">
                            Recent Reviews
                            <button type="button" id="addReview"
                                class="text-sm px-3 py-1 float-right bg-blue-500 text-white rounded">
                                + Add Review
                            </button>
                        </h3>
                        <div id="recentReviewsWrap">
                            <div class="review-item border rounded p-3 mb-3 bg-gray-50">
                                <div class="flex space-x-2 mb-2">
                                    <input type="text" name="recent_reviews[0][name]" class="w-1/2 border rounded px-2 py-2"
                                        placeholder="User name">
                                    <input type="number" name="recent_reviews[0][rating]"
                                        class="w-1/4 border rounded px-2 py-2" placeholder="Stars (1–5)">
                                </div>
                                <textarea name="recent_reviews[0][comment]" class="w-full border rounded px-3 py-2" rows="2"
                                    placeholder="Review comment"></textarea>
                                <button type="button" class="removeRow text-sm text-red-600 mt-2">
                                    Remove
                                </button>
                            </div>
                        </div>
                    </div>
                    <hr class="my-4">
                    <div class="mb-4">
                        <h3 class="text-sm font-semibold mb-2">
                            Car Charges
                            <button type="button" id="addCarCharge"
                                class="text-sm px-3 py-1 float-right bg-blue-500 text-white rounded">
                                + Add Charge
                            </button>
                        </h3>
                        <div id="carChargesWrap">
                            <!-- ONE CHARGE CARD -->
                            <div class="border rounded p-4 mb-3 bg-gray-50 car-charge-row">
                                <div class="grid grid-cols-2 gap-4">
                                    <!-- Charge Type -->
                                    <div>
                                        <label class="text-xs font-medium text-gray-600 mb-1 block">
                                            Charge Type
                                        </label>
                                        <select name="car_charges[charges_type_id][]"
                                            class="charges_type_select w-full border border-gray-300 rounded px-3 py-2">
                                        </select>
                                    </div>
                                    <!-- Title -->
                                    <div>
                                        <label class="text-xs font-medium text-gray-600 mb-1 block">
                                            Charge Title
                                        </label>
                                        <input type="text" name="car_charges[title][]"
                                            class="w-full border border-gray-300 rounded px-3 py-2"
                                            placeholder="e.g. Night Charge / Toll Charge">
                                    </div>
                                    <!-- Amount -->
                                    <div>
                                        <label class="text-xs font-medium text-gray-600 mb-1 block">
                                            Amount (₹)
                                        </label>
                                        <input type="number" step="0.01" name="car_charges[amount][]"
                                            class="w-full border border-gray-300 rounded px-3 py-2" placeholder="e.g. 150">
                                    </div>
                                    <!-- Charge Unit -->
                                    <div>
                                        <label class="text-xs font-medium text-gray-600 mb-1 block">
                                            Charge Unit
                                        </label>
                                        <select name="car_charges[charge_unit][]"
                                            class="w-full border border-gray-300 rounded px-3 py-2">
                                            <option value="0">Flat</option>
                                            <option value="1">Per KM</option>
                                            <option value="2">Per Hour</option>
                                        </select>
                                    </div>
                                    <!-- Free Waiting -->
                                    <div>
                                        <label class="text-xs font-medium text-gray-600 mb-1 block">
                                            Free Waiting Minutes
                                        </label>
                                        <input type="number" name="car_charges[free_wait_minutes][]"
                                            class="w-full border border-gray-300 rounded px-3 py-2" placeholder="e.g. 5">
                                    </div>
                                    <!-- Waiting Charge Unit -->
                                    <div>
                                        <label class="text-xs font-medium text-gray-600 mb-1 block">
                                            Waiting Charge Unit
                                        </label>
                                        <select name="car_charges[wait_charge_unit][]"
                                            class="w-full border border-gray-300 rounded px-3 py-2">
                                            <option value="0">Per Minute</option>
                                            <option value="1">Per Hour</option>
                                        </select>
                                    </div>
                                    <!-- Min KM -->
                                    <div>
                                        <label class="text-xs font-medium text-gray-600 mb-1 block">
                                            Minimum KM
                                        </label>
                                        <input type="number" name="car_charges[min_km][]"
                                            class="w-full border border-gray-300 rounded px-3 py-2" placeholder="e.g. 0">
                                    </div>
                                    <div>
                                        <label class="text-xs font-medium text-gray-600 mb-1 block">
                                            Maximum KM
                                        </label>
                                        <input type="number" name="car_charges[max_km][]"
                                            class="w-full border border-gray-300 rounded px-3 py-2" placeholder="e.g. 10">
                                    </div>
                                </div>
                                <button type="button" class="removeCarCharge text-red-500 text-sm mt-3">
                                    Remove Charge
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button"
                            class="px-4 py-2 closeModal bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-md">Cancel</button>
                        <button type="submit"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white addBtn rounded-md">Add Car</button>
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

            function fetchCategories(selectedId = null, callback = null) {
                return $.ajax({
                    url: '{{ route('car.carData') }}',
                    method: 'GET',
                    success: function (response) {
                        let carTypeOptions = '<option value="">Select Car</option>';
                        response.forEach(function (carType) {
                            carTypeOptions += `<option value="${carType.id}">${carType.car_type}</option>`;
                        });
                        $('#cat_id').html(carTypeOptions);

                        if (selectedId) {
                            $('#cat_id').val(selectedId).trigger('change');
                        }

                        if (typeof callback === 'function') callback();
                    },
                    error: function () {
                        $.toastr.error("Something went wrong while loading Car Type.");
                    }
                });
            }


            let chargesTypesCache = null;

            function fetchChargesTypes($target = null, selectedId = null, callback = null) {
                if (chargesTypesCache) {
                    applyChargesOptions(chargesTypesCache, $target, selectedId, callback);
                    return $.Deferred().resolve(chargesTypesCache).promise();
                }
                return $.ajax({
                    url: '{{ route("car.chargesType") }}',
                    method: 'GET',
                    success: function (response) {
                        chargesTypesCache = response;
                        applyChargesOptions(response, $target, selectedId, callback);
                    },
                    error: function () {
                        $.toastr.error('Failed to load Charges Type');
                    }
                });
            }

            function applyChargesOptions(data, $target, selectedId, callback) {
                let options = '<option value="">Select Charge Type</option>';
                data.forEach(function (item) {
                    options += `<option value="${item.id}">${item.charges_type}</option>`;
                });

                // Target can be a jQuery object or null (global)
                const $els = $target || $('.charges_type_select');

                $els.each(function () {
                    const $this = $(this);
                    const currentVal = $this.val(); // Capture current selection

                    $this.html(options);

                    if (selectedId) {
                        $this.val(selectedId).trigger('change');
                    } else if (currentVal) {
                        $this.val(currentVal); // Restore previous selection
                    }
                });

                if (typeof callback === 'function') {
                    callback();
                }
            }

            $('#addModalBtn').on('click', function () {
                $('#Modal h2').text('Add car');
                $('.addBtn').text('Add Car');
                $('#dataForm')[0].reset();
                $('#iconPreview').addClass('hidden');
                isEditing = false;
                editingId = null;
                $('#Modal').removeClass('hidden').addClass('flex');
                // ensure modal scrolled to top and focus first field
                const modalContent = $('#Modal > div[role="dialog"]');
                modalContent.animate({ scrollTop: 0 }, 200);
                $('#cat_id').focus();
                fetchCategories();
                fetchChargesTypes();
            });

            $('.closeModal, .closeUserModal').on('click', function () {
                $('#Modal').removeClass('flex').addClass('hidden');
            });

            $('#Modal').on('click', function (e) {
                if ($(e.target).is('#Modal')) {
                    $(this).removeClass('flex').addClass('hidden');
                }
            });
            const table = $('#table').DataTable({
                processing: true,
                serverSide: true,
                scrollX: true,
                responsive: true,
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
                ajax: {
                    url: '{{ route("car.data") }}',
                    type: 'GET',
                },
                columns: [
                    {
                        data: null,
                        name: 'id',
                        render: function (data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    { data: 'car_name', name: 'car_name' },
                    { data: 'car_type', name: 'car_type' },
                    { data: 'car_seats', name: 'car_seats' },
                    {
                        data: 'is_ac',
                        name: 'is_ac',
                        render: function (data, type, row) {
                            return data == 1 ? 'Yes' : 'No';
                        }
                    },
                    {
                        data: 'car_photos',
                        name: 'car_photos',
                        render: function (data) {
                            return data
                                ? `<img src="{{ asset('images/car') }}/${data}" width="50" height="50" class="mx-auto" />`
                                : 'No Icon';
                        }
                    },
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

            $('#addBookingInclude').on('click', function () {
                $('#bookingIncludesWrap').append(`
                       <div class="flex gap-2 mt-2">
                           <input type="text" name="booking_includes[]"
                               class="w-full border border-gray-300 rounded px-3 py-2"
                               placeholder="Include item">
                           <button type="button" class="removeInclude text-red-500">✕</button>
                       </div>
                   `);
            });

            $(document).on('click', '.removeInclude', function () {
                $(this).parent().remove();
            });

            $('#addWhyBook').on('click', function () {
                $('#whyBookWrap').append(`
                       <div class="flex gap-2 mt-2">
                           <input type="text" name="why_book_us[]"
                               class="w-full border border-gray-300 rounded px-3 py-2"
                               placeholder="Reason">
                           <button type="button" class="removeWhyBook text-red-500">✕</button>
                       </div>
                   `);
            });

            $(document).on('click', '.removeWhyBook', function () {
                $(this).parent().remove();
            });


            let tripPolicyIndex = 1;

            $('#addTripPolicy').on('click', function () {
                $('#tripPoliciesWrap').append(`
                    <div class="policy-item border rounded p-3 mb-3 bg-gray-50">
                        <input type="text"
                            name="trip_policies[${tripPolicyIndex}][question]"
                            class="w-full border border-gray-300 rounded px-3 py-2 mb-2"
                            placeholder="Question">

                        <textarea name="trip_policies[${tripPolicyIndex}][answer]"
                            class="w-full border border-gray-300 rounded px-3 py-2"
                            rows="2"
                            placeholder="Answer"></textarea>

                        <button type="button" class="removeRow text-sm text-red-600 mt-2">
                            Remove
                        </button>
                    </div>
                `);
                tripPolicyIndex++;
            });

            /* ================= RECENT REVIEWS ================= */
            let reviewIndex = 1;

            $('#addReview').on('click', function () {
                $('#recentReviewsWrap').append(`
                    <div class="review-item border rounded p-3 mb-3 bg-gray-50">
                        <div class="flex space-x-2 mb-2">
                            <input type="text"
                                name="recent_reviews[${reviewIndex}][name]"
                                class="w-1/2 border rounded px-2 py-2"
                                placeholder="User name">

                            <input type="number"
                                name="recent_reviews[${reviewIndex}][rating]"
                                class="w-1/4 border rounded px-2 py-2"
                                placeholder="Stars (1–5)">
                        </div>

                        <textarea name="recent_reviews[${reviewIndex}][comment]"
                            class="w-full border rounded px-3 py-2"
                            rows="2"
                            placeholder="Review comment"></textarea>

                        <button type="button" class="removeRow text-sm text-red-600 mt-2">
                            Remove
                        </button>
                    </div>
                `);
                reviewIndex++;
            });

            /* ================= REMOVE ROW ================= */
            $(document).on('click', '.removeRow', function () {
                $(this).closest('.policy-item, .review-item').remove();
            });

            $(document).on('click', '.removeRow', function () {
                $(this).closest('.review-item').remove();
            });


            $('#addCarCharge').on('click', function () {
                const html = `
                   <div class="border rounded p-4 mb-3 bg-gray-50 car-charge-row">
                       <div class="grid grid-cols-2 gap-4">
                           <div>
                               <label class="text-xs font-medium text-gray-600 mb-1 block">Charge Type</label>
                               <select name="car_charges[charges_type_id][]"
                                   class="charges_type_select w-full border rounded px-3 py-2"></select>
                           </div>
                           <div>
                               <label class="text-xs font-medium text-gray-600 mb-1 block">Charge Title</label>
                               <input type="text" name="car_charges[title][]"
                                   class="w-full border rounded px-3 py-2"
                                   placeholder="e.g. Night Charge / Extra KM">
                           </div>
                           <div>
                               <label class="text-xs font-medium text-gray-600 mb-1 block">Amount (₹)</label>
                               <input type="number" step="0.01" name="car_charges[amount][]"
                                   class="w-full border rounded px-3 py-2"
                                   placeholder="e.g. 150">
                           </div>
                           <div>
                               <label class="text-xs font-medium text-gray-600 mb-1 block">Charge Unit</label>
                               <select name="car_charges[charge_unit][]"
                                   class="charge_unit w-full border rounded px-3 py-2">
                                   <option value="0">Flat</option>
                                   <option value="1">Per KM</option>
                                   <option value="2">Per Hour</option>
                               </select>
                           </div>
                           <div>
                               <label class="text-xs font-medium text-gray-600 mb-1 block">Free Wait (min)</label>
                               <input type="number" name="car_charges[free_wait_minutes][]"
                                   class="w-full border rounded px-3 py-2"
                                   placeholder="e.g. 5">
                           </div>
                           <div>
                               <label class="text-xs font-medium text-gray-600 mb-1 block">Wait Charge Unit</label>
                               <select name="car_charges[wait_charge_unit][]"
                                   class="w-full border rounded px-3 py-2">
                                   <option value="0">Per Minute</option>
                                   <option value="1">Per Hour</option>
                               </select>
                           </div>
                           <div>
                               <label class="text-xs font-medium text-gray-600 mb-1 block">Min KM</label>
                               <input type="number" name="car_charges[min_km][]"
                                   class="w-full border rounded px-3 py-2"
                                   placeholder="0">
                           </div>
                           <div>
                               <label class="text-xs font-medium text-gray-600 mb-1 block">Max KM</label>
                               <input type="number" name="car_charges[max_km][]"
                                   class="w-full border rounded px-3 py-2"
                                   placeholder="10">
                           </div>
                       </div>
                       <button type="button"
                           class="removeCarCharge text-red-500 text-sm mt-3">
                           Remove Charge
                       </button>
                   </div>`;
                $('#carChargesWrap').append(html);
                const row = $('#carChargesWrap .car-charge-row').last();
                // load charge types and then set defaults for this row
                fetchChargesTypes(row.find('.charges_type_select')).done(function () {
                    autoSetChargeTitle(row);
                }).fail(function () {
                    // ignore
                });
            });

            function autoSetChargeTitle(row) {
                const unit = row.find('select[name="car_charges[charge_unit][]"]').val();
                const typeText = row.find('.charges_type_select option:selected').text();
                let title = '';
                if (unit == 0) title = typeText + ' Charge';        // Flat
                if (unit == 1) title = 'Extra KM Charge';           // Per KM
                if (unit == 2) title = 'Hourly Charge';             // Per Hour
                const titleInput = row.find('input[name="car_charges[title][]"]');
                // set only if empty (admin can override)
                if (!titleInput.val()) {
                    titleInput.val(title);
                }
            }

            $(document).on('change', '.charge_unit, .charges_type_select', function () {
                const row = $(this).closest('.car-charge-row');
                autoSetChargeTitle(row);
            });



            $(document).on('click', '.removeCarCharge', function () {
                $(this).closest('.car-charge-row').remove();
            });


            $('#dataForm').on('submit', function (e) {
                e.preventDefault();
                const form = this;
                const formData = new FormData(form);
                const url = isEditing ? `{{ route('car.update', ':id') }}`.replace(':id', editingId) : $(form).attr('action');
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
                        $(".addBtn").html(isEditing ? 'Update Car' : 'Add Car').prop('disabled', false);
                    }
                });
            });

            function handleValidationErrors(xhr) {
                $('.error-text').remove();
                $('input, textarea, select').removeClass('border-red-500');
                if (xhr.status === 422 && xhr.responseJSON.errors) {
                    const inserted = new Set();

                    function findInput(field) {
                        field = field.replace(/(\.\w+)\1+/g, '$1');

                        let $el = $(`[name="${field}"]`);
                        if ($el.length) return $el.first();

                        const bracket = field.replace(/\.(\d+|\w+)/g, '[$1]');
                        $el = $(`[name="${bracket}"]`);
                        if ($el.length) return $el.first();

                        let m = field.match(/^([^\.\[]+)\.(\d+)$/);
                        if (m) {
                            let base = m[1], idx = parseInt(m[2], 10);
                            let inputs = $(`[name="${base}[]"]`);
                            if (inputs.length && inputs.eq(idx).length) return inputs.eq(idx);
                            if (inputs.length) return inputs.first();
                        }

                        let m2 = field.match(/^([^\.\[]+)\.([^\.\[]+)\.(\d+)$/);
                        if (m2) {
                            let base = m2[1], sub = m2[2], idx = parseInt(m2[3], 10);
                            let inputs = $(`[name="${base}[${sub}][]"]`);
                            if (inputs.length && inputs.eq(idx).length) return inputs.eq(idx);
                            if (inputs.length) return inputs.first();
                        }
                        const baseName = field.split('.')[0];
                        $el = $(`[name^="${baseName}"]`);
                        if ($el.length) return $el.first();

                        return $();
                    }

                    let firstError = null;
                    $.each(xhr.responseJSON.errors, function (key, messages) {
                        const message = messages && messages.length ? messages[0] : 'Invalid value';
                        const $field = findInput(key);

                        if ($field && $field.length) {
                            const el = $field.get(0);
                            if (!inserted.has(el)) {
                                inserted.add(el);
                                $field.addClass('border-red-500');
                                $('<div class="text-red-500 text-sm error-text mt-1 block" role="alert"></div>').text(message).insertAfter($field);
                                if (!firstError) firstError = $field;
                            }
                        } else {
                            const genKey = 'general:' + message;
                            if (!inserted.has(genKey)) {
                                inserted.add(genKey);
                                $('#dataForm').prepend(`<div class="text-red-500 text-sm error-text mb-2" role="alert">${message}</div>`);
                                if (!firstError) firstError = $('#dataForm');
                            }
                        }

                    });

                    if (firstError) {
                        const modalContent = $('#Modal > div[role="dialog"]');
                        if (modalContent.length && $(firstError).closest('#Modal').length) {
                            const top = $(firstError).offset().top - modalContent.offset().top + modalContent.scrollTop() - 20;
                            modalContent.animate({ scrollTop: top }, 300);
                        } else if (firstError.length && typeof firstError.offset === 'function') {
                            $('html, body').animate({ scrollTop: firstError.offset().top - 120 }, 400);
                        }
                        try { $(firstError).focus(); } catch (e) { }
                    }
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

            $('#table tbody').on('click', '.edit-btn', function () {
                editingId = $(this).data('id');
                isEditing = true;
                $.get(`{{ route('car.edit', ':id') }}`.replace(':id', editingId), function (response) {
                    if (response.success) {
                        const data = response.data;
                        $('#dataForm')[0].reset();
                        function escapeHtml(str) {
                            return $('<div/>').text(str).html();
                        }
                        fetchCategories().done(function () {
                            $('#cat_id').val(data.car_type_id).trigger('change');
                        });
                        $('#car_seats').val(data.car_seats);
                        $('#car_name').val(data.car_name);
                        $('#car_ac').val(data.is_ac);
                        $('#max_passengers').val(data.max_passengers || '');
                        $('#max_bags').val(data.max_bags || '');
                        $('#rating_summary').val(data.rating_summary || '');
                        $('#rating_value').val(data.rating_value || '');
                        $('#rating_count').val(data.rating_count || '');

                        if (data.car_photos) {
                            $('#iconPreview')
                                .attr('src', `{{ asset('storage/app/public/images/car') }}/${data.car_photos}`)
                                .removeClass('hidden');
                        } else {
                            $('#iconPreview').addClass('hidden');
                        }
                        $('#bookingIncludesWrap').empty();
                        if (data.booking_includes && data.booking_includes.length) {
                            data.booking_includes.forEach(function (item) {
                                $('#bookingIncludesWrap').append(`
                                       <div class="flex gap-2 mt-2">
                                           <input type="text" name="booking_includes[]" class="w-full border border-gray-300 rounded px-3 py-2" value="${escapeHtml(item)}">
                                           <button type="button" class="removeInclude text-red-500">✕</button>
                                       </div>
                                   `);
                            });
                        } else {
                            $('#bookingIncludesWrap').append(`<input type="text" name="booking_includes[]" class="w-full mt-2 border border-gray-300 rounded px-3 py-2" placeholder="e.g. Professional driver">`);
                        }
                        $('#whyBookWrap').empty();
                        if (data.why_book_us && data.why_book_us.length) {
                            data.why_book_us.forEach(function (item) {
                                $('#whyBookWrap').append(`
                                       <div class="flex gap-2 mt-2">
                                           <input type="text" name="why_book_us[]" class="w-full border border-gray-300 rounded px-3 py-2" value="${escapeHtml(item)}">
                                           <button type="button" class="removeWhyBook text-red-500">✕</button>
                                       </div>
                                   `);
                            });
                        } else {
                            $('#whyBookWrap').append(`<input type="text" name="why_book_us[]" class="w-full mt-2 border border-gray-300 rounded px-3 py-2" placeholder="e.g. Assured on-time pickup">`);
                        }
                        $('#tripPoliciesWrap').empty();
                        let localTripIndex = 0;
                        if (data.trip_policies && data.trip_policies.length) {
                            data.trip_policies.forEach(function (policy, idx) {
                                $('#tripPoliciesWrap').append(`
                                       <div class="policy-item border rounded p-3 mb-3 bg-gray-50">
                                           <input type="text" name="trip_policies[${idx}][question]" class="w-full border border-gray-300 rounded px-3 py-2 mb-2" placeholder="Question" value="${escapeHtml(policy.question)}">
                                           <textarea name="trip_policies[${idx}][answer]" class="w-full border border-gray-300 rounded px-3 py-2" rows="2" placeholder="Answer">${escapeHtml(policy.answer)}</textarea>
                                           <button type="button" class="removeRow text-sm text-red-600 mt-2">Remove</button>
                                       </div>
                                   `);
                                localTripIndex = idx + 1;
                            });
                        } else {
                            $('#tripPoliciesWrap').append(`
                                   <div class="policy-item border rounded p-3 mb-3 bg-gray-50">
                                       <input type="text" name="trip_policies[0][question]" class="w-full border border-gray-300 rounded px-3 py-2 mb-2" placeholder="Question">
                                       <textarea name="trip_policies[0][answer]" class="w-full border border-gray-300 rounded px-3 py-2" rows="2" placeholder="Answer"></textarea>
                                       <button type="button" class="removeRow text-sm text-red-600 mt-2">Remove</button>
                                   </div>
                               `);
                            localTripIndex = 1;
                        }
                        tripPolicyIndex = localTripIndex;
                        $('#recentReviewsWrap').empty();
                        let localReviewIndex = 0;
                        if (data.recent_reviews && data.recent_reviews.length) {
                            data.recent_reviews.forEach(function (rev, idx) {
                                $('#recentReviewsWrap').append(`
                                       <div class="review-item border rounded p-3 mb-3 bg-gray-50">
                                           <div class="flex space-x-2 mb-2">
                                               <input type="text" name="recent_reviews[${idx}][name]" class="w-1/2 border rounded px-2 py-2" placeholder="User name" value="${escapeHtml(rev.name)}">
                                               <input type="number" name="recent_reviews[${idx}][rating]" class="w-1/4 border rounded px-2 py-2" placeholder="Stars (1–5)" value="${escapeHtml(rev.rating)}">
                                           </div>
                                           <textarea name="recent_reviews[${idx}][comment]" class="w-full border rounded px-3 py-2" rows="2" placeholder="Review comment">${escapeHtml(rev.comment)}</textarea>
                                           <button type="button" class="removeRow text-sm text-red-600 mt-2">Remove</button>
                                       </div>
                                   `);
                                localReviewIndex = idx + 1;
                            });
                        } else {
                            $('#recentReviewsWrap').append(`
                                   <div class="review-item border rounded p-3 mb-3 bg-gray-50">
                                       <div class="flex space-x-2 mb-2">
                                           <input type="text" name="recent_reviews[0][name]" class="w-1/2 border rounded px-2 py-2" placeholder="User name">
                                           <input type="number" name="recent_reviews[0][rating]" class="w-1/4 border rounded px-2 py-2" placeholder="Stars (1–5)">
                                       </div>
                                       <textarea name="recent_reviews[0][comment]" class="w-full border rounded px-3 py-2" rows="2" placeholder="Review comment"></textarea>
                                       <button type="button" class="removeRow text-sm text-red-600 mt-2">Remove</button>
                                   </div>
                               `);
                            localReviewIndex = 1;
                        }
                        reviewIndex = localReviewIndex;
                        $('#carChargesWrap').empty();
                        if (data.charges && data.charges.length) {
                            data.charges.forEach(function (charge) {
                                const html = `
                                       <div class="border rounded p-4 mb-3 bg-gray-50 car-charge-row">
                                           <div class="grid grid-cols-2 gap-4">
                                               <div>
                                                   <label class="text-xs font-medium text-gray-600 mb-1 block">Charge Type</label>
                                                   <select name="car_charges[charges_type_id][]" class="charges_type_select w-full border border-gray-300 rounded px-3 py-2"></select>
                                               </div>
                                               <div>
                                                   <label class="text-xs font-medium text-gray-600 mb-1 block">Charge Title</label>
                                                   <input type="text" name="car_charges[title][]" class="w-full border border-gray-300 rounded px-3 py-2" placeholder="e.g. Night Charge / Toll Charge" value="${escapeHtml(charge.title)}">
                                               </div>
                                               <div>
                                                   <label class="text-xs font-medium text-gray-600 mb-1 block">Amount (₹)</label>
                                                   <input type="number" step="0.01" name="car_charges[amount][]" class="w-full border border-gray-300 rounded px-3 py-2" placeholder="e.g. 150" value="${escapeHtml(charge.amount)}">
                                               </div>
                                               <div>
                                                   <label class="text-xs font-medium text-gray-600 mb-1 block">Charge Unit</label>
                                                   <select name="car_charges[charge_unit][]" class="w-full border border-gray-300 rounded px-3 py-2">
                                                       <option value="0">Flat</option>
                                                       <option value="1">Per KM</option>
                                                       <option value="2">Per Hour</option>
                                                   </select>
                                               </div>
                                               <div>
                                                   <label class="text-xs font-medium text-gray-600 mb-1 block">Free Waiting Minutes</label>
                                                   <input type="number" name="car_charges[free_wait_minutes][]" class="w-full border border-gray-300 rounded px-3 py-2" placeholder="e.g. 5" value="${escapeHtml(charge.free_wait_minutes)}">
                                               </div>
                                               <div>
                                                   <label class="text-xs font-medium text-gray-600 mb-1 block">Waiting Charge Unit</label>
                                                   <select name="car_charges[wait_charge_unit][]" class="w-full border border-gray-300 rounded px-3 py-2">
                                                       <option value="0">Per Minute</option>
                                                       <option value="1">Per Hour</option>
                                                   </select>
                                               </div>
                                               <div>
                                                   <label class="text-xs font-medium text-gray-600 mb-1 block">Minimum KM</label>
                                                   <input type="number" name="car_charges[min_km][]" class="w-full border border-gray-300 rounded px-3 py-2" placeholder="e.g. 0" value="${escapeHtml(charge.min_km)}">
                                               </div>
                                               <div>
                                                   <label class="text-xs font-medium text-gray-600 mb-1 block">Maximum KM</label>
                                                   <input type="number" name="car_charges[max_km][]" class="w-full border border-gray-300 rounded px-3 py-2" placeholder="e.g. 10" value="${escapeHtml(charge.max_km)}">
                                               </div>
                                           </div>
                                           <button type="button" class="removeCarCharge text-red-500 text-sm mt-3">Remove Charge</button>
                                       </div>`;
                                $('#carChargesWrap').append(html);
                            });
                            fetchChargesTypes().done(function () {
                                $('#carChargesWrap .car-charge-row').each(function (index) {
                                    const charge = data.charges[index];
                                    const $row = $(this);
                                    $row.find('.charges_type_select').val(charge.charges_type_id).trigger('change');
                                    $row.find('select[name="car_charges[charge_unit][]"]').val(charge.charge_unit).trigger('change');
                                    $row.find('select[name="car_charges[wait_charge_unit][]"]').val(charge.wait_charge_unit).trigger('change');
                                    autoSetChargeTitle($row);
                                });
                            }).fail(function () {
                                $('#carChargesWrap .car-charge-row').each(function (index) {
                                    const charge = data.charges[index];
                                    const $row = $(this);
                                    $row.find('.charges_type_select').val(charge.charges_type_id);
                                    $row.find('select[name="car_charges[charge_unit][]"]').val(charge.charge_unit);
                                    $row.find('select[name="car_charges[wait_charge_unit][]"]').val(charge.wait_charge_unit);
                                    autoSetChargeTitle($row);
                                });
                            });
                        } else {
                            $('#addCarCharge').trigger('click');
                        }

                        $('#Modal h2').text('Edit Car');
                        $('.addBtn').text('Update Car');
                        $('#Modal').removeClass('hidden').addClass('flex');
                    } else {
                        $.toastr.error("Failed to fetch car data.");
                    }
                }).fail(function () {
                    $.toastr.error("Something went wrong while fetching the car.");
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
                            url: `car/${id}/changeStatus`,
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

            $('#table tbody').on('click', '.delete-btn', function () {
                const id = $(this).data('id');
                Swal.fire({
                    title: 'Confirm Delete',
                    html: '<p class="text-gray-700 text-sm">Are you sure you want to delete this car? This action cannot be undone.</p>',
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
                            url: `{{ url('car') }}/${id}`,
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
        });
    </script>
@endsection