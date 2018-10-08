<?php

namespace Stats\StatsTasks;

use Stats\StatsConfig;
use Verse\Statistic\Core\Schema\BasicStatsSchema;
use Verse\Statistic\Core\StatProcessor;
use Verse\Statistic\Core\StatsContainer;
use Verse\Statistic\Core\StatsContext;

class StatsCalculationTask
{
    protected $_isTestRun = false;


    /**
     * @return \Verse\Statistic\Core\StatsContainer
     */
    public function run()
    {
        $context = new StatsContext();

        $context->statisticFactory = StatsConfig::getStatisticFactory();
        $context->groupingFactory = StatsConfig::getGroupingFactory();
        $context->eventsStream = StatsConfig::getEventStream();
        $context->statsStorage = StatsConfig::getStatsStorage();
        $context->uniqueStorage = StatsConfig::getUniqueStorage();

        $container = new StatsContainer();
        
        $processor = new StatProcessor();
        $processor->setContainer($container);
        $processor->setContext($context);
        $processor->addSchema(new BasicStatsSchema());

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