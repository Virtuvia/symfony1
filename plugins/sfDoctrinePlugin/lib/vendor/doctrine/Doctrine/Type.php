<?php

declare(strict_types=1);

interface Doctrine_Type
{
    public function convertToDatabaseValue(mixed $phpValue): mixed;

    public function convertToPHPValue(mixed $databaseValue): mixed;

    public function getPhpType(): string;
}
