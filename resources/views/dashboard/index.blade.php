@extends('layouts.main')

@section('content')
{{-- Breadcrumb --}}
<div class="flex items-center gap-2 text-slate-500 text-[13px] mb-8">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" stroke-width="2"/></svg>
    <span class="text-slate-600">/</span>
    <span class="text-white font-medium">Dashboard</span>
</div>

{{-- Page Title --}}
<div class="mb-8">
    <h1 class="text-[32px] font-extrabold text-white tracking-tight leading-none">Admin Dashboard</h1>
    <p class="text-slate-400 text-[14px] mt-2">Essential energy metrics and system overview</p>
</div>

{{-- Summary Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-8" id="summary-cards">
    {{-- Total Energy - TEAL icon --}}
    <div class="rounded-2xl p-6 relative overflow-hidden transition-all duration-300" style="background:#161d2e; border:1px solid #232c3d;">
        <div class="flex items-start justify-between mb-5">
            <div class="w-11 h-11 rounded-xl flex items-center justify-center" style="background:rgba(0,212,170,0.15); border:1px solid rgba(0,212,170,0.2);">
                <svg class="w-5 h-5" style="color:#00d4aa" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z" stroke-width="2"/></svg>
            </div>
            <span id="energy-change-badge" class="text-[11px] font-bold px-2 py-1 rounded-lg" style="color:#00d4aa; background:rgba(0,212,170,0.1);">+12.5%</span>
        </div>
        <p class="text-slate-500 text-[11px] font-bold uppercase tracking-wider mb-1">Total Energy Used Last Month</p>
        <p class="text-[28px] font-extrabold text-white leading-none" id="total-energy">—</p>
        <p class="text-slate-600 text-[12px] mt-1" id="energy-period">Loading...</p>
    </div>

    {{-- Energy Efficiency - BLUE icon --}}
    <div class="rounded-2xl p-6 relative overflow-hidden transition-all duration-300" style="background:#161d2e; border:1px solid #232c3d;">
        <div class="flex items-start justify-between mb-5">
            <div class="w-11 h-11 rounded-xl flex items-center justify-center" style="background:rgba(99,179,237,0.15); border:1px solid rgba(99,179,237,0.2);">
                <svg class="w-5 h-5" style="color:#63b3ed" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" stroke-width="2"/></svg>
            </div>
            <span class="text-[11px] font-bold px-2 py-1 rounded-lg" style="color:#63b3ed; background:rgba(99,179,237,0.1);">+5.7%</span>
        </div>
        <p class="text-slate-500 text-[11px] font-bold uppercase tracking-wider mb-1">Energy Efficiency</p>
        <p class="text-[28px] font-extrabold text-white leading-none"><span id="efficiency-value">—</span><span class="text-[16px] text-slate-500 ml-1">%</span></p>
        <p class="text-slate-600 text-[12px] mt-1">Score</p>
    </div>

    {{-- Active Rooms - PURPLE icon --}}
    <div class="rounded-2xl p-6 relative overflow-hidden transition-all duration-300" style="background:#161d2e; border:1px solid #232c3d;">
        <div class="flex items-start justify-between mb-5">
            <div class="w-11 h-11 rounded-xl flex items-center justify-center" style="background:rgba(183,148,246,0.15); border:1px solid rgba(183,148,246,0.2);">
                <svg class="w-5 h-5" style="color:#b794f6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" stroke-width="2"/></svg>
            </div>
            <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 15l7-7 7 7" stroke-width="2"/></svg>
        </div>
        <p class="text-slate-500 text-[11px] font-bold uppercase tracking-wider mb-1">Active Rooms</p>
        <p class="text-[28px] font-extrabold text-white leading-none" id="active-rooms">—</p>
        <p class="text-slate-600 text-[12px] mt-1" id="rooms-total">of — total</p>
    </div>

    {{-- Active Devices - ORANGE icon --}}
    <div class="rounded-2xl p-6 relative overflow-hidden transition-all duration-300" style="background:#161d2e; border:1px solid #232c3d;">
        <div class="flex items-start justify-between mb-5">
            <div class="w-11 h-11 rounded-xl flex items-center justify-center" style="background:rgba(246,173,85,0.15); border:1px solid rgba(246,173,85,0.2);">
                <svg class="w-5 h-5" style="color:#f6ad55" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" stroke-width="2"/></svg>
            </div>
            <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="2"/></svg>
        </div>
        <p class="text-slate-500 text-[11px] font-bold uppercase tracking-wider mb-1">Active Devices</p>
        <p class="text-[28px] font-extrabold text-white leading-none" id="active-devices">—</p>
        <p class="text-slate-600 text-[12px] mt-1">Connected</p>
    </div>
</div>

{{-- Energy Usage Trend Chart --}}
<div class="bg-[#1c2333] border border-[#2a3347] rounded-2xl p-6 mb-8">
    <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
        <div>
            <h2 class="text-[18px] font-bold text-white">Energy Usage Trend</h2>
            <p class="text-slate-500 text-[12px] mt-0.5">12 months • Monthly consumption</p>
        </div>
        <div class="flex items-center gap-2">
            <button id="trend-prev" class="w-8 h-8 rounded-lg bg-[#1e293b] border border-[#334155] text-slate-400 hover:text-white hover:border-[#475569] transition-all flex items-center justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="2"/></svg>
            </button>
            <span id="trend-year" class="px-4 h-8 rounded-lg bg-[#00d4aa] text-[#0b1120] font-bold text-[13px] flex items-center">2026</span>
            <button id="trend-next" class="w-8 h-8 rounded-lg bg-[#1e293b] border border-[#334155] text-slate-400 hover:text-white hover:border-[#475569] transition-all flex items-center justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="2"/></svg>
            </button>
        </div>
    </div>
    <div class="relative w-full" style="height:400px;">
        <canvas id="trend-chart"></canvas>
        <div id="trend-loading" class="absolute inset-0 flex items-center justify-center">
            <div class="flex flex-col items-center gap-3">
                <div class="w-8 h-8 border-2 border-[#00d4aa] border-t-transparent rounded-full animate-spin"></div>
                <span class="text-slate-500 text-[12px]">Loading data...</span>
            </div>
        </div>
    </div>
    <div class="flex items-center justify-between mt-4">
        <p class="text-slate-600 text-[11px]">Annual consumption data</p>
        <div class="flex items-center gap-1.5">
            <div class="w-2 h-2 rounded-full bg-[#00d4aa] animate-pulse"></div>
            <span class="text-[#00d4aa] text-[11px] font-bold">Live</span>
        </div>
    </div>
</div>

{{-- Room Energy Overview --}}
<div class="bg-[#1c2333] border border-[#2a3347] rounded-2xl p-6">
    <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
        <div>
            <h2 class="text-[18px] font-bold text-white">Room Energy Overview</h2>
            <p class="text-slate-500 text-[12px] mt-0.5">Energy consumption per room this period</p>
        </div>
        <div id="rooms-period-badge" class="px-3 py-1.5 rounded-lg text-slate-400 text-[12px] font-medium" style="background:#253044; border:1px solid #334155;">—</div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-[13px]">
            <thead>
                <tr class="border-b border-[#2e3a4e]">
                    <th class="text-left text-slate-500 font-semibold uppercase tracking-wider text-[11px] pb-3 pr-4">Room</th>
                    <th class="text-left text-slate-500 font-semibold uppercase tracking-wider text-[11px] pb-3 pr-4">Location</th>
                    <th class="text-left text-slate-500 font-semibold uppercase tracking-wider text-[11px] pb-3 pr-4">Status</th>
                    <th class="text-left text-slate-500 font-semibold uppercase tracking-wider text-[11px] pb-3 pr-4">Devices</th>
                    <th class="text-left text-slate-500 font-semibold uppercase tracking-wider text-[11px] pb-3 pr-4">Consumption</th>
                    <th class="text-left text-slate-500 font-semibold uppercase tracking-wider text-[11px] pb-3">Power</th>
                </tr>
            </thead>
            <tbody id="rooms-table-body">
                <tr>
                    <td colspan="6" class="py-10 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <div class="w-8 h-8 border-2 border-[#00d4aa] border-t-transparent rounded-full animate-spin"></div>
                            <span class="text-slate-500">Loading rooms...</span>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div id="rooms-empty" class="hidden py-10 text-center">
        <svg class="w-12 h-12 text-slate-700 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" stroke-width="2"/></svg>
        <p class="text-slate-500">No room data available</p>
    </div>
</div>

{{-- Last Updated --}}
<div class="mt-6 text-center">
    <p class="text-slate-600 text-[12px] flex items-center justify-center gap-1.5">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2"/></svg>
        Last updated: <span id="last-updated-time">—</span>
    </p>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function () {
    let trendChart = null;
    let currentYear = new Date().getFullYear();

    function fmt(num) {
        return new Intl.NumberFormat('id-ID', {
            minimumFractionDigits: 3,
            maximumFractionDigits: 3
        }).format(parseFloat(num) || 0);
    }

    function setEl(id, val) {
        const el = document.getElementById(id);
        if (el) el.textContent = val;
    }

    function nowStr() {
        return new Date().toLocaleTimeString('id-ID', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });
    }

    function escHtml(str) {
        return String(str ?? '').replace(/[&<>"']/g, c => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#39;'
        }[c]));
    }

    async function getDeviceStatusMap() {
    const map = {};

    try {
        const res = await window.apiFetch('/devices');
        if (!res.ok) return map;

        const data = await res.json();
        const devices = Array.isArray(data) ? data : (data.data || []);

        for (const device of devices) {
            if (!device.ruangan_id) continue;

            try {
                const statusRes = await window.apiFetch('/devices/' + device.id + '/status');
                if (!statusRes.ok) continue;

                const status = await statusRes.json();

                const relay = String(status.relay || 'OFF').trim().toUpperCase();
                const energy = parseFloat(status.energy ?? 0) || 0;
                const power = parseFloat(status.power ?? 0) || 0;

                map[device.ruangan_id] = {
                    relay: status.relay
                        ? String(status.relay).trim().toUpperCase()
                        : (status.online ? 'ON' : 'OFF'),
                    energy: parseFloat(status.energy ?? 0) || 0,
                    power: parseFloat(status.power ?? 0) || 0
                };
            } catch (e) {}
        }
    } catch (e) {}

    return map;
}

    function totalEnergyFromMap(statusMap) {
        let total = 0;

        Object.values(statusMap).forEach(device => {
            total += parseFloat(device.energy) || 0;
        });

        return total;
    }

    function activeDeviceCountFromMap(statusMap) {
    let total = 0;

    Object.values(statusMap).forEach(device => {
        if (device.relay === 'ON') {
            total++;
        }
    });

    return total;
}

    (function setGreeting() {
        const h = new Date().getHours();
        const greet = h < 12 ? 'Good Morning' : h < 17 ? 'Good Afternoon' : 'Good Evening';
        const el = document.querySelector('header h2');
        if (el) el.textContent = `${greet}, Admin`;
    })();

    async function loadSummary() {
        try {
            const res = await window.apiFetch('/dashboard/summary');
            if (!res.ok) return;

            const data = await res.json();
            const s = data.summary;
            const p = data.period;

            const statusMap = await getDeviceStatusMap();
            const liveEnergy = totalEnergyFromMap(statusMap);
            const activeDevices = activeDeviceCountFromMap(statusMap);

            setEl('total-energy', fmt(liveEnergy) + ' kWh');
            setEl('efficiency-value', fmt(s.energy_efficiency_percent));
            setEl('active-rooms', s.active_rooms);
            setEl('active-devices', activeDevices);
            setEl('energy-period', p.month_name + ' ' + p.year);
            setEl('last-updated-time', nowStr());
        } catch (e) {
            console.error('[Dashboard] summary error:', e);
        }
    }

    async function loadTrend(year) {
        setEl('trend-year', year);

        const loading = document.getElementById('trend-loading');
        if (loading) loading.style.display = 'flex';

        try {
            const res = await window.apiFetch('/dashboard/trend?year=' + year);
            if (!res.ok) {
                if (loading) loading.style.display = 'none';
                return;
            }

            const data = await res.json();
            const monthly = data.trend.monthly || [];

            const labels = monthly.map(m => m.month_name);
            const values = monthly.map(m => parseFloat(m.total_kwh) || 0);

            const statusMap = await getDeviceStatusMap();
            const liveEnergy = totalEnergyFromMap(statusMap);
            const currentMonthIndex = new Date().getMonth();

            if (year === new Date().getFullYear()) {
                values[currentMonthIndex] = liveEnergy;
            }

            if (loading) loading.style.display = 'none';
            renderTrendChart(labels, values);
        } catch (e) {
            console.error('[Dashboard] trend error:', e);
            if (loading) loading.style.display = 'none';
        }
    }

    function renderTrendChart(labels, values) {
        const canvas = document.getElementById('trend-chart');
        if (!canvas) return;

        const ctx = canvas.getContext('2d');

        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(0, 212, 170, 0.40)');
        gradient.addColorStop(0.6, 'rgba(0, 212, 170, 0.15)');
        gradient.addColorStop(1, 'rgba(0, 212, 170, 0.01)');

        if (trendChart) trendChart.destroy();

        trendChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels,
                datasets: [{
                    data: values,
                    borderColor: '#00d4aa',
                    borderWidth: 2.5,
                    backgroundColor: gradient,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: '#00d4aa',
                    pointBorderColor: '#0b1120',
                    pointBorderWidth: 2,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        borderColor: '#334155',
                        borderWidth: 1,
                        titleColor: '#94a3b8',
                        bodyColor: '#ffffff',
                        padding: 12,
                        callbacks: {
                            label: ctx => ' ' + fmt(ctx.parsed.y) + ' kWh'
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { color: 'rgba(51,65,85,0.4)', drawTicks: false },
                        border: { display: false },
                        ticks: { color: '#64748b', font: { size: 11 } }
                    },
                    y: {
                        grid: { color: 'rgba(51,65,85,0.4)', drawTicks: false },
                        border: { display: false },
                        ticks: {
                            color: '#64748b',
                            font: { size: 11 },
                            callback: v => fmt(v)
                        },
                        beginAtZero: true
                    }
                }
            }
        });
    }

    async function loadRooms() {
        try {
            const res = await window.apiFetch('/dashboard/rooms');
            if (!res.ok) return;

            const data = await res.json();
            const rooms = data.rooms || [];
            const p = data.period;

            const statusMap = await getDeviceStatusMap();

            rooms.forEach(room => {
                const iot = statusMap[room.id];

                if (iot) {
                    room.power = iot.relay === 'ON' ? 'ON' : 'OFF';
                    room.consumption_kwh = parseFloat(iot.energy) || 0;
                }
            });

            setEl('rooms-period-badge', p.month_name + ' ' + p.year);

            const tbody = document.getElementById('rooms-table-body');
            const empty = document.getElementById('rooms-empty');

            if (!rooms.length) {
                tbody.innerHTML = '';
                empty.classList.remove('hidden');
                return;
            }

            empty.classList.add('hidden');

            const maxKwh = Math.max(...rooms.map(r => parseFloat(r.consumption_kwh) || 0), 1);

            tbody.innerHTML = rooms.map(r => {
                const statusColor = r.status === 'digunakan'
                    ? 'bg-emerald-500/15 text-emerald-400 border-emerald-500/20'
                    : 'bg-slate-500/15 text-slate-400 border-slate-500/20';

                const statusLabel = r.status === 'digunakan' ? 'In Use' : 'Available';

                const powerColor = r.power === 'ON'
                    ? 'bg-[#00d4aa]/15 text-[#00d4aa] border-[#00d4aa]/20'
                    : 'bg-slate-700/40 text-slate-500 border-slate-600/20';

                const kwh = parseFloat(r.consumption_kwh) || 0;
                const barPct = maxKwh > 0 ? ((kwh / maxKwh) * 100).toFixed(1) : 0;

                return `<tr class="border-b border-[#2e3a4e]/70 hover:bg-[#253044]/50 transition-colors">
                    <td class="py-4 pr-4">
                        <span class="font-semibold text-white">${escHtml(r.nama_ruangan)}</span>
                    </td>
                    <td class="py-4 pr-4 text-slate-400">${escHtml(r.lokasi || '—')}</td>
                    <td class="py-4 pr-4">
                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold border ${statusColor}">${statusLabel}</span>
                    </td>
                    <td class="py-4 pr-4 text-slate-300">${r.devices_count}</td>
                    <td class="py-4 pr-4">
                        <div class="flex items-center gap-3">
                            <div class="w-24 h-1.5 rounded-full overflow-hidden" style="background:#2e3a4e;">
                                <div class="h-full bg-[#00d4aa] rounded-full transition-all duration-700" style="width:${barPct}%"></div>
                            </div>
                            <span class="text-white font-medium whitespace-nowrap">${fmt(kwh)} kWh</span>
                        </div>
                    </td>
                    <td class="py-4">
                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold border ${powerColor}">${r.power}</span>
                    </td>
                </tr>`;
            }).join('');

            const totalRooms = rooms.length;
            const activeCount = rooms.filter(r => r.status === 'digunakan').length;

            setEl('rooms-total', `of ${totalRooms} total`);
            setEl('active-rooms', activeCount);
            setEl('last-updated-time', nowStr());
        } catch (e) {
            console.error('[Dashboard] rooms error:', e);
        }
    }

    document.getElementById('trend-prev').addEventListener('click', () => {
        currentYear--;
        loadTrend(currentYear);
    });

    document.getElementById('trend-next').addEventListener('click', () => {
        currentYear++;
        loadTrend(currentYear);
    });

    loadSummary();
    loadTrend(currentYear);
    loadRooms();

    setInterval(() => {
        loadSummary();
        loadRooms();
        loadTrend(currentYear);
    }, 5000);
})();
</script>
@endpush
