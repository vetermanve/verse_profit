<?php


namespace Stats;


use Stats\ViewConfig\VisitStats;
use Verse\Statistic\Aggregate\EventStream\EventStreamInterface;
use Verse\Statistic\Aggregate\EventStream\FilesDirectoryEventStream;
use Verse\Statistic\Configuration\Grouping\AbstractGrouping;
use Verse\Statistic\Configuration\Grouping\BasicGroping;
use Verse\Statistic\Configuration\GroupingFactory;
use Verse\Statistic\Configuration\StatisticFactory;
use Verse\Statistic\Configuration\Stats\AbstractStatistic;
use Verse\Statistic\Storage\Records\StatRecordsStorageInterface;
use Verse\Statistic\Storage\Records\VerseStorageStatRecords;
use Verse\Statistic\Storage\Unique\UniqueStorageInterface;
use Verse\Statistic\Storage\Unique\VerseStorageUnique;

class StatsConfig
{
    public static function getStatFilesDirectory()
    {
        return getcwd().'/stats-data/stats-files';
    }

    public static function getStatStorageDirectory()
    {
        return getcwd().'/stats-data/jbase';
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

    /**
     * @return EventStreamInterface
     */
    public static function getEventStream() : EventStreamInterface
    {
        $stream = new FilesDirectoryEventStream();
        $stream->setStatFilesDirectory(self::getStatFilesDirectory());
        return $stream;
    }

    /**
     * @return StatRecordsStorageInterface|\Verse\Storage\SimpleStorage
     */
    public static function getStatsStorage() : StatRecordsStorageInterface
    {
        $storage = new VerseStorageStatRecords();
        $storage->getContext()->set(VerseStorageStatRecords::DATA_ROOT_PATH, self::getStatStorageDirectory());
        return $storage;
    }

    /**
     * @return UniqueStorageInterface
     */
    public static function getUniqueStorage() : UniqueStorageInterface
    {
        $storage = new VerseStorageUnique();
        $storage->getContext()->set(VerseStorageStatRecords::DATA_ROOT_PATH, self::getStatStorageDirectory());
        return $storage;
    }
}