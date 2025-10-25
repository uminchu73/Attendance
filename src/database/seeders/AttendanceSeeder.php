<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Attendance;
use App\Models\AttendanceBreak;
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
        $users = User::all();

        //勤怠メモの候補を準備
        $notes = [
            '遅刻（電車遅延）',
            '早出対応（開店準備）',
            '体調不良で早退',
            '通常勤務',
            '外出対応（客先訪問）',
            '残業1時間',
            'システムトラブル対応',
            '会議対応'
        ];

        foreach ($users as $user) {
            //過去60日分の日付を生成
            $dates = collect(range(1, 60))->map(fn($i) => Carbon::today()->subDays($i));

            foreach ($dates as $date) {
                //７0%の確率で勤怠データを作る
                if (rand(1, 100) <= 70) {
                    $attendance = Attendance::factory()->create([
                        'user_id' => $user->id,
                        'work_date' => $date->toDateString(),
                        'note' => rand(1, 100) <= 30 ? $notes[array_rand($notes)] : null,
                    ]);

                    //昼休み（必ず）
                    $attendance->breaks()->create([
                        'break_start' => '12:00:00',
                        'break_end'   => '13:00:00',
                    ]);

                    // 午後の小休憩（50%くらいの確率）
                    if (rand(0, 1)) {
                        $hour = rand(15, 16); // 15〜16時
                        $attendance->breaks()->create([
                            'break_start' => sprintf('%02d:00:00', $hour),
                            'break_end'   => sprintf('%02d:15:00', $hour), // 15分
                        ]);
                    }
                }
            }
        }
    }
}