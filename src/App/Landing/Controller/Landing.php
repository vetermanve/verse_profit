<?php

namespace App\Landing\Controller;

use Base\Controller\BasicController;
use Service\Calendar\CalendarService;

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
        $calendar = new CalendarService();
        
        $currentYear = $this->p('year');
        if ($currentYear) {
            $this->setState('year', $currentYear);
        } else {
            $currentYear = $this->getState('year', (int)date('Y'));
        }
        
        $months = $calendar->getMonthsStarts($currentYear);
        
        return $this->_render(__FUNCTION__, [
            'title'      => 'Цели и средства в твоем распоряжении!',
            'authorised' => (bool) $this->_userId,
            'year' => $currentYear,
            'months' => $months,
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