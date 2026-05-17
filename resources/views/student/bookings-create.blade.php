@extends('layouts.student')

@section('content')
{{-- Page Header --}}
<div class="flex items-center gap-4 mb-2">
    <a href="/student/bookings"
       class="flex items-center justify-center w-9 h-9 rounded-xl bg-[#1e293b] border border-[#334155] text-slate-400 hover:text-white hover:border-slate-500 transition-all flex-shrink-0">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="2"/></svg>
    </a>
    <div>
        <h1 class="text-[26px] font-extrabold text-white tracking-tight leading-none">New Booking Request</h1>
        <p class="text-slate-400 text-[13px] mt-1">Submit a room borrowing request</p>
    </div>
</div>

<div class="mt-7 max-w-3xl">
    <div class="space-y-6">
        {{-- Room Selection --}}
        <div class="rounded-2xl p-6" style="background:#1a2236; border:1px solid #263047;">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0"
                     style="background:rgba(0,212,170,0.15); border:1px solid rgba(0,212,170,0.25);">
                    <svg class="w-4 h-4" style="color:#00d4aa" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" stroke-width="2"/>
                    </svg>
                </div>
                <h2 class="text-[16px] font-bold text-white">Room Selection</h2>
            </div>

            <label class="block text-[13px] font-semibold text-slate-300 mb-2">
                Select Room <span class="text-red-400">*</span>
            </label>
            <div class="relative">
                <select id="ruangan_id" name="ruangan_id" required
                    class="w-full appearance-none rounded-xl px-4 py-3.5 text-[14px] text-white focus:outline-none focus:ring-2 focus:ring-[#00d4aa]/50 transition-all cursor-pointer pr-10"
                    style="background:#0f172a; border:1.5px solid #334155;">
                    <option value="">Choose a room...</option>
                </select>
                <svg class="absolute right-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500 pointer-events-none"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M19 9l-7 7-7-7" stroke-width="2"/>
                </svg>
            </div>

            {{-- Room Info Card - hidden until selected --}}
            <div id="room-info-card" class="hidden mt-4 rounded-xl p-4 flex items-center gap-5"
                 style="background:#0f172a; border:1px solid #1e293b;">
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

        {{-- Schedule Details --}}
        <div class="rounded-2xl p-6" style="background:#1a2236; border:1px solid #263047;">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0"
                     style="background:rgba(0,170,255,0.15); border:1px solid rgba(0,170,255,0.25);">
                    <svg class="w-4 h-4 text-[#00aaff]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2"/>
                    </svg>
                </div>
                <h2 class="text-[16px] font-bold text-white">Schedule Details</h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="block text-[13px] font-semibold text-slate-300 mb-2">
                        Date <span class="text-red-400">*</span>
                    </label>
                    <input type="date" id="tanggal_mulai" name="tanggal_mulai" required
                        class="w-full rounded-xl px-4 py-3.5 text-[14px] text-white focus:outline-none focus:ring-2 focus:ring-[#00d4aa]/50 transition-all [color-scheme:dark]"
                        style="background:#0f172a; border:1.5px solid #334155;">
                </div>
                <div>
                    <label class="block text-[13px] font-semibold text-slate-300 mb-2">
                        Start Time <span class="text-red-400">*</span>
                    </label>
                    <input type="time" id="waktu_mulai" name="waktu_mulai" required
                        class="w-full rounded-xl px-4 py-3.5 text-[14px] text-white focus:outline-none focus:ring-2 focus:ring-[#00d4aa]/50 transition-all [color-scheme:dark]"
                        style="background:#0f172a; border:1.5px solid #334155;">
                </div>
                <div>
                    <label class="block text-[13px] font-semibold text-slate-300 mb-2">
                        End Time <span class="text-red-400">*</span>
                    </label>
                    <input type="time" id="waktu_selesai" name="waktu_selesai" required
                        class="w-full rounded-xl px-4 py-3.5 text-[14px] text-white focus:outline-none focus:ring-2 focus:ring-[#00d4aa]/50 transition-all [color-scheme:dark]"
                        style="background:#0f172a; border:1.5px solid #334155;">
                </div>
            </div>

            <p class="text-[12px] text-slate-600 mt-1 flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2"/>
                </svg>
                Each room can only be booked by one person at a time. Max end time: 20:00.
            </p>
        </div>

        {{-- Purpose --}}
        <div class="rounded-2xl p-6" style="background:#1a2236; border:1px solid #263047;">
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
                style="background:#0f172a; border:1.5px solid #334155;"></textarea>
        </div>

        {{-- Evidence (Bukti Peminjaman) --}}
        <div class="rounded-2xl p-6" style="background:#1a2236; border:1px solid #263047;">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0"
                     style="background:rgba(255,170,0,0.15); border:1px solid rgba(255,170,0,0.25);">
                    <svg class="w-4 h-4 text-[#ffaa00]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" stroke-width="2"/>
                    </svg>
                </div>
                <h2 class="text-[16px] font-bold text-white">Evidence / Document</h2>
            </div>

            <label class="block text-[13px] font-semibold text-slate-300 mb-2">
                Upload File (Optional)
            </label>
            <div class="relative">
                <input type="file" id="evidence" name="evidence" accept=".pdf,.png,.jpg,.jpeg"
                    class="w-full rounded-xl px-4 py-3.5 text-[14px] text-white file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-[12px] file:font-semibold file:bg-[#00d4aa]/10 file:text-[#00d4aa] hover:file:bg-[#00d4aa]/20 transition-all cursor-pointer"
                    style="background:#0f172a; border:1.5px solid #334155;">
            </div>
            <p class="text-[11px] text-slate-500 mt-2">Format yang didukung: PDF, PNG, JPG, JPEG. (Maks 5MB)</p>
        </div>

        {{-- Action Buttons --}}
        <div class="flex gap-4">
            <a href="/student/bookings"
               class="flex-1 flex items-center justify-center py-3.5 rounded-xl font-bold text-[14px] text-white transition-all"
               style="background:#1e293b; border:1px solid #334155;">
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
<script>
(function () {
    let roomsData = [];

    const statusMap = {
        tersedia:  { label: 'Tersedia',         cls: 'text-emerald-400 border-emerald-500/30 bg-emerald-500/10' },
        digunakan: { label: 'Sedang Digunakan', cls: 'text-orange-400 border-orange-500/30 bg-orange-500/10' },
        dipesan:   { label: 'Dipesan',          cls: 'text-blue-400 border-blue-500/30 bg-blue-500/10' },
    };

    /* ── Load rooms from API ─────────────────────────── */
    async function loadRooms() {
        try {
            const res = await apiFetch('/ruangan');
            if (!res.ok) throw new Error('API error ' + res.status);
            const raw  = await res.json();
            roomsData  = Array.isArray(raw) ? raw : (raw.data || []);

            const sel = document.getElementById('ruangan_id');
            if (!roomsData.length) {
                sel.innerHTML = '<option value="">Tidak ada ruangan tersedia</option>';
                return;
            }

            sel.innerHTML = '<option value="">Choose a room...</option>' +
                roomsData.map(r =>
                    `<option value="${r.id}" ${r.status !== 'tersedia' ? 'disabled' : ''}>
                        ${r.nama_ruangan || r.id}${r.kode ? ' (' + r.kode + ')' : ''} — ${statusMap[r.status]?.label ?? r.status}
                    </option>`
                ).join('');

            /* Pre-select from query param */
            const preRoom = new URLSearchParams(location.search).get('room_id');
            if (preRoom) { sel.value = preRoom; updateRoomInfo(preRoom); }
        } catch (err) {
            console.error('[NewBooking] loadRooms error:', err);
            const sel = document.getElementById('ruangan_id');
            sel.innerHTML = '<option value="">Gagal memuat ruangan</option>';
        }
    }

    /* ── Update room info card ───────────────────────── */
    function updateRoomInfo(id) {
        const room = roomsData.find(r => String(r.id) === String(id));
        const card = document.getElementById('room-info-card');
        if (!room) { card.classList.add('hidden'); return; }

        const s = statusMap[room.status] || { label: room.status, cls: 'text-slate-400 border-slate-500/30 bg-slate-500/10' };
        document.getElementById('room-info-name').textContent     = room.nama_ruangan || room.id;
        document.getElementById('room-info-meta').textContent     = (room.kode || room.id) + ' · Tel-U Bandung';
        document.getElementById('room-info-capacity').textContent = (room.kapasitas ?? '—') + ' orang';
        const badge = document.getElementById('room-info-status');
        badge.textContent  = s.label;
        badge.className    = `px-2.5 py-1 rounded-full text-[11px] font-bold border ${s.cls}`;
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

        if (!ruanganId)            { return vsAlert.warning('Form Belum Lengkap', 'Pilih ruangan terlebih dahulu.'); }
        if (!tanggal)              { return vsAlert.warning('Form Belum Lengkap', 'Pilih tanggal peminjaman.'); }
        if (!waktuMulai)           { return vsAlert.warning('Form Belum Lengkap', 'Masukkan waktu mulai.'); }
        if (!waktuSel)             { return vsAlert.warning('Form Belum Lengkap', 'Masukkan waktu selesai.'); }
        if (waktuSel <= waktuMulai){ return vsAlert.warning('Waktu Tidak Valid', 'Waktu selesai harus setelah waktu mulai.'); }
        if (waktuSel > '20:00')    { return vsAlert.warning('Waktu Tidak Valid', 'Waktu selesai maksimal pukul 20:00.'); }
        if (!tujuan)               { return vsAlert.warning('Form Belum Lengkap', 'Isi tujuan peminjaman.'); }

        const btn = document.getElementById('submit-booking-btn');
        const orig = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<div class="w-4 h-4 border-2 border-[#0b1120] border-t-transparent rounded-full animate-spin mr-2"></div> Mengirim...';

        try {
            const evidenceFile = document.getElementById('evidence').files[0];
            
            // For file uploads, we generally need FormData.
            const formData = new FormData();
            formData.append('ruangan_id', ruanganId);
            formData.append('tanggal_mulai', tanggal);
            formData.append('tanggal_selesai', tanggal);
            formData.append('waktu_mulai', waktuMulai);
            formData.append('waktu_selesai', waktuSel);
            formData.append('tujuan', tujuan);
            if (evidenceFile) {
                formData.append('evidence', evidenceFile);
            }

            // Notice: When using FormData, we cannot use apiFetch with Content-Type: application/json.
            // But if your apiFetch already handles FormData correctly, we can pass it directly.
            // If apiFetch sets application/json manually, it might break. 
            // We will attempt to send standard FormData if your backend supports it.
            const token = localStorage.getItem('token');
            const res = await fetch('http://127.0.0.1:8000/api/peminjaman', {
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                    // Do NOT set Content-Type for FormData! Let the browser set it with the boundary.
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

    /* ── Event listeners ─────────────────────────────── */
    document.getElementById('ruangan_id').addEventListener('change', function () { updateRoomInfo(this.value); });

    // Attach submit to the real button (outside hidden form)
    document.getElementById('submit-booking-btn').addEventListener('click', submitBooking);

    // Min date = today
    document.getElementById('tanggal_mulai').min = new Date().toISOString().split('T')[0];

    document.addEventListener('DOMContentLoaded', loadRooms);
})();
</script>
@endpush
