<?php

namespace App\Http\Controllers\Borrower;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    /**
     * Update theme & language preferences.
     * POST /dashboard/settings
     */
    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'theme' => ['nullable', 'string', 'in:light,dark'],
            'language' => ['nullable', 'string', 'in:id,en'],
        ]);

        $user = auth()->user();
        $updated = [];

        if (isset($validated['theme'])) {
            $user->theme = $validated['theme'];
            $updated[] = 'theme';
        }

        if (isset($validated['language'])) {
            $user->language = $validated['language'];
            $updated[] = 'language';
            // Set session locale untuk immediate effect
            session()->put('locale', $validated['language']);
            app()->setLocale($validated['language']);
        }

        if (!empty($updated)) {
            $user->save();
        }

        return response()->json([
            'success' => true,
            'message' => __('ui.settings_saved'),
            'data' => [
                'theme' => $user->theme,
                'language' => $user->language,
            ],
        ]);
    }
}
