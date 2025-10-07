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


### ５. テスト用データベース作成

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


