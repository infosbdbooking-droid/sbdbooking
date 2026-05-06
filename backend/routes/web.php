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


Route::prefix('panel')->group(function () {

    Route::get('/', function () {
        return view('index');
    });

    Route::post('/signin/verify', [ActionController::class, 'signIn'])->name('signin.verify');
    Route::middleware(['web', CheckAuthentication::class])->group(function () {

        # Dashboard
        Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

        # Logout
        Route::get('/signout', [ActionController::class, 'logout']);

        # Cab Orders (Bookings)
        Route::prefix('cab-orders')->group(function () {
            Route::get('/', [CabOrderWebController::class, 'index'])->name('cabOrders');
            Route::get('/{id}', [CabOrderWebController::class, 'show'])->name('cabOrders.show');
            Route::get('/{id}/invoice', [CabOrderWebController::class, 'downloadInvoice'])->name('cabOrders.invoice');
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