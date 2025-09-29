<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;


class AttendanceController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $today = Carbon::today()->toDateString();

        $attendance = Attendance::where('user_id', $user->id)
            ->where('work_date', $today)
            ->first();

        Carbon::setLocale('ja');

        return view('index', [
            'attendance'   => $attendance,
            'status'       => $attendance ? $attendance->status_label : '勤務外',
            'currentDate'  => Carbon::now()->isoFormat('YYYY年M月D日(ddd)'),
            'currentTime'  => Carbon::now()->format('H:i'),
        ]);
    }
}
