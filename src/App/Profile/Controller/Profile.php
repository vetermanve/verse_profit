<?php
/**
 * Created by PhpStorm.
 * User: mikhaildenisov
 * Date: 04/12/2018
 * Time: 19:55
 */

namespace App\Profile\Controller;


use Base\Controller\BasicController;
use Service\User\Model\UserModel;
use Service\User\UserService;

class Profile extends BasicController
{
    public function index () {
        $userService = new UserService();
        $user = $userService->getUser($this->_userId);

        return $this->_render(__FUNCTION__, [
            'message' => $this->message,
            'user' => $user,
        ]);
    }

    public function edit () {
        $userId = $this->p('id');
        if ($userId !== $this->_userId) {
            $this->message = 'Ты пытаетешься обновить данные после авторизации под другим пользователем';
        } else {
            $name = $this->p('name');
            $email = $this->p('email');

            $userService = new UserService();
            $result = $userService->updateUser($this->_userId, [
                 UserModel::NAME => $name,
                 UserModel::EMAIL => $email,
            ]);

            $this->message = $result ? 'Данные успешно обновлены' : 'Не удалось обновить данные';
        }

        $this->loadUser();

        return $this->index();
    }

    protected function getClassDirectory()
    {
        return __DIR__;
    }
}