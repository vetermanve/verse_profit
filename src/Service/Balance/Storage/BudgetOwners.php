<?php


namespace Service\Balance\Storage;


use Base\Storage\GoalsBasicStorage;

class BudgetOwners extends GoalsBasicStorage
{
    protected function getTableName() : string
    {
        return 'budget-owners';
    }
}