@extends('layouts.app')

    @section('css')
        <link rel="stylesheet" href="{{ asset('css/index.css') }}">
    @endsection


@section('content')
    <div class="attendance-container">
        {{-- ステータス表示 --}}
        <div class="status-badge">
            {{ $status }}
        </div>

        {{-- 日付表示 --}}
        <div class="date-display" id="current-date">
            {{ $currentDate }}
        </div>

        {{-- 時刻表示 --}}
        <div class="time-display" id="current-time">
            {{ $currentTime }}
        </div>

        {{-- 打刻ボタン --}}
        <div class="button-group">
            @if($status === '勤務外')
                {{-- 出勤ボタン --}}
                <form method="POST" action="">
                    @csrf
                    <button type="submit" class="attendance-button">
                        出勤
                    </button>
                </form>

            @elseif($status === '出勤中')
                {{-- 休憩入ボタン --}}
                <form method="POST" action="">
                    @csrf
                    <button type="submit" class="attendance-button break-button">
                        休憩入
                    </button>
                </form>

                {{-- 退勤ボタン --}}
                <form method="POST" action="">
                    @csrf
                <button type="submit" class="attendance-button clock-out-button">
                    退勤
                </button>
            </form>

            @elseif($status === '休憩中')
                {{-- 休憩戻ボタン --}}
                <form method="POST" action="">
                    @csrf
                    <button type="submit" class="attendance-button break-return-button">
                        休憩戻
                    </button>
                </form>

            @elseif($status === '退勤済')
                {{-- 完了メッセージ --}}
                <div class="completion-message">
                    お疲れ様でした。
                </div>
            @endif
        </div>
    </div>

@endsection
