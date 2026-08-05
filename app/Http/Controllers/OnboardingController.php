<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class OnboardingController extends Controller
{
    /**
     * Step 1: Personal Information (Render Halaman Utama)
     */
    public function step1(): View|RedirectResponse
    {
        $user = auth()->user();

        // Jika user sudah menyelesaikan onboarding sebelumnya (memiliki interests)
        if (! is_null($user->interests)) {
            return redirect()->route('home')->with('message', __('ui.onboarding_success'));
        }

        return view('onboarding.step1', compact('user'));
    }

    /**
     * Store Step 1: Name, Phone, DOB, Domicile, Gender (AJAX JSON)
     */
    public function storeStep1(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:20', 'regex:/^[0-9+\-\s]+$/'],
            'dob' => ['required', 'date', 'before:today'],
            'domicile' => ['required', 'string', 'max:100'],
            'gender' => ['required', 'in:male,female,other'], // Pastikan value HTML lowercase (male/female/other)
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi data pribadi gagal. Periksa kembali isian Anda.',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = auth()->user();
        $user->update($validator->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Data pribadi Step 1 berhasil disimpan.'
        ], 200);
    }

    /**
     * Store Step 2: Interests (AJAX JSON)
     */
    public function storeStep2(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'interests' => ['required', 'array', 'min:1'],
            'interests.*' => ['string', 'in:music,gaming,photography,sports,automotive,furniture,fashion,technology'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi minat gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        $user = auth()->user();
        $user->interests = $validated['interests'];
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Minat Step 2 berhasil disimpan.'
        ], 200);
    }

    /**
     * Store Step 3: Upload KTP + complete onboarding (AJAX JSON)
     */
    public function storeStep3(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'identity_doc' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi dokumen KTP gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = auth()->user();

        // Hapus KTP lama jika ada untuk menghemat storage
        if ($user->identity_doc) {
            Storage::disk('public')->delete($user->identity_doc);
        }

        // Simpan file ke folder storage/app/public/ktp
        $path = $request->file('identity_doc')->store('ktp', 'public');

        $user->identity_doc = $path;
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Onboarding berhasil diselesaikan! Data Anda sedang ditinjau.'
        ], 200);
    }

    /**
     * AJAX Method: Kirim Email Verifikasi secara manual saat tombol diklik.
     *
     * Menggunakan Mail::send() langsung seperti OTP — bukan notification system —
     * karena notification pipeline (MailChannel) bisa gagal secara silent
     * di production saat mengonversi MailMessage ke Mailable.
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
            // Generate signed verification URL — identik dengan yang dibuat oleh
            // VerifyEmail notification, tapi kita generate sendiri agar tidak
            // bergantung pada notification pipeline.
            $verificationUrl = URL::temporarySignedRoute(
                'verification.verify',
                now()->addMinutes(60),
                [
                    'id' => $user->getKey(),
                    'hash' => sha1($user->getEmailForVerification()),
                ]
            );

            // Kirim via Mail::send() — pendekatan yang sama persis dengan OTP
            Mail::send([], [], function ($message) use ($user, $verificationUrl) {
                $message->to($user->email)
                    ->subject('Verifikasi Email — SI-RENT')
                    ->html("
                        <div style='font-family: sans-serif; padding: 20px; max-width: 500px; border: 1px solid #ddd; border-radius: 12px;'>
                            <h2 style='color: #3673fb;'>Verifikasi Email SI-RENT</h2>
                            <p>Halo {$user->name}, klik tombol di bawah untuk memverifikasi alamat email Anda:</p>
                            <div style='text-align: center; margin: 30px 0;'>
                                <a href='{$verificationUrl}'
                                   style='background: #3673fb; color: #fff; padding: 14px 40px;
                                          text-decoration: none; border-radius: 8px; font-size: 16px;
                                          font-weight: bold; display: inline-block;'>
                                    Verifikasi Email
                                </a>
                            </div>
                            <p style='font-size: 12px; color: #777;'>
                                Tautan berlaku selama 60 menit. Jika Anda tidak membuat akun SI-RENT, abaikan email ini.
                            </p>
                        </div>
                    ");
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Tautan verifikasi berhasil dikirim ke email Anda!'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Gagal mengirim email verifikasi onboarding: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengirim email verifikasi. Silakan coba beberapa saat lagi.'
            ], 500);
        }
    }

    /**
     * AJAX Method: Memeriksa apakah status verifikasi EMAIL user saat ini sudah aktif
     */
    public function checkEmailStatus(): JsonResponse
    {
        $user = auth()->user();
        
        return response()->json([
            'verified' => $user->hasVerifiedEmail()
        ], 200);
    }

}