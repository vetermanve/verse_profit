<?php


namespace Service\Budget\Storage;


use Base\Storage\GoalsBasicStorage;

class BudgetOwners extends GoalsBasicStorage
{
    protected function getTableName() : string
    {
        return 'budget-owners';
    }
}