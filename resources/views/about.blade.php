@extends('layouts.app')

@section('title', 'Tentang SI-RENT')

@section('content')
<main class="about-page">

    {{-- =====================================================
         HERO SECTION
         ===================================================== --}}
    <section class="about-hero">
        <div class="about-hero__overlay"></div>

        <div class="container about-hero__content">

            {{-- LEFT CONTENT --}}
            <div class="about-hero__copy">
                <span class="about-eyebrow about-eyebrow--light">
                    TENTANG SI-RENT
                </span>

                <h1 class="about-hero__title">
                    Barang yang kamu butuhkan,
                    <span>tanpa harus selalu membeli.</span>
                </h1>

                <p class="about-hero__description">
                    SI-RENT menghubungkan penyewa dengan pemilik barang dalam
                    satu platform rental yang praktis, terjangkau, dan terpercaya.
                </p>

                <div class="about-hero__actions">
                    <a
                        href="{{ route('products.index') }}"
                        class="about-btn about-btn--primary"
                    >
                        Mulai Sewa
                    </a>

                    <a
                        href="#our-business"
                        class="about-btn about-btn--outline-light"
                    >
                        Kenali SI-RENT
                    </a>
                </div>
            </div>

            {{-- RIGHT LOGO --}}
            <div class="about-hero__visual">
                <img
                    src="{{ asset('images/logo-sirent.png') }}"
                    alt="Logo SI-RENT"
                    class="about-hero__logo"
                >
            </div>

        </div>

        <a
            href="#statistics"
            class="about-scroll-indicator"
            aria-label="Lihat informasi SI-RENT"
        >
            <span></span>
        </a>
    </section>


    {{-- =====================================================
         STATISTICS SECTION
         ===================================================== --}}
    <section class="about-statistics" id="statistics">
        <div class="container">
            <div class="about-statistics__wrapper">

                <div class="about-statistics__intro">
                    <span class="about-eyebrow">
                        EKOSISTEM KAMI
                    </span>

                    <h2>
                        Membangun akses rental untuk
                        <span>
                            menghubungkan penyewa dan pemilik barang.
                        </span>
                    </h2>

                    <p>
                        Kami membantu masyarakat mendapatkan barang yang
                        dibutuhkan tanpa harus membelinya, sekaligus membuka
                        peluang bagi pemilik barang untuk memperoleh penghasilan.
                    </p>
                </div>

                <div class="about-statistics__grid">
                    @foreach ($stats as $stat)
                        <article class="about-statistic">
                            <div class="about-statistic__number">
                                {{ number_format($stat['value'], 0, ',', '.') }}

                                @if (!empty($stat['suffix']))
                                    <sup>{{ $stat['suffix'] }}</sup>
                                @endif
                            </div>

                            <p>{{ $stat['label'] }}</p>
                        </article>
                    @endforeach
                </div>

            </div>
        </div>
    </section>


    {{-- =====================================================
         BUSINESS / VALUE SECTION
         ===================================================== --}}
    <section class="about-business" id="our-business">
        <div class="container">

            <header class="about-section-header">
                <span class="about-eyebrow">
                    KENAPA SI-RENT?
                </span>

                <h2>
                    Satu platform untuk pengalaman rental yang
                    lebih sederhana dan bernilai.
                </h2>

                <p>
                    SI-RENT dirancang untuk memberikan lebih banyak pilihan,
                    biaya yang lebih efisien, dan transaksi yang lebih aman.
                </p>
            </header>

            <div class="about-value-grid">

                <article class="about-value-card">
                    <span class="about-value-card__number">
                        01
                    </span>

                    <h3>Beragam</h3>

                    <p>
                        Temukan berbagai kategori barang dari banyak toko
                        dalam satu platform, mulai dari kebutuhan sehari-hari,
                        elektronik, perlengkapan acara, hingga hobi.
                    </p>
                </article>

                <article class="about-value-card">
                    <span class="about-value-card__number">
                        02
                    </span>

                    <h3>Terjangkau</h3>

                    <p>
                        Gunakan barang sesuai kebutuhan tanpa harus mengeluarkan
                        biaya penuh untuk membelinya. Lebih hemat untuk penyewa
                        dan lebih produktif bagi pemilik barang.
                    </p>
                </article>

                <article class="about-value-card">
                    <span class="about-value-card__number">
                        03
                    </span>

                    <h3>Terpercaya</h3>

                    <p>
                        Profil toko, informasi barang, riwayat transaksi,
                        dan sistem rating membantu pengguna mengambil keputusan
                        rental dengan lebih yakin.
                    </p>
                </article>

            </div>
        </div>
    </section>


    {{-- =====================================================
         FINAL CTA
         ===================================================== --}}
    <section class="about-final-cta">
        <div class="about-final-cta__decoration"></div>

        <div class="container about-final-cta__content">
            <span class="about-eyebrow about-eyebrow--light">
                MULAI BERSAMA SI-RENT
            </span>

            <h2>
                Siap menjadi bagian dari ekosistem rental yang lebih cerdas?
            </h2>

            <p>
                Temukan barang yang kamu butuhkan atau mulai menghasilkan
                pendapatan dari barang yang kamu miliki.
            </p>

            <div class="about-final-cta__actions">
                <a
                    href="{{ route('products.index') }}"
                    class="about-btn about-btn--light"
                >
                    Mulai Sewa
                </a>

                <a
                    href="{{ route('borrower.dashboard') }}"
                    class="about-btn about-btn--outline-light"
                >
                    Buka Toko
                </a>
            </div>
        </div>
    </section>

</main>
@endsection