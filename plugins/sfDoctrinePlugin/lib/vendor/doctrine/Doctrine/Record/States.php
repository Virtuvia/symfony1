<?php

declare(strict_types=1);

interface Doctrine_Record_States
{
    /**
     * STATE CONSTANTS
     */

    /**
     * DIRTY STATE
     * a Doctrine_Record is in dirty state when its properties are changed
     */
    public const STATE_DIRTY       = 1;

    /**
     * TDIRTY STATE
     * a Doctrine_Record is in transient dirty state when it is created
     * and some of its fields are modified but it is NOT yet persisted into database
     */
    public const STATE_TDIRTY      = 2;

    /**
     * CLEAN STATE
     * a Doctrine_Record is in clean state when all of its properties are loaded from the database
     * and none of its properties are changed
     */
    public const STATE_CLEAN       = 3;

    /**
     * PROXY STATE
     * a Doctrine_Record is in proxy state when its properties are not fully loaded
     */
    public const STATE_PROXY       = 4;

    /**
     * NEW TCLEAN
     * a Doctrine_Record is in transient clean state when it is created and none of its fields are modified
     */
    public const STATE_TCLEAN      = 5;

    /**
     * LOCKED STATE
     * a Doctrine_Record is temporarily locked during deletes and saves
     *
     * This state is used internally to ensure that circular deletes
     * and saves will not cause infinite loops
     */
    public const STATE_LOCKED     = 6;

    /**
     * TLOCKED STATE
     * a Doctrine_Record is temporarily locked (and transient) during deletes and saves
     *
     * This state is used internally to ensure that circular deletes
     * and saves will not cause infinite loops
     */
    public const STATE_TLOCKED     = 7;
}
