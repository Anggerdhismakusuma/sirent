<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\URL;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
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

        // Jika user sudah menyelesaikan onboarding sebelumnya
        if ($user->verification_status !== User::VERIFICATION_UNVERIFIED) {
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
        $user->verification_status = User::VERIFICATION_PENDING;
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Onboarding berhasil diselesaikan! Data Anda sedang ditinjau.'
        ], 200);
    }

    /**
     * AJAX Method: Kirim Email Verifikasi secara manual saat tombol diklik
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
            // Memicu pengiriman notification email bawaan Laravel (MustVerifyEmail)
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
     * AJAX Method: Memeriksa apakah status verifikasi EMAIL user saat ini sudah aktif
     */
    public function checkEmailStatus(): JsonResponse
    {
        $user = auth()->user();
        
        return response()->json([
            'verified' => $user->hasVerifiedEmail()
        ], 200);
    }

    /**
     * AJAX Method: Proses request pembuatan dan pengiriman WhatsApp Magic Link
     */
    public function verifyWhatsApp(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'phone' => ['required', 'string', 'max:20', 'regex:/^[0-9+\-\s]+$/'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Format nomor WhatsApp tidak valid.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = auth()->user();
            $phone = $request->phone;

            // Simpan nomor HP ke database user terlebih dahulu
            $user->update(['phone' => $phone]);

            // Generate Temporary Signed URL (berlaku hanya 15 menit demi keamanan)
            $magicLink = URL::temporarySignedRoute(
                'onboarding.verify-whatsapp-link', // Nama route penampung klik
                now()->addMinutes(15),
                ['user' => $user->id]
            );

            // Catat di log agar kita bisa ambil link-nya di localhost tanpa gateway asli
            Log::info("WhatsApp Magic Link untuk {$user->name} ({$phone}): " . $magicLink);

            return response()->json([
                'status' => 'success',
                'message' => 'Magic link verifikasi berhasil dikirim ke WhatsApp Anda.',
                'debug_link' => $magicLink // Kirim balik ke frontend agar gampang di-copy saat dev
            ], 200);
        } catch (\Exception $e) {
            Log::error('WhatsApp Magic Link Error: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memproses pembuatan Magic Link WhatsApp.'
            ], 500);
        }
    }

    /**
     * Web Route Method: Dipicu saat user mengklik Magic Link dari WhatsApp
     */
    public function verifyWhatsAppLink(Request $request, User $user)
    {
        // Proteksi bawaan Laravel untuk mengecek validitas tanda tangan URL & masa kadaluwarsa
        if (! $request->hasValidSignature()) {
            abort(401, 'Link verifikasi kadaluwarsa atau tidak valid.');
        }

        // Ubah status WhatsApp menjadi terverifikasi penuh
        $user->update([
            'verification_status' => User::VERIFICATION_VERIFIED
        ]);

        // Tampilkan halaman sukses statis yang rapi di tab baru/HP user setelah link diklik
        return "
            <div style='text-align: center; margin-top: 80px; font-family: \"Segoe UI\", Tahoma, Geneva, Verdana, sans-serif; color: #333;'>
                <div style='max-width: 500px; margin: 0 auto; padding: 40px; border: 1px solid #e4e4e4; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05);'>
                    <h2 style='color: #28a745; margin-bottom: 10px;'>✓ WhatsApp Berhasil Diverifikasi</h2>
                    <p style='color: #666; font-size: 15px; line-height: 1.6; margin-bottom: 25px;'>
                        Nomor WhatsApp Anda telah sukses terverifikasi di sistem. Silakan kembali ke layar utama Anda, halaman onboarding akan otomatis melanjutkan proses.
                    </p>
                </div>
            </div>
        ";
    }

    /**
     * AJAX Method: Memeriksa apakah status verifikasi WHATSAPP user saat ini sudah berubah (diakses dari polling frontend)
     */
    public function checkWhatsAppStatus(): JsonResponse
    {
        $user = auth()->user();
        
        return response()->json([
            'verified' => $user->verification_status === User::VERIFICATION_VERIFIED
        ], 200);
    }
}