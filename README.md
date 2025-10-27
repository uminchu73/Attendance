# coachtech勤怠アプリ

## 環境構築

### 1. リポジトリをクローン

```
git clone git@github.com:uminchu73/Attendance.git
```
```
docker-compose up -d --build
```

### 2. Laravel パッケージのダウンロード

```
docker-compose exec php bash
```
```
composer install
```

### 3. 環境変数ファイルの設定

```
cp .env.example .env
```
```
php artisan key:generate
```

`.env` を編集し、以下を設定します。

```
DB_HOST=mysql

DB_DATABASE=laravel_db

DB_USERNAME=laravel_user

DB_PASSWORD=laravel_pass
```

### 4. マイグレーション・シーディングを実行

```
php artisan migrate --seed
```
テーブルを作成し、ダミーデータを投入します。


### ５. テストの実行

#### ①テスト用データベースの準備


```
docker-compose exec mysql bash
```
```
mysql -u root -p
```
パスワードは `docker-compose.yml` 内の `MYSQL_ROOT_PASSWORD` に設定されている値を使用してください。
```
CREATE DATABASE demo_test;
```

#### ②テスト用の.envファイル作成
```
docker-compose exec php bash
```
```
cp .env .env.testing
```
.env.testingファイルの文頭部分にあるAPP_ENVとAPP_KEYを編集します。

```
APP_ENV=test
APP_KEY=
```
.env.testingにデータベースの接続情報を加えてください。
```
DB_DATABASE=demo_test
DB_USERNAME=root
DB_PASSWORD=root
```
APP_KEYに新たなテスト用のアプリケーションキーを加えます。
```
php artisan key:generate --env=testing
```
```
php artisan config:clear
```
```
php artisan migrate --env=testing
```

#### ③テストの実行
```
php artisan test
```



## 初期ログイン情報

#### 一般ユーザー

メールアドレス：`user@example.com`

パスワード：`userpass123`

#### 管理者

メールアドレス：`admin@example.com`

パスワード：`adminpass123`

## 使用技術（実行環境）

フレームワーク：Laravel Framework 8.83.29

言語：PHP 8.1.33

Webサーバー：Nginx v1.21.1

データベース：MySQL v8.0.26



## ER図



## URL

アプリケーション：http://localhost


phpMyAdmin：http://localhost:8080


