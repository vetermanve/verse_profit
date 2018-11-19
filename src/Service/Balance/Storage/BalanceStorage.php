<?php


namespace Service\Balance\Storage;


use Base\Storage\GoalsBasicStorage;

class BalanceStorage extends GoalsBasicStorage
{

    protected function getTableName() : string
    {
        return 'balance';
    }
}