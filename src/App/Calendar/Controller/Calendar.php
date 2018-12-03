<?php


namespace App\Calendar\Controller;


use Base\Controller\BasicController;
use Service\Calendar\CalendarService;
use Service\Plan\Model\PlanModel;
use Service\Plan\PlansService;

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

        $plans = new PlansService();
        $minDay = min($days);
        $maxDay = max($days) + CalendarService::DAY_SECONDS;

        $plans = $plans->getPlans($this->_budgetId, $minDay, $maxDay);

        $daysPlans = [];
        foreach ($days as $day) {
            $daysPlans[$day]['plans'] = [];
        }

        $message = '';
        foreach ($plans as $plan) {
            $dayTime = new \DateTime();
            $dayTime->setTimestamp($plan[PlanModel::DATE]);
            $dayTime->setTime(0, 0, 0);
            $day = $dayTime->getTimestamp();
            if (!isset($daysPlans[$day])) {
                $message .= ', Suggestion ' . date('c') . 'not found a day';
                continue;
            }

            $daysPlans[$day]['plans'][] = $plan;
        }

        foreach ($daysPlans as &$plansData) {
            $increase = 0;
            $decrease = 0;

            foreach ($plansData['plans'] as $plan) {
                if ($plan[PlanModel::AMOUNT] > 0) {
                    $increase += $plan[PlanModel::AMOUNT];
                } else {
                    $decrease += $plan[PlanModel::AMOUNT];
                }
            }

            $plansData['increase'] = $increase;
            $plansData['decrease'] = $decrease;
            $plansData['balance'] = $increase + $decrease;
        }
        unset ($plansData);

        $message && var_dump($message);

        return $this->_render('month', [
            'month'     => $this->month,
            'year'      => $this->yearNumber,
            'days'      => $days,
            'daysPlans' => $daysPlans,
            'message'   => $message,
        ]);
    }

    protected function getClassDirectory()
    {
        return __DIR__;
    }
}