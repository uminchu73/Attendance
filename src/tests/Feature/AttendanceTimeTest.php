<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Carbon\Carbon;

class AttendanceTimeTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 現在の日時情報がUIと同じ形式で出力されている
     */
    public function test_attendance_time()
    {
        // ユーザー作成・ログイン
        $user = User::factory()->create();
        $this->actingAs($user);

        // 現在時刻を固定（Carbonでテストの安定性アップ）
        Carbon::setTestNow($now = Carbon::now());

        // 日本語曜日配列
        $weekdayMap = ['日','月','火','水','木','金','土'];

        $formattedDate = $now->format('Y年n月j日') . '(' . $weekdayMap[$now->dayOfWeek] . ')';
        $formattedTime = $now->format('H:i');


        // 勤怠打刻画面にアクセス
        $response = $this->get('/attendance');

        // 画面に現在時刻が表示されているか確認
        $response->assertSee($formattedDate);
        $response->assertSee($formattedTime);
    }
}
