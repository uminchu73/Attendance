@extends('layouts.app')

    @section('css')
        <link rel="stylesheet" href="{{ asset('css/admin-summary.css') }}">
    @endsection


@section('content')

    <div class="attendance-container">
        <h1>{{ \Carbon\Carbon::parse($date)->format('Y年m月d日の勤怠') }}</h1>

        <div class="day-switch">
            <a href="{{ route('admin.summary', ['date' => \Carbon\Carbon::parse($date)->subDay()->format('Y-m-d')]) }}">前日</a>
            <form method="get" style="display:inline;">
                <input type="date" name="date" value="{{ $date }}" onchange="this.form.submit()">
            </form>
            <a href="{{ route('admin.summary', ['date' => \Carbon\Carbon::parse($date)->addDay()->format('Y-m-d')]) }}">翌日</a>
        </div>

        <table class="attendance-table">
            <thead>
                <tr>
                    <th>名前</th>
                    <th>出勤</th>
                    <th>退勤</th>
                    <th>休憩</th>
                    <th>合計</th>
                    <th>詳細</th>
                </tr>
            </thead>
            <tbody>
                @foreach($attendances as $attendance)
                    <tr>
                        <td>{{ $attendance->user->name }}</td>
                        <td>{{ \Carbon\Carbon::parse($attendance->clock_in)->format('H:i') }}</td>
                        <td>{{ \Carbon\Carbon::parse($attendance->clock_out)->format('H:i') }}</td>
                        <td>{{ $attendance->break_time ?? '0:00' }}</td>
                        <td>{{ $attendance->work_time ?? '' }}</td>
                        <td>
                            <a href="{{ route('admin.detail', $attendance->id) }}">詳細</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
