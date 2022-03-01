<?php

namespace Core;

abstract class Model extends QueryBuilder
{
    protected string $label = '';

    protected array $fillables = [];

    protected static array $hidden = [];

    public function __construct($values = [])
    {
        parent::__construct();
        $this->fillables = get_class_vars(get_called_class())['fillables'];
        foreach ($values as $key => $value){
            $this->{$key} = $value;
        }
    }

    public function save()
    {
        
    }
}