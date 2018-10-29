<?php

namespace App\Landing\Controller;

use Base\Controller\BasicController;
use Verse\Run\Util\Uuid;

class Landing extends BasicController
{
    public function index()
    {
        return $this->_render('login', [
            'title'      => 'Цели и средства в твоем распоряжении!',
            'authorised' => (bool) $this->_userId,
            'statsId'    => $this->_scopeId,
        ]);
    }

    public function login()
    {
        $login = $this->p('login');
        $password = $this->p('password');

        $pairs = [
            'demo' => [
                'pass'     => 'demo',
                'id'       => Uuid::v4(),
                'scope_id' => Uuid::v4(),
            ],
        ];

        $authorised = false;
        if (isset($pairs[$login]) && $pairs[$login]['pass'] === $password) {
            $ttl = 3600 * 24;
            $this->_userId = $pairs[$login]['id'];
            $this->_scopeId = $pairs[$login]['scope_id'];

            $this->_secureState->setState(self::STATE_KEY_USER_ID, $this->_userId, $ttl);
            $this->_secureState->setState(self::STATE_KEY_SCOPE_ID, $this->_scopeId, $ttl);
            
            $authorised = true;
        }

        return $this->_render('login', [
            'title'      => 'Авторизация',
            'authorised' => $authorised,
            'login'      => $login,
        ]);
    }

    public function logout()
    {
        $this->_secureState->setState(self::STATE_KEY_USER_ID, null, 3600);
        $this->_secureState->setState(self::STATE_KEY_SCOPE_ID, null, 3600);

        return $this->_render('login', [
            'title' => 'Ты успешно вышел из системы!',
        ]);
    }

    public function signup()
    {
        return $this->_render(__FUNCTION__, [
            'title' => 'Contacts Page',
        ]);
    }

    public function contacts()
    {
        return $this->_render(__FUNCTION__, [
            'title' => 'Contacts Page',
        ]);
    }

    protected function getClassDirectory()
    {
        return __DIR__;
    }
}