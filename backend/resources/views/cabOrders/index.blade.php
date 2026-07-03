@extends('layouts.app')

@section('content')
<div class="container mx-auto py-6">
    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-200 text-green-800 rounded-lg flex items-center gap-2 shadow-sm">
            <i class="fas fa-check-circle text-lg"></i>
            <div class="font-medium">{{ session('success') }}</div>
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 p-4 bg-red-100 border border-red-200 text-red-800 rounded-lg flex items-center gap-2 shadow-sm">
            <i class="fas fa-times-circle text-lg"></i>
            <div class="font-medium">{{ session('error') }}</div>
        </div>
    @endif

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Cab Bookings</h1>
        <a href="{{ route('cabOrders.create') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-lg shadow-sm hover:shadow font-semibold transition-all duration-150">
            <i class="fas fa-plus"></i>
            Manual Booking
        </a>
    </div>

    <div class="bg-white shadow rounded-lg overflow-hidden border border-gray-200">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 font-semibold text-gray-700">Order Number</th>
                        <th class="px-6 py-4 font-semibold text-gray-700">Customer Details</th>
                        <th class="px-6 py-4 font-semibold text-gray-700">Pickup Date/Time</th>
                        <th class="px-6 py-4 font-semibold text-gray-700">Trip Type</th>
                        <th class="px-6 py-4 font-semibold text-gray-700">Amount</th>
                        @if(!auth()->user()->isVendor())
                            <th class="px-6 py-4 font-semibold text-gray-700">Vendor</th>
                        @endif
                        <th class="px-6 py-4 font-semibold text-gray-700">Status</th>
                        <th class="px-6 py-4 font-semibold text-gray-700 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($orders as $order)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-medium text-blue-600">
                                {{ $order->order_number }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-800">{{ $order->customer_name }}</div>
                                <div class="text-xs text-gray-500">{{ $order->customer_mobile }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div>{{ $order->pickup_date ? \Carbon\Carbon::parse($order->pickup_date)->format('d M, Y') : '-' }}</div>
                                <div class="text-xs text-gray-500">{{ $order->pickup_time ? (strpos($order->pickup_time, ' to ') !== false ? $order->pickup_time : \Carbon\Carbon::parse($order->pickup_time)->format('h:i A')) : '-' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="capitalize">{{ str_replace('_', ' ', $order->trip_type) }}</div>
                                <div class="text-xs text-gray-500">{{ $order->total_km }} km</div>
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-900">
                                ₹{{ number_format($order->total_amount, 2) }}
                            </td>
                            @if(!auth()->user()->isVendor())
                                <td class="px-6 py-4">
                                    @if($order->vendor)
                                        <button type="button" class="view-vendor-btn font-semibold text-blue-600 hover:text-blue-800 hover:underline text-left flex items-center gap-2 focus:outline-none" data-id="{{ $order->vendor->id }}">
                                            @if($order->vendor->company_logo)
                                                <img src="{{ asset('images/' . $order->vendor->company_logo) }}" alt="Logo" class="w-6 h-6 rounded-full object-cover border border-gray-200">
                                            @else
                                                <div class="w-6 h-6 rounded-full bg-slate-100 flex items-center justify-center border border-gray-200 text-[10px] text-slate-500 font-bold">
                                                    {{ substr($order->vendor->name, 0, 1) }}
                                                </div>
                                            @endif
                                            <span>{{ $order->vendor->name }}</span>
                                        </button>
                                    @else
                                        <span class="text-gray-400 text-xs font-semibold flex items-center gap-1.5">
                                            <i class="fas fa-user-shield text-slate-300"></i>
                                            SBD Admin
                                        </span>
                                    @endif
                                </td>
                            @endif
                            <td class="px-6 py-4">
                                @if($order->booking_status === 'pending')
                                    <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-semibold">Pending</span>
                                @elseif($order->booking_status === 'confirmed')
                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">Confirmed</span>
                                @elseif($order->booking_status === 'cancelled')
                                    <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-semibold">Cancelled</span>
                                @else
                                    <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-semibold capitalize">{{ str_replace('_', ' ', $order->booking_status) }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('cabOrders.show', $order->id) }}" class="inline-flex items-center justify-center p-2 text-blue-600 hover:text-white hover:bg-blue-600 rounded transition border border-transparent hover:border-blue-600" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->isVendor() ? 7 : 8 }}" class="px-6 py-8 text-center text-gray-500">
                                <i class="fas fa-box-open text-3xl mb-3 text-gray-300 block"></i>
                                No cab bookings found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($orders->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $orders->links() }}
        </div>
        @endif
    </div>
</div>

<!-- VENDOR DETAILS MODAL -->
<div id="vendorDetailsModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden items-center justify-center z-50 transition-opacity duration-300">
    <div class="bg-white w-full max-w-2xl mx-auto rounded-2xl shadow-2xl relative max-h-[90vh] overflow-y-auto transform scale-95 transition-transform duration-300 flex flex-col">
        <!-- Close Button -->
        <button type="button" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 text-2xl closeVendorModalBtn focus:outline-none">&times;</button>
        
        <!-- Modal Body -->
        <div class="p-8 flex-1">
            <!-- Header section (Photo and Company Logo) -->
            <div class="flex flex-col sm:flex-row items-center gap-6 border-b border-gray-100 pb-6 mb-6">
                <div class="relative">
                    <div class="w-24 h-24 rounded-2xl overflow-hidden bg-slate-50 border-2 border-slate-100 shadow flex items-center justify-center">
                        <img id="vendorPhotoImg" src="" alt="Vendor Photo" class="w-full h-full object-cover">
                    </div>
                    <!-- Small absolute company logo overlay -->
                    <div class="absolute -bottom-2 -right-2 w-10 h-10 rounded-xl overflow-hidden bg-white border border-slate-100 shadow flex items-center justify-center p-0.5">
                        <img id="vendorCompanyLogoImg" src="" alt="Company Logo" class="w-full h-full object-cover rounded-lg">
                    </div>
                </div>
                
                <div class="text-center sm:text-left flex-1">
                    <div class="flex flex-wrap items-center justify-center sm:justify-start gap-3">
                        <h2 id="vendorNameVal" class="text-xl font-bold text-gray-900">Name</h2>
                        <span id="vendorStatusBadge" class="px-2.5 py-0.5 rounded-full text-[10px] font-black tracking-wider uppercase border">STATUS</span>
                    </div>
                    <p class="text-xs text-gray-400 mt-1 flex flex-wrap justify-center sm:justify-start items-center gap-2">
                        <span><i class="fas fa-envelope text-[#d84e55] mr-1"></i> <span id="vendorEmailVal">Email</span></span>
                    </p>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Contact Details -->
                <div class="bg-slate-50/50 border border-slate-100 p-4 rounded-xl">
                    <h3 class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-3 flex items-center gap-2">
                        <i class="fas fa-phone-alt text-[#d84e55]"></i> Contact Info
                    </h3>
                    <div class="space-y-2 text-xs">
                        <div class="flex justify-between"><span class="text-gray-400">Mobile:</span> <strong id="vendorMobileVal" class="text-gray-800">N/A</strong></div>
                        <div class="flex justify-between"><span class="text-gray-400">Alt Mobile:</span> <strong id="vendorAltMobileVal" class="text-gray-800">N/A</strong></div>
                    </div>
                </div>
                
                <!-- Commission Info -->
                <div class="bg-slate-50/50 border border-slate-100 p-4 rounded-xl">
                    <h3 class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-3 flex items-center gap-2">
                        <i class="fas fa-calculator text-[#d84e55]"></i> Commission Settings
                    </h3>
                    <div class="space-y-2 text-xs">
                        <div class="flex justify-between"><span class="text-gray-400">Commission Type:</span> <strong id="vendorCommTypeVal" class="text-gray-800">N/A</strong></div>
                        <div class="flex justify-between"><span class="text-gray-400">Commission Rate:</span> <strong id="vendorCommRateVal" class="text-red-500">N/A</strong></div>
                    </div>
                </div>
                
                <!-- Address Details -->
                <div class="bg-slate-50/50 border border-slate-100 p-4 rounded-xl md:col-span-2">
                    <h3 class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-3 flex items-center gap-2">
                        <i class="fas fa-map-marker-alt text-[#d84e55]"></i> Address Details
                    </h3>
                    <p id="vendorAddressVal" class="text-xs text-gray-700 leading-relaxed">N/A</p>
                </div>
                
                <!-- Verification Documents -->
                <div class="bg-slate-50/50 border border-slate-100 p-4 rounded-xl md:col-span-2">
                    <h3 class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-3 flex items-center gap-2">
                        <i class="fas fa-shield-alt text-[#d84e55]"></i> Verification Documents
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs mt-2">
                        <div class="border border-slate-200 bg-white p-3 rounded-lg flex flex-col justify-between gap-2">
                            <div>
                                <span class="text-gray-400 block text-[10px] uppercase font-bold">Aadhaar Card</span>
                                <span id="vendorAadhaarNoVal" class="font-bold text-gray-800">N/A</span>
                            </div>
                            <a id="vendorAadhaarLinkVal" href="#" target="_blank" class="text-[11px] font-bold text-blue-600 hover:underline flex items-center gap-1">
                                <i class="fas fa-external-link-alt text-[9px]"></i> View Document
                            </a>
                        </div>
                        
                        <div class="border border-slate-200 bg-white p-3 rounded-lg flex flex-col justify-between gap-2">
                            <div>
                                <span class="text-gray-400 block text-[10px] uppercase font-bold">PAN Card</span>
                                <span id="vendorPanNoVal" class="font-bold text-gray-800">N/A</span>
                            </div>
                            <a id="vendorPanLinkVal" href="#" target="_blank" class="text-[11px] font-bold text-blue-600 hover:underline flex items-center gap-1">
                                <i class="fas fa-external-link-alt text-[9px]"></i> View Document
                            </a>
                        </div>
                        
                        <div class="border border-slate-200 bg-white p-3 rounded-lg flex flex-col justify-between gap-2">
                            <div>
                                <span class="text-gray-400 block text-[10px] uppercase font-bold">GSTIN Details</span>
                                <span id="vendorGstNoVal" class="font-bold text-gray-800">N/A</span>
                            </div>
                            <span class="text-[10px] text-gray-400">Registered</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="bg-slate-50 border-t border-slate-100 px-8 py-4 flex justify-end gap-2 rounded-b-2xl">
            <button type="button" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-xs font-bold rounded-lg closeVendorModalBtn transition">Close</button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    const modal = $('#vendorDetailsModal');
    
    // Close Modal actions
    $('.closeVendorModalBtn').on('click', function() {
        modal.removeClass('flex').addClass('hidden');
    });
    
    $(window).on('click', function(e) {
        if ($(e.target).is('#vendorDetailsModal')) {
            modal.removeClass('flex').addClass('hidden');
        }
    });
    
    // Open Modal and load details
    $('.view-vendor-btn').on('click', function() {
        const vendorId = $(this).data('id');
        const fetchUrl = "{{ route('vendors.show', ':id') }}".replace(':id', vendorId);
        
        $.ajax({
            url: fetchUrl,
            method: 'GET',
            success: function(response) {
                if (response.success && response.data) {
                    const vendor = response.data;
                    
                    // Set textual values
                    $('#vendorNameVal').text(vendor.name || 'N/A');
                    $('#vendorEmailVal').text(vendor.email || 'N/A');
                    $('#vendorMobileVal').text(vendor.mobile || 'N/A');
                    $('#vendorAltMobileVal').text(vendor.alternate_mobile || 'N/A');
                    
                    // Set Status Badge
                    const badge = $('#vendorStatusBadge');
                    badge.text(vendor.profile_status || 'Pending');
                    badge.removeClass('bg-green-100 text-green-800 border-green-200 bg-yellow-100 text-yellow-800 border-yellow-200 bg-red-100 text-red-800 border-red-200');
                    if (vendor.profile_status === 'Approved') {
                        badge.addClass('bg-green-100 text-green-800 border-green-200');
                    } else if (vendor.profile_status === 'Rejected') {
                        badge.addClass('bg-red-100 text-red-800 border-red-200');
                    } else {
                        badge.addClass('bg-yellow-100 text-yellow-800 border-yellow-200');
                    }
                    
                    // Set Commission Settings
                    const commType = vendor.commission_type === 'flat' ? 'Flat Fee' : 'Percentage';
                    const commRate = vendor.commission_type === 'flat'
                        ? `₹${parseFloat(vendor.flat_commission || 0).toFixed(2)}`
                        : `${parseFloat(vendor.commission_percentage || 0).toFixed(2)}%`;
                    $('#vendorCommTypeVal').text(commType);
                    $('#vendorCommRateVal').text(commRate);
                    
                    // Set Address
                    const addressStr = `${vendor.address || ''}, ${vendor.city || ''}, ${vendor.state || ''} - ${vendor.pincode || ''}`.replace(/^,\s*|,\s*$/, '').trim();
                    $('#vendorAddressVal').text(addressStr || 'Address details not provided.');
                    
                    // Set Document Details
                    $('#vendorAadhaarNoVal').text(vendor.aadhaar_number || 'N/A');
                    $('#vendorPanNoVal').text(vendor.pan_number || 'N/A');
                    $('#vendorGstNoVal').text(vendor.gst_number || 'N/A');
                    
                    // Set Images
                    const photoSrc = vendor.photo ? `/images/${vendor.photo}` : 'https://placehold.co/150x150?text=No+Photo';
                    const logoSrc = vendor.company_logo ? `/images/${vendor.company_logo}` : 'https://placehold.co/100x100?text=No+Logo';
                    $('#vendorPhotoImg').attr('src', photoSrc);
                    $('#vendorCompanyLogoImg').attr('src', logoSrc);
                    
                    // Set Document Links
                    if (vendor.aadhaar_file) {
                        $('#vendorAadhaarLinkVal').attr('href', `/images/${vendor.aadhaar_file}`).show();
                    } else {
                        $('#vendorAadhaarLinkVal').hide();
                    }
                    if (vendor.pan_file) {
                        $('#vendorPanLinkVal').attr('href', `/images/${vendor.pan_file}`).show();
                    } else {
                        $('#vendorPanLinkVal').hide();
                    }
                    
                    // Open Modal
                    modal.removeClass('hidden').addClass('flex');
                } else {
                    $.toastr.error('Could not fetch vendor profile details.');
                }
            },
            error: function() {
                $.toastr.error('Failed to communicate with the server.');
            }
        });
    });
});
</script>
@endsection
