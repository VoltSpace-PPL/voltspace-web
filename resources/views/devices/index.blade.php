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
                        <th class="px-6 py-5 text-[11px] font-bold text-slate-500 uppercase tracking-widest">Actions</th>
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

<!-- Add Device Modal -->
<div id="add-device-modal" class="fixed inset-0 z-[100] hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-md" onclick="closeAddDeviceModal()"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-[480px] p-4">
        <div class="glass-effect rounded-[24px] shadow-2xl overflow-hidden">
            <div class="p-6 flex justify-between items-center border-b border-white/10">
                <h3 class="text-[22px] font-bold text-white">Add Device</h3>
                <button onclick="closeAddDeviceModal()" class="w-8 h-8 flex items-center justify-center rounded-full bg-white/5 text-slate-400 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2.5"/></svg>
                </button>
            </div>
            <form id="add-device-form" class="p-8 space-y-5">
                <!-- 2-col: Device ID + Type -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="block text-[13px] font-bold text-slate-400 uppercase tracking-wider">Device ID</label>
                        <input type="text" name="device_code" placeholder="DEV-001"
                               class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-[14px] text-white placeholder:text-slate-600 focus:outline-none focus:border-[#00d4aa] transition-colors">
                    </div>
                    <div class="space-y-2">
                        <label class="block text-[13px] font-bold text-slate-400 uppercase tracking-wider">Type</label>
                        <input type="text" name="type" placeholder="Energy Meter" required
                               class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-[14px] text-white placeholder:text-slate-600 focus:outline-none focus:border-[#00d4aa] transition-colors">
                    </div>
                </div>

                <!-- Name -->
                <div class="space-y-2">
                    <label class="block text-[13px] font-bold text-slate-400 uppercase tracking-wider">Name</label>
                    <input type="text" name="name" placeholder="Smart Meter #4521" required
                           class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-[14px] text-white placeholder:text-slate-600 focus:outline-none focus:border-[#00d4aa] transition-colors">
                </div>

                <!-- Room -->
                <div class="space-y-2">
                    <label class="block text-[13px] font-bold text-slate-400 uppercase tracking-wider">Room</label>
                    <div class="relative">
                        <select name="ruangan_id"
                                class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-[14px] text-white focus:outline-none focus:border-[#00d4aa] transition-colors appearance-none cursor-pointer">
                            <option value="">— Select Room —</option>
                        </select>
                        <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="2"/></svg>
                    </div>
                </div>

                <!-- IP Address -->
                <div class="space-y-2">
                    <label class="block text-[13px] font-bold text-slate-400 uppercase tracking-wider">IP Address</label>
                    <input type="text" name="ip_address" placeholder="192.168.1.1" required
                           class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-[14px] text-white placeholder:text-slate-600 focus:outline-none focus:border-[#00d4aa] transition-colors">
                </div>

                <!-- Buttons -->
                <div class="flex gap-4 pt-2">
                    <button type="button" onclick="closeAddDeviceModal()" class="flex-1 py-3.5 bg-white/5 border border-white/10 text-white font-bold rounded-xl hover:bg-white/10 transition-colors">Cancel</button>
                    <button type="submit" class="flex-1 py-3.5 bg-[#00d4aa] text-white font-bold rounded-xl hover:bg-[#00bfa0] transition-colors shadow-lg shadow-[#00d4aa]/20">Add Device</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Device Modal -->
<div id="edit-device-modal" class="fixed inset-0 z-[100] hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-md" onclick="closeEditDeviceModal()"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-[480px] p-4 max-h-[100dvh] overflow-y-auto overscroll-contain">
        <div class="glass-effect rounded-[24px] shadow-2xl overflow-hidden">
            <div class="p-6 flex justify-between items-center border-b border-white/10">
                <h3 class="text-[22px] font-bold text-white">Edit Device</h3>
                <button onclick="closeEditDeviceModal()" class="w-8 h-8 flex items-center justify-center rounded-full bg-white/5 text-slate-400 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2.5"/></svg>
                </button>
            </div>
            <form id="edit-device-form" class="p-8 space-y-5">
                <input type="hidden" name="edit_device_internal_id">
                <!-- Device ID (readonly) -->
                <div class="space-y-2">
                    <label class="block text-[13px] font-bold text-slate-400 uppercase tracking-wider">Device ID</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" stroke-width="2"/></svg>
                        </span>
                        <input type="text" name="edit_device_code" readonly
                               class="w-full bg-white/5 border border-white/10 rounded-xl pl-11 pr-4 py-3.5 text-[15px] text-slate-400 cursor-not-allowed focus:outline-none">
                    </div>
                </div>
                <!-- Name -->
                <div class="space-y-2">
                    <label class="block text-[13px] font-bold text-slate-400 uppercase tracking-wider">Name</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18" stroke-width="2"/></svg>
                        </span>
                        <input type="text" name="edit_name" placeholder="Smart Meter #4521" required
                               class="w-full bg-white/5 border border-white/10 rounded-xl pl-11 pr-4 py-3.5 text-[15px] text-white placeholder:text-slate-600 focus:outline-none focus:border-[#00d4aa] transition-colors">
                    </div>
                </div>
                <!-- Type -->
                <div class="space-y-2">
                    <label class="block text-[13px] font-bold text-slate-400 uppercase tracking-wider">Type</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" stroke-width="2"/></svg>
                        </span>
                        <input type="text" name="edit_type" placeholder="Energy Meter" required
                               class="w-full bg-white/5 border border-white/10 rounded-xl pl-11 pr-4 py-3.5 text-[15px] text-white placeholder:text-slate-600 focus:outline-none focus:border-[#00d4aa] transition-colors">
                    </div>
                </div>
                <!-- IP Address -->
                <div class="space-y-2">
                    <label class="block text-[13px] font-bold text-slate-400 uppercase tracking-wider">IP Address</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9" stroke-width="2"/></svg>
                        </span>
                        <input type="text" name="edit_ip_address" placeholder="192.168.1.1" required
                               class="w-full bg-white/5 border border-white/10 rounded-xl pl-11 pr-4 py-3.5 text-[15px] text-white placeholder:text-slate-600 focus:outline-none focus:border-[#00d4aa] transition-colors">
                    </div>
                </div>
                <!-- Room -->
                <div class="space-y-2">
                    <label class="block text-[13px] font-bold text-slate-400 uppercase tracking-wider">Room</label>
                    <div class="relative">
                        <select name="edit_ruangan_id"
                                class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3.5 text-[15px] text-white focus:outline-none focus:border-[#00d4aa] transition-colors appearance-none cursor-pointer">
                            <option value="">— Select Room —</option>
                        </select>
                        <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="2"/></svg>
                    </div>
                </div>
                <!-- Buttons -->
                <div class="flex gap-4 pt-2">
                    <button type="button" onclick="closeEditDeviceModal()" class="flex-1 py-3.5 bg-white/5 border border-white/10 text-white font-bold rounded-xl hover:bg-white/10 transition-colors">Cancel</button>
                    <button type="submit" class="flex-1 py-3.5 bg-[#00d4aa] text-white font-bold rounded-xl hover:bg-[#00bfa0] transition-colors shadow-lg shadow-[#00d4aa]/20">Update Device</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Device Modal -->
<div id="delete-device-modal" class="fixed inset-0 z-[100] hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-md" onclick="closeDeleteDeviceModal()"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-[420px] p-4">
        <div class="glass-effect rounded-[24px] shadow-2xl p-8">
            <div class="flex justify-between items-start mb-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-red-500/10 flex items-center justify-center text-red-500 border border-red-500/20">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" stroke-width="2"/></svg>
                    </div>
                    <h3 class="text-[20px] font-bold text-white">Delete Device?</h3>
                </div>
                <button onclick="closeDeleteDeviceModal()" class="w-8 h-8 flex items-center justify-center rounded-full bg-white/5 text-slate-400 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2.5"/></svg>
                </button>
            </div>
            <p id="delete-device-message" class="text-[14px] text-slate-400 mb-5">Are you sure you want to delete this device?</p>
            <div class="flex items-start gap-3 p-4 bg-yellow-500/5 border border-yellow-500/20 rounded-xl mb-8">
                <svg class="w-5 h-5 text-yellow-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" stroke-width="2"/></svg>
                <p class="text-[13px] text-yellow-500/80">This action cannot be undone. The device and all associated data will be permanently deleted.</p>
            </div>
            <div class="flex gap-4">
                <button onclick="closeDeleteDeviceModal()" class="flex-1 py-3.5 bg-white/5 border border-white/10 text-white font-bold rounded-xl hover:bg-white/10 transition-colors">Cancel</button>
                <button id="confirm-delete-device-btn" class="flex-1 py-3.5 bg-red-500 text-white font-bold rounded-xl hover:bg-red-600 transition-colors">Delete Device</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    let devicesMap = {};
    let deleteDeviceId = null;

    function openEditDeviceModal(device) {
        const f = document.getElementById('edit-device-form');
        f.edit_device_internal_id.value = device.id ?? '';
        f.edit_device_code.value = device.device_code || ('DEV-' + String(device.id).padStart(3, '0'));
        f.edit_name.value = device.name || '';
        f.edit_type.value = device.type || '';
        f.edit_ip_address.value = device.ip_address || '';
        // populate edit room dropdown then set value
        loadRoomsDropdown('edit').then(() => {
            f.edit_ruangan_id.value = device.ruangan_id || '';
        });
        document.getElementById('edit-device-modal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    function closeEditDeviceModal() {
        document.getElementById('edit-device-modal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function openDeleteDeviceModal(id, name) {
        deleteDeviceId = id;
        document.getElementById('delete-device-message').textContent = `Are you sure you want to delete device "${name}"?`;
        document.getElementById('delete-device-modal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    function closeDeleteDeviceModal() {
        document.getElementById('delete-device-modal').classList.add('hidden');
        document.body.style.overflow = 'auto';
        deleteDeviceId = null;
    }

    function openAddDeviceModal() {
        document.getElementById('add-device-modal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    function closeAddDeviceModal() {
        document.getElementById('add-device-modal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Load rooms into a Room dropdown. mode='add'|'edit'
    async function loadRoomsDropdown(mode) {
        const selName = mode === 'edit' ? 'edit_ruangan_id' : 'ruangan_id';
        const sel = document.querySelector(`select[name="${selName}"]`);
        if (!sel) return;
        // Only populate once (skip if already has options beyond placeholder)
        if (mode !== 'edit' && sel.options.length > 1) return;
        // For edit, always rebuild
        if (mode === 'edit') {
            while (sel.options.length > 1) sel.remove(1);
        }
        try {
            const res = await apiFetch('/ruangan');
            if (!res.ok) return;
            const data = await res.json();
            const rooms = Array.isArray(data) ? data : (data.data || []);
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
                const did = String(d.id);
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
                    <td class="px-6 py-5">
                        <div class="flex items-center gap-2">
                            <button data-edit-id="${did}" class="btn-edit-device flex items-center gap-1.5 px-3 py-1.5 bg-[#00aaff]/10 border border-[#00aaff]/20 text-[#00aaff] rounded-lg text-[12px] font-bold hover:bg-[#00aaff]/20 transition-all">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                Edit
                            </button>
                            <button data-delete-id="${did}" class="btn-delete-device flex items-center gap-1.5 px-3 py-1.5 bg-red-500/10 border border-red-500/20 text-red-500 rounded-lg text-[12px] font-bold hover:bg-red-500/20 transition-all">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                Delete
                            </button>
                        </div>
                    </td>
                </tr>`;
            }).join('');

            document.querySelectorAll('.btn-edit-device').forEach(btn => {
                btn.addEventListener('click', function() {
                    const device = devicesMap[this.dataset.editId];
                    if (device) openEditDeviceModal(device);
                });
            });
            document.querySelectorAll('.btn-delete-device').forEach(btn => {
                btn.addEventListener('click', function() {
                    const device = devicesMap[this.dataset.deleteId];
                    if (device) openDeleteDeviceModal(device.id, device.name);
                });
            });


        } catch (err) {
            tbody.innerHTML = `<tr><td colspan="5" class="px-6 py-12 text-center text-slate-500 text-[14px]">Failed to load devices. Please refresh.</td></tr>`;
        }
    }

    document.getElementById('add-device-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const f = e.target;
        const btn = f.querySelector('button[type="submit"]');
        const orig = btn.textContent;
        btn.disabled = true; btn.textContent = 'Saving...';

        const payload = {
            name:        f.name.value.trim(),
            type:        f.type.value.trim(),
            ip_address:  f.ip_address.value.trim(),
            ruangan_id:  f.ruangan_id.value || null,
        };
        if (f.device_code.value.trim()) payload.device_code = f.device_code.value.trim();

        try {
            const res = await apiFetch('/devices', { method: 'POST', body: JSON.stringify(payload) });
            if (res.ok) {
                closeAddDeviceModal();
                f.reset();
                await loadDevices();
            } else {
                const err = await res.json();
                const msg = err?.errors ? Object.values(err.errors).flat().join('\n') : (err.message || 'Failed to add device');
                alert('Error: ' + msg);
            }
        } catch(err) {
            alert('Network error. Please try again.');
        } finally {
            btn.disabled = false; btn.textContent = orig;
        }
    });

    document.getElementById('edit-device-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const f = e.target;
        const btn = f.querySelector('button[type="submit"]');
        const orig = btn.innerHTML;
        btn.disabled = true; btn.textContent = 'Saving...';
        const id = f.edit_device_internal_id.value;
        const payload = {
            name:       f.edit_name.value.trim(),
            type:       f.edit_type.value.trim(),
            ip_address: f.edit_ip_address.value.trim(),
            ruangan_id: f.edit_ruangan_id.value || null,
        };
        try {
            const res = await apiFetch('/devices/' + id, { method: 'PUT', body: JSON.stringify(payload) });
            if (res.ok) {
                closeEditDeviceModal();
                await loadDevices();
            } else {
                const err = await res.json();
                const msg = err?.errors ? Object.values(err.errors).flat().join('\n') : (err.message || 'Failed to update device');
                alert('Error: ' + msg);
            }
        } catch(err) {
            alert('Network error. Please try again.');
        } finally {
            btn.disabled = false; btn.innerHTML = orig;
        }
    });

    document.getElementById('confirm-delete-device-btn').addEventListener('click', async () => {
        if (!deleteDeviceId) return;
        const btn = document.getElementById('confirm-delete-device-btn');
        btn.disabled = true; btn.textContent = 'Deleting...';
        try {
            const res = await apiFetch('/devices/' + deleteDeviceId, { method: 'DELETE' });
            if (res.ok) {
                closeDeleteDeviceModal();
                await loadDevices();
            } else {
                const err = await res.json();
                alert('Error: ' + (err.message || 'Failed to delete device'));
            }
        } catch(e) {
            alert('Network error. Please try again.');
        } finally {
            btn.disabled = false; btn.textContent = 'Delete Device';
        }
    });

    document.addEventListener('DOMContentLoaded', () => {
        loadDevices();
        loadRoomsDropdown('add');
    });
</script>
@endpush
