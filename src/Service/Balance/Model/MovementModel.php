<?php


namespace Service\Balance\Model;


class MovementModel
{
    /**
     * Transaction movement id
     */
    public const ID = 'id';

    /**
     * Related balance id
     */
    public const BALANCE_ID = 'balance_id';

    /**
     * Movement amount
     */
    public const AMOUNT = 'amount';

    /**
     * Related transaction id
     */
    public const TRANSACTION_ID = 'transaction_id';

    /**
     * Processing date
     */
    public const DATE = 'date';

}