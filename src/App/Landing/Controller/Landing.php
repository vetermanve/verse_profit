<?php

namespace App\Landing\Controller;

use Base\Controller\BasicController;
use Verse\Run\Util\Uuid;

class Landing extends BasicController
{
    public function index () 
    {
        $statsId = $this->requestWrapper->getState('stats_id');
        if (!$statsId) {
            $statsId = Uuid::v4();
            $this->requestWrapper->setState('stats_id', $statsId, 7200);
        }
        
        return $this->_render(__FUNCTION__, [
            'title' => 'Main Page',
            'statsId' => $statsId,
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

    protected function getClassDirectory()
    {
        return __DIR__;
    }
}