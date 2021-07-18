<?php


namespace App\controllers;

use App\models\Category;
use Core\Controller;
use Core\Request;
use Core\View;
use function Core\dd;
use function Core\redirect;

class CategoryController extends Controller
{

    public function index()
    {
        $categories = Category::builder()->rawQuery('SELECT id, title, sub_category FROM categories WHERE sub_category IS NULL 
        UNION 
         SELECT c2.Id, c2.title, c1.title FROM categories c1
         JOIN categories c2
              ON c1.id = c2.sub_category');

        View::renderTemplate('categories', ['categories' => $categories]);
    }

    public function show()
    {
        $categories = Category::builder()->get();
        View::renderTemplate('category-create', ['categories' => $categories]);
    }

    public function create(Request $request)
    {
        Category::builder()->create([
            'title' => $request->post('title'),
            'sub_category' => $request->post('sub_category') ?? null
        ]);

        redirect('/categories');
    }

    public function edit(Request $request)
    {
        $categories = Category::builder()->get();
        $category = Category::builder()->where(['id' => $request->get('id')])->first();

        View::renderTemplate('category-update', ['category' => $category, 'categories' => $categories]);
    }

    public function update(Request $request)
    {
        Category::builder()->where(['id' => $request->get('id')])->update([
            'title' => $request->post('title'),
            'sub_category' => $request->post('sub_category')
        ]);

        redirect('/categories');
    }

    public function delete(Request $request)
    {
        Category::builder()->where(['id' => $request->get('id')])->delete();

        redirect('/categories');
    }
}