<?php


namespace App\controllers;


use App\Models\User;
use App\resources\UserResource;
use Carbon\Carbon;
use Core\Auth;
use Core\Controller;
use Core\Request;

class UserController extends Controller
{
    public function login(Request $request)
    {
        $auth = Auth::login($request->post('username'), $request->post('password'));

        if ($auth) {
            try {
                $token = Auth::generate_jwt(['alg' => 'HS256', 'typ' => 'JWT'],
                    ['username' => $auth->username, 'exp' => Carbon::now()->addDays(365)->toArray()['timestamp']]
                );
                Auth::builder()
                    ->match('u', 'User', ['uuid' => $auth->uuid])
                    ->createConstraint('a', 'Auth', ['token' => $token], '<-[r:authenticatedBy]-(u)')
                    ->build()->return();

                response(['token' => $token, 'type' => 'Bearer']);
            } catch (\Exception $e) {
                throw new \Exception('Create token is failed' . $e->getMessage());
            }
        } else {
            throw new \Exception('User not found', 404);
        }
    }

    public function index(Request $request)
    {
        if (!empty($request->query())) {
            $users = User::builder()->where($request->query())->first();
        } else {
            $users = User::builder()->get();
        }
        response(UserResource::collection($users));
    }

    public function register(Request $request)
    {
        $user = User::builder()->createConstraint('u', 'User', [
            'name' => $request->post('name'),
            'email' => $request->post('email'),
            'username' => $request->post('username'),
            'password' => password_hash($request->post('password'), PASSWORD_BCRYPT),
        ])->build()->return('u')->properties();

        response($user, 201);
    }

    public function update(Request $request)
    {
        $user = User::builder()->update($request->get('id'), $request->post());

        response($user, 202);
    }

    public function delete(Request $request)
    {
        $result = User::builder()->where(['uuid' => $request->get('id')])->delete();

        response($result);
    }

    public function follow(Request $request)
    {
        if ($request->post('users')) {
            $query = User::builder()->match('i', 'User', ['uuid' => Auth::user()->uuid]);
            foreach ($request->post('users') as $key => $user) {
                $k = $key + 1;
                $query->match('u' . $k, 'User', ['uuid' => $user]);
                $query->createRelation('u' . $k, '-[:followed_by]->', 'i');
            }
            $query->build()->return();

            response('users are followed successfully', 201);
        } else {
            response('users to follow not found', 404);
        }
    }

    public function unfollow(Request $request)
    {
        if ($request->post('users')) {
            $query = User::builder()->match('i', 'User', ['uuid' => Auth::user()->uuid]);
            $deletes = [];
            foreach ($request->post('users') as $key => $user) {
                $k = $key + 1;
                $deletes[] = 'r' . $k;
                $query->match('u' . $k, 'User', ['uuid' => $user], '-[r' . $k . ':followed_by]->(i)');
            }
            $query->build()->deleteRelation($deletes);

            response('users are unfollowed successfully', 201);
        } else {
            response('users to unfollow not found', 404);
        }
    }
}