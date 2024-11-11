<?php

declare(strict_types=1);

trait Doctrine_Record_TreeNodeTrait
{
    /**
     * @var null|Doctrine_Node_Interface        node object
     */
    private ?Doctrine_Node_Interface $_node;

    /**
     * getter for node associated with this record
     *
     * @TODO rename to not get*
     * @return Doctrine_Node_Interface    false if component is not a Tree
     */
    public function getNode()
    {
        if (! $this->getTable()->isTree()) {
            return false;
        }

        if (! isset($this->_node)) {
            $this->_node = new Doctrine_Node_NestedSet($this, $this->getTable()->getOption('treeOptions'));
        }

        return $this->_node;
    }

    /**
     * used to delete node from tree - MUST BE USE TO DELETE RECORD IF TABLE ACTS AS TREE
     *
     */
    public function deleteNode()
    {
        $this->getNode()->delete();
    }
}
