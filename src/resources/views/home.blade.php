@extends('layouts.app')

    @section('css')
        <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    @endsection

@section('content')
    <div class="home-container">
        <a href="{{ route('admin.login') }}" class="admin-link">
            管理者ログイン
        </a>
        <a href="{{ route('login') }}" class="user-link">
            一般ユーザーログイン
        </a>
    </div>

@endsection