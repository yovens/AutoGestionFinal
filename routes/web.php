<?php

use App\Http\Controllers\Admin\AdminProfileController;
use App\Http\Controllers\Admin\ContactController as AdminContactController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
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
use App\Http\Controllers\Client\TicketController as ClientTicket;
use App\Http\Controllers\Counter\CashController;
use App\Http\Controllers\Counter\ClientController as CounterClient;
use App\Http\Controllers\Counter\DashboardController as CounterDashboard;
use App\Http\Controllers\Counter\LoanController;
use App\Http\Controllers\Counter\SaleController;
use App\Http\Controllers\Counter\TicketController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SocialiteController;
use App\Http\Controllers\Super\ActivityController;
use App\Http\Controllers\Super\ReportController;
use App\Http\Controllers\Super\SettingsController;
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

    // Tranzaksyon
    Route::resource('transactions', TransactionController::class)->only(['index', 'show', 'store', 'destroy']);
});

/*
|--------------------------------------------------------------------------
| DIRECTION (super admin sèlman)
|--------------------------------------------------------------------------
*/
Route::prefix('direction')->name('super.')->middleware(['auth', 'active', 'super'])->group(function () {
    Route::get('/rapports', [ReportController::class, 'index'])->name('reports');
    Route::get('/rapports/export', [ReportController::class, 'export'])->name('reports.export');
    Route::get('/journal', [ActivityController::class, 'index'])->name('activity');
    Route::get('/parametres', [SettingsController::class, 'edit'])->name('settings');
    Route::put('/parametres', [SettingsController::class, 'update'])->name('settings.update');
});

/*
|--------------------------------------------------------------------------
| COMPTOIR (sekretè + admin)
|--------------------------------------------------------------------------
*/
Route::prefix('comptoir')->name('counter.')->middleware(['auth', 'active', 'staff'])->group(function () {
    Route::get('/', [CounterDashboard::class, 'index'])->name('index');
    Route::get('/recherche', [CounterDashboard::class, 'search'])->name('search');

    // Lokasyon
    Route::prefix('locations')->name('loans.')->controller(LoanController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/nouvelle', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{loan}', 'show')->name('show');
        Route::post('/{loan}/confirmer', 'approve')->name('approve');
        Route::post('/{loan}/refuser', 'reject')->name('reject');
        Route::post('/{loan}/annuler', 'cancel')->name('cancel');
        Route::get('/{loan}/remise', 'checkoutForm')->name('checkout');
        Route::post('/{loan}/remise', 'checkout')->name('checkout.store');
        Route::get('/{loan}/retour', 'checkinForm')->name('checkin');
        Route::post('/{loan}/retour', 'checkin')->name('checkin.store');
        Route::post('/{loan}/prolonger', 'extend')->name('extend');
        Route::post('/{loan}/prolongation/accepter', 'approveRenewal')->name('renewal.approve');
        Route::post('/{loan}/prolongation/refuser', 'declineRenewal')->name('renewal.decline');
        Route::delete('/{loan}', 'destroy')->name('destroy');
    });

    // Vant: ankese epi remèt machin nan
    Route::post('/ventes/{transaction}/encaisser', [SaleController::class, 'confirm'])->name('sales.confirm');
    Route::post('/ventes/{transaction}/annuler', [SaleController::class, 'cancel'])->name('sales.cancel');

    // Kliyan
    Route::get('/clients', [CounterClient::class, 'index'])->name('clients.index');
    Route::post('/clients', [CounterClient::class, 'store'])->name('clients.store');
    Route::get('/clients/{user}', [CounterClient::class, 'show'])->name('clients.show');

    // Tikè ak kès
    Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/{ticket}', [TicketController::class, 'show'])->name('tickets.show');
    Route::get('/caisse', [CashController::class, 'index'])->name('cash');

    // Pwofil anplwaye (admin + sekretè)
    Route::controller(AdminProfileController::class)->group(function () {
        Route::get('/profil', 'edit')->name('profile');
        Route::put('/profil', 'update')->name('profile.update');
        Route::put('/profil/mot-de-passe', 'updatePassword')->name('profile.password');
    });
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
    Route::get('/tickets/{ticket}', [ClientTicket::class, 'show'])->name('tickets.show');

    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'show')->name('profile');
        Route::put('/profile/update', 'update')->name('profile.update');
        Route::put('/profile/password', 'updatePassword')->name('profile.password');
    });

    Route::get('/contact', [ClientContactController::class, 'index'])->name('contact');
    Route::post('/contact/send', [ClientContactController::class, 'send'])->middleware('throttle:5,1')->name('contact.send');
});
