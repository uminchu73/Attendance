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
        return [
            'user_id' => null, // テスト時に指定する
            'work_date' => Carbon::today()->toDateString(),
            'status' => Attendance::STATUS_OFF, // デフォルトは勤務外
        ];
    }
}
