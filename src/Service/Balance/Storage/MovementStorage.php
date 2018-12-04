<?php


namespace Service\Balance\Storage;


use Base\Storage\GoalsBasicStorage;

class MovementStorage extends GoalsBasicStorage
{
    protected function getTableName() : string
    {
        return 'movement';
    }
}