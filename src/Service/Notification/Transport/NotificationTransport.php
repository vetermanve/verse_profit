<?php


namespace Service\Notification\Transport;


use Service\Notification\Model\AbstractNotification;
use Service\Notification\Transport\Channel\EmailMailGunChannel;

class NotificationTransport
{
    private $config = [];
    
    public function sendEmailNotification (AbstractNotification $notification): bool
    {
        $channel = $this->_getEmailChannel();
        return $channel->send($notification);
    }
    
    private function _getEmailChannel() {
        $config = [
            EmailMailGunChannel::CONFIG_DOMAIN => $this->config['domain'],
            EmailMailGunChannel::CONFIG_APP_KEY => $this->config['mailgun']['app_key'],
        ];
        
        $channel = new EmailMailGunChannel();
        $channel->setConfig($config);
        
        return $channel;
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