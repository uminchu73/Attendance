<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Http\Requests\LoginRequest as FortifyLoginRequest;
use App\Http\Requests\LoginRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Admin;


class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);

        Fortify::registerView(function () {
            return view('auth.register');    
        });

        Fortify::loginView(function () {
            // URL が admin なら管理者ログイン画面
            if (request()->is('admin/*')) {
                return view('admin.login');
            }
            // それ以外は一般ユーザー
            return view('auth.login');    
        });

        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->email;

            return Limit::perMinute(10)->by($email . $request->ip());
        });


        Fortify::authenticateUsing(function (Request $request) {
            // 管理者ログインページから来た場合
            if ($request->is('admin/*')) {
                $admin = \App\Models\Admin::where('email', $request->email)->first();

                if ($admin && \Hash::check($request->password, $admin->password)) {
                    auth()->guard('admin')->login($admin);
                    return $admin;
                }
            }

            // 通常ユーザー
            $user = \App\Models\User::where('email', $request->email)->first();

            if ($user && \Hash::check($request->password, $user->password)) {
                auth()->guard('web')->login($user); 
                return $user;
            }

            // ユーザーが存在しない or パスワードが間違っている場合
            throw ValidationException::withMessages([
                'email' => ['ログイン情報が登録されていません'],
            ]);
        });

        $this->app->instance(LoginResponse::class, new class implements LoginResponse {
            public function toResponse($request)
            {
                // 管理者なら
                if ($request->user('admin')) {
                    return redirect()->intended('/admin/summary');
                }

                // 一般ユーザーなら
                return redirect()->intended('/attendance');
            }
        });

        app()->bind(FortifyLoginRequest::class, LoginRequest::class);

    }
}
