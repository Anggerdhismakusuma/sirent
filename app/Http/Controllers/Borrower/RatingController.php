<?php

namespace App\Http\Controllers\Borrower;

use App\Http\Controllers\Controller;
use App\Http\Requests\RatingRequest;
use App\Models\Rating;
use App\Models\RentalRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class RatingController extends Controller
{
    /**
     * BERI RATING KE OWNER — POST /peminjaman/{id}/rating (PRD section 16.3)
     *
     * Business rules (PRD section 14):
     * - HANYA bisa jika rental_requests.status = 'completed'
     * - Belum pernah rating untuk kombinasi rental_request_id + rater_id + type ini
     *   (UNIQUE constraint di section 11.2 tabel ratings)
     * - Setelah rating tersimpan, recalculate rating_avg_as_owner pada user owner
     */
    public function storeForOwner(RatingRequest $request, string $id): JsonResponse
    {
        $rental = RentalRequest::with('product.owner')->findOrFail($id);

        // ── Pastikan user yang login adalah borrower dari rental ini ──
        if ($rental->borrower_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda bukan peminjam pada transaksi ini.',
            ], 403);
        }

        // ── Business Rule: hanya bisa rating jika status completed ──
        if ($rental->status !== RentalRequest::STATUS_COMPLETED) {
            return response()->json([
                'success' => false,
                'message' => 'Rating hanya bisa diberikan setelah peminjaman selesai (completed).',
            ], 422);
        }

        // ── Business Rule: satu rating per rental_request + rater + type ──
        $alreadyRated = Rating::where('rental_request_id', $rental->id)
            ->where('rater_id', auth()->id())
            ->where('type', Rating::TYPE_TO_OWNER)
            ->exists();

        if ($alreadyRated) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah memberikan rating untuk peminjaman ini.',
            ], 422);
        }

        $data = $request->validated();

        DB::transaction(function () use ($rental, $data) {
            // Simpan rating
            Rating::create([
                'rental_request_id' => $rental->id,
                'rater_id'          => auth()->id(),
                'ratee_id'          => $rental->owner_id,
                'type'              => Rating::TYPE_TO_OWNER,
                'score'             => $data['score'],
                'review'            => $data['review'] ?? null,
            ]);

            // ── Recalculate rating_avg_as_owner pada user owner ──
            $avg = Rating::where('ratee_id', $rental->owner_id)
                ->where('type', Rating::TYPE_TO_OWNER)
                ->avg('score');

            User::where('id', $rental->owner_id)->update([
                'rating_avg_as_owner' => round((float) $avg, 2),
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Rating berhasil disimpan.',
        ], 201);
    }
}
