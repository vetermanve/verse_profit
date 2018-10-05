<?php


namespace App\Stats\Controller;


use Base\Controller\BasicController;
use Stats\StatsConfig;
use Stats\StatsTasks\StatsCalculationTask;
use Verse\Statistic\Core\Model\StatRecord;
use Verse\Statistic\ReadClient\ReadClient;

class Stats extends BasicController
{
    private $_statsScopeId = 0;
    
    public function view () 
    {
        $this->_statsScopeId = $this->_scopeId;
            
        return $this->site();
    }
    
    public function site ()
    {
        if ($this->p('recalculate')) {
            $this->_recalculateSiteStats();
        }

        $stFactory = StatsConfig::getStatisticFactory();
        
        $statistic = null;
        
        if ($statsId = $this->p('statsId')) {
            $statistic = $stFactory->getStatsById($statsId);
        }
        
        $availableStat = new ReadClient();
        $stIdToName = $availableStat->getAllStatistics($stFactory);
        
        $fields = $statistic ? $statistic->getFields() : [];
        
        return $this->_render('view', [
            'title' => 'All site statistic',
            'stats' => $stIdToName,
            'statsId' => $statsId,
            'fields' => $fields,
            'userId' => $this->_userId,
        ]);
    }
    
    public function calculate () 
    {
        $task = new StatsCalculationTask();
        $container = $task->run();

        uasort($container->data, function ($rec1, $rec2) {
            return $rec1[StatRecord::SCOPE_ID].$rec1[StatRecord::TIME] > $rec2[StatRecord::SCOPE_ID].$rec2[StatRecord::TIME];
        });

        array_walk($container->data, function (&$rec) {
            $rec[StatRecord::TIME] = \date('c', $rec[StatRecord::TIME]);
        });
        
        return $this->_render(__FUNCTION__, [
            'title' => 'Statistic calculation process',
            'results' => json_encode($container->data, JSON_PRETTY_PRINT),
        ]);
    }
    
    private function _recalculateSiteStats () {
        $calculator = new StatsCalculationTask();
        $calculator->run();
    }

    protected function getClassDirectory()
    {
        return __DIR__;
    }
}