<?php

namespace App\Models;

use Core\Model;

class Post extends Model
{
    protected string $label = 'Post';

    protected array $fillables = [
        'uuid',
        'caption',
        'comment_allowed',
        'created_at',
        'updated_at'
    ];

    public function __construct($values = [])
    {
        parent::__construct($values);
    }
}