<?php

declare(strict_types=1);

trait Doctrine_Record_AbstractAccessorsTrait
{
    abstract public function get(string $fieldName, bool $load = true): mixed;
}
