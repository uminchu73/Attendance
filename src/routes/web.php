<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AttendanceController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/', function () {
    return view('welcome');
});

/**
 * 未認証一般ユーザー
 */
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

/**
 * 認証済み一般ユーザー
 */
Route::middleware('auth')->group(function () {
    Route::post('logout', [LogoutController::class, 'logout'])->name('logout');
    Route::get('attendance', [AttendanceController::class, 'index'])->name('home');

    Route::post('/attendance/clock-in', [AttendanceController::class, 'clockIn'])->name('attendance.clock-in');
    Route::post('/attendance/clock-out', [AttendanceController::class, 'clockOut'])->name('attendance.clock-out');
    Route::post('/attendance/break-in', [AttendanceController::class, 'breakIn'])->name('attendance.break-in');
    Route::post('/attendance/break-out', [AttendanceController::class, 'breakOut'])->name('attendance.break-out');

    Route::get('attendance/list', [AttendanceController::class, 'list'])->name('attendance.list');
    Route::get('attendance/detail/{attendance}', [AttendanceController::class, 'show'])
        ->name('attendance.detail');


});

/*
 * 管理者ルート
 */
// 未認証管理者（ログイン画面）
Route::get('admin/login', [AuthenticatedSessionController::class, 'create'])
    ->middleware('guest:admin')
    ->name('admin.login');

Route::post('admin/login', [AuthenticatedSessionController::class, 'store'])
    ->middleware('guest:admin')
    ->name('admin.login.submit');

// 認証済み管理者
Route::get('admin/summary', [AdminController::class, 'index'])
    ->middleware('auth:admin')
    ->name('admin.summary');

Route::post('admin/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth:admin')
    ->name('admin.logout');