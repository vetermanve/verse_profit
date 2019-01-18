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
    public function getStorage(): PlanStorage
    {
        if (!$this->storage) {
            $this->storage = new PlanStorage();
        }
        
        return $this->storage;
    }
    
    public function addPlan($bind)
    {
        return $this->getStorage()->write()->insert(Uuid::v4(), $bind, __METHOD__);
    }
    
    public function updatePlan($id, $bind)
    {
        return $this->getStorage()->write()->update($id, $bind, __METHOD__);
    }
    
    public function getPlan ($id, $default = []) 
    {
        return $this->getStorage()->read()->get($id, __METHOD__, $default);
    }
    
    public function removePlan($id)
    {
        return $this->getStorage()->write()->remove($id, __METHOD__);
    }
    
    public function getPlans($budgetId, $dateFrom = null, $dateTo = null, $limit = 1000)
    {
        $filter = [
            [PlanModel::BUDGET_ID, Compare::EQ, $budgetId]
        ];
        
        if ($dateFrom) {
            $filter[] = [PlanModel::DATE, Compare::GRATER_OR_EQ, $dateFrom];
        }
        
        if ($dateTo) {
            $filter[] = [PlanModel::DATE, Compare::LESS_OR_EQ, $dateTo];
        }
        
        $result = $this->getStorage()->search()->find($filter, $limit, __METHOD__);
        
        uasort($result, function ($a1, $a2) {
            return $a1[PlanModel::DATE] < $a2[PlanModel::DATE] ? -1 : 1;
        });
        
        return $result;
    }
}