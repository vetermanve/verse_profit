<?php


namespace Service\Balance\Storage;


use Base\Storage\GoalsBasicStorage;

class BudgetStorage extends GoalsBasicStorage
{

    protected function getTableName() : string
    {
        return 'budget';
    }
}