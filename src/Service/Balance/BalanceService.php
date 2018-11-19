<?php


namespace Service\Balance;


use Service\Balance\Model\BudgetModel;
use Service\Balance\Storage\BudgetOwners;
use Service\Balance\Model\BudgetOwnersModel;
use Service\Balance\Storage\BalanceStorage;
use Service\Balance\Storage\BudgetStorage;
use Verse\Run\Util\Uuid;
use Verse\Storage\Spec\Compare;

class BalanceService
{
    public function getStorage () 
    {
        return new BalanceStorage();
    }
    
    public function getBudget ($budgetId) 
    {
        return (new BudgetStorage())->read()->get($budgetId, __METHOD__, []);
    }
    
    public function getBudgetsByUserId ($userId) 
    {
        $budgetOwnersStorage = new BudgetOwners();
        
        $budgetsOwners = $budgetOwnersStorage->search()->find([
            [BudgetOwnersModel::USER_ID, Compare::EQ, $userId]
        ], 100, __METHOD__);
        
        $budgetsOwners = $budgetsOwners ? $budgetsOwners : [];
        
        $budgetStorage = new BudgetStorage();
        return $budgetStorage->read()->mGet(array_column($budgetsOwners, BudgetOwnersModel::BUDGET_ID), __METHOD__, []);
    }
    
    public function createBudget ($userId, $name, $description) 
    {
        $budgetStorage = new BudgetStorage();
        $budgetId = Uuid::v4();
        $budget = [
            BudgetModel::NAME => $name,
            BudgetModel::DESCRIPTION => $description
        ];
        
        $budget = $budgetStorage->write()->insert($budgetId , $budget, __METHOD__); 
        if ($budget) {
            $budgetOwnersStorage = new BudgetOwners();
            
            $res = $budgetOwnersStorage->write()->insert(Uuid::v4(), [
               BudgetOwnersModel::USER_ID => $userId,
               BudgetOwnersModel::BUDGET_ID => $budgetId 
            ], __METHOD__);
            
            if ($res) {
                return $budget; 
            }
        }
        
        return false;
    }
    
    public function createBalance ($userId) 
    {
        $bind = [
            
        ];
    }
}