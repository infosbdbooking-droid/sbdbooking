@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
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
                                <span class="text-gray-900 font-medium">Page Details</span>
                            </div>
                        </li>
                    </ol>
                </nav>
                <h1 class="text-xl font-bold text-gray-900">SEO Page Details</h1>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('seoPages.index') }}" class="px-4 py-2 border border-gray-200 hover:bg-gray-50 text-gray-700 rounded-md text-xs font-bold transition">
                    <i class="fas fa-arrow-left mr-1.5"></i> Back
                </a>
                <a href="{{ route('seoPages.edit', $page->id) }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-xs font-bold transition shadow">
                    <i class="fas fa-edit mr-1.5"></i> Edit Page
                </a>
            </div>
        </div>

        <!-- Banner Banner -->
        @if ($page->banner_image)
            <div class="w-full h-64 md:h-80 rounded-2xl overflow-hidden mb-6 relative shadow border">
                <img src="{{ asset('images/seo/banners/' . $page->banner_image) }}" alt="Banner" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/10 to-transparent flex items-end p-6">
                    <div>
                        <span class="bg-blue-600 text-white text-xs font-bold px-3 py-1 rounded-full shadow block w-max mb-2">
                            {{ $page->category ? $page->category->category_name : 'Uncategorized' }}
                        </span>
                        <h1 class="text-2xl md:text-3xl font-black text-white leading-tight">{{ $page->title }}</h1>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left 2 Columns -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Blog Main Content Card -->
                <div class="bg-white shadow-sm border border-gray-100 rounded-xl p-6">
                    @if (!$page->banner_image)
                        <div class="flex items-center gap-2 mb-2">
                            <span class="bg-blue-600 text-white text-xs font-bold px-3 py-1 rounded-full shadow">
                                {{ $page->category ? $page->category->category_name : 'Uncategorized' }}
                            </span>
                            @if ($page->featured)
                                <span class="bg-amber-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow">
                                    <i class="fas fa-star mr-1"></i> Featured
                                </span>
                            @endif
                        </div>
                        <h1 class="text-2xl font-black text-gray-900 leading-tight mb-4">{{ $page->title }}</h1>
                    @endif

                    @if ($page->short_description)
                        <div class="bg-slate-50 border-l-4 border-blue-500 p-4 rounded-r-lg mb-6">
                            <p class="text-sm italic text-gray-700">{{ $page->short_description }}</p>
                        </div>
                    @endif

                    <!-- Page Body Content -->
                    <div class="prose max-w-none text-sm text-gray-800 leading-relaxed font-sans">
                        {!! $page->content !!}
                    </div>
                </div>

                <!-- Why Choose, Included, Excluded Details -->
                <div class="bg-white shadow-sm border border-gray-100 rounded-xl p-6 space-y-6">
                    @if ($page->extended('why_choose_us'))
                        <div>
                            <h3 class="text-xs font-black text-gray-900 uppercase tracking-wider mb-2 border-l-4 border-blue-500 pl-2">Why Choose Us</h3>
                            <div class="text-sm text-gray-700 prose max-w-none font-sans">{!! $page->extended('why_choose_us') !!}</div>
                        </div>
                    @endif
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @if ($page->extended('services_included'))
                            <div>
                                <h3 class="text-xs font-black text-gray-900 uppercase tracking-wider mb-2 border-l-4 border-green-500 pl-2">Services Included</h3>
                                <div class="text-sm text-gray-700 prose max-w-none font-sans">{!! $page->extended('services_included') !!}</div>
                            </div>
                        @endif
                        @if ($page->extended('services_excluded'))
                            <div>
                                <h3 class="text-xs font-black text-gray-900 uppercase tracking-wider mb-2 border-l-4 border-red-500 pl-2">Services Excluded</h3>
                                <div class="text-sm text-gray-700 prose max-w-none font-sans">{!! $page->extended('services_excluded') !!}</div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Gallery Images -->
                @if ($page->gallery_images && count($page->gallery_images) > 0)
                    <div class="bg-white shadow-sm border border-gray-100 rounded-xl p-6">
                        <h2 class="text-xs font-bold text-gray-900 uppercase tracking-wider border-b pb-2 mb-4">Gallery Images</h2>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                            @foreach ($page->gallery_images as $image)
                                <div class="border rounded-lg overflow-hidden h-28 bg-gray-50 shadow-sm">
                                    <img src="{{ asset('images/seo/gallery/' . $image) }}" class="w-full h-full object-cover">
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- FAQs Section -->
                @if ($page->extended('faqs') && count($page->extended('faqs')) > 0)
                    <div class="bg-white shadow-sm border border-gray-100 rounded-xl p-6">
                        <h2 class="text-xs font-bold text-gray-900 uppercase tracking-wider border-b pb-2 mb-4">Frequently Asked Questions</h2>
                        <div class="space-y-4">
                            @foreach ($page->extended('faqs') as $faq)
                                <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                                    <span class="text-xs font-black text-slate-800 block"><i class="far fa-question-circle mr-1.5 text-blue-500"></i>{{ $faq['question'] ?? 'N/A' }}</span>
                                    <p class="text-xs text-slate-600 mt-2 leading-relaxed pl-5">{{ $faq['answer'] ?? 'N/A' }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right Column -->
            <div class="space-y-6">
                <!-- Cover Card -->
                @if ($page->featured_image)
                    <div class="bg-white shadow-sm border border-gray-100 rounded-xl overflow-hidden">
                        <div class="h-48 bg-gray-100">
                            <img src="{{ asset('images/seo/featured/' . $page->featured_image) }}" alt="Cover" class="w-full h-full object-cover">
                        </div>
                    </div>
                @endif

                <!-- Details & Metrics Card -->
                <div class="bg-white shadow-sm border border-gray-100 rounded-xl p-6">
                    <h2 class="text-xs font-bold text-gray-900 uppercase tracking-wider border-b pb-2 mb-4">Page Information</h2>
                    <div class="space-y-3 text-xs">
                        <div class="flex justify-between py-1.5 border-b border-gray-50">
                            <span class="text-gray-500 font-medium">Author</span>
                            <span class="font-bold text-gray-800">{{ $page->author ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-gray-50">
                            <span class="text-gray-500 font-medium">Status</span>
                            <span class="font-bold text-gray-800">{{ $page->status ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-gray-50">
                            <span class="text-gray-500 font-medium">Views</span>
                            <span class="font-bold text-blue-600"><i class="far fa-eye mr-1"></i>{{ number_format($page->view_count) }}</span>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-gray-50">
                            <span class="text-gray-500 font-medium">Created Date</span>
                            <span class="font-bold text-gray-800">{{ $page->created_at ? $page->created_at->format('Y-m-d H:i') : '-' }}</span>
                        </div>
                        <div class="flex justify-between py-1.5">
                            <span class="text-gray-500 font-medium">Updated Date</span>
                            <span class="font-bold text-gray-800">{{ $page->updated_at ? $page->updated_at->format('Y-m-d H:i') : '-' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Geography Location Details -->
                <div class="bg-white shadow-sm border border-gray-100 rounded-xl p-6">
                    <h2 class="text-xs font-bold text-gray-900 uppercase tracking-wider border-b pb-2 mb-4">Geography Details</h2>
                    <div class="space-y-3 text-xs">
                        <div class="flex justify-between py-1.5 border-b border-gray-50">
                            <span class="text-gray-500 font-medium">State</span>
                            <span class="font-bold text-gray-800">{{ $page->state ? $page->state->state_name : '-' }}</span>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-gray-50">
                            <span class="text-gray-500 font-medium">City</span>
                            <span class="font-bold text-gray-800">{{ $page->city ? $page->city->city_name : '-' }}</span>
                        </div>
                        @if ($page->route)
                            <div class="flex justify-between py-1.5 border-b border-gray-50">
                                <span class="text-gray-500 font-medium">Route</span>
                                <span class="font-bold text-gray-800">{{ $page->route->route_name }}</span>
                            </div>
                        @endif
                        @if ($page->pickup_location)
                            <div class="flex justify-between py-1.5 border-b border-gray-50">
                                <span class="text-gray-500 font-medium">Pickup</span>
                                <span class="font-bold text-gray-800">{{ $page->pickup_location }}</span>
                            </div>
                        @endif
                        @if ($page->destination_location)
                            <div class="flex justify-between py-1.5">
                                <span class="text-gray-500 font-medium">Destination</span>
                                <span class="font-bold text-gray-800">{{ $page->destination_location }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Travel Guide Card -->
                <div class="bg-white shadow-sm border border-gray-100 rounded-xl p-6">
                    <h2 class="text-xs font-bold text-gray-900 uppercase tracking-wider border-b pb-2 mb-4">Travel Guide Specifications</h2>
                    <div class="space-y-3 text-xs">
                        <div class="flex justify-between py-1.5 border-b border-gray-50">
                            <span class="text-gray-500 font-medium">Best Time to Visit</span>
                            <span class="font-bold text-gray-800">{{ $page->best_time_to_visit ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-gray-50">
                            <span class="text-gray-500 font-medium">Distance</span>
                            <span class="font-bold text-gray-800">{{ $page->extended('distance') ?: '-' }}</span>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-gray-50">
                            <span class="text-gray-500 font-medium">Est. Travel Time</span>
                            <span class="font-bold text-gray-800">{{ $page->extended('estimated_travel_time') ?: '-' }}</span>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-gray-50">
                            <span class="text-gray-500 font-medium">Starting Price</span>
                            <span class="font-bold text-gray-800">{{ $page->starting_price ? '₹' . number_format($page->starting_price, 2) : '-' }}</span>
                        </div>
                        <div class="flex justify-between py-1.5">
                            <span class="text-gray-500 font-medium">Nearby Attractions</span>
                            <span class="font-bold text-gray-800 truncate max-w-[150px]" title="{{ $page->extended('nearby_attractions') }}">{{ $page->extended('nearby_attractions') ?: '-' }}</span>
                        </div>
                    </div>
                </div>

                <!-- CTAs Preview -->
                <div class="bg-white shadow-sm border border-gray-100 rounded-xl p-6">
                    <h2 class="text-xs font-bold text-gray-900 uppercase tracking-wider border-b pb-2 mb-4">CTA Buttons Enabled</h2>
                    <div class="grid grid-cols-2 gap-2 text-center text-xs">
                        @if ($page->extended('cta_book_cab', 1) == 1)
                            <div class="bg-blue-50 text-blue-700 p-2.5 rounded-lg font-bold border border-blue-100">Book Cab</div>
                        @endif
                        @if ($page->extended('cta_join_shared_trip') == 1)
                            <div class="bg-purple-50 text-purple-700 p-2.5 rounded-lg font-bold border border-purple-100">Join Shared Trip</div>
                        @endif
                        @if ($page->extended('cta_whatsapp', 1) == 1)
                            <div class="bg-green-50 text-green-700 p-2.5 rounded-lg font-bold border border-green-100">WhatsApp</div>
                        @endif
                        @if ($page->extended('cta_call_now', 1) == 1)
                            <div class="bg-amber-50 text-amber-700 p-2.5 rounded-lg font-bold border border-amber-100">Call Now</div>
                        @endif
                    </div>
                </div>

                <!-- SEO Metadata Card -->
                <div class="bg-white shadow-sm border border-gray-100 rounded-xl p-6">
                    <h2 class="text-xs font-bold text-gray-900 uppercase tracking-wider border-b pb-2 mb-4">SEO Parameters</h2>
                    <div class="space-y-4 text-xs">
                        <div>
                            <span class="text-gray-500 font-semibold uppercase tracking-wider block mb-1">Meta Title</span>
                            <p class="font-bold text-gray-800 leading-normal">{{ $page->meta_title ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <span class="text-gray-500 font-semibold uppercase tracking-wider block mb-1">Meta Keywords</span>
                            <p class="font-medium text-gray-800 leading-normal">{{ $page->meta_keywords ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <span class="text-gray-500 font-semibold uppercase tracking-wider block mb-1">Canonical URL</span>
                            <p class="font-medium text-blue-500 hover:underline leading-normal truncate">{{ $page->canonical_url ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <span class="text-gray-500 font-semibold uppercase tracking-wider block mb-1">Schema Markup Type</span>
                            <p class="font-extrabold text-slate-800 leading-normal">{{ $page->schema_type ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <span class="text-gray-500 font-semibold uppercase tracking-wider block mb-1">Meta Description</span>
                            <p class="text-gray-600 leading-relaxed bg-gray-50 border p-3 rounded-lg">{{ $page->meta_description ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
