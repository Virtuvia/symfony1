<?php

declare(strict_types=1);

trait Doctrine_Record_PropertyAccessTrait
{
    /**
     * @deprecated use appropriate set* or {@see Doctrine_Record::set()}
     */
    final public function __set(string $name, mixed $value): void
    {
        $this->set($name, $value);
    }

    /**
     * @deprecated use appropriate get* or {@see Doctrine_Record::get()}
     */
    final public function __get(string $name): mixed
    {
        return $this->get($name);
    }

    /**
     * @deprecated
     */
    final public function __isset(string $name): bool
    {
        return $this->contains($name);
    }

    /**
     * @deprecated
     */
    final public function __unset(string $name): void
    {
        $this->remove($name);
    }
}
