<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //固定の初期ユーザー（テストログイン用）
        User::create([
            'name' => 'テスト太郎',
            'email' => 'user@example.com',
            'password' => Hash::make('userpass123'),
        ]);

        // スタッフ10人を作成
        User::factory(15)->create();
    }
}
