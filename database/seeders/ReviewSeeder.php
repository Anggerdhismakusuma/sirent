<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReviewSeeder extends Seeder
{
    /**
     * Realistic Indonesian reviews grouped by category slug.
     */
    private array $reviews = [
        'kamera' => [
            'Kamera dalam kondisi sangat prima, hasil fotonya tajam. Lensa bersih tanpa jamur. Recommended!',
            'Fungsi autofocus-nya cepat dan akurat banget. Puas dengan hasil jepretan selama trip kemarin.',
            'Baterai masih awet, bisa seharian penuh buat shooting. Tidak ada kendala sama sekali.',
            'Kamera sesuai deskripsi, shutter count masih rendah. Pemilik ramah dan fast response.',
            'Paket sudah termasuk lensa kit, charger, dan tas. Sangat worth it buat yang butuh kamera dadakan.',
            'Agak lecet sedikit di body, tapi performa tetap oke. Harga sewa cukup terjangkau.',
            'Pinjam buat wedding kemarin, hasilnya luar biasa. Tamu pada nanya pinjem kamera dari mana.',
            'Kamera mirrorless ini ringan banget, enak dibawa traveling. APS-C tapi hasilnya mendekati full-frame.',
        ],
        'drone' => [
            'Drone terbang stabil meskipun kondisi angin cukup kencang. GPS lock-nya cepat.',
            'Kualitas video 4K-nya amazing! Udara pagi di gunung terekam dengan sangat baik.',
            'Baterai sesuai klaim, bisa terbang sekitar 40 menit. Bawa 2 baterai jadi bisa ganti-ganti.',
            'Fiturnya lengkap, obstacle sensing-nya bekerja dengan baik. Aman buat pemula seperti saya.',
            'Sayang sempat hujan gerimis, tapi drone tetap aman karena quick landing. Overall recommended.',
            'Hasil foto aerial pantai sangat memuaskan. Warna langsung oke tanpa perlu edit banyak.',
            'Drone compact, mudah dibawa di tas kamera. Cocok buat traveller yang nggak mau ribet.',
        ],
        'audio' => [
            'Suara yang dihasilkan jernih banget, noise floor rendah. Cocok buat recording podcast.',
            'Amplifier-nya warm khas tube, bikin dengerin musik jazz berasa di lounge mewah.',
            'Monitor speaker flat response, bagus buat mixing. Frekuensi low cukup nendang untuk ukuran near-field.',
            'Kabel dan konektor lengkap, tinggal colok langsung pakai. Owner kasih tutorial singkat cara setup.',
            'Pernah nyewa di tempat lain dapat yang dengung, ini bersih suaranya. Bakal jadi langganan!',
            'Mic wireless-nya clear, nggak ada latency berarti. Dipakai shooting outdoor tetap stabil sinyalnya.',
            'Unit dalam kondisi well-maintained. Terlihat pemilik paham cara merawat peralatan audio.',
        ],
        'alat-musik' => [
            'Gitar action-nya rendah, intonasi perfect. Betah main berjam-jam karena neck-nya nyaman.',
            'Drum elektrik sensivitasnya oke, cocok buat latihan di apartemen karena bisa pakai headphone.',
            'Kondisi senar masih baru, fret tidak ada yang aus. Jelas dirawat dengan baik oleh pemiliknya.',
            'Suara clean Stratocaster ini classic banget, posisi neck pickup creamy untuk blues.',
            'Module drum-nya banyak preset kit, tinggal pilih genre. Bluetooth buat play along juga helpful.',
            'Cocok buat recording di home studio. Noise dari pad minimal, di-mix tinggal EQ dikit.',
            'Kapok nyewa gitar di tempat lain, sering dapat yang fret buzz. Di sini semuanya prima!',
        ],
        'gaming' => [
            'PS5-nya mulus, controller tidak ada drift. Main Gran Turismo 7 berasa real banget!',
            'Nintendo Switch OLED layarnya tajam, beda jauh sama yang regular. Worth it buat long session.',
            'Sudah include 5 game populer, jadi nggak perlu beli lagi. Langsung main setibanya di rumah!',
            'Konsol di-reset bersih sebelum dikirim, storage kosong siap pakai. Profesional banget.',
            'Paket lengkap dengan docking dan controller tambahan. Bisa main multiplayer bareng keluarga.',
            'DualSense-nya masih haptic feedback normal, adaptive trigger juga responsif. Imersif!',
            'Anak-anak happy berat, jadi nggak rewel selama liburan. Nyewa lagi bulan depan!',
        ],
        'perlengkapan-mendaki' => [
            'Tenda tahan hujan deras semalaman di ketinggian, nggak ada rembesan sama sekali.',
            'Carrier nyaman dipakai trekking 10 jam, pinggang nggak sakit. Ventilasinya juara!',
            'Kompor mendidih cepat, gas irit. Pas dipakai di shelter, temen-temen pada iri.',
            'Sewa alat di sini selalu bersih dan wangi. Nggak ada bau apek seperti rental pada umumnya.',
            'Peralatan dalam kondisi layak pakai, sesuai untuk pendakian serius. Recommended!',
            'Tenda ringan banget, nggak nambah beban signifikan di carrier. Setup juga gampang.',
            'Sudah beberapa kali sewa di sini, kualitasnya konsisten. Owner sangat helpful.',
        ],
        'olahraga' => [
            'Raket padel karbonnya terasa solid, sweet spot lebar. Main jadi lebih percaya diri.',
            'Roadbike-nya ringan banget, shifting Shimano Ultegra mulus. Puas buat long ride.',
            'Sepeda di-deliver dalam kondisi bersih dan ter-tune. Seperti baru keluar dari toko!',
            'Raket grip-nya masih bagus, nggak licin meskipun keringatan. Tensi senar juga pas.',
            'Pinjam buat iven padel pertama, langsung menang. Pinjam lagi buat next tournament!',
            'Carbon frame-nya berasa responsif pas sprint. Disc brake juga pakem di turunan.',
        ],
        'videografi' => [
            'Gimbal stabil banget, footage smooth seperti pakai dolly. Calibration-nya gampang.',
            'Action cam tahan air sesuai klaim, dipakai snorkeling aman. Gambar tajam di bawah air.',
            'Gimbal payload-nya cukup buat mirrorless + lensa standar. Baterai tahan seharian shoot.',
            'GoPro-nya lengkap dengan mounting kit, bisa dipasang di helm, handlebar, atau dashboard.',
            'Stabilizer ini ringan, nggak bikin pegal meskipun dipakai handheld seharian.',
            'Hasil video cinematic banget, klien puas. Nggak perlu rental yang lebih mahal.',
        ],
    ];

    private array $reciprocalReviews = [
        'Penyewa sangat bertanggung jawab, barang dikembalikan dalam kondisi bersih dan rapi.',
        'Komunikasi lancar, tepat waktu saat pengambilan dan pengembalian. Recommended tenant!',
        'Penyewa ramah dan kooperatif. Barang dirawat dengan baik selama masa sewa.',
        'Proses sewa berjalan mulus, tidak ada komplain atau keterlambatan.',
        'Penyewa profesional, memperlakukan barang dengan hati-hati. Senang bertransaksi!',
        'Tepat waktu dan sesuai kesepakatan. Barang kembali tanpa kerusakan.',
    ];

    public function run(): void
    {
        // ── Clean up previous REVIEW_SEED data (idempotent re-run) ──
        $this->cleanOldSeedData();

        // ── Get all products with their owner and category ──
        $products = Product::with('owner', 'category')->get();

        // ── Borrower pool (non-owner users, IDs 7-16) ──
        $borrowerIds = User::where('role', User::ROLE_BORROWER)
            ->whereNotIn('id', [2, 3, 4, 5, 6])
            ->pluck('id')
            ->toArray();

        if (empty($borrowerIds)) {
            $borrowerIds = range(7, 16);
        }

        $now = now();
        $totalRatings = 0;

        foreach ($products as $product) {
            $categorySlug = $product->category->slug ?? 'kamera';
            $ownerId      = $product->owner_id;

            // ── How many reviews this product gets ──
            $rentedCount  = (int) $product->total_rented;
            $reviewCount  = match (true) {
                $rentedCount >= 100 => rand(6, 9),
                $rentedCount >= 50  => rand(4, 7),
                $rentedCount >= 20  => rand(3, 5),
                default             => rand(2, 4),
            };

            // ── Pick unique borrowers for this product ──
            shuffle($borrowerIds);
            $selectedBorrowers = array_slice($borrowerIds, 0, $reviewCount);

            foreach ($selectedBorrowers as $borrowerId) {
                // ── Create a completed rental request ──
                $startDate = $now->copy()
                    ->subMonths(rand(0, 5))
                    ->subDays(rand(3, 45));

                $totalDays  = rand(1, 7);
                $endDate    = $startDate->copy()->addDays($totalDays - 1);
                $totalPrice = (float) $product->price_per_day * $totalDays;

                $createdAt   = $startDate->copy()->subDays(rand(1, 5));
                $completedAt = $endDate->copy()->addHours(rand(2, 8));

                $rentalId = DB::table('rental_requests')->insertGetId([
                    'borrower_id'    => $borrowerId,
                    'product_id'     => $product->id,
                    'owner_id'       => $ownerId,
                    'start_date'     => $startDate->toDateString(),
                    'end_date'       => $endDate->toDateString(),
                    'total_days'     => $totalDays,
                    'quantity'       => 1,
                    'total_price'    => $totalPrice,
                    'status'         => 'completed',
                    'payment_status' => 'paid',
                    'notes'          => 'REVIEW_SEED',
                    'approved_at'    => $createdAt,
                    'completed_at'   => $completedAt,
                    'paid_at'        => $createdAt,
                    'created_at'     => $createdAt,
                    'updated_at'     => $completedAt,
                ]);

                // ── to_owner rating (borrower reviews owner/product) ──
                $score  = $this->weightedScore((float) $product->rating_avg);
                $review = $this->pickReview($categorySlug);

                DB::table('ratings')->insert([
                    'rental_request_id' => $rentalId,
                    'rater_id'          => $borrowerId,
                    'ratee_id'          => $ownerId,
                    'type'              => 'to_owner',
                    'score'             => $score,
                    'review'            => $review,
                    'created_at'        => $completedAt,
                    'updated_at'        => $completedAt,
                ]);
                $totalRatings++;

                // ── ~60% chance of reciprocal to_borrower rating ──
                if (rand(1, 100) <= 60) {
                    $reciprocalScore  = $this->weightedScore(4.2);
                    $reciprocalReview = $this->reciprocalReviews[array_rand($this->reciprocalReviews)];

                    DB::table('ratings')->insert([
                        'rental_request_id' => $rentalId,
                        'rater_id'          => $ownerId,
                        'ratee_id'          => $borrowerId,
                        'type'              => 'to_borrower',
                        'score'             => $reciprocalScore,
                        'review'            => $reciprocalReview,
                        'created_at'        => $completedAt,
                        'updated_at'        => $completedAt,
                    ]);
                    $totalRatings++;
                }
            }
        }

        // ── Recalculate product rating_avg (only from seed data for consistency) ──
        foreach ($products as $product) {
            $avg = DB::table('ratings')
                ->whereIn('rental_request_id', function ($q) use ($product) {
                    $q->select('id')
                        ->from('rental_requests')
                        ->where('product_id', $product->id);
                })
                ->where('type', 'to_owner')
                ->avg('score');

            DB::table('products')
                ->where('id', $product->id)
                ->update([
                    'rating_avg' => round($avg ?? 0, 2),
                    'updated_at' => now(),
                ]);
        }

        // ── Recalculate user rating_avg_as_owner ──
        $ownerIds = Product::distinct()->pluck('owner_id');
        foreach ($ownerIds as $ownerId) {
            $avg = DB::table('ratings')
                ->where('ratee_id', $ownerId)
                ->where('type', 'to_owner')
                ->avg('score');

            DB::table('users')
                ->where('id', $ownerId)
                ->update([
                    'rating_avg_as_owner' => round($avg ?? 0, 2),
                    'updated_at'          => now(),
                ]);
        }

        // ── Recalculate user rating_avg_as_borrower ──
        $borrowerIdsWithRatings = DB::table('ratings')
            ->where('type', 'to_borrower')
            ->distinct()
            ->pluck('ratee_id');

        foreach ($borrowerIdsWithRatings as $userId) {
            $avg = DB::table('ratings')
                ->where('ratee_id', $userId)
                ->where('type', 'to_borrower')
                ->avg('score');

            DB::table('users')
                ->where('id', $userId)
                ->update([
                    'rating_avg_as_borrower' => round($avg ?? 0, 2),
                    'updated_at'             => now(),
                ]);
        }

        $this->command?->info("ReviewSeeder: {$totalRatings} ratings seeded across {$products->count()} products.");
    }

    // ──────────────────────────────────────────────────
    //  Helpers
    // ──────────────────────────────────────────────────

    private function cleanOldSeedData(): void
    {
        DB::table('ratings')
            ->whereIn('rental_request_id', function ($query) {
                $query->select('id')
                    ->from('rental_requests')
                    ->where('notes', 'REVIEW_SEED');
            })
            ->delete();

        DB::table('rental_requests')
            ->where('notes', 'REVIEW_SEED')
            ->delete();
    }

    /**
     * Weighted-random score that tends toward the target average.
     */
    private function weightedScore(float $targetAvg): int
    {
        $pool = [];

        $weights = [
            5 => max(1, (int) round(($targetAvg - 3.0) * 20)),
            4 => 25,
            3 => 15,
            2 => 8,
            1 => 4,
        ];

        foreach ($weights as $score => $weight) {
            for ($i = 0; $i < max(1, $weight); $i++) {
                $pool[] = $score;
            }
        }

        return $pool[array_rand($pool)];
    }

    private function pickReview(string $categorySlug): string
    {
        $pool = $this->reviews[$categorySlug] ?? $this->reviews['kamera'];

        return $pool[array_rand($pool)];
    }
}
