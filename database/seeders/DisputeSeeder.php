<?php

namespace Database\Seeders;

use App\Models\RentalRequest;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class DisputeSeeder extends Seeder
{
    public function run(): void
    {
        /*
         * Cari admin yang akan menjadi handler untuk dispute
         * berstatus in_review, resolved, dan rejected.
         */
        $adminId = User::query()
            ->where('role', 'admin')
            ->value('id');

        /*
         * Prioritaskan transaksi yang sudah disetujui atau berjalan.
         */
        $rentalRequests = RentalRequest::query()
            ->whereIn('status', [
                'approved',
                'ongoing',
                'completed',
                'rejected',
            ])
            ->latest('id')
            ->limit(8)
            ->get([
                'id',
                'borrower_id',
                'owner_id',
            ]);

        /*
         * Fallback untuk development apabila belum ada rental
         * dengan status di atas.
         */
        if ($rentalRequests->isEmpty()) {
            $rentalRequests = RentalRequest::query()
                ->latest('id')
                ->limit(8)
                ->get([
                    'id',
                    'borrower_id',
                    'owner_id',
                ]);
        }

        if ($rentalRequests->isEmpty()) {
            throw new RuntimeException(
                'DisputeSeeder membutuhkan minimal satu rental request.'
            );
        }

        $templates = [
            [
                'reporter_type' => 'borrower',
                'reason' => 'Barang yang diterima memiliki goresan pada lensa dan kondisinya tidak sesuai dengan deskripsi produk.',
                'status' => 'open',
                'resolution' => null,
                'days_ago' => 12,
            ],
            [
                'reporter_type' => 'owner',
                'reason' => 'Penyewa belum mengembalikan barang meskipun periode penyewaan telah berakhir.',
                'status' => 'open',
                'resolution' => null,
                'days_ago' => 10,
            ],
            [
                'reporter_type' => 'borrower',
                'reason' => 'Pemilik toko tidak hadir pada waktu penyerahan barang yang telah disepakati.',
                'status' => 'open',
                'resolution' => null,
                'days_ago' => 8,
            ],
            [
                'reporter_type' => 'owner',
                'reason' => 'Charger dan kabel bawaan produk tidak dikembalikan bersama dengan barang utama.',
                'status' => 'in_review',
                'resolution' => null,
                'days_ago' => 6,
            ],
            [
                'reporter_type' => 'borrower',
                'reason' => 'Produk beberapa kali mati saat digunakan dan tidak dapat dipakai sesuai kebutuhan penyewaan.',
                'status' => 'in_review',
                'resolution' => null,
                'days_ago' => 4,
            ],
            [
                'reporter_type' => 'owner',
                'reason' => 'Barang dikembalikan dalam kondisi rusak dan terdapat kerusakan baru pada bagian bodi.',
                'status' => 'resolved',
                'resolution' => 'Klaim pemilik toko diterima. Penyewa diwajibkan membayar biaya perbaikan sesuai bukti servis.',
                'days_ago' => 15,
            ],
            [
                'reporter_type' => 'borrower',
                'reason' => 'Penyewa mengajukan pengembalian dana karena merasa barang tidak bekerja secara optimal.',
                'status' => 'rejected',
                'resolution' => 'Klaim ditolak karena hasil pemeriksaan menunjukkan barang berfungsi normal saat diserahkan.',
                'days_ago' => 14,
            ],
            [
                'reporter_type' => 'owner',
                'reason' => 'Penyewa mengembalikan produk dalam keadaan kotor dan membutuhkan biaya pembersihan tambahan.',
                'status' => 'resolved',
                'resolution' => 'Kedua pihak menyetujui pemotongan sebagian deposit untuk biaya pembersihan.',
                'days_ago' => 11,
            ],
        ];

        DB::transaction(function () use (
            $templates,
            $rentalRequests,
            $adminId
        ): void {
            foreach ($templates as $index => $template) {
                /*
                 * Satu dispute untuk satu rental request.
                 * Kalau rental request kurang dari jumlah template,
                 * loop berhenti secara aman.
                 */
                $rentalRequest = $rentalRequests->get($index);

                if (!$rentalRequest) {
                    break;
                }

                $reporterId = $template['reporter_type'] === 'owner'
                    ? $rentalRequest->owner_id
                    : $rentalRequest->borrower_id;

                $status = $template['status'];

                $isBeingHandled = in_array(
                    $status,
                    [
                        'in_review',
                        'resolved',
                        'rejected',
                    ],
                    true
                );

                $isFinished = in_array(
                    $status,
                    [
                        'resolved',
                        'rejected',
                    ],
                    true
                );

                /*
                 * Kalau admin belum tersedia, jangan membuat record
                 * selesai dengan handled_by yang invalid.
                 */
                if ($isBeingHandled && !$adminId) {
                    $status = 'open';
                    $isBeingHandled = false;
                    $isFinished = false;
                }

                $createdAt = now()
                    ->subDays($template['days_ago'])
                    ->subHours($index);

                DB::table('disputes')->updateOrInsert(
                    [
                        'rental_request_id' => $rentalRequest->id,
                        'reporter_id' => $reporterId,
                        'reason' => $template['reason'],
                    ],
                    [
                        'evidence' => null,
                        'status' => $status,

                        'resolution' => $isFinished
                            ? $template['resolution']
                            : null,

                        'handled_by' => $isBeingHandled
                            ? $adminId
                            : null,

                        'resolved_at' => $isFinished
                            ? $createdAt->copy()->addDays(2)
                            : null,

                        'created_at' => $createdAt,
                        'updated_at' => now(),
                    ]
                );
            }
        });
    }
}