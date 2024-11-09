<?php

declare(strict_types=1);

class Doctrine_Type_IntegerType extends Doctrine_Type
{
    public function isValueModified(mixed $old, mixed $new): bool
    {
        if (is_numeric($old) && is_numeric($new)) {
            return $old != $new;
        }

        return parent::isValueModified($old, $new);
    }
}
