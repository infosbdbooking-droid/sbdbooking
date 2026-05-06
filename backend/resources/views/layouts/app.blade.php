@include('layouts.header')

<body class="bg-gray-50 overflow-x-hidden">
    <div class="flex min-h-screen bg-gray-50 overflow-x-hidden">

        <!-- ================= LEFT SIDEBAR ================= -->
        <aside id="sidebar" class="fixed inset-y-0 left-0 w-64 bg-white border-r border-gray-200 z-40
           transform -translate-x-full lg:translate-x-0 transition-transform duration-300">

            <!-- Logo -->
            <div class="h-16 flex items-center justify-center border-b">
                <a href="{{ route('dashboard') }}">
                    <img src="{{ asset('images/logo/' . session('logo')) }}" alt="Logo" class="h-10">
                </a>
            </div>

            <!-- Menu -->
            <nav class="p-4 space-y-1 text-sm font-medium">

                @can('dashboard')
                            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-2 rounded-lg transition
                                                           {{ request()->is('dashboard')
                    ? 'bg-blue-600 text-white shadow'
                    : 'text-gray-600 hover:bg-blue-50' }}">
                                <i class="fas fa-home text-base"></i>
                                Dashboard
                            </a>
                @endcan

                @can('manage_orders')
                            <div x-data="{ open: {{ Request::is('orders*') || Request::is('cab-orders*') ? 'true' : 'false' }} }">
                                <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-2 rounded-lg transition
                                                                {{ Request::is('orders*') || Request::is('cab-orders*')
                    ? 'bg-blue-600 text-white shadow'
                    : 'text-gray-600 hover:bg-blue-50' }}">
                                    <div class="flex items-center gap-3">
                                        <i class="fas fa-box text-base"></i>
                                        Manage Orders
                                    </div>
                                    <i class="fas fa-chevron-down text-xs transition-transform"
                                        :class="open ? 'rotate-180' : ''"></i>
                                </button>

                                <div x-show="open" x-transition class="mt-1 pl-8 space-y-1">
                                    <a href="{{ route('cabOrders') }}" class="flex items-center gap-2 px-3 py-2 rounded-md text-sm
                                                                   {{ Request::is('cab-orders*')
                    ? 'bg-blue-100 text-blue-600'
                    : 'text-gray-600 hover:bg-blue-50' }}">
                                        <i class="fas fa-taxi"></i>
                                        Cab Bookings
                                    </a>
                                </div>
                            </div>
                @endcan

                @can('charges_type')
                            <a href="{{ route('chargesType') }}" class="flex items-center gap-3 px-4 py-2 rounded-lg transition
                                                           {{ request()->is('chargesType')
                    ? 'bg-blue-600 text-white shadow'
                    : 'text-gray-600 hover:bg-blue-50' }}">
                                <i class="fas fa-layer-group"></i>
                                Charges Type
                            </a>
                @endcan

                @can('car')
                            <a href="{{ route('car') }}" class="flex items-center gap-3 px-4 py-2 rounded-lg transition
                                                           {{ request()->is('car')
                    ? 'bg-blue-600 text-white shadow'
                    : 'text-gray-600 hover:bg-blue-50' }}">
                                <i class="fas fa-car"></i>
                                Car
                            </a>
                @endcan

                @can('car_type')
                            <a href="{{ route('carType') }}" class="flex items-center gap-3 px-4 py-2 rounded-lg transition
                                                            {{ request()->is('carType')
                    ? 'bg-blue-600 text-white shadow'
                    : 'text-gray-600 hover:bg-blue-50' }}">
                                <i class="fas fa-car-side"></i>
                                Car Type
                            </a>
                @endcan

                @can('sliders')
                            <a href="{{ route('sliders.index') }}" class="flex items-center gap-3 px-4 py-2 rounded-lg transition
                                                            {{ request()->is('sliders*')
                    ? 'bg-blue-600 text-white shadow'
                    : 'text-gray-600 hover:bg-blue-50' }}">
                                <i class="fas fa-images"></i>
                                Sliders
                            </a>
                @endcan

                @can('access')
                            <div x-data="{ open: {{ Request::is('access*') ? 'true' : 'false' }} }">
                                <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-2 rounded-lg transition
                                                                {{ Request::is('access*')
                    ? 'bg-blue-600 text-white shadow'
                    : 'text-gray-600 hover:bg-blue-50' }}">
                                    <div class="flex items-center gap-3">
                                        <i class="fas fa-user-shield"></i>
                                        Access
                                    </div>
                                    <i class="fas fa-chevron-down text-xs" :class="open ? 'rotate-180' : ''"></i>
                                </button>

                                <div x-show="open" x-transition class="mt-1 pl-8 space-y-1">
                                    <a href="{{ route('access.permissions') }}"
                                        class="block px-3 py-2 rounded-md text-sm hover:bg-blue-50">
                                        Permissions
                                    </a>
                                    <a href="{{ route('access.roles') }}"
                                        class="block px-3 py-2 rounded-md text-sm hover:bg-blue-50">
                                        Roles
                                    </a>
                                    <a href="{{ route('access.user') }}"
                                        class="block px-3 py-2 rounded-md text-sm hover:bg-blue-50">
                                        Users
                                    </a>
                                </div>
                            </div>
                @endcan

                @can('settings')
                            <a href="{{ route('settings') }}" class="flex items-center gap-3 px-4 py-2 rounded-lg transition
                                                           {{ request()->is('settings*')
                    ? 'bg-blue-600 text-white shadow'
                    : 'text-gray-600 hover:bg-blue-50' }}">
                                <i class="fas fa-cog"></i>
                                Settings
                            </a>
                @endcan

                <hr class="my-3">

                <a href="{{ url('signout') }}"
                    class="flex items-center gap-3 px-4 py-2 rounded-lg text-red-600 hover:bg-red-50">
                    <i class="fas fa-sign-out-alt"></i>
                    Logout
                </a>

            </nav>
        </aside>

        <!-- ================= RIGHT CONTENT ================= -->
        <div class="flex-1 lg:ml-64 flex flex-col min-w-0">

            <!-- Header -->
            <header class="h-16 bg-white border-b flex items-center justify-between px-6">
                <button data-drawer-target="sidebar" data-drawer-toggle="sidebar"
                    class="lg:hidden p-2 rounded hover:bg-gray-100">
                    <i class="fas fa-bars"></i>
                </button>

                <div class="hidden sm:block">
                    <h1 class="text-lg font-semibold">Tours & Trips Management Dashboard</h1>
                    <p class="text-xs text-gray-600">Manage tours, trips & reports</p>
                </div>

                <div class="flex items-center gap-4 ml-auto">
                    <i class="far fa-bell text-lg cursor-pointer"></i>
                    <i class="fas fa-user-circle text-xl cursor-pointer"></i>
                </div>
            </header>

            <!-- Content -->
            <main class="flex-1 p-6">
                @yield('content')
            </main>

            <!-- Footer -->
            <footer class="bg-white border-t text-center py-4 text-sm">
                © 2026 <a href="https://sbdbooking.com" class="text-blue-600">sbdbooking</a>
            </footer>

        </div>
    </div>

    @include('layouts.footer')
    @yield('scripts')
</body>

</html>