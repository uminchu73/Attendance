<!-- 共通レイアウトのHTML -->
<!DOCTYPE html>
<html lang="ja">

<head>
    <!-- 文字コードやレスポンシブ対応 -->
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <!-- CSRFトークン -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- ページタイトル -->
    <title>coachtech勤怠</title>

    <!-- 共通CSSの読み込み -->
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/common.css') }}" />

    <!-- 各ページごとの追加CSS -->
    @yield('css')
</head>

<body>
    {{-- ヘッダー --}}
    <header class="header">
        <div class="header__inner">
            <a class="header__logo" href="/">
                <img src="{{ asset('images/logo.svg') }}" alt="COACHTECH">
            </a>
            <nav>
                <ul class="header-nav">
                    {{-- ログイン時のみ表示 --}}
                    @auth
                    {{-- 勤怠ボタン --}}
                    <li class="header-nav__item">
                        <a class="attendance" href="">勤怠</a>
                    </li>
                    {{-- 勤怠一覧ボタン --}}
                    <li class="header-nav__item">
                        <a class="summary_button" href="/attendance/list">勤怠一覧</a>
                    </li>
                    {{-- 申請ボタン --}}
                    <li class="header-nav__item">
                        <a class="request_button" href="">申請</a>
                    </li>
                    <li class="header-nav__item">
                        {{-- ログアウトボタン --}}
                        <form class="form" action="/logout" method="post">
                            @csrf
                            <button class="logout_button">ログアウト</button>
                        </form>
                    </li>
                    @endauth
                </ul>
            </nav>
        </div>
    </header>

    <!-- ページごとの中身を表示 -->
    <main>
        @yield('content')

    </main>
</body>

</html>
