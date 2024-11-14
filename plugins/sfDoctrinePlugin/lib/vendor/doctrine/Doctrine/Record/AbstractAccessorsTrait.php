<?php

declare(strict_types=1);

trait Doctrine_Record_AbstractAccessorsTrait
{
    abstract public function get(string $fieldName, bool $load = true): mixed;

    abstract protected function _get($fieldName, $load = true);

    abstract protected function internalGetValue(string $fieldName): mixed;

    abstract protected function internalGetData(string $fieldName, bool $load = true): mixed;

    abstract protected function internalGetReference(string $fieldName, bool $load = true): Doctrine_Record|Doctrine_Collection|null;

    abstract protected function internalGetReferenceOrNull(string $fieldName, bool $load = true): Doctrine_Record|Doctrine_Collection|null;
}
