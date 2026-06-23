<?php

namespace Database\Seeders;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Database\Seeder;

class ChatSeeder extends Seeder
{
    public function run(): void
    {
        // Borrower utama (Fuat Meminjam, id=11) chat dengan 4 owner berbeda
        $borrowerId = 11; // Fuat Meminjam

        // ── Conversation 1: Fuat Meminjam ↔ Fuat Photography (owner_id=2), product Sony ZV E10 (id=1) ──
        $conv1 = Conversation::firstOrCreate(
            ['borrower_id' => $borrowerId, 'owner_id' => 2, 'product_id' => 1],
            ['last_message_at' => now()->subMinutes(5)]
        );

        $messages1 = [
            ['sender_id' => $borrowerId, 'body' => 'Halo Kak! Mau tanya soal Sony ZV E10 yang disewakan. Apakah masih tersedia untuk tanggal 14-17 Agustus?', 'is_read' => true, 'created_at' => now()->subDays(2)->setTime(10, 30)],
            ['sender_id' => 2, 'body' => 'Halo kak, tersedia kok untuk tanggal segitu. Mau tanya, butuhnya untuk foto atau video ya?', 'is_read' => true, 'created_at' => now()->subDays(2)->setTime(10, 32)],
            ['sender_id' => $borrowerId, 'body' => 'Untuk video kak. Saya ada project short film kecil-kecilan. Ada lensa kit-nya kan ya?', 'is_read' => true, 'created_at' => now()->subDays(2)->setTime(10, 35)],
            ['sender_id' => 2, 'body' => 'Ada kak, lengkap dengan lensa kit 16-50mm. Kalau butuh lensa tambahan, saya juga ada Sigma 30mm f/1.4 bisa sekalian disewa.', 'is_read' => true, 'created_at' => now()->subDays(2)->setTime(10, 38)],
            ['sender_id' => $borrowerId, 'body' => 'Wah menarik. Apakah sudah termasuk baterai cadangan dan charger? Saya takut baterainya kurang buat shooting seharian.', 'is_read' => true, 'created_at' => now()->subDays(1)->setTime(14, 15)],
            ['sender_id' => 2, 'body' => 'Sudah kak, saya kasih 2 baterai + charger. Sama memory card 64GB juga saya pinjemin. Jadi tinggal bawa diri aja 😊', 'is_read' => true, 'created_at' => now()->subDays(1)->setTime(14, 20)],
            ['sender_id' => $borrowerId, 'body' => 'Oke kak, apakah sudah bisa langsung booking? Saya mau langsung sewa untuk 3 hari.', 'is_read' => true, 'created_at' => now()->subDays(1)->setTime(14, 22)],
            ['sender_id' => 2, 'body' => 'Bisa banget kak. Langsung booking aja lewat aplikasi, nanti saya approve. Jangan lupa baca tips pemakaian di deskripsi ya.', 'is_read' => true, 'created_at' => now()->subDays(1)->setTime(14, 25)],
            ['sender_id' => $borrowerId, 'body' => 'Sudah lengkap ya kak data bookingnya. Terima kasih banyak infonya! 🙏', 'is_read' => true, 'created_at' => now()->subHours(3)->setTime(9, 15)],
            ['sender_id' => 2, 'body' => 'Baik kak, saya lanjut approve bookingnya ya. Kalau ada pertanyaan tinggal chat aja.', 'is_read' => true, 'created_at' => now()->subHours(3)->setTime(9, 17)],
            ['sender_id' => $borrowerId, 'body' => 'Siap, terima kasih banyak kak!', 'is_read' => true, 'created_at' => now()->subMinutes(5)],
        ];

        foreach ($messages1 as $msg) {
            Message::create([
                'conversation_id' => $conv1->id,
                'sender_id' => $msg['sender_id'],
                'body' => $msg['body'],
                'is_read' => $msg['is_read'],
                'created_at' => $msg['created_at'],
                'updated_at' => $msg['created_at'],
            ]);
        }

        // ── Conversation 2: Fuat Meminjam ↔ SoundMntp Shop (owner_id=3), product Yamaha HS8 (id=7) ──
        $conv2 = Conversation::firstOrCreate(
            ['borrower_id' => $borrowerId, 'owner_id' => 3, 'product_id' => 7],
            ['last_message_at' => now()->subHours(1)]
        );

        $messages2 = [
            ['sender_id' => $borrowerId, 'body' => 'Halo kak, mau tanya Yamaha HS8-nya. Ini yang versi apa ya?', 'is_read' => true, 'created_at' => now()->subDays(3)->setTime(16, 0)],
            ['sender_id' => 3, 'body' => 'Halo! Ini Yamaha HS8 versi terbaru kak, kondisi masih sangat bagus. Baru dipakai 3x project.', 'is_read' => true, 'created_at' => now()->subDays(3)->setTime(16, 5)],
            ['sender_id' => $borrowerId, 'body' => 'Oke kak. Saya tertarik untuk sewa 2 unit. Ada kah?', 'is_read' => true, 'created_at' => now()->subDays(3)->setTime(16, 8)],
            ['sender_id' => 3, 'body' => 'Untuk 2 unit ready kak. Tapi harus di-book dulu ya biar gak keduluan. Akhir-akhir ini banyak yang tanya.', 'is_read' => true, 'created_at' => now()->subDays(3)->setTime(16, 10)],
            ['sender_id' => $borrowerId, 'body' => 'Siap kak, nanti saya booking 2 unit untuk weekend depan ya.', 'is_read' => true, 'created_at' => now()->subHours(1)],
        ];

        foreach ($messages2 as $msg) {
            Message::create([
                'conversation_id' => $conv2->id,
                'sender_id' => $msg['sender_id'],
                'body' => $msg['body'],
                'is_read' => $msg['is_read'],
                'created_at' => $msg['created_at'],
                'updated_at' => $msg['created_at'],
            ]);
        }

        // ── Conversation 3: Fuat Meminjam ↔ Camping Story (owner_id=6), product Naturehike Tent (id=11) ──
        // This one has UNREAD messages (F-CHT-03)
        $conv3 = Conversation::firstOrCreate(
            ['borrower_id' => $borrowerId, 'owner_id' => 6, 'product_id' => 11],
            ['last_message_at' => now()->subMinutes(30)]
        );

        $messages3 = [
            ['sender_id' => $borrowerId, 'body' => 'Halo kak, mau tanya tenda Naturehike-nya. Kapasitas berapa orang ya?', 'is_read' => true, 'created_at' => now()->subDays(1)->setTime(20, 0)],
            ['sender_id' => 6, 'body' => 'Halo! Kapasitas 2 orang kak, cocok untuk camping berdua. Materialnya udah waterproof jadi aman kalau hujan.', 'is_read' => true, 'created_at' => now()->subDays(1)->setTime(20, 10)],
            ['sender_id' => $borrowerId, 'body' => 'Wah pas banget. Saya rencana mau camping ke Gunung Gede akhir bulan ini. Apakah ready?', 'is_read' => true, 'created_at' => now()->subDays(1)->setTime(20, 15)],
            ['sender_id' => 6, 'body' => 'Ready kak untuk akhir bulan. Mau sekalian sama sleeping bag dan matras? Saya ada paket camping lengkap.', 'is_read' => false, 'created_at' => now()->subHours(2)],
            ['sender_id' => 6, 'body' => 'Untuk paket lengkap (tenda + 2 sleeping bag + 2 matras + nesting) saya kasih harga spesial 200k/hari. Gimana kak?', 'is_read' => false, 'created_at' => now()->subHours(1)->setTime(50, 0)],
            ['sender_id' => 6, 'body' => 'Kalau tertarik bisa langsung booking ya, takut keduluan soalnya akhir bulan banyak yang camping ⛺', 'is_read' => false, 'created_at' => now()->subMinutes(30)],
        ];

        foreach ($messages3 as $msg) {
            Message::create([
                'conversation_id' => $conv3->id,
                'sender_id' => $msg['sender_id'],
                'body' => $msg['body'],
                'is_read' => $msg['is_read'],
                'created_at' => $msg['created_at'],
                'updated_at' => $msg['created_at'],
            ]);
        }

        // ── Conversation 4: Fuat Meminjam ↔ Tony Tech (owner_id=5), product DJI Air 3S (id=4) ──
        $conv4 = Conversation::firstOrCreate(
            ['borrower_id' => $borrowerId, 'owner_id' => 5, 'product_id' => 4],
            ['last_message_at' => now()->subDays(2)]
        );

        $messages4 = [
            ['sender_id' => $borrowerId, 'body' => 'Kak, DJI Air 3S-nya masih available?', 'is_read' => true, 'created_at' => now()->subDays(5)->setTime(11, 0)],
            ['sender_id' => 5, 'body' => 'Halo kak, masih available. Mau tanya, udah pernah terbangin drone sebelumnya?', 'is_read' => true, 'created_at' => now()->subDays(5)->setTime(11, 5)],
            ['sender_id' => $borrowerId, 'body' => 'Baru pertama kali kak. Apakah ada tutorial atau panduannya?', 'is_read' => true, 'created_at' => now()->subDays(5)->setTime(11, 10)],
            ['sender_id' => 5, 'body' => 'Tenang kak, saya kasih briefing 15 menit pas serah terima. Drone-nya juga sudah ada sensor obstacle avoidance jadi aman buat pemula.', 'is_read' => true, 'created_at' => now()->subDays(5)->setTime(11, 15)],
            ['sender_id' => $borrowerId, 'body' => 'Wah oke banget kak. Saya jadi tertarik. Tapi ini perlu izin terbang gak sih?', 'is_read' => true, 'created_at' => now()->subDays(4)->setTime(15, 30)],
            ['sender_id' => 5, 'body' => 'Untuk drone di bawah 2kg gak perlu izin khusus kak. Tapi hindari terbang di area no-fly zone ya. Nanti saya kasih tau area yang aman.', 'is_read' => true, 'created_at' => now()->subDays(4)->setTime(15, 35)],
            ['sender_id' => $borrowerId, 'body' => 'Oke kak, makasih infonya. Saya diskusi dulu sama temen ya, nanti kabarin lagi.', 'is_read' => true, 'created_at' => now()->subDays(2)],
        ];

        foreach ($messages4 as $msg) {
            Message::create([
                'conversation_id' => $conv4->id,
                'sender_id' => $msg['sender_id'],
                'body' => $msg['body'],
                'is_read' => $msg['is_read'],
                'created_at' => $msg['created_at'],
                'updated_at' => $msg['created_at'],
            ]);
        }

        // ── Conversation 5: Fuat Meminjam ↔ Gio Games (owner_id=4), product PS5 (id=9) ──
        $conv5 = Conversation::firstOrCreate(
            ['borrower_id' => $borrowerId, 'owner_id' => 4, 'product_id' => 9],
            ['last_message_at' => now()->subDays(7)]
        );

        $messages5 = [
            ['sender_id' => $borrowerId, 'body' => 'Halo kak, mau tanya PS5-nya. Ini include controller berapa?', 'is_read' => true, 'created_at' => now()->subDays(10)->setTime(19, 0)],
            ['sender_id' => 4, 'body' => 'Halo! Include 2 controller + beberapa game digital. Ada GTA V, FIFA, sama Spider-Man 2.', 'is_read' => true, 'created_at' => now()->subDays(10)->setTime(19, 5)],
            ['sender_id' => $borrowerId, 'body' => 'Wah lengkap banget. Saya mau sewa buat weekend aja. Bisa short rental 2 hari?', 'is_read' => true, 'created_at' => now()->subDays(10)->setTime(19, 10)],
            ['sender_id' => 4, 'body' => 'Bisa kak. Tapi minimal sewa 1 hari sih. Kalau 2 hari malah lebih hemat, saya ada paket weekend rate.', 'is_read' => true, 'created_at' => now()->subDays(10)->setTime(19, 15)],
            ['sender_id' => $borrowerId, 'body' => 'Oke deh kak, nanti saya booking untuk weekend besok ya. Makasih!', 'is_read' => true, 'created_at' => now()->subDays(7)],
        ];

        foreach ($messages5 as $msg) {
            Message::create([
                'conversation_id' => $conv5->id,
                'sender_id' => $msg['sender_id'],
                'body' => $msg['body'],
                'is_read' => $msg['is_read'],
                'created_at' => $msg['created_at'],
                'updated_at' => $msg['created_at'],
            ]);
        }

        echo "ChatSeeder: 5 conversations + " . (count($messages1) + count($messages2) + count($messages3) + count($messages4) + count($messages5)) . " messages created.\n";
    }
}
