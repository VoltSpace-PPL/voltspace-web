@extends('layouts.student')

@section('content')
{{-- Header --}}
<div class="flex items-center justify-between mb-7 flex-wrap gap-4">
    <div>
        <h1 class="text-[28px] font-extrabold text-white tracking-tight leading-none">My Bookings</h1>
        <p class="text-slate-400 text-[14px] mt-1.5">View your room booking requests and approval status</p>
    </div>
    <a href="/student/bookings/create"
       class="flex items-center gap-2 px-5 py-2.5 rounded-xl font-bold text-[14px] transition-all shadow-lg"
       style="background:#00d4aa; color:#0b1120; box-shadow:0 4px 16px rgba(0,212,170,0.3);">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
            <path d="M12 4v16m8-8H4"/>
        </svg>
        New Booking
    </a>
</div>

{{-- Stats Row --}}
<div class="grid grid-cols-3 gap-4 mb-6">
    <div class="rounded-2xl p-5 flex items-center gap-4" style="background:#1a2236; border:1px solid #263047;">
        <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0"
             style="background:rgba(99,179,237,0.15); border:1px solid rgba(99,179,237,0.2);">
            <svg class="w-5 h-5 text-[#63b3ed]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" stroke-width="2"/>
            </svg>
        </div>
        <div>
            <p class="text-slate-500 text-[11px] font-bold uppercase tracking-wider">Total Requests</p>
            <p class="text-[26px] font-extrabold text-white leading-none mt-0.5" id="s-total">—</p>
        </div>
    </div>

    <div class="rounded-2xl p-5 flex items-center gap-4" style="background:#1a2236; border:1px solid #263047;">
        <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0"
             style="background:rgba(246,173,85,0.15); border:1px solid rgba(246,173,85,0.2);">
            <svg class="w-5 h-5 text-[#f6ad55]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2"/>
            </svg>
        </div>
        <div>
            <p class="text-slate-500 text-[11px] font-bold uppercase tracking-wider">Pending</p>
            <p class="text-[26px] font-extrabold text-white leading-none mt-0.5" id="s-pending">—</p>
        </div>
    </div>

    <div class="rounded-2xl p-5 flex items-center gap-4" style="background:#1a2236; border:1px solid #263047;">
        <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0"
             style="background:rgba(0,212,170,0.15); border:1px solid rgba(0,212,170,0.2);">
            <svg class="w-5 h-5 text-[#00d4aa]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2"/>
            </svg>
        </div>
        <div>
            <p class="text-slate-500 text-[11px] font-bold uppercase tracking-wider">Approved</p>
            <p class="text-[26px] font-extrabold text-white leading-none mt-0.5" id="s-approved">—</p>
        </div>
    </div>
</div>

{{-- Search + Filter --}}
<div class="flex items-center gap-3 mb-6 flex-wrap">
    <div class="relative flex-1 min-w-[220px]">
        <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500 pointer-events-none"
             fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2"/>
        </svg>
        <input id="booking-search" type="text" placeholder="Search by room name or building..."
            class="w-full pl-11 pr-4 py-2.5 rounded-xl text-white text-[13px] placeholder:text-slate-600 focus:outline-none focus:ring-2 focus:ring-[#00d4aa]/40 transition-all"
            style="background:#1a2236; border:1px solid #263047;">
    </div>
    <div class="flex items-center gap-2 flex-shrink-0">
        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" stroke-width="2"/>
        </svg>
        <select id="booking-status-filter"
            class="px-4 py-2.5 rounded-xl text-white text-[13px] focus:outline-none focus:ring-2 focus:ring-[#00d4aa]/40 transition-all cursor-pointer"
            style="background:#1a2236; border:1px solid #263047;">
            <option value="">All</option>
            <option value="pending">Pending</option>
            <option value="disetujui">Approved</option>
            <option value="ditolak">Rejected</option>
            <option value="dibatalkan">Cancelled</option>
        </select>
    </div>
</div>

{{-- Booking List --}}
<div id="booking-list" class="space-y-4">
    <div class="flex flex-col items-center justify-center py-20 gap-3">
        <div class="w-8 h-8 border-2 border-[#00d4aa] border-t-transparent rounded-full animate-spin"></div>
        <span class="text-slate-500 text-[13px]">Memuat pengajuan...</span>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    let allBookings = [];

    const statusCfg = {
        pending:    { label: 'Pending',    cls: 'bg-yellow-500/15 text-yellow-400 border-yellow-500/30',   dot: 'bg-yellow-400' },
        disetujui:  { label: 'Approved',   cls: 'bg-emerald-500/15 text-emerald-400 border-emerald-500/30', dot: 'bg-emerald-400' },
        ditolak:    { label: 'Rejected',   cls: 'bg-red-500/15 text-red-400 border-red-500/30',             dot: 'bg-red-400' },
        dibatalkan: { label: 'Cancelled',  cls: 'bg-slate-500/15 text-slate-400 border-slate-500/30',       dot: 'bg-slate-400' },
    };

    function fmtDate(d) {
        if (!d) return '-';
        return new Date(d).toLocaleDateString('id-ID', { year:'numeric', month:'2-digit', day:'2-digit' });
    }
    function fmtDateTime(d) {
        if (!d) return '-';
        return new Date(d).toLocaleString('id-ID', { year:'numeric', month:'2-digit', day:'2-digit', hour:'2-digit', minute:'2-digit' }).replace(',','');
    }
    function time5(t) { return (t || '').substring(0, 5); }

    function canCancel(b) {
        if (['dibatalkan','ditolak'].includes(b.status)) return false;
        const tgl = new Date(b.tanggal_mulai); tgl.setHours(0,0,0,0);
        const now = new Date(); now.setHours(0,0,0,0);
        return Math.floor((tgl - now) / 86400000) >= 2;
    }

    function renderBookings(list) {
        const el = document.getElementById('booking-list');

        if (!list.length) {
            el.innerHTML = `<div class="text-center py-20">
                <svg class="w-14 h-14 text-slate-700 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" stroke-width="2"/>
                </svg>
                <p class="text-slate-500 text-[15px] font-medium">Tidak ada pengajuan ditemukan.</p>
                <a href="/student/bookings/create"
                   class="mt-5 inline-flex items-center gap-2 px-6 py-3 rounded-xl font-bold text-[13px] transition-all"
                   style="background:#00d4aa; color:#0b1120;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M12 4v16m8-8H4"/></svg>
                    Buat Peminjaman Baru
                </a>
            </div>`;
            return;
        }

        el.innerHTML = list.map(b => {
            const room = b.ruangan || {};
            const s    = statusCfg[b.status] || { label: b.status, cls: 'bg-slate-500/15 text-slate-400 border-slate-500/30', dot: 'bg-slate-400' };
            const showCancel = canCancel(b);

            return `
<div class="rounded-2xl overflow-hidden transition-all hover:border-slate-500"
     style="background:#1a2236; border:1px solid #263047;">

    {{-- Card Header --}}
    <div class="px-6 py-4 flex items-center justify-between gap-3 border-b" style="border-color:#263047;">
        <div class="flex items-center gap-3 flex-wrap min-w-0">
            {{-- Room icon + name --}}
            <div class="flex items-center gap-2.5 min-w-0">
                <svg class="w-5 h-5 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" stroke-width="2"/>
                </svg>
                <span class="text-white font-bold text-[17px] truncate">${room.nama_ruangan || 'Ruangan'}</span>
            </div>
            {{-- Status badge --}}
            <span class="flex-shrink-0 flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold border ${s.cls}">
                <span class="w-1.5 h-1.5 rounded-full ${s.dot}"></span>
                ${s.label}
            </span>
        </div>
        <span class="text-slate-600 text-[12px] flex-shrink-0 font-mono">ID: ${b.id || '—'}</span>
    </div>

    {{-- Sub-header meta --}}
    <div class="px-6 pt-3 pb-0 flex items-center gap-4 text-[12px] text-slate-500">
        <span class="flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" stroke-width="2"/></svg>
            ${room.kode || room.id || '—'}
        </span>
        <span class="flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" stroke-width="2"/></svg>
            Tel-U Bandung
        </span>
    </div>

    {{-- Info cards grid --}}
    <div class="px-6 py-4 grid grid-cols-3 gap-3">
        <div class="rounded-xl px-4 py-3" style="background:#0f172a; border:1px solid #1e293b;">
            <p class="flex items-center gap-1.5 text-slate-500 text-[11px] font-semibold uppercase tracking-wider mb-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2"/></svg>
                Date
            </p>
            <p class="text-white font-bold text-[14px]">${fmtDate(b.tanggal_mulai)}</p>
        </div>
        <div class="rounded-xl px-4 py-3" style="background:#0f172a; border:1px solid #1e293b;">
            <p class="flex items-center gap-1.5 text-slate-500 text-[11px] font-semibold uppercase tracking-wider mb-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2"/></svg>
                Time
            </p>
            <p class="text-white font-bold text-[14px]">${time5(b.waktu_mulai)} - ${time5(b.waktu_selesai)}</p>
        </div>
        <div class="rounded-xl px-4 py-3" style="background:#0f172a; border:1px solid #1e293b;">
            <p class="flex items-center gap-1.5 text-slate-500 text-[11px] font-semibold uppercase tracking-wider mb-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" stroke-width="2"/></svg>
                Attendees
            </p>
            <p class="text-white font-bold text-[14px]">—</p>
        </div>
    </div>

    {{-- Purpose --}}
    <div class="px-6 pb-4">
        <div class="rounded-xl px-4 py-3" style="background:#0f172a; border:1px solid #1e293b;">
            <p class="flex items-center gap-1.5 text-slate-500 text-[11px] font-semibold uppercase tracking-wider mb-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke-width="2"/></svg>
                Purpose
            </p>
            <p class="text-white text-[13px] leading-relaxed">${b.tujuan || '—'}</p>
        </div>
    </div>

    ${b.catatan_admin ? `
    <div class="px-6 pb-4">
        <div class="rounded-xl px-4 py-3" style="background:rgba(239,68,68,0.05); border:1px solid rgba(239,68,68,0.2);">
            <p class="text-red-400 text-[11px] font-bold uppercase tracking-wider mb-1.5">Catatan Admin</p>
            <p class="text-red-300 text-[13px]">${b.catatan_admin}</p>
        </div>
    </div>` : ''}

    {{-- Footer --}}
    <div class="px-6 py-3 flex items-center justify-between border-t" style="border-color:#263047;">
        <p class="text-slate-600 text-[12px]">
            Requested At: <span class="text-slate-500">${fmtDateTime(b.created_at)}</span>
        </p>
        ${showCancel ? `
        <button onclick="confirmCancel(${b.id})"
            class="flex items-center gap-2 px-4 py-2 rounded-xl text-[12px] font-bold transition-all"
            style="background:rgba(239,68,68,0.1); border:1px solid rgba(239,68,68,0.25); color:#f87171;">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M6 18L18 6M6 6l12 12"/></svg>
            Cancel Booking
        </button>` : ''}
    </div>
</div>`;
        }).join('');
    }

    /* ── Cancel booking ──────────────────────────────── */
    window.confirmCancel = async function (id) {
        const ok = await vsAlert.confirm(
            'Batalkan Peminjaman?',
            'Apakah kamu yakin ingin membatalkan pengajuan ini?<br><span class="text-yellow-400 text-[12px]">⚠ Pembatalan hanya bisa dilakukan maksimal H-2 sebelum tanggal acara.</span>',
            'Ya, Batalkan',
            'Tidak'
        );
        if (!ok) return;
        try {
            const res  = await apiFetch(`/peminjaman/${id}/cancel`, { method: 'POST' });
            const data = await res.json();
            if (res.ok) {
                vsAlert.success('Berhasil Dibatalkan', 'Pengajuan peminjaman berhasil dibatalkan.');
                loadBookings();
            } else {
                vsAlert.error('Gagal Membatalkan', data.message || 'Terjadi kesalahan.');
            }
        } catch (e) {
            vsAlert.error('Koneksi Gagal', 'Tidak dapat terhubung ke server.');
        }
    };

    /* ── Filter ──────────────────────────────────────── */
    function filterAndRender() {
        const q = document.getElementById('booking-search').value.toLowerCase();
        const s = document.getElementById('booking-status-filter').value;
        renderBookings(allBookings.filter(b => {
            const name = (b.ruangan?.nama_ruangan || '').toLowerCase();
            return (!q || name.includes(q) || (b.tujuan || '').toLowerCase().includes(q))
                && (!s || b.status === s);
        }));
    }

    /* ── Load stats ──────────────────────────────────── */
    async function loadStats() {
        try {
            const res  = await apiFetch('/mahasiswa/dashboard/peminjaman');
            if (!res.ok) return;
            const data = await res.json();
            document.getElementById('s-total').textContent    = data.total_request ?? 0;
            document.getElementById('s-pending').textContent  = data.pending ?? 0;
            document.getElementById('s-approved').textContent = data.approved ?? 0;
        } catch (e) {}
    }

    /* ── Load bookings ───────────────────────────────── */
    async function loadBookings() {
        try {
            const res  = await apiFetch('/peminjaman?per_page=50');
            if (!res.ok) throw new Error();
            const data = await res.json();
            allBookings = data.data || [];
            filterAndRender();
            loadStats();
        } catch (e) {
            document.getElementById('booking-list').innerHTML =
                '<div class="text-center py-16 text-slate-500">Gagal memuat data pengajuan.</div>';
        }
    }

    document.getElementById('booking-search').addEventListener('input', filterAndRender);
    document.getElementById('booking-status-filter').addEventListener('change', filterAndRender);
    document.addEventListener('DOMContentLoaded', loadBookings);
})();
</script>
@endpush
