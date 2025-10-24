<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\AttendanceRequest;
use App\Http\Requests\StoreAttendanceRequest;
use App\Http\Requests\AttendanceBreakRequest;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;


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
            'clock_in'  => $data['clock_in'] ? date('H:i:s', strtotime($data['clock_in'])) : null,
            'clock_out' => $data['clock_out'] ? date('H:i:s', strtotime($data['clock_out'])) : null,
            'note' => $data['note'] ?? null,
            'status' => AttendanceRequest::STATUS_PENDING,
        ]);

        //休憩も申請用に作成
        if (!empty($data['breaks'])) {
            foreach ($data['breaks'] as $key => $break) {
                //startとendが空ならスキップ
                if (empty($break['start']) && empty($break['end'])) {
                    continue;
                }

                \App\Models\AttendanceBreak::create([
                    'attendance_id' => $attendance->id,           // 元勤怠ID
                    'attendance_request_id' => $attendanceRequest->id, // 修正申請ID
                    'break_start' => $break['start'] ?: null,
                    'break_end'   => $break['end'] ?: null,
                ]);
            }
        }

        return redirect()->back()->with('message', '修正を申請しました！');
    }



    /**
     * 申請一覧
     */
    public function requestsList(Request $request)
    {
        $user = Auth::user();
        $status = $request->query('status'); //URLパラメータ取得

        //管理者の場合
        if (Auth::guard('admin')->check()) {
            $pendingRequests = AttendanceRequest::with('attendance.user')
                ->where('status', AttendanceRequest::STATUS_PENDING)
                ->latest()
                ->get();

            $approvedRequests = AttendanceRequest::with('attendance.user')
                ->where('status', AttendanceRequest::STATUS_APPROVED)
                ->latest()
                ->get();

            $status = $request->query('status', 'pending'); //デフォルトは pending
            return view('admin.request-list', compact('pendingRequests', 'approvedRequests', 'status'));
        }

        //一般ユーザーの場合（
        $query = AttendanceRequest::with('attendance.user')
        ->where('user_id', $user->id)
        ->orderBy('created_at', 'desc');


        //パラメータに応じて絞り込み
        if ($status === 'pending') {
            $query->where('status', AttendanceRequest::STATUS_PENDING);
        } elseif ($status === 'approved') {
            $query->where('status', AttendanceRequest::STATUS_APPROVED);
        }
            $requests = $query->get();

        return view('request', compact('requests', 'status'));
    }

    /**
     * 管理者による申請承認画面
     */
    public function approve($attendance_correct_request_id)
    {
        if (!Auth::guard('admin')->check()) {
            abort(403, '権限がありません。');
        }

        $requestData = AttendanceRequest::with(['attendance.user', 'attendanceBreaks'])->findOrFail($attendance_correct_request_id);


        $attendance = $requestData->attendance;
        $user = $attendance->user;

        return view('admin.approve', compact('requestData', 'attendance', 'user'));
    }

    /**
     * 管理者による承認
     */

    public function approveSubmit($id)
    {
        if (!Auth::guard('admin')->check()) {
            abort(403, '権限がありません。');
        }

        $requestData = AttendanceRequest::with(['attendance', 'attendanceBreaks'])->findOrFail($id);
        $attendance = $requestData->attendance;

        // ① 勤怠の基本情報を更新
        $attendance->update([
            'clock_in'  => $requestData->clock_in,
            'clock_out' => $requestData->clock_out,
            'note'      => $requestData->note,
        ]);

        // ② 既存の休憩データを削除して、申請された休憩を反映
        $attendance->breaks()->delete();

        unset($attendance->breaks);
        $attendance->refresh();

        // attendance_breaks に申請時の休憩が存在する場合のみ反映
        $breaks = \App\Models\AttendanceBreak::where('attendance_request_id', $requestData->id)->get();

        foreach ($breaks as $break) {
            //Carbonでも文字列でも対応できるように変換
            $start = $break->break_start instanceof Carbon
                ? $break->break_start->format('H:i:s')
                : (string)$break->break_start;

            $end = $break->break_end instanceof Carbon
                ? $break->break_end->format('H:i:s')
                : (string)$break->break_end;

            \App\Models\AttendanceBreak::create([
                'attendance_id'         => $attendance->id,
                'attendance_request_id' => null,
                'break_start'           => $start ?: null,
                'break_end'             => $end ?: null,
            ]);
        }

        // ステータスを承認済みに変更
        $requestData->update([
            'status' => AttendanceRequest::STATUS_APPROVED,
        ]);

        $updatedRequest = AttendanceRequest::with(['attendance.user', 'attendanceBreaks'])
            ->findOrFail($requestData->id);

        return view('admin.approve', [
            'requestData' => $updatedRequest,
            'attendance'  => $attendance,
            'user'        => $attendance->user,
        ])->with('message', '申請を承認しました！');

    }

}
