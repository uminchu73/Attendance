<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'work_date',
        'clock_in',
        'clock_out',
        'status',
    ];

    /**
     * リレーション：ユーザー(User)との１対１
     * 誰の勤怠か
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }


    /**
     * リレーション：休憩（AttendanceBreak）との1対多
     * １日に複数回休憩が取れる
     */

    public function breaks()
    {
        return $this->hasMany(AttendanceBreak::class);
    }


    /**
     * リレーション：修正申請(AttendanceRequest)との1対多
     * 何度も修正申請できる
     */
    public function requests()
    {
        return $this->hasMany(AttendanceRequest::class);
    }
}
