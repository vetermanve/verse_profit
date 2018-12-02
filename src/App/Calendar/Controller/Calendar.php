<?php


namespace App\Calendar\Controller;


use Base\Controller\BasicController;
use Service\Calendar\CalendarService;

class Calendar extends BasicController
{
    /**
     * Year, human-readable eg 2018
     * @var int
     */
    private $yearNumber;

    /**
     * Month number, human-readable eg 1
     * @var int
     */
    private $monthNumber;

    /**
     * Month start unix time eg 1514754000
     *
     * @var int
     */
    private $month;

    public function run()
    {
        $this->yearNumber = $this->p('year');
        $this->monthNumber = $this->p('month');

        if ($this->yearNumber) {
            $this->setState('year', $this->yearNumber);
        } else {
            $this->yearNumber = $this->getState('year', (int) date('Y'));
        }

        if ($this->monthNumber) {
            $this->setState('month', $this->monthNumber);
        } else {
            $this->monthNumber = $this->getState('month', (int) date('m'));
        }

        $this->month = mktime(0, 0, 0, $this->monthNumber, 1, $this->yearNumber);

        return parent::run();
    }

    public function index()
    {
        $calendar = new CalendarService();

        $days = $calendar->getDaysOfMonthDate($this->month);

        return $this->_render('month', [
            'month' => $this->month,
            'year'  => $this->yearNumber,
            'days'  => $days,
        ]);
    }

    protected function getClassDirectory()
    {
        return __DIR__;
    }
}