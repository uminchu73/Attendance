<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\AttendanceRequest;
use Illuminate\Support\Facades\Auth;


class AttendanceRequestController extends Controller
{
    /**
     * 修正申請作成
     */
    public function store(Request $request, $attendanceId)
    {
        $attendance = Attendance::findOrFail($attendanceId);

        // 承認待ちがある場合はブロック
        if ($attendance->hasPendingRequest()) {
            return redirect()->back()->with('error', '承認待ちのため申請できません。');
        }

        // バリデーション
        $request->validate([
            'clock_in' => 'required|date_format:H:i',
            'clock_out' => 'required|date_format:H:i|after:clock_in',
            'note' => 'nullable|string',
            'breaks.*.start' => 'nullable|date_format:H:i',
            'breaks.*.end'   => 'nullable|date_format:H:i|after:breaks.*.start',
        ]);

        // 修正内容をJSONにまとめる（文字列でもOK）
        $requestContent = [
            'clock_in'  => $request->clock_in,
            'clock_out' => $request->clock_out,
            'breaks'    => $request->breaks ?? [],
            'note'      => $request->note,
        ];

        // 申請作成
        AttendanceRequest::create([
            'user_id' => Auth::id(),
            'attendance_id' => $attendance->id,
            'type' => '修正',
            'request_content' => json_encode($requestContent),
            'status' => AttendanceRequest::STATUS_PENDING,
        ]);

        return redirect()->back()->with('message', '修正申請を送信しました。');
    }
}
