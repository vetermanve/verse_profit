<?php


namespace Service\Balance\Model;


class BalanceModel
{
    /**
     * Balance Id
     */
    public const ID            = 'id';

    /**
     * Balance name
     */
    public const NAME          = 'name';

    /**
     * Budget id balance relates to 
     */
    public const BUDGET_ID = 'budget_id';

    /**
     * Balance type
     * @see \Service\Balance\Model\BalanceType
     */
    public const TYPE  = 'type'; // ?

    /**
     * Balance currency amount
     */
    public const AMOUNT = 'amount';
    
    // const BALANCE_TYPE = 'balance_type'; // ?
    // public const NAME_OFFICIAL = 'name_official';
}