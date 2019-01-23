<?php


namespace Service\Notification\Transport\Channel;


use Psr\Log\LoggerInterface;
use Service\Notification\Model\AbstractNotification;
use Verse\Di\Env;

class TelegramBotChannel implements NotificationChannelInterface
{
    private $_accessKey = '';
    
    private $telegramHost = 'https://api.telegram.org';
    
    public function send(AbstractNotification $notification)
    {
        $target = $notification->to;
        
//        if (!is_numeric($target)) {
//            $chatData = $this->_call('getChat', [
//                'chat_id' => $target 
//            ], $this->pwrtHost);
//            
//            if ($chatData['ok']) {
//               $target = $chatData['result']['id']; 
//            }
//        }
        
//        $this->_call('getUpdates', []);
    
        $params = [
            'chat_id' => $target,
            'text'    => $notification->body,
        ];
        
        $data = $this->_call('sendMessage', $params, $this->telegramHost);
        
        return $data ? true : false ; // ;)  
    }
    
    private function _call($method, $params, $host) {
        $website = $host.'/bot' . $this->_accessKey;
        $ch     = curl_init($website . '/'.$method);
    
        $this->_debug('Api request '.$method, [
            'api' => $website,
            'params' => $params,
        ]);
    
        curl_setopt_array($ch, [
            CURLOPT_HEADER         => false,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_POST           => 1,
            CURLOPT_POSTFIELDS     => $params,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_TIMEOUT        => 10,
        ]);
    
        $result = curl_exec($ch);
        curl_close($ch);
    
        $data = json_decode($result, true);
    
        $this->_debug('Api response', $data ?: [
            'string' => $result,
        ]);
        
        return $data;
    }
    
    private function _debug(string $message, array $data = []) : void 
    {
        /* @var $logger LoggerInterface */
        $logger = Env::getContainer()->bootstrap(LoggerInterface::class, false);
        if ($logger) {
            $logger->debug(__CLASS__.': '.$message, $data);
        }
    }
    
    /**
     * @return string
     */
    public function getAccessKey(): string
    {
        return $this->_accessKey;
    }
    
    /**
     * @param string $accessKey
     */
    public function setAccessKey(string $accessKey): void
    {
        $this->_accessKey = $accessKey;
    }
}