@extends('layouts.student')

@section('content')
{{-- Page Header --}}
<div class="mb-8">
    <h1 class="text-[26px] font-bold text-white tracking-tight leading-none">Student Dashboard</h1>
    <p class="text-slate-400 text-[13px] mt-1.5">Welcome to VoltSpace Room Booking System</p>
</div>

{{-- Stats Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8" id="student-stats">
    <!-- Total Requests -->
    <div class="rounded-2xl p-6 flex items-center justify-between" style="background:#161e2d; border:1px solid #1e2d45;">
        <div>
            <p class="text-slate-400 text-[12px] font-medium mb-3">Total Requests</p>
            <h3 class="text-[36px] font-bold text-white leading-none" id="stat-total">—</h3>
        </div>
        <div class="rounded-full flex items-center justify-center flex-shrink-0" style="width:52px;height:52px;background:rgba(59,130,246,0.12);border:1px solid rgba(59,130,246,0.25);">
            <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        </div>
    </div>

    <!-- Pending -->
    <div class="rounded-2xl p-6 flex items-center justify-between" style="background:#161e2d; border:1px solid #1e2d45;">
        <div>
            <p class="text-slate-400 text-[12px] font-medium mb-3">Pending</p>
            <h3 class="text-[36px] font-bold text-white leading-none" id="stat-pending">—</h3>
        </div>
        <div class="rounded-full flex items-center justify-center flex-shrink-0" style="width:52px;height:52px;background:rgba(234,179,8,0.12);border:1px solid rgba(234,179,8,0.25);">
            <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
    </div>

    <!-- Approved -->
    <div class="rounded-2xl p-6 flex items-center justify-between" style="background:#161e2d; border:1px solid #1e2d45;">
        <div>
            <p class="text-slate-400 text-[12px] font-medium mb-3">Approved</p>
            <h3 class="text-[36px] font-bold text-white leading-none" id="stat-approved">—</h3>
        </div>
        <div class="rounded-full flex items-center justify-center flex-shrink-0" style="width:52px;height:52px;background:rgba(34,197,94,0.12);border:1px solid rgba(34,197,94,0.25);">
            <svg class="w-6 h-6 text-[#22c55e]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        </div>
    </div>

    <!-- Rejected -->
    <div class="rounded-2xl p-6 flex items-center justify-between" style="background:#161e2d; border:1px solid #1e2d45;">
        <div>
            <p class="text-slate-400 text-[12px] font-medium mb-3">Rejected</p>
            <h3 class="text-[36px] font-bold text-white leading-none" id="stat-rejected">—</h3>
        </div>
        <div class="rounded-full flex items-center justify-center flex-shrink-0" style="width:52px;height:52px;background:rgba(239,68,68,0.12);border:1px solid rgba(239,68,68,0.25);">
            <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9" stroke-width="2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 9l-6 6M9 9l6 6"/></svg>
        </div>
    </div>
</div>

{{-- Quick Actions --}}
<div class="mb-8">
    <h2 class="text-[16px] font-bold text-white mb-4">Quick Actions</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <a href="/student/room-availability" class="group flex flex-col p-6 rounded-2xl transition-all hover:scale-[1.02] hover:border-blue-500/50" style="background:#161e2d; border:1px solid #1e2d45;">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center mb-5" style="background:rgba(59,130,246,0.12);border:1px solid rgba(59,130,246,0.25);">
                <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" stroke-width="2"/></svg>
            </div>
            <p class="text-white font-bold text-[15px] mb-1">View Available Rooms</p>
            <p class="text-slate-400 text-[12px]">Check room availability and usage status</p>
        </a>

        <a href="/student/bookings/create" class="group flex flex-col p-6 rounded-2xl transition-all hover:scale-[1.02] hover:border-emerald-500/50" style="background:#161e2d; border:1px solid #1e2d45;">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center mb-5" style="background:rgba(34,197,94,0.12);border:1px solid rgba(34,197,94,0.25);">
                <svg class="w-6 h-6 text-[#22c55e]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2"/></svg>
            </div>
            <p class="text-white font-bold text-[15px] mb-1">New Booking Request</p>
            <p class="text-slate-400 text-[12px]">Submit a new room borrowing request</p>
        </a>

        <a href="/student/bookings" class="group flex flex-col p-6 rounded-2xl transition-all hover:scale-[1.02] hover:border-purple-500/50" style="background:#161e2d; border:1px solid #1e2d45;">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center mb-5" style="background:rgba(168,85,247,0.12);border:1px solid rgba(168,85,247,0.25);">
                <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" stroke-width="2"/></svg>
            </div>
            <p class="text-white font-bold text-[15px] mb-1">My Bookings</p>
            <p class="text-slate-400 text-[12px]">View your booking requests and status</p>
        </a>
    </div>
</div>

{{-- Recent Bookings --}}
<div class="sm:rounded-2xl -mx-6 lg:mx-0 px-6 sm:px-6 pt-5 pb-2 mb-10" style="background:#161e2d; border:1px solid #1e2d45;">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-[16px] font-bold text-white">Recent Booking Requests</h2>
        <a href="/student/bookings" class="text-[#00d4aa] text-[13px] font-bold hover:underline flex items-center gap-1">
            View All &rarr;
        </a>
    </div>
    
    <div class="overflow-x-auto custom-scrollbar -mx-6 sm:mx-0 px-6 sm:px-0">
        <table class="w-full text-left border-collapse min-w-[700px]">
            <thead>
                <tr class="text-slate-400 text-[12px]" style="border-bottom:1px solid #1e2d45;">
                    <th class="px-6 py-4 font-medium w-[120px]">ID</th>
                    <th class="px-6 py-4 font-medium">Room</th>
                    <th class="px-6 py-4 font-medium">Date</th>
                    <th class="px-6 py-4 font-medium">Time</th>
                    <th class="px-6 py-4 font-medium">Status</th>
                </tr>
            </thead>
            <tbody id="recent-bookings-list">
                <tr>
                    <td colspan="5" class="px-6 py-14 text-center">
                        <div class="flex flex-col items-center justify-center gap-3">
                            <div class="w-7 h-7 border-2 border-[#00d4aa] border-t-transparent rounded-full animate-spin"></div>
                            <span class="text-slate-500 text-[13px]">Loading data...</span>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
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
                    document.getElementById('recent-bookings-list').innerHTML = '<tr><td colspan="5" class="text-slate-500 text-center py-6 text-[13px]">Feature restricted to students.</td></tr>';
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
            'pending':    { bg: 'rgba(234,179,8,0.12)',   color: '#eab308', border: 'rgba(234,179,8,0.3)',   label: 'Pending' },
            'disetujui':  { bg: 'rgba(34,197,94,0.12)',   color: '#22c55e', border: 'rgba(34,197,94,0.3)',   label: 'Approved' },
            'ditolak':    { bg: 'rgba(239,68,68,0.12)',   color: '#ef4444', border: 'rgba(239,68,68,0.3)',   label: 'Rejected' },
            'dibatalkan': { bg: 'rgba(100,116,139,0.12)', color: '#94a3b8', border: 'rgba(100,116,139,0.3)', label: 'Cancelled' },
        };
        const s = map[status] || { bg: 'rgba(100,116,139,0.12)', color: '#94a3b8', border: 'rgba(100,116,139,0.3)', label: status };
        return `<span class="px-3 py-1.5 rounded-full text-[11px] font-bold" style="background:${s.bg};color:${s.color};border:1px solid ${s.border};">${s.label}</span>`;
    }

    function renderRecentBookings(bookings) {
        const el = document.getElementById('recent-bookings-list');
        if (!bookings.length) {
            el.innerHTML = `<tr><td colspan="5" class="text-center py-10">
                <p class="text-slate-500 text-[13px]">No recent bookings found.</p>
            </td></tr>`;
            return;
        }

        el.innerHTML = bookings.map(b => {
            const room = b.ruangan || {};
            const id = 'BK' + String(b.id).padStart(3, '0');
            const date = b.tanggal_mulai ? b.tanggal_mulai.substring(0, 10) : '-';
            const time = `${(b.waktu_mulai||'').substring(0,5)} - ${(b.waktu_selesai||'').substring(0,5)}`;
            
            return `
                <tr class="transition-colors" style="border-bottom:1px solid rgba(30,45,69,0.7);" onmouseenter="this.style.background='rgba(255,255,255,0.015)'" onmouseleave="this.style.background=''">
                    <td class="px-6 py-5 text-[14px] font-bold text-white whitespace-nowrap">${id}</td>
                    <td class="px-6 py-5">
                        <p class="text-[14px] font-bold text-white truncate max-w-[200px]">${room.nama_ruangan || 'Ruangan'}</p>
                        <p class="text-[12px] text-slate-500 truncate max-w-[200px] mt-0.5">${room.kode || '-'}</p>
                    </td>
                    <td class="px-6 py-5">
                        <div class="flex items-center gap-2 text-[13px] text-slate-300">
                            <svg class="w-4 h-4 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            ${date}
                        </div>
                    </td>
                    <td class="px-6 py-5">
                        <div class="flex items-center gap-2 text-[13px] text-slate-300 whitespace-nowrap">
                            <svg class="w-4 h-4 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            ${time}
                        </div>
                    </td>
                    <td class="px-6 py-5 whitespace-nowrap">
                        ${statusBadge(b.status)}
                    </td>
                </tr>
            `;
        }).join('');
    }

    document.addEventListener('DOMContentLoaded', loadDashboard);
})();
</script>
@endpush
