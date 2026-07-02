@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="mb-6">
            <nav class="flex text-xs text-gray-500 mb-2" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-2">
                    <li class="inline-flex items-center">
                        <a href="{{ route('dashboard') }}" class="hover:text-gray-700">Dashboard</a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-[10px] mx-2 text-gray-400"></i>
                            <a href="{{ route('seoPages.index') }}" class="hover:text-gray-700">SEO Pages</a>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-[10px] mx-2 text-gray-400"></i>
                            <span class="text-gray-900 font-medium">Add SEO Page</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <h1 class="text-xl font-bold text-gray-900">Add Location-Based SEO Page</h1>
        </div>

        <form id="seoForm" action="{{ route('seoPages.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left 2 Columns: Details & Metadata -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Basic Information -->
                    <div class="bg-white shadow-sm border border-gray-100 rounded-xl p-6">
                        <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wider border-b pb-2 mb-4">Basic Information</h2>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">SEO Page Title <span class="text-red-500">*</span></label>
                                <input type="text" name="title" id="title" placeholder="e.g. Cab Service in Delhi"
                                    class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-1 focus:ring-blue-500 focus:outline-none">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Slug (Auto-generated)</label>
                                <input type="text" name="slug" id="slug" placeholder="e.g. cab-service-in-delhi"
                                    class="w-full border border-gray-300 rounded px-3 py-2 text-sm bg-gray-50 focus:ring-1 focus:ring-blue-500 focus:outline-none">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Short Description</label>
                                <textarea name="short_description" rows="3" placeholder="Brief page abstract..."
                                    class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-1 focus:ring-blue-500 focus:outline-none"></textarea>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Full SEO Page Content <span class="text-red-500">*</span></label>
                                <textarea name="content" id="mytextarea" class="w-full border border-gray-300 rounded"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Travel & Transport Guide Info -->
                    <div class="bg-white shadow-sm border border-gray-100 rounded-xl p-6">
                        <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wider border-b pb-2 mb-4">Travel Information Details</h2>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Best Time to Visit</label>
                                <input type="text" name="best_time_to_visit" placeholder="e.g. Oct to Mar"
                                    class="w-full border border-gray-300 rounded px-3 py-2 text-xs focus:ring-1 focus:ring-blue-500 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Distance (km)</label>
                                <input type="text" name="distance" placeholder="e.g. 230 km"
                                    class="w-full border border-gray-300 rounded px-3 py-2 text-xs focus:ring-1 focus:ring-blue-500 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Estimated Travel Time</label>
                                <input type="text" name="estimated_travel_time" placeholder="e.g. 4.5 hours"
                                    class="w-full border border-gray-300 rounded px-3 py-2 text-xs focus:ring-1 focus:ring-blue-500 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Starting Price (₹)</label>
                                <input type="number" step="0.01" name="starting_price" placeholder="e.g. 2500"
                                    class="w-full border border-gray-300 rounded px-3 py-2 text-xs focus:ring-1 focus:ring-blue-500 focus:outline-none">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Nearby Attractions</label>
                                <input type="text" name="nearby_attractions" placeholder="e.g. Taj Mahal, Red Fort"
                                    class="w-full border border-gray-300 rounded px-3 py-2 text-xs focus:ring-1 focus:ring-blue-500 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Nearby Railway Station</label>
                                <input type="text" name="nearby_railway_station" placeholder="e.g. New Delhi Station"
                                    class="w-full border border-gray-300 rounded px-3 py-2 text-xs focus:ring-1 focus:ring-blue-500 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Nearby Airport</label>
                                <input type="text" name="nearby_airport" placeholder="e.g. IGI Airport"
                                    class="w-full border border-gray-300 rounded px-3 py-2 text-xs focus:ring-1 focus:ring-blue-500 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Nearby Bus Stand</label>
                                <input type="text" name="nearby_bus_stand" placeholder="e.g. ISBT Kashmiri Gate"
                                    class="w-full border border-gray-300 rounded px-3 py-2 text-xs focus:ring-1 focus:ring-blue-500 focus:outline-none">
                            </div>
                        </div>

                        <div class="space-y-4 mt-6">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Why Choose Us</label>
                                <textarea name="why_choose_us" class="tinymce-basic w-full border border-gray-300 rounded"></textarea>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-1">Services Included</label>
                                    <textarea name="services_included" class="tinymce-basic w-full border border-gray-300 rounded"></textarea>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-1">Services Excluded</label>
                                    <textarea name="services_excluded" class="tinymce-basic w-full border border-gray-300 rounded"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- FAQs Section -->
                    <div class="bg-white shadow-sm border border-gray-100 rounded-xl p-6">
                        <div class="flex justify-between items-center border-b pb-2 mb-4">
                            <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Frequently Asked Questions</h2>
                            <button type="button" id="addFaqBtn" class="px-2.5 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded text-xs font-semibold transition">
                                <i class="fas fa-plus mr-1"></i> Add FAQ
                            </button>
                        </div>
                        <div id="faqsWrapper" class="space-y-4"></div>
                    </div>
                </div>

                <!-- Right Column: Media, Geography, Meta -->
                <div class="space-y-6">
                    <!-- Media File Uploads -->
                    <div class="bg-white shadow-sm border border-gray-100 rounded-xl p-6">
                        <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wider border-b pb-2 mb-4">Page Media</h2>
                        <div class="space-y-4">
                            <!-- Banner Image -->
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Banner Image</label>
                                <input type="file" name="banner_image" id="bannerImageInput" accept="image/*"
                                    class="w-full border border-gray-300 rounded px-3 py-1.5 text-xs focus:outline-none">
                                <div id="bannerPreviewContainer" class="hidden mt-3 border rounded overflow-hidden h-28 bg-gray-50 flex items-center justify-center relative">
                                    <img id="bannerPreview" class="w-full h-full object-cover" src="">
                                    <button type="button" id="removeBannerBtn" class="absolute top-1 right-1 w-6 h-6 bg-red-600 text-white rounded-full flex items-center justify-center text-xs shadow hover:bg-red-700 transition">&times;</button>
                                </div>
                            </div>

                            <!-- Featured Image -->
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Featured Cover Image</label>
                                <input type="file" name="featured_image" id="featuredImageInput" accept="image/*"
                                    class="w-full border border-gray-300 rounded px-3 py-1.5 text-xs focus:outline-none">
                                <div id="featuredPreviewContainer" class="hidden mt-3 border rounded overflow-hidden h-28 bg-gray-50 flex items-center justify-center relative">
                                    <img id="featuredPreview" class="w-full h-full object-cover" src="">
                                    <button type="button" id="removeFeaturedBtn" class="absolute top-1 right-1 w-6 h-6 bg-red-600 text-white rounded-full flex items-center justify-center text-xs shadow hover:bg-red-700 transition">&times;</button>
                                </div>
                            </div>

                            <hr class="border-gray-100">

                            <!-- Gallery -->
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Gallery Images</label>
                                <input type="file" name="gallery_images[]" id="galleryImagesInput" accept="image/*" multiple
                                    class="w-full border border-gray-300 rounded px-3 py-1.5 text-xs focus:outline-none">
                                <div id="galleryPreviews" class="grid grid-cols-3 gap-2 mt-3"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Geography Location Details -->
                    <div class="bg-white shadow-sm border border-gray-100 rounded-xl p-6">
                        <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wider border-b pb-2 mb-4">Location Mapping</h2>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">State <span class="text-red-500">*</span></label>
                                <select name="state_id" id="state_id" class="w-full border border-gray-300 rounded px-3 py-2 text-xs focus:ring-1 focus:ring-blue-500 focus:outline-none">
                                    <option value="">Select State</option>
                                    @foreach ($states as $state)
                                        <option value="{{ $state->id }}">{{ $state->state_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">City <span class="text-red-500">*</span></label>
                                <select name="city_id" id="city_id" class="w-full border border-gray-300 rounded px-3 py-2 text-xs focus:ring-1 focus:ring-blue-500 focus:outline-none">
                                    <option value="">Select City</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Route (Optional)</label>
                                <select name="route_id" class="w-full border border-gray-300 rounded px-3 py-2 text-xs focus:ring-1 focus:ring-blue-500 focus:outline-none">
                                    <option value="">Select Popular Route</option>
                                    @foreach ($routes as $route)
                                        <option value="{{ $route->id }}">{{ $route->route_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-1">Pickup Location</label>
                                    <input type="text" name="pickup_location" placeholder="e.g. Airport"
                                        class="w-full border border-gray-300 rounded px-3 py-2 text-xs focus:ring-1 focus:ring-blue-500 focus:outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-1">Destination</label>
                                    <input type="text" name="destination_location" placeholder="e.g. Hotel"
                                        class="w-full border border-gray-300 rounded px-3 py-2 text-xs focus:ring-1 focus:ring-blue-500 focus:outline-none">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Category taxonomy selection -->
                    <div class="bg-white shadow-sm border border-gray-100 rounded-xl p-6">
                        <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wider border-b pb-2 mb-4">Service Category</h2>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Category <span class="text-red-500">*</span></label>
                            <select name="category_id" class="w-full border border-gray-300 rounded px-3 py-2 text-xs focus:ring-1 focus:ring-blue-500 focus:outline-none">
                                <option value="">Select Service Category</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->category_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- SEO Fields -->
                    <div class="bg-white shadow-sm border border-gray-100 rounded-xl p-6">
                        <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wider border-b pb-2 mb-4">Google Search SEO</h2>
                        <div class="space-y-4">
                            <div>
                                <div class="flex justify-between items-center mb-1">
                                    <label class="block text-xs font-semibold text-gray-700">Meta Title</label>
                                    <span id="metaTitleCount" class="text-[10px] text-gray-400">0/70</span>
                                </div>
                                <input type="text" name="meta_title" id="metaTitle" placeholder="SEO search title" maxlength="70"
                                    class="w-full border border-gray-300 rounded px-3 py-2 text-xs focus:ring-1 focus:ring-blue-500 focus:outline-none">
                            </div>
                            <div>
                                <div class="flex justify-between items-center mb-1">
                                    <label class="block text-xs font-semibold text-gray-700">Meta Description</label>
                                    <span id="metaDescCount" class="text-[10px] text-gray-400">0/160</span>
                                </div>
                                <textarea name="meta_description" id="metaDesc" rows="3" placeholder="SEO description snippet..." maxlength="160"
                                    class="w-full border border-gray-300 rounded px-3 py-2 text-xs focus:ring-1 focus:ring-blue-500 focus:outline-none"></textarea>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Meta Keywords</label>
                                <input type="text" name="meta_keywords" placeholder="Keywords (comma separated)"
                                    class="w-full border border-gray-300 rounded px-3 py-2 text-xs focus:ring-1 focus:ring-blue-500 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Canonical URL</label>
                                <input type="url" name="canonical_url" placeholder="https://sbdbooking.com/..."
                                    class="w-full border border-gray-300 rounded px-3 py-2 text-xs focus:ring-1 focus:ring-blue-500 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Schema Markup Type</label>
                                <select name="schema_type" class="w-full border border-gray-300 rounded px-3 py-2 text-xs focus:ring-1 focus:ring-blue-500 focus:outline-none">
                                    <option value="LocalBusiness">LocalBusiness</option>
                                    <option value="TravelAgency">TravelAgency</option>
                                    <option value="Service">Service</option>
                                    <option value="FAQPage">FAQPage</option>
                                    <option value="Article">Article</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Related Links & CTAs -->
                    <div class="bg-white shadow-sm border border-gray-100 rounded-xl p-6">
                        <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wider border-b pb-2 mb-4">Related Links & CTAs</h2>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Select Related Blogs</label>
                                <select name="related_blogs[]" class="w-full select2 border border-gray-300 rounded text-xs" multiple>
                                    @foreach ($blogs as $blog)
                                        <option value="{{ $blog->id }}">{{ $blog->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Select Related SEO Pages</label>
                                <select name="related_pages[]" class="w-full select2 border border-gray-300 rounded text-xs" multiple>
                                    @foreach ($seoPages as $sp)
                                        <option value="{{ $sp->id }}">{{ $sp->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Select Related Popular Routes</label>
                                <select name="related_routes[]" class="w-full select2 border border-gray-300 rounded text-xs" multiple>
                                    @foreach ($routes as $route)
                                        <option value="{{ $route->id }}">{{ $route->route_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <hr class="border-gray-100">

                            <!-- CTA Buttons Toggles -->
                            <div class="space-y-3">
                                <span class="block text-xs font-bold text-gray-800 uppercase tracking-wider">CTA Buttons</span>
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-gray-600 font-semibold">Enable "Book Cab"</span>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="cta_book_cab" value="1" checked class="sr-only peer">
                                        <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-blue-600"></div>
                                    </label>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-gray-600 font-semibold">Enable "Join Shared Trip"</span>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="cta_join_shared_trip" value="1" class="sr-only peer">
                                        <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-blue-600"></div>
                                    </label>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-gray-600 font-semibold">Enable "WhatsApp"</span>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="cta_whatsapp" value="1" {{ ($defaults['enable_whatsapp_button'] ?? 1) == 1 ? 'checked' : '' }} class="sr-only peer">
                                        <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-blue-600"></div>
                                    </label>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-gray-600 font-semibold">Enable "Call Now"</span>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="cta_call_now" value="1" {{ ($defaults['enable_call_button'] ?? 1) == 1 ? 'checked' : '' }} class="sr-only peer">
                                        <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-blue-600"></div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Publishing Status -->
                    <div class="bg-white shadow-sm border border-gray-100 rounded-xl p-6">
                        <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wider border-b pb-2 mb-4">Publish & Status</h2>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Author</label>
                                <input type="text" name="author" value="{{ $defaults['default_author'] }}"
                                    class="w-full border border-gray-300 rounded px-3 py-2 text-xs focus:ring-1 focus:ring-blue-500 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                                <select name="status" class="w-full border border-gray-300 rounded px-3 py-2 text-xs focus:ring-1 focus:ring-blue-500 focus:outline-none">
                                    <option value="Draft" {{ $defaults['default_page_status'] === 'Draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="Published" {{ $defaults['default_page_status'] === 'Published' ? 'selected' : '' }}>Published</option>
                                </select>
                            </div>
                            <div class="flex items-center justify-between">
                                <div>
                                    <label class="block text-xs font-bold text-gray-800">Featured Page</label>
                                    <span class="text-[10px] text-gray-400">Display this page as featured.</span>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="featured" value="1" {{ ($defaults['enable_featured_pages'] ?? 1) == 1 ? 'checked' : '' }} class="sr-only peer">
                                    <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-blue-600"></div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons Footer -->
            <div class="bg-white border border-gray-100 shadow-sm rounded-xl p-5 mt-6 flex justify-end gap-3">
                <a href="{{ route('seoPages.index') }}" class="px-5 py-2.5 border border-gray-200 hover:bg-gray-50 text-gray-700 rounded-md text-xs font-bold transition">
                    Cancel
                </a>
                <button type="submit" id="submitBtn" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-xs font-bold transition shadow">
                    Create SEO Page
                </button>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            // Select2 Tag init
            $('.select2').select2({
                placeholder: "Select options"
            });

            // Initialize Basic TinyMCE for Why Choose, Included, Excluded
            if (typeof tinymce !== 'undefined') {
                tinymce.init({
                    selector: '.tinymce-basic',
                    height: 200,
                    menubar: false,
                    plugins: 'lists link code',
                    toolbar: 'undo redo | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist | link code'
                });
            }

            // State-city dynamic load
            $('#state_id').on('change', function() {
                const stateId = $(this).val();
                $('#city_id').html('<option value="">Select City</option>');
                if (stateId) {
                    $.get(`{{ url('panel/seo-cities/by-state') }}/${stateId}`, function(cities) {
                        $.each(cities, function(i, city) {
                            $('#city_id').append(`<option value="${city.id}">${city.city_name}</option>`);
                        });
                    });
                }
            });

            // Auto slug generator from Title
            $('#title').on('input', function() {
                let val = $(this).val().toLowerCase()
                    .replace(/[^a-z0-9 -]/g, '')
                    .replace(/\s+/g, '-')
                    .replace(/-+/g, '-');
                $('#slug').val(val);
            });

            // Meta Counters
            $('#metaTitle').on('input', function() {
                $('#metaTitleCount').text($(this).val().length + '/70');
            });
            $('#metaDesc').on('input', function() {
                $('#metaDescCount').text($(this).val().length + '/160');
            });

            // Image uploads previews
            $('#bannerImageInput').on('change', function (e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        $('#bannerPreview').attr('src', e.target.result);
                        $('#bannerPreviewContainer').removeClass('hidden');
                    }
                    reader.readAsDataURL(file);
                }
            });

            $('#removeBannerBtn').on('click', function () {
                $('#bannerImageInput').val('');
                $('#bannerPreviewContainer').addClass('hidden');
            });

            $('#featuredImageInput').on('change', function (e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        $('#featuredPreview').attr('src', e.target.result);
                        $('#featuredPreviewContainer').removeClass('hidden');
                    }
                    reader.readAsDataURL(file);
                }
            });

            $('#removeFeaturedBtn').on('click', function () {
                $('#featuredImageInput').val('');
                $('#featuredPreviewContainer').addClass('hidden');
            });

            $('#galleryImagesInput').on('change', function (e) {
                const files = e.target.files;
                $('#galleryPreviews').html('');
                if (files) {
                    for (let i = 0; i < files.length; i++) {
                        const file = files[i];
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            $('#galleryPreviews').append(`
                                <div class="relative border rounded overflow-hidden h-20 bg-gray-50 flex items-center justify-center">
                                    <img src="${e.target.result}" class="w-full h-full object-cover">
                                </div>
                            `);
                        }
                        reader.readAsDataURL(file);
                    }
                }
            });

            // FAQ dynamics
            let faqIndex = 0;
            function addFaqRow() {
                const html = `
                    <div class="faq-item border border-gray-200 rounded-lg p-4 relative bg-gray-50/50 flex flex-col gap-2">
                        <button type="button" class="absolute top-2 right-2 text-red-500 hover:text-red-700 remove-faq-btn focus:outline-none font-bold">&times;</button>
                        <div class="grid grid-cols-1 gap-3">
                            <div>
                                <label class="block text-[10px] font-semibold text-gray-500 uppercase mb-0.5">Question</label>
                                <input type="text" name="faqs[${faqIndex}][question]" class="w-full border rounded px-3 py-1.5 text-xs focus:ring-1 focus:ring-blue-500 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-[10px] font-semibold text-gray-500 uppercase mb-0.5">Answer</label>
                                <textarea name="faqs[${faqIndex}][answer]" rows="2" class="w-full border rounded px-3 py-1.5 text-xs focus:ring-1 focus:ring-blue-500 focus:outline-none"></textarea>
                            </div>
                        </div>
                    </div>
                `;
                $('#faqsWrapper').append(html);
                faqIndex++;
            }

            $('#addFaqBtn').on('click', addFaqRow);
            $(document).on('click', '.remove-faq-btn', function() {
                $(this).closest('.faq-item').remove();
            });

            // Add initial FAQ block
            addFaqRow();

            // Submit AJAX
            $('#seoForm').on('submit', function(e) {
                e.preventDefault();

                if (typeof tinymce !== 'undefined') {
                    tinymce.triggerSave();
                }

                const form = this;
                const formData = new FormData(form);

                $.ajax({
                    url: $(form).attr('action'),
                    method: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    beforeSend: function () {
                        $("#submitBtn").prop('disabled', true);
                        $("#submitBtn").html('<span class="animate-spin inline-block w-4 h-4 border-t-2 border-white rounded-full mr-2"></span> Saving...');
                    },
                    success: function (response) {
                        if (response.success) {
                            $.toastr.success(response.message);
                            window.location.href = "{{ route('seoPages.index') }}";
                        } else {
                            $.toastr.error(response.message);
                        }
                    },
                    error: function (xhr) {
                        $('.error-text').remove();
                        $('input, select, textarea').removeClass('border-red-500');
                        if (xhr.status === 422 && xhr.responseJSON.errors) {
                            $.each(xhr.responseJSON.errors, function (key, val) {
                                const fieldKey = key.replace(/\./g, '\\.');
                                const input = $(`[name="${key}"], [name="${key}[]"], #${fieldKey}`);
                                if (input.length) {
                                    input.addClass('border-red-500');
                                    input.after(`<span class="text-red-500 text-xs error-text mt-1 block">${val[0]}</span>`);
                                }
                                $.toastr.error(val[0]);
                            });
                            const firstErr = $('.border-red-500').first();
                            if (firstErr.length) {
                                $('html, body').animate({ scrollTop: firstErr.offset().top - 120 }, 400);
                            }
                        } else {
                            $.toastr.error("Something went wrong!");
                        }
                    },
                    complete: function () {
                        $("#submitBtn").prop('disabled', false).text('Create SEO Page');
                    }
                });
            });
        });
    </script>
@endsection
