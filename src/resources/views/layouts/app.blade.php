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
                    {{-- 管理者用メニュー --}}
                    @auth('admin')
                        <li class="header-nav__item">
                            <a class="attendance_list" href="{{ route('admin.summary') }}">勤怠一覧</a>
                        </li>
                        <li class="header-nav__item">
                            <a class="stuff_list" href="#">スタッフ一覧</a>
                        </li>
                        <li class="header-nav__item">
                            <a class="request_list" href="#">申請一覧</a>
                        </li>
                        <li class="header-nav__item">
                            <form action="{{ route('admin.logout') }}" method="POST">
                                @csrf
                                <button class="logout_button" type="submit">ログアウト</button>
                            </form>
                        </li>
                    {{-- 一般ユーザー用メニュー --}}
                    @elseauth('web')
                        <li class="header-nav__item">
                            <a class="attendance" href="{{ route('home') }}">勤怠</a>
                        </li>
                        <li class="header-nav__item">
                            <a class="summary_button" href="{{ route('attendance.list') }}">勤怠一覧</a>
                        </li>
                        <li class="header-nav__item">
                            <a class="request_button" href="{{ route('request.list') }}">申請</a>
                        </li>
                        <li class="header-nav__item">
                            <form action="/logout" method="post">
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
