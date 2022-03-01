<?php


namespace App\models;


use Core\Model;

class Photo extends Model
{
    protected string $label = 'Photo';

    protected array $fillables = [
        'uuid',
        'name',
    ];
}