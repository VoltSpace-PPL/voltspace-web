@extends('layouts.main')

@section('content')
<div class="mt-2">
    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-[13px] text-slate-400 mb-6">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" stroke-width="2"/></svg>
        <span>&rsaquo;</span>
        <span>Dashboard</span>
        <span>&rsaquo;</span>
        <span class="text-white font-medium">Electricity Schedule</span>
    </nav>

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-[32px] font-bold text-white leading-tight">Electricity Schedule</h1>
            <p class="text-[14px] text-slate-500 mt-1">Automate electricity control with schedules</p>
        </div>
        <button onclick="openAddScheduleModal()" class="flex items-center gap-2 px-5 py-2.5 bg-[#00d4aa] hover:bg-[#00bfa0] text-white rounded-lg font-bold text-[14px] transition-all self-start sm:self-auto shadow-lg shadow-[#00d4aa]/20">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Add Schedule
        </button>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
        <div class="bg-[#1e293b] border border-[#334155] rounded-[16px] p-5">
            <h3 class="text-[13px] font-bold text-slate-400 mb-2">Total Schedules</h3>
            <div class="text-[32px] font-bold text-white leading-none" id="stat-total">0</div>
        </div>
        <div class="bg-[#1e293b] border border-[#334155] rounded-[16px] p-5">
            <h3 class="text-[13px] font-bold text-slate-400 mb-2">Active</h3>
            <div class="text-[32px] font-bold text-[#00d4aa] leading-none" id="stat-active">0</div>
        </div>
        <div class="bg-[#1e293b] border border-[#334155] rounded-[16px] p-5">
            <h3 class="text-[13px] font-bold text-slate-400 mb-2">Rooms Scheduled</h3>
            <div class="text-[32px] font-bold text-[#00aaff] leading-none" id="stat-rooms">0</div>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-[#1e293b] border border-[#334155] rounded-[24px] overflow-hidden shadow-2xl">
        <div class="p-6 border-b border-[#334155] flex items-center gap-3">
            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2"/></svg>
            <h2 class="text-[16px] font-bold text-white">Weekly Schedule</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#0f172a] border-b border-[#334155]">
                        <th class="px-6 py-5 text-[11px] font-bold text-slate-500 uppercase tracking-widest">ID</th>
                        <th class="px-6 py-5 text-[11px] font-bold text-slate-500 uppercase tracking-widest">Room</th>
                        <th class="px-6 py-5 text-[11px] font-bold text-slate-500 uppercase tracking-widest w-1/3">Day</th>
                        <th class="px-6 py-5 text-[11px] font-bold text-slate-500 uppercase tracking-widest">Time</th>
                        <th class="px-6 py-5 text-[11px] font-bold text-slate-500 uppercase tracking-widest text-center">Action</th>
                        <th class="px-6 py-5 text-[11px] font-bold text-slate-500 uppercase tracking-widest text-center">Status</th>
                        <th class="px-6 py-5 text-[11px] font-bold text-slate-500 uppercase tracking-widest text-right">Controls</th>
                    </tr>
                </thead>
                <tbody id="schedules-table-body" class="divide-y divide-[#334155]">
                    <tr>
                        <td colspan="7" class="px-6 py-16 text-center text-slate-500">
                            <div class="flex flex-col items-center gap-3">
                                <svg class="w-10 h-10 text-slate-600 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" stroke-width="2"/></svg>
                                <span class="text-[14px]">Loading schedules...</span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Schedule Modal -->
<div id="add-schedule-modal" class="fixed inset-0 z-[100] hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-md" onclick="closeAddScheduleModal()"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-[560px] p-4 max-h-[100dvh] overflow-y-auto overscroll-contain custom-scrollbar">
        <div class="glass-effect rounded-[24px] shadow-2xl overflow-hidden">
            <div class="p-6 flex justify-between items-center border-b border-white/10">
                <h3 class="text-[22px] font-bold text-white">Add New Schedule</h3>
                <button onclick="closeAddScheduleModal()" class="w-8 h-8 flex items-center justify-center rounded-full bg-white/5 text-slate-400 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" stroke-width="2.5"/></svg>
                </button>
            </div>
            <form id="add-schedule-form" class="p-8 space-y-6">
                <!-- Select Room -->
                <div class="space-y-2">
                    <label class="block text-[13px] font-bold text-slate-400 uppercase tracking-wider">Select Room</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" stroke-width="2"/></svg>
                        </span>
                        <select name="ruangan_id" required
                                class="w-full bg-white/5 border border-white/10 rounded-xl pl-11 pr-10 py-3.5 text-[14px] text-white focus:outline-none focus:border-[#00d4aa] transition-colors appearance-none cursor-pointer">
                            <option value="" class="bg-[#1e293b]">— Select Room —</option>
                        </select>
                        <svg class="absolute right-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" stroke-width="2"/></svg>
                    </div>
                </div>

                <!-- Select Days -->
                <div class="space-y-2">
                    <label class="block text-[13px] font-bold text-slate-400 uppercase tracking-wider flex items-center gap-2">
                        Select Days <span class="text-[10px] text-slate-500 lowercase font-normal tracking-normal">(Multiple selection allowed)</span>
                    </label>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2" id="add-days-container">
                        <button type="button" data-day="monday" class="day-btn py-2 rounded-lg border border-white/10 text-[13px] font-medium text-slate-400 hover:bg-white/5 transition-colors">Monday</button>
                        <button type="button" data-day="tuesday" class="day-btn py-2 rounded-lg border border-white/10 text-[13px] font-medium text-slate-400 hover:bg-white/5 transition-colors">Tuesday</button>
                        <button type="button" data-day="wednesday" class="day-btn py-2 rounded-lg border border-white/10 text-[13px] font-medium text-slate-400 hover:bg-white/5 transition-colors">Wednesday</button>
                        <button type="button" data-day="thursday" class="day-btn py-2 rounded-lg border border-white/10 text-[13px] font-medium text-slate-400 hover:bg-white/5 transition-colors">Thursday</button>
                        <button type="button" data-day="friday" class="day-btn py-2 rounded-lg border border-white/10 text-[13px] font-medium text-slate-400 hover:bg-white/5 transition-colors">Friday</button>
                        <button type="button" data-day="saturday" class="day-btn py-2 rounded-lg border border-white/10 text-[13px] font-medium text-slate-400 hover:bg-white/5 transition-colors">Saturday</button>
                        <button type="button" data-day="sunday" class="day-btn py-2 rounded-lg border border-white/10 text-[13px] font-medium text-slate-400 hover:bg-white/5 transition-colors">Sunday</button>
                    </div>
                    <input type="hidden" name="selected_days" id="add-selected-days">
                </div>

                <!-- Time -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="block text-[13px] font-bold text-slate-400 uppercase tracking-wider">Start Time</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2"/></svg>
                            </span>
                            <input type="time" name="start_time" required
                                   class="w-full bg-white/5 border border-white/10 rounded-xl pl-11 pr-4 py-3.5 text-[14px] text-white focus:outline-none focus:border-[#00d4aa] transition-colors [color-scheme:dark]">
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-[13px] font-bold text-slate-400 uppercase tracking-wider">End Time</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2"/></svg>
                            </span>
                            <input type="time" name="end_time" required
                                   class="w-full bg-white/5 border border-white/10 rounded-xl pl-11 pr-4 py-3.5 text-[14px] text-white focus:outline-none focus:border-[#00d4aa] transition-colors [color-scheme:dark]">
                        </div>
                    </div>
                </div>

                <!-- Automation Action -->
                <div class="space-y-2">
                    <label class="block text-[13px] font-bold text-slate-400 uppercase tracking-wider">Automation Action</label>
                    <div class="relative">
                        <select name="automation_action" required
                                class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3.5 text-[14px] text-white focus:outline-none focus:border-[#00d4aa] transition-colors appearance-none cursor-pointer">
                            <option value="on" class="bg-[#1e293b]">⚡ Turn ON Automatically</option>
                            <option value="off" class="bg-[#1e293b]">🔌 Turn OFF Automatically</option>
                        </select>
                        <svg class="absolute right-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" stroke-width="2"/></svg>
                    </div>
                </div>

                <!-- Schedule Status -->
                <div class="space-y-2">
                    <label class="block text-[13px] font-bold text-slate-400 uppercase tracking-wider mb-3">Schedule Status</label>
                    <div class="flex items-center gap-6">
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <div class="relative flex items-center justify-center w-5 h-5 rounded-full border-2 border-[#00d4aa] bg-transparent group-hover:bg-[#00d4aa]/10 transition-colors">
                                <input type="radio" name="schedule_status" value="active" class="peer sr-only" checked>
                                <div class="w-2.5 h-2.5 rounded-full bg-[#00d4aa] opacity-0 peer-checked:opacity-100 transition-opacity"></div>
                            </div>
                            <span class="text-[14px] text-white group-hover:text-[#00d4aa] transition-colors font-medium flex items-center gap-1.5"><svg class="w-3.5 h-3.5 text-[#00d4aa]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg> Active (Running)</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <div class="relative flex items-center justify-center w-5 h-5 rounded-full border-2 border-slate-500 bg-transparent group-hover:bg-slate-500/10 transition-colors">
                                <input type="radio" name="schedule_status" value="inactive" class="peer sr-only">
                                <div class="w-2.5 h-2.5 rounded-full bg-slate-400 opacity-0 peer-checked:opacity-100 transition-opacity"></div>
                            </div>
                            <span class="text-[14px] text-slate-400 group-hover:text-white transition-colors font-medium flex items-center gap-1.5"><svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> Inactive (Paused)</span>
                        </label>
                    </div>
                </div>

                <!-- Note -->
                <div class="bg-[#00aaff]/10 border border-[#00aaff]/20 rounded-xl p-4 flex gap-3">
                    <svg class="w-5 h-5 text-[#00aaff] shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2"/></svg>
                    <p class="text-[13px] text-[#00aaff]/80 leading-relaxed"><strong class="text-[#00aaff]">Note:</strong> The system will automatically control electricity based on this schedule. Make sure the times are correct.</p>
                </div>

                <!-- Buttons -->
                <div class="flex gap-4 pt-2">
                    <button type="button" onclick="closeAddScheduleModal()" class="flex-1 py-3.5 bg-white/5 border border-white/10 text-white font-bold rounded-xl hover:bg-white/10 transition-colors">Cancel</button>
                    <button type="submit" class="flex-1 py-3.5 bg-[#00d4aa] text-white font-bold rounded-xl hover:bg-[#00bfa0] transition-colors shadow-lg shadow-[#00d4aa]/20">Create Schedule</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Schedule Modal -->
<div id="edit-schedule-modal" class="fixed inset-0 z-[100] hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-md" onclick="closeEditScheduleModal()"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-[560px] p-4 max-h-[100dvh] overflow-y-auto overscroll-contain custom-scrollbar">
        <div class="glass-effect rounded-[24px] shadow-2xl overflow-hidden">
            <div class="p-6 flex justify-between items-center border-b border-white/10">
                <h3 class="text-[22px] font-bold text-white">Edit Schedule</h3>
                <button onclick="closeEditScheduleModal()" class="w-8 h-8 flex items-center justify-center rounded-full bg-white/5 text-slate-400 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" stroke-width="2.5"/></svg>
                </button>
            </div>
            <form id="edit-schedule-form" class="p-8 space-y-6">
                <input type="hidden" name="edit_id">
                
                <!-- Select Room -->
                <div class="space-y-2">
                    <label class="block text-[13px] font-bold text-slate-400 uppercase tracking-wider">Select Room</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" stroke-width="2"/></svg>
                        </span>
                        <select name="edit_ruangan_id" required
                                class="w-full bg-white/5 border border-white/10 rounded-xl pl-11 pr-10 py-3.5 text-[14px] text-white focus:outline-none focus:border-[#00d4aa] transition-colors appearance-none cursor-pointer">
                            <option value="" class="bg-[#1e293b]">— Select Room —</option>
                        </select>
                        <svg class="absolute right-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" stroke-width="2"/></svg>
                    </div>
                </div>

                <!-- Select Days -->
                <div class="space-y-2">
                    <label class="block text-[13px] font-bold text-slate-400 uppercase tracking-wider flex items-center gap-2">
                        Select Days <span class="text-[10px] text-slate-500 lowercase font-normal tracking-normal">(Multiple selection allowed)</span>
                    </label>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2" id="edit-days-container">
                        <button type="button" data-day="monday" class="day-btn py-2 rounded-lg border border-white/10 text-[13px] font-medium text-slate-400 hover:bg-white/5 transition-colors">Monday</button>
                        <button type="button" data-day="tuesday" class="day-btn py-2 rounded-lg border border-white/10 text-[13px] font-medium text-slate-400 hover:bg-white/5 transition-colors">Tuesday</button>
                        <button type="button" data-day="wednesday" class="day-btn py-2 rounded-lg border border-white/10 text-[13px] font-medium text-slate-400 hover:bg-white/5 transition-colors">Wednesday</button>
                        <button type="button" data-day="thursday" class="day-btn py-2 rounded-lg border border-white/10 text-[13px] font-medium text-slate-400 hover:bg-white/5 transition-colors">Thursday</button>
                        <button type="button" data-day="friday" class="day-btn py-2 rounded-lg border border-white/10 text-[13px] font-medium text-slate-400 hover:bg-white/5 transition-colors">Friday</button>
                        <button type="button" data-day="saturday" class="day-btn py-2 rounded-lg border border-white/10 text-[13px] font-medium text-slate-400 hover:bg-white/5 transition-colors">Saturday</button>
                        <button type="button" data-day="sunday" class="day-btn py-2 rounded-lg border border-white/10 text-[13px] font-medium text-slate-400 hover:bg-white/5 transition-colors">Sunday</button>
                    </div>
                    <input type="hidden" name="edit_selected_days" id="edit-selected-days">
                </div>

                <!-- Time -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="block text-[13px] font-bold text-slate-400 uppercase tracking-wider">Start Time</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2"/></svg>
                            </span>
                            <input type="time" name="edit_start_time" required
                                   class="w-full bg-white/5 border border-white/10 rounded-xl pl-11 pr-4 py-3.5 text-[14px] text-white focus:outline-none focus:border-[#00d4aa] transition-colors [color-scheme:dark]">
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-[13px] font-bold text-slate-400 uppercase tracking-wider">End Time</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2"/></svg>
                            </span>
                            <input type="time" name="edit_end_time" required
                                   class="w-full bg-white/5 border border-white/10 rounded-xl pl-11 pr-4 py-3.5 text-[14px] text-white focus:outline-none focus:border-[#00d4aa] transition-colors [color-scheme:dark]">
                        </div>
                    </div>
                </div>

                <!-- Automation Action -->
                <div class="space-y-2">
                    <label class="block text-[13px] font-bold text-slate-400 uppercase tracking-wider">Automation Action</label>
                    <div class="relative">
                        <select name="edit_automation_action" required
                                class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3.5 text-[14px] text-white focus:outline-none focus:border-[#00d4aa] transition-colors appearance-none cursor-pointer">
                            <option value="on" class="bg-[#1e293b]">⚡ Turn ON Automatically</option>
                            <option value="off" class="bg-[#1e293b]">🔌 Turn OFF Automatically</option>
                        </select>
                        <svg class="absolute right-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" stroke-width="2"/></svg>
                    </div>
                </div>

                <!-- Schedule Status -->
                <div class="space-y-2">
                    <label class="block text-[13px] font-bold text-slate-400 uppercase tracking-wider mb-3">Schedule Status</label>
                    <div class="flex items-center gap-6">
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <div class="relative flex items-center justify-center w-5 h-5 rounded-full border-2 border-[#00d4aa] bg-transparent group-hover:bg-[#00d4aa]/10 transition-colors">
                                <input type="radio" name="edit_schedule_status" value="active" class="peer sr-only">
                                <div class="w-2.5 h-2.5 rounded-full bg-[#00d4aa] opacity-0 peer-checked:opacity-100 transition-opacity"></div>
                            </div>
                            <span class="text-[14px] text-white group-hover:text-[#00d4aa] transition-colors font-medium flex items-center gap-1.5"><svg class="w-3.5 h-3.5 text-[#00d4aa]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg> Active (Running)</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <div class="relative flex items-center justify-center w-5 h-5 rounded-full border-2 border-slate-500 bg-transparent group-hover:bg-slate-500/10 transition-colors">
                                <input type="radio" name="edit_schedule_status" value="inactive" class="peer sr-only">
                                <div class="w-2.5 h-2.5 rounded-full bg-slate-400 opacity-0 peer-checked:opacity-100 transition-opacity"></div>
                            </div>
                            <span class="text-[14px] text-slate-400 group-hover:text-white transition-colors font-medium flex items-center gap-1.5"><svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> Inactive (Paused)</span>
                        </label>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex gap-4 pt-2">
                    <button type="button" onclick="closeEditScheduleModal()" class="flex-1 py-3.5 bg-white/5 border border-white/10 text-white font-bold rounded-xl hover:bg-white/10 transition-colors">Cancel</button>
                    <button type="submit" class="flex-1 py-3.5 bg-[#00d4aa] text-white font-bold rounded-xl hover:bg-[#00bfa0] transition-colors shadow-lg shadow-[#00d4aa]/20">Update Schedule</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Schedule Modal -->
<div id="delete-schedule-modal" class="fixed inset-0 z-[100] hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-md" onclick="closeDeleteScheduleModal()"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-[420px] p-4">
        <div class="glass-effect rounded-[24px] shadow-2xl p-8">
            <div class="flex justify-between items-start mb-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-red-500/10 flex items-center justify-center text-red-500 border border-red-500/20">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" stroke-width="2"/></svg>
                    </div>
                    <h3 class="text-[20px] font-bold text-white">Delete Schedule?</h3>
                </div>
                <button onclick="closeDeleteScheduleModal()" class="w-8 h-8 flex items-center justify-center rounded-full bg-white/5 text-slate-400 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" stroke-width="2.5"/></svg>
                </button>
            </div>
            <p id="delete-schedule-message" class="text-[14px] text-slate-400 mb-5">Are you sure you want to delete this schedule?</p>
            <div class="flex items-start gap-3 p-4 bg-yellow-500/5 border border-yellow-500/20 rounded-xl mb-8">
                <svg class="w-5 h-5 text-yellow-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" stroke-width="2"/></svg>
                <p class="text-[13px] text-yellow-500/80">This action cannot be undone. All user data and associated records will be permanently deleted.</p>
            </div>
            <div class="flex gap-4">
                <button onclick="closeDeleteScheduleModal()" class="flex-1 py-3.5 bg-white/5 border border-white/10 text-white font-bold rounded-xl hover:bg-white/10 transition-colors">Cancel</button>
                <button id="confirm-delete-schedule-btn" class="flex-1 py-3.5 bg-red-500 text-white font-bold rounded-xl hover:bg-red-600 transition-colors shadow-lg shadow-red-500/20">Delete Schedule</button>
            </div>
        </div>
    </div>
</div>

<!-- View Schedule Modal -->
<div id="view-schedule-modal" class="fixed inset-0 z-[100] hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-md" onclick="closeViewScheduleModal()"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-[560px] p-4 max-h-[100dvh] overflow-y-auto overscroll-contain custom-scrollbar">
        <div class="glass-effect rounded-[24px] shadow-2xl overflow-hidden">
            <div class="p-6 flex justify-between items-center border-b border-white/10">
                <h3 class="text-[22px] font-bold text-white">Schedule Details</h3>
                <button onclick="closeViewScheduleModal()" class="w-8 h-8 flex items-center justify-center rounded-full bg-white/5 text-slate-400 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" stroke-width="2.5"/></svg>
                </button>
            </div>
            <div class="p-8 space-y-6">
                <div id="view-schedule-content" class="space-y-6"></div>
                <div class="flex gap-4 pt-2">
                    <button type="button" onclick="closeViewScheduleModal()" class="flex-1 py-3.5 bg-white/5 border border-white/10 text-white font-bold rounded-xl hover:bg-white/10 transition-colors">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    let schedulesMap = {};
    let deleteScheduleId = null;
    let addDayManager, editDayManager;
    let roomsMap = {};

    function initDaySelector(containerId, inputId) {
        const container = document.getElementById(containerId);
        const input = document.getElementById(inputId);
        const btns = container.querySelectorAll('.day-btn');
        
        btns.forEach(btn => {
            btn.addEventListener('click', () => {
                btn.classList.toggle('selected');
                if (btn.classList.contains('selected')) {
                    btn.classList.replace('border-white/10', 'border-[#00d4aa]/30');
                    btn.classList.replace('text-slate-400', 'text-[#00d4aa]');
                    btn.classList.add('bg-[#00d4aa]/10');
                } else {
                    btn.classList.replace('border-[#00d4aa]/30', 'border-white/10');
                    btn.classList.replace('text-[#00d4aa]', 'text-slate-400');
                    btn.classList.remove('bg-[#00d4aa]/10');
                }
                updateInput();
            });
        });

        function updateInput() {
            const selected = Array.from(btns).filter(b => b.classList.contains('selected')).map(b => b.dataset.day);
            input.value = JSON.stringify(selected);
        }
        
        return {
            setDays: (daysArray) => {
                btns.forEach(btn => {
                    if (daysArray.includes(btn.dataset.day)) {
                        if (!btn.classList.contains('selected')) btn.click();
                    } else {
                        if (btn.classList.contains('selected')) btn.click();
                    }
                });
            },
            reset: () => {
                btns.forEach(btn => {
                    if (btn.classList.contains('selected')) btn.click();
                });
            }
        };
    }

    async function loadRoomsDropdowns() {
        try {
            const res = await apiFetch('/ruangan');
            if (!res.ok) return;
            const data = await res.json();
            const rooms = Array.isArray(data) ? data : (data.data || []);
            
            const addSel = document.querySelector('select[name="ruangan_id"]');
            const editSel = document.querySelector('select[name="edit_ruangan_id"]');
            
            // clear existing options except first
            while(addSel.options.length > 1) addSel.remove(1);
            while(editSel.options.length > 1) editSel.remove(1);

            roomsMap = {};
            rooms.forEach(r => {
                roomsMap[r.id] = r.nama_ruangan;
                const opt1 = document.createElement('option');
                opt1.value = r.id;
                opt1.className = 'bg-[#1e293b]';
                opt1.textContent = r.nama_ruangan + (r.id ? ` (${r.id})` : '');
                
                const opt2 = opt1.cloneNode(true);
                addSel.appendChild(opt1);
                editSel.appendChild(opt2);
            });
        } catch(e) { console.error('Failed to load rooms', e); }
    }

    async function loadSchedules() {
        const tbody = document.getElementById('schedules-table-body');
        try {
            const res = await apiFetch('/jadwal-listrik');
            const data = await res.json();
            const schedules = Array.isArray(data) ? data : (data.data || []);

            schedulesMap = {};
            let activeCount = 0;
            let roomsScheduled = new Set();

            schedules.forEach(s => { 
                schedulesMap[s.id] = s; 
                if (s.schedule_status === 'active') activeCount++;
                if (s.ruangan_id) roomsScheduled.add(s.ruangan_id);
            });

            // Update stats
            document.getElementById('stat-total').textContent = schedules.length;
            document.getElementById('stat-active').textContent = activeCount;
            document.getElementById('stat-rooms').textContent = roomsScheduled.size;

            if (schedules.length === 0) {
                tbody.innerHTML = `<tr><td colspan="7" class="px-6 py-16 text-center text-slate-500">
                    <div class="flex flex-col items-center gap-3">
                        <svg class="w-10 h-10 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2"/></svg>
                        <span class="text-[14px]">No schedules found. Click "+ Add Schedule" to automate electricity.</span>
                    </div></td></tr>`;
                return;
            }

            tbody.innerHTML = schedules.map(s => {
                const scheduleId = 'SCH-' + String(s.id).padStart(3, '0');
                const roomName = roomsMap[s.ruangan_id] || s.ruangan_id || '–';
                
                // Format days nicely
                const daysMap = { monday:'Monday', tuesday:'Tuesday', wednesday:'Wednesday', thursday:'Thursday', friday:'Friday', saturday:'Saturday', sunday:'Sunday' };
                const days = (s.selected_days || []).map(d => daysMap[d] || d).join(', ');
                
                // Format time (remove seconds if present)
                const startTime = (s.start_time || '').substring(0,5);
                const endTime = (s.end_time || '').substring(0,5);
                
                // Action badge
                const isAutoOn = s.automation_action === 'on';
                const actionBadge = isAutoOn 
                    ? `<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#00d4aa]/10 text-[#00d4aa] border border-[#00d4aa]/20 text-[11px] font-bold tracking-wider whitespace-nowrap"><svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.381z" clip-rule="evenodd"/></svg> AUTO ON</span>`
                    : `<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-red-500/10 text-red-500 border border-red-500/20 text-[11px] font-bold tracking-wider whitespace-nowrap"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636a9 9 0 010 12.728m0 0l-2.829-2.829m2.829 2.829L21 21M15.536 8.464a5 5 0 010 7.072m0 0l-2.829-2.829m-4.243-4.243a5 5 0 00-1.414 7.072m0 0L4 21m2.828-9.9a9 9 0 010-12.728m0 0L4 3m2.828 2.829l2.829 2.829" /></svg> AUTO OFF</span>`;

                // Status badge
                const isActive = s.schedule_status === 'active';
                const statusBadge = isActive
                    ? `<span class="inline-flex items-center justify-center px-3 py-1 rounded-full bg-[#00aaff]/10 text-[#00aaff] border border-[#00aaff]/20 text-[11px] font-bold tracking-wider">Active</span>`
                    : `<span class="inline-flex items-center justify-center px-3 py-1 rounded-full bg-slate-500/10 text-slate-400 border border-slate-500/20 text-[11px] font-bold tracking-wider">Inactive</span>`;

                return `
                <tr class="hover:bg-white/[0.02] transition-all group">
                    <td class="px-6 py-5 whitespace-nowrap">
                        <span class="text-[13px] font-bold text-[#00d4aa] tracking-wider">${scheduleId}</span>
                    </td>
                    <td class="px-6 py-5 whitespace-nowrap">
                        <span class="text-[14px] font-medium text-white">${roomName}</span>
                    </td>
                    <td class="px-6 py-5">
                        <p class="text-[13px] text-slate-400 font-medium leading-relaxed">${days}</p>
                    </td>
                    <td class="px-6 py-5 whitespace-nowrap">
                        <span class="text-[13px] font-bold text-white tracking-wider">${startTime} - ${endTime}</span>
                    </td>
                    <td class="px-6 py-5 text-center whitespace-nowrap">
                        ${actionBadge}
                    </td>
                    <td class="px-6 py-5 text-center whitespace-nowrap">
                        ${statusBadge}
                    </td>
                    <td class="px-6 py-5">
                        <div class="flex items-center justify-end gap-2">
                            <button data-edit-id="${s.id}" class="btn-edit-schedule w-8 h-8 flex items-center justify-center rounded-lg bg-[#00aaff]/10 border border-[#00aaff]/20 text-[#00aaff] hover:bg-[#00aaff]/20 transition-all" title="Edit Schedule">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            </button>
                            <button data-delete-id="${s.id}" class="btn-delete-schedule w-8 h-8 flex items-center justify-center rounded-lg bg-red-500/10 border border-red-500/20 text-red-500 hover:bg-red-500/20 transition-all" title="Delete Schedule">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                            <button data-view-id="${s.id}" class="btn-view-schedule w-8 h-8 flex items-center justify-center rounded-lg bg-slate-500/10 border border-slate-500/20 text-slate-400 hover:bg-slate-500/20 hover:text-white transition-all" title="View Schedule">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </button>
                        </div>
                    </td>
                </tr>`;
            }).join('');

            document.querySelectorAll('.btn-edit-schedule').forEach(btn => {
                btn.addEventListener('click', function() {
                    const sch = schedulesMap[this.dataset.editId];
                    if (sch) openEditScheduleModal(sch);
                });
            });
            document.querySelectorAll('.btn-delete-schedule').forEach(btn => {
                btn.addEventListener('click', function() {
                    const sch = schedulesMap[this.dataset.deleteId];
                    if (sch) openDeleteScheduleModal(sch.id, sch.ruangan_id);
                });
            });
            document.querySelectorAll('.btn-view-schedule').forEach(btn => {
                btn.addEventListener('click', function() {
                    const sch = schedulesMap[this.dataset.viewId];
                    if (sch) openViewScheduleModal(sch);
                });
            });

        } catch (err) {
            tbody.innerHTML = `<tr><td colspan="7" class="px-6 py-12 text-center text-slate-500 text-[14px]">Failed to load schedules. Please refresh.</td></tr>`;
        }
    }

    function openAddScheduleModal() {
        const form = document.getElementById('add-schedule-form');
        form.reset();
        addDayManager.reset();
        document.getElementById('add-schedule-modal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeAddScheduleModal() {
        document.getElementById('add-schedule-modal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function openEditScheduleModal(schedule) {
        const form = document.getElementById('edit-schedule-form');
        form.edit_id.value = schedule.id;
        form.edit_ruangan_id.value = schedule.ruangan_id;
        
        // set time (limit to HH:mm)
        form.edit_start_time.value = (schedule.start_time || '').substring(0,5);
        form.edit_end_time.value = (schedule.end_time || '').substring(0,5);
        
        form.edit_automation_action.value = schedule.automation_action || 'on';
        
        // set radio buttons for status
        const statusRadios = form.querySelectorAll('input[name="edit_schedule_status"]');
        statusRadios.forEach(r => {
            r.checked = (r.value === schedule.schedule_status);
        });

        // Set days
        editDayManager.setDays(schedule.selected_days || []);

        document.getElementById('edit-schedule-modal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeEditScheduleModal() {
        document.getElementById('edit-schedule-modal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function openDeleteScheduleModal(id) {
        deleteScheduleId = id;
        const scheduleCode = 'SCH-' + String(id).padStart(3, '0');
        document.getElementById('delete-schedule-message').innerHTML = `Are you sure you want to delete schedule <strong class="text-white">"${scheduleCode}"</strong>?`;
        document.getElementById('delete-schedule-modal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeDeleteScheduleModal() {
        document.getElementById('delete-schedule-modal').classList.add('hidden');
        document.body.style.overflow = 'auto';
        deleteScheduleId = null;
    }

    function openViewScheduleModal(schedule) {
        const roomName = roomsMap[schedule.ruangan_id] || schedule.ruangan_id || '–';
        const scheduleId = 'SCH-' + String(schedule.id).padStart(3, '0');
        
        const daysMap = { monday:'Monday', tuesday:'Tuesday', wednesday:'Wednesday', thursday:'Thursday', friday:'Friday', saturday:'Saturday', sunday:'Sunday' };
        const days = (schedule.selected_days || []).map(d => `<span class="px-2.5 py-1 rounded-md bg-white/5 border border-white/10 text-slate-300 text-[12px] font-medium">${daysMap[d] || d}</span>`).join('');
        
        const startTime = (schedule.start_time || '').substring(0,5);
        const endTime = (schedule.end_time || '').substring(0,5);
        
        const isAutoOn = schedule.automation_action === 'on';
        const actionText = isAutoOn ? '⚡ Turn ON Automatically' : '🔌 Turn OFF Automatically';
        const actionColor = isAutoOn ? 'text-[#00d4aa]' : 'text-red-500';

        const isActive = schedule.schedule_status === 'active';
        const statusText = isActive ? 'Active (Running)' : 'Inactive (Paused)';
        const statusColor = isActive ? 'text-[#00aaff]' : 'text-slate-400';

        document.getElementById('view-schedule-content').innerHTML = `
            <div class="grid grid-cols-2 gap-6">
                <div class="space-y-1">
                    <span class="text-[12px] font-bold text-slate-500 uppercase tracking-wider">Schedule ID</span>
                    <p class="text-[15px] font-bold text-white">${scheduleId}</p>
                </div>
                <div class="space-y-1">
                    <span class="text-[12px] font-bold text-slate-500 uppercase tracking-wider">Room</span>
                    <p class="text-[15px] font-bold text-white">${roomName}</p>
                </div>
            </div>
            
            <div class="space-y-2">
                <span class="text-[12px] font-bold text-slate-500 uppercase tracking-wider">Selected Days</span>
                <div class="flex flex-wrap gap-2">
                    ${days || '<span class="text-slate-500 text-[13px]">No days selected</span>'}
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div class="space-y-1">
                    <span class="text-[12px] font-bold text-slate-500 uppercase tracking-wider">Time</span>
                    <p class="text-[15px] font-bold text-white flex items-center gap-2">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2"/></svg>
                        ${startTime} - ${endTime}
                    </p>
                </div>
                <div class="space-y-1">
                    <span class="text-[12px] font-bold text-slate-500 uppercase tracking-wider">Status</span>
                    <p class="text-[15px] font-bold ${statusColor}">${statusText}</p>
                </div>
            </div>

            <div class="space-y-1">
                <span class="text-[12px] font-bold text-slate-500 uppercase tracking-wider">Automation Action</span>
                <p class="text-[15px] font-bold ${actionColor}">${actionText}</p>
            </div>
        `;

        document.getElementById('view-schedule-modal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeViewScheduleModal() {
        document.getElementById('view-schedule-modal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Forms Submission
    document.getElementById('add-schedule-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const f = e.target;
        
        let selectedDays = [];
        try { selectedDays = JSON.parse(f.selected_days.value || '[]'); } catch(e){}
        if (selectedDays.length === 0) {
            alert("Please select at least one day for the schedule.");
            return;
        }

        const btn = f.querySelector('button[type="submit"]');
        const origText = btn.textContent;
        btn.disabled = true; btn.textContent = 'Saving...';

        const payload = {
            ruangan_id: f.ruangan_id.value,
            selected_days: selectedDays,
            start_time: f.start_time.value ? f.start_time.value.substring(0, 5) : null,
            end_time: f.end_time.value ? f.end_time.value.substring(0, 5) : null,
            automation_action: f.automation_action.value,
            schedule_status: f.querySelector('input[name="schedule_status"]:checked').value
        };

        try {
            const res = await apiFetch('/jadwal-listrik', { method: 'POST', body: JSON.stringify(payload) });
            if (res.ok) {
                closeAddScheduleModal();
                await loadSchedules();
            } else {
                const err = await res.json();
                alert('Error: ' + (err.message || 'Failed to create schedule'));
            }
        } catch(err) {
            alert('Network error. Please try again.');
        } finally {
            btn.disabled = false; btn.textContent = origText;
        }
    });

    document.getElementById('edit-schedule-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const f = e.target;
        const id = f.edit_id.value;
        
        let selectedDays = [];
        try { selectedDays = JSON.parse(f.edit_selected_days.value || '[]'); } catch(e){}
        if (selectedDays.length === 0) {
            alert("Please select at least one day for the schedule.");
            return;
        }

        const btn = f.querySelector('button[type="submit"]');
        const origText = btn.textContent;
        btn.disabled = true; btn.textContent = 'Updating...';

        const payload = {
            ruangan_id: f.edit_ruangan_id.value,
            selected_days: selectedDays,
            start_time: f.edit_start_time.value ? f.edit_start_time.value.substring(0, 5) : null,
            end_time: f.edit_end_time.value ? f.edit_end_time.value.substring(0, 5) : null,
            automation_action: f.edit_automation_action.value,
            schedule_status: f.querySelector('input[name="edit_schedule_status"]:checked').value
        };

        try {
            const res = await apiFetch('/jadwal-listrik/' + id, { method: 'PUT', body: JSON.stringify(payload) });
            if (res.ok) {
                closeEditScheduleModal();
                await loadSchedules();
            } else {
                const err = await res.json();
                alert('Error: ' + (err.message || 'Failed to update schedule'));
            }
        } catch(err) {
            alert('Network error. Please try again.');
        } finally {
            btn.disabled = false; btn.textContent = origText;
        }
    });

    document.getElementById('confirm-delete-schedule-btn').addEventListener('click', async () => {
        if (!deleteScheduleId) return;
        const btn = document.getElementById('confirm-delete-schedule-btn');
        const origText = btn.textContent;
        btn.disabled = true; btn.textContent = 'Deleting...';
        
        try {
            const res = await apiFetch('/jadwal-listrik/' + deleteScheduleId, { method: 'DELETE' });
            if (res.ok) {
                closeDeleteScheduleModal();
                await loadSchedules();
            } else {
                const err = await res.json();
                alert('Error: ' + (err.message || 'Failed to delete schedule'));
            }
        } catch(e) {
            alert('Network error. Please try again.');
        } finally {
            btn.disabled = false; btn.textContent = origText;
        }
    });

    document.addEventListener('DOMContentLoaded', async () => {
        addDayManager = initDaySelector('add-days-container', 'add-selected-days');
        editDayManager = initDaySelector('edit-days-container', 'edit-selected-days');
        
        await loadRoomsDropdowns();
        await loadSchedules();
    });
</script>
@endpush
