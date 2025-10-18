<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Admin\AdminAuthenticatedSessionController;
use App\Http\Controllers\Admin\AdminLogoutController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminStaffController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AttendanceRequestController;

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
    return view('home');
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
    // 勤怠詳細表示（id）
    Route::get('attendance/detail/{id}', [AttendanceController::class, 'show'])->name('attendance.detail');
    Route::post('attendance/{id}/request', [AttendanceRequestController::class, 'store'])->name('attendance.request');
    Route::get('stamp_correction_request/list', [AttendanceRequestController::class, 'requestsList'])
        ->name('request.list');
});

/**
 * 管理者ルート
*/

Route::prefix('admin')->group(function () {
    // 未ログイン時
    Route::middleware('guest:admin')->group(function () {
        Route::get('login', [AdminAuthenticatedSessionController::class, 'create'])->name('admin.login');
        Route::post('login', [AdminAuthenticatedSessionController::class, 'store'])->name('admin.login.submit');
    });

    // ログイン済み
    Route::middleware('auth:admin')->group(function () {
        Route::post('logout', [AdminLogoutController::class, 'logout'])->name('admin.logout');
        Route::get('attendance/list', [AdminController::class, 'index'])->name('admin.summary');
        Route::get('attendance/{id}', [AdminController::class, 'show'])->name('admin.detail');
        Route::post('attendance/{id}/update', [AdminController::class, 'update'])->name('admin.attendance.update');

        Route::get('staff/list', [AdminStaffController::class, 'index'])->name('admin.staff.list');
        Route::get('attendance/staff/{id}', [AdminStaffController::class, 'show'])->name('admin.staff.attendance');
        Route::post('staff/{id}/export', [AdminStaffController::class, 'exportCsv'])->name('admin.staff.export');
    });
});
