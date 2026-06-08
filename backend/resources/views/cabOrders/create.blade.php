@extends('layouts.app')

@section('content')
<div class="container mx-auto py-6 max-w-7xl" x-data="manualBookingApp()" @update-map-data.window="handleMapData($event.detail)">
    <!-- Header -->
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <div>
            <a href="{{ route('cabOrders') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-blue-600 transition mb-1">
                <i class="fas fa-arrow-left"></i> Back to Cab Bookings
            </a>
            <h1 class="text-2xl font-bold text-gray-800">New Manual Cab Booking</h1>
            <p class="text-sm text-gray-500">Create a professional booking from backend. You can either auto-calculate or manually override pricing.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-100 border border-green-200 text-green-800 rounded-lg shadow-sm">
            <h4 class="font-semibold text-sm mb-1">Success</h4>
            <p class="text-xs">{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 p-4 bg-red-100 border border-red-200 text-red-800 rounded-lg shadow-sm">
            <h4 class="font-semibold text-sm mb-1">Error</h4>
            <p class="text-xs">{{ session('error') }}</p>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 p-4 bg-red-100 border border-red-200 text-red-800 rounded-lg shadow-sm">
            <h4 class="font-semibold text-sm mb-1">Please fix the following validation errors:</h4>
            <ul class="list-disc pl-5 text-xs space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('cabOrders.store') }}" method="POST" @submit="submitForm($event)">
        @csrf
        
        <!-- Hidden input parameters for coordinates -->
        <input type="hidden" name="pickup_lat" value="0">
        <input type="hidden" name="pickup_lng" value="0">
        <input type="hidden" name="drop_lat" value="0">
        <input type="hidden" name="drop_lng" value="0">
        <input type="hidden" name="return_pickup_lat" value="0">
        <input type="hidden" name="return_pickup_lng" value="0">
        <input type="hidden" name="return_drop_lat" value="0">
        <input type="hidden" name="return_drop_lng" value="0">
        
        <!-- Input rates for building breakdown in controller -->
        <input type="hidden" name="per_km_rate_unit" :value="perKmRateUnit">
        <input type="hidden" name="ac_rate_unit" :value="acRateUnit">

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left 2 Columns: Details Form -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- 1. Customer Information Card -->
                <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm transition hover:shadow-md duration-300">
                    <div class="flex items-center gap-2 mb-4 pb-2 border-b border-gray-100">
                        <span class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-sm">1</span>
                        <h2 class="text-lg font-bold text-gray-800">Customer Details</h2>
                    </div>

                    <div class="mb-4">
                        <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2">Customer Type</label>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="customer_type" value="existing" x-model="customerType" class="text-blue-600 focus:ring-blue-500">
                                <span class="text-sm font-medium text-gray-700">Existing Customer</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="customer_type" value="new" x-model="customerType" class="text-blue-600 focus:ring-blue-500">
                                <span class="text-sm font-medium text-gray-700">Create New Customer / Guest</span>
                            </label>
                        </div>
                    </div>

                    <!-- Existing Customer Selector -->
                    <div x-show="customerType === 'existing'" x-transition class="relative">
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Select Existing Customer</label>
                        <div class="relative mt-1.5" @click.away="showDropdown = false">
                            <div class="flex gap-2">
                                <div class="relative flex-1">
                                    <input type="text" x-model="customerQuery" @input="searchCustomers()" @focus="showDropdown = true" placeholder="Search by name or mobile number..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                                    <i class="fas fa-search absolute right-3 top-3 text-gray-400"></i>
                                </div>
                                <button type="button" @click="clearCustomerSelection()" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs font-semibold rounded-lg transition" x-show="customerId">
                                    Clear
                                </button>
                            </div>

                            <!-- Dropdown list -->
                            <div x-show="showDropdown && filteredCustomers.length > 0" class="absolute top-full left-0 right-0 z-[100] mt-1 bg-white border border-gray-200 rounded-xl shadow-2xl max-h-60 overflow-y-auto">
                                <template x-for="cust in filteredCustomers" :key="cust.id">
                                    <div @click="selectCustomer(cust)" class="px-4 py-3 hover:bg-blue-50 cursor-pointer flex justify-between items-center transition border-b border-gray-100 last:border-b-0">
                                        <div>
                                            <div class="font-bold text-sm text-gray-800" x-text="cust.name"></div>
                                            <div class="text-xs text-gray-500 mt-0.5"><i class="fas fa-phone-alt text-[10px] mr-1"></i> <span x-text="cust.mobile"></span></div>
                                        </div>
                                        <span class="text-[10px] uppercase font-bold bg-gray-100 text-gray-600 px-2 py-1 rounded-md" x-text="cust.email || 'No email'"></span>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Selected Customer Info Box -->
                        <div x-show="customerId" class="mt-3 p-4 bg-blue-50 border border-blue-200 rounded-xl relative shadow-sm">
                            <div class="flex justify-between items-center mb-3">
                                <div class="text-xs text-blue-700 font-bold uppercase tracking-wider flex items-center gap-1.5"><i class="fas fa-user-check"></i> Selected Customer</div>
                                <div class="text-[10px] text-blue-500 font-semibold bg-white px-2 py-0.5 rounded border border-blue-100">Editable</div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-blue-900 mb-1">Update Name (Optional)</label>
                                    <input type="text" x-model="customerName" class="w-full px-3 py-1.5 border border-blue-200 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm bg-white font-bold text-gray-800 shadow-inner">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-blue-900 mb-1">Update Mobile (Optional)</label>
                                    <input type="text" x-model="customerMobile" class="w-full px-3 py-1.5 border border-blue-200 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm bg-white font-bold text-gray-800 shadow-inner">
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="customer_id" :value="customerId">
                        <template x-if="customerId">
                            <div>
                                <input type="hidden" name="customer_name" :value="customerName">
                                <input type="hidden" name="customer_mobile" :value="customerMobile">
                            </div>
                        </template>
                    </div>

                    <!-- New Customer Registration Inputs -->
                    <div x-show="customerType === 'new'" x-transition class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                            <input type="text" :disabled="customerId !== ''" name="customer_name" x-model="customerName" placeholder="e.g. John Doe" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Mobile Number <span class="text-red-500">*</span></label>
                            <input type="text" :disabled="customerId !== ''" name="customer_mobile" x-model="customerMobile" placeholder="e.g. 9876543210" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Email (Optional)</label>
                            <input type="email" name="customer_email" x-model="customerEmail" placeholder="e.g. john@example.com" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                        </div>
                    </div>
                </div>

                <!-- 2. Trip & Vehicle Configuration -->
                <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm transition hover:shadow-md duration-300">
                    <div class="flex items-center gap-2 mb-4 pb-2 border-b border-gray-100">
                        <span class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-sm">2</span>
                        <h2 class="text-lg font-bold text-gray-800">Trip & Vehicle Information</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                        <!-- Trip Type -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Trip Type <span class="text-red-500">*</span></label>
                            <div class="flex gap-2">
                                <button type="button" @click="tripType = 'one_way'; updateKm();" :class="tripType === 'one_way' ? 'bg-blue-600 text-white border-blue-600 shadow' : 'bg-white text-gray-700 border-gray-300'" class="flex-1 py-2 px-3 border rounded-lg text-sm font-semibold transition-all duration-150">
                                    One Way
                                </button>
                                <button type="button" @click="tripType = 'round_trip'; updateKm();" :class="tripType === 'round_trip' ? 'bg-blue-600 text-white border-blue-600 shadow' : 'bg-white text-gray-700 border-gray-300'" class="flex-1 py-2 px-3 border rounded-lg text-sm font-semibold transition-all duration-150">
                                    Round Trip
                                </button>
                            </div>
                            <input type="hidden" name="trip_type" :value="tripType">
                        </div>

                        <!-- Car Selector -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Vehicle Type <span class="text-red-500">*</span></label>
                            <select name="car_id" x-model="carId" @change="carSelected()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                                <option value="">-- Choose Car --</option>
                                <template x-for="car in cars" :key="car.id">
                                    <option :value="car.id" x-text="car.car_name + ' (' + (car.car_seats || 4) + ' Seats)'"></option>
                                </template>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <!-- AC / Non-AC Toggle -->
                        <div class="flex flex-col justify-end">
                            <label class="flex items-center gap-2 cursor-pointer p-2.5 bg-gray-50 border border-gray-200 rounded-lg hover:bg-gray-100 transition">
                                <input type="checkbox" name="is_ac" value="1" x-model="isAc" @change="calculateCharges()" class="text-blue-600 focus:ring-blue-500 w-4 h-4 rounded">
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-gray-700">AC Cabin</span>
                                    <span class="text-[10px] text-gray-500">AC charge applies</span>
                                </div>
                            </label>
                        </div>

                        <!-- Passengers Count -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Passengers <span class="text-red-500">*</span></label>
                            <input type="number" name="passengers" x-model="passengers" min="1" max="10" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                        </div>

                        <!-- Bags Count -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Luggage Bags</label>
                            <input type="number" name="bags" x-model="bags" min="0" max="10" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                        </div>
                    </div>

                    <!-- Car details banner if selected -->
                    <div x-show="selectedCar" x-transition class="mt-4 p-4 bg-gray-50 border border-gray-200 rounded-lg flex items-center gap-4">
                        <div class="bg-white p-2 border rounded shadow-sm w-16 h-12 flex items-center justify-center font-bold text-gray-400 uppercase text-xs" x-text="selectedCarName.substring(0, 3)"></div>
                        <div>
                            <div class="font-bold text-gray-800" x-text="selectedCarName"></div>
                            <div class="text-xs text-gray-500">
                                Max Capacity: <span class="font-semibold" x-text="selectedCarMaxPassengers || '4'"></span> Passengers, 
                                <span class="font-semibold" x-text="selectedCarMaxBags || '2'"></span> Bags. 
                                Min Trip Amount: <span class="font-semibold">₹<span x-text="selectedCarMinAmount"></span></span>.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. Pick / Drop Route & Address -->
                <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm transition hover:shadow-md duration-300">
                    <div class="flex items-center gap-2 mb-4 pb-2 border-b border-gray-100">
                        <span class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-sm">3</span>
                        <h2 class="text-lg font-bold text-gray-800">Route & Distance Details</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Pickup Address <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <input type="text" id="pickup_address" name="pickup_address" x-model="pickupAddress" placeholder="Enter pickup location" class="w-full pl-9 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                                <i class="fas fa-map-marker-alt absolute left-3 top-3 text-red-500"></i>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Drop Address <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <input type="text" id="drop_address" name="drop_address" x-model="dropAddress" placeholder="Enter drop-off destination" class="w-full pl-9 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                                <i class="fas fa-flag-checkered absolute left-3 top-3 text-green-500"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Return Trip Location Option (visible for round trip) -->
                    <div x-show="tripType === 'round_trip'" x-transition class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Return trip location</label>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="return_location_type" value="same" x-model="returnLocationType" @change="if(window.drawFullRoute) window.drawFullRoute()" class="text-blue-600 focus:ring-blue-500">
                                <span class="text-sm text-gray-700">Same as pickup & drop</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="return_location_type" value="custom" x-model="returnLocationType" @change="if(window.drawFullRoute) window.drawFullRoute()" class="text-blue-600 focus:ring-blue-500">
                                <span class="text-sm text-gray-700">Change return location</span>
                            </label>
                        </div>
                    </div>

                    <!-- Return Pickup/Drop (visible only if custom return location) -->
                    <div x-show="tripType === 'round_trip' && returnLocationType === 'custom'" x-transition class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4 p-4 bg-blue-50/50 rounded-lg border border-dashed border-blue-200">
                        <div>
                            <label class="block text-sm font-semibold text-blue-900 mb-1">Return Pickup Address</label>
                            <div class="relative">
                                <input type="text" id="return_pickup_address" name="return_pickup_address" x-model="returnPickupAddress" placeholder="Same as Drop-off or enter custom" class="w-full pl-9 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm bg-white">
                                <i class="fas fa-map-marker-alt absolute left-3 top-3 text-blue-500"></i>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-blue-900 mb-1">Return Drop Address</label>
                            <div class="relative">
                                <input type="text" id="return_drop_address" name="return_drop_address" x-model="returnDropAddress" placeholder="Same as Pickup or enter custom" class="w-full pl-9 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm bg-white">
                                <i class="fas fa-flag-checkered absolute left-3 top-3 text-blue-500"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Map Container -->
                    <div id="routeMap" class="w-full h-64 rounded-xl border border-gray-200 mb-4 overflow-hidden z-0 bg-gray-100"></div>

                    <!-- Travel Time Info Banner -->
                    <div class="mb-4 bg-gray-50 border border-gray-200 rounded-lg px-4 py-3 text-sm flex items-center justify-between" x-show="travelTime">
                        <span class="font-semibold text-gray-700">Estimated Travel Time</span>
                        <strong><span class="text-blue-700" x-text="travelTime"></span></strong>
                    </div>

                    <!-- Trip Distance Details -->
                    <div class="mt-4 bg-gray-50 border border-gray-200 rounded-lg p-4 text-sm text-gray-700 space-y-2 mb-4" x-show="totalKm > 0">
                        <div class="font-bold text-gray-800 mb-2">Trip Distance Details</div>
                        <div class="flex justify-between items-center pb-2 border-b border-gray-200">
                            <span>Pickup &rarr; Drop</span>
                            <strong><span x-text="oneWayKm"></span> km</strong>
                        </div>
                        <div class="flex justify-between items-center pb-2 border-b border-gray-200">
                            <span>Return &rarr; Pickup <span x-show="tripType === 'one_way'">(Car Return)</span></span>
                            <strong><span x-text="returnKm"></span> km</strong>
                        </div>
                        <div class="flex justify-between items-center font-bold text-gray-900 pt-1">
                            <span>Total Distance</span>
                            <span>
                                <span x-show="tripType === 'one_way'" x-text="oneWayKm + ' * 2 = ' + parseFloat(totalKm).toFixed(2) + ' km'"></span>
                                <span x-show="tripType === 'round_trip'" x-text="oneWayKm + ' + ' + returnKm + ' = ' + parseFloat(totalKm).toFixed(2) + ' km'"></span>
                            </span>
                        </div>
                    </div>
                    
                    <!-- Hidden inputs for form submission -->
                    <input type="hidden" name="one_way_km" :value="oneWayKm">
                    <input type="hidden" name="return_km" :value="returnKm">
                    <input type="hidden" name="total_km" :value="totalKm">
                </div>

                <!-- 4. Schedule & Date / Time -->
                <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm transition hover:shadow-md duration-300">
                    <div class="flex items-center gap-2 mb-4 pb-2 border-b border-gray-100">
                        <span class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-sm">4</span>
                        <h2 class="text-lg font-bold text-gray-800">Schedule Date & Time</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Pickup Date <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <input type="date" name="pickup_date" x-model="pickupDate" @change="calculateCharges()" class="w-full pl-9 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                                <i class="far fa-calendar-alt absolute left-3 top-3 text-gray-400"></i>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Pickup Time <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <select name="pickup_time" x-model="pickupTime" @change="calculateCharges()" class="w-full pl-9 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm bg-white appearance-none">
                                    <option value="06:00 AM to 08:00 AM">06:00 AM to 08:00 AM</option>
                                    <option value="08:00 AM to 10:00 AM">08:00 AM to 10:00 AM</option>
                                    <option value="10:00 AM to 12:00 PM">10:00 AM to 12:00 PM</option>
                                    <option value="12:00 PM to 02:00 PM">12:00 PM to 02:00 PM</option>
                                    <option value="02:00 PM to 04:00 PM">02:00 PM to 04:00 PM</option>
                                    <option value="04:00 PM to 06:00 PM">04:00 PM to 06:00 PM</option>
                                    <option value="06:00 PM to 08:00 PM">06:00 PM to 08:00 PM</option>
                                    <option value="08:00 PM to 10:00 PM">08:00 PM to 10:00 PM</option>
                                    <option value="10:00 PM to 12:00 AM">10:00 PM to 12:00 AM</option>
                                    <option value="12:00 AM to 06:00 AM">12:00 AM to 06:00 AM</option>
                                </select>
                                <i class="far fa-clock absolute left-3 top-3 text-gray-400"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Return Schedule (visible for round trip) -->
                    <div x-show="tripType === 'round_trip'" x-transition class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 bg-blue-50/50 rounded-lg border border-dashed border-blue-200">
                        <div>
                            <label class="block text-sm font-semibold text-blue-900 mb-1">Return Trip Pickup Date</label>
                            <div class="relative">
                                <input type="date" name="return_date" x-model="returnDate" @change="calculateCharges()" class="w-full pl-9 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm bg-white">
                                <i class="far fa-calendar-alt absolute left-3 top-3 text-blue-500"></i>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-blue-900 mb-1">Return Trip Pickup Time</label>
                            <div class="relative">
                                <select name="return_time" x-model="returnTime" @change="calculateCharges()" class="w-full pl-9 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm bg-white appearance-none">
                                    <option value="">-- Select Time --</option>
                                    <option value="06:00 AM to 08:00 AM">06:00 AM to 08:00 AM</option>
                                    <option value="08:00 AM to 10:00 AM">08:00 AM to 10:00 AM</option>
                                    <option value="10:00 AM to 12:00 PM">10:00 AM to 12:00 PM</option>
                                    <option value="12:00 PM to 02:00 PM">12:00 PM to 02:00 PM</option>
                                    <option value="02:00 PM to 04:00 PM">02:00 PM to 04:00 PM</option>
                                    <option value="04:00 PM to 06:00 PM">04:00 PM to 06:00 PM</option>
                                    <option value="06:00 PM to 08:00 PM">06:00 PM to 08:00 PM</option>
                                    <option value="08:00 PM to 10:00 PM">08:00 PM to 10:00 PM</option>
                                    <option value="10:00 PM to 12:00 AM">10:00 PM to 12:00 AM</option>
                                    <option value="12:00 AM to 06:00 AM">12:00 AM to 06:00 AM</option>
                                </select>
                                <i class="far fa-clock absolute left-3 top-3 text-blue-500"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Notes for Driver -->
                    <div class="mt-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Notes / Instructions for Driver</label>
                        <textarea name="notes_for_driver" x-model="notesForDriver" rows="2" placeholder="Write any specific driver instructions here..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm"></textarea>
                    </div>
                </div>

            </div>

            <!-- Right Sidebar: Live Fare breakdown & Booking Status -->
            <div class="space-y-6">
                <!-- Live Pricing breakdown invoice Card -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden sticky top-6">
                    <div class="bg-gradient-to-r from-blue-700 to-blue-800 px-6 py-4 text-white">
                        <div class="flex items-center justify-between">
                            <h3 class="font-bold text-lg">Fare Summary</h3>
                            <span class="text-xs bg-blue-600 border border-blue-500 text-white font-bold px-2 py-0.5 rounded-full uppercase" x-text="tripType.replace('_', ' ')"></span>
                        </div>
                        <p class="text-[11px] text-blue-100 mt-1">Updates live dynamically as options change.</p>
                    </div>

                    <div class="p-6 space-y-4">
                        <!-- Manual override option -->
                        <div class="p-3 bg-yellow-50 border border-yellow-100 rounded-lg flex items-center justify-between">
                            <div class="flex flex-col">
                                <span class="text-xs font-bold text-yellow-800">Manual Override Price</span>
                                <span class="text-[10px] text-yellow-600">Admin custom rates override API</span>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" x-model="manualOverride" @change="calculateCharges()" class="sr-only peer">
                                <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-yellow-500"></div>
                            </label>
                        </div>

                        <!-- Individual charge breakdown items -->
                        <div class="space-y-2.5 text-sm">
                            <!-- Per KM charge -->
                            <div class="flex justify-between items-center py-1.5 border-b border-gray-100">
                                <div>
                                    <div class="font-bold text-gray-700">Distance Fare</div>
                                    <div class="text-[10px] text-gray-400" x-show="!manualOverride">
                                        ₹<span x-text="perKmRateUnit"></span>/KM × <span x-text="totalKm"></span> KM
                                    </div>
                                    <div class="text-[10px] text-yellow-600 font-semibold" x-show="manualOverride">
                                        Unit rate: ₹<input type="number" x-model="perKmRateUnit" class="w-10 px-0.5 border border-yellow-300 rounded text-center text-[10px]">
                                    </div>
                                </div>
                                <div class="flex items-center gap-1">
                                    <span class="text-gray-500 font-medium" x-show="!manualOverride">₹</span>
                                    <input type="number" name="per_km_amount" x-model="perKmAmount" :readonly="!manualOverride" @input="recalculateManualTotals()" :class="manualOverride ? 'w-20 px-2 py-0.5 border border-yellow-300 rounded text-right font-semibold bg-yellow-50/50' : 'bg-transparent text-right border-none font-bold text-gray-800 select-all pointer-events-none p-0 w-20'">
                                </div>
                            </div>

                            <!-- Driver allowance -->
                            <div class="flex justify-between items-center py-1.5 border-b border-gray-100">
                                <div>
                                    <div class="font-bold text-gray-700">Driver Allowance</div>
                                    <span class="text-[10px] text-gray-400">Fixed stay/trip allowance</span>
                                </div>
                                <div class="flex items-center gap-1">
                                    <span class="text-gray-500 font-medium" x-show="!manualOverride">₹</span>
                                    <input type="number" name="driver_allowance" x-model="driverAllowance" :readonly="!manualOverride" @input="recalculateManualTotals()" :class="manualOverride ? 'w-20 px-2 py-0.5 border border-yellow-300 rounded text-right font-semibold bg-yellow-50/50' : 'bg-transparent text-right border-none font-bold text-gray-800 select-all pointer-events-none p-0 w-20'">
                                </div>
                            </div>

                            <!-- AC Charges -->
                            <div class="flex justify-between items-center py-1.5 border-b border-gray-100" x-show="isAc || manualOverride">
                                <div>
                                    <div class="font-bold text-gray-700">AC Charges</div>
                                    <div class="text-[10px] text-gray-400" x-show="!manualOverride">
                                        ₹<span x-text="acRateUnit"></span>/KM × <span x-text="totalKm"></span> KM
                                    </div>
                                    <div class="text-[10px] text-yellow-600 font-semibold" x-show="manualOverride">
                                        Unit rate: ₹<input type="number" x-model="acRateUnit" class="w-10 px-0.5 border border-yellow-300 rounded text-center text-[10px]">
                                    </div>
                                </div>
                                <div class="flex items-center gap-1">
                                    <span class="text-gray-500 font-medium" x-show="!manualOverride">₹</span>
                                    <input type="number" name="ac_charges" x-model="acCharges" :readonly="!manualOverride" @input="recalculateManualTotals()" :class="manualOverride ? 'w-20 px-2 py-0.5 border border-yellow-300 rounded text-right font-semibold bg-yellow-50/50' : 'bg-transparent text-right border-none font-bold text-gray-800 select-all pointer-events-none p-0 w-20'">
                                </div>
                            </div>

                            <!-- Platform Charges -->
                            <div class="flex justify-between items-center py-1.5 border-b border-gray-100">
                                <div>
                                    <div class="font-bold text-gray-700">Platform Charge</div>
                                    <span class="text-[10px] text-gray-400">Fixed convenience fee</span>
                                </div>
                                <div class="flex items-center gap-1">
                                    <span class="text-gray-500 font-medium" x-show="!manualOverride">₹</span>
                                    <input type="number" name="platform_charges" x-model="platformCharges" :readonly="!manualOverride" @input="recalculateManualTotals()" :class="manualOverride ? 'w-20 px-2 py-0.5 border border-yellow-300 rounded text-right font-semibold bg-yellow-50/50' : 'bg-transparent text-right border-none font-bold text-gray-800 select-all pointer-events-none p-0 w-20'">
                                </div>
                            </div>

                            <!-- Stay Charges -->
                            <div class="flex justify-between items-center py-1.5 border-b border-gray-100">
                                <div>
                                    <div class="font-bold text-gray-700">Stay Charges</div>
                                    <div class="text-[10px] text-gray-400" x-show="!manualOverride">
                                        Overnight/Day stay fees
                                    </div>
                                    <div class="text-[10px] text-yellow-600 font-semibold" x-show="manualOverride">
                                        Unit rate: ₹<input type="number" class="w-10 px-0.5 border border-yellow-300 rounded text-center text-[10px]">
                                    </div>
                                </div>
                                <div class="flex items-center gap-1">
                                    <span class="text-gray-500 font-medium" x-show="!manualOverride">₹</span>
                                    <input type="number" name="stay_charges" x-model="stayCharges" :readonly="!manualOverride" @input="recalculateManualTotals()" :class="manualOverride ? 'w-20 px-2 py-0.5 border border-yellow-300 rounded text-right font-semibold bg-yellow-50/50' : 'bg-transparent text-right border-none font-bold text-gray-800 select-all pointer-events-none p-0 w-20'">
                                </div>
                            </div>



                            <!-- Discount Coupon -->
                            <div class="flex justify-between items-center py-1.5 border-b border-gray-100">
                                <div>
                                    <div class="font-bold text-green-700">Discount Amount</div>
                                    <div class="text-[10px] text-yellow-600 font-semibold" x-show="couponCode">
                                        Coupon Applied: <span x-text="couponCode"></span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1">
                                    <span class="text-green-600 font-medium">-₹</span>
                                    <input type="number" name="discount_amount" x-model="discountAmount" @input="recalculateManualTotals()" class="w-20 px-2 py-0.5 border border-green-300 rounded text-right font-semibold bg-white text-green-700 focus:ring-1 focus:ring-green-500">
                                </div>
                            </div>

                            <!-- Coupon Code Input field -->
                            <div class="py-2 flex gap-2">
                                <input type="text" name="coupon_code" x-model="couponCode" placeholder="COUPON CODE" class="flex-1 px-3 py-1 text-xs uppercase border border-gray-300 rounded focus:ring-1 focus:ring-blue-500">
                                <button type="button" @click="applyCoupon()" class="px-3 py-1 bg-gray-800 hover:bg-gray-900 text-white font-bold rounded text-xs transition">Apply</button>
                            </div>
                        </div>

                        <!-- Final Totals banner -->
                        <div class="pt-3 border-t border-gray-200 space-y-1">
                            <div class="flex justify-between items-center text-xs text-gray-500">
                                <span>Subtotal</span>
                                <span class="font-bold">₹<span x-text="parseFloat(subtotal).toFixed(2)"></span></span>
                            </div>
                            <div class="flex justify-between items-center py-1">
                                <span class="font-bold text-gray-800 text-base">Net Total</span>
                                <span class="font-black text-2xl text-blue-700">₹<span x-text="parseFloat(totalAmount).toFixed(2)"></span></span>
                            </div>
                        </div>

                        <!-- Booking Status selection -->
                        <div class="pt-4 border-t border-gray-100 space-y-3 text-sm">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Booking Status</label>
                                <select name="booking_status" x-model="bookingStatus" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white font-semibold">
                                    <option value="pending">Pending Approval</option>
                                    <option value="confirmed">Confirmed / Booked</option>
                                    <option value="completed">Completed Trip</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Payment Status</label>
                                <select name="payment_status" x-model="paymentStatus" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white font-semibold">
                                    <option value="unpaid">Unpaid / Outstanding</option>
                                    <option value="partially_paid">Partially Paid</option>
                                    <option value="paid">Fully Paid</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Payment Method</label>
                                <select name="payment_method" x-model="paymentMethod" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white font-semibold">
                                    <option value="Cash">Cash on Delivery</option>
                                    <option value="Online">Online / Card Payment</option>
                                    <option value="Bank Transfer">Bank Wire Transfer</option>
                                    <option value="Wallet">Customer Wallet</option>
                                </select>
                            </div>
                        </div>

                        <!-- Primary CTA Button (Moved to Sticky Footer) -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Sticky Bottom Checkout Bar -->
        <div class="sticky bottom-0 mt-8 z-50 bg-white border-t border-gray-200 shadow-[0_-15px_30px_-5px_rgba(0,0,0,0.1)] rounded-t-2xl px-6 py-4 mx-auto transition-all duration-300 w-full" x-show="totalAmount > 0" x-transition>
            <div class="flex flex-col md:flex-row items-center justify-between gap-4 max-w-7xl mx-auto">
                <div class="flex items-center gap-6 w-full md:w-auto">
                    <div class="bg-gradient-to-r from-blue-50 to-blue-100/50 px-5 py-2.5 rounded-xl border border-blue-200 shadow-inner">
                        <div class="text-[10px] text-blue-600 font-extrabold uppercase tracking-widest mb-0.5">Final Net Total</div>
                        <div class="text-3xl font-black text-blue-900 leading-none drop-shadow-sm">₹<span x-text="parseFloat(totalAmount).toFixed(2)"></span></div>
                    </div>
                    <div class="hidden md:block h-10 w-px bg-gray-200"></div>
                    <div class="hidden md:block">
                        <div class="text-[10px] text-gray-500 font-extrabold uppercase tracking-widest mb-0.5">Total Distance</div>
                        <div class="text-lg font-bold text-gray-800 leading-none"><span x-text="parseFloat(totalKm).toFixed(2)"></span> <span class="text-sm text-gray-500 font-medium">km</span></div>
                    </div>
                </div>
                
                <div class="w-full md:w-auto flex gap-3">
                    <button type="submit" class="w-full md:w-auto px-8 py-4 bg-gradient-to-r from-blue-600 to-blue-800 hover:from-blue-700 hover:to-blue-900 text-white font-black rounded-xl shadow-[0_4px_14px_0_rgba(37,99,235,0.39)] hover:shadow-[0_6px_20px_rgba(37,99,235,0.23)] hover:scale-[1.02] transition-all duration-200 flex items-center justify-center gap-2 text-[13px] uppercase tracking-wider">
                        <i class="fas fa-check-circle text-xl drop-shadow-md"></i>
                        Confirm & Book Trip
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    function manualBookingApp() {
        return {
            // Data injected from backend
            cars: @json($cars),
            customers: @json($customers),

            // Customer selection state
            customerType: 'existing',
            customerQuery: '',
            filteredCustomers: [],
            showDropdown: false,
            
            // Selected customer
            customerId: '',
            selectedCustomerName: '',
            selectedCustomerMobile: '',

            // Input Customer Details
            customerName: '',
            customerMobile: '',
            customerEmail: '',

            // Trip settings
            tripType: 'one_way',
            returnLocationType: 'same',
            carId: '',
            isAc: false,
            passengers: 1,
            bags: 0,
            notesForDriver: '',
            
            // Route
            pickupAddress: '',
            dropAddress: '',
            returnPickupAddress: '',
            returnDropAddress: '',
            
            // Distance
            oneWayKm: 0,
            returnKm: 0,
            totalKm: 0,
            travelTime: '',

            // Schedule
            pickupDate: '',
            pickupTime: '',
            returnDate: '',
            returnTime: '',

            // Pricing state
            manualOverride: false,
            perKmRateUnit: 0,
            perKmAmount: 0,
            driverAllowance: 0,
            platformCharges: 0,
            acRateUnit: 0,
            acCharges: 0,
            stayCharges: 0,
            discountAmount: 0,
            couponCode: '',
            subtotal: 0,
            totalAmount: 0,

            // Statuses
            bookingStatus: 'pending',
            paymentStatus: 'unpaid',
            paymentMethod: 'Cash',

            // Helper computed attributes
            selectedCar: null,
            selectedCarName: '',
            selectedCarSeats: '',
            selectedCarMinAmount: 0,
            selectedCarMaxPassengers: 4,
            selectedCarMaxBags: 2,

            init() {
                // Initialize default date and time values
                const today = new Date();
                const dd = String(today.getDate()).padStart(2, '0');
                const mm = String(today.getMonth() + 1).padStart(2, '0');
                const yyyy = today.getFullYear();
                this.pickupDate = `${yyyy}-${mm}-${dd}`;
                this.pickupTime = "06:00 AM to 08:00 AM";
                
                this.searchCustomers();

                this.$watch('tripType', (value) => {
                    if (window.drawFullRoute) window.drawFullRoute(value);
                });
            },

            searchCustomers() {
                if (!this.customerQuery) {
                    this.filteredCustomers = this.customers.slice(0, 8);
                    return;
                }
                const q = String(this.customerQuery).toLowerCase();
                this.filteredCustomers = this.customers.filter(c => 
                    (c.name && String(c.name).toLowerCase().includes(q)) || 
                    (c.mobile && String(c.mobile).includes(q))
                ).slice(0, 8);
            },

            selectCustomer(cust) {
                this.customerId = cust.id;
                this.selectedCustomerName = cust.name;
                this.selectedCustomerMobile = cust.mobile;
                this.customerName = cust.name;
                this.customerMobile = cust.mobile;
                this.customerQuery = cust.name;
                this.showDropdown = false;
            },

            clearCustomerSelection() {
                this.customerId = '';
                this.selectedCustomerName = '';
                this.selectedCustomerMobile = '';
                this.customerName = '';
                this.customerMobile = '';
                this.customerQuery = '';
                this.searchCustomers();
            },

            carSelected() {
                if (!this.carId) {
                    this.selectedCar = null;
                    this.selectedCarName = '';
                    this.selectedCarMinAmount = 0;
                    this.selectedCarMaxPassengers = 4;
                    this.selectedCarMaxBags = 2;
                    return;
                }

                const car = this.cars.find(c => c.id == this.carId);
                this.selectedCar = car;
                this.selectedCarName = car.car_name;
                this.selectedCarSeats = car.car_seats;
                this.selectedCarMinAmount = car.min_trip_amount || 0;
                this.selectedCarMaxPassengers = car.max_passengers || 4;
                this.selectedCarMaxBags = car.max_bags || 2;
                
                // Set default capacities
                this.passengers = Math.min(this.passengers, this.selectedCarMaxPassengers);
                this.bags = Math.min(this.bags, this.selectedCarMaxBags);

                // Fetch charges configuration for pre-filling manual input
                fetch(`/api/v1/car/${this.carId}/charges`)
                    .then(res => res.json())
                    .then(res => {
                        if (res.success && res.data && res.data.charges) {
                            const charges = res.data.charges;
                            
                            // Find base km rate and ac rate
                            charges.forEach(ch => {
                                const title = ch.title.toLowerCase();
                                const cType = ch.charge_type ? ch.charge_type.charges_type.toLowerCase() : '';
                                
                                if (title.includes('km') || cType.includes('km')) {
                                    this.perKmRateUnit = parseFloat(ch.amount || 0);
                                }
                                if (title.includes('ac') || cType.includes('ac')) {
                                    this.acRateUnit = parseFloat(ch.amount || 0);
                                }
                            });
                        }
                        this.calculateCharges();
                    })
                    .catch(err => {
                        console.error("Error loading car charges", err);
                        this.calculateCharges();
                    });
            },

            updateKm() {
                if (this.tripType === 'one_way') {
                    this.totalKm = parseFloat(this.oneWayKm || 0) * 2;
                    this.returnKm = parseFloat(this.oneWayKm || 0).toFixed(2);
                } else {
                    this.totalKm = parseFloat(this.oneWayKm || 0);
                    const rKm = parseFloat(this.returnKm || 0);
                    this.totalKm += rKm > 0 ? rKm : parseFloat(this.oneWayKm || 0);
                }
                this.calculateCharges();
            },

            handleMapData(data) {
                this.pickupAddress = data.pickupAddress || this.pickupAddress;
                this.dropAddress = data.dropAddress || this.dropAddress;
                this.returnPickupAddress = data.returnPickupAddress || this.returnPickupAddress;
                this.returnDropAddress = data.returnDropAddress || this.returnDropAddress;
                this.oneWayKm = data.oneWayKm;
                this.returnKm = data.returnKm;
                this.travelTime = data.travelTime;
                
                // Update hidden coordinates inputs dynamically
                const form = document.querySelector('form');
                if (form) {
                    form.querySelector('input[name="pickup_lat"]').value = data.coords.pickup_lat || 0;
                    form.querySelector('input[name="pickup_lng"]').value = data.coords.pickup_lng || 0;
                    form.querySelector('input[name="drop_lat"]').value = data.coords.drop_lat || 0;
                    form.querySelector('input[name="drop_lng"]').value = data.coords.drop_lng || 0;
                    form.querySelector('input[name="return_pickup_lat"]').value = data.coords.return_pickup_lat || 0;
                    form.querySelector('input[name="return_pickup_lng"]').value = data.coords.return_pickup_lng || 0;
                    form.querySelector('input[name="return_drop_lat"]').value = data.coords.return_drop_lat || 0;
                    form.querySelector('input[name="return_drop_lng"]').value = data.coords.return_drop_lng || 0;
                }
                this.updateKm();
            },

            calculateCharges() {
                if (this.manualOverride) {
                    this.recalculateManualTotals();
                    return;
                }

                if (!this.carId || this.totalKm <= 0) {
                    this.perKmAmount = 0;
                    this.driverAllowance = 0;
                    this.platformCharges = 0;
                    this.acCharges = 0;
                    this.stayCharges = 0;
                    this.subtotal = 0;
                    this.totalAmount = 0;
                    return;
                }

                let days = 1;
                let hours = 0;

                if (this.tripType === 'round_trip' && this.pickupDate && this.returnDate) {
                    const pDate = new Date(this.pickupDate);
                    const rDate = new Date(this.returnDate);
                    
                    if (rDate > pDate) {
                        const diffTime = Math.abs(rDate - pDate);
                        days = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
                    } else if (rDate.toDateString() === pDate.toDateString()) {
                        days = 1;
                    }

                    if (this.pickupTime && this.returnTime) {
                        const pTimeClean = this.pickupTime.split(' ')[0]; 
                        const rTimeClean = this.returnTime.split(' ')[0];
                        
                        const start = new Date(`${this.pickupDate}T${pTimeClean}`);
                        const end = new Date(`${this.returnDate}T${rTimeClean}`);
                        
                        if (end > start) {
                            hours = Math.abs(end - start) / 36e5;
                        }
                    }
                }

                // Call calculation endpoint
                fetch('/api/v1/calculate-charges', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        car_id: this.carId,
                        distance_km: this.totalKm,
                        is_ac: this.isAc ? 1 : 0,
                        trip_type: this.tripType,
                        hours: hours,
                        days: days
                    })
                })
                .then(res => res.json())
                .then(res => {
                    if (res.success && res.data) {
                        const d = res.data;
                        
                        // Clear amounts
                        this.perKmAmount = 0;
                        this.driverAllowance = 0;
                        this.platformCharges = 0;
                        this.acCharges = 0;
                        this.stayCharges = 0;
                        
                        // Populate breakdown
                        d.charges_breakdown.forEach(c => {
                            const name = (c.master_type || c.charge_title).toLowerCase();
                            
                            if (name.includes('km') || name.includes('distance') || name.includes('per km')) {
                                this.perKmRateUnit = c.rate;
                                this.perKmAmount = c.amount;
                            } else if (name.includes('allowance')) {
                                this.driverAllowance = c.amount;
                            } else if (name.includes('stay') || name.includes('night') || name.includes('day')) {
                                this.stayCharges = c.amount;
                            } else if (name.includes('platform')) {
                                this.platformCharges = c.amount;
                            } else if (name.includes('ac')) {
                                this.acRateUnit = c.rate;
                                this.acCharges = c.amount;
                            }
                        });

                        this.subtotal = d.total_amount;
                        this.recalculateManualTotals();
                    }
                })
                .catch(err => console.error("Error calculating fare", err));
            },

            recalculateManualTotals() {
                if (this.manualOverride) {
                    this.perKmAmount = parseFloat(this.totalKm || 0) * parseFloat(this.perKmRateUnit || 0);
                    
                    if (this.isAc) {
                        this.acCharges = parseFloat(this.totalKm || 0) * parseFloat(this.acRateUnit || 0);
                    } else {
                        this.acCharges = 0;
                    }
                }

                this.subtotal = parseFloat(this.perKmAmount || 0) + 
                                parseFloat(this.driverAllowance || 0) + 
                                parseFloat(this.platformCharges || 0) + 
                                parseFloat(this.acCharges || 0) + 
                                parseFloat(this.stayCharges || 0);

                this.totalAmount = Math.max(0, this.subtotal - parseFloat(this.discountAmount || 0));
            },

            applyCoupon() {
                if (!this.couponCode) return;
                
                // Local check or simulated API check for promo codes
                const code = this.couponCode.toUpperCase().trim();
                let discount = 0;

                if (code === 'WELCOME10') {
                    discount = this.subtotal * 0.1;
                } else if (code === 'FLAT100') {
                    discount = 100;
                } else if (code === 'SBD50') {
                    discount = Math.min(this.subtotal * 0.5, 300);
                } else {
                    alert('Invalid coupon code!');
                    this.couponCode = '';
                    this.discountAmount = 0;
                    this.recalculateManualTotals();
                    return;
                }

                this.discountAmount = Math.round(discount * 100) / 100;
                this.recalculateManualTotals();
                alert(`Coupon ${code} applied successfully! Discount: ₹${this.discountAmount}`);
            },

            submitForm(event) {
                // Perform quick custom validations before submitting
                if (this.customerType === 'existing' && !this.customerId) {
                    alert('Please select an existing customer or change type to Guest.');
                    event.preventDefault();
                    return;
                }

                if (this.customerType === 'new' && (!this.customerName || !this.customerMobile)) {
                    alert('Please enter guest name and mobile number.');
                    event.preventDefault();
                    return;
                }

                if (!this.carId) {
                    alert('Please select a vehicle type.');
                    event.preventDefault();
                    return;
                }

                if (!this.pickupAddress || !this.dropAddress) {
                    alert('Please enter pickup and drop addresses.');
                    event.preventDefault();
                    return;
                }

                if (this.oneWayKm <= 0) {
                    alert('Distance must be greater than 0.');
                    event.preventDefault();
                    return;
                }

                if (!this.pickupDate || !this.pickupTime) {
                    alert('Please specify the pickup date and time.');
                    event.preventDefault();
                    return;
                }
            }
        };
    }

    // =====================================================
    // GOOGLE MAPS & AUTOCOMPLETE LOGIC
    // =====================================================
    let map, directionsService, directionsRenderer;
    let pickupAutocomplete, dropAutocomplete, returnPickupAuto, returnDropAuto;

    function initMap() {
        const mapEl = document.getElementById("routeMap");
        map = new google.maps.Map(mapEl, { 
            center: { lat: 28.6139, lng: 77.2090 }, // Default center (New Delhi)
            zoom: 11 
        });

        directionsService = new google.maps.DirectionsService();
        directionsRenderer = new google.maps.DirectionsRenderer({
            map: map,
            suppressMarkers: false
        });

        // Initialize Autocompletes
        pickupAutocomplete = new google.maps.places.Autocomplete(
            document.getElementById("pickup_address"), { componentRestrictions: { country: "in" } }
        );
        dropAutocomplete = new google.maps.places.Autocomplete(
            document.getElementById("drop_address"), { componentRestrictions: { country: "in" } }
        );
        returnPickupAuto = new google.maps.places.Autocomplete(
            document.getElementById("return_pickup_address"), { componentRestrictions: { country: "in" } }
        );
        returnDropAuto = new google.maps.places.Autocomplete(
            document.getElementById("return_drop_address"), { componentRestrictions: { country: "in" } }
        );

        pickupAutocomplete.addListener("place_changed", function() {
            const place = pickupAutocomplete.getPlace();
            if (place.geometry) {
                document.querySelector('input[name="pickup_lat"]').value = place.geometry.location.lat();
                document.querySelector('input[name="pickup_lng"]').value = place.geometry.location.lng();
                document.querySelector('input[name="pickup_address"]').dispatchEvent(new Event('input')); // Update Alpine model
                window.drawFullRoute();
            }
        });

        dropAutocomplete.addListener("place_changed", function() {
            const place = dropAutocomplete.getPlace();
            if (place.geometry) {
                document.querySelector('input[name="drop_lat"]').value = place.geometry.location.lat();
                document.querySelector('input[name="drop_lng"]').value = place.geometry.location.lng();
                document.querySelector('input[name="drop_address"]').dispatchEvent(new Event('input'));
                window.drawFullRoute();
            }
        });

        returnPickupAuto.addListener("place_changed", function() {
            const place = returnPickupAuto.getPlace();
            if (place.geometry) {
                document.querySelector('input[name="return_pickup_lat"]').value = place.geometry.location.lat();
                document.querySelector('input[name="return_pickup_lng"]').value = place.geometry.location.lng();
                document.querySelector('input[name="return_pickup_address"]').dispatchEvent(new Event('input'));
                window.drawFullRoute();
            }
        });

        returnDropAuto.addListener("place_changed", function() {
            const place = returnDropAuto.getPlace();
            if (place.geometry) {
                document.querySelector('input[name="return_drop_lat"]').value = place.geometry.location.lat();
                document.querySelector('input[name="return_drop_lng"]').value = place.geometry.location.lng();
                document.querySelector('input[name="return_drop_address"]').dispatchEvent(new Event('input'));
                window.drawFullRoute();
            }
        });
    }

    window.drawFullRoute = function(overrideTripType = null) {
        const A_lat = parseFloat(document.querySelector('input[name="pickup_lat"]').value) || 0;
        const A_lng = parseFloat(document.querySelector('input[name="pickup_lng"]').value) || 0;
        const B_lat = parseFloat(document.querySelector('input[name="drop_lat"]').value) || 0;
        const B_lng = parseFloat(document.querySelector('input[name="drop_lng"]').value) || 0;

        if (!A_lat || !B_lat) return;

        const tripType = overrideTripType || document.querySelector('input[name="trip_type"]').value || 'one_way';
        const returnLocationType = document.querySelector('input[name="return_location_type"]:checked')?.value || 'same';
        const C_lat = parseFloat(document.querySelector('input[name="return_pickup_lat"]').value) || 0;
        const C_lng = parseFloat(document.querySelector('input[name="return_pickup_lng"]').value) || 0;
        const D_lat = parseFloat(document.querySelector('input[name="return_drop_lat"]').value) || 0;
        const D_lng = parseFloat(document.querySelector('input[name="return_drop_lng"]').value) || 0;

        let request = {
            origin: { lat: A_lat, lng: A_lng },
            destination: { lat: B_lat, lng: B_lng },
            travelMode: google.maps.TravelMode.DRIVING
        };

        if (tripType === 'round_trip') {
            request.destination = { lat: A_lat, lng: A_lng }; // Return to pickup
            const waypoints = [ { location: { lat: B_lat, lng: B_lng }, stopover: true } ];
            if (returnLocationType === 'custom' && C_lat && D_lat) {
                waypoints.push({ location: { lat: C_lat, lng: C_lng }, stopover: true });
                waypoints.push({ location: { lat: D_lat, lng: D_lng }, stopover: true });
            }
            request.waypoints = waypoints;
        }

        directionsService.route(request, function(result, status) {
            if (status !== 'OK') return;
            directionsRenderer.setDirections(result);

            let totalKm = 0;
            let totalSeconds = 0;
            
            result.routes[0].legs.forEach(leg => {
                totalKm += leg.distance.value / 1000;
                totalSeconds += leg.duration.value;
            });

            // Format travel time
            const hours = Math.floor(totalSeconds / 3600);
            const minutes = Math.floor((totalSeconds % 3600) / 60);
            let travelTime = "";
            if (hours > 0) travelTime += `${hours} hr `;
            travelTime += `${minutes} mins`;

            const oneWayKm = tripType === 'one_way' ? totalKm : (result.routes[0].legs[0].distance.value / 1000);
            const returnKm = tripType === 'round_trip' ? (totalKm - oneWayKm) : 0;

            // Dispatch event to Alpine
            window.dispatchEvent(new CustomEvent('update-map-data', {
                detail: {
                    pickupAddress: document.querySelector('input[name="pickup_address"]').value,
                    dropAddress: document.querySelector('input[name="drop_address"]').value,
                    returnPickupAddress: document.querySelector('input[name="return_pickup_address"]').value,
                    returnDropAddress: document.querySelector('input[name="return_drop_address"]').value,
                    oneWayKm: oneWayKm.toFixed(2),
                    returnKm: returnKm.toFixed(2),
                    travelTime: travelTime,
                    coords: {
                        pickup_lat: A_lat, pickup_lng: A_lng,
                        drop_lat: B_lat, drop_lng: B_lng,
                        return_pickup_lat: C_lat, return_pickup_lng: C_lng,
                        return_drop_lat: D_lat, return_drop_lng: D_lng
                    }
                }
            }));
        });
    }
</script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBmaR3DSseRPUCCvGT0Ru8aK-Jrm39NlTE&libraries=places&callback=initMap" async defer></script>
@endsection
