<?php


namespace App\Plans\Controller;


use Base\Controller\BasicController;
use Service\Balance\BalanceService;
use Service\Plan\Model\PlanModel;
use Service\Plan\PlansService;

class Plans extends BasicController
{
    
    public function index()
    {
        $budgetId = $this->_budgetId;
        $date     = $this->p('date', strtotime('+1 day'));
        
        $plansService = new PlansService();
        $plans        = $plansService->getPlans($budgetId);
        
        $balanceService = new BalanceService();
        $balances       = $balanceService->getBudgetBalances($this->_budgetId);
        
        return $this->_render(__FUNCTION__, [
            'plans'    => $plans,
            'balances' => $balances,
            'message'  => $this->message,
            'date'     => $date,
        ]);
    }
    
    public function add()
    {
        $budgetId = $this->_budgetId;
        
        $name        = $this->p('name');
        $dateRaw     = $this->p('date');
        $date        = strtotime($dateRaw, time() - (new \DateTimeZone($this->_user['timezone']))->getOffset(new \DateTime())) ?? time();
        $amount      = $this->p('amount');
        $balanceFrom = $this->p('balance_from');
        $balanceTo   = $this->p('balance_to');
        
        $service = new PlansService();
        $bind    = [
            PlanModel::NAME         => $name,
            PlanModel::AMOUNT       => $amount,
            PlanModel::DATE         => $date,
            PlanModel::BUDGET_ID    => $budgetId,
            PlanModel::BALANCE_FROM => $balanceFrom,
            PlanModel::BALANCE_TO   => $balanceTo,
        ];
        
        $plan = $service->addPlan($bind);
        
        if ($plan) {
            $this->message = 'План сохранен! Дата: ' . date('d.m.Y', $date);
        }
        
        return $this->index();
    }
    
    public function delete()
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