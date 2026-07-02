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
                            <span class="text-gray-900 font-medium">Blogs Dashboard</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <h1 class="text-xl font-bold text-gray-900 font-sans">Blogs Dashboard</h1>
        </div>

        <!-- Summary Cards Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
            <!-- Total Blogs -->
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
                <div>
                    <span class="text-xs text-gray-500 font-semibold uppercase tracking-wider block">Total Blogs</span>
                    <span class="text-2xl font-black text-gray-900 mt-1 block">{{ $stats['total_blogs'] }}</span>
                </div>
                <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center text-lg shadow-sm">
                    <i class="fas fa-newspaper"></i>
                </div>
            </div>

            <!-- Published Blogs -->
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
                <div>
                    <span class="text-xs text-gray-500 font-semibold uppercase tracking-wider block">Published</span>
                    <span class="text-2xl font-black text-green-600 mt-1 block">{{ $stats['published_blogs'] }}</span>
                </div>
                <div class="w-12 h-12 bg-green-50 text-green-600 rounded-xl flex items-center justify-center text-lg shadow-sm">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>

            <!-- Draft Blogs -->
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
                <div>
                    <span class="text-xs text-gray-500 font-semibold uppercase tracking-wider block">Drafts</span>
                    <span class="text-2xl font-black text-yellow-600 mt-1 block">{{ $stats['draft_blogs'] }}</span>
                </div>
                <div class="w-12 h-12 bg-yellow-50 text-yellow-600 rounded-xl flex items-center justify-center text-lg shadow-sm">
                    <i class="fas fa-edit"></i>
                </div>
            </div>

            <!-- Scheduled Blogs -->
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
                <div>
                    <span class="text-xs text-gray-500 font-semibold uppercase tracking-wider block">Scheduled</span>
                    <span class="text-2xl font-black text-purple-600 mt-1 block">{{ $stats['scheduled_blogs'] }}</span>
                </div>
                <div class="w-12 h-12 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center text-lg shadow-sm">
                    <i class="fas fa-clock"></i>
                </div>
            </div>

            <!-- Featured Blogs -->
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
                <div>
                    <span class="text-xs text-gray-500 font-semibold uppercase tracking-wider block">Featured</span>
                    <span class="text-2xl font-black text-red-500 mt-1 block">{{ $stats['featured_blogs'] }}</span>
                </div>
                <div class="w-12 h-12 bg-red-50 text-red-500 rounded-xl flex items-center justify-center text-lg shadow-sm">
                    <i class="fas fa-star"></i>
                </div>
            </div>

            <!-- Total Categories -->
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
                <div>
                    <span class="text-xs text-gray-500 font-semibold uppercase tracking-wider block">Categories</span>
                    <span class="text-2xl font-black text-gray-900 mt-1 block">{{ $stats['total_categories'] }}</span>
                </div>
                <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center text-lg shadow-sm">
                    <i class="fas fa-folder"></i>
                </div>
            </div>

            <!-- Total Tags -->
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
                <div>
                    <span class="text-xs text-gray-500 font-semibold uppercase tracking-wider block">Tags</span>
                    <span class="text-2xl font-black text-gray-900 mt-1 block">{{ $stats['total_tags'] }}</span>
                </div>
                <div class="w-12 h-12 bg-teal-50 text-teal-600 rounded-xl flex items-center justify-center text-lg shadow-sm">
                    <i class="fas fa-tags"></i>
                </div>
            </div>

            <!-- Pending Comments -->
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
                <div>
                    <span class="text-xs text-gray-500 font-semibold uppercase tracking-wider block">Pending Comments</span>
                    <span class="text-2xl font-black text-amber-600 mt-1 block">{{ $stats['pending_comments'] }}</span>
                </div>
                <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center text-lg shadow-sm">
                    <i class="fas fa-comment-dots"></i>
                </div>
            </div>

            <!-- Approved Comments -->
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
                <div>
                    <span class="text-xs text-gray-500 font-semibold uppercase tracking-wider block font-sans">Approved Comments</span>
                    <span class="text-2xl font-black text-emerald-600 mt-1 block">{{ $stats['approved_comments'] }}</span>
                </div>
                <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center text-lg shadow-sm">
                    <i class="fas fa-comments"></i>
                </div>
            </div>

            <!-- Total Blog Views -->
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
                <div>
                    <span class="text-xs text-gray-500 font-semibold uppercase tracking-wider block">Total Views</span>
                    <span class="text-2xl font-black text-sky-600 mt-1 block">{{ number_format($stats['total_views']) }}</span>
                </div>
                <div class="w-12 h-12 bg-sky-50 text-sky-600 rounded-xl flex items-center justify-center text-lg shadow-sm">
                    <i class="fas fa-eye"></i>
                </div>
            </div>
        </div>

        <!-- Lists Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Blogs -->
            <div class="bg-white shadow border border-gray-100 rounded-2xl p-5">
                <div class="flex items-center justify-between border-b pb-3 mb-4">
                    <h2 class="text-sm font-black text-gray-900 uppercase tracking-wider">Recent Blog Posts</h2>
                    <a href="{{ route('blogs.index') }}" class="text-xs text-blue-600 font-bold hover:underline">View All</a>
                </div>
                <div class="space-y-4">
                    @forelse ($recentBlogs as $blog)
                        <div class="flex items-center gap-3 pb-3 border-b border-gray-50 last:border-0 last:pb-0">
                            <div class="w-12 h-12 rounded-lg overflow-hidden bg-gray-100 flex-shrink-0 shadow-sm">
                                @if ($blog->featured_image)
                                    <img src="{{ asset('images/blogs/featured/' . $blog->featured_image) }}" alt="Featured" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-400 bg-gray-50 text-xs">No Cover</div>
                                @endif
                            </div>
                            <div class="min-w-0 flex-1">
                                <a href="{{ route('blogs.show', $blog->id) }}" class="text-xs font-bold text-gray-800 hover:text-blue-600 block truncate">{{ $blog->title }}</a>
                                <div class="flex items-center gap-2 mt-1 text-[10px] text-gray-500">
                                    <span class="bg-blue-50 text-blue-700 px-2 py-0.5 rounded font-medium">{{ $blog->category ? $blog->category->category_name : 'N/A' }}</span>
                                    <span>• By {{ $blog->author }}</span>
                                    <span>• {{ $blog->view_count }} views</span>
                                </div>
                            </div>
                            <div>
                                @php
                                    $statusClass = 'bg-yellow-100 text-yellow-800';
                                    if ($blog->status === 'Published') $statusClass = 'bg-green-100 text-green-800';
                                    if ($blog->status === 'Scheduled') $statusClass = 'bg-purple-100 text-purple-800';
                                @endphp
                                <span class="px-2 py-0.5 text-[9px] font-bold rounded-full {{ $statusClass }}">{{ $blog->status }}</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-gray-500 text-center py-4">No recent blogs found.</p>
                    @endforelse
                </div>
            </div>

            <!-- Most Viewed Blogs -->
            <div class="bg-white shadow border border-gray-100 rounded-2xl p-5">
                <div class="flex items-center justify-between border-b pb-3 mb-4">
                    <h2 class="text-sm font-black text-gray-900 uppercase tracking-wider">Most Viewed Blogs</h2>
                    <a href="{{ route('blogs.index') }}" class="text-xs text-blue-600 font-bold hover:underline">View All</a>
                </div>
                <div class="space-y-4">
                    @forelse ($mostViewedBlogs as $blog)
                        <div class="flex items-center gap-3 pb-3 border-b border-gray-50 last:border-0 last:pb-0">
                            <div class="w-12 h-12 rounded-lg overflow-hidden bg-gray-100 flex-shrink-0 shadow-sm">
                                @if ($blog->featured_image)
                                    <img src="{{ asset('images/blogs/featured/' . $blog->featured_image) }}" alt="Featured" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-400 bg-gray-50 text-xs">No Cover</div>
                                @endif
                            </div>
                            <div class="min-w-0 flex-1">
                                <a href="{{ route('blogs.show', $blog->id) }}" class="text-xs font-bold text-gray-800 hover:text-blue-600 block truncate">{{ $blog->title }}</a>
                                <div class="flex items-center gap-2 mt-1 text-[10px] text-gray-500">
                                    <span class="bg-blue-50 text-blue-700 px-2 py-0.5 rounded font-medium">{{ $blog->category ? $blog->category->category_name : 'N/A' }}</span>
                                    <span>• By {{ $blog->author }}</span>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-xs font-black text-sky-600"><i class="far fa-eye mr-1"></i>{{ $blog->view_count }}</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-gray-500 text-center py-4">No data available.</p>
                    @endforelse
                </div>
            </div>

            <!-- Recent Comments -->
            <div class="bg-white shadow border border-gray-100 rounded-2xl p-5">
                <div class="flex items-center justify-between border-b pb-3 mb-4">
                    <h2 class="text-sm font-black text-gray-900 uppercase tracking-wider">Recent Comments</h2>
                    <a href="{{ route('blogComments.index') }}" class="text-xs text-blue-600 font-bold hover:underline">Manage</a>
                </div>
                <div class="space-y-4">
                    @forelse ($recentComments as $comment)
                        <div class="pb-3 border-b border-gray-50 last:border-0 last:pb-0">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-gray-800">{{ $comment->name }}</span>
                                <span class="text-[10px] text-gray-400">{{ $comment->created_at ? $comment->created_at->diffForHumans() : '' }}</span>
                            </div>
                            <p class="text-[11px] text-gray-500 mt-1 italic line-clamp-2">"{{ $comment->comment }}"</p>
                            <div class="flex items-center justify-between mt-2">
                                <a href="{{ route('blogs.show', $comment->blog_id) }}" class="text-[9px] font-semibold text-blue-500 hover:underline">On: {{ $comment->blog ? $comment->blog->title : 'N/A' }}</a>
                                @php
                                    $badge = 'bg-yellow-100 text-yellow-800';
                                    if ($comment->status === 'Approved') $badge = 'bg-green-100 text-green-800';
                                    if ($comment->status === 'Rejected') $badge = 'bg-red-100 text-red-800';
                                @endphp
                                <span class="px-2 py-0.5 text-[8px] font-extrabold rounded-full {{ $badge }} uppercase">{{ $comment->status }}</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-gray-500 text-center py-4">No recent comments.</p>
                    @endforelse
                </div>
            </div>

            <!-- Latest Published Blogs -->
            <div class="bg-white shadow border border-gray-100 rounded-2xl p-5">
                <div class="flex items-center justify-between border-b pb-3 mb-4">
                    <h2 class="text-sm font-black text-gray-900 uppercase tracking-wider">Latest Published Articles</h2>
                    <a href="{{ route('blogs.index') }}" class="text-xs text-blue-600 font-bold hover:underline">View All</a>
                </div>
                <div class="space-y-4">
                    @forelse ($latestPublished as $blog)
                        <div class="flex items-center gap-3 pb-3 border-b border-gray-50 last:border-0 last:pb-0">
                            <div class="w-12 h-12 rounded-lg overflow-hidden bg-gray-100 flex-shrink-0 shadow-sm">
                                @if ($blog->featured_image)
                                    <img src="{{ asset('images/blogs/featured/' . $blog->featured_image) }}" alt="Featured" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-400 bg-gray-50 text-xs">No Cover</div>
                                @endif
                            </div>
                            <div class="min-w-0 flex-1">
                                <a href="{{ route('blogs.show', $blog->id) }}" class="text-xs font-bold text-gray-800 hover:text-blue-600 block truncate">{{ $blog->title }}</a>
                                <div class="flex items-center gap-2 mt-1 text-[10px] text-gray-500">
                                    <span class="bg-blue-50 text-blue-700 px-2 py-0.5 rounded font-medium">{{ $blog->category ? $blog->category->category_name : 'N/A' }}</span>
                                    <span>• {{ $blog->published_at ? $blog->published_at->format('M d, Y') : '-' }}</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-gray-500 text-center py-4">No published blogs found.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
