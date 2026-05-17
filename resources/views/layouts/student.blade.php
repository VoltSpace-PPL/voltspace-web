<!DOCTYPE html>
<html lang="en" class="bg-[#0b1120] selection:bg-[#00d4aa]/30">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VoltSpace - Student Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #0b1120; color: #ffffff; }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #334155; border-radius: 10px; }
        .nav-item-active { background: rgba(0, 212, 170, 0.1); border: 1px solid rgba(0, 212, 170, 0.2); color: #00d4aa; }
        .grid-pattern {
            background-image: linear-gradient(rgba(255,255,255,0.02) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.02) 1px, transparent 1px);
            background-size: 50px 50px;
        }
        .glass-effect {
            background: rgba(30, 41, 59, 0.7) !important;
            backdrop-filter: blur(12px) !important;
            -webkit-backdrop-filter: blur(12px) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
        }
        .main-shell { min-width: 0; }
        /* Status badge animations */
        @keyframes pulse-dot { 0%,100%{opacity:1} 50%{opacity:.4} }
    </style>
</head>
<body class="antialiased overflow-x-hidden grid-pattern">
    <div class="flex min-h-screen w-full max-w-full min-w-0">
        <!-- Sidebar -->
        <aside id="sidebar" class="fixed left-0 top-0 bottom-0 w-[320px] max-w-[min(320px,100vw)] bg-[#0b1120] border-r border-[#334155] z-50 flex flex-col transition-transform duration-300 -translate-x-full lg:translate-x-0 overflow-hidden">
            <!-- Logo -->
            <div class="p-8 mb-4">
                <div class="flex items-center gap-4 cursor-pointer">
                    <div class="w-12 h-12 relative flex-shrink-0 overflow-hidden rounded-full bg-white p-1">
                        <img src="/images/voltspace-logo.png" alt="VoltSpace Logo" class="w-full h-full object-contain">
                    </div>
                    <div class="overflow-hidden">
                        <h2 class="text-[24px] font-bold text-white leading-none tracking-tight">VoltSpace</h2>
                        <p class="text-[10px] font-bold text-[#00d4aa] uppercase tracking-[1px] mt-1 whitespace-nowrap">Student Portal</p>
                    </div>
                </div>
            </div>

            <nav class="flex-1 min-h-0 px-4 space-y-1 overflow-y-auto custom-scrollbar">
                @php
                    $studentMenu = [
                        ['id' => 'new-booking',   'label' => 'New Booking',      'icon' => 'M12 4v16m8-8H4', 'route' => '/student/bookings/create'],
                        ['id' => 'my-bookings',   'label' => 'My Bookings',      'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4', 'route' => '/student/bookings'],
                    ];
                @endphp

                @foreach($studentMenu as $item)
                    <a href="{{ $item['route'] }}" id="nav-{{ $item['id'] }}"
                       class="flex items-center justify-between px-4 py-3 rounded-xl transition-all group {{ request()->is(ltrim($item['route'], '/')) ? 'nav-item-active' : 'text-slate-400 hover:text-white hover:bg-[#1e293b]' }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path d="{{ $item['icon'] }}"/>
                            </svg>
                            <span class="text-[14px] font-medium">{{ $item['label'] }}</span>
                        </div>
                        @if($item['id'] === 'my-bookings')
                            <span id="booking-badge" class="hidden w-5 h-5 rounded-full bg-[#00d4aa]/20 text-[#00d4aa] text-[10px] font-bold flex items-center justify-center">0</span>
                        @endif
                    </a>
                @endforeach
            </nav>

            <!-- Bottom: user info + switch to admin -->
            <div class="p-4 border-t border-[#334155] space-y-2">
                <div class="flex items-center gap-3 px-2 py-2">
                    <div class="w-9 h-9 rounded-full bg-[#00d4aa]/20 flex items-center justify-center text-[#00d4aa] font-bold text-sm flex-shrink-0" id="student-sidebar-avatar">MH</div>
                    <div class="flex-1 min-w-0">
                        <p class="text-[14px] font-bold text-white truncate leading-none" id="student-sidebar-name">Mahasiswa</p>
                        <p class="text-[12px] text-slate-500 truncate mt-0.5" id="student-sidebar-email">mahasiswa@telyu.ac.id</p>
                    </div>
                </div>
                <button onclick="switchToAdmin()" id="switch-admin-btn"
                    class="w-full flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#1e293b] border border-[#334155] text-slate-300 hover:text-white hover:border-[#475569] transition-all text-[13px] font-semibold">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                    Switch to Admin
                </button>
                <button id="student-logout-btn" onclick="handleStudentLogout()"
                    class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-red-400 hover:text-red-300 hover:bg-red-500/10 border border-transparent hover:border-red-500/20 transition-all">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    <span class="text-[13px] font-semibold" id="student-logout-label">Logout</span>
                </button>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="main-shell flex-1 lg:ml-[320px] min-h-screen w-full max-w-full bg-[#0b1120]">
            <!-- Top Bar -->
            <header class="bg-[#0b1120]/80 backdrop-blur-md px-6 lg:px-10 py-5 border-b border-[#334155]/30">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 min-w-0">
                    <div class="min-w-0">
                        <div class="flex items-center gap-3 lg:hidden mb-3">
                            <button onclick="toggleStudentSidebar()" class="p-2 bg-[#1e293b] rounded-lg text-white">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16" stroke-width="2"/></svg>
                            </button>
                            <h2 class="text-[16px] font-bold text-white">VoltSpace</h2>
                        </div>
                        <h2 class="text-[20px] font-bold text-white tracking-tight leading-tight" id="student-greeting">Good Morning, Student</h2>
                        <p class="text-[12px] text-slate-400 mt-0.5">VoltSpace – Smart Energy Monitoring Dashboard</p>
                        <div class="flex flex-wrap items-center gap-3 mt-3">
                            <div class="flex items-center gap-2 px-3 py-1.5 bg-[#10b981]/10 border border-[#10b981]/20 rounded-lg">
                                <div class="w-2 h-2 rounded-full bg-[#10b981]"></div>
                                <span class="text-[11px] font-bold text-[#10b981] uppercase tracking-wider">System Operational</span>
                            </div>
                            <div class="flex items-center gap-2 px-3 py-1.5 bg-[#1e293b] border border-[#334155] rounded-lg text-slate-400">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" stroke-width="2"/></svg>
                                <span class="text-[11px] font-bold uppercase tracking-wider">Tel-U Bandung</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 self-start md:self-auto">
                        <div class="relative">
                            <div class="w-9 h-8 rounded-[9px] border border-[#334155]/70 bg-[#0f1b38]/45 flex items-center justify-center text-slate-400 hover:text-white transition-colors cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" stroke-width="2"/></svg>
                            </div>
                        </div>
                        <!-- Switch Role button (topbar) -->
                        <button onclick="switchToAdmin()" class="flex items-center gap-1.5 h-8 px-3 rounded-[9px] border border-[#334155]/70 bg-[#0f1b38]/45 text-slate-400 hover:text-white hover:border-[#475569] transition-colors cursor-pointer text-[11px] font-bold uppercase tracking-wider">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            Switch Role
                        </button>
                        <div id="student-real-time-clock" class="text-[12px] font-medium text-slate-500 flex items-center gap-1.5 h-8 px-3 rounded-[9px] border border-[#334155]/70 bg-[#0f1b38]/45">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2"/></svg>
                            <span>--:--</span>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <div class="px-6 lg:px-10 py-8 max-w-full min-w-0">
                @yield('content')
            </div>
        </main>
    </div>

    @include('partials.voltspace-api')

    <!-- Global Custom Alert Modal (reuse same style) -->
    <div id="vs-alert-modal" class="fixed inset-0 z-[9999] hidden" role="dialog" aria-modal="true">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="vsAlert.close()"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-[400px] p-4">
            <div class="glass-effect rounded-[20px] shadow-2xl overflow-hidden">
                <div class="p-6 flex justify-between items-center border-b border-white/10">
                    <div class="flex items-center gap-3">
                        <div id="vs-alert-icon" class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"></div>
                        <h3 id="vs-alert-title" class="text-[17px] font-bold text-white"></h3>
                    </div>
                    <button onclick="vsAlert.close()" class="w-8 h-8 flex items-center justify-center rounded-full bg-white/5 text-slate-400 hover:text-white transition-colors shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2.5"/></svg>
                    </button>
                </div>
                <div class="px-6 py-5">
                    <p id="vs-alert-message" class="text-[14px] text-slate-300 leading-relaxed"></p>
                </div>
                <div class="px-6 pb-6 flex gap-3" id="vs-alert-actions"></div>
            </div>
        </div>
    </div>

    <script>
    const vsAlert = {
        _resolve: null,
        show({ type = 'info', title, message, confirmText = 'OK', cancelText = null, confirmClass = null }) {
            const modal   = document.getElementById('vs-alert-modal');
            const iconEl  = document.getElementById('vs-alert-icon');
            const titleEl = document.getElementById('vs-alert-title');
            const msgEl   = document.getElementById('vs-alert-message');
            const actions = document.getElementById('vs-alert-actions');
            const themes = {
                success: { bg: 'bg-emerald-500/15 border border-emerald-500/20', color: 'text-emerald-400', svg: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>', btnClass: 'bg-emerald-500 hover:bg-emerald-600' },
                error:   { bg: 'bg-red-500/15 border border-red-500/20',     color: 'text-red-400',     svg: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>', btnClass: 'bg-red-500 hover:bg-red-600' },
                warning: { bg: 'bg-yellow-500/15 border border-yellow-500/20', color: 'text-yellow-400', svg: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>', btnClass: 'bg-yellow-500 hover:bg-yellow-600' },
                info:    { bg: 'bg-[#00aaff]/15 border border-[#00aaff]/20',  color: 'text-[#00aaff]',  svg: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>', btnClass: 'bg-[#00d4aa] hover:bg-[#00bfa0]' },
                confirm: { bg: 'bg-orange-500/15 border border-orange-500/20', color: 'text-orange-400', svg: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>', btnClass: 'bg-orange-500 hover:bg-orange-600' },
            };
            const t = themes[type] || themes.info;
            iconEl.className = `w-10 h-10 rounded-xl flex items-center justify-center shrink-0 ${t.bg} ${t.color}`;
            iconEl.innerHTML = t.svg;
            titleEl.textContent = title;
            msgEl.innerHTML = message;
            const finalBtnClass = confirmClass || t.btnClass;
            actions.innerHTML = '';
            return new Promise((resolve) => {
                this._resolve = resolve;
                const confirmBtn = document.createElement('button');
                confirmBtn.className = `flex-1 py-3 ${finalBtnClass} text-white font-bold rounded-xl transition-colors text-[14px]`;
                confirmBtn.textContent = confirmText;
                confirmBtn.onclick = () => { this.close(); resolve(true); };
                actions.appendChild(confirmBtn);
                if (cancelText) {
                    const cancelBtn = document.createElement('button');
                    cancelBtn.className = 'flex-1 py-3 bg-white/5 border border-white/10 text-white font-bold rounded-xl hover:bg-white/10 transition-colors text-[14px]';
                    cancelBtn.textContent = cancelText;
                    cancelBtn.onclick = () => { this.close(); resolve(false); };
                    actions.insertBefore(cancelBtn, confirmBtn);
                }
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            });
        },
        close() { document.getElementById('vs-alert-modal').classList.add('hidden'); document.body.style.overflow = 'auto'; },
        success(title, message) { return this.show({ type: 'success', title, message }); },
        error(title, message)   { return this.show({ type: 'error',   title, message }); },
        warning(title, message) { return this.show({ type: 'warning', title, message }); },
        info(title, message)    { return this.show({ type: 'info',    title, message }); },
        confirm(title, message, confirmText = 'Ya, Konfirmasi', cancelText = 'Batal') {
            return this.show({ type: 'confirm', title, message, confirmText, cancelText });
        },
    };

    function toggleStudentSidebar() {
        document.getElementById('sidebar').classList.toggle('-translate-x-full');
    }

    function updateStudentClock() {
        const now = new Date();
        const days = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
        const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        const day = days[now.getDay()], month = months[now.getMonth()], date = now.getDate(), year = now.getFullYear();
        let hours = now.getHours();
        const minutes = now.getMinutes().toString().padStart(2,'0');
        const ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12 || 12;
        const clockEl = document.querySelector('#student-real-time-clock span');
        if (clockEl) clockEl.textContent = `${days[now.getDay()]}, ${month} ${date}, ${hours.toString().padStart(2,'0')}:${minutes} ${ampm}`;
        // Greeting
        const h = now.getHours();
        const greet = h < 12 ? 'Good Morning' : h < 17 ? 'Good Afternoon' : 'Good Evening';
        const gEl = document.getElementById('student-greeting');
        if (gEl) gEl.textContent = `${greet}, Student`;
    }
    setInterval(updateStudentClock, 1000);
    updateStudentClock();

    async function loadStudentUser() {
        try {
            const res = await apiFetch('/auth/me');
            if (!res.ok) return;
            const data = await res.json();
            const user = data.data || data.user || data;
            if (user.name) {
                const initials = user.name.split(' ').map(w => w[0]).join('').substring(0, 2).toUpperCase();
                const avatar = document.getElementById('student-sidebar-avatar');
                const name = document.getElementById('student-sidebar-name');
                const greeting = document.getElementById('student-greeting');
                if (avatar) avatar.textContent = initials;
                if (name) name.textContent = user.name;
                if (greeting) {
                    const h = new Date().getHours();
                    const greet = h < 12 ? 'Good Morning' : h < 17 ? 'Good Afternoon' : 'Good Evening';
                    greeting.textContent = `${greet}, ${user.name.split(' ')[0]}`;
                }
            }
            if (user.email) {
                const email = document.getElementById('student-sidebar-email');
                if (email) email.textContent = user.email;
            }
            // Check if user is mahasiswa, if not redirect
            if (user.role && user.role !== 'mahasiswa') {
                // Admin viewing student portal - show switch back to admin
                const btn = document.getElementById('switch-admin-btn');
                if (btn) btn.style.display = 'flex';
            }
        } catch (e) {}
    }

    async function switchToAdmin() {
        try {
            const res = await fetch('/api/auth/login', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({ email: 'admin@voltspace.id', password: 'admin123' })
            });
            const data = await res.json();
            if (res.ok) {
                localStorage.setItem('token', data.token);
                location.href = '/dashboard';
            } else {
                alert('Gagal switch role: kredensial admin tidak ditemukan.');
            }
        } catch(e) {
            alert('Koneksi gagal saat switch role.');
        }
    }

    async function handleStudentLogout() {
        const btn = document.getElementById('student-logout-btn');
        const label = document.getElementById('student-logout-label');
        btn.disabled = true;
        label.textContent = 'Logging out…';
        try { await apiFetch('/auth/logout', { method: 'POST' }); } catch (e) {}
        localStorage.removeItem('token');
        location.href = '/login';
    }

    // Load pending bookings count for badge
    async function loadBookingBadge() {
        try {
            const res = await apiFetch('/mahasiswa/dashboard/peminjaman');
            if (!res.ok) return;
            const data = await res.json();
            const badge = document.getElementById('booking-badge');
            if (badge && data.pending > 0) {
                badge.textContent = data.pending;
                badge.classList.remove('hidden');
                badge.classList.add('flex');
            }
        } catch(e) {}
    }

    document.addEventListener('DOMContentLoaded', () => {
        loadStudentUser();
        loadBookingBadge();
    });
    </script>
    @stack('scripts')
</body>
</html>
