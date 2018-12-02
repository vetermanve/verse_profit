<?php


namespace Service\Plan;


use Service\Plan\Model\PlanModel;
use Service\Plan\Storage\PlanStorage;
use Verse\Run\Util\Uuid;
use Verse\Storage\Spec\Compare;

class PlansService
{
    /**
     * @var Storage\PlanStorage
     */
    private $storage;

    /**
     * @return PlanStorage
     */
    public function getStorage() : PlanStorage
    {
        if (!$this->storage) {
            $this->storage = new PlanStorage();
        }
        
        return $this->storage;
    }
    
    public function addPlan ($budgetId, $name, $amount, $date, $belongsType = null, $belongsId = null)  
    {
        $bind = [
            PlanModel::BUDGET_ID    => $budgetId,
            PlanModel::NAME         => $name,
            PlanModel::AMOUNT       => $amount,
            PlanModel::DATE         => $date,
            PlanModel::BELONGS_TYPE => $belongsType,
            PlanModel::BELONGS_ID   => $belongsId
        ];
        
        return $this->getStorage()->write()->insert(Uuid::v4(), $bind, __METHOD__);    
    }
    
    public function removePlan ($suggestionId) 
    {
        return $this->getStorage()->write()->remove($suggestionId, __METHOD__);
    }
    
    public function getPlans ($budgetId, $dateFrom, $dateTo, $limit = 1000) 
    {
        $result = $this->getStorage()->search()->find([
            [PlanModel::BUDGET_ID, Compare::EQ, $budgetId],
            [PlanModel::DATE, Compare::GRATER_OR_EQ, $dateFrom],
            [PlanModel::DATE, Compare::LESS_OR_EQ, $dateTo],
        ], $limit, __METHOD__);
        
        uasort($result, function ($a1, $a2) {
            return $a1[PlanModel::DATE] >= $a2[PlanModel::DATE] ? 1 : 0; 
        });
        
        return $result;
    }
}