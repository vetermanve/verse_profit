<?php


namespace App\Stats\Controller;


use Base\Controller\BasicController;

class Stats extends BasicController
{
    public function view () 
    {
        return $this->_render(__FUNCTION__, [
            
        ]);
    }
    
    public function actions ()
    {
        return $this->_render(__FUNCTION__, [

        ]);
    }

    protected function getClassDirectory()
    {
        return __DIR__;
    }
}