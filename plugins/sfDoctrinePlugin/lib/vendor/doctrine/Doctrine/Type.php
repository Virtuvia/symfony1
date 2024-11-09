<?php

declare(strict_types=1);

abstract class Doctrine_Type implements Doctrine_Type_Interface
{
    public function convertToPhpValue(mixed $databaseValue): mixed
    {
        if ($databaseValue === null) {
            return null;
        }

        return $databaseValue;
    }

    public function convertToDatabaseValue(mixed $phpValue): mixed
    {
        if ($phpValue === null) {
            return null;
        }

        return $phpValue;
    }

    public function isValueModified(mixed $old, mixed $new): bool
    {
        return $old !== $new;
    }

    public function getPhpType(): string
    {
        return 'string';
    }
}
