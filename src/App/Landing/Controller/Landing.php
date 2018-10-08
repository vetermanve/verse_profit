<?php

namespace App\Landing\Controller;

use Base\Controller\BasicController;
use Stats\Events;
use Verse\Run\Util\Uuid;

class Landing extends BasicController
{
    public function index () 
    {
        return $this->_render(__FUNCTION__, [
            'title' => 'Main Page',
            'statsId' => $this->_scopeId,
        ]);
    }
    
    public function contacts () 
    {
        return $this->_render(__FUNCTION__, [
            'title' => 'Contacts Page',
        ]);
    }
    
    public function regenerate () 
    {
        $statsId = Uuid::v4();
        $this->requestWrapper->setState('stats_id', $statsId);
        
        return $this->_render(__FUNCTION__, [
            'title' => 'Regenerated!',
            'statsId' => $statsId,
        ]);
    }
    
    public function regenerateUserId () 
    {
        $this->_userId = Uuid::v4();
        $this->requestWrapper->setState('user_id', $this->_userId, 3600 * 24 * 365);
        
        return $this->_render('regenerate', [
            'title' => 'Regenerated!',
            'statsId' => $this->_userId,
        ]);
    }

    protected function getClassDirectory()
    {
        return __DIR__;
    }
}