<?php


namespace App\Auth\Controller;


use Base\Controller\BasicController;
use Service\Auth\AuthService;
use Service\Auth\Model\KeypairModel;
use Service\Auth\Model\KeyTypes;
use Service\Notification\NotificationService;
use Service\Notification\NotificationTypes;

class Auth extends BasicController
{
    public function index()
    {
        $email       = $this->p('email');
        $password    = $this->p('password');
        $isSubmitted = (bool)$this->p('submit');;
        
        $authService = new AuthService();
        $userId      = $authService->getUserIdByPair(KeyTypes::EMAIL, $email, $password);
        $message     = '';
        
        if ($userId) {
            $this->authoriseUser($userId);
        } else {
            if ($isSubmitted) {
                $message = 'Email или пароль введен не верно.';
            }
        }
        
        return $this->_render('login', [
            'title'      => 'Авторизация',
            'authorised' => (bool)$userId,
            'email'      => $email,
            'message'    => $message,
        ]);
    }
    
    public function logout()
    {
        $this->_secureState->setState(self::STATE_KEY_USER_ID, null, 3600);
        $this->_secureState->setState(self::STATE_KEY_BUDGET_ID, null, 3600);
        
        $this->_user     = self::DEFAULT_USER;
        $this->_userId   = self::DEFAULT_USER_ID;
        $this->_budgetId = null;
        
        return $this->_render('login', [
            'title' => 'Ты успешно вышел из системы!',
        ]);
    }
    
    public function reset()
    {
        $email = mb_strtolower(trim($this->p('email')));
        
        $success = false;
        $message = '';
        
        if ($email) {
            $authService = new AuthService();
            $pairModel   = $authService->getPairByTypeAndKey(KeyTypes::EMAIL, $email);
            
            if ($pairModel) {
                $pass    = bin2hex(\random_bytes(25));
                $oldPair = $authService->getPairByTypeAndKey(KeyTypes::EMAIL_CODE, $email);
                
                if ($oldPair) {
                    $authService->removePairById($oldPair[KeypairModel::ID]);
                }
                
                $newPair = $authService->addAuthPair(KeyTypes::EMAIL_CODE, $email, $pass,
                    $pairModel[KeypairModel::USER_ID]);
                if ($newPair) {
                    $notificationService = new NotificationService();
                    
                    $result = $notificationService->sendEmail(NotificationTypes::PASSWORD_RESET, $email, [
                        'key'  => $email,
                        'pass' => $pass,
                    ]);
                    
                    if ($result) {
                        $success = true;
                        $message = 'Код успешно отправлен ' . $email;
                    } else {
                        $message = 'Не удалось отправить почтовое сообщение. Обратитесь в поддрежку.';
                    }
                } else {
                    $message = 'Не удалось создать код восстановления пароля, обратитесь в поддержку.';
                }
            } else {
                $message = 'Email не найден в системе';
            }
        }
        
        return $this->_render(__FUNCTION__, [
            'email'   => $email ?? $this->p('for_email'),
            'message' => $message,
            'success' => $success,
        ]);
    }
    
    public function createPassword()
    {
        $email       = $this->p('key');
        $password    = $this->p('password');
        $newPassword = $this->p('new_password');
        
        $message = '';
        $success = false;
        
        $authService = new AuthService();
        
        $userId = $authService->getUserIdByPair(KeyTypes::EMAIL_CODE, $email, $password);
        if ($userId) {
            $this->authoriseUser($userId);
            
            if ($newPassword) {
                $oldPair = $authService->getPairByTypeAndKey(KeyTypes::EMAIL, $email);
                
                if ($oldPair) {
                    $authService->removePairById($oldPair[KeypairModel::ID]);
                }
                
                $authService->addAuthPair(KeyTypes::EMAIL, $email, $newPassword, $userId);
                $success = true;
            }
        } elseif ($email) {
            $message = 'Ссылка не действительна, попробуйте запросить восстановление пароля еще раз.';
        }
        
        return $this->_render(__FUNCTION__, [
            'message'  => $message,
            'success'  => $success,
            'password' => $password,
            'email'    => $email,
        ]);
    }
    
    public function addTelegram () 
    {
        $id = $this->p('id');
        $codeCheck = $this->p('code');
        
        // test stub
        $code = 'test';
        $botName = 'goals_test_bot';
        
        // logic
    
        if ($codeCheck) {
            if ($code === $codeCheck) {
                $this->message = 'Код введен верно, поздравляю!';
            }  else {
                $this->message = 'C кодом что-то не так, попробуй еще раз';
            }
        } else if ($id) {
            $notifications = new NotificationService();
            $res = $notifications->sendTelegramMessage(NotificationTypes::CHANNEL_VERIFY, $id, [
                'code' => $code,
            ]);
            
            if ($res) {
                $this->message = 'Сообщение отправлено';
            } else {
                $this->message = 'А ты уверен что ты уже обратился к боту?';
            }
        }
        
        return $this->_render(__FUNCTION__, [
            'message' => $this->message,
            'id' => $id,
            'botName' => $botName
        ]);
    }
    
    
    protected function getClassDirectory()
    {
        return __DIR__;
    }
}