@include('layouts.header')

<body class="bg-slate-50 text-slate-800 antialiased overflow-x-hidden" x-data="{ sidebarOpen: window.innerWidth >= 1024 }">

    <!-- Mobile Sidebar Backdrop Overlay -->
    <div x-show="sidebarOpen" @click="sidebarOpen = false" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-slate-900/50 z-30 lg:hidden">
    </div>

    <div class="flex min-h-screen bg-slate-50 overflow-x-hidden">

        <!-- ================= LEFT SIDEBAR ================= -->
        <aside id="sidebar" class="fixed inset-y-0 left-0 w-64 bg-[#1a1a24] text-[#b5b5c3] border-r border-[#282836] z-40
           transition-all duration-300 ease-in-out shadow-2xl flex flex-col transform"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">

            <!-- Logo -->
            <div class="h-20 flex flex-col items-center justify-center border-b border-[#282836] px-6 gap-1">
                <a href="{{ route('dashboard') }}" class="flex items-center justify-center">
                    @if(session('logo'))
                        <img src="{{ asset('images/logo/' . session('logo')) }}" alt="Logo" class="h-10 object-contain brightness-110">
                    @else
                        <span class="text-white font-black text-lg tracking-wider">Hommlie Shop</span>
                    @endif
                </a>
            </div>

            <!-- ADMIN/VENDOR Pill Badge -->
            <div class="px-4 py-3 border-b border-[#282836]/60 bg-[#15151e]/50">
                @if(auth()->user()->isVendor())
                    <div class="flex items-center justify-center gap-2 bg-gradient-to-r from-yellow-500 to-amber-400 text-slate-950 font-extrabold text-xs uppercase tracking-wider py-2 px-4 rounded-xl shadow-lg border border-yellow-200/20">
                        <i class="fas fa-store text-[10px]"></i>
                        VENDOR
                    </div>
                @else
                    <div class="flex items-center justify-center gap-2 bg-gradient-to-r from-amber-400 to-yellow-300 text-slate-900 font-extrabold text-xs uppercase tracking-wider py-2 px-4 rounded-xl shadow-lg border border-yellow-200/20">
                        <i class="fas fa-key text-[10px]"></i>
                        ADMIN
                    </div>
                @endif
            </div>

            <!-- Menu -->
            <nav class="flex-1 p-4 space-y-5 text-sm font-semibold overflow-y-auto custom-scrollbar">

                @if(auth()->user()->isVendor())
                    <!-- SECTION: VENDOR PORTAL -->
                    <div class="space-y-1">
                        <div class="px-4 py-1.5 text-[9px] font-extrabold uppercase tracking-widest text-[#62627e]/70 bg-[#15151e]/30 rounded-md">
                            Vendor Portal
                        </div>

                        <a href="{{ route('dashboard') }}" class="group flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all duration-200
                            {{ request()->is('dashboard') || request()->is('panel/dashboard')
                                ? 'bg-[#d84e55] text-white shadow-lg border-l-4 border-[#ffb1b5] pl-3'
                                : 'text-[#b5b5c3] hover:text-white hover:bg-white/5' }}">
                            <i class="fas fa-chart-line text-base transition-transform group-hover:scale-110"></i>
                            <span>Dashboard</span>
                        </a>

                        <a href="{{ route('vendor.verify') }}" class="group flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all duration-200
                            {{ request()->is('panel/vendor/verify')
                                ? 'bg-[#d84e55] text-white shadow-lg border-l-4 border-[#ffb1b5] pl-3'
                                : 'text-[#b5b5c3] hover:text-white hover:bg-white/5' }}">
                            <i class="fas fa-user-cog text-base transition-transform group-hover:scale-110"></i>
                            <span>Profile</span>
                        </a>

                        @if(auth()->user()->profile_status === 'Approved')
                            <!-- Manage Orders -->
                            <div x-data="{ open: {{ Request::is('orders*') || Request::is('cab-orders*') || Request::is('panel/cab-orders*') ? 'true' : 'false' }} }">
                                <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-2.5 rounded-xl transition-all duration-200
                                    {{ Request::is('orders*') || Request::is('cab-orders*') || Request::is('panel/cab-orders*')
                                        ? 'bg-[#d84e55] text-white shadow-lg border-l-4 border-[#ffb1b5] pl-3'
                                        : 'text-[#b5b5c3] hover:text-white hover:bg-white/5' }}">
                                    <div class="flex items-center gap-3">
                                        <i class="fas fa-ticket-alt text-base"></i>
                                        <span>Manage Orders</span>
                                    </div>
                                    <i class="fas fa-chevron-down text-xs transition-transform duration-200"
                                        :class="open ? 'rotate-180' : ''"></i>
                                </button>

                                <div x-show="open" x-transition class="mt-1 pl-4 space-y-1 border-l border-[#282836]/60 ml-6">
                                    <a href="{{ route('cabOrders') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150
                                        {{ Request::is('cab-orders') || (Request::is('cab-orders/*') && !Request::is('cab-orders/create')) || Request::is('panel/cab-orders')
                                            ? 'bg-[#d84e55]/10 text-[#d84e55] font-bold border-l-2 border-[#d84e55] pl-2'
                                            : 'text-[#92929f] hover:text-white hover:bg-white/5' }}">
                                        <i class="fas fa-taxi text-[10px]"></i>
                                        Cab Bookings
                                    </a>
                                </div>
                            </div>

                            <!-- Car Fleet -->
                            <a href="{{ route('car') }}" class="group flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all duration-200
                                {{ request()->is('car') || request()->is('panel/car')
                                    ? 'bg-[#d84e55] text-white shadow-lg border-l-4 border-[#ffb1b5] pl-3'
                                    : 'text-[#b5b5c3] hover:text-white hover:bg-white/5' }}">
                                <i class="fas fa-car text-base transition-transform group-hover:scale-110"></i>
                                <span>Car Fleet</span>
                            </a>
                        @endif
                    </div>
                @else
                    <!-- SECTION 1: CORE PORTAL -->
                    <div class="space-y-1">
                        <div class="px-4 py-1.5 text-[9px] font-extrabold uppercase tracking-widest text-[#62627e]/70 bg-[#15151e]/30 rounded-md">
                            Core Portal
                        </div>

                        @can('dashboard')
                            <a href="{{ route('dashboard') }}" class="group flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all duration-200
                                {{ request()->is('dashboard') || request()->is('panel/dashboard')
                                    ? 'bg-[#d84e55] text-white shadow-lg border-l-4 border-[#ffb1b5] pl-3'
                                    : 'text-[#b5b5c3] hover:text-white hover:bg-white/5' }}">
                                <i class="fas fa-chart-line text-base transition-transform group-hover:scale-110"></i>
                                <span>Dashboard</span>
                            </a>
                        @endcan

                    @can('manage_orders')
                        <div x-data="{ open: {{ Request::is('orders*') || Request::is('cab-orders*') || Request::is('panel/cab-orders*') ? 'true' : 'false' }} }">
                            <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-2.5 rounded-xl transition-all duration-200
                                {{ Request::is('orders*') || Request::is('cab-orders*') || Request::is('panel/cab-orders*')
                                    ? 'bg-[#d84e55] text-white shadow-lg border-l-4 border-[#ffb1b5] pl-3'
                                    : 'text-[#b5b5c3] hover:text-white hover:bg-white/5' }}">
                                <div class="flex items-center gap-3">
                                    <i class="fas fa-ticket-alt text-base"></i>
                                    <span>Manage Orders</span>
                                </div>
                                <i class="fas fa-chevron-down text-xs transition-transform duration-200"
                                    :class="open ? 'rotate-180' : ''"></i>
                            </button>

                            <div x-show="open" x-transition class="mt-1 pl-4 space-y-1 border-l border-[#282836]/60 ml-6">
                                <a href="{{ route('cabOrders') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150
                                    {{ Request::is('cab-orders') || (Request::is('cab-orders/*') && !Request::is('cab-orders/create')) || Request::is('panel/cab-orders')
                                        ? 'bg-[#d84e55]/10 text-[#d84e55] font-bold border-l-2 border-[#d84e55] pl-2'
                                        : 'text-[#92929f] hover:text-white hover:bg-white/5' }}">
                                    <i class="fas fa-taxi text-[10px]"></i>
                                    Cab Bookings
                                </a>
                                <a href="{{ route('cabOrders.create') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150
                                    {{ Request::is('cab-orders/create') || Request::is('panel/cab-orders/create')
                                        ? 'bg-[#d84e55]/10 text-[#d84e55] font-bold border-l-2 border-[#d84e55] pl-2'
                                        : 'text-[#92929f] hover:text-white hover:bg-white/5' }}">
                                    <i class="fas fa-plus-circle text-[10px]"></i>
                                    New Booking
                                </a>
                            </div>
                        </div>
                    @endcan
                </div>

                @if(auth()->user()->roles && strtolower(auth()->user()->roles->title) === 'admin')
                <!-- SECTION 2: FLEET & CUSTOMIZATION -->
                <div class="space-y-1">
                    <div class="px-4 py-1.5 text-[9px] font-extrabold uppercase tracking-widest text-[#62627e]/70 bg-[#15151e]/30 rounded-md">
                        Fleet & Inventory
                    </div>

                    @can('charges_type')
                        <a href="{{ route('chargesType') }}" class="group flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all duration-200
                            {{ request()->is('chargesType') || request()->is('panel/chargesType')
                                ? 'bg-[#d84e55] text-white shadow-lg border-l-4 border-[#ffb1b5] pl-3'
                                : 'text-[#b5b5c3] hover:text-white hover:bg-white/5' }}">
                            <i class="fas fa-tags text-base transition-transform group-hover:scale-110"></i>
                            <span>Charges Type</span>
                        </a>
                    @endcan

                    @can('car')
                        <a href="{{ route('car') }}" class="group flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all duration-200
                            {{ request()->is('car') || request()->is('panel/car')
                                ? 'bg-[#d84e55] text-white shadow-lg border-l-4 border-[#ffb1b5] pl-3'
                                : 'text-[#b5b5c3] hover:text-white hover:bg-white/5' }}">
                            <i class="fas fa-car text-base transition-transform group-hover:scale-110"></i>
                            <span>Car Fleet</span>
                        </a>
                    @endcan

                    @can('car_type')
                        <a href="{{ route('carType') }}" class="group flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all duration-200
                            {{ request()->is('carType') || request()->is('panel/carType')
                                ? 'bg-[#d84e55] text-white shadow-lg border-l-4 border-[#ffb1b5] pl-3'
                                : 'text-[#b5b5c3] hover:text-white hover:bg-white/5' }}">
                            <i class="fas fa-bus-alt text-base transition-transform group-hover:scale-110"></i>
                            <span>Car Type</span>
                        </a>
                    @endcan

                    @can('sliders')
                        <a href="{{ route('sliders.index') }}" class="group flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all duration-200
                            {{ request()->is('sliders*') || request()->is('panel/sliders*')
                                ? 'bg-[#d84e55] text-white shadow-lg border-l-4 border-[#ffb1b5] pl-3'
                                : 'text-[#b5b5c3] hover:text-white hover:bg-white/5' }}">
                            <i class="fas fa-images text-base transition-transform group-hover:scale-110"></i>
                            <span>Sliders</span>
                        </a>
                    @endcan
                </div>

                <!-- SECTION 2.5: BLOG MANAGEMENT -->
                <div class="space-y-1">
                    <div class="px-4 py-1.5 text-[9px] font-extrabold uppercase tracking-widest text-[#62627e]/70 bg-[#15151e]/30 rounded-md">
                        Blog Portal
                    </div>

                    @can('blogs_dashboard')
                        <div x-data="{ open: {{ Request::is('panel/blogs*') || Request::is('panel/blog-categories*') || Request::is('panel/blog-tags*') || Request::is('panel/blog-comments*') || Request::is('panel/blog-settings*') ? 'true' : 'false' }} }">
                            <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-2.5 rounded-xl transition-all duration-200
                                {{ Request::is('panel/blogs*') || Request::is('panel/blog-categories*') || Request::is('panel/blog-tags*') || Request::is('panel/blog-comments*') || Request::is('panel/blog-settings*')
                                    ? 'bg-[#d84e55] text-white shadow-lg border-l-4 border-[#ffb1b5] pl-3'
                                    : 'text-[#b5b5c3] hover:text-white hover:bg-white/5' }}">
                                <div class="flex items-center gap-3">
                                    <i class="fas fa-blog text-base"></i>
                                    <span>Blog Management</span>
                                </div>
                                <i class="fas fa-chevron-down text-xs transition-transform duration-200"
                                    :class="open ? 'rotate-180' : ''"></i>
                            </button>

                            <div x-show="open" x-transition class="mt-1 pl-4 space-y-1 border-l border-[#282836]/60 ml-6">
                                @can('blogs_dashboard')
                                    <a href="{{ route('blogs.dashboard') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150
                                        {{ Request::routeIs('blogs.dashboard')
                                            ? 'bg-[#d84e55]/10 text-[#d84e55] font-bold border-l-2 border-[#d84e55] pl-2'
                                            : 'text-[#92929f] hover:text-white hover:bg-white/5' }}">
                                        <i class="fas fa-chart-pie text-[10px]"></i>
                                        Blogs Dashboard
                                    </a>
                                @endcan

                                @can('blogs')
                                    <a href="{{ route('blogs.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150
                                        {{ Request::routeIs('blogs.index') || Request::routeIs('blogs.show')
                                            ? 'bg-[#d84e55]/10 text-[#d84e55] font-bold border-l-2 border-[#d84e55] pl-2'
                                            : 'text-[#92929f] hover:text-white hover:bg-white/5' }}">
                                        <i class="fas fa-list text-[10px]"></i>
                                        All Blogs
                                    </a>
                                    <a href="{{ route('blogs.create') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150
                                        {{ Request::routeIs('blogs.create')
                                            ? 'bg-[#d84e55]/10 text-[#d84e55] font-bold border-l-2 border-[#d84e55] pl-2'
                                            : 'text-[#92929f] hover:text-white hover:bg-white/5' }}">
                                        <i class="fas fa-plus text-[10px]"></i>
                                        Add New Blog
                                    </a>
                                @endcan

                                @can('blog_categories')
                                    <a href="{{ route('blogCategories.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150
                                        {{ Request::routeIs('blogCategories.index')
                                            ? 'bg-[#d84e55]/10 text-[#d84e55] font-bold border-l-2 border-[#d84e55] pl-2'
                                            : 'text-[#92929f] hover:text-white hover:bg-white/5' }}">
                                        <i class="fas fa-folder text-[10px]"></i>
                                        Categories
                                    </a>
                                @endcan

                                @can('blog_tags')
                                    <a href="{{ route('blogTags.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150
                                        {{ Request::routeIs('blogTags.index')
                                            ? 'bg-[#d84e55]/10 text-[#d84e55] font-bold border-l-2 border-[#d84e55] pl-2'
                                            : 'text-[#92929f] hover:text-white hover:bg-white/5' }}">
                                        <i class="fas fa-tags text-[10px]"></i>
                                        Tags
                                    </a>
                                @endcan

                                @can('blog_comments')
                                    <a href="{{ route('blogComments.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150
                                        {{ Request::routeIs('blogComments.index')
                                            ? 'bg-[#d84e55]/10 text-[#d84e55] font-bold border-l-2 border-[#d84e55] pl-2'
                                            : 'text-[#92929f] hover:text-white hover:bg-white/5' }}">
                                        <i class="fas fa-comments text-[10px]"></i>
                                        Comments
                                    </a>
                                @endcan

                                @can('blog_settings')
                                    <a href="{{ route('blogSettings.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150
                                        {{ Request::routeIs('blogSettings.index')
                                            ? 'bg-[#d84e55]/10 text-[#d84e55] font-bold border-l-2 border-[#d84e55] pl-2'
                                            : 'text-[#92929f] hover:text-white hover:bg-white/5' }}">
                                        <i class="fas fa-cogs text-[10px]"></i>
                                        Blog Settings
                                    </a>
                                @endcan
                            </div>
                        </div>
                    @endcan
                </div>

                <!-- SECTION 2.6: SEO LANDING PAGES -->
                <div class="space-y-1">
                    <div class="px-4 py-1.5 text-[9px] font-extrabold uppercase tracking-widest text-[#62627e]/70 bg-[#15151e]/30 rounded-md">
                        SEO Portal
                    </div>

                    @can('seo_dashboard')
                        <div x-data="{ open: {{ Request::is('panel/seo*') || Request::is('panel/seo-service-categories*') || Request::is('panel/seo-states*') || Request::is('panel/seo-cities*') || Request::is('panel/seo-routes*') || Request::is('panel/seo-faqs*') || Request::is('panel/seo-settings*') ? 'true' : 'false' }}, openMasters: false }">
                            <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-2.5 rounded-xl transition-all duration-200
                                {{ Request::is('panel/seo*') || Request::is('panel/seo-service-categories*') || Request::is('panel/seo-states*') || Request::is('panel/seo-cities*') || Request::is('panel/seo-routes*') || Request::is('panel/seo-faqs*') || Request::is('panel/seo-settings*')
                                    ? 'bg-[#d84e55] text-white shadow-lg border-l-4 border-[#ffb1b5] pl-3'
                                    : 'text-[#b5b5c3] hover:text-white hover:bg-white/5' }}">
                                <div class="flex items-center gap-3">
                                    <i class="fas fa-globe-americas text-base"></i>
                                    <span>SEO Landing Pages</span>
                                </div>
                                <i class="fas fa-chevron-down text-xs transition-transform duration-200"
                                    :class="open ? 'rotate-180' : ''"></i>
                            </button>

                            <div x-show="open" x-transition class="mt-1 pl-4 space-y-1 border-l border-[#282836]/60 ml-6">
                                @can('seo_dashboard')
                                    <a href="{{ route('seoPages.dashboard') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150
                                        {{ Request::routeIs('seoPages.dashboard')
                                            ? 'bg-[#d84e55]/10 text-[#d84e55] font-bold border-l-2 border-[#d84e55] pl-2'
                                            : 'text-[#92929f] hover:text-white hover:bg-white/5' }}">
                                        <i class="fas fa-chart-pie text-[10px]"></i>
                                        Dashboard
                                    </a>
                                @endcan

                                @can('seo_pages')
                                    <a href="{{ route('seoPages.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150
                                        {{ Request::routeIs('seoPages.index') || Request::routeIs('seoPages.show')
                                            ? 'bg-[#d84e55]/10 text-[#d84e55] font-bold border-l-2 border-[#d84e55] pl-2'
                                            : 'text-[#92929f] hover:text-white hover:bg-white/5' }}">
                                        <i class="fas fa-list text-[10px]"></i>
                                        All SEO Pages
                                    </a>
                                    <a href="{{ route('seoPages.create') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150
                                        {{ Request::routeIs('seoPages.create')
                                            ? 'bg-[#d84e55]/10 text-[#d84e55] font-bold border-l-2 border-[#d84e55] pl-2'
                                            : 'text-[#92929f] hover:text-white hover:bg-white/5' }}">
                                        <i class="fas fa-plus text-[10px]"></i>
                                        Add New SEO Page
                                    </a>
                                @endcan

                                <!-- Masters Submenu -->
                                <div>
                                    <button type="button" @click="openMasters = !openMasters" class="w-full flex items-center justify-between px-3 py-2 rounded-lg text-xs text-[#92929f] hover:text-white hover:bg-white/5 transition-all duration-150">
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-database text-[10px]"></i>
                                            <span>Masters</span>
                                        </div>
                                        <i class="fas fa-chevron-down text-[8px] transition-transform duration-200" :class="openMasters ? 'rotate-180' : ''"></i>
                                    </button>
                                    <div x-show="openMasters" x-transition class="mt-1 pl-3 space-y-1 border-l border-white/10 ml-2">
                                        @can('seo_service_categories')
                                            <a href="{{ route('seoServiceCategories.index') }}" class="flex items-center gap-2 px-3 py-1.5 rounded-lg text-[11px] transition-all duration-150
                                                {{ Request::routeIs('seoServiceCategories.index')
                                                    ? 'text-[#d84e55] font-bold'
                                                    : 'text-[#92929f] hover:text-white' }}">
                                                Service Categories
                                            </a>
                                        @endcan
                                        @can('seo_states')
                                            <a href="{{ route('seoStates.index') }}" class="flex items-center gap-2 px-3 py-1.5 rounded-lg text-[11px] transition-all duration-150
                                                {{ Request::routeIs('seoStates.index')
                                                    ? 'text-[#d84e55] font-bold'
                                                    : 'text-[#92929f] hover:text-white' }}">
                                                States
                                            </a>
                                        @endcan
                                        @can('seo_cities')
                                            <a href="{{ route('seoCities.index') }}" class="flex items-center gap-2 px-3 py-1.5 rounded-lg text-[11px] transition-all duration-150
                                                {{ Request::routeIs('seoCities.index')
                                                    ? 'text-[#d84e55] font-bold'
                                                    : 'text-[#92929f] hover:text-white' }}">
                                                Cities
                                            </a>
                                        @endcan
                                        @can('seo_routes')
                                            <a href="{{ route('seoRoutes.index') }}" class="flex items-center gap-2 px-3 py-1.5 rounded-lg text-[11px] transition-all duration-150
                                                {{ Request::routeIs('seoRoutes.index')
                                                    ? 'text-[#d84e55] font-bold'
                                                    : 'text-[#92929f] hover:text-white' }}">
                                                Popular Routes
                                            </a>
                                        @endcan
                                        @can('seo_faqs')
                                            <a href="{{ route('seoFaqs.index') }}" class="flex items-center gap-2 px-3 py-1.5 rounded-lg text-[11px] transition-all duration-150
                                                {{ Request::routeIs('seoFaqs.index')
                                                    ? 'text-[#d84e55] font-bold'
                                                    : 'text-[#92929f] hover:text-white' }}">
                                                FAQs
                                            </a>
                                        @endcan
                                    </div>
                                </div>

                                @can('seo_settings')
                                    <a href="{{ route('seoSettings.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all duration-150
                                        {{ Request::routeIs('seoSettings.index')
                                            ? 'bg-[#d84e55]/10 text-[#d84e55] font-bold border-l-2 border-[#d84e55] pl-2'
                                            : 'text-[#92929f] hover:text-white hover:bg-white/5' }}">
                                        <i class="fas fa-cogs text-[10px]"></i>
                                        Settings
                                    </a>
                                @endcan
                            </div>
                        </div>
                    @endcan
                </div>

                <!-- SECTION 3: SYSTEM CONTROL -->
                <div class="space-y-1">
                    <div class="px-4 py-1.5 text-[9px] font-extrabold uppercase tracking-widest text-[#62627e]/70 bg-[#15151e]/30 rounded-md">
                        System & Access
                    </div>

                    @can('access')
                        <div x-data="{ open: {{ Request::is('access*') || Request::is('panel/access*') ? 'true' : 'false' }} }">
                            <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-2.5 rounded-xl transition-all duration-200
                                {{ Request::is('access*') || Request::is('panel/access*')
                                    ? 'bg-[#d84e55] text-white shadow-lg border-l-4 border-[#ffb1b5] pl-3'
                                    : 'text-[#b5b5c3] hover:text-white hover:bg-white/5' }}">
                                <div class="flex items-center gap-3">
                                    <i class="fas fa-user-shield text-base"></i>
                                    <span>Access Permissions</span>
                                </div>
                                <i class="fas fa-chevron-down text-xs transition-transform duration-200"
                                    :class="open ? 'rotate-180' : ''"></i>
                            </button>

                            <div x-show="open" x-transition class="mt-1 pl-4 space-y-1 border-l border-[#282836]/60 ml-6">
                                <a href="{{ route('access.permissions') }}"
                                    class="block px-3 py-2 rounded-lg text-xs transition-all duration-150
                                    {{ Request::is('panel/access/permissions')
                                        ? 'bg-[#d84e55]/10 text-[#d84e55] font-bold border-l-2 border-[#d84e55] pl-2'
                                        : 'text-[#92929f] hover:text-white hover:bg-white/5' }}">
                                    Permissions
                                </a>
                                <a href="{{ route('access.roles') }}"
                                    class="block px-3 py-2 rounded-lg text-xs transition-all duration-150
                                    {{ Request::is('panel/access/roles')
                                        ? 'bg-[#d84e55]/10 text-[#d84e55] font-bold border-l-2 border-[#d84e55] pl-2'
                                        : 'text-[#92929f] hover:text-white hover:bg-white/5' }}">
                                    Roles
                                </a>
                                <a href="{{ route('access.user') }}"
                                    class="block px-3 py-2 rounded-lg text-xs transition-all duration-150
                                    {{ Request::is('panel/access/user')
                                        ? 'bg-[#d84e55]/10 text-[#d84e55] font-bold border-l-2 border-[#d84e55] pl-2'
                                        : 'text-[#92929f] hover:text-white hover:bg-white/5' }}">
                                    Users
                                </a>
                                <a href="{{ route('vendors.index') }}"
                                    class="block px-3 py-2 rounded-lg text-xs transition-all duration-150
                                    {{ Request::is('panel/vendors*')
                                        ? 'bg-[#d84e55]/10 text-[#d84e55] font-bold border-l-2 border-[#d84e55] pl-2'
                                        : 'text-[#92929f] hover:text-white hover:bg-white/5' }}">
                                    Vendor Management
                                </a>
                            </div>
                        </div>
                    @endcan

                    @can('contact_messages')
                        <a href="{{ route('contactMessages.index') }}" class="group flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all duration-200
                            {{ request()->is('panel/contact-messages*')
                                ? 'bg-[#d84e55] text-white shadow-lg border-l-4 border-[#ffb1b5] pl-3'
                                : 'text-[#b5b5c3] hover:text-white hover:bg-white/5' }}">
                            <i class="fas fa-envelope text-base transition-transform group-hover:scale-110"></i>
                            <span>Messages</span>
                        </a>
                    @endcan

                    @can('settings')
                        <a href="{{ route('settings') }}" class="group flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all duration-200
                            {{ request()->is('settings*') || request()->is('panel/settings*')
                                ? 'bg-[#d84e55] text-white shadow-lg border-l-4 border-[#ffb1b5] pl-3'
                                : 'text-[#b5b5c3] hover:text-white hover:bg-white/5' }}">
                            <i class="fas fa-cogs text-base transition-transform group-hover:scale-110"></i>
                            <span>Settings</span>
                        </a>
                    @endcan
                </div>
                @endif
                @endif

                <hr class="my-4 border-[#282836]/40">

                <a href="{{ route('logout') }}"
                    class="group flex items-center gap-3 px-4 py-2.5 rounded-xl text-rose-400 hover:bg-rose-500/10 hover:text-rose-300 transition-all duration-200">
                    <i class="fas fa-sign-out-alt text-base transition-transform group-hover:translate-x-0.5"></i>
                    <span>Logout</span>
                </a>

            </nav>
        </aside>

        <!-- ================= RIGHT CONTENT ================= -->
        <div class="flex-1 flex flex-col min-w-0 transition-all duration-300 ease-in-out"
             :class="sidebarOpen ? 'lg:ml-64' : 'lg:ml-0'">

            <!-- Header -->
            <header class="h-16 bg-white border-b border-slate-100 flex items-center justify-between px-6 shadow-sm z-30">
                <button @click="sidebarOpen = !sidebarOpen"
                    class="p-2 rounded-lg text-slate-500 hover:bg-slate-50 hover:text-slate-700 transition">
                    <i class="fas fa-bars"></i>
                </button>

                <div class="flex items-center gap-4 ml-auto">
                    <!-- Notifications -->
                    <button class="relative p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-50 rounded-full transition">
                        <i class="far fa-bell text-lg"></i>
                        <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-[#d84e55] rounded-full ring-2 ring-white"></span>
                    </button>
                    
                    <!-- User Profile Info with Interactive Dropdown -->
                    <div class="relative" x-data="{ userMenuOpen: false }" @click.away="userMenuOpen = false">
                        <button @click="userMenuOpen = !userMenuOpen" class="flex items-center gap-2 pl-3 border-l border-slate-100 hover:bg-slate-50/50 p-1.5 rounded-xl transition duration-150 text-left focus:outline-none select-none cursor-pointer">
                            @if(auth()->user()->isVendor() && auth()->user()->company_logo)
                                <div class="w-8 h-8 rounded-full overflow-hidden ring-2 ring-yellow-400/60 shadow-sm flex-shrink-0">
                                    <img src="{{ asset('images/vendor_logos/' . basename(auth()->user()->company_logo)) }}"
                                         alt="Company Logo"
                                         class="w-full h-full object-cover">
                                </div>
                            @else
                                <div class="w-8 h-8 rounded-full bg-[#d84e55] text-white flex items-center justify-center font-bold text-xs uppercase shadow-sm ring-2 ring-red-50/50">
                                    {{ substr(auth()->user()->name ?? 'Hommlie Shop', 0, 1) }}
                                </div>
                            @endif
                            <div class="hidden md:flex flex-col leading-none">
                                <span class="text-xs font-bold text-slate-700">{{ auth()->user()->name ?? 'Hommlie Shop' }}</span>
                                <span class="text-[9px] text-[#d84e55] font-bold mt-0.5">Online</span>
                            </div>
                            <i class="fas fa-chevron-down text-[10px] text-slate-400 ml-1 transition-transform duration-200" :class="userMenuOpen ? 'rotate-180' : ''"></i>
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="userMenuOpen"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="transform opacity-100 scale-100"
                            x-transition:leave-end="transform opacity-0 scale-95"
                            class="absolute right-0 mt-2 w-48 bg-white border border-slate-100 rounded-2xl shadow-xl py-2 z-50 origin-top-right"
                            style="display: none;">
                            
                            <!-- Profile header (for mobile/compact view) -->
                            <div class="px-4 py-2 border-b border-slate-50 md:hidden">
                                <p class="text-xs font-bold text-slate-700">{{ auth()->user()->name ?? 'Hommlie Shop' }}</p>
                                <p class="text-[9px] text-[#d84e55] font-bold mt-0.5">Online</p>
                            </div>

                            @can('settings')
                            <a href="{{ route('settings') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-50 hover:text-slate-800 transition">
                                <i class="fas fa-cogs text-slate-400 text-sm w-4"></i>
                                <span>Settings</span>
                            </a>
                            @endcan

                            <!-- Logout option -->
                            <a href="{{ route('logout') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-semibold text-rose-500 hover:bg-rose-50 transition border-t border-slate-50">
                                <i class="fas fa-sign-out-alt text-rose-400 text-sm w-4"></i>
                                <span>Logout</span>
                            </a>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Content -->
            <main class="flex-1 p-6 bg-slate-50">
                @yield('content')
            </main>

            <!-- Footer -->
            <footer class="bg-white border-t border-slate-100 text-center py-4 text-xs text-slate-400 font-medium">
                © 2026 <a href="https://sbdbooking.com" class="text-[#d84e55] hover:underline">sbdbooking</a>. All Rights Reserved.
            </footer>

        </div>
    </div>

    @include('layouts.footer')
    @yield('scripts')
</body>

</html>