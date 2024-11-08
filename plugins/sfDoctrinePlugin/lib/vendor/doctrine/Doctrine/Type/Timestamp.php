<?php

declare(strict_types=1);

class Doctrine_Type_Timestamp implements Doctrine_Type
{
    public function convertToDatabaseValue(mixed $phpValue): int|null|string
    {
        if ($phpValue === null) {
            return null;
        }

        if (is_string($phpValue)) {
            return $phpValue;
        }

        if ($phpValue instanceof \DateTimeInterface) {
            return $phpValue->getTimestamp();
        }

        throw new \InvalidArgumentException();
    }

    public function convertToPHPValue(mixed $databaseValue): null|string
    {
        if ($databaseValue === null) {
            return null;
        }

        if (is_string($databaseValue)) {
            return $databaseValue;
        }

        throw new \InvalidArgumentException();
    }

    public function getPhpType(): string
    {
        return 'string';
    }
}
