<?php


namespace Stats;


use Stats\ViewConfig\VisitStats;
use Verse\Statistic\Configuration\Grouping\AbstractGrouping;
use Verse\Statistic\Configuration\Grouping\BasicGroping;
use Verse\Statistic\Configuration\GroupingFactory;
use Verse\Statistic\Configuration\StatisticFactory;
use Verse\Statistic\Configuration\Stats\AbstractStatistic;

class StatsConfig
{
    public static function getStatFilesDirectory()
    {
        return getcwd().'/stats-data/stats-files';
    }

    /**
     * @return AbstractStatistic[]
     */
    public static function getAllStats() : array
    {
        return [
            new VisitStats()
        ];
    }
    
    /**
     * @return StatisticFactory
     */
    public static function getStatisticFactory() : StatisticFactory
    {
        $statisticFactory = new StatisticFactory();

        foreach (self::getAllStats() as $stat) {
            $statisticFactory->addStats($stat);
        }
        
        return $statisticFactory;
    }

    /**
     * @return GroupingFactory
     */
    public static function getGroupingFactory() : GroupingFactory
    {
        $groupingFactory = new GroupingFactory();
        
        foreach (self::getAllGroupings() as $id => $model) {
            $groupingFactory->addGroupingModel($id, $model);
        }

        return $groupingFactory;
    }

    /**
     * @return AbstractGrouping[]
     */
    public static function getAllGroupings() : array
    {
        return [
            BasicGroping::TYPE => new BasicGroping(),
        ];
    }
}