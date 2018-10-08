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
     * @return array
     */
    public function run()
    {
        $context = new StatsContext();

        $context->statisticFactory = StatsConfig::getStatisticFactory();
        $context->groupingFactory = StatsConfig::getGroupingFactory();
        $context->eventsStream = StatsConfig::getEventStream();
        $context->statsStorage = StatsConfig::getStatsStorage();
        $context->uniqueStorage = StatsConfig::getUniqueStorage();

        $results = [];
        
        do {
            $container = new StatsContainer();
            $processor = new StatProcessor();
            $processor->setContainer($container);
            $processor->setContext($context);
            $processor->addSchema(new BasicStatsSchema());
            $processor->run();
            $results = \array_merge($results, array_values($container->results));
        } while ($container->evensContainer && $container->evensContainer->events);
        
        return $results;
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