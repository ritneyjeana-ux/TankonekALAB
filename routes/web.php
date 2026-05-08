<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\AuthController;

// 🏠 MAIN LANDING PAGE (For all user types)
Route::get('/', function () {
    return Inertia::render('LandingPage');
})->name('landing');

// 👨‍💼 ADMIN AUTH - HYPHEN VERSION
Route::get('/admin-login', function () {
    return Inertia::render('AdminLogin');
})->name('admin.login');

// ✅ OPTIONAL: Add direct admin route
Route::get('/admin', function () {
    return redirect('/admin-login');
})->name('admin.landing');

// 🔹 ADDED: Admin Landing Route (Pure Login Form)
Route::get('/admin-landing', function () {
    return Inertia::render('AdminLanding');
})->name('admin.landing.page');

// 🔹 ADDED: Admin Login Post Route - HYPHEN VERSION
Route::post('/admin-login', [AuthController::class, 'adminLogin'])->name('admin.login.attempt');

// 📊 DASHBOARD & APP ROUTES

// 👩 CUSTOMER ROUTES
Route::prefix('customer')->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('customer/CustomerOrder');
    })->name('customer.dashboard');

    Route::get('/orders', function () {
        return Inertia::render('customer/CustomerOrder');
    })->name('customer.orders');

    Route::get('/my-orders', function () {
        return Inertia::render('customer/MyOrder');
    })->name('customer.myorders');

    Route::get('/profile', function () {
        return Inertia::render('customer/Profile');
    })->name('customer.profile');

    Route::get('/rewards', function () {
        return Inertia::render('customer/Reward');
    })->name('customer.rewards');
});

// 🏪 RETAILER ROUTES
Route::prefix('retailer')->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('retailer/RetailerOrder');
    })->name('retailer.dashboard');

    Route::get('/orders', function () {
        return Inertia::render('retailer/RetailerOrder');
    })->name('retailer.orders');

    Route::get('/inventory', function () {
        return Inertia::render('retailer/Inventory');
    })->name('retailer.inventory');

    Route::get('/deliveries', function () {
        return Inertia::render('retailer/Deliveries');
    })->name('retailer.deliveries');

    Route::get('/notifications', function () {
        return Inertia::render('retailer/Notification');
    })->name('retailer.notifications');

    Route::get('/settings', function () {
        return Inertia::render('retailer/Settings');
    })->name('retailer.settings');
});

// 🚚 DELIVERY PARTNER ROUTES
Route::prefix('delivery-partner')->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('delivery-partner/Available');
    })->name('delivery.dashboard');

    Route::get('/available', function () {
        return Inertia::render('delivery-partner/Available');
    })->name('delivery.available');

    Route::get('/active', function () {
        return Inertia::render('delivery-partner/Active');
    })->name('delivery.active');

    Route::get('/history', function () {
        return Inertia::render('delivery-partner/History');
    })->name('delivery.history');
});

// 👨‍💼 ADMIN ROUTES
Route::prefix('admin')->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('admin/Overview');
    })->name('admin.dashboard');

    Route::get('/overview', function () {
        return Inertia::render('admin/Overview');
    })->name('admin.overview');

    Route::get('/analytics', function () {
        return Inertia::render('admin/Analytics');
    })->name('admin.analytics');

    Route::get('/users', function () {
        return Inertia::render('admin/User');
    })->name('admin.users');

    Route::get('/orders', function () {
        return Inertia::render('admin/Order');
    })->name('admin.orders');
});