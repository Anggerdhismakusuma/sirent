<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RentalRequestSeeder extends Seeder
{
    public function run(): void
    {
        $ownerId = 2; // Fuat Photography

        $products = [
            ['id' => 1,  'price' => 150000],
            ['id' => 2,  'price' => 175000],
            ['id' => 3,  'price' => 200000],
            ['id' => 15, 'price' => 90000],
            ['id' => 20, 'price' => 65000],
        ];

        $borrowerIds = [7, 8, 9, 10, 11, 12, 13, 14, 15, 16];

        // Hapus data dummy lama biar seed bisa diulang tanpa numpuk
        DB::table('ratings')
            ->whereIn('rental_request_id', function ($query) {
                $query->select('id')
                    ->from('rental_requests')
                    ->where('notes', 'LIKE', '%DUMMY_SEED%');
            })
            ->delete();

        DB::table('rental_requests')
            ->where('notes', 'LIKE', '%DUMMY_SEED%')
            ->delete();

        $statuses = [
            'completed', 'completed', 'completed', 'completed', 'completed',
            'completed', 'completed', 'completed', 'completed', 'completed',
            'ongoing', 'ongoing',
            'approved',
            'pending',
            'cancelled',
        ];

        $rentalRequestIds = [];

        foreach ($statuses as $index => $status) {
            $product = $products[$index % count($products)];
            $borrowerId = $borrowerIds[$index % count($borrowerIds)];

            $startDate = Carbon::now()
                ->subMonths(rand(0, 5))
                ->subDays(rand(1, 20));

            $totalDays = rand(1, 5);
            $endDate = $startDate->copy()->addDays($totalDays - 1);

            $totalPrice = $product['price'] * $totalDays;

            $createdAt = $startDate->copy()->subDays(rand(1, 7));
            $approvedAt = in_array($status, ['approved', 'ongoing', 'completed'])
                ? $createdAt->copy()->addHours(rand(2, 24))
                : null;

            $completedAt = $status === 'completed'
                ? $endDate->copy()->addHours(rand(3, 12))
                : null;

            $id = DB::table('rental_requests')->insertGetId([
                'borrower_id' => $borrowerId,
                'product_id' => $product['id'],
                'owner_id' => $ownerId,
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
                'total_days' => $totalDays,
                'total_price' => $totalPrice,
                'notes' => 'DUMMY_SEED - historical rental data',
                'rejection_reason' => null,
                'status' => $status,
                'approved_at' => $approvedAt,
                'completed_at' => $completedAt,
                'created_at' => $createdAt,
                'updated_at' => $completedAt ?? $approvedAt ?? $createdAt,
            ]);

            if ($status === 'completed') {
                $rentalRequestIds[] = [
                    'id' => $id,
                    'borrower_id' => $borrowerId,
                    'owner_id' => $ownerId,
                    'created_at' => $completedAt,
                ];
            }
        }

        // Seed rating untuk transaksi yang sudah completed
        foreach ($rentalRequestIds as $request) {
            DB::table('ratings')->insert([
                'rental_request_id' => $request['id'],
                'rater_id' => $request['borrower_id'],
                'ratee_id' => $request['owner_id'],
                'type' => 'to_owner',
                'score' => rand(4, 5),
                'review' => 'DUMMY_SEED - Good rental experience.',
                'created_at' => $request['created_at'],
                'updated_at' => $request['created_at'],
            ]);
        }

        // Update total_rented per product
        foreach ($products as $product) {
            $totalRented = DB::table('rental_requests')
                ->where('product_id', $product['id'])
                ->where('status', 'completed')
                ->count();

            DB::table('products')
                ->where('id', $product['id'])
                ->update([
                    'total_rented' => $totalRented,
                    'updated_at' => now(),
                ]);
        }

        // Update rating owner Fuat
        $ownerRating = DB::table('ratings')
            ->where('ratee_id', $ownerId)
            ->where('type', 'to_owner')
            ->avg('score');

        DB::table('users')
            ->where('id', $ownerId)
            ->update([
                'rating_avg_as_owner' => $ownerRating ?? 0,
                'updated_at' => now(),
            ]);
    }
}