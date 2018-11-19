<?php


namespace Service\Suggestion\Storage;


use Base\Storage\GoalsBasicStorage;

class SuggestionStorage extends GoalsBasicStorage
{

    protected function getTableName() : string
    {
        return 'suggestion';
    }
}