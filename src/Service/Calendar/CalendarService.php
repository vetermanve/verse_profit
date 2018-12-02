<?php


namespace Service\Calendar;


class CalendarService
{
    public const DAY_SECONDS = 86400;
    
    public function getMonthsStarts($year) : array
    {
        $result = [];
        foreach (range(1, 12) as $month) {
            $result[$month] = mktime(0, 0, 0, $month, 1, $year);
        }

        return $result;
    }
    
    public function getDaysOfMonthDate ($monthDate) : array
    {
        $monthDay = new \DateTime();
        $monthDay->setTimestamp($monthDate);

        $nextMonth = new \DateTime();
        $nextMonth->setTimestamp($monthDate);
        $nextMonth->modify('first day of next month');
        $nextMonthStartDayTime = $nextMonth->getTimestamp();
        
        $result = [];
        while ($monthDay->getTimestamp() !== $nextMonthStartDayTime) {
            $result[] = $monthDay->getTimestamp();
            $monthDay->modify('+1 day');
        }
        
        return $result;
    }

    public function getYears() : array
    {
        return range(2018, 2022);
    }
}