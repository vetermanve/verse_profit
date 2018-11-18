<?php


namespace App\Auth\Controller;


use Base\Controller\BasicController;
use Service\Auth\AuthService;
use Service\Auth\Model\KeyTypes;

class Auth extends BasicController
{
    public function index()
    {
        $email = $this->p('email');
        $password = $this->p('password');
        $isSubmitted = (bool) $this->p('submit');;

        $authService = new AuthService();
        $userId = $authService->getUserIdByPair(KeyTypes::EMAIL, $email, $password);
        $message = '';

        if ($userId) {
            $this->_userId = $userId;
            // $this->_scopeId = $pairs[$email]['scope_id'];
            // $this->_secureState->setState(self::STATE_KEY_SCOPE_ID, $this->_scopeId, $ttl);

            $this->_secureState->setState(
                self::STATE_KEY_USER_ID,
                $this->_userId,
                self::STATE_AUTHORISE_DEFAULT_TTL
            );

            $this->loadUser();
        } else if ($isSubmitted) {
            $message = 'Email или пароль введен не верно.';
        }

        return $this->_render('login', [
            'title'      => 'Авторизация',
            'authorised' => (bool) $userId,
            'email'      => $email,
            'message'    => $message,
        ]);
    }

    public function logout()
    {
        $this->_secureState->setState(self::STATE_KEY_USER_ID, null, 3600);
        $this->_secureState->setState(self::STATE_KEY_SCOPE_ID, null, 3600);

        $this->_user = self::DEFAULT_USER;
        $this->_userId = self::DEFAULT_USER_ID;
        $this->_scopeId = null;

        return $this->_render('login', [
            'title' => 'Ты успешно вышел из системы!',
        ]);
    }
    protected function getClassDirectory()
    {
        return __DIR__;
    }
}