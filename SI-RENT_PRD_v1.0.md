# SI-RENT — Product Requirements Document (PRD)

**Platform Sewa Peralatan Hobi Multi-Vendor**

| Versi Dokumen | 1.0 |
|---|---|
| Tanggal | Juni 2025 |
| Status | Draft |
| Teknologi Backend | Laravel (PHP) |
| Teknologi Frontend | Bootstrap 5 |
| Database | MySQL |

---

## Daftar Isi

1. Ringkasan Eksekutif
2. Latar Belakang & Tujuan
3. Target Pengguna
4. Ruang Lingkup Sistem (termasuk Scope Fase 1 — Borrower Only)
5. Peran & Hak Akses (Role)
6. Fitur & Kebutuhan Fungsional
7. Alur Proses Utama
8. Kebutuhan Non-Fungsional
9. Tech Stack & Rekomendasi Library
10. Struktur Folder Proyek
11. Skema Database (Detail)
12. Enum & Status Values
13. Validasi Form
14. Business Rules & Edge Cases
15. Keamanan & Verifikasi Identitas
16. Daftar Route Aplikasi
17. Referensi Desain UI (Figma)
18. Risiko & Mitigasi

---

## 1. RINGKASAN EKSEKUTIF

SI-RENT adalah platform web multi-vendor berbasis Laravel yang memungkinkan pengguna untuk menyewa dan menyewakan peralatan hobi seperti kamera, drone, alat musik, perlengkapan mendaki, dan peralatan lainnya. Platform ini beroperasi layaknya e-commerce dua arah (seperti Tokopedia atau Shopee) di mana seorang pengguna dapat berperan sebagai penyewa (borrower) sekaligus pemilik barang (owner).

Platform ini dirancang untuk menjawab kebutuhan individu yang memerlukan peralatan hobi berkualitas secara temporer tanpa harus melakukan pembelian permanen, sekaligus memberikan kesempatan kepada pemilik peralatan untuk menghasilkan pendapatan pasif dari aset yang tidak selalu aktif digunakan.

---

## 2. LATAR BELAKANG & TUJUAN

### 2.1 Latar Belakang

Peralatan hobi berkualitas tinggi seringkali memiliki harga yang sangat tinggi, sementara penggunaannya bersifat sporadis atau musiman. Di sisi lain, banyak pemilik peralatan yang membiarkan barang mereka tidak terpakai. Kondisi ini menciptakan inefisiensi di kedua sisi pasar.

SI-RENT hadir sebagai jembatan yang menghubungkan keduanya dalam satu ekosistem yang aman, terpercaya, dan mudah digunakan.

### 2.2 Tujuan Produk

- Menyediakan akses terhadap peralatan hobi berkualitas dengan biaya yang terjangkau melalui mekanisme sewa.
- Memberikan platform bagi pemilik peralatan untuk memonetisasi aset idle mereka.
- Membangun ekosistem sewa yang aman melalui verifikasi identitas, sistem rating, dan penanganan dispute.
- Mendorong ekonomi berbagi (sharing economy) di segmen peralatan hobi di Indonesia.

---

## 3. TARGET PENGGUNA

### 3.1 Profil Pengguna Utama

**A. Pengguna Situasional (Kebutuhan Mendesak)**

Individu yang menghadapi kebutuhan satu kali atau situasi mendesak yang memerlukan peralatan tertentu tanpa berniat membeli secara permanen.

- Contoh: Fotografer amatir yang membutuhkan lensa premium untuk acara pernikahan teman.
- Contoh: Seseorang yang ingin merekam video perjalanan dengan drone tanpa harus membelinya.
- Motivasi utama: Solusi instan, hemat biaya, tanpa komitmen kepemilikan jangka panjang.

**B. Hobby Addicts (Antusias Hobi)**

Para penggemar hobi dengan standar tinggi terhadap kualitas peralatan namun bersifat eksploratif dan selektif sebelum membeli.

- Sub-segmen 1: Trial-and-error sebelum beli — ingin mencoba kamera mirrorless, perlengkapan mendaki, atau alat musik kelas atas sebelum memutuskan untuk berinvestasi.
- Sub-segmen 2: Kolektor musiman — membutuhkan peralatan tambahan untuk aktivitas musiman tanpa menambah beban penyimpanan.
- Motivasi utama: Eksplorasi kualitas, fleksibilitas, dan optimasi ruang penyimpanan.

### 3.2 Profil Owner

- Individu yang memiliki peralatan hobi berkualitas yang tidak selalu aktif digunakan.
- Komunitas fotografi, videografi, atau pecinta alam yang ingin berbagi peralatan secara terorganisir.
- Motivasi utama: Penghasilan pasif dari aset idle, membangun reputasi dalam komunitas.

---

## 4. RUANG LINGKUP SISTEM

### 4.1 Dalam Lingkup (In-Scope)

- Registrasi, autentikasi, dan manajemen akun pengguna.
- Manajemen listing produk sewa oleh owner.
- Pencarian dan browse produk oleh borrower.
- Alur pengajuan dan persetujuan peminjaman.
- Sistem chat real-time antara borrower dan owner.
- Verifikasi identitas pengguna oleh admin.
- Sistem rating dan ulasan dua arah (borrower menilai owner, owner menilai borrower).
- Dashboard pendapatan dan riwayat transaksi untuk owner.
- Panel admin untuk monitoring, verifikasi, dan dispute resolution.
- Notifikasi in-app dan email.

### 4.2 Di Luar Lingkup (Out-of-Scope) — Versi 1.0

- Integrasi payment gateway (transaksi dilakukan di luar platform / cash).
- Fitur asuransi barang otomatis.
- Aplikasi mobile native (iOS/Android).
- Sistem pengiriman/kurir terintegrasi.

### 4.3 Scope Implementasi Fase 1 (Borrower-Only)

Pembangunan aplikasi dilakukan bertahap. Fase 1 yang dikerjakan saat ini HANYA mencakup sisi Borrower, halaman publik, autentikasi, dan chat — karena desain Figma untuk sisi Owner dan Admin belum tersedia. AI coding assistant TIDAK membangun atau menebak desain UI untuk halaman Owner/Admin pada fase ini, meski struktur database dan route untuk keduanya sudah didefinisikan lebih dulu di PRD (section 11 dan 16) sebagai persiapan fase berikutnya.

**Termasuk dalam Fase 1:**
- Seluruh halaman publik: Home (guest & logged-in), About Us, Detail Produk, Store (Produk/About/Reviews).
- Autentikasi: modal Login/Register, verifikasi email, lupa password.
- Seluruh halaman Borrower: Dashboard, Activity Ongoing, History, Settings.
- Fitur Chat (borrower berkomunikasi dengan akun owner yang sudah ada melalui seeder).
- Aksi borrower: ajukan peminjaman, batalkan peminjaman, beri rating ke owner.

**TIDAK Termasuk dalam Fase 1 (Menyusul):**
- Halaman & dashboard Owner (kelola listing, kelola permintaan, riwayat penyewaan, dashboard pendapatan) — menunggu desain Figma tersedia.
- Panel Admin (verifikasi identitas, monitoring, dispute resolution, kelola kategori) — menunggu desain Figma tersedia.
- Tombol/flow 'Jadi Owner' untuk mengaktifkan `is_owner_active` TIDAK ditampilkan/dinonaktifkan dulu di UI Settings, agar tidak ada pintu masuk ke fitur yang belum dibangun.

**Mengisi Data Sisi Owner untuk Kebutuhan Fase 1:**

Karena halaman Borrower tetap menampilkan data milik owner (kartu produk, nama toko, rating, lawan chat), data owner pada fase ini disediakan melalui database seeder (lihat section 10, folder `database/seeders/`), BUKAN dibangunkan UI manage-nya. Seeder membuat user dengan `is_owner_active = TRUE` beserta beberapa produk dummy, supaya halaman Browse, Detail Produk, Store, dan Chat dapat berfungsi dan diuji secara penuh dari sisi borrower tanpa perlu UI Owner.

**Borrower vs Owner: Bukan Role Terpisah, tapi Mode Berbeda dalam 1 Akun**

Mirip Tokopedia/Shopee (mode Beli vs mode Jual/Seller Center), SI-RENT tidak memisahkan user borrower dan user owner sebagai dua jenis akun berbeda. Satu akun yang sama dapat menjalankan kedua peran ini secara paralel (lihat catatan kolom `role` & `is_owner_active` pada section 11.1). Namun secara FUNGSIONAL kedua mode ini menampilkan data dan aksi yang sepenuhnya berbeda:

| Aspek | Sebagai Borrower | Sebagai Owner (Fase Berikutnya) |
|---|---|---|
| Dashboard | Ringkasan transaksi yang DIA AJUKAN sebagai peminjam | Ringkasan transaksi yang MASUK sebagai pemilik + grafik pendapatan bulanan |
| Data utama | `rental_requests` dengan `borrower_id` = dirinya | `products` miliknya + `rental_requests` dengan `owner_id` = dirinya |
| Aksi utama | Ajukan sewa, batalkan (jika masih pending), beri rating ke owner | Approve/reject permintaan masuk, kelola listing & ketersediaan, beri rating ke borrower |
| Halaman terkait | Dashboard, Activity Ongoing, History, Settings | Dashboard Owner, Kelola Listing, Kelola Permintaan, Riwayat Penyewaan |
| Status Figma | Sudah ada link node (section 17.3) | Belum ada link node — menyusul |

Bagian yang tetap sama di kedua mode: identitas akun (nama, email, foto profil), dan sistem rating yang saling terhubung — skor sebagai borrower dan skor sebagai owner dihitung & disimpan terpisah (`rating_avg_as_borrower` dan `rating_avg_as_owner` pada tabel `users`), namun keduanya dapat ditampilkan bersamaan di profil publik seseorang.

---

## 5. PERAN & HAK AKSES (ROLE)

Sistem SI-RENT memiliki tiga peran utama dengan hak akses yang berbeda:

| Fitur / Aksi | Borrower | Owner | Admin |
|---|---|---|---|
| Registrasi & Login | ✓ | ✓ | ✓ |
| Manage Profil & Akun | ✓ | ✓ | ✓ |
| Browse & Cari Produk | ✓ | ✓ | – |
| Lihat Detail Produk | ✓ | ✓ | – |
| Ajukan Peminjaman | ✓ | – | – |
| Lihat Status & Riwayat Peminjaman | ✓ | ✓ | ✓ |
| Beri Rating ke Owner | ✓ | – | – |
| Beri Rating ke Borrower | – | ✓ | – |
| Kelola Listing Barang | – | ✓ | – |
| Atur Ketersediaan & Harga | – | ✓ | – |
| Terima / Tolak Permintaan | – | ✓ | – |
| Dashboard Penjualan & Penghasilan | – | ✓ | – |
| Fitur Chat | ✓ | ✓ | – |
| Verifikasi Identitas User | – | – | ✓ |
| Monitoring Aktivitas & Transaksi | – | – | ✓ |
| Tangani Dispute | – | – | ✓ |
| Suspend / Hapus Akun | – | – | ✓ |

> Catatan: Seorang pengguna dapat mendaftar sebagai Borrower dan juga mengaktifkan fitur Owner (seperti model Tokopedia/Shopee). Role Admin ditentukan secara internal oleh tim SI-RENT.

---

## 6. FITUR & KEBUTUHAN FUNGSIONAL

### 6.0 Halaman Publik & Umum

Halaman-halaman berikut dapat diakses oleh siapa saja (guest maupun pengguna yang sudah login) tanpa role spesifik.

| ID | Fitur | Deskripsi |
|---|---|---|
| F-PUB-01 | Home (Guest) | Landing page untuk pengunjung yang belum login. Menampilkan value proposition platform, kategori populer, dan produk unggulan, dengan CTA untuk Login/Register. |
| F-PUB-02 | Home (Logged-in) | Versi Home setelah login: menampilkan rekomendasi personalisasi, status akun (verified/belum), dan akses cepat ke Dashboard. |
| F-PUB-03 | About Us | Halaman informasi tentang platform SI-RENT: visi misi, cara kerja platform, dan informasi kontak/tim. |

### 6.1 Autentikasi & Manajemen Akun (Semua Role)

Login dan Register **TIDAK** menggunakan halaman terpisah (bukan `/login` atau `/register` sebagai full page). Kedua fitur ditampilkan sebagai modal / floating card yang muncul di atas halaman aktif (overlay), sehingga pengguna dapat tetap berada pada konteks halaman yang sedang dilihat (misalnya halaman detail produk) tanpa kehilangan posisi atau perlu reload halaman.

> Referensi desain Login dan Register sudah tersedia di Figma — lihat tabel mapping pada section 17.3 (frame 'Register' dan 'Login'). Slicing modal ini WAJIB mengacu pada kedua node tersebut via Figma MCP, bukan didesain bebas oleh AI.

**Mekanisme Modal Login/Register:**
- Modal dipicu oleh tombol 'Masuk' / 'Daftar' pada navbar, atau otomatis muncul ketika guest mencoba melakukan aksi yang membutuhkan login (contoh: klik 'Ajukan Peminjaman' atau 'Mulai Chat').
- Modal dibangun menggunakan Bootstrap Modal component yang disempurnakan dengan Alpine.js untuk reaktivitas (switch antara form Login dan Register tanpa reload).
- Submit form dilakukan secara AJAX/Fetch (tanpa redirect). Jika berhasil, modal tertutup otomatis dan halaman ter-update (misalnya navbar berubah menampilkan nama user) tanpa full page reload.
- Jika validasi gagal (email sudah terdaftar, password salah, dsb), pesan error ditampilkan langsung di dalam modal tanpa menutup modal.
- Setelah login sukses melalui modal yang dipicu oleh sebuah aksi (contoh: klik 'Ajukan Peminjaman'), sistem akan otomatis melanjutkan aksi tersebut (intended action) tanpa pengguna perlu mengulang klik.
- Tersedia tautan 'Lupa Password' di dalam modal Login yang membuka sub-state form reset password di dalam modal yang sama (tanpa pindah halaman).
- Modal mendukung tombol close (X), klik di luar area modal, dan tombol ESC untuk menutup tanpa submit.

| ID | Fitur | Deskripsi |
|---|---|---|
| F-AUTH-01 | Registrasi (Floating Card) | Form registrasi muncul sebagai card mengambang/modal di atas halaman aktif. Input: email, nama, nomor HP, password. Submit via AJAX tanpa reload. |
| F-AUTH-02 | Login (Floating Card) | Form login muncul sebagai card mengambang di atas halaman aktif. Input: email, password. Submit via AJAX, sesi langsung aktif tanpa redirect ke halaman lain. |
| F-AUTH-03 | Switch Login <-> Register | Di dalam modal yang sama, pengguna dapat beralih antara tab/state Login dan Register tanpa menutup modal atau pindah halaman. |
| F-AUTH-04 | Lupa Password | Sub-state di dalam modal Login. Reset password melalui tautan yang dikirim ke email. |
| F-AUTH-05 | Verifikasi Email | Email verifikasi dikirim saat registrasi. Akun aktif setelah email dikonfirmasi. |
| F-AUTH-06 | Edit Profil | Pengguna dapat mengubah nama, foto profil, nomor HP, dan bio singkat (halaman tersendiri, bukan modal). |
| F-AUTH-07 | Upload KTP/Identitas | Pengguna dapat mengunggah foto KTP untuk proses verifikasi oleh admin (halaman tersendiri). |
| F-AUTH-08 | Kelola Password | Pengguna dapat mengubah password dari halaman akun (halaman tersendiri, bukan modal). |

### 6.2 Fitur Borrower

> Catatan struktur halaman: berdasarkan desain Figma, area borrower terbagi menjadi 4 halaman terpisah — Dashboard (ringkasan), Activity Ongoing (transaksi yang sedang berjalan), History (riwayat transaksi selesai/dibatalkan), dan Settings (kelola akun) — bukan satu halaman gabungan. Halaman profil owner publik disebut 'Store' (bukan 'owner profile'), terdiri dari 3 tab: Produk, About, dan Reviews.

| ID | Fitur | Deskripsi |
|---|---|---|
| F-BRW-01 | Browse Produk (Home) | Halaman Home menampilkan daftar produk sewa dengan filter kategori, lokasi, harga, dan ketersediaan. Tampilan berbeda antara guest (Home logged-out) dan user yang sudah login (Home logged-in, menampilkan rekomendasi personalisasi & status akun). |
| F-BRW-02 | Pencarian Produk | Search bar dengan autocomplete pada halaman Home. Filter lanjutan: harga per hari, rating owner, kondisi barang. |
| F-BRW-03 | Detail Produk | Halaman detail (slug produk) menampilkan foto barang, deskripsi, spesifikasi, harga/hari, lokasi, dan ringkasan toko owner beserta rating-nya, dengan tautan menuju halaman Store penuh. |
| F-BRW-04 | Store (Toko Owner) | Halaman publik profil owner dengan 3 tab: Produk (daftar listing aktif), About (deskripsi & info toko), dan Reviews (daftar rating & ulasan yang diterima owner). |
| F-BRW-05 | Ajukan Peminjaman | Borrower memilih tanggal mulai dan selesai, mengisi catatan, lalu mengirim permintaan ke owner dari halaman detail produk. |
| F-BRW-06 | Dashboard Borrower | Halaman ringkasan akun borrower: jumlah transaksi aktif, jumlah selesai, notifikasi terbaru, dan akses cepat ke Activity Ongoing & History. |
| F-BRW-07 | Activity Ongoing | Halaman khusus menampilkan transaksi peminjaman dengan status pending, approved, dan ongoing (yang masih berjalan/menunggu proses). |
| F-BRW-08 | History | Halaman khusus menampilkan riwayat transaksi yang sudah completed atau cancelled/rejected, beserta tautan untuk memberi rating jika belum dinilai. |
| F-BRW-09 | Settings | Halaman kelola akun: edit profil, ubah password, upload dokumen identitas (KTP), dan preferensi notifikasi. |
| F-BRW-10 | Beri Rating & Ulasan | Dari halaman History, setelah sewa selesai, borrower dapat memberikan rating bintang (1–5) dan ulasan teks kepada owner. |

### 6.3 Fitur Owner

| ID | Fitur | Deskripsi |
|---|---|---|
| F-OWN-01 | Tambah Listing Barang | Owner membuat listing baru dengan nama, kategori, deskripsi, foto (maks. 5 foto), kondisi, harga/hari, dan lokasi. |
| F-OWN-02 | Edit & Hapus Listing | Owner dapat mengubah informasi barang atau menonaktifkan/menghapus listing. |
| F-OWN-03 | Atur Ketersediaan | Owner dapat memblokir tanggal tertentu (barang tidak tersedia) menggunakan kalender ketersediaan. |
| F-OWN-04 | Kelola Permintaan | Owner menerima notifikasi permintaan baru dan dapat menerima atau menolak dengan alasan. |
| F-OWN-05 | Riwayat Penyewaan | Daftar semua transaksi yang masuk beserta status dan detail borrower. |
| F-OWN-06 | Dashboard Pendapatan | Tampilan ringkasan penghasilan bulanan, grafik tren peminjaman, dan daftar transaksi terbaru. |
| F-OWN-07 | Beri Rating Borrower | Setelah sewa selesai, owner dapat memberikan rating (1–5) dan ulasan kepada borrower. |

> **Catatan: Belum ada referensi desain Figma untuk fitur Owner — lihat section 4.3 (Scope Fase 1).**

### 6.4 Fitur Admin

| ID | Fitur | Deskripsi |
|---|---|---|
| F-ADM-01 | Login Admin | Login dengan kredensial admin khusus. Akses panel admin terpisah dari antarmuka user. |
| F-ADM-02 | Verifikasi Identitas | Admin meninjau KTP/identitas yang diunggah user dan memberikan status: Terverifikasi atau Ditolak dengan catatan. |
| F-ADM-03 | Monitoring Transaksi | Admin dapat melihat semua transaksi yang terjadi di platform beserta statusnya. |
| F-ADM-04 | Monitoring Pengguna | Daftar semua pengguna dengan status akun, status verifikasi, dan histori aktivitas. |
| F-ADM-05 | Penanganan Dispute | Admin dapat menerima laporan dispute, meninjau riwayat chat dan transaksi, serta memberikan resolusi. |
| F-ADM-06 | Suspend Akun | Admin dapat menangguhkan akun sementara dengan alasan dan durasi yang ditentukan. |
| F-ADM-07 | Hapus Akun | Admin dapat menghapus akun yang melanggar kebijakan secara permanen setelah konfirmasi. |
| F-ADM-08 | Kelola Kategori | Admin dapat menambah, mengedit, atau menghapus kategori produk yang tersedia di platform. |

> **Catatan: Belum ada referensi desain Figma untuk Panel Admin — lihat section 4.3 (Scope Fase 1).**

### 6.5 Fitur Chat

Fitur chat berfungsi sebagai sarana komunikasi langsung antara borrower dan owner untuk koordinasi serah terima barang, negosiasi, dan klarifikasi kondisi barang.

| ID | Fitur | Deskripsi |
|---|---|---|
| F-CHT-01 | Inisiasi Chat | Chat dapat dimulai oleh borrower dari halaman detail produk atau halaman status peminjaman. |
| F-CHT-02 | Pesan Real-Time | Pesan terkirim dan diterima secara real-time menggunakan WebSocket (Laravel Echo + Pusher/Soketi). |
| F-CHT-03 | Notifikasi Pesan | Notifikasi pesan baru muncul di ikon chat pada navbar tanpa perlu refresh halaman. |
| F-CHT-04 | Riwayat Chat | Semua percakapan tersimpan dan dapat diakses kembali dari halaman Pesan. |
| F-CHT-05 | Lampiran Foto | Pengguna dapat mengirimkan foto dalam chat (kondisi barang, bukti serah terima, dll.). |

---

## 7. ALUR PROSES UTAMA

### 7.1 Alur Peminjaman Barang

1. Borrower melakukan pencarian produk berdasarkan kategori, kata kunci, atau lokasi.
2. Borrower membuka halaman detail produk dan memeriksa ketersediaan pada kalender.
3. Borrower mengirimkan permintaan peminjaman dengan memilih tanggal dan mengisi catatan opsional.
4. Owner menerima notifikasi permintaan baru dan meninjau detail borrower (termasuk rating).
5. Owner menyetujui atau menolak permintaan. Jika disetujui, status berubah menjadi 'Disetujui'.
6. Borrower dan Owner berkomunikasi via chat untuk menentukan waktu dan tempat serah terima.
7. Serah terima barang dilakukan secara langsung (offline).
8. Pada tanggal selesai, status otomatis berubah menjadi 'Selesai' atau dapat dikonfirmasi manual.
9. Kedua pihak memberikan rating dan ulasan satu sama lain.

### 7.2 Alur Pendaftaran & Verifikasi

1. Pengguna mengklik tombol 'Daftar' pada navbar — sebuah modal/floating card muncul di atas halaman aktif tanpa pindah URL.
2. Pengguna mengisi form registrasi (email, nama, nomor HP, password) dan submit secara AJAX.
3. Sistem mengirimkan email verifikasi. Modal menampilkan pesan sukses dan otomatis tertutup atau beralih ke state 'Cek Email Anda'.
4. Pengguna mengkonfirmasi email melalui tautan yang dikirim, lalu kembali ke aplikasi (sesi sudah aktif sejak registrasi).
5. Pengguna dapat langsung menggunakan fitur borrower (browse dan ajukan sewa) walau di halaman manapun, karena tidak pernah meninggalkan konteks halaman saat mendaftar.
6. Untuk menjadi owner atau untuk meningkatkan kepercayaan, pengguna mengunggah identitas (KTP) melalui halaman akun (bukan modal, karena melibatkan upload file).
7. Admin meninjau dokumen identitas dan memberikan status 'Terverifikasi'.
8. Akun yang telah terverifikasi mendapatkan badge khusus pada profil.

### 7.3 Alur Dispute

1. Salah satu pihak (borrower atau owner) mengajukan laporan dispute melalui halaman transaksi.
2. Sistem mengirimkan notifikasi ke admin tentang adanya dispute baru.
3. Admin meninjau riwayat chat, detail transaksi, dan dokumen terkait.
4. Admin menghubungi kedua pihak jika diperlukan informasi tambahan.
5. Admin memberikan resolusi: mediasi, pengembalian deposit, atau sanksi akun.

---

## 8. KEBUTUHAN NON-FUNGSIONAL

| Kategori | Kebutuhan | Target / Standar |
|---|---|---|
| Performa | Waktu muat halaman utama | < 3 detik pada koneksi 4G |
| Performa | Response time API | < 500ms untuk 95% request |
| Skalabilitas | Kapasitas pengguna bersamaan | Mendukung 500 concurrent users pada fase awal |
| Keamanan | Autentikasi | Laravel Sanctum + CSRF Protection + Rate Limiting |
| Keamanan | Data sensitif (KTP) | Disimpan terenkripsi, hanya diakses admin |
| Keamanan | Proteksi XSS & SQL Injection | Wajib menggunakan Eloquent ORM & Blade escaping |
| Availability | Uptime target | 99.5% per bulan |
| UX | Responsivitas | Tampilan optimal di desktop, tablet, dan mobile (Bootstrap 5) |
| Maintainability | Kode | Mengikuti PSR-12 coding standard, dokumentasi terdapat di README |
| SEO | Halaman listing produk | URL yang bersih (slug), meta tag dinamis |

---

## 9. TECH STACK & REKOMENDASI LIBRARY

### 9.1 Core Stack

| Layer | Teknologi | Versi | Keterangan |
|---|---|---|---|
| Backend | Laravel | 12.x | Framework PHP utama |
| Frontend | Bootstrap | 5.3 | CSS framework utama |
| Database | MySQL | 8.0+ | Relational database utama |
| Runtime | PHP | 8.2+ | Versi PHP minimum |
| Asset | Vite | 5.x | Build tool untuk asset bundling (default Laravel) |

### 9.2 Rekomendasi Library Backend (Laravel/PHP)

| Library | Kegunaan | Alasan Rekomendasi |
|---|---|---|
| Laravel Sanctum | Autentikasi API & session | Ringan, sudah terintegrasi dengan Laravel, cocok untuk SPA hybrid |
| Laravel Echo + Pusher / Soketi | Real-time WebSocket untuk fitur chat | Soketi adalah self-hosted open-source Pusher, menghindari biaya langganan |
| Spatie Laravel Permission | Manajemen Role & Permission (RBAC) | Package terpopuler untuk multi-role, mudah diintegrasikan dengan model User |
| Spatie Laravel Media Library | Upload & manajemen file (foto produk, KTP) | Mendukung konversi gambar, multiple collections, integrasi disk Laravel |
| Laravel Telescope | Debugging & monitoring (development) | Memantau query, job, request, dan log secara visual di environment dev |
| barryvdh/laravel-dompdf | Generate PDF (invoice/laporan) | Menghasilkan PDF dari view Blade, berguna untuk laporan transaksi owner |
| Laravel Horizon | Monitoring queue (untuk notifikasi email & real-time) | Dashboard visual untuk Redis queue jobs |
| Carbon (built-in) | Manipulasi tanggal untuk kalender ketersediaan | Sudah ada di Laravel, sangat powerful untuk logic tanggal sewa |

### 9.3 Rekomendasi Library Frontend (Bootstrap + JS)

| Library | Kegunaan | Alasan Rekomendasi |
|---|---|---|
| Alpine.js | Reaktivitas UI ringan (dropdown, modal, toggle) | Sangat ringan (15kb), sintaks deklaratif di HTML, cocok dipadukan Bootstrap |
| Flatpickr | Date range picker untuk kalender peminjaman | Ringan, cantik, mendukung range selection & blackout dates (tanggal tidak tersedia) |
| Swiper.js | Slider/carousel foto produk | Touch-friendly, performa tinggi, tampilan profesional untuk galeri produk |
| Chart.js | Grafik dashboard penghasilan owner | Mudah diintegrasikan, banyak tipe chart, dokumentasi lengkap |
| Toastr.js / SweetAlert2 | Notifikasi dan konfirmasi dialog yang menarik | Pengganti alert browser yang lebih cantik untuk UX yang lebih baik |
| Select2 | Dropdown dengan search (filter kategori, lokasi) | UX jauh lebih baik dari select biasa, mendukung AJAX untuk data besar |
| DataTables | Tabel admin yang sortable, searchable, paginated | Sangat cocok untuk panel admin monitoring transaksi dan pengguna |
| Dropzone.js | Drag & drop upload foto produk / KTP | UX upload yang modern dengan preview foto sebelum submit |

---

## 10. STRUKTUR FOLDER PROYEK

Struktur folder berikut **WAJIB** diikuti oleh AI coding assistant / tools vibe coding agar konsisten antar sesi generate dan tidak menciptakan struktur baru yang berbeda setiap kali.

**app/**
- `Http/Controllers/Auth/` → AuthController.php (handle AJAX login/register/logout)
- `Http/Controllers/Borrower/` → ProductController, RentalController, RatingController
- `Http/Controllers/Owner/` → DashboardController, ProductController, RentalRequestController, RatingController
- `Http/Controllers/Admin/` → DashboardController, UserController, TransactionController, DisputeController, CategoryController
- `Http/Controllers/ChatController.php`
- `Http/Middleware/` → CheckRole.php (cek role borrower/owner/admin)
- `Http/Requests/` → StoreProductRequest, StoreRentalRequest, RegisterRequest, LoginRequest, dll (Form Request untuk validasi)
- `Models/` → User, Product, ProductImage, ProductAvailability, Category, RentalRequest, Rating, Conversation, Message, Dispute, Notification
- `Policies/` → ProductPolicy, RentalRequestPolicy, DisputePolicy (Laravel Authorization)
- `Events/` → MessageSent.php (broadcast event untuk chat real-time)
- `Notifications/` → RentalRequestCreated, RentalRequestApproved, RentalRequestRejected, NewMessageReceived

**resources/views/**
- `layouts/app.blade.php` → layout utama (navbar, footer, slot modal login/register)
- `layouts/admin.blade.php` → layout khusus panel admin (sidebar berbeda)
- `components/auth-modal.blade.php` → Blade component untuk modal login/register (dipakai di semua halaman via layout)
- `home.blade.php`, `products/index.blade.php`, `products/show.blade.php`
- `borrower/rentals/index.blade.php`, `borrower/rentals/show.blade.php`
- `owner/dashboard.blade.php`, `owner/products/{index,create,edit}.blade.php`, `owner/requests/index.blade.php`
- `admin/dashboard.blade.php`, `admin/users/index.blade.php`, `admin/disputes/{index,show}.blade.php`
- `chat/index.blade.php`, `chat/show.blade.php`

**resources/js/**
- `bootstrap.js` → konfigurasi Laravel Echo + Pusher/Soketi
- `auth-modal.js` → logic Alpine.js untuk modal login/register (switch state, AJAX submit, error handling)
- `chat.js` → logic real-time chat (listen channel, render pesan baru)
- `availability-calendar.js` → integrasi Flatpickr untuk kalender ketersediaan produk

**database/**
- `migrations/` → urutan file sesuai dependency FK (lihat section 11.4)
- `seeders/` → CategorySeeder, UserSeeder (dummy borrower/owner/admin), ProductSeeder
- `factories/` → UserFactory, ProductFactory, RentalRequestFactory (untuk testing & seeding dummy data)

**routes/**
- `web.php` → seluruh route halaman & AJAX auth (lihat section 16 — Daftar Route Aplikasi)
- `channels.php` → definisi broadcast channel untuk chat (private channel per conversation)

File konfigurasi tambahan yang wajib ada: `.env.example` (lihat section 10.1), `config/broadcasting.php` (driver pusher/soketi).

### 10.1 Environment Variables (.env) yang Dibutuhkan

| Key | Contoh Nilai | Keterangan |
|---|---|---|
| APP_NAME | SI-RENT | Nama aplikasi |
| APP_URL | http://localhost:8000 | Base URL aplikasi |
| DB_CONNECTION | mysql | Driver database |
| DB_DATABASE | sirent_db | Nama database MySQL |
| MAIL_MAILER | smtp | Driver email untuk verifikasi & notifikasi |
| BROADCAST_DRIVER | pusher | Driver broadcasting untuk chat real-time |
| PUSHER_APP_KEY / PUSHER_APP_SECRET / PUSHER_APP_ID | (diisi sesuai akun Pusher/Soketi) | Kredensial WebSocket |
| FILESYSTEM_DISK | public (untuk foto produk), local (untuk KTP) | Disk storage berbeda untuk data publik vs privat |

### 10.2 Daftar Komponen Reusable Wajib (Blade Component)

Elemen UI berikut **MUNCUL BERULANG** di lebih dari satu halaman. AI coding assistant **WAJIB** mengekstraknya sebagai Blade component pada path yang sudah ditentukan berikut — **TIDAK BOLEH** menulis ulang markup yang sama langsung di tiap file halaman. Jika saat slicing sebuah halaman ditemukan elemen yang cocok dengan tabel ini, gunakan/perbarui component yang sudah ada, jangan buat duplikat baru.

| Component | Path File | Props / Slot Utama | Dipakai di Halaman |
|---|---|---|---|
| Navbar | `components/layout/navbar.blade.php` | isLoggedIn, user (nullable) | Semua halaman (via layouts/app.blade.php). Tampilan berbeda guest vs logged-in (lihat F-PUB-01/02). |
| Footer | `components/layout/footer.blade.php` | (statis, tanpa props) | Semua halaman (via layouts/app.blade.php). |
| Modal Auth (Login/Register) | `components/auth-modal.blade.php` | mode (login/register), redirectIntent (nullable) | Semua halaman (dipanggil dari navbar atau trigger aksi guest). |
| Card Produk | `components/product/product-card.blade.php` | product (object: title, image, price_per_day, rating_avg, owner_name) | Home, Browse Produk, Kategori, Store (tab Produk), Dashboard (rekomendasi). |
| Badge Rating | `components/shared/rating-badge.blade.php` | score (decimal), totalReviews (int) | Card Produk, Detail Produk, Store, History (saat tampil ulasan). |
| Badge Status Transaksi | `components/shared/status-badge.blade.php` | status (enum dari section 12) | Dashboard, Activity Ongoing, History. |
| Badge Verified | `components/shared/verified-badge.blade.php` | isVerified (boolean) | Store, Card Produk, Detail Produk, Chat (info lawan bicara). |
| Avatar User | `components/shared/avatar.blade.php` | imagePath (nullable), name (fallback inisial), size (sm/md/lg) | Navbar, Chat, Store, Settings, History (ulasan). |
| Date Range Picker | `components/product/date-range-picker.blade.php` | blockedDates (array), productId | Detail Produk (form ajukan peminjaman). |
| Tab Switcher Store | `components/store/store-tabs.blade.php` | activeTab (produk/about/reviews), username | Store, Store (About), Store (Reviews) — satu component dipakai di ketiga state tab. |
| Empty State | `components/shared/empty-state.blade.php` | message, iconType (opsional) | Activity Ongoing (jika kosong), History (jika kosong), Chat (jika belum ada percakapan). |
| Pagination | `components/shared/pagination.blade.php` | paginator (Laravel paginator instance) | Browse Produk, Store (tab Produk), History, Store (Reviews). |

> Catatan implementasi: gunakan fitur native Laravel Blade Component (`php artisan make:component`) untuk komponen yang membutuhkan logic class (contoh: Status Badge perlu method untuk mapping enum ke label & warna), dan Anonymous Component (cukup file `.blade.php` tanpa class) untuk komponen murni tampilan seperti Footer dan Empty State.

---

## 11. SKEMA DATABASE (DETAIL)

Skema berikut bersifat final/wajib diikuti apa adanya oleh AI coding assistant — termasuk nama tabel, nama kolom, tipe data, dan relasi — agar tidak terjadi penamaan yang berbeda-beda pada saat generate migration, model, atau query di sesi yang berlainan.

### 11.1 Tabel: users

| Kolom | Tipe Data | Constraint | Keterangan |
|---|---|---|---|
| id | BIGINT | PK, auto_increment | Primary key |
| name | VARCHAR(100) | NOT NULL | Nama lengkap pengguna |
| email | VARCHAR(150) | NOT NULL, UNIQUE | Email login |
| email_verified_at | TIMESTAMP | NULLABLE | Diisi setelah verifikasi email |
| password | VARCHAR(255) | NOT NULL | Hash bcrypt |
| phone | VARCHAR(20) | NOT NULL | Nomor HP aktif |
| avatar | VARCHAR(255) | NULLABLE | Path foto profil |
| bio | TEXT | NULLABLE | Bio singkat pengguna |
| role | ENUM('borrower','owner','admin') | NOT NULL, DEFAULT 'borrower' | Role utama. Lihat catatan di bawah |
| is_owner_active | BOOLEAN | DEFAULT FALSE | TRUE jika user telah mengaktifkan fitur owner (dual role) |
| identity_doc | VARCHAR(255) | NULLABLE | Path file KTP (disk private) |
| verification_status | ENUM('unverified','pending','verified','rejected') | DEFAULT 'unverified' | Status verifikasi identitas oleh admin |
| verification_note | TEXT | NULLABLE | Catatan admin jika rejected |
| rating_avg_as_borrower | DECIMAL(3,2) | DEFAULT 0.00 | Rata-rata rating diterima sebagai borrower |
| rating_avg_as_owner | DECIMAL(3,2) | DEFAULT 0.00 | Rata-rata rating diterima sebagai owner |
| account_status | ENUM('active','suspended','banned') | DEFAULT 'active' | Status akun (dikontrol admin) |
| suspended_until | TIMESTAMP | NULLABLE | Tanggal akhir suspend jika account_status = suspended |
| remember_token | VARCHAR(100) | NULLABLE | Token remember me Laravel |
| created_at, updated_at | TIMESTAMP | NOT NULL | Laravel timestamps |

> **Catatan penting role:** Kolom `role` pada dasarnya menyimpan 'borrower' atau 'admin'. Setiap user yang register otomatis berperan sebagai borrower. Untuk menjadi owner, user TIDAK pindah role melainkan mengaktifkan `is_owner_active = TRUE` (mirip model dual-role Tokopedia/Shopee, satu akun bisa jadi pembeli & penjual). Role 'admin' hanya diset manual melalui seeder/database, tidak ada flow registrasi admin dari frontend.

### 11.2 Tabel Lainnya

**categories**

| Kolom | Tipe Data | Constraint | Keterangan |
|---|---|---|---|
| id | BIGINT | PK, auto_increment | Primary key |
| name | VARCHAR(100) | NOT NULL | Nama kategori (Kamera, Drone, dll) |
| slug | VARCHAR(120) | NOT NULL, UNIQUE | Slug untuk URL |
| icon | VARCHAR(100) | NULLABLE | Class icon (contoh: bi-camera untuk Bootstrap Icons) |
| created_at, updated_at | TIMESTAMP | NOT NULL | |

**products**

| Kolom | Tipe Data | Constraint | Keterangan |
|---|---|---|---|
| id | BIGINT | PK, auto_increment | Primary key |
| owner_id | BIGINT | FK → users.id, NOT NULL | Pemilik barang |
| category_id | BIGINT | FK → categories.id, NOT NULL | Kategori barang |
| title | VARCHAR(150) | NOT NULL | Nama produk |
| slug | VARCHAR(180) | NOT NULL, UNIQUE | Slug untuk URL detail produk |
| description | TEXT | NOT NULL | Deskripsi lengkap barang |
| condition | ENUM('new','like_new','good','fair') | NOT NULL | Kondisi barang |
| price_per_day | DECIMAL(12,2) | NOT NULL | Harga sewa per hari (Rupiah) |
| deposit_amount | DECIMAL(12,2) | DEFAULT 0.00 | Nominal deposit/jaminan (jika ada) |
| location_city | VARCHAR(100) | NOT NULL | Kota lokasi barang untuk filter & serah terima |
| location_detail | VARCHAR(255) | NULLABLE | Detail lokasi (tidak ditampilkan publik, hanya saat chat disetujui) |
| status | ENUM('active','inactive','draft') | DEFAULT 'draft' | active = tampil publik, inactive = disembunyikan owner, draft = belum dipublikasikan |
| rating_avg | DECIMAL(3,2) | DEFAULT 0.00 | Rata-rata rating produk |
| total_rented | INT | DEFAULT 0 | Jumlah berapa kali barang sudah disewa (counter) |
| created_at, updated_at | TIMESTAMP | NOT NULL | |

**product_images**

| Kolom | Tipe Data | Constraint | Keterangan |
|---|---|---|---|
| id | BIGINT | PK, auto_increment | Primary key |
| product_id | BIGINT | FK → products.id, NOT NULL, ON DELETE CASCADE | Relasi ke produk |
| image_path | VARCHAR(255) | NOT NULL | Path file gambar (disk public) |
| is_primary | BOOLEAN | DEFAULT FALSE | Foto utama yang ditampilkan di listing/card |
| sort_order | INT | DEFAULT 0 | Urutan tampil galeri foto |
| created_at, updated_at | TIMESTAMP | NOT NULL | |

**product_availabilities**

| Kolom | Tipe Data | Constraint | Keterangan |
|---|---|---|---|
| id | BIGINT | PK, auto_increment | Primary key |
| product_id | BIGINT | FK → products.id, NOT NULL, ON DELETE CASCADE | Relasi ke produk |
| blocked_date | DATE | NOT NULL | Tanggal di mana barang tidak tersedia disewa |
| reason | VARCHAR(255) | NULLABLE | Alasan opsional (maintenance, dipakai pribadi, dll) |
| created_at, updated_at | TIMESTAMP | NOT NULL | |

**rental_requests**

| Kolom | Tipe Data | Constraint | Keterangan |
|---|---|---|---|
| id | BIGINT | PK, auto_increment | Primary key |
| borrower_id | BIGINT | FK → users.id, NOT NULL | Peminjam |
| product_id | BIGINT | FK → products.id, NOT NULL | Barang yang diajukan |
| owner_id | BIGINT | FK → users.id, NOT NULL | Denormalisasi owner_id untuk query cepat |
| start_date | DATE | NOT NULL | Tanggal mulai sewa |
| end_date | DATE | NOT NULL | Tanggal selesai sewa |
| total_days | INT | NOT NULL | Hasil hitung (end_date - start_date + 1) |
| total_price | DECIMAL(12,2) | NOT NULL | total_days * price_per_day saat pengajuan |
| notes | TEXT | NULLABLE | Catatan dari borrower saat mengajukan |
| rejection_reason | TEXT | NULLABLE | Alasan owner jika status rejected |
| status | ENUM('pending','approved','rejected','ongoing','completed','cancelled') | DEFAULT 'pending' | Lihat detail enum di section 12 |
| approved_at | TIMESTAMP | NULLABLE | Waktu owner menyetujui |
| completed_at | TIMESTAMP | NULLABLE | Waktu transaksi ditandai selesai |
| created_at, updated_at | TIMESTAMP | NOT NULL | |

**ratings**

| Kolom | Tipe Data | Constraint | Keterangan |
|---|---|---|---|
| id | BIGINT | PK, auto_increment | Primary key |
| rental_request_id | BIGINT | FK → rental_requests.id, NOT NULL | Transaksi terkait |
| rater_id | BIGINT | FK → users.id, NOT NULL | Pengguna yang memberi rating |
| ratee_id | BIGINT | FK → users.id, NOT NULL | Pengguna yang dinilai |
| type | ENUM('to_owner','to_borrower') | NOT NULL | Arah rating |
| score | TINYINT | NOT NULL, CHECK 1-5 | Nilai bintang 1 sampai 5 |
| review | TEXT | NULLABLE | Ulasan teks opsional |
| created_at, updated_at | TIMESTAMP | NOT NULL | |

> Constraint tambahan: UNIQUE (rental_request_id, rater_id, type) — mencegah pengguna memberi rating ganda untuk transaksi & arah yang sama.

**conversations**

| Kolom | Tipe Data | Constraint | Keterangan |
|---|---|---|---|
| id | BIGINT | PK, auto_increment | Primary key |
| borrower_id | BIGINT | FK → users.id, NOT NULL | Pihak borrower dalam percakapan |
| owner_id | BIGINT | FK → users.id, NOT NULL | Pihak owner dalam percakapan |
| product_id | BIGINT | FK → products.id, NULLABLE | Produk konteks percakapan |
| last_message_at | TIMESTAMP | NULLABLE | Untuk sorting daftar percakapan |
| created_at, updated_at | TIMESTAMP | NOT NULL | |

> Constraint tambahan: UNIQUE (borrower_id, owner_id, product_id) — satu percakapan per kombinasi borrower-owner-produk.

**messages**

| Kolom | Tipe Data | Constraint | Keterangan |
|---|---|---|---|
| id | BIGINT | PK, auto_increment | Primary key |
| conversation_id | BIGINT | FK → conversations.id, NOT NULL, ON DELETE CASCADE | Relasi percakapan |
| sender_id | BIGINT | FK → users.id, NOT NULL | Pengirim pesan |
| body | TEXT | NULLABLE | Isi pesan teks (nullable jika hanya kirim lampiran) |
| attachment | VARCHAR(255) | NULLABLE | Path file lampiran foto |
| is_read | BOOLEAN | DEFAULT FALSE | Status sudah dibaca penerima |
| created_at, updated_at | TIMESTAMP | NOT NULL | |

**disputes**

| Kolom | Tipe Data | Constraint | Keterangan |
|---|---|---|---|
| id | BIGINT | PK, auto_increment | Primary key |
| rental_request_id | BIGINT | FK → rental_requests.id, NOT NULL | Transaksi yang dipermasalahkan |
| reporter_id | BIGINT | FK → users.id, NOT NULL | Pengguna yang melapor |
| reason | TEXT | NOT NULL | Alasan/penjelasan dispute |
| evidence | VARCHAR(255) | NULLABLE | Path file bukti (foto/dokumen) |
| status | ENUM('open','in_review','resolved','rejected') | DEFAULT 'open' | Lihat detail enum di section 12 |
| resolution | TEXT | NULLABLE | Keputusan/penjelasan admin |
| handled_by | BIGINT | FK → users.id, NULLABLE | Admin yang menangani |
| resolved_at | TIMESTAMP | NULLABLE | Waktu dispute ditutup |
| created_at, updated_at | TIMESTAMP | NOT NULL | |

**notifications**

| Kolom | Tipe Data | Constraint | Keterangan |
|---|---|---|---|
| id (BIGINT / UUID) | PK | | Primary key (default Laravel notifications table) |
| user_id (notifiable_id) | BIGINT | FK → users.id, NOT NULL | Penerima notifikasi |
| type | VARCHAR(255) | NOT NULL | Class notifikasi |
| data | JSON | NOT NULL | Payload notifikasi |
| read_at | TIMESTAMP | NULLABLE | Waktu dibaca, null jika belum dibaca |
| created_at, updated_at | TIMESTAMP | NOT NULL | |

> Catatan: Tabel notifications mengikuti struktur default Laravel Notification (`php artisan notifications:table`), tidak perlu dibuat manual.

### 11.3 Relasi Antar Tabel (Entity Relationship)

- users 1—N products (satu owner punya banyak produk, via owner_id)
- users 1—N rental_requests sebagai borrower (via borrower_id)
- users 1—N rental_requests sebagai owner (via owner_id)
- categories 1—N products (via category_id)
- products 1—N product_images (via product_id, cascade delete)
- products 1—N product_availabilities (via product_id, cascade delete)
- products 1—N rental_requests (via product_id)
- rental_requests 1—N ratings (maksimal 2 rating per transaksi: to_owner & to_borrower)
- users 1—N conversations sebagai borrower, users 1—N conversations sebagai owner
- conversations 1—N messages (via conversation_id, cascade delete)
- rental_requests 1—N disputes (via rental_request_id)
- users 1—N notifications (polymorphic notifiable, default Laravel)

### 11.4 Urutan Migration (Wajib Diikuti karena Foreign Key)

1. create_users_table
2. create_categories_table
3. create_products_table (FK ke users, categories)
4. create_product_images_table (FK ke products)
5. create_product_availabilities_table (FK ke products)
6. create_rental_requests_table (FK ke users, products)
7. create_ratings_table (FK ke rental_requests, users)
8. create_conversations_table (FK ke users, products)
9. create_messages_table (FK ke conversations, users)
10. create_disputes_table (FK ke rental_requests, users)
11. create_notifications_table (default Laravel: `php artisan notifications:table`)

---

## 12. ENUM & STATUS VALUES

Daftar berikut adalah definisi **PASTI** dari setiap nilai enum yang digunakan di sistem. AI coding assistant harus menggunakan value persis seperti ini (huruf kecil, snake_case) pada migration, model constant, dan logic — tidak boleh membuat variasi penulisan baru di tempat berbeda.

**rental_requests.status**

| Value | Label Tampilan | Keterangan |
|---|---|---|
| pending | Menunggu Konfirmasi | Baru diajukan borrower, menunggu respon owner |
| approved | Disetujui | Owner menyetujui, menunggu tanggal mulai sewa |
| rejected | Ditolak | Owner menolak permintaan (final, tidak bisa diubah) |
| ongoing | Sedang Berjalan | Tanggal hari ini berada di antara start_date dan end_date |
| completed | Selesai | Melewati end_date, kedua pihak dapat memberi rating |
| cancelled | Dibatalkan | Dibatalkan oleh borrower sebelum disetujui owner |

**products.status**

| Value | Label Tampilan | Keterangan |
|---|---|---|
| draft | Draft | Listing belum dipublikasikan, hanya terlihat owner |
| active | Aktif | Tampil publik dan dapat disewa |
| inactive | Nonaktif | Disembunyikan sementara oleh owner (tidak dihapus) |

**users.verification_status**

| Value | Label Tampilan | Keterangan |
|---|---|---|
| unverified | Belum Verifikasi | Belum mengunggah dokumen identitas |
| pending | Menunggu Review | Dokumen sudah diunggah, menunggu admin |
| verified | Terverifikasi | Disetujui admin, mendapat badge Verified |
| rejected | Ditolak | Dokumen ditolak admin, dapat upload ulang |

**users.account_status**

| Value | Label Tampilan | Keterangan |
|---|---|---|
| active | Aktif | Akun normal, dapat menggunakan semua fitur |
| suspended | Ditangguhkan | Sementara tidak dapat login hingga suspended_until |
| banned | Diblokir Permanen | Tidak dapat login sama sekali |

**disputes.status**

| Value | Label Tampilan | Keterangan |
|---|---|---|
| open | Baru Dilaporkan | Dispute baru masuk, belum ditangani admin manapun |
| in_review | Sedang Ditinjau | Admin sedang menyelidiki/menghubungi kedua pihak |
| resolved | Terselesaikan | Admin sudah memberikan keputusan final |
| rejected | Ditolak | Laporan dispute dianggap tidak valid |

**products.condition**

| Value | Label Tampilan |
|---|---|
| new | Baru |
| like_new | Seperti Baru |
| good | Baik |
| fair | Cukup / Ada Bekas Pakai |

---

## 13. VALIDASI FORM (LARAVEL FORM REQUEST RULES)

Aturan validasi berikut wajib diterapkan persis seperti ini pada Form Request terkait, agar tidak ada perbedaan rule antar form yang dibuat di sesi generate berbeda.

**RegisterRequest**

| Field | Rule |
|---|---|
| name | required\|string\|max:100 |
| email | required\|email\|max:150\|unique:users,email |
| phone | required\|string\|max:20\|regex:/^[0-9+\-\s]+$/ |
| password | required\|string\|min:8\|confirmed |

**LoginRequest**

| Field | Rule |
|---|---|
| email | required\|email |
| password | required\|string |

**StoreProductRequest (Tambah/Edit Listing)**

| Field | Rule |
|---|---|
| title | required\|string\|max:150 |
| category_id | required\|exists:categories,id |
| description | required\|string\|min:20 |
| condition | required\|in:new,like_new,good,fair |
| price_per_day | required\|numeric\|min:1000 |
| deposit_amount | nullable\|numeric\|min:0 |
| location_city | required\|string\|max:100 |
| images | required\|array\|min:1\|max:5 |
| images.* | image\|mimes:jpg,jpeg,png\|max:2048 |

**StoreRentalRequest (Ajukan Peminjaman)**

| Field | Rule |
|---|---|
| product_id | required\|exists:products,id |
| start_date | required\|date\|after_or_equal:today |
| end_date | required\|date\|after_or_equal:start_date |
| notes | nullable\|string\|max:500 |

> Validasi tambahan via custom rule/logic (bukan rule string biasa): tanggal start_date–end_date tidak boleh menabrak tanggal yang ada di `product_availabilities` (blocked) maupun `rental_requests` lain yang sudah berstatus approved/ongoing untuk produk yang sama.

**IdentityUploadRequest (Upload KTP)**

| Field | Rule |
|---|---|
| identity_doc | required\|image\|mimes:jpg,jpeg,png\|max:3072 |

**RatingRequest**

| Field | Rule |
|---|---|
| score | required\|integer\|min:1\|max:5 |
| review | nullable\|string\|max:500 |

**DisputeRequest**

| Field | Rule |
|---|---|
| rental_request_id | required\|exists:rental_requests,id |
| reason | required\|string\|min:20\|max:1000 |
| evidence | nullable\|image\|mimes:jpg,jpeg,png\|max:3072 |

**ChatMessageRequest**

| Field | Rule |
|---|---|
| body | required_without:attachment\|nullable\|string\|max:1000 |
| attachment | required_without:body\|nullable\|image\|mimes:jpg,jpeg,png\|max:2048 |

---

## 14. BUSINESS RULES & EDGE CASES

Bagian ini mendefinisikan keputusan logika bisnis secara eksplisit agar AI tidak menebak sendiri perilaku sistem pada kondisi yang ambigu.

**Peminjaman & Ketersediaan**
- Borrower tidak dapat mengajukan peminjaman untuk tanggal yang sudah dikunci oleh `blocked_date` di `product_availabilities`.
- Borrower tidak dapat mengajukan peminjaman yang tanggalnya overlap dengan `rental_requests` lain pada produk yang sama yang berstatus approved atau ongoing. Pengajuan dengan status pending dari user lain TIDAK memblokir tanggal (boleh ada multiple pending pada tanggal yang sama, owner memilih salah satu untuk disetujui).
- Ketika owner menyetujui salah satu rental_request berstatus pending, seluruh rental_request pending lain pada produk yang sama dengan tanggal overlap otomatis berubah menjadi rejected dengan rejection_reason otomatis: 'Tanggal sudah dipesan pihak lain'.
- Borrower hanya dapat membatalkan (cancelled) rental_request miliknya selama status masih pending. Setelah approved, pembatalan harus melalui chat/komunikasi langsung dengan owner (tidak ada tombol cancel otomatis untuk menghindari pembatalan sepihak yang merugikan owner).
- Status berubah dari approved menjadi ongoing secara otomatis (melalui scheduled command harian) ketika tanggal hari ini >= start_date.
- Status berubah dari ongoing menjadi completed secara otomatis ketika tanggal hari ini > end_date. completed_at diisi otomatis.

**Owner & Listing**
- User baru otomatis berperan sebagai borrower. Untuk mengaktifkan fitur owner, user mengakses halaman 'Jadi Owner' yang mengubah `is_owner_active` menjadi TRUE tanpa proses approval admin (self-service), namun listing tetap memerlukan verifikasi identitas (`verification_status = verified`) sebelum status produk dapat diubah dari draft ke active.
- Produk dengan status draft atau inactive tidak muncul di hasil pencarian/browse publik maupun di halaman kategori.
- Owner tidak dapat menghapus (hard delete) produk yang masih memiliki rental_requests berstatus pending, approved, atau ongoing. Owner hanya bisa mengubah status menjadi inactive.

**Rating**
- Rating hanya dapat diberikan jika `rental_requests.status = completed`.
- Setiap rental_request hanya dapat memiliki maksimal 1 rating dengan type=to_owner (dari borrower) dan 1 rating dengan type=to_borrower (dari owner). Sudah memberi rating tidak dapat diedit/dihapus oleh user.
- rating_avg pada tabel users dan products dihitung ulang (recalculate) setiap kali ada rating baru masuk, bukan dihitung on-the-fly setiap request (untuk performa).

**Verifikasi Identitas**
- Borrower TIDAK wajib verifikasi identitas untuk mengajukan peminjaman pertama kali, namun status verification_status ditampilkan kepada owner sebagai pertimbangan saat menerima/menolak permintaan.
- Owner WAJIB verification_status = verified sebelum dapat mempublikasikan produk apapun (status active).
- Jika admin menolak (rejected) dokumen identitas, user dapat mengunggah ulang tanpa batas, namun setiap pengajuan ulang mereset status menjadi pending.

**Dispute**
- Dispute hanya dapat diajukan untuk rental_requests dengan status ongoing atau completed, dan paling lambat 7 hari setelah completed_at (mencegah klaim yang terlalu lama setelah transaksi).
- Satu rental_request dapat memiliki lebih dari satu dispute (misalnya dilaporkan oleh kedua pihak), namun masing-masing reporter_id hanya dapat membuat 1 dispute aktif (status open/in_review) per rental_request.
- Selama dispute berstatus open atau in_review, kedua pihak terkait tidak dapat memberikan rating satu sama lain untuk rental_request tersebut (mencegah rating balas dendam sebelum kasus selesai).

**Suspend & Banned**
- User dengan account_status = suspended tidak dapat login (response API mengembalikan pesan & suspended_until). Setelah suspended_until terlewati, sistem otomatis mengembalikan status ke active saat user mencoba login (lazy check, bukan cron).
- User dengan account_status = banned tidak dapat login permanen. Listing produk milik user banned otomatis disetel inactive.

---

## 15. KEAMANAN & VERIFIKASI IDENTITAS

### 15.1 Verifikasi Identitas

- Pengguna yang ingin menjadi owner wajib mengunggah dokumen identitas (KTP/SIM/Paspor).
- Dokumen disimpan terenkripsi menggunakan Laravel Storage dengan disk private (tidak dapat diakses publik).
- Admin meninjau dokumen melalui panel khusus dan memberikan status verifikasi.
- Pengguna terverifikasi mendapatkan badge 'Verified' yang ditampilkan pada profil dan listing produk.
- Pengguna tidak terverifikasi tetap dapat browse, namun dibatasi untuk melakukan peminjaman hingga verifikasi selesai (opsional, dapat dikonfigurasi).

### 15.2 Sistem Keamanan Teknis

- CSRF Protection: Otomatis via Laravel middleware pada semua form.
- SQL Injection Prevention: Selalu menggunakan Eloquent ORM atau query builder dengan binding.
- XSS Prevention: Blade template engine menggunakan double curly braces `{{ }}` yang auto-escape.
- Rate Limiting: Membatasi percobaan login (maks. 5x/menit) menggunakan Laravel built-in throttle.
- Authorization: Setiap aksi diproteksi dengan Laravel Policy dan Gate untuk memastikan hanya role yang tepat yang dapat mengakses.
- File Upload Validation: Validasi MIME type, ukuran file, dan ekstensi pada setiap upload.

### 15.3 Sistem Rating & Kepercayaan

- Rating dua arah: Borrower menilai owner, dan owner menilai borrower setelah setiap transaksi selesai.
- Rating ditampilkan secara publik pada profil pengguna dan listing produk.
- Sistem dapat memfilter atau menyembunyikan produk dari owner dengan rating rendah (< 3.0).
- Borrower dengan rating rendah dapat ditolak secara otomatis atau diblokir oleh owner tertentu.

---

## 16. DAFTAR ROUTE APLIKASI

Berikut adalah daftar route Laravel yang digunakan pada aplikasi SI-RENT. Route untuk Login dan Register TIDAK memiliki halaman/view tersendiri (bukan GET /login atau GET /register yang menampilkan full page) karena keduanya ditampilkan sebagai modal/floating card di atas halaman aktif. Route auth hanya berupa endpoint AJAX (POST) yang dipanggil dari JavaScript saat form di dalam modal disubmit.

### 16.1 Route Autentikasi (AJAX — Tanpa Halaman Terpisah)

| Method | URI | Nama Route | Keterangan |
|---|---|---|---|
| POST | /auth/register | auth.register | Submit form register dari modal. Response JSON, tidak ada redirect. |
| POST | /auth/login | auth.login | Submit form login dari modal. Response JSON berisi status sesi & data user. |
| POST | /auth/logout | auth.logout | Logout pengguna, hapus sesi, kembalikan response JSON. |
| POST | /auth/forgot-password | auth.forgot-password | Kirim email reset password dari sub-state modal login. |
| POST | /auth/reset-password | auth.reset-password | Proses reset password (dipanggil dari halaman link email, bukan modal). |
| GET | /email/verify/{id}/{hash} | verification.verify | Endpoint verifikasi email yang diklik dari tautan email. |
| POST | /email/verification-notification | verification.send | Kirim ulang email verifikasi. |

> Catatan implementasi: Karena tidak ada halaman /login dan /register, middleware 'auth' yang menolak guest akan mengarahkan ke halaman sebelumnya (intended URL) dengan query parameter (contoh: `?auth=login`) yang dibaca oleh JavaScript untuk otomatis membuka modal login pada halaman tersebut, bukan redirect ke route halaman penuh.

### 16.2 Route Publik (Guest & Semua Role)

| Method | URI | Nama Route | Keterangan |
|---|---|---|---|
| GET | / | home | Landing page. Menampilkan view berbeda untuk guest (Home logged-out) vs user login (Home logged-in) menggunakan kondisi @auth pada Blade, tetap satu route/URL yang sama. |
| GET | /produk | products.index | Halaman browse & pencarian semua produk sewa. |
| GET | /produk/{slug} | products.show | Halaman detail produk (slug). |
| GET | /kategori/{slug} | categories.show | Daftar produk berdasarkan kategori. |
| GET | /toko/{username} | store.show | Halaman Store (toko publik) milik owner. Tab default: Produk. |
| GET | /toko/{username}/about | store.about | Tab About pada halaman Store yang sama (dapat berupa anchor/AJAX tab, tidak harus full reload). |
| GET | /toko/{username}/reviews | store.reviews | Tab Reviews pada halaman Store yang sama, menampilkan seluruh rating & ulasan owner. |
| GET | /about-us | about.index | Halaman About Us (informasi platform, visi misi, kontak). |

> Catatan implementasi Store: ketiga tab (Produk, About, Reviews) sebaiknya diimplementasikan sebagai satu Blade view dengan komponen tab Bootstrap (nav-tabs) yang memuat data tab aktif via Alpine.js/AJAX, agar perpindahan tab tidak memerlukan full page reload — konsisten dengan filosofi UX modal auth pada section 6.1.

### 16.3 Route Borrower (Middleware: auth)

> Catatan: tidak menggunakan middleware role:borrower karena setiap user terdaftar otomatis berperan sebagai borrower (lihat catatan role pada section 11.1); middleware auth saja sudah cukup.

| Method | URI | Nama Route | Keterangan |
|---|---|---|---|
| GET | /dashboard | borrower.dashboard | Halaman Dashboard: ringkasan transaksi aktif, selesai, dan notifikasi terbaru. |
| GET | /aktivitas | borrower.activity | Halaman Activity Ongoing: daftar rental_requests dengan status pending, approved, ongoing. |
| GET | /riwayat | borrower.history | Halaman History: daftar rental_requests dengan status completed, cancelled, rejected. |
| GET | /riwayat/{id} | borrower.history.show | Detail satu transaksi dari History. |
| GET | /pengaturan | borrower.settings | Halaman Settings: edit profil, ubah password, upload KTP, preferensi notifikasi. |
| PUT | /pengaturan | borrower.settings.update | Update data profil dari halaman Settings. |
| POST | /pengaturan/identitas | borrower.settings.identity | Upload dokumen KTP untuk verifikasi. |
| POST | /peminjaman | rentals.store | Mengajukan permintaan peminjaman baru dari halaman detail produk. |
| POST | /peminjaman/{id}/batal | rentals.cancel | Membatalkan permintaan peminjaman (hanya jika status masih pending). |
| POST | /peminjaman/{id}/rating | ratings.storeForOwner | Memberikan rating & ulasan kepada owner dari halaman History. |

### 16.4 Route Owner (Middleware: auth, owner.active — cek is_owner_active = TRUE)

| Method | URI | Nama Route | Keterangan |
|---|---|---|---|
| GET | /owner/dashboard | owner.dashboard | Dashboard ringkasan penghasilan & grafik tren. |
| GET | /owner/produk | owner.products.index | Daftar listing barang milik owner. |
| GET | /owner/produk/baru | owner.products.create | Form tambah listing baru. |
| POST | /owner/produk | owner.products.store | Simpan listing baru. |
| GET | /owner/produk/{id}/edit | owner.products.edit | Form edit listing. |
| PUT | /owner/produk/{id} | owner.products.update | Update data listing. |
| DELETE | /owner/produk/{id} | owner.products.destroy | Hapus / nonaktifkan listing. |
| POST | /owner/produk/{id}/ketersediaan | owner.products.availability | Atur tanggal blokir ketersediaan barang. |
| GET | /owner/permintaan | owner.requests.index | Daftar permintaan peminjaman masuk. |
| POST | /owner/permintaan/{id}/terima | owner.requests.approve | Menyetujui permintaan peminjaman. |
| POST | /owner/permintaan/{id}/tolak | owner.requests.reject | Menolak permintaan peminjaman. |
| GET | /owner/penyewaan | owner.rentals.index | Riwayat seluruh penyewaan barang owner. |
| POST | /owner/penyewaan/{id}/rating | ratings.storeForBorrower | Memberikan rating & ulasan kepada borrower. |

### 16.5 Route Chat (Middleware: auth)

| Method | URI | Nama Route | Keterangan |
|---|---|---|---|
| GET | /pesan | chat.index | Halaman daftar percakapan pengguna. |
| GET | /pesan/{conversation} | chat.show | Membuka satu percakapan beserta riwayat pesan. |
| POST | /pesan/{conversation} | chat.send | Mengirim pesan baru (dengan/tanpa lampiran foto). |
| POST | /pesan/mulai/{product} | chat.start | Memulai percakapan baru dari halaman detail produk. |

### 16.6 Route Admin (Middleware: auth, role:admin, prefix: /admin)

| Method | URI | Nama Route | Keterangan |
|---|---|---|---|
| GET | /admin/dashboard | admin.dashboard | Ringkasan statistik platform secara keseluruhan. |
| GET | /admin/pengguna | admin.users.index | Monitoring seluruh pengguna & status verifikasi. |
| GET | /admin/pengguna/{id} | admin.users.show | Detail pengguna & riwayat aktivitas. |
| POST | /admin/pengguna/{id}/verifikasi | admin.users.verify | Menyetujui/menolak dokumen identitas pengguna. |
| POST | /admin/pengguna/{id}/suspend | admin.users.suspend | Menangguhkan akun pengguna. |
| DELETE | /admin/pengguna/{id} | admin.users.destroy | Menghapus akun pengguna secara permanen. |
| GET | /admin/transaksi | admin.transactions.index | Monitoring seluruh transaksi peminjaman di platform. |
| GET | /admin/dispute | admin.disputes.index | Daftar laporan dispute yang masuk. |
| GET | /admin/dispute/{id} | admin.disputes.show | Detail satu kasus dispute beserta riwayat chat & transaksi. |
| POST | /admin/dispute/{id}/resolusi | admin.disputes.resolve | Memberikan resolusi/keputusan atas dispute. |
| GET | /admin/kategori | admin.categories.index | Kelola kategori produk. |
| POST | /admin/kategori | admin.categories.store | Tambah kategori baru. |
| PUT | /admin/kategori/{id} | admin.categories.update | Update kategori. |
| DELETE | /admin/kategori/{id} | admin.categories.destroy | Hapus kategori. |

---

## 17. REFERENSI DESAIN UI (FIGMA)

Desain UI aplikasi SI-RENT dibuat di Figma dan menjadi acuan visual utama untuk proses slicing oleh AI coding assistant (vibe coding). Tools yang digunakan (Claude Code) telah tersambung ke Figma melalui MCP (Model Context Protocol), sehingga AI dapat membaca struktur desain asli (layout, komponen, spacing, warna, varian) secara langsung dari file Figma, bukan hanya dari gambar statis.

**File Figma utama:** https://www.figma.com/design/vkelQuIuFkYIuAd8ip0fJC/Sirent

### 17.1 Instruksi untuk AI Coding Assistant (Slicing via Figma MCP)

- Sebelum membangun/slicing sebuah halaman, AI **WAJIB** mengambil node Figma yang sesuai dari tabel referensi pada section 17.3 menggunakan Figma MCP (bukan menebak desain dari deskripsi teks saja).
- AI membaca properti asli dari Figma: struktur layout (auto-layout/flex), spacing, ukuran komponen, warna (hex), tipografi (font family, size, weight), dan varian komponen (default/hover/active/disabled) — lalu menerjemahkannya ke markup Bootstrap 5 + class custom CSS yang sesuai.
- Jika sebuah komponen di Figma dipakai berulang di banyak halaman (contoh: card produk, navbar, modal login/register), AI membuat satu Blade component/partial reusable (lihat section 10.2), **BUKAN** menduplikasi markup di setiap halaman.
- Penamaan class CSS dan struktur HTML hasil slicing harus konsisten dengan struktur folder pada section 10 (file Blade view ditaruh sesuai mapping halaman pada tabel 17.2).
- Jika ada elemen di Figma yang tidak dijelaskan datanya di PRD ini (misal teks dummy, jumlah item placeholder), AI menggunakan data dummy yang konsisten dengan skema database section 11 — bukan mengarang struktur data baru.
- Breakpoint responsif mengikuti grid default Bootstrap 5 (sm 576px, md 768px, lg 992px, xl 1200px) kecuali Figma menunjukkan breakpoint custom secara eksplisit pada frame.

### 17.2 Design System Inheritance untuk Mode Owner & Admin

Sistem SI-RENT pada dasarnya hanya memiliki dua jenis akun: **User** (satu akun yang dapat menjalankan dua mode — Borrower dan Owner, lihat section 4.3) dan **Admin**. Saat ini prototype Figma HANYA tersedia untuk mode Borrower (section 17.3). Mode Owner dan panel Admin akan dibangun menyusul **sebelum** desainnya tersedia di Figma, sehingga AI **WAJIB** menurunkan (inherit) gaya visual dari hasil slicing mode Borrower yang sudah jadi, **BUKAN** menciptakan gaya visual baru yang berdiri sendiri.

**Aturan Inheritance:**
- Sebelum membangun halaman Owner manapun, AI WAJIB membaca ulang hasil kode (Blade view, CSS, component) dari halaman-halaman Borrower yang sudah dibangun, untuk mengekstrak: palet warna (primary, secondary, success, danger, warning yang dipakai), jenis font & ukuran heading/body, radius border, shadow, spacing antar elemen, dan gaya tombol/badge/card yang sudah established.
- Komponen reusable yang sudah dibuat di section 10.2 (Navbar, Avatar, Badge Status, Badge Rating, Pagination, Empty State, dll) DIPAKAI ULANG di halaman Owner jika konteksnya cocok — bukan dibuat ulang dengan styling berbeda. Contoh: Badge Status Transaksi yang dipakai borrower di Activity Ongoing/History harus dipakai juga di Kelola Permintaan & Riwayat Penyewaan Owner, dengan tampilan visual yang identik.
- Untuk elemen yang BELUM ada presedennya di mode Borrower (misal grafik pendapatan bulanan di Dashboard Owner, kalender blokir ketersediaan), AI tetap mengikuti palet warna dan gaya visual yang sama (warna chart mengikuti warna primary/accent yang sudah dipakai), namun bentuk komponennya boleh baru karena memang kebutuhan fungsionalnya tidak ada di Borrower.
- Layout umum (struktur navbar atas, sidebar jika ada, lebar container, jarak antar section) tetap konsisten dengan layout Borrower, agar perpindahan antara mode Borrower dan Owner pada satu akun yang sama terasa seperti satu produk, bukan dua aplikasi berbeda.
- Panel Admin (section 6.4) TIDAK perlu mengikuti gaya visual Borrower secara ketat, karena merupakan tools internal terpisah yang lazim memiliki desain lebih utilitarian (mengacu pada library DataTables/Bootstrap Admin Template standar) — namun tetap memakai warna primary brand SI-RENT yang sama untuk konsistensi identitas.

**Ringkasan prioritas referensi desain saat membangun halaman baru tanpa Figma:**
1. Cek dulu apakah komponen yang dibutuhkan sudah ada di section 10.2 (Daftar Komponen Reusable) — jika ada, pakai langsung.
2. Jika tidak ada presedennya, lihat halaman Borrower yang sudah jadi dengan konteks paling mirip, dan turunkan gaya visualnya (warna, tipografi, spacing, border-radius).
3. Hanya jika benar-benar tidak ada referensi sama sekali (elemen baru total), AI dapat membuat komponen baru dengan tetap memakai variabel warna/tipografi yang sama dari Bootstrap custom theme yang sudah ditentukan saat membangun Borrower — bukan memilih warna atau font baru secara bebas.

### 17.3 Mapping Halaman ke Node Figma

| Halaman / Frame Figma | Halaman Aplikasi & Referensi Fitur | Link Node Figma |
|---|---|---|
| Home | F-PUB-01 (Home Guest) — route: home | node-id=1737-2623 |
| Home (User Login) | F-PUB-02 (Home Logged-in) — route: home (kondisi @auth) | node-id=1749-4405 |
| About Us | F-PUB-03 — route: about.index | node-id=1513-1599 |
| Slug Detail Product | F-BRW-03 — route: products.show | node-id=1686-3959 |
| Store | F-BRW-04 (tab Produk) — route: store.show | node-id=1764-3219 |
| Store (About) | F-BRW-04 (tab About) — route: store.about | node-id=1819-3462 |
| Store (Reviews) | F-BRW-04 (tab Reviews) — route: store.reviews | node-id=1819-4179 |
| Chat | F-CHT-01 s.d. F-CHT-05 — route: chat.index / chat.show | node-id=1860-3538 |
| Dashboard (Borrower) | F-BRW-06 — route: borrower.dashboard | node-id=1629-3182 |
| History (Borrower) | F-BRW-08, F-BRW-10 — route: borrower.history | node-id=1689-3693 |
| Settings (Borrower) | F-BRW-09, F-AUTH-06/07/08 — route: borrower.settings | node-id=1816-3472 |
| Activity Ongoing (Borrower) | F-BRW-07 — route: borrower.activity | node-id=1742-3499 |
| Register (Modal) | F-AUTH-01, F-AUTH-03 — endpoint: auth.register | node-id=1908-4208 |
| Login (Modal) | F-AUTH-02, F-AUTH-03, F-AUTH-04 — endpoint: auth.login | node-id=1853-3562 |

> URL dasar file: https://www.figma.com/design/vkelQuIuFkYIuAd8ip0fJC/Sirent — bagian yang dijadikan acuan tetap node-id (parameter `?t=` adalah timestamp share yang dapat berubah).

**Halaman yang BELUM memiliki link Figma** (perlu dilengkapi menyusul — AI tidak boleh slicing halaman ini sebelum link tersedia, gunakan struktur fitur di section 6 sebagai acuan sementara):
- Form Ajukan Peminjaman, jika berbeda dari modal/section pada halaman Slug Detail Product (F-BRW-05)
- Dashboard Owner, Kelola Listing Barang, Kelola Permintaan Peminjaman, Riwayat Penyewaan Owner (F-OWN-01 s.d. F-OWN-07)
- Dashboard Admin, Verifikasi Identitas, Dispute Resolution, Kelola Kategori (F-ADM-01 s.d. F-ADM-08)
- Komponen reusable: Navbar & Footer (jika berbeda antar frame di atas, perlu frame komponen tersendiri)

---

## 18. RISIKO & MITIGASI

| Risiko | Tingkat | Mitigasi |
|---|---|---|
| Barang rusak atau hilang saat dipinjam | Tinggi | Verifikasi identitas wajib; sistem rating; panduan serah terima; dokumentasi foto via chat |
| Pengguna palsu / identitas tidak valid | Tinggi | Verifikasi manual KTP oleh admin; batas aktivitas sebelum terverifikasi |
| Dispute antara pihak yang sulit diselesaikan | Sedang | Riwayat chat tersimpan sebagai bukti; admin memiliki akses penuh untuk mediasi |
| Performa database lambat seiring pertumbuhan data | Sedang | Indexing pada kolom yang sering di-query; query optimization; caching dengan Redis |
| Keamanan data identitas pengguna bocor | Tinggi | Storage terenkripsi; akses terbatas hanya admin; audit log akses file identitas |
| Fitur chat tidak real-time (latensi tinggi) | Sedang | Implementasi Soketi (self-hosted) atau Pusher; fallback ke polling jika WebSocket gagal |
| Owner tidak responsif terhadap permintaan | Rendah | Notifikasi pengingat otomatis; batas waktu respons; rating dipengaruhi kecepatan respons |

---

*— Akhir Dokumen PRD SI-RENT v1.0 —*

*Dokumen ini bersifat confidential dan digunakan untuk keperluan pengembangan internal.*
