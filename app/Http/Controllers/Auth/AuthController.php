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
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    /**
     * Handle AJAX registration from modal — Step 1: Save data to session & Send OTP.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        // 1. Generate 6 digit angka OTP secara acak
        $otp = rand(100000, 999999);

        // 2. Simpan data registrasi sementara & OTP ke dalam Session
        session([
            'register_data' => [
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
            ],
            'register_otp' => $otp,
            'otp_expires_at' => now()->addMinutes(10) // Expired dalam 10 menit
        ]);

        // 3. Kirim email OTP ke Mailpit
        Mail::send([], [], function ($message) use ($request, $otp) {
            $message->to($request->email)
                ->subject('Kode Verifikasi Registrasi SI-RENT')
                ->html("
                        <div style='font-family: sans-serif; padding: 20px; max-width: 500px; border: 1px solid #ddd; border-radius: 12px;'>
                            <h2 style='color: #3673fb;'>Verifikasi Akun SI-RENT</h2>
                            <p>Halo, terima kasih telah mendaftar di SI-RENT. Gunakan kode OTP di bawah ini untuk menyelesaikan pendaftaran Anda:</p>
                            <div style='background: #f0f4ff; padding: 15px; text-align: center; border-radius: 8px; margin: 20px 0;'>
                                <span style='font-size: 32px; font-weight: bold; letter-spacing: 5px; color: #3673fb;'>{$otp}</span>
                            </div>
                            <p style='font-size: 12px; color: #777;'>Kode ini berlaku selama 10 menit. Jangan sebarkan kode ini kepada siapa pun.</p>
                        </div>
                    ");
        });

        // 4. Return response sukses, kirim balik email-nya untuk di-render di frontend
        return response()->json([
            'success' => true,
            'message' => 'Kode OTP berhasil dikirim ke email Anda.',
            'email' => $request->email,
        ]);
    }

    /**
     * Handle AJAX OTP verification — Step 2: Validate OTP & Create Account.
     */
    public function verifyOtp(Request $request): JsonResponse
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        // 1. Cek apakah session data registrasi ada dan OTP belum expired
        if (!session()->has('register_otp') || now()->gt(session('otp_expires_at'))) {
            return response()->json([
                'success' => false,
                'message' => 'Kode OTP sudah kedaluwarsa atau tidak valid. Silakan daftar ulang.',
            ], 422);
        }

        // 2. Cocokkan OTP yang diinput dengan yang ada di Session
        if ($request->otp !== (string) session('register_otp')) {
            return response()->json([
                'success' => false,
                'message' => 'Kode OTP yang Anda masukkan salah.',
                'errors' => [
                    'otp' => ['Kode OTP tidak cocok.']
                ]
            ], 422);
        }

        // 3. Jika cocok, ambil data sementara tadi dan simpan resmi ke Database
        $userData = session('register_data');
        $user = User::create([
            'name' => $userData['name'],
            'email' => $userData['email'],
            'phone' => $userData['phone'],
            'password' => $userData['password'],
            'role' => User::ROLE_BORROWER, // Default sebagai penyewa
            'email_verified_at' => now(), // Langsung tandai terverifikasi karena via OTP
        ]);

        // Memicu event registered bawaan laravel (opsional)
        event(new Registered($user));

        // 4. Hapus session data sementara
        session()->forget(['register_data', 'register_otp', 'otp_expires_at']);

        // 5. Login-kan user secara otomatis ke sistem
        Auth::login($user);
        $request->session()->regenerate();

        return response()->json([
            'success' => true,
            'message' => 'Akun Anda berhasil diverifikasi!',
            'redirect' => route('onboarding.step1'),
        ]);
    }

    /**
     * Handle AJAX login from modal.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if (!Auth::attempt($credentials, $remember)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah.',
                'errors' => [
                    'email' => [
                        'Kredensial yang diberikan tidak cocok dengan data kami.'
                    ],
                ],
            ], 422);
        }
        /** @var User $user */
        $user = Auth::user();
        
        /*
        * User berhasil memasukkan password yang benar,
        * tetapi akun sedang disuspend.
        */
        if ($user->account_status === User::ACCOUNT_SUSPENDED) {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return response()->json([
                'success' => false,
                'code' => 'ACCOUNT_SUSPENDED',
                'message' => 'Akun Anda sedang disuspend oleh administrator SI-RENT. '
                    . 'Anda tidak dapat melakukan pemesanan atau menggunakan fitur akun '
                    . 'sampai akun diaktifkan kembali.',
            ], 403);
        }

        $request->session()->regenerate();

        // Tentukan halaman tujuan berdasarkan role
        if ($user->role === User::ROLE_ADMIN) {
            $redirect = route('admin.dashboard');
        } elseif (!$user->hasVerifiedEmail()) {
            $redirect = route('onboarding.step1');
        } else {
            $redirect = route('borrower.dashboard');
        }

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => $user->avatar,
                'role' => $user->role,
            ],
            'message' => 'Login successful.',
            'redirect' => $redirect,
        ]);
    }

    /**
     * Handle logout — redirects to home for regular requests, JSON for AJAX.
     */
    public function logout(Request $request): JsonResponse|\Illuminate\Http\RedirectResponse
    {
        $this->guard()->logout();

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

    /**
     * Get the guard to be used during authentication.
     */
    protected function guard()
    {
        return Auth::guard('web');
    }
}
