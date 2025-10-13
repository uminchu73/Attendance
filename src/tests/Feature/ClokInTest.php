<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;


class ClokInTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 出勤ボタンが正しく機能する
     */
    public function test_clock_in_button()
    {
        $user = User::factory()->create();

        //勤務外状態
        $this->actingAs($user)->get('/attendance')
            ->assertStatus(200)
            ->assertSee('出勤');

        //出勤処理を実行
        $response = $this->post('/attendance/clock-in');

        //リダイレクト確認
        $response->assertRedirect('/attendance');

        //DB確認
        $this->assertDatabaseHas('attendances', [
            'user_id' => $user->id,
            'status' => Attendance::STATUS_WORKING,
        ]);

        //画面にも「出勤中」が表示される
        $this->actingAs($user)->get('/attendance')
            ->assertSee('出勤中');
    }

    /**
     * 出勤は一日一回のみできる
     */
    public function test__clock_in_only_once()
    {
        $user = User::factory()->create();

        //すでに退勤済み
        Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => Carbon::today()->toDateString(),
            'status' => Attendance::STATUS_LEAVE,
        ]);

        //出勤ボタンが表示されないことを確認
        $this->actingAs($user)->get('/attendance')
            ->assertDontSee('出勤');
    }

    /**
     * 出勤時刻が勤怠一覧画面で確認できる
     */
    public function test_clock_in_time_appears()
    {
        $user = User::factory()->create();

        //出勤処理実行
        $response = $this->post('/attendance/clock-in');

        //勤怠一覧を確認
        $response = $this->actingAs($user)->get('/attendance/list');

        $today = Carbon::today()->format('Y-m-d');
        $time = Carbon::now()->format('H:i');

        $response->assertStatus(200)
            ->assertSee($today)
            ->assertSee(substr($time, 0, 2));
    }
}
