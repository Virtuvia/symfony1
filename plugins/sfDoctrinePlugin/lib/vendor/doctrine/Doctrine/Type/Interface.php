<?php

declare(strict_types=1);

interface Doctrine_Type_Interface
{
    /**
     * @throws Doctrine_Type_Exception_ConversionFailed
     */
    public function convertToDatabaseValue(mixed $phpValue): mixed;

    /**
     * @throws Doctrine_Type_Exception_ConversionFailed
     */
    public function convertToPhpValue(mixed $databaseValue): mixed;

    public function isValueModified(mixed $old, mixed $new): bool;

    public function getPhpType(): string;
}
