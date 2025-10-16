<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Attendance;
use Carbon\Carbon;

class AttendanceFactory extends Factory
{
    protected $model = Attendance::class;

    public function definition()
    {
        // 直近1ヶ月の日付をランダムに
        $date = Carbon::today()->subDays(rand(0, 30));

        // 出勤・退勤時刻を現実的な範囲で設定
        $clockIn = Carbon::parse($date)->setTime(rand(7, 10), rand(0, 59));
        $clockOut = (clone $clockIn)->addHours(rand(7, 9))->addMinutes(rand(0, 59));

        // ステータスをランダムに選ぶ
        $statuses = [
            Attendance::STATUS_OFF,
            Attendance::STATUS_WORKING,
            Attendance::STATUS_LEAVE,
            Attendance::STATUS_BREAK,
        ];

        return [
            'user_id' => null, // Seederで指定する
            'work_date' => $date->toDateString(),
            'clock_in' => $clockIn,
            'clock_out' => $clockOut,
            'status' => $this->faker->randomElement($statuses),
            'note' => $this->faker->optional(0.3)->sentence(), // 30%の確率でメモつき
        ];
    }
}
