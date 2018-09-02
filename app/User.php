<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    const USER_VERIFICATED = '1';
    const USER_NOT_VERIFICATED = '0';

    const USER_ADMIN = 'true';
    const USER_REGULAR = 'false';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'verified',
        'verification_token',
        'admin',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'verification_token'
    ];

    public function idVerificated() {
        return $this->verified == User::USER_VERIFICATED;
    }

    public function idAdmin() {
        return $this->admin == User::USER_ADMIN;
    }

    public static function generateVerificateToken() {
        return str_random(40);
    }
}
