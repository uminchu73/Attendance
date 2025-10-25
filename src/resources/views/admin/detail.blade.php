@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin-detail.css') }}">
@endsection

@section('content')
<div class="alert">
    {{-- メッセージ表示 --}}
    @if(session('message'))
        <div class="alert-success">
            {{ session('message') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert-error">
            {{ session('error') }}
        </div>
    @endif
</div>
<div class="attendance-detail">
    <h1>勤怠詳細</h1>

    @if($attendance->hasPendingRequest())
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
                    {{ $attendance->clock_in ? $attendance->clock_in->format('H:i') : '未入力' }} 〜
                    {{ $attendance->clock_out ? $attendance->clock_out->format('H:i') : '未入力' }}
                </td>
            </tr>
            <tr>
                <th>休憩</th>
                <td>
                    @foreach($attendance->breaks as $break)
                        {{ $break->break_start ? \Carbon\Carbon::parse($break->break_start)->format('H:i') : '' }} 〜
                        {{ $break->break_end ? \Carbon\Carbon::parse($break->break_end)->format('H:i') : '' }}
                        @if(!$loop->last)<br>@endif
                    @endforeach
                </td>
            </tr>
            <tr>
                <th>備考</th>
                <td>{{ $attendance->note ?? 'なし' }}</td>
            </tr>
        </table>
        <p>* 承認待ちのため修正はできません。</p>
    @else
        <form action="{{ route('admin.attendance.update', $attendance->id) }}" method="POST">
            @csrf
            <table class="detail-table">
                <tr><th>名前</th><td>{{ $user->name }}</td></tr>
                <tr><th>日付</th><td>{{ $attendance->work_date->format('Y年n月j日') }}</td></tr>
                <tr>
                    <th>出勤・退勤</th>
                    <td>
                        <input type="time" name="clock_in" value="{{ old('clock_in', $attendance->clock_in ? \Carbon\Carbon::parse($attendance->clock_in)->format('H:i') : '') }}">
                        <span class="time-separator">〜</span>
                        <input type="time" name="clock_out" value="{{ old('clock_out', $attendance->clock_out ? \Carbon\Carbon::parse($attendance->clock_out)->format('H:i') : '') }}">
                        @error('clock_in')
                            <div class="error">{{ $message }}</div>
                        @enderror
                        @error('clock_out')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </td>
                </tr>
                @foreach($attendance->breaks as $i => $break)
                    <tr>
                        <th>休憩</th>
                        <td>
                            <input type="hidden" name="breaks[{{ $i }}][id]" value="{{ $break->id }}">
                            <input type="time" name="breaks[{{ $i }}][start]" value="{{ old("breaks.$i.start", $break->break_start ? \Carbon\Carbon::parse($break->break_start)->format('H:i') : '') }}">
                            <span class="time-separator">〜</span>
                            <input type="time" name="breaks[{{ $i }}][end]" value="{{ old("breaks.$i.end", $break->break_end ? \Carbon\Carbon::parse($break->break_end)->format('H:i') : '') }}">
                            @error("breaks.$i.start")
                                <div class="error">{{ $message }}</div>
                            @enderror
                            @error("breaks.$i.end")
                                <div class="error">{{ $message }}</div>
                            @enderror
                        </td>
                    </tr>
                @endforeach

                {{-- 新しい休憩 --}}
                <tr>
                    <th>追加休憩</th>
                    <td>
                        <input type="time" name="breaks[new][start]" placeholder="開始">
                        <span class="time-separator">〜</span>
                        <input type="time" name="breaks[new][end]" placeholder="終了">
                        @error("breaks.new.start")
                            <div class="error">{{ $message }}</div>
                        @enderror
                        @error("breaks.new.end")
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </td>
                </tr>
                <tr>
                    <th>備考</th>
                    <td>
                        <textarea name="note" class="request_content">{{ old('note', $attendance->note) }}</textarea>
                        @error('note')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </td>
                </tr>
            </table>
            <button type="submit" class="btn btn-primary">修正</button>
        </form>
    @endif
</div>

@endsection