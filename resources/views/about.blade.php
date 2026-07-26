@extends('layouts.app')

@section('title', __('ui.about.page_title'))

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
                    {{ __('ui.about.hero.eyebrow') }}
                </span>

                <h1 class="about-hero__title">
                    {{ __('ui.about.hero.title') }}

                    <span>
                        {{ __('ui.about.hero.title_highlight') }}
                    </span>
                </h1>

                <p class="about-hero__description">
                    {{ __('ui.about.hero.description') }}
                </p>

                <div class="about-hero__actions">
                    <a
                        href="{{ route('products.index') }}"
                        class="about-btn about-btn--primary"
                    >
                        {{ __('ui.about.hero.start_renting') }}
                    </a>

                    <a
                        href="#our-business"
                        class="about-btn about-btn--outline-light"
                    >
                        {{ __('ui.about.hero.learn_more') }}
                    </a>
                </div>
            </div>

            {{-- RIGHT LOGO --}}
            <div class="about-hero__visual">
                <img
                    src="{{ asset('images/logo-sirent.png') }}"
                    alt="{{ __('ui.about.hero.logo_alt') }}"
                    class="about-hero__logo"
                >
            </div>

        </div>

        <a
            href="#statistics"
            class="about-scroll-indicator"
            aria-label="{{ __('ui.about.hero.scroll_label') }}"
        >
            <span></span>
        </a>
    </section>


    {{-- =====================================================
         STATISTICS SECTION
         ===================================================== --}}
    <section
        class="about-statistics"
        id="statistics"
    >
        <div class="container">
            <div class="about-statistics__wrapper">

                <div class="about-statistics__intro">
                    <span class="about-eyebrow">
                        {{ __('ui.about.statistics.eyebrow') }}
                    </span>

                    <h2>
                        {{ __('ui.about.statistics.title') }}

                        <span>
                            {{ __('ui.about.statistics.title_highlight') }}
                        </span>
                    </h2>

                    <p>
                        {{ __('ui.about.statistics.description') }}
                    </p>
                </div>

                <div class="about-statistics__grid">
                @php
                    $statLabelKeys = [
                        'ui.about.statistics.labels.users',
                        'ui.about.statistics.labels.products',
                        'ui.about.statistics.labels.stores',
                        'ui.about.statistics.labels.transactions',
                    ];
                @endphp

                            @foreach ($stats as $stat)
                                <article class="about-statistic">
                                    <div class="about-statistic__number">
                                        {{ number_format(
                                            $stat['value'],
                                            0,
                                            ',',
                                            '.'
                                        ) }}

                                        @if (! empty($stat['suffix']))
                                            <sup>
                                                {{ $stat['suffix'] }}
                                            </sup>
                                        @endif
                                    </div>

                                    <p>
                                        {{ __(
                                            $statLabelKeys[$loop->index]
                                            ?? $stat['label']
                                        ) }}
                                    </p>
                                </article>
                            @endforeach
                        </div>

            </div>
        </div>
    </section>


    {{-- =====================================================
         BUSINESS / VALUE SECTION
         ===================================================== --}}
    <section
        class="about-business"
        id="our-business"
    >
        <div class="container">

            <header class="about-section-header">
                <span class="about-eyebrow">
                    {{ __('ui.about.business.eyebrow') }}
                </span>

                <h2>
                    {{ __('ui.about.business.title') }}
                </h2>

                <p>
                    {{ __('ui.about.business.description') }}
                </p>
            </header>

            <div class="about-value-grid">

                {{-- Diverse --}}
                <article class="about-value-card">
                    <span class="about-value-card__number">
                        01
                    </span>

                    <h3>
                        {{ __('ui.about.business.values.diverse.title') }}
                    </h3>

                    <p>
                        {{ __('ui.about.business.values.diverse.description') }}
                    </p>
                </article>

                {{-- Affordable --}}
                <article class="about-value-card">
                    <span class="about-value-card__number">
                        02
                    </span>

                    <h3>
                        {{ __('ui.about.business.values.affordable.title') }}
                    </h3>

                    <p>
                        {{ __('ui.about.business.values.affordable.description') }}
                    </p>
                </article>

                {{-- Trusted --}}
                <article class="about-value-card">
                    <span class="about-value-card__number">
                        03
                    </span>

                    <h3>
                        {{ __('ui.about.business.values.trusted.title') }}
                    </h3>

                    <p>
                        {{ __('ui.about.business.values.trusted.description') }}
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
                {{ __('ui.about.final_cta.eyebrow') }}
            </span>

            <h2>
                {{ __('ui.about.final_cta.title') }}
            </h2>

            <p>
                {{ __('ui.about.final_cta.description') }}
            </p>

            <div class="about-final-cta__actions">
                <a
                    href="{{ route('products.index') }}"
                    class="about-btn about-btn--light"
                >
                    {{ __('ui.about.final_cta.start_renting') }}
                </a>

                @auth
                    <a
                        href="{{ route(
                            'borrower.dashboard',
                            ['tab' => 'store']
                        ) }}"
                        class="about-btn about-btn--outline-light"
                    >
                        {{ __('ui.about.final_cta.open_store') }}
                    </a>
                @else
                    <button
                        type="button"
                        class="about-btn about-btn--outline-light"
                        onclick="
                            window.dispatchEvent(
                                new CustomEvent('open-auth-modal', {
                                    detail: {
                                        mode: 'login'
                                    }
                                })
                            )
                        "
                    >
                        {{ __('ui.about.final_cta.open_store') }}
                    </button>
                @endauth
            </div>
        </div>
    </section>

</main>
@endsection