<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\StoreController;
use Illuminate\Support\Facades\Route;

// ============================================
// Auth Routes (AJAX — no dedicated pages)
// ============================================
Route::post('/auth/register', [AuthController::class, 'register'])->name('auth.register');
Route::post('/auth/login', [AuthController::class, 'login'])->name('auth.login');
Route::post('/auth/logout', [AuthController::class, 'logout'])->name('auth.logout');
Route::post('/auth/forgot-password', [AuthController::class, 'forgotPassword'])->name('auth.forgot-password');
Route::post('/auth/reset-password', [AuthController::class, 'resetPassword'])->name('auth.reset-password');

// Email Verification — signed URL from email link
Route::get('/email/verify/{id}/{hash}', function (\Illuminate\Http\Request $request) {
    $user = \App\Models\User::findOrFail($request->route('id'));

    if (! hash_equals((string) $request->route('id'), (string) $user->getKey())) {
        abort(403);
    }

    if (! hash_equals((string) $request->route('hash'), sha1($user->getEmailForVerification()))) {
        abort(403);
    }

    if ($user->hasVerifiedEmail()) {
        return redirect()->route('home')->with('message', __('ui.email_verified'));
    }

    $user->markEmailAsVerified();
    event(new \Illuminate\Auth\Events\Verified($user));

    // Auto-login after email verification
    \Illuminate\Support\Facades\Auth::login($user);

    return redirect()->route('onboarding.step1');
})->middleware(['signed', 'throttle:6,1'])->name('verification.verify');

Route::post('/email/verification-notification', function (\Illuminate\Http\Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return response()->json(['success' => true, 'message' => 'Verification email resent.']);
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// ============================================
// Onboarding Routes (3-step after email verification)
// ============================================
Route::middleware('auth')->prefix('onboarding')->name('onboarding.')->group(function () {
    Route::get('/step-1', [\App\Http\Controllers\OnboardingController::class, 'step1'])->name('step1');
    Route::post('/step-1', [\App\Http\Controllers\OnboardingController::class, 'storeStep1'])->name('step1.store');
    Route::post('/step-2', [\App\Http\Controllers\OnboardingController::class, 'storeStep2'])->name('step2.store');
    Route::post('/step-3', [\App\Http\Controllers\OnboardingController::class, 'storeStep3'])->name('step3.store');
});

// ============================================
// Public Routes
// ============================================
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/produk', [SearchController::class, 'index'])->name('products.index');

Route::get('/produk/{slug}', [ProductController::class, 'show'])->name('products.show');

Route::get('/about-us', function () {
    $products = \App\Models\Product::with(['primaryImage', 'owner'])
        ->where('status', 'active')
        ->latest()
        ->get();
    return view('about', [
        'recomended' => $products->take(6),
        'nearYou' => $products->shuffle()->take(6),
        'availableNow' => $products->sortByDesc('created_at')->take(6),
        'featuredProduct' => $products->first(),
    ]);
})->name('about.index');

// ============================================
// Store Routes (Public — F-BRW-04)
// ============================================
Route::get('/toko/{user}', [StoreController::class, 'show'])->name('store.show');
Route::get('/toko/{user}/about', [StoreController::class, 'show'])->name('store.about');
Route::get('/toko/{user}/reviews', [StoreController::class, 'show'])->name('store.reviews');

// ============================================
// Borrower Routes (Middleware: auth — Phase 1)
// ============================================
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Borrower\DashboardController::class, 'index'])
        ->name('borrower.dashboard');

    Route::get('/aktivitas', function () {
        return view('home');
    })->name('borrower.activity');

    Route::get('/riwayat', function () {
        return view('home');
    })->name('borrower.history');

    Route::get('/pengaturan', function () {
        return redirect()->route('borrower.dashboard', ['tab' => 'settings']);
    })->name('borrower.settings');

    // ── Settings (Theme & Language) ──
    Route::post('/dashboard/settings', [\App\Http\Controllers\Borrower\SettingsController::class, 'update'])
        ->name('borrower.settings.update');

    // ── Chat (PRD sections 6.5, 16.5) ──
    Route::prefix('pesan')->name('chat.')->group(function () {
        Route::get('/', [ChatController::class, 'index'])->name('index');
        Route::get('/unread/count', [ChatController::class, 'unreadCount'])->name('unread');
        Route::post('/mulai/{product}', [ChatController::class, 'start'])->name('start');
        Route::get('/{conversation}', [ChatController::class, 'show'])->name('show');
        Route::post('/{conversation}', [ChatController::class, 'send'])->name('send');
    });

    // ── Peminjaman (PRD section 16.3) ──
    Route::post('/peminjaman', [\App\Http\Controllers\Borrower\RentalController::class, 'store'])
        ->name('rentals.store');
    Route::post('/peminjaman/{id}/batal', [\App\Http\Controllers\Borrower\RentalController::class, 'cancel'])
        ->name('rentals.cancel');

    // ── Rating (PRD section 16.3) ──
    Route::post('/peminjaman/{id}/rating', [\App\Http\Controllers\Borrower\RatingController::class, 'storeForOwner'])
        ->name('ratings.storeForOwner');
});


Route::get('/about', function(){
    return view('about');
});

