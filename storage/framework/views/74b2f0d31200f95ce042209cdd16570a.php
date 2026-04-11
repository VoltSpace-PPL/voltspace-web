<?php $__env->startSection('content'); ?>
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
                <option>All Buildings</option>
            </select>
            <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="2"/></svg>
        </div>
    </div>

    <!-- Grid - Updated to 3 columns to make cards larger as per Photo 5 -->
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
                        <label class="block text-[13px] font-bold text-slate-400 uppercase tracking-wider">Building</label>
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

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
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

    // Default rooms data to show immediately as requested
    const DEFAULT_ROOMS = [
        { id: 'R-101', nama_ruangan: 'Computer Lab 1', lokasi: 'TULT', consumption: 245, device: 24, status: 'Occupied', power: true },
        { id: 'R-102', nama_ruangan: 'Lecture Hall A', lokasi: 'TULT', consumption: 189, device: 12, status: 'Empty', power: true },
        { id: 'R-201', nama_ruangan: 'Reading Room', lokasi: 'GKU', consumption: 156, device: 18, status: 'Occupied', power: false },
        { id: 'R-301', nama_ruangan: 'Meeting Room', lokasi: 'Cacuk', consumption: 98, device: 8, status: 'Empty', power: false }
    ];

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

    async function loadRooms() {
        const grid = document.getElementById('rooms-grid');
        let rooms = [...DEFAULT_ROOMS];
        
        try {
            const res = await apiFetch('/ruangan');
            const data = await res.json();
            const apiRooms = Array.isArray(data) ? data : (data.data || []);
            if (apiRooms.length > 0) {
                rooms = apiRooms;
            }
        } catch (err) {
            console.warn('Backend logic skipped or unreachable, using default display data.');
        }

        grid.innerHTML = rooms.map(room => {
            const displayStatus = uiStatusFromApi(room.status);
            const isOccupied = displayStatus === 'Occupied';
            const isPowerOn = room.power === 'ON' || room.power === 1 || room.power === true;
            const consumption = room.consumption || 0;
            const devices = room.device ?? room.kapasitas ?? room.devices ?? 0;
            const title = room.nama_ruangan || room.name_en || room.name_id || 'Unnamed Room';
            const subParts = [];
            if (room.id != null && room.id !== '') subParts.push(String(room.id));
            else if (room.room_id) subParts.push(String(room.room_id));
            if (room.lokasi) subParts.push(String(room.lokasi));
            const subtitle = escapeHtml(subParts.join(' · '));

            return `
            <div class="bg-[#1e293b] border border-[#334155] rounded-[24px] p-6 transition-all hover:border-slate-500 group shadow-lg min-w-0">
                <div class="flex justify-between items-start gap-3 mb-6 min-w-0">
                    <div class="flex items-center gap-4 min-w-0 flex-1">
                        <div class="w-12 h-12 rounded-xl bg-[#00aaff]/10 flex items-center justify-center text-[#00aaff] border border-[#00aaff]/20 shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" stroke-width="2"/></svg>
                        </div>
                        <div class="min-w-0">
                            <h4 class="text-[17px] font-bold text-white leading-none mb-1.5 break-words">${escapeHtml(title)}</h4>
                            <p class="text-[13px] text-slate-500 font-medium break-words">${subtitle}</p>
                        </div>
                    </div>
                    <span class="shrink-0 px-3.5 py-1.5 rounded-lg text-[10px] font-bold uppercase tracking-widest ${isOccupied ? 'bg-orange-500/10 text-orange-500 border border-orange-500/20' : 'bg-emerald-500/10 text-emerald-500 border border-emerald-500/20'}">
                        ${displayStatus}
                    </span>
                </div>

                <div class="space-y-4 mb-8">
                    <div class="flex justify-between items-center">
                        <span class="text-[14px] text-slate-400 font-medium">Consumption</span>
                        <span class="text-[17px] font-bold text-white">${consumption} <span class="text-[12px] text-slate-500 font-medium ml-0.5">kWh</span></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-2 text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" stroke-width="2"/></svg>
                            <span class="text-[14px] font-medium">Devices</span>
                        </div>
                        <span class="text-[17px] font-bold text-white">${devices}</span>
                    </div>
                </div>

                <div class="flex items-center justify-between p-4 bg-[#0f172a] rounded-xl border border-[#334155] mb-6">
                    <span class="text-[13px] font-bold text-slate-400">Power</span>
                    <div class="flex items-center gap-3">
                        <label class="switch">
                            <input type="checkbox" ${isPowerOn ? 'checked' : ''} onchange="togglePower(${JSON.stringify(String(room.id))}, this.checked)">
                            <span class="slider"></span>
                        </label>
                        <span class="text-[13px] font-bold w-8 ${isPowerOn ? 'text-emerald-500' : 'text-slate-600'}">${isPowerOn ? 'ON' : 'OFF'}</span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3 mb-3">
                    <button class="flex items-center justify-center gap-2 py-2.5 bg-[#00aaff]/10 border border-[#00aaff]/20 text-[#00aaff] rounded-xl text-[13px] font-bold hover:bg-[#00aaff]/20 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                        Edit
                    </button>
                    <button class="flex items-center justify-center gap-2 py-2.5 bg-red-500/10 border border-red-500/20 text-red-500 rounded-xl text-[13px] font-bold hover:bg-red-500/20 transition-all">
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
    }

    async function togglePower(id, state) {
        console.info('Power toggle UI-only:', id, state);
        await loadRooms();
    }

    document.getElementById('room-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const f = e.target;
        const btn = f.querySelector('button[type="submit"]');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = 'Processing...';

        const payload = {
            id: f.room_id.value.trim(),
            nama_ruangan: f.name_id.value || f.name_en.value,
            lokasi: f.building.value,
            device: parseInt(f.devices.value, 10),
            status: apiStatusFromUi(f.querySelector('input[name="status"]:checked').value)
        };

        try {
            const res = await apiFetch('/ruangan', {method:'POST', body: JSON.stringify(payload)});
            if(res.ok){ 
                closeModal(); 
                f.reset(); 
                await loadRooms(); 
            } else {
                const err = await res.json();
                const validationMessage = err?.errors ? Object.values(err.errors).flat().join('\n') : null;
                alert('Error: ' + (validationMessage || err.message || 'Failed to add room'));
            }
        } catch(err) {
            console.warn('Backend unavailable, simulating success.');
            closeModal();
            f.reset();
            // In a real scenario we'd push to DEFAULT_ROOMS for demo
            alert('Room added (Simulated)!');
            await loadRooms();
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    });

    document.addEventListener('DOMContentLoaded', loadRooms);
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.main', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Telkom University\Materi Kuliah Semester 6\Proyek Perangkat Lunak\project_ppl-main\backend\resources\views\rooms\index.blade.php ENDPATH**/ ?>