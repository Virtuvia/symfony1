<?php

/**
 * Maintain behavior that Symfony1 expects where neeed.
 *
 * is_countable = count
 * NULL = 0
 * anything else = 1
 *
 * @param mixed $value
 * @return int
 */
function symfony1_count($value)
{
    if (is_countable($value)) {
        return count($value);
    }
    
    return $value !== null ? 1 : 0;
}