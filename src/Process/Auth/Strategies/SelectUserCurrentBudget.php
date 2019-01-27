<?php


namespace Process\Auth\Strategies;


use Process\Auth\AuthProcessContainer;
use Process\Auth\AuthProcessContext;
use Process\Auth\AuthProcessModuleProto;
use Service\Budget\Model\BudgetModel;
use Verse\Modular\ModularStrategyInterface;

class SelectUserCurrentBudget extends AuthProcessModuleProto implements ModularStrategyInterface
{
    
    public function prepare()
    {
        
    }
    
    public function run()
    {
        $userId = $this->context->get(AuthProcessContext::USER_ID);
        $budgetService = $this->context->getBudgetService();
        
        $budgetId = $budgetService->getSelectedBudgetId($userId);
        
        if ($budgetId) {
            $this->container->data[AuthProcessContainer::USER_CURRENT_BUDGET] = $budgetId;    
        }
    }
    
    public function shouldProcess()
    {
        return $this->context->is(AuthProcessContext::USER_ID);
    }
}