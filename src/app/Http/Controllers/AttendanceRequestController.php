<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\AttendanceRequest;
use App\Http\Requests\StoreAttendanceRequest;
use App\Http\Requests\AttendanceBreakRequest;
use Illuminate\Support\Facades\Auth;


class AttendanceRequestController extends Controller
{
    /**
     * 修正申請作成
     */
    public function store(StoreAttendanceRequest $request, $attendanceId)
    {
        $attendance = Attendance::findOrFail($attendanceId);

        if ($attendance->hasPendingRequest()) {
            return redirect()->back()->with('error', '承認待ちのため申請できません。');
        }

        $data = $request->validated();

        $attendanceRequest = AttendanceRequest::create([
            'user_id' => Auth::id(),
            'attendance_id' => $attendance->id,
            'type' => '修正',
            'clock_in' => $data['clock_in'] ?? null,
            'clock_out' => $data['clock_out'] ?? null,
            'note' => $data['note'] ?? null,
            'status' => AttendanceRequest::STATUS_PENDING,
        ]);


        return redirect()->back()->with('message', '修正を申請しました！');
    }



    /**
     * 申請一覧
     */
    public function requestsList(Request $request)
    {
        $user = Auth::user();
        $status = $request->query('status'); // ← URLパラメータ取得

        $query = AttendanceRequest::with('attendance.user')
        ->orderBy('created_at', 'desc');

        // パラメータに応じて絞り込み
        if ($status === 'pending') {
            $query->where('status', AttendanceRequest::STATUS_PENDING);
        } elseif ($status === 'approved') {
            $query->where('status', AttendanceRequest::STATUS_APPROVED);
        }
            $requests = $query->get();

        return view('request', compact('requests', 'status'));
    }
}
