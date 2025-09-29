<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

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
    const STATUS_OFF = 0; //勤務外
    const STATUS_WORKING = 1; //出勤中
    const STATUS_LEAVE = 2; //退勤済
    const STATUS_BREAK = 3; //休憩中


    /**
     * ステータスのラベル対応表
     */
    public static $statusLabels = [
        self::STATUS_OFF => '勤務外',
        self::STATUS_WORKING => '出勤中',
        self::STATUS_LEAVE => '退勤済',
        self::STATUS_BREAK => '休憩中',
    ];

    /** ステータス名を返すアクセサ */
    public function getStatusLabelAttribute()
    {
        return self::$statusLabels[$this->status] ?? '不明';
    }

    /**
     * 勤務時間休憩時間合計
     */
    // 勤務時間(秒)
    public function getWorkSecondsAttribute()
    {
        if (!$this->clock_in || !$this->clock_out) return 0;

        $start = strtotime($this->clock_in);
        $end   = strtotime($this->clock_out);

        $breakSeconds = $this->breaks->sum(function($b) {
            if (!$b->break_end) return 0;
            return strtotime($b->break_end) - strtotime($b->break_start);
        });

        return ($end - $start) - $breakSeconds;
    }

    // 勤務時間を hh:mm 形式で返す
    public function getWorkTimeAttribute()
    {
        $seconds = $this->work_seconds;
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        return sprintf('%d:%02d', $hours, $minutes);
    }

    // 休憩時間 hh:mm
    public function getBreakTimeAttribute()
    {
        $seconds = $this->breaks->sum(function($b) {
            if (!$b->break_end) return 0;
            return strtotime($b->break_end) - strtotime($b->break_start);
        });

        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        return sprintf('%d:%02d', $hours, $minutes);
    }



    /**
     * 今日の勤怠を取得、なければ作成
     */
    public static function todayFor(User $user)
    {
        $today = Carbon::today()->toDateString();
        return self::firstOrCreate(
            ['user_id' => $user->id, 'work_date' => $today],
            ['status' => self::STATUS_OFF]
        );
    }

    /**
     * 出勤打刻
     */
    public function clockIn()
    {
        if ($this->status !== self::STATUS_OFF) {
            throw new \Exception('すでに出勤済みです');
        }

        $this->update([
            'clock_in' => now(),
            'status'   => self::STATUS_WORKING,
        ]);
    }

    /**
     * 退勤打刻
     */
    public function clockOut()
    {
        if ($this->status !== self::STATUS_WORKING) {
            throw new \Exception('出勤していません');
        }

        $this->update([
            'clock_out' => now(),
            'status'    => self::STATUS_LEAVE,
        ]);
    }

    /**
     * 休憩開始
     */
    public function startBreak()
    {
        if ($this->status !== self::STATUS_WORKING) {
            throw new \Exception('出勤中でないと休憩できません');
        }

        // 休憩レコード作成
        $this->breaks()->create(['break_start' => now()]);

        // ステータス変更
        $this->update(['status' => self::STATUS_BREAK]);
    }

    /**
     * 休憩終了
     */
    public function endBreak()
    {
        if ($this->status !== self::STATUS_BREAK) {
            throw new \Exception('休憩中でないと休憩戻できません');
        }

        // 直近の休憩レコードに終了時刻をセット
        $latestBreak = $this->breaks()->latest()->first();
        if ($latestBreak) {
            $latestBreak->update(['break_end' => now()]);
        }

        // ステータスを出勤中に戻す
        $this->update(['status' => self::STATUS_WORKING]);
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
