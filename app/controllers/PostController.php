<?php


namespace App\Controllers;

use App\models\Category;
use App\models\Post;
use Core\Controller;
use Core\QueryBuilder;
use Core\Request;
use Core\View;
use function Core\dd;
use function Core\redirect;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $posts = Post::builder()->get();

        View::renderTemplate('home', ['posts' => $posts]);
    }

    public function create(Request $request)
    {
        if (!empty($request->post())) {
            Post::builder()->create([
                'title' => $request->post('title'),
                'body' => $request->post('body'),
                'category_id' => $request->post('category_id')
            ]);
        }

        $posts = Post::builder()->get();

        View::renderTemplate('posts', ['posts' => $posts]);
    }

    public function edit(Request $request)
    {
        $categories = Category::builder()->get();
        $post = Post::builder()->where(['id' => $request->get('id')])->first();

        View::renderTemplate('edit-post', ['post' => $post, 'categories' => $categories]);
    }

    public function update(Request $request)
    {
        if (!empty($request->post())) {
            Post::builder()->where(['id' => $request->get('id')])->update([
                'title' => $request->post('title') ?? 'عنوان خیلی جدید',
                'body' => $request->post('body'),
                'category_id' => (int)$request->post('category_id')
            ]);
        }

        redirect('http://blog.com/');
    }

    public function show()
    {
        $posts = Post::builder()->rawQuery('SELECT posts.id, posts.title, posts.body, c.title as cTitle FROM posts LEFT JOIN
             (SELECT id, title, sub_category FROM categories WHERE sub_category IS NULL
              UNION
              SELECT c2.id, c2.title, c1.title FROM categories c1 JOIN categories c2 ON c1.id = c2.sub_category
             ) AS c ON posts.category_id = c.id;');

        View::renderTemplate('posts', ['posts' => $posts]);
    }

    public function prepare()
    {
        $categories = Category::builder()->get();

        View::renderTemplate('create', ['categories' => $categories]);
    }

    public function watch()
    {
        preg_match('/\d+/', $_SERVER['QUERY_STRING'], $matches);
        $post = Post::builder()->find($matches[0]);

        View::renderTemplate('show-post', ['post' => $post]);
    }
}