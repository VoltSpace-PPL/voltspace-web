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
    
    // Check if user is student from layout context (we're in admin layout now but just in case)
    const isStudent = false;

    async function loadMonthData(y, m) {
        try {
            document.getElementById('timeline-loading').classList.remove('hidden');
            document.getElementById('timeline-grid').innerHTML = '';
            
            // we load bookings for the whole month
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
            document.getElementById('timeline-grid').innerHTML = '<div class="col-span-full text-center text-red-400 py-10">Failed to load schedule. Error: ' + e.message + '</div>';
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

        /* ── event blocks ── */
        let blocks = '';

        // Booking blocks (blue)
        dayBookings.forEach(b => {
            const [sh, sm] = (b.waktu_mulai || '06:00').substring(0,5).split(':').map(Number);
            const [eh, em] = (b.waktu_selesai || '07:00').substring(0,5).split(':').map(Number);
            const top = ((sh - S) + sm / 60) * HR;
            const ht  = ((eh - sh) + (em - sm) / 60) * HR;
            if (ht <= 0) return;
            const sl = `${String(sh).padStart(2,'0')}:${String(sm).padStart(2,'0')}`;
            const el = `${String(eh).padStart(2,'0')}:${String(em).padStart(2,'0')}`;
            const userName = 'Student Use';

            const bookDate = new Date(dateStr + 'T00:00:00');
            const today = new Date(); today.setHours(0,0,0,0);
            const diff = Math.floor((bookDate - today) / (1000*60*60*24));
            const cancelBtn = diff >= 1
                ? `<button onclick="event.stopPropagation();cancelBooking(${b.id})" class="absolute top-2 right-2 px-2.5 py-1 rounded-lg text-[11px] font-bold bg-red-500/20 border border-red-500/40 text-red-300 hover:bg-red-500 hover:text-white transition-all opacity-0 group-hover:opacity-100 z-30">Cancel</button>`
                : '';

            blocks += `<div class="absolute group" style="top:${top+2}px;left:${LW+6}px;right:6px;height:${ht-4}px;background:linear-gradient(135deg,rgba(37,99,235,0.25),rgba(0,212,170,0.10));border:1px solid rgba(59,130,246,0.45);border-radius:10px;overflow:hidden;z-index:5;">
                <div class="p-2.5 h-full flex flex-col justify-between">
                    <div>
                        <p class="text-[13px] font-bold text-white leading-tight truncate">${userName}</p>
                        <p class="text-[11px] text-slate-400 mt-0.5">${sl} – ${el}</p>
                    </div>
                    ${ht > 55 ? `<span class="text-[10px] px-2 py-0.5 rounded-full self-start" style="background:rgba(59,130,246,0.2);border:1px solid rgba(59,130,246,0.35);color:#93c5fd;">Booked</span>` : ''}
                </div>
                ${cancelBtn}
            </div>`;
        });

        // Schedule blocks (red/orange) — only if no booking overlaps
        daySchedules.forEach(s => {
            const [sh, sm] = (s.waktu_mulai || s.start_time || '06:00').substring(0,5).split(':').map(Number);
            const [eh, em] = (s.waktu_selesai || s.end_time   || '07:00').substring(0,5).split(':').map(Number);
            const ht  = ((eh - sh) + (em - sm) / 60) * HR;
            if (ht <= 0) return;

            const sSl = `${String(sh).padStart(2,'0')}:${String(sm).padStart(2,'0')}`;
            const sEl = `${String(eh).padStart(2,'0')}:${String(em).padStart(2,'0')}`;

            // Skip this schedule block if a booking already covers this period
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
                        <p class="text-[13px] font-bold text-red-300 leading-tight">Routine Class</p>
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

    window.cancelBooking = async function(bookingId) {
        const modalHtml = `
            <div id="cancel-booking-modal" class="fixed inset-0 z-[300]" role="dialog" aria-modal="true">
                <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="document.getElementById('cancel-booking-modal').remove()"></div>
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-[400px] p-4">
                    <div class="glass-effect rounded-[20px] shadow-2xl overflow-hidden">
                        <div class="p-6 border-b border-white/10">
                            <h3 class="text-[17px] font-bold text-white">Cancel Booking</h3>
                        </div>
                        <div class="p-6">
                            <label class="block text-[13px] font-bold text-slate-300 mb-2">Alasan Pembatalan</label>
                            <textarea id="cancel-reason" rows="3" class="w-full px-4 py-3 rounded-xl bg-black/20 border border-white/10 text-white text-[14px] focus:outline-none focus:border-[#00d4aa] transition-colors" placeholder="Masukkan alasan pembatalan (wajib)"></textarea>
                        </div>
                        <div class="px-6 pb-6 flex gap-3">
                            <button onclick="document.getElementById('cancel-booking-modal').remove()" class="flex-1 py-3 bg-white/5 border border-white/10 text-white font-bold rounded-xl hover:bg-white/10 transition-colors text-[14px]">Batal</button>
                            <button id="confirm-cancel-btn" class="flex-1 py-3 bg-red-500 hover:bg-red-600 text-white font-bold rounded-xl transition-colors text-[14px]">Ya, Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        document.body.insertAdjacentHTML('beforeend', modalHtml);
        
        document.getElementById('confirm-cancel-btn').addEventListener('click', async () => {
            const reason = document.getElementById('cancel-reason').value.trim();
            if (!reason) {
                vsAlert.error('Error', 'Alasan pembatalan wajib diisi.');
                return;
            }
            
            try {
                const res = await apiFetch('/peminjaman/' + bookingId + '/cancel', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ catatan_admin: reason })
                });
                const data = await res.json();
                
                if (res.ok) {
                    document.getElementById('cancel-booking-modal').remove();
                    vsAlert.success('Success', 'Peminjaman berhasil dibatalkan.');
                    loadMonthData(calendarYear, calendarMonth);
                } else {
                    vsAlert.error('Failed', data.message || 'Gagal membatalkan peminjaman.');
                }
            } catch (e) {
                vsAlert.error('Error', 'Terjadi kesalahan jaringan.');
            }
        });
    };

</script>
@endpush
