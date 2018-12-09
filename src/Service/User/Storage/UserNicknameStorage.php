<?php


namespace Service\User\Storage;


use Base\Storage\GoalsBasicStorage;

class UserNicknameStorage extends GoalsBasicStorage
{
    protected function getTableName() : string
    {
        return 'user-nickname';
    }
}