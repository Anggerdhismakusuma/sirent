<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Kamera', 'slug' => 'kamera', 'icon' => 'bi-camera'],
            ['name' => 'Drone', 'slug' => 'drone', 'icon' => 'bi-drone'],
            ['name' => 'Alat Musik', 'slug' => 'alat-musik', 'icon' => 'bi-music-note'],
            ['name' => 'Perlengkapan Mendaki', 'slug' => 'perlengkapan-mendaki', 'icon' => 'bi-compass'],
            ['name' => 'Audio', 'slug' => 'audio', 'icon' => 'bi-soundwave'],
            ['name' => 'Gaming', 'slug' => 'gaming', 'icon' => 'bi-joystick'],
            ['name' => 'Olahraga', 'slug' => 'olahraga', 'icon' => 'bi-bicycle'],
            ['name' => 'Videografi', 'slug' => 'videografi', 'icon' => 'bi-camera-video'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
