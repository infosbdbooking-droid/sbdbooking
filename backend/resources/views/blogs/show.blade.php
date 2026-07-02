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
                                <a href="{{ route('blogs.index') }}" class="hover:text-gray-700">All Blogs</a>
                            </div>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <i class="fas fa-chevron-right text-[10px] mx-2 text-gray-400"></i>
                                <span class="text-gray-900 font-medium">Blog Details</span>
                            </div>
                        </li>
                    </ol>
                </nav>
                <h1 class="text-xl font-bold text-gray-900">Blog Details</h1>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('blogs.index') }}" class="px-4 py-2 border border-gray-200 hover:bg-gray-50 text-gray-700 rounded-md text-xs font-bold transition">
                    <i class="fas fa-arrow-left mr-1.5"></i> Back
                </a>
                <a href="{{ route('blogs.edit', $blog->id) }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-xs font-bold transition shadow">
                    <i class="fas fa-edit mr-1.5"></i> Edit Post
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left 2 Columns: Main Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Blog Main Content Card -->
                <div class="bg-white shadow-sm border border-gray-100 rounded-xl overflow-hidden">
                    <!-- Featured Image -->
                    <div class="h-80 bg-gray-100 relative">
                        @if ($blog->featured_image)
                            <img src="{{ asset('images/blogs/featured/' . $blog->featured_image) }}" alt="Featured Image" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex flex-col items-center justify-center text-gray-400">
                                <i class="fas fa-image text-4xl mb-2"></i>
                                <span>No Featured Image Uploaded</span>
                            </div>
                        @endif
                        <div class="absolute bottom-4 left-4 flex gap-2">
                            <span class="bg-blue-600 text-white text-xs font-bold px-3 py-1 rounded-full shadow">
                                {{ $blog->category ? $blog->category->category_name : 'Uncategorized' }}
                            </span>
                            @if ($blog->featured)
                                <span class="bg-amber-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow">
                                    <i class="fas fa-star mr-1"></i> Featured
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Title & Content -->
                    <div class="p-6">
                        <h1 class="text-2xl font-black text-gray-900 leading-tight mb-2">{{ $blog->title }}</h1>
                        <p class="text-xs text-gray-500 mb-6">
                            <span><i class="far fa-user mr-1"></i> By {{ $blog->author }}</span>
                            <span class="mx-2">•</span>
                            <span><i class="far fa-calendar-alt mr-1"></i> {{ $blog->published_at ? $blog->published_at->format('M d, Y') : 'Not Published' }}</span>
                        </p>

                        @if ($blog->short_description)
                            <div class="bg-slate-50 border-l-4 border-blue-500 p-4 rounded-r-lg mb-6">
                                <p class="text-sm italic text-gray-700">{{ $blog->short_description }}</p>
                            </div>
                        @endif

                        <div class="prose max-w-none text-sm text-gray-800 leading-relaxed font-sans mt-4">
                            {!! $blog->content !!}
                        </div>
                    </div>
                </div>

                <!-- Gallery Images Card -->
                <div class="bg-white shadow-sm border border-gray-100 rounded-xl p-6">
                    <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wider border-b pb-2 mb-4">Gallery Images</h2>
                    @if ($blog->gallery_images && count($blog->gallery_images) > 0)
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                            @foreach ($blog->gallery_images as $image)
                                <a href="{{ asset('images/blogs/gallery/' . $image) }}" data-lightbox="blog-gallery" class="group block border rounded-lg overflow-hidden h-28 bg-gray-50 transition shadow-sm hover:shadow">
                                    <img src="{{ asset('images/blogs/gallery/' . $image) }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-350">
                                </a>
                            @endforeach
                        </div>
                    @else
                        <p class="text-xs text-gray-500 text-center py-4">No gallery images uploaded.</p>
                    @endif
                </div>

                <!-- Google Map Embed Card -->
                @if ($blog->google_map)
                    <div class="bg-white shadow-sm border border-gray-100 rounded-xl p-6">
                        <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wider border-b pb-2 mb-4">Location Map</h2>
                        <div class="h-64 rounded-lg overflow-hidden border">
                            <iframe src="{{ $blog->google_map }}" class="w-full h-full border-0" allowfullscreen="" loading="lazy"></iframe>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right Column: Sidebar info & SEO -->
            <div class="space-y-6">
                <!-- Statistics Card -->
                <div class="bg-white shadow-sm border border-gray-100 rounded-xl p-6">
                    <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wider border-b pb-2 mb-4">Post Metrics</h2>
                    <div class="grid grid-cols-3 gap-4 text-center">
                        <div class="bg-slate-50 p-3 rounded-lg border border-slate-100/50">
                            <span class="text-[10px] font-semibold text-gray-500 uppercase tracking-wider block">Views</span>
                            <span class="text-lg font-black text-slate-800 mt-1 block">{{ $blog->view_count }}</span>
                        </div>
                        <div class="bg-slate-50 p-3 rounded-lg border border-slate-100/50">
                            <span class="text-[10px] font-semibold text-gray-500 uppercase tracking-wider block">Likes</span>
                            <span class="text-lg font-black text-slate-800 mt-1 block">{{ $blog->like_count }}</span>
                        </div>
                        <div class="bg-slate-50 p-3 rounded-lg border border-slate-100/50">
                            <span class="text-[10px] font-semibold text-gray-500 uppercase tracking-wider block">Shares</span>
                            <span class="text-lg font-black text-slate-800 mt-1 block">{{ $blog->share_count }}</span>
                        </div>
                    </div>
                </div>

                <!-- Travel Information Card -->
                <div class="bg-white shadow-sm border border-gray-100 rounded-xl p-6">
                    <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wider border-b pb-2 mb-4">Travel Guide</h2>
                    <div class="space-y-3 text-xs">
                        <div class="flex justify-between py-1.5 border-b border-gray-50">
                            <span class="text-gray-500 font-medium">Travel Type</span>
                            <span class="font-bold text-gray-800">{{ $blog->travel_type ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-gray-50">
                            <span class="text-gray-500 font-medium">Destination</span>
                            <span class="font-bold text-gray-800">{{ $blog->destination ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-gray-50">
                            <span class="text-gray-500 font-medium">City / State</span>
                            <span class="font-bold text-gray-800">
                                @if ($blog->city || $blog->state)
                                    {{ implode(', ', array_filter([$blog->city, $blog->state])) }}
                                @else
                                    -
                                @endif
                            </span>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-gray-50">
                            <span class="text-gray-500 font-medium">Best Time to Visit</span>
                            <span class="font-bold text-gray-800">{{ $blog->best_time ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-gray-50">
                            <span class="text-gray-500 font-medium">Estimated Budget</span>
                            <span class="font-bold text-gray-800">{{ $blog->estimated_budget ? '₹' . number_format($blog->estimated_budget, 2) : '-' }}</span>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-gray-50">
                            <span class="text-gray-500 font-medium">Trip Duration</span>
                            <span class="font-bold text-gray-800">{{ $blog->trip_duration ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between py-1.5">
                            <span class="text-gray-500 font-medium">Distance</span>
                            <span class="font-bold text-gray-800">{{ $blog->distance ?? '-' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Tags Card -->
                <div class="bg-white shadow-sm border border-gray-100 rounded-xl p-6">
                    <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wider border-b pb-2 mb-4">Assigned Tags</h2>
                    <div class="flex flex-wrap gap-1.5">
                        @forelse ($blog->tags_models as $tag)
                            <span class="bg-gray-100 text-gray-800 text-xs font-semibold px-2.5 py-1 rounded">
                                # {{ $tag->tag_name }}
                            </span>
                        @empty
                            <span class="text-xs text-gray-400">No tags assigned.</span>
                        @endforelse
                    </div>
                </div>

                <!-- SEO Information Card -->
                <div class="bg-white shadow-sm border border-gray-100 rounded-xl p-6">
                    <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wider border-b pb-2 mb-4">SEO Metadata</h2>
                    <div class="space-y-4 text-xs">
                        <div>
                            <span class="text-gray-500 font-semibold uppercase tracking-wider block mb-1">SEO Title</span>
                            <p class="font-bold text-gray-800 leading-normal">{{ $blog->seo_title ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <span class="text-gray-500 font-semibold uppercase tracking-wider block mb-1">Meta Keywords</span>
                            <p class="font-medium text-gray-800 leading-normal">{{ $blog->meta_keywords ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <span class="text-gray-500 font-semibold uppercase tracking-wider block mb-1">Meta Description</span>
                            <p class="text-gray-600 leading-relaxed bg-gray-50 border p-3 rounded-lg">{{ $blog->meta_description ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
