<?php


namespace App\Plans\Controller;


use Base\Controller\BasicController;
use Service\Balance\BalanceService;
use Service\Balance\Model\BalanceStatus;
use Service\Plan\Model\PlanStatus;
use Service\Plan\Model\PlanModel;
use Service\Plan\PlansService;

class Plans extends BasicController
{
    
    private function _getMenu()
    {
        return [
            ''                    => 'Активные планы',
            PlanStatus::DELETED   => 'Удаленные планы',
            PlanStatus::COMPLETED => 'Завершенные планы',
        ];
    }
    
    public function index()
    {
        $budgetId = $this->_budgetId;
        $date     = $this->p('date', strtotime('+1 day'));
        $status   = (string)$this->p('status');
        
        $plansService = new PlansService();
        
        if ($status) {
            $statusesToShow = [
                $status
            ];
        } else {
            $statusesToShow = [
                PlanStatus::PLANNED,
                PlanStatus::CREATED,
            ];
        }
        
        $plans = $plansService->getPlansWithStatuses($budgetId, $statusesToShow);
        
        $balanceService = new BalanceService();
        $balances       = $balanceService->getBudgetBalances($this->_budgetId, BalanceStatus::ACTIVE);
        
        return $this->_render(__FUNCTION__, [
            'plans'    => $plans,
            'balances' => $balances,
            'message'  => $this->message,
            'date'     => $date,
            'pageStatus' => $status,
            'menu'     => $this->_getMenu(),
        ]);
    }
    
    public function add()
    {
        return $this->edit();
    }
    
    public function show()
    {
        $id = $this->p('id');
        
        $service = new PlansService();
        $plan    = $service->getPlan($id);
        
        $balances = (new BalanceService())->getBudgetBalances($this->_budgetId);
        
        return $this->_render(__FUNCTION__, [
            'plan'     => $plan,
            'balances' => $balances,
        ]);
    }
    
    public function edit()
    {
        $id = $this->p('id');
        
        $budgetId = $this->_budgetId;
        
        $name        = \trim($this->p('name'));
        $dateRaw     = $this->p('date');
        $date        = strtotime($dateRaw,
                time() - (new \DateTimeZone($this->_user['timezone']))->getOffset(new \DateTime())) ?? time();
        $amount      = $this->p('amount');
        $balanceFrom = $this->p('balance_from');
        $balanceTo   = $this->p('balance_to');
        $status      = $this->p('status', PlanStatus::CREATED);
        
        $service = new PlansService();
        
        if ($name) {
            $plan = [
                PlanModel::NAME         => $name,
                PlanModel::AMOUNT       => $amount,
                PlanModel::DATE         => $date,
                PlanModel::BUDGET_ID    => $budgetId,
                PlanModel::BALANCE_FROM => $balanceFrom,
                PlanModel::BALANCE_TO   => $balanceTo,
                PlanModel::STATUS       => $status,
            ];
            
            if ($id) {
                $updatePlan = $service->updatePlan($id, $plan);
                
                $this->message = $updatePlan ? 'План обновелен!' : 'Не удалось обновить план :(';
                
                if ($updatePlan) {
                    $plan = $updatePlan;
                }
            } else {
                $createdPlan = $service->addPlan($plan);
                
                $this->message = $createdPlan ? 'План сохранен!' : 'Не удалось сохранить план :(';
                
                if ($createdPlan) {
                    $plan = $createdPlan;
                }
            }
        } else {
            $plan = $service->getPlan($id);
        }
        
        $balances = (new BalanceService())->getBudgetBalances($this->_budgetId, BalanceStatus::ACTIVE);
        $statuses = $service->getPlanStatuses();
        
        return $this->_render(__FUNCTION__, [
            'action'   => isset($plan[PlanModel::ID]) ? 'edit' : 'add',
            'plan'     => $plan,
            'balances' => $balances,
            'message'  => $this->message,
            'statuses' => $statuses,
        ]);
    }
    
    public function complete()
    {
        $id = $this->p('id');
        if ($id) {
            $service = new PlansService();
            
            $bind = [
                PlanModel::STATUS => PlanStatus::DELETED
            ];
            
            if ($service->updatePlan($id, $bind)) {
                $this->message = 'План завершен!';
            }
        }
        
        return $this->show();
    }
    
    public function delete()
    {
        $id = $this->p('id');
        if ($id) {
            $service = new PlansService();
            
            $bind = [
                PlanModel::STATUS => PlanStatus::DELETED
            ];
            
            if ($service->updatePlan($id, $bind)) {
                $this->message = 'План удален!';
            }
        }
        
        return $this->show();
    }
    
    protected function getClassDirectory()
    {
        return __DIR__;
    }
}