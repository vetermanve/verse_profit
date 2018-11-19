<?php


namespace Service\Suggestion;


use Service\Suggestion\Model\SuggestionModel;
use Service\Suggestion\Storage\SuggestionStorage;
use Verse\Run\Util\Uuid;
use Verse\Storage\Spec\Compare;

class SuggestionService
{
    /**
     * @var Storage\SuggestionStorage
     */
    private $storage;

    /**
     * @return SuggestionStorage
     */
    public function getStorage() : SuggestionStorage
    {
        if (!$this->storage) {
            $this->storage = new SuggestionStorage();
        }
        
        return $this->storage;
    }
    
    
    public function addSuggestion ($budgetId, $name, $amount, $date, $belongsType = null, $belongsId = null)  
    {
        $bind = [
            SuggestionModel::BUDGET_ID => $budgetId,
            SuggestionModel::NAME => $name,
            SuggestionModel::AMOUNT => $amount,
            SuggestionModel::DATE => $date,
            SuggestionModel::BELONGS_TYPE => $belongsType,
            SuggestionModel::BELONGS_ID => $belongsId
        ];
        
        return $this->getStorage()->write()->insert(Uuid::v4(), $bind, __METHOD__);    
    }
    
    public function removeSuggestion ($suggestionId) 
    {
        return $this->getStorage()->write()->remove($suggestionId, __METHOD__);
    }
    
    public function getSuggestions ($budgetId, $dateFrom, $dateTo, $limit = 1000) 
    {
        return $this->getStorage()->search()->find([
            [SuggestionModel::BUDGET_ID, Compare::EQ, $budgetId],
            [SuggestionModel::DATE, Compare::GRATER_OR_EQ, $dateFrom],
            [SuggestionModel::DATE, Compare::LESS_OR_EQ, $dateTo],
        ], $limit, __METHOD__);
    }
}