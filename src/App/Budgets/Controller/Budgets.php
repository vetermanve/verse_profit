<?php


namespace App\Budgets\Controller;


use Base\Controller\BasicController;
use Service\Balance\BalanceService;
use Service\Balance\Model\BudgetModel;

class Budgets extends BasicController
{
    private $message = '';
    
    public function index () 
    {
        $balanceService = new BalanceService();
        $budgets = $balanceService->getBudgetsByUserId($this->_userId);
        
        return $this->_render(__FUNCTION__, [
            'budgets' => $budgets,
            'message' => $this->message,
        ]);
    }
    
    public function add () 
    {
        $name = $this->p('name');
        $desc = $this->p('desc');
        $isSwitchNow = $this->p('switch');
        
        $balanceService = new BalanceService();
        $budget = $balanceService->createBudget($this->_userId, $name, $desc);
        
        if ($budget) {
            $this->message = 'Бюджет создан!';    
        } else {
            $this->message = 'Не удалось сохранить бюджет :(';
        }
        
        if ($isSwitchNow) {
            $this->selectBudget($budget[BudgetModel::ID]);
        }
        
        return $this->index();
    }
    
    public function select () 
    {
        $budgetId = $this->p('budget_id');
        $this->selectBudget($budgetId);
        $this->message = 'Бюджет выбран!';
        
        return $this->index();
    }
    
    private function selectBudget($budgetId) {
        $this->_budgetId = $budgetId;
        $this->_secureState->setState(self::STATE_KEY_BUDGET_ID, $this->_budgetId, self::STATE_AUTHORISE_DEFAULT_TTL);
        $this->loadUser();
    }

    protected function getClassDirectory()
    {
        return __DIR__;
    }
}