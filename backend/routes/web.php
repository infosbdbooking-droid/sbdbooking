<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ActionController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\CheckAuthentication;
use App\Http\Controllers\ChargesTypeController;
use App\Http\Controllers\CarController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\PermissionsController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\CarTypeController;
use App\Http\Controllers\CabOrderWebController;
use App\Http\Controllers\SliderController;
use App\Http\Controllers\ContactMessageController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\BlogCategoryController;
use App\Http\Controllers\BlogTagController;
use App\Http\Controllers\BlogCommentController;
use App\Http\Controllers\BlogSettingsController;
use App\Http\Controllers\SeoPageController;
use App\Http\Controllers\SeoServiceCategoryController;
use App\Http\Controllers\SeoStateController;
use App\Http\Controllers\SeoCityController;
use App\Http\Controllers\SeoRouteController;
use App\Http\Controllers\SeoFaqController;
use App\Http\Controllers\SeoSettingsController;
use App\Http\Controllers\VendorController;





Route::prefix('panel')->group(function () {

    Route::get('/', function () {
        return view('index');
    });

    Route::post('/signin/verify', [ActionController::class, 'signIn'])->name('signin.verify');
    Route::middleware(['web', CheckAuthentication::class])->group(function () {

        # Dashboard
        Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

        # Vendor Verification Lock Screen
        Route::get('/vendor/verify', function () {
            return view('vendor.verify');
        })->name('vendor.verify');
        Route::post('/vendor/verify/submit', [VendorController::class, 'submitProfile'])->name('vendor.verify.submit');
 
        # Logout
        Route::get('/signout', [ActionController::class, 'logout'])->name('logout');

        # Cab Orders (Bookings)
        Route::prefix('cab-orders')->group(function () {
            Route::get('/', [CabOrderWebController::class, 'index'])->name('cabOrders');
            Route::get('/create', [CabOrderWebController::class, 'create'])->name('cabOrders.create');
            Route::post('/store', [CabOrderWebController::class, 'store'])->name('cabOrders.store');
            Route::post('/{id}/accept', [CabOrderWebController::class, 'acceptBooking'])->name('cabOrders.accept');
            Route::post('/{id}/approve-payment', [CabOrderWebController::class, 'approvePayment'])->name('cabOrders.approvePayment');
            Route::post('/{id}/cancel', [CabOrderWebController::class, 'cancelBooking'])->name('cabOrders.cancel');
            Route::get('/{id}', [CabOrderWebController::class, 'show'])->name('cabOrders.show');
            Route::get('/{id}/invoice', [CabOrderWebController::class, 'downloadInvoice'])->name('cabOrders.invoice');
            
            // New routes for CRM workflows
            Route::post('/{id}/approve-setup', [CabOrderWebController::class, 'approveAndSetupPayment'])->name('cabOrders.approveSetup');
            Route::post('/{id}/collect-payment', [CabOrderWebController::class, 'collectPayment'])->name('cabOrders.collectPayment');
            Route::post('/{id}/assign-driver', [CabOrderWebController::class, 'assignDriver'])->name('cabOrders.assignDriver');
            Route::post('/{id}/update-status', [CabOrderWebController::class, 'updateStatus'])->name('cabOrders.updateStatus');
        });



        # Charges Type
        Route::prefix('chargesType')->group(function () {
            Route::get('/', function () {return view('chargesType.chargesType'); })->name('chargesType');
            Route::get('/data', [ChargesTypeController::class, 'index'])->name('chargesType.data');
            Route::post('/store', [ChargesTypeController::class, 'store'])->name('chargesType.store');
            Route::get('/{id}/edit', [ChargesTypeController::class, 'edit'])->name('chargesType.edit');
            Route::put('/{id}', [ChargesTypeController::class, 'update'])->name('chargesType.update');
            Route::delete('/{id}', [ChargesTypeController::class, 'destroy'])->name('chargesType.destroy');
            Route::post('/{id}/changeStatus', [ChargesTypeController::class, 'changeStatus'])->name('chargesType.changeStatus');
        });  

        # Car Type
        Route::prefix('carType')->group(function () {
            Route::get('/', function () {return view('carType.carType'); })->name('carType');
            Route::get('/data', [CarTypeController::class, 'index'])->name('carType.data');
            Route::post('/store', [CarTypeController::class, 'store'])->name('carType.store');
            Route::get('/{id}/edit', [CarTypeController::class, 'edit'])->name('carType.edit');
            Route::put('/{id}', [CarTypeController::class, 'update'])->name('carType.update');
            Route::delete('/{id}', [CarTypeController::class, 'destroy'])->name('carType.destroy');
            Route::post('/{id}/changeStatus', [CarTypeController::class, 'changeStatus'])->name('carType.changeStatus');
        }); 
        
        Route::prefix('car')->group(function () {
            Route::get('/', function () {return view('car.car'); })->name('car');
            Route::get('/data', [CarController::class, 'index'])->name('car.data');
            Route::get('/chargesType', [CarController::class, 'chargesType'])->name('car.chargesType');
            Route::post('/store', [CarController::class, 'store'])->name('car.store');
            Route::get('/car', [CarController::class, 'getCarTypes'])->name('car.carData');
            Route::get('/{id}/edit', [CarController::class, 'edit'])->name('car.edit');
            Route::put('/{id}', [CarController::class, 'update'])->name('car.update');
            Route::delete('/{id}', [CarController::class, 'destroy'])->name('car.destroy');
            Route::post('/{id}/changeStatus', [CarController::class, 'changeStatus'])->name('car.changeStatus');
        });




        # Access Management
        Route::prefix('access')->group(function () {

            #  permission 
            Route::get('/permissions', function () {return view('access.permissions'); })->name('access.permissions');
            Route::get('/permissions/data', [PermissionsController::class, 'index'])->name('access.permissions.data');
            Route::post('/permissions/store', [PermissionsController::class, 'store'])->name('access.permissions.store');
            Route::get('/permissions/{id}/edit', [PermissionsController::class, 'edit'])->name('access.permissions.edit');
            Route::put('/permissions/{id}', [PermissionsController::class, 'update'])->name('access.permissions.update');
            Route::delete('/permissions/{id}', [PermissionsController::class, 'destroy'])->name('access.permissions.destroy');

            #  Role
            Route::get('/roles', function () {return view('access.roles'); })->name('access.roles');
            Route::get('/roles/data', [RolesController::class, 'index'])->name('access.roles.data');
            Route::post('/roles/store', [RolesController::class, 'store'])->name('access.roles.store');
            Route::get('/rolespermissions/permissionsData', [RolesController::class, 'getPermissionsList'])->name('access.roles.permissionsData');
            Route::get('/roles/{id}/edit', [RolesController::class, 'edit'])->name('access.roles.edit');
            Route::put('/roles/{id}', [RolesController::class, 'update'])->name('access.roles.update');
            Route::delete('/roles/{id}', [RolesController::class, 'destroy'])->name('access.roles.destroy');

            #  User Page
            Route::get('/user', function () {return view('access.user'); })->name('access.user');
            Route::get('/user/data', [UserController::class, 'index'])->name('access.user.data');
            Route::get('/user/getRole', [UserController::class, 'getRole'])->name('access.user.getRole');
            Route::post('/user/store', [UserController::class, 'store'])->name('access.user.store');
            Route::get('/user/{id}/edit', [UserController::class, 'edit'])->name('access.user.edit');
            Route::put('/user/{id}', [UserController::class, 'update'])->name('access.user.update');
            Route::delete('/user/{id}', [UserController::class, 'destroy'])->name('access.user.destroy');
        });

        # Vendor Management
        Route::prefix('vendors')->group(function () {
            Route::get('/', [VendorController::class, 'index'])->name('vendors.index');
            Route::get('/data', [VendorController::class, 'index'])->name('vendors.data');
            Route::post('/{id}/approve', [VendorController::class, 'approve'])->name('vendors.approve');
            Route::post('/{id}/reject', [VendorController::class, 'reject'])->name('vendors.reject');
            Route::post('/{id}/commission', [VendorController::class, 'updateCommission'])->name('vendors.commission');
            Route::get('/{id}/show', [VendorController::class, 'show'])->name('vendors.show');
        });

        # Vendor Wallet & Settlements (Vendor Portal)
        Route::prefix('vendor/wallet')->group(function () {
            Route::get('/', [VendorController::class, 'walletDashboard'])->name('vendor.wallet');
            Route::post('/settlement-request', [VendorController::class, 'storeSettlementRequest'])->name('vendor.settlement.request');
        });

        # Finance Management (Admin Portal)
        Route::prefix('finance')->group(function () {
            // 1. Vendor Wallets (Landing Page)
            Route::get('/vendor-wallets', [VendorController::class, 'vendorWalletsIndex'])->name('finance.vendor-wallets.index');
            Route::get('/vendor-wallets/{vendor_id}', [VendorController::class, 'vendorFinanceDashboard'])->name('finance.vendor-wallets.show');
            
            // 2. Settlement Requests
            Route::get('/settlements', [VendorController::class, 'adminSettlementsIndex'])->name('finance.settlements.index');
            Route::post('/settlements/{id}/approve', [VendorController::class, 'adminSettlementApprove'])->name('finance.settlements.approve');
            Route::post('/settlements/{id}/reject', [VendorController::class, 'adminSettlementReject'])->name('finance.settlements.reject');
            
            // 3. Commission History
            Route::get('/commissions', [VendorController::class, 'adminCommissionsIndex'])->name('finance.commissions.index');
            
            // 4. Payment Transactions
            Route::get('/transactions', [VendorController::class, 'adminTransactionsIndex'])->name('finance.transactions.index');
            
            // 5. Reports
            Route::get('/reports', [VendorController::class, 'financeReportsIndex'])->name('finance.reports.index');
            Route::get('/reports/export', [VendorController::class, 'financeReportsExport'])->name('finance.reports.export');
            
            // Actions & Adjustments
            Route::post('/vendor-wallets/{vendor_id}/adjust', [VendorController::class, 'vendorWalletAdjust'])->name('finance.vendor-wallets.adjust');
            Route::post('/vendor-wallets/{vendor_id}/status', [VendorController::class, 'vendorWalletStatusUpdate'])->name('finance.vendor-wallets.status');
        });



        # Sliders Management
        Route::prefix('sliders')->group(function () {
            Route::get('/', function () { return view('sliders.index'); })->name('sliders.index');
            Route::get('/data', [SliderController::class, 'index'])->name('sliders.data');
            Route::post('/store', [SliderController::class, 'store'])->name('sliders.store');
            Route::get('/{id}/edit', [SliderController::class, 'edit'])->name('sliders.edit');
            Route::post('/{id}/update', [SliderController::class, 'update'])->name('sliders.update'); // using POST for file uploads
            Route::delete('/{id}', [SliderController::class, 'destroy'])->name('sliders.destroy');
            Route::post('/{id}/changeStatus', [SliderController::class, 'changeStatus'])->name('sliders.changeStatus');
        });

        # Contact Messages
        Route::prefix('contact-messages')->group(function () {
            Route::get('/', [ContactMessageController::class, 'index'])->name('contactMessages.index');
            Route::get('/data', [ContactMessageController::class, 'index'])->name('contactMessages.data');
            Route::get('/{id}', [ContactMessageController::class, 'show'])->name('contactMessages.show');
            Route::post('/{id}/update', [ContactMessageController::class, 'update'])->name('contactMessages.update');
            Route::delete('/{id}', [ContactMessageController::class, 'destroy'])->name('contactMessages.destroy');
        });

        # Blogs Management
        Route::prefix('blogs')->group(function () {
            Route::get('/dashboard', [BlogController::class, 'dashboard'])->name('blogs.dashboard');
            Route::get('/', [BlogController::class, 'index'])->name('blogs.index');
            Route::get('/data', [BlogController::class, 'getData'])->name('blogs.data');
            Route::get('/create', [BlogController::class, 'create'])->name('blogs.create');
            Route::post('/store', [BlogController::class, 'store'])->name('blogs.store');
            Route::get('/{id}/edit', [BlogController::class, 'edit'])->name('blogs.edit');
            Route::put('/{id}', [BlogController::class, 'update'])->name('blogs.update');
            Route::delete('/{id}', [BlogController::class, 'destroy'])->name('blogs.destroy');
            Route::get('/{id}', [BlogController::class, 'show'])->name('blogs.show');
            Route::post('/{id}/duplicate', [BlogController::class, 'duplicate'])->name('blogs.duplicate');
            Route::post('/bulk-action', [BlogController::class, 'bulkAction'])->name('blogs.bulkAction');
            Route::post('/{id}/changeFeatured', [BlogController::class, 'changeFeatured'])->name('blogs.changeFeatured');
            Route::post('/{id}/changeStatus', [BlogController::class, 'changeStatus'])->name('blogs.changeStatus');
        });

        Route::prefix('blog-categories')->group(function () {
            Route::get('/', [BlogCategoryController::class, 'view'])->name('blogCategories.index');
            Route::get('/data', [BlogCategoryController::class, 'index'])->name('blogCategories.data');
            Route::post('/store', [BlogCategoryController::class, 'store'])->name('blogCategories.store');
            Route::get('/{id}/edit', [BlogCategoryController::class, 'edit'])->name('blogCategories.edit');
            Route::put('/{id}', [BlogCategoryController::class, 'update'])->name('blogCategories.update');
            Route::delete('/{id}', [BlogCategoryController::class, 'destroy'])->name('blogCategories.destroy');
            Route::post('/{id}/changeStatus', [BlogCategoryController::class, 'changeStatus'])->name('blogCategories.changeStatus');
        });

        Route::prefix('blog-tags')->group(function () {
            Route::get('/', [BlogTagController::class, 'view'])->name('blogTags.index');
            Route::get('/data', [BlogTagController::class, 'index'])->name('blogTags.data');
            Route::post('/store', [BlogTagController::class, 'store'])->name('blogTags.store');
            Route::get('/{id}/edit', [BlogTagController::class, 'edit'])->name('blogTags.edit');
            Route::put('/{id}', [BlogTagController::class, 'update'])->name('blogTags.update');
            Route::delete('/{id}', [BlogTagController::class, 'destroy'])->name('blogTags.destroy');
        });

        Route::prefix('blog-comments')->group(function () {
            Route::get('/', [BlogCommentController::class, 'view'])->name('blogComments.index');
            Route::get('/data', [BlogCommentController::class, 'index'])->name('blogComments.data');
            Route::post('/{id}/approve', [BlogCommentController::class, 'approve'])->name('blogComments.approve');
            Route::post('/{id}/reject', [BlogCommentController::class, 'reject'])->name('blogComments.reject');
            Route::delete('/{id}', [BlogCommentController::class, 'destroy'])->name('blogComments.destroy');
            Route::post('/bulk-action', [BlogCommentController::class, 'bulkAction'])->name('blogComments.bulkAction');
        });

        Route::prefix('blog-settings')->group(function () {
            Route::get('/', [BlogSettingsController::class, 'index'])->name('blogSettings.index');
            Route::post('/update', [BlogSettingsController::class, 'update'])->name('blogSettings.update');
        });

        # SEO Landing Pages Management
        Route::prefix('seo')->group(function () {
            Route::get('/dashboard', [SeoPageController::class, 'dashboard'])->name('seoPages.dashboard');
            Route::get('/', [SeoPageController::class, 'index'])->name('seoPages.index');
            Route::get('/data', [SeoPageController::class, 'getData'])->name('seoPages.data');
            Route::get('/create', [SeoPageController::class, 'create'])->name('seoPages.create');
            Route::post('/store', [SeoPageController::class, 'store'])->name('seoPages.store');
            Route::get('/{id}/edit', [SeoPageController::class, 'edit'])->name('seoPages.edit');
            Route::put('/{id}', [SeoPageController::class, 'update'])->name('seoPages.update');
            Route::delete('/{id}', [SeoPageController::class, 'destroy'])->name('seoPages.destroy');
            Route::get('/{id}', [SeoPageController::class, 'show'])->name('seoPages.show');
            Route::post('/{id}/duplicate', [SeoPageController::class, 'duplicate'])->name('seoPages.duplicate');
            Route::post('/bulk-action', [SeoPageController::class, 'bulkAction'])->name('seoPages.bulkAction');
            Route::post('/{id}/changeFeatured', [SeoPageController::class, 'changeFeatured'])->name('seoPages.changeFeatured');
            Route::post('/{id}/changeStatus', [SeoPageController::class, 'changeStatus'])->name('seoPages.changeStatus');
        });

        Route::prefix('seo-service-categories')->group(function () {
            Route::get('/', [SeoServiceCategoryController::class, 'view'])->name('seoServiceCategories.index');
            Route::get('/data', [SeoServiceCategoryController::class, 'index'])->name('seoServiceCategories.data');
            Route::post('/store', [SeoServiceCategoryController::class, 'store'])->name('seoServiceCategories.store');
            Route::get('/{id}/edit', [SeoServiceCategoryController::class, 'edit'])->name('seoServiceCategories.edit');
            Route::put('/{id}', [SeoServiceCategoryController::class, 'update'])->name('seoServiceCategories.update');
            Route::delete('/{id}', [SeoServiceCategoryController::class, 'destroy'])->name('seoServiceCategories.destroy');
            Route::post('/{id}/changeStatus', [SeoServiceCategoryController::class, 'changeStatus'])->name('seoServiceCategories.changeStatus');
        });

        Route::prefix('seo-states')->group(function () {
            Route::get('/', [SeoStateController::class, 'view'])->name('seoStates.index');
            Route::get('/data', [SeoStateController::class, 'index'])->name('seoStates.data');
            Route::post('/store', [SeoStateController::class, 'store'])->name('seoStates.store');
            Route::get('/{id}/edit', [SeoStateController::class, 'edit'])->name('seoStates.edit');
            Route::put('/{id}', [SeoStateController::class, 'update'])->name('seoStates.update');
            Route::delete('/{id}', [SeoStateController::class, 'destroy'])->name('seoStates.destroy');
            Route::post('/{id}/changeStatus', [SeoStateController::class, 'changeStatus'])->name('seoStates.changeStatus');
        });

        Route::prefix('seo-cities')->group(function () {
            Route::get('/', [SeoCityController::class, 'view'])->name('seoCities.index');
            Route::get('/data', [SeoCityController::class, 'index'])->name('seoCities.data');
            Route::post('/store', [SeoCityController::class, 'store'])->name('seoCities.store');
            Route::get('/{id}/edit', [SeoCityController::class, 'edit'])->name('seoCities.edit');
            Route::put('/{id}', [SeoCityController::class, 'update'])->name('seoCities.update');
            Route::delete('/{id}', [SeoCityController::class, 'destroy'])->name('seoCities.destroy');
            Route::post('/{id}/changeStatus', [SeoCityController::class, 'changeStatus'])->name('seoCities.changeStatus');
            Route::get('/by-state/{state_id}', [SeoCityController::class, 'getCitiesByState'])->name('seoCities.byState');
        });

        Route::prefix('seo-routes')->group(function () {
            Route::get('/', [SeoRouteController::class, 'view'])->name('seoRoutes.index');
            Route::get('/data', [SeoRouteController::class, 'index'])->name('seoRoutes.data');
            Route::post('/store', [SeoRouteController::class, 'store'])->name('seoRoutes.store');
            Route::get('/{id}/edit', [SeoRouteController::class, 'edit'])->name('seoRoutes.edit');
            Route::put('/{id}', [SeoRouteController::class, 'update'])->name('seoRoutes.update');
            Route::delete('/{id}', [SeoRouteController::class, 'destroy'])->name('seoRoutes.destroy');
            Route::post('/{id}/changeStatus', [SeoRouteController::class, 'changeStatus'])->name('seoRoutes.changeStatus');
        });

        Route::prefix('seo-faqs')->group(function () {
            Route::get('/', [SeoFaqController::class, 'view'])->name('seoFaqs.index');
            Route::get('/data', [SeoFaqController::class, 'index'])->name('seoFaqs.data');
            Route::post('/store', [SeoFaqController::class, 'store'])->name('seoFaqs.store');
            Route::get('/{id}/edit', [SeoFaqController::class, 'edit'])->name('seoFaqs.edit');
            Route::put('/{id}', [SeoFaqController::class, 'update'])->name('seoFaqs.update');
            Route::delete('/{id}', [SeoFaqController::class, 'destroy'])->name('seoFaqs.destroy');
            Route::post('/{id}/changeStatus', [SeoFaqController::class, 'changeStatus'])->name('seoFaqs.changeStatus');
        });

        Route::prefix('seo-settings')->group(function () {
            Route::get('/', [SeoSettingsController::class, 'index'])->name('seoSettings.index');
            Route::post('/update', [SeoSettingsController::class, 'update'])->name('seoSettings.update');
        });

        # Settings
        Route::prefix('settings')->group(function () {
            Route::get('settings', function () {
                return view('settings.settings'); })->name('settings');
            Route::get('/data', [SettingsController::class, 'index'])->name('settings.data');
            Route::post('/update', [SettingsController::class, 'update'])->name('settings.update');
        });
    });
});
?>