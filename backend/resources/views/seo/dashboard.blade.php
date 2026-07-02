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
                            <span class="text-gray-900 font-medium">SEO Dashboard</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <h1 class="text-xl font-bold text-gray-900">SEO Landing Pages Dashboard</h1>
        </div>

        <!-- Summary Cards Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
            <!-- Total SEO Pages -->
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
                <div>
                    <span class="text-xs text-gray-500 font-semibold uppercase tracking-wider block">Total Pages</span>
                    <span class="text-2xl font-black text-gray-900 mt-1 block">{{ $stats['total_pages'] }}</span>
                </div>
                <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center text-lg shadow-sm">
                    <i class="fas fa-globe"></i>
                </div>
            </div>

            <!-- Published Pages -->
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
                <div>
                    <span class="text-xs text-gray-500 font-semibold uppercase tracking-wider block font-sans">Published</span>
                    <span class="text-2xl font-black text-green-600 mt-1 block">{{ $stats['published_pages'] }}</span>
                </div>
                <div class="w-12 h-12 bg-green-50 text-green-600 rounded-xl flex items-center justify-center text-lg shadow-sm">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>

            <!-- Draft Pages -->
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
                <div>
                    <span class="text-xs text-gray-500 font-semibold uppercase tracking-wider block">Drafts</span>
                    <span class="text-2xl font-black text-yellow-600 mt-1 block">{{ $stats['draft_pages'] }}</span>
                </div>
                <div class="w-12 h-12 bg-yellow-50 text-yellow-600 rounded-xl flex items-center justify-center text-lg shadow-sm">
                    <i class="fas fa-edit"></i>
                </div>
            </div>

            <!-- Featured Pages -->
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
                <div>
                    <span class="text-xs text-gray-500 font-semibold uppercase tracking-wider block">Featured</span>
                    <span class="text-2xl font-black text-purple-600 mt-1 block">{{ $stats['featured_pages'] }}</span>
                </div>
                <div class="w-12 h-12 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center text-lg shadow-sm">
                    <i class="fas fa-star"></i>
                </div>
            </div>

            <!-- Total Views -->
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
                <div>
                    <span class="text-xs text-gray-500 font-semibold uppercase tracking-wider block">Total Views</span>
                    <span class="text-2xl font-black text-sky-600 mt-1 block">{{ number_format($stats['total_views']) }}</span>
                </div>
                <div class="w-12 h-12 bg-sky-50 text-sky-600 rounded-xl flex items-center justify-center text-lg shadow-sm">
                    <i class="fas fa-eye"></i>
                </div>
            </div>

            <!-- Service Categories -->
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
                <div>
                    <span class="text-xs text-gray-500 font-semibold uppercase tracking-wider block">Categories</span>
                    <span class="text-2xl font-black text-gray-900 mt-1 block">{{ $stats['total_categories'] }}</span>
                </div>
                <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center text-lg shadow-sm">
                    <i class="fas fa-folder"></i>
                </div>
            </div>

            <!-- Cities -->
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
                <div>
                    <span class="text-xs text-gray-500 font-semibold uppercase tracking-wider block font-sans">Total Cities</span>
                    <span class="text-2xl font-black text-gray-900 mt-1 block">{{ $stats['total_cities'] }}</span>
                </div>
                <div class="w-12 h-12 bg-teal-50 text-teal-600 rounded-xl flex items-center justify-center text-lg shadow-sm">
                    <i class="fas fa-city"></i>
                </div>
            </div>

            <!-- Popular Routes -->
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
                <div>
                    <span class="text-xs text-gray-500 font-semibold uppercase tracking-wider block">Routes</span>
                    <span class="text-2xl font-black text-amber-600 mt-1 block">{{ $stats['total_routes'] }}</span>
                </div>
                <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center text-lg shadow-sm">
                    <i class="fas fa-route"></i>
                </div>
            </div>

            <!-- FAQs -->
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
                <div>
                    <span class="text-xs text-gray-500 font-semibold uppercase tracking-wider block font-sans">FAQs</span>
                    <span class="text-2xl font-black text-emerald-600 mt-1 block">{{ $stats['total_faqs'] }}</span>
                </div>
                <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center text-lg shadow-sm">
                    <i class="fas fa-question-circle"></i>
                </div>
            </div>
        </div>

        <!-- Lists Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Latest Pages -->
            <div class="bg-white shadow border border-gray-100 rounded-2xl p-5">
                <h2 class="text-sm font-black text-gray-900 uppercase tracking-wider border-b pb-3 mb-4">Latest SEO Pages</h2>
                <div class="space-y-4">
                    @forelse ($latestPages as $p)
                        <div class="flex items-center gap-3 pb-3 border-b border-gray-50 last:border-0 last:pb-0">
                            <div class="w-12 h-12 rounded-lg overflow-hidden bg-gray-100 flex-shrink-0 shadow-sm">
                                @if ($p->featured_image)
                                    <img src="{{ asset('images/seo/featured/' . $p->featured_image) }}" alt="Featured" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-400 bg-gray-50 text-[10px]">No Cover</div>
                                @endif
                            </div>
                            <div class="min-w-0 flex-1">
                                <a href="{{ route('seoPages.show', $p->id) }}" class="text-xs font-bold text-gray-800 hover:text-blue-600 block truncate">{{ $p->title }}</a>
                                <div class="flex items-center gap-2 mt-1 text-[10px] text-gray-500">
                                    <span class="bg-blue-50 text-blue-700 px-2 py-0.5 rounded font-medium">{{ $p->category ? $p->category->category_name : 'N/A' }}</span>
                                    <span>• {{ $p->city ? $p->city->city_name : 'N/A' }}</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-gray-500 text-center py-4">No pages found.</p>
                    @endforelse
                </div>
            </div>

            <!-- Most Viewed Pages -->
            <div class="bg-white shadow border border-gray-100 rounded-2xl p-5">
                <h2 class="text-sm font-black text-gray-900 uppercase tracking-wider border-b pb-3 mb-4">Most Viewed Pages</h2>
                <div class="space-y-4">
                    @forelse ($mostViewedPages as $p)
                        <div class="flex items-center gap-3 pb-3 border-b border-gray-50 last:border-0 last:pb-0">
                            <div class="w-12 h-12 rounded-lg overflow-hidden bg-gray-100 flex-shrink-0 shadow-sm">
                                @if ($p->featured_image)
                                    <img src="{{ asset('images/seo/featured/' . $p->featured_image) }}" alt="Featured" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-400 bg-gray-50 text-[10px]">No Cover</div>
                                @endif
                            </div>
                            <div class="min-w-0 flex-1">
                                <a href="{{ route('seoPages.show', $p->id) }}" class="text-xs font-bold text-gray-800 hover:text-blue-600 block truncate">{{ $p->title }}</a>
                                <div class="flex items-center gap-2 mt-1 text-[10px] text-gray-500">
                                    <span class="bg-blue-50 text-blue-700 px-2 py-0.5 rounded font-medium">{{ $p->category ? $p->category->category_name : 'N/A' }}</span>
                                    <span>• {{ $p->view_count }} views</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-gray-500 text-center py-4">No views recorded yet.</p>
                    @endforelse
                </div>
            </div>

            <!-- Latest Published Pages -->
            <div class="bg-white shadow border border-gray-100 rounded-2xl p-5">
                <h2 class="text-sm font-black text-gray-900 uppercase tracking-wider border-b pb-3 mb-4">Latest Published</h2>
                <div class="space-y-4">
                    @forelse ($latestPublished as $p)
                        <div class="flex items-center gap-3 pb-3 border-b border-gray-50 last:border-0 last:pb-0">
                            <div class="w-12 h-12 rounded-lg overflow-hidden bg-gray-100 flex-shrink-0 shadow-sm">
                                @if ($p->featured_image)
                                    <img src="{{ asset('images/seo/featured/' . $p->featured_image) }}" alt="Featured" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-400 bg-gray-50 text-[10px]">No Cover</div>
                                @endif
                            </div>
                            <div class="min-w-0 flex-1">
                                <a href="{{ route('seoPages.show', $p->id) }}" class="text-xs font-bold text-gray-800 hover:text-blue-600 block truncate">{{ $p->title }}</a>
                                <div class="flex items-center gap-2 mt-1 text-[10px] text-gray-500">
                                    <span>By {{ $p->author }}</span>
                                    <span>• {{ $p->published_at ? $p->published_at->format('Y-m-d') : '-' }}</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-gray-500 text-center py-4">No published pages found.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
