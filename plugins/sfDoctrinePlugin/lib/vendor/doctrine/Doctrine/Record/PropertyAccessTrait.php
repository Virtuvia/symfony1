<?php

declare(strict_types=1);

trait Doctrine_Record_PropertyAccessTrait
{
    final public function __set(string $name, mixed $value): void
    {
        $this->set($name, $value);
    }

    final public function __get(string $name): mixed
    {
        return $this->get($name);
    }

    final public function __isset(string $name): bool
    {
        return $this->contains($name);
    }

    final public function __unset(string $name): void
    {
        $this->remove($name);
    }
}
