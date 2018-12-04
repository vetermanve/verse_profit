<?php


namespace Service\Balance;

use Service\Balance\Model\BalanceModel;
use Service\Balance\Model\BalanceType;
use Service\Balance\Model\MovementModel;
use Service\Balance\Model\TransactionModel;
use Service\Balance\Model\TransactionStatus;
use Service\Balance\Storage\BalanceStorage;
use Service\Balance\Storage\MovementStorage;
use Service\Balance\Storage\TransactionStorage;
use Verse\Run\Util\Uuid;
use Verse\Storage\Spec\Compare;

class BalanceService
{
    public function getBalanceStorage()
    {
        return new BalanceStorage();
    }

    public function getTransactionStorage()
    {
        return new TransactionStorage();
    }

    public function getMovementStorage()
    {
        return new MovementStorage();
    }

    public function createBalance($budgetId, $name, $type = BalanceType::CURRENT)
    {
        $id = Uuid::v4();

        $bind = [
            BalanceModel::ID        => $id,
            BalanceModel::NAME      => $name,
            BalanceModel::BUDGET_ID => $budgetId,
            BalanceModel::AMOUNT    => 0,
            BalanceModel::TYPE      => $type,
        ];

        return $this->getBalanceStorage()->write()->insert($id, $bind, __METHOD__);
    }

    public function getBudgetBalances($budgetId)
    {
        return $this->getBalanceStorage()->search()->find([
            [BalanceModel::BUDGET_ID, Compare::EQ, $budgetId],
        ], 1000, __METHOD__) ? : [];
    }

    public function removeBalance($balanceId)
    {
        return $this->getBalanceStorage()->write()->remove($balanceId, __METHOD__);
    }

    public function addTransactionAndMovements($amount, $description, $balanceToId, $balanceFromId = null)
    {
        $transactionStorage = $this->getTransactionStorage();
        $movementStorage = $this->getMovementStorage();

        $transactionId = Uuid::v4();
        $date = time();

        $transactionBind = [
            TransactionModel::ID           => $transactionId,
            TransactionModel::AMOUNT       => $amount,
            TransactionModel::DESCRIPTION  => $description,
            TransactionModel::BALANCE_FROM => $balanceFromId,
            TransactionModel::BALANCE_TO   => $balanceToId,
            TransactionModel::STATUS       => TransactionStatus::CREATED,
            TransactionModel::CREATED_DATE => $date,
        ];

        $transaction = $transactionStorage->write()->insert($transactionId, $transactionBind, __METHOD__);
        if (!$transaction) {
            return false;
        }
        
        $transactionStorage->write()->update($transactionId, [
            TransactionModel::STATUS => TransactionStatus::STARTED 
        ], __METHOD__);

        if ($balanceFromId) {
            $movementOutId = Uuid::v4();
            $movementOutBind = [
                MovementModel::ID             => $movementOutId,
                MovementModel::BALANCE_ID     => $balanceFromId,
                MovementModel::TRANSACTION_ID => $transactionId,
                MovementModel::AMOUNT         => -$amount,
            ];

            $out = $movementStorage->write()->insert($movementOutId, $movementOutBind, __METHOD__);
            if (!$out) {
                $transactionStorage->write()->update($transactionId, [
                    TransactionModel::STATUS => TransactionStatus::CREATED
                ], __METHOD__);
                
                return false;
            }

            $transactionStorage->write()->update($transactionId, [
                TransactionModel::STATUS => TransactionStatus::MONEY_OUT
            ], __METHOD__);
        }
        
        $movementInId = Uuid::v4();
        $movementInBind = [
            MovementModel::ID             => $movementInId,
            MovementModel::BALANCE_ID     => $balanceToId,
            MovementModel::TRANSACTION_ID => $transactionId,
            MovementModel::AMOUNT         => $amount,
        ];

        $in = $movementStorage->write()->insert($movementInId, $movementInBind, __METHOD__);
        if (!$in) {
            if (isset($movementOutId)) {
                $movementStorage->write()->remove($movementOutId, __METHOD__); 
            }
            
            $transactionStorage->write()->update($transactionId, [
                TransactionModel::STATUS => TransactionStatus::CREATED
            ], __METHOD__);
            
            return false;
        }

        $transactionStorage->write()->update($transactionId, [
            TransactionModel::STATUS => TransactionStatus::MONEY_IN
        ], __METHOD__);
        
        $this->updateBalance($balanceToId);
        
        if (isset($movementOutId)) {
            $this->updateBalance($balanceFromId);
        }

        $transactionStorage->write()->update($transactionId, [
            TransactionModel::STATUS => TransactionStatus::FINISHED
        ], __METHOD__);
        
        return $transaction;
    }
    
    public function updateBalance ($balanceId) 
    {
        $balance = $this->getBalanceStorage()->read()->get($balanceId, __METHOD__);
        if (!$balance) {
            return false;
        }
        
        // $lastMovementDate = $balance[BalanceModel::LAST_MOVEMENT_DATE];
        // $lastMovementId = $balance[BalanceModel::LAST_MOVEMENT_ID];
        $movements = $this->getMovementStorage()->search()->find([
            [MovementModel::BALANCE_ID, Compare::EQ, $balanceId]
        ], 10000, __METHOD__);
        
        if (!\is_array($movements)) {
            return false;
        }
        
        $amount = 0;
        foreach ($movements as &$movement) {
            $amount += (float)$movement[MovementModel::AMOUNT];
        } unset($movement);
        
        return $this->getBalanceStorage()->write()->update($balanceId, [
             BalanceModel::AMOUNT => $amount
        ], __METHOD__);
    }
    
    public function getBalance ($balanceId) 
    {
        return $this->getBalanceStorage()->read()->get($balanceId, __METHOD__);
    }

    public function getBalanceTransactions($balanceId, $limit = 10000, $offset = 0)
    {
        $movements = $this->getMovementStorage()->search()->find([
            [MovementModel::BALANCE_ID, Compare::EQ, $balanceId],
        ], $limit, __METHOD__);
        
        if (!$movements) {
            return []; 
        }
        
        $transactionsIds = array_column($movements, MovementModel::TRANSACTION_ID); 
        $transactions = $this->getTransactionStorage()->read()->mGet($transactionsIds, __METHOD__);
        
        return $transactions;
    }
}