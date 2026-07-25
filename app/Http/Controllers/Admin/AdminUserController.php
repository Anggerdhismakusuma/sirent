<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
    public function updateStatus(
        Request $request,
        User $user
    ): RedirectResponse {
        $validated = $request->validate([
            'status' => [
                'required',
                Rule::in([
                    User::ACCOUNT_ACTIVE,
                    User::ACCOUNT_SUSPENDED,
                ]),
            ],
        ]);

        // Admin tidak boleh menonaktifkan akun sendiri.
        if ($request->user()->is($user)) {
            return back()->withErrors([
                'status' => 'Anda tidak dapat mengubah status akun sendiri.',
            ]);
        }

        // Proteksi agar admin lain tidak disuspend.
        if ($user->role === User::ROLE_ADMIN) {
            return back()->withErrors([
                'status' => 'Akun administrator tidak dapat disuspend.',
            ]);
        }

        $user->account_status = $validated['status'];
        $user->save();

        /*
         * Jika SESSION_DRIVER=database, hapus semua session user
         * agar user yang sedang login langsung terputus.
         */
        if (
            $validated['status'] === User::ACCOUNT_SUSPENDED
            && config('session.driver') === 'database'
        ) {
            DB::table(config('session.table', 'sessions'))
                ->where('user_id', $user->getKey())
                ->delete();
        }

        $message = $validated['status'] === User::ACCOUNT_SUSPENDED
            ? "{$user->name} berhasil disuspend."
            : "{$user->name} berhasil diaktifkan kembali.";

        return back()->with('success', $message);
    }
}