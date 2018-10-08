<?php


namespace App\Stats\Controller;


use Base\Controller\BasicController;
use Stats\Events;
use Stats\StatsConfig;
use Stats\StatsTasks\StatsCalculationTask;
use Verse\Statistic\Configuration\Grouping\BasicGroping;
use Verse\Statistic\Core\Model\StatRecord;
use Verse\Statistic\Core\Model\TimeScale;
use Verse\Statistic\View\Dater;

class Stats extends BasicController
{
    private $_statsScopeId = 0;
    
    public function view () 
    {
        $this->_statsScopeId = $this->_scopeId;
        $this->_statsClient->event(Events::VISIT_SCOPED_STATS, $this->_userId, $this->_scopeId);
            
        return $this->site();
    }
    
    public function site ()
    {
        if ($this->p('recalculate')) {
            $this->_recalculateSiteStats();
        }
        
        if (!$this->_statsScopeId) {
            $this->_statsClient->event(Events::VISIT_SITE_STATS, $this->_userId, $this->_scopeId);
        }

        $stFactory = StatsConfig::getStatisticFactory();
        $allStats = $stFactory->getStats();
        
        $statistic = null;
        
        if (($statsId = $this->p('statsId')) && isset($allStats[$statsId])) {
            $statistic = $allStats[$statsId];
        } else {
            $statistic = reset($allStats);
            $statsId = $statistic->getId();
        }
        
        $stIdToName = [];
        foreach ($allStats as $statistic) {
            $stIdToName[$statistic->getId()] = $statistic->getName();
        }
        
        $fields = $statistic ? $statistic->getFields() : [];
        $eventIds = array_map('crc32', $fields);
        
        $statsData = StatsConfig::getStatsStorage()->findRecords($eventIds, 0, time() + 3600 *24, TimeScale::HOUR, BasicGroping::TYPE, [], $this->_statsScopeId);
        
        array_walk($statsData, function (&$rec) {
            ksort($rec);
            unset($rec['id']);
            $rec['date'] = date('c', $rec[StatRecord::TIME]);
        });
        
        $keys = array_keys(reset($statsData));
        
        $grouping = StatsConfig::getGroupingFactory()->getGroupingModelById(BasicGroping::TYPE);
        
        $dater = new Dater();
        $dater->setStatisticConfiguration($statistic);
        $dater->setGrouping($grouping);
        $dater->setCurrentView();
        $dater->setRawData($statsData);
        $dater->setTimeScale(TimeScale::HOUR);
        $dater->setFromTime(strtotime('-2 days'));
        $dater->setToTime(time());
        
        $dater->buildViewData();
        
        $data = $dater->getResultData();
        
        return $this->_render('view', [
            'title' => 'All site statistic',
            'stats' => $stIdToName,
            'statsId' => $statsId,
            'fields' => $fields,
            'userId' => $this->_userId,
            'data' => $statsData,
            'keys' => $keys,
            'table' => $data[Dater::TABLE],
            'rows' => $dater->getRowsNamed(),
            'names' => $data[Dater::COLUMN_NAMES],
            'seriesEncoded' => json_encode($data[Dater::SERIES], JSON_UNESCAPED_UNICODE),
        ]);
    }
    
    public function calculate () 
    {
        $task = new StatsCalculationTask();
        $data = $task->run();

        uasort($data, function ($rec1, $rec2) {
            return $rec1[StatRecord::SCOPE_ID].$rec1[StatRecord::TIME] > $rec2[StatRecord::SCOPE_ID].$rec2[StatRecord::TIME];
        });

        array_walk($data, function (&$rec) {
            $rec[StatRecord::TIME] = \date('c', $rec[StatRecord::TIME]);
        });
        
        return $this->_render(__FUNCTION__, [
            'title' => 'Statistic calculation results',
            'results' => json_encode(array_values($data), JSON_PRETTY_PRINT),
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