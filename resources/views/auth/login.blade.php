<x-guest-layout>
    <div class="mb-6 text-center">
        <h2 class="text-xl font-extrabold text-white tracking-tight">Staff Portal Login</h2>
        <p class="text-xs text-slate-400 mt-1">Enter your credentials to access mill operations & inventory</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4" id="loginForm">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                {{ __('Email Address') }}
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-amber-400/70">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206" />
                    </svg>
                </div>
                <input 
                    id="email" 
                    type="email" 
                    name="email" 
                    value="{{ old('email') }}" 
                    required 
                    autofocus 
                    autocomplete="username"
                    placeholder="staff@ricemill.com"
                    class="block w-full pl-11 pr-4 py-2.5 bg-black/40 border border-white/10 rounded-xl text-white placeholder-slate-500 text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition duration-150 shadow-inner"
                />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-1.5 text-xs text-rose-400" />
        </div>

        <!-- Password -->
        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-300">
                    {{ __('Password') }}
                </label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-xs font-semibold text-amber-400 hover:text-amber-300 transition-colors">
                        {{ __('Forgot password?') }}
                    </a>
                @endif
            </div>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-amber-400/70">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <input 
                    id="password" 
                    type="password" 
                    name="password" 
                    required 
                    autocomplete="current-password"
                    placeholder="••••••••"
                    class="block w-full pl-11 pr-11 py-2.5 bg-black/40 border border-white/10 rounded-xl text-white placeholder-slate-500 text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition duration-150 shadow-inner"
                />
                <button type="button" onclick="togglePasswordVisibility()" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-200">
                    <svg id="eyeIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-1.5 text-xs text-rose-400" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between pt-1">
            <label for="remember_me" class="inline-flex items-center cursor-pointer select-none">
                <input 
                    id="remember_me" 
                    type="checkbox" 
                    class="w-4 h-4 rounded border-white/20 bg-black/40 text-amber-500 focus:ring-amber-500 focus:ring-offset-slate-900" 
                    name="remember"
                >
                <span class="ms-2 text-xs font-medium text-slate-300">{{ __('Remember this device') }}</span>
            </label>
        </div>

        <!-- Submit Button -->
        <div class="pt-2">
            <button 
                type="submit" 
                id="submitBtn"
                class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 bg-gradient-to-r from-amber-500 via-amber-400 to-yellow-400 hover:brightness-110 text-slate-950 font-extrabold text-sm rounded-xl shadow-lg shadow-amber-500/20 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:ring-offset-2 focus:ring-offset-slate-900 transition duration-200 active:scale-[0.98]"
            >
                <svg id="loadingSpinner" class="hidden animate-spin h-4 w-4 text-slate-950" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span id="btnText">{{ __('Sign in to Account') }}</span>
                <svg class="w-4 h-4 text-slate-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </button>
        </div>

        @if (Route::has('register'))
            <div class="text-center pt-3 border-t border-white/10 mt-5">
                <p class="text-xs text-slate-400">
                    {{ __("Need new system access?") }}
                    <a href="{{ route('register') }}" class="font-bold text-amber-400 hover:text-amber-300 hover:underline ms-1">
                        {{ __('Register staff member') }}
                    </a>
                </p>
            </div>
        @endif
    </form>

    <script>
        function togglePasswordVisibility() {
            const pwd = document.getElementById('password');
            if (pwd.type === 'password') {
                pwd.type = 'text';
            } else {
                pwd.type = 'password';
            }
        }

        const form = document.getElementById('loginForm');
        if (form) {
            form.addEventListener('submit', function() {
                const btn = document.getElementById('submitBtn');
                const spinner = document.getElementById('loadingSpinner');
                const btnText = document.getElementById('btnText');
                if (btn) {
                    btn.disabled = true;
                    btn.classList.add('opacity-80', 'cursor-not-allowed');
                }
                if (spinner) spinner.classList.remove('hidden');
                if (btnText) btnText.innerText = 'Signing in...';
            });
        }
    </script>
</x-guest-layout>
