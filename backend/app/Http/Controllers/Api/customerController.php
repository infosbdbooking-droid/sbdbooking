<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\Customer;

class CustomerController extends Controller
{
    /**
     * Login OR Register customer using mobile + password
     */
    public function loginOrRegister(Request $request)
    {
        try {
            // ✅ Validation
            $validator = Validator::make($request->all(), [
                'mobile'   => 'required|digits_between:10,15',
                'password' => 'required|min:6',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => 0,
                    'message' => $validator->errors()->first(),
                    'data'    => null
                ], 422);
            }

            // ✅ Check if customer exists
            $customer = Customer::where('mobile', $request->mobile)->first();

            // ======================
            // LOGIN FLOW
            // ======================
            if ($customer) {

                if ($customer->status != 1) {
                    return response()->json([
                        'status'  => 0,
                        'message' => 'Your account is inactive',
                        'data'    => null
                    ], 200);
                }

                if (!Hash::check($request->password, $customer->password)) {
                    return response()->json([
                        'status'  => 0,
                        'message' => 'Invalid password',
                        'data'    => null
                    ], 200);
                }

                // ✅ Create Sanctum Token
                $token = $customer->createToken('customer-token')->plainTextToken;

                // Profile photo path
                $customer->profile_photo = $customer->profile_photo
                    ? asset('storage/app/public/images/customers/' . $customer->profile_photo)
                    : null;

                return response()->json([
                    'status'  => 1,
                    'message' => 'Login successful',
                    'token'   => $token,
                    'data'    => $customer
                ], 200);
            }

            // ======================
            // REGISTRATION FLOW
            // ======================
            $newCustomer = Customer::create([
                'name'     => 'User_' . substr($request->mobile, -4),
                'mobile'   => $request->mobile,
                'password' => Hash::make($request->password),
                'status'   => 1,
            ]);

            // ✅ Create token after registration
            $token = $newCustomer->createToken('customer-token')->plainTextToken;

            return response()->json([
                'status'  => 1,
                'message' => 'Registration successful',
                'token'   => $token,
                'data'    => $newCustomer
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 0,
                'message' => 'Something went wrong',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
