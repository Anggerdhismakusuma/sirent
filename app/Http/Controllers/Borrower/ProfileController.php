<?php

namespace App\Http\Controllers\Borrower;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Update profile text fields (name, email, phone, dob, domicile, gender, bio).
     * POST /dashboard/profile/info
     */
    public function updateInfo(Request $request): JsonResponse
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name'     => ['required', 'string', 'max:100'],
            'email'    => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'phone'    => ['required', 'string', 'max:20', 'regex:/^[0-9+\-\s]+$/'],
            'dob'      => ['nullable', 'date', 'before:today'],
            'domicile' => ['nullable', 'string', 'max:100'],
            'gender'   => ['nullable', 'string', 'in:male,female,other'],
            'bio'      => ['nullable', 'string', 'max:500'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        // If email changed, require re-verification
        if (isset($validated['email']) && $validated['email'] !== $user->email) {
            $user->email_verified_at = null;
        }

        // If phone changed, reset WhatsApp verification
        if (isset($validated['phone']) && $validated['phone'] !== $user->phone) {
            $user->whatsapp_verified_at = null;
        }

        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => __('ui.profile_updated'),
            'data'    => [
                'name'            => $user->name,
                'email'           => $user->email,
                'phone'           => $user->phone,
                'dob'             => $user->dob,
                'domicile'        => $user->domicile,
                'gender'          => $user->gender,
                'bio'             => $user->bio,
                'email_verified'  => $user->hasVerifiedEmail(),
                'whatsapp_verified' => ! is_null($user->whatsapp_verified_at),
            ],
        ]);
    }

    /**
     * Update profile banner / cover image.
     * POST /dashboard/profile/banner
     */
    public function updateBanner(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'banner' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        $user = $request->user();

        // Delete old banner
        if ($user->banner) {
            Storage::disk('public')->delete($user->banner);
        }

        $path = $request->file('banner')->store('banners', 'public');
        $user->update(['banner' => $path]);

        return response()->json([
            'success' => true,
            'message' => __('ui.banner_updated'),
            'data'    => ['banner' => $path],
        ]);
    }

    /**
     * Send email verification notification from the dashboard profile tab.
     * POST /dashboard/profile/send-email-verification
     */
    public function sendEmailVerification(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'success' => true,
                'status'  => 'already_verified',
                'message' => __('ui.email_already_verified'),
            ]);
        }

        try {
            $user->sendEmailVerificationNotification();

            return response()->json([
                'success' => true,
                'status'  => 'sent',
                'message' => __('ui.verify_email_sent'),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send verification email from dashboard: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'status'  => 'error',
                'message' => __('ui.verify_email_failed'),
            ], 500);
        }
    }

    /**
     * Update profile avatar.
     * POST /dashboard/profile/avatar
     */
    public function updateAvatar(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'avatar' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        $user = $request->user();

        // Delete old avatar
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $path = $request->file('avatar')->store('avatars', 'public');
        $user->update(['avatar' => $path]);

        return response()->json([
            'success' => true,
            'message' => __('ui.avatar_updated'),
            'data'    => ['avatar' => $path],
        ]);
    }
}
