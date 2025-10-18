<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;



class AdminStaffController extends Controller
{
    /**
     * スタッフ一覧表示
     */
    public function index()
    {
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


    /**
     * CSV出力
     */
    public function exportCsv($id, Request $request)
    {
        $user = User::findOrFail($id);

        //月の指定
        $month = $request->input('month', Carbon::now()->format('Y-m'));
        $startOfMonth = Carbon::parse($month)->startOfMonth();
        $endOfMonth   = Carbon::parse($month)->endOfMonth();

        $attendances = Attendance::where('user_id', $user->id)
            ->whereBetween('work_date', [$startOfMonth, $endOfMonth])
            ->get();

        //CSVヘッダー
        $headers = ['日付', '出勤', '退勤', '休憩', '合計', '備考'];

        // CSV データ作成
        $csvData = [];
        foreach ($attendances as $att) {
            $csvData[] = [
                $att->work_date->format('Y-m-d'),
                $att->clock_in ? $att->clock_in->format('H:i') : '',
                $att->clock_out ? $att->clock_out->format('H:i') : '',
                $att->break_time,
                $att->work_time,
                $att->note ?? '',
            ];
}

        // CSV 文字列作成
        $callback = function() use ($headers, $csvData) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        $filename = $user->name . '_勤怠_' . $month . '.csv';

        return Response::stream($callback, 200, [
            "Content-Type" => "text/csv",
            "Content-Disposition" => "attachment; filename={$filename}",
        ]);
    }

}
