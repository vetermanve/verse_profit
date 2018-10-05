<?php

namespace Stats\StatsTasks;
 
use Stats\StatsConfig;
use Verse\Statistic\Core\Schema\FileBasedStatsSchema;
use Verse\Statistic\Core\StatProcessor;
use Verse\Statistic\Core\StatsContainer;
use Verse\Statistic\Core\StatsContext;

class StatsCalculationTask
{
    protected $_isTestRun = false;


    /**
     * @return \Verse\Statistic\Core\StatsContainer
     */
    public function run () 
    {
        $context = new StatsContext();
        $context->set(StatsContext::FILE_STATS_DIRECTORY, StatsConfig::getStatFilesDirectory());
        
        $context->statisticFactory = StatsConfig::getStatisticFactory();
        $context->groupingFactory = StatsConfig::getGroupingFactory();
        
        $container = new StatsContainer();
        
        $processor = new StatProcessor();
        $processor->setContainer($container);
        $processor->setContext($context);
        $processor->addSchema(new FileBasedStatsSchema());

        $processor->run();
        
        return $container;
    }

    /**
     * @return bool
     */
    public function isTestRun() : bool
    {
        return $this->_isTestRun;
    }

    /**
     * @param bool $isTestRun
     */
    public function setIsTestRun(bool $isTestRun)
    {
        $this->_isTestRun = $isTestRun;
    }


}