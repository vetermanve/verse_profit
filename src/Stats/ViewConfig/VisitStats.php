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
            Events::PAGE_VISIT,
            Events::VISIT_CONTACTS,  
            Events::VISIT_MAINPAGE,
            Events::VISIT_STATS_ACTIONS,
            Events::VISIT_SCOPED_STATS,
            Events::VISIT_SITE_STATS,
        ];
    }
    
    public function getFieldNames ()
    {
        return [
            Events::PAGE_VISIT => 'Pages Visit',
            Events::VISIT_CONTACTS => 'Contact Page Visits',
            Events::VISIT_MAINPAGE => 'Main Page Visits',
            Events::VISIT_SCOPED_STATS => 'Scoped Stats Page Visits', 
            Events::VISIT_SITE_STATS => 'All Stats Page Visits',
        ];
    }
}