<?php


namespace App\Goals\Controller;


use Base\Controller\BasicController;

class Goals extends BasicController
{
    public function index () 
    {
        return __METHOD__;
    }

    protected function getClassDirectory()
    {
        return __DIR__;
    }
}