{{-- SI-RENT Auth Modal: Login / Register / Forgot Password --}}
<style>
    .fake-cursor {
        animation: blink 1s infinite;
    }

    @keyframes blink {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: 0;
        }
    }
</style>

<div x-data="{
    show: false,
    mode: 'login',
    emailForOtp: '',
    isLoading: false,

    submitRegisterAlpine(e) {
        const formData = new FormData(e.target);
        this.isLoading = true;

        document.querySelectorAll('.invalid-feedback').forEach(el => el.innerText = '');
        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

        fetch('{{ route('auth.register') }}', {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(async res => {
                const data = await res.json();
                if (!res.ok) throw data;
                return data;
            })
            .then(data => {
                if (data.success) {
                    this.mode = 'verify_otp';
                    this.emailForOtp = data.email;

                    // Auto focus ke input OTP tersembunyi
                    setTimeout(() => {
                        const inputReal = document.getElementById('otp-real-input');
                        if (inputReal) inputReal.focus();
                    }, 200);
                }
            })
            .catch(err => {
                if (err.errors) {
                    Object.keys(err.errors).forEach(key => {
                        const errorBox = document.getElementById('register-' + key + '-error');
                        const inputField = e.target.querySelector('[name=' + key + ']');
                        if (errorBox) errorBox.innerText = err.errors[key][0];
                        if (inputField) inputField.classList.add('is-invalid');
                    });
                } else {
                    alert(err.message || 'Terjadi kesalahan, silakan coba lagi.');
                }
            })
            .finally(() => {
                this.isLoading = false;
            });
    }
}" x-init="$watch('show', v => document.body.style.overflow = v ? 'hidden' : '')"
    x-on:open-auth-modal.window="mode = $event.detail.mode; if($event.detail.email) { emailForOtp = $event.detail.email }; show = true"
    x-on:close-auth-modal.window="show = false">

    {{-- Overlay --}}
    <div x-cloak x-show="show" class="position-fixed top-0 start-0 w-100 h-100"
        style="background:rgba(0,0,0,0.5); z-index:1055;" x-on:click.self="show = false"
        x-on:keydown.escape.window="show = false">

        <div class="bg-white shadow rounded-4 position-absolute top-50 start-50 translate-middle"
            style="width:500px; max-width:95vw;">

            {{-- Close --}}
            <button type="button" class="position-absolute top-0 end-0 m-3 border-0 bg-transparent fs-3 text-muted"
                style="z-index:1000; width:36px; height:36px; cursor:pointer; line-height:1;" @click="show = false"
                aria-label="{{ __('ui.close') }}">&times;</button>

            {{-- ============ LOGIN ============ --}}
            <div x-show="mode === 'login'" class="px-5 py-5">
                <h2 class="fw-semibold mb-1" style="font-size:28px;">{{ __('ui.welcome_back') }}</h2>
                <p class="text-black-50 mb-4" style="font-size:18px;">{{ __('ui.login_subtitle') }}</p>

                <form id="login-form" x-on:submit.prevent="submitLogin($el)">
                    @csrf
                    <div class="mb-3">
                        <label class="fw-medium mb-1" style="font-size:16px;">{{ __('ui.email') }}</label>
                        <input type="email" name="email" class="form-control"
                            placeholder="{{ __('ui.enter_email') }}"
                            style="height:63px; border-radius:12px; border:0.2px solid #000; box-shadow:0px 4px 4px rgba(0,0,0,0.25); font-size:16px;"
                            required>
                        <div class="invalid-feedback" id="login-email-error"></div>
                    </div>
                    <div class="mb-3">
                        <label class="fw-medium mb-1" style="font-size:16px;">{{ __('ui.password') }}</label>
                        <input type="password" name="password" class="form-control"
                            placeholder="{{ __('ui.enter_password') }}"
                            style="height:63px; border-radius:12px; border:0.2px solid #000; box-shadow:0px 4px 4px rgba(0,0,0,0.25); font-size:16px;"
                            required>
                        <div class="invalid-feedback" id="login-password-error"></div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3" style="font-size:14px;">
                        <label class="form-check-label d-flex align-items-center gap-1">
                            <input type="checkbox" name="remember" class="form-check-input me-1">
                            {{ __('ui.remember_me') }}
                        </label>
                        <a href="#" class="text-decoration-none" style="color:var(--primary-blue);"
                            x-on:click.prevent="mode = 'forgot'">{{ __('ui.forgot_password') }}</a>
                    </div>
                    <button type="submit" class="btn w-100 text-white fw-bold" id="login-btn"
                        style="background:#3673fb; border-radius:12px; height:63px; font-size:20px; box-shadow:0px 4px 4px rgba(0,0,0,0.25);">{{ __('ui.login') }}</button>
                    <div id="login-spinner" class="text-center my-2" hidden>
                        <div class="spinner-border text-primary spinner-border-sm"></div>
                    </div>
                    <p class="text-center mt-4 mb-0" style="font-size:16px;">
                        {{ __('ui.no_account') }}
                        <a href="#" class="text-decoration-none fw-medium" style="color:var(--primary-blue);"
                            x-on:click.prevent="mode = 'register'">{{ __('ui.sign_up_link') }}</a>
                    </p>
                </form>
            </div>

            {{-- ============ REGISTER ============ --}}
            <div x-show="mode === 'register'" class="px-5 py-5"
                style="width:500px; max-width:95vw; max-height:90vh; overflow-y:auto; display:flex; flex-direction:column;">
                <h2 class="fw-semibold mb-1" style="font-size:26px;">{{ __('ui.create_account') }}</h2>
                <p class="text-black-50 mb-4" style="font-size:16px;">{{ __('ui.register_subtitle') }}</p>

                <form id="register-form" x-on:submit.prevent="submitRegisterAlpine($event)">
                    @csrf
                    <div class="mb-3">
                        <input type="text" name="name" class="form-control"
                            placeholder="{{ __('ui.enter_name') }}"
                            style="height:55px; border-radius:12px; border:0.2px solid #000; box-shadow:0px 4px 4px rgba(0,0,0,0.25); font-size:14px;"
                            required>
                        <div class="invalid-feedback d-block" id="register-name-error"></div>
                    </div>
                    <div class="mb-3">
                        <input type="email" name="email" class="form-control"
                            placeholder="{{ __('ui.enter_email_address') }}"
                            style="height:63px; border-radius:12px; border:0.2px solid #000; box-shadow:0px 4px 4px rgba(0,0,0,0.25); font-size:14px;"
                            required>
                        <div class="invalid-feedback d-block" id="register-email-error"></div>
                    </div>
                    <div class="mb-3">
                        <input type="password" name="password" class="form-control"
                            placeholder="{{ __('ui.enter_your_password') }}"
                            style="height:63px; border-radius:12px; border:0.2px solid #000; box-shadow:0px 4px 4px rgba(0,0,0,0.25); font-size:14px;"
                            required>
                        <div class="invalid-feedback d-block" id="register-password-error"></div>
                    </div>
                    <div class="mb-3">
                        <input type="password" name="password_confirmation" class="form-control"
                            placeholder="{{ __('ui.confirm_password') }}"
                            style="height:63px; border-radius:12px; border:0.2px solid #000; box-shadow:0px 4px 4px rgba(0,0,0,0.25); font-size:14px;"
                            required>
                        <div class="invalid-feedback d-block" id="register-password_confirmation-error"></div>
                    </div>
                    <input type="hidden" name="phone" value="081200000000">
                    <div class="mb-3" style="font-size:14px;">
                        <label class="form-check-label d-flex align-items-start gap-1">
                            <input type="checkbox" name="agree" class="form-check-input me-1 mt-1" required>
                            <span>{{ __('ui.agree_terms') }} <a href="#" class="text-decoration-none"
                                    style="color:var(--primary-blue);">{{ __('ui.terms_of_service') }}</a>
                                {{ __('ui.and') }} <a href="#" class="text-decoration-none"
                                    style="color:var(--primary-blue);">{{ __('ui.privacy_policy') }}</a></span>
                        </label>
                    </div>
                    <button type="submit" class="btn w-100 text-white fw-bold" :disabled="isLoading"
                        style="background:#3673fb; border-radius:12px; height:55px; font-size:20px; box-shadow:0px 4px 4px rgba(0,0,0,0.25);">{{ __('ui.register') }}</button>

                    <div class="text-center my-2" x-show="isLoading">
                        <div class="spinner-border text-primary spinner-border-sm"></div>
                    </div>
                    <p class="text-center mt-4 mb-0" style="font-size:16px;">
                        {{ __('ui.have_account') }}
                        <a href="#" class="text-decoration-none fw-medium" style="color:var(--primary-blue);"
                            x-on:click.prevent="mode = 'login'">{{ __('ui.sign_in_link') }}</a>
                    </p>
                </form>
            </div>

            {{-- ============ VERIFY OTP ============ --}}
            <div x-show="mode === 'verify_otp'" class="px-5 py-5" x-cloak>
                <h2 class="fw-semibold mb-1" style="font-size:26px;">Verifikasi Email</h2>
                <p class="text-black-50 mb-4" style="font-size:16px;">
                    Kami telah mengirimkan 6 digit kode OTP ke email <strong x-text="emailForOtp"></strong>.
                </p>

                <form id="otp-form" @submit.prevent="submitOtp($el)">
                    @csrf
                    <input type="hidden" name="email" :value="emailForOtp">

                    <div class="mb-4" x-data="{
                        otpCode: '',
                        updateOtp(val) {
                            this.otpCode = val.replace(/[^0-9]/g, '').substring(0, 6);
                        }
                    }">
                        <input type="text" name="otp" id="otp-real-input" maxlength="6" inputmode="numeric"
                            pattern="[0-9]*" :value="otpCode" @input="updateOtp($event.target.value)"
                            class="position-absolute opacity-0" style="width: 1px; height: 1px; z-index: -1;" required
                            autofocus>

                        {{-- DIUBAH: Memperbaiki tag container @click double yang sempat double/bermprosal --}}
                        <div class="d-flex justify-content-center gap-2"
                            @click="document.getElementById('otp-real-input').focus()">
                            <template x-for="i in 6">
                                <div class="d-flex align-items-center justify-content-center fw-bold fs-3 position-relative transition-all"
                                    :class="otpCode.length >= i ? 'border-primary shadow-sm' : 'border-secondary-subtle'"
                                    style="width: 55px; height: 60px; border: 2px solid; border-radius: 12px; background: var(--bg-surface); cursor: text;"
                                    x-text="otpCode[i-1] || ''">

                                    {{-- Fake Blinking Cursor: Muncul cuma di kotak kosong pertama yang aktif --}}
                                    <template x-if="document.activeElement === document.getElementById('otp-real-input') && otpCode.length === (i - 1)">
                                        <div class="position-absolute bg-primary fake-cursor"
                                            style="width: 2px; height: 24px;"></div>
                                    </template>

                                </div>
                            </template>
                        </div>

                        <div class="invalid-feedback text-center d-block mt-3" id="otp-error"></div>
                    </div>

                    <button type="submit" class="btn w-100 text-white fw-bold" id="otp-btn"
                        style="background:#3673fb; border-radius:12px; height:55px; font-size:20px; box-shadow:0px 4px 4px rgba(0,0,0,0.25);">
                        Verifikasi Kode
                    </button>

                    <div id="otp-spinner" class="text-center my-2" hidden>
                        <div class="spinner-border text-primary spinner-border-sm"></div>
                    </div>
                </form>
            </div>

            {{-- ============ FORGOT PASSWORD ============ --}}
            <div x-show="mode === 'forgot'" class="px-5 py-5">
                <h2 class="fw-semibold mb-1" style="font-size:26px;">{{ __('ui.reset_password') }}</h2>
                <p class="text-black-50 mb-4" style="font-size:16px;">{{ __('ui.forgot_password_subtitle') }}</p>

                <form id="forgot-form" x-on:submit.prevent="submitForgot($el)">
                    @csrf
                    <div class="mb-3">
                        <label class="fw-medium mb-1" style="font-size:16px;">{{ __('ui.email') }}</label>
                        <input type="email" name="email" class="form-control"
                            placeholder="{{ __('ui.enter_email') }}"
                            style="height:63px; border-radius:12px; border:0.2px solid #000; font-size:14px;" required>
                        <div class="invalid-feedback" id="forgot-email-error"></div>
                    </div>
                    <button type="submit" class="btn w-100 text-white fw-bold" id="forgot-btn"
                        style="background:#3673fb; border-radius:12px; height:63px; font-size:20px;">{{ __('ui.send_reset_link') }}</button>
                    <div id="forgot-message" class="mt-3"></div>
                </form>

                <p class="text-center mt-4 mb-0" style="font-size:14px;">
                    <a href="#" class="text-decoration-none" style="color:var(--primary-blue);"
                        x-on:click.prevent="mode = 'login'">&larr; {{ __('ui.back_to_login') }}</a>
                </p>
            </div>
        </div>
    </div>
</div>

<script>
    // TAHAP 2: Kirim 6 Digit OTP ke Backend & Redirect
    function submitOtp(formElement) {
        const formData = new FormData(formElement);
        const submitBtn = document.getElementById('otp-btn');
        const spinner = document.getElementById('otp-spinner');
        const errorBox = document.getElementById('otp-error');
        const inputReal = document.getElementById('otp-real-input');

        if (submitBtn) submitBtn.disabled = true;
        if (spinner) spinner.hidden = false;
        if (errorBox) errorBox.innerText = '';

        fetch("{{ route('auth.verify-otp') }}", {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(async res => {
                const data = await res.json();
                if (!res.ok) throw data;
                return data;
            })
            .then(data => {
                if (data.success) {
                    window.location.href = data.redirect;
                }
            })
            .catch(err => {
                if (errorBox) errorBox.innerText = err.message || 'Kode OTP salah.';
                if (inputReal) inputReal.classList.add('is-invalid');
            })
            .finally(() => {
                if (submitBtn) submitBtn.disabled = false;
                if (spinner) spinner.hidden = true;
            });
    }

    // TAHAP EXTRA: Handler Login Lama
    function submitLogin(formElement) {
        const formData = new FormData(formElement);
        const submitBtn = document.getElementById('login-btn');
        const spinner = document.getElementById('login-spinner');

        if (submitBtn) submitBtn.disabled = true;
        if (spinner) spinner.hidden = false;

        fetch("{{ route('auth.login') }}", {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(async res => {
                const data = await res.json();
                if (!res.ok) throw data;
                return data;
            })
            .then(data => {
                if (data.success) window.location.reload();
            })
            .catch(err => {
                if (err.errors) {
                    Object.keys(err.errors).forEach(key => {
                        const errorBox = document.getElementById(`login-${key}-error`);
                        if (errorBox) errorBox.innerText = err.errors[key][0];
                    });
                } else {
                    alert(err.message || 'Login gagal.');
                }
            })
            .finally(() => {
                if (submitBtn) submitBtn.disabled = false;
                if (spinner) spinner.hidden = true;
            });
    }

    // TAHAP EXTRA: Handler Forgot Password Lama
    function submitForgot(formElement) {
        const formData = new FormData(formElement);
        const msgBox = document.getElementById('forgot-message');

        fetch("{{ route('auth.forgot-password') }}", {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(async res => {
                const data = await res.json();
                msgBox.innerHTML =
                    `<div class="alert alert-${res.ok ? 'success' : 'danger'}">${data.message}</div>`;
            });
    }
</script>