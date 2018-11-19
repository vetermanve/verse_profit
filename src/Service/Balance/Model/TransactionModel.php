<?php


namespace Service\Balance\Model;


class TransactionModel
{
    const ID          = 'id';
    const TYPE        = 'type';
    const STATUS      = 'status';
    const DESCRIPTION = 'description';
    const AMOUNT      = 'amount';

    const BELONGS_TYPE = 'belongs_type';
    const BELONGS_ID   = 'belongs_id';

    const BALANCE_FROM = 'balance_from';
    const BALANCE_TO   = 'balance_to';

    const SEND_CONFIRMED    = 'send_confirmed';
    const SEND_DOCUMENT     = 'send_document';
    const SEND_DATE         = 'send_date';
    
    const RECEIVE_CONFIRMED = 'receive_confirmed';
    const RECEIVE_DOCUMENT  = 'receive_document';
    const RECEIVE_DATE      = 'receive_date';

    const SUGGESTION_ID = 'suggestion_id';
    
    const CREATED_DATE = 'created_date';
}