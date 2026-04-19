@extends('layouts.main')

@section('content')
<div class="mt-2">
    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-[13px] text-slate-400 mb-6">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" stroke-width="2"/></svg>
        <span>&rsaquo;</span>
        <span>Dashboard</span>
        <span>&rsaquo;</span>
        <span class="text-white font-medium">Devices</span>
    </nav>

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-[32px] font-bold text-white leading-tight">Devices</h1>
            <p class="text-[14px] text-slate-500 mt-1">IoT device management and monitoring</p>
        </div>
        <button onclick="openAddDeviceModal()" class="flex items-center gap-2 px-5 py-2.5 bg-[#00d4aa] hover:bg-[#00bfa0] text-white rounded-lg font-bold text-[14px] transition-all self-start sm:self-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M12 4v16m8-8H4"/></svg>
            Add Device
        </button>
    </div>

    <!-- Table -->
    <div class="bg-[#1e293b] border border-[#334155] rounded-[24px] overflow-hidden shadow-2xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#0f172a] border-b border-[#334155]">
                        <th class="px-6 py-5 text-[11px] font-bold text-slate-500 uppercase tracking-widest">Device ID</th>
                        <th class="px-6 py-5 text-[11px] font-bold text-slate-500 uppercase tracking-widest">Name</th>
                        <th class="px-6 py-5 text-[11px] font-bold text-slate-500 uppercase tracking-widest">Type</th>
                        <th class="px-6 py-5 text-[11px] font-bold text-slate-500 uppercase tracking-widest">IP Address</th>
                        <th class="px-6 py-5 text-[11px] font-bold text-slate-500 uppercase tracking-widest">Room</th>
                    </tr>
                </thead>
                <tbody id="devices-table-body" class="divide-y divide-[#334155]">
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center text-slate-500">
                            <div class="flex flex-col items-center gap-3">
                                <svg class="w-10 h-10 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" stroke-width="2"/></svg>
                                <span class="text-[14px]">Loading devices...</span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    let devicesMap = {};

    function openAddDeviceModal() {
        document.getElementById('add-device-modal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    function closeAddDeviceModal() {
        document.getElementById('add-device-modal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Load rooms into the Room dropdown
    async function loadRoomsDropdown() {
        try {
            const res = await apiFetch('/ruangan');
            if (!res.ok) return;
            const data = await res.json();
            const rooms = Array.isArray(data) ? data : (data.data || []);
            const sel = document.querySelector('select[name="ruangan_id"]');
            rooms.forEach(r => {
                const opt = document.createElement('option');
                opt.value = r.id;
                opt.textContent = r.nama_ruangan + (r.id ? ` (${r.id})` : '');
                sel.appendChild(opt);
            });
        } catch(e) { /* ignore */ }
    }

    async function loadDevices() {
        const tbody = document.getElementById('devices-table-body');
        try {
            const res = await apiFetch('/devices');
            const data = await res.json();
            const devices = Array.isArray(data) ? data : (data.data || []);

            devicesMap = {};
            devices.forEach(d => { devicesMap[d.id] = d; });

            if (devices.length === 0) {
                tbody.innerHTML = `<tr><td colspan="5" class="px-6 py-16 text-center text-slate-500">
                    <div class="flex flex-col items-center gap-3">
                        <svg class="w-10 h-10 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" stroke-width="2"/></svg>
                        <span class="text-[14px]">No devices found. Click "+ Add Device" to add one.</span>
                    </div></td></tr>`;
                return;
            }

            tbody.innerHTML = devices.map(d => {
                const deviceCode = d.device_code || ('DEV-' + String(d.id).padStart(3, '0'));
                const roomName   = d.ruangan ? d.ruangan.nama_ruangan : (d.ruangan_id || '–');
                return `
                <tr class="hover:bg-white/[0.02] transition-all group">
                    <td class="px-6 py-5">
                        <span class="text-[13px] font-bold text-[#00d4aa] tracking-wider uppercase">${deviceCode}</span>
                    </td>
                    <td class="px-6 py-5">
                        <span class="text-[15px] font-bold text-white">${d.name || '-'}</span>
                    </td>
                    <td class="px-6 py-5 text-[14px] text-slate-400 font-medium">${d.type || '-'}</td>
                    <td class="px-6 py-5 text-[14px] text-slate-400 font-medium">${d.ip_address || '-'}</td>
                    <td class="px-6 py-5 text-[14px] text-slate-400 font-medium">${roomName}</td>
                </tr>`;
            }).join('');


        } catch (err) {
            tbody.innerHTML = `<tr><td colspan="5" class="px-6 py-12 text-center text-slate-500 text-[14px]">Failed to load devices. Please refresh.</td></tr>`;
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        loadDevices();
        loadRoomsDropdown();
    });
</script>
@endpush
