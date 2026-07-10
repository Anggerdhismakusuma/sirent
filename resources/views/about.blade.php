@extends('layouts.app')

@section('title', 'About Us')

@section('hide-navbar', true)

@section('content')
@vite(['resources/js/app.js', 'resources/sass/app.scss'])

<section class="about-page text-white">
    <a class="about-back" href="{{ route('home') }}"> 
        <i class="bi bi-chevron-left"></i>
    </a>

    <div class="container-fluid p-0">
        <div class="row g-0 min-vh-100">
            <div class="col-lg-7 about-left d-flex align-items-center justify-content-center">
                <div class="about-content">
                    <h1>SI-RENT</h1>
                    <p>
                        We are multi-vendor website that aims to provide hobby and
                        equipment rental services, such as cameras, drones and other
                        tools, while maintaining and guaranteeing security between
                        renters and owners of goods.
                    </p>

                    <div class="about-features">
                        <div class="about-feature-item">
                            <h3>Variety</h3>
                            <p>Find all kinds of hobby gear in one place.</p>
                        </div>

                        <div class="about-feature-item">
                            <h3>Affordable</h3>
                            <p>Rent high quality equipment without overspending.</p>
                        </div>

                        <div class="about-feature-item">
                            <h3>Trusted</h3>
                            <p>Secure transactions between renters and owners.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5 about-right d-flex align-items-center justify-content-center">
                <img src="{{ asset('images/logo-sirent 1.png') }}" alt="SI-RENT Logo" class="about-logo">
            </div>
        </div>
    </div>
</section>
@endsection
