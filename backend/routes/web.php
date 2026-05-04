<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\actionController;
use App\Http\Controllers\userController;
use App\Http\Middleware\CheckAuthentication;
use App\Http\Controllers\chargesTypeController;
use App\Http\Controllers\carController;
use App\Http\Controllers\settingsController;
use App\Http\Controllers\permissionsController;
use App\Http\Controllers\rolesController;
use App\Http\Controllers\ordersController;
use App\Http\Controllers\kmPriceController;
use App\Http\Controllers\serviceFrequencyController;
use App\Http\Controllers\carTypeController;
use App\Http\Controllers\slidersController;
use App\Http\Controllers\bannersController;
use App\Http\Controllers\blogsController;
use App\Http\Controllers\CabOrderWebController;


Route::prefix('panel')->group(function () {

    Route::get('/', function () {
        return view('index');
    });

    Route::post('/signin/verify', [actionController::class, 'signIn'])->name('signin.verify');

    Route::middleware(['web', CheckAuthentication::class])->group(function () {

        # Dashboard
        Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

        # Logout
        Route::get('/signout', [actionController::class, 'logout']);

        # Orders
        Route::prefix('orders')->group(function () {
            Route::get('/', function () {return view('orders.add'); })->name('orders');
            Route::post('/businessStore', [ordersController::class, 'businessStore'])->name('orders.businessStore');
            Route::get('/getBusinessRegion', [ordersController::class, 'getBusinessRegion'])->name('orders.getBusinessRegion');
            Route::post('/getBranchCode', [ordersController::class, 'getBranchCode'])->name('orders.getBranchCode');
            Route::get('/category', [ordersController::class, 'getCategory'])->name('orders.getCategory');
            Route::post('/getSubCategory', [ordersController::class, 'getSubCategory'])->name('orders.getSubCategory');
            Route::get('/getServiceType', [ordersController::class, 'getServiceType'])->name('orders.getServiceType');
            Route::post('/getServiceFrequency', [ordersController::class, 'getServiceFrequency'])->name('orders.getServiceFrequency');
            Route::get('/getSector', [ordersController::class, 'getSector'])->name('orders.getSector');
            Route::post('/serviceStore', [ordersController::class, 'serviceStore'])->name('orders.serviceStore');
            //  Orders store edit update 
            Route::post('/store', [ordersController::class, 'store'])->name('orders.store');
        });

        # Cab Orders (Bookings)
        Route::prefix('cab-orders')->group(function () {
            Route::get('/', [CabOrderWebController::class, 'index'])->name('cabOrders');
            Route::get('/{id}', [CabOrderWebController::class, 'show'])->name('cabOrders.show');
            Route::get('/{id}/invoice', [CabOrderWebController::class, 'downloadInvoice'])->name('cabOrders.invoice');
        });

        # Km Price
        Route::prefix('kmPrice')->group(function () {
            Route::get('/', function () {return view('kmPrice.kmPrice'); })->name('kmPrice');
            Route::get('/data', [kmPriceController::class, 'index'])->name('kmPrice.data');
            Route::post('/store', [kmPriceController::class, 'store'])->name('kmPrice.store');
            Route::get('/{id}/edit', [kmPriceController::class, 'edit'])->name('kmPrice.edit');
            Route::put('/{id}', [kmPriceController::class, 'update'])->name('kmPrice.update');
            Route::delete('/{id}', [kmPriceController::class, 'destroy'])->name('kmPrice.destroy');
            Route::post('/{id}/changeStatus', [kmPriceController::class, 'changeStatus'])->name('kmPrice.changeStatus');
        });

        # Charges Type
        Route::prefix('chargesType')->group(function () {
            Route::get('/', function () {return view('chargesType.chargesType'); })->name('chargesType');
            Route::get('/data', [chargesTypeController::class, 'index'])->name('chargesType.data');
            Route::post('/store', [chargesTypeController::class, 'store'])->name('chargesType.store');
            Route::get('/{id}/edit', [chargesTypeController::class, 'edit'])->name('chargesType.edit');
            Route::put('/{id}', [chargesTypeController::class, 'update'])->name('chargesType.update');
            Route::delete('/{id}', [chargesTypeController::class, 'destroy'])->name('chargesType.destroy');
            Route::post('/{id}/changeStatus', [chargesTypeController::class, 'changeStatus'])->name('chargesType.changeStatus');
        });  

        # Car Type
        Route::prefix('carType')->group(function () {
            Route::get('/', function () {return view('carType.carType'); })->name('carType');
            Route::get('/data', [carTypeController::class, 'index'])->name('carType.data');
            Route::post('/store', [carTypeController::class, 'store'])->name('carType.store');
            Route::get('/{id}/edit', [carTypeController::class, 'edit'])->name('carType.edit');
            Route::put('/{id}', [carTypeController::class, 'update'])->name('carType.update');
            Route::delete('/{id}', [carTypeController::class, 'destroy'])->name('carType.destroy');
            Route::post('/{id}/changeStatus', [carTypeController::class, 'changeStatus'])->name('carType.changeStatus');
        }); 
        
        Route::prefix('car')->group(function () {
            Route::get('/', function () {return view('car.car'); })->name('car');
            Route::get('/data', [carController::class, 'index'])->name('car.data');
            Route::get('/chargesType', [carController::class, 'chargesType'])->name('car.chargesType');
            Route::post('/store', [carController::class, 'store'])->name('car.store');
            Route::get('/car', [carController::class, 'getCarTypes'])->name('car.carData');
            Route::get('/{id}/edit', [carController::class, 'edit'])->name('car.edit');
            Route::put('/{id}', [carController::class, 'update'])->name('car.update');
            Route::delete('/{id}', [carController::class, 'destroy'])->name('car.destroy');
            Route::post('/{id}/changeStatus', [carController::class, 'changeStatus'])->name('car.changeStatus');
        });

        Route::prefix('serviceFrequency')->group(function () {
            Route::get('/', function () {return view('serviceFrequency.serviceFrequency'); })->name('serviceFrequency');
            Route::get('/data', [serviceFrequencyController::class, 'index'])->name('serviceFrequency.data');
            Route::get('/getServiceType', [ordersController::class, 'getServiceType'])->name('serviceFrequency.getServiceType');
            Route::post('/store', [serviceFrequencyController::class, 'store'])->name('serviceFrequency.store');
            Route::get('/{id}/edit', [serviceFrequencyController::class, 'edit'])->name('serviceFrequency.edit');
            Route::put('/{id}', [serviceFrequencyController::class, 'update'])->name('serviceFrequency.update');
            Route::delete('/{id}', [serviceFrequencyController::class, 'destroy'])->name('serviceFrequency.destroy');
            Route::post('/{id}/changeStatus', [serviceFrequencyController::class, 'changeStatus'])->name('serviceFrequency.changeStatus');
        });


        # Access Management
        Route::prefix('access')->group(function () {

            #  permission 
            Route::get('/permissions', function () {return view('access.permissions'); })->name('access.permissions');
            Route::get('/permissions/data', [permissionsController::class, 'index'])->name('access.permissions.data');
            Route::post('/permissions/store', [permissionsController::class, 'store'])->name('access.permissions.store');
            Route::get('/permissions/{id}/edit', [permissionsController::class, 'edit'])->name('access.permissions.edit');
            Route::put('/permissions/{id}', [permissionsController::class, 'update'])->name('access.permissions.update');
            Route::delete('/permissions/{id}', [permissionsController::class, 'destroy'])->name('access.permissions.destroy');

            #  Role
            Route::get('/roles', function () {return view('access.roles'); })->name('access.roles');
            Route::get('/roles/data', [rolesController::class, 'index'])->name('access.roles.data');
            Route::post('/roles/store', [rolesController::class, 'store'])->name('access.roles.store');
            Route::get('/rolespermissions/permissionsData', [rolesController::class, 'getPermissionsList'])->name('access.roles.permissionsData');
            Route::get('/roles/{id}/edit', [rolesController::class, 'edit'])->name('access.roles.edit');
            Route::put('/roles/{id}', [rolesController::class, 'update'])->name('access.roles.update');
            Route::delete('/roles/{id}', [rolesController::class, 'destroy'])->name('access.roles.destroy');

            #  User Page
            Route::get('/user', function () {return view('access.user'); })->name('access.user');
            Route::get('/user/data', [userController::class, 'index'])->name('access.user.data');
            Route::get('/user/getRole', [userController::class, 'getRole'])->name('access.user.getRole');
            Route::post('/user/store', [userController::class, 'store'])->name('access.user.store');
            Route::get('/user/{id}/edit', [userController::class, 'edit'])->name('access.user.edit');
            Route::put('/user/{id}', [userController::class, 'update'])->name('access.user.update');
            Route::delete('/user/{id}', [userController::class, 'destroy'])->name('access.user.destroy');
        });



        # Sliders Management
        Route::prefix('sliders')->group(function () {
            Route::get('/', function () { return view('sliders.index'); })->name('sliders.index');
        });

        # Settings
        Route::prefix('settings')->group(function () {
            Route::get('settings', function () {
                return view('settings.settings'); })->name('settings');
            Route::get('/data', [settingsController::class, 'index'])->name('settings.data');
            Route::post('/update', [settingsController::class, 'update'])->name('settings.update');
        });
    });
});
?>