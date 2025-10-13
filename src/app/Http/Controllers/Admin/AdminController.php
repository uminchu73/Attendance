<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Http\Requests\StoreAttendanceRequest;


class AdminController extends Controller
{
    /**
     * 一覧表示
     */
    public function index(Request $request)
    {
        $date = $request->input('date', now()->format('Y-m-d'));
        $attendances = Attendance::with('user')
                        ->whereDate('work_date', $date)
                        ->get();

        return view('admin.summary', compact('attendances', 'date'));
    }

    /**
     * 詳細表示
     */
    public function show($id)
    {
        $attendance = Attendance::with('user', 'breaks')->findOrFail($id);
        $user = $attendance->user;

        return view('admin.detail', compact('attendance', 'user'));
    }

    /**
     * 修正更新
     */
    public function update(StoreAttendanceRequest $request, $id)
    {
        $attendance = Attendance::with('breaks')->findOrFail($id);

        // 承認待ちは編集不可
        if ($attendance->status === 'pending') {
            return back()->with('error', '承認待ちのため修正はできません。');
        }

        $validated = $request->validated();

        // 勤怠本体更新（出勤・退勤・備考）
        $attendance->update([
            'clock_in'  => $validated['clock_in'],
            'clock_out' => $validated['clock_out'],
            'note'      => $validated['note'], // これで備考も更新される
        ]);

        // 休憩更新
        if (isset($validated['breaks'])) {
            foreach ($validated['breaks'] as $key => $b) {
                // 新規休憩
                if ($key === 'new') {
                    if (!empty($b['start']) || !empty($b['end'])) {
                        $attendance->breaks()->create([
                            'break_start' => $b['start'] ?: null,
                            'break_end'   => $b['end'] ?: null,
                        ]);
                    }
                } else {
                    // 既存休憩の更新
                    $break = $attendance->breaks()->find($b['id'] ?? $key);
                    if ($break) {
                        $break->update([
                            'break_start' => $b['start'] ?: $break->break_start,
                            'break_end'   => $b['end'] ?: $break->break_end,
                        ]);
                    }
                }
            }
        }

        return redirect()->route('admin.detail', $id)
                        ->with('message', '勤怠を更新しました');
    }
}
