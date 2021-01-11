<?php
namespace com\chrissyx\newsscript;
use \PDO as PDO;
use \PDOStatement as PDOStatement;

/**
 * Abstract PDO data storage control using a detected concrete DB implementation.
 *
 * @author Chrissyx <chris@chrissyx.com>
 * @package CHSNewsscript
 */
abstract class DB extends PDO
{
    /**
     * Counter of performed queries.
     *
     * @var int Counted queries
     */
    private $queryCounter = 0;

    /**
     * Returns the detected concrete DB implementation of the PDO data storage engine.
     * By having more than one the used DB instance is undefined!
     *
     * @return DB A concrete DB implementation
     */
    public static function getInstance()
    {
        static $instance;
        if(is_null($instance))
        {
            if(file_exists('cache/dbImplementation.php'))
                $dbImplementation = include('cache/dbImplementation.php');
            else
                foreach(Functions::glob('modules/DB/*.php') as $curDbImplementation)
                    if(Functions::stripos(php_strip_whitespace($curDbImplementation), ' extends DB') !== false)
                    {
                        file_put_contents('cache/dbImplementation.php', '<?php include(\'' . $curDbImplementation . '\'); return \'' . ($dbImplementation = __NAMESPACE__ . '\\' . basename($curDbImplementation, '.php')) . '\'; ?>', LOCK_EX);
                        include($curDbImplementation);
                    }
            $instance = new $dbImplementation;
            if(error_reporting() == E_ALL)
                $instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $instance->setAttribute(PDO::ATTR_STATEMENT_CLASS, array(__NAMESPACE__ . '\\' . 'DBStatement', array()));
            PlugIns::getInstance()->callHook(PlugIns::HOOK_DB_INIT);
        }
        return $instance;
    }

    /**
     * Returns PDO's representation of the given variable's type.
     *
     * @param mixed $variable Variable to get its PDO parameter type
     * @return int PDO type representation
     */
    public static function getPdoParam($variable)
    {
        return is_int($variable) || is_long($variable) ? PDO::PARAM_INT
            : (is_bool($variable) ? PDO::PARAM_BOOL
            : (is_null($variable) ? PDO::PARAM_NULL
            : PDO::PARAM_STR));
    }

    /**
     * Executes given SQL query with automatic escaping.
     *
     * @param string $query The query statement to perform
     * @param mixed,... $params Values to insert into statement before
     * @return int Number of affected rows
     */
    public function exec($query)
    {
        $this->increaseQueryCounter();
        //Insert params into query (if any)
        if(func_num_args() > 1)
            $query = vsprintf($query, array_map(array($this, 'escape'), array_slice(func_get_args(), 1)));
        //Execute query
        return parent::exec($query);
    }

    /**
     * Returns number of performed queries since initialization and connecting to database.
     *
     * @return int Number of performed queries
     */
    public function getQueryCounter()
    {
        return $this->queryCounter;
    }

    /**
     * Increases the internal query counter.
     */
    public function increaseQueryCounter()
    {
        $this->queryCounter++;
    }

    /**
     * Executes given SQL query with automatic escaping.
     *
     * @param string $query The query statement to perform
     * @param mixed,... $params Values to insert into statement before
     * @return PDOStatement|bool Result set or false on failure
     */
    public function query($query)
    {
        $this->increaseQueryCounter();
        //Insert params into query (if any)
        if(func_num_args() > 1)
            $query = vsprintf($query, array_map(array($this, 'escape'), array_slice(func_get_args(), 1)));
        //Execute query
        return parent::query($query);
    }

    /**
     * Escapes special characters in a string for use in a PDO statement using {@link PDO::quote()}.
     *
     * @param mixed $data String or array of strings to escape
     * @return mixed Escaped data
     */
    private function escape($data)
    {
        return is_array($data) ? array_map(array($this, 'escape'), $data) : parent::quote($data, DB::getPdoParam($data));
    }
}

/**
 * Enhanced PDO Statement with some accompanied query counter handling.
 */
class DBStatement extends PDOStatement
{
    /**
     * Empty constructor.
     *
     * @return DBStatement New instance of this class
     */
    private function __construct()
    {
    }

    /**
    * Executes a prepared statement.
    *
    * @param array $input_parameters Optional values for binding
    * @return bool Successful execution
    */
    public function execute($input_parameters=null)
    {
        DB::getInstance()->increaseQueryCounter();
        return parent::execute($input_parameters);
    }
}
?>