<?php

declare(strict_types=1);

trait Doctrine_Record_ArrayAccessTrait
{
    final public function offsetExists(mixed $offset): bool
    {
        return $this->contains($offset);
    }

    final public function offsetGet(mixed $offset): mixed
    {
        return $this->get($offset);
    }

    final public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->set($offset, $value);
    }

    final public function offsetUnset(mixed $offset): void
    {
        $this->remove($offset);
    }
}
