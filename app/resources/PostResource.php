<?php


namespace App\resources;


use Carbon\Carbon;
use Core\Resource;

class PostResource extends Resource
{
    protected array $resource;

    public function toArray()
    {
        $post = [];
        if (self::$isCollection) {
            foreach ($this->resource as $key => $value) {
                if ($key === 'Post') {
                    foreach ($value as $k => $item) {
                        if ($k === 'updated_at' || $k === 'created_at') {
                            $post[$k] = Carbon::parse($item)->toDateTimeString();
                        } elseif ($k === 'comment_allowed') {
                            $post[$k] = (bool)$item;
                        } else {
                            $post[$k] = $item;
                        }
                    }
                }
            }
            $result = $this->getOtherResource($this->resource);
            $post['user'] = $result[0];
            $post['content'] = $result[1];
            return $post;
        } else {
            $value = self::flatten($this->resource)[0];
            $value['Post']['updated_at'] = Carbon::parse($value['Post']['updated_at'])->toDateTimeString();
            $value['Post']['created_at'] = Carbon::parse($value['Post']['created_at'])->toDateTimeString();
            $value['Post']['comment_allowed'] = (bool)$value['Post']['comment_allowed'];
            $post = $value['Post'];

            $result = $this->getOtherResource($value);
            $post['user'] = $result[0];
            $post['content'] = $result[1];
            return $post;
        }
    }

    private function getOtherResource($resources)
    {
        $result = [];
        $user = [];
        foreach ($resources as $k => $item) {
            if (array_key_exists('Video', $item)) {
                $obj = new MediaResource($item['Video']);
                $result['videos'][] = $obj->toArray();
            }
            if (array_key_exists('Photo', $item)) {
                $obj = new MediaResource($item['Photo']);
                $result['photos'][] = $obj->toArray();
            }
            if (array_key_exists('User', $item)) {
                $obj = new UserResource($item['User']);
                $user = $obj->toArray();
            }
        }
        return [$user, $result];
    }
}