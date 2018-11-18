<?php


namespace Service\User\Storage;


use Base\Storage\GoalsBasicStorage;

class UserStorage extends GoalsBasicStorage
{
    protected function getTableName() : string
    {
        return 'user-profile';
    }
}