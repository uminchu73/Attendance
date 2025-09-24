<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    /**
     * リレーション：出勤記録（Attendance）との1対多
     * 一人のユーザーが複数回出勤する
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * リレーション：修正申請（AttendanceRequest）との1対多
     * 一人のユーザーが複数回修正申請する
     */
    public function requests()
    {
        return $this->hasMany(Request::class);
    }
}
