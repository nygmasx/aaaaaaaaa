<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExchangeController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('dashboard', function () {
    if (Auth::user()->role === 'ROLE_ADMIN') {
        return redirect('/admin/exchanges');
    }
    return app(DashboardController::class)->index();
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/currencies', [CurrencyController::class, 'index'])
    ->name('currency.index')
    ->middleware(['auth', 'verified']);

Route::get('/transfer', [ExchangeController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('transfer');

Route::post('/exchange/convert', [ExchangeController::class, 'convert'])
    ->middleware(['auth', 'verified'])->name('exchange.convert');

Route::post('/exchange/find-user', [ExchangeController::class, 'findUserByIban'])
    ->middleware(['auth', 'verified'])->name('exchange.findUser');

Route::post('/exchange/store', [ExchangeController::class, 'store'])
    ->middleware(['auth', 'verified'])->name('exchange.store');

// Routes d'administration
Route::prefix('admin')->name('admin.')->middleware(['auth', 'verified', 'admin'])->group(function () {
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/users/{id}', [AdminController::class, 'userDetails'])->name('user.details');
    Route::post('/users/{id}/toggle-admin', [AdminController::class, 'toggleAdmin'])->name('user.toggle-admin');
    Route::post('/users/{id}/toggle-block', [AdminController::class, 'toggleBlock'])->name('user.toggle-block');
    Route::get('/exchanges', [AdminController::class, 'exchanges'])->name('exchanges');
});

require __DIR__ . '/settings.php';
