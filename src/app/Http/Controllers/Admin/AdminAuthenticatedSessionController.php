<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Fortify\Http\Requests\LoginRequest;
use Laravel\Fortify\Contracts\LoginViewResponse;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController as FortifyController;

class AdminAuthenticatedSessionController extends FortifyController
{
    public function create(Request $request): LoginViewResponse
    {
        //FortifyのLoginViewResponse契約を使って、独自のBladeを返すようにする
        return app(LoginViewResponse::class, [
            'view' => 'admin.auth.login', // 管理者用ログインビュー
        ]);
    }

    public function store(LoginRequest $request)
    {
        //一時的にFortifyのguardをadminに差し替える
        config(['fortify.guard' => 'admin']);

        // Fortify標準のログイン処理を呼び出す
        return parent::store($request);
    }
}
