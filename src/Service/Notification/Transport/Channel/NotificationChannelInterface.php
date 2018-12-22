<?php


namespace Service\Notification\Transport\Channel;


use Service\Notification\Model\AbstractNotification;

interface NotificationChannelInterface
{
    public function send (AbstractNotification $notification); 
}