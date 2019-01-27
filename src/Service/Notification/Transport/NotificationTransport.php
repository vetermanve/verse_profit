<?php


namespace Service\Notification\Transport;


use Service\Notification\Model\AbstractNotification;
use Service\Notification\Transport\Channel\EmailMailGunChannel;
use Service\Notification\Transport\Channel\NotificationChannelInterface;
use Service\Notification\Transport\Channel\TelegramBotChannel;

class NotificationTransport
{
    private $config = [];
    
    public function sendEmailNotification (AbstractNotification $notification): bool
    {
        $channel = $this->_getEmailChannel();
        return $channel->send($notification);
    }
    
    public function sendTelegramNotofication (AbstractNotification $notification) : bool 
    {
        $channel = $this->_getTelegramChannel();
        return $channel->send($notification);
    }
    
    private function _getEmailChannel() : NotificationChannelInterface
    {
        $config = [
            EmailMailGunChannel::CONFIG_DOMAIN => $this->config['domain'],
            EmailMailGunChannel::CONFIG_APP_KEY => $this->config['mailgun']['app_key'],
        ];
        
        $channel = new EmailMailGunChannel();
        $channel->setConfig($config);
        
        return $channel;
    }
    
    private function _getTelegramChannel() : NotificationChannelInterface
    {
        $channel = new TelegramBotChannel();
        $channel->setAccessKey($this->config['telegram']['app_key']);
        
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