<?php


namespace Service\Budget\Storage;


use Base\Storage\GoalsBasicStorage;
use Service\Budget\Model\BudgetSelectionModel;

class BudgetSelectionStorage extends GoalsBasicStorage
{
    protected function getTableName() : string
    {
        return 'budget-selection';
    }
    
    protected function getPrimaryKey()
    {
        return BudgetSelectionModel::USER_ID;
    }
}