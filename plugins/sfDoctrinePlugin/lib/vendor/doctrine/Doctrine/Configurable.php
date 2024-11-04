<?php
/*
 *  $Id: Configurable.php 7490 2010-03-29 19:53:27Z jwage $
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
 * Doctrine_Configurable
 * the base for Doctrine_Table, Doctrine_Manager and Doctrine_Connection
 *
 * @package     Doctrine
 * @subpackage  Configurable
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link        www.doctrine-project.org
 * @since       1.0
 * @version     $Revision: 7490 $
 * @author      Konsta Vesterinen <kvesteri@cc.hut.fi>
 */
abstract class Doctrine_Configurable
{
    /**
     * @var array $attributes               an array of containing all attributes
     */
    protected $attributes = [];

    /**
     * @var Doctrine_Configurable $parent   the parent of this component
     */
    protected $parent;

    /**
     * setAttribute
     * sets a given attribute
     *
     * <code>
     * $manager->setAttribute(Doctrine_Core::ATTR_PORTABILITY, Doctrine_Core::PORTABILITY_ALL);
     * </code>
     *
     * @param mixed $attribute              either a Doctrine_Core::ATTR_* integer constant or a string
     *                                      corresponding to a constant
     * @param mixed $value                  the value of the attribute
     * @see Doctrine_Core::ATTR_* constants
     * @throws Doctrine_Exception           if the value is invalid
     * @return void
     */
    public function setAttribute($attribute, $value)
    {
        switch ($attribute) {
            case Doctrine_Core::ATTR_LISTENER:
                $this->setEventListener($value);
                break;
            case Doctrine_Core::ATTR_COLL_KEY:
                if (! ($this instanceof Doctrine_Table)) {
                    throw new Doctrine_Exception("This attribute can only be set at table level.");
                }
                if ($value !== null && ! $this->hasField($value)) {
                    throw new Doctrine_Exception("Couldn't set collection key attribute. No such field '$value'.");
                }
                break;
            case Doctrine_Core::ATTR_QUERY_CACHE:
                if ($value !== null) {
                    if (! ($value instanceof Doctrine_Cache_Interface)) {
                        throw new Doctrine_Exception('Cache driver should implement Doctrine_Cache_Interface');
                    }
                }
                break;
            case Doctrine_Core::ATTR_FIELD_CASE:
                if ($value != 0 && $value != CASE_LOWER && $value != CASE_UPPER) {
                    throw new Doctrine_Exception('Field case attribute should be either 0, CASE_LOWER or CASE_UPPER constant.');
                }
                break;
            case Doctrine_Core::ATTR_IDXNAME_FORMAT:
            case Doctrine_Core::ATTR_TBLNAME_FORMAT:
            case Doctrine_Core::ATTR_FKNAME_FORMAT:
                if ($this instanceof Doctrine_Table) {
                    throw new Doctrine_Exception('Index name format attributes cannot be set'
                                               . 'at table level (only at connection or global level).');
                }
                break;
        }

        $this->attributes[$attribute] = $value;
    }

    /**
     * @param Doctrine_EventListener $listener
     * @return void
     */
    public function setEventListener($listener)
    {
        return $this->setListener($listener);
    }

    /**
     * addRecordListener
     *
     * @param Doctrine_EventListener_Interface|Doctrine_Overloadable $listener
     * @return Doctrine_Configurable        this object
     */
    public function addRecordListener($listener, $name = null)
    {
        if (! isset($this->attributes[Doctrine_Core::ATTR_RECORD_LISTENER]) ||
             ! ($this->attributes[Doctrine_Core::ATTR_RECORD_LISTENER] instanceof Doctrine_Record_Listener_Chain)) {

            $this->attributes[Doctrine_Core::ATTR_RECORD_LISTENER] = new Doctrine_Record_Listener_Chain();
        }
        $this->attributes[Doctrine_Core::ATTR_RECORD_LISTENER]->add($listener, $name);

        return $this;
    }

    /**
     * getListener
     *
     * @return Doctrine_EventListener_Interface|Doctrine_Overloadable
     */
    public function getRecordListener()
    {
        if (! isset($this->attributes[Doctrine_Core::ATTR_RECORD_LISTENER])) {
            if (isset($this->parent)) {
                return $this->parent->getRecordListener();
            }
            return null;
        }
        return $this->attributes[Doctrine_Core::ATTR_RECORD_LISTENER];
    }

    /**
     * addListener
     *
     * @param Doctrine_EventListener_Interface|Doctrine_Overloadable $listener
     * @return Doctrine_Configurable    this object
     */
    public function addListener($listener, $name = null)
    {
        if (! isset($this->attributes[Doctrine_Core::ATTR_LISTENER]) ||
             ! ($this->attributes[Doctrine_Core::ATTR_LISTENER] instanceof Doctrine_EventListener_Chain)) {

            $this->attributes[Doctrine_Core::ATTR_LISTENER] = new Doctrine_EventListener_Chain();
        }
        $this->attributes[Doctrine_Core::ATTR_LISTENER]->add($listener, $name);

        return $this;
    }

    /**
     * getListener
     *
     * @return Doctrine_EventListener_Interface|Doctrine_Overloadable
     */
    public function getListener()
    {
        if (! isset($this->attributes[Doctrine_Core::ATTR_LISTENER])) {
            if (isset($this->parent)) {
                return $this->parent->getListener();
            }
            return null;
        }
        return $this->attributes[Doctrine_Core::ATTR_LISTENER];
    }

    /**
     * setListener
     *
     * @param Doctrine_EventListener_Interface|Doctrine_Overloadable $listener
     * @return Doctrine_Configurable        this object
     */
    public function setListener($listener)
    {
        if (! ($listener instanceof Doctrine_EventListener_Interface)
            && ! ($listener instanceof Doctrine_Overloadable)
        ) {
            throw new Doctrine_EventListener_Exception("Couldn't set eventlistener. EventListeners should implement either Doctrine_EventListener_Interface or Doctrine_Overloadable");
        }
        $this->attributes[Doctrine_Core::ATTR_LISTENER] = $listener;

        return $this;
    }

    /**
     * returns the value of an attribute
     *
     * @param int $attribute
     * @return mixed
     */
    public function getAttribute($attribute)
    {
        if (isset($this->attributes[$attribute])) {
            return $this->attributes[$attribute];
        }

        if (isset($this->parent)) {
            return $this->parent->getAttribute($attribute);
        }
        return null;
    }

    /**
     * Set the charset
     *
     * @param string $charset
     */
    public function setCharset($charset)
    {
        $this->setAttribute(Doctrine_Core::ATTR_DEFAULT_TABLE_CHARSET, $charset);
    }

    /**
     * Set the collate
     *
     * @param string $collate
     */
    public function setCollate($collate)
    {
        $this->setAttribute(Doctrine_Core::ATTR_DEFAULT_TABLE_COLLATE, $collate);
    }

    /**
     * sets a parent for this configurable component
     * the parent must be configurable component itself
     *
     * @param Doctrine_Configurable $component
     * @return void
     */
    public function setParent(Doctrine_Configurable $component)
    {
        $this->parent = $component;
    }

    /**
     * getParent
     * returns the parent of this component
     *
     * @return Doctrine_Configurable
     */
    public function getParent()
    {
        return $this->parent;
    }
}
