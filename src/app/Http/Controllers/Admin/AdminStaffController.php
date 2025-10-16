<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;



class AdminStaffController extends Controller
{
    public function index()
    {
        /**
         * スタッフ一覧表示
         */
        // 一般ユーザーのみ取得
        $users = User::all();

        return view('admin.staff-list', compact('users'));
    }

    /**
     * 月次勤怠詳細画面
     */
    public function show($id, Request $request)

    {
        $user = User::findOrFail($id);

        //月の指定（デフォルトは今月）
        $month = $request->input('month', Carbon::now()->format('Y-m'));
        $startOfMonth = Carbon::parse($month)->startOfMonth();
        $endOfMonth   = Carbon::parse($month)->endOfMonth();

        //カレンダーを作る
        $dates = [];
        for ($date = $startOfMonth->copy(); $date->lte($endOfMonth); $date->addDay()) {
            $dates[] = $date->copy();
        }

        //ユーザーの勤怠データを取得
        $attendances = Attendance::where('user_id', $user->id)
            ->whereBetween('work_date', [$startOfMonth, $endOfMonth])
            ->get()
            ->keyBy(fn($item) => $item->work_date->toDateString());


        Carbon::setLocale('ja');

        return view('admin.staff-attendance', [
            'user'        => $user,
            'dates'       => $dates,
            'attendances' => $attendances,
            'month'       => $month,
        ]);
    }
}
