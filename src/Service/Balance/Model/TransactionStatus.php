<?php


namespace Service\Balance\Model;


class TransactionStatus
{
    public const CREATED   = 'created';
    public const STARTED   = 'started';
    public const MONEY_OUT = 'money_out';
    public const MONEY_IN  = 'money_in';
    public const FINISHED  = 'finished';
}