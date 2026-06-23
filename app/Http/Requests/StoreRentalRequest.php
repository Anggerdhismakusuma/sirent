<?php

namespace App\Http\Requests;

use App\Models\ProductAvailability;
use App\Models\RentalRequest;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class StoreRentalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'exists:products,id'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date'   => ['required', 'date', 'after_or_equal:start_date'],
            'notes'      => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required'    => 'Produk wajib dipilih.',
            'product_id.exists'      => 'Produk tidak ditemukan.',
            'start_date.required'    => 'Tanggal mulai wajib diisi.',
            'start_date.date'        => 'Format tanggal mulai tidak valid.',
            'start_date.after_or_equal' => 'Tanggal mulai tidak boleh sebelum hari ini.',
            'end_date.required'      => 'Tanggal selesai wajib diisi.',
            'end_date.date'          => 'Format tanggal selesai tidak valid.',
            'end_date.after_or_equal'=> 'Tanggal selesai harus setelah atau sama dengan tanggal mulai.',
            'notes.max'              => 'Catatan maksimal 500 karakter.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $data = $this->validated();

            if (empty($data['product_id']) || empty($data['start_date']) || empty($data['end_date'])) {
                return;
            }

            $start = Carbon::parse($data['start_date'])->startOfDay();
            $end   = Carbon::parse($data['end_date'])->startOfDay();

            // ── Business Rule: Tolak jika tanggal overlap dengan blocked_date ──
            $blockedExists = ProductAvailability::where('product_id', $data['product_id'])
                ->whereBetween('blocked_date', [$start->toDateString(), $end->toDateString()])
                ->exists();

            if ($blockedExists) {
                $validator->errors()->add('start_date', 'Tanggal yang dipilih tidak tersedia. Ada tanggal yang diblokir oleh pemilik produk.');
                return;
            }

            // ── Business Rule: Tolak jika tanggal overlap dengan rental_requests
            //    berstatus approved / ongoing untuk produk yang sama ──
            $overlapExists = RentalRequest::where('product_id', $data['product_id'])
                ->whereIn('status', [RentalRequest::STATUS_APPROVED, RentalRequest::STATUS_ONGOING])
                ->where(function ($q) use ($start, $end) {
                    // Overlap: existing.start_date <= new.end_date AND existing.end_date >= new.start_date
                    $q->where('start_date', '<=', $end->toDateString())
                      ->where('end_date', '>=', $start->toDateString());
                })
                ->exists();

            if ($overlapExists) {
                $validator->errors()->add('start_date', 'Tanggal yang dipilih bentrok dengan peminjaman lain yang sudah disetujui.');
            }
        });
    }
}
