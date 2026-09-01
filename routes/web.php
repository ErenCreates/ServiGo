<?php

use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\RequestController as AdminRequestController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Customer\AppointmentController as CustomerAppointmentController;
use App\Http\Controllers\Customer\DashboardController as CustomerDashboardController;
use App\Http\Controllers\Customer\ReviewController as CustomerReviewController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Provider\AppointmentController as ProviderAppointmentController;
use App\Http\Controllers\Provider\DashboardController as ProviderDashboardController;
use App\Models\Role;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $categories = collect();
    $stats = [
        'providers' => 0,
        'completed' => 0,
        'reviews'   => 0,
    ];

    if (\Illuminate\Support\Facades\Schema::hasTable('service_categories')) {
        $categories = \App\Models\ServiceCategory::where('is_active', true)
            ->withCount('serviceProviders')
            ->orderBy('name')
            ->take(6)
            ->get();
    }

    if (\Illuminate\Support\Facades\Schema::hasTable('service_providers')) {
        $stats['providers'] = \App\Models\ServiceProvider::count();
    }

    if (\Illuminate\Support\Facades\Schema::hasTable('appointments')) {
        $stats['completed'] = \App\Models\Appointment::where('status', 'completed')->count();
    }

    if (\Illuminate\Support\Facades\Schema::hasTable('reviews')) {
        $stats['reviews'] = \App\Models\Review::where('is_approved', true)->count();
    }

    return view('welcome', compact('categories', 'stats'));
});

Route::get('/dashboard', function () {
    return redirect()->route(auth()->user()->homeRoute());
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {

    // Admin Rotaları
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Kategori Yönetimi (CRUD & Aktif/Pasif)
        Route::get('/kategoriler', [AdminCategoryController::class, 'index'])->name('categories.index');
        Route::post('/kategoriler', [AdminCategoryController::class, 'store'])->name('categories.store');
        Route::put('/kategoriler/{id}', [AdminCategoryController::class, 'update'])->name('categories.update');
        Route::patch('/kategoriler/{id}/durum', [AdminCategoryController::class, 'toggleStatus'])->name('categories.toggle');
        Route::delete('/kategoriler/{id}', [AdminCategoryController::class, 'destroy'])->name('categories.destroy');

        // Kullanıcı ve Usta Yönetimi (Silme & Aktif/Pasif)
        Route::get('/kullanicilar', [AdminUserController::class, 'index'])->name('users.index');
        Route::patch('/kullanicilar/{id}/durum', [AdminUserController::class, 'toggleStatus'])->name('users.toggle');
        Route::delete('/kullanicilar/{id}', [AdminUserController::class, 'destroy'])->name('users.destroy');

        // Talepler & Randevular Genel Görünümü
        Route::get('/talepler', [AdminRequestController::class, 'index'])->name('requests.index');
        Route::patch('/talepler/{id}/durum', [AdminRequestController::class, 'updateStatus'])->name('requests.update');
        Route::delete('/talepler/{id}', [AdminRequestController::class, 'destroy'])->name('requests.destroy');

        // Yorumlar & Değerlendirmeler Genel Görünümü
        Route::get('/yorumlar', [AdminReviewController::class, 'index'])->name('reviews.index');
        Route::patch('/yorumlar/{id}/onay', [AdminReviewController::class, 'toggleApproval'])->name('reviews.toggle');
        Route::delete('/yorumlar/{id}', [AdminReviewController::class, 'destroy'])->name('reviews.destroy');
    });

    // Müşteri Rotaları
    Route::get('/musteri', [CustomerDashboardController::class, 'index'])->name('customer.dashboard');
    Route::get('/musteri/randevular', [CustomerAppointmentController::class, 'index'])->name('customer.appointments.index');
    Route::patch('/musteri/randevular/{id}/iptal', [CustomerAppointmentController::class, 'cancel'])->name('customer.appointments.cancel');
    Route::post('/musteri/randevular/{id}/degerlendir', [CustomerReviewController::class, 'store'])->name('customer.reviews.store');
    Route::get('/usta-profili/{id}', [CustomerDashboardController::class, 'show'])->name('customer.provider.show');
    Route::post('/usta-profili/{id}/talep', [CustomerDashboardController::class, 'storeRequest'])->name('customer.request.store');

    // Usta Rotaları
    Route::get('/usta', [ProviderDashboardController::class, 'index'])->name('provider.dashboard');
    Route::get('/usta/profil', [ProviderDashboardController::class, 'editProfile'])->name('provider.profile.edit');
    Route::put('/usta/profil', [ProviderDashboardController::class, 'updateProfile'])->name('provider.profile.update');
    Route::put('/usta', [ProviderDashboardController::class, 'update'])->name('provider.dashboard.update');
    
    // Usta Randevu Rotaları
    Route::get('/usta/randevular', [ProviderAppointmentController::class, 'index'])->name('provider.appointments.index');
    Route::patch('/usta/randevular/{id}/tamamla', [ProviderAppointmentController::class, 'complete'])->name('provider.appointments.complete');
    Route::patch('/usta/randevular/{id}/iptal', [ProviderAppointmentController::class, 'cancel'])->name('provider.appointments.cancel');
    Route::patch('/usta/randevular/{id}/ertele', [ProviderAppointmentController::class, 'reschedule'])->name('provider.appointments.reschedule');

    // Usta Talep İşlemleri
    Route::post('/provider/requests/{id}/accept', [ProviderDashboardController::class, 'acceptRequest'])->name('provider.request.accept');
    Route::post('/provider/requests/{id}/reject', [ProviderDashboardController::class, 'rejectRequest'])->name('provider.request.reject');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';