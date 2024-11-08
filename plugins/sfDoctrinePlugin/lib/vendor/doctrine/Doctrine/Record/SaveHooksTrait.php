<?php

declare(strict_types=1);

trait Doctrine_Record_SaveHooksTrait
{
    /**
     * Array containing the save hooks and events that have been invoked
     *
     * @var array
     */
    private $_invokedSaveHooks = false;

    /**
     * calls a subclass hook. Idempotent until @see clearInvokedSaveHooks() is called.
     *
     * <code>
     * $this->invokeSaveHooks('pre', 'save');
     * </code>
     *
     * @param string $when           'post' or 'pre'
     * @param string $type           save, delete, update, insert, validate, dqlSelect, dqlDelete, hydrate
     * @param Doctrine_Event $event  event raised
     * @return Doctrine_Event        the event generated using the type, if not specified
     */
    public function invokeSaveHooks($when, $type, $event = null)
    {
        $func = $when . ucfirst($type);

        if (is_null($event)) {
            $constant = constant('Doctrine_Event::RECORD_' . strtoupper($type));
            $event = new Doctrine_Event($this, $constant);
        }

        if ($this->_invokedSaveHooks === false) {
            $this->_invokedSaveHooks = [];
        }

        if (! isset($this->_invokedSaveHooks[$func])) {
            $this->$func($event);
            $this->getTable()->getRecordListener()->$func($event);

            $this->_invokedSaveHooks[$func] = $event;
        } else {
            $event = $this->_invokedSaveHooks[$func];
        }

        return $event;
    }

    /**
     * makes all the already used save hooks available again
     */
    public function clearInvokedSaveHooks()
    {
        $this->_invokedSaveHooks = [];
    }
}
