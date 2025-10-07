<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class AdminLogoutController extends Controller
{
    public function logout(Request $request)
    {
        Auth::guard('admin')->logout(); // 管理者ガードでログアウト

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // 管理者ログインページへリダイレクト
        return redirect()->route('admin.login');
    }
}
