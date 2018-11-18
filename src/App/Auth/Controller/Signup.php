<?php


namespace App\Auth\Controller;


use Base\Controller\BasicController;
use Service\User\Model\UserModel;
use Service\User\UserService;

class Signup extends BasicController
{
    public function index()
    {
        $isSubmit = (bool) $this->p('submit');
        $isAuthorised = $this->_userId && $this->_userId !== '';

        $message = '';

        if ($isSubmit && !$isAuthorised) {
            $name = $this->p('name');
            $email = $this->p('email');
            $pass = $this->p('password');
            if ($name && $email && $pass) {
                $userApi = new UserService();
                $user = $userApi->createUser([
                    UserModel::NAME  => $name,
                    UserModel::EMAIL => $email,
                ]);

                if ($user && isset($user[UserModel::ID])) {
                    $userId = $user[UserModel::ID];
                    $this->_userId = $userId;
                    $this->_secureState->setState(self::STATE_KEY_USER_ID, $this->_userId, $ttl);
                    $message = 'Вы успено зарегистированы';
                } else {
                    $message = 'Не удалось создать пользователя.';
                }
            } else {
                $message = 'Имя пользователя или пароль не заданы!';
            }
        }

        return $this->_render(__FUNCTION__, [
            'message'      => $message,
            'isAuthorised' => $isAuthorised,
        ]);
    }

    private function _createUser($data)
    {

    }

    protected function getClassDirectory()
    {
        return __DIR__;
    }
}