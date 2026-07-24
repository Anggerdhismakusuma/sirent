<div
    x-data="{
        show: @js(session()->has('account_suspended')),
        message: @js(
            session(
                'account_suspended',
                'Akun Anda sedang disuspend oleh administrator SI-RENT.'
            )
        )
    }"
    x-on:account-suspended.window="
        message = $event.detail.message;
        show = true;
    "
>
    <div
        x-cloak
        x-show="show"
        x-transition.opacity
        class="position-fixed top-0 start-0 w-100 h-100"
        style="background: rgba(0,0,0,0.55); z-index: 2000;"
    >
        <div
            class="position-absolute top-50 start-50 translate-middle
                   bg-white rounded-4 shadow p-5 text-center"
            style="width: 460px; max-width: 92vw;"
        >
            <div
                class="d-flex align-items-center justify-content-center
                       rounded-circle mx-auto mb-3"
                style="
                    width: 64px;
                    height: 64px;
                    background: #fee4e2;
                    color: #d92d20;
                    font-size: 30px;
                    font-weight: 700;
                "
            >
                !
            </div>

            <h3 class="fw-bold mb-3" style="color: #b42318;">
                Akun Disuspend
            </h3>

            <p
                class="text-secondary mb-4"
                style="line-height: 1.6;"
                x-text="message"
            ></p>

            <p class="small text-muted mb-4">
                Anda telah dikeluarkan dari akun dan tidak dapat melakukan
                pemesanan sampai akun diaktifkan kembali.
            </p>

            <button
                type="button"
                class="btn btn-primary w-100 fw-semibold"
                style="height: 48px; border-radius: 10px;"
                @click="show = false"
            >
                Mengerti
            </button>
        </div>
    </div>
</div>