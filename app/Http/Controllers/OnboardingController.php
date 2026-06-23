<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
}
