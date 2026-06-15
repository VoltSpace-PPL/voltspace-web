@extends('layouts.main')

@section('content')
{{-- Page Header --}}
<div class="flex items-start justify-between mb-6 flex-wrap gap-4">
    <div>
        <h1 class="text-[28px] font-extrabold text-white tracking-tight leading-none">Room Availability</h1>
        <p class="text-slate-400 text-[14px] mt-2">Monitor room usage and click any room to see its booking calendar</p>
    </div>
</div>

{{-- Stats Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    <div class="rounded-2xl p-5 flex items-center gap-4" style="background:#161e2d; border:1px solid #1e2d45;">
        <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
             style="background:rgba(99,179,237,0.15); border:1px solid rgba(99,179,237,0.2);">
            <svg class="w-6 h-6 text-[#63b3ed]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" stroke-width="2"/>
            </svg>
        </div>
        <div>
            <p class="text-slate-500 text-[11px] font-bold uppercase tracking-wider">Total Rooms</p>
            <p class="text-[30px] font-extrabold text-white leading-none mt-0.5" id="stat-total">—</p>
        </div>
    </div>
    <div class="rounded-2xl p-5 flex items-center gap-4" style="background:#161e2d; border:1px solid #1e2d45;">
        <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
             style="background:rgba(0,212,170,0.15); border:1px solid rgba(0,212,170,0.2);">
            <svg class="w-6 h-6 text-[#00d4aa]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2"/>
            </svg>
        </div>
        <div>
            <p class="text-slate-500 text-[11px] font-bold uppercase tracking-wider">Available</p>
            <p class="text-[30px] font-extrabold text-white leading-none mt-0.5" id="stat-available">—</p>
        </div>
    </div>
    <div class="rounded-2xl p-5 flex items-center gap-4" style="background:#161e2d; border:1px solid #1e2d45;">
        <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
             style="background:rgba(246,173,85,0.15); border:1px solid rgba(246,173,85,0.2);">
            <svg class="w-6 h-6 text-[#f6ad55]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2"/>
            </svg>
        </div>
        <div>
            <p class="text-slate-500 text-[11px] font-bold uppercase tracking-wider">Booked/In Use</p>
            <p class="text-[30px] font-extrabold text-white leading-none mt-0.5" id="stat-booked">—</p>
        </div>
    </div>
</div>

{{-- Filter Bar --}}
<div class="flex items-center gap-3 mb-6 flex-wrap">
    <div class="relative flex-1 min-w-[200px]">
        <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2"/>
        </svg>
        <input id="room-search" type="text" placeholder="Search by room name..."
            class="w-full pl-11 pr-4 py-2.5 rounded-xl text-white text-[13px] placeholder:text-slate-600 focus:outline-none focus:ring-2 focus:ring-[#00d4aa]/40 transition-all"
            style="background:#161e2d; border:1px solid #1e2d45;">
    </div>
    {{-- Date filter --}}
    <div class="flex items-center gap-2 flex-shrink-0">
        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2"/>
        </svg>
        <input type="date" id="date-filter"
            class="px-4 py-2.5 rounded-xl text-white text-[13px] focus:outline-none focus:ring-2 focus:ring-[#00d4aa]/40 transition-all [color-scheme:dark]"
            style="background:#161e2d; border:1px solid #1e2d45;">
        <button id="clear-date-btn" class="hidden text-[12px] text-slate-400 hover:text-white px-2 py-1 rounded-lg hover:bg-white/5 transition-all">Clear</button>
    </div>
</div>

{{-- Rooms Grid --}}
<div id="rooms-grid" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
    <div class="col-span-3 flex flex-col items-center justify-center py-20 gap-3">
        <div class="w-8 h-8 border-2 border-[#00d4aa] border-t-transparent rounded-full animate-spin"></div>
        <span class="text-slate-500 text-[13px]">Loading rooms...</span>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════
     ROOM DETAIL / CALENDAR MODAL
     ════════════════════════════════════════════════════ --}}
<div id="room-detail-modal" class="fixed inset-0 z-[200] hidden" role="dialog" aria-modal="true">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeRoomDetail()"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-[720px] p-4 max-h-[92dvh] overflow-y-auto overscroll-contain" style="scrollbar-width:thin;">
        <div class="glass-effect rounded-[24px] shadow-2xl overflow-hidden">
            {{-- Header --}}
            <div class="px-7 py-5 flex justify-between items-center border-b border-white/10">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-[#00aaff]/15 border border-[#00aaff]/25 flex items-center justify-center text-[#00aaff]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <div>
                        <h3 class="text-[18px] font-bold text-white leading-none" id="modal-room-name">Room</h3>
                        <p class="text-[12px] text-slate-500 mt-0.5" id="modal-room-meta">—</p>
                    </div>
                </div>
                <button onclick="closeRoomDetail()" class="w-8 h-8 flex items-center justify-center rounded-full bg-white/5 text-slate-400 hover:text-white transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2.5"/></svg>
                </button>
            </div>

            {{-- Calendar navigation --}}
            <div class="px-7 py-4 flex items-center justify-between border-b border-white/5">
                <button onclick="changeCalendarMonth(-1)" class="w-8 h-8 flex items-center justify-center rounded-full bg-white/5 text-slate-300 hover:text-white hover:bg-white/10 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="2"/></svg>
                </button>
                <span id="calendar-month-label" class="text-[15px] font-bold text-white">—</span>
                <button onclick="changeCalendarMonth(1)" class="w-8 h-8 flex items-center justify-center rounded-full bg-white/5 text-slate-300 hover:text-white hover:bg-white/10 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="2"/></svg>
                </button>
            </div>

            {{-- Calendar grid --}}
            <div class="px-7 py-5">
                {{-- Day headers --}}
                <div class="grid grid-cols-7 gap-1 mb-2">
                    @foreach(['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $d)
                    <div class="text-center text-[11px] font-bold text-slate-500 uppercase tracking-wider py-1">{{ $d }}</div>
                    @endforeach
                </div>
                {{-- Calendar cells --}}
                <div id="calendar-grid" class="grid grid-cols-7 gap-1">
                    {{-- Populated by JS --}}
                </div>
            </div>

            {{-- Day Detail Panel (shown when a day is clicked) --}}
            <div id="day-detail-panel" class="hidden border-t border-white/10 px-7 py-5">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-[15px] font-bold text-white" id="day-detail-title">—</h4>
                    <button onclick="closeDayDetail()" class="text-[12px] text-slate-400 hover:text-white transition-colors">← Back to calendar</button>
                </div>

                {{-- Time slots grid --}}
                <div id="time-slots-grid" class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                    {{-- Populated by JS --}}
                </div>

                <div id="day-loading" class="hidden flex items-center justify-center py-8 gap-3">
                    <div class="w-5 h-5 border-2 border-[#00d4aa] border-t-transparent rounded-full animate-spin"></div>
                    <span class="text-slate-500 text-[13px]">Loading schedule...</span>
                </div>
            </div>

            {{-- Legend --}}
            <div class="px-7 pb-5 flex items-center gap-5 flex-wrap">
                <div class="flex items-center gap-2 text-[12px] text-slate-400">
                    <div class="w-3 h-3 rounded bg-[#00d4aa]/20 border border-[#00d4aa]/40"></div> Available
                </div>
                <div class="flex items-center gap-2 text-[12px] text-slate-400">
                    <div class="w-3 h-3 rounded bg-orange-500/20 border border-orange-500/40"></div> Booked
                </div>
                <div class="flex items-center gap-2 text-[12px] text-slate-400">
                    <div class="w-3 h-3 rounded bg-slate-700 border border-slate-600"></div> Today
                </div>
                <div class="flex items-center gap-2 text-[12px] text-slate-400">
                    <div class="w-3 h-3 rounded bg-slate-800 border border-slate-700 opacity-40"></div> Past
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
(function () {
    let allRooms = [];
    let activeBookings = [];
    let currentRoomId = null;
    let currentRoomName = '';
    let calendarYear  = new Date().getFullYear();
    let calendarMonth = new Date().getMonth(); // 0-based

    /* ── Status config ─────────────────────────────────── */
    const statusCfg = {
        tersedia:  { label: 'Available', cls: 'bg-emerald-500/15 text-emerald-400 border-emerald-500/30',  dot: 'bg-emerald-400' },
        dipesan:   { label: 'Booked',    cls: 'bg-orange-500/15 text-orange-400 border-orange-500/30',    dot: 'bg-orange-400' },
        digunakan: { label: 'In Use',    cls: 'bg-red-500/15 text-red-400 border-red-500/30',             dot: 'bg-red-400' },
    };

    function statusInfo(s) {
        return statusCfg[s] || { label: s, cls: 'bg-slate-500/15 text-slate-400 border-slate-500/30', dot: 'bg-slate-400' };
    }

    /* ── Stats ─────────────────────────────────────────── */
    function updateStats(rooms) {
        document.getElementById('stat-total').textContent     = rooms.length;
        document.getElementById('stat-available').textContent = rooms.filter(r => r.status === 'tersedia').length;
        document.getElementById('stat-booked').textContent    = rooms.filter(r => r.status === 'dipesan' || r.status === 'digunakan').length;
    }

    /* ── Render rooms grid ─────────────────────────────── */
    function renderRooms(rooms) {
        const grid = document.getElementById('rooms-grid');
        if (!rooms.length) {
            grid.innerHTML = `<div class="col-span-3 text-center py-20 text-slate-500">No rooms found.</div>`;
            return;
        }

        grid.innerHTML = rooms.map(r => {
            const s = statusInfo(r.status);
            const booking = activeBookings.find(b => b.ruangan_id === r.id && b.status === 'disetujui');

            return `
<div class="rounded-2xl overflow-hidden transition-all hover:scale-[1.01] hover:shadow-xl group"
     style="background:#161e2d; border:1px solid #1e2d45;">
    <div class="px-5 pt-5 pb-4">
        <div class="flex items-start justify-between gap-3 mb-4">
            <div class="flex items-center gap-3 min-w-0 flex-1">
                <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0"
                     style="background:rgba(0,170,255,0.12); border:1px solid rgba(0,170,255,0.2);">
                    <svg class="w-5 h-5 text-[#00aaff]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" stroke-width="2"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <h3 class="text-[15px] font-bold text-white leading-tight truncate">${r.nama_ruangan || 'Room'}</h3>
                    <p class="text-[12px] text-slate-500 mt-0.5">${r.kode || r.id} · Floor ${r.lantai || 1}</p>
                </div>
            </div>
            ${s.label !== 'In Use' && s.label !== 'Booked' ? `
            <span class="flex-shrink-0 flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold border ${s.cls}">
                <span class="w-1.5 h-1.5 rounded-full ${s.dot}"></span>
                ${s.label}
            </span>
            ` : ''}
        </div>

        <div class="space-y-2">
            <div class="flex items-center gap-2 text-slate-400 text-[13px]">
                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" stroke-width="2"/>
                </svg>
                <span>${r.kapasitas ?? 0} people</span>
            </div>
        </div>
    </div>

    <div class="px-5 pb-5 pt-1">
        <a href="/room-availability/${r.id}"
            class="w-full flex items-center justify-center gap-2 py-2.5 rounded-xl font-bold text-[13px] hover:scale-[1.01] transition-all"
            style="background:linear-gradient(135deg,rgba(0,212,170,0.15),rgba(0,170,255,0.15)); color:#00d4aa; border:1px solid rgba(0,212,170,0.3);">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            View Booking Calendar
        </a>
    </div>
</div>`;
        }).join('');
    }

    /* ── Filter rooms ──────────────────────────────────── */
    function filterAndRender() {
        const q    = document.getElementById('room-search').value.toLowerCase();
        const dateVal = document.getElementById('date-filter').value;

        let filtered = allRooms.filter(r => {
            if (r.status !== 'tersedia') return false;
            const name = (r.nama_ruangan || '').toLowerCase();
            const kode = (r.kode || r.id || '').toLowerCase();
            if (q && !name.includes(q) && !kode.includes(q)) return false;
            return true;
        });

        // If date filter set: also filter by bookings on that date
        if (dateVal) {
            const bookedRoomIds = activeBookings
                .filter(b => b.status === 'disetujui' && (b.tanggal_mulai || '').substring(0,10) === dateVal)
                .map(b => b.ruangan_id);
            filtered = filtered.map(r => ({
                ...r,
                _dateBooked: bookedRoomIds.includes(r.id)
            }));
        }

        renderRooms(filtered);
        updateStats(allRooms);
    }

    /* ── Load rooms ────────────────────────────────────── */
    async function loadRooms() {
        try {
            const res  = await apiFetch('/ruangan');
            if (!res.ok) throw new Error();
            const data = await res.json();
            allRooms   = Array.isArray(data) ? data : (data.data || []);
            filterAndRender();
        } catch (e) {
            document.getElementById('rooms-grid').innerHTML =
                '<div class="col-span-3 text-center py-16 text-slate-500">Failed to load room data.</div>';
        }
    }

    /* ── Load active bookings ───────────────────────────── */
    async function loadActiveBookings() {
        try {
            const res  = await apiFetch('/peminjaman?per_page=200');
            if (!res.ok) return;
            const data = await res.json();
            activeBookings = data.data || data || [];
        } catch (e) { activeBookings = []; }
    }

    /* ── Open Room Detail Modal ─────────────────────────── */
    window.openRoomDetail = function(roomId, roomName) {
        currentRoomId   = roomId;
        currentRoomName = roomName;
        const room = allRooms.find(r => r.id === roomId);

        document.getElementById('modal-room-name').textContent = roomName;
        document.getElementById('modal-room-meta').textContent =
            (room?.kode || roomId) + ' · Floor ' + (room?.lantai || 1) + ' · Capacity: ' + (room?.kapasitas ?? '?') + ' people';

        calendarYear  = new Date().getFullYear();
        calendarMonth = new Date().getMonth();

        closeDayDetail();
        renderCalendar();

        document.getElementById('room-detail-modal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    };

    window.closeRoomDetail = function() {
        document.getElementById('room-detail-modal').classList.add('hidden');
        document.body.style.overflow = 'auto';
        currentRoomId = null;
    };

    /* ── Calendar rendering ─────────────────────────────── */
    const MONTH_NAMES = ['January','February','March','April','May','June','July','August','September','October','November','December'];

    function renderCalendar() {
        document.getElementById('calendar-month-label').textContent = `${MONTH_NAMES[calendarMonth]} ${calendarYear}`;

        const grid = document.getElementById('calendar-grid');
        const today = new Date();
        const todayStr = today.toISOString().split('T')[0];

        // First day of month
        const firstDay = new Date(calendarYear, calendarMonth, 1).getDay(); // 0=Sun
        const daysInMonth = new Date(calendarYear, calendarMonth + 1, 0).getDate();

        // Get bookings for this room in this month
        const monthBookings = activeBookings.filter(b => {
            if (!['disetujui', 'pending'].includes(b.status)) return false;
            if (b.ruangan_id !== currentRoomId) return false;
            const d = (b.tanggal_mulai || '').substring(0, 7); // YYYY-MM
            return d === `${calendarYear}-${String(calendarMonth + 1).padStart(2, '0')}`;
        });

        // Map: day → [bookings]
        const dayMap = {};
        monthBookings.forEach(b => {
            const day = parseInt((b.tanggal_mulai || '').substring(8, 10));
            if (!dayMap[day]) dayMap[day] = [];
            dayMap[day].push(b);
        });

        let html = '';
        // Empty cells before first day
        for (let i = 0; i < firstDay; i++) {
            html += `<div></div>`;
        }

        for (let d = 1; d <= daysInMonth; d++) {
            const dateStr = `${calendarYear}-${String(calendarMonth + 1).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
            const isToday = dateStr === todayStr;
            const isPast  = dateStr < todayStr;
            const bookings = dayMap[d] || [];
            const hasBooking = bookings.length > 0;

            let cellCls, dotHtml = '';
            if (isPast) {
                cellCls = 'opacity-40 cursor-not-allowed';
            } else if (isToday) {
                cellCls = 'ring-2 ring-[#00d4aa] cursor-pointer hover:bg-white/5';
            } else {
                cellCls = 'cursor-pointer hover:bg-white/5';
            }

            if (hasBooking) {
                dotHtml = `<div class="mt-1 flex justify-center gap-0.5">${bookings.map(() => '<div class="w-1 h-1 rounded-full bg-orange-400"></div>').slice(0,3).join('')}</div>`;
            }

            html += `
            <div class="rounded-xl p-2 text-center transition-all ${cellCls} ${hasBooking ? 'bg-orange-500/10 border border-orange-500/20' : 'border border-transparent hover:border-white/10'}"
                 onclick="${isPast ? '' : `openDayDetail('${dateStr}', ${d})`}" >
                <span class="text-[13px] font-${isToday ? 'extrabold text-[#00d4aa]' : hasBooking ? 'bold text-orange-300' : 'medium text-slate-300'}">${d}</span>
                ${dotHtml}
            </div>`;
        }

        grid.innerHTML = html;
    }

    window.changeCalendarMonth = function(delta) {
        calendarMonth += delta;
        if (calendarMonth > 11) { calendarMonth = 0; calendarYear++; }
        if (calendarMonth < 0)  { calendarMonth = 11; calendarYear--; }
        closeDayDetail();
        renderCalendar();
    };

    /* ── Day Detail: time slots ─────────────────────────── */
    window.openDayDetail = async function(dateStr, day) {
        const panel = document.getElementById('day-detail-panel');
        const title = document.getElementById('day-detail-title');
        const slotsGrid = document.getElementById('time-slots-grid');
        const loading = document.getElementById('day-loading');

        const dateObj = new Date(dateStr + 'T00:00:00');
        const dayNames = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
        title.textContent = `${dayNames[dateObj.getDay()]}, ${MONTH_NAMES[calendarMonth]} ${day}, ${calendarYear}`;

        panel.classList.remove('hidden');
        slotsGrid.innerHTML = '';
        loading.classList.remove('hidden');

        // Build 7:00–20:00 in 1-hour slots
        const hours = [];
        for (let h = 7; h < 20; h++) hours.push(h);

        try {
            // Fetch bookings for this room on this specific date
            const res = await apiFetch(`/peminjaman?tanggal=${dateStr}&per_page=100`);
            let dayBookings = [];
            if (res.ok) {
                const data = await res.json();
                const all = data.data || data || [];
                dayBookings = all.filter(b =>
                    b.ruangan_id === currentRoomId &&
                    ['disetujui', 'pending'].includes(b.status) &&
                    (b.tanggal_mulai || '').substring(0, 10) === dateStr
                );
            }

            loading.classList.add('hidden');

            // Check each hour slot
            const today = new Date();
            const nowStr = `${String(today.getHours()).padStart(2,'0')}:${String(today.getMinutes()).padStart(2,'0')}`;
            const todayDateStr = today.toISOString().split('T')[0];

            slotsGrid.innerHTML = hours.map(h => {
                const slotStart = `${String(h).padStart(2,'0')}:00`;
                const slotEnd   = `${String(h+1).padStart(2,'0')}:00`;
                const label     = `${slotStart} – ${slotEnd}`;

                // Find booking overlapping this slot
                const booking = dayBookings.find(b => {
                    const bStart = (b.waktu_mulai || '').substring(0,5);
                    const bEnd   = (b.waktu_selesai || '').substring(0,5);
                    return !(slotEnd <= bStart || slotStart >= bEnd);
                });

                const isPastSlot = dateStr < todayDateStr || (dateStr === todayDateStr && slotEnd <= nowStr);

                let slotCls, slotLabel, slotSub;
                if (booking) {
                    slotCls   = 'bg-orange-500/10 border-orange-500/30 text-orange-300';
                    slotLabel = booking.status === 'pending' ? 'Pending' : 'Booked';
                    slotSub   = booking.user?.name || 'Someone';
                } else if (isPastSlot) {
                    slotCls   = 'bg-slate-800/40 border-slate-700/40 text-slate-600 opacity-50';
                    slotLabel = 'Past';
                    slotSub   = '';
                } else {
                    slotCls   = 'bg-[#00d4aa]/8 border-[#00d4aa]/25 text-[#00d4aa]';
                    slotLabel = 'Available';
                    slotSub   = '';
                }

                return `
                <div class="rounded-xl p-3 border ${slotCls} transition-all">
                    <p class="text-[12px] font-bold">${label}</p>
                    <p class="text-[11px] mt-0.5 font-semibold">${slotLabel}</p>
                    ${slotSub ? `<p class="text-[10px] mt-0.5 opacity-70 truncate">${slotSub}</p>` : ''}
                </div>`;
            }).join('');
        } catch (e) {
            loading.classList.add('hidden');
            slotsGrid.innerHTML = '<div class="col-span-3 text-slate-500 text-[13px] py-4 text-center">Failed to load schedule.</div>';
        }
    };

    window.closeDayDetail = function() {
        document.getElementById('day-detail-panel').classList.add('hidden');
    };

    /* ── Admin cancel booking ──────────────────────────── */
    window.adminCancelBooking = async function(id, roomName, userName) {
        const ok = await vsAlert.confirm(
            'Cancel Booking?',
            `Cancel the booking for <strong>${roomName}</strong> by <strong>${userName}</strong>?`,
            'Yes, Cancel', 'No'
        );
        if (!ok) return;
        try {
            const res  = await apiFetch(`/peminjaman/${id}/cancel`, { method: 'POST' });
            const data = await res.json();
            if (res.ok) {
                vsAlert.success('Cancelled', 'Booking has been cancelled.');
                await loadActiveBookings();
                await loadRooms();
            } else {
                vsAlert.error('Failed', data.message || 'Something went wrong.');
            }
        } catch (e) {
            vsAlert.error('Error', 'Could not connect to server.');
        }
    };

    /* ── Import schedule (file picker) – XLSX only ─────── */
    window.triggerImport = function() {
        document.getElementById('import-file-input').value = '';
        document.getElementById('import-file-input').click();
    };

    window.handleImportFile = async function(input) {
        const file = input.files[0];
        if (!file) return;

        if (!file.name.toLowerCase().endsWith('.xlsx')) {
            vsAlert.error('Invalid File', 'Only .xlsx files are allowed for import.');
            input.value = '';
            return;
        }

        const btn = document.getElementById('import-schedule-btn');
        const origText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = `<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path></svg> Importing...`;

        try {
            const form = new FormData();
            form.append('file', file);
            const res  = await apiFetch('/jadwal-listrik/import', { method: 'POST', body: form });
            const data = await res.json();
            if (res.ok) {
                vsAlert.success('Import Successful', `Successfully imported ${data.created ?? 0} schedule(s).`);
                loadRooms();
            } else {
                vsAlert.error('Import Failed', data.message || 'Could not process the file.');
            }
        } catch (e) {
            vsAlert.error('Connection Error', 'Could not connect to server.');
        } finally {
            btn.disabled = false;
            btn.innerHTML = origText;
        }
    };

    /* ── Event listeners ───────────────────────────────── */
    document.getElementById('room-search').addEventListener('input', filterAndRender);
    document.getElementById('date-filter').addEventListener('change', function() {
        document.getElementById('clear-date-btn').classList.toggle('hidden', !this.value);
        filterAndRender();
    });
    document.getElementById('clear-date-btn').addEventListener('click', function() {
        document.getElementById('date-filter').value = '';
        this.classList.add('hidden');
        filterAndRender();
    });

    /* ── Init ──────────────────────────────────────────── */
    document.addEventListener('DOMContentLoaded', async () => {
        await loadActiveBookings();
        await loadRooms();
    });
})();
</script>
@endpush
