<?php


namespace Service\Relations\Storage;


use Base\Storage\GoalsBasicStorage;

class RelationsStorage extends GoalsBasicStorage
{
    protected function getTableName() : string
    {
        return 'user-relations';
    }
}