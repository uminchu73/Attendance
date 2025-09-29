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
     * ステータス定義
     */
    const STATUS_OFF = 0;   // 勤務外
    const STATUS_WORKING  = 1;   // 出勤中
    const STATUS_LEAVE     = 2;   // 退勤済

    /**
     * ステータスのラベル対応表
     */
    public static $statusLabels = [
        self::STATUS_OFF => '勤務外',
        self::STATUS_WORKING  => '出勤中',
        self::STATUS_LEAVE     => '退勤済',
    ];

    /** ステータス名を返すアクセサ */
    public function getStatusLabelAttribute()
    {
        return self::$statusLabels[$this->status] ?? '不明';
    }


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
