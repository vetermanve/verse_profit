<?php


namespace Process\Auth;


use Service\Budget\BudgetService;
use Verse\Modular\ModularContextProto;

class AuthProcessContext extends ModularContextProto
{
    /**
     * @var BudgetService
     */
    private $budgetService;
    
    public const USER_ID = 'user_id';
    
    /**
     * @return BudgetService
     */
    public function getBudgetService(): BudgetService
    {
        return $this->budgetService;
    }
    
    /**
     * @param BudgetService $budgetService
     */
    public function setBudgetService(BudgetService $budgetService): void
    {
        $this->budgetService = $budgetService;
    }
}