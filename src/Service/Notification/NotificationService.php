<?php


namespace Service\Notification;


use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerTrait;
use Psr\Log\LogLevel;
use Service\Notification\Model\EmailMessage;
use Service\Notification\Render\EmailNotificationCreator;
use Service\Notification\Render\Exception\RenderException;
use Service\Notification\Transport\NotificationTransport;
use Verse\Di\Env;
use Verse\Run\RunContext;

class NotificationService
{
    use LoggerAwareTrait;
    
    private $_domain = 'goals.vetermanve.com';
    
    public function sendEmail($type, $to, $params) : bool 
    {
        $notification = null;
        
        $renderer = new EmailNotificationCreator();
        $renderer->setDomain($this->_domain);
        
        // create notification
        try {
            $notification = $renderer->create($type, $params);
        } catch (RenderException $exception) {
            $this->_log('Could not render email notification', [
                'type' => $type,
                'to'   => $to,
                'e'    => $exception,
            ]);
        }
        
        // set receiver
        if ($notification !== null) {
            $notification->to = $to;
        } else {
            return false;
        }
        
        // drop it to transport
        $transport = new NotificationTransport();
        $transport->setConfig($this->_getTransportConfig());
        
        return $transport->sendEmailNotification($notification);
    }
    
    private function _log($message, $context = [])
    {
        if ($this->logger) {
            $this->logger->warning($message, $context);
        }
    }
    
    private function _getTransportConfig () {
        /* @var $context RunContext */
        $context = Env::getContainer()->bootstrap(RunContext::class);
        
        return [
            'domain' => $this->_domain,
            'mailgun' => [
                'app_key' => $context->getScope('env', 'MAILGUN_APP_KEY')
            ]
        ];
    }
}