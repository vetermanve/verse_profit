<?php

namespace Service\User;


use Service\User\Model\UserModel;
use Service\User\Model\UserNicknameModel;
use Service\User\Storage\UserNicknameStorage;
use Service\User\Storage\UserStorage;
use Verse\Run\Util\Uuid;
use Verse\Storage\Spec\Compare;

class UserService
{
    /**
     * @var UserStorage
     */
    private $userStorage;

    /**
     * @return UserStorage
     */
    private function getUserProfileStorage () : UserStorage 
    {
        if (!$this->userStorage) {
            $this->userStorage = new UserStorage();
        }
        
        return $this->userStorage;
    }

    /**
     * @return UserNicknameStorage
     */
    private function getUserNicknameStorage () : UserNicknameStorage
    {
        return new UserNicknameStorage();
    }
    
    public function createUser ($data) 
    {
        $id = Uuid::v4();
        return $this->getUserProfileStorage()->write()->insert($id, $data, __METHOD__);
    }
    
    public function getUser ($userId) 
    {
        return $this->getUserProfileStorage()->read()->get($userId, __METHOD__, []);   
    }

    public function updateUser ($userId, $bind) {
        return $this->getUserProfileStorage()->write()->update($userId, $bind, __METHOD__);
    }

    public function getUsers(array $usersIds)
    {
        return $this->getUserProfileStorage()->read()->mGet($usersIds, __METHOD__);
    }

    public function getUsersWithNicknames(array $usersIds)
    {
        $users = $this->getUserProfileStorage()->read()->mGet($usersIds, __METHOD__);
        $nicknameIds = array_column($users, UserModel::NICKNAME_ID);
        $nicknames = $this->getUserNicknameStorage()->read()->mGet($nicknameIds, __METHOD__);
        foreach ($users as &$user) {
            if (isset($user[UserModel::NICKNAME_ID], $nicknames[$user[UserModel::NICKNAME_ID]])) {
                $user[UserModel::VIRTUAL_NICKNAME] = $nicknames[$user[UserModel::NICKNAME_ID]]; 
            }       
        } unset($user);
        
        return $users;
    }
    
    public function createUserNickname ($userId, $nickname) 
    {
        $id = $this->_getNicknameId($nickname);
        $bind = [   
            UserNicknameModel::ID => $id,
            UserNicknameModel::USER_ID => $userId,
            UserNicknameModel::NICKNAME => $nickname
        ];
        
        return $this->getUserNicknameStorage()->write()->insert($id, $bind, __METHOD__);
    }
    
    public function getUserNicknames ($userId) 
    {
        return $this->getUserNicknameStorage()->search()->find([
            [UserNicknameModel::USER_ID, Compare::EQ, $userId] 
        ], 100, __METHOD__);
    }
    
    public function getNicknameById ($nicknameId) 
    {
        return $this->getUserNicknameStorage()->read()->get($nicknameId, __METHOD__);
    }
    
    public function getNicknameByNickname ($nickname) 
    {
        $nicknameId = $this->_getNicknameId($nickname);
        return $this->getUserNicknameStorage()->read()->get($nicknameId, __METHOD__);
    }
    
    public function getUserByNickname ($nickname) 
    {
        $nicknameId = $this->_getNicknameId($nickname);
        $nickname = $this->getUserNicknameStorage()->read()->get($nicknameId, __METHOD__);
        if ($nickname) {
            $userId = $nickname[UserNicknameModel::USER_ID];
            return $this->getUserProfileStorage()->read()->get($userId, __METHOD__);
        }
        
        return null;
    }
    
    private function _getNicknameId($nickname) {
        return \crc32($nickname);
    }
}