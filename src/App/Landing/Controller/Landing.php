<?php

namespace App\Landing\Controller;

use Base\Controller\BasicController;
use Stats\Events;
use Stats\ViewConfig\VisitStats;
use Verse\Run\Util\Uuid;

class Landing extends BasicController
{
    public function index () 
    {
        $this->_statsClient->event(Events::VISIT_MAINPAGE, $this->_userId, $this->_scopeId);
        
        return $this->_render(__FUNCTION__, [
            'title' => 'Main Page',
            'statsId' => $this->_scopeId,
        ]);
    }
    
    public function contacts () 
    {
        $this->_statsClient->event(Events::VISIT_CONTACTS, $this->_userId, $this->_scopeId);
        
        return $this->_render(__FUNCTION__, [
            'title' => 'Contacts Page',
        ]);
    }
    
    public function regenerate () 
    {
        $this->_statsClient->event(Events::STATS_REGENERATION, $this->_userId, $this->_scopeId);
        
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