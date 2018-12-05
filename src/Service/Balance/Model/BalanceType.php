<?php


namespace Service\Balance\Model;


class BalanceType
{
    public const CURRENT  = 'current';
    public const EXTERNAL = 'external';
    public const DEBT     = 'debt';
    public const CREDIT   = 'credit';
    public const BLACK_HOLE  = 'black_hole';

    public static function getValues() : array
    {
        return [
            self::CURRENT  => 'Текущий',
            self::EXTERNAL => 'Внешний',
            self::DEBT     => 'Долговой',
            self::CREDIT   => 'Кредитный',
            self::BLACK_HOLE => 'Черная дыра',
        ];
    }
}