<?php


namespace App\Balances\Controller;


use Base\Controller\BasicController;
use Service\Balance\BalanceService;
use Service\Balance\Model\BalanceType;
use Service\Balance\Model\TransactionModel;

class Balances extends BasicController
{
    public function index()
    {
        $balanceService = new BalanceService();

        $balances = $balanceService->getBudgetBalances($this->_budgetId);

        return $this->_render(__FUNCTION__, [
            'balances'     => $balances,
            'balanceTypes' => BalanceType::getValues(),
            'message'      => $this->message,
        ]);
    }

    public function add()
    {
        $name = $this->p('name');
        $type = $this->p('type');
        if ($name) {
            $balanceService = new BalanceService();
            $res = $balanceService->createBalance($this->_budgetId, $name, $type);
            if ($res) {
                $this->message = 'Счет удачно создан!';
            } else {
                $this->message = 'Не удалось создать счет.';
            }
        }

        return $this->index();
    }

    public function delete()
    {
        $id = $this->p('id');
        $balanceService = new BalanceService();
        if ($id) {
            $res = $balanceService->removeBalance($id);
            if ($res) {
                $this->message = 'Баланс успешно удален';
            } else {
                $this->message = 'Не удалось удалить баланс';
            }
        }

        return $this->index();
    }

    public function show()
    {
        $balanceId = $this->p('id', $this->p('balance_to_id'));
        $balanceService = new BalanceService();
        $transactions = $balanceService->getBalanceTransactions($balanceId);
        uasort($transactions, function ($tr1, $tr2) {
            return $tr1[TransactionModel::CREATED_DATE] <= $tr2[TransactionModel::CREATED_DATE] ? 1 : -1;
        });

        $balance = $balanceService->getBalance($balanceId);
        $allBalances = $balanceService->getBudgetBalances($this->_budgetId);

        return $this->_render(__FUNCTION__, [
            'message'      => $this->message,
            'balance'      => $balance,
            'allBalances'  => $allBalances,
            'transactions' => $transactions,
            'balanceTypes' => BalanceType::getValues(),
            'bind' => [
                'date' => date('d.m.Y'),
            ],
        ]);
    }

    public function addTransaction()
    {
        $balanceId = $this->p('balance_to_id');
        $balanceFromId = $this->p('balance_from_id');
        $amount = (float)$this->p('amount');
        $description = $this->p('description');

        if ($amount) {
            $balanceService = new BalanceService();
            $res = $balanceService->addTransactionAndMovements(
                $amount,
                $description,
                $balanceId,
                $balanceFromId
            );

            if ($res) {
                $this->message = 'Транзакция сохранена!';
            } else {
                $this->message = 'Не удалось сохранить транзакцию.';
            }
        }

        return $this->show();
    }

    protected function getClassDirectory()
    {
        return __DIR__;
    }
}