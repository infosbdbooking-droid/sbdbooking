@extends('layouts.app')
@section('content')
    <div class="container mx-auto  py-6">
        <div class="mb-6">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <h1 class="text-xl font-bold text-gray-900">Create Orders*</h1>
                <div class="flex space-x-4 ml-4">
                    <button type="button" id="addaddBusiness"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md flex items-center transition-colors">
                        <i class="fas fa-plus mr-2"></i>Business Region
                    </button>
                    <button type="button" id="addServiceCenter"
                        class="px-4 py-2 bg-blue-600  hover:bg-blue-700 text-white rounded-md flex items-center transition-colors">
                        <i class="fas fa-plus mr-2"></i>Service Center
                    </button>
                </div>
            </div>
        </div>
        <div class="bg-white shadow border border-gray-200   rounded-lg p-6">
            <div class="flex space-x-4 ml-4">
                <button
                    class="w-32 h-9 flex items-center justify-center bg-blue-600 text-white font-medium rounded-lg shadow hover:bg-blue-700 transition">
                    Induvidal
                </button>
                <button
                    class="w-32 h-9 flex items-center   justify-center bg-transparent border-2 border-blue-600 text-blue-600 font-medium rounded-lg hover:bg-blue-50 transition">
                    Multilocation
                </button>
            </div>
            <form id="orderForm" action="{{ route('orders.store') }}" method="POST" class="space-y-6">
                @csrf
                <div class="grid md:grid-cols-3 gap-4">
                    <!-- Company Details Card -->
                    <div class="md:col-span-3">
                        <div class="bg-white border border-gray-200 rounded-lg shadow p-6">
                            <h3 class="text-xl font-semibold text-gray-800 mb-6">Company Details</h3>

                            <!-- Row 1: first three fields -->
                            <div class="grid md:grid-cols-3 gap-4 mb-6">
                                <!-- Site Request -->
                                <div>
                                    <label class="text-gray-700">Site Request*</label>
                                    <select name="site_request" id="site_request"
                                        class="w-full select2 border border-gray-300 rounded px-3 py-2">
                                        <option value="">Site Request</option>
                                        <option value="commercial_route">Commercial Route</option>
                                        <option value="commercial_onsite">Commercial Onsite</option>
                                    </select>
                                </div>
                                <!-- Service Center Type -->
                                <div>
                                    <label class="text-gray-700">Service Center Type*</label>
                                    <select name="service_center_type"
                                        class="w-full select2 border border-gray-300 rounded px-3 py-2">
                                        <option value="">Service Center Type</option>
                                        <option value="hommlie">Hommlie</option>
                                        <option value="vendor">Vendor</option>
                                        <option value="franchise">Franchise</option>
                                    </select>
                                </div>
                                <!-- Employee Name -->
                                <div>
                                    <label class="text-gray-700">Employee Name*</label>
                                    <input type="text" name="employee_name" readonly
                                        class="w-full border border-gray-300 rounded px-3 py-2" placeholder="Auto-generated"
                                        value="{{ session('logged_name') }}">
                                </div>
                            </div>

                            <!-- Row 2: last four fields all in one line -->
                            <div class="grid md:grid-cols-4 gap-4">
                                <!-- Billing -->
                                <div>
                                    <label class="text-gray-700">Billing*</label>
                                    <select name="billing" class="w-full select2 border border-gray-300 rounded px-3 py-2">
                                        <option value="">Billing Type</option>
                                        <option value="head_office">Head Office</option>
                                        <option value="regional_office">Regional Office</option>
                                        <option value="branch_office">Branch Office</option>
                                    </select>
                                </div>
                                <!-- Business Region -->
                                <div>
                                    <label class="text-gray-700">Business Region*</label>
                                    <select name="business_region" id="business_region"
                                        class="w-full select2 border border-gray-300 rounded px-3 py-2">
                                        <option value="">Business Region</option>
                                    </select>
                                </div>
                                <!-- Business Sub Region -->
                                <div>
                                    <label class="text-gray-700">Business Sub Region*</label>
                                    <select name="business_sub_region" id="business_sub_region"
                                        class="w-full select2 border border-gray-300 rounded px-3 py-2">
                                        <option value="">Business Sub Region</option>
                                    </select>
                                </div>
                                <!-- Branch Code -->
                                <div>
                                    <label class="text-gray-700">Branch Code*</label>
                                    <input type="text" name="branch_codes" id="branch_codes" readonly
                                        class="w-full border border-gray-300 rounded px-3 py-2"
                                        placeholder="Auto-generated">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="md:col-span-3">
                        <div class="bg-white border border-gray-200 rounded-lg shadow p-6">
                            <h3 class="text-xl font-semibold text-gray-800 mb-4">Customer Details</h3>
                            <div class="grid md:grid-cols-3 gap-4">
                                <!-- Customer Type -->
                                <div>
                                    <label for="customer_type" class="text-gray-700">Customer Type*</label>
                                    <select id="customer_type" name="customer_type"
                                        class="w-full select2 border border-gray-300 rounded px-3 py-2">
                                        <option value="">e.g. New Customer</option>
                                        <option value="new">New Customer</option>
                                        <option value="existing">Existing Customer</option>
                                    </select>
                                </div>

                                <!-- Business Lead -->
                                <div id="businessLeadDiv">
                                    <label for="business_lead" class="text-gray-700">Business Lead*</label>
                                    <select id="business_lead" name="business_lead"
                                        class="w-full select2 border border-gray-300 rounded px-3 py-2">
                                        <option value="">e.g. Web Lead</option>
                                        <option value="exhibition">Exhibition</option>
                                        <option value="webLead">Web Lead</option>
                                        <option value="serviceLead">Service Lead</option>
                                    </select>
                                </div>

                                <!-- Mobile (conditional) -->
                                <div id="mobileInputDiv" class="hidden">
                                    <label for="mobile_number" class="text-gray-700">Mobile Number*</label>
                                    <div class="relative">
                                        <input id="mobile_number" type="text" name="mobile_number"
                                            class="w-full border border-gray-300 rounded px-3 py-2"
                                            placeholder="e.g. 9876543210">
                                        <span
                                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 cursor-pointer">🔍</span>
                                    </div>
                                </div>

                                <!-- Customer Legal Name -->
                                <div>
                                    <label for="customer_legal_name" class="text-gray-700">Customer Legal Name</label>
                                    <input id="customer_legal_name" type="text" name="customer_legal_name"
                                        class="w-full border border-gray-300 rounded px-3 py-2"
                                        placeholder="e.g. Acme Corporation">
                                </div>

                                <!-- Customer Trade Name -->
                                <div>
                                    <label for="customer_trade_name" class="text-gray-700">Customer Trade Name</label>
                                    <input id="customer_trade_name" type="text" name="customer_trade_name"
                                        class="w-full border border-gray-300 rounded px-3 py-2"
                                        placeholder="e.g. Acme Trading">
                                </div>

                                <!-- Contact Person -->
                                <div>
                                    <label for="contact_person" class="text-gray-700">Contact Person</label>
                                    <input id="contact_person" type="text" name="contact_person"
                                        class="w-full border border-gray-300 rounded px-3 py-2" placeholder="e.g. John Doe">
                                </div>

                                <!-- Designation -->
                                <div>
                                    <label for="designation" class="text-gray-700">Designation</label>
                                    <input id="designation" type="text" name="designation"
                                        class="w-full border border-gray-300 rounded px-3 py-2"
                                        placeholder="e.g. Sales Manager">
                                </div>

                                <!-- Address -->
                                <div class="md:col-span-3">
                                    <label for="address" class="text-gray-700">Address</label>
                                    <textarea id="mytextarea" name="address"
                                        class="w-full border border-gray-300 myEditor rounded px-3 py-2 h-24"
                                        placeholder="e.g. 456 Industrial Area, Bangalore"></textarea>
                                </div>

                                <!-- Landmark -->
                                <div>
                                    <label for="landmark" class="text-gray-700">Landmark</label>
                                    <input id="landmark" type="text" name="landmark"
                                        class="w-full border border-gray-300 rounded px-3 py-2"
                                        placeholder="e.g. Near Central Park">
                                </div>

                                <!-- City -->
                                <div>
                                    <label for="city" class="text-gray-700">City</label>
                                    <input id="city" type="text" name="city"
                                        class="w-full border border-gray-300 rounded px-3 py-2"
                                        placeholder="e.g. Bangalore">
                                </div>

                                <!-- State -->
                                <div>
                                    <label for="state" class="text-gray-700">State</label>
                                    <input id="state" type="text" name="state"
                                        class="w-full border border-gray-300 rounded px-3 py-2"
                                        placeholder="e.g. Karnataka">
                                </div>

                                <!-- Pincode -->
                                <div>
                                    <label for="pincode" class="text-gray-700">Pincode</label>
                                    <input id="pincode" type="text" name="pincode"
                                        class="w-full border border-gray-300 rounded px-3 py-2" placeholder="e.g. 560001">
                                </div>

                                <!-- Country -->
                                <div>
                                    <label for="country" class="text-gray-700">Country</label>
                                    <input id="country" type="text" name="country"
                                        class="w-full border border-gray-300 rounded px-3 py-2" placeholder="e.g. India">
                                </div>

                                <!-- Phone Number 1 -->
                                <div>
                                    <label for="phone_1" class="text-gray-700">Phone Number 1</label>
                                    <input id="phone_1" type="text" name="phone_1"
                                        class="w-full border border-gray-300 rounded px-3 py-2"
                                        placeholder="e.g. +91 9876543210">
                                </div>

                                <!-- Phone Number 2 -->
                                <div>
                                    <label for="phone_2" class="text-gray-700">Phone Number 2</label>
                                    <input id="phone_2" type="text" name="phone_2"
                                        class="w-full border border-gray-300 rounded px-3 py-2"
                                        placeholder="e.g. +91 9123456780">
                                </div>

                                <!-- E-Mail 1 -->
                                <div>
                                    <label for="email_1" class="text-gray-700">E-Mail 1</label>
                                    <input id="email_1" type="email" name="email_1"
                                        class="w-full border border-gray-300 rounded px-3 py-2"
                                        placeholder="e.g. john@example.com">
                                </div>

                                <!-- E-Mail 2 -->
                                <div>
                                    <label for="email_2" class="text-gray-700">E-Mail 2</label>
                                    <input id="email_2" type="email" name="email_2"
                                        class="w-full border border-gray-300 rounded px-3 py-2"
                                        placeholder="e.g. contact@acme.com">
                                </div>

                                <!-- GSTN -->
                                <div>
                                    <label for="gstn" class="text-gray-700">GSTN</label>
                                    <input id="gstnNum" type="text" name="gstnNum"
                                        class="w-full border border-gray-300 rounded px-3 py-2"
                                        placeholder="e.g. 29ABCDE1234F2Z5">
                                </div>

                                <!-- Others -->
                                <div>
                                    <label for="others" class="text-gray-700">Others</label>
                                    <input id="others" type="text" name="others"
                                        class="w-full border border-gray-300 rounded px-3 py-2"
                                        placeholder="e.g. Additional note">
                                </div>

                                <!-- Lat/Long -->
                                <div>
                                    <label for="latlong" class="text-gray-700">Lat/Long</label>
                                    <div class="flex">
                                        <input id="clatlon" type="text" name="clatlon"
                                            class="flex-grow border border-gray-300 rounded-l-lg px-3 py-2 focus:outline-none focus:ring-2 "
                                            placeholder="e.g. 12.9716,77.5946" readonly />
                                        <button type="button"
                                            class="bg-blue-600 hover:bg-blue-700 text-white rounded-r-lg px-4 flex items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" id="openMapModal" class="h-5 w-5"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                <div class="md:col-span-3 flex items-center">
                                    <input id="billing_diff" type="checkbox"
                                        class="h-4 w-4 text-blue-600 border-gray-300 rounded" />
                                    <label for="billing_diff" class="ml-2 text-gray-700">
                                        Billing address is different
                                    </label>
                                </div>
                                <!--Billing Address -->
                                <div id="billingAddressCard" class="md:col-span-3 hidden">
                                    <hr>
                                    <div class="grid md:grid-cols-3 mt-3 gap-4">
                                        <!-- Customer Legal Name -->
                                        <div>
                                            <label for="customer_legal_name" class="text-gray-700">Customer Legal
                                                Name</label>
                                            <input id="bill_customer_legal_name" type="text" name="bill_customer_legal_name"
                                                class="w-full border border-gray-300 rounded px-3 py-2"
                                                placeholder="e.g. Acme Corporation">
                                        </div>

                                        <div>
                                            <label for="customer_trade_name" class="text-gray-700">Customer Trade
                                                Name</label>
                                            <input id="bill_customer_trade_name" type="text" name="bill_customer_trade_name"
                                                class="w-full border border-gray-300 rounded px-3 py-2"
                                                placeholder="e.g. Acme Trading">
                                        </div>

                                        <div>
                                            <label for="contact_person" class="text-gray-700">Contact Person</label>
                                            <input id="bill_contact_person" type="text" name="bill_contact_person"
                                                class="w-full border border-gray-300 rounded px-3 py-2"
                                                placeholder="e.g. John Doe">
                                        </div>

                                        <div>
                                            <label for="designation" class="text-gray-700">Designation</label>
                                            <input id="bill_designation" type="text" name="bill_designation"
                                                class="w-full border border-gray-300 rounded px-3 py-2"
                                                placeholder="e.g. Sales Manager">
                                        </div>
                                        <div>
                                            <label for="phone_2" class="text-gray-700">Phone Number 2</label>
                                            <input id="bill_phone" type="text" name="bill_phone"
                                                class="w-full border border-gray-300 rounded px-3 py-2"
                                                placeholder="e.g. +91 9123456780">
                                        </div>

                                        <!-- E-Mail 1 -->
                                        <div>
                                            <label for="email_1" class="text-gray-700">E-Mail 1</label>
                                            <input id="bill_email" type="email" name="bill_email"
                                                class="w-full border border-gray-300 rounded px-3 py-2"
                                                placeholder="e.g. john@example.com">
                                        </div>
                                        <div class="md:col-span-3">
                                            <label for="bill_address" class="text-gray-700">Address*</label>
                                            <textarea id="bill_address" name="bill_address"
                                                class="w-full border border-gray-300 rounded px-3 py-2 h-24"
                                                placeholder="e.g. 123 Corporate Park, Bangalore"></textarea>
                                        </div>
                                        <div>
                                            <label for="bill_city" class="text-gray-700">City*</label>
                                            <input id="bill_city" name="bill_city" type="text"
                                                class="w-full border border-gray-300 rounded px-3 py-2"
                                                placeholder="e.g. Bangalore">
                                        </div>
                                        <div>
                                            <label for="bill_state" class="text-gray-700">State*</label>
                                            <input id="bill_state" name="bill_state" type="text"
                                                class="w-full border border-gray-300 rounded px-3 py-2"
                                                placeholder="e.g. Karnataka">
                                        </div>
                                        <div>
                                            <label for="bill_pincode" class="text-gray-700">Pincode*</label>
                                            <input id="bill_pincode" name="bill_pincode" type="text"
                                                class="w-full border border-gray-300 rounded px-3 py-2"
                                                placeholder="e.g. 560001">
                                        </div>
                                        <div class="md:col-span-2">
                                            <label for="bill_landmark" class="text-gray-700">Landmark</label>
                                            <input id="bill_landmark" name="bill_landmark" type="text"
                                                class="w-full border border-gray-300 rounded px-3 py-2"
                                                placeholder="e.g. Opposite City Mall">
                                        </div>
                                        <div>
                                            <label for="bill_country" class="text-gray-700">Country*</label>
                                            <input id="bill_country" name="bill_country" type="text"
                                                class="w-full border border-gray-300 rounded px-3 py-2"
                                                placeholder="e.g. India">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="md:col-span-3">
                    <div class="bg-white border border-gray-200 rounded-lg shadow p-6">
                        <h3 class="text-xl font-semibold text-gray-800 mb-4">Service Details</h3>
                        <div class="grid md:grid-cols-3 gap-4">

                            <!-- Category -->
                            <div class="md:col-span-3">
                                <label class="text-gray-700">Category</label>
                                <select name="category" id="category"
                                    class="w-full select2 border border-gray-300 rounded px-3 py-2">
                                    <option value="">Category</option>
                                </select>
                            </div>

                            <!-- Sub Category -->
                            <div class="md:col-span-3">
                                <label class="text-gray-700 mb-2 block">Sub Category</label>
                                <select name="sub_category[]" id="sub_category" multiple
                                    class="w-full select2 border border-gray-300 rounded px-3 py-2">
                                </select>
                            </div>
                            <div id="subcategory-cards" class="md:col-span-3">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="md:col-span-3">
                    <div class="bg-white border border-gray-200 rounded-lg shadow p-6">
                        <h3 class="text-xl font-semibold text-gray-800 mb-4">Others Details</h3>
                        <div class="grid md:grid-cols-3 gap-4">
                            <!-- Audit Requirement -->
                            <div>
                                <label for="audit_requirement" class="text-gray-700">Audit Requirement*</label>
                                <select name="audit_requirement" id="audit_requirement"
                                    class="w-full select2 border border-gray-300 rounded px-3 py-2">
                                    <option value="">Audit Requirement</option>
                                    <option value="One Time">One Time</option>
                                    <option value="Monthly">Monthly</option>
                                    <option value="Bi-Monthly">Bi-Monthly</option>
                                    <option value="Quarterly">Quarterly</option>
                                    <option value="Half Yearly">Half Yearly</option>
                                    <option value="Yearly">Yearly</option>
                                    <option value="No">No</option>
                                </select>
                            </div>
                            <div>
                                <label for="technician_assign" class="text-gray-700">Technician Assign</label>
                                <select name="technician_assign" id="technician_assign"
                                    class="w-full select2 border border-gray-300 rounded px-3 py-2">
                                    <option value="">Technician Assign</option>
                                    <option value="Preferred">Preferred</option>
                                    <option value="Required">Required</option>
                                </select>
                            </div>
                            <div>
                                <label for="technician_assign" class="text-gray-700">Technician</label>
                                <select name="technician" id="technician"
                                    class="w-full select2 border border-gray-300 rounded px-3 py-2">
                                    <option value="">Technician</option>
                                </select>
                            </div>

                            <div>
                                <label for="contract_start_date" class="text-gray-700">Contract Start Date</label>
                                <input type="date" name="contract_start_date" id="contract_start_date"
                                    class="w-full border border-gray-300 rounded px-3 py-2" />
                            </div>

                            <div>
                                <label for="total_order_value" class="text-gray-700">Total Order Value</label>
                                <input type="number" name="total_order_value" id="total_order_value" step="0.01"
                                    class="w-full border border-gray-300 rounded px-3 py-2" placeholder="0.00" readonly />
                            </div>

                            <div>
                                <label for="discount" class="text-gray-700">Discount</label>
                                <input type="number" name="discount" id="discount" step="0.01"
                                    class="w-full border border-gray-300 rounded px-3 py-2" placeholder="0.00" />
                            </div>

                            <div>
                                <label for="discounted_price" class="text-gray-700">Discounted Price</label>
                                <input type="number" name="discounted_price" id="discounted_price" step="0.01" readonly
                                    class="w-full bg-gray-100 border border-gray-300 rounded px-3 py-2"
                                    placeholder="0.00" />
                            </div>

                            <div>
                                <label for="sez" class="text-gray-700">SEZ</label>
                                <select name="sez" id="sez" class="w-full select2 border border-gray-300 rounded px-3 py-2">
                                    <option value="">SEZ</option>
                                    <option value="yes">Yes</option>
                                    <option value="no">No</option>
                                </select>
                            </div>
                            <div>
                                <label for="tax" class="text-gray-700">Taxibility</label>
                                <input type="number" name="tax" id="tax" placeholder="0.00"
                                    class="w-full border border-gray-300 rounded px-3 py-2" />
                            </div>
                            <div class="md:col-span-3">
                                <label for="final_price" class="text-gray-700">Final Price</label>
                                <input type="number" name="final_price" id="final_price" step="0.01" readonly
                                    class="w-full bg-gray-100 border border-gray-300 rounded px-3 py-2"
                                    placeholder="0.00" />
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Submit Button -->
                <div class="pt-4">
                    <button type="submit"
                        class="bg-blue-600 text-white px-4 py-2 rounded addOrder hover:bg-blue-700">Submit</button>
                </div>
            </form>
        </div>
    </div>
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden" id="serviceCenterModal">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-3xl p-6 overflow-y-auto max-h-[90vh]">
            <div class="flex justify-between items-center border-b pb-4 mb-4">
                <h2 class="text-xl font-semibold">Add Sub Business Region</h2>
                <button type="button" class="text-gray-500 closeModal hover:text-gray-700 text-xl">&times;</button>
            </div>
            <form id="serviceCenterForm" action="{{ route('orders.serviceStore') }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Region Name</label>
                        <select name="region_name" id="region_name"
                            class="w-full select2 border border-gray-300 rounded px-3 py-2">

                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Name of the Branch</label>
                        <input type="text" name="branch_name" placeholder="e.g. Mumbai Branch"
                            class="w-full border border-gray-300 rounded px-3 py-2">
                    </div>
                </div>
                <!-- Branch Code -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Branch Code</label>
                    <input type="text" name="branch_code" placeholder="e.g. HM-001"
                        class="w-full border border-gray-300 rounded px-3 py-2 bg-gray-100">
                </div>
                <!-- Office Address -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Registered Office Address</label>
                    <textarea name="office_address" rows="2" placeholder="e.g. 123 Business St, City"
                        class="w-full border border-gray-300 rounded px-3 py-2"></textarea>
                </div>
                <!-- GSTN and Agri Licence -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">GST Number</label>
                        <select name="gstn" class="w-full border select2 border-gray-300 rounded px-3 py-2"
                            onchange="toggleImageUpload()">
                            <option value="" disabled selected>- Select GST Status -</option>
                            <option value="yes">Yes</option>
                            <option value="no">No</option>
                            <option value="pending">Pending</option>
                        </select>
                        <div id="gstImageContainer" class="mt-2 hidden">
                            <label class="block text-sm font-medium text-gray-700">Upload GST Image</label>
                            <input type="file" name="gst_image[]" multiple
                                class="w-full border border-gray-300 rounded px-3 py-2">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Agri Licence</label>
                        <select name="agri_licence" class="w-full select2 border border-gray-300 rounded px-3 py-2"
                            onchange="toggleImageUpload()">
                            <option value="" disabled selected>- Select Agri Licence Status -</option>
                            <option value="yes">Yes</option>
                            <option value="no">No</option>
                            <option value="pending">Pending</option>
                        </select>
                        <div id="agriImageContainer" class="mt-2 hidden">
                            <label class="block text-sm font-medium text-gray-700">Upload Agri Licence Image</label>
                            <input type="file" name="agri_image[]" multiple
                                class="w-full border border-gray-300 rounded px-3 py-2">
                        </div>
                    </div>
                </div>
                <!-- Shop & Establishment and Contact Person -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Shop and Establishment</label>
                        <select name="shop_establishment" class="w-full select2 border border-gray-300 rounded px-3 py-2"
                            onchange="toggleImageUpload()">
                            <option value="" disabled selected>- Select Status -</option>
                            <option value="yes">Yes</option>
                            <option value="no">No</option>
                            <option value="pending">Pending</option>
                        </select>
                        <div id="shopImageContainer" class="mt-2 hidden">
                            <label class="block text-sm font-medium text-gray-700">Upload Shop Establishment
                                Image</label>
                            <input type="file" name="shop_image[]" multiple
                                class="w-full border border-gray-300 rounded px-3 py-2">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Contact Person Name</label>
                        <input type="text" name="contact_person_name" placeholder="e.g. John Doe"
                            class="w-full border border-gray-300 rounded px-3 py-2">
                    </div>
                </div>
                <!-- Contact Number and Email -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Contact Number</label>
                        <input type="text" name="contact_number" placeholder="e.g. +91 9876543210"
                            class="w-full border border-gray-300 rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email ID</label>
                        <input type="email" name="email_id" placeholder="e.g. contact@company.com"
                            class="w-full border border-gray-300 rounded px-3 py-2">
                    </div>
                </div>
                <!-- Submit Button -->
                <div class="text-center">
                    <button type="submit"
                        class="bg-blue-600 addserviceCenterBtn hover:bg-blue-700 text-white px-6 py-2 rounded shadow">
                        Create Branch
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="addBusinessModel" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-3xl p-6 overflow-y-auto max-h-[90vh]">
            <div class="flex justify-between items-center border-b pb-3">
                <h2 class="text-lg font-semibold text-gray-800">Add Business Region</h2>
                <button type="button" class="text-gray-500 closeBusinessModal hover:text-gray-700 text-xl">&times;</button>
            </div>
            <form id="businessForm" action="{{ route('orders.businessStore') }}" method="POST" class="mt-4 space-y-4">
                @csrf
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="w-full md:w-1/2">
                        <label for="regionName" class="block text-sm font-medium text-gray-700">Region Name</label>
                        <input type="text" id="regionName" name="regionName" placeholder="Enter region name"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="w-full md:w-1/2">
                        <label for="stateName" class="block text-sm font-medium text-gray-700">State's Name</label>
                        <input type="text" id="stateName" name="stateName" placeholder="Enter state name"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
                <div class="text-right">
                    <button type="submit" class="bg-blue-600 text-white addbusinessBtn px-4 py-2 rounded hover:bg-blue-700">
                        Save Region
                    </button>
                </div>
            </form>

        </div>
    </div>
    <!-- MAP MODEL -->
    <div id="mapModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-3xl p-6 overflow-y-auto max-h-[90vh]">
            <div class="flex justify-between items-center border-b pb-3">
                <h2 class="text-lg font-semibold text-gray-800">Search location</h2>
                <button id="closeMapModal" type="button" class="text-gray-500  hover:text-gray-700 text-xl">&times;</button>
            </div>
            <div class="px-6 py-4">
                <div>
                    <input id="pac-input" type="search"
                        class="w-full pl-3 pr-8 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Search..." />
                    <div id="map" class="w-full h-[400px] "></div>
                    <div id="info" class="mt-2 text-sm text-gray-600"></div>
                </div>
            </div>
            <div class="flex items-center justify-between border-t px-6 py-4">
                <div id="latlong" class="text-sm text-gray-700"></div>
                <button type="button" id="save-location"
                    class="bg-blue-600 text-white px-4 py-2 hover:bg-blue-700 focus:outline-none focus:ring focus:ring-blue-300">
                    Save
                </button>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function () {
            const csrf = '{{ csrf_token() }}';
            let serviceTypes = [];
            let sectors = [];

            // Show Service Center Modal
            $('#addServiceCenter').on('click', function () {
                $('#serviceCenterModal').removeClass('hidden').addClass('flex');
                getBusinessRegion();
            });

            // Close Service Center Modal
            $('.closeModal').on('click', function () {
                $('#serviceCenterModal').removeClass('flex').addClass('hidden');
            });

            // Show Business Region Modal
            $('#addaddBusiness').on('click', function () {
                $('#addBusinessModel').removeClass('hidden').addClass('flex');
            });

            // Close Business Region Modal
            $('.closeBusinessModal').on('click', function () {
                $('#addBusinessModel').removeClass('flex').addClass('hidden');
            });

            // Add Search Box  in select fild Select
            $('.select2').select2({
                placeholder: 'Select an option',
                allowClear: true,
                width: '100%',
            });

            // Get Business Region
            function getBusinessRegion() {
                $.ajax({
                    url: "{{ route('orders.getBusinessRegion') }}",
                    method: "GET",
                    success: function (response) {
                        if (response.success && response.data) {
                            const options = ['<option value="">Business Region</option>'];
                            response.data.forEach(region => {
                                options.push(`<option value="${region.id}">${region.state}</option>`);
                            });

                            // Fill both select fields
                            $('#business_region').html(options.join(''));
                            $('#region_name').html(options.join(''));
                        } else {
                            $.toastr.error("Failed to load business regions.");
                        }
                    },
                    error: function () {
                        $.toastr.error("Something went wrong while fetching regions.");
                    }
                });
            }
            // load business regions 
            getBusinessRegion();

            // Business Sub Region
            $('#business_region').on('change', function () {
                const regionId = $(this).val();
                $('#business_sub_region').html('<option value="">Business Sub Region</option>');
                $('#branch_codes').val('');
                if (regionId) {
                    $.post('{{ route('orders.getBranchCode') }}', {
                        region_id: regionId,
                        _token: csrf
                    }, function (response) {
                        if (response.success && response.data.length > 0) {
                            const options = response.data.map(c =>
                                `<option value="${c.id}" data-branch-code="${c.branch_code}">${c.branch_name}</option>`
                            );
                            $('#business_sub_region').append(options);
                        } else {
                            $('#branch_codes').val('');
                            $.toastr.error(response.message);
                        }
                    }).fail(() => $.toastr.error('Error fetching sub-regions.'));
                }
            });
            // Branch Code
            $('#business_sub_region').on('change', function () {
                const branchCode = $(this).find(':selected').data('branch-code') || '';
                $('#branch_codes').val(branchCode);
            });
            //  Customer Type
            $('#customer_type').on('change', function () {
                if ($(this).val() === 'new') {
                    $('#businessLeadDiv').hide();
                    $('#mobileInputDiv').removeClass('hidden');
                } else {
                    $('#businessLeadDiv').show();
                    $('#mobileInputDiv').addClass('hidden');
                }
            });
            $('#customer_legal_name').on('input', function () {
                $('#customer_trade_name').val($(this).val());
            });
            $('#billing_diff').on('change', function () {
                $('#billingAddressCard').toggle(this.checked);
            });
            $('#bill_customer_legal_name').on('input', function () {
                $('#bill_customer_trade_name').val($(this).val());
            });
            // MAP
            var map, marker, searchBox;
            function InitMap() {
                var location;
                var latLon = $('#clatlon').val();
                if (latLon) {
                    var latLng = latLon.split(',');
                    var lat = parseFloat(latLng[0]);
                    var lng = parseFloat(latLng[1]);

                    if (!isNaN(lat) && !isNaN(lng)) {
                        location = new google.maps.LatLng(lat, lng);
                    } else {
                        location = new google.maps.LatLng(12.9715987, 77.5945627);
                    }
                } else {
                    location = new google.maps.LatLng(12.9715987, 77.5945627);
                }

                var mapOptions = {
                    zoom: 12,
                    center: location,
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                };

                map = new google.maps.Map($('#map')[0], mapOptions);

                marker = new google.maps.Marker({
                    map: map,
                    position: location,
                    draggable: true
                });

                searchBox = new google.maps.places.SearchBox($('#pac-input')[0]);
                map.controls[google.maps.ControlPosition.TOP_CENTER].push($('#pac-input')[0]);

                google.maps.event.addListener(searchBox, 'places_changed', function () {
                    var places = searchBox.getPlaces();
                    if (places.length === 0) return;

                    var bounds = new google.maps.LatLngBounds();
                    $.each(places, function (i, place) {
                        if (!place.geometry || !place.geometry.location) return;
                        marker.setPosition(place.geometry.location);
                        bounds.extend(place.geometry.location);
                    });

                    map.fitBounds(bounds);
                    map.setZoom(Math.min(map.getZoom(), 12));
                });
            }

            $('#save-location').on('click', function () {
                var position = marker.getPosition();
                console.log("Latitude: " + position.lat() + ", Longitude: " + position.lng());
                $('#clatlon').val(position.lat() + "," + position.lng());
                $('#mapModal').removeClass('flex').addClass('hidden');
            });

            $('#openMapModal').on('click', function () {
                $('#mapModal').removeClass('hidden').addClass('flex');
                if (!map) {
                    InitMap();
                }
                var $input = $('#pac-input');
                if ($input.length) {
                    var autocomplete = new google.maps.places.Autocomplete($input[0]);
                    autocomplete.setFields(['place_id', 'geometry', 'name']);
                    autocomplete.addListener('place_changed', function () {
                        var place = autocomplete.getPlace();
                        if (place.geometry) {
                            var lat = place.geometry.location.lat();
                            var lng = place.geometry.location.lng();
                            map.setCenter(place.geometry.location);
                            map.setZoom(13);
                            marker.setPosition(place.geometry.location);
                            $('#coordinates').val(lat + ", " + lng);
                            $('#info').html('Selected: ' + place.name);
                        }
                    });
                }
            });

            $('#closeMapModal').on('click', function () {
                $('#mapModal').removeClass('flex').addClass('hidden');
            });

            // Category
            function getgetCategory() {
                $.ajax({
                    url: "{{ route('orders.getCategory') }}",
                    method: "GET",
                    success: function (response) {
                        if (response.success && response.data) {
                            const options = ['<option value="">Category</option>'];
                            response.data.forEach(category => {
                                options.push(`<option value="${category.id}">${category.category_name}</option>`);
                            });
                            $('#category').html(options.join(''));
                        } else {
                            $.toastr.error(response.message);
                        }
                    },
                    error: function () {
                        $.toastr.error("Something went wrong while fetching categories.");
                    }
                });
            }
            getgetCategory();

            // Sub Category
            function getSubCategory(categoryId) {
                $.ajax({
                    url: "{{ route('orders.getSubCategory') }}",
                    method: "POST",
                    data: {
                        cat_id: categoryId,
                        _token: csrf
                    },
                    success: function (response) {
                        if (response.success && response.data) {
                            const options = [];
                            response.data.forEach(sub => {
                                options.push(`<option value="${sub.id}">${sub.subcategory_name}</option>`);
                            });
                            $('#sub_category').html(options.join(''));
                            $('#sub_category').trigger('change');
                        } else {
                            $.toastr.error(response.message || "No subcategories found.");
                        }
                    },
                    error: function () {
                        $.toastr.error("Something went wrong while fetching subcategories.");
                    }
                });
            }

            $('#category').on('change', function () {
                const categoryId = $(this).val();
                if (categoryId) {
                    getSubCategory(categoryId);
                } else {
                    $('#sub_category').html('<option value="">Sub Category</option>');
                }
            });
            $('#sub_category').select2({
                placeholder: "Select Sub Categories",
                allowClear: true
            });

            // Container for dynamic cards (place this in your HTML under Sub Category)
            // <div id="subcategory-cards" class="grid md:grid-cols-2 gap-4 mt-6"></div>

            // serviceType
            function getServiceType() {
                $.ajax({
                    url: "{{ route('orders.getServiceType') }}",
                    method: "GET",
                    success: function (response) {
                        if (response.success && response.data) {
                            serviceTypes = response.data;
                            const options = ['<option value="">Service Type</option>'];
                            response.data.forEach(type => {
                                options.push(`<option value="${type.id}">${type.service_type}</option>`);
                            });
                            $('#service_type').html(options.join(''));
                        } else {
                            $.toastr.error(response.message);
                        }
                    },
                    error: function () {
                        $.toastr.error("Something went wrong while fetching service types.");
                    }
                });
            }
            getServiceType();



            // Sector
            function getSector() {
                $.ajax({
                    url: "{{ route('orders.getSector') }}",
                    method: "GET",
                    success: function (response) {
                        if (response.success && response.data) {
                            sectors = response.data;
                            const options = ['<option value="">Sector</option>'];
                            response.data.forEach(sector => {
                                options.push(`<option value="${sector.id}">${sector.sector_code}-${sector.sector_name}</option>`);
                            });
                            $('#sector').html(options.join(''));
                        } else {
                            $.toastr.error(response.message);
                        }
                    },
                    error: function () {
                        $.toastr.error("Something went wrong while fetching Sector.");
                    }
                });
            }
            getSector();

            // Dynamic cards for each selected sub-category
            function generateSubcategoryCards() {
                const selected = $('#sub_category').select2('data');
                const $container = $('#subcategory-cards').empty();

                selected.forEach(sub => {
                    const id = sub.id,
                        text = sub.text;

                    let typeOpts = '<option value="">Service Type</option>';
                    serviceTypes.forEach(t => {
                        typeOpts += `<option value="${t.id}">${t.service_type}</option>`;
                    });

                    let freqOpts = '<option value="">Service Frequency</option>';
                    // will populate dynamically when service_type changes

                    let sectorOpts = '<option value="">Sector</option>';
                    sectors.forEach(s => {
                        sectorOpts += `<option value="${s.id}">${s.sector_code}-${s.sector_name}</option>`;
                    });

                    const card = `
                                      <div class="bg-white mt-3 rounded-lg p-4">
                                        <!-- sub-category ID -->
                                        <input type="hidden" 
                                               name="services[${id}][sub_category]" 
                                               value="${id}" />

                                        <h4 class="text-lg font-medium text-gray-800 mb-4">${text}</h4>
                                        <div class="grid md:grid-cols-3 gap-4">

                                          <div>
                                            <label class="text-gray-700">Service Type*</label>
                                            <select name="services[${id}][service_type]"
                                                    data-subid="${id}"
                                                    class="w-full select2 service_type border border-gray-300 rounded px-3 py-2">
                                              ${typeOpts}
                                            </select>
                                          </div>

                                          <div>
                                            <label class="text-gray-700">Service Frequency*</label>
                                            <select name="services[${id}][service_frequency]"
                                                    data-subid="${id}"
                                                    class="w-full select2 service_frequency border border-gray-300 rounded px-3 py-2">
                                              ${freqOpts}
                                            </select>
                                          </div>

                                          <div>
                                            <label class="text-gray-700">No. of Services*</label>
                                            <input type="text" readonly
                                                   name="services[${id}][no_of_services]"
                                                   id="no_of_services_${id}"
                                                   placeholder="Auto-generated"
                                                   class="w-full border border-gray-300 rounded px-3 py-2" />
                                          </div>

                                          <div>
                                            <label class="text-gray-700">Scheduled Every*</label>
                                            <input type="text" readonly
                                                   name="services[${id}][scheduled_day]"
                                                   id="scheduled_day_${id}"
                                                   placeholder="Auto-generated"
                                                   class="w-full border border-gray-300 rounded px-3 py-2" />
                                          </div>

                                          <div>
                                            <label class="text-gray-700">Sector*</label>
                                            <select name="services[${id}][sector]"
                                                    data-subid="${id}"
                                                    class="w-full select2 sector border border-gray-300 rounded px-3 py-2">
                                              ${sectorOpts}
                                            </select>
                                          </div>
                                         <div>
                                            <label class="text-gray-700">Service price*</label>
                                            <input type="text" 
                                                  name="services[${id}][price]"
                                                  id="price_${id}"
                                                   placeholder="e.g. 500."
                                                   class="w-full border border-gray-300 price_input rounded px-3 py-2" />
                                          </div>
                                          </div>
                                        </div>
                                      </div>
                                    `;
                    $container.append(card);
                });

                // re-init select2 on the newly added fields
                $container.find('select.select2').select2({
                    placeholder: 'Select an option',
                    allowClear: true,
                    width: '100%',
                });
            }


            $('#sub_category').on('change', generateSubcategoryCards);

            // Delegated handlers for dynamic cards
            $(document).on('change', '.service_type', function () {
                const subId = $(this).data('subid'),
                    typeId = $(this).val(),
                    $freq = $(`.service_frequency[data-subid="${subId}"]`);
                $freq.html('<option value="">Service Frequency</option>');
                $(`#no_of_services_${subId}`).val('');
                $(`#scheduled_day_${subId}`).val('');

                if (!typeId) return;
                $.post("{{ route('orders.getServiceFrequency') }}", {
                    service_type_id: typeId,
                    _token: csrf
                }).done(res => {
                    if (res.success && res.data) {
                        res.data.forEach(f => {
                            $freq.append(
                                `<option value="${f.id}"
                                        data-services="${f.no_of_services}"
                                        data-day="${f.scheduled_day}">
                                  ${f.service_frequency}
                                </option>`
                            );
                        });
                    } else {
                        $.toastr.error(res.message || 'No frequencies found.');
                    }
                }).fail(() => $.toastr.error('Error fetching frequencies.'));
            });

            $(document).on('change', '.service_frequency', function () {
                const subId = $(this).data('subid'),
                    $opt = $(this).find('option:selected');
                $(`#no_of_services_${subId}`).val($opt.data('services') || '');
                $(`#scheduled_day_${subId}`).val($opt.data('day') || '');
            });

            // Service Center Form Submission
            $('#serviceCenterForm').on('submit', function (e) {
                e.preventDefault();
                const form = this;
                const formData = new FormData(form);
                $.ajax({
                    url: form.action,
                    type: form.method,
                    data: formData,
                    contentType: false,
                    processData: false,
                    beforeSend: function () {
                        $(".addserviceCenterBtn").html(
                            '<span class="animate-spin inline-block w-4 h-4 border-t-2 border-white rounded-full mr-2"></span> Processing...'
                        ).prop('disabled', true);
                    },
                    success: function (response) {
                        if (response.success) {
                            $.toastr.success(response.message);
                            handleSuccessResponse(form, 'serviceCenterModal');
                        } else {
                            $.toastr.error(response.message);
                        }
                    },
                    error: function (xhr) {
                        handleValidationErrors(xhr);
                    },
                    complete: function () {
                        $(".addserviceCenterBtn").html("Submit").prop('disabled', false);
                    }
                });
            });
            //  Order Form Submit
            $('#orderForm').on('submit', function (e) {
                e.preventDefault();
                const form = this;
                const formData = new FormData(form);
                $.ajax({
                    url: form.action,
                    type: form.method,
                    data: formData,
                    contentType: false,
                    processData: false,
                    beforeSend: function () {
                        $(".addOrder").html(
                            '<span class="animate-spin inline-block w-4 h-4 border-t-2 border-white rounded-full mr-2"></span> Processing...'
                        ).prop('disabled', true);
                    },
                    success: function (response) {
                        if (response.success) {
                            $.toastr.success(response.message);
                        } else {
                            $.toastr.error(response.message);
                        }
                    },
                    error: function (xhr) {
                        handleValidationErrors(xhr);
                    },
                    complete: function () {
                        $(".addOrder").html("Submit").prop('disabled', false);
                    }
                });
            });

            // Business Region Form Submission
            $('#businessForm').on('submit', function (e) {
                e.preventDefault();
                const form = this;
                const formData = new FormData(form);
                $.ajax({
                    url: form.action,
                    type: form.method,
                    data: formData,
                    contentType: false,
                    processData: false,
                    beforeSend: function () {
                        $(".addbusinessBtn").html(
                            '<span class="animate-spin inline-block w-4 h-4 border-t-2 border-white rounded-full mr-2"></span> Processing...'
                        ).prop('disabled', true);
                    },
                    success: function (response) {
                        if (response.success) {
                            $.toastr.success(response.message);
                            handleSuccessResponse(form, 'addBusinessModel');
                        } else {
                            $.toastr.error(response.message);
                        }
                    },
                    error: function (xhr) {
                        handleValidationErrors(xhr);
                    },
                    complete: function () {
                        $(".addbusinessBtn").html("Submit").prop('disabled', false);
                    }
                });
            });
            function handleValidationErrors(xhr) {
                $('.error-text').remove();
                $('input, select, textarea').removeClass('border-red-500');
                $('.select2').next().removeClass('border-red-500');

                if (xhr.status === 422 && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    let firstErrorField = null;

                    Object.keys(errors).forEach(key => {

                        const nameAttr = key.split('.').reduce((acc, part, idx) => {
                            return idx === 0 ? part : acc + `[${part}]`;
                        }, '');

                        const $input = $(`[name="${nameAttr}"]`);
                        const msgs = errors[key];

                        $input.addClass('border-red-500');
                        if ($input.hasClass('select2-hidden-accessible')) {
                            const $container = $input.next('.select2');
                            $container.addClass('border-red-500');
                            $container.after(`<span class="text-red-500 text-sm error-text">${msgs[0]}</span>`);
                        } else {
                            $input.after(`<span class="text-red-500 text-sm error-text">${msgs[0]}</span>`);
                        }
                        $.toastr.error(msgs[0]);

                        if (!firstErrorField) {
                            firstErrorField = $input;
                        }
                    });
                    if (firstErrorField) {
                        if (firstErrorField.hasClass('select2-hidden-accessible')) {
                            firstErrorField.select2('open');
                        } else {
                            firstErrorField.focus();
                        }
                    }
                } else {
                    $.toastr.error("Something went wrong!");
                }
            }



            function handleSuccessResponse(form, modalId) {
                $(`#${modalId}`).removeClass('flex').addClass('hidden');
                form.reset();
            }

            $(document).on('input', '.price_input', function () {
               
                let total = 0;
                $('.price_input').each(function () {
                    const v = parseFloat($(this).val());
                    if (!isNaN(v)) total += v;
                });
                 console.log(total);
                $('#total_order_value').val(total.toFixed(2));

                calculatePrice();
            });

            $('#discount, #tax').on('input', calculatePrice);

            function calculatePrice() {
                const total = +$('#total_order_value').val() || 0;
                const disc = +$('#discount').val() || 0;
                const dp = total - disc;
                $('#discounted_price').val(dp.toFixed(2));

                const tax = +$('#tax').val() || 0;
                $('#final_price').val((dp + tax).toFixed(2));
            }


            $('#total_order_value, #discount, #tax, #sez')
                .on('input change', calculatePrice);
            calculatePrice();

        });

        function toggleImageUpload() {
            $('#gstImageContainer').toggle($('[name="gstn"]').val() === 'yes');
            $('#agriImageContainer').toggle($('[name="agri_licence"]').val() === 'yes');
            $('#shopImageContainer').toggle($('[name="shop_establishment"]').val() === 'yes');
        }
    </script>

@endsection