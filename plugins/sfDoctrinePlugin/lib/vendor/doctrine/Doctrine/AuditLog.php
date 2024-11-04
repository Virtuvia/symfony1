<?php
/*
 *  $Id$
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
 * Doctrine_AuditLog
 *
 * @package     Doctrine
 * @subpackage  AuditLog
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link        www.doctrine-project.org
 * @since       1.0
 * @version     $Revision$
 * @author      Konsta Vesterinen <kvesteri@cc.hut.fi>
 */
class Doctrine_AuditLog extends Doctrine_Record_Generator
{
    /**
     * Array of AuditLog Options
     *
     * @var array
     */
    protected $_options = ['className'         => '%CLASS%Version',
        'localRelationOptions' => [
            'foreignKeyName' => null,
        ],
        'version'           => ['name'   => 'version',
            'alias'  => null,
            'type'   => 'integer',
            'length' => 8,
            'options' => ['primary' => true]],
        'tableName'         => false,
        'generateFiles'     => false,
        'table'             => false,
        'pluginTable'       => false,
        'children'          => [],
        'auditLog'          => true,
        'deleteVersions'    => true,
        'cascadeDelete'     => true,
        'excludeFields'     => [],
        'appLevelDelete'    => false];

    /**
     * Accepts array of options to configure the AuditLog
     *
     * @param   array $options An array of options
     * @return  void
     */
    public function __construct(array $options = [])
    {
        $this->_options = Doctrine_Lib::arrayDeepMerge($this->_options, $options);
    }

    public function buildRelation()
    {
        $this->buildForeignRelation('Version');
        $this->buildLocalRelation();
    }

    /**
     * Set the table definition for the audit log table
     *
     * @return  void
     */
    public function setTableDefinition()
    {
        $name = $this->_options['table']->getComponentName();

        // Building columns
        $columns = $this->_options['table']->getColumns();

        // remove all autoincrement and unique constraint definitions and add to the behavior model
        foreach ($columns as $column => $definition) {
            if (in_array($column, $this->_options['excludeFields'])) {
                continue;
            }
            unset($definition['autoincrement']);
            unset($definition['unique']);

            $fieldName = $this->_options['table']->getFieldName($column);
            if ($fieldName != $column) {
                $name = $column . ' as ' . $fieldName;
            } else {
                $name = $fieldName;
            }

            $this->hasColumn($name, $definition['type'], $definition['length'], $definition);
        }

        $indexes = $this->_options['indexes'] ?? [];

        foreach ($indexes as $indexName => $indexDefinition) {
            $this->index($indexName, $indexDefinition);
        }

        // the version column should be part of the primary key definition
        $this->hasColumn(
            $this->_options['version']['name'],
            $this->_options['version']['type'],
            $this->_options['version']['length'],
            $this->_options['version']['options']);
    }

    /**
     * Get array of information for the passed record and the specified version
     *
     * @param   Doctrine_Record $record
     * @param   int         $version
     * @param   int         $hydrationMode
     * @param	bool			$asCollection
     * @return  array           An array or Doctrine_Collection or a Doctrine_Record
     */
    public function getVersion(Doctrine_Record $record, $version, $hydrationMode = Doctrine_Core::HYDRATE_ARRAY, $asCollection = true)
    {
        $className = $this->_options['className'];
        $method    = ($asCollection) ? 'execute' : 'fetchOne';

        $q = Doctrine_Core::getTable($className)
            ->createQuery();

        $values = [];
        foreach ((array) $this->_options['table']->getIdentifier() as $id) {
            $conditions[] = $className . '.' . $id . ' = ?';
            $values[] = $record->get($id);
        }

        $where = implode(' AND ', $conditions) . ' AND ' . $className . '.' . $this->_options['version']['name'] . ' = ?';

        $values[] = $version;

        $q->where($where);

        return $q->$method($values, $hydrationMode);
    }

    /**
     * Get the max version number for a given Doctrine_Record
     *
     * @param Doctrine_Record $record
     * @return int $versionnumber
     */
    public function getMaxVersion(Doctrine_Record $record)
    {
        $className = $this->_options['className'];
        $select = 'MAX(' . $className . '.' . $this->_options['version']['name'] . ') max_version';

        foreach ((array) $this->_options['table']->getIdentifier() as $id) {
            $conditions[] = $className . '.' . $id . ' = ?';
            $values[] = $record->get($id);
        }

        $q = Doctrine_Core::getTable($className)
            ->createQuery()
            ->select($select)
            ->where(implode(' AND ', $conditions));

        $result = $q->execute($values, Doctrine_Core::HYDRATE_ARRAY);

        return isset($result[0]['max_version']) ? $result[0]['max_version'] : 0;
    }
}
