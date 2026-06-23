{{-- SI-RENT Footer — static, no props --}}
<footer class="bg-light pt-5 pb-4 border-top">
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-4">
                <span class="font-logo fs-3" style="color:var(--primary-blue);">SI-RENT</span>
                <p class="text-muted mt-2" style="font-size:14px;">
                    {{ __('ui.footer_tagline') }}<br>
                    {{ __('ui.footer_subtagline') }}
                </p>
            </div>
            <div class="col-md-2 mb-3">
                <h6 class="fw-bold mb-3">{{ __('ui.menu') }}</h6>
                <ul class="list-unstyled" style="font-size:14px;">
                    <li class="mb-2"><a href="{{ url('/') }}" class="text-muted text-decoration-none">{{ __('ui.home') }}</a></li>
                    <li class="mb-2"><a href="{{ url('/produk') }}" class="text-muted text-decoration-none">{{ __('ui.browse') }}</a></li>
                    <li class="mb-2"><a href="{{ url('/about-us') }}" class="text-muted text-decoration-none">{{ __('ui.about_us') }}</a></li>
                </ul>
            </div>
            <div class="col-md-2 mb-3">
                <h6 class="fw-bold mb-3">{{ __('ui.categories') }}</h6>
                <ul class="list-unstyled" style="font-size:14px;">
                    <li class="mb-2"><a href="{{ url('/kategori/kamera') }}" class="text-muted text-decoration-none">{{ __('ui.camera') }}</a></li>
                    <li class="mb-2"><a href="{{ url('/kategori/drone') }}" class="text-muted text-decoration-none">{{ __('ui.drone') }}</a></li>
                    <li class="mb-2"><a href="{{ url('/kategori/alat-musik') }}" class="text-muted text-decoration-none">{{ __('ui.music_instruments') }}</a></li>
                    <li class="mb-2"><a href="{{ url('/kategori/gaming') }}" class="text-muted text-decoration-none">{{ __('ui.gaming') }}</a></li>
                </ul>
            </div>
            <div class="col-md-2 mb-3">
                <h6 class="fw-bold mb-3">{{ __('ui.help') }}</h6>
                <ul class="list-unstyled" style="font-size:14px;">
                    <li class="mb-2"><a href="#" class="text-muted text-decoration-none">{{ __('ui.help_center') }}</a></li>
                    <li class="mb-2"><a href="#" class="text-muted text-decoration-none">{{ __('ui.terms_conditions') }}</a></li>
                    <li class="mb-2"><a href="#" class="text-muted text-decoration-none">{{ __('ui.privacy_policy') }}</a></li>
                </ul>
            </div>
            <div class="col-md-2 mb-3">
                <h6 class="fw-bold mb-3">{{ __('ui.contact') }}</h6>
                <ul class="list-unstyled" style="font-size:14px;">
                    <li class="mb-2"><a href="mailto:support@sirent.id" class="text-muted text-decoration-none">support@sirent.id</a></li>
                    <li class="mb-2"><span class="text-muted">+62 812-3456-7890</span></li>
                </ul>
            </div>
        </div>
        <hr>
        <div class="text-center text-muted" style="font-size:13px;">
            &copy; {{ date('Y') }} SI-RENT. {{ __('ui.all_rights_reserved') }}
        </div>
    </div>
</footer>
