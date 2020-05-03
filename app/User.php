<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * ID for default user role
     */
    public const ROLE_USER = 0;

    /**
     * ID for manager role
     */
    public const ROLE_MANAGER = 1;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Проверка на обычного пользователя
     * @return bool
     */
    public function isUser() : bool {
        return $this->role == self::ROLE_USER;
    }

    /**
     * Проверка на менеджера
     * @return bool
     */
    public function isManager() : bool {
        return $this->role == self::ROLE_MANAGER;
    }

}
