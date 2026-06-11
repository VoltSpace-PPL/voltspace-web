@extends('layouts.main')

@section('content')
{{-- Page Header --}}
<div class="flex items-start justify-between mb-8 flex-wrap gap-4">
    <div>
        <h1 class="text-[28px] font-extrabold text-white tracking-tight leading-none">Energy Alerts</h1>
        <p class="text-slate-400 text-[14px] mt-2">Energy anomaly monitoring and alerts</p>
    </div>
    <div class="flex items-center gap-3">
        {{-- Active alerts badge --}}
        <span id="active-alerts-badge" class="hidden items-center gap-1.5 px-3 py-1.5 rounded-full text-[12px] font-bold"
              style="background:rgba(239,68,68,0.15); border:1px solid rgba(239,68,68,0.3); color:#f87171;">
            <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse inline-block"></span>
            <span id="active-alerts-count">0</span> Active Alerts
        </span>
        {{-- Auto-refresh indicator --}}
        <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl text-slate-400 text-[12px] font-medium" style="background:#161e2d; border:1px solid #1e2d45;">
            <div class="w-1.5 h-1.5 rounded-full bg-[#00d4aa] animate-pulse"></div>
            Real-time Monitoring
        </div>
        {{-- Refresh --}}
        <button id="refresh-btn" onclick="loadAlerts()"
            class="w-9 h-9 rounded-xl flex items-center justify-center transition-all hover:bg-[#263047]"
            style="background:#161e2d; border:1px solid #1e2d45; color:#94a3b8;"
            title="Refresh alerts">
            <svg id="refresh-icon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" stroke-width="2"/>
            </svg>
        </button>
    </div>
</div>

{{-- Stats Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
    {{-- Critical --}}
    <div class="rounded-2xl p-6 relative overflow-hidden transition-all" style="background:#161e2d; border:1px solid rgba(239,68,68,0.25);">
        <div class="absolute inset-0 opacity-5" style="background:linear-gradient(135deg,#ef4444,transparent);"></div>
        <p class="text-[42px] font-extrabold leading-none mb-1" style="color:#ef4444;" id="count-danger">—</p>
        <p class="text-slate-400 text-[13px] font-semibold flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-red-500 inline-block"></span>
            Critical
        </p>
    </div>
    {{-- Warning --}}
    <div class="rounded-2xl p-6 relative overflow-hidden transition-all" style="background:#161e2d; border:1px solid rgba(234,179,8,0.25);">
        <div class="absolute inset-0 opacity-5" style="background:linear-gradient(135deg,#eab308,transparent);"></div>
        <p class="text-[42px] font-extrabold leading-none mb-1" style="color:#eab308;" id="count-warning">—</p>
        <p class="text-slate-400 text-[13px] font-semibold flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-yellow-500 inline-block"></span>
            Warning
        </p>
    </div>
    {{-- Info (threshold setting) --}}
    <div class="rounded-2xl p-6 relative overflow-hidden transition-all" style="background:#161e2d; border:1px solid rgba(99,179,237,0.25);">
        <div class="absolute inset-0 opacity-5" style="background:linear-gradient(135deg,#63b3ed,transparent);"></div>
        <p class="text-[42px] font-extrabold leading-none mb-1 text-[#63b3ed]" id="threshold-display">—</p>
        <p class="text-slate-400 text-[13px] font-semibold flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-blue-400 inline-block"></span>
            Threshold (kWh)
        </p>
    </div>
</div>

{{-- Threshold Settings Banner --}}
<div id="threshold-banner" class="mb-6 px-5 py-4 rounded-2xl flex items-center justify-between gap-4 flex-wrap"
     style="background:rgba(0,170,255,0.07); border:1px solid rgba(0,170,255,0.18);">
    <div class="flex items-center gap-3">
        <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0"
             style="background:rgba(0,170,255,0.15); border:1px solid rgba(0,170,255,0.2);">
            <svg class="w-4 h-4 text-[#00aaff]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" stroke-width="2"/>
                <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" stroke-width="2"/>
            </svg>
        </div>
        <div>
            <p class="text-[#00aaff] text-[13px] font-bold">Alert Thresholds</p>
            <p class="text-slate-400 text-[12px] mt-0.5">
                High Usage: <strong class="text-white" id="banner-threshold">—</strong> kWh &nbsp;·&nbsp;
                Warning at 80% &nbsp;·&nbsp; Critical at 85%
            </p>
        </div>
    </div>
    <p class="text-slate-500 text-[12px]">Alerts are computed in real-time. Dismiss resets on page refresh.</p>
</div>

{{-- Alert List --}}
<div id="alerts-container">
    <div class="flex flex-col items-center justify-center py-20 gap-3">
        <div class="w-8 h-8 border-2 border-[#00d4aa] border-t-transparent rounded-full animate-spin"></div>
        <span class="text-slate-500 text-[13px]">Checking energy usage...</span>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    /* ── Session-based dismiss store ─────────────────────────────── */
    const DISMISS_KEY = 'vs_dismissed_alerts';
    function getDismissed() {
        try { return JSON.parse(sessionStorage.getItem(DISMISS_KEY) || '[]'); } catch { return []; }
    }
    function addDismissed(key) {
        const d = getDismissed(); d.push(key); sessionStorage.setItem(DISMISS_KEY, JSON.stringify(d));
    }
    function isDismissed(key) { return getDismissed().includes(key); }



    /* ── Severity config ──────────────────────────────────────────── */
    const severityCfg = {
        danger:  {
            label: 'Critical',
            badgeCls: 'bg-red-500/15 text-red-400 border-red-500/30',
            dot: 'bg-red-500',
            cardBg: 'rgba(239,68,68,0.06)',
            cardBorder: 'rgba(239,68,68,0.25)',
            leftBar: '#ef4444',
            icon: `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" stroke-width="2"/></svg>`,
        },
        warning: {
            label: 'Warning',
            badgeCls: 'bg-yellow-500/15 text-yellow-400 border-yellow-500/30',
            dot: 'bg-yellow-400',
            cardBg: 'rgba(234,179,8,0.06)',
            cardBorder: 'rgba(234,179,8,0.25)',
            leftBar: '#eab308',
            icon: `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2"/></svg>`,
        },
    };

    function fmt(n, dec = 1) { return parseFloat(n || 0).toFixed(dec); }

    /* ── Render alert list ────────────────────────────────────────── */
    function renderAlerts(data) {
        const container = document.getElementById('alerts-container');
        const allAlerts = data.alerts || [];
        const threshold = parseFloat(data.threshold_kwh || 0);

        // Update stats
        const dangers  = allAlerts.filter(a => a.severity === 'danger').length;
        const warnings = allAlerts.filter(a => a.severity === 'warning').length;
        document.getElementById('count-danger').textContent   = dangers;
        document.getElementById('count-warning').textContent  = warnings;
        document.getElementById('threshold-display').textContent = fmt(threshold, 0);
        document.getElementById('banner-threshold').textContent  = fmt(threshold, 0);

        // Active alerts badge (not dismissed)
        const visible = allAlerts.filter(a => !isDismissed(a.ruangan_id + '_' + a.severity));
        const activeCount = visible.length;
        const badge = document.getElementById('active-alerts-badge');
        document.getElementById('active-alerts-count').textContent = activeCount;
        if (activeCount > 0) { badge.classList.remove('hidden'); badge.classList.add('flex'); }
        else { badge.classList.add('hidden'); badge.classList.remove('flex'); }

        if (!allAlerts.length) {
            container.innerHTML = `
<div class="flex flex-col items-center justify-center py-24 gap-5">
    <div class="relative w-24 h-24">
        <div class="absolute inset-0 rounded-full opacity-10 animate-ping" style="background:#00d4aa;"></div>
        <div class="relative w-24 h-24 rounded-full flex items-center justify-center" style="background:rgba(0,212,170,0.12); border:2px solid rgba(0,212,170,0.3);">
            <svg class="w-10 h-10 text-[#00d4aa]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2"/>
            </svg>
        </div>
    </div>
    <div class="text-center">
        <p class="text-[20px] font-bold text-white mb-1">All Systems Nominal</p>
        <p class="text-slate-500 text-[14px]">No energy anomalies detected for this period.</p>
        <p class="text-slate-600 text-[12px] mt-1">Threshold: <span class="text-slate-400 font-medium">${fmt(threshold,0)} kWh</span></p>
    </div>
</div>`;
            return;
        }

        // Sort: danger first, then warning
        const sorted = [...allAlerts].sort((a, b) => {
            const order = { danger: 0, warning: 1 };
            return (order[a.severity] ?? 2) - (order[b.severity] ?? 2);
        });

        container.innerHTML = `<div class="space-y-3">${sorted.map(alert => {
            const cfg = severityCfg[alert.severity] || severityCfg.warning;
            const key = alert.ruangan_id + '_' + alert.severity;
            const dismissed = isDismissed(key);
            const pct = Math.min(100, parseFloat(alert.persentase_ambang || 0));

            return `
<div id="alert-card-${key}" class="rounded-2xl overflow-hidden transition-all duration-300 ${dismissed ? 'opacity-40' : ''}"
     style="background:${cfg.cardBg}; border:1px solid ${cfg.cardBorder}; border-left:3px solid ${cfg.leftBar};">
    <div class="px-5 py-4 flex items-start justify-between gap-4">
        <div class="flex items-start gap-4 flex-1 min-w-0">
            {{-- Severity icon --}}
            <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0 mt-0.5 ${cfg.badgeCls} border">
                ${cfg.icon}
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2.5 flex-wrap mb-1">
                    <span class="w-2 h-2 rounded-full ${cfg.dot} flex-shrink-0"></span>
                    <h3 class="text-[14px] font-bold text-white">${escHtml(alert.nama_ruangan)}</h3>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border ${cfg.badgeCls}">${cfg.label}</span>
                    ${dismissed ? '<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-500/15 text-slate-400 border border-slate-500/30">Dismissed</span>' : ''}
                </div>
                <p class="text-slate-400 text-[13px] mb-3 leading-relaxed">${escHtml(alert.message)}</p>
                {{-- Progress bar --}}
                <div class="mb-2">
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-slate-500 text-[11px] font-semibold uppercase tracking-wider">Usage vs Threshold</span>
                        <span class="text-[12px] font-bold" style="color:${cfg.leftBar};">${pct}%</span>
                    </div>
                    <div class="h-1.5 rounded-full overflow-hidden" style="background:rgba(255,255,255,0.05);">
                        <div class="h-full rounded-full transition-all duration-700"
                             style="width:${pct}%; background:${cfg.leftBar};"></div>
                    </div>
                </div>
                {{-- Meta chips --}}
                <div class="flex items-center gap-3 flex-wrap mt-3">
                    <span class="flex items-center gap-1.5 text-[12px] text-slate-500">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z" stroke-width="2"/></svg>
                        <span class="text-white font-bold">${fmt(alert.total_kwh, 1)} kWh</span> consumed
                    </span>
                    <span class="text-slate-700">·</span>
                    <span class="flex items-center gap-1.5 text-[12px] text-slate-500">
                        Limit: <span class="text-slate-300 font-semibold">${fmt(alert.threshold_kwh, 0)} kWh</span>
                    </span>
                    ${alert.sisa_kwh_sebelum_batas !== undefined ? `
                    <span class="text-slate-700">·</span>
                    <span class="flex items-center gap-1.5 text-[12px] text-yellow-500">
                        ${fmt(alert.sisa_kwh_sebelum_batas, 1)} kWh remaining
                    </span>` : ''}
                </div>
            </div>
        </div>
        {{-- Dismiss / Resolved button --}}
        <button onclick="dismissAlert('${key}')"
            class="flex-shrink-0 flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-[12px] font-bold transition-all hover:scale-[1.03] active:scale-[0.97] ${dismissed ? 'opacity-60 cursor-default' : ''}"
            style="background:${dismissed ? 'rgba(71,85,105,0.2)' : 'rgba(16,185,129,0.12)'}; border:1px solid ${dismissed ? 'rgba(71,85,105,0.3)' : 'rgba(16,185,129,0.3)'}; color:${dismissed ? '#64748b' : '#34d399'};"
            ${dismissed ? 'disabled' : ''}>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                <path d="M5 13l4 4L19 7"/>
            </svg>
            ${dismissed ? 'Resolved' : 'Resolve'}
        </button>
    </div>
</div>`;
        }).join('')}</div>`;
    }

    function escHtml(s) {
        return String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
    }

    /* ── Dismiss an alert (session) ───────────────────────────────── */
    window.dismissAlert = function(key) {
        addDismissed(key);
        const card = document.getElementById('alert-card-' + key);
        if (card) {
            card.classList.add('opacity-40');
            const btn = card.querySelector('button');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = `<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M5 13l4 4L19 7"/></svg> Resolved`;
                btn.style.background = 'rgba(71,85,105,0.2)';
                btn.style.borderColor = 'rgba(71,85,105,0.3)';
                btn.style.color = '#64748b';
            }
        }
        // Update badge count
        const badge = document.getElementById('active-alerts-badge');
        const countEl = document.getElementById('active-alerts-count');
        const current = parseInt(countEl.textContent) - 1;
        countEl.textContent = current;
        if (current <= 0) { badge.classList.add('hidden'); badge.classList.remove('flex'); }
    };

    /* ── Load alerts ──────────────────────────────────────────────── */
    window.loadAlerts = async function () {
        const container = document.getElementById('alerts-container');
        const icon = document.getElementById('refresh-icon');
        icon.classList.add('animate-spin');
        container.innerHTML = `<div class="flex flex-col items-center justify-center py-20 gap-3">
            <div class="w-8 h-8 border-2 border-[#00d4aa] border-t-transparent rounded-full animate-spin"></div>
            <span class="text-slate-500 text-[13px]">Checking energy usage...</span>
        </div>`;

        const now = new Date();
        const m = now.getMonth() + 1;
        const y = now.getFullYear();

        try {
            const res  = await apiFetch(`/energy-alerts?bulan=${m}&tahun=${y}`);
            const data = await res.json();
            if (!res.ok) {
                container.innerHTML = `<div class="text-center py-16 text-red-400 text-[14px]">${data.message || 'Failed to load alerts.'}</div>`;
                return;
            }
            renderAlerts(data);
        } catch (e) {
            container.innerHTML = `<div class="text-center py-16 text-slate-500 text-[14px]">Connection error – could not fetch alert data.</div>`;
        } finally {
            icon.classList.remove('animate-spin');
        }
    };

    /* ── Init ─────────────────────────────────────────────────────── */
    document.addEventListener('DOMContentLoaded', () => {
        loadAlerts();
        // Auto-refresh every 60s
        setInterval(loadAlerts, 60000);
    });
})();
</script>
@endpush
