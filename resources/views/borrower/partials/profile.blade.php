{{-- SI-RENT Dashboard: Profile Tab --}}
@php
    $userName = $user->name;
    $userEmail = $user->email;
    $userPhone = $user->phone;
    $userDob = $user->dob ? \Carbon\Carbon::parse($user->dob)->format('Y-m-d') : '';
    $userDomicile = $user->domicile ?? '';
    $userGender = $user->gender ?? '';
    $userBio = $user->bio ?? '';
    $userEmailVerified = $user->hasVerifiedEmail();
    $userWhatsappVerified = !is_null($user->whatsapp_verified_at);
    $currentLocale = app()->getLocale();
@endphp

<div x-data="profileEditor" x-cloak>
<div class="row">
    <div class="col-lg-8 mb-4">
        <div class="bg-white rounded-4 p-4 shadow-sm border" style="border-color: var(--border-default);">

            {{-- Saved indicator --}}
            <div x-show="saved" x-cloak
                 class="d-inline-flex align-items-center gap-1 float-end text-success fw-semibold"
                 style="font-family:'Mona Sans',sans-serif; font-size:13px;">
                <i class="bi bi-check-circle-fill"></i> {{ __('ui.saved') }}
            </div>

            <h4 class="fw-semibold mb-3" style="font-family:'Mona Sans',sans-serif; font-size:24px; color: var(--primary-blue-light);">{{ __('ui.edit_personal_data') }}</h4>

            {{-- Name --}}
            <div class="row mb-2">
                <div class="col-sm-4"><span style="font-family:'Mona Sans',sans-serif; font-size:15px; color: var(--text-primary);">{{ __('ui.name') }}</span></div>
                <div class="col-sm-6">
                    <span x-show="editing !== 'name'" style="font-family:'Mona Sans',sans-serif; font-size:15px; color: var(--text-primary);" x-text="name"></span>
                    <div x-show="editing === 'name'" x-cloak>
                        <input type="text" x-model="name" class="form-control form-control-sm"
                               :class="{'is-invalid': errors.name}"
                               style="font-family:'Mona Sans',sans-serif; font-size:14px;">
                        <div class="invalid-feedback" x-show="errors.name" x-text="errors.name?.[0]" style="font-size:12px;"></div>
                    </div>
                </div>
                <div class="col-sm-2 text-end">
                    <template x-if="editing !== 'name'">
                        <a href="#" @click.prevent="startEdit('name')" style="font-family:'Mona Sans',sans-serif; font-size:13px; color: var(--primary-blue-light); text-decoration:none;">{{ __('ui.edit') }}</a>
                    </template>
                    <template x-if="editing === 'name'">
                        <div class="d-flex gap-1 justify-content-end">
                            <button @click="saveField()" class="btn btn-sm btn-primary rounded-pill px-2" :disabled="saving"
                                    style="font-family:'Mona Sans',sans-serif; font-size:12px;">
                                <span x-show="!saving">{{ __('ui.save') }}</span>
                                <span x-show="saving" class="spinner-border spinner-border-sm" role="status"></span>
                            </button>
                            <button @click="cancelEdit()" class="btn btn-sm btn-light rounded-pill px-2" :disabled="saving"
                                    style="font-family:'Mona Sans',sans-serif; font-size:12px;">{{ __('ui.cancel') }}</button>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Date of Birth --}}
            <div class="row mb-2">
                <div class="col-sm-4"><span style="font-family:'Mona Sans',sans-serif; font-size:15px; color: var(--text-primary);">{{ __('ui.dob') }}</span></div>
                <div class="col-sm-6">
                    <span x-show="editing !== 'dob'" style="font-family:'Mona Sans',sans-serif; font-size:15px; color: var(--text-primary);" x-text="formatDob(dob)"></span>
                    <div x-show="editing === 'dob'" x-cloak>
                        <input type="date" x-model="dob" class="form-control form-control-sm"
                               :class="{'is-invalid': errors.dob}"
                               style="font-family:'Mona Sans',sans-serif; font-size:14px;">
                        <div class="invalid-feedback" x-show="errors.dob" x-text="errors.dob?.[0]" style="font-size:12px;"></div>
                    </div>
                </div>
                <div class="col-sm-2 text-end">
                    <template x-if="editing !== 'dob'">
                        <a href="#" @click.prevent="startEdit('dob')" style="font-family:'Mona Sans',sans-serif; font-size:13px; color: var(--primary-blue-light); text-decoration:none;">{{ __('ui.edit') }}</a>
                    </template>
                    <template x-if="editing === 'dob'">
                        <div class="d-flex gap-1 justify-content-end">
                            <button @click="saveField()" class="btn btn-sm btn-primary rounded-pill px-2" :disabled="saving"
                                    style="font-family:'Mona Sans',sans-serif; font-size:12px;">
                                <span x-show="!saving">{{ __('ui.save') }}</span>
                                <span x-show="saving" class="spinner-border spinner-border-sm" role="status"></span>
                            </button>
                            <button @click="cancelEdit()" class="btn btn-sm btn-light rounded-pill px-2" :disabled="saving"
                                    style="font-family:'Mona Sans',sans-serif; font-size:12px;">{{ __('ui.cancel') }}</button>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Gender --}}
            <div class="row mb-2">
                <div class="col-sm-4"><span style="font-family:'Mona Sans',sans-serif; font-size:15px; color: var(--text-primary);">{{ __('ui.gender') }}</span></div>
                <div class="col-sm-6">
                    <span x-show="editing !== 'gender'" style="font-family:'Mona Sans',sans-serif; font-size:15px; color: var(--text-primary);" x-text="genderLabel(gender)"></span>
                    <div x-show="editing === 'gender'" x-cloak>
                        <select x-model="gender" class="form-select form-select-sm"
                                :class="{'is-invalid': errors.gender}"
                                style="font-family:'Mona Sans',sans-serif; font-size:14px;">
                            <option value="">{{ __('ui.select_gender') }}</option>
                            <option value="male">{{ __('ui.male') }}</option>
                            <option value="female">{{ __('ui.female') }}</option>
                            <option value="other">{{ __('ui.other') }}</option>
                        </select>
                        <div class="invalid-feedback" x-show="errors.gender" x-text="errors.gender?.[0]" style="font-size:12px;"></div>
                    </div>
                </div>
                <div class="col-sm-2 text-end">
                    <template x-if="editing !== 'gender'">
                        <a href="#" @click.prevent="startEdit('gender')" style="font-family:'Mona Sans',sans-serif; font-size:13px; color: var(--primary-blue-light); text-decoration:none;">{{ __('ui.edit') }}</a>
                    </template>
                    <template x-if="editing === 'gender'">
                        <div class="d-flex gap-1 justify-content-end">
                            <button @click="saveField()" class="btn btn-sm btn-primary rounded-pill px-2" :disabled="saving"
                                    style="font-family:'Mona Sans',sans-serif; font-size:12px;">
                                <span x-show="!saving">{{ __('ui.save') }}</span>
                                <span x-show="saving" class="spinner-border spinner-border-sm" role="status"></span>
                            </button>
                            <button @click="cancelEdit()" class="btn btn-sm btn-light rounded-pill px-2" :disabled="saving"
                                    style="font-family:'Mona Sans',sans-serif; font-size:12px;">{{ __('ui.cancel') }}</button>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Location (Domicile) --}}
            <div class="row mb-4">
                <div class="col-sm-4"><span style="font-family:'Mona Sans',sans-serif; font-size:15px; color: var(--text-primary);">{{ __('ui.location') }}</span></div>
                <div class="col-sm-6">
                    <span x-show="editing !== 'domicile'" style="font-family:'Mona Sans',sans-serif; font-size:15px; color: var(--text-primary);" x-text="domicile || '-'"></span>
                    <div x-show="editing === 'domicile'" x-cloak>
                        <input type="text" x-model="domicile" class="form-control form-control-sm"
                               :class="{'is-invalid': errors.domicile}"
                               placeholder="e.g. Bogor, Jawa Barat"
                               style="font-family:'Mona Sans',sans-serif; font-size:14px;">
                        <div class="invalid-feedback" x-show="errors.domicile" x-text="errors.domicile?.[0]" style="font-size:12px;"></div>
                    </div>
                </div>
                <div class="col-sm-2 text-end">
                    <template x-if="editing !== 'domicile'">
                        <a href="#" @click.prevent="startEdit('domicile')" style="font-family:'Mona Sans',sans-serif; font-size:13px; color: var(--primary-blue-light); text-decoration:none;">{{ __('ui.edit') }}</a>
                    </template>
                    <template x-if="editing === 'domicile'">
                        <div class="d-flex gap-1 justify-content-end">
                            <button @click="saveField()" class="btn btn-sm btn-primary rounded-pill px-2" :disabled="saving"
                                    style="font-family:'Mona Sans',sans-serif; font-size:12px;">
                                <span x-show="!saving">{{ __('ui.save') }}</span>
                                <span x-show="saving" class="spinner-border spinner-border-sm" role="status"></span>
                            </button>
                            <button @click="cancelEdit()" class="btn btn-sm btn-light rounded-pill px-2" :disabled="saving"
                                    style="font-family:'Mona Sans',sans-serif; font-size:12px;">{{ __('ui.cancel') }}</button>
                        </div>
                    </template>
                </div>
            </div>

            <hr style="border-color: var(--border-light);">

            <h4 class="fw-semibold mb-3 mt-4" style="font-family:'Mona Sans',sans-serif; font-size:24px; color: var(--primary-blue-light);">{{ __('ui.edit_contact') }}</h4>

            {{-- Email --}}
            <div class="row mb-2">
                <div class="col-sm-4"><span style="font-family:'Mona Sans',sans-serif; font-size:15px; color: var(--text-primary);">{{ __('ui.email') }}</span></div>
                <div class="col-sm-4">
                    <span x-show="editing !== 'email'" style="font-family:'Mona Sans',sans-serif; font-size:15px; color: var(--text-primary);" x-text="email"></span>
                    <div x-show="editing === 'email'" x-cloak>
                        <input type="email" x-model="email" class="form-control form-control-sm"
                               :class="{'is-invalid': errors.email}"
                               style="font-family:'Mona Sans',sans-serif; font-size:14px;">
                        <div class="invalid-feedback" x-show="errors.email" x-text="errors.email?.[0]" style="font-size:12px;"></div>
                        <div x-show="email !== originalEmail" class="form-text mt-1" style="font-size:11px; color: #e67e22;">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i>{{ __('ui.email_change_note') }}
                        </div>
                    </div>
                </div>
                <div class="col-sm-4 text-end">
                    <span x-show="editing !== 'email'"
                          class="d-inline-block px-2 py-0 rounded-2 fw-bold me-1"
                          :class="emailVerified ? 'bg-success' : 'bg-warning'"
                          :style="emailVerified ? 'background:#b5d4ff!important; color:#0a1e78!important; font-family:\'Mona Sans\',sans-serif; font-size:13px;' : 'background:#fff3cd!important; color:#856404!important; font-family:\'Mona Sans\',sans-serif; font-size:13px;'">
                        <span x-show="emailVerified">{{ __('ui.verified') }}</span>
                        <span x-show="!emailVerified">{{ __('ui.unverified') }}</span>
                    </span>
                    {{-- Verify Email action when unverified (read mode) --}}
                    <template x-if="editing !== 'email' && !emailVerified">
                        <a href="#" @click.prevent="sendVerificationEmail()"
                           class="me-1"
                           :class="verifyingEmail ? 'disabled text-muted' : ''"
                           style="font-family:'Mona Sans',sans-serif; font-size:13px; color: var(--primary-blue-light); text-decoration:none;">
                            <span x-show="!verifyingEmail">{{ __('ui.verify_email') }}</span>
                            <span x-show="verifyingEmail" class="spinner-border spinner-border-sm" role="status" style="width:12px;height:12px;"></span>
                        </a>
                    </template>
                    {{-- Check verification status after sending --}}
                    <template x-if="editing !== 'email' && !emailVerified && verificationSent">
                        <a href="#" @click.prevent="checkVerificationStatus()"
                           class="me-1"
                           :class="checkingVerification ? 'disabled text-muted' : ''"
                           style="font-family:'Mona Sans',sans-serif; font-size:13px; color: #28a745; text-decoration:none;">
                            <span x-show="!checkingVerification">{{ __('ui.check_verification') }}</span>
                            <span x-show="checkingVerification" style="font-size:12px;">{{ __('ui.verification_checking') }}</span>
                        </a>
                    </template>
                    <template x-if="editing !== 'email'">
                        <a href="#" @click.prevent="startEdit('email')" style="font-family:'Mona Sans',sans-serif; font-size:13px; color: var(--text-primary); text-decoration:none;">{{ __('ui.edit') }}</a>
                    </template>
                    <template x-if="editing === 'email'">
                        <div class="d-flex gap-1 justify-content-end">
                            <button @click="saveField()" class="btn btn-sm btn-primary rounded-pill px-2" :disabled="saving"
                                    style="font-family:'Mona Sans',sans-serif; font-size:12px;">
                                <span x-show="!saving">{{ __('ui.save') }}</span>
                                <span x-show="saving" class="spinner-border spinner-border-sm" role="status"></span>
                            </button>
                            <button @click="cancelEdit()" class="btn btn-sm btn-light rounded-pill px-2" :disabled="saving"
                                    style="font-family:'Mona Sans',sans-serif; font-size:12px;">{{ __('ui.cancel') }}</button>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Phone --}}
            <div class="row">
                <div class="col-sm-4"><span style="font-family:'Mona Sans',sans-serif; font-size:15px; color: var(--text-primary);">{{ __('ui.phone_number') }}</span></div>
                <div class="col-sm-4">
                    <span x-show="editing !== 'phone'" style="font-family:'Mona Sans',sans-serif; font-size:15px; color: var(--text-primary);" x-text="phone"></span>
                    <div x-show="editing === 'phone'" x-cloak>
                        <input type="tel" x-model="phone" class="form-control form-control-sm"
                               :class="{'is-invalid': errors.phone}"
                               style="font-family:'Mona Sans',sans-serif; font-size:14px;">
                        <div class="invalid-feedback" x-show="errors.phone" x-text="errors.phone?.[0]" style="font-size:12px;"></div>
                    </div>
                </div>
                <div class="col-sm-4 text-end">
                    <template x-if="editing !== 'phone'">
                        <a href="#" @click.prevent="startEdit('phone')" style="font-family:'Mona Sans',sans-serif; font-size:13px; color: var(--text-primary); text-decoration:none;">{{ __('ui.edit') }}</a>
                    </template>
                    <template x-if="editing === 'phone'">
                        <div class="d-flex gap-1 justify-content-end">
                            <button @click="saveField()" class="btn btn-sm btn-primary rounded-pill px-2" :disabled="saving"
                                    style="font-family:'Mona Sans',sans-serif; font-size:12px;">
                                <span x-show="!saving">{{ __('ui.save') }}</span>
                                <span x-show="saving" class="spinner-border spinner-border-sm" role="status"></span>
                            </button>
                            <button @click="cancelEdit()" class="btn btn-sm btn-light rounded-pill px-2" :disabled="saving"
                                    style="font-family:'Mona Sans',sans-serif; font-size:12px;">{{ __('ui.cancel') }}</button>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    {{-- Trust Score --}}
    <div class="col-lg-4 mb-4">
        <div class="bg-white rounded-4 p-4 shadow-sm border d-flex align-items-center gap-3" style="border-color: var(--border-default);">
            <div>
                <div class="fw-bold" style="font-family:'Mona Sans',sans-serif; font-size:40px; color: var(--text-primary);">{{ $trustScore }}</div>
                <div class="fw-semibold" style="font-family:'Mona Sans',sans-serif; font-size:16px; color: var(--text-primary);">{{ __('ui.trust_score') }}</div>
                <div style="font-family:'Mona Sans',sans-serif; font-size:16px; color: var(--text-tertiary);">{{ $trustScore >= 80 ? __('ui.very_trusted') : ($trustScore >= 50 ? __('ui.trusted') : __('ui.new_member')) }}</div>
            </div>
            <div class="ms-auto" style="width:127px; height:75px;">
                <svg viewBox="0 0 127 75" width="127" height="75">
                    <path d="M10 65 A55 55 0 0 1 117 65" fill="none" stroke="#e0e0e0" stroke-width="12" stroke-linecap="round"/>
                    <path d="M10 65 A55 55 0 0 1 117 65" fill="none" stroke="#0031e1" stroke-width="12" stroke-linecap="round"
                          stroke-dasharray="{{ $trustScore * 1.7 }}, 300" />
                </svg>
            </div>
        </div>
    </div>
</div>
</div>

{{-- Alpine component definition --}}
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('profileEditor', () => ({
        // Editable fields
        name: @js($userName),
        email: @js($userEmail),
        phone: @js($userPhone),
        dob: @js($userDob),
        domicile: @js($userDomicile),
        gender: @js($userGender),
        bio: @js($userBio),

        // Original values
        originalEmail: @js($userEmail),

        // UI state
        editing: null,
        saving: false,
        saved: false,
        errors: {},
        uploadingAvatar: false,
        verifyingEmail: false,
        verificationSent: false,
        checkingVerification: false,
        emailVerified: @js($userEmailVerified),
        whatsappVerified: @js($userWhatsappVerified),

        startEdit(field) {
            this.editing = field;
            this.errors = {};
            this.saved = false;
        },

        cancelEdit() {
            this.name = @js($userName);
            this.email = @js($userEmail);
            this.phone = @js($userPhone);
            this.dob = @js($userDob);
            this.domicile = @js($userDomicile);
            this.gender = @js($userGender);
            this.bio = @js($userBio);
            this.editing = null;
            this.errors = {};
        },

        async sendVerificationEmail() {
            if (this.verifyingEmail) return;
            this.verifyingEmail = true;
            this.verificationSent = false;

            const csrf = document.querySelector('meta[name=csrf-token]').content;

            try {
                const res = await fetch(@js(route('borrower.profile.send-email-verification')), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                    },
                });

                const data = await res.json();
                this.verifyingEmail = false;

                if (data.status === 'already_verified') {
                    this.emailVerified = true;
                    Swal.fire({
                        icon: 'info',
                        title: @js(__('ui.verified')),
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false,
                    });
                    return;
                }

                if (data.success) {
                    this.verificationSent = true;
                    Swal.fire({
                        icon: 'success',
                        title: @js(__('ui.success')),
                        text: data.message,
                        timer: 3000,
                        showConfirmButton: false,
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: @js(__('ui.oops')),
                        text: data.message || @js(__('ui.error_try_again')),
                        confirmButtonColor: '#0031e1',
                    });
                }
            } catch (err) {
                this.verifyingEmail = false;
                Swal.fire({
                    icon: 'error',
                    title: @js(__('ui.network_error_title')),
                    text: @js(__('ui.verify_email_failed')),
                    confirmButtonColor: '#0031e1',
                });
            }
        },

        async checkVerificationStatus() {
            if (this.checkingVerification) return;
            this.checkingVerification = true;

            try {
                const res = await fetch('/api/user/check-email-status', {
                    headers: { 'Accept': 'application/json' },
                });

                const data = await res.json();
                this.checkingVerification = false;

                if (data.verified) {
                    this.emailVerified = true;
                    this.verificationSent = false;
                    Swal.fire({
                        icon: 'success',
                        title: @js(__('ui.verified')),
                        text: @js(__('ui.email_verified')),
                        timer: 2000,
                        showConfirmButton: false,
                    });
                } else {
                    Swal.fire({
                        icon: 'info',
                        title: @js(__('ui.unverified')),
                        text: @js(__('ui.email_change_note')),
                        timer: 2000,
                        showConfirmButton: false,
                    });
                }
            } catch (err) {
                this.checkingVerification = false;
                Swal.fire({
                    icon: 'error',
                    title: @js(__('ui.network_error_title')),
                    text: @js(__('ui.error_try_again')),
                    confirmButtonColor: '#0031e1',
                });
            }
        },

        async saveField() {
            this.saving = true;
            this.errors = {};
            this.saved = false;

            const csrf = document.querySelector('meta[name=csrf-token]').content;

            try {
                const res = await fetch(@js(route('borrower.profile.update-info')), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                    },
                    body: JSON.stringify({
                        name: this.name,
                        email: this.email,
                        phone: this.phone,
                        dob: this.dob || null,
                        domicile: this.domicile || null,
                        gender: this.gender || null,
                        bio: this.bio || null,
                    }),
                });

                const data = await res.json();

                if (!res.ok && res.status === 422) {
                    this.errors = data.errors || {};
                    this.saving = false;
                    return;
                }

                if (!data.success) {
                    this.saving = false;
                    Swal.fire({
                        icon: 'error',
                        title: @js(__('ui.oops')),
                        text: data.message || @js(__('ui.error_try_again')),
                        confirmButtonColor: '#0031e1',
                    });
                    return;
                }

                this.name = data.data.name;
                this.email = data.data.email;
                this.phone = data.data.phone;
                this.dob = data.data.dob || '';
                this.domicile = data.data.domicile || '';
                this.gender = data.data.gender || '';
                this.bio = data.data.bio || '';
                this.emailVerified = data.data.email_verified;
                this.whatsappVerified = data.data.whatsapp_verified;

                this.editing = null;
                this.saving = false;
                this.saved = true;

                // Update banner name and location
                const bannerName = document.getElementById('banner-user-name');
                const bannerLoc = document.getElementById('banner-user-location');
                if (bannerName) bannerName.textContent = this.name;
                if (bannerLoc) bannerLoc.textContent = this.domicile || '-';

                setTimeout(() => { this.saved = false; }, 2000);
            } catch (err) {
                this.saving = false;
                Swal.fire({
                    icon: 'error',
                    title: @js(__('ui.network_error_title')),
                    text: @js(__('ui.network_error')),
                    confirmButtonColor: '#0031e1',
                });
            }
        },

        formatDob(dateStr) {
            if (!dateStr) return '-';
            const d = new Date(dateStr + 'T00:00:00');
            const locale = @js($currentLocale === 'id' ? 'id-ID' : 'en-US');
            return d.toLocaleDateString(locale, { day: 'numeric', month: 'long', year: 'numeric' });
        },

        genderLabel(g) {
            if (g === 'male') return @js(__('ui.male'));
            if (g === 'female') return @js(__('ui.female'));
            if (g === 'other') return @js(__('ui.other'));
            return '-';
        },

        async uploadAvatar(event) {
            const file = event.target.files[0];
            if (!file) return;

            if (file.size > 2 * 1024 * 1024) {
                Swal.fire({
                    icon: 'error',
                    title: @js(__('ui.oops')),
                    text: @js(__('ui.photo_too_large')),
                    confirmButtonColor: '#0031e1',
                });
                event.target.value = '';
                return;
            }

            this.uploadingAvatar = true;
            const formData = new FormData();
            formData.append('avatar', file);
            formData.append('_token', document.querySelector('meta[name=csrf-token]').content);

            try {
                const res = await fetch(@js(route('borrower.profile.update-avatar')), {
                    method: 'POST',
                    headers: { 'Accept': 'application/json' },
                    body: formData,
                });

                const data = await res.json();
                this.uploadingAvatar = false;
                event.target.value = '';

                if (!data.success) {
                    Swal.fire({
                        icon: 'error',
                        title: @js(__('ui.oops')),
                        text: data.message || @js(__('ui.photo_upload_error')),
                        confirmButtonColor: '#0031e1',
                    });
                    return;
                }

                const newSrc = '/storage/' + data.data.avatar + '?t=' + Date.now();

                document.querySelectorAll('.user-avatar-img').forEach(el => {
                    el.src = newSrc;
                    el.style.display = '';
                });

                document.querySelectorAll('.avatar-fallback').forEach(el => {
                    const img = document.createElement('img');
                    img.src = newSrc;
                    img.alt = @js($userName);
                    img.className = 'rounded-circle object-fit-cover user-avatar-img';
                    img.style.cssText = el.style.cssText;
                    el.replaceWith(img);
                });

                Swal.fire({
                    icon: 'success',
                    title: @js(__('ui.success')),
                    text: data.message,
                    timer: 1500,
                    showConfirmButton: false,
                });
            } catch (err) {
                this.uploadingAvatar = false;
                event.target.value = '';
                Swal.fire({
                    icon: 'error',
                    title: @js(__('ui.network_error_title')),
                    text: @js(__('ui.photo_upload_error')),
                    confirmButtonColor: '#0031e1',
                });
            }
        },

        init() {
            window.__profileComponent = this;
        },
    }));
});
</script>
