@extends('layouts.main')

@section('content')
<div class="space-y-6">
    {{-- Page Header --}}
    <div>
        <h1 class="text-[26px] font-bold text-white tracking-tight leading-none">Room Bookings Management</h1>
        <p class="text-slate-400 text-[13px] mt-1.5">Manage and approve student room borrowing requests</p>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        {{-- Total Bookings --}}
        <div class="rounded-2xl p-6 flex items-center justify-between" style="background:#161e2d; border:1px solid #1e2d45;">
            <div>
                <p class="text-slate-400 text-[12px] font-medium mb-3">Total Bookings</p>
                <h3 class="text-[36px] font-bold text-white leading-none" id="stat-total">—</h3>
            </div>
            <div class="w-13 h-13 rounded-xl flex items-center justify-center flex-shrink-0" style="width:52px;height:52px;background:rgba(59,130,246,0.1);border:1px solid rgba(59,130,246,0.2);">
                <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
        </div>

        {{-- Pending Requests --}}
        <div class="rounded-2xl p-6 flex items-center justify-between" style="background:#161e2d; border:1px solid #1e2d45;">
            <div>
                <p class="text-slate-400 text-[12px] font-medium mb-3">Pending Requests</p>
                <h3 class="text-[36px] font-bold text-white leading-none" id="stat-pending">—</h3>
            </div>
            <div class="rounded-xl flex items-center justify-center flex-shrink-0" style="width:52px;height:52px;background:rgba(234,179,8,0.1);border:1px solid rgba(234,179,8,0.2);">
                <svg class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>

        {{-- Approved Today --}}
        <div class="rounded-2xl p-6 flex items-center justify-between" style="background:#161e2d; border:1px solid #1e2d45;">
            <div>
                <p class="text-slate-400 text-[12px] font-medium mb-3">Approved Today</p>
                <h3 class="text-[36px] font-bold text-white leading-none" id="stat-approved-today">—</h3>
            </div>
            <div class="rounded-xl flex items-center justify-center flex-shrink-0" style="width:52px;height:52px;background:rgba(16,185,129,0.1);border:1px solid rgba(16,185,129,0.2);">
                <svg class="w-6 h-6 text-[#10b981]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
        </div>
    </div>

    {{-- Search & Filter --}}
    <div class="rounded-2xl p-4 flex flex-col sm:flex-row gap-3 items-center" style="background:#161e2d; border:1px solid #1e2d45;">
        <div class="flex-1 relative w-full">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <input type="text" id="searchInput" placeholder="Search by student name, ID, or room..."
                class="w-full text-slate-300 text-[13px] rounded-xl pl-10 pr-4 py-3 transition-all placeholder-slate-500 outline-none focus:ring-1 focus:ring-[#00d4aa] border border-[#00d4aa]"
                style="background:#0b1120;">
        </div>
        <div class="flex items-center gap-2.5 shrink-0">
            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
            <select id="statusFilter"
                class="text-slate-300 text-[13px] rounded-xl px-4 py-3 w-[120px] transition-colors outline-none focus:ring-1 focus:ring-[#00d4aa] border border-[#1e2d45]"
                style="background:#0b1120;">
                <option value="">All</option>
                <option value="pending">Pending</option>
                <option value="disetujui">Approved</option>
                <option value="ditolak">Rejected</option>
                <option value="dibatalkan">Cancelled</option>
            </select>
        </div>
    </div>

    {{-- Table --}}
    <div class="rounded-2xl overflow-hidden" style="background:#161e2d; border:1px solid #1e2d45;">
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left border-collapse min-w-[950px]">
                <thead>
                    <tr class="text-slate-400 text-[12px]" style="border-bottom:1px solid #1e293b;">
                        <th class="px-6 py-4 font-medium w-[100px]">ID</th>
                        <th class="px-6 py-4 font-medium">Student</th>
                        <th class="px-6 py-4 font-medium">Room</th>
                        <th class="px-6 py-4 font-medium">Date</th>
                        <th class="px-6 py-4 font-medium">Time</th>
                        <th class="px-6 py-4 font-medium">Status</th>
                        <th class="px-6 py-4 font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody id="bookingsTableBody">
                    <tr>
                        <td colspan="7" class="px-6 py-14 text-center">
                            <div class="flex flex-col items-center justify-center gap-3">
                                <div class="w-7 h-7 border-2 border-[#00d4aa] border-t-transparent rounded-full animate-spin"></div>
                                <span class="text-slate-500 text-[13px]">Loading bookings...</span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="px-6 py-4 flex flex-col sm:flex-row justify-between items-center gap-3 hidden" id="paginationContainer" style="border-top:1px solid #1e293b;">
            <span class="text-[12px] text-slate-500" id="paginationInfo">Showing 0 to 0 of 0 entries</span>
            <div class="flex gap-2" id="paginationButtons"></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function() {
    let allData = [];
    let currentPage = 1;

    const tableBody = document.getElementById('bookingsTableBody');
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');

    // ── Load Data ────────────────────────────────────────
    async function loadBookings(page = 1) {
        currentPage = page;
        try {
            const res = await apiFetch(`/peminjaman?per_page=100&page=${page}`);
            if (!res.ok) throw new Error('API error');
            const json = await res.json();
            allData = json.data || json;

            if (json.last_page !== undefined) {
                updatePagination(json);
            } else {
                document.getElementById('paginationContainer').classList.add('hidden');
            }

            updateStats();
            renderTable();
        } catch (err) {
            tableBody.innerHTML = `<tr><td colspan="7" class="px-6 py-10 text-center text-red-400 text-[13px]">Failed to load data. Please try again.</td></tr>`;
        }
    }

    // ── Stats ─────────────────────────────────────────────
    function updateStats() {
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('stat-total').textContent = allData.length;
        document.getElementById('stat-pending').textContent = allData.filter(d => d.status === 'pending').length;
        document.getElementById('stat-approved-today').textContent = allData.filter(d => d.status === 'disetujui' && d.reviewed_at && d.reviewed_at.startsWith(today)).length;
    }

    // ── Render Table ──────────────────────────────────────
    function renderTable() {
        const search = searchInput.value.toLowerCase();
        const status = statusFilter.value;

        const filtered = allData.filter(item => {
            if (status && item.status !== status) return false;
            if (search) {
                const name  = (item.user?.name || '').toLowerCase();
                const room  = (item.ruangan?.nama_ruangan || '').toLowerCase();
                const email = (item.user?.email || '').toLowerCase();
                const id    = `BK${String(item.id).padStart(3,'0')}`.toLowerCase();
                return name.includes(search) || room.includes(search) || email.includes(search) || id.includes(search);
            }
            return true;
        });

        if (!filtered.length) {
            tableBody.innerHTML = `<tr><td colspan="7" class="px-6 py-10 text-center text-slate-500 text-[13px]">No bookings found matching your criteria.</td></tr>`;
            return;
        }

        tableBody.innerHTML = filtered.map(item => {
            const bkId     = `BK${String(item.id).padStart(3, '0')}`;
            const name     = item.user?.name || 'Unknown';
            const nim      = (item.user?.email || '').split('@')[0];
            const roomName = item.ruangan?.nama_ruangan || '-';
            const roomCode = item.ruangan?.kode || '';
            const date     = item.tanggal_mulai ? item.tanggal_mulai.substring(0, 10) : '-';
            const time     = `${(item.waktu_mulai||'').substring(0,5)} - ${(item.waktu_selesai||'').substring(0,5)}`;

            // Status badge — match Figma colors
            const statusMap = {
                pending:    { bg: 'rgba(234,179,8,0.1)',   color: '#eab308', border: 'rgba(234,179,8,0.2)',   label: 'Pending' },
                disetujui:  { bg: 'rgba(16,185,129,0.1)',  color: '#10b981', border: 'rgba(16,185,129,0.2)',  label: 'Approved' },
                ditolak:    { bg: 'rgba(239,68,68,0.1)',   color: '#ef4444', border: 'rgba(239,68,68,0.2)',   label: 'Rejected' },
                dibatalkan: { bg: 'rgba(100,116,139,0.1)', color: '#94a3b8', border: 'rgba(100,116,139,0.2)', label: 'Cancelled' },
            };
            const s = statusMap[item.status] || { bg: 'rgba(100,116,139,0.1)', color: '#94a3b8', border: 'rgba(100,116,139,0.2)', label: item.status };
            const badge = `<span class="px-3 py-1 rounded-full text-[11px] font-bold" style="background:${s.bg};color:${s.color};border:1px solid ${s.border};">${s.label}</span>`;

            // Action buttons — circular outline style
            let actions = '';
            if (item.status === 'pending') {
                actions = `
                    <div class="flex items-center gap-2">
                        <button onclick="approveBooking(${item.id})"
                            title="Approve"
                            style="width:28px;height:28px;border-radius:50%;border:1px solid rgba(16,185,129,0.5);background:rgba(16,185,129,0.1);color:#10b981;display:flex;align-items:center;justify-content:center;transition:all 0.2s;"
                            onmouseenter="this.style.background='rgba(16,185,129,0.2)'"
                            onmouseleave="this.style.background='rgba(16,185,129,0.1)'">
                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </button>
                        <button onclick="rejectBooking(${item.id})"
                            title="Reject"
                            style="width:28px;height:28px;border-radius:50%;border:1px solid rgba(239,68,68,0.5);background:rgba(239,68,68,0.1);color:#ef4444;display:flex;align-items:center;justify-content:center;transition:all 0.2s;"
                            onmouseenter="this.style.background='rgba(239,68,68,0.2)'"
                            onmouseleave="this.style.background='rgba(239,68,68,0.1)'">
                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>`;
            }

            return `
                <tr class="transition-colors" style="border-bottom:1px solid #1e293b;" onmouseenter="this.style.background='rgba(255,255,255,0.02)'" onmouseleave="this.style.background=''">
                    <td class="px-6 py-4 text-[13px] font-bold text-white whitespace-nowrap">${bkId}</td>
                    <td class="px-6 py-4">
                        <p class="text-[13px] font-bold text-white">${name}</p>
                        <p class="text-[12px] text-slate-500 mt-0.5">${nim}</p>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-[13px] font-bold text-white">${roomName}</p>
                        <p class="text-[12px] text-slate-500 mt-0.5">${roomCode}</p>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center gap-2 text-[13px] text-slate-300">
                            <svg class="w-4 h-4 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            ${date}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center gap-2 text-[13px] text-slate-300">
                            <svg class="w-4 h-4 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            ${time}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">${badge}</td>
                    <td class="px-6 py-4 whitespace-nowrap">${actions}</td>
                </tr>`;
        }).join('');
    }

    // ── Pagination ────────────────────────────────────────
    function updatePagination(meta) {
        const container = document.getElementById('paginationContainer');
        container.classList.remove('hidden');
        document.getElementById('paginationInfo').textContent =
            `Showing ${meta.from || 0} to ${meta.to || 0} of ${meta.total} entries`;

        let btns = '';
        if (meta.current_page > 1)
            btns += `<button onclick="loadBookings(${meta.current_page - 1})" class="px-4 py-1.5 rounded-lg text-white text-[12px] transition-colors" style="background:#1e2d45;border:1px solid #2a3d5a;" onmouseenter="this.style.background='#2a3d5a'" onmouseleave="this.style.background='#1e2d45'">Prev</button>`;
        if (meta.current_page < meta.last_page)
            btns += `<button onclick="loadBookings(${meta.current_page + 1})" class="px-4 py-1.5 rounded-lg text-white text-[12px] transition-colors" style="background:#1e2d45;border:1px solid #2a3d5a;" onmouseenter="this.style.background='#2a3d5a'" onmouseleave="this.style.background='#1e2d45'">Next</button>`;
        document.getElementById('paginationButtons').innerHTML = btns;
    }

    // ── Actions ───────────────────────────────────────────
    window.approveBooking = async function(id) {
        const ok = await vsAlert.confirm('Approve Booking', 'Are you sure you want to approve this booking request?');
        if (!ok) return;
        try {
            const res  = await apiFetch(`/peminjaman/${id}/approve`, { method: 'POST' });
            const data = await res.json();
            if (res.ok) {
                await vsAlert.success('Approved!', 'Booking has been approved successfully.');
                loadBookings(currentPage);
            } else {
                vsAlert.error('Failed', data.message || 'Failed to approve booking.');
            }
        } catch(e) { vsAlert.error('Error', 'Could not connect to server.'); }
    };

    window.rejectBooking = async function(id) {
        const ok = await vsAlert.confirm('Reject Booking', 'Are you sure you want to reject this booking request?');
        if (!ok) return;
        const note = prompt('Reason for rejection (optional):');
        if (note === null) return;
        try {
            const res  = await apiFetch(`/peminjaman/${id}/reject`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ catatan_admin: note })
            });
            const data = await res.json();
            if (res.ok) {
                await vsAlert.success('Rejected', 'Booking has been rejected.');
                loadBookings(currentPage);
            } else {
                vsAlert.error('Failed', data.message || 'Failed to reject booking.');
            }
        } catch(e) { vsAlert.error('Error', 'Could not connect to server.'); }
    };

    // ── Init ──────────────────────────────────────────────
    searchInput.addEventListener('input', renderTable);
    statusFilter.addEventListener('change', renderTable);
    document.addEventListener('DOMContentLoaded', () => loadBookings());
})();
</script>
@endpush
