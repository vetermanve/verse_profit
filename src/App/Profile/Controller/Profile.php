<?php
/**
 * Created by PhpStorm.
 * User: mikhaildenisov
 * Date: 04/12/2018
 * Time: 19:55
 */

namespace App\Profile\Controller;


use Base\Controller\BasicController;
use DateTimeZone;
use Service\Auth\AuthService;
use Service\Notification\NotificationService;
use Service\Notification\NotificationTypes;
use Service\User\Model\UserModel;
use Service\User\Model\UserNicknameModel;
use Service\User\UserService;

class Profile extends BasicController
{
    public function index () {
        $userService = new UserService();
        $user = $userService->getUser($this->_userId);
        
        $nickname = [];
        if ($nicknameId = $user[UserModel::NICKNAME_ID]) {
            $nickname = $userService->getNicknameById($nicknameId);     
        }

        return $this->_render(__FUNCTION__, [
            'message' => $this->message,
            'user' => $user,
            'nickname' => $nickname,
            'timezones' => DateTimeZone::listIdentifiers(),
        ]);
    }

    public function edit () {
        $userId = $this->p('id');
        if ($userId !== $this->_userId) {
            $this->message = 'Ты пытаетешься обновить данные после авторизации под другим пользователем';
        } else {
            $name = $this->p('name');
            $email = $this->p('email');
            $timezone = $this->p('timezone');
            
            // lets check timezone
            $date = new \DateTime();
            if ($date->setTimezone(new DateTimeZone($timezone)) === false) {
                $timezone = null; 
            }
            
            $userService = new UserService();
            $result = $userService->updateUser($this->_userId, [
                 UserModel::NAME => $name,
                 UserModel::EMAIL => $email,
                 UserModel::TIMEZONE => $timezone,
            ]);

            $this->message = $result ? 'Данные успешно обновлены' : 'Не удалось обновить данные';
        }

        $this->loadUser();

        return $this->index();
    }
    
    public function setNickname () 
    {
        $userId = $this->p('id');
        if ($userId !== $this->_userId) {
            $this->message = 'Ты пытаетешься обновить данные после авторизации под другим пользователем';
        } else {
            $nicknameString = $this->p('nickname');
            $nicknameString = \strtolower(\mb_ereg_replace('[^A-Za-z0-9\.\-\_]', '', $nicknameString));

            $userService = new UserService();
            $nicknameModel = $userService->getNicknameByNickname($nicknameString);
            
            if (!$nicknameModel) {
                $nicknameModel = $userService->createUserNickname($this->_userId, $nicknameString);    
            }
            
            if ($nicknameModel && $nicknameModel[UserNicknameModel::USER_ID] === $this->_userId) {
                $userService->updateUser($this->_userId,[
                    UserModel::NICKNAME_ID => $nicknameModel[UserNicknameModel::ID]
                ]);
                $this->message = 'Никнейм устарновлен';
            } else {
                $this->message = 'Не удалось установить никнейм, возможно он уже занят';    
            }
        }

        $this->loadUser();

        return $this->index();
    }
    
    public function sendMail () 
    {
        $userId = $this->p('id');
        $email = $this->p('email');
        
        if ($userId !== $this->_userId) {
            $this->message = 'Ты пытаетешься обновить данные после авторизации под другим пользователем';
        } else {
            if (strpos($email, '@') !== false) {
                $notification = new NotificationService();
                $res = $notification->sendEmail(NotificationTypes::CHANNEL_VERIFY, $email, []);
                $this->message = $res ? 'Cообщение успешно отрпавлено!' : 'Сообщение к сожалению не отправлено';
            }
        }
        
        return $this->index();
    }

    protected function getClassDirectory()
    {
        return __DIR__;
    }
}