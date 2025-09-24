<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'attendance_id',
        'type',
        'request_content',
        'status',
    ];

    /**
     * リレーション：ユーザー(User)との１対１
     * 誰が申請したか
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * リレーション：修正申請（AttendanceRequest）との１対１
     * どの勤怠を修正するか
     */
    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }
}
