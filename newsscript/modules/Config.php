<?php
namespace com\chrissyx\newsscript;
use \PDO as PDO;
use \InvalidArgumentException as InvalidArgumentException;

/**
 * Generic configuration getter and setter.
 *
 * @author Chrissyx <chris@chrissyx.com>
 * @package CHSNewsscript
 */
class Config
{
    use Singleton;

    /**
     * Loaded configuration identifiers and their values.
     *
     * @var array Loaded configuration set
     */
    private $configSet = array();

    /**
     * Loads the configuration set.
     *
     * @return Config New instance of this class
     */
    private function __construct()
    {
        if(DB::getInstance()->exec('CREATE TABLE IF NOT EXISTS config ("key" VARCHAR(127) NOT NULL, "value" VARCHAR(511), PRIMARY KEY ("key"))') != 0)
            Logger::getInstance()->info('Configuration table created');
        foreach(DB::getInstance()->query('SELECT * FROM config')->fetchAll(PDO::FETCH_ASSOC) as $curConfig)
            $this->configSet[$curConfig['key']] = unserialize($curConfig['value']);
        //Set default values
        if(empty($this->configSet))
        {
            $this->configSet = array('log_level' => Logger::WARN,
                'smarty_dir' => '',
                'tpl_dir' => 'default',
                'clickjacking' => true,
                'output_compression' => true);
            $preparedStmt = DB::getInstance()->prepare('INSERT INTO config VALUES(:key, :value)');
            foreach($this->configSet as $curKey => $curValue)
            {
                $preparedStmt->bindValue(':key', $curKey, PDO::PARAM_STR);
                $preparedStmt->bindValue(':value', serialize($curValue), PDO::PARAM_STR);
                $preparedStmt->execute();
            }
            unset($preparedStmt);
        }
        PlugIns::getInstance()->callHook(PlugIns::HOOK_CONFIG_INIT);
    }

    /**
     * Returns a single configuration value.
     *
     * @param string $key Identifier of config value
     * @return string Requested config value
     * @throws InvalidArgumentException If config value was not found
     */
    public function getConfigVal($key)
    {
        if(isset($this->configSet[$key]))
            return $this->configSet[$key];
        else
            throw new InvalidArgumentException('Config value ' . $key . ' does not exist!');
    }

    /**
     * Sets a single configuration value. <b>Existing data will be overwritten!</b>
     *
     * @param string $key Identifier to access the value
     * @param mixed $value Configuration entry
     * @param bool $persist Store all config values to the config file
     */
    public function setConfigVal($key, $value, $persist=false)
    {
        $this->configSet[$key] = $value;
        if($persist)
        {
            $preparedStmt = DB::getInstance()->prepare('UPDATE config SET "value" = :value WHERE "key" = :key');
            foreach($this->configSet as $curKey => $curValue)
            {
                $preparedStmt->bindValue(':key', $curKey, PDO::PARAM_STR);
                $preparedStmt->bindValue(':value', serialize($curValue), PDO::PARAM_STR);
                $preparedStmt->execute();
            }
            unset($preparedStmt);
        }
    }
}
?>