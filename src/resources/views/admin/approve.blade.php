@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin-approve.css') }}">
@endsection

@section('content')
    <div class="alert">
        {{-- メッセージ表示 --}}
        @if(session('message'))
            <div class="alert-success">
                {{ session('message') }}
            </div>
        @endif
    </div>
    <div class="attendance-detail">
        <h1>勤怠詳細</h1>
        <table class="detail-table">
            <tr>
                <th>名前</th>
                <td>
                    {{ $user->name }}
                </td>
            </tr>
            <tr>
                <th>日付</th>
                <td>
                    {{ $attendance->work_date->format('Y年n月j日') }}
                </td>
            </tr>
            <tr>
                <th>出勤・退勤</th>
                <td>
                    {{ $requestData->clock_in ? \Carbon\Carbon::parse($requestData->clock_in)->format('H:i') : '変更なし' }}〜
                    {{ $requestData->clock_out ? \Carbon\Carbon::parse($requestData->clock_out)->format('H:i') : '変更なし' }}
                </td>
            </tr>
            <tr>
                <th>休憩</th>
                <td>
                    @forelse($requestData->attendanceBreaks as $break)
                        {{ $break->break_start ? $break->break_start->format('H:i') : '' }} 〜
                        {{ $break->break_end ? $break->break_end->format('H:i') : '' }}
                        @if(!$loop->last)<br>@endif
                    @empty
                        変更なし
                    @endforelse
                </td>
            </tr>
            <tr>
                <th>備考</th>
                <td>{{ $requestData->note ?? 'なし' }}</td>
            </tr>
        </table>
        {{-- 状態によってボタン切り替え --}}
        @if ($requestData->status === \App\Models\AttendanceRequest::STATUS_PENDING)
            <form method="POST" action="{{ route('admin.request.approve.submit', $requestData->id) }}">
                @csrf
                <button type="submit" class="approve-btn">承認する</button>
            </form>
        @elseif ($requestData->status === \App\Models\AttendanceRequest::STATUS_APPROVED)
            <button type="button" class="complete-btn" disabled>承認済み</button>
        @endif
    </div>

@endsection