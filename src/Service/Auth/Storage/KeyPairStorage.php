<?php


namespace Service\Auth\Storage;


use Base\Storage\GoalsBasicStorage;

class KeyPairStorage extends GoalsBasicStorage
{
    protected function getTableName() : string
    {
        return 'user-key-pairs';
    }
}