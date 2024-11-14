<?php

declare(strict_types=1);

trait Doctrine_Record_AbstractMutatorsTrait
{
    abstract public function set(string $fieldName, mixed $value, bool $load = true): static;

    abstract protected function _set($fieldName, $value, $load = true);
}
