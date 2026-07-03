<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Roles;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\DataTables;

class VendorController extends Controller
{
    /**
     * Display a listing of the vendors.
     */
    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                $data = User::whereHas('roles', function ($q) {
                    $q->whereRaw('LOWER(title) = ?', ['vendor']);
                })->with('approvedBy');

                // Filter by verification status
                if ($request->filled('status')) {
                    $data->where('profile_status', $request->status);
                }

                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('approved_by_name', function ($user) {
                        return $user->approvedBy ? $user->approvedBy->name : 'N/A';
                    })
                    ->toJson();
            }

            return view('vendors.index');
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again later.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Approve vendor.
     */
    public function approve($id)
    {
        try {
            $user = User::findOrFail($id);
            if (!$user->isVendor()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User is not a vendor.'
                ], 400);
            }

            $user->profile_status = 'Approved';
            $user->profile_verified_at = now();
            $user->approved_by = Auth::id();
            $user->rejection_reason = null;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Vendor profile has been approved successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve vendor.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject vendor.
     */
    public function reject(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'rejection_reason' => 'required|string|max:1000'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = User::findOrFail($id);
            if (!$user->isVendor()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User is not a vendor.'
                ], 400);
            }

            $user->profile_status = 'Rejected';
            $user->profile_verified_at = null;
            $user->approved_by = null;
            $user->rejection_reason = $request->rejection_reason;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Vendor profile has been rejected.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reject vendor.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update vendor commission settings.
     */
    public function updateCommission(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            if (!$user->isVendor()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User is not a vendor.'
                ], 400);
            }

            $validator = Validator::make($request->all(), [
                'commission_type' => 'required|in:percentage,flat',
                'commission_percentage' => 'required_if:commission_type,percentage|nullable|numeric|min:0|max:100',
                'flat_commission' => 'required_if:commission_type,flat|nullable|numeric|min:0'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $validatedData = $validator->validated();

            $user->commission_type = $validatedData['commission_type'];
            $user->commission_percentage = ($validatedData['commission_type'] === 'percentage') ? $validatedData['commission_percentage'] : null;
            $user->flat_commission = ($validatedData['commission_type'] === 'flat') ? $validatedData['flat_commission'] : null;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Vendor commission settings updated successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update commission.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Vendor self-service submit profile details.
     */
    public function submitProfile(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user || !$user->isVendor()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized action.'
                ], 403);
            }

            // If files are already present, they are optional. If not, they are required.
            $hasAadhaar = !empty($user->aadhaar_file);
            $hasPan = !empty($user->pan_file);
            $hasPhoto = !empty($user->photo);

            $rules = [
                'name' => 'required|string|max:255',
                'mobile' => 'required|string|max:15',
                'alternate_mobile' => 'nullable|string|max:15',
                'aadhaar_number' => 'required|string|size:12',
                'aadhaar_file' => ($hasAadhaar ? 'nullable' : 'required') . '|file|mimes:pdf,jpg,jpeg,png|max:4096',
                'pan_number' => 'required|string|size:10',
                'pan_file' => ($hasPan ? 'nullable' : 'required') . '|file|mimes:pdf,jpg,jpeg,png|max:4096',
                'photo' => ($hasPhoto ? 'nullable' : 'required') . '|file|image|mimes:jpg,jpeg,png|max:2048',
                'company_logo' => 'nullable|file|image|mimes:jpg,jpeg,png|max:2048',
                'has_gst' => 'required|in:yes,no',
                'gst_number' => 'required_if:has_gst,yes|nullable|string|size:15',
                'address' => 'required|string|max:500',
                'city' => 'required|string|max:100',
                'state' => 'required|string|max:100',
                'pincode' => 'required|string|max:10',
                'password' => 'nullable|string|min:6',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $validatedData = $validator->validated();

            // Document uploads using direct file move to public_path
            if ($request->hasFile('aadhaar_file')) {
                if ($user->aadhaar_file) {
                    $oldPath = public_path('storage/' . $user->aadhaar_file);
                    if (file_exists($oldPath)) {
                        @unlink($oldPath);
                    }
                }
                $file = $request->file('aadhaar_file');
                $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('storage/vendor_docs/aadhaar'), $fileName);
                $user->aadhaar_file = 'vendor_docs/aadhaar/' . $fileName;
            }
            if ($request->hasFile('pan_file')) {
                if ($user->pan_file) {
                    $oldPath = public_path('storage/' . $user->pan_file);
                    if (file_exists($oldPath)) {
                        @unlink($oldPath);
                    }
                }
                $file = $request->file('pan_file');
                $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('storage/vendor_docs/pan'), $fileName);
                $user->pan_file = 'vendor_docs/pan/' . $fileName;
            }
            if ($request->hasFile('photo')) {
                if ($user->photo) {
                    $oldPath = public_path('storage/' . $user->photo);
                    if (file_exists($oldPath)) {
                        @unlink($oldPath);
                    }
                }
                $file = $request->file('photo');
                $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('storage/vendor_photos'), $fileName);
                $user->photo = 'vendor_photos/' . $fileName;
            }
            if ($request->hasFile('company_logo')) {
                if ($user->company_logo) {
                    $oldPath = public_path('storage/' . $user->company_logo);
                    if (file_exists($oldPath)) {
                        @unlink($oldPath);
                    }
                }
                $file = $request->file('company_logo');
                $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('storage/vendor_logos'), $fileName);
                $user->company_logo = 'vendor_logos/' . $fileName;
            }

            $user->name = $validatedData['name'];
            $user->mobile = $validatedData['mobile'];
            $user->alternate_mobile = $validatedData['alternate_mobile'] ?? null;
            $user->aadhaar_number = $validatedData['aadhaar_number'];
            $user->pan_number = $validatedData['pan_number'];
            $user->gst_number = ($validatedData['has_gst'] === 'yes') ? $validatedData['gst_number'] : null;
            $user->address = $validatedData['address'];
            $user->city = $validatedData['city'];
            $user->state = $validatedData['state'];
            $user->pincode = $validatedData['pincode'];
            
            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }

            // Set profile status back to pending upon submission/re-submission
            $user->profile_status = 'Pending';
            $user->rejection_reason = null;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Your profile details have been submitted successfully and are now under review.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit profile.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
