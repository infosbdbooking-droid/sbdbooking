@extends('layouts.app')

@section('content')
<div class="space-y-6 pb-12">
    @php
        $user = auth()->user();
        $status = $user->profile_status ?? 'Pending';
        $isProfileFilled = !empty($user->mobile);
    @endphp

    <!-- Warning Status Banner -->
    @if($status !== 'Approved')
        <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-sm text-left">
            <div class="flex items-start sm:items-center gap-3">
                <div class="p-2.5 bg-amber-100 text-amber-700 rounded-xl">
                    <i class="fas fa-exclamation-triangle text-base animate-pulse"></i>
                </div>
                <div>
                    @if($status === 'Rejected')
                        <h4 class="text-xs font-bold text-amber-800 uppercase tracking-wider">Profile Verification Rejected</h4>
                        <p class="text-xs text-amber-700 font-semibold mt-0.5">
                            Reason: "{{ $user->rejection_reason }}". Please update your profile details and documents below and re-submit.
                        </p>
                    @else
                        <h4 class="text-xs font-bold text-amber-800 uppercase tracking-wider">Profile Pending Approval</h4>
                        <p class="text-xs text-amber-700 font-semibold mt-0.5">
                            Your account is currently under review. Please complete your profile details and wait for admin approval to access all features.
                        </p>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Profile Header Card -->
    <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm flex flex-col md:flex-row items-center gap-6">
        <div class="relative group">
            <div class="w-24 h-24 rounded-2xl overflow-hidden bg-slate-100 border-2 border-slate-200 flex items-center justify-center">
                @if($user->company_logo)
                    <img src="/storage/{{ $user->company_logo }}" alt="Company Logo" class="w-full h-full object-cover">
                @elseif($user->photo)
                    <img src="/storage/{{ $user->photo }}" alt="Vendor Photo" class="w-full h-full object-cover">
                @else
                    <i class="fas fa-store text-4xl text-slate-300"></i>
                @endif
            </div>
        </div>
        <div class="text-center md:text-left flex-1">
            <div class="flex flex-col md:flex-row md:items-center gap-2">
                <h2 class="text-xl font-bold text-slate-800">{{ $user->name }}</h2>
                <div>
                    <span class="px-3 py-1 bg-yellow-500/10 text-yellow-700 border border-yellow-200 text-[10px] font-black rounded-full uppercase tracking-wider">
                        {{ $status }}
                    </span>
                </div>
            </div>
            <p class="text-xs text-slate-400 mt-1 flex flex-col md:flex-row md:items-center gap-2">
                <span><i class="fas fa-envelope mr-1 text-[#d84e55]"></i> {{ $user->email }}</span>
                @if($user->mobile)
                    <span class="hidden md:inline text-slate-300">|</span>
                    <span><i class="fas fa-phone mr-1 text-[#d84e55]"></i> {{ $user->mobile }}</span>
                @endif
            </p>
        </div>
    </div>

    <!-- Main Layout Grid -->
    <form id="profileForm" action="{{ route('vendor.verify.submit') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Left 2/3 Column: Information Forms -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Section 1: Personal Details -->
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm text-left">
                    <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider border-b pb-2 mb-4 flex items-center gap-2">
                        <i class="fas fa-user text-[#d84e55]"></i> Personal Details
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                            <input type="text" name="name" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-xs focus:outline-none focus:border-[#d84e55]" value="{{ $user->name }}" required>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Email Address <span class="text-red-500">*</span></label>
                            <input type="email" name="email" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-xs bg-slate-50 text-slate-400 cursor-not-allowed focus:outline-none" value="{{ $user->email }}" readonly>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Phone Number <span class="text-red-500">*</span></label>
                            <input type="text" name="mobile" id="mobile" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-xs focus:outline-none focus:border-[#d84e55]" placeholder="10-digit number" value="{{ $user->mobile }}" required>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">New Password (leave blank if unchanged)</label>
                            <input type="password" name="password" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-xs focus:outline-none focus:border-[#d84e55]" placeholder="Min 6 characters">
                        </div>
                    </div>
                </div>

                <!-- Section 2: Business Information -->
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm text-left">
                    <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider border-b pb-2 mb-4 flex items-center gap-2">
                        <i class="fas fa-briefcase text-[#d84e55]"></i> Business Information
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-gray-700 mb-1">Trade Address / Full Address <span class="text-red-500">*</span></label>
                            <input type="text" name="address" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-xs focus:outline-none focus:border-[#d84e55]" placeholder="Full office address" value="{{ $user->address }}" required>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">PAN Number <span class="text-red-500">*</span></label>
                            <input type="text" name="pan_number" id="pan_number" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-xs focus:outline-none focus:border-[#d84e55]" placeholder="10-digit PAN (e.g. ABCDE1234F)" value="{{ $user->pan_number }}" required>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Aadhaar Number <span class="text-red-500">*</span></label>
                            <input type="text" name="aadhaar_number" id="aadhaar_number" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-xs focus:outline-none focus:border-[#d84e55]" placeholder="12-digit Aadhaar Number" value="{{ $user->aadhaar_number }}" required>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Do you have GST? <span class="text-red-500">*</span></label>
                            <select name="has_gst" id="has_gst" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-xs focus:outline-none focus:border-[#d84e55]">
                                <option value="no" {{ empty($user->gst_number) ? 'selected' : '' }}>No</option>
                                <option value="yes" {{ !empty($user->gst_number) ? 'selected' : '' }}>Yes</option>
                            </select>
                        </div>
                        <div id="gstNumberField" class="{{ empty($user->gst_number) ? 'hidden' : '' }}">
                            <label class="block text-xs font-bold text-gray-700 mb-1">GST Number <span class="text-red-500">*</span></label>
                            <input type="text" name="gst_number" id="gst_number" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-xs focus:outline-none focus:border-[#d84e55]" placeholder="15-character GSTIN" value="{{ $user->gst_number }}">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-slate-400 mb-1">Commission Rate</label>
                            <div class="w-full px-4 py-3 bg-purple-50 border border-purple-100 rounded-xl text-purple-700 font-extrabold text-xs">
                                @if($user->commission_type === 'flat')
                                    <i class="fas fa-percent mr-1.5"></i> Flat rate of ₹{{ number_format($user->flat_commission, 2) }} per booking
                                @elseif($user->commission_type === 'percentage')
                                    <i class="fas fa-percent mr-1.5"></i> Standard Platform Fee: {{ number_format($user->commission_percentage, 2) }}% commission
                                @else
                                    <i class="fas fa-percent mr-1.5"></i> Standard Platform Fee
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Location Context -->
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm text-left">
                    <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider border-b pb-2 mb-4 flex items-center gap-2">
                        <i class="fas fa-map-marker-alt"></i> Location Context
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Pincode <span class="text-red-500">*</span></label>
                            <input type="text" name="pincode" id="pincode" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-xs focus:outline-none focus:border-[#d84e55]" placeholder="6-digit pincode" value="{{ $user->pincode }}" required>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Country</label>
                            <input type="text" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-xs bg-slate-50 text-slate-400 cursor-not-allowed focus:outline-none" value="India" readonly>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">State <span class="text-red-500">*</span></label>
                            <input type="text" name="state" id="state" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-xs focus:outline-none focus:border-[#d84e55]" placeholder="State" value="{{ $user->state }}" required>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">City <span class="text-red-500">*</span></label>
                            <input type="text" name="city" id="city" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-xs focus:outline-none focus:border-[#d84e55]" placeholder="City" value="{{ $user->city }}" required>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right 1/3 Column: Verification Files Upload -->
            <div class="lg:col-span-1 space-y-6">
                
                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm text-left flex flex-col h-full justify-between">
                    <div>
                        <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider border-b pb-2 mb-6 flex items-center gap-2">
                            <i class="fas fa-file-upload text-[#d84e55]"></i> Verification Files
                        </h3>

                        <!-- File 1: Passport Photo -->
                        <div class="mb-5">
                            <div class="flex justify-between items-center mb-1">
                                <label class="block text-xs font-bold text-gray-700">Passport Photo <span class="text-red-500">*</span></label>
                                @if($user->photo)
                                    <a href="/storage/{{ $user->photo }}" target="_blank" class="text-[10px] text-blue-600 font-bold hover:underline">View Existing</a>
                                @endif
                            </div>
                            <input type="file" name="photo" id="photo" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-xs bg-slate-50 focus:outline-none">
                        </div>

                        <!-- File 2: Aadhaar Card -->
                        <div class="mb-5">
                            <div class="flex justify-between items-center mb-1">
                                <label class="block text-xs font-bold text-gray-700">Aadhaar Card (PDF/Image) <span class="text-red-500">*</span></label>
                                @if($user->aadhaar_file)
                                    <a href="/storage/{{ $user->aadhaar_file }}" target="_blank" class="text-[10px] text-blue-600 font-bold hover:underline">View Existing</a>
                                @endif
                            </div>
                            <input type="file" name="aadhaar_file" id="aadhaar_file" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-xs bg-slate-50 focus:outline-none">
                        </div>

                        <!-- File 3: PAN Card -->
                        <div class="mb-5">
                            <div class="flex justify-between items-center mb-1">
                                <label class="block text-xs font-bold text-gray-700">PAN Card (PDF/Image) <span class="text-red-500">*</span></label>
                                @if($user->pan_file)
                                    <a href="/storage/{{ $user->pan_file }}" target="_blank" class="text-[10px] text-blue-600 font-bold hover:underline">View Existing</a>
                                @endif
                            </div>
                            <input type="file" name="pan_file" id="pan_file" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-xs bg-slate-50 focus:outline-none">
                        </div>

                        <!-- File 4: Company Logo -->
                        <div class="mb-6">
                            <div class="flex justify-between items-center mb-1">
                                <label class="block text-xs font-bold text-gray-700">Company Logo (Image)</label>
                                @if($user->company_logo)
                                    <a href="/storage/{{ $user->company_logo }}" target="_blank" class="text-[10px] text-blue-600 font-bold hover:underline">View Existing</a>
                                @endif
                            </div>
                            <input type="file" name="company_logo" id="company_logo" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-xs bg-slate-50 focus:outline-none">
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-6 border-t border-slate-100">
                        <button type="submit" class="w-full py-3 bg-[#d84e55] hover:bg-[#c04349] text-white rounded-xl text-xs font-extrabold shadow-lg shadow-red-500/20 transition-all duration-150 flex items-center justify-center gap-2 submitBtn">
                            <i class="fas fa-save"></i> Save Changes
                        </button>
                    </div>
                </div>

            </div>

        </div>
    </form>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.3.0/jquery.form.min.js"></script>
<script>
    $(document).ready(function () {
        // Toggle GST Field
        $('#has_gst').on('change', function () {
            if ($(this).val() === 'yes') {
                $('#gstNumberField').removeClass('hidden');
            } else {
                $('#gstNumberField').addClass('hidden');
                $('#gst_number').val('');
            }
        });

        // AJAX Form Submit
        $('#profileForm').on('submit', function (e) {
            e.preventDefault();
            
            // Simple JS Validations
            const mobile = $('#mobile').val().trim();
            const aadhaar = $('#aadhaar_number').val().trim();
            const pan = $('#pan_number').val().trim();
            
            if (mobile.length < 10) {
                $.toastr.error("Please enter a valid 10-digit mobile number.");
                return false;
            }
            if (aadhaar.length !== 12) {
                $.toastr.error("Please enter a 12-digit Aadhaar number.");
                return false;
            }
            if (pan.length !== 10) {
                $.toastr.error("Please enter a valid 10-digit PAN number.");
                return false;
            }

            const form = $(this);
            const submitBtn = $('.submitBtn');
            const originalHtml = submitBtn.html();

            form.ajaxSubmit({
                beforeSend: function () {
                    submitBtn.html('<span class="animate-spin inline-block w-4 h-4 border-t-2 border-white rounded-full mr-2"></span> Saving...').prop('disabled', true);
                },
                success: function (response) {
                    if (response.success) {
                        $.toastr.success(response.message);
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        $.toastr.error(response.message);
                        submitBtn.html(originalHtml).prop('disabled', false);
                    }
                },
                error: function (xhr) {
                    submitBtn.html(originalHtml).prop('disabled', false);
                    $('.error-border').removeClass('border-red-500 error-border');
                    
                    if (xhr.status === 422 && xhr.responseJSON.errors) {
                        $.each(xhr.responseJSON.errors, function (key, val) {
                            const input = $('[name="' + key + '"]');
                            input.addClass('border-red-500 error-border');
                            $.toastr.error(val[0]);
                        });
                    } else {
                        $.toastr.error(xhr.responseJSON?.message || "Failed to submit verification request. Please check files and try again.");
                    }
                }
            });
        });
    });
</script>
@endsection
