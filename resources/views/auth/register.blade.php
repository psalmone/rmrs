<x-guest-layout>
<style>
    .form-title { font-size: 22px; font-weight: 700; color: #f1f5f9; margin-bottom: 4px; }
    .form-subtitle { font-size: 13px; color: #64748b; margin-bottom: 28px; }
    .form-group { margin-bottom: 16px; }
    .form-label {
        display: block; font-size: 12px; font-weight: 600; color: #94a3b8;
        margin-bottom: 7px; text-transform: uppercase; letter-spacing: 0.05em;
    }
    .input-wrap { position: relative; }
    .input-icon { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #475569; pointer-events: none; }
    .form-input {
        width: 100%; padding: 10px 14px 10px 38px;
        background: #1a1f2e; border: 1px solid #2d3448; border-radius: 8px;
        font-size: 14px; color: #e2e8f0; outline: none;
        transition: border-color 0.15s, box-shadow 0.15s; font-family: inherit;
    }
    .form-input::placeholder { color: #475569; }
    .form-input:focus { border-color: #f59e0b; box-shadow: 0 0 0 3px rgba(245,158,11,0.12); }
    .form-error { font-size: 12px; color: #f87171; margin-top: 5px; }
    .btn-submit {
        display: flex; align-items: center; justify-content: center; gap: 8px;
        width: 100%; padding: 11px 20px; background: #f59e0b;
        border: none; border-radius: 8px; font-size: 14px; font-weight: 700; color: #1a1f2e;
        cursor: pointer; margin-top: 22px; transition: background 0.15s, transform 0.1s; font-family: inherit;
    }
    .btn-submit:hover { background: #fbbf24; }
    .btn-submit:active { transform: scale(0.98); }
    .form-divider { border: none; border-top: 1px solid #2d3448; margin: 24px 0; }
    .login-link { text-align: center; font-size: 13px; color: #64748b; }
    .login-link a { color: #f59e0b; text-decoration: none; font-weight: 600; }
    .login-link a:hover { color: #fbbf24; }
    .info-note {
        padding: 10px 14px; background: rgba(245,158,11,0.07); border: 1px solid rgba(245,158,11,0.15);
        border-radius: 8px; font-size: 12px; color: #94a3b8; margin-bottom: 22px;
    }
    .info-note strong { color: #f59e0b; }
</style>

    <h2 class="form-title">Create staff account</h2>
    <p class="form-subtitle">Register an authorized operator or administrator</p>

    <div class="info-note">
        <strong>Note:</strong> Only authorized personnel should be registered. Contact your system administrator if you need access.
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div class="form-group">
            <label for="name" class="form-label">Full Name</label>
            <div class="input-wrap">
                <span class="input-icon">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                </span>
                <input id="name" type="text" name="name"
                    value="{{ old('name') }}" required autofocus autocomplete="name"
                    placeholder="e.g. Juan Dela Cruz" class="form-input">
            </div>
            @error('name')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

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
                    value="{{ old('email') }}" required autocomplete="username"
                    placeholder="staff@malapote.com" class="form-input">
            </div>
            @error('email')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div class="form-group">
            <label for="password" class="form-label">Password</label>
            <div class="input-wrap">
                <span class="input-icon">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                        <path d="M7 11V7a5 5 0 0110 0v4"/>
                    </svg>
                </span>
                <input id="password" type="password" name="password"
                    required autocomplete="new-password"
                    placeholder="Min. 8 characters" class="form-input">
            </div>
            @error('password')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div class="form-group">
            <label for="password_confirmation" class="form-label">Confirm Password</label>
            <div class="input-wrap">
                <span class="input-icon">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </span>
                <input id="password_confirmation" type="password" name="password_confirmation"
                    required autocomplete="new-password"
                    placeholder="Repeat password" class="form-input">
            </div>
            @error('password_confirmation')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="btn-submit">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <path d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
                <line x1="19" y1="8" x2="19" y2="14"/>
                <line x1="22" y1="11" x2="16" y2="11"/>
            </svg>
            Create Account
        </button>
    </form>

    <hr class="form-divider">
    <p class="login-link">Already have an account? <a href="{{ route('login') }}">Sign in</a></p>

</x-guest-layout>
