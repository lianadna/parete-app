<?php

namespace App\Models;

use MongoDB\Laravel\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    protected $connection = 'mongodb';

    protected $table = 'admin_pengguna';

    public $timestamps = false;

    /** @var list<string> */
    protected $fillable = [
        'username',
        'password',
        'nama',
    ];

    /** @var list<string> */
    protected $hidden = [
        'remember_token',
    ];
}
