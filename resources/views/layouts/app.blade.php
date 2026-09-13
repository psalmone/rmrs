<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title . ' — ' : '' }}Malapote Ricemill System</title>
    <meta name="description" content="Malapote Ricemill Management Information System">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Inter', system-ui, -apple-system, sans-serif; }

        /* ─── Sidebar ─── */
        .sidebar {
            position: fixed;
            top: 0; left: 0; bottom: 0;
            width: 240px;
            background: #1a1f2e;
            border-right: 1px solid #2d3448;
            display: flex;
            flex-direction: column;
            z-index: 40;
            transition: transform 0.25s ease;
        }
        .sidebar-brand {
            height: 60px;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 0 18px;
            border-bottom: 1px solid #2d3448;
            flex-shrink: 0;
        }
        .sidebar-logo {
            width: 32px; height: 32px;
            background: linear-gradient(135deg, #f59e0b, #d97706);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .sidebar-brand-text { font-size: 14px; font-weight: 700; color: #f1f5f9; line-height: 1.2; }
        .sidebar-brand-sub { font-size: 10px; color: #64748b; text-transform: uppercase; letter-spacing: 0.06em; }

        .sidebar-section { padding: 20px 0 0; flex: 1; overflow-y: auto; }
        .sidebar-label {
            font-size: 10px; font-weight: 600; color: #475569;
            text-transform: uppercase; letter-spacing: 0.08em;
            padding: 0 18px 8px;
        }
        .sidebar-nav { list-style: none; margin: 0 0 24px; padding: 0; }
        .sidebar-nav li a {
            display: flex; align-items: center; gap: 10px;
            padding: 9px 18px;
            font-size: 13px; font-weight: 500; color: #94a3b8;
            text-decoration: none;
            border-left: 3px solid transparent;
            transition: all 0.15s ease;
        }
        .sidebar-nav li a:hover { color: #f1f5f9; background: #2d3448; }
        .sidebar-nav li a.active {
            color: #f59e0b;
            background: rgba(245,158,11,0.08);
            border-left-color: #f59e0b;
        }
        .sidebar-nav li a svg { flex-shrink: 0; opacity: 0.7; }
        .sidebar-nav li a.active svg { opacity: 1; }

        .sidebar-footer {
            border-top: 1px solid #2d3448;
            padding: 14px 18px;
            flex-shrink: 0;
        }
        .user-info { display: flex; align-items: center; gap: 10px; }
        .user-avatar {
            width: 32px; height: 32px; border-radius: 50%;
            background: linear-gradient(135deg, #f59e0b, #10b981);
            display: flex; align-items: center; justify-content: center;
            font-size: 13px; font-weight: 700; color: #1a1f2e;
            flex-shrink: 0;
        }
        .user-name { font-size: 13px; font-weight: 600; color: #e2e8f0; }
        .user-role { font-size: 11px; color: #64748b; }
        .user-actions { display: flex; gap: 6px; margin-top: 10px; }
        .user-btn {
            flex: 1; padding: 7px 10px;
            font-size: 12px; font-weight: 500;
            border-radius: 6px; border: 1px solid #2d3448;
            background: transparent; color: #94a3b8;
            cursor: pointer; text-decoration: none; text-align: center;
            transition: all 0.15s;
        }
        .user-btn:hover { background: #2d3448; color: #e2e8f0; }
        .user-btn.danger:hover { background: #7f1d1d; border-color: #991b1b; color: #fca5a5; }

        /* ─── Main Content ─── */
        .main-content {
            margin-left: 240px;
            min-height: 100vh;
            background: #0f1117;
            display: flex; flex-direction: column;
        }
        .topbar {
            height: 60px;
            background: #1a1f2e;
            border-bottom: 1px solid #2d3448;
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 28px;
            position: sticky; top: 0; z-index: 30;
            flex-shrink: 0;
        }
        .topbar-title { font-size: 16px; font-weight: 700; color: #f1f5f9; }
        .topbar-sub { font-size: 12px; color: #64748b; margin-top: 1px; }
        .topbar-right { display: flex; align-items: center; gap: 14px; }
        .status-badge {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 5px 12px; border-radius: 20px;
            font-size: 11px; font-weight: 600;
            background: rgba(16,185,129,0.1); color: #34d399; border: 1px solid rgba(16,185,129,0.2);
        }
        .status-dot { width: 6px; height: 6px; border-radius: 50%; background: #34d399; animation: pulse 2s infinite; }
        @keyframes pulse { 0%,100% { opacity: 1; } 50% { opacity: 0.4; } }

        /* ─── Page Body ─── */
        .page-body { padding: 28px; flex: 1; }

        /* ─── Hamburger mobile ─── */
        .hamburger-btn {
            display: none; padding: 8px;
            background: none; border: none; cursor: pointer; color: #94a3b8;
        }

        /* ─── Overlay ─── */
        .sidebar-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,0.5); z-index: 35;
        }

        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .sidebar-overlay.open { display: block; }
            .main-content { margin-left: 0; }
            .hamburger-btn { display: flex; align-items: center; }
            .page-body { padding: 18px; }
        }
    </style>
</head>
<body class="h-full" style="background:#0f1117; color: #e2e8f0;">

    <!-- Sidebar Overlay (mobile) -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <!-- ====== SIDEBAR ====== -->
    <aside class="sidebar" id="sidebar">
        <!-- Brand -->
        <div class="sidebar-brand">
            <div class="sidebar-logo">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#1a1f2e" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                    <polyline points="9,22 9,12 15,12 15,22"/>
                </svg>
            </div>
            <div>
                <div class="sidebar-brand-text">Malapote Ricemill</div>
                <div class="sidebar-brand-sub">MIS v1.0</div>
            </div>
        </div>

        <!-- Navigation -->
        <div class="sidebar-section">
            <p class="sidebar-label">Operations</p>
            <ul class="sidebar-nav">
                <li>
                    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                            <rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
                        </svg>
                        Dashboard
                    </a>
                </li>
                <li>
                    <a href="{{ route('palay-intakes.index') }}" class="{{ request()->routeIs('palay-intakes.*') ? 'active' : '' }}">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/>
                        </svg>
                        Palay Intake
                    </a>
                </li>
                <li>
                    <a href="{{ route('milling-batches.index') }}" class="{{ request()->routeIs('milling-batches.*') ? 'active' : '' }}">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="3"/><path d="M19.07 4.93l-1.41 1.41M5.34 18.66l-1.41 1.41M20 12h2M2 12h2M17.66 17.66l1.41 1.41M6.34 5.34l1.41 1.41M12 18v2M12 4V2"/>
                        </svg>
                        Milling Runs
                    </a>
                </li>
                <li>
                    <a href="{{ route('sales.index') }}" class="{{ request()->routeIs('sales.*') ? 'active' : '' }}">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/>
                            <line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/>
                        </svg>
                        Sales / POS
                    </a>
                </li>
            </ul>

            <p class="sidebar-label">Inventory</p>
            <ul class="sidebar-nav">
                <li>
                    <a href="{{ route('inventory.index') }}" class="{{ request()->routeIs('inventory.*') ? 'active' : '' }}">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                        </svg>
                        Stock Items
                    </a>
                </li>
                <li>
                    <a href="{{ route('inventory.adjust.form') }}" class="{{ request()->is('inventory-adjust*') ? 'active' : '' }}">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <polyline points="17 1 21 5 17 9"/><path d="M3 11V9a4 4 0 014-4h14"/>
                            <polyline points="7 23 3 19 7 15"/><path d="M21 13v2a4 4 0 01-4 4H3"/>
                        </svg>
                        Adjustments
                    </a>
                </li>
            </ul>

            <p class="sidebar-label">Account</p>
            <ul class="sidebar-nav">
                <li>
                    <a href="{{ route('profile.edit') }}" class="{{ request()->routeIs('profile.*') ? 'active' : '' }}">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                        My Profile
                    </a>
                </li>
            </ul>
        </div>

        <!-- User Footer -->
        <div class="sidebar-footer">
            <div class="user-info">
                <div class="user-avatar">{{ substr(Auth::user()->name, 0, 1) }}</div>
                <div>
                    <div class="user-name">{{ Str::limit(Auth::user()->name, 16) }}</div>
                    <div class="user-role">{{ Auth::user()->email }}</div>
                </div>
            </div>
            <div class="user-actions">
                <a href="{{ route('profile.edit') }}" class="user-btn">Settings</a>
                <form method="POST" action="{{ route('logout') }}" style="flex:1">
                    @csrf
                    <button type="submit" class="user-btn danger" style="width:100%">Log Out</button>
                </form>
            </div>
        </div>
    </aside>

    <!-- ====== MAIN CONTENT ====== -->
    <div class="main-content" id="mainContent">
        <!-- Topbar -->
        <div class="topbar">
            <div style="display:flex; align-items:center; gap:12px;">
                <button class="hamburger-btn" onclick="toggleSidebar()" id="hamburgerBtn">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <line x1="3" y1="6" x2="21" y2="6"/>
                        <line x1="3" y1="12" x2="21" y2="12"/>
                        <line x1="3" y1="18" x2="21" y2="18"/>
                    </svg>
                </button>
                @isset($header)
                    <div>{{ $header }}</div>
                @endisset
            </div>
            <div class="topbar-right">
                <div class="status-badge">
                    <span class="status-dot"></span>
                    System Online
                </div>
            </div>
        </div>

        <!-- Page Content -->
        <main class="page-body">
            {{ $slot }}
        </main>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            sidebar.classList.toggle('open');
            overlay.classList.toggle('open');
        }
    </script>
</body>
</html>
