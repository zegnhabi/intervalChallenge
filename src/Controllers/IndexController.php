<?php 
declare(strict_types=1);

namespace App\Controllers;

class IndexController{
    protected $viewPath;

    public function __construct(){
        $this->viewPath = dirname(__DIR__) . "/Views/";
    }
    public function index(): String {
        header('Content-Type: text/html');
        return file_get_contents($this->viewPath . __FUNCTION__.".php");;
    }
}