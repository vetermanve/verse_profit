<?php


namespace App\Relations\Controller;


use Base\Controller\BasicController;
use Service\Relations\Model\RelationModel;
use Service\Relations\RelationsService;
use Service\User\Model\UserModel;
use Service\User\Model\UserNicknameModel;
use Service\User\UserService;

class Users extends BasicController
{
    protected $message = '';
    
    public function index () 
    {
        $nicknameSearchString = \trim($this->p('nickname'));
        
        $relationsService = new RelationsService();
        $userService = new UserService();
        
        $users = 
        $usersIds =
        $relationsData =
            [];
        
        if ($this->_userId) {
            $relations = $relationsService->getRelations($this->_userId);
            if ($relations) {
                $relationsData = array_column($relations, null, RelationModel::RELATED_USER_ID);
                $usersIds = array_keys($relationsData);
            }

            if ($nicknameSearchString) {
                $nicknameModel = $userService->getNicknameByNickname($nicknameSearchString);
                if ($nicknameModel) {
                    \array_unshift($usersIds, $nicknameModel[UserNicknameModel::USER_ID]);
                }
            }

            if ($usersIds) {
                $users = $userService->getUsersWithNicknames($usersIds);
            }
        }

        return $this->_render(__FUNCTION__, [
            'message' => $this->message,
            'users' => $users,
            'relations' => $relationsData,
        ]);
    }
    
    protected function add()
    {
        $email = $this->p('email');
        $name = $this->p('name');
        
        if ($email && $name) {
            $userService = new UserService();
            $user = $userService->createUser([
                UserModel::EMAIL => $email,
                UserModel::NAME => $name,
            ]);
            
            $userId = $user[UserModel::ID] ?? null;
            if ($userId !== null) {
                $relationsService = new RelationsService();
                $relationsService->createRelation(
                    $this->_userId,
                    $userId
                );
                
                $this->message = 'Успешно добавлен!';
            }
        }
        
        return $this->index();
    }
    
    public function addFriend () 
    {
        $userId = $this->p('id');
        if ($userId) {
            $relationsService = new RelationsService();
            $relationsService->createRelation(
                $this->_userId,
                $userId
            );

            $this->message = 'Успешно добавлен!';
        }
        
        return $this->index();
    }

    public function removeFriend ()
    {
        $userId = $this->p('id');
        if ($userId) {
            $relationsService = new RelationsService();
            $relationsService->removeRelation(
                $this->_userId,
                $userId
            );

            $this->message = 'Успешно убран!';
        }

        return $this->index();
    }

    protected function getClassDirectory()
    {
        return __DIR__;
    }
}