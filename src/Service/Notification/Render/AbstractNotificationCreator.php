<?php


namespace Service\Notification\Render;


use Service\Notification\Model\AbstractNotification;

abstract class AbstractNotificationCreator
{
    abstract public function create(string $type, array $params = []) : AbstractNotification;
}