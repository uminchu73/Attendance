<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceBreak extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendance_id',
        'attendance_request_id',
        'break_start',
        'break_end',
    ];


    /**
     * リレーション：出勤記録（Attendance）との１対１
     * どの勤怠の休憩か
     */
    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

    public function attendanceRequest()
    {
        return $this->belongsTo(AttendanceRequest::class);
    }

}
