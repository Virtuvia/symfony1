<?php
/*
 *  $Id: Collection.php 7686 2010-08-24 16:54:40Z jwage $
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the LGPL. For more information, see
 * <http://www.doctrine-project.org>.
 */

/**
 * Doctrine_Collection
 * Collection of Doctrine_Record objects.
 *
 * @package     Doctrine
 * @subpackage  Collection
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link        www.doctrine-project.org
 * @since       1.0
 * @version     $Revision: 7686 $
 * @author      Konsta Vesterinen <kvesteri@cc.hut.fi>
 */
final class Doctrine_Collection extends Doctrine_Access implements Countable, IteratorAggregate
{
    /**
     * @var array $data                     an array containing the records of this collection
     */
    protected $data = [];

    /**
     * @var Doctrine_Table $table           each collection has only records of specified table
     */
    protected $_table;

    /**
     * @var array $_snapshot                a snapshot of the fetched data
     */
    protected $_snapshot = [];

    /**
     * @var Doctrine_Record $reference      collection can belong to a record
     */
    protected $reference;

    /**
     * @var string $referenceField         the reference field of the collection
     */
    protected $referenceField;

    /**
     * @var Doctrine_Relation               the record this collection is related to, if any
     */
    protected $relation;

    /**
     * @var string $keyColumn               the name of the column that is used for collection key mapping
     */
    protected $keyColumn;

    /**
     * constructor
     *
     * @param Doctrine_Table|string $table
     */
    public function __construct($table, $keyColumn = null)
    {
        if (! ($table instanceof Doctrine_Table)) {
            $table = Doctrine_Core::getTable($table);
        }

        $this->_table = $table;

        if ($keyColumn === null) {
            $keyColumn = $table->getBoundQueryPart('indexBy');
        }

        if ($keyColumn === null) {
            $keyColumn = $table->getAttribute(Doctrine_Core::ATTR_COLL_KEY);
        }

        if ($keyColumn !== null) {
            $this->keyColumn = $keyColumn;
        }
    }

    public static function create($table, $keyColumn = null, $class = null)
    {
        if (is_null($class)) {
            if (! $table instanceof Doctrine_Table) {
                $table = Doctrine_Core::getTable($table);
            }
            $class = $table->getAttribute(Doctrine_Core::ATTR_COLLECTION_CLASS);
        }

        return new $class($table, $keyColumn);
    }

    /**
     * Get the table this collection belongs to
     *
     * @return Doctrine_Table
     */
    public function getTable()
    {
        return $this->_table;
    }

    /**
     * Set the data for the Doctrin_Collection instance
     *
     * @param array $data
     * @return Doctrine_Collection
     */
    public function setData(array $data)
    {
        $this->data = $data;
    }

    public function __serialize(): array
    {
        throw new \LogicException('serialize is not supported for ' . self::class);
    }

    public function __unserialize(array $data): void
    {
        throw new \LogicException('unserialize is not supported for ' . self::class);
    }

    /**
     * Sets the key column for this collection
     *
     * @param string $column
     * @return Doctrine_Collection $this
     */
    public function setKeyColumn($column)
    {
        $this->keyColumn = $column;

        return $this;
    }

    /**
     * Get the name of the key column
     *
     * @return string
     */
    public function getKeyColumn()
    {
        return $this->keyColumn;
    }

    /**
     * Get all the records as an array
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Get the first record in the collection
     *
     * @return Doctrine_Record
     */
    public function getFirst()
    {
        return reset($this->data);
    }

    /**
     * Get the last record in the collection
     *
     * @return Doctrine_Record
     */
    public function getLast()
    {
        return end($this->data);
    }

    /**
     * Get the last record in the collection
     *
     * @return Doctrine_Record
     */
    public function end()
    {
        return end($this->data);
    }

    /**
     * Get the current key
     *
     * @return Doctrine_Record
     */
    public function key()
    {
        return key($this->data);
    }

    /**
     * Sets a reference pointer
     *
     * @return void
     */
    public function setReference(Doctrine_Record $record, Doctrine_Relation $relation)
    {
        $this->reference = $record;
        $this->relation  = $relation;

        if ($relation instanceof Doctrine_Relation_ForeignKey ||
                $relation instanceof Doctrine_Relation_LocalKey) {
            $this->referenceField = $relation->getForeignFieldName();

            $value = $record->get($relation->getLocalFieldName());

            foreach ($this->data as $record) {
                if ($value !== null) {
                    $record->set($this->referenceField, $value, false);
                } else {
                    $record->set($this->referenceField, $this->reference, false);
                }
            }
        } elseif ($relation instanceof Doctrine_Relation_Association) {

        }
    }

    /**
     * Get reference to Doctrine_Record instance
     *
     * @return Doctrine_Record $reference
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * Removes a specified collection element
     *
     * @param mixed $key
     * @return bool
     */
    public function remove($key)
    {
        $removed = $this->data[$key];

        unset($this->data[$key]);
        return $removed;
    }

    /**
     * Whether or not this collection contains a specified element
     *
     * @param mixed $key                    the key of the element
     * @return bool
     */
    public function contains($key)
    {
        return isset($this->data[$key]);
    }

    /**
     * Search a Doctrine_Record instance
     *
     * @param Doctrine_Record $record
     * @return false|int|string
     */
    public function search(Doctrine_Record $record)
    {
        return array_search($record, $this->data, true);
    }

    /**
     * Gets a record for given key
     *
     * There are two special cases:
     *
     * 1. if null is given as a key a new record is created and attached
     * at the end of the collection
     *
     * 2. if given key does not exist, then a new record is create and attached
     * to the given key
     *
     * Collection also maps referential information to newly created records
     *
     * @param mixed $key                    the key of the element
     * @return Doctrine_Record              return a specified record
     */
    public function get($key)
    {
        if (! isset($this->data[$key])) {
            $record = $this->_table->create();

            if (isset($this->referenceField)) {
                $value = $this->reference->get($this->relation->getLocalFieldName());

                if ($value !== null) {
                    $record->set($this->referenceField, $value, false);
                } else {
                    $record->set($this->referenceField, $this->reference, false);
                }
            }
            if ($key === null) {
                $this->data[] = $record;
            } else {
                $this->data[$key] = $record;
            }

            if (isset($this->keyColumn)) {
                $record->set($this->keyColumn, $key);
            }

            return $record;
        }

        return $this->data[$key];
    }

    /**
     * Get array of primary keys for all the records in the collection
     *
     * @return array                an array containing all primary keys
     */
    public function getPrimaryKeys()
    {
        $list = [];
        $name = $this->_table->getIdentifier();

        foreach ($this->data as $record) {
            if (is_array($record) && isset($record[$name])) {
                $list[] = $record[$name];
            } else {
                $list[] = $record->getIncremented();
            }
        }
        return $list;
    }

    /**
     * Get all keys of the data in the collection
     *
     * @return array
     */
    public function getKeys()
    {
        return array_keys($this->data);
    }

    /**
     * Gets the number of records in this collection
     * This class implements interface countable
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->data);
    }

    /**
     * Set a Doctrine_Record instance to the collection
     *
     * @param int $key
     * @param Doctrine_Record $record
     * @return void
     */
    public function set($key, $record)
    {
        if (isset($this->referenceField)) {
            $record->set($this->referenceField, $this->reference, false);
        }

        $this->data[$key] = $record;
    }

    /**
     * Adds a record to collection
     *
     * @param Doctrine_Record $record              record to be added
     * @param string $key                          optional key for the record
     * @return bool
     */
    public function add($record, $key = null)
    {
        if (isset($this->referenceField)) {
            $value = $this->reference->get($this->relation->getLocalFieldName());
            if ($value !== null) {
                $record->set($this->referenceField, $value, false);
            } else {
                $record->set($this->referenceField, $this->reference, false);
            }
            $relations = $this->relation['table']->getRelations();
            foreach ($relations as $relation) {
                if ($this->relation['class'] == $relation['localTable']->getOption('name') && $relation->getLocal() == $this->relation->getForeignFieldName()) {
                    $record->{$relation['alias']} = $this->reference;
                    break;
                }
            }
        }
        /**
         * for some weird reason in_array cannot be used here (php bug ?)
         *
         * if used it results in fatal error : [ nesting level too deep ]
         */
        foreach ($this->data as $val) {
            if ($val === $record) {
                return false;
            }
        }

        if (isset($key)) {
            if (isset($this->data[$key])) {
                return false;
            }
            $this->data[$key] = $record;
            return true;
        }

        if (isset($this->keyColumn)) {
            $value = $record->get($this->keyColumn);
            if ($value === null) {
                throw new Doctrine_Collection_Exception("Couldn't create collection index. Record field '" . $this->keyColumn . "' was null.");
            }
            $this->data[$value] = $record;
        } else {
            $this->data[] = $record;
        }

        return true;
    }

    /**
     * Merges collection into $this and returns merged collection
     *
     * @param Doctrine_Collection $coll
     * @return Doctrine_Collection
     */
    public function merge(Doctrine_Collection $coll)
    {
        $localBase = $this->getTable()->getComponentName();
        $otherBase = $coll->getTable()->getComponentName();

        if ($otherBase != $localBase && !is_subclass_of($otherBase, $localBase)) {
            throw new Doctrine_Collection_Exception("Can't merge collections with incompatible record types");
        }

        foreach ($coll->getData() as $record) {
            $this->add($record);
        }

        return $this;
    }

    /**
     * Takes a snapshot from this collection
     *
     * snapshots are used for diff processing, for example
     * when a fetched collection has three elements, then two of those
     * are being removed the diff would contain one element
     *
     * Doctrine_Collection::save() attaches the diff with the help of last
     * snapshot.
     *
     * @return Doctrine_Collection
     */
    public function takeSnapshot()
    {
        $this->_snapshot = $this->data;

        return $this;
    }

    /**
     * Processes the difference of the last snapshot and the current data
     *
     * an example:
     * Snapshot with the objects 1, 2 and 4
     * Current data with objects 2, 3 and 5
     *
     * The process would remove object 4
     *
     * @return Doctrine_Collection
     */
    public function processDiff()
    {
        foreach (array_udiff($this->_snapshot, $this->data, [$this, "compareRecords"]) as $record) {
            $record->delete();
        }

        return $this;
    }

    /**
     * Mimics the result of a $query->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
     *
     * @param bool $deep
     */
    public function toArray($deep = true, $prefixKey = false)
    {
        $data = [];
        foreach ($this as $key => $record) {

            $key = $prefixKey ? get_class($record) . '_' . $key : $key;

            $data[$key] = $record->toArray($deep, $prefixKey);
        }

        return $data;
    }

    public function toHierarchy()
    {
        $collection = $this;
        $table = $collection->getTable();

        if (! $table->isTree() || ! $table->hasColumn('level')) {
            throw new Doctrine_Exception('Cannot hydrate model that does not implements Tree behavior with `level` column');
        }

        // Trees mapped
        $trees = new Doctrine_Collection($table);
        $l = 0;

        if (count($collection) > 0) {
            // Node Stack. Used to help building the hierarchy
            $stack = new Doctrine_Collection($table);

            foreach ($collection as $child) {
                $item = $child;

                $item->mapValue('__children', new Doctrine_Collection($table));

                // Number of stack items
                $l = count($stack);

                // Check if we're dealing with different levels
                while ($l > 0 && $stack[$l - 1]['level'] >= $item['level']) {
                    array_pop($stack->data);
                    $l--;
                }

                // Stack is empty (we are inspecting the root)
                if ($l == 0) {
                    // Assigning the root child
                    $i = count($trees);
                    $trees[$i] = $item;
                    $stack[] = $trees[$i];
                } else {
                    // Add child to parent
                    $i = count($stack[$l - 1]['__children']);
                    $stack[$l - 1]['__children'][$i] = $item;
                    $stack[] = $stack[$l - 1]['__children'][$i];
                }
            }
        }
        return $trees;
    }

    /**
     * Populate a Doctrine_Collection from an array of data
     *
     * @param string $array
     * @return void
     */
    public function fromArray($array, $deep = true)
    {
        $data = [];
        foreach ($array as $rowKey => $row) {
            $this[$rowKey]->fromArray($row, $deep);
        }
    }

    /**
     * Perform a delete diff between the last snapshot and the current data
     *
     * @return array $diff
     */
    public function getDeleteDiff()
    {
        return array_udiff($this->_snapshot, $this->data, [$this, 'compareRecords']);
    }

    /**
     * Perform a insert diff between the last snapshot and the current data
     *
     * @return array $diff
     */
    public function getInsertDiff()
    {
        return array_udiff($this->data, $this->_snapshot, [$this, "compareRecords"]);
    }

    /**
     * Compares two records. To be used on _snapshot diffs using array_udiff
     *
     * @param Doctrine_Record $a
     * @param Doctrine_Record $b
     * @return int
     */
    protected function compareRecords($a, $b)
    {
        if ($a->getOid() == $b->getOid()) {
            return 0;
        }

        return ($a->getOid() > $b->getOid()) ? 1 : -1;
    }

    /**
     * Saves all records of this collection and processes the
     * difference of the last snapshot and the current data
     *
     * @param Doctrine_Connection $conn     optional connection parameter
     * @return Doctrine_Collection
     */
    public function save(Doctrine_Connection $conn = null, $processDiff = true)
    {
        if ($conn == null) {
            $conn = $this->_table->getConnection();
        }

        try {
            $conn->beginInternalTransaction();

            $conn->transaction->addCollection($this);

            if ($processDiff) {
                $this->processDiff();
            }

            foreach ($this->getData() as $key => $record) {
                $record->save($conn);
            }

            $conn->commit();
        } catch (Exception $e) {
            $conn->rollback();
            throw $e;
        }

        return $this;
    }

    /**
     * Replaces all records of this collection and processes the
     * difference of the last snapshot and the current data
     *
     * @param Doctrine_Connection $conn     optional connection parameter
     * @return Doctrine_Collection
     */
    public function replace(Doctrine_Connection $conn = null, $processDiff = true)
    {
        if ($conn == null) {
            $conn = $this->_table->getConnection();
        }

        try {
            $conn->beginInternalTransaction();

            $conn->transaction->addCollection($this);

            if ($processDiff) {
                $this->processDiff();
            }

            foreach ($this->getData() as $key => $record) {
                $record->replace($conn);
            }

            $conn->commit();
        } catch (Exception $e) {
            $conn->rollback();
            throw $e;
        }

        return $this;
    }

    /**
     * Deletes all records from this collection
     *
     * @return Doctrine_Collection
     */
    public function delete(Doctrine_Connection $conn = null, $clearColl = true)
    {
        if ($conn == null) {
            $conn = $this->_table->getConnection();
        }

        try {
            $conn->beginInternalTransaction();
            $conn->transaction->addCollection($this);

            foreach ($this as $key => $record) {
                $record->delete($conn);
            }

            $conn->commit();
        } catch (Exception $e) {
            $conn->rollback();
            throw $e;
        }

        if ($clearColl) {
            $this->clear();
        }

        return $this;
    }

    /**
     * Clears the collection.
     *
     * @return void
     */
    public function clear()
    {
        $this->data = [];
    }

    /**
     * Frees the resources used by the collection.
     * WARNING: After invoking free() the collection is no longer considered to
     * be in a useable state. Subsequent usage may result in unexpected behavior.
     *
     * @return void
     */
    public function free($deep = false)
    {
        foreach ($this->getData() as $key => $record) {
            if (! ($record instanceof Doctrine_Null)) {
                $record->free($deep);
            }
        }

        $this->data = [];

        if ($this->reference) {
            $this->reference->free($deep);
            $this->reference = null;
        }
    }

    /**
     * Get collection data iterator
     *
     * @return Traversable|iterable<Doctrine_Record>
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->data);
    }

    /**
     * Returns the relation object
     *
     * @return Doctrine_Relation
     */
    public function getRelation()
    {
        return $this->relation;
    }

    /**
     * checks if one of the containing records is modified
     * returns true if modified, false otherwise
     *
     * @return bool
     */
    final public function isModified()
    {
        $dirty = (count($this->getInsertDiff()) > 0 || count($this->getDeleteDiff()) > 0);
        if (! $dirty) {
            foreach ($this as $record) {
                if ($dirty = $record->isModified()) {
                    break;
                }
            }
        }
        return $dirty;
    }
}
