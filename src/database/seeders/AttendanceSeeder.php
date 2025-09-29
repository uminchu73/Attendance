<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;


class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //初期ユーザー取得
        $user = User::first();
        if (!$user) return;

        $months = [
            Carbon::now()->subMonth(),
            Carbon::now()->subMonth(2)
        ];

        foreach ($months as $month) {
            $start = $month->copy()->startOfMonth();
            $end   = $month->copy()->endOfMonth();

            for ($date = $start; $date->lte($end); $date->addDay()) {

                //土日を休みにする
                if ($date->isWeekend()) {
                    continue; // スキップ
                }
                //平日の勤怠作成
                $attendance = Attendance::create([
                    'user_id'   => $user->id,
                    'work_date' => $date->toDateString(),
                    'clock_in'  => $date->copy()->setHour(9)->setMinute(0)->format('H:i:s'),
                    'clock_out' => $date->copy()->setHour(18)->setMinute(0)->format('H:i:s'),
                    'status'    => Attendance::STATUS_LEAVE,
                ]);

                //休憩
                $attendance->breaks()->create([
                    'break_start' => $date->copy()->setHour(12)->setMinute(0)->format('H:i:s'),
                    'break_end'   => $date->copy()->setHour(13)->setMinute(0)->format('H:i:s'),
                ]);
            }
        }
    }
}