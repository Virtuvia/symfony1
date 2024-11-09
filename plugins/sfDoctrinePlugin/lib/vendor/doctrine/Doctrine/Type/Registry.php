<?php

declare(strict_types=1);

class Doctrine_Type_Registry
{
    public function __construct(
        /** @var array<string, Doctrine_Type> */
        private array $types = [],
    ) {
    }

    public function registerType(string $name, Doctrine_Type $type): void
    {
        $this->types[$name] = $type;
    }

    /**
     * @throws Doctrine_Type_Exception_UnknownType
     */
    public function getType(string $name): Doctrine_Type
    {
        return $this->types[$name] ?? throw new Doctrine_Type_Exception_UnknownType(sprintf('Type "%s" not found.', $name));
    }
}
