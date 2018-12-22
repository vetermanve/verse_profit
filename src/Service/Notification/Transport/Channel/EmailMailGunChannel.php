<?php


namespace Service\Notification\Transport\Channel;


use Mailgun\Mailgun;
use Service\Notification\Model\AbstractNotification;
use Service\Notification\Model\EmailMessage;

class EmailMailGunChannel implements NotificationChannelInterface
{
    public const CONFIG_APP_KEY = 'app_key';
    public const CONFIG_DOMAIN = 'app_domain';
    
    private $config = [];
    
    private $_mailGunClient;
    
    private function getMailGunClinet (): Mailgun
    {
        if (!$this->_mailGunClient) {
            $this->_mailGunClient = Mailgun::create($this->config[self::CONFIG_APP_KEY]);  
        }
        return $this->_mailGunClient;
    }
    
    public function send (AbstractNotification $emailMessage): bool
    {
        $domain = $this->config[self::CONFIG_DOMAIN];
        
        $response =  $this->getMailGunClinet()->messages()->send($domain, [
            'from'    => $emailMessage->from,
            'to'      => $emailMessage->to,
            'subject' => $emailMessage->subject,
            'html'    => $emailMessage->body,
        ]);
        
        return \strlen($response->getMessage()) > 0;
    }
    
    /**
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }
    
    /**
     * @param array $config
     */
    public function setConfig(array $config): void
    {
        $this->config = $config;
    }
}