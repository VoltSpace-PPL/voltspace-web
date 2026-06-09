@extends('layouts.main')

@section('content')

{{-- Page Header --}}
<div class="mb-8">
    <nav class="flex items-center gap-2 text-[13px] text-slate-500 mb-4">
        <a href="/dashboard" class="hover:text-white transition-colors">Dashboard</a>
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="2"/></svg>
        <span class="text-white font-medium">Reports</span>
    </nav>
    <h1 class="text-[28px] font-extrabold text-white tracking-tight leading-none">Reports</h1>
    <p class="text-slate-400 text-[14px] mt-1.5">Analytics, insights, and energy reports</p>
</div>

{{-- ─── Generate Electricity Report Card ─────────────────────────────────────── --}}
<div class="rounded-[20px] mb-8 glass-effect overflow-hidden">
    <div class="px-6 pt-6 pb-5 flex items-center gap-4 border-b border-white/5">
        <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
             style="background:linear-gradient(135deg,#d946ef,#ec4899); box-shadow:0 4px 20px rgba(236,72,153,0.3);">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke-width="2"/></svg>
        </div>
        <div>
            <h2 class="text-[18px] font-bold text-white">Generate Electricity Report</h2>
            <p class="text-slate-400 text-[13px] mt-0.5">Create detailed energy consumption reports</p>
        </div>
    </div>

    <div class="p-6">
        {{-- Report Type Toggle --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-6">
            {{-- Monthly --}}
            <label id="type-monthly-label" for="type-monthly"
                   class="flex items-start gap-4 p-5 rounded-2xl cursor-pointer transition-all border border-white/10 bg-[#1e293b]/60 shadow-[0_0_15px_rgba(59,130,246,0.05)]">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 mt-0.5"
                     style="background:linear-gradient(135deg,#3b82f6,#2563eb); box-shadow:0 4px 15px rgba(59,130,246,0.3);">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-[15px] font-bold text-white">Monthly Report</span>
                        <div id="monthly-check" class="w-5 h-5 rounded-full flex items-center justify-center flex-shrink-0 bg-[#3b82f6]">
                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M5 13l4 4L19 7"/></svg>
                        </div>
                    </div>
                    <p class="text-slate-400 text-[12px] mt-0.5">Single month analysis</p>
                    {{-- Month picker --}}
                    <div id="monthly-picker" class="mt-4">
                        <p class="text-slate-400 text-[12px] font-medium mb-2">Select Month</p>
                        <input type="month" id="input-month"
                               class="w-full px-4 py-2.5 rounded-xl text-[13px] text-white outline-none transition-colors border border-white/10 bg-[#0f172a]/80 focus:border-[#3b82f6] focus:bg-[#0f172a]"
                               style="color-scheme:dark;">
                    </div>
                </div>
                <input type="radio" id="type-monthly" name="report-type" value="bulanan" class="sr-only" checked>
            </label>

            {{-- Yearly --}}
            <label id="type-yearly-label" for="type-yearly"
                   class="flex items-start gap-4 p-5 rounded-2xl cursor-pointer transition-all border border-white/5 bg-[#1e293b]/30 hover:bg-[#1e293b]/40">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 mt-0.5 bg-[#334155]">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" stroke-width="2"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-[15px] font-bold text-white">Yearly Report</span>
                        <div id="yearly-check" class="w-5 h-5 rounded-full flex-shrink-0 border-2 border-[#475569]"></div>
                    </div>
                    <p class="text-slate-400 text-[12px] mt-0.5">Full year analysis</p>
                    {{-- Year picker --}}
                    <div id="yearly-picker" class="mt-4 hidden">
                        <p class="text-slate-400 text-[12px] font-medium mb-2">Select Year</p>
                        <select id="input-year"
                                class="w-full px-4 py-2.5 rounded-xl text-[13px] text-white outline-none transition-colors border border-white/10 bg-[#0f172a]/80 focus:border-[#3b82f6] focus:bg-[#0f172a]">
                            @for ($y = date('Y'); $y >= 2023; $y--)
                                <option value="{{ $y }}" {{ $y == date('Y') ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
                <input type="radio" id="type-yearly" name="report-type" value="tahunan" class="sr-only">
            </label>
        </div>

        {{-- Generate Button --}}
        <button id="generate-btn" onclick="generateReport()"
            class="w-full flex items-center justify-center gap-2.5 py-3.5 rounded-xl font-bold text-[15px] transition-all hover:opacity-90 active:scale-[0.99] text-white"
            style="background:linear-gradient(135deg,#00d4aa,#00bfa0); box-shadow:0 4px 15px rgba(0,212,170,0.25);">
            <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke-width="2"/></svg>
            <span id="generate-label">Generate Report</span>
        </button>
    </div>
</div>

{{-- ─── Generated Reports List ──────────────────────────────────────────────── --}}
<div class="mb-10">
    <div class="flex items-center justify-between gap-4 mb-4">
        <div class="flex items-center gap-3">
            <h2 class="text-[18px] font-bold text-white">Generated Reports</h2>
            <span id="reports-count-badge" class="hidden px-3 py-1 rounded-full text-[12px] font-bold text-[#00d4aa] bg-[#00d4aa]/10 border border-[#00d4aa]/20">0 Reports</span>
        </div>
        <button id="toggle-hidden-btn" onclick="toggleShowHidden()" class="hidden flex items-center gap-2 px-4 py-2 rounded-xl text-[13px] font-bold transition-all border"
            style="background:rgba(148,163,184,0.08); border-color:rgba(148,163,184,0.2); color:#94a3b8;">
            <svg id="toggle-hidden-icon-eye" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
            </svg>
            <svg id="toggle-hidden-icon-hide" class="w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
            <span id="toggle-hidden-label">See Hidden Reports</span>
        </button>
    </div>

    {{-- Main Visible Reports --}}
    <div class="rounded-[20px] glass-effect overflow-hidden">
        {{-- Loading --}}
        <div id="list-loading" class="flex flex-col items-center justify-center py-16 gap-3">
            <div class="w-7 h-7 border-2 border-[#00d4aa] border-t-transparent rounded-full animate-spin"></div>
            <span class="text-slate-400 text-[13px]">Loading reports...</span>
        </div>

        {{-- List --}}
        <div id="list-container" class="hidden divide-y divide-white/5"></div>

        {{-- Empty --}}
        <div id="list-empty" class="hidden flex flex-col items-center justify-center py-16 gap-4">
            <div class="w-14 h-14 rounded-2xl flex items-center justify-center bg-[#00d4aa]/5 border border-[#00d4aa]/10">
                <svg class="w-7 h-7 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke-width="2"/></svg>
            </div>
            <div class="text-center">
                <p class="text-white font-bold text-[15px]">No Reports Yet</p>
                <p class="text-slate-400 text-[13px] mt-1">Generate your first report above.</p>
            </div>
        </div>
    </div>

    {{-- Hidden Reports Section --}}
    <div id="hidden-reports-section" class="hidden mt-4">
        <div class="flex items-center gap-3 mb-3 px-1">
            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
            </svg>
            <p class="text-[13px] font-bold text-slate-500 uppercase tracking-wider">Hidden Reports</p>
        </div>
        <div class="rounded-[20px] border border-dashed border-white/10 overflow-hidden">
            <div id="hidden-list-container" class="divide-y divide-white/5"></div>
        </div>
    </div>
</div>

{{-- ─── Key Insights ───────────────────────────────────────────────────────── --}}
<div class="mb-8">
    <h2 class="text-[18px] font-bold text-white mb-4">Key Insights</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        {{-- Energy Savings --}}
        <div class="rounded-2xl p-5 border border-emerald-500/20 bg-emerald-500/10 transition-all hover:bg-emerald-500/15 cursor-default">
            <div class="flex items-center gap-2 mb-2">
                <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <span class="font-bold text-[14px] text-emerald-400">Energy Savings</span>
            </div>
            <p class="text-slate-300 text-[13px] leading-relaxed">Campus achieved 15.7% energy savings compared to last year through efficiency improvements.</p>
        </div>
        
        {{-- Peak Reduction --}}
        <div class="rounded-2xl p-5 border border-blue-500/20 bg-blue-500/10 transition-all hover:bg-blue-500/15 cursor-default">
            <div class="flex items-center gap-2 mb-2">
                <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <span class="font-bold text-[14px] text-blue-400">Peak Reduction</span>
            </div>
            <p class="text-slate-300 text-[13px] leading-relaxed">Peak demand reduced by 12% through load management strategies.</p>
        </div>
    </div>
</div>

{{-- ─── Preview Modal ──────────────────────────────────────────────────────── --}}
<div id="preview-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center">
    <!-- Backdrop -->
    <div class="absolute inset-0 bg-slate-900/80 backdrop-blur-sm" onclick="closePreviewModal()"></div>
    
    <!-- Modal Content -->
    <div class="relative w-full max-w-2xl mx-4 glass-effect rounded-2xl shadow-2xl border border-white/10 flex flex-col max-h-[85vh] transform transition-all scale-95 opacity-0 duration-200" id="preview-modal-content">
        
        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-5 border-b border-white/5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-blue-500/20 border border-blue-500/30 text-blue-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke-width="2"/></svg>
                </div>
                <div>
                    <h3 id="preview-title" class="text-[17px] font-bold text-white">Report Preview</h3>
                    <p id="preview-date" class="text-slate-400 text-[13px] mt-0.5">Loading data...</p>
                </div>
            </div>
            <button onclick="closePreviewModal()" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-white hover:bg-white/10 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- Body -->
        <div class="p-6 overflow-y-auto custom-scrollbar flex-1">
            <!-- Loading State -->
            <div id="preview-loading" class="flex flex-col items-center justify-center py-12">
                <div class="w-8 h-8 border-2 border-blue-500 border-t-transparent rounded-full animate-spin mb-4"></div>
                <p class="text-slate-400 text-[13px]">Fetching report details...</p>
            </div>

            <!-- Data State -->
            <div id="preview-data" class="hidden space-y-6">
                <!-- Summary Cards -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="rounded-2xl border border-white/5 bg-[#1e293b]/40 p-4">
                        <div class="flex items-center gap-2 text-slate-400 mb-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z" stroke-width="2"/></svg>
                            <span class="text-[12px] uppercase tracking-wider font-semibold">Total Konsumsi</span>
                        </div>
                        <div class="flex items-baseline gap-1 mt-1">
                            <span id="preview-kwh" class="text-[24px] font-bold text-[#00d4aa]">0</span>
                            <span class="text-slate-400 text-[14px]">kWh</span>
                        </div>
                    </div>
                    
                    <div class="rounded-2xl border border-white/5 bg-[#1e293b]/40 p-4">
                        <div class="flex items-center gap-2 text-slate-400 mb-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" stroke-width="2"/></svg>
                            <span class="text-[12px] uppercase tracking-wider font-semibold">Jumlah Ruangan</span>
                        </div>
                        <div class="flex items-baseline gap-1 mt-1">
                            <span id="preview-rooms-count" class="text-[24px] font-bold text-white">0</span>
                            <span class="text-slate-400 text-[14px]">Ruangan</span>
                        </div>
                    </div>
                </div>

                <!-- Rooms Table -->
                <div>
                    <h4 class="text-[14px] font-bold text-white mb-3 px-1">Rincian Ruangan</h4>
                    <div class="rounded-2xl border border-white/5 bg-[#1e293b]/30 overflow-hidden">
                        <table class="w-full text-left text-[13px]">
                            <thead class="bg-[#1e293b]/50 border-b border-white/5">
                                <tr>
                                    <th class="px-5 py-3 text-slate-400 font-semibold">Ruangan</th>
                                    <th class="px-5 py-3 text-slate-400 font-semibold text-right">Konsumsi Energi</th>
                                </tr>
                            </thead>
                            <tbody id="preview-rooms-list" class="divide-y divide-white/5">
                                <!-- Filled by JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="px-6 py-5 border-t border-white/5 bg-[#0f172a]/50 rounded-b-2xl flex justify-end">
            <button onclick="closePreviewModal()" class="px-6 py-2.5 rounded-xl text-[13px] font-bold text-white bg-slate-700 hover:bg-slate-600 transition-colors">
                Tutup
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
(function () {
    /* ── Helpers ──────────────────────────────────────────────────────────── */
    function fmt(n, dec = 1) { return parseFloat(n || 0).toLocaleString('en-US', { minimumFractionDigits: dec, maximumFractionDigits: dec }); }
    function fmtDate(iso) {
        if (!iso) return '—';
        return new Date(iso).toLocaleString('en-US', { month: 'numeric', day: 'numeric', year: 'numeric', hour: 'numeric', minute: '2-digit', hour12: true });
    }

    /* ── Radio toggle UI ─────────────────────────────────────────────────── */
    function setActiveType(type) {
        const isMonthly = type === 'bulanan';

        const monthlyLbl = document.getElementById('type-monthly-label');
        const monthlyChk = document.getElementById('monthly-check');
        const yearlyLbl = document.getElementById('type-yearly-label');
        const yearlyChk = document.getElementById('yearly-check');

        const activeLabelClass = 'flex items-start gap-4 p-5 rounded-2xl cursor-pointer transition-all border border-[#3b82f6]/40 bg-[#1e293b] shadow-lg';
        const inactiveLabelClass = 'flex items-start gap-4 p-5 rounded-2xl cursor-pointer transition-all border border-white/5 bg-[#161e2d] hover:bg-[#1e293b]/60';

        if (isMonthly) {
            monthlyLbl.className = activeLabelClass;
            monthlyChk.className = 'w-5 h-5 rounded-full flex items-center justify-center flex-shrink-0 bg-[#3b82f6]';
            monthlyChk.innerHTML = '<svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M5 13l4 4L19 7"/></svg>';
            
            yearlyLbl.className = inactiveLabelClass;
            yearlyChk.className = 'w-5 h-5 rounded-full flex-shrink-0 border border-slate-600 bg-transparent';
            yearlyChk.innerHTML = '';
        } else {
            yearlyLbl.className = activeLabelClass;
            yearlyChk.className = 'w-5 h-5 rounded-full flex items-center justify-center flex-shrink-0 bg-[#3b82f6]';
            yearlyChk.innerHTML = '<svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M5 13l4 4L19 7"/></svg>';
            
            monthlyLbl.className = inactiveLabelClass;
            monthlyChk.className = 'w-5 h-5 rounded-full flex-shrink-0 border border-slate-600 bg-transparent';
            monthlyChk.innerHTML = '';
        }

        document.getElementById('monthly-picker').classList.toggle('hidden', !isMonthly);
        document.getElementById('yearly-picker').classList.toggle('hidden', isMonthly);
    }

    document.getElementById('type-monthly').addEventListener('change', () => setActiveType('bulanan'));
    document.getElementById('type-yearly').addEventListener('change',  () => setActiveType('tahunan'));

    // Set default month input to current month
    const now = new Date();
    document.getElementById('input-month').value =
        `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}`;

    /* ── Generate Report ─────────────────────────────────────────────────── */
    window.generateReport = async function () {
        const type   = document.querySelector('input[name="report-type"]:checked').value;
        const btn    = document.getElementById('generate-btn');
        const label  = document.getElementById('generate-label');

        let bulan = null, tahun = null;

        if (type === 'bulanan') {
            const val = document.getElementById('input-month').value; 
            if (!val) { vsAlert.warning('Pilih Bulan', 'Silakan pilih bulan terlebih dahulu.'); return; }
            [tahun, bulan] = val.split('-').map(Number);
        } else {
            tahun = parseInt(document.getElementById('input-year').value);
        }

        btn.disabled = true;
        label.textContent = 'Generating...';
        btn.style.opacity = '0.7';

        try {
            const payload = { jenis_periode: type, tahun };
            if (type === 'bulanan') payload.bulan = bulan;

            const res  = await apiFetch('/laporan-energi/generate', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload),
            });
            const data = await res.json();

            if (!res.ok) {
                vsAlert.error('Gagal Generate', data.message || 'Terjadi kesalahan saat generate laporan.');
                return;
            }

            vsAlert.success('Laporan Berhasil Dibuat', `"${data.data?.title}" berhasil digenerate.`);
            loadReportList();
        } catch (e) {
            vsAlert.error('Koneksi Error', 'Tidak dapat terhubung ke server.');
        } finally {
            btn.disabled = false;
            label.textContent = 'Generate Report';
            btn.style.opacity = '1';
        }
    };

    /* ── Download ────────────────────────────────────────────────────────── */
    window.downloadReport = async function (id, title) {
        try {
            const token = localStorage.getItem('token');
            const res   = await fetch(`/api/laporan-energi/${id}/download`, {
                headers: { 'Authorization': `Bearer ${token}`, 'Accept': 'application/json' }
            });

            if (!res.ok) {
                const d = await res.json().catch(() => ({}));
                vsAlert.error('Download Gagal', d.message || 'File tidak tersedia.');
                return;
            }

            const blob = await res.blob();
            const url  = URL.createObjectURL(blob);
            const a    = document.createElement('a');
            a.href     = url;
            a.download = title.replace(/[^a-z0-9_\-]/gi, '_') + '.xlsx';
            a.click();
            URL.revokeObjectURL(url);
        } catch (e) {
            vsAlert.error('Download Error', 'Terjadi kesalahan saat mengunduh laporan.');
        }
    };

    /* ── Toggle Hide ─────────────────────────────────────────────────────── */
    window.toggleHideReport = async function (id, title, isHidden) {
        const actionText = isHidden ? 'menampilkan kembali' : 'menyembunyikan';
        const ok = await vsAlert.confirm(
            isHidden ? 'Tampilkan Laporan' : 'Sembunyikan Laporan',
            `Yakin ingin ${actionText} laporan "<strong>${title}</strong>"?`,
            'Ya', 'Batal'
        );
        if (!ok) return;

        try {
            const res = await apiFetch(`/laporan-energi/${id}/toggle-hide`, { method: 'POST' });
            if (res.ok) {
                const data = await res.json();
                vsAlert.success('Berhasil', data.message || `Laporan berhasil ${isHidden ? 'ditampilkan' : 'disembunyikan'}.`);
                loadReportList();
            } else {
                const d = await res.json();
                vsAlert.error('Gagal', d.message || 'Terjadi kesalahan.');
            }
        } catch (e) {
            vsAlert.error('Koneksi Error', 'Tidak dapat terhubung ke server.');
        }
    };

    /* ── Render list item ────────────────────────────────────────────────── */
    function renderItem(r) {
        const isYearly = r.jenis_periode === 'tahunan';
        const iconGrad = isYearly
            ? 'linear-gradient(135deg,#c026d3,#db2777)'
            : 'linear-gradient(135deg,#3b82f6,#2563eb)';
        
        const consumption = r.total_kwh_ringkasan || 0; 
        const totalRooms = r.jumlah_ruangan || 0;
        const isHidden = r.is_hidden;
        
        return `
<div class="flex flex-col xl:flex-row xl:items-center justify-between gap-4 px-6 py-5 hover:bg-white/5 transition-colors">
    <div class="flex items-center gap-4">
        <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
             style="background:${iconGrad};">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2"/></svg>
        </div>
        <div>
            <p class="text-[14px] font-bold text-white">${r.title || r.periode_label || '—'}</p>
            <p class="text-[12px] text-slate-400 mt-0.5">Generated on ${fmtDate(r.created_at)}</p>
        </div>
    </div>
    
    <div class="flex flex-wrap items-center gap-x-8 gap-y-4">
        <div class="text-right">
            <p class="text-[11px] text-slate-400 uppercase tracking-wider">Rooms Analyzed</p>
            <p class="text-[14px] font-bold text-white mt-0.5">${totalRooms} Ruangan</p>
        </div>
        <div class="text-right">
            <p class="text-[11px] text-slate-400 uppercase tracking-wider">Total Consumption</p>
            <p class="text-[14px] font-bold text-[#00d4aa] mt-0.5">${fmt(consumption, 1)} kWh</p>
        </div>
        
        <div class="flex items-center gap-2 ml-2">
            <button onclick="previewReport(${r.id})"
                class="flex items-center gap-1.5 px-4 py-2 rounded-xl text-[13px] font-bold text-white transition-all hover:opacity-90 active:scale-[0.97]"
                style="background:rgba(59,130,246,0.15); border:1px solid rgba(59,130,246,0.3); color:#60a5fa;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                Preview
            </button>
            <button onclick="downloadReport(${r.id}, '${(r.title||'').replace(/'/g,"\\'")}' )"
                class="flex items-center gap-1.5 px-4 py-2 rounded-xl text-[13px] font-bold text-white transition-all hover:opacity-90 active:scale-[0.97]"
                style="background:linear-gradient(135deg,#00d4aa,#00bfa0);">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Download
            </button>
            <button onclick="toggleHideReport(${r.id}, '${(r.title||'').replace(/'/g,"\\'")}', ${isHidden ? 'true' : 'false'})"
                class="w-9 h-9 rounded-xl flex items-center justify-center transition-all text-slate-400 hover:text-white"
                style="background:rgba(255,255,255,0.05);"
                title="${isHidden ? 'Tampilkan laporan' : 'Sembunyikan laporan'}">
                ${isHidden
                    ? '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>'
                    : '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>'
                }
            </button>
        </div>
    </div>
</div>`;
    }

    /* ── Show/Hide Toggle ────────────────────────────────────────────────── */
    let _showHidden = false;
    window.toggleShowHidden = function() {
        _showHidden = !_showHidden;
        const btn = document.getElementById('toggle-hidden-btn');
        const label = document.getElementById('toggle-hidden-label');
        const iconEye = document.getElementById('toggle-hidden-icon-eye');
        const iconHide = document.getElementById('toggle-hidden-icon-hide');
        const hiddenSection = document.getElementById('hidden-reports-section');

        if (_showHidden) {
            label.textContent = 'Hide Hidden Reports';
            btn.style.background = 'rgba(99,102,241,0.1)';
            btn.style.borderColor = 'rgba(99,102,241,0.3)';
            btn.style.color = '#818cf8';
            iconEye.classList.add('hidden');
            iconHide.classList.remove('hidden');
            hiddenSection.classList.remove('hidden');
        } else {
            label.textContent = 'See Hidden Reports';
            btn.style.background = 'rgba(148,163,184,0.08)';
            btn.style.borderColor = 'rgba(148,163,184,0.2)';
            btn.style.color = '#94a3b8';
            iconEye.classList.remove('hidden');
            iconHide.classList.add('hidden');
            hiddenSection.classList.add('hidden');
        }
    };

    /* ── Load list ───────────────────────────────────────────────────────── */
    async function loadReportList() {
        const loading   = document.getElementById('list-loading');
        const container = document.getElementById('list-container');
        const empty     = document.getElementById('list-empty');
        const badge     = document.getElementById('reports-count-badge');
        const toggleBtn = document.getElementById('toggle-hidden-btn');
        const hiddenContainer = document.getElementById('hidden-list-container');
        const hiddenSection = document.getElementById('hidden-reports-section');

        loading.classList.remove('hidden');
        container.classList.add('hidden');
        empty.classList.add('hidden');

        try {
            const res  = await apiFetch('/laporan-energi?per_page=50');
            const data = await res.json();

            const allItems = data.data || [];
            const visibleItems = allItems.filter(r => !r.is_hidden);
            const hiddenItems  = allItems.filter(r => r.is_hidden);
            loading.classList.add('hidden');

            // Update toggle button
            if (hiddenItems.length > 0) {
                toggleBtn.classList.remove('hidden');
                hiddenContainer.innerHTML = hiddenItems.map(renderItem).join('');
            } else {
                toggleBtn.classList.add('hidden');
                hiddenSection.classList.add('hidden');
                _showHidden = false;
            }

            if (!visibleItems.length) {
                empty.classList.remove('hidden');
                badge.classList.add('hidden');
                return;
            }

            badge.textContent = `${visibleItems.length} Report${visibleItems.length > 1 ? 's' : ''}`;
            badge.classList.remove('hidden');

            container.innerHTML = visibleItems.map(renderItem).join('');
            container.classList.remove('hidden');
        } catch (e) {
            loading.classList.add('hidden');
            empty.classList.remove('hidden');
        }
    }

    /* ── Preview Report ──────────────────────────────────────────────────── */
    window.previewReport = async function(id) {
        const modal = document.getElementById('preview-modal');
        const modalContent = document.getElementById('preview-modal-content');
        const loading = document.getElementById('preview-loading');
        const dataContainer = document.getElementById('preview-data');
        const titleEl = document.getElementById('preview-title');
        const dateEl = document.getElementById('preview-date');
        const kwhEl = document.getElementById('preview-kwh');
        const roomsCountEl = document.getElementById('preview-rooms-count');
        const roomsListEl = document.getElementById('preview-rooms-list');

        // Show modal & loading state
        modal.classList.remove('hidden');
        // Small delay for transition
        setTimeout(() => {
            modalContent.classList.remove('scale-95', 'opacity-0');
            modalContent.classList.add('scale-100', 'opacity-100');
        }, 10);

        loading.classList.remove('hidden');
        dataContainer.classList.add('hidden');
        titleEl.textContent = 'Report Preview';
        dateEl.textContent = 'Loading data...';
        
        try {
            const res = await apiFetch(`/laporan-energi/${id}/preview`);
            const data = await res.json();
            
            if (!res.ok) {
                closePreviewModal();
                vsAlert.error('Gagal', data.message || 'Gagal memuat preview.');
                return;
            }

            const r = data.data;
            const ruanganList = r.ruangan || [];
            
            // Populate Header
            titleEl.textContent = r.title || 'Laporan Energi';
            dateEl.textContent = `Generated on ${fmtDate(r.generated_at)}`;
            
            // Populate Summary
            kwhEl.textContent = fmt(r.total_kwh_ringkasan, 1);
            roomsCountEl.textContent = r.jumlah_ruangan;
            
            // Populate Table
            if (ruanganList.length) {
                roomsListEl.innerHTML = ruanganList.map(ru => `
                    <tr class="hover:bg-white/5 transition-colors">
                        <td class="px-5 py-3 text-white">
                            <span class="font-bold">${ru.nama_ruangan || 'Unknown'}</span>
                            <span class="text-slate-500 block text-[11px] mt-0.5 tracking-wide">${ru.kode_ruangan || ''}</span>
                        </td>
                        <td class="px-5 py-3 text-right font-bold text-[#00d4aa]">${fmt(ru.total_kwh || 0, 1)} kWh</td>
                    </tr>
                `).join('');
            } else {
                roomsListEl.innerHTML = `<tr><td colspan="2" class="px-5 py-8 text-center text-slate-500 text-[13px]">Tidak ada rincian ruangan.</td></tr>`;
            }
            
            // Show data
            loading.classList.add('hidden');
            dataContainer.classList.remove('hidden');
            
        } catch (e) {
            closePreviewModal();
            vsAlert.error('Koneksi Error', 'Tidak dapat terhubung ke server untuk memuat preview.');
        }
    };

    window.closePreviewModal = function() {
        const modal = document.getElementById('preview-modal');
        const modalContent = document.getElementById('preview-modal-content');
        
        modalContent.classList.remove('scale-100', 'opacity-100');
        modalContent.classList.add('scale-95', 'opacity-0');
        
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 200);
    };

    document.addEventListener('DOMContentLoaded', () => {
        setActiveType('bulanan');
        loadReportList();
    });
})();
</script>
@endpush

