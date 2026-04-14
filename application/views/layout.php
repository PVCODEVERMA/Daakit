<!doctype html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=0'>
    <meta content="Daakit" name="description">
    <title>Daakit</title>
    <!-- Favicon -->
    <link rel="shortcut icon" id="favicon" href="<?php echo base_url(); ?>assets/images/dakit-favicon.gif">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        :root {
            --primary-blue: #0047FF;
            --sidebar-bg:  #1957FF;
            --sidebar-w:   240px;   /* expanded width */
            --header-h:    60px;
            --text-dark:   #1E293B;
            --text-muted:  #64748B;
            --body-bg:     #F8FAFC;
            --radius-lg:   10px;
            --radius-xl:   16px;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', sans-serif;
            background: var(--body-bg);
            color: var(--text-dark);
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }
        a { text-decoration: none; color: inherit; }
        img { max-width: 100%; height: auto; }

        /* ── OVERLAY ─────────────────────────────────────────── */
        .sidebar-overlay {
            display: none;
            position: fixed; inset: 0;
            background: rgba(0,0,0,.4);
            backdrop-filter: blur(2px);
            z-index: 900;
        }
        body.sb-open .sidebar-overlay { display: block; }

        /* ── HEADER ──────────────────────────────────────────── */
        .app-header {
            position: fixed; top: 0; left: 0; right: 0;
            height: var(--header-h);
            background: #fff;
            border-bottom: 1px solid #E2E8F0;
            box-shadow: 0 1px 4px rgba(0,0,0,.06);
            display: flex; align-items: center;
            justify-content: space-between;
            padding: 0 1rem;
            z-index: 800;
            transition: left .3s;
        }
        .header-left { display: flex; align-items: center; gap: 10px; }

        .hamburger {
            width: 40px; height: 40px;
            display: flex; align-items: center; justify-content: center;
            border-radius: 8px; cursor: pointer;
            color: var(--text-dark); transition: background .2s;
            border: none; background: none;
        }
        .hamburger:hover { background: #F1F5F9; }

        .header-brand {
            font-weight: 800; font-size: 1.1rem;
            color: var(--primary-blue); letter-spacing: -.5px;
        }

        .header-actions { display: flex; align-items: center; gap: 8px; }

        .recharge-btn {
            background: var(--primary-blue); color: #fff;
            border-radius: 999px; padding: 0 16px; height: 38px;
            font-weight: 700; font-size: .75rem; text-transform: uppercase;
            display: flex; align-items: center; gap: 6px;
            box-shadow: 0 4px 12px rgba(0,71,255,.2);
            transition: transform .2s, box-shadow .2s;
            border: none; cursor: pointer;
            text-decoration: none;
        }
        .recharge-btn:hover { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(0,71,255,.3); color: #fff; }
        .recharge-btn .lbl { display: none; }

        .wallet-pill {
            background: #F1F5F9; border: 1px solid #E2E8F0;
            border-radius: 999px; padding: 0 12px; height: 38px;
            font-weight: 600; font-size: .8125rem;
            display: flex; align-items: center; gap: 6px;
            white-space: nowrap;
        }

        .bell-icon {
            width: 38px; height: 38px;
            display: flex; align-items: center; justify-content: center;
            background: #F8FAFC; border: 1px solid #E2E8F0;
            border-radius: 50%; color: #64748B; cursor: pointer;
            transition: border-color .2s;
        }
        .bell-icon:hover { border-color: var(--primary-blue); color: var(--text-dark); }

        .user-pill {
            background: #F8FAFC; border: 1px solid #E2E8F0;
            border-radius: 999px; padding: 3px 10px 3px 4px; height: 38px;
            display: flex; align-items: center; gap: 8px; cursor: pointer;
            transition: border-color .2s;
            position: relative;
        }
        .user-pill:hover { border-color: var(--primary-blue); }
        .avatar {
            width: 30px; height: 30px; border-radius: 50%;
            background: var(--primary-blue); color: #fff;
            display: flex; align-items: center; justify-content: center;
            font-size: .7rem; font-weight: 700;
        }
        .uname { display: none; font-size: .8rem; font-weight: 600; }

        /* User Dropdown */
        .user-dropdown { position: relative; }
        .dropdown-menu {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            min-width: 200px;
            background: #fff;
            border: 1px solid #E2E8F0;
            border-radius: var(--radius-lg);
            box-shadow: 0 8px 24px rgba(0,0,0,0.1);
            display: none;
            z-index: 2000;
            padding: 8px 0;
        }
        .user-dropdown.active .dropdown-menu { display: block; }
        .dropdown-menu a {
            display: block;
            padding: 8px 16px;
            font-size: 0.875rem;
            color: var(--text-dark);
            text-decoration: none;
        }
        .dropdown-menu a:hover { background: #F8FAFC; }

        /* ── SIDEBAR ─────────────────────────────────────────── */
        .app-sidebar {
            position: fixed; top: 0; left: 0; bottom: 0;
            width: var(--sidebar-w);
            background: var(--sidebar-bg);
            z-index: 1000;
            display: flex; flex-direction: column;
            transform: translateX(-100%);
            transition: transform .3s ease, width .3s ease;
            overflow-y: auto;
            overflow-x: hidden;
        }
        body.sb-open .app-sidebar { transform: translateX(0); }

        .sidebar-logo {
            display: flex; align-items: center; justify-content: flex-start;
            padding: 16px 20px;
            border-bottom: 1px solid rgba(255,255,255,.12);
            min-height: var(--header-h);
            overflow: hidden;
            white-space: nowrap;
        }
        .sidebar-logo img.logo-full { width: auto; height: 32px; object-fit: contain; }
        .sidebar-logo img.logo-icon { width: 32px; height: 32px; object-fit: contain; display: none; }
        .sidebar-logo .wordmark {
            color: #fff; font-size: 1.25rem; font-weight: 800;
            letter-spacing: -.5px; margin-left: 10px;
        }

        /* Nav */
        .side-menu {
            list-style: none;
            padding: 12px 10px;
            flex: 1;
        }
        .slide { margin-bottom: 2px; }

        .side-menu__item {
            display: flex; align-items: center; gap: 10px;
            padding: 11px 12px;
            border-radius: var(--radius-lg);
            color: rgba(255,255,255,.75);
            font-size: .875rem; font-weight: 500;
            cursor: pointer;
            transition: background .2s, color .2s;
            user-select: none;
            text-decoration: none;
        }
        .side-menu__item:hover,
        .side-menu__item.active {
            background: rgba(255,255,255,.18);
            color: #fff;
        }
        .side-menu__icon {
            width: 20px; height: 20px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .side-menu__icon svg, .side-menu__icon i { width: 18px; height: 18px; font-size: 16px; }
        .side-menu__label { flex: 1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; transition: opacity .2s; }
        .angle { font-size: 12px; transition: transform .25s, opacity .2s; flex-shrink: 0; }
        .slide.open > .side-menu__item .angle { transform: rotate(90deg); }

        /* Submenu */
        .slide-menu {
            list-style: none;
            max-height: 0;
            overflow: hidden;
            transition: max-height .3s ease;
            padding-left: 0;
        }
        .slide.open .slide-menu { max-height: 500px; }

        .slide-item {
            display: flex; align-items: center; gap: 8px;
            padding: 9px 12px 9px 42px;
            font-size: .8125rem;
            color: rgba(255,255,255,.65);
            border-radius: 8px;
            transition: background .2s, color .2s;
            text-decoration: none;
        }
        .slide-item:hover, .slide-item.active { background: rgba(255,255,255,.12); color: #fff; }

        .sub-category { padding: 14px 12px 4px; }
        .sub-category h3 {
            font-size: .6875rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: .08em;
            color: rgba(255,255,255,.4);
        }

        /* ── MAIN CONTENT ───────────────────────────────────── */
        .main-content {
            margin-left: 0;
            padding-top: calc(var(--header-h) + 16px);
            min-height: 100vh;
            transition: margin-left .3s;
            background: var(--body-bg);
        }
        footer {
            padding: 24px;
            text-align: center;
            font-size: 0.8125rem;
            color: var(--text-muted);
        }

        /* ── BREAKPOINTS ────────────────────────────────────── */
        @media (min-width: 480px) {
            .recharge-btn .lbl { display: inline; }
            .recharge-btn { padding: 0 16px; }
        }

        @media (min-width: 768px) {
            .uname { display: block; }
        }

        @media (min-width: 1024px) {
            :root { --sidebar-mini: 88px; }
            .app-sidebar { transform: translateX(0) !important; }
            .sidebar-overlay { display: none !important; }
            .app-header { left: var(--sidebar-w); transition: left 0.3s ease; }
            .main-content { margin-left: var(--sidebar-w); padding: calc(var(--header-h) + 1.5rem) 1.5rem 0; transition: margin-left 0.3s ease; }
            
            /* Mini sidebar state */
            body.sb-collapsed .app-sidebar:not(:hover) {
                width: var(--sidebar-mini);
            }
            body.sb-collapsed .app-sidebar:not(:hover) .side-menu__label,
            body.sb-collapsed .app-sidebar:not(:hover) .angle,
            body.sb-collapsed .app-sidebar:not(:hover) .slide-menu,
            body.sb-collapsed .app-sidebar:not(:hover) .wordmark,
            body.sb-collapsed .app-sidebar:not(:hover) img.logo-full {
                opacity: 0; display: none;
            }
            body.sb-collapsed .app-sidebar:not(:hover) img.logo-icon {
                display: block;
                margin: 0 auto;
            }
            body.sb-collapsed .app-sidebar:not(:hover) .sub-category {
                display: none;
            }
            body.sb-collapsed .app-sidebar:not(:hover) .sidebar-logo {
                padding: 16px 0; justify-content: center;
            }
            body.sb-collapsed .app-sidebar:not(:hover) .side-menu__item {
                justify-content: center;
                padding-left: 0;
                padding-right: 0;
            }
            body.sb-collapsed .app-sidebar:hover {
                box-shadow: 4px 0 24px rgba(0,0,0,0.1);
            }
            
            body.sb-collapsed .app-header { left: var(--sidebar-mini); }
            body.sb-collapsed .main-content { margin-left: var(--sidebar-mini); }
        }
    </style>
</head>

<body class="<?php echo $this->uri->segment(1) == 'analytics' ? 'analytics-page' : ''; ?>">
    <div class="page">
        <div class="sidebar-overlay" onclick="toggleSidebar()"></div>

        <!-- HEADER -->
        <header class="app-header">
            <div class="header-left">
                <button class="hamburger" onclick="toggleSidebar()" aria-label="Toggle sidebar">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
                    </svg>
                </button>
                
            </div>

            <div class="header-actions">
                <a href="<?php echo base_url('billing/rechage_wallet'); ?>" class="recharge-btn">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                        <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    <span class="lbl">RECHARGE</span>
                </a>

                <div class="wallet-pill">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/>
                    </svg>
                    ₹<?php echo number_format($user_details->wallet_balance ?? 0, 0); ?>
                </div>

                <div class="bell-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                    </svg>
                </div>

                <div class="user-dropdown" id="userMenu">
                    <div class="user-pill" onclick="toggleUserMenu()">
                        <div class="avatar"><?php echo strtoupper(substr($user_details->fname ?? 'A', 0, 1)); ?></div>
                        <span class="uname"><?php echo $user_details->fname ?? 'Admin'; ?></span>
                    </div>
                    <div class="dropdown-menu">
                        <div style="padding: 10px 16px; border-bottom: 1px solid #E2E8F0; margin-bottom: 4px;">
                            <div style="font-weight:700; font-size: 0.875rem; color: var(--text-dark);"><?php echo $user_details->fname ?? 'Admin'; ?></div>
                            <div style="font-size: 0.75rem; color: var(--text-muted);"><?php echo $user_details->email ?? ''; ?></div>
                        </div>
                        <a href="<?php echo base_url('setting'); ?>">Profile</a>
                        <a href="<?php echo base_url('billing/rechage_wallet'); ?>">Wallet</a>
                        <a href="<?php echo base_url('support'); ?>">Support</a>
                        <div style="border-top:1px solid #E2E8F0; margin: 4px 0;"></div>
                        <a href="<?php echo base_url('users/logout'); ?>" style="color: #EF4444;">Sign out</a>
                    </div>
                </div>
            </div>
        </header>

        <!-- SIDEBAR -->
        <aside class="app-sidebar" id="sidebar">
            <div class="sidebar-logo">
                <img class="logo-full" src="<?php echo base_url(); ?>assets/images/logo-daakit.png" alt="logo">
                <img class="logo-icon" src="<?php echo base_url(); ?>assets/images/favicon.png" alt="icon">
            </div>

            <ul class="side-menu">
                

                <!-- Dashboard -->
                <li class="slide">
                    <a class="side-menu__item <?php echo ($this->uri->segment(1) == 'analytics') ? 'active' : ''; ?>" href="<?php echo base_url('analytics'); ?>">
                        <span class="side-menu__icon">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M3 13h1v7c0 1.103.897 2 2 2h12c1.103 0 2-.897 2-2v-7h1a1 1 0 0 0 .707-1.707l-9-9a.999.999 0 0 0-1.414 0l-9 9A1 1 0 0 0 3 13zm7 7v-5h4v5h-4zm2-15.586 6 6V15l.001 5H16v-5c0-1.103-.897-2-2-2h-4c-1.103 0-2 .897-2 2v5H6v-9.586l6-6z"/></svg>
                        </span>
                        <span class="side-menu__label">Dashboard</span>
                    </a>
                </li>

                <!-- Orders & Shipments -->
                <li class="slide <?php echo ($this->uri->segment(1) == 'orders') ? 'open' : ''; ?>" onclick="toggleSlide(this)">
                    <div class="side-menu__item <?php echo ($this->uri->segment(1) == 'orders') ? 'active' : ''; ?>">
                        <span class="side-menu__icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M6 6h15l-1.5 9H7.5L6 6z"/>
                                <path d="M6 6l1.5 9m9-9l-1.5 9M6 18a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm12 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"/>
                            </svg>
                        </span>
                        <span class="side-menu__label">Orders & Shipments</span>
                        <i class="angle fa fa-angle-right"></i>
                    </div>
                    <ul class="slide-menu">
                        <li><a class="slide-item <?php echo ($this->uri->segment(2) == 'all') ? 'active' : ''; ?>" href="<?php echo base_url('orders/all'); ?>"><i class="fa fa-shopping-cart"></i> All Orders</a></li>
                    </ul>
                </li>

                <!-- NDR -->
                <li class="slide">
                    <a class="side-menu__item <?php echo ($this->uri->segment(1) == 'ndr') ? 'active' : ''; ?>" href="<?php echo base_url('ndr'); ?>">
                        <span class="side-menu__icon"><i class="fa fa-ban"></i></span>
                        <span class="side-menu__label">NDR (Non-Delivery)</span>
                    </a>
                </li>

                <!-- Finance -->
                <li class="slide <?php echo ($this->uri->segment(1) == 'billing') ? 'open' : ''; ?>" onclick="toggleSlide(this)">
                    <div class="side-menu__item <?php echo ($this->uri->segment(1) == 'billing') ? 'active' : ''; ?>">
                        <span class="side-menu__icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M3 17h18M3 7h18M3 12h18"/>
                            </svg>
                        </span>
                        <span class="side-menu__label">Finance</span>
                        <i class="angle fa fa-angle-right"></i>
                    </div>
                    <ul class="slide-menu">
                        <li><a class="slide-item" href="<?php echo base_url('billing/rechage_wallet'); ?>">&#8377; Wallet Recharge</a></li>
                        <li><a class="slide-item" href="<?php echo base_url('billing'); ?>"><i class="fa fa-file-text"></i> Billing History</a></li>
                        <li><a class="slide-item" href="<?php echo base_url('billing/weight_reconciliation'); ?>"><i class="fa fa-balance-scale"></i> Weight Reconciliation</a></li>
                    </ul>
                </li>

                <!-- Settings -->
                <li class="slide <?php echo ($this->uri->segment(1) == 'setting' || $this->uri->segment(1) == 'warehouse') ? 'open' : ''; ?>" onclick="toggleSlide(this)">
                    <div class="side-menu__item <?php echo ($this->uri->segment(1) == 'setting' || $this->uri->segment(1) == 'warehouse') ? 'active' : ''; ?>">
                        <span class="side-menu__icon"><i class="fa fa-cogs"></i></span>
                        <span class="side-menu__label">Settings</span>
                        <i class="angle fa fa-angle-right"></i>
                    </div>
                    <ul class="slide-menu">
                        <li><a class="slide-item <?php echo ($this->uri->segment(1) == 'warehouse') ? 'active' : ''; ?>" href="<?php echo base_url('warehouse'); ?>"><i class="fa fa-bank"></i> Warehouse</a></li>
                        <li><a class="slide-item <?php echo ($this->uri->segment(1) == 'setting') ? 'active' : ''; ?>" href="<?php echo base_url('setting'); ?>"><i class="fa fa-building-o"></i> Company Profile</a></li>
                    </ul>
                </li>

                <!-- Support -->
                <li class="slide">
                    <a class="side-menu__item <?php echo ($this->uri->segment(1) == 'support') ? 'active' : ''; ?>" href="<?php echo base_url('support'); ?>">
                        <span class="side-menu__icon"><i class="fa fa-phone"></i></span>
                        <span class="side-menu__label">Support 24x7</span>
                    </a>
                </li>
            </ul>
        </aside>

        <!-- CONTENT -->
        <main class="main-content">
            <div style="padding: 0 1rem;">
                <?php echo $maincontent; ?>
            </div>
            <!-- <footer>
                Copyright © 2024 <a href="/" style="color:var(--primary-blue); font-weight:600; text-decoration:none;">Daakit</a>. All rights reserved.
            </footer> -->
        </main>
    </div>

    <script>
        function toggleSidebar() { 
            if (window.innerWidth >= 1024) {
                document.body.classList.toggle('sb-collapsed');
            } else {
                document.body.classList.toggle('sb-open'); 
            }
        }
        function closeSidebar() { 
            document.body.classList.remove('sb-open'); 
        }

        function toggleSlide(el) {
            const isOpen = el.classList.contains('open');
            const parent = el.parentElement;
            parent.querySelectorAll('.slide.open').forEach(s => {
                if (s !== el) s.classList.remove('open');
            });
            el.classList.toggle('open');
        }

        function toggleUserMenu() {
            document.getElementById('userMenu').classList.toggle('active');
        }

        document.addEventListener('click', function(e) {
            const menu = document.getElementById('userMenu');
            if (menu && !menu.contains(e.target)) {
                menu.classList.remove('active');
            }
        });

        window.addEventListener('resize', () => {
            if (window.innerWidth >= 1024) {
                closeSidebar(); // Ensure mobile sidebar is closed when resizing up
            }
        });
    </script>
</body>
</html>