<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $ownerIds = User::where('is_owner_active', true)->pluck('id')->toArray();
        $categories = Category::pluck('id', 'slug')->toArray();

        $products = [
            // Kamera — owner: Fuat Photography (ownerIds[0])
            [
                'owner_id' => $ownerIds[0],
                'category_id' => $categories['kamera'],
                'title' => 'Sony ZV E10',
                'description' => 'The Sony ZV-E10 is a 24.2MP APS-C mirrorless camera specifically designed for vloggers and content creators, featuring an interchangeable E-mount system, 4K video recording, a side flip-out screen, and advanced autofocus. Key, vlogging-focused features include a dedicated product showcase setting, background defocus, and a directional 3-capsule microphone with a windscreen.',
                'condition' => Product::CONDITION_LIKE_NEW,
                'price_per_day' => 150000,
                'deposit_amount' => 500000,
                'location_city' => 'Kota Administrasi Jakarta Selatan',
                'status' => Product::STATUS_ACTIVE,
                'rating_avg' => 4.80,
                'total_rented' => 120,
            ],
            [
                'owner_id' => $ownerIds[0],
                'category_id' => $categories['kamera'],
                'title' => 'Canon EOS R10',
                'description' => 'Canon EOS R10 adalah kamera mirrorless APS-C dengan 24.2MP, dual pixel AF, burst shooting 15fps, dan perekaman video 4K 30fps. Cocok untuk fotografi wildlife dan sport.',
                'condition' => Product::CONDITION_GOOD,
                'price_per_day' => 175000,
                'deposit_amount' => 750000,
                'location_city' => 'Kota Administrasi Jakarta Selatan',
                'status' => Product::STATUS_ACTIVE,
                'rating_avg' => 4.50,
                'total_rented' => 85,
            ],
            [
                'owner_id' => $ownerIds[0],
                'category_id' => $categories['kamera'],
                'title' => 'Fujifilm X-T5',
                'description' => 'Kamera mirrorless Fujifilm X-T5 dengan sensor 40.2MP X-Trans CMOS 5 HR, IBIS 5-axis, dan desain retro klasik. Ideal untuk street photography dan portrait.',
                'condition' => Product::CONDITION_NEW,
                'price_per_day' => 200000,
                'deposit_amount' => 1000000,
                'location_city' => 'Kota Administrasi Jakarta Selatan',
                'status' => Product::STATUS_ACTIVE,
                'rating_avg' => 4.90,
                'total_rented' => 45,
            ],

            // Drone — owner: Tony Tech (ownerIds[3])
            [
                'owner_id' => $ownerIds[3],
                'category_id' => $categories['drone'],
                'title' => 'DJI Air 3S Drone',
                'description' => 'DJI Air 3S dengan dual camera system, wide-angle dan medium tele, 48MP photo, 4K/60fps HDR video, omnidirectional obstacle sensing, dan 46 menit flight time.',
                'condition' => Product::CONDITION_LIKE_NEW,
                'price_per_day' => 185000,
                'deposit_amount' => 1500000,
                'location_city' => 'Surabaya',
                'status' => Product::STATUS_ACTIVE,
                'rating_avg' => 4.70,
                'total_rented' => 74,
            ],
            [
                'owner_id' => $ownerIds[3],
                'category_id' => $categories['drone'],
                'title' => 'DJI Mini 4 Pro',
                'description' => 'Drone ringan di bawah 249g dengan kamera 48MP, 4K/100fps, omnidirectional obstacle sensing, dan 34 menit flight time. Tanpa perlu registrasi.',
                'condition' => Product::CONDITION_GOOD,
                'price_per_day' => 125000,
                'deposit_amount' => 1000000,
                'location_city' => 'Surabaya',
                'status' => Product::STATUS_ACTIVE,
                'rating_avg' => 4.60,
                'total_rented' => 52,
            ],

            // Audio — owner: SoundMntp Shop (ownerIds[1])
            [
                'owner_id' => $ownerIds[1],
                'category_id' => $categories['audio'],
                'title' => 'Hybrid Tube HiFi Stereo Amplifier',
                'description' => 'Amplifier tabung hybrid dengan suara warm dan detail. 50W per channel, input RCA dan Bluetooth 5.0. Cocok untuk audiophile dan home studio.',
                'condition' => Product::CONDITION_GOOD,
                'price_per_day' => 100000,
                'deposit_amount' => 400000,
                'location_city' => 'Kota Administrasi Jakarta Barat',
                'status' => Product::STATUS_ACTIVE,
                'rating_avg' => 4.30,
                'total_rented' => 30,
            ],
            [
                'owner_id' => $ownerIds[1],
                'category_id' => $categories['audio'],
                'title' => 'Yamaha HS8 Studio Monitor',
                'description' => 'Studio monitor aktif 8-inch dengan bi-amped 120W, respons frekuensi 38Hz-30kHz. Standar industri untuk mixing dan mastering.',
                'condition' => Product::CONDITION_FAIR,
                'price_per_day' => 80000,
                'deposit_amount' => 350000,
                'location_city' => 'Kota Administrasi Jakarta Barat',
                'status' => Product::STATUS_ACTIVE,
                'rating_avg' => 4.10,
                'total_rented' => 18,
            ],

            // Alat Musik — owner: SoundMntp Shop (ownerIds[1])
            [
                'owner_id' => $ownerIds[1],
                'category_id' => $categories['alat-musik'],
                'title' => 'Fender Stratocaster American Pro II',
                'description' => 'Gitar elektrik Fender Stratocaster American Pro II dengan V-Mod II single-coil pickups, maple neck, dan finishing olympic white.',
                'condition' => Product::CONDITION_LIKE_NEW,
                'price_per_day' => 120000,
                'deposit_amount' => 800000,
                'location_city' => 'Kota Administrasi Jakarta Barat',
                'status' => Product::STATUS_ACTIVE,
                'rating_avg' => 4.75,
                'total_rented' => 22,
            ],

            // Gaming — owner: Gio Games (ownerIds[2])
            [
                'owner_id' => $ownerIds[2],
                'category_id' => $categories['gaming'],
                'title' => 'PlayStation 5',
                'description' => 'PS5 Digital Edition dengan controller DualSense, SSD 825GB, dukungan 4K/120fps, dan ray tracing. Include 2 controller.',
                'condition' => Product::CONDITION_GOOD,
                'price_per_day' => 75000,
                'deposit_amount' => 500000,
                'location_city' => 'Jakarta Pusat',
                'status' => Product::STATUS_ACTIVE,
                'rating_avg' => 4.40,
                'total_rented' => 210,
            ],
            [
                'owner_id' => $ownerIds[2],
                'category_id' => $categories['gaming'],
                'title' => 'Nintendo Switch OLED',
                'description' => 'Nintendo Switch OLED dengan layar 7-inch, Joy-Con, docking station, dan 5 game populer (Zelda, Mario Kart, Pokemon, dll).',
                'condition' => Product::CONDITION_LIKE_NEW,
                'price_per_day' => 55000,
                'deposit_amount' => 300000,
                'location_city' => 'Jakarta Pusat',
                'status' => Product::STATUS_ACTIVE,
                'rating_avg' => 4.55,
                'total_rented' => 168,
            ],

            // Perlengkapan Mendaki — owner: Camping Story (ownerIds[4])
            [
                'owner_id' => $ownerIds[4],
                'category_id' => $categories['perlengkapan-mendaki'],
                'title' => 'Naturehike Cloud Up 2 Tent',
                'description' => 'Tenda ultralight 2-person, berat 1.9kg, double layer waterproof PU4000, cocok untuk hiking dan camping gunung.',
                'condition' => Product::CONDITION_GOOD,
                'price_per_day' => 40000,
                'deposit_amount' => 150000,
                'location_city' => 'Bandung',
                'status' => Product::STATUS_ACTIVE,
                'rating_avg' => 4.35,
                'total_rented' => 95,
            ],
            [
                'owner_id' => $ownerIds[4],
                'category_id' => $categories['perlengkapan-mendaki'],
                'title' => 'Osprey Atmos AG 65 Backpack',
                'description' => 'Carrier hiking 65L dengan teknologi Anti-Gravity suspension, ventilasi maksimal, dan fit compression straps. Nyaman untuk trek jauh.',
                'condition' => Product::CONDITION_FAIR,
                'price_per_day' => 35000,
                'deposit_amount' => 100000,
                'location_city' => 'Bandung',
                'status' => Product::STATUS_ACTIVE,
                'rating_avg' => 4.20,
                'total_rented' => 62,
            ],

            // Olahraga
            [
                'owner_id' => $ownerIds[2],
                'category_id' => $categories['olahraga'],
                'title' => 'SVRG Padel Racket',
                'description' => 'Raket padel premium dengan carbon frame dan EVA core, grip nyaman, termasuk tas raket. Ideal untuk turnamen dan latihan.',
                'condition' => Product::CONDITION_NEW,
                'price_per_day' => 85000,
                'deposit_amount' => 300000,
                'location_city' => 'Jakarta Pusat',
                'status' => Product::STATUS_ACTIVE,
                'rating_avg' => 4.65,
                'total_rented' => 70,
            ],
            [
                'owner_id' => $ownerIds[4],
                'category_id' => $categories['olahraga'],
                'title' => 'Roadbike Carbon PINARELLO',
                'description' => 'Sepeda roadbike carbon frame Pinarello, Shimano Ultegra groupset, disc brake, berat 8.2kg. Termasuk helm dan pedal.',
                'condition' => Product::CONDITION_GOOD,
                'price_per_day' => 200000,
                'deposit_amount' => 2000000,
                'location_city' => 'Bandung',
                'status' => Product::STATUS_ACTIVE,
                'rating_avg' => 4.85,
                'total_rented' => 22,
            ],

            // Videografi — owner: Fuat Photography (ownerIds[0])
            [
                'owner_id' => $ownerIds[0],
                'category_id' => $categories['videografi'],
                'title' => 'DJI RS 3 Gimbal',
                'description' => 'Gimbal 3-axis stabilizer untuk kamera mirrorless/DSLR, payload 3kg, layar sentuh 1.8 inch, dan auto-lock axis.',
                'condition' => Product::CONDITION_LIKE_NEW,
                'price_per_day' => 90000,
                'deposit_amount' => 500000,
                'location_city' => 'Kota Administrasi Jakarta Selatan',
                'status' => Product::STATUS_ACTIVE,
                'rating_avg' => 4.50,
                'total_rented' => 38,
            ],
            [
                'owner_id' => $ownerIds[3],
                'category_id' => $categories['videografi'],
                'title' => 'GoPro Hero 13 Black',
                'description' => 'Action camera 5.3K60, waterproof 10m, HyperSmooth 6.0, GPS, Horizon Lock. Include mounting kit dan 2 baterai.',
                'condition' => Product::CONDITION_NEW,
                'price_per_day' => 95000,
                'deposit_amount' => 400000,
                'location_city' => 'Surabaya',
                'status' => Product::STATUS_ACTIVE,
                'rating_avg' => 4.70,
                'total_rented' => 55,
            ],

            // Alat Musik — owner: Gio Games (ownerIds[2]) — drum set
            [
                'owner_id' => $ownerIds[2],
                'category_id' => $categories['alat-musik'],
                'title' => 'Roland TD-17KVX Electronic Drum',
                'description' => 'Drum elektrik Roland TD-17KVX dengan mesh head pads, Bluetooth, coaching functions, dan 50 preset kits. Termasuk pedal dan throne.',
                'condition' => Product::CONDITION_GOOD,
                'price_per_day' => 110000,
                'deposit_amount' => 750000,
                'location_city' => 'Jakarta Pusat',
                'status' => Product::STATUS_ACTIVE,
                'rating_avg' => 4.40,
                'total_rented' => 15,
            ],

            // Kamera — owner: Tony Tech (ownerIds[3])
            [
                'owner_id' => $ownerIds[3],
                'category_id' => $categories['kamera'],
                'title' => 'Sony A7 IV',
                'description' => 'Full-frame mirrorless camera 33MP, 4K 60fps video, Real-time Eye AF, 5-axis IBIS. Body only, tersedia juga lensa tambahan.',
                'condition' => Product::CONDITION_LIKE_NEW,
                'price_per_day' => 250000,
                'deposit_amount' => 2000000,
                'location_city' => 'Surabaya',
                'status' => Product::STATUS_ACTIVE,
                'rating_avg' => 4.90,
                'total_rented' => 33,
            ],

            // Perlengkapan Mendaki — owner: Camping Story (ownerIds[4])
            [
                'owner_id' => $ownerIds[4],
                'category_id' => $categories['perlengkapan-mendaki'],
                'title' => 'Jetboil Flash Cooking System',
                'description' => 'Kompor portable untuk mendaki, mendidihkan air dalam 100 detik, ringan (371g), dengan indikator perubahan warna suhu.',
                'condition' => Product::CONDITION_GOOD,
                'price_per_day' => 25000,
                'deposit_amount' => 50000,
                'location_city' => 'Bandung',
                'status' => Product::STATUS_ACTIVE,
                'rating_avg' => 4.25,
                'total_rented' => 140,
            ],

            // Audio — owner: Fuat Photography (ownerIds[0])
            [
                'owner_id' => $ownerIds[0],
                'category_id' => $categories['audio'],
                'title' => 'Rode Wireless PRO Microphone',
                'description' => 'Wireless microphone system 2.4GHz dengan dual transmitter, 32-bit float onboard recording, dan range hingga 260m.',
                'condition' => Product::CONDITION_NEW,
                'price_per_day' => 65000,
                'deposit_amount' => 250000,
                'location_city' => 'Kota Administrasi Jakarta Selatan',
                'status' => Product::STATUS_ACTIVE,
                'rating_avg' => 4.75,
                'total_rented' => 48,
            ],
        ];

        foreach ($products as $data) {
            $data['slug'] = Str::slug($data['title']) . '-' . Str::random(6);
            $product = Product::create($data);

            // Create 1 primary product image (dummy placeholder)
            ProductImage::create([
                'product_id' => $product->id,
                'image_path' => 'products/dummy-' . $product->id . '.jpg',
                'is_primary' => true,
                'sort_order' => 0,
            ]);
        }
    }
}
