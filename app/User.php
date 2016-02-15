<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\Domain;

class User extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function domains()
    {
        return $this->hasMany(Domain::class);
    }
}
