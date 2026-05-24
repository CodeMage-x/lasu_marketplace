<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MeetupProposalController;
use App\Http\Controllers\PaystackController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\Buyer\CartController;
use App\Http\Controllers\Buyer\OrderController as BuyerOrderController;
use App\Http\Controllers\Seller\DashboardController as SellerDashboardController;
use App\Http\Controllers\Seller\ListingController as SellerListingController;
use App\Http\Controllers\Seller\OrderController as SellerOrderController;
use App\Http\Controllers\Seller\StoreController as SellerStoreController;
use App\Http\Controllers\Admin\CampusZoneController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ListingController as AdminListingController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\StoreController as AdminStoreController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use Illuminate\Support\Facades\Route;

// ── Public routes ──────────────────────────────────────────────────────────────
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/listings', [HomeController::class, 'listings'])->name('listings.index');
Route::get('/listings/{listing}', [HomeController::class, 'show'])->name('listings.show');

// Store public profile
Route::get('/stores/{store:slug}', [SellerStoreController::class, 'show'])->name('stores.public');

// ── Auth routes ────────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
});

Route::post('/logout', [LoginController::class, 'destroy'])->name('logout')->middleware('auth');

// Email verification (Laravel built-in)
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', fn () => view('auth.verify-email'))->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', function (\Illuminate\Foundation\Auth\EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect()->route('home')->with('success', 'Email verified successfully!');
    })->middleware('signed')->name('verification.verify');
    Route::post('/email/verification-notification', function (\Illuminate\Http\Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('success', 'Verification link sent!');
    })->middleware('throttle:6,1')->name('verification.send');
});

// ── Authenticated routes ───────────────────────────────────────────────────────
Route::middleware(['auth', 'verified'])->group(function () {

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Notifications (mark as read)
    Route::post('/notifications/read-all', function () {
        auth()->user()->unreadNotifications->markAsRead();
        return back()->with('success', 'All notifications marked as read.');
    })->name('notifications.readAll');

    // Conversations & Messaging
    Route::get('/conversations', [ConversationController::class, 'index'])->name('conversations.index');
    Route::post('/conversations/listing/{listing}', [ConversationController::class, 'openOrCreate'])->name('conversations.open');
    Route::get('/conversations/{conversation}', [ConversationController::class, 'show'])->name('conversations.show');
    Route::post('/conversations/{conversation}/messages', [ConversationController::class, 'sendMessage'])->name('conversations.message');

    // Meetup Proposals
    Route::post('/conversations/{conversation}/meetup', [MeetupProposalController::class, 'store'])->name('meetup.store');
    Route::patch('/meetup/{proposal}/accept', [MeetupProposalController::class, 'accept'])->name('meetup.accept');
    Route::patch('/meetup/{proposal}/decline', [MeetupProposalController::class, 'decline'])->name('meetup.decline');
    Route::post('/meetup/{proposal}/counter', [MeetupProposalController::class, 'counter'])->name('meetup.counter');

    // Reviews
    Route::post('/orders/{order}/review', [ReviewController::class, 'store'])->name('reviews.store');

    // Reports
    Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');

    // ── Buyer routes ───────────────────────────────────────────────────────────
    Route::prefix('cart')->name('cart.')->group(function () {
        Route::get('/', [CartController::class, 'index'])->name('index');
        Route::post('/add/{listing}', [CartController::class, 'add'])->name('add');
        Route::patch('/update/{cartItem}', [CartController::class, 'update'])->name('update');
        Route::delete('/remove/{cartItem}', [CartController::class, 'remove'])->name('remove');
        Route::delete('/clear', [CartController::class, 'clear'])->name('clear');
    });

    Route::prefix('orders')->name('buyer.orders.')->group(function () {
        Route::get('/checkout', [BuyerOrderController::class, 'checkout'])->name('checkout');
        Route::post('/place', [BuyerOrderController::class, 'place'])->name('place');
        Route::get('/', [BuyerOrderController::class, 'index'])->name('index');
        Route::get('/{order}', [BuyerOrderController::class, 'show'])->name('show');
        Route::patch('/{order}/confirm-received', [BuyerOrderController::class, 'confirmReceived'])->name('confirmReceived');
        Route::patch('/{order}/cancel', [BuyerOrderController::class, 'cancel'])->name('cancel');
    });

    // Paystack payment
    Route::get('/payment/initiate/{order}', [PaystackController::class, 'initiate'])->name('payment.initiate');
    Route::get('/payment/callback', [PaystackController::class, 'callback'])->name('payment.callback');

    // ── Seller routes ──────────────────────────────────────────────────────────
    Route::middleware('role:seller,admin')->prefix('seller')->name('seller.')->group(function () {
        Route::get('/dashboard', [SellerDashboardController::class, 'index'])->name('dashboard');

        // Store management
        Route::get('/store', [SellerStoreController::class, 'show'])->name('store.show');
        Route::get('/store/create', [SellerStoreController::class, 'create'])->name('store.create');
        Route::post('/store', [SellerStoreController::class, 'store'])->name('store.store');
        Route::get('/store/edit', [SellerStoreController::class, 'edit'])->name('store.edit');
        Route::patch('/store', [SellerStoreController::class, 'update'])->name('store.update');

        // Listing management
        Route::resource('listings', SellerListingController::class)->except(['show']);

        // Order management
        Route::get('/orders', [SellerOrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [SellerOrderController::class, 'show'])->name('orders.show');
        Route::patch('/orders/{order}/confirm', [SellerOrderController::class, 'confirm'])->name('orders.confirm');
        Route::patch('/orders/{order}/handed-over', [SellerOrderController::class, 'markHandedOver'])->name('orders.handedOver');
    });

    // ── Admin routes ───────────────────────────────────────────────────────────
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Users
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::get('/users/{user}', [AdminUserController::class, 'show'])->name('users.show');
        Route::patch('/users/{user}/suspend', [AdminUserController::class, 'suspend'])->name('users.suspend');
        Route::patch('/users/{user}/activate', [AdminUserController::class, 'activate'])->name('users.activate');
        Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
        Route::patch('/users/{id}/restore', [AdminUserController::class, 'restore'])->name('users.restore');

        // Stores
        Route::get('/stores', [AdminStoreController::class, 'index'])->name('stores.index');
        Route::patch('/stores/{store}/verify', [AdminStoreController::class, 'verify'])->name('stores.verify');
        Route::patch('/stores/{store}/suspend', [AdminStoreController::class, 'suspend'])->name('stores.suspend');

        // Listings
        Route::get('/listings', [AdminListingController::class, 'index'])->name('listings.index');
        Route::delete('/listings/{listing}', [AdminListingController::class, 'destroy'])->name('listings.destroy');
        Route::patch('/listings/{id}/restore', [AdminListingController::class, 'restore'])->name('listings.restore');

        // Reports
        Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');
        Route::patch('/reports/{report}/review', [AdminReportController::class, 'review'])->name('reports.review');
        Route::patch('/reports/{report}/resolve', [AdminReportController::class, 'resolve'])->name('reports.resolve');

        // Campus Zones
        Route::resource('zones', CampusZoneController::class)->names([
            'index'   => 'zones.index',
            'create'  => 'zones.create',
            'store'   => 'zones.store',
            'edit'    => 'zones.edit',
            'update'  => 'zones.update',
            'destroy' => 'zones.destroy',
        ]);
    });
});

// Paystack webhook (no auth — verified by signature)
Route::post('/webhooks/paystack', [PaystackController::class, 'webhook'])
    ->name('webhooks.paystack')
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
