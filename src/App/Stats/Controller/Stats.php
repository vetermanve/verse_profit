<?php


namespace App\Stats\Controller;


use Base\Controller\BasicController;
use Stats\StatsTasks\SiteStatsCalculator;

class Stats extends BasicController
{
    public function actions ()
    {
        return $this->_render(__FUNCTION__, [
            
        ]);
    }

    public function view () 
    {
        if ($this->p('recalculate')) {
            $this->_recalculateSiteStats();
        }

        return $this->_render('view', [
            'title' => 'Your generated test statistic',
            'userId' => $this->_userId,
        ]);
    }
    
    public function site ()
    {
        if ($this->p('recalculate')) {
            $this->_recalculateSiteStats();
        }
        
        return $this->_render('view', [
            'title' => 'All site statistic',
            'userId' => $this->_userId,
        ]);
    }
    
    public function calculate () 
    {
        $task = new SiteStatsCalculator();
        $results = $task->run();
        
        return $this->_render(__FUNCTION__, [
            'title' => 'Statistic calculation process',
            'results' => json_encode($results, JSON_PRETTY_PRINT),
        ]);
    }
    
    private function _recalculateSiteStats () {
        $calculator = new SiteStatsCalculator();
        $calculator->run();
    }

    protected function getClassDirectory()
    {
        return __DIR__;
    }
}