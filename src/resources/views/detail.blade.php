@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/detail.css') }}">
@endsection

@section('content')
<div class="attendance-detail">
    <h1>勤怠詳細</h1>

    @if($attendance->hasPendingRequest())
        <table class="detail-table">
            <tr><th>名前</th><td>{{ $user->name }}</td></tr>
            <tr><th>日付</th><td>{{ $attendance->work_date->format('Y年n月j日') }}</td></tr>
            <tr>
                <th>出勤・退勤</th>
                <td>
                    {{ $attendance->clock_in ? $attendance->clock_in->format('H:i') : '未入力' }} 〜
                    {{ $attendance->clock_out ? $attendance->clock_out->format('H:i') : '未入力' }}
                </td>
            </tr>
            <tr>
                <th>休憩</th>
                <td>
                    @foreach($attendance->breaks as $break)
                        {{ $break->break_start?->format('H:i') ?? '' }} 〜 {{ $break->break_end?->format('H:i') ?? '' }}
                        @if(!$loop->last)<br>@endif
                    @endforeach
                </td>
            </tr>
            <tr><th>備考</th><td>{{ $attendance->note ?? 'なし' }}</td></tr>
        </table>
        <p>承認待ちのため申請できません。</p>
    @else
        <form action="{{ route('attendance.request', $attendance->id) }}" method="POST">
            @csrf
            <table class="detail-table">
                <tr><th>名前</th><td>{{ $user->name }}</td></tr>
                <tr><th>日付</th><td>{{ $attendance->work_date->format('Y年n月j日') }}</td></tr>
                <tr>
                    <th>出勤・退勤</th>
                    <td>
                        <input type="time" name="clock_in" value="{{ old('clock_in', $attendance->clock_in ? \Carbon\Carbon::parse($attendance->clock_in)->format('H:i') : '') }}">
                        〜
                        <input type="time" name="clock_out" value="{{ old('clock_out', $attendance->clock_out ? \Carbon\Carbon::parse($attendance->clock_out)->format('H:i') : '') }}">
                    </td>
                </tr>
                <tr>
                    <th>休憩</th>
                    <td>
                        @foreach($attendance->breaks as $i => $break)
                            <input type="time" name="breaks[{{ $i }}][start]" value="{{ old("breaks.$i.start", $break->break_start ? \Carbon\Carbon::parse($break->break_start)->format('H:i') : '') }}">
                            〜
                            <input type="time" name="breaks[{{ $i }}][end]" value="{{ old("breaks.$i.end", $break->break_end ? \Carbon\Carbon::parse($break->break_end)->format('H:i') : '') }}">
                        @endforeach
                        {{-- 新しい休憩 --}}
                        <input type="time" name="breaks[new][start]" placeholder="開始">
                        〜
                        <input type="time" name="breaks[new][end]" placeholder="終了">
                    </td>
                </tr>
                <tr>
                    <th>備考</th>
                    <td><textarea name="note">{{ old('note', $attendance->note) }}</textarea></td>
                </tr>
            </table>
            <button type="submit" class="btn btn-primary">修正申請</button>
        </form>
    @endif
</div>

@endsection