<?php


namespace App\Relations\Controller;


use Base\Controller\BasicController;
use Service\Relations\Model\RelationModel;
use Service\Relations\RelationsService;
use Service\User\Model\UserModel;
use Service\User\UserService;

class Users extends BasicController
{
    protected $message = '';
    
    public function index () 
    {
        $relationsService = new RelationsService();
        
        $users = [];
        if ($this->_userId) {
            $relations = $relationsService->getRelations($this->_userId);
            if ($relations) {
                $usersIds = array_column($relations, RelationModel::RELATED_USER_ID);
                $userService = new UserService();
                $users = $userService->getUsers($usersIds);
            }
        }
        
        return $this->_render(__FUNCTION__, [
            'message' => $this->message,
            'users' => $users,
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

    protected function getClassDirectory()
    {
        return __DIR__;
    }
}