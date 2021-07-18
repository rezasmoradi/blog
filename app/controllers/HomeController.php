<?php

namespace App\Controllers;

use Core\Controller;
use Core\View;
use App\Models\Post;

class HomeController extends Controller
{
    public function index()
    {
        $posts = Post::builder()->get();
        View::renderTemplate('home', ['title' => 'Home', 'posts' => $posts]);
    }

    public function login()
    {
        
    }

}