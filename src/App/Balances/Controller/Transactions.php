<?php


namespace App\Balances\Controller;


use Base\Controller\BasicController;
use Service\Balance\BalanceService;
use Service\Balance\Model\BalanceModel;

class Transactions extends BasicController
{
    public function index () 
    {
        $balances =
        $transactions = [];
        
        if ($this->_budgetId) {
            $balanceService = new BalanceService();
            
            $balances = $balanceService->getBudgetBalances($this->_budgetId);
            $balanceIds = array_column($balances, BalanceModel::ID);
            $transactions = $balanceService->getBalancesTransactions($balanceIds, strtotime('-1 month'), \time());
        }
        
        return $this->_render('transactions', [
            'allBalances' => $balances,
            'transactions' => $transactions, 
        ]);
    }
    
    protected function getClassDirectory()
    {
        return __DIR__;
    }
}