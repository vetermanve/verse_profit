<?php


namespace App\Stats\Controller;


use Base\Controller\BasicController;

class Actions extends BasicController
{
    public function index ()
    {
        return $this->_render('actions', [

        ]);
    }
    
    protected function getClassDirectory()
    {
        return __DIR__;
    }
}