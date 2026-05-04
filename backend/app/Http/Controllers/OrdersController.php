<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\businessRegion;
use App\Models\servicesCenter;
use Illuminate\Support\Facades\Validator;
use App\Models\category;
use App\Models\subCategory;
use App\Models\serviceType;
use App\Models\serviceFrequency;
use App\Models\sector;
use App\Models\orders;

use Yajra\DataTables\DataTables;
class OrdersController extends Controller
{


    #Store Orders
    public function store(Request $request)
    {
       $validator = Validator::make($request->all(), [
            // core fields (all required)
            'site_request' => 'required|string',
            'service_center_type' => 'required|string',
            'employee_name' => 'required|string|max:255',
            'billing' => 'required|string',

            'business_region' => 'required|exists:business_region,id',
            'business_sub_region' => 'required|exists:services_center,id',
            'branch_codes' => 'required|string',

            'customer_type' => 'required|in:new,existing',
            'business_lead' => 'required',
            'mobile_number' => 'required|regex:/^[0-9]{10}$/',
            'customer_legal_name' => 'required|string|max:255',
            'customer_trade_name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'designation' => 'required|string|max:255',
            'address' => 'required|string',
            'landmark' => 'required|string',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'pincode' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'phone_1' => 'required|string|max:20',
            'phone_2' => 'nullable|string|different:phone_1|max:20',
            'email_1' => 'required|email|max:255',
            'email_2' => 'nullable|email|different:email_1|max:255',
            'gstnNum' => 'required|string|max:50',
            'others' => 'required|string',
            'clatlon' => 'required|string',
            'bill_customer_legal_name' => 'nullable|string|max:255',
            'bill_customer_trade_name' => 'nullable|string|max:255',
            'bill_contact_person' => 'nullable|string|max:255',
            'bill_designation' => 'nullable|string|max:255',
            'bill_phone' => 'nullable|string|max:20',
            'bill_email' => 'nullable|email|max:255',
            'bill_address' => 'nullable|string',
            'bill_city' => 'nullable|string|max:100',
            'bill_state' => 'nullable|string|max:100',
            'bill_pincode' => 'nullable|string|max:20',
            'bill_landmark' => 'nullable|string',
            'bill_country' => 'nullable|string|max:100',
            'category' => 'required|exists:categories,id',
            'sub_category' => 'required|array|min:1',
            'sub_category.*' => 'exists:service_type,id',
            'services' => 'required|array|min:1',
            'services.*.sub_category' => 'required|exists:subcategories,id',
            'services.*.service_type' => 'required|exists:service_type,id',
            'services.*.service_frequency' => 'required|exists:service_frequency,id',
            'services.*.no_of_services' => 'required|integer|min:1',
            'services.*.scheduled_day' => 'required|integer|min:1',
            'services.*.sector' => 'required|exists:sector,id',
            'audit_requirement' => 'required|in:One Time,Monthly,Bi-Monthly,Quarterly,Half Yearly,Yearly,No',
            'technician_assign' => 'nullable|in:Preferred,Required',
            'technician' => 'required|exists:technicians,id',
            'contract_start_date' => 'required|date',
            'total_order_value' => 'required|numeric|min:0',
            'discount' => 'required|numeric|min:0|lte:total_order_value',
            'discounted_price' => 'required|numeric|min:0',
            'sez' => 'required|in:yes,no',
            'tax' => 'required|numeric|min:0',
            'final_price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
           return response()->json([
               'success' => false,
               'errors' => $validator->errors()
           ], 422);
        }


    


        

        

    }

    public function getBusinessRegion()
    {
        try {
            $businessRegions = businessRegion::where('status', 1)
                ->orderBy('id', 'asc')
                ->get();
            return response()->json([
                'success' => true,
                'data' => $businessRegions
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to load service center data. Please try again later.',
            ], 500);
        }
    }

    public function getBranchCode(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'region_id' => 'required|integer',
            ]);


            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $regionId = $request->input('region_id');
            $branchCodes = servicesCenter::where('region_id', $regionId)
                ->get(['id', 'branch_code', 'branch_name',]);
            if ($branchCodes->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No branch  found for the selected region.'
                ], 201);
            }

            return response()->json([
                'success' => true,
                'data' => $branchCodes
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to load branch codes. Please try again later.',
            ], 500);
        }
    }

    public function getCategory()
    {
        try {
            $category = category::orderBy('id', 'asc')->get();
            if ($category->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No categories found.'
                ], 201);
            }
            return response()->json([
                'success' => true,
                'data' => $category
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to load categories. Please try again later.',
            ], 500);
        }
    }
    public function getSubCategory(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'cat_id' => 'required|integer',
            ]);


            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $catId = $request->input('cat_id');
            $subCategory = subCategory::where('cat_id', $catId)->get();
            if ($subCategory->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No subcategories found for the selected category.'
                ], 201);
            }

            return response()->json([
                'success' => true,
                'data' => $subCategory
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again later.',
            ], 500);
        }
    }

    public function getServiceType(Request $request)
    {
        try {
            $serviceType = serviceType::where('status', 1)
                ->orderBy('id', 'asc')
                ->get();
            if ($serviceType->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No Service Type found .'
                ], 201);
            }
            return response()->json([
                'success' => true,
                'data' => $serviceType
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to load Service Type. Please try again later.',
            ], 500);
        }
    }

    public function getSector()
    {
        try {
            $sector = Sector::where('status', 1)
                ->orderBy('id', 'asc')
                ->get();
            if ($sector->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No Sector found.'
                ], 201);
            }
            return response()->json([
                'success' => true,
                'data' => $sector
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to load Sector. Please try again later.',
            ], 500);
        }
    }


    public function getserviceFrequency(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'service_type_id' => 'required|integer',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $serviceTypeId = $request->input('service_type_id');
            $serviceFrequency = serviceFrequency::where('service_type_id', $serviceTypeId)
                ->where('status', 1)
                ->get();
            if ($serviceFrequency->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No Service Frequency found for the selected Service Type.'
                ], 201);
            }

            return response()->json([
                'success' => true,
                'data' => $serviceFrequency
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again later.',
            ], 500);
        }
    }


    public function businessStore(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'regionName' => 'required|string|max:255',
                'stateName' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $regionName = strtolower($request->input('regionName'));
            $stateName = strtolower($request->input('stateName'));

            $exists = businessRegion::whereRaw('LOWER(zone) = ?', [$regionName])
                ->whereRaw('LOWER(state) = ?', [$stateName])
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'This region and state combination already exists.'
                ], 201);
            }

            $businessRegion = new businessRegion();
            $businessRegion->zone = ucwords($regionName);
            $businessRegion->state = ucwords($stateName);
            $businessRegion->status = 1;
            $businessRegion->save();

            return response()->json([
                'success' => true,
                'message' => 'Business region added successfully.',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again later.',
            ], 500);
        }
    }

    public function serviceStore(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'region_name' => 'required|integer',
                'branch_name' => 'required|string|max:255',
                'branch_code' => 'required|string|max:255|unique:services_center',
                'office_address' => 'required|string|max:500',
                'gstn' => 'nullable|string|max:100',
                'agri_licence' => 'nullable|string|max:100',
                'shop_establishment' => 'nullable|string|max:100',
                'contact_person_name' => 'required|string|max:255',
                'contact_number' => 'required|string|max:20',
                'email_id' => 'nullable|email|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $serviceCenter = new servicesCenter();
            $serviceCenter->region_id = $request->input('region_name');
            $serviceCenter->branch_name = $request->input('branch_name');
            $serviceCenter->branch_code = $request->input('branch_code');
            $serviceCenter->office_address = $request->input('office_address');
            $serviceCenter->gstn = $request->input('gstn');
            $serviceCenter->agri_licence = $request->input('agri_licence');
            $serviceCenter->shop_establishment = $request->input('shop_establishment');
            $serviceCenter->contact_person_name = $request->input('contact_person_name');
            $serviceCenter->contact_number = $request->input('contact_number');
            $serviceCenter->email_id = $request->input('email_id');
            $serviceCenter->save();
            return response()->json([
                'success' => true,
                'message' => 'Service center added successfully.',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again later.',
            ], 500);
        }
    }

}

?>