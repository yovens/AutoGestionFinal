<?php

use App\Http\Controllers\Admin\AdminProfileController;
use App\Http\Controllers\Admin\ContactController as AdminContactController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\LoanController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VehicleController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Client\CartController;
use App\Http\Controllers\Client\ContactController as ClientContactController;
use App\Http\Controllers\Client\DashboardController as ClientDashboard;
use App\Http\Controllers\Client\LoanController as ClientLoan;
use App\Http\Controllers\Client\ProfileController;
use App\Http\Controllers\Client\PurchaseController;
use App\Http\Controllers\Client\VehicleController as ClientVehicle;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SocialiteController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| PIBLIK + OTANTIFIKASYON
|--------------------------------------------------------------------------
*/
Route::get('/', HomeController::class)->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:6,1')->name('login.submit');
    Route::get('/register', [RegisterController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->middleware('throttle:6,1')->name('register.submit');
    Route::get('/auth/google', [SocialiteController::class, 'redirectToGoogle'])->name('google.redirect');
    Route::get('/auth/google/callback', [SocialiteController::class, 'handleGoogleCallback'])->name('google.callback');
});

Route::post('/logout', [LogoutController::class, 'logout'])->middleware('auth')->name('logout');

/*
|--------------------------------------------------------------------------
| ADMIN
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware(['auth', 'active', 'admin'])->group(function () {
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

    Route::controller(AdminProfileController::class)->group(function () {
        Route::get('/profile', 'edit')->name('profile');
        Route::put('/profile', 'update')->name('profile.update');
        Route::put('/profile/password', 'updatePassword')->name('profile.password');
    });

    // Itilizatè
    Route::resource('users', UserController::class)->except(['create', 'edit']);
    Route::post('/users/{user}/block', [UserController::class, 'block'])->name('users.block');

    // Machin
    Route::resource('vehicles', VehicleController::class)->except(['create', 'edit']);

    // Mesaj kliyan
    Route::prefix('messages')->name('contacts.')->controller(AdminContactController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/{contact}/read', 'markRead')->name('read');
        Route::post('/{contact}/reply', 'reply')->name('reply');
        Route::delete('/{contact}', 'destroy')->name('destroy');
    });

    // Lokasyon
    Route::prefix('loans')->name('loans.')->controller(LoanController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::get('/{loan}', 'show')->name('show');
        Route::post('/{loan}/approve', 'approve')->name('approve');
        Route::post('/{loan}/reject', 'reject')->name('reject');
        Route::post('/{loan}/extend', 'extend')->name('extend');
        Route::post('/{loan}/renewal/approve', 'approveRenewal')->name('renewal.approve');
        Route::post('/{loan}/renewal/decline', 'declineRenewal')->name('renewal.decline');
        Route::post('/{loan}/return', 'markReturned')->name('return');
        Route::delete('/{loan}', 'destroy')->name('destroy');
    });

    // Tranzaksyon
    Route::resource('transactions', TransactionController::class)->only(['index', 'show', 'store', 'destroy']);
    Route::post('/transactions/{transaction}/confirm', [TransactionController::class, 'confirm'])->name('transactions.confirm');
    Route::post('/transactions/{transaction}/cancel', [TransactionController::class, 'cancel'])->name('transactions.cancel');
});

/*
|--------------------------------------------------------------------------
| KLIYAN
|--------------------------------------------------------------------------
*/
Route::prefix('client')->name('client.')->middleware(['auth', 'active', 'client'])->group(function () {
    Route::get('/dashboard', [ClientDashboard::class, 'index'])->name('dashboard');
    Route::get('/about', [ClientDashboard::class, 'about'])->name('about');

    Route::get('/vehicles', [ClientVehicle::class, 'index'])->name('vehicles');
    Route::get('/vehicle/{vehicle}', [ClientVehicle::class, 'show'])->name('vehicle.show');

    Route::controller(CartController::class)->group(function () {
        Route::get('/cart', 'index')->name('cart');
        Route::post('/cart/add/{vehicle}', 'add')->name('cart.add');
        Route::delete('/cart/delete/{id}', 'delete')->name('cart.delete');
    });

    Route::controller(ClientLoan::class)->group(function () {
        Route::get('/loan', 'index')->name('loan');
        Route::post('/loan/start/{vehicle}', 'start')->name('loan.start');
        Route::post('/loan/renew/{id}', 'renew')->name('loan.renew');
        Route::get('/transactions', 'transactions')->name('transactions');
    });

    Route::post('/purchase/start/{vehicle}', [PurchaseController::class, 'start'])->name('purchase.start');

    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'show')->name('profile');
        Route::put('/profile/update', 'update')->name('profile.update');
        Route::put('/profile/password', 'updatePassword')->name('profile.password');
    });

    Route::get('/contact', [ClientContactController::class, 'index'])->name('contact');
    Route::post('/contact/send', [ClientContactController::class, 'send'])->middleware('throttle:5,1')->name('contact.send');
});
