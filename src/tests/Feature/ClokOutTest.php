<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;


class ClokOutTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 退勤ボタンが正しく機能する
     */
    public function test_clock_out_button()
    {
        $user = User::factory()->create();

        //出勤済みの勤怠データを作成（8時間前に出勤）
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'clock_in' => Carbon::now()->subHours(8),
            'status' => Attendance::STATUS_WORKING,
        ]);

        // 勤務中状態
        $this->actingAs($user)->get('/attendance')
            ->assertStatus(200)
            ->assertSee('退勤');

        //退勤処理を実行
        $response = $this->post('/attendance/clock-out');

        //リダイレクト確認
        $response->assertRedirect('/attendance');

        //DB確認
        $this->assertDatabaseHas('attendances', [
            'id' => $attendance->id,
            'status' => Attendance::STATUS_LEAVE,
        ]);

        //画面にも「退勤済」が表示される
        $this->actingAs($user)->get('/attendance')
            ->assertSee('退勤済');
    }

    /**
     * 退勤時刻が勤怠一覧画面で確認できる
     */
    public function test_clock_out_time_appears()
    {
        $user = User::factory()->create();

        //出勤
        $this->actingAs($user)->post('/attendance/clock-in');

        //退勤
        $this->actingAs($user)->post('/attendance/clock-out');

        //登録された勤怠レコードを取得
        $attendance = Attendance::first();

        //勤怠一覧を確認
        $response = $this->actingAs($user)->get('/attendance/list');

        $response->assertStatus(200);
        $response->assertSee(Carbon::parse($attendance->clock_out)->format('H:i'));
    }
}
