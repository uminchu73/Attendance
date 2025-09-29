@extends('layouts.app')

    @section('css')
        <link rel="stylesheet" href="{{ asset('css/summary.css') }}">
    @endsection

@section('content')
<div class="attendance-container">
    <h2>勤務一覧</h2>

    <div class="month-switch">
        <a href="?month={{ \Carbon\Carbon::parse($month)->subMonth()->format('Y-m') }}">← 前月</a>
        <span>{{ \Carbon\Carbon::parse($month)->format('Y/m') }}</span>
        <a href="?month={{ \Carbon\Carbon::parse($month)->addMonth()->format('Y-m') }}">翌月 →</a>
    </div>

    <table class="attendance-table">
        <thead>
            <tr>
                <th>日付</th>
                <th>出勤</th>
                <th>退勤</th>
                <th>休憩</th>
                <th>合計</th>
                <th>詳細</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dates as $date)
                @php $attendance = $attendances[$date->toDateString()] ?? null; @endphp
                <tr>
                    <td>{{ $date->format('m/d') }}({{ $date->locale('ja')->isoFormat('ddd') }})</td>
                    <td>{{ $attendance?->clock_in ? \Carbon\Carbon::parse($attendance->clock_in)->format('H:i') : '' }}</td>
                    <td>{{ $attendance?->clock_out ? \Carbon\Carbon::parse($attendance->clock_out)->format('H:i') : '' }}</td>
                    <td>{{ $attendance?->break_time ?? '0:00' }}</td>
                    <td>{{ $attendance?->work_time ?? '' }}</td>
                    <td>
                        @if($attendance)
                            <a href="{{ route('attendance.detail', $attendance->id) }}">詳細</a>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
