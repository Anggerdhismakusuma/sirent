<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FollowSeeder extends Seeder
{
    public function run(): void
    {
        // Owners (users with active store)
        $ownerIds = User::where('is_owner_active', true)->pluck('id')->toArray();

        // Borrowers (non-owner users)
        $borrowerIds = User::where('role', User::ROLE_BORROWER)
            ->whereNotIn('id', $ownerIds)
            ->pluck('id')
            ->toArray();

        if (empty($borrowerIds)) {
            $borrowerIds = range(7, 16);
        }

        // Clean old seed data for idempotent re-runs
        DB::table('follows')->delete();

        $inserts = [];

        foreach ($ownerIds as $ownerId) {
            // Each owner gets 40-90% of borrowers as followers
            shuffle($borrowerIds);
            $followerCount = rand(
                (int) (count($borrowerIds) * 0.4),
                (int) (count($borrowerIds) * 0.9)
            );
            $selectedFollowers = array_slice($borrowerIds, 0, $followerCount);

            $now = now();
            foreach ($selectedFollowers as $followerId) {
                $inserts[] = [
                    'follower_id' => $followerId,
                    'followee_id' => $ownerId,
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ];
            }

            // Some borrowers also follow each other (cross-follow)
            if (rand(1, 100) <= 30) {
                $otherOwner = $ownerIds[array_rand($ownerIds)];
                if ($otherOwner !== $ownerId) {
                    // Some owners follow other owners
                }
            }
        }

        // Also: some owners follow other owners (cross-pollination)
        foreach ($ownerIds as $ownerId) {
            foreach ($ownerIds as $otherOwnerId) {
                if ($ownerId === $otherOwnerId) continue;
                if (rand(1, 100) <= 25) {
                    $inserts[] = [
                        'follower_id' => $ownerId,
                        'followee_id' => $otherOwnerId,
                        'created_at'  => now(),
                        'updated_at'  => now(),
                    ];
                }
            }
        }

        // Batch insert
        foreach (array_chunk($inserts, 100) as $chunk) {
            DB::table('follows')->insert($chunk);
        }

        $this->command?->info('FollowSeeder: ' . count($inserts) . ' follow relationships seeded.');
    }
}
