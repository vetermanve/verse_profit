<?php


namespace Service\Balance\Model;


class BalanceType
{
    public const CURRENT  = 'current';
    public const EXTERNAL = 'external';

    public static function getValues() : array
    {
        return [
            self::CURRENT  => 'Текущий',
            self::EXTERNAL => 'Внешний',
        ];
    }
}