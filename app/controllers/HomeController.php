<?php

require_once '../core/Controller.php';
require_once '../core/View.php';

class HomeController extends Controller
{
    public function index()
    {
        View::render('home', ['title' => 'Home']);
    }


}