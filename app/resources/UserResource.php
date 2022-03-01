<?php


namespace App\resources;


use Carbon\Carbon;
use Core\Resource;

class UserResource extends Resource
{

    /**
     * @inheritDoc
     */
    public function toArray()
    {
        $user = [];
//        $flatten = self::flatten($this->resource);
        foreach ($this->resource as $key => $value) {
            if ($key !== 'password') {
                if ($key === 'updated_at' || $key === 'created_at') {
                    $user[$key] = Carbon::parse($value)->toDateTimeString();
                } else {
                    $user[$key] = $value;
                }
            }
        }
        return $user;
    }
}