<?php


namespace App\controllers;


use App\Models\User;
use Core\Controller;
use Core\Request;
use Core\Session;
use Core\View;
use function Core\dd;
use function Core\redirect;
use function Core\response;

class UserController extends Controller
{
    public function index()
    {
        $users = User::builder()->get();
        View::renderTemplate('users', ['users' => $users]);
    }

    public function create(Request $request)
    {
        $user = User::builder()->create([
            'name' => $request->post('name'),
            'email' => $request->post('email')
        ]);

        response($user, 201);
    }

}