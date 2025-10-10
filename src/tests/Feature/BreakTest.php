<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\AttendanceBreak;
use Carbon\Carbon;


class BreakTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 休憩ボタンが正しく機能する
     */
    public function test_break_in_button()
    {
        //出勤中ユーザーを作成
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'status' => Attendance::STATUS_WORKING, // 出勤中
        ]);

        //出勤中の状態で画面を開く
        $response = $this->actingAs($user)->get('/attendance');
        $response->assertStatus(200);
        $response->assertSee('休憩入'); //休憩ボタンが表示されている

        //休憩処理を行う
        $response = $this->actingAs($user)->post('/attendance/break-in');

        //リダイレクト確認
        $response->assertRedirect('/attendance');

        //ステータスが「休憩中」に変わっていることを確認
        $this->assertDatabaseHas('attendances', [
            'user_id' => $user->id,
            'status' => Attendance::STATUS_BREAK,
        ]);
    }

    /**
     * 休憩は一日に何回でもできる
     */
    public function test_many_breaks_in_a_day()
    {
        $user = \App\Models\User::factory()->create();

        //出勤中ステータスを持つ勤怠データを作成
        $attendance = \App\Models\Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => \Carbon\Carbon::today()->toDateString(),
            'status' => \App\Models\Attendance::STATUS_WORKING,
        ]);

        //ログインして休憩入を実行
        $this->actingAs($user)->post('/attendance/break-in');
        $this->assertDatabaseHas('attendances', [
            'user_id' => $user->id,
            'status' => \App\Models\Attendance::STATUS_BREAK,
        ]);

        //休憩戻を実行（出勤中に戻る）
        $this->actingAs($user)->post('/attendance/break-out');
        $this->assertDatabaseHas('attendances', [
            'user_id' => $user->id,
            'status' => \App\Models\Attendance::STATUS_WORKING,
        ]);

        //画面に「休憩入」ボタンが再表示される
        $page = $this->actingAs($user)->get('/attendance');
        $page->assertStatus(200);
        $page->assertSee('休憩入');
    }

    /**
     * 休憩戻ボタンが正しく機能する
     */
    public function test_break_out_button()
    {
        $user = \App\Models\User::factory()->create();

        //出勤中ユーザーの勤怠データを作成
        $attendance = \App\Models\Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => \Carbon\Carbon::today()->toDateString(),
            'status' => \App\Models\Attendance::STATUS_WORKING,
        ]);

        //休憩入処理（休憩中へ）
        $this->actingAs($user)->post('/attendance/break-in');
        $this->assertDatabaseHas('attendances', [
            'user_id' => $user->id,
            'status' => \App\Models\Attendance::STATUS_BREAK,
        ]);

        //画面に休憩戻ボタンが表示されることを確認
        $page = $this->actingAs($user)->get('/attendance');
        $page->assertStatus(200);
        $page->assertSee('休憩戻');

        //休憩戻処理（出勤中へ戻る）
        $response = $this->actingAs($user)->post('/attendance/break-out');
        $response->assertRedirect('/attendance'); //正常遷移

        //ステータスが「出勤中」に戻ったことを確認
        $this->assertDatabaseHas('attendances', [
            'user_id' => $user->id,
            'status' => \App\Models\Attendance::STATUS_WORKING,
        ]);

    }

    /**
     * 休憩戻は一日に何回でもできる
     */
    public function test_many_breaks_out_a_day()
    {
        $user = \App\Models\User::factory()->create();

        //出勤中ステータスを持つ勤怠データを作成
        $attendance = \App\Models\Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => \Carbon\Carbon::today()->toDateString(),
            'status' => \App\Models\Attendance::STATUS_WORKING,
        ]);

        //ログインして休憩入を実行
        $this->actingAs($user)->post('/attendance/break-in');
        $this->assertDatabaseHas('attendances', [
            'user_id' => $user->id,
            'status' => \App\Models\Attendance::STATUS_BREAK,
        ]);

        //休憩戻を実行（出勤中に戻る）
        $this->actingAs($user)->post('/attendance/break-out');
        $this->assertDatabaseHas('attendances', [
            'user_id' => $user->id,
            'status' => \App\Models\Attendance::STATUS_WORKING,
        ]);

        //再度休憩入を実行して、もう一度休憩できることを確認
        $response = $this->actingAs($user)->post('/attendance/break-in');
        $response->assertRedirect('/attendance'); // 正常遷移
        $this->assertDatabaseHas('attendances', [
            'user_id' => $user->id,
            'status' => \App\Models\Attendance::STATUS_BREAK,
        ]);

        //画面に「休憩戻」ボタンが再表示される
        $page = $this->actingAs($user)->get('/attendance');
        $page->assertStatus(200);
        $page->assertSee('休憩戻');
    }

    /**
     * 休憩時刻が勤怠一覧画面で確認できる
     */
    public function test_break_times_in_summary()
    {
        $user = User::factory()->create();

        //出勤中の勤怠データを作成
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => Carbon::today()->toDateString(),
            'status' => Attendance::STATUS_WORKING,
        ]);

        //休憩入 → 休憩中に
        $this->actingAs($user)->post('/attendance/break-in');

        //DBに休憩開始時刻が記録されていることを確認
        $this->assertDatabaseHas('attendance_breaks', [
            'attendance_id' => $attendance->id,
        ]);

        $break = AttendanceBreak::where('attendance_id', $attendance->id)->first();
        $this->assertNotNull($break->break_start);

        //休憩戻 → 出勤中に戻る
        $this->actingAs($user)->post('/attendance/break-out');

        //DBに休憩終了時刻が記録されていることを確認
        $break->refresh();
        $this->assertNotNull($break->break_end);


        //勤怠一覧画面を開く
        $response = $this->actingAs($user)->get('/attendance/list');

        $response->assertStatus(200);

        //休憩時刻が画面に表示されていることを確認
        $response->assertSee($attendance->fresh()->break_time);
    }

}
