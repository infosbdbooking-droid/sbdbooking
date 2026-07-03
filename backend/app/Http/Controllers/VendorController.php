<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Roles;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\SettlementRequest;
use App\Services\WalletService;
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
                    $oldPath = public_path('images/' . $user->aadhaar_file);
                    if (file_exists($oldPath)) @unlink($oldPath);
                }
                $file     = $request->file('aadhaar_file');
                $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('images/vendor_docs/aadhaar'), $fileName);
                $user->aadhaar_file = 'vendor_docs/aadhaar/' . $fileName;
            }
            if ($request->hasFile('pan_file')) {
                if ($user->pan_file) {
                    $oldPath = public_path('images/' . $user->pan_file);
                    if (file_exists($oldPath)) @unlink($oldPath);
                }
                $file     = $request->file('pan_file');
                $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('images/vendor_docs/pan'), $fileName);
                $user->pan_file = 'vendor_docs/pan/' . $fileName;
            }
            if ($request->hasFile('photo')) {
                if ($user->photo) {
                    $oldPath = public_path('images/' . $user->photo);
                    if (file_exists($oldPath)) @unlink($oldPath);
                }
                $file     = $request->file('photo');
                $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('images/vendor_photos'), $fileName);
                $user->photo = 'vendor_photos/' . $fileName;
            }
            if ($request->hasFile('company_logo')) {
                if ($user->company_logo) {
                    $oldPath = public_path('images/' . $user->company_logo);
                    if (file_exists($oldPath)) @unlink($oldPath);
                }
                $file     = $request->file('company_logo');
                $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('images/vendor_logos'), $fileName);
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

    /**
     * Fetch vendor profile details.
     */
    public function show($id)
    {
        try {
            $user = User::with('approvedBy')->findOrFail($id);
            if (!$user->isVendor()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User is not a vendor.'
                ], 400);
            }

            return response()->json([
                'success' => true,
                'data' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch vendor details.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the vendor's wallet dashboard.
     */
    public function walletDashboard(Request $request)
    {
        try {
            $user = Auth::user();
            $walletService = app(WalletService::class);
            $wallet = $walletService->getOrCreateWallet($user);

            // Fetch transactions
            $transactions = WalletTransaction::where('wallet_id', $wallet->id)
                ->orderBy('created_at', 'desc')
                ->paginate(15, ['*'], 'tx_page');

            // Fetch settlement requests
            $settlements = SettlementRequest::where('vendor_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->paginate(10, ['*'], 'set_page');

            return view('vendor.wallet', compact('wallet', 'transactions', 'settlements'));
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to load wallet dashboard: ' . $e->getMessage());
        }
    }

    /**
     * Store a new settlement request from a vendor.
     */
    public function storeSettlementRequest(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'payout_method' => 'required|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            $user = Auth::user();
            $walletService = app(WalletService::class);
            $wallet = $walletService->getOrCreateWallet($user);

            if ($wallet->status === 'frozen') {
                return back()->with('error', 'Your wallet is frozen. Settlement requests are disabled.');
            }

            $amount = (float)$request->amount;

            if ($wallet->balance < $amount) {
                return back()->with('error', 'Insufficient wallet balance for this settlement request.');
            }

            \Illuminate\Support\Facades\DB::transaction(function () use ($user, $amount, $request, $walletService) {
                // 1. Create the settlement request
                $settlement = SettlementRequest::create([
                    'vendor_id' => $user->id,
                    'amount' => $amount,
                    'status' => 'pending',
                    'payout_method' => $request->payout_method,
                    'notes' => $request->notes,
                ]);

                // 2. Debit immediately to hold the funds
                $walletService->debit(
                    $user,
                    $amount,
                    'settlement_payout',
                    $settlement,
                    "Settlement payout request #SR-{$settlement->id} (Pending Approval)"
                );
            });

            return back()->with('success', 'Settlement request submitted successfully and funds have been reserved.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to submit settlement request: ' . $e->getMessage());
        }
    }

    /**
     * Landing Page: Vendor Wallets list with advanced filtering.
     */
    public function vendorWalletsIndex(Request $request)
    {
        try {
            if (Auth::user()->isVendor()) {
                abort(403, 'Unauthorized.');
            }

            $query = User::whereHas('roles', function ($q) {
                $q->whereRaw('LOWER(title) = ?', ['vendor']);
            })->with('wallet');

            // Apply search
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('mobile', 'like', "%{$search}%");
                });
            }

            // Filter by profile status
            if ($request->filled('status')) {
                $query->where('profile_status', $request->status);
            }

            // Filter by wallet balance threshold
            if ($request->filled('balance_filter')) {
                $bf = $request->balance_filter;
                $query->whereHas('wallet', function($q) use ($bf) {
                    if ($bf === 'negative') {
                        $q->where('balance', '<', 0);
                    } elseif ($bf === 'zero') {
                        $q->where('balance', '=', 0);
                    } elseif ($bf === 'positive') {
                        $q->where('balance', '>', 0);
                    }
                });
            }

            $vendors = $query->paginate(15);

            // Compute extra fields
            foreach ($vendors as $vendor) {
                // Get or create wallet
                $wallet = app(WalletService::class)->getOrCreateWallet($vendor);
                
                $vendor->wallet_balance = $wallet->balance;
                $vendor->total_earnings = WalletTransaction::where('wallet_id', $wallet->id)
                    ->where('type', 'credit')
                    ->sum('amount');
                $vendor->pending_settlement = SettlementRequest::where('vendor_id', $vendor->id)
                    ->where('status', 'pending')
                    ->sum('amount');
                $vendor->total_paid = SettlementRequest::where('vendor_id', $vendor->id)
                    ->where('status', 'approved')
                    ->sum('amount');
            }

            return view('finance.vendor_wallets', compact('vendors'));
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to load vendor wallets: ' . $e->getMessage());
        }
    }

    /**
     * Dashboard: Vendor Details Finance Dashboard
     */
    public function vendorFinanceDashboard(Request $request, $vendor_id)
    {
        try {
            if (Auth::user()->isVendor()) {
                abort(403, 'Unauthorized.');
            }

            $vendor = User::with('approvedBy')->findOrFail($vendor_id);
            if (!$vendor->isVendor()) {
                abort(404, 'Vendor not found.');
            }

            $wallet = app(WalletService::class)->getOrCreateWallet($vendor);

            // Finance Summary Cards
            $walletBalance = $wallet->balance;
            $pendingSettlement = SettlementRequest::where('vendor_id', $vendor_id)->where('status', 'pending')->sum('amount');
            $totalPaid = SettlementRequest::where('vendor_id', $vendor_id)->where('status', 'approved')->sum('amount');
            $totalEarnings = WalletTransaction::where('wallet_id', $wallet->id)->where('type', 'credit')->sum('amount');
            $adminCommission = WalletTransaction::where('wallet_id', $wallet->id)->where('type', 'debit')->where('transaction_type', 'admin_commission')->sum('amount');
            $todayEarnings = WalletTransaction::where('wallet_id', $wallet->id)->where('type', 'credit')->whereDate('created_at', today())->sum('amount');

            // Section 3: Recent Ride Earnings
            $rides = \App\Models\CabOrder::where('vendor_id', $vendor_id)
                ->orderBy('created_at', 'desc')
                ->paginate(10, ['*'], 'rides_page');

            // Section 4: Wallet Ledger (with computed running balance)
            $rawTransactions = WalletTransaction::where('wallet_id', $wallet->id)
                ->orderBy('created_at', 'asc')
                ->get();
            
            $running = 0.00;
            foreach ($rawTransactions as $tx) {
                if ($tx->type === 'credit') {
                    $running += (float)$tx->amount;
                } else {
                    $running -= (float)$tx->amount;
                }
                $tx->running_balance = $running;
            }
            
            // Paginate computed transactions descending
            $transactions = $rawTransactions->reverse()->values();
            // Paginate manually
            $page = $request->get('ledger_page', 1);
            $perPage = 15;
            $slicedTx = $transactions->slice(($page - 1) * $perPage, $perPage)->all();
            $ledger = new \Illuminate\Pagination\LengthAwarePaginator($slicedTx, $transactions->count(), $perPage, $page, [
                'path' => $request->url(),
                'query' => $request->query(),
                'pageName' => 'ledger_page'
            ]);

            // Section 5: Settlement Requests
            $settlementRequests = SettlementRequest::where('vendor_id', $vendor_id)
                ->orderBy('created_at', 'desc')
                ->paginate(10, ['*'], 'settlements_page');

            // Section 6: Payment History
            $paymentHistory = SettlementRequest::where('vendor_id', $vendor_id)
                ->where('status', 'approved')
                ->orderBy('processed_at', 'desc')
                ->paginate(10, ['*'], 'payouts_page');

            // Section 7: Commission History
            $commissions = \App\Models\CabOrder::where('vendor_id', $vendor_id)
                ->where('booking_status', 'completed')
                ->orderBy('created_at', 'desc')
                ->paginate(10, ['*'], 'commissions_page');

            return view('finance.vendor_details', compact(
                'vendor', 'wallet', 'walletBalance', 'pendingSettlement', 'totalPaid', 'totalEarnings', 'adminCommission', 'todayEarnings',
                'rides', 'ledger', 'settlementRequests', 'paymentHistory', 'commissions'
            ));
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to load Vendor Finance Dashboard: ' . $e->getMessage());
        }
    }

    /**
     * Display all settlement requests for admin.
     */
    public function adminSettlementsIndex(Request $request)
    {
        try {
            if (Auth::user()->isVendor()) {
                abort(403, 'Unauthorized.');
            }

            // Fetch pending settlements
            $pendingSettlements = SettlementRequest::with('vendor')
                ->where('status', 'pending')
                ->orderBy('created_at', 'asc')
                ->get();

            // Fetch all settlements history
            $settlementsHistory = SettlementRequest::with(['vendor', 'processedBy'])
                ->where('status', '!=', 'pending')
                ->orderBy('created_at', 'desc')
                ->paginate(15);

            return view('finance.settlements', compact('pendingSettlements', 'settlementsHistory'));
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to load settlements: ' . $e->getMessage());
        }
    }

    /**
     * Approve a settlement request (Admin).
     */
    public function adminSettlementApprove($id)
    {
        try {
            if (Auth::user()->isVendor()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
            }

            $settlement = SettlementRequest::findOrFail($id);
            if ($settlement->status !== 'pending') {
                return response()->json(['success' => false, 'message' => 'Request is already processed.'], 400);
            }

            \Illuminate\Support\Facades\DB::transaction(function () use ($settlement) {
                $settlement->update([
                    'status' => 'approved',
                    'processed_by' => Auth::id(),
                    'processed_at' => now(),
                ]);

                // Update the description of the corresponding wallet transaction to approved
                WalletTransaction::where('reference_id', $settlement->id)
                    ->where('reference_type', SettlementRequest::class)
                    ->where('transaction_type', 'settlement_payout')
                    ->update([
                        'description' => "Settlement payout request #SR-{$settlement->id} (Approved)"
                    ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Settlement request approved successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve settlement: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject a settlement request (Admin).
     */
    public function adminSettlementReject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        try {
            if (Auth::user()->isVendor()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
            }

            $settlement = SettlementRequest::findOrFail($id);
            if ($settlement->status !== 'pending') {
                return response()->json(['success' => false, 'message' => 'Request is already processed.'], 400);
            }

            \Illuminate\Support\Facades\DB::transaction(function () use ($settlement, $request) {
                $settlement->update([
                    'status' => 'rejected',
                    'notes' => $request->rejection_reason,
                    'processed_by' => Auth::id(),
                    'processed_at' => now(),
                ]);

                // Refund the amount to the vendor's wallet
                $walletService = app(WalletService::class);
                $walletService->credit(
                    $settlement->vendor,
                    (float)$settlement->amount,
                    'adjustment',
                    $settlement,
                    "Refund for rejected settlement request #SR-{$settlement->id}"
                );

                // Update original description
                WalletTransaction::where('reference_id', $settlement->id)
                    ->where('reference_type', SettlementRequest::class)
                    ->where('transaction_type', 'settlement_payout')
                    ->update([
                        'description' => "Settlement payout request #SR-{$settlement->id} (Rejected)"
                    ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Settlement request rejected successfully and funds refunded.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reject settlement: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Global Commission History log.
     */
    public function adminCommissionsIndex(Request $request)
    {
        try {
            if (Auth::user()->isVendor()) {
                abort(403, 'Unauthorized.');
            }

            $commissions = \App\Models\CabOrder::with('vendor')
                ->where('booking_status', 'completed')
                ->orderBy('created_at', 'desc')
                ->paginate(15);

            return view('finance.commissions', compact('commissions'));
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to load commissions log: ' . $e->getMessage());
        }
    }

    /**
     * Global Wallet Transaction ledger.
     */
    public function adminTransactionsIndex(Request $request)
    {
        try {
            if (Auth::user()->isVendor()) {
                abort(403, 'Unauthorized.');
            }

            $transactions = WalletTransaction::with('wallet.user')
                ->orderBy('created_at', 'desc')
                ->paginate(15);

            return view('finance.transactions', compact('transactions'));
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to load global transaction log: ' . $e->getMessage());
        }
    }

    /**
     * Finance Reports index page.
     */
    public function financeReportsIndex(Request $request)
    {
        try {
            if (Auth::user()->isVendor()) {
                abort(403, 'Unauthorized.');
            }

            $startDate = $request->filled('start_date') ? \Carbon\Carbon::parse($request->start_date)->startOfDay() : now()->startOfMonth();
            $endDate = $request->filled('end_date') ? \Carbon\Carbon::parse($request->end_date)->endOfDay() : now()->endOfDay();

            // Summary stats
            $totalCredited = WalletTransaction::where('type', 'credit')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('amount');

            $totalCommissions = WalletTransaction::where('type', 'debit')
                ->where('transaction_type', 'admin_commission')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('amount');

            $totalPayouts = SettlementRequest::where('status', 'approved')
                ->whereBetween('processed_at', [$startDate, $endDate])
                ->sum('amount');

            return view('finance.reports', compact('totalCredited', 'totalCommissions', 'totalPayouts', 'startDate', 'endDate'));
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to load finance reports: ' . $e->getMessage());
        }
    }

    /**
     * Export reports statement.
     */
    public function financeReportsExport(Request $request)
    {
        return back()->with('error', 'Export templates are ready to download in details panel.');
    }

    /**
     * Manually adjust a vendor's wallet balance (Admin).
     */
    public function vendorWalletAdjust(Request $request, $vendor_id)
    {
        $request->validate([
            'action' => 'required|in:credit,debit',
            'amount' => 'required|numeric|min:0.01',
            'type' => 'required|in:wallet_recharge,adjustment',
            'description' => 'required|string|max:1000',
        ]);

        try {
            if (Auth::user()->isVendor()) {
                return back()->with('error', 'Unauthorized access.');
            }

            $vendor = User::findOrFail($vendor_id);
            
            // Check frozen wallet
            $wallet = app(WalletService::class)->getOrCreateWallet($vendor);
            if ($wallet->status === 'frozen') {
                return back()->with('error', 'Wallet is frozen. Please activate the wallet before performing manual adjustments.');
            }

            $amount = (float)$request->amount;
            $walletService = app(WalletService::class);

            if ($request->action === 'credit') {
                $walletService->credit($vendor, $amount, $request->type, null, $request->description);
            } else {
                $walletService->debit($vendor, $amount, $request->type, null, $request->description);
            }

            return back()->with('success', 'Vendor wallet balance adjusted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to adjust wallet: ' . $e->getMessage());
        }
    }

    /**
     * Update wallet status: freeze, activate, or reset balance.
     */
    public function vendorWalletStatusUpdate(Request $request, $vendor_id)
    {
        $request->validate([
            'action' => 'required|in:freeze,activate,reset',
        ]);

        try {
            if (Auth::user()->isVendor()) {
                return back()->with('error', 'Unauthorized access.');
            }

            $vendor = User::findOrFail($vendor_id);
            $wallet = app(WalletService::class)->getOrCreateWallet($vendor);

            if ($request->action === 'freeze') {
                $wallet->update(['status' => 'frozen']);
                return back()->with('success', 'Wallet frozen successfully. Settlement requests and adjustments are now disabled for this vendor.');
            } elseif ($request->action === 'activate') {
                $wallet->update(['status' => 'active']);
                return back()->with('success', 'Wallet activated successfully.');
            } elseif ($request->action === 'reset') {
                // Ensure only Super Admin or appropriate access (we will allow for Admin now)
                $oldBalance = (float)$wallet->balance;
                if ($oldBalance === 0.0) {
                    return back()->with('success', 'Wallet balance is already zero.');
                }

                \Illuminate\Support\Facades\DB::transaction(function() use ($vendor, $wallet, $oldBalance) {
                    if ($oldBalance > 0) {
                        // Debit the positive balance to make it zero
                        app(WalletService::class)->debit($vendor, $oldBalance, 'adjustment', null, 'Wallet balance reset to zero by Admin');
                    } else {
                        // Credit the negative balance to make it zero
                        app(WalletService::class)->credit($vendor, abs($oldBalance), 'adjustment', null, 'Wallet balance reset to zero by Admin');
                    }
                });

                return back()->with('success', 'Wallet balance has been reset to zero successfully.');
            }

            return back()->with('error', 'Invalid action.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update wallet status: ' . $e->getMessage());
        }
    }
}

