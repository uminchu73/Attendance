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
        'clock_in',
        'clock_out',
        'note',
        'status',
    ];


    // 承認ステータス
    const STATUS_PENDING  = 0; // 承認待ち
    const STATUS_APPROVED = 1; // 承認済
    const STATUS_DENIED   = 2; // 否認

    public static $statusLabels = [
        self::STATUS_PENDING  => '承認待ち',
        self::STATUS_APPROVED => '承認',
        self::STATUS_DENIED   => '否認',
    ];

    public function getStatusLabelAttribute()
    {
        return self::$statusLabels[$this->status] ?? '不明';
    }


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

    public function breaks()
    {
        return $this->hasMany(AttendanceRequestBreak::class);
    }
}
