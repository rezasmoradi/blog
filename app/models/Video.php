<?php


namespace App\models;


use Core\Model;

class Video extends Model
{
    protected string $label = 'Video';

    protected array $fillables = [
        'uuid',
        'name',
        'duration'
    ];
}