<?php


namespace Service\Notification;


use Psr\Log\LoggerAwareTrait;
use Service\Notification\Render\EmailNotificationCreator;
use Service\Notification\Render\Exception\RenderException;
use Service\Notification\Transport\NotificationTransport;
use Verse\Di\Env;
use Verse\Modular\ModularContextProto;
use Verse\Run\RunContext;

class NotificationService
{
    use LoggerAwareTrait;
    
    private $_domain = 'goals.vetermanve.com';
    
    /**
     * @var ModularContextProto 
     */
    private $_context;
    
    /**
     * NotificationService constructor.
     *
     * @param ModularContextProto $context
     */
    public function __construct(ModularContextProto $context = null)
    {
        $this->_context = $context ?? Env::getContainer()->bootstrap(RunContext::class);
    }
    
    public function sendEmail($type, $to, $params) : bool 
    {
        $notification = null;
        
        $renderer = new EmailNotificationCreator();
        $renderer->setDomain($this->getAppDomain());
        
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
    
    public function getAppDomain () 
    {
        return $this->_context->getScope('env', 'APP_DOMAIN', $this->_domain);
    }
    
    private function _getTransportConfig () {
        return [
            'domain' => $this->_context->getScope('env', 'MAILGUN_DOMAIN', $this->_domain),
            'mailgun' => [
                'app_key' => $this->_context->getScope('env', 'MAILGUN_APP_KEY')
            ]
        ];
    }
}