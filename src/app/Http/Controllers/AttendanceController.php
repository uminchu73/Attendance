<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\AttendanceBreak;
use App\Models\AttendanceRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;


class AttendanceController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $today = Carbon::today()->toDateString();

        $attendance = Attendance::where('user_id', $user->id)
            ->where('work_date', $today)
            ->first();

        Carbon::setLocale('ja');

        return view('index', [
            'attendance'   => $attendance,
            'status'       => $attendance ? $attendance->status_label : '勤務外',
            'currentDate'  => Carbon::now()->isoFormat('YYYY年M月D日(ddd)'),
            'currentTime'  => Carbon::now()->format('H:i'),
        ]);
    }

    /**
     * 出勤
     */
    public function clockIn(Request $request)
    {
        $attendance = Attendance::todayFor(Auth::user());

        try {
            $attendance->clockIn();
            return redirect('/attendance')->with('message', '出勤完了しました！');
        } catch (\Exception $e) {
            return redirect('/attendance')->with('error', $e->getMessage());
        }
    }

    /**
     * 退勤
     */
    public function clockOut(Request $request)
    {
        $attendance = Attendance::todayFor(Auth::user());

        try {
            $attendance->clockOut();
            return redirect('/attendance')->with('message', '退勤完了しました！');
        } catch (\Exception $e) {
            return redirect('/attendance')->with('error', $e->getMessage());
        }
    }

    /**
     * 休憩入
     */
    public function breakIn(Request $request)
    {
        $attendance = Attendance::todayFor(Auth::user());

        try {
            $attendance->startBreak();
            return redirect('/attendance')->with('message', '休憩開始しました！');
        } catch (\Exception $e) {
            return redirect('/attendance')->with('error', $e->getMessage());
        }
    }

    /**
     * 休憩戻
     */
    public function breakOut(Request $request)
    {
        $attendance = Attendance::todayFor(Auth::user());

        try {
            $attendance->endBreak();
            return redirect('/attendance')->with('message', '休憩終了しました！');
        } catch (\Exception $e) {
            return redirect('/attendance')->with('error', $e->getMessage());
        }
    }

    /**
     * 一覧表示
     */
    public function list(Request $request)
    {
        $user = Auth::user();

        // 月の指定（デフォルトは今月）
        $month = $request->input('month', Carbon::now()->format('Y-m'));
        $startOfMonth = Carbon::parse($month)->startOfMonth();
        $endOfMonth   = Carbon::parse($month)->endOfMonth();

            // その月の全日付を生成
        $dates = [];
        for ($date = $startOfMonth->copy(); $date->lte($endOfMonth); $date->addDay()) {
            $dates[] = $date->copy();
        }

        // ユーザーの勤怠データを取得
        $attendances = Attendance::where('user_id', $user->id)
            ->whereBetween('work_date', [$startOfMonth, $endOfMonth])
            ->get()
            ->keyBy(fn($item) => $item->work_date->toDateString());


        Carbon::setLocale('ja');

        return view('summary', [
            'dates'       => $dates,
            'attendances' => $attendances,
            'month'       => $month,
        ]);
    }


    /**
     * 詳細表示
     */
    public function show($idOrDate)
    {
        $user = Auth::user();
        $attendance = Attendance::findOrCreateForUser($user->id, $idOrDate);

        if (!$attendance) {
            abort(404);
        }

        return view('detail', compact('attendance', 'user'));
    }



}
