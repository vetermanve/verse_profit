<?php


namespace Service\Plan\Storage;


use Base\Storage\GoalsBasicStorage;

class PlanStorage extends GoalsBasicStorage
{

    protected function getTableName() : string
    {
        return 'suggestion';
    }
}