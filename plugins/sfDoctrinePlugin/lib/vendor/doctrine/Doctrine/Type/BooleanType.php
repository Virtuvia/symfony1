<?php

declare(strict_types=1);

class Doctrine_Type_BooleanType extends Doctrine_Type
{
    public function convertToDatabaseValue(mixed $phpValue): ?int
    {
        if ($phpValue === null) {
            return null;
        }

        if ($phpValue === true || $phpValue === 1) {
            return 1;
        }

        if ($phpValue === false || $phpValue === 0) {
            return 0;
        }

        throw new Doctrine_Type_Exception_ConversionFailed(sprintf('Expected true, false, "0", or "1" but got "%s"', get_debug_type($phpValue)));
    }

    public function convertToPhpValue(mixed $databaseValue): ?bool
    {
        if ($databaseValue === null) {
            return null;
        }

        return (bool) $databaseValue;
    }

    public function isValueModified(mixed $old, mixed $new): bool
    {
        if ((is_bool($old) || is_numeric($old)) && (is_bool($new) || is_numeric($new)) && $old == $new) {
            return false;
        }

        return parent::isValueModified($old, $new);
    }

    public function getPhpType(): string
    {
        return 'bool';
    }
}
