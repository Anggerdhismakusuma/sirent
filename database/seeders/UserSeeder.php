<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::create([
            'name' => 'Admin SI-RENT',
            'email' => 'admin@sirent.id',
            'password' => Hash::make('password'),
            'phone' => '081111111111',
            'role' => User::ROLE_ADMIN,
        ]);

        // Owners (is_owner_active = true, verified)
        $owners = [
            [
                'name' => 'Fuat Photography',
                'email' => 'fuat@sirent.id',
                'phone' => '081222222221',
                'bio' => 'Premium photography, videography, and outdoor gear rental. Trusted equipment for creators, travelers, and professionals.',
                'avatar' => 'avatars/owner1.jpg',
            ],
            [
                'name' => 'SoundMntp Shop',
                'email' => 'soundmntp@sirent.id',
                'phone' => '081222222222',
                'bio' => 'Toko audio berkualitas. Menyediakan berbagai perlengkapan audio untuk event dan recording.',
                'avatar' => 'avatars/owner2.jpg',
            ],
            [
                'name' => 'Gio Games',
                'email' => 'gio@sirent.id',
                'phone' => '081222222223',
                'bio' => 'Rental konsol game dan aksesoris gaming terlengkap di Jakarta Pusat.',
                'avatar' => 'avatars/owner3.jpg',
            ],
            [
                'name' => 'Tony Tech',
                'email' => 'tony@sirent.id',
                'phone' => '081222222224',
                'bio' => 'Drone dan gadget terbaru untuk kebutuhan aerial photography dan videography.',
                'avatar' => 'avatars/owner4.jpg',
            ],
            [
                'name' => 'Camping Story',
                'email' => 'camping@sirent.id',
                'phone' => '081222222225',
                'bio' => 'Perlengkapan mendaki dan camping berkualitas. Kami mendukung petualanganmu!',
                'avatar' => 'avatars/owner5.jpg',
            ],
        ];

        foreach ($owners as $owner) {
            User::create(array_merge($owner, [
                'password' => Hash::make('password'),
                'role' => User::ROLE_BORROWER,
                'is_owner_active' => true,
                'verification_status' => User::VERIFICATION_VERIFIED,
                'rating_avg_as_owner' => fake()->randomFloat(2, 3.5, 5.0),
                'rating_avg_as_borrower' => fake()->randomFloat(2, 3.5, 5.0),
            ]));
        }

        // Borrowers (regular users)
        $borrowers = [
            ['name' => 'Kent Nathanael', 'email' => 'kent@sirent.id', 'phone' => '081333333331'],
            ['name' => 'Kus Kus', 'email' => 'kuskus@sirent.id', 'phone' => '081333333332'],
            ['name' => 'Albert Epstein', 'email' => 'albert@sirent.id', 'phone' => '081333333333'],
            ['name' => 'Nate Higgerson', 'email' => 'nate@sirent.id', 'phone' => '081333333334'],
            ['name' => 'Fuat Meminjam', 'email' => 'fuatpinjam@sirent.id', 'phone' => '081333333335'],
            ['name' => 'Sarah Amalia', 'email' => 'sarah@sirent.id', 'phone' => '081333333336'],
            ['name' => 'Budi Santoso', 'email' => 'budi@sirent.id', 'phone' => '081333333337'],
            ['name' => 'Dian Pratiwi', 'email' => 'dian@sirent.id', 'phone' => '081333333338'],
            ['name' => 'Rizky Aditya', 'email' => 'rizky@sirent.id', 'phone' => '081333333339'],
            ['name' => 'Maya Indah', 'email' => 'maya@sirent.id', 'phone' => '081333333340'],
        ];

        foreach ($borrowers as $borrower) {
            User::create(array_merge($borrower, [
                'password' => Hash::make('password'),
                'role' => User::ROLE_BORROWER,
                'is_owner_active' => false,
                'rating_avg_as_borrower' => fake()->randomFloat(2, 3.0, 5.0),
            ]));
        }
    }
}
