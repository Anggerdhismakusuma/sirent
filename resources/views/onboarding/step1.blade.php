{{-- SI-RENT Onboarding — 3-Step Verification (Figma: 01. Getting Started) --}}
@extends('layouts.app')

@section('title', __('ui.onboarding_title') . ' — SI-RENT')

@section('content')
    {{-- Menggunakan min-height 100vh penuh karena navbar & footer disembunyikan di layouts.app --}}
    <div class="d-flex align-items-center justify-content-center"
        style="min-height: 100vh; background: var(--bg-grey, #f0f0f0); padding: 40px 0;">
        <div x-data="onboarding({{ request('step', 1) }})" class="w-100" style="max-width: 900px;" x-cloak>

            {{-- Step Progress Bars --}}
            <div class="d-flex gap-2 justify-content-center mb-4">
                <template x-for="i in 3" :key="i">
                    <div class="flex-grow-0"
                        style="width: 260px; height: 8px; border-radius: 28px; transition: background-color 0.3s;"
                        :style="{ background: step >= i ? 'var(--primary-blue, #0031e1)' : '#d9d9d9' }"></div>
                </template>
            </div>

            <div class="text-center mb-4">
                <span x-text="'{{ __('ui.onboarding_title') }} — {{ __('ui.step') }} ' + step + ' {{ __('ui.of') }} 3'"
                    style="font-family: 'Mona Sans', sans-serif; font-size: 14px; color: var(--text-secondary, #5c5c5c);"></span>
            </div>

            <div class="text-center mb-4">
                <h3 class="fw-bold" x-text="stepTitle" style="font-family: 'Mona Sans', sans-serif; font-size: 32px;"></h3>
            </div>

            {{-- Main Card --}}
            <div class="bg-white rounded-4 shadow-sm mx-auto"
                style="max-width: 800px; border-radius: 20px; border: 1px solid var(--border-default, #d4d4d4);">
                <div class="p-5">

                    {{-- ====== STEP 1: Personal Information ====== --}}
                    <div x-show="step === 1" x-transition>
                        <form method="POST" action="{{ route('onboarding.step1.store') }}"
                            @submit.prevent="submitStep($el)">
                            @csrf
                            {{-- Name --}}
                            <div class="mb-3">
                                <label class="fw-semibold mb-2 d-block"
                                    style="font-family:'Mona Sans',sans-serif; font-size:14px;">{{ __('ui.name') }}</label>
                                <div class="position-relative">
                                    <i class="bi bi-person position-absolute"
                                        style="top:18px; left:16px; color: var(--text-secondary);"></i>
                                    <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}"
                                        required class="form-control ps-5" placeholder="{{ __('ui.enter_name') }}"
                                        style="height:60px; border-radius:10px; font-family:'Mona Sans',sans-serif; font-size:14px; border-color: var(--border-default);">
                                </div>
                            </div>

                            {{-- Email Verification Row --}}
                            <div class="mb-3">
                                <label class="fw-semibold mb-2 d-block"
                                    style="font-family:'Mona Sans',sans-serif; font-size:14px;">Email Address</label>
                                <div class="d-flex gap-2 align-items-center">
                                    <div class="position-relative flex-grow-1">
                                        <i class="bi bi-envelope position-absolute"
                                            style="top:18px; left:16px; color: var(--text-secondary);"></i>
                                        <input type="email" name="email" id="email_input"
                                            value="{{ auth()->user()->email }}" disabled class="form-control ps-5 bg-light"
                                            style="height:60px; border-radius:10px; font-family:'Mona Sans',sans-serif; font-size:14px; border-color: var(--border-default);">
                                    </div>
                                    <div>
                                        <button type="button" @click="verifyEmail()"
                                            :disabled="emailVerified || emailCooldown > 0"
                                            class="btn fw-semibold px-4 d-flex align-items-center justify-content-center gap-2"
                                            :class="emailVerified ? 'btn-success text-white' : 'btn-outline-primary'"
                                            style="height:60px; min-width:140px; border-radius:10px; font-family:'Mona Sans',sans-serif; font-size:14px;">
                                            <span x-show="emailVerified"><i class="bi bi-check-circle-fill"></i>
                                                Verified</span>
                                            <span x-show="!emailVerified && emailCooldown === 0">Verify Email</span>
                                            <span x-show="!emailVerified && emailCooldown > 0"
                                                x-text="'Resend (' + emailCooldown + 's)'"></span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            {{-- Phone / WhatsApp Number Verification Row --}}
                            <div class="mb-3">
                                <label class="fw-semibold mb-2 d-block"
                                    style="font-family:'Mona Sans',sans-serif; font-size:14px;">{{ __('ui.phone_number') }}
                                    (WhatsApp)</label>
                                <div class="d-flex gap-2 align-items-center">
                                    <div class="position-relative flex-grow-1">
                                        <i class="bi bi-whatsapp position-absolute"
                                            style="top:18px; left:16px; color: var(--text-secondary);"></i>
                                        <input type="text" name="phone" x-model="phone" required :disabled="false"
                                            class="form-control ps-5" placeholder="Ex: 0812345678910"
                                            style="height:60px; border-radius:10px; font-family:'Mona Sans',sans-serif; font-size:14px; border-color: var(--border-default);">
                                    </div>
                                    <div>
                                        <button type="button" @click="verifyWhatsApp()"
                                            :disabled="whatsappVerified || whatsappCooldown > 0 || !phone"
                                            class="btn fw-semibold px-4 d-flex align-items-center justify-content-center gap-2"
                                            :class="whatsappVerified ? 'btn-success text-white' : 'btn-outline-success'"
                                            style="height:60px; min-width:140px; border-radius:10px; font-family:'Mona Sans',sans-serif; font-size:14px;">
                                            <span x-show="whatsappVerified"><i class="bi bi-check-circle-fill"></i>
                                                Verified</span>
                                            <span x-show="!whatsappVerified && whatsappCooldown === 0">Verify WA</span>
                                            <span x-show="!whatsappVerified && whatsappCooldown > 0"
                                                x-text="'Resend (' + whatsappCooldown + 's)'"></span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            {{-- Date of Birth + Domicile row --}}
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="fw-semibold mb-2 d-block"
                                        style="font-family:'Mona Sans',sans-serif; font-size:14px;">{{ __('ui.dob') }}</label>
                                    <div class="position-relative">
                                        <i class="bi bi-calendar position-absolute"
                                            style="top:18px; left:16px; color: var(--text-secondary);"></i>
                                        <input type="date" name="dob" value="{{ old('dob') }}" required
                                            class="form-control ps-5" placeholder="DD/MM/YYYY"
                                            style="height:60px; border-radius:10px; font-family:'Mona Sans',sans-serif; font-size:14px; border-color: var(--border-default);">
                                    </div>
                                </div>
                                <div class="col-md-6" x-data="{ openDropdown: false }">
                                    <label class="fw-semibold mb-2 d-block"
                                        style="font-family:'Mona Sans',sans-serif; font-size:14px;">Domicile</label>

                                    <!-- Wrapper utama dengan posisi relatif -->
                                    <div class="position-relative" @click.away="openDropdown = false">
                                        <i class="bi bi-geo-alt position-absolute"
                                            style="top:18px; left:16px; color: var(--text-secondary); z-index: 3;"></i>

                                        <!-- Input tunggal yang menampung hasil akhir format "Kota, Provinsi" -->
                                        <input type="text" name="domicile" x-model="domicileInput"
                                            @click="openDropdown = !openDropdown" readonly required
                                            class="form-control ps-5 bg-white" placeholder="Select Domicile"
                                            style="height:60px; border-radius:10px; font-family:'Mona Sans',sans-serif; font-size:14px; border-color: var(--border-default); cursor: pointer;">

                                        <!-- Dropdown bertingkat custom melayang (Floating Panel) -->
                                        <div x-show="openDropdown"
                                            class="position-absolute w-100 bg-white border mt-1 shadow-sm p-3"
                                            style="border-radius: 10px; z-index: 1000; max-height: 300px; overflow-y: auto; border-color: var(--border-default);">

                                            <!-- TAHAP 1: Pilih Provinsi -->
                                            <div x-show="!selectedProvinceId">
                                                <div class="fw-semibold text-muted small mb-2"
                                                    style="font-family:'Mona Sans',sans-serif;">Select Province:</div>
                                                <div class="list-group list-group-flush">
                                                    <template x-for="prov in provinces" :key="prov.id">
                                                        <button type="button" @click="selectProvince(prov.id, prov.name)"
                                                            class="list-group-item list-group-item-action text-start border-0 py-2"
                                                            style="font-family:'Mona Sans',sans-serif; font-size:14px;">
                                                            <span x-text="prov.name"></span>
                                                        </button>
                                                    </template>
                                                </div>
                                            </div>

                                            <!-- TAHAP 2: Pilih Kota / Kabupaten -->
                                            <div x-show="selectedProvinceId">
                                                <div class="d-flex justify-between align-items-center mb-2">
                                                    <button type="button" @click="resetRegionSelection()"
                                                        class="btn btn-sm btn-link p-0 text-decoration-none text-secondary"
                                                        style="font-size: 12px; font-family:'Mona Sans',sans-serif;">
                                                        <i class="bi bi-arrow-left"></i> Back to Provinces
                                                    </button>
                                                </div>

                                                <!-- Indikator Loading saat fetching data kota -->
                                                <div x-show="loadingCities" class="text-center py-3 text-muted small"
                                                    style="font-family:'Mona Sans',sans-serif;">
                                                    <div class="spinner-border spinner-border-sm me-2 text-secondary"
                                                        role="status"></div>
                                                    Loading cities...
                                                </div>

                                                <!-- Daftar Kota -->
                                                <div x-show="!loadingCities" class="list-group list-group-flush">
                                                    <template x-for="city in cities" :key="city.id">
                                                        <button type="button" @click="selectCity(city.name)"
                                                            class="list-group-item list-group-item-action text-start border-0 py-2"
                                                            style="font-family:'Mona Sans',sans-serif; font-size:14px;">
                                                            <span x-text="city.name"></span>
                                                        </button>
                                                    </template>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Gender --}}
                            <div class="mb-4">
                                <label class="fw-semibold mb-2 d-block"
                                    style="font-family:'Mona Sans',sans-serif; font-size:14px;">{{ __('ui.gender') }}</label>
                                <div class="d-flex gap-3">
                                    <!-- Pilihan Male -->
                                    <label class="flex-fill gender-label position-relative m-0">
                                        <input type="radio" name="gender" value="male"
                                            class="position-absolute opacity-0 w-0 h-0"
                                            {{ old('gender') === 'male' ? 'checked' : '' }} required>
                                        <div class="rounded-3 border text-center py-3"
                                            style="cursor:pointer; border-color: var(--border-default); transition:0.2s; font-family:'Mona Sans',sans-serif; font-size:14px;">
                                            <i class="bi bi-gender-male d-block mb-1" style="font-size:20px;"></i>
                                            Male
                                        </div>
                                    </label>

                                    <!-- Pilihan Female -->
                                    <label class="flex-fill gender-label position-relative m-0">
                                        <input type="radio" name="gender" value="female"
                                            class="position-absolute opacity-0 w-0 h-0"
                                            {{ old('gender') === 'female' ? 'checked' : '' }}>
                                        <div class="rounded-3 border text-center py-3"
                                            style="cursor:pointer; border-color: var(--border-default); transition:0.2s; font-family:'Mona Sans',sans-serif; font-size:14px;">
                                            <i class="bi bi-gender-female d-block mb-1" style="font-size:20px;"></i>
                                            Female
                                        </div>
                                    </label>

                                    <!-- Pilihan Prefer Not To Say -->
                                    <label class="flex-fill gender-label position-relative m-0">
                                        <input type="radio" name="gender" value="other"
                                            class="position-absolute opacity-0 w-0 h-0"
                                            {{ old('gender') === 'other' ? 'checked' : '' }}>
                                        <div class="rounded-3 border text-center py-3"
                                            style="cursor:pointer; border-color: var(--border-default); transition:0.2s; font-family:'Mona Sans',sans-serif; font-size:14px;">
                                            <i class="bi bi-slash-circle d-block mb-1" style="font-size:20px;"></i>
                                            Prefer not to say
                                        </div>
                                    </label>
                                </div>
                            </div>

                            {{-- Next Button --}}
                            <div class="text-center">
                                <button type="submit" class="btn text-white fw-semibold px-5 py-2"
                                    :disabled="loading || !emailVerified || !whatsappVerified"
                                    style="background: var(--primary-blue); border-radius:10px; font-family:'Mona Sans',sans-serif; font-size:16px;">
                                    <span x-show="!loading">{{ __('ui.onboarding_next') }}</span>
                                    <span x-show="loading">Saving...</span>
                                </button>
                                <small x-show="!emailVerified || !whatsappVerified" class="d-block text-danger mt-2"
                                    style="font-size: 12px;">
                                    * Selesaikan verifikasi Email dan WhatsApp terlebih dahulu untuk melanjutkan.
                                </small>
                            </div>
                        </form>
                    </div>

                    {{-- ====== STEP 2: Pick Interests ====== --}}
                    <div x-show="step === 2" x-transition>
                        <form method="POST" action="{{ route('onboarding.step2.store') }}"
                            @submit.prevent="submitStep($el)">
                            @csrf
                            <div class="row g-3 mb-4">
                                @php
                                    $interests = [
                                        'music' => 'Music',
                                        'gaming' => 'Gaming',
                                        'photography' => 'Photography',
                                        'sports' => 'Sports',
                                        'automotive' => 'Automotive',
                                        'furniture' => 'Furniture',
                                        'fashion' => 'Fashion',
                                        'technology' => 'Technology',
                                    ];
                                    $stored = old('interests', auth()->user()->interests ?? []);
                                @endphp
                                @foreach ($interests as $key => $label)
                                    <div class="col-6 col-md-3">
                                        <label class="d-block interest-label">
                                            <input type="checkbox" name="interests[]" value="{{ $key }}"
                                                class="position-absolute opacity-0 w-0 h-0 interest-checkbox"
                                                {{ in_array($key, (array) $stored) ? 'checked' : '' }}>
                                            <div class="rounded-pill border text-center py-3 d-flex align-items-center justify-content-center gap-2"
                                                style="cursor:pointer; border-color: var(--border-default); transition:0.2s; height:63px;">
                                                <span
                                                    style="font-family:'Mona Sans',sans-serif; font-size:14px;">{{ $label }}</span>
                                            </div>
                                        </label>
                                    </div>
                                @endforeach
                            </div>

                            <div class="text-center">
                                <button type="submit" class="btn text-white fw-semibold px-5 py-2"
                                    :disabled="loading"
                                    style="background: var(--primary-blue); border-radius:10px; font-family:'Mona Sans',sans-serif; font-size:16px;">
                                    <span x-show="!loading">{{ __('ui.onboarding_next') }}</span>
                                    <span x-show="loading">Saving...</span>
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- ====== STEP 3: Upload KTP ====== --}}
                    <div x-show="step === 3" x-transition>
                        <form method="POST" action="{{ route('onboarding.step3.store') }}"
                            enctype="multipart/form-data" x-data="{ fileName: '', preview: '' }" @submit.prevent="submitStep($el)">
                            @csrf
                            <div class="text-center mb-4">
                                <div class="border rounded-4 d-flex flex-column align-items-center justify-content-center mx-auto position-relative"
                                    style="max-width:678px; height:357px; border-radius:50px; border:2px dashed var(--border-default); cursor:pointer;"
                                    @click="$refs.ktpInput.click()" @dragover.prevent
                                    @drop.prevent="$refs.ktpInput.files = $event.dataTransfer.files; $refs.ktpInput.dispatchEvent(new Event('change'))">
                                    <input type="file" name="identity_doc" x-ref="ktpInput" class="d-none"
                                        accept="image/*" required
                                        @change="fileName = $el.files[0]?.name || ''; if ($el.files[0]) { const r = new FileReader(); r.onload = e => preview = e.target.result; r.readAsDataURL($el.files[0]) }">
                                    <template x-if="!preview">
                                        <div>
                                            <i class="bi bi-cloud-upload d-block mb-2"
                                                style="font-size:64px; color: var(--primary-blue);"></i>
                                            <p class="fw-semibold"
                                                style="font-family:'Mona Sans',sans-serif; color: var(--text-primary, #000);">
                                                {{ __('ui.drag_drop_hint') }}</p>
                                            <p class="text-muted" style="font-family:'Mona Sans',sans-serif;">
                                                {{ __('ui.supported_formats') }}</p>
                                            <span class="btn btn-outline-primary mt-2 rounded-3"
                                                style="font-family:'Mona Sans',sans-serif;">{{ __('ui.upload_file') }}</span>
                                        </div>
                                    </template>
                                    <template x-if="preview">
                                        <img :src="preview"
                                            style="max-height:300px; max-width:100%; border-radius:20px; object-fit:contain;">
                                    </template>
                                </div>
                                <p x-show="fileName" x-text="fileName" class="mt-2 text-muted"
                                    style="font-family:'Mona Sans',sans-serif;"></p>
                            </div>

                            <div class="text-center">
                                <button type="submit" class="btn text-white fw-semibold px-5 py-2"
                                    :disabled="loading"
                                    style="background: var(--primary-blue); border-radius:10px; font-family:'Mona Sans',sans-serif; font-size:16px;">
                                    <span x-show="!loading">{{ __('ui.onboarding_submit') }}</span>
                                    <span x-show="loading">Submitting...</span>
                                </button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function onboarding(initialStep) {
            return {
                step: initialStep,
                loading: false,

                // --- STATE DROPDOWN WILAYAH (GABUNGAN) ---
                provinces: [],
                cities: [],
                selectedProvinceId: '',
                selectedProvinceName: '',
                domicileInput: '{{ old('domicile') }}',
                loadingCities: false,

                phone: '{{ old('phone', auth()->user()->phone) }}',
                emailVerified: {{ auth()->user()->hasVerifiedEmail() ? 'true' : 'false' }},
                whatsappVerified: {{ auth()->user()->phone ? 'true' : 'false' }},

                emailCooldown: 0,
                whatsappCooldown: 0,
                emailPollingInterval: null,

                get stepTitle() {
                    const titles = {
                        1: '{{ __('ui.onboarding_step1_title') }}',
                        2: '{{ __('ui.onboarding_step2_title') }}',
                        3: '{{ __('ui.onboarding_step3_title') }}',
                    };
                    return titles[this.step] || '';
                },

                init() {
                    // Daftarkan step awal ke history state saat pertama kali dimuat
                    window.history.replaceState({
                        step: this.step
                    }, '', window.location.search);

                    window.addEventListener('popstate', (event) => {
                        if (event.state && event.state.step) {
                            this.step = event.state.step;
                        } else {
                            const urlParams = new URLSearchParams(window.location.search);
                            this.step = parseInt(urlParams.get('step')) || 1;
                        }
                    });

                    if (!this.emailVerified) {
                        this.startEmailPolling(4000);
                    }

                    // Ambil data provinsi saat awal onboarding dimuat
                    this.fetchProvinces();
                },

                // --- FUNGSI API WILAYAH (GABUNGAN) ---
                async fetchProvinces() {
                    try {
                        const response = await fetch('https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json');
                        if (response.ok) {
                            this.provinces = await response.json();
                        }
                    } catch (e) {
                        console.error('Gagal memuat data provinsi:', e);
                    }
                },

                async selectProvince(id, name) {
                    this.selectedProvinceId = id;
                    this.selectedProvinceName = name;
                    this.loadingCities = true;
                    this.cities = [];

                    try {
                        const response = await fetch(
                            `https://www.emsifa.com/api-wilayah-indonesia/api/regencies/${id}.json`);
                        if (response.ok) {
                            this.cities = await response.json();
                        }
                    } catch (e) {
                        console.error('Gagal memuat data kota:', e);
                    } finally {
                        this.loadingCities = false;
                    }
                },

                selectCity(cityName) {
                    // Masukkan teks dengan format "Kota, Provinsi" ke input field utama
                    this.domicileInput = `${cityName}, ${this.selectedProvinceName}`;

                    // Reset menu internal agar saat diklik lagi mengulang dari Provinsi
                    this.resetRegionSelection();
                },

                resetRegionSelection() {
                    this.selectedProvinceId = '';
                    this.selectedProvinceName = '';
                    this.cities = [];
                },

                // --- PENGATURAN EMAIL POLLING ---
                startEmailPolling(ms = 3000) {
                    if (this.emailPollingInterval) clearInterval(this.emailPollingInterval);

                    this.emailPollingInterval = setInterval(async () => {
                        try {
                            const response = await fetch("/api/user/check-email-status");
                            const data = await response.json();

                            if (data.verified) {
                                this.emailVerified = true;
                                clearInterval(this.emailPollingInterval);
                                alert('Email Anda telah sukses diverifikasi!');
                            }
                        } catch (e) {
                            console.error('Gagal memproses polling status email:', e);
                        }
                    }, ms);
                },

                // --- VERIFIKASI EMAIL ---
                async verifyEmail() {
                    this.emailCooldown = 60;
                    let interval = setInterval(() => {
                        this.emailCooldown--;
                        if (this.emailCooldown <= 0) clearInterval(interval);
                    }, 1000);

                    try {
                        const response = await fetch("{{ route('verification.send') }}", {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute(
                                    'content'),
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            }
                        });

                        const data = await response.json();

                        if (response.ok) {
                            alert(data.message ||
                                'Tautan verifikasi sukses dikirim ke email kamu! Silakan cek inbox Anda.');
                            this.startEmailPolling(2000);
                        } else {
                            alert(data.message || 'Gagal mengirim email verifikasi.');
                            this.emailCooldown = 0;
                            clearInterval(interval);
                        }
                    } catch (e) {
                        alert('Gagal tersambung ke server untuk verifikasi email.');
                        this.emailCooldown = 0;
                        clearInterval(interval);
                    }
                },

                // --- VERIFIKASI WHATSAPP ---
                async verifyWhatsApp() {
                    if (!this.phone) return alert('Silakan masukkan nomor WhatsApp terlebih dahulu.');

                    this.whatsappCooldown = 60;
                    let interval = setInterval(() => {
                        this.whatsappCooldown--;
                        if (this.whatsappCooldown <= 0) clearInterval(interval);
                    }, 1000);

                    try {
                        const response = await fetch("{{ route('onboarding.verify.whatsapp') }}", {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute(
                                    'content'),
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                phone: this.phone
                            })
                        });

                        const data = await response.json();
                        if (!response.ok) {
                            alert(data.message || 'Gagal mengirim OTP.');
                            this.whatsappCooldown = 0;
                            clearInterval(interval);
                            return;
                        }

                        alert('Kode OTP simulasi telah dicatat di log server (storage/logs/laravel.log).');

                        let userOtp = prompt("Masukkan 6 digit kode OTP yang dikirimkan ke WhatsApp Anda:");
                        if (userOtp && userOtp.trim() !== "") {
                            this.whatsappVerified = true;
                            alert('Nomor WhatsApp terverifikasi sukses!');
                        } else {
                            alert('Verifikasi WhatsApp dibatalkan.');
                            this.whatsappCooldown = 0;
                            clearInterval(interval);
                        }
                    } catch (e) {
                        alert('Gagal memproses verifikasi WhatsApp.');
                        this.whatsappCooldown = 0;
                        clearInterval(interval);
                    }
                },

                // --- SUBMIT FORM PER-STEP ---
                async submitStep(formElement) {
                    if (this.step === 1 && (!this.emailVerified || !this.whatsappVerified)) {
                        alert('Anda harus memverifikasi Email dan WhatsApp terlebih dahulu.');
                        return;
                    }

                    this.loading = true;
                    let formData = new FormData(formElement);

                    try {
                        const response = await fetch(formElement.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute(
                                    'content'),
                                'Accept': 'application/json'
                            }
                        });

                        if (response.ok) {
                            if (this.step < 3) {
                                this.step++;
                                // Menyimpan state objek agar tombol Back/Forward browser berfungsi mulus
                                window.history.pushState({
                                    step: this.step
                                }, '', '?step=' + this.step);
                            } else {
                                // Hentikan polling sebelum pindah halaman
                                if (this.emailPollingInterval) clearInterval(this.emailPollingInterval);
                                window.location.href = '/dashboard';
                            }
                        } else {
                            const errorData = await response.json();
                            alert(errorData.message || 'Terjadi kesalahan pada validasi data.');
                        }
                    } catch (error) {
                        console.error('AJAX Error:', error);
                        alert('Gagal tersambung ke server.');
                    } finally {
                        this.loading = false;
                    }
                }
            };
        }
    </script>
@endpush
