<?php

declare(strict_types=1);

class Doctrine_Type_DecimalType extends Doctrine_Type
{
    public function isValueModified(mixed $old, mixed $new): bool
    {
        if (is_numeric($old) && is_numeric($new)) {
            return $old * 100 != $new * 100;
        }

        return parent::isValueModified($old, $new);
    }
}
