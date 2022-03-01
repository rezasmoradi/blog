<?php


namespace App\policies;


use App\Models\Post;
use Core\Auth;

class PostPolicy
{
    /**
     * @param $postId
     * @return bool|void
     */
    public static function delete($postId)
    {
        $userId = Auth::user()->uuid;
        $post = Post::builder()
            ->match('p', 'Post', ['uuid' => $postId])
            ->match('u', 'User', ['uuid' => $userId], '<-[r]-(p)')
            ->build()
            ->return(['u', 'p']);

        return $post && array_key_exists(0, $post) && $post[0]->properties()['uuid'] === $userId;
    }
}