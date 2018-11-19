<?php

namespace Service\User;


use Service\User\Storage\UserStorage;
use Verse\Run\Util\Uuid;

class UserService
{
    /**
     * @var UserStorage
     */
    private $storage;

    public function getStorage () : UserStorage 
    {
        if (!$this->storage) {
            $this->storage = new UserStorage();
        }
        
        return $this->storage;
    }
    
    public function createUser ($data) 
    {
        $id = Uuid::v4();
        $res = $this->getStorage()->write()->insert($id, $data, __METHOD__);
        return $res;
    }
    
    public function getUser ($userId) 
    {
        return $this->getStorage()->read()->get($userId, __METHOD__, []);   
    }

    public function getUsers(array $usersIds)
    {
        return $this->getStorage()->read()->mGet($usersIds, __METHOD__);
    }
}