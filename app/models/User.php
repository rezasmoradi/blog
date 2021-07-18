<?php

namespace App\Models;

use Core\Model;

class User extends Model
{
    protected string $table = 'users';

    public function save()
    {
        return $this->saveModel($this);
    }
}