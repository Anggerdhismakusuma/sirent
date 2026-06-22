{{-- SI-RENT Dashboard: Profile Tab --}}
<div class="row">
    <div class="col-lg-8 mb-4">
        <div class="bg-white rounded-4 p-4 shadow-sm border" style="border-color: var(--border-default);">
            <h4 class="fw-semibold mb-3" style="font-family:'Mona Sans',sans-serif; font-size:24px; color: var(--primary-blue-light);">{{ __('ui.edit_personal_data') }}</h4>

            <div class="row mb-2">
                <div class="col-sm-4"><span style="font-family:'Mona Sans',sans-serif; font-size:15px; color: var(--text-primary);">{{ __('ui.name') }}</span></div>
                <div class="col-sm-6"><span style="font-family:'Mona Sans',sans-serif; font-size:15px; color: var(--text-primary);">{{ $user->name }}</span></div>
                <div class="col-sm-2 text-end"><a href="#" style="font-family:'Mona Sans',sans-serif; font-size:13px; color: var(--primary-blue-light); text-decoration:none;">{{ __('ui.edit') }}</a></div>
            </div>
            <div class="row mb-2">
                <div class="col-sm-4"><span style="font-family:'Mona Sans',sans-serif; font-size:15px; color: var(--text-primary);">{{ __('ui.dob') }}</span></div>
                <div class="col-sm-6"><span style="font-family:'Mona Sans',sans-serif; font-size:15px; color: var(--text-primary);">{{ __('ui.edit_dob') }}</span></div>
                <div class="col-sm-2 text-end"><a href="#" style="font-family:'Mona Sans',sans-serif; font-size:13px; color: var(--primary-blue-light); text-decoration:none;">{{ __('ui.edit') }}</a></div>
            </div>
            <div class="row mb-4">
                <div class="col-sm-4"><span style="font-family:'Mona Sans',sans-serif; font-size:15px; color: var(--text-primary);">{{ __('ui.gender') }}</span></div>
                <div class="col-sm-6"><span style="font-family:'Mona Sans',sans-serif; font-size:15px; color: var(--text-primary);">{{ __('ui.edit_gender') }}</span></div>
                <div class="col-sm-2 text-end"><a href="#" style="font-family:'Mona Sans',sans-serif; font-size:13px; color: var(--primary-blue-light); text-decoration:none;">{{ __('ui.edit') }}</a></div>
            </div>

            <hr style="border-color: var(--border-light);">

            <h4 class="fw-semibold mb-3 mt-4" style="font-family:'Mona Sans',sans-serif; font-size:24px; color: var(--primary-blue-light);">{{ __('ui.edit_contact') }}</h4>

            <div class="row mb-2">
                <div class="col-sm-4"><span style="font-family:'Mona Sans',sans-serif; font-size:15px; color: var(--text-primary);">{{ __('ui.email') }}</span></div>
                <div class="col-sm-4"><span style="font-family:'Mona Sans',sans-serif; font-size:15px; color: var(--text-primary);">{{ $user->email }}</span></div>
                <div class="col-sm-4 text-end">
                    <span class="d-inline-block px-2 py-0 rounded-2 fw-bold me-1"
                          style="background:#b5d4ff; color:#0a1e78; font-family:'Mona Sans',sans-serif; font-size:13px;">{{ __('ui.verified') }}</span>
                    <a href="#" style="font-family:'Mona Sans',sans-serif; font-size:13px; color: var(--text-primary); text-decoration:none;">{{ __('ui.edit') }}</a>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-4"><span style="font-family:'Mona Sans',sans-serif; font-size:15px; color: var(--text-primary);">{{ __('ui.phone_number') }}</span></div>
                <div class="col-sm-4"><span style="font-family:'Mona Sans',sans-serif; font-size:15px; color: var(--text-primary);">{{ $user->phone }}</span></div>
                <div class="col-sm-4 text-end">
                    <span class="d-inline-block px-2 py-0 rounded-2 fw-bold me-1"
                          style="background:#b5d4ff; color:#0a1e78; font-family:'Mona Sans',sans-serif; font-size:13px;">{{ __('ui.verified') }}</span>
                    <a href="#" style="font-family:'Mona Sans',sans-serif; font-size:13px; color: var(--text-primary); text-decoration:none;">{{ __('ui.edit') }}</a>
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
