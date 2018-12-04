<?php


namespace App\Plans\Controller;


use Base\Controller\BasicController;
use Service\Plan\PlansService;

class Plans extends BasicController
{
    private $addDate = '';

    public function index()
    {
        $budgetId = $this->_budgetId;
        $fromDate = $this->p('from', strtotime('-5day'));
        $toDate = $this->p('to', strtotime('+20day'));
        $this->addDate = $this->p('date', strtotime('+1 day'));

        $service = new PlansService();
        $suggestions = $service->getPlans($budgetId, $fromDate, $toDate);

        return $this->_render(__FUNCTION__, [
            'suggestions' => $suggestions,
            'message'     => $this->message,
            'date'        => $this->addDate,
        ]);
    }

    public function add()
    {
        $budgetId = $this->_budgetId;

        $name = $this->p('name');
        $dateRaw = $this->p('date');
        $date = strtotime($dateRaw) ?? time();
        $amount = $this->p('amount');

        $service = new PlansService();
        $suggestion = $service->addPlan($budgetId, $name, $amount, $date);

        if ($suggestion) {
            $this->message = 'План сохранен! Дата: ' . date('d.m.Y', $date);
        }

        return $this->index();
    }
    
    public function delete () 
    {
        $id = $this->p('id');
        if ($id) {
            $service = new PlansService();
            if ($service->removePlan($id)) {
                $this->message = 'План удален!';    
            }
        }
        
        return $this->index();
    }

    protected function getClassDirectory()
    {
        return __DIR__;
    }
}