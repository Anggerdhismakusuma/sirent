// SI-RENT Auth Modal — AJAX submit helpers
// Called from inline x-on:submit in auth-modal.blade.php

window.submitLogin = async function(form) {
    clearAuthErrors();
    const btn = document.getElementById('login-btn');
    const spinner = document.getElementById('login-spinner');
    btn.disabled = true;
    spinner.hidden = false;

    try {
        const res = await fetch('/auth/login', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                'Accept': 'application/json',
            },
            body: new FormData(form),
        });
        const data = await res.json();

        if (res.ok && data.success) {
            window.dispatchEvent(new CustomEvent('close-auth-modal'));
            window.dispatchEvent(new CustomEvent('auth-changed', { detail: { user: data.user } }));
            window.location.reload();
        } else {
            if (data.errors) displayAuthErrors(data.errors, 'login');
            else alert(data.message || 'Login failed.');
        }
    } catch (e) {
        alert('Network error. Please check your connection.');
    } finally {
        btn.disabled = false;
        spinner.hidden = true;
    }
};

window.submitRegister = async function(form) {
    clearAuthErrors();
    const btn = document.getElementById('register-btn');
    const spinner = document.getElementById('register-spinner');
    btn.disabled = true;
    spinner.hidden = false;

    try {
        const res = await fetch('/auth/register', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                'Accept': 'application/json',
            },
            body: new FormData(form),
        });
        const data = await res.json();

        if (res.ok && data.success) {
            window.dispatchEvent(new CustomEvent('close-auth-modal'));
            window.dispatchEvent(new CustomEvent('auth-changed', { detail: { user: data.user } }));
            window.location.reload();
        } else {
            if (data.errors) displayAuthErrors(data.errors, 'register');
            else alert(data.message || 'Registration failed.');
        }
    } catch (e) {
        alert('Network error. Please check your connection.');
    } finally {
        btn.disabled = false;
        spinner.hidden = true;
    }
};

window.submitForgot = async function(form) {
    const msgEl = document.getElementById('forgot-message');
    msgEl.innerHTML = '';

    try {
        const res = await fetch('/auth/forgot-password', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                'Accept': 'application/json',
            },
            body: new FormData(form),
        });
        const data = await res.json();

        if (res.ok && data.success) {
            msgEl.innerHTML = '<div class="alert alert-success">' + (data.message || 'Reset link sent! Check your email.') + '</div>';
        } else {
            if (data.errors) displayAuthErrors(data.errors, 'forgot');
            else msgEl.innerHTML = '<div class="alert alert-danger">' + (data.message || 'Failed.') + '</div>';
        }
    } catch (e) {
        msgEl.innerHTML = '<div class="alert alert-danger">Network error. Please try again.</div>';
    }
};

function clearAuthErrors() {
    document.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
}

function displayAuthErrors(errors, formId) {
    for (const [field, messages] of Object.entries(errors)) {
        const input = document.querySelector('#' + formId + '-form [name="' + field + '"]');
        const errorEl = document.getElementById(formId + '-' + field + '-error');
        if (input) input.classList.add('is-invalid');
        if (errorEl) errorEl.textContent = Array.isArray(messages) ? messages[0] : messages;
    }
}

// Listen for auth-changed event
window.addEventListener('auth-changed', (e) => {
    console.log('Auth changed:', e.detail?.user?.name);
});
