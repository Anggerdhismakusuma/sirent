<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // akun admin
        User::factory()->create([
            'name' => 'Fuad Admin',
            'email' => 'admin@sirent.test',
            'role' => 'admin',
        ]);

        // akun owner
        User::factory()->create([
            'name' => 'Gatot Owner',
            'email' => 'owner@sirent.test',
            'role' => 'pemilik',
        ]);

        // akun borrower
        User::factory()->create([
            'name' => 'Caca Borrower',
            'email' => 'peminjam@sirent.test',
            'role' => 'peminjam',
        ]);

        // buat akun random buat cek tampilan user list
        User::factory(10)->create();
    }
}
