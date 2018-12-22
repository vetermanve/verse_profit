<?php


namespace Service\Notification\Model;


abstract class AbstractNotification
{
    public $from;
    public $to;
    public $subject;
    public $body;
}