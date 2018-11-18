<?php

namespace App\Landing\Controller;

use Base\Controller\BasicController;

class Landing extends BasicController
{
    public function index()
    {
        if ((bool)$this->_userId) {
            return $this->index_auth(); 
        }
        
        return $this->index_unauth();
    }

    protected function index_auth() {
        return $this->_render(__FUNCTION__, [
            'title'      => 'Цели и средства в твоем распоряжении!',
            'authorised' => (bool) $this->_userId,
        ]);
    }

    protected function index_unauth() {
        return $this->_render(__FUNCTION__, [
            'title'      => 'Цели и средства в твоем распоряжении!',
            'authorised' => (bool) $this->_userId,
        ]);
    }

    public function contacts()
    {
        return $this->_render(__FUNCTION__, [
            'title' => 'Contacts Page',
        ]);
    }

    protected function getClassDirectory()
    {
        return __DIR__;
    }
}