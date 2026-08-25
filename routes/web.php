<?php

use App\Http\Controllers\Customer\DashboardController as CustomerDashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Provider\DashboardController as ProviderDashboardController;
use App\Models\Role;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return redirect()->route(auth()->user()->homeRoute());
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/admin', function () {
        abort_unless((int) auth()->user()->role_id === Role::ADMIN, 403);
        return view('admin.dashboard');
    })->name('admin.dashboard');

    // Müşteri Rotaları
    Route::get('/musteri', [CustomerDashboardController::class, 'index'])->name('customer.dashboard');
    Route::get('/usta-profili/{id}', [CustomerDashboardController::class, 'show'])->name('customer.provider.show');

    // USTA Rotaları
    Route::get('/usta', [ProviderDashboardController::class, 'index'])->name('provider.dashboard');
    Route::put('/usta', [ProviderDashboardController::class, 'update'])->name('provider.dashboard.update');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';