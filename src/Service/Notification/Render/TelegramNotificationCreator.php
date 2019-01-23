<?php


namespace Service\Notification\Render;


use Base\Render\RendererInterface;
use Base\Render\Twig\TwigRenderer;
use Service\Notification\Model\AbstractNotification;
use Service\Notification\Model\TelegramMessage;

class TelegramNotificationCreator extends AbstractNotificationCreator
{
    public function create(string $type, array $params = []): AbstractNotification
    {
        // getting renderer
        $renderer = $this->_getRenderer();
    
        // processing render
        $notification = new TelegramMessage();
        $notification->body = $renderer->render($type, $params, '_layout',[
            __DIR__ . '/Template/Telegram'
        ]);
        
        return $notification;
    }
    
    private function _getRenderer() : RendererInterface {
        return new TwigRenderer();
    }
}