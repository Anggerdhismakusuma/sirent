{{-- SI-RENT Auth Modal: Login / Register / Forgot Password --}}
<div x-data="{ show: false, mode: 'login' }"
     x-init="$watch('show', v => document.body.style.overflow = v ? 'hidden' : '')"
     x-on:open-auth-modal.window="mode = $event.detail.mode; show = true"
     x-on:close-auth-modal.window="show = false">

    {{-- Overlay --}}
    <div x-cloak x-show="show"
         class="position-fixed top-0 start-0 w-100 h-100"
         style="background:rgba(0,0,0,0.5); z-index:1055;"
         x-on:click.self="show = false"
         x-on:keydown.escape.window="show = false">

        <div class="bg-white shadow rounded-4 position-absolute top-50 start-50 translate-middle"
             style="width:500px; max-width:95vw;">

            {{-- Close --}}
            <button type="button"
                    class="position-absolute top-0 end-0 m-3 border-0 bg-transparent fs-3 text-muted"
                    style="z-index:1000; width:36px; height:36px; cursor:pointer; line-height:1;"
                    @click="show = false"
                    aria-label="{{ __('ui.close') }}">&times;</button>

            {{-- ============ LOGIN ============ --}}
            <div x-show="mode === 'login'" class="px-5 py-5">
                <h2 class="fw-semibold mb-1" style="font-size:28px;">{{ __('ui.welcome_back') }}</h2>
                <p class="text-black-50 mb-4" style="font-size:18px;">{{ __('ui.login_subtitle') }}</p>

                <form id="login-form" x-on:submit.prevent="submitLogin($el)">
                    @csrf
                    <div class="mb-3">
                        <label class="fw-medium mb-1" style="font-size:16px;">{{ __('ui.email') }}</label>
                        <input type="email" name="email" class="form-control" placeholder="{{ __('ui.enter_email') }}"
                               style="height:63px; border-radius:12px; border:0.2px solid #000; box-shadow:0px 4px 4px rgba(0,0,0,0.25); font-size:16px;" required>
                        <div class="invalid-feedback" id="login-email-error"></div>
                    </div>
                    <div class="mb-3">
                        <label class="fw-medium mb-1" style="font-size:16px;">{{ __('ui.password') }}</label>
                        <input type="password" name="password" class="form-control" placeholder="{{ __('ui.enter_password') }}"
                               style="height:63px; border-radius:12px; border:0.2px solid #000; box-shadow:0px 4px 4px rgba(0,0,0,0.25); font-size:16px;" required>
                        <div class="invalid-feedback" id="login-password-error"></div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3" style="font-size:14px;">
                        <label class="form-check-label d-flex align-items-center gap-1">
                            <input type="checkbox" name="remember" class="form-check-input me-1"> {{ __('ui.remember_me') }}
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
            <div x-show="mode === 'register'" class="px-5 py-5">
                <h2 class="fw-semibold mb-1" style="font-size:26px;">{{ __('ui.create_account') }}</h2>
                <p class="text-black-50 mb-4" style="font-size:16px;">{{ __('ui.register_subtitle') }}</p>

                <form id="register-form" x-on:submit.prevent="submitRegister($el)">
                    @csrf
                    <div class="mb-3">
                        <input type="text" name="name" class="form-control" placeholder="{{ __('ui.enter_name') }}"
                               style="height:55px; border-radius:12px; border:0.2px solid #000; box-shadow:0px 4px 4px rgba(0,0,0,0.25); font-size:14px;" required>
                        <div class="invalid-feedback" id="register-name-error"></div>
                    </div>
                    <div class="mb-3">
                        <input type="email" name="email" class="form-control" placeholder="{{ __('ui.enter_email_address') }}"
                               style="height:63px; border-radius:12px; border:0.2px solid #000; box-shadow:0px 4px 4px rgba(0,0,0,0.25); font-size:14px;" required>
                        <div class="invalid-feedback" id="register-email-error"></div>
                    </div>
                    <div class="mb-3">
                        <input type="password" name="password" class="form-control" placeholder="{{ __('ui.enter_your_password') }}"
                               style="height:63px; border-radius:12px; border:0.2px solid #000; box-shadow:0px 4px 4px rgba(0,0,0,0.25); font-size:14px;" required>
                        <div class="invalid-feedback" id="register-password-error"></div>
                    </div>
                    <div class="mb-3">
                        <input type="password" name="password_confirmation" class="form-control" placeholder="{{ __('ui.confirm_password') }}"
                               style="height:63px; border-radius:12px; border:0.2px solid #000; box-shadow:0px 4px 4px rgba(0,0,0,0.25); font-size:14px;" required>
                        <div class="invalid-feedback" id="register-password_confirmation-error"></div>
                    </div>
                    <input type="hidden" name="phone" value="081200000000">
                    <div class="mb-3" style="font-size:14px;">
                        <label class="form-check-label d-flex align-items-start gap-1">
                            <input type="checkbox" name="agree" class="form-check-input me-1 mt-1" required>
                            <span>{{ __('ui.agree_terms') }} <a href="#" class="text-decoration-none" style="color:var(--primary-blue);">{{ __('ui.terms_of_service') }}</a> {{ __('ui.and') }} <a href="#" class="text-decoration-none" style="color:var(--primary-blue);">{{ __('ui.privacy_policy') }}</a></span>
                        </label>
                    </div>
                    <button type="submit" class="btn w-100 text-white fw-bold" id="register-btn"
                            style="background:#3673fb; border-radius:12px; height:55px; font-size:20px; box-shadow:0px 4px 4px rgba(0,0,0,0.25);">{{ __('ui.register') }}</button>
                    <div id="register-spinner" class="text-center my-2" hidden>
                        <div class="spinner-border text-primary spinner-border-sm"></div>
                    </div>
                    <p class="text-center mt-4 mb-0" style="font-size:16px;">
                        {{ __('ui.have_account') }}
                        <a href="#" class="text-decoration-none fw-medium" style="color:var(--primary-blue);"
                           x-on:click.prevent="mode = 'login'">{{ __('ui.sign_in_link') }}</a>
                    </p>
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
                        <input type="email" name="email" class="form-control" placeholder="{{ __('ui.enter_email') }}"
                               style="height:63px; border-radius:12px; border:0.2px solid #000; font-size:14px;" required>
                        <div class="invalid-feedback" id="forgot-email-error"></div>
                    </div>
                    <button type="submit" class="btn w-100 text-white fw-bold"
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
