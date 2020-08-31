<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject; //注意:不要忘记引用这个类

class User extends Authenticatable implements JWTSubject //注意:不要忘记加上这一行
{
    use Notifiable;

    protected $table = 'users';

    protected $primaryKey = 'id';

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}