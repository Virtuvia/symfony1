<?php

declare(strict_types=1);

class Doctrine_Type_TimestampType extends Doctrine_Type
{
    public const MYSQL_DATE_TIME_FORMAT = 'Y-m-d H:i:s';

    public function convertToDatabaseValue(mixed $phpValue): ?string
    {
        if ($phpValue === null) {
            return null;
        }

        if ($phpValue instanceof \DateTimeInterface) {
            return $phpValue->format(self::MYSQL_DATE_TIME_FORMAT);
        }

        return $phpValue;
    }

    public function getPhpType(): string
    {
        return 'string';
    }
}
