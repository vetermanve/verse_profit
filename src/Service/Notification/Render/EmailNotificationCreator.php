<?php


namespace Service\Notification\Render;


use Base\Render\RendererInterface;
use Base\Render\Twig\TwigRenderer;
use Service\Notification\Model\AbstractNotification;
use Service\Notification\Model\EmailMessage;

class EmailNotificationCreator extends AbstractNotificationCreator
{
    private $domain = '';
    
    public function create(string $type, array $params = []): AbstractNotification
    {
        // getting renderer
        $renderer = $this->_getRenderer();
        
        // bind default params
        $params = [
            '_domain' => $this->domain,
        ] + $params;
        
        // processing render
        $notification = new EmailMessage();
        $notification->body = $renderer->render($type, $params, '_layout',[
            __DIR__.'/Template/Email'
        ]);
        $notification->from = 'info@'.$this->domain;
        
        $notification->subject = "Info";
        
        return $notification;
    }
    
    private function _getRenderer() : RendererInterface {
        return new TwigRenderer();
    }
    
    /**
     * @param string $domain
     */
    public function setDomain(string $domain): void
    {
        $this->domain = $domain;
    }
}