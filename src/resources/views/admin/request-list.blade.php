@extends('layouts.app')

    @section('css')
        <link rel="stylesheet" href="{{ asset('css/admin-request-list.css') }}">
    @endsection

@section('content')
<div class="request-container">
    <h1>申請一覧</h1>

    <div class="filter-links">
        <a href="{{ route('admin.request.list', ['status' => 'pending']) }}" class="{{ $status === 'pending' ? 'active' : '' }}">承認待ち</a>
        <a href="{{ route('admin.request.list', ['status' => 'approved']) }}" class="{{ $status === 'approved' ? 'active' : '' }}">承認済み</a>
    </div>

    <table class="request-table">
        <thead>
            <tr>
                <th>状態</th>
                <th>名前</th>
                <th>対象日時</th>
                <th>申請理由</th>
                <th>申請日時</th>
                <th>詳細</th>
            </tr>
        </thead>
        <tbody>
            @php
                $currentRequests = ($status ?? 'pending') === 'pending' ? $pendingRequests : $approvedRequests;
            @endphp
            @forelse($currentRequests as $request)
                <tr>
                    <td>{{ $request->status_label }}</td>
                    <td>{{ $request->attendance->user->name }}</td>
                    <td>{{ \Carbon\Carbon::parse($request->attendance->work_date)->format('Y/m/d') }}</td>
                    <td>{{ $request->note ?? 'なし' }}
                    </td>
                    <td>{{ $request->created_at->format('Y/m/d') }}</td>
                    <td><a href="{{ route('admin.request.approve', $request->id) }}">詳細</a></td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">申請はありません</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
