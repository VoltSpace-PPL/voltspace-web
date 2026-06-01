@extends('layouts.student')

@section('content')
{{-- Page Header --}}
<div class="mb-8">
    <h1 class="text-[28px] font-extrabold text-white tracking-tight leading-none">Dashboard</h1>
    <p class="text-slate-400 text-[14px] mt-2">Selamat datang di Student Portal VoltSpace</p>
</div>

{{-- Stats Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8" id="student-stats">
    <div class="rounded-2xl p-5 relative overflow-hidden transition-all duration-300" style="background:#161d2e; border:1px solid #232c3d;">
        <div class="flex items-start justify-between mb-4">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:rgba(99,179,237,0.15); border:1px solid rgba(99,179,237,0.2);">
                <svg class="w-5 h-5" style="color:#63b3ed" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" stroke-width="2"/></svg>
            </div>
        </div>
        <p class="text-slate-500 text-[10px] font-bold uppercase tracking-wider mb-1">Total Requests</p>
        <p class="text-[26px] font-extrabold text-white leading-none" id="stat-total">—</p>
    </div>

    <div class="rounded-2xl p-5 relative overflow-hidden transition-all duration-300" style="background:#161d2e; border:1px solid #232c3d;">
        <div class="flex items-start justify-between mb-4">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:rgba(246,173,85,0.15); border:1px solid rgba(246,173,85,0.2);">
                <svg class="w-5 h-5" style="color:#f6ad55" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2"/></svg>
            </div>
        </div>
        <p class="text-slate-500 text-[10px] font-bold uppercase tracking-wider mb-1">Pending</p>
        <p class="text-[26px] font-extrabold text-white leading-none" id="stat-pending">—</p>
    </div>

    <div class="rounded-2xl p-5 relative overflow-hidden transition-all duration-300" style="background:#161d2e; border:1px solid #232c3d;">
        <div class="flex items-start justify-between mb-4">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:rgba(0,212,170,0.15); border:1px solid rgba(0,212,170,0.2);">
                <svg class="w-5 h-5" style="color:#00d4aa" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2"/></svg>
            </div>
        </div>
        <p class="text-slate-500 text-[10px] font-bold uppercase tracking-wider mb-1">Approved</p>
        <p class="text-[26px] font-extrabold text-white leading-none" id="stat-approved">—</p>
    </div>

    <div class="rounded-2xl p-5 relative overflow-hidden transition-all duration-300" style="background:#161d2e; border:1px solid #232c3d;">
        <div class="flex items-start justify-between mb-4">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:rgba(248,113,113,0.15); border:1px solid rgba(248,113,113,0.2);">
                <svg class="w-5 h-5" style="color:#f87171" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2"/></svg>
            </div>
        </div>
        <p class="text-slate-500 text-[10px] font-bold uppercase tracking-wider mb-1">Rejected</p>
        <p class="text-[26px] font-extrabold text-white leading-none" id="stat-rejected">—</p>
    </div>
</div>

{{-- Quick Actions --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
    <a href="/student/rooms" class="group flex items-center gap-4 p-5 rounded-2xl transition-all hover:scale-[1.02]" style="background:rgba(0,170,255,0.08); border:1px solid rgba(0,170,255,0.2);">
        <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-[#00aaff]/15 border border-[#00aaff]/20 flex-shrink-0">
            <svg class="w-6 h-6 text-[#00aaff]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" stroke-width="2"/></svg>
        </div>
        <div>
            <p class="text-white font-bold text-[15px]">Room Available</p>
            <p class="text-slate-400 text-[12px] mt-0.5">Lihat ketersediaan ruangan</p>
        </div>
        <svg class="w-5 h-5 text-slate-600 ml-auto group-hover:text-[#00aaff] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="2"/></svg>
    </a>

    <a href="/student/bookings/create" class="group flex items-center gap-4 p-5 rounded-2xl transition-all hover:scale-[1.02]" style="background:rgba(0,212,170,0.08); border:1px solid rgba(0,212,170,0.2);">
        <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-[#00d4aa]/15 border border-[#00d4aa]/20 flex-shrink-0">
            <svg class="w-6 h-6 text-[#00d4aa]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="2.5"/></svg>
        </div>
        <div>
            <p class="text-white font-bold text-[15px]">New Booking</p>
            <p class="text-slate-400 text-[12px] mt-0.5">Ajukan peminjaman ruangan</p>
        </div>
        <svg class="w-5 h-5 text-slate-600 ml-auto group-hover:text-[#00d4aa] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="2"/></svg>
    </a>

    <a href="/student/bookings" class="group flex items-center gap-4 p-5 rounded-2xl transition-all hover:scale-[1.02]" style="background:rgba(183,148,246,0.08); border:1px solid rgba(183,148,246,0.2);">
        <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-[#b794f6]/15 border border-[#b794f6]/20 flex-shrink-0">
            <svg class="w-6 h-6 text-[#b794f6]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" stroke-width="2"/></svg>
        </div>
        <div>
            <p class="text-white font-bold text-[15px]">My Bookings</p>
            <p class="text-slate-400 text-[12px] mt-0.5">Lihat semua pengajuanmu</p>
        </div>
        <svg class="w-5 h-5 text-slate-600 ml-auto group-hover:text-[#b794f6] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="2"/></svg>
    </a>
</div>

{{-- Recent Bookings --}}
<div class="bg-[#1c2333] border border-[#2a3347] rounded-2xl p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-[18px] font-bold text-white">Recent Bookings</h2>
            <p class="text-slate-500 text-[12px] mt-0.5">5 pengajuan terakhirmu</p>
        </div>
        <a href="/student/bookings" class="flex items-center gap-1.5 text-[#00d4aa] text-[13px] font-semibold hover:underline">
            Lihat Semua
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="2"/></svg>
        </a>
    </div>
    <div id="recent-bookings-list">
        <div class="flex flex-col items-center justify-center py-10 gap-3">
            <div class="w-7 h-7 border-2 border-[#00d4aa] border-t-transparent rounded-full animate-spin"></div>
            <span class="text-slate-500 text-[13px]">Memuat data...</span>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function() {
    async function loadDashboard() {
        try {
            const res = await apiFetch('/mahasiswa/dashboard/peminjaman');
            if (!res.ok) {
                if (res.status === 403) {
                    // Not mahasiswa - show info
                    document.getElementById('recent-bookings-list').innerHTML = '<p class="text-slate-500 text-center py-6 text-[13px]">Fitur ini khusus untuk akun mahasiswa.</p>';
                }
                return;
            }
            const data = await res.json();
            document.getElementById('stat-total').textContent    = data.total_request ?? 0;
            document.getElementById('stat-pending').textContent  = data.pending ?? 0;
            document.getElementById('stat-approved').textContent = data.approved ?? 0;
            document.getElementById('stat-rejected').textContent = data.rejected ?? 0;

            renderRecentBookings(data.recent_booking || []);
        } catch(e) {
            console.error('[Student Dashboard]', e);
        }
    }

    function statusBadge(status) {
        const map = {
            'pending':    { cls: 'bg-yellow-500/15 text-yellow-400 border-yellow-500/20',  label: 'Pending' },
            'disetujui':  { cls: 'bg-emerald-500/15 text-emerald-400 border-emerald-500/20', label: 'Approved' },
            'ditolak':    { cls: 'bg-red-500/15 text-red-400 border-red-500/20',            label: 'Rejected' },
            'dibatalkan': { cls: 'bg-slate-500/15 text-slate-400 border-slate-500/20',      label: 'Cancelled' },
        };
        const t = map[status] || { cls: 'bg-slate-500/15 text-slate-400 border-slate-500/20', label: status };
        return `<span class="px-2.5 py-1 rounded-full text-[11px] font-bold border ${t.cls}">${t.label}</span>`;
    }

    function renderRecentBookings(bookings) {
        const el = document.getElementById('recent-bookings-list');
        if (!bookings.length) {
            el.innerHTML = `<div class="text-center py-10">
                <svg class="w-12 h-12 text-slate-700 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" stroke-width="2"/></svg>
                <p class="text-slate-500 text-[14px]">Belum ada pengajuan peminjaman.</p>
                <a href="/student/bookings/create" class="mt-4 inline-flex items-center gap-2 px-5 py-2.5 bg-[#00d4aa] text-white rounded-xl font-bold text-[13px] hover:bg-[#00bfa0] transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="2.5"/></svg>
                    Buat Peminjaman
                </a>
            </div>`;
            return;
        }

        el.innerHTML = bookings.map(b => {
            const room = b.ruangan || {};
            const tanggal = b.tanggal_mulai ? new Date(b.tanggal_mulai).toLocaleDateString('id-ID', {day:'2-digit',month:'short',year:'numeric'}) : '-';
            const waktu = `${(b.waktu_mulai||'').substring(0,5)} - ${(b.waktu_selesai||'').substring(0,5)}`;
            return `<div class="flex items-center justify-between py-4 border-b border-[#2a3347] last:border-0 gap-4">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-white font-bold text-[14px] truncate">${room.nama_ruangan || 'Ruangan'}</span>
                        ${statusBadge(b.status)}
                    </div>
                    <p class="text-slate-500 text-[12px]">${tanggal} &bull; ${waktu}</p>
                    <p class="text-slate-600 text-[12px] truncate mt-0.5">${b.tujuan || '-'}</p>
                </div>
                <a href="/student/bookings" class="flex-shrink-0 text-[#00d4aa] hover:underline text-[12px] font-semibold">Detail →</a>
            </div>`;
        }).join('');
    }

    document.addEventListener('DOMContentLoaded', loadDashboard);
})();
</script>
@endpush
