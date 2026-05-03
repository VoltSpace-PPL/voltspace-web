@extends('layouts.main')

@section('content')
<div class="mt-2">
    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-[13px] text-slate-400 mb-6">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" stroke-width="2"/></svg>
        <span>&rsaquo;</span>
        <span>Dashboard</span>
        <span>&rsaquo;</span>
        <span class="text-white font-medium">Rooms</span>
    </nav>

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-[32px] font-bold text-white leading-tight">Rooms</h1>
            <p class="text-[14px] text-slate-500 mt-1">Room-level energy monitoring</p>
        </div>
        <button onclick="openModal()" class="flex items-center gap-2 px-5 py-2.5 bg-[#00d4aa] hover:bg-[#00bfa0] text-white rounded-lg font-bold text-[14px] transition-all self-start sm:self-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M12 4v16m8-8H4"/></svg>
            Add Room
        </button>
    </div>

    <!-- Filter -->
    <div class="flex flex-wrap items-center gap-3 mb-8 min-w-0">
        <div class="p-2 bg-[#1e293b] border border-[#334155] rounded-lg text-slate-400 shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L4.293 7.293A1 1 0 014 6.586V4z" stroke-width="2"/></svg>
        </div>
        <div class="relative min-w-0 flex-1 basis-[min(100%,12rem)] sm:basis-auto sm:flex-initial max-w-full">
            <select class="appearance-none bg-[#1e293b] border border-[#334155] text-slate-300 text-[13px] px-4 py-2 pr-10 rounded-lg focus:outline-none cursor-pointer w-full min-w-0 max-w-full">
                <option>All Locations</option>
            </select>
            <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="2"/></svg>
        </div>
    </div>

    <!-- Grid -->
    <div id="rooms-grid" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
        <!-- Will be populated by JS -->
    </div>
</div>

<!-- Modal with Glassmorphism -->
<div id="room-modal" class="fixed inset-0 z-[100] hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-md" onclick="closeModal()"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-[480px] p-4 max-h-[100dvh] overflow-y-auto overscroll-contain">
        <div class="glass-effect rounded-[24px] shadow-2xl overflow-hidden">
            <div class="p-6 flex justify-between items-center border-b border-white/10">
                <h3 class="text-[22px] font-bold text-white">Add New Room</h3>
                <button onclick="closeModal()" class="w-8 h-8 flex items-center justify-center rounded-full bg-white/5 text-slate-400 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2.5"/></svg>
                </button>
            </div>
            
            <form id="room-form" class="p-8 space-y-6">
                <div class="space-y-2">
                    <label class="block text-[13px] font-bold text-slate-400 uppercase tracking-wider">Room ID</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" stroke-width="2"/></svg>
                        </span>
                        <input type="text" name="room_id" placeholder="e.g., R-101" required
                               class="w-full bg-white/5 border border-white/10 rounded-xl pl-11 pr-4 py-3.5 text-[15px] text-white placeholder:text-slate-600 focus:outline-none focus:border-[#00d4aa] transition-colors">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="block text-[13px] font-bold text-slate-400 uppercase tracking-wider">Room Name (EN)</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" stroke-width="2"/></svg>
                            </span>
                            <input type="text" name="name_en" placeholder="Computer Lab 1" required
                                   class="w-full bg-white/5 border border-white/10 rounded-xl pl-11 pr-4 py-3.5 text-[15px] text-white placeholder:text-slate-600 focus:outline-none focus:border-[#00d4aa] transition-colors">
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-[13px] font-bold text-slate-400 uppercase tracking-wider">Room Name (ID)</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" stroke-width="2"/></svg>
                            </span>
                            <input type="text" name="name_id" placeholder="Lab Komputer 1" required
                                   class="w-full bg-white/5 border border-white/10 rounded-xl pl-11 pr-4 py-3.5 text-[15px] text-white placeholder:text-slate-600 focus:outline-none focus:border-[#00d4aa] transition-colors">
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="block text-[13px] font-bold text-slate-400 uppercase tracking-wider">Location</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" stroke-width="2"/></svg>
                            </span>
                            <input type="text" name="building" placeholder="TULT" required
                                   class="w-full bg-white/5 border border-white/10 rounded-xl pl-11 pr-4 py-3.5 text-[15px] text-white placeholder:text-slate-600 focus:outline-none focus:border-[#00d4aa] transition-colors">
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-[13px] font-bold text-slate-400 uppercase tracking-wider">Devices</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" stroke-width="2"/></svg>
                            </span>
                            <input type="number" name="devices" placeholder="24" required
                                   class="w-full bg-white/5 border border-white/10 rounded-xl pl-11 pr-4 py-3.5 text-[15px] text-white placeholder:text-slate-600 focus:outline-none focus:border-[#00d4aa] transition-colors">
                        </div>
                    </div>
                </div>

                <div class="space-y-3">
                    <label class="block text-[13px] font-bold text-slate-400 uppercase tracking-wider">Occupancy Status</label>
                    <div class="flex items-center gap-8">
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="radio" name="status" value="Occupied" checked class="w-4 h-4 accent-[#00d4aa]">
                            <span class="text-[15px] text-slate-400 group-hover:text-white transition-colors">Occupied</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="radio" name="status" value="Empty" class="w-4 h-4 accent-[#00d4aa]">
                            <span class="text-[15px] text-slate-400 group-hover:text-white transition-colors">Empty</span>
                        </label>
                    </div>
                </div>

                <div class="flex gap-4 pt-6">
                    <button type="button" onclick="closeModal()" class="flex-1 py-3.5 bg-white/5 border border-white/10 text-white font-bold rounded-xl hover:bg-white/10 transition-colors">Cancel</button>
                    <button type="submit" class="flex-1 py-3.5 bg-[#00d4aa] text-white font-bold rounded-xl hover:bg-[#00bfa0] transition-colors shadow-lg shadow-[#00d4aa]/20">Add Room</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Room Modal -->
<div id="edit-room-modal" class="fixed inset-0 z-[100] hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-md" onclick="closeEditModal()"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-[480px] p-4">
        <div class="glass-effect rounded-[24px] shadow-2xl overflow-hidden">
            <div class="p-6 flex justify-between items-center border-b border-white/10">
                <h3 class="text-[22px] font-bold text-white">Edit Room</h3>
                <button onclick="closeEditModal()" class="w-8 h-8 flex items-center justify-center rounded-full bg-white/5 text-slate-400 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2.5"/></svg>
                </button>
            </div>

            <form id="edit-room-form" class="p-8 space-y-6">
                <input type="hidden" name="edit_id">

                <!-- Room ID (readonly) -->
                <div class="space-y-2">
                    <label class="block text-[13px] font-bold text-slate-400 uppercase tracking-wider">Room ID</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" stroke-width="2"/></svg>
                        </span>
                        <input type="text" name="edit_room_id" readonly
                               class="w-full bg-white/5 border border-white/10 rounded-xl pl-11 pr-4 py-3.5 text-[15px] text-slate-400 cursor-not-allowed focus:outline-none">
                    </div>
                </div>

                <!-- Room Name EN + ID -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="block text-[13px] font-bold text-slate-400 uppercase tracking-wider">Room Name (EN)</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" stroke-width="2"/></svg>
                            </span>
                            <input type="text" name="edit_name_en" placeholder="Computer Lab 1" required
                                   class="w-full bg-white/5 border border-white/10 rounded-xl pl-11 pr-4 py-3.5 text-[15px] text-white placeholder:text-slate-600 focus:outline-none focus:border-[#00d4aa] transition-colors">
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-[13px] font-bold text-slate-400 uppercase tracking-wider">Room Name (ID)</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" stroke-width="2"/></svg>
                            </span>
                            <input type="text" name="edit_name_id" placeholder="Lab Komputer 1"
                                   class="w-full bg-white/5 border border-white/10 rounded-xl pl-11 pr-4 py-3.5 text-[15px] text-white placeholder:text-slate-600 focus:outline-none focus:border-[#00d4aa] transition-colors">
                        </div>
                    </div>
                </div>

                <!-- Building + Devices -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="block text-[13px] font-bold text-slate-400 uppercase tracking-wider">Location</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" stroke-width="2"/></svg>
                            </span>
                            <input type="text" name="edit_building" placeholder="TULT" required
                                   class="w-full bg-white/5 border border-white/10 rounded-xl pl-11 pr-4 py-3.5 text-[15px] text-white placeholder:text-slate-600 focus:outline-none focus:border-[#00d4aa] transition-colors">
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-[13px] font-bold text-slate-400 uppercase tracking-wider">Devices</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" stroke-width="2"/></svg>
                            </span>
                            <input type="number" name="edit_devices" placeholder="24" min="0"
                                   class="w-full bg-white/5 border border-white/10 rounded-xl pl-11 pr-4 py-3.5 text-[15px] text-white placeholder:text-slate-600 focus:outline-none focus:border-[#00d4aa] transition-colors">
                        </div>
                    </div>
                </div>

                <!-- Occupancy Status -->
                <div class="space-y-3">
                    <label class="block text-[13px] font-bold text-slate-400 uppercase tracking-wider">Occupancy Status</label>
                    <div class="flex items-center gap-8">
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="radio" name="edit_status" value="Occupied" checked class="w-4 h-4 accent-[#00d4aa]">
                            <span class="text-[15px] text-slate-400 group-hover:text-white transition-colors">Occupied</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="radio" name="edit_status" value="Empty" class="w-4 h-4 accent-[#00d4aa]">
                            <span class="text-[15px] text-slate-400 group-hover:text-white transition-colors">Empty</span>
                        </label>
                    </div>
                </div>

                <div class="flex gap-4 pt-2">
                    <button type="button" onclick="closeEditModal()" class="flex-1 py-3.5 bg-white/5 border border-white/10 text-white font-bold rounded-xl hover:bg-white/10 transition-colors">Cancel</button>
                    <button type="submit" class="flex-1 py-3.5 bg-[#00d4aa] text-white font-bold rounded-xl hover:bg-[#00bfa0] transition-colors shadow-lg shadow-[#00d4aa]/20">Update Room</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Room Modal -->
<div id="delete-room-modal" class="fixed inset-0 z-[100] hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-md" onclick="closeDeleteModal()"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-[420px] p-4">
        <div class="glass-effect rounded-[24px] shadow-2xl p-8">
            <div class="flex justify-between items-start mb-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-red-500/10 flex items-center justify-center text-red-500 border border-red-500/20">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" stroke-width="2"/></svg>
                    </div>
                    <h3 class="text-[20px] font-bold text-white">Delete Room?</h3>
                </div>
                <button onclick="closeDeleteModal()" class="w-8 h-8 flex items-center justify-center rounded-full bg-white/5 text-slate-400 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2.5"/></svg>
                </button>
            </div>
            <p id="delete-room-message" class="text-[14px] text-slate-400 mb-5">Are you sure you want to delete this room?</p>
            <div class="flex items-start gap-3 p-4 bg-yellow-500/5 border border-yellow-500/20 rounded-xl mb-8">
                <svg class="w-5 h-5 text-yellow-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" stroke-width="2"/></svg>
                <p class="text-[13px] text-yellow-500/80">This action cannot be undone. The room and all associated data will be permanently deleted.</p>
            </div>
            <div class="flex gap-4">
                <button onclick="closeDeleteModal()" class="flex-1 py-3.5 bg-white/5 border border-white/10 text-white font-bold rounded-xl hover:bg-white/10 transition-colors">Cancel</button>
                <button id="confirm-delete-room-btn" class="flex-1 py-3.5 bg-red-500 text-white font-bold rounded-xl hover:bg-red-600 transition-colors">Delete Room</button>
            </div>
        </div>
    </div>
</div>


@endsection

@push('scripts')


<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    let roomDeviceMap = {};

async function loadRoomDevices() {
    roomDeviceMap = {};

    try {
        const res = await apiFetch('/devices');
        if (!res.ok) return;

        const data = await res.json();
        const devices = Array.isArray(data) ? data : (data.data || []);

        await Promise.all(devices.map(async (device) => {
            if (!device.ruangan_id) return;

            let relay = 'OFF';
            let energy = 0;

            try {
                const statusRes = await apiFetch('/devices/' + device.id + '/status');

                if (statusRes.ok) {
                    const status = await statusRes.json();
                    relay = status.relay || 'OFF';
                    energy = parseFloat(status.energy ?? 0) || 0;
                }
            } catch (e) {}

            roomDeviceMap[device.ruangan_id] = {
                ...device,
                relay: relay,
                energy: energy,
            };
        }));
    } catch (e) {
        console.warn('[Rooms] Could not load room devices:', e);
    }
}
    function escapeHtml(s) {
        return String(s ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    function openModal() { 
        document.getElementById('room-modal').classList.remove('hidden'); 
        document.body.style.overflow = 'hidden';
    }
    function closeModal() { 
        document.getElementById('room-modal').classList.add('hidden'); 
        document.body.style.overflow = 'auto';
    }

    function openEditModal(room) {
        const f = document.getElementById('edit-room-form');
        f.edit_id.value       = room.id ?? '';
        f.edit_room_id.value  = room.id ?? '';
        f.edit_name_en.value  = room.nama_ruangan || '';
        f.edit_name_id.value  = room.nama_ruangan || '';
        f.edit_building.value = room.lokasi || '';
        f.edit_devices.value  = room.kapasitas ?? '';
        const uiStatus = uiStatusFromApi(room.status);
        f.querySelectorAll('input[name="edit_status"]').forEach(r => r.checked = r.value === uiStatus);
        document.getElementById('edit-room-modal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    function closeEditModal() {
        document.getElementById('edit-room-modal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    let deleteRoomId = null;
    function openDeleteModal(id, name) {
        deleteRoomId = id;
        document.getElementById('delete-room-message').textContent = `Are you sure you want to delete room "${name}"?`;
        document.getElementById('delete-room-modal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    function closeDeleteModal() {
        document.getElementById('delete-room-modal').classList.add('hidden');
        document.body.style.overflow = 'auto';
        deleteRoomId = null;
    }

    document.getElementById('confirm-delete-room-btn').addEventListener('click', async () => {
        if (!deleteRoomId) return;
        const btn = document.getElementById('confirm-delete-room-btn');
        btn.disabled = true;
        btn.textContent = 'Deleting...';
        try {
            const res = await apiFetch('/ruangan/' + deleteRoomId, { method: 'DELETE' });
            if (res.ok) {
                closeDeleteModal();
                await loadRooms();
            } else {
                const err = await res.json();
                alert('Error: ' + (err.message || 'Failed to delete room'));
            }
        } catch(e) {
            alert('Network error. Please try again.');
        } finally {
            btn.disabled = false;
            btn.textContent = 'Delete Room';
        }
    });

    document.getElementById('edit-room-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const f = e.target;
        const btn = f.querySelector('button[type="submit"]');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.textContent = 'Saving...';
        const id = f.edit_id.value;
        const rawKapasitas = parseInt(f.edit_devices.value, 10);
        const payload = {
            nama_ruangan: f.edit_name_en.value.trim() || f.edit_name_id.value.trim(),
            lokasi:       f.edit_building.value.trim(),
            status:       apiStatusFromUi(f.querySelector('input[name="edit_status"]:checked').value)
        };
        if (!isNaN(rawKapasitas)) payload.kapasitas = rawKapasitas;
        try {
            const res = await apiFetch('/ruangan/' + id, { method: 'PUT', body: JSON.stringify(payload) });
            if (res.ok) {
                closeEditModal();
                await loadRooms();
            } else {
                const err = await res.json();
                alert('Error: ' + (err.message || 'Failed to update room'));
            }
        } catch(e) {
            alert('Network error. Please try again.');
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    });

    // Global rooms store - keyed by id
    let roomsMap = {};

    function uiStatusFromApi(status) {
        const s = String(status || '').toLowerCase();
        if (s === 'digunakan') return 'Occupied';
        if (s === 'dipesan') return 'Booked';
        return 'Empty';
    }

    function apiStatusFromUi(status) {
        const s = String(status || '').toLowerCase();
        if (s === 'occupied') return 'digunakan';
        if (s === 'booked') return 'dipesan';
        return 'tersedia';
    }

    // Trend data: { trendMap: {roomId: [6 values]}, trendLabels: [6 labels] }
    let trendMap = {};
    let trendLabels = [];

    async function loadRoomTrends() {
        const now = new Date();
        const currentYear = now.getFullYear();
        const promises = [];
        const labels = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        for (let m = 1; m <= 12; m++) {
            promises.push(
                apiFetch(`/dashboard/rooms?month=${m}&year=${currentYear}`)
                    .then(r => r.ok ? r.json() : { rooms: [] })
                    .catch(() => ({ rooms: [] }))
            );
        }
        trendLabels = labels;
        const results = await Promise.all(promises);
        trendMap = {};
        results.forEach((data, idx) => {
            (data.rooms || []).forEach(r => {
                if (!trendMap[r.id]) trendMap[r.id] = new Array(12).fill(0);
                trendMap[r.id][idx] = parseFloat(r.consumption_kwh) || 0;
            });
        });
    }

    let consumptionMap = {};

    async function loadConsumption() {
        try {
            const res = await apiFetch('/dashboard/rooms');
            if (!res.ok) return;
            const data = await res.json();
            const rooms = data.rooms || [];
            consumptionMap = {};
            rooms.forEach(r => {
                consumptionMap[r.id] = r.consumption_kwh ?? 0;
            });
        } catch (e) {
            console.warn('[Rooms] Could not load consumption data:', e);
        }
    }

    function fmtKwh(val) {
        const n = parseFloat(val) || 0;
        return new Intl.NumberFormat('id-ID', {
            minimumFractionDigits: 3,
            maximumFractionDigits: 3
        }).format(n);
    }

    async function loadRooms() {
        const grid = document.getElementById('rooms-grid');
        grid.innerHTML = '<div class="col-span-3 text-center text-slate-500 py-20">Loading rooms...</div>';

        // Load consumption + 6-month trend in parallel
        await Promise.all([
            loadConsumption(),
            loadRoomTrends(),
            loadRoomDevices()
        ]);

        try {
            const res = await apiFetch('/ruangan');
            if (!res.ok) throw new Error('API error');
            const data = await res.json();
            const rooms = Array.isArray(data) ? data : (data.data || []);

            roomsMap = {};
            rooms.forEach(r => { roomsMap[r.id] = r; });

            if (rooms.length === 0) {
                grid.innerHTML = '<div class="col-span-3 text-center text-slate-500 py-20">No rooms found. Click "+ Add Room" to add one.</div>';
                return;
            }

            grid.innerHTML = rooms.map(room => {
                const displayStatus = uiStatusFromApi(room.status);
                const isOccupied = displayStatus === 'Occupied';
                const isPowerOn = roomDeviceMap[room.id]?.relay === 'ON';
                const kapasitas = room.kapasitas ?? 0;
                const title = escapeHtml(room.nama_ruangan || 'Unnamed Room');
                const subtitle = escapeHtml([room.id, room.lokasi].filter(Boolean).join(' \u00b7 '));
                const roomId = escapeHtml(String(room.id));
                const consumption = roomDeviceMap[room.id]?.energy ?? 0;
                const consumptionDisplay = consumption !== null
                    ? `<span class="text-[20px] font-extrabold text-white">${fmtKwh(consumption)} <span class="text-[13px] text-slate-400 font-medium">kWh</span></span>`
                    : `<span class="text-[14px] text-slate-600 italic">No data</span>`;
                const safeId = String(room.id).replace(/[^a-zA-Z0-9]/g, '-');

                return `
                <div class="bg-[#1e293b] border border-[#334155] rounded-[24px] p-6 transition-all hover:border-slate-500 group shadow-lg min-w-0">
                    <div class="flex justify-between items-start gap-3 mb-5 min-w-0">
                        <div class="flex items-center gap-4 min-w-0 flex-1">
                            <div class="w-12 h-12 rounded-xl bg-[#00aaff]/10 flex items-center justify-center text-[#00aaff] border border-[#00aaff]/20 shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" stroke-width="2"/></svg>
                            </div>
                            <div class="min-w-0">
                                <h4 class="text-[17px] font-bold text-white leading-none mb-1.5 break-words">${title}</h4>
                                <p class="text-[13px] text-slate-500 font-medium break-words">${subtitle}</p>
                            </div>
                        </div>
                        <span class="shrink-0 px-3.5 py-1.5 rounded-lg text-[10px] font-bold uppercase tracking-widest ${isOccupied ? 'bg-orange-500/10 text-orange-500 border border-orange-500/20' : 'bg-emerald-500/10 text-emerald-500 border border-emerald-500/20'}">
                            ${displayStatus}
                        </span>
                    </div>

                    <!-- Consumption highlight + chart -->
                    <div class="rounded-xl p-4 mb-5" style="background:#0f172a; border:1px solid #1e293b;">
                        <div class="flex items-start justify-between mb-3">
                            <div>
                                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Annual Consumption ({{ now()->year }})</p>
                                ${consumptionDisplay}
                            </div>
                            <div class="flex items-center gap-1.5">
                                <div class="w-2 h-2 rounded-full bg-[#00d4aa] animate-pulse"></div>
                                <span class="text-[11px] text-[#00d4aa] font-bold">Live</span>
                            </div>
                        </div>
                        <div style="height:150px; position:relative;">
                            <canvas id="chart-${safeId}" style="width:100%; height:150px;"></canvas>
                        </div>
                    </div>

                    <!-- Info rows -->
                    <div class="grid grid-cols-2 gap-3 mb-5">
                        <div class="rounded-xl p-3" style="background:#162032; border:1px solid #1e293b;">
                            <p class="text-[11px] text-slate-500 uppercase tracking-wider font-bold mb-1">Devices</p>
                            <p class="text-[20px] font-extrabold text-white">${kapasitas}</p>
                        </div>
                        <div class="rounded-xl p-3" style="background:#162032; border:1px solid #1e293b;">
                            <p class="text-[11px] text-slate-500 uppercase tracking-wider font-bold mb-1">Location</p>
                            <p class="text-[15px] font-bold text-white truncate">${escapeHtml(room.lokasi || '-')}</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-[#0f172a] rounded-xl border border-[#334155] mb-5">
                        <span class="text-[13px] font-bold text-slate-400">Power</span>
                        <div class="flex items-center gap-3">
                            <label class="switch">
                                <input type="checkbox" ${isPowerOn ? 'checked' : ''} onchange="togglePower('${roomId}', this)">
                                <span class="slider"></span>
                            </label>
                            <span class="text-[13px] font-bold w-8 ${isPowerOn ? 'text-emerald-500' : 'text-slate-600'}">${isPowerOn ? 'ON' : 'OFF'}</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <button data-edit-id="${roomId}" class="btn-edit-room flex items-center justify-center gap-2 py-2.5 bg-[#00aaff]/10 border border-[#00aaff]/20 text-[#00aaff] rounded-xl text-[13px] font-bold hover:bg-[#00aaff]/20 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            Edit
                        </button>
                        <button data-delete-id="${roomId}" class="btn-delete-room flex items-center justify-center gap-2 py-2.5 bg-red-500/10 border border-red-500/20 text-red-500 rounded-xl text-[13px] font-bold hover:bg-red-500/20 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            Delete
                        </button>
                    </div>
                    <button class="w-full py-3.5 bg-gradient-to-r from-[#6366f1] to-[#8b5cf6] text-white rounded-xl text-[13px] font-bold flex items-center justify-center gap-2 shadow-lg shadow-indigo-500/20 hover:brightness-110 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Manage Schedule
                    </button>
                </div>
                `;
            }).join('');

            // Render sparkline charts
            if (window.Chart) {
                rooms.forEach(room => {
                    const safeId = String(room.id).replace(/[^a-zA-Z0-9]/g, '-');
                    const canvas = document.getElementById('chart-' + safeId);
                    if (!canvas) return;
                    const trendData = trendMap[room.id] || new Array(12).fill(0);

                    const device = roomDeviceMap[room.id];
                    if (device && device.energy !== undefined) {
                        const currentMonthIndex = new Date().getMonth();
                        trendData[currentMonthIndex] = parseFloat(device.energy) || 0;
                    }
                    const ctx = canvas.getContext('2d');
                    const grad = ctx.createLinearGradient(0, 0, 0, 150);
                    grad.addColorStop(0, 'rgba(0,212,170,0.35)');
                    grad.addColorStop(1, 'rgba(0,212,170,0.01)');
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: trendLabels,
                            datasets: [{ data: trendData, borderColor: '#00d4aa', borderWidth: 2, backgroundColor: grad, fill: true, tension: 0.4, pointRadius: 3, pointBackgroundColor: '#00d4aa', pointBorderColor: '#0f172a', pointBorderWidth: 1.5, pointHoverRadius: 5 }]
                        },
                        options: {
                            responsive: true, maintainAspectRatio: false,
                            plugins: { legend: { display: false }, tooltip: { backgroundColor: '#1e293b', borderColor: '#334155', borderWidth: 1, titleColor: '#94a3b8', bodyColor: '#fff', callbacks: { label: c => ' ' + fmtKwh(c.parsed.y) + ' kWh' } } },
                            scales: {
                                x: { grid: { display: false }, border: { display: false }, ticks: { color: '#475569', font: { size: 10 } } },
                                y: { display: false, beginAtZero: true }
                            }
                        }
                    });
                });
            }

            // Attach event listeners after rendering
            document.querySelectorAll('.btn-edit-room').forEach(btn => {
                btn.addEventListener('click', function() {
                    const room = roomsMap[this.dataset.editId];
                    if (room) openEditModal(room);
                });
            });
            document.querySelectorAll('.btn-delete-room').forEach(btn => {
                btn.addEventListener('click', function() {
                    const room = roomsMap[this.dataset.deleteId];
                    if (room) openDeleteModal(room.id, room.nama_ruangan);
                });
            });

        } catch (err) {
            grid.innerHTML = '<div class="col-span-3 text-center text-slate-500 py-20">Failed to load rooms. Make sure you are logged in.</div>';
        }
    }

        async function togglePower(id, checkbox) {
        const device = roomDeviceMap[id];

        if (!device) {
            checkbox.checked = !checkbox.checked;
            alert('Belum ada device IoT untuk room ini.');
            return;
        }

        const aksi = checkbox.checked ? 'on' : 'off';
        const oldChecked = !checkbox.checked;
        const label = checkbox.parentElement.nextElementSibling;

        try {
            const res = await apiFetch('/devices/toggle', {
                method: 'POST',
                body: JSON.stringify({
                    device_id: device.id,
                    aksi: aksi,
                }),
            });

            if (!res.ok) {
                checkbox.checked = oldChecked;
                alert('Gagal mengontrol device IoT.');
                return;
            }

            label.textContent = checkbox.checked ? 'ON' : 'OFF';
            label.className = `text-[13px] font-bold w-8 ${checkbox.checked ? 'text-emerald-500' : 'text-slate-600'}`;

            roomDeviceMap[id].relay = checkbox.checked ? 'ON' : 'OFF';

        } catch (e) {
            checkbox.checked = oldChecked;
            alert('Device IoT tidak terhubung.');
        }
    }

    document.getElementById('room-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const f = e.target;
        const btn = f.querySelector('button[type="submit"]');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = 'Processing...';

        const payload = {
            nama_ruangan: f.name_id.value.trim() || f.name_en.value.trim(),
            lokasi: f.building.value.trim(),
            kapasitas: parseInt(f.devices.value, 10) || 0,
            status: apiStatusFromUi(f.querySelector('input[name="status"]:checked').value)
        };
        if (f.room_id.value.trim()) payload.id = f.room_id.value.trim();

        try {
            const res = await apiFetch('/ruangan', {method:'POST', body: JSON.stringify(payload)});
            if (res.ok) {
                closeModal();
                f.reset();
                await loadRooms();
            } else {
                const err = await res.json();
                const msg = err?.errors ? Object.values(err.errors).flat().join('\n') : (err.message || 'Failed to add room');
                alert('Error: ' + msg);
            }
        } catch(err) {
            alert('Network error. Please try again.');
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    });

    document.addEventListener('DOMContentLoaded', loadRooms);
</script>
@endpush


