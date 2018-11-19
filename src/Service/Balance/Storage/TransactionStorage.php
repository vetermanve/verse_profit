<?php


namespace Service\Balance\Storage;


use Base\Storage\GoalsBasicStorage;

class TransactionStorage extends GoalsBasicStorage
{
    protected function getTableName() : string
    {
        return 'transaction';
    }
}