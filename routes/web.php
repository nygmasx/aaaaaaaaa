<?php

use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\ExchangeController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
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

require __DIR__ . '/settings.php';
