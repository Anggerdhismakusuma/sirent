<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class OnboardingController extends Controller
{
    /**
     * Step 1: Personal Information
     */
    public function step1(): View|RedirectResponse
    {
        $user = auth()->user();

        // Already completed onboarding
        if ($user->verification_status !== User::VERIFICATION_UNVERIFIED) {
            return redirect()->route('home')->with('message', __('ui.onboarding_success'));
        }

        return view('onboarding.step1', compact('user'));
    }

    /**
     * Store Step 1: Name, Phone, DOB, Domicile, Gender
     */
    public function storeStep1(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:20', 'regex:/^[0-9+\-\s]+$/'],
            'dob' => ['required', 'date', 'before:today'],
            'domicile' => ['required', 'string', 'max:100'],
            'gender' => ['required', 'in:male,female,other'],
        ]);

        $user = auth()->user();
        $user->update($validated);

        return redirect()->route('onboarding.step1', ['step' => 2]);
    }

    /**
     * Store Step 2: Interests (JSON array)
     */
    public function storeStep2(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'interests' => ['required', 'array', 'min:1'],
            'interests.*' => ['string', 'in:music,gaming,photography,sports,automotive,furniture,fashion,technology'],
        ]);

        $user = auth()->user();
        $user->interests = $validated['interests'];
        $user->save();

        return redirect()->route('onboarding.step1', ['step' => 3]);
    }

    /**
     * Store Step 3: Upload KTP + complete onboarding
     */
    public function storeStep3(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'identity_doc' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
        ]);

        $user = auth()->user();

        // Delete old KTP if exists
        if ($user->identity_doc) {
            Storage::disk('public')->delete($user->identity_doc);
        }

        $path = $request->file('identity_doc')->store('ktp', 'public');

        $user->identity_doc = $path;
        $user->verification_status = User::VERIFICATION_PENDING;
        $user->save();

        return redirect()->route('home')->with('message', __('ui.onboarding_success'));
    }

    /**
     * AJAX Method: Kirim Email Verifikasi secara manual saat tombol di klik
     */
    public function sendVerificationEmail(Request $request): JsonResponse
    {
        $user = $request->user();

        // Jika email ternyata sudah diverifikasi sebelumnya
        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'status' => 'already_verified',
                'message' => 'Email Anda sudah terverifikasi.'
            ], 200);
        }

        try {
            // Memicu pengiriman notification email bawaan dari system `MustVerifyEmail` Laravel
            $user->sendEmailVerificationNotification();

            return response()->json([
                'status' => 'success',
                'message' => 'Tautan verifikasi berhasil dikirim ke email Anda!'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Gagal mengirim email verifikasi onboarding: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengirim email verifikasi. Silakan coba beberapa saat lagi.'
            ], 500);
        }
    }

    /**
     * AJAX Method: Proses request verifikasi WhatsApp OTP
     */
    public function verifyWhatsApp(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => ['required', 'string', 'max:20', 'regex:/^[0-9+\-\s]+$/'],
        ]);

        try {
            $phone = $request->phone;
            
            // Generate 6 digit angka OTP acak
            $otp = rand(100000, 999999);
            
            // Simpan kode OTP ke dalam session server (berlaku sementara)
            session(['whatsapp_otp' => $otp, 'whatsapp_phone' => $phone]);

            // TODO: Integrasikan pengiriman pesan dengan gateway WhatsApp pilihanmu disini.
            // Contoh payload log:
            Log::info("Kirim OTP WhatsApp ke {$phone}: Kode OTP kamu adalah {$otp}");

            return response()->json([
                'status' => 'success',
                'message' => 'Kode OTP berhasil dikirimkan ke WhatsApp Anda.'
            ], 200);

        } catch (\Exception $e) {
            Log::error('WhatsApp OTP Error: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memproses pengiriman OTP WhatsApp.'
            ], 500);
        }
    }
}<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class OnboardingController extends Controller
{
    /**
     * Step 1: Personal Information (Render Halaman Utama)
     */
    public function step1(): View|RedirectResponse
    {
        $user = auth()->user();

        // Already completed onboarding
        if ($user->verification_status !== User::VERIFICATION_UNVERIFIED) {
            return redirect()->route('home')->with('message', __('ui.onboarding_success'));
        }

        return view('onboarding.step1', compact('user'));
    }

    /**
     * Store Step 1: Name, Phone, DOB, Domicile, Gender
     */
    public function storeStep1(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:20', 'regex:/^[0-9+\-\s]+$/'],
            'dob' => ['required', 'date', 'before:today'],
            'domicile' => ['required', 'string', 'max:100'],
            'gender' => ['required', 'in:male,female,other'],
        ]);

        $user = auth()->user();
        $user->update($validated);

        // DIUBAH: Mengembalikan JSON agar ditangkap oleh Alpine.js submitStep()
        return response()->json([
            'status' => 'success',
            'message' => 'Data pribadi Step 1 berhasil disimpan.'
        ], 200);
    }

    /**
     * Store Step 2: Interests (JSON array)
     */
    public function storeStep2(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'interests' => ['required', 'array', 'min:1'],
            'interests.*' => ['string', 'in:music,gaming,photography,sports,automotive,furniture,fashion,technology'],
        ]);

        $user = auth()->user();
        $user->interests = $validated['interests'];
        $user->save();

        // DIUBAH: Mengembalikan JSON agar ditangkap oleh Alpine.js submitStep()
        return response()->json([
            'status' => 'success',
            'message' => 'Interests Step 2 berhasil disimpan.'
        ], 200);
    }

    /**
     * Store Step 3: Upload KTP + complete onboarding
     */
    public function storeStep3(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'identity_doc' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
        ]);

        $user = auth()->user();

        // Delete old KTP if exists
        if ($user->identity_doc) {
            Storage::disk('public')->delete($user->identity_doc);
        }

        $path = $request->file('identity_doc')->store('ktp', 'public');

        $user->identity_doc = $path;
        $user->verification_status = User::VERIFICATION_PENDING;
        $user->save();

        // DIUBAH: Mengembalikan JSON agar JavaScript tahu proses onboarding telah selesai sepenuhnya
        return response()->json([
            'status' => 'success',
            'message' => 'Onboarding berhasil diselesaikan!'
        ], 200);
    }

    /**
     * AJAX Method: Kirim Email Verifikasi secara manual saat tombol di klik
     */
    public function sendVerificationEmail(Request $request): JsonResponse
    {
        $user = $request->user();

        // Jika email ternyata sudah diverifikasi sebelumnya
        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'status' => 'already_verified',
                'message' => 'Email Anda sudah terverifikasi.'
            ], 200);
        }

        try {
            // Memicu pengiriman notification email bawaan dari system `MustVerifyEmail` Laravel
            $user->sendEmailVerificationNotification();

            return response()->json([
                'status' => 'success',
                'message' => 'Tautan verifikasi berhasil dikirim ke email Anda!'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Gagal mengirim email verifikasi onboarding: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengirim email verifikasi. Silakan coba beberapa saat lagi.'
            ], 500);
        }
    }

    /**
     * AJAX Method: Proses request verifikasi WhatsApp OTP
     */
    public function verifyWhatsApp(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => ['required', 'string', 'max:20', 'regex:/^[0-9+\-\s]+$/'],
        ]);

        try {
            $phone = $request->phone;
            
            // Generate 6 digit angka OTP acak
            $otp = rand(100000, 999999);
            
            // Simpan kode OTP ke dalam session server (berlaku sementara)
            session(['whatsapp_otp' => $otp, 'whatsapp_phone' => $phone]);

            // TODO: Integrasikan pengiriman pesan dengan gateway WhatsApp pilihanmu disini.
            Log::info("Kirim OTP WhatsApp ke {$phone}: Kode OTP kamu adalah {$otp}");

            return response()->json([
                'status' => 'success',
                'message' => 'Kode OTP berhasil dikirimkan ke WhatsApp Anda.'
            ], 200);

        } catch (\Exception $e) {
            Log::error('WhatsApp OTP Error: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memproses pengiriman OTP WhatsApp.'
            ], 500);
        }
    }
}