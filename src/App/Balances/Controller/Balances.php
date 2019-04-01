<?php


namespace App\Balances\Controller;


use Base\Controller\BasicController;
use Service\Balance\BalanceService;
use Service\Balance\Model\BalanceModel;
use Service\Balance\Model\BalanceStatus;
use Service\Balance\Model\BalanceType;
use Service\Balance\Model\TransactionModel;
use Service\Budget\Model\BudgetModel;

class Balances extends BasicController
{
    public function index()
    {
        $balanceService = new BalanceService();

        $balances = $balanceService->getBudgetBalances($this->_budgetId, BalanceStatus::ACTIVE);

        $balancesByTypes = array_combine(array_keys(BalanceType::getValues()),
            array_fill(0, count(BalanceType::getValues()), []));

        foreach ($balances as $balance) {
            $balancesByTypes[$balance[BalanceModel::TYPE]][] = $balance;
        }

        return $this->_render(__FUNCTION__, [
            'balancesByTypes' => $balancesByTypes,
            'balanceTypes'    => BalanceType::getValues(),
            'message'         => $this->message,
        ]);
    }

    public function add()
    {
        $name = $this->p('name');
        $type = $this->p('type');
        if ($name) {
            $balanceService = new BalanceService();
            $res            = $balanceService->createBalance($this->_budgetId, $name, $type);
            if ($res) {
                $this->message = 'Счет удачно создан!';
            } else {
                $this->message = 'Не удалось создать счет.';
            }
        }

        return $this->index();
    }

    public function archive()
    {
        $id             = $this->p('id');
        $balanceService = new BalanceService();
        if ($id) {
            $res = $balanceService->updateBalance($id, [
                BalanceModel::STATUS => BalanceStatus::ARCHIVED
            ]);

            if ($res) {
                $this->message = 'Баланс успешно архивирован';
            } else {
                $this->message = 'Не удалось заархивировать баланс';
            }
        }

        return $this->index();
    }

    public function show()
    {
        $balanceId      = $this->p('id', $this->p('balance_to_id'));
        $balanceService = new BalanceService();
        $transactions   = $balanceService->getBalanceTransactions($balanceId);
        uasort($transactions, function ($tr1, $tr2) {
            return $tr1[TransactionModel::CREATED_DATE] <= $tr2[TransactionModel::CREATED_DATE] ? 1 : -1;
        });

        $balance     = $balanceService->getBalance($balanceId);
        $allBalances = $balanceService->getBudgetBalances($this->_budgetId);
        $activeBalances = $balanceService->getBudgetBalances($this->_budgetId, BalanceStatus::ACTIVE);

        return $this->_render(__FUNCTION__, [
            'message'      => $this->message,
            'balance'      => $balance,
            'activeBalances' => $activeBalances,
            'allBalances'  => $allBalances,
            'transactions' => $transactions,
            'balanceTypes' => BalanceType::getValues(),
            'bind'         => [
                'date' => date('d.m.Y'),
            ],
        ]);
    }

    public function edit()
    {
        $id   = $this->p('id');
        $name = trim($this->p('name'));
        $type = $this->p('type');

        $balanceService = new BalanceService();
        $balance        = $balanceService->getBalance($id);

        if (!$balance) {
            $this->message = 'Баланс не найден';
        } elseif ($name && $type) {
            $updateBind = [
                BalanceModel::NAME => $name,
                BalanceModel::TYPE => $type,
            ];

            $balance = $balanceService->updateBalance($balance[BalanceModel::ID], $updateBind);

            if ($balance) {
                $this->message = 'Счет удачно обновлен!';
            } else {
                $this->message = 'Не удалось обновить счет :(';
            }
        }

        return $this->_render(__FUNCTION__, [
            'balanceTypes' => BalanceType::getValues(),
            'balance'      => $balance ?? [],
            'action'       => 'edit',
            'message'      => $this->message,
        ]);
    }

    public function addTransaction()
    {
        $balanceId     = $this->p('balance_to_id');
        $balanceFromId = $this->p('balance_from_id');
        $amountString  = (string)$this->p('amount');
        $description   = $this->p('description');

        $amount = $amountString ? (float)preg_replace('/[,.]+/', '.', preg_replace('/[^0-9\.,]/', '', $amountString)) : 0;

        if ($amount) {
            $balanceService = new BalanceService();

            $res = $balanceService->addTransactionAndMovements($amount, $description, $balanceId, $balanceFromId);

            if ($res) {
                $this->message = 'Транзакция сохранена!';
            } else {
                $this->message = 'Не удалось сохранить транзакцию.';
            }
        }

        return $this->show();
    }

    public function setBalanceAmount()
    {
        $balanceId     = $this->p('balance_to_id');
        $description   = $this->p('description');
        $targetAmount  = (string)$this->p('amount');

        $balanceToTransferDifference = $this->p('balance_from_id');

        $targetAmount = $targetAmount ? (float)preg_replace('/[,.]+/', '.', preg_replace('/[^0-9\.,-]/', '', $targetAmount)) : 0;

        $balanceService = new BalanceService();
        $balance = $balanceService->getBalance($balanceId);
        $balanceAmount = $balance[BalanceModel::AMOUNT];

        $action = null;
        $difference = 0;

        switch (true) {
                 // target amount positive, need to add money
            case $targetAmount > 0 && $targetAmount > $balanceAmount:
                $action = 'add';
                $difference = $targetAmount - $balanceAmount;
                break;

                // target amout positive, need to spend money
            case $targetAmount > 0 && $targetAmount < $balanceAmount:
                $action = 'reduce';
                $difference = $balanceAmount - $targetAmount;
                break;
                // target amount negative, need to spend
            case $targetAmount <= 0 && $targetAmount < $balanceAmount:
                $action = 'reduce';
                $difference = $balanceAmount - $targetAmount;
                break;
                // target amount negative, need to add
            case $targetAmount <= 0 && $targetAmount > $balanceAmount:
                $action = 'add';
                $difference = $targetAmount - $balanceAmount;
                break;
        }

        if ($action && $difference) {
            $res = null;

            if ($action === 'add') {
                $res = $balanceService->addTransactionAndMovements($difference, $description, $balanceId, $balanceToTransferDifference);
            } else {
                $res = $balanceService->addTransactionAndMovements($difference, $description, $balanceToTransferDifference, $balanceId);
            }

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