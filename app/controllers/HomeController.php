<?php

namespace App\Controllers;

use App\Models\User;
use Core\Controller;
use Core\Request;
use Core\Session;
use Core\View;
use App\Models\Post;
use function Core\redirect;

class HomeController extends Controller
{
    public function index()
    {
        $posts = Post::builder()->get();

        response($posts);
    }

}