<?php

namespace Stats\ViewConfig;

use Stats\Events;
use Verse\Statistic\Configuration\Grouping\BasicGroping;
use Verse\Statistic\Configuration\Stats\AbstractStatistic;

class VisitStats extends AbstractStatistic
{
    public function getName()
    {
        return 'Статистика посещений';
    }

    public function getId()
    {
        return 'visits';
    }

    public function getGroupingIds()
    {
        return [
            BasicGroping::TYPE  
        ];
    }
    
    public function getFields () 
    {
        return [
            Events::VISIT_CONTACTS,  
            Events::VISIT_MAINPAGE,
            Events::VISIT_STATS_ACTIONS,
            Events::VISIT_GENERATED_STATS_VIEW,
            Events::VISIT_SITE_STATS_VIEW,
        ];
    }   
}