@extends('layouts.student')

@section('content')
<div class="mb-6 flex items-center gap-4">
    <a href="/student/room-availability" class="w-10 h-10 rounded-xl flex items-center justify-center bg-white/5 text-slate-400 hover:text-white hover:bg-white/10 transition-all">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" stroke-width="2"/></svg>
    </a>
    <div>
        <h1 class="text-[28px] font-extrabold text-white tracking-tight leading-none">{{ $ruangan->nama_ruangan }}</h1>
        <p class="text-slate-400 text-[14px] mt-2">{{ $ruangan->kode }} · Floor {{ $ruangan->lantai }} · Capacity: {{ $ruangan->kapasitas }} people</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
    <!-- Left column: Calendar -->
    <div class="lg:col-span-4 rounded-2xl p-5 flex flex-col" style="background:#161e2d; border:1px solid #1e2d45;">
        <div class="flex items-center justify-between mb-4">
            <button id="prev-month" class="w-8 h-8 rounded-lg bg-white/5 flex items-center justify-center hover:bg-white/10 transition-colors">
                <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="2"/></svg>
            </button>
            <h3 id="cal-month" class="text-[15px] font-bold text-white">Month Year</h3>
            <button id="next-month" class="w-8 h-8 rounded-lg bg-white/5 flex items-center justify-center hover:bg-white/10 transition-colors">
                <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="2"/></svg>
            </button>
        </div>
        <div class="grid grid-cols-7 gap-1 mb-2">
            @foreach(['Su','Mo','Tu','We','Th','Fr','Sa'] as $d)
            <div class="text-center text-[11px] font-bold text-slate-500 py-1">{{ $d }}</div>
            @endforeach
        </div>
        <div id="cal-grid" class="grid grid-cols-7 gap-1">
            <!-- populated by JS -->
        </div>
        
        <div class="mt-6 flex flex-col gap-2 border-t pt-4" style="border-color:#1e2d45;">
            <div class="flex items-center gap-2 text-[12px] text-slate-400">
                <div class="w-3 h-3 rounded bg-red-500/20 border border-red-500/40"></div> In Use (Booked)
            </div>
            <div class="flex items-center gap-2 text-[12px] text-slate-400">
                <div class="w-3 h-3 rounded bg-[#00d4aa]/20 border border-[#00d4aa]/40"></div> Selected Date
            </div>
            <div class="flex items-center gap-2 text-[12px] text-slate-400">
                <div class="w-3 h-3 rounded bg-slate-700 border border-slate-600"></div> Today
            </div>
        </div>
    </div>

    <!-- Right column: Time Slots -->
    <div class="lg:col-span-8 rounded-2xl p-5" style="background:#161e2d; border:1px solid #1e2d45;">
        <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
            <h2 id="selected-date-title" class="text-[18px] font-bold text-white">Loading...</h2>
            <div class="flex items-center gap-4 text-[12px]">
                <div class="flex items-center gap-1.5 text-slate-400">
                    <div class="w-3 h-3 rounded bg-emerald-500/10 border border-emerald-500/30"></div> Available
                </div>
                <div class="flex items-center gap-1.5 text-slate-400">
                    <div class="w-3 h-3 rounded bg-red-500/10 border border-red-500/30"></div> In Use
                </div>
            </div>
        </div>

        <div id="timeline-grid">
            <!-- Populated by JS -->
        </div>
        
        <div id="timeline-loading" class="flex flex-col items-center justify-center py-20 gap-3">
            <div class="w-8 h-8 border-2 border-[#00d4aa] border-t-transparent rounded-full animate-spin"></div>
            <span class="text-slate-500 text-[13px]">Loading schedule...</span>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const roomId = "{{ $ruangan->id }}";
    const roomName = "{{ $ruangan->nama_ruangan }}";
    
    let currentDateStr = "{{ $dateStr }}";
    let calendarYear = new Date().getFullYear();
    let calendarMonth = new Date().getMonth();
    
    let monthBookings = [];
    let activeSchedules = [];

    const MONTH_NAMES = ['January','February','March','April','May','June','July','August','September','October','November','December'];

    async function loadMonthData(y, m) {
        try {
            document.getElementById('timeline-loading').classList.remove('hidden');
            document.getElementById('timeline-grid').innerHTML = '';
            const fromDate = `${y}-${String(m+1).padStart(2,'0')}-01`;
            const lastDay = new Date(y, m+1, 0).getDate();
            const toDate = `${y}-${String(m+1).padStart(2,'0')}-${String(lastDay).padStart(2,'0')}`;
            
            const res = await apiFetch(`/peminjaman?ruangan_id=${roomId}&status=disetujui&per_page=500&from=${fromDate}&to=${toDate}`);
            if (res.ok) {
                const data = await res.json();
                monthBookings = data.data || [];
            } else {
                monthBookings = [];
            }
            
            const schRes = await apiFetch(`/jadwal-listrik?ruangan_id=${roomId}`);
            if (schRes.ok) {
                const sData = await schRes.json();
                activeSchedules = (sData.data || sData || []).filter(s => s.schedule_status === 'active');
            } else {
                activeSchedules = [];
            }
            
            renderCalendar();
            renderTimeline(currentDateStr);
        } catch (e) {
            console.error(e);
            document.getElementById('timeline-loading').classList.add('hidden');
            document.getElementById('timeline-grid').innerHTML = '<div class="col-span-full text-center text-red-400 py-10">Failed to load schedule. Please try again.</div>';
        }
    }

    function renderCalendar() {
        document.getElementById('cal-month').textContent = `${MONTH_NAMES[calendarMonth]} ${calendarYear}`;
        const grid = document.getElementById('cal-grid');
        
        const firstDay = new Date(calendarYear, calendarMonth, 1).getDay(); // 0=Sun
        const daysInMonth = new Date(calendarYear, calendarMonth + 1, 0).getDate();
        const todayStr = new Date().toISOString().split('T')[0];

        let html = '';
        for (let i = 0; i < firstDay; i++) {
            html += `<div></div>`;
        }

        for (let d = 1; d <= daysInMonth; d++) {
            const dateStr = `${calendarYear}-${String(calendarMonth + 1).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
            const isToday = dateStr === todayStr;
            const isSelected = dateStr === currentDateStr;
            const isPast = dateStr < todayStr;
            
            const hasBooking = monthBookings.some(b => (b.tanggal_mulai || '').substring(0,10) === dateStr) || 
                               activeSchedules.some(s => {
                                  if (s.tanggal_mulai && s.tanggal_mulai === dateStr) return true;
                                  if (!s.tanggal_mulai) {
                                      const dObj = new Date(dateStr + 'T00:00:00');
                                      const dayName = dObj.toLocaleDateString('en-US', { weekday: 'long' }).toLowerCase();
                                      let sDays = s.selected_days || [];
                                      if (typeof sDays === 'string') {
                                          try { sDays = JSON.parse(sDays); } catch(e){ sDays = []; }
                                      }
                                      return sDays.includes(dayName);
                                  }
                                  return false;
                               });

            let cls = 'cursor-pointer hover:bg-white/5 border border-transparent';

            if (isSelected) {
                cls = 'bg-[#00d4aa]/20 border-[#00d4aa]/40 ring-1 ring-[#00d4aa]';
            } else if (isToday) {
                cls = 'bg-slate-700 border-slate-600';
            } else if (isPast) {
                cls = 'opacity-40 cursor-not-allowed';
            }

            html += `
            <div class="rounded-xl p-2 text-center transition-all ${cls}" 
                 onclick="${isPast ? '' : `selectDate('${dateStr}')`}">
                <span class="text-[13px] font-bold ${isSelected ? 'text-[#00d4aa]' : isToday ? 'text-white' : 'text-slate-300'}">${d}</span>
                ${hasBooking && !isSelected && !isToday ? `<div class="w-1 h-1 rounded-full bg-red-400 mx-auto mt-1"></div>` : ''}
            </div>`;
        }
        grid.innerHTML = html;
    }

    window.selectDate = function(dateStr) {
        currentDateStr = dateStr;
        const url = new URL(window.location);
        url.searchParams.set('date', dateStr);
        window.history.pushState({}, '', url);
        
        renderCalendar();
        renderTimeline(dateStr);
    };
    function renderTimeline(dateStr) {
        document.getElementById('timeline-loading').classList.add('hidden');
        const timelineEl = document.getElementById('timeline-grid');

        const dObj = new Date(dateStr + 'T00:00:00');
        const dayNames = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
        const dayName = dayNames[dObj.getDay()];
        document.getElementById('selected-date-title').textContent =
            `${dayName}, ${dObj.getDate()} ${MONTH_NAMES[dObj.getMonth()]} ${dObj.getFullYear()}`;

        const dayBookings = monthBookings.filter(b => (b.tanggal_mulai || '').substring(0,10) === dateStr);
        const daySchedules = activeSchedules.filter(s => {
            if (s.tanggal_mulai && s.tanggal_mulai === dateStr) return true;
            if (!s.tanggal_mulai) {
                let sDays = s.selected_days || [];
                if (typeof sDays === 'string') { try { sDays = JSON.parse(sDays); } catch(e){ sDays = []; } }
                return sDays.includes(dayName.toLowerCase());
            }
            return false;
        });

        const HR = 64;   // px per hour
        const S  = 6;    // start hour (06:00)
        const E  = 20;   // end hour   (20:00)
        const LW = 52;   // label column width px
        const TOTAL = (E - S) * HR;

        const todayStr = new Date().toISOString().split('T')[0];
        const now      = new Date();
        const nowMins  = now.getHours() * 60 + now.getMinutes();
        const nowStr   = `${String(now.getHours()).padStart(2,'0')}:${String(now.getMinutes()).padStart(2,'0')}`;

        /* ── hour grid lines + labels ── */
        let gridHtml = '';
        for (let h = S; h <= E; h++) {
            const top = (h - S) * HR;
            gridHtml += `
            <div class="absolute w-full" style="top:${top}px;border-top:1px solid rgba(255,255,255,0.05);"></div>
            <span class="absolute text-[11px] font-mono text-slate-500" style="top:${top + 4}px;left:0;width:${LW - 6}px;text-align:right;">${String(h).padStart(2,'0')}:00</span>`;
        }

        /* ── current-time indicator ── */
        let nowLine = '';
        if (dateStr === todayStr && nowMins >= S * 60 && nowMins <= E * 60) {
            const t = (nowMins - S * 60) / 60 * HR;
            nowLine = `<div class="absolute z-20 flex items-center" style="top:${t}px;left:${LW}px;right:0;">
                <div class="w-2 h-2 rounded-full bg-[#00d4aa] -ml-1 flex-shrink-0"></div>
                <div class="flex-1 h-[2px] bg-[#00d4aa]"></div>
            </div>`;
        }

        /* ── clickable available hour slots ── */
        let blocks = '';
        for (let h = S; h < E; h++) {
            const slotStart = `${String(h).padStart(2,'0')}:00`;
            const slotEnd   = `${String(h+1).padStart(2,'0')}:00`;
            const occupied  = dayBookings.some(b => {
                const bS = (b.waktu_mulai||'').substring(0,5);
                const bE = (b.waktu_selesai||'').substring(0,5);
                return !(slotEnd <= bS || slotStart >= bE);
            }) || daySchedules.some(s => {
                const sS = (s.waktu_mulai||s.start_time||'').substring(0,5);
                const sE = (s.waktu_selesai||s.end_time||'').substring(0,5);
                return !(slotEnd <= sS || slotStart >= sE);
            });
            const isPast = dateStr < todayStr || (dateStr === todayStr && slotEnd <= nowStr);

            if (!occupied && !isPast) {
                const top = (h - S) * HR;
                blocks += `<div onclick="window.location.href='/student/bookings/create?ruangan_id=${roomId}&selected_date=${dateStr}&start_time=${slotStart}&end_time=${slotEnd}'" class="absolute cursor-pointer group" style="top:${top+1}px;left:${LW+4}px;right:4px;height:${HR-2}px;border-radius:8px;z-index:2;">
                    <div class="w-full h-full rounded-lg flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all" style="background:rgba(16,185,129,0.1);border:1px solid rgba(16,185,129,0.3);">
                        <span class="text-[12px] font-bold text-emerald-400">+ Book this slot</span>
                    </div>
                </div>`;
            }
        }

        /* ── booking event blocks (blue) ── */
        dayBookings.forEach(b => {
            const [sh, sm] = (b.waktu_mulai || '06:00').substring(0,5).split(':').map(Number);
            const [eh, em] = (b.waktu_selesai || '07:00').substring(0,5).split(':').map(Number);
            const top = ((sh - S) + sm / 60) * HR;
            const ht  = ((eh - sh) + (em - sm) / 60) * HR;
            if (ht <= 0) return;
            const sl = `${String(sh).padStart(2,'0')}:${String(sm).padStart(2,'0')}`;
            const el = `${String(eh).padStart(2,'0')}:${String(em).padStart(2,'0')}`;

            blocks += `<div class="absolute" style="top:${top+2}px;left:${LW+6}px;right:6px;height:${ht-4}px;background:linear-gradient(135deg,rgba(239,68,68,0.2),rgba(239,68,68,0.10));border:1px solid rgba(239,68,68,0.4);border-radius:10px;overflow:hidden;z-index:5;">
                <div class="p-2.5 h-full flex flex-col justify-between">
                    <div>
                        <p class="text-[13px] font-bold text-red-300 leading-tight">In Use</p>
                        <p class="text-[11px] text-slate-400 mt-0.5">${sl} – ${el}</p>
                    </div>
                </div>
            </div>`;
        });

        /* ── schedule blocks (red) — skip if booking already covers it ── */
        daySchedules.forEach(s => {
            const [sh, sm] = (s.waktu_mulai || s.start_time || '06:00').substring(0,5).split(':').map(Number);
            const [eh, em] = (s.waktu_selesai || s.end_time   || '07:00').substring(0,5).split(':').map(Number);
            const ht  = ((eh - sh) + (em - sm) / 60) * HR;
            if (ht <= 0) return;
            const sSl = `${String(sh).padStart(2,'0')}:${String(sm).padStart(2,'0')}`;
            const sEl = `${String(eh).padStart(2,'0')}:${String(em).padStart(2,'0')}`;

            // Skip if a booking already occupies this time
            const overlapsBooking = dayBookings.some(b => {
                const bS = (b.waktu_mulai || '').substring(0,5);
                const bE = (b.waktu_selesai || '').substring(0,5);
                return !(sEl <= bS || sSl >= bE);
            });
            if (overlapsBooking) return;

            const top = ((sh - S) + sm / 60) * HR;
            blocks += `<div class="absolute" style="top:${top+2}px;left:${LW+6}px;right:6px;height:${ht-4}px;background:rgba(239,68,68,0.12);border:1px solid rgba(239,68,68,0.3);border-radius:10px;overflow:hidden;z-index:5;">
                <div class="p-2.5 h-full flex flex-col justify-between">
                    <div>
                        <p class="text-[13px] font-bold text-red-300 leading-tight">In Use</p>
                        <p class="text-[11px] text-slate-400 mt-0.5">${sSl} – ${sEl}</p>
                    </div>
                </div>
            </div>`;
        });

        timelineEl.className = 'relative overflow-y-auto rounded-xl custom-scrollbar';
        timelineEl.style.maxHeight = '560px';
        timelineEl.style.minHeight = '200px';
        timelineEl.innerHTML = `<div class="relative" style="height:${TOTAL}px;padding-left:${LW}px;margin-right:4px;">${gridHtml}${nowLine}${blocks}</div>`;
    }

    document.getElementById('prev-month').addEventListener('click', () => {
        calendarMonth--;
        if (calendarMonth < 0) { calendarMonth = 11; calendarYear--; }
        loadMonthData(calendarYear, calendarMonth);
    });

    document.getElementById('next-month').addEventListener('click', () => {
        calendarMonth++;
        if (calendarMonth > 11) { calendarMonth = 0; calendarYear++; }
        loadMonthData(calendarYear, calendarMonth);
    });

    document.addEventListener('DOMContentLoaded', () => {
        if (currentDateStr) {
            const parts = currentDateStr.split('-');
            calendarYear = parseInt(parts[0]);
            calendarMonth = parseInt(parts[1]) - 1;
        }
        loadMonthData(calendarYear, calendarMonth);
    });

</script>
@endpush
