<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Borrower\StoreRentalRequestController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\Admin\DisputeController;
use App\Http\Controllers\Admin\AdminUserController;

use Illuminate\Support\Facades\Route;


// ============================================
// Auth Routes (AJAX — no dedicated pages)
// ============================================
Route::post('/auth/register', [AuthController::class, 'register'])->name('auth.register');
Route::post('/auth/verify_otp', [AuthController::class, 'verifyOtp'])->name('auth.verify-otp');
Route::post('/auth/login', [AuthController::class, 'login'])->name('auth.login');
Route::post('/auth/logout', [AuthController::class, 'logout'])->name('auth.logout');
Route::post('/auth/forgot-password', [AuthController::class, 'forgotPassword'])->name('auth.forgot-password');
Route::post('/auth/reset-password', [AuthController::class, 'resetPassword'])->name('auth.reset-password');

// Email Verification — Tampilan sukses statis di tab baru saat link diklik
Route::get('/email/verify/{id}/{hash}', function (\Illuminate\Http\Request $request) {
    $user = \App\Models\User::findOrFail($request->route('id'));

    if (! hash_equals((string) $request->route('id'), (string) $user->getKey())) {
        abort(403);
    }

    if (! hash_equals((string) $request->route('hash'), sha1($user->getEmailForVerification()))) {
        abort(403);
    }

    if (!$user->hasVerifiedEmail()) {
        $user->markEmailAsVerified();
        event(new \Illuminate\Auth\Events\Verified($user));
    }

    // Auto-login setelah email verification jika session sempat hilang
    \Illuminate\Support\Facades\Auth::login($user);

    return "
        <div style='text-align: center; margin-top: 80px; font-family: \"Segoe UI\", Tahoma, Geneva, Verdana, sans-serif; color: #333;'>
            <div style='max-width: 500px; margin: 0 auto; padding: 40px; border: 1px solid #e4e4e4; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05);'>
                <h2 style='color: #28a745; margin-bottom: 10px;'>✓ Email Berhasil Diverifikasi</h2>
                <p style='color: #666; font-size: 15px; line-height: 1.6; margin-bottom: 25px;'>
                    Terima kasih! Email Anda telah terverifikasi di sistem SI-RENT. Silakan kembali ke tab utama onboarding Anda untuk melanjutkan pengisian data.
                </p>
                <button onclick='window.close()' style='background: #0031e1; color: white; border: none; padding: 12px 30px; font-weight: 600; border-radius: 8px; cursor: pointer; font-size: 14px; transition: 0.2s;'>
                    Tutup Halaman Ini
                </button>
            </div>
        </div>
    ";
})->middleware(['signed', 'throttle:6,1'])->name('verification.verify');

// Memicu pengiriman email lewat OnboardingController
Route::post('/email/verification-notification', [OnboardingController::class, 'sendVerificationEmail'])
    ->middleware(['auth', 'throttle:6,1'])
    ->name('verification.send');

// ============================================
// Onboarding Routes (3-step after email verification)
// ============================================
Route::middleware(['auth', 'account.active'])->prefix('onboarding')->name('onboarding.')->group(function () {
    Route::get('/step-1', [OnboardingController::class, 'step1'])->name('step1');
    Route::post('/step-1', [OnboardingController::class, 'storeStep1'])->name('step1.store');
    Route::post('/step-2', [OnboardingController::class, 'storeStep2'])->name('step2.store');
    Route::post('/step-3', [OnboardingController::class, 'storeStep3'])->name('step3.store');
    
    // Route AJAX baru untuk memicu OTP WhatsApp dari Alpine.js
    Route::post('/verify-whatsapp', [OnboardingController::class, 'verifyWhatsApp'])->name('verify.whatsapp');
});

// ============================================
// Public Routes
// ============================================
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/produk', [SearchController::class, 'index'])->name('products.index');

Route::get('/produk/{slug}', [ProductController::class, 'show'])->name('products.show');

Route::get('/about-us', [AboutController::class, 'index'])
    ->name('about');

// ============================================
// Store Routes (Public — F-BRW-04)
// ============================================
Route::get('/toko/{user}', [StoreController::class, 'show'])->name('store.show');
Route::get('/toko/{user}/about', [StoreController::class, 'show'])->name('store.about');
Route::get('/toko/{user}/reviews', [StoreController::class, 'show'])->name('store.reviews');

// ============================================
// Admin Routes
// ============================================
Route::middleware(['auth', 'admin', 'account.active'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Admin Dashboard
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])
            ->name('dashboard');

        // User Status
        Route::patch(
            '/users/{user}/status',
            [AdminUserController::class, 'updateStatus']
        )->name('users.update-status');

        // Dispute Management
        Route::get('/disputes', [DisputeController::class, 'index'])
            ->name('disputes.index');

        Route::patch(
            '/disputes/{dispute}/approve',
            [DisputeController::class, 'approve']
        )->name('disputes.approve');

        Route::patch(
            '/disputes/{dispute}/reject',
            [DisputeController::class, 'reject']
        )->name('disputes.reject');
    });

// ============================================
// Borrower Routes (Middleware: auth — Phase 1)
// ============================================
Route::middleware(['auth', 'account.active'])->group(function () {
    
    // API Endpoint ringan untuk kebutuhan Polling status email dari halaman onboarding
    Route::get('/api/user/check-email-status', function() {
        return response()->json([
            'verified' => auth()->user()->hasVerifiedEmail()
        ]);
    });

    Route::get('/dashboard', [\App\Http\Controllers\Borrower\DashboardController::class, 'index'])
        ->name('borrower.dashboard');

    Route::post('/dashboard/store/open', [StoreController::class, 'openDashboardStore'])
        ->name('borrower.store.open');

    Route::post('/dashboard/store/products', [StoreController::class, 'storeProduct'])
    ->name('borrower.store.products.store');

    Route::delete('/dashboard/store/products/{product}', [StoreController::class, 'deleteProduct'])
    ->name('borrower.store.products.delete');

    Route::patch('/dashboard/store/products/{product}', [StoreController::class, 'updateProduct'])
    ->name('borrower.store.products.update');

    // Rental Approve
    Route::patch(
    '/dashboard/store/rental-requests/{rentalRequest}/approve',
        [StoreRentalRequestController::class, 'approve']
    )->name('borrower.store.rental-requests.approve');

    Route::patch(
        '/dashboard/store/rental-requests/{rentalRequest}/reject',
        [StoreRentalRequestController::class, 'reject']
    )->name('borrower.store.rental-requests.reject');

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
