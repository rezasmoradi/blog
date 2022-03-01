<?php


namespace App\resources;


use Carbon\Carbon;
use Core\Auth;
use Core\Resource;

class MediaResource extends Resource
{
    protected array $resource;

    public function toArray()
    {
        $result = [];
        foreach ($this->resource as $key => $value) {
            switch ($key) {
                case 'updated_at':
                case 'created_at':
                    $result[$key] = Carbon::parse($value)->toDateTimeString();
                    break;
                case 'duration':
                    $result['duration'] = floatval($value);
                    break;
                case 'path':
                    $result['url'] = getenv('APP_URL') . '/storage/posts/' . $this->resource['path'] . '/' . $this->resource['name'];
                    break;
                default:
                    $result[$key] = $value;
            }
        }

        return $result;
    }
}