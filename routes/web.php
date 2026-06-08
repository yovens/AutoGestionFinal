<?php

use Illuminate\Support\Facades\Route;

// Import Controllers
use App\Http\Controllers\Auth\{LoginController, RegisterController, LogoutController};
use App\Http\Controllers\Admin\{
    DashboardController as AdminDashboard, UserController, VehicleController, 
    TransactionController, LoanController, AdminProfileController, ContactController as AdminContactController
};
use App\Http\Controllers\Client\{
    DashboardController as ClientDashboard, VehicleController as ClientVehicle, 
    CartController, LoanController as ClientLoan, ContactController as ClientContactController, 
    PurchaseController, ProfileController
};
use App\Http\Middleware\{IsAdmin, IsClient, CheckUserStatus};
use App\Http\Controllers\SocialiteController;
/*
|--------------------------------------------------------------------------
| AUTH ROUTES
|--------------------------------------------------------------------------
*/
Route::get('/', function () { return view('home'); })->name('home');
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::get('/register', [RegisterController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.submit');
Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');
Route::get('/auth/google', [SocialiteController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [SocialiteController::class, 'handleGoogleCallback']);
/*
|--------------------------------------------------------------------------
| ADMIN ROUTES
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware(['auth', IsAdmin::class, CheckUserStatus::class])->group(function () {
    
    // Profile
    Route::controller(AdminProfileController::class)->group(function () {
        Route::get('/profile', 'edit')->name('profile');
        Route::put('/profile', 'update')->name('profile.update');
        Route::put('/profile/password', 'updatePassword')->name('profile.password');
    });

    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

    // Users
    Route::resource('users', UserController::class);
    Route::post('/users/{user}/block', [UserController::class, 'block'])->name('users.block');

    // Vehicles
    Route::resource('vehicles', VehicleController::class);

    // Messages (Contacts) - ITILIZE AdminContactController
    Route::prefix('messages')->name('contacts.')->group(function () {
        Route::get('/', [AdminContactController::class, 'index'])->name('index');
        Route::delete('/{id}', [AdminContactController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/reply', [AdminContactController::class, 'reply'])->name('reply');
    });

    // Loans
    Route::controller(LoanController::class)->group(function () {
        Route::get('/loans', 'index')->name('loans.index');
        Route::get('/loans/{id}', 'show')->name('loans.show');
        Route::put('/loans/{id}', 'update')->name('loans.update');
        Route::post('/loans/{id}/renew', 'renew')->name('loans.renew');
        Route::delete('/loans/{id}', 'destroy')->name('loans.destroy');
        Route::post('/loans/approve-renewal/{id}', 'approveRenewal')->name('loans.approve-renewal');
        Route::post('/loans', 'store')->name('loans.store');
    });

    // Transactions
    Route::resource('transactions', TransactionController::class)->only(['index', 'show', 'destroy', 'store']);
});

/*
|--------------------------------------------------------------------------
| CLIENT ROUTES
|--------------------------------------------------------------------------
*/
Route::prefix('client')->name('client.')->middleware(['auth', IsClient::class, CheckUserStatus::class])->group(function () {
    Route::get('/dashboard', [ClientDashboard::class, 'index'])->name('dashboard');
    Route::get('/vehicles', [ClientVehicle::class, 'index'])->name('vehicles');
    Route::get('/vehicle/{id}', [ClientVehicle::class, 'show'])->name('vehicle.show');

    Route::controller(CartController::class)->group(function () {
        Route::get('/cart', 'index')->name('cart');
        Route::post('/cart/add/{id}', 'add')->name('cart.add');
        Route::delete('/cart/delete/{id}', 'delete')->name('cart.delete');
    });

    Route::controller(ClientLoan::class)->group(function () {
        Route::get('/loan', 'index')->name('loan');
        Route::post('/loan/start/{id}', 'start')->name('loan.start');
        Route::post('/loan/renew/{id}', 'renew')->name('loan.renew');
        Route::get('/transactions', 'transactions')->name('transactions');
    });

  Route::controller(ProfileController::class)->group(function () {
    Route::get('/profile', 'show')->name('profile');
    
    // Wout pou Noms ak Email
    Route::put('/profile/update', 'update')->name('profile.update');
    
    // Wout espesifik pou Modpas
    Route::put('/profile/password', 'updatePassword')->name('profile.password');
});

    Route::controller(PurchaseController::class)->group(function () {
        Route::post('/purchase/start/{id}', 'start')->name('purchase.start');
        Route::post('/purchase/store', 'store')->name('purchase.store');
    });

    Route::get('/about', [ClientDashboard::class, 'about'])->name('about');
    Route::get('/contact', [ClientContactController::class, 'index'])->name('contact');
    Route::post('/contact/send', [ClientContactController::class, 'send'])->name('contact.send');
    // Asire w li se yon POST, pa yon GET

});