<?php

class SiteController extends Controller
{
    public $allowed = ['index', 'page404', 'paramhandler'];

    public function index()
    {
        $this->render('index.php');
    }

    public function page404()
    {
        $this->render('page404.php');
    }

    public function paramhandler()
    {
        echo 'paramhandler()';
        echo '<pre>';
        echo '$_GET', '<br>';
        print_r($_GET);
        echo '<br>';
        echo '$_POST', '<br>';
        print_r($_POST);
        echo '</pre>';
    }
}