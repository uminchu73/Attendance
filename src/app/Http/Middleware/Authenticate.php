<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        // 管理者エリアの場合
        if ($request->is('admin/*')) {
            return route('admin.login');
        }

        // 一般ユーザー
        return $request->expectsJson() ? null : route('login');
    }
}
