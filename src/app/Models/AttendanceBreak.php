<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceBreak extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendance_id',
        'break_start',
        'break_end',
    ];


    /**
     * Carbonインスタンス
     */
    protected $casts = [
        'break_start' => 'datetime',
        'break_end'   => 'datetime',
    ];


    /**
     * リレーション：出勤記録（Attendance）との１対１
     * どの勤怠の休憩か
     */
    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }
}
