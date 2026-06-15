<?php

namespace App\Http\Controllers;

use App\Models\Ruangan;
use App\Models\Peminjaman;
use App\Models\JadwalListrik;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RoomAvailabilityController extends Controller
{
    public function show($id, Request $request)
    {
        $ruangan = Ruangan::findOrFail($id);
        
        $dateStr = $request->query('date', Carbon::today()->toDateString());
        
        // Fetch approved bookings for this room on this date
        $bookings = Peminjaman::query()
            ->with('user:id,name')
            ->where('ruangan_id', $id)
            ->whereIn('status', ['disetujui', 'pending'])
            ->whereDate('tanggal_mulai', $dateStr)
            ->get();
            
        // Fetch active electricity schedules for this room
        $schedules = JadwalListrik::query()
            ->where('ruangan_id', $id)
            ->where('schedule_status', 'active')
            ->get();
            
        if ($request->is('student/*')) {
            return view('student.room-availability-show', compact('ruangan', 'bookings', 'schedules', 'dateStr'));
        }
        
        return view('room-availability.show', compact('ruangan', 'bookings', 'schedules', 'dateStr'));
    }
}
