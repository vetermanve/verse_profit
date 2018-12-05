<?php


namespace Service\Balance\Model;


class TransactionModel
{
    public const ID          = 'id';
    public const TYPE        = 'type';
    public const STATUS      = 'status';
    public const DESCRIPTION = 'description';
    public const AMOUNT      = 'amount';

    public const BELONGS_TYPE = 'belongs_type';
    public const BELONGS_ID   = 'belongs_id';

    public const BALANCE_FROM = 'balance_from';
    public const BALANCE_TO   = 'balance_to';

    public const SEND_CONFIRMED    = 'send_confirmed';
    public const SEND_DOCUMENT     = 'send_document';
    public const SEND_DATE         = 'send_date';
    
    public const RECEIVE_CONFIRMED = 'receive_confirmed';
    public const RECEIVE_DOCUMENT  = 'receive_document';
    public const RECEIVE_DATE      = 'receive_date';

    public const SUGGESTION_ID = 'suggestion_id';
    
    public const CREATED_DATE = 'created_date';
}