@extends('layouts.student')

@section('content')
{{-- Page Header --}}
<div class="flex items-center gap-4 mb-2">
    <a href="/student/bookings"
       class="flex items-center justify-center w-9 h-9 rounded-xl bg-[#161e2d] border border-[#1e2d45] text-slate-400 hover:text-white hover:border-slate-500 transition-all flex-shrink-0">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="2"/></svg>
    </a>
    <div>
        <h1 class="text-[26px] font-extrabold text-white tracking-tight leading-none">New Booking Request</h1>
        <p class="text-slate-400 text-[13px] mt-1">Submit a room borrowing request</p>
    </div>
</div>

<div class="mt-7 max-w-3xl">
    <div class="space-y-6">

        {{-- STEP 1: Schedule Details (first) --}}
        <div class="rounded-2xl p-6" style="background:#161e2d; border:1px solid #1e2d45;">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0"
                     style="background:rgba(0,170,255,0.15); border:1px solid rgba(0,170,255,0.25);">
                    <svg class="w-4 h-4 text-[#00aaff]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-[16px] font-bold text-white">Schedule Details</h2>
                    <p class="text-[12px] text-slate-500 mt-0.5">Fill in your date and time first, then available rooms will appear below</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="block text-[13px] font-semibold text-slate-300 mb-2">
                        Date <span class="text-red-400">*</span>
                    </label>
                    <input type="date" id="tanggal_mulai" name="tanggal_mulai" required
                        class="w-full rounded-xl px-4 py-3.5 text-[14px] text-white focus:outline-none focus:ring-2 focus:ring-[#00d4aa]/50 transition-all [color-scheme:dark] bg-[#0d1829] border border-[#1e2d45]">
                </div>
                <div>
                    <label class="block text-[13px] font-semibold text-slate-300 mb-2">
                        Start Time <span class="text-red-400">*</span>
                    </label>
                    <input type="text" id="waktu_mulai" name="waktu_mulai" required placeholder="18.00"
                        class="w-full rounded-xl px-4 py-3.5 text-[14px] text-white focus:outline-none focus:ring-2 focus:ring-[#00d4aa]/50 transition-all bg-[#0d1829] border border-[#1e2d45]">
                </div>
                <div>
                    <label class="block text-[13px] font-semibold text-slate-300 mb-2">
                        End Time <span class="text-red-400">*</span>
                    </label>
                    <input type="text" id="waktu_selesai" name="waktu_selesai" required placeholder="20.00"
                        class="w-full rounded-xl px-4 py-3.5 text-[14px] text-white focus:outline-none focus:ring-2 focus:ring-[#00d4aa]/50 transition-all bg-[#0d1829] border border-[#1e2d45]">
                </div>
            </div>

            <p class="text-[12px] text-slate-600 mt-1 flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2"/>
                </svg>
                Isi tanggal dan waktu terlebih dahulu agar ruangan yang tersedia di jam tersebut muncul. (Rentang waktu peminjaman: 06:00 - 20:00 WIB)
            </p>

            {{-- Availability check status --}}
            <div id="availability-status" class="hidden mt-4 rounded-xl px-4 py-3 flex items-center gap-3 text-[13px]" style="background:#0d1829; border:1px solid #1e2d45;">
                <div id="availability-spinner" class="hidden w-4 h-4 border-2 border-[#00d4aa] border-t-transparent rounded-full animate-spin flex-shrink-0"></div>
                <svg id="availability-check-icon" class="hidden w-4 h-4 text-[#00d4aa] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                <span id="availability-text" class="text-slate-400"></span>
            </div>
        </div>

        {{-- STEP 2: Room Selection (populated after time input) --}}
        <div class="rounded-2xl p-6" style="background:#161e2d; border:1px solid #1e2d45;">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0"
                     style="background:rgba(0,212,170,0.15); border:1px solid rgba(0,212,170,0.25);">
                    <svg class="w-4 h-4" style="color:#00d4aa" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" stroke-width="2"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-[16px] font-bold text-white">Room Selection</h2>
                    <p class="text-[12px] text-slate-500 mt-0.5">Rooms available at your selected time slot</p>
                </div>
            </div>

            {{-- Placeholder when no time is selected yet --}}
            <div id="room-placeholder" class="rounded-xl p-6 text-center" style="background:#0d1829; border:1px dashed #1e2d45;">
                <svg class="w-8 h-8 text-slate-700 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2"/>
                </svg>
                <p class="text-slate-500 text-[13px] font-medium">Pilih tanggal dan waktu terlebih dahulu</p>
                <p class="text-slate-600 text-[12px] mt-1">Ruangan yang tersedia akan muncul di sini</p>
            </div>

            {{-- Actual select dropdown (shown once time filled) --}}
            <div id="room-select-wrap" class="hidden">
                <label class="block text-[13px] font-semibold text-slate-300 mb-2">
                    Select Room <span class="text-red-400">*</span>
                </label>
                <div class="relative">
                    <select id="ruangan_id" name="ruangan_id" required
                        class="w-full appearance-none rounded-xl px-4 py-3.5 text-[14px] text-white focus:outline-none focus:ring-2 focus:ring-[#00d4aa]/50 transition-all cursor-pointer pr-10"
                        style="background:#0d1829; border:1px solid #1e2d45;">
                        <option value="">Choose a room...</option>
                    </select>
                    <svg class="absolute right-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500 pointer-events-none"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M19 9l-7 7-7-7" stroke-width="2"/>
                    </svg>
                </div>

                {{-- Room Info Card - hidden until selected --}}
                <div id="room-info-card" class="hidden mt-4 rounded-xl p-4 flex items-center gap-5"
                     style="background:#0d1829; border:1px solid #1e2d45;">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                         style="background:rgba(0,170,255,0.12); border:1px solid rgba(0,170,255,0.2);">
                        <svg class="w-5 h-5 text-[#00aaff]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" stroke-width="2"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-white font-bold text-[14px]" id="room-info-name">—</p>
                        <p class="text-slate-500 text-[12px] mt-0.5" id="room-info-meta">—</p>
                    </div>
                    <div id="room-info-status-wrap">
                        <span id="room-info-status" class="px-2.5 py-1 rounded-full text-[11px] font-bold border"></span>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <p class="text-slate-500 text-[11px]">Kapasitas</p>
                        <p class="text-white font-bold text-[15px]" id="room-info-capacity">—</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Purpose --}}
        <div class="rounded-2xl p-6" style="background:#161e2d; border:1px solid #1e2d45;">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0"
                     style="background:rgba(183,148,246,0.15); border:1px solid rgba(183,148,246,0.25);">
                    <svg class="w-4 h-4 text-[#b794f6]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke-width="2"/>
                    </svg>
                </div>
                <h2 class="text-[16px] font-bold text-white">Purpose</h2>
            </div>

            <label class="block text-[13px] font-semibold text-slate-300 mb-2">
                Purpose <span class="text-red-400">*</span>
            </label>
            <textarea id="tujuan" name="tujuan" rows="4" required
                placeholder="Describe the purpose of room usage (e.g., study group, meeting, presentation, workshop)"
                class="w-full rounded-xl px-4 py-3.5 text-[14px] text-white placeholder:text-slate-600 focus:outline-none focus:ring-2 focus:ring-[#00d4aa]/50 transition-all resize-none"
                style="background:#0d1829; border:1px solid #1e2d45;"></textarea>
        </div>

        {{-- Evidence (Bukti Peminjaman) --}}
        <div class="rounded-2xl p-6" style="background:#161e2d; border:1px solid #1e2d45;">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0"
                     style="background:rgba(255,170,0,0.15); border:1px solid rgba(255,170,0,0.25);">
                    <svg class="w-4 h-4 text-[#ffaa00]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" stroke-width="2"/>
                    </svg>
                </div>
                <h2 class="text-[16px] font-bold text-white">Evidence / Document</h2>
            </div>

            <div class="flex items-center justify-between mb-3">
                <label class="block text-[13px] font-semibold text-slate-300">
                    Upload File <span class="text-red-400">*</span> <span class="text-slate-500 font-normal">(Required – XLSX only)</span>
                </label>
                <button type="button" id="download-template-btn"
                    class="text-[12px] text-[#00d4aa] bg-[#00d4aa]/10 border border-[#00d4aa]/20 hover:bg-[#00d4aa]/20 px-3 py-1.5 rounded-lg flex items-center gap-1.5 font-bold transition-all focus:outline-none shadow-sm">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" stroke-width="2.5"/></svg>
                    Download Template Surat
                </button>
            </div>
            <div class="relative">
                <input type="file" id="surat_peminjaman" name="surat_peminjaman" accept=".xlsx" required
                    class="w-full rounded-xl px-4 py-3.5 text-[14px] text-white file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-[12px] file:font-semibold file:bg-[#00d4aa]/10 file:text-[#00d4aa] hover:file:bg-[#00d4aa]/20 transition-all cursor-pointer"
                    style="background:#0d1829; border:1px solid #1e2d45;">
            </div>
            <p class="text-[11px] text-slate-500 mt-2">Hanya format <span class="text-[#00d4aa] font-bold">.xlsx</span> yang diterima. (Maks 5MB)</p>
        </div>

        {{-- Action Buttons --}}
        <div class="flex gap-4">
            <a href="/student/bookings"
               class="flex-1 flex items-center justify-center py-3.5 rounded-xl font-bold text-[14px] text-white transition-all"
               style="background:#1e293b; border:1px solid #1e2d45;">
                Cancel
            </a>
            <button type="submit" form="booking-form" id="submit-booking-btn"
                class="flex-1 flex items-center justify-center py-3.5 rounded-xl font-bold text-[14px] transition-all shadow-lg"
                style="background:#00d4aa; color:#0b1120; box-shadow:0 4px 20px rgba(0,212,170,0.3);">
                Submit Request
            </button>
        </div>
    </div>
</div>

{{-- Hidden form wrapper for submit --}}
<form id="booking-form" class="hidden"></form>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" type="text/css" href="https://npmcdn.com/flatpickr/dist/themes/dark.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://npmcdn.com/flatpickr/dist/l10n/id.js"></script>
<script>
(function () {
    let roomsData = [];
    let availabilityCheckTimer = null;

    const urlParams = new URLSearchParams(window.location.search);

    flatpickr("#waktu_mulai", {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        altInput: true,
        altFormat: "H.i",
        time_24hr: true,
        minTime: "06:00",
        maxTime: "20:00",
        defaultDate: urlParams.get('start_time') || null,
        onChange: scheduleAvailabilityCheck
    });

    flatpickr("#waktu_selesai", {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        altInput: true,
        altFormat: "H.i",
        time_24hr: true,
        minTime: "06:00",
        maxTime: "20:00",
        defaultDate: urlParams.get('end_time') || null,
        onChange: scheduleAvailabilityCheck
    });

    /* ── Load ALL rooms from API (cache) ────────────── */
    async function loadAllRooms() {
        try {
            const res = await apiFetch('/ruangan');
            if (!res.ok) throw new Error('API error ' + res.status);
            const raw = await res.json();
            roomsData = Array.isArray(raw) ? raw : (raw.data || []);
        } catch (err) {
            console.error('[NewBooking] loadAllRooms error:', err);
            roomsData = [];
        }
    }

    /* ── Filter rooms by checking bookings for the selected time slot ── */
    async function checkAndFilterRooms() {
        const tanggal    = document.getElementById('tanggal_mulai').value;
        const waktuMulai = document.getElementById('waktu_mulai').value;
        const waktuSel   = document.getElementById('waktu_selesai').value;

        // If not all three filled, reset room select to placeholder
        if (!tanggal || !waktuMulai || !waktuSel) {
            showRoomPlaceholder();
            return;
        }

        if (waktuSel <= waktuMulai) {
            showAvailabilityStatus('error', 'Waktu selesai harus lebih dari waktu mulai.');
            showRoomPlaceholder();
            return;
        }

        showAvailabilityStatus('loading', 'Memeriksa ketersediaan ruangan...');

        try {
            // Fetch available rooms from the backend, allowing it to handle all complex schedule checks
            // Backend will return ALL rooms, but append `is_available` and `reason` properties to them.
            const res = await apiFetch(`/ruangan?cek_tanggal=${tanggal}&waktu_mulai=${waktuMulai}&waktu_selesai=${waktuSel}`);
            
            if (!res.ok) throw new Error('API error ' + res.status);
            const processedRooms = await res.json();

            populateRoomSelect(processedRooms);

            const countAvailable = processedRooms.filter(r => r.is_available).length;
            if (countAvailable === 0) {
                showAvailabilityStatus('error', `Semua ruangan penuh pada jam ${waktuMulai}–${waktuSel}.`);
            } else {
                showAvailabilityStatus('ok', `Menampilkan total ${processedRooms.length} ruangan (Tersedia: ${countAvailable}).`);
            }
        } catch (e) {
            console.error('[NewBooking] availability check error:', e);
            // Fallback: show all rooms
            const processedRooms = roomsData.map(r => ({
                ...r,
                is_available: true,
                reason: 'Tersedia (Offline)'
            }));
            populateRoomSelect(processedRooms);
            showAvailabilityStatus('ok', `Menampilkan total ${processedRooms.length} ruangan (mode offline).`);
        }
    }

    function showRoomPlaceholder() {
        document.getElementById('room-placeholder').classList.remove('hidden');
        document.getElementById('room-select-wrap').classList.add('hidden');
        document.getElementById('room-info-card').classList.add('hidden');
        document.getElementById('availability-status').classList.add('hidden');
    }

    function showAvailabilityStatus(type, msg) {
        const wrap = document.getElementById('availability-status');
        const spinner = document.getElementById('availability-spinner');
        const checkIcon = document.getElementById('availability-check-icon');
        const text = document.getElementById('availability-text');

        wrap.classList.remove('hidden');
        spinner.classList.add('hidden');
        checkIcon.classList.add('hidden');

        if (type === 'loading') {
            spinner.classList.remove('hidden');
            text.className = 'text-slate-400';
        } else if (type === 'ok') {
            checkIcon.classList.remove('hidden');
            text.className = 'text-[#00d4aa]';
        } else {
            text.className = 'text-orange-400';
        }
        text.textContent = msg;
    }

    function populateRoomSelect(rooms) {
        document.getElementById('room-placeholder').classList.add('hidden');
        document.getElementById('room-select-wrap').classList.remove('hidden');
        document.getElementById('room-info-card').classList.add('hidden');

        const sel = document.getElementById('ruangan_id');
        sel.value = '';

        if (!rooms.length) {
            sel.innerHTML = '<option value="">Data ruangan kosong</option>';
            return;
        }

        sel.innerHTML = '<option value="">Pilih ruangan...</option>' +
            rooms.map(r => {
                const name = `${r.nama_ruangan || r.id}${r.kode ? ' (' + r.kode + ')' : ''}`;
                if (r.is_available === false) {
                    return `<option value="" disabled style="color: #64748b;">${name} — Tidak Tersedia (${r.reason})</option>`;
                }
                return `<option value="${r.id}">${name} — Kapasitas: ${r.kapasitas ?? '?'}</option>`;
            }).join('');

        /* Pre-select from query param */
        const preRoom = new URLSearchParams(location.search).get('ruangan_id') || new URLSearchParams(location.search).get('room_id');
        if (preRoom) {
            const r = rooms.find(rm => String(rm.id) === String(preRoom));
            if (r && r.is_available !== false) {
                sel.value = preRoom;
                updateRoomInfo(preRoom);
            }
        }
    }

    /* ── Update room info card ───────────────────────── */
    function updateRoomInfo(id) {
        const room = roomsData.find(r => String(r.id) === String(id));
        const card = document.getElementById('room-info-card');
        if (!room) { card.classList.add('hidden'); return; }

        document.getElementById('room-info-name').textContent     = room.nama_ruangan || room.id;
        document.getElementById('room-info-meta').textContent     = (room.kode || room.id) + ' · Tel-U Bandung';
        document.getElementById('room-info-capacity').textContent = (room.kapasitas ?? '—') + ' orang';
        const badge = document.getElementById('room-info-status');
        badge.textContent  = 'Tersedia';
        badge.className    = 'px-2.5 py-1 rounded-full text-[11px] font-bold border text-emerald-400 border-emerald-500/30 bg-emerald-500/10';
        card.classList.remove('hidden');
    }

    /* ── Form submit ─────────────────────────────────── */
    async function submitBooking(e) {
        e.preventDefault();

        const ruanganId  = document.getElementById('ruangan_id').value;
        const tanggal    = document.getElementById('tanggal_mulai').value;
        const waktuMulai = document.getElementById('waktu_mulai').value;
        const waktuSel   = document.getElementById('waktu_selesai').value;
        const tujuan     = document.getElementById('tujuan').value.trim();

        if (!tanggal)              { return vsAlert.warning('Form Belum Lengkap', 'Pilih tanggal peminjaman.'); }
        if (!waktuMulai)           { return vsAlert.warning('Form Belum Lengkap', 'Masukkan waktu mulai.'); }
        if (!waktuSel)             { return vsAlert.warning('Form Belum Lengkap', 'Masukkan waktu selesai.'); }
        if (waktuSel <= waktuMulai){ return vsAlert.warning('Waktu Tidak Valid', 'Waktu selesai harus setelah waktu mulai.'); }
        if (waktuMulai < "06:00" || waktuSel > "20:00") {
            return vsAlert.warning('Waktu Tidak Valid', 'Pemesanan hanya diizinkan antara pukul 06:00 hingga 20:00 WIB.');
        }
        if (!ruanganId)            { return vsAlert.warning('Form Belum Lengkap', 'Pilih ruangan terlebih dahulu.'); }
        if (!tujuan)               { return vsAlert.warning('Form Belum Lengkap', 'Isi tujuan peminjaman.'); }

        const evidenceFile = document.getElementById('surat_peminjaman').files[0];
        if (!evidenceFile)         { return vsAlert.warning('Form Belum Lengkap', 'Upload bukti/surat peminjaman (format XLSX).'); }
        if (!evidenceFile.name.toLowerCase().endsWith('.xlsx')) { return vsAlert.warning('Format Tidak Valid', 'File harus berformat XLSX.'); }

        const btn = document.getElementById('submit-booking-btn');
        const orig = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<div class="w-4 h-4 border-2 border-[#0b1120] border-t-transparent rounded-full animate-spin mr-2"></div> Mengirim...';

        try {
            const formData = new FormData();
            formData.append('ruangan_id', ruanganId);
            formData.append('tanggal_mulai', tanggal);
            formData.append('tanggal_selesai', tanggal);
            formData.append('waktu_mulai', waktuMulai);
            formData.append('waktu_selesai', waktuSel);
            formData.append('tujuan', tujuan);
            formData.append('surat_peminjaman', evidenceFile);

            const token = localStorage.getItem('token');
            const res = await fetch('http://127.0.0.1:8000/api/peminjaman', {
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                },
                body: formData,
            });
            const data = await res.json();
            if (res.ok) {
                await vsAlert.success('Pengajuan Berhasil!', 'Permintaan peminjaman ruangan telah dikirim. Tunggu persetujuan dari admin ya!');
                location.href = '/student/bookings';
            } else {
                const msg = data?.errors
                    ? Object.values(data.errors).flat().join('<br>')
                    : (data.message || 'Terjadi kesalahan.');
                vsAlert.error('Gagal Mengajukan', msg);
            }
        } catch (err) {
            vsAlert.error('Koneksi Gagal', 'Tidak dapat terhubung ke server.');
        } finally {
            btn.disabled = false;
            btn.innerHTML = orig;
        }
    }

    /* ── Debounced check when any time/date field changes ── */
    function scheduleAvailabilityCheck() {
        clearTimeout(availabilityCheckTimer);
        availabilityCheckTimer = setTimeout(checkAndFilterRooms, 600);
    }

    /* ── Event listeners ─────────────────────────────── */
    document.getElementById('tanggal_mulai').addEventListener('change', scheduleAvailabilityCheck);

    document.getElementById('ruangan_id').addEventListener('change', function () { updateRoomInfo(this.value); });

    document.getElementById('download-template-btn').addEventListener('click', async function() {
        try {
            const btn = this;
            const originalHtml = btn.innerHTML;
            btn.innerHTML = 'Downloading...';
            btn.disabled = true;

            const res = await apiFetch('/peminjaman/template/download');
            if (!res.ok) { throw new Error('Gagal mengunduh template'); }
            const blob = await res.blob();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'Template_Surat_Peminjaman.xlsx';
            document.body.appendChild(a);
            a.click();
            a.remove();
            window.URL.revokeObjectURL(url);

            btn.innerHTML = originalHtml;
            btn.disabled = false;
        } catch(e) {
            vsAlert.error('Error', 'Gagal mengunduh template surat peminjaman.');
            this.innerHTML = '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" stroke-width="2"/></svg> Download Template Surat';
            this.disabled = false;
        }
    });

    // Attach submit to the real button (outside hidden form)
    document.getElementById('submit-booking-btn').addEventListener('click', submitBooking);

    // Min date = tomorrow
    let tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    document.getElementById('tanggal_mulai').min = tomorrow.toISOString().split('T')[0];

    document.addEventListener('DOMContentLoaded', async () => {
        const params = new URLSearchParams(window.location.search);
        const dateParam = params.get('selected_date') || params.get('date');
        if (dateParam) {
            const tglInput = document.getElementById('tanggal_mulai');
            tglInput.value = dateParam;
        }

        await loadAllRooms();

        if (dateParam && params.get('start_time') && params.get('end_time')) {
            checkAndFilterRooms();
        }
    });
})();
</script>
@endpush
