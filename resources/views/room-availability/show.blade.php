@extends('layouts.main')

@section('content')
<div class="mb-6 flex items-center gap-4">
    <a href="/room-availability" class="w-10 h-10 rounded-xl flex items-center justify-center bg-white/5 text-slate-400 hover:text-white hover:bg-white/10 transition-all">
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
                <div class="w-3 h-3 rounded bg-orange-500/20 border border-orange-500/40"></div> Booked / In Use
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
        <div class="flex items-center justify-between mb-6">
            <h2 id="selected-date-title" class="text-[18px] font-bold text-white">Loading...</h2>
            <div class="flex items-center gap-4 text-[12px]">
                <div class="flex items-center gap-1.5 text-slate-400">
                    <div class="w-3 h-3 rounded bg-[#00d4aa]/10 border border-[#00d4aa]/30"></div> Available
                </div>
                <div class="flex items-center gap-1.5 text-slate-400">
                    <div class="w-3 h-3 rounded bg-orange-500/10 border border-orange-500/30"></div> Unavailable
                </div>
            </div>
        </div>

        <div id="timeline-grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
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
    const roomId = {{ $ruangan->id }};
    const roomName = "{{ $ruangan->nama_ruangan }}";
    
    let currentDateStr = "{{ $dateStr }}";
    let calendarYear = new Date().getFullYear();
    let calendarMonth = new Date().getMonth();
    
    let monthBookings = [];
    let activeSchedules = [];

    const MONTH_NAMES = ['January','February','March','April','May','June','July','August','September','October','November','December'];
    
    // Check if user is student from layout context (we're in admin layout now but just in case)
    const isStudent = false;

    async function loadMonthData(y, m) {
        try {
            // we load bookings for the whole month
            const fromDate = `${y}-${String(m+1).padStart(2,'0')}-01`;
            const lastDay = new Date(y, m+1, 0).getDate();
            const toDate = `${y}-${String(m+1).padStart(2,'0')}-${String(lastDay).padStart(2,'0')}`;
            
            const res = await apiFetch(`/peminjaman?ruangan_id=${roomId}&status=disetujui&per_page=500&from=${fromDate}&to=${toDate}`);
            if (res.ok) {
                const data = await res.json();
                monthBookings = data.data || [];
            }
            
            const schRes = await apiFetch(`/jadwal-listrik?ruangan_id=${roomId}`);
            if (schRes.ok) {
                const sData = await schRes.json();
                activeSchedules = (sData.data || sData || []).filter(s => s.schedule_status === 'active');
            }
            
            renderCalendar();
            renderTimeline(currentDateStr);
        } catch (e) {
            console.error(e);
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
            
            // Check if this day has bookings
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
            } else if (hasBooking) {
                cls = 'bg-orange-500/10 border-orange-500/20';
            } else if (isPast) {
                cls = 'opacity-40 cursor-not-allowed';
            }

            html += `
            <div class="rounded-xl p-2 text-center transition-all ${cls}" 
                 onclick="${isPast ? '' : `selectDate('${dateStr}')`}">
                <span class="text-[13px] font-bold ${isSelected ? 'text-[#00d4aa]' : isToday ? 'text-white' : hasBooking ? 'text-orange-300' : 'text-slate-300'}">${d}</span>
                ${hasBooking && !isSelected && !isToday ? `<div class="w-1 h-1 rounded-full bg-orange-400 mx-auto mt-1"></div>` : ''}
            </div>`;
        }
        grid.innerHTML = html;
    }

    window.selectDate = function(dateStr) {
        currentDateStr = dateStr;
        // update url without reload
        const url = new URL(window.location);
        url.searchParams.set('date', dateStr);
        window.history.pushState({}, '', url);
        
        renderCalendar();
        renderTimeline(dateStr);
    };

    function renderTimeline(dateStr) {
        document.getElementById('timeline-loading').classList.add('hidden');
        const grid = document.getElementById('timeline-grid');
        
        const dObj = new Date(dateStr + 'T00:00:00');
        const dayNames = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
        const dayName = dayNames[dObj.getDay()];
        
        document.getElementById('selected-date-title').textContent = `${dayName}, ${dObj.getDate()} ${MONTH_NAMES[dObj.getMonth()]} ${dObj.getFullYear()}`;
        
        const dayBookings = monthBookings.filter(b => (b.tanggal_mulai || '').substring(0,10) === dateStr);
        const daySchedules = activeSchedules.filter(s => {
            if (s.tanggal_mulai && s.tanggal_mulai === dateStr) return true;
            if (!s.tanggal_mulai) {
                let sDays = s.selected_days || [];
                if (typeof sDays === 'string') {
                    try { sDays = JSON.parse(sDays); } catch(e){ sDays = []; }
                }
                return sDays.includes(dayName.toLowerCase());
            }
            return false;
        });

        // 06:00 to 20:00
        const hours = [];
        for (let h = 6; h < 20; h++) hours.push(h);
        
        const todayStr = new Date().toISOString().split('T')[0];
        const nowStr = `${String(new Date().getHours()).padStart(2,'0')}:${String(new Date().getMinutes()).padStart(2,'0')}`;

        grid.innerHTML = hours.map(h => {
            const slotStart = `${String(h).padStart(2,'0')}:00`;
            const slotEnd   = `${String(h+1).padStart(2,'0')}:00`;
            const timeLabel = `${slotStart} - ${slotEnd} WIB`;
            
            // Overlap check
            const booking = dayBookings.find(b => {
                const bS = (b.waktu_mulai || '').substring(0,5);
                const bE = (b.waktu_selesai || '').substring(0,5);
                return !(slotEnd <= bS || slotStart >= bE);
            });
            const schedule = daySchedules.find(s => {
                const sS = (s.start_time || '').substring(0,5);
                const sE = (s.end_time || '').substring(0,5);
                return !(slotEnd <= sS || slotStart >= sE);
            });
            
            const isPast = dateStr < todayStr || (dateStr === todayStr && slotEnd <= nowStr);
            const isOccupied = booking || schedule;
            
            let cls = '';
            let label = '';
            let subText = '';
            let canBook = false;
            
            if (isOccupied) {
                cls = 'bg-orange-500/10 border-orange-500/30 text-orange-300';
                label = 'Unavailable';
                subText = booking ? (booking.user?.name || 'Booked') : 'Routine Class';
            } else if (isPast) {
                cls = 'bg-slate-800/40 border-slate-700/40 text-slate-500 opacity-60';
                label = 'Past';
            } else {
                cls = 'bg-[#00d4aa]/10 border-[#00d4aa]/30 text-[#00d4aa] hover:bg-[#00d4aa]/20 cursor-pointer';
                label = 'Available';
                canBook = true;
            }

            return `
            <div class="rounded-xl p-3 border ${cls} transition-all relative group"
                 ${canBook ? `onclick="window.location.href='/student/bookings/create?room_id=${roomId}&date=${dateStr}&start_time=${slotStart}&end_time=${slotEnd}'"` : ''}>
                <p class="text-[14px] font-bold mb-1">${timeLabel}</p>
                <p class="text-[12px] font-semibold">${label}</p>
                ${subText ? `<p class="text-[11px] mt-1 opacity-80 truncate">${subText}</p>` : ''}
                
                ${canBook ? `
                <div class="absolute inset-0 flex items-center justify-center bg-[#00d4aa] text-[#0b1120] font-bold text-[13px] rounded-xl opacity-0 group-hover:opacity-100 transition-opacity">
                    Direct Booking
                </div>` : ''}
            </div>`;
        }).join('');
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
