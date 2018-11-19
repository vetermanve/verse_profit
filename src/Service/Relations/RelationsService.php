<?php


namespace Service\Relations;


use Service\Relations\Model\RelationModel;
use Service\Relations\Storage\RelationsStorage;
use Verse\Run\Util\Uuid;
use Verse\Storage\Spec\Compare;

class RelationsService
{
    /**
     * @return RelationsStorage
     */
    public function getStorage () : RelationsStorage
    {
        return new RelationsStorage();
    }
    
    public function getRelations(string $userId)
    {
        return $this->getStorage()->search()->find([
            [RelationModel::OWNER_USER_ID, Compare::EQ, $userId] 
        ], 500, __METHOD__);
    }

    public function createRelation(string $ownerUserId, $userId)
    {
        $bind = [
            RelationModel::OWNER_USER_ID => $ownerUserId,
            RelationModel::RELATED_USER_ID => $userId
        ];
        
        return $this->getStorage()->write()->insert(Uuid::v4(), $bind, __METHOD__);
    }
}