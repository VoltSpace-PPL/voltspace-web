<!DOCTYPE html>
<html lang="en" class="bg-[#0b1120] selection:bg-[#00d4aa]/30">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VoltSpace - Smart Energy Monitoring</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Plus Jakarta Sans', 'sans-serif'; 
            background-color: #0b1120;
            color: #ffffff;
        }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #334155; border-radius: 10px; }
        
        .nav-item-active {
            background: rgba(0, 212, 170, 0.1);
            border: 1px solid rgba(0, 212, 170, 0.2);
            color: #00d4aa;
        }
        
        .switch { position: relative; display: inline-block; width: 48px; height: 24px; }
        .switch input { opacity: 0; width: 0; height: 0; }
        .slider { position: absolute; cursor: pointer; inset: 0; background-color: #334155; transition: .4s; border-radius: 24px; }
        .slider:before { position: absolute; content: ""; height: 18px; width: 18px; left: 3px; bottom: 3px; background-color: white; transition: .4s; border-radius: 50%; }
        input:checked + .slider { background-color: #00d4aa; }
        input:checked + .slider:before { transform: translateX(24px); }

        .grid-pattern {
            background-image: 
                linear-gradient(rgba(255, 255, 255, 0.02) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.02) 1px, transparent 1px);
            background-size: 50px 50px;
        }

        /* Glassmorphism Effect */
        .glass-effect {
            background: rgba(30, 41, 59, 0.7) !important;
            backdrop-filter: blur(12px) !important;
            -webkit-backdrop-filter: blur(12px) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
        }
        /* Prevent flex children from forcing horizontal scroll on narrow viewports */
        .main-shell { min-width: 0; }
    </style>
</head>
<body class="antialiased overflow-x-hidden grid-pattern">
    <div class="flex min-h-screen w-full max-w-full min-w-0">
        <!-- Sidebar - Made more responsive with hidden on mobile -->
        <aside id="sidebar" class="fixed left-0 top-0 bottom-0 w-[280px] max-w-[min(280px,100vw)] bg-[#0b1120] border-r border-[#334155] z-50 flex flex-col transition-transform duration-300 -translate-x-full lg:translate-x-0 overflow-hidden">
            <div class="p-8 mb-4">
                <div class="flex items-center gap-4 group cursor-pointer">
                    <div class="w-12 h-12 relative flex-shrink-0 overflow-hidden rounded-full bg-white p-1">
                        <img src="/images/voltspace-logo.png" alt="VoltSpace Logo" class="w-full h-full object-contain">
                    </div>
                    <div class="overflow-hidden">
                        <h2 class="text-[24px] font-bold text-white leading-none tracking-tight">VoltSpace</h2>
                        <p class="text-[10px] font-bold text-[#00d4aa] uppercase tracking-[1px] mt-1 whitespace-nowrap">Smart Energy Monitoring</p>
                    </div>
                </div>
            </div>

            <nav class="flex-1 min-h-0 px-4 space-y-1.5 overflow-y-auto custom-scrollbar">
                @php
                    $menu = [
                        ['id' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6', 'route' => '/dashboard'],
                        ['id' => 'monitoring', 'label' => 'Energy Monitoring', 'icon' => 'M13 10V3L4 14h7v7l9-11h-7z'],
                        ['id' => 'buildings', 'label' => 'Buildings', 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
                        ['id' => 'rooms', 'label' => 'Rooms', 'icon' => 'M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z', 'route' => '/rooms'],
                        ['id' => 'devices', 'label' => 'Devices', 'icon' => 'M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4', 'route' => '/devices'],
                        ['id' => 'schedule', 'label' => 'Electricity Schedule', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z', 'route' => '/schedule'],
                        ['id' => 'bookings', 'label' => 'Room Bookings', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4', 'badge' => 4],
                        ['id' => 'alerts', 'label' => 'Energy Alerts', 'icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z', 'badge' => 3],
                        ['id' => 'reports', 'label' => 'Reports', 'icon' => 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                        ['id' => 'users', 'label' => 'Users', 'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z', 'route' => '/users'],
                        ['id' => 'settings', 'label' => 'Settings', 'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z'],
                    ];
                @endphp

                @foreach($menu as $item)
                    <a href="{{ $item['route'] ?? '#' }}" class="flex items-center justify-between px-4 py-3 rounded-xl transition-all group {{ (request()->is(ltrim($item['route'] ?? '', '/'))) ? 'nav-item-active' : 'text-slate-400 hover:text-white hover:bg-[#1e293b]' }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="{{ $item['icon'] }}"/></svg>
                            <span class="text-[15px] font-medium">{{ $item['label'] }}</span>
                        </div>
                        @if(isset($item['badge']))
                            <span class="w-5 h-5 rounded-full bg-red-500/20 text-red-500 text-[10px] font-bold flex items-center justify-center">{{ $item['badge'] }}</span>
                        @endif
                    </a>
                @endforeach
            </nav>

            <div class="p-4 border-t border-[#334155] space-y-2">
                <!-- User Info -->
                <div class="flex items-center gap-3 px-2 py-1">
                    <div class="w-9 h-9 rounded-full bg-[#00d4aa]/20 flex items-center justify-center text-[#00d4aa] font-bold text-sm flex-shrink-0" id="sidebar-avatar">AD</div>
                    <div class="flex-1 min-w-0">
                        <p class="text-[13px] font-bold text-white truncate leading-none" id="sidebar-name">Admin User</p>
                        <p class="text-[11px] text-slate-500 truncate mt-0.5" id="sidebar-email">admin@voltspace.id</p>
                    </div>
                </div>
                <!-- Logout Button -->
                <button id="logout-btn" onclick="handleLogout()"
                    class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-red-400 hover:text-red-300 hover:bg-red-500/10 border border-transparent hover:border-red-500/20 transition-all group">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    <span class="text-[14px] font-semibold" id="logout-label">Logout</span>
                </button>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="main-shell flex-1 lg:ml-[280px] min-h-screen w-full max-w-full bg-[#0b1120]">
            <!-- Top Bar -->
            <header class="bg-[#0b1120]/80 backdrop-blur-md px-6 lg:px-10 py-6 border-b border-[#334155]/30">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 min-w-0">
                    <div class="min-w-0">
                        <div class="flex items-center gap-3 lg:hidden mb-4">
                            <button onclick="toggleSidebar()" class="p-2 bg-[#1e293b] rounded-lg text-white">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16" stroke-width="2"/></svg>
                            </button>
                            <h2 class="text-[18px] font-bold text-white">VoltSpace</h2>
                        </div>
                        <h2 class="text-[22px] font-bold text-white tracking-tight leading-tight">Good Evening, Admin</h2>
                        <p class="text-[13px] text-slate-400 mt-0.5">VoltSpace – Smart Energy Monitoring Dashboard</p>
                        
                        <!-- Badges moved to below greeting as requested -->
                        <div class="flex flex-wrap items-center gap-3 mt-4">
                            <div class="flex items-center gap-2.5 px-3 py-1.5 bg-[#10b981]/10 border border-[#10b981]/20 rounded-lg">
                                <div class="w-2 h-2 rounded-full bg-[#10b981]"></div>
                                <span class="text-[11px] font-bold text-[#10b981] uppercase tracking-wider">System Operational</span>
                            </div>
                            <div class="flex items-center gap-2.5 px-3 py-1.5 bg-[#1e293b] border border-[#334155] rounded-lg text-slate-400">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" stroke-width="2"/></svg>
                                <span class="text-[11px] font-bold uppercase tracking-wider">Tel-U Bandung</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-6 self-stretch md:self-auto min-w-0 w-full md:w-auto">
                        <div class="flex flex-col items-stretch md:items-end gap-4 text-slate-400 w-full md:w-auto min-w-0">
                            <div class="flex flex-wrap items-center gap-2 sm:gap-4 justify-end md:flex-nowrap md:whitespace-nowrap">
                                <div class="relative shrink-0">
                                    <div class="w-10 h-8 rounded-[9px] border border-[#334155]/70 bg-[#0f1b38]/45 flex items-center justify-center hover:border-[#475569] transition-colors">
                                        <svg class="w-4 h-4 cursor-pointer hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" stroke-width="2"/></svg>
                                    </div>
                                    <span class="absolute -top-1 -right-1 w-3.5 h-3.5 bg-red-500 border-2 border-[#0b1120] rounded-full text-[8px] font-bold text-white flex items-center justify-center">3</span>
                                </div>
                                <div class="flex items-center gap-1.5 cursor-pointer hover:text-white transition-colors shrink-0 h-8 px-3 rounded-[9px] border border-[#334155]/70 bg-[#0f1b38]/45">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" stroke-width="2"/></svg>
                                    <span class="text-[11px] font-bold uppercase tracking-wider">ID</span>
                                </div>
                                <div class="flex items-center gap-1.5 cursor-pointer hover:text-white transition-colors shrink-0 h-8 px-3 rounded-[9px] border border-[#334155]/70 bg-[#0f1b38]/45">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" stroke-width="2"/></svg>
                                    <span class="text-[11px] font-bold uppercase tracking-wider">Switch Role</span>
                                </div>
                            </div>
                            <div id="real-time-clock" class="text-[13px] font-medium text-slate-500 flex items-center gap-2 shrink-0 justify-end">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2"/></svg>
                                <span>Fri, Apr 10, 05:23 PM</span>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <div class="px-6 lg:px-10 py-10 max-w-full min-w-0">
                @yield('content')
            </div>
        </main>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('-translate-x-full');
        }

        function updateClock() {
            const now = new Date();
            const days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            
            const day = days[now.getDay()];
            const month = months[now.getMonth()];
            const date = now.getDate();
            const year = now.getFullYear();
            
            let hours = now.getHours();
            const minutes = now.getMinutes().toString().padStart(2, '0');
            const ampm = hours >= 12 ? 'PM' : 'AM';
            hours = hours % 12;
            hours = hours ? hours : 12;
            const hoursStr = hours.toString().padStart(2, '0');
            
            const timeStr = `${day}, ${month} ${date}, ${hoursStr}:${minutes} ${ampm}`;
            const clockEl = document.querySelector('#real-time-clock span');
            if (clockEl) clockEl.textContent = timeStr;
        }
        setInterval(updateClock, 1000);
        updateClock();
    </script>
    @include('partials.voltspace-api')
    <script>
        // ── Sidebar user info ──────────────────────────────────────────────
        async function loadSidebarUser() {
            try {
                const res = await apiFetch('/auth/me');
                if (!res.ok) return;
                const data = await res.json();
                const user = data.data || data.user || data;
                if (user.name) {
                    const initials = user.name.split(' ').map(w => w[0]).join('').substring(0, 2).toUpperCase();
                    document.getElementById('sidebar-avatar').textContent = initials;
                    document.getElementById('sidebar-name').textContent   = user.name;
                }
                if (user.email) {
                    document.getElementById('sidebar-email').textContent = user.email;
                }
            } catch (e) { /* silently ignore */ }
        }

        // ── Logout ────────────────────────────────────────────────────────
        async function handleLogout() {
            const btn   = document.getElementById('logout-btn');
            const label = document.getElementById('logout-label');
            btn.disabled  = true;
            label.textContent = 'Logging out…';

            try {
                await apiFetch('/auth/logout', { method: 'POST' });
            } catch (e) { /* ignore network errors – still clear session */ }

            localStorage.removeItem('token');
            location.href = '/login';
        }

        document.addEventListener('DOMContentLoaded', loadSidebarUser);
    </script>
    @stack('scripts')
</body>
</html>
