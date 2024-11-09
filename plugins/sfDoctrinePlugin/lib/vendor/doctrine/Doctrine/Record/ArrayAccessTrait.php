<?php

declare(strict_types=1);

trait Doctrine_Record_ArrayAccessTrait
{
    /**
     * @deprecated
     */
    final public function offsetExists(mixed $offset): bool
    {
        return $this->contains($offset);
    }

    /**
     * @deprecated use appropriate get* or {@see Doctrine_Record::get()}
     */
    final public function offsetGet(mixed $offset): mixed
    {
        return $this->get($offset);
    }

    /**
     * @deprecated use appropriate set* or {@see Doctrine_Record::set()}
     */
    final public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->set($offset, $value);
    }

    /**
     * @deprecated
     */
    final public function offsetUnset(mixed $offset): void
    {
        $this->remove($offset);
    }
}
