<?php
/*
 *  $Id: Manager.php 7657 2010-06-08 17:57:01Z jwage $
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
 *
 * Doctrine_Manager is the base component of all doctrine based projects.
 * It opens and keeps track of all connections (database connections).
 *
 * @package     Doctrine
 * @subpackage  Manager
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link        www.doctrine-project.org
 * @since       1.0
 * @version     $Revision: 7657 $
 * @author      Konsta Vesterinen <kvesteri@cc.hut.fi>
 */
class Doctrine_Manager extends Doctrine_Configurable implements Countable, IteratorAggregate
{
    /**
     * @var array $connections          an array containing all the opened connections
     */
    protected $_connections   = [];

    /**
     * @var array $bound                an array containing all components that have a bound connection
     */
    protected $_bound         = [];

    /**
     * @var int $index              the incremented index
     */
    protected $_index         = 0;

    /**
     * @var int $currIndex          the current connection index
     */
    protected $_currIndex     = 0;

    /**
     * @var Doctrine_Query_Registry     the query registry
     */
    protected $_queryRegistry;

    /**
     * @var array                       Array of registered validators
     */
    protected $_validators = [];

    /**
     * @var array                       Array of registered hydrators
     */
    protected $_hydrators = [
        Doctrine_Core::HYDRATE_ARRAY            => 'Doctrine_Hydrator_ArrayDriver',
        Doctrine_Core::HYDRATE_RECORD           => 'Doctrine_Hydrator_RecordDriver',
        Doctrine_Core::HYDRATE_NONE             => 'Doctrine_Hydrator_NoneDriver',
        Doctrine_Core::HYDRATE_SCALAR           => 'Doctrine_Hydrator_ScalarDriver',
        Doctrine_Core::HYDRATE_SINGLE_SCALAR    => 'Doctrine_Hydrator_SingleScalarDriver',
        Doctrine_Core::HYDRATE_ARRAY_HIERARCHY  => 'Doctrine_Hydrator_ArrayHierarchyDriver',
        Doctrine_Core::HYDRATE_RECORD_HIERARCHY => 'Doctrine_Hydrator_RecordHierarchyDriver',
        Doctrine_Core::HYDRATE_ARRAY_SHALLOW    => 'Doctrine_Hydrator_ArrayShallowDriver',
    ];

    protected $_connectionDrivers = [
        'mysql'    => 'Doctrine_Connection_Mysql',
        'mysqli'   => 'Doctrine_Connection_Mysql',
        'sqlite'   => 'Doctrine_Connection_Sqlite',
        'mock'     => 'Doctrine_Connection_Mock',
    ];

    protected $_extensions = [];

    /**
     * @var bool                     Whether or not the validators from disk have been loaded
     */
    protected $_loadedValidatorsFromDisk = false;

    private static ?Doctrine_Manager $_instance;

    private $_initialized = false;

    private Doctrine_Type_Registry $typeRegistry;

    /**
     * constructor
     *
     * this is private constructor (use getInstance to get an instance of this class)
     */
    private function __construct()
    {
        $null = new Doctrine_Null();
        Doctrine_Record::initNullObject($null);
        Doctrine_Table::initNullObject($null);
        Doctrine_Hydrator_RecordDriver::initNullObject($null);
        Doctrine_Record_Iterator::initNullObject($null);

        $this->typeRegistry = new Doctrine_Type_Registry();
        $this->registerType('blob', new Doctrine_Type_BlobType());
        $this->registerType('boolean', new Doctrine_Type_BooleanType());
        $this->registerType('clob', new Doctrine_Type_ClobType());
        $this->registerType('date', new Doctrine_Type_DateType());
        $this->registerType('decimal', new Doctrine_Type_DecimalType());
        $this->registerType('double', new Doctrine_Type_DoubleType());
        $this->registerType('enum', new Doctrine_Type_EnumType());
        $this->registerType('float', new Doctrine_Type_FloatType());
        $this->registerType('integer', new Doctrine_Type_IntegerType());
        $this->registerType('string', new Doctrine_Type_StringType());
        $this->registerType('text', new Doctrine_Type_TextType());
        $this->registerType('timestamp', new Doctrine_Type_TimestampType());
        $this->registerType('time', new Doctrine_Type_TimeType());
    }

    /**
     * @throws Doctrine_Type_Exception_ConversionFailed
     * @throws Doctrine_Type_Exception_UnknownType
     */
    public function convertToDatabaseValue(string $type, mixed $phpValue): mixed
    {
        return $this->typeRegistry->getType($type)->convertToDatabaseValue($phpValue);
    }

    /**
     * @throws Doctrine_Type_Exception_ConversionFailed
     * @throws Doctrine_Type_Exception_UnknownType
     */
    public function convertToPhpValue(string $type, mixed $databaseValue): mixed
    {
        return $this->typeRegistry->getType($type)->convertToPhpValue($databaseValue);
    }

    /**
     * @throws Doctrine_Type_Exception_UnknownType
     */
    public function isValueModified(string $type, mixed $old, mixed $new): bool
    {
        return $this->typeRegistry->getType($type)->isValueModified($old, $new);
    }

    /**
     * @throws Doctrine_Type_Exception_UnknownType
     */
    public function getType(string $type): Doctrine_Type
    {
        return $this->typeRegistry->getType($type);
    }

    public function registerType(string $name, Doctrine_Type $type): void
    {
        $this->typeRegistry->registerType($name, $type);
    }

    /**
     * Sets default attributes values.
     *
     * This method sets default values for all null attributes of this
     * instance. It is idempotent and can only be called one time. Subsequent
     * calls does not alter the attribute values.
     *
     * @return bool      true if inizialization was executed
     */
    public function setDefaultAttributes()
    {
        if (! $this->_initialized) {
            $this->_initialized = true;
            $attributes = [
                Doctrine_Core::ATTR_QUERY_CACHE                  => null,
                Doctrine_Core::ATTR_LOAD_REFERENCES              => true,
                Doctrine_Core::ATTR_LISTENER                     => new Doctrine_EventListener(),
                Doctrine_Core::ATTR_RECORD_LISTENER              => new Doctrine_Record_Listener(),
                Doctrine_Core::ATTR_THROW_EXCEPTIONS             => true,
                Doctrine_Core::ATTR_VALIDATE                     => Doctrine_Core::VALIDATE_NONE,
                Doctrine_Core::ATTR_QUERY_LIMIT                  => Doctrine_Core::LIMIT_RECORDS,
                Doctrine_Core::ATTR_IDXNAME_FORMAT               => "%s_idx",
                Doctrine_Core::ATTR_SEQNAME_FORMAT               => "%s_seq",
                Doctrine_Core::ATTR_TBLNAME_FORMAT               => "%s",
                Doctrine_Core::ATTR_FKNAME_FORMAT                => "%s",
                Doctrine_Core::ATTR_QUOTE_IDENTIFIER             => false,
                Doctrine_Core::ATTR_SEQCOL_NAME                  => 'id',
                Doctrine_Core::ATTR_PORTABILITY                  => Doctrine_Core::PORTABILITY_NONE,
                Doctrine_Core::ATTR_EXPORT                       => Doctrine_Core::EXPORT_ALL,
                Doctrine_Core::ATTR_DECIMAL_PLACES               => 2,
                Doctrine_Core::ATTR_DEFAULT_PARAM_NAMESPACE      => 'doctrine',
                Doctrine_Core::ATTR_AUTOLOAD_TABLE_CLASSES       => false,
                Doctrine_Core::ATTR_USE_DQL_CALLBACKS            => false,
                Doctrine_Core::ATTR_AUTO_FREE_QUERY_OBJECTS      => false,
                Doctrine_Core::ATTR_DEFAULT_COLUMN_OPTIONS       => [],
                Doctrine_Core::ATTR_HYDRATE_OVERWRITE            => true,
                Doctrine_Core::ATTR_QUERY_CLASS                  => 'Doctrine_Query',
                Doctrine_Core::ATTR_COLLECTION_CLASS             => 'Doctrine_Collection',
                Doctrine_Core::ATTR_TABLE_CLASS                  => 'Doctrine_Table',
                Doctrine_Core::ATTR_CASCADE_SAVES                => true,
                Doctrine_Core::ATTR_TABLE_CLASS_FORMAT           => '%sTable',
            ];
            foreach ($attributes as $attribute => $value) {
                $old = $this->getAttribute($attribute);
                if ($old === null) {
                    $this->setAttribute($attribute, $value);
                }
            }
            return true;
        }
        return false;
    }

    public static function getInstance(): Doctrine_Manager
    {
        return self::$_instance ??= new self();
    }

    /**
     * Lazy-initializes the query registry object and returns it
     *
     * @return Doctrine_Query_Registry
     */
    public function getQueryRegistry()
    {
        if (! isset($this->_queryRegistry)) {
            $this->_queryRegistry = new Doctrine_Query_Registry();
        }
        return $this->_queryRegistry;
    }

    /**
     * Sets the query registry
     *
     * @return Doctrine_Manager     this object
     */
    public function setQueryRegistry(Doctrine_Query_Registry $registry)
    {
        $this->_queryRegistry = $registry;

        return $this;
    }

    /**
     * Open a new connection. If the adapter parameter is set this method acts as
     * a short cut for Doctrine_Manager::getInstance()->openConnection($adapter, $name);
     *
     * if the adapter paramater is not set this method acts as
     * a short cut for Doctrine_Manager::getInstance()->getCurrentConnection()
     *
     * @param PDO|Doctrine_Adapter_Interface $adapter   database driver
     * @param string $name                              name of the connection, if empty numeric key is used
     * @throws Doctrine_Manager_Exception               if trying to bind a connection with an existing name
     * @return Doctrine_Connection
     */
    public static function connection($adapter = null, $name = null)
    {
        if ($adapter == null) {
            return Doctrine_Manager::getInstance()->getCurrentConnection();
        } else {
            return Doctrine_Manager::getInstance()->openConnection($adapter, $name);
        }
    }

    /**
     * Opens a new connection and saves it to Doctrine_Manager->connections
     *
     * @param PDO|Doctrine_Adapter_Interface $adapter   database driver
     * @param string $name                              name of the connection, if empty numeric key is used
     * @throws Doctrine_Manager_Exception               if trying to bind a connection with an existing name
     * @throws Doctrine_Manager_Exception               if trying to open connection for unknown driver
     * @return Doctrine_Connection
     */
    public function openConnection($adapter, $name = null, $setCurrent = true)
    {
        if (is_object($adapter)) {
            if (! ($adapter instanceof PDO) && ! in_array('Doctrine_Adapter_Interface', class_implements($adapter))) {
                throw new Doctrine_Manager_Exception("First argument should be an instance of PDO or implement Doctrine_Adapter_Interface");
            }

            $driverName = $adapter->getAttribute(Doctrine_Core::ATTR_DRIVER_NAME);
        } elseif (is_array($adapter)) {
            if (! isset($adapter[0])) {
                throw new Doctrine_Manager_Exception('Empty data source name given.');
            }
            $e = explode(':', $adapter[0]);

            if ($e[0] == 'uri') {
                $e[0] = 'odbc';
            }

            $parts['dsn']    = $adapter[0];
            $parts['scheme'] = $e[0];
            $parts['user']   = (isset($adapter[1])) ? $adapter[1] : null;
            $parts['pass']   = (isset($adapter[2])) ? $adapter[2] : null;
            $driverName = $e[0];
            $adapter = $parts;
        } else {
            $parts = $this->parseDsn($adapter);
            $driverName = $parts['scheme'];
            $adapter = $parts;
        }

        // Decode adapter information
        if (is_array($adapter)) {
            foreach ($adapter as $key => $value) {
                $adapter[$key]  = $value ? urldecode($value) : null;
            }
        }

        // initialize the default attributes
        $this->setDefaultAttributes();

        if ($name !== null) {
            $name = (string) $name;
            if (isset($this->_connections[$name])) {
                if ($setCurrent) {
                    $this->_currIndex = $name;
                }
                return $this->_connections[$name];
            }
        } else {
            $name = $this->_index;
            $this->_index++;
        }

        if (! isset($this->_connectionDrivers[$driverName])) {
            throw new Doctrine_Manager_Exception('Unknown driver ' . $driverName);
        }

        $className = $this->_connectionDrivers[$driverName];
        $conn = new $className($this, $adapter);
        $conn->setName($name);

        $this->_connections[$name] = $conn;

        if ($setCurrent) {
            $this->_currIndex = $name;
        }
        return $this->_connections[$name];
    }

    /**
     * Parse a pdo style dsn in to an array of parts
     *
     * @param array $dsn An array of dsn information
     * @return array The array parsed
     * @todo package:dbal
     */
    public function parsePdoDsn($dsn)
    {
        $parts = [];

        $names = ['dsn', 'scheme', 'host', 'port', 'user', 'pass', 'path', 'query', 'fragment', 'unix_socket'];

        foreach ($names as $name) {
            if (! isset($parts[$name])) {
                $parts[$name] = null;
            }
        }

        $e = explode(':', $dsn);
        $parts['scheme'] = $e[0];
        $parts['dsn'] = $dsn;

        $e = explode(';', $e[1]);
        foreach ($e as $string) {
            if ($string) {
                $e2 = explode('=', $string);

                if (isset($e2[0]) && isset($e2[1])) {
                    if (count($e2) > 2) {
                        $key = $e2[0];
                        unset($e2[0]);
                        $value = implode('=', $e2);
                    } else {
                        list($key, $value) = $e2;
                    }
                    $parts[$key] = $value;
                }
            }
        }

        return $parts;
    }

    /**
     * Build the blank dsn parts array used with parseDsn()
     *
     * @see parseDsn()
     * @param string $dsn
     * @return array $parts
     */
    protected function _buildDsnPartsArray($dsn)
    {
        // fix sqlite dsn so that it will parse correctly
        $dsn = str_replace("////", "/", $dsn);
        $dsn = str_replace("\\", "/", $dsn);
        $dsn = preg_replace("/\/\/\/(.*):\//", "//$1:/", $dsn);

        // silence any warnings
        $parts = @parse_url($dsn);

        $names = ['dsn', 'scheme', 'host', 'port', 'user', 'pass', 'path', 'query', 'fragment'];

        foreach ($names as $name) {
            if (! isset($parts[$name])) {
                $parts[$name] = null;
            }
        }

        if (count($parts) == 0 || ! isset($parts['scheme'])) {
            throw new Doctrine_Manager_Exception('Could not parse dsn');
        }

        return $parts;
    }

    /**
     * Parse a Doctrine style dsn string in to an array of parts
     *
     * @param string $dsn
     * @return array Parsed contents of DSN
     * @todo package:dbal
     */
    public function parseDsn($dsn)
    {
        $parts = $this->_buildDsnPartsArray($dsn);

        switch ($parts['scheme']) {
            case 'sqlite':
            case 'sqlite2':
            case 'sqlite3':
                if (isset($parts['host']) && $parts['host'] == ':memory') {
                    $parts['database'] = ':memory:';
                    $parts['dsn']      = 'sqlite::memory:';
                } else {
                    //fix windows dsn we have to add host: to path and set host to null
                    if (isset($parts['host'])) {
                        $parts['path'] = $parts['host'] . ":" . $parts["path"];
                        $parts['host'] = null;
                    }
                    $parts['database'] = $parts['path'];
                    $parts['dsn'] = $parts['scheme'] . ':' . $parts['path'];
                }

                break;

            case 'mysql':
            case 'mock':
                if (! isset($parts['path']) || $parts['path'] == '/') {
                    throw new Doctrine_Manager_Exception('No database available in data source name');
                }
                if (isset($parts['path'])) {
                    $parts['database'] = substr($parts['path'], 1);
                }
                if (! isset($parts['host'])) {
                    throw new Doctrine_Manager_Exception('No hostname set in data source name');
                }

                $parts['dsn'] = $parts['scheme'] . ':host='
                              . $parts['host'] . (isset($parts['port']) ? ';port=' . $parts['port'] : null) . ';dbname='
                              . $parts['database'];

                break;
            default:
                $parts['dsn'] = $dsn;
        }

        return $parts;
    }

    /**
     * Get the connection instance for the passed name
     *
     * @param string $name                  name of the connection, if empty numeric key is used
     * @return Doctrine_Connection
     * @throws Doctrine_Manager_Exception   if trying to get a non-existent connection
     */
    public function getConnection($name)
    {
        if (! isset($this->_connections[$name])) {
            throw new Doctrine_Manager_Exception('Unknown connection: ' . $name);
        }

        return $this->_connections[$name];
    }

    /**
     * Get the name of the passed connection instance
     *
     * @param Doctrine_Connection $conn     connection object to be searched for
     * @return string                       the name of the connection
     */
    public function getConnectionName(Doctrine_Connection $conn)
    {
        return array_search($conn, $this->_connections, true);
    }

    /**
     * Binds given component to given connection
     * this means that when ever the given component uses a connection
     * it will be using the bound connection instead of the current connection
     *
     * @param string $componentName
     * @param string $connectionName
     * @return bool
     */
    public function bindComponent($componentName, $connectionName)
    {
        $this->_bound[$componentName] = $connectionName;
    }

    /**
     * Get the connection instance for the specified component
     *
     * @param string $componentName
     * @return Doctrine_Connection
     */
    public function getConnectionForComponent($componentName)
    {
        Doctrine_Core::modelsAutoload($componentName);

        if (isset($this->_bound[$componentName])) {
            return $this->getConnection($this->_bound[$componentName]);
        }

        return $this->getCurrentConnection();
    }

    /**
     * Check if a component is bound to a connection
     *
     * @param string $componentName
     * @return bool
     */
    public function hasConnectionForComponent($componentName = null)
    {
        return isset($this->_bound[$componentName]);
    }

    /**
     * Closes the specified connection
     *
     * @param Doctrine_Connection $connection
     * @return void
     */
    public function closeConnection(Doctrine_Connection $connection)
    {
        $connection->close();

        $key = array_search($connection, $this->_connections, true);

        if ($key !== false) {
            unset($this->_connections[$key]);

            if ($key === $this->_currIndex) {
                $key = key($this->_connections);
                $this->_currIndex = ($key !== null) ? $key : 0;
            }
        }

        unset($connection);
    }

    /**
     * Returns all opened connections
     *
     * @return array
     */
    public function getConnections()
    {
        return $this->_connections;
    }

    /**
     * Sets the current connection to $key
     *
     * @param mixed $key                        the connection key
     * @throws InvalidKeyException
     * @return void
     */
    public function setCurrentConnection($key)
    {
        $key = (string) $key;
        if (! isset($this->_connections[$key])) {
            throw new Doctrine_Manager_Exception("Connection key '$key' does not exist.");
        }
        $this->_currIndex = $key;
    }

    /**
     * Whether or not the manager contains specified connection
     *
     * @param mixed $key                        the connection key
     * @return bool
     */
    public function contains($key)
    {
        return isset($this->_connections[$key]);
    }

    /**
     * Returns the number of opened connections
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->_connections);
    }

    /**
     * Returns an ArrayIterator that iterates through all connections
     *
     * @return Traversable|iterable<Doctrine_Connection>
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->_connections);
    }

    /**
     * Get the current connection instance
     *
     * @throws Doctrine_Connection_Exception       if there are no open connections
     * @return Doctrine_Connection
     */
    public function getCurrentConnection()
    {
        $i = $this->_currIndex;
        if (! isset($this->_connections[$i])) {
            throw new Doctrine_Connection_Exception('There is no open connection');
        }
        return $this->_connections[$i];
    }

    /**
     * Get available doctrine validators
     *
     * @return array $validators
     */
    public function getValidators()
    {
        if (! $this->_loadedValidatorsFromDisk) {
            $this->_loadedValidatorsFromDisk = true;

            $validators = [];

            $dir = Doctrine_Core::getPath() . DIRECTORY_SEPARATOR . 'Doctrine' . DIRECTORY_SEPARATOR . 'Validator';

            $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir), RecursiveIteratorIterator::LEAVES_ONLY);
            foreach ($files as $file) {
                $e = explode('.', $file->getFileName());

                if (end($e) == 'php') {
                    $name = strtolower($e[0]);

                    $validators[] = $name;
                }
            }

            $this->registerValidators($validators);
        }

        return $this->_validators;
    }

    /**
     * Register validators so that Doctrine is aware of them
     *
     * @param  mixed $validators Name of validator or array of validators
     * @return void
     */
    public function registerValidators($validators)
    {
        $validators = (array) $validators;
        foreach ($validators as $validator) {
            if (! in_array($validator, $this->_validators)) {
                $this->_validators[] = $validator;
            }
        }
    }

    /**
     * Register a new driver for hydration
     *
     * @return void
     */
    public function registerHydrator($name, $class)
    {
        $this->_hydrators[$name] = $class;
    }

    /**
     * Get all registered hydrators
     *
     * @return array $hydrators
     */
    public function getHydrators()
    {
        return $this->_hydrators;
    }

    /**
     * Register a custom connection driver
     *
     * @return void
     */
    public function registerConnectionDriver($name, $class)
    {
        $this->_connectionDrivers[$name] = $class;
    }
}
