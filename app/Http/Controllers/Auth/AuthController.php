<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
    /**
     * Handle AJAX registration from modal.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => User::ROLE_BORROWER,
        ]);

        event(new Registered($user));

        $user->sendEmailVerificationNotification();

        return response()->json([
            'success' => true,
            'message' => __('ui.register_success_verify'),
            'redirect' => route('home'),
        ]);
    }

    /**
     * Handle AJAX login from modal.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');

        // Check account status before attempting login
        $user = User::where('email', $request->email)->first();

        if ($user && $user->account_status === User::ACCOUNT_BANNED) {
            return response()->json([
                'success' => false,
                'message' => 'Akun Anda telah diblokir permanen. Hubungi admin untuk informasi lebih lanjut.',
            ], 403);
        }

        if ($user && $user->account_status === User::ACCOUNT_SUSPENDED) {
            if ($user->suspended_until && now()->lt($user->suspended_until)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akun Anda ditangguhkan hingga ' . $user->suspended_until->format('d M Y H:i') . '. Silakan coba lagi nanti.',
                ], 403);
            }
            // Lazy un-suspend
            $user->update(['account_status' => User::ACCOUNT_ACTIVE, 'suspended_until' => null]);
        }

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $user = Auth::user();

            return response()->json([
                'success' => true,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'avatar' => $user->avatar,
                ],
                'message' => 'Login successful.',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Email atau password salah.',
            'errors' => [
                'email' => ['Kredensial yang diberikan tidak cocok dengan data kami.'],
            ],
        ], 422);
    }

    /**
     * Handle logout — redirects to home for regular requests, JSON for AJAX.
     */
    public function logout(Request $request): JsonResponse|\Illuminate\Http\RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Logged out successfully.',
            ]);
        }

        return redirect()->route('home');
    }

    /**
     * Handle forgot password from modal sub-state.
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'success' => true,
                'message' => 'Tautan reset password telah dikirim ke email Anda.',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Gagal mengirim tautan reset. Pastikan email terdaftar.',
            'errors' => ['email' => ['Email tidak ditemukan dalam sistem kami.']],
        ], 422);
    }

    /**
     * Handle reset password (called from email link page, not modal).
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill(['password' => Hash::make($password)])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json([
                'success' => true,
                'message' => 'Password berhasil direset. Silakan login.',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Token reset tidak valid atau sudah kadaluarsa.',
        ], 422);
    }
}
