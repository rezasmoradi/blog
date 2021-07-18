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

    public function updateAction(Request $request)
    {
        if ($request->isApi) {
            $user = User::builder()->where(['name' => $request->get('name')])->update([
                'password' => password_hash($request->post('password'), PASSWORD_BCRYPT, ['cost' => 11])
            ]);

            response($user, 202);
        } else {
            $user = $request->user();

            $user->name = $request->post('name') ?? $user->name;
            $user->email = $request->post('email') ?? $user->email;
            $user->password = $request->post('password') ?? $user->password;

            $update = $user->save($user);

            if ($update) {
                Session::forget('user');
                Session::set('user', json_encode($user));
            }

            redirect('/');
        }
    }

}