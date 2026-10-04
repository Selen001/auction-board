<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Seller\AuctionController as SellerAuctionController;
use App\Http\Controllers\AuctionController;
use App\Http\Controllers\BidController;

// Public auction board
Route::get('/', [AuctionController::class, 'index'])->name('auctions.index');
Route::get('/auctions/{auction}', [AuctionController::class, 'show'])->name('auctions.show');

// Guests only
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->middleware('throttle:10,1');

    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:10,1');
});

// Logged-in users
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/seller/dashboard', [DashboardController::class, 'seller'])
        ->middleware('role:seller')
        ->name('seller.dashboard');

    Route::get('/buyer/dashboard', [DashboardController::class, 'buyer'])
        ->middleware('role:buyer')
        ->name('buyer.dashboard');

    Route::middleware('role:seller')
        ->prefix('seller')
        ->name('seller.')
        ->group(function () {
    Route::resource('auctions', SellerAuctionController::class)
        ->except(['show', 'destroy']);

    Route::patch('auctions/{auction}/cancel', [SellerAuctionController::class, 'cancel'])
        ->name('auctions.cancel');
        });
            Route::post('/auctions/{auction}/bids', [BidController::class, 'store'])
        ->middleware(['role:buyer', 'throttle:30,1'])
        ->name('auctions.bids.store');
});