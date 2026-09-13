<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Malapote Ricemill System') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html, body { height: 100%; }
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: #0f1117;
            color: #e2e8f0;
            display: flex;
            min-height: 100vh;
        }

        /* ─── Left Panel ─── */
        .auth-left {
            display: none;
            flex: 1;
            background: #1a1f2e;
            border-right: 1px solid #2d3448;
            padding: 48px;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }
        @media (min-width: 1024px) { .auth-left { display: flex; } }

        .auth-left::before {
            content: '';
            position: absolute;
            top: -80px; right: -80px;
            width: 280px; height: 280px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(245,158,11,0.12) 0%, transparent 70%);
            pointer-events: none;
        }
        .auth-left::after {
            content: '';
            position: absolute;
            bottom: -60px; left: -60px;
            width: 220px; height: 220px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(16,185,129,0.10) 0%, transparent 70%);
            pointer-events: none;
        }

        .brand-logo {
            width: 48px; height: 48px;
            background: linear-gradient(135deg, #f59e0b, #d97706);
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 16px;
        }
        .brand-name { font-size: 26px; font-weight: 700; color: #f1f5f9; letter-spacing: -0.5px; }
        .brand-name span { color: #f59e0b; }
        .brand-sub { font-size: 12px; color: #64748b; text-transform: uppercase; letter-spacing: 0.08em; margin-top: 4px; }

        .feature-list { list-style: none; margin-top: 48px; display: flex; flex-direction: column; gap: 20px; }
        .feature-item { display: flex; align-items: flex-start; gap: 14px; }
        .feature-icon {
            width: 36px; height: 36px; border-radius: 8px;
            background: rgba(245,158,11,0.1); border: 1px solid rgba(245,158,11,0.2);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0; color: #f59e0b;
        }
        .feature-title { font-size: 14px; font-weight: 600; color: #e2e8f0; }
        .feature-desc { font-size: 12px; color: #64748b; margin-top: 2px; }

        .auth-left-footer { font-size: 12px; color: #475569; }
        .auth-left-footer strong { color: #94a3b8; }

        /* ─── Right Panel ─── */
        .auth-right {
            width: 100%;
            max-width: 440px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px 28px;
            background: #0f1117;
            position: relative;
        }
        @media (min-width: 1024px) { .auth-right { border-left: 1px solid #2d3448; } }

        .auth-form-wrap { width: 100%; max-width: 360px; }

        /* Mobile brand */
        .mobile-brand {
            text-align: center;
            margin-bottom: 32px;
        }
        @media (min-width: 1024px) { .mobile-brand { display: none; } }
        .mobile-brand-logo {
            width: 44px; height: 44px;
            background: linear-gradient(135deg, #f59e0b, #d97706);
            border-radius: 10px;
            display: inline-flex; align-items: center; justify-content: center;
            margin-bottom: 10px;
        }
        .mobile-brand-name { font-size: 20px; font-weight: 700; color: #f1f5f9; }
        .mobile-brand-name span { color: #f59e0b; }
        .mobile-brand-sub { font-size: 11px; color: #64748b; text-transform: uppercase; letter-spacing: 0.08em; margin-top: 2px; }
    </style>
</head>
<body>
    <!-- Left Branding Panel -->
    <div class="auth-left">
        <div>
            <div class="brand-logo">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#1a1f2e" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                    <polyline points="9,22 9,12 15,12 15,22"/>
                </svg>
            </div>
            <h1 class="brand-name">Malapote <span>Ricemill</span></h1>
            <p class="brand-sub">Management Information System</p>

            <ul class="feature-list">
                <li class="feature-item">
                    <div class="feature-icon">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/>
                        </svg>
                    </div>
                    <div>
                        <div class="feature-title">Palay Intake Tracking</div>
                        <div class="feature-desc">Record and track raw palay from farmers</div>
                    </div>
                </li>
                <li class="feature-item">
                    <div class="feature-icon">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="3"/><path d="M12 1v4M12 19v4M4.22 4.22l2.83 2.83M16.95 16.95l2.83 2.83M1 12h4M19 12h4M4.22 19.78l2.83-2.83M16.95 7.05l2.83-2.83"/>
                        </svg>
                    </div>
                    <div>
                        <div class="feature-title">Milling Operations</div>
                        <div class="feature-desc">Manage milling batches and recovery rates</div>
                    </div>
                </li>
                <li class="feature-item">
                    <div class="feature-icon" style="background:rgba(16,185,129,0.1); border-color:rgba(16,185,129,0.2); color:#10b981">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                        </svg>
                    </div>
                    <div>
                        <div class="feature-title">Inventory Management</div>
                        <div class="feature-desc">Real-time stock levels with low-stock alerts</div>
                    </div>
                </li>
                <li class="feature-item">
                    <div class="feature-icon" style="background:rgba(59,130,246,0.1); border-color:rgba(59,130,246,0.2); color:#60a5fa">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="feature-title">Sales & Reports</div>
                        <div class="feature-desc">POS transactions and revenue analytics</div>
                    </div>
                </li>
            </ul>
        </div>

        <p class="auth-left-footer">
            &copy; {{ date('Y') }} <strong>Malapote Ricemill</strong> &bull; All rights reserved
        </p>
    </div>

    <!-- Right Form Panel -->
    <div class="auth-right">
        <div class="auth-form-wrap">
            <!-- Mobile Brand -->
            <div class="mobile-brand">
                <div class="mobile-brand-logo">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#1a1f2e" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                        <polyline points="9,22 9,12 15,12 15,22"/>
                    </svg>
                </div>
                <div class="mobile-brand-name">Malapote <span>Ricemill</span></div>
                <div class="mobile-brand-sub">Management Information System</div>
            </div>

            {{ $slot }}
        </div>
    </div>
</body>
</html>
