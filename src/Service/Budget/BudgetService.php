<?php


namespace Service\Budget;


use Service\Budget\Model\BudgetModel;
use Service\Budget\Model\BudgetOwnersModel;
use Service\Budget\Storage\BudgetOwnersStorage;
use Service\Budget\Storage\BudgetStorage;
use Verse\Run\Util\Uuid;
use Verse\Storage\Spec\Compare;

class BudgetService
{
    public function getBudget($budgetId)
    {
        return (new BudgetStorage())->read()->get($budgetId, __METHOD__, []);
    }
    
    public function getBudgetsByUserId($userId)
    {
        $budgetOwnersStorage = new BudgetOwnersStorage();
        
        $budgetsOwners = $budgetOwnersStorage->search()->find([
            [BudgetOwnersModel::USER_ID, Compare::EQ, $userId]
        ], 100, __METHOD__);
        
        $budgetsOwners = $budgetsOwners ? $budgetsOwners : [];
        
        $budgetStorage = new BudgetStorage();
        
        return $budgetStorage->read()->mGet(array_column($budgetsOwners, BudgetOwnersModel::BUDGET_ID), __METHOD__, []);
    }
    
    public function addUserToBudget($budgetId, $userId, $userAdderId)
    {
        $budgetOwnersStorage = new BudgetOwnersStorage();
        $res                 = $budgetOwnersStorage->write()->insert(Uuid::v4(), [
            BudgetOwnersModel::USER_ID       => $userId,
            BudgetOwnersModel::BUDGET_ID     => $budgetId,
            BudgetOwnersModel::ADDER_USER_ID => $userAdderId
        ], __METHOD__);
        
        return $res;
    }
    
    public function removeUserFromBudget($budgetId, $userId, $userRemoverId)
    {
        $budgetOwnersStorage = new BudgetOwnersStorage();
        
        $budgetRelations = $budgetOwnersStorage->search()->find([
            [BudgetOwnersModel::USER_ID, Compare::EQ, $userId],
            [BudgetOwnersModel::BUDGET_ID, Compare::EQ, $budgetId],
        ], 100, __METHOD__);
        
        $relationsIds = array_column($budgetRelations, BudgetOwnersModel::ID);
        
        foreach ($relationsIds as $relationId) {
            $budgetOwnersStorage->write()->remove($relationId, __METHOD__);    
        }
        
        return $budgetRelations;
    }
    
    public function getBudgetOwnersByBudgetId($budgetId, $limit = 100)
    {
        $budgetOwnersStorage = new BudgetOwnersStorage();
        
        $budgetsOwners = $budgetOwnersStorage->search()->find([
            [BudgetOwnersModel::BUDGET_ID, Compare::EQ, $budgetId]
        ], $limit, __METHOD__);
        
        return $budgetsOwners ?: [];
    }
    
    public function createBudget($userId, $name, $description)
    {
        $budgetStorage = new BudgetStorage();
        
        $budgetId = Uuid::v4();
        
        $budget = [
            BudgetModel::NAME        => $name,
            BudgetModel::DESCRIPTION => $description,
            BudgetModel::CREATED_AT  => \time(),
            BudgetModel::OWNER_ID    => $userId,
        ];
        
        $budget = $budgetStorage->write()->insert($budgetId, $budget, __METHOD__);
        if ($budget) {
            $budgetOwnersStorage = new BudgetOwnersStorage();
            
            $res = $budgetOwnersStorage->write()->insert(Uuid::v4(), [
                BudgetOwnersModel::USER_ID   => $userId,
                BudgetOwnersModel::BUDGET_ID => $budgetId
            ], __METHOD__);
            
            if ($res) {
                return $budget;
            }
        }
        
        return false;
    }
}