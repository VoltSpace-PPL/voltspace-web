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

        {{-- Approved --}}
        <div class="rounded-2xl p-6 flex items-center justify-between" style="background:#161e2d; border:1px solid #1e2d45;">
            <div>
                <p class="text-slate-400 text-[12px] font-medium mb-3">Approved</p>
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
            <table class="w-full text-left border-collapse min-w-[1000px]">
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

{{-- ═══════════════════════════════════════════════════════
     BOOKING DETAIL MODAL
     ════════════════════════════════════════════════════ --}}
<div id="booking-detail-modal" class="fixed inset-0 z-[200] hidden" role="dialog" aria-modal="true">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeBookingDetail()"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-[600px] p-4 max-h-[100dvh] overflow-y-auto overscroll-contain custom-scrollbar">
        <div class="glass-effect rounded-[24px] shadow-2xl overflow-hidden">
            {{-- Header --}}
            <div class="px-7 py-5 flex justify-between items-center border-b border-white/10">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-[#00d4aa]/15 border border-[#00d4aa]/25 flex items-center justify-center text-[#00d4aa]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    </div>
                    <div>
                        <h3 class="text-[18px] font-bold text-white leading-none">Booking Detail</h3>
                        <p class="text-[12px] text-slate-500 mt-0.5" id="detail-booking-id">—</p>
                    </div>
                </div>
                <button onclick="closeBookingDetail()" class="w-8 h-8 flex items-center justify-center rounded-full bg-white/5 text-slate-400 hover:text-white transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2.5"/></svg>
                </button>
            </div>

            {{-- Content --}}
            <div class="px-7 py-6 space-y-5">
                {{-- Status badge --}}
                <div class="flex items-center justify-between">
                    <span class="text-[12px] font-bold text-slate-400 uppercase tracking-wider">Status</span>
                    <span id="detail-status-badge" class="px-3 py-1 rounded-full text-[11px] font-bold">—</span>
                </div>

                <div class="h-px bg-white/5"></div>

                {{-- Student Info --}}
                <div>
                    <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-3">Student Information</p>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-white/[0.03] rounded-xl p-4 border border-white/5">
                            <p class="text-[11px] text-slate-500 mb-1">Name</p>
                            <p class="text-[14px] font-bold text-white" id="detail-student-name">—</p>
                        </div>
                        <div class="bg-white/[0.03] rounded-xl p-4 border border-white/5">
                            <p class="text-[11px] text-slate-500 mb-1">Email / NIM</p>
                            <p class="text-[13px] font-medium text-slate-300 truncate" id="detail-student-email">—</p>
                        </div>
                    </div>
                </div>

                {{-- Room Info --}}
                <div>
                    <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-3">Room & Schedule</p>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-white/[0.03] rounded-xl p-4 border border-white/5">
                            <p class="text-[11px] text-slate-500 mb-1">Room</p>
                            <p class="text-[14px] font-bold text-white" id="detail-room-name">—</p>
                            <p class="text-[11px] text-slate-500" id="detail-room-code">—</p>
                        </div>
                        <div class="bg-white/[0.03] rounded-xl p-4 border border-white/5">
                            <p class="text-[11px] text-slate-500 mb-1">Date</p>
                            <p class="text-[14px] font-bold text-white" id="detail-date">—</p>
                        </div>
                        <div class="bg-white/[0.03] rounded-xl p-4 border border-white/5">
                            <p class="text-[11px] text-slate-500 mb-1">Start Time</p>
                            <p class="text-[14px] font-bold text-[#00d4aa]" id="detail-start-time">—</p>
                        </div>
                        <div class="bg-white/[0.03] rounded-xl p-4 border border-white/5">
                            <p class="text-[11px] text-slate-500 mb-1">End Time</p>
                            <p class="text-[14px] font-bold text-[#00d4aa]" id="detail-end-time">—</p>
                        </div>
                    </div>
                </div>

                {{-- Purpose --}}
                <div>
                    <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">Purpose</p>
                    <div class="bg-white/[0.03] rounded-xl p-4 border border-white/5">
                        <p class="text-[13px] text-slate-300 leading-relaxed" id="detail-purpose">—</p>
                    </div>
                </div>

                {{-- Admin Note (if rejected or cancelled) --}}
                <div id="detail-rejection-section" class="hidden">
                    <p id="detail-rejection-title" class="text-[11px] font-bold text-red-500/70 uppercase tracking-wider mb-2">Admin Note</p>
                    <div class="bg-red-500/5 rounded-xl p-4 border border-red-500/20">
                        <p class="text-[13px] text-red-300 leading-relaxed" id="detail-rejection-reason">—</p>
                    </div>
                </div>

                {{-- Evidence --}}
                <div id="detail-evidence-section">
                    <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">Evidence / Surat Peminjaman</p>
                    <div class="flex items-center gap-3">
                        <button id="detail-preview-surat-btn"
                            onclick="openSuratPreview()"
                            class="flex items-center gap-2 px-4 py-2.5 bg-[#6366f1]/10 border border-[#6366f1]/30 text-[#818cf8] rounded-xl text-[13px] font-bold hover:bg-[#6366f1]/20 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            Preview Surat Peminjaman
                        </button>
                        <span id="detail-no-evidence" class="text-[13px] text-slate-500 italic hidden">No evidence uploaded</span>
                    </div>
                </div>

                {{-- Reviewed at --}}
                <div class="flex items-center justify-between text-[12px] text-slate-500 pt-2 border-t border-white/5">
                    <span>Submitted: <span id="detail-submitted-at" class="text-slate-400">—</span></span>
                    <span id="detail-reviewed-wrap" class="hidden">Reviewed: <span id="detail-reviewed-at" class="text-slate-400">—</span></span>
                </div>
            </div>

            {{-- Footer Actions --}}
            <div class="px-7 py-5 flex gap-3 border-t border-white/10" id="detail-action-buttons">
                <button onclick="closeBookingDetail()" class="flex-1 py-3 bg-white/5 border border-white/10 text-white font-bold rounded-xl hover:bg-white/10 transition-colors text-[14px]">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════
     SURAT PREVIEW MODAL
     ════════════════════════════════════════════════════ --}}
<div id="surat-preview-modal" class="fixed inset-0 z-[300] hidden" role="dialog" aria-modal="true">
    <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" onclick="closeSuratPreview()"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-[700px] p-4 max-h-[90dvh] overflow-y-auto overscroll-contain custom-scrollbar">
        <div class="glass-effect rounded-[24px] shadow-2xl overflow-hidden">
            <div class="px-7 py-5 flex justify-between items-center border-b border-white/10">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-[#6366f1]/15 border border-[#6366f1]/25 flex items-center justify-center text-[#818cf8]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-[18px] font-bold text-white leading-none">Surat Peminjaman</h3>
                        <p class="text-[12px] text-slate-500 mt-0.5" id="surat-booking-id-label">Preview Surat</p>
                    </div>
                </div>
                <button onclick="closeSuratPreview()" class="w-8 h-8 flex items-center justify-center rounded-full bg-white/5 text-slate-400 hover:text-white transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2.5"/></svg>
                </button>
            </div>

            <div class="px-7 py-6">
                {{-- Letter Preview Content --}}
                <div id="surat-content" class="bg-white rounded-xl p-4 text-gray-800 text-[13px] overflow-auto max-h-[60vh]">
                    <div id="excel-loading" class="flex flex-col items-center justify-center py-10 gap-3 hidden">
                        <div class="w-8 h-8 border-2 border-[#6366f1] border-t-transparent rounded-full animate-spin"></div>
                        <p class="text-slate-500">Loading document...</p>
                    </div>
                    <div id="excel-error" class="hidden text-center py-10 text-red-500">
                        <svg class="w-12 h-12 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p id="excel-error-msg">Failed to load document.</p>
                        <div id="fallback-download" class="mt-4 hidden">
                            <a id="fallback-download-btn" href="#" target="_blank" class="px-4 py-2 bg-[#6366f1] text-white rounded-xl text-[13px] font-bold hover:bg-[#4f46e5] transition-colors inline-block">Download Document Instead</a>
                        </div>
                    </div>
                    <div id="excel-table-container" class="w-full"></div>
                </div>
            </div>

            <div class="px-7 py-5 flex gap-3 border-t border-white/10">
                <button onclick="closeSuratPreview()" class="flex-1 py-3 bg-white/5 border border-white/10 text-white font-bold rounded-xl hover:bg-white/10 transition-colors text-[14px]">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════
     REJECT BOOKING MODAL – with reason form
     ════════════════════════════════════════════════════ --}}
<div id="reject-booking-modal" class="fixed inset-0 z-[200] hidden" role="dialog" aria-modal="true">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeRejectModal()"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-[480px] p-4">
        <div class="glass-effect rounded-[24px] shadow-2xl overflow-hidden">
            <div class="px-7 py-5 flex justify-between items-center border-b border-white/10">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-red-500/15 border border-red-500/25 flex items-center justify-center text-red-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-[18px] font-bold text-white leading-none">Reject Booking</h3>
                        <p class="text-[12px] text-slate-500 mt-0.5" id="reject-booking-id-label">—</p>
                    </div>
                </div>
                <button onclick="closeRejectModal()" class="w-8 h-8 flex items-center justify-center rounded-full bg-white/5 text-slate-400 hover:text-white transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2.5"/></svg>
                </button>
            </div>

            <div class="px-7 py-6 space-y-5">
                <div class="flex items-start gap-3 p-4 bg-red-500/5 border border-red-500/20 rounded-xl">
                    <svg class="w-5 h-5 text-red-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <p class="text-[13px] text-red-300 leading-relaxed">You are about to reject this booking request. The student will be notified with the reason you provide.</p>
                </div>

                <div>
                    <label class="block text-[13px] font-bold text-slate-300 uppercase tracking-wider mb-2">
                        Reason for Rejection <span class="text-red-400">*</span>
                    </label>
                    <textarea id="reject-reason-input" rows="4" placeholder="e.g., Room is reserved for faculty use, Time conflict with scheduled maintenance, Incomplete documentation..."
                        class="w-full rounded-xl px-4 py-3.5 text-[14px] text-white placeholder:text-slate-600 focus:outline-none focus:ring-2 focus:ring-red-500/50 transition-all resize-none border border-white/10 bg-white/5"></textarea>
                    <p class="text-[11px] text-slate-500 mt-1.5">Please provide a clear reason so the student understands why the request was rejected.</p>
                </div>
            </div>

            <div class="px-7 py-5 flex gap-3 border-t border-white/10">
                <button onclick="closeRejectModal()" class="flex-1 py-3 bg-white/5 border border-white/10 text-white font-bold rounded-xl hover:bg-white/10 transition-colors text-[14px]">Cancel</button>
                <button id="confirm-reject-btn" onclick="confirmReject()" class="flex-1 py-3 bg-red-500 hover:bg-red-600 text-white font-bold rounded-xl transition-colors text-[14px] flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    Reject Booking
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
(function() {
    let allData = [];
    let currentPage = 1;
    let currentDetailItem = null;
    let rejectBookingId = null;

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
            const time     = `${(item.waktu_mulai||'').substring(0,5)} - ${(item.waktu_selesai||'').substring(0,5)} WIB`;

            const statusMap = {
                pending:    { bg: 'rgba(234,179,8,0.1)',   color: '#eab308', border: 'rgba(234,179,8,0.2)',   label: 'Pending' },
                disetujui:  { bg: 'rgba(16,185,129,0.1)',  color: '#10b981', border: 'rgba(16,185,129,0.2)',  label: 'Approved' },
                ditolak:    { bg: 'rgba(239,68,68,0.1)',   color: '#ef4444', border: 'rgba(239,68,68,0.2)',   label: 'Rejected' },
                dibatalkan: { bg: 'rgba(100,116,139,0.1)', color: '#94a3b8', border: 'rgba(100,116,139,0.2)', label: 'Cancelled' },
            };
            const s = statusMap[item.status] || { bg: 'rgba(100,116,139,0.1)', color: '#94a3b8', border: 'rgba(100,116,139,0.2)', label: item.status };
            const badge = `<span class="px-3 py-1 rounded-full text-[11px] font-bold" style="background:${s.bg};color:${s.color};border:1px solid ${s.border};">${s.label}</span>`;

            // Action buttons
            let actions = `
                <div class="flex items-center gap-2">
                    <button onclick="openBookingDetail(${item.id})"
                        title="View Detail"
                        style="width:28px;height:28px;border-radius:50%;border:1px solid rgba(99,102,241,0.5);background:rgba(99,102,241,0.1);color:#818cf8;display:flex;align-items:center;justify-content:center;transition:all 0.2s;"
                        onmouseenter="this.style.background='rgba(99,102,241,0.2)'"
                        onmouseleave="this.style.background='rgba(99,102,241,0.1)'">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </button>`;

            if (item.status === 'pending') {
                actions += `
                    <button onclick="approveBooking(${item.id})"
                        title="Approve"
                        style="width:28px;height:28px;border-radius:50%;border:1px solid rgba(16,185,129,0.5);background:rgba(16,185,129,0.1);color:#10b981;display:flex;align-items:center;justify-content:center;transition:all 0.2s;"
                        onmouseenter="this.style.background='rgba(16,185,129,0.2)'"
                        onmouseleave="this.style.background='rgba(16,185,129,0.1)'">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </button>
                    <button onclick="openRejectModal(${item.id})"
                        title="Reject"
                        style="width:28px;height:28px;border-radius:50%;border:1px solid rgba(239,68,68,0.5);background:rgba(239,68,68,0.1);color:#ef4444;display:flex;align-items:center;justify-content:center;transition:all 0.2s;"
                        onmouseenter="this.style.background='rgba(239,68,68,0.2)'"
                        onmouseleave="this.style.background='rgba(239,68,68,0.1)'">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>`;
            }

            actions += `</div>`;

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

    // ── Approve ───────────────────────────────────────────
    window.approveBooking = async function(id) {
        const ok = await vsAlert.confirm('Approve Booking', 'Are you sure you want to approve this booking request?');
        if (!ok) return;
        try {
            const res  = await apiFetch(`/peminjaman/${id}/approve`, { method: 'POST' });
            const data = await res.json();
            if (res.ok) {
                await vsAlert.success('Approved!', 'Booking has been approved successfully.');
                loadBookings(currentPage);
                closeBookingDetail();
            } else {
                vsAlert.error('Failed', data.message || 'Failed to approve booking.');
            }
        } catch(e) { vsAlert.error('Error', 'Could not connect to server.'); }
    };

    // ── Reject Modal ──────────────────────────────────────
    window.openRejectModal = function(id) {
        rejectBookingId = id;
        const bkId = `BK${String(id).padStart(3, '0')}`;
        document.getElementById('reject-booking-id-label').textContent = bkId;
        document.getElementById('reject-reason-input').value = '';
        document.getElementById('reject-booking-modal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        setTimeout(() => document.getElementById('reject-reason-input').focus(), 100);
    };

    window.closeRejectModal = function() {
        document.getElementById('reject-booking-modal').classList.add('hidden');
        document.body.style.overflow = 'auto';
        rejectBookingId = null;
    };

    window.confirmReject = async function() {
        if (!rejectBookingId) return;
        const note = document.getElementById('reject-reason-input').value.trim();
        if (!note) {
            vsAlert.warning('Reason Required', 'Please provide a reason for rejection.');
            return;
        }

        const btn = document.getElementById('confirm-reject-btn');
        const orig = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-2"></div>Rejecting...';

        try {
            const res = await apiFetch(`/peminjaman/${rejectBookingId}/reject`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ catatan_admin: note })
            });
            const data = await res.json();
            if (res.ok) {
                closeRejectModal();
                await vsAlert.success('Rejected', 'Booking has been rejected.');
                loadBookings(currentPage);
                closeBookingDetail();
            } else {
                vsAlert.error('Failed', data.message || 'Failed to reject booking.');
            }
        } catch(e) {
            vsAlert.error('Error', 'Could not connect to server.');
        } finally {
            btn.disabled = false;
            btn.innerHTML = orig;
        }
    };

    // ── Booking Detail Modal ───────────────────────────────
    window.openBookingDetail = function(id) {
        const item = allData.find(d => d.id === id);
        if (!item) return;
        currentDetailItem = item;

        const bkId = `BK${String(item.id).padStart(3, '0')}`;
        const statusMap = {
            pending:    { bg: 'rgba(234,179,8,0.1)',   color: '#eab308', border: 'rgba(234,179,8,0.2)',   label: 'Pending' },
            disetujui:  { bg: 'rgba(16,185,129,0.1)',  color: '#10b981', border: 'rgba(16,185,129,0.2)',  label: 'Approved' },
            ditolak:    { bg: 'rgba(239,68,68,0.1)',   color: '#ef4444', border: 'rgba(239,68,68,0.2)',   label: 'Rejected' },
            dibatalkan: { bg: 'rgba(100,116,139,0.1)', color: '#94a3b8', border: 'rgba(100,116,139,0.2)', label: 'Cancelled' },
        };
        const s = statusMap[item.status] || { bg: 'rgba(100,116,139,0.1)', color: '#94a3b8', border: 'rgba(100,116,139,0.2)', label: item.status };

        document.getElementById('detail-booking-id').textContent = bkId;
        document.getElementById('detail-status-badge').innerHTML = item.status;
        document.getElementById('detail-status-badge').setAttribute('style', `background:${s.bg};color:${s.color};border:1px solid ${s.border};padding:3px 12px;border-radius:9999px;font-size:11px;font-weight:700;`);

        document.getElementById('detail-student-name').textContent = item.user?.name || '—';
        document.getElementById('detail-student-email').textContent = item.user?.email || '—';
        document.getElementById('detail-room-name').textContent = item.ruangan?.nama_ruangan || '—';
        document.getElementById('detail-room-code').textContent = item.ruangan?.kode || '';
        document.getElementById('detail-date').textContent = item.tanggal_mulai ? item.tanggal_mulai.substring(0,10) : '—';
        const formatTimeWib = (t) => t ? t.substring(0,5) + ' WIB' : '—';
        document.getElementById('detail-start-time').textContent = formatTimeWib(item.waktu_mulai);
        document.getElementById('detail-end-time').textContent   = formatTimeWib(item.waktu_selesai);
        document.getElementById('detail-purpose').textContent = item.tujuan || '—';

        // Rejection / Cancellation reason
        if ((item.status === 'ditolak' || item.status === 'dibatalkan') && item.catatan_admin) {
            document.getElementById('detail-rejection-section').classList.remove('hidden');
            document.getElementById('detail-rejection-title').textContent = item.status === 'ditolak' ? 'Rejection Reason' : 'Cancellation Reason';
            document.getElementById('detail-rejection-reason').textContent = item.catatan_admin;
        } else {
            document.getElementById('detail-rejection-section').classList.add('hidden');
        }

        // Evidence
        if (item.surat_peminjaman) {
            document.getElementById('detail-preview-surat-btn').classList.remove('hidden');
            document.getElementById('detail-no-evidence').classList.add('hidden');
        } else {
            document.getElementById('detail-preview-surat-btn').classList.add('hidden');
            document.getElementById('detail-no-evidence').classList.remove('hidden');
        }

        // Timestamps
        document.getElementById('detail-submitted-at').textContent = item.created_at ? new Date(item.created_at).toLocaleDateString('id-ID', {day:'numeric',month:'long',year:'numeric',hour:'2-digit',minute:'2-digit'}) : '—';
        if (item.reviewed_at) {
            document.getElementById('detail-reviewed-wrap').classList.remove('hidden');
            document.getElementById('detail-reviewed-at').textContent = new Date(item.reviewed_at).toLocaleDateString('id-ID', {day:'numeric',month:'long',year:'numeric',hour:'2-digit',minute:'2-digit'});
        } else {
            document.getElementById('detail-reviewed-wrap').classList.add('hidden');
        }

        // Footer action buttons
        const actionDiv = document.getElementById('detail-action-buttons');
        let btnsHtml = `<button onclick="closeBookingDetail()" class="flex-1 py-3 bg-white/5 border border-white/10 text-white font-bold rounded-xl hover:bg-white/10 transition-colors text-[14px]">Close</button>`;
        if (item.status === 'pending') {
            btnsHtml += `
                <button onclick="approveBooking(${item.id})" class="flex-1 py-3 bg-emerald-500 hover:bg-emerald-600 text-white font-bold rounded-xl transition-colors text-[14px] flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Approve
                </button>
                <button onclick="openRejectModal(${item.id})" class="flex-1 py-3 bg-red-500 hover:bg-red-600 text-white font-bold rounded-xl transition-colors text-[14px] flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg> Reject
                </button>`;
        }
        actionDiv.innerHTML = btnsHtml;

        document.getElementById('booking-detail-modal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    };

    window.closeBookingDetail = function() {
        document.getElementById('booking-detail-modal').classList.add('hidden');
        document.body.style.overflow = 'auto';
        currentDetailItem = null;
    };

    // ── Surat Preview ─────────────────────────────────────
    window.openSuratPreview = async function() {
        if (!currentDetailItem || !currentDetailItem.surat_peminjaman) return;
        const item = currentDetailItem;
        const bkId = `BK${String(item.id).padStart(3, '0')}`;

        document.getElementById('surat-booking-id-label').textContent = bkId;
        
        const container = document.getElementById('excel-table-container');
        const loading = document.getElementById('excel-loading');
        const errorDiv = document.getElementById('excel-error');
        const errorMsg = document.getElementById('excel-error-msg');
        const fallbackDownload = document.getElementById('fallback-download');
        const fallbackBtn = document.getElementById('fallback-download-btn');
        
        container.innerHTML = '';
        errorDiv.classList.add('hidden');
        fallbackDownload.classList.add('hidden');
        loading.classList.remove('hidden');
        
        document.getElementById('surat-preview-modal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';

        const token = localStorage.getItem('token');
        const url = window.VoltSpaceApi ? window.VoltSpaceApi.getBase() + '/peminjaman/' + item.id + '/surat' : '/api/peminjaman/' + item.id + '/surat';

        try {
            const res = await fetch(url, {
                headers: { 'Authorization': 'Bearer ' + token }
            });
            if (!res.ok) {
                const errText = await res.text();
                throw new Error(`Server returned ${res.status}: ${errText}`);
            }
            const data = await res.arrayBuffer();
            const fileExt = item.surat_peminjaman.split('.').pop().toLowerCase();
            
            if (['xlsx', 'xls', 'csv'].includes(fileExt)) {
                if (typeof XLSX === 'undefined') throw new Error('Excel library failed to load.');
                const workbook = XLSX.read(data, {type: 'array'});
                const firstSheetName = workbook.SheetNames[0];
                const worksheet = workbook.Sheets[firstSheetName];
                
                const htmlTable = XLSX.utils.sheet_to_html(worksheet, { id: 'excel-data-table', editable: false });
                
                loading.classList.add('hidden');
                container.innerHTML = htmlTable;
                
                // Add some styling to the generated table
                const table = container.querySelector('table');
                if (table) {
                    table.classList.add('w-full', 'border-collapse', 'border', 'border-slate-300');
                    table.querySelectorAll('td, th').forEach(cell => {
                        cell.classList.add('border', 'border-slate-300', 'p-2');
                    });
                }
            } else if (fileExt === 'pdf') {
                const blob = new Blob([data], { type: 'application/pdf' });
                const objectUrl = URL.createObjectURL(blob);
                loading.classList.add('hidden');
                container.innerHTML = `<iframe src="${objectUrl}" width="100%" height="600px" style="border:none;"></iframe>`;
            } else if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(fileExt)) {
                const blob = new Blob([data], { type: 'image/' + (fileExt === 'jpg' ? 'jpeg' : fileExt) });
                const objectUrl = URL.createObjectURL(blob);
                loading.classList.add('hidden');
                container.innerHTML = `<img src="${objectUrl}" class="max-w-full h-auto mx-auto rounded-xl">`;
            } else {
                throw new Error('Preview for this file type is not supported in the browser.');
            }
        } catch (e) {
            console.error('Error loading document:', e);
            loading.classList.add('hidden');
            errorDiv.classList.remove('hidden');
            errorMsg.textContent = 'Failed to load document: ' + (e.message || 'Unknown error');
            
            // Allow them to download it instead
            fallbackDownload.classList.remove('hidden');
            fallbackBtn.href = url + '?token=' + token; // Just a fallback, better to use blob if we have it
            
            // If we failed after fetching data (e.g. XLSX parse error or unsupported type), we can download from blob
            if (e.message.includes('Preview for this file type') || e.message.includes('Excel')) {
                fetch(url, { headers: { 'Authorization': 'Bearer ' + token } })
                    .then(r => r.blob())
                    .then(blob => {
                        fallbackBtn.href = URL.createObjectURL(blob);
                        fallbackBtn.download = item.surat_peminjaman.split('/').pop() || 'document';
                    });
            }
        }
    };

    window.closeSuratPreview = function() {
        document.getElementById('surat-preview-modal').classList.add('hidden');
        document.body.style.overflow = 'hidden'; // keep detail modal scroll locked
    };

    // ── Init ──────────────────────────────────────────────
    searchInput.addEventListener('input', renderTable);
    statusFilter.addEventListener('change', renderTable);
    document.addEventListener('DOMContentLoaded', () => loadBookings());
})();
</script>
@endpush
