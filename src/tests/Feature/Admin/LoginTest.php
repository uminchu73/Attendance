<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * メールアドレスが未入力の場合、バリデーションメッセージが表示される
     */
    public function test_email_required()
    {
        //会員登録ページを開く（ステータス200）
        $this->get('/login')->assertStatus(200);

        //入力データを送信（パスワードのみ）
        $response = $this->post('/login', [
            'email' => '',
            'password' => 'password123',
        ]);

        //バリデーションが表示されたか確認
        $response->assertSessionHasErrors([
            'email' => 'メールアドレスを入力してください',
        ]);
    }

    /**
     * パスワードが未入力の場合、バリデーションメッセージが表示される
     */
    public function test_password_required()
    {
        //会員登録ページを開く（ステータス200）
        $this->get('/login')->assertStatus(200);

        //入力データを送信（メールアドレスのみ）
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => '',
        ]);

        //バリデーションが表示されたか確認
        $response->assertSessionHasErrors([
            'password' => 'パスワードを入力してください',
        ]);
    }

    /**
     * 登録内容と一致しない場合、バリデーションメッセージが表示される
     */
    public function test_invalid_user()
    {
        //会員登録ページを開く（ステータス200）
        $this->get('/login')->assertStatus(200);

        //入力データを送信（誤った情報）
        $response = $this->post('/login', [
            'email' => 'nouser@example.com',
            'password' => 'misspassword',
        ]);

        //バリデーションが表示されたか確認
        $response->assertSessionHasErrors([
            'email' => 'ログイン情報が登録されていません',
        ]);
    }
}
