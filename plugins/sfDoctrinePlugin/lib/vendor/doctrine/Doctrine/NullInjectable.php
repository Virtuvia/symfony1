<?php

declare(strict_types=1);

trait Doctrine_NullInjectable
{
    private static Doctrine_Null $_null;

    public static function initNullObject(Doctrine_Null $null): void
    {
        self::$_null = $null;
    }
}
