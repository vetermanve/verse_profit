<?php


namespace App\Balances\Controller;


use Base\Controller\BasicController;
use Service\Balance\Model\BalanceType;

class Balances extends BasicController
{
    public function index () 
    {
        return $this->_render(__FUNCTION__, [
            'balances' => [],
            'balanceTypes' => BalanceType::getValues(),
        ]);
    }
    
    protected function getClassDirectory()
    {
        return __DIR__;
    }
}