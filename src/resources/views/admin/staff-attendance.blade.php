@extends('layouts.app')

    @section('css')
        <link rel="stylesheet" href="{{ asset('css/admin-staff-attendance.css') }}">
    @endsection

@section('content')
<div class="staff-attendance-container">
    <h1>{{ $user->name }} さんの勤怠一覧</h1>

    <div class="month-switch">
        <a href="?month={{ \Carbon\Carbon::parse($month)->subMonth()->format('Y-m') }}">← 前月</a>
        <form method="get" style="display:inline;">
            <input type="month" name="month" value="{{ \Carbon\Carbon::parse($month)->format('Y-m') }}" onchange="this.form.submit()">
        </form>
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
                @php
                    $attendance = $attendances[$date->toDateString()] ?? null;
                @endphp
                <tr>
                    <td>{{ $date->format('m/d') }}({{ $date->locale('ja')->isoFormat('ddd') }})</td>
                    <td>
                        @if($attendance && $attendance->clock_in)
                            {{ \Carbon\Carbon::parse($attendance->clock_in)->format('H:i') }}
                        @endif
                    </td>
                    <td>
                        @if($attendance && $attendance->clock_out)
                            {{ \Carbon\Carbon::parse($attendance->clock_out)->format('H:i') }}
                        @endif
                    </td>
                    <td>
                        @if($attendance)
                            {{ $attendance->break_time ?? '0:00' }}
                        @endif
                    </td>
                    <td>
                        @if($attendance)
                            {{ $attendance->work_time }}
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.detail', $attendance->id ?? $date->toDateString()) }}">詳細</a>

                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {{-- CSV出力 --}}
        <form method="POST" action="{{ route('admin.staff.export', $user->id) }}">
            @csrf
            <input type="hidden" name="month" value="{{ $month }}">
            <button type="submit" class="btn-export">CSV出力</button>
        </form>
</div>
@endsection
