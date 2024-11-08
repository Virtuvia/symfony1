<?php

declare(strict_types=1);

trait Doctrine_Record_ListenerTrait
{
    /**
     * Empty template method to provide concrete Record classes with the possibility
     * to hook into the saving procedure.
     */
    public function preSave(Doctrine_Event $event)
    {
    }

    /**
     * Empty template method to provide concrete Record classes with the possibility
     * to hook into the saving procedure.
     */
    public function postSave(Doctrine_Event $event)
    {
    }

    /**
     * Empty template method to provide concrete Record classes with the possibility
     * to hook into the deletion procedure.
     */
    public function preDelete(Doctrine_Event $event)
    {
    }

    /**
     * Empty template method to provide concrete Record classes with the possibility
     * to hook into the deletion procedure.
     */
    public function postDelete(Doctrine_Event $event)
    {
    }

    /**
     * Empty template method to provide concrete Record classes with the possibility
     * to hook into the saving procedure only when the record is going to be
     * updated.
     */
    public function preUpdate(Doctrine_Event $event)
    {
    }

    /**
     * Empty template method to provide concrete Record classes with the possibility
     * to hook into the saving procedure only when the record is going to be
     * updated.
     */
    public function postUpdate(Doctrine_Event $event)
    {
    }

    /**
     * Empty template method to provide concrete Record classes with the possibility
     * to hook into the saving procedure only when the record is going to be
     * inserted into the data store the first time.
     */
    public function preInsert(Doctrine_Event $event)
    {
    }

    /**
     * Empty template method to provide concrete Record classes with the possibility
     * to hook into the saving procedure only when the record is going to be
     * inserted into the data store the first time.
     */
    public function postInsert(Doctrine_Event $event)
    {
    }

    /**
     * Empty template method to provide concrete Record classes with the possibility
     * to hook into the validation procedure. Useful for cleaning up data before
     * validating it.
     */
    public function preValidate(Doctrine_Event $event)
    {
    }
    /**
     * Empty template method to provide concrete Record classes with the possibility
     * to hook into the validation procedure.
     */
    public function postValidate(Doctrine_Event $event)
    {
    }

    /**
     * Empty template method to provide Record classes with the ability to alter DQL select
     * queries at runtime
     */
    public function preDqlSelect(Doctrine_Event $event)
    {
    }

    /**
     * Empty template method to provide Record classes with the ability to alter DQL update
     * queries at runtime
     */
    public function preDqlUpdate(Doctrine_Event $event)
    {
    }

    /**
     * Empty template method to provide Record classes with the ability to alter DQL delete
     * queries at runtime
     */
    public function preDqlDelete(Doctrine_Event $event)
    {
    }

    /**
     * Empty template method to provide Record classes with the ability to alter hydration
     * before it runs
     */
    public function preHydrate(Doctrine_Event $event)
    {
    }

    /**
     * Empty template method to provide Record classes with the ability to alter hydration
     * after it runs
     */
    public function postHydrate(Doctrine_Event $event)
    {
    }
}
