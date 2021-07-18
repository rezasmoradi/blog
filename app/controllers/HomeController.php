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
        View::renderTemplate('home', ['title' => 'Home', 'posts' => $posts]);
    }

    public function login(Request $request)
    {
        $user = User::builder()->where(['email' => $request->post('email')])->first();

        if ($this->check($request->post('password'), $user->password)) {
            Session::set('user', json_encode($user));
            redirect('http://blog.com');
        } else {
            redirect('http://blog.com/login');
        }
    }

    private function check($receivedPassword, $userPassword)
    {
        if (password_verify($receivedPassword, $userPassword)) {
            return true;
        }
        return false;
    }

    public function logout(Request $request)
    {
        if ($request->get('logout') !== null) {
            Session::clear();
            unset($_SESSION['user']);
            redirect('/');
        }
    }

}