<?php

namespace App\Models;

use Core\Model;

class User extends Model
{
    protected string $label = 'User';

    public static array $hidden = [
        'password'
    ];

    protected array $fillables = [
        'uuid',
        'name',
        'username',
        'password',
        'email',
        'bio',
        'photo',
        'code',
        'verified_at',
        'created_at',
        'updated_at'
    ];

    public function __construct($values = [])
    {
        parent::__construct($values);
    }

    public function save()
    {
        return $this->saveModel($this);
    }

    public function logout()
    {
//        $this->getConnection()->run('MATCH (');
    }
}