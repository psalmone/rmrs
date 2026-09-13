<x-guest-layout>
<style>
    .form-title { font-size: 22px; font-weight: 700; color: #f1f5f9; margin-bottom: 4px; }
    .form-subtitle { font-size: 13px; color: #64748b; margin-bottom: 28px; }

    .form-group { margin-bottom: 18px; }
    .form-label {
        display: block;
        font-size: 12px; font-weight: 600; color: #94a3b8;
        margin-bottom: 7px; text-transform: uppercase; letter-spacing: 0.05em;
    }
    .input-wrap { position: relative; }
    .input-icon {
        position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
        color: #475569; pointer-events: none;
    }
    .form-input {
        width: 100%;
        padding: 10px 14px 10px 38px;
        background: #1a1f2e;
        border: 1px solid #2d3448;
        border-radius: 8px;
        font-size: 14px; color: #e2e8f0;
        outline: none;
        transition: border-color 0.15s, box-shadow 0.15s;
        font-family: inherit;
    }
    .form-input::placeholder { color: #475569; }
    .form-input:focus { border-color: #f59e0b; box-shadow: 0 0 0 3px rgba(245,158,11,0.12); }
    .input-toggle {
        position: absolute; right: 10px; top: 50%; transform: translateY(-50%);
        background: none; border: none; cursor: pointer; color: #475569; padding: 4px;
        display: flex; align-items: center;
    }
    .input-toggle:hover { color: #94a3b8; }

    .form-row { display: flex; align-items: center; justify-content: space-between; }
    .checkbox-label { display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 13px; color: #94a3b8; }
    .checkbox-label input[type="checkbox"] {
        width: 15px; height: 15px; border-radius: 4px;
        accent-color: #f59e0b; cursor: pointer;
    }
    .forgot-link { font-size: 13px; color: #f59e0b; text-decoration: none; font-weight: 500; }
    .forgot-link:hover { color: #fbbf24; }

    .btn-submit {
        display: flex; align-items: center; justify-content: center; gap: 8px;
        width: 100%; padding: 11px 20px;
        background: #f59e0b;
        border: none; border-radius: 8px;
        font-size: 14px; font-weight: 700; color: #1a1f2e;
        cursor: pointer; margin-top: 24px;
        transition: background 0.15s, transform 0.1s;
        font-family: inherit;
    }
    .btn-submit:hover { background: #fbbf24; }
    .btn-submit:active { transform: scale(0.98); }

    .form-error { font-size: 12px; color: #f87171; margin-top: 5px; }
    .form-alert {
        padding: 10px 14px; border-radius: 8px; margin-bottom: 18px;
        font-size: 13px;
    }
    .form-alert.success { background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.2); color: #34d399; }
    .form-divider { border: none; border-top: 1px solid #2d3448; margin: 28px 0; }
    .register-link { text-align: center; font-size: 13px; color: #64748b; }
    .register-link a { color: #f59e0b; text-decoration: none; font-weight: 600; }
    .register-link a:hover { color: #fbbf24; }
</style>

    <h2 class="form-title">Welcome back</h2>
    <p class="form-subtitle">Sign in to access the operations portal</p>

    <!-- Session Status -->
    @if (session('status'))
        <div class="form-alert success">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}" id="loginForm">
        @csrf

        <!-- Email -->
        <div class="form-group">
            <label for="email" class="form-label">Email Address</label>
            <div class="input-wrap">
                <span class="input-icon">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                        <polyline points="22,6 12,13 2,6"/>
                    </svg>
                </span>
                <input id="email" type="email" name="email"
                    value="{{ old('email') }}" required autofocus autocomplete="username"
                    placeholder="you@malapote.com" class="form-input">
            </div>
            @error('email')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div class="form-group">
            <div class="form-row" style="margin-bottom:7px">
                <label for="password" class="form-label" style="margin-bottom:0">Password</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="forgot-link">Forgot password?</a>
                @endif
            </div>
            <div class="input-wrap">
                <span class="input-icon">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                        <path d="M7 11V7a5 5 0 0110 0v4"/>
                    </svg>
                </span>
                <input id="password" type="password" name="password"
                    required autocomplete="current-password"
                    placeholder="••••••••" class="form-input" style="padding-right:38px">
                <button type="button" class="input-toggle" onclick="togglePwd()" id="eyeBtn">
                    <svg id="eyeIconOff" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/>
                        <line x1="1" y1="1" x2="23" y2="23"/>
                    </svg>
                    <svg id="eyeIconOn" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display:none">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                        <circle cx="12" cy="12" r="3"/>
                    </svg>
                </button>
            </div>
            @error('password')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <!-- Remember Me -->
        <div class="form-row" style="margin-top: 4px;">
            <label for="remember_me" class="checkbox-label">
                <input id="remember_me" type="checkbox" name="remember">
                <span>Stay signed in</span>
            </label>
        </div>

        <!-- Submit -->
        <button type="submit" class="btn-submit" id="loginBtn">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <path d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4"/>
                <polyline points="10 17 15 12 10 7"/>
                <line x1="15" y1="12" x2="3" y2="12"/>
            </svg>
            Sign in to Portal
        </button>
    </form>

    @if (Route::has('register'))
        <hr class="form-divider">
        <p class="register-link">
            Need a staff account? <a href="{{ route('register') }}">Register here</a>
        </p>
    @endif

<script>
function togglePwd() {
    const inp = document.getElementById('password');
    const off = document.getElementById('eyeIconOff');
    const on  = document.getElementById('eyeIconOn');
    if (inp.type === 'password') {
        inp.type = 'text';
        off.style.display = 'none';
        on.style.display  = 'block';
    } else {
        inp.type = 'password';
        off.style.display = 'block';
        on.style.display  = 'none';
    }
}
document.getElementById('loginForm').addEventListener('submit', function() {
    const btn = document.getElementById('loginBtn');
    btn.disabled = true;
    btn.innerHTML = '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="animation:spin 1s linear infinite"><path d="M21 12a9 9 0 11-6.219-8.56"/></svg> Signing in...';
});
</script>
</x-guest-layout>
