<?php
namespace com\chrissyx\newsscript;

/**
 * Logging module with different priorities to log a message.
 *
 * @author Chrissyx <chris@chrissyx.com>
 * @package CHSNewsscript
 */
class Logger
{
    use Singleton;

    const ERROR = 0;
    const WARN = 1;
    const INFO = 2;
    const DEBUG = 3;

    /**
     * Name of file to use for logging.
     *
     * @var string Path and filename
     */
    private $logFile;

    /**
     * Log level to filter log messages based on this priority value.
     *
     * @var int Priority value
     */
    private $priority;

    /**
     * Detects and sets log filename.
     *
     * @return Logger New instance of this class
     */
    private function __construct()
    {
        $this->logFile = 'logs/' . date('Y-m-d') . '.log';
        $this->priority = self::DEBUG;
    }

    /**
     * Logs the given message with debug priority.
     *
     * @param string $message Message to write
     */
    public function debug($message)
    {
        $this->log(func_get_args(), self::DEBUG);
    }

    /**
     * Logs the given message with info priority.
     *
     * @param string $message Message to write
     */
    public function info($message)
    {
        $this->log(func_get_args(), self::INFO);
    }

    /**
     * Logs the given message with error priority.
     *
     * @param string $message Message to write
     */
    public function error($message)
    {
        $this->log(func_get_args(), self::ERROR);
    }

    /**
     * Logs the given message with warning priority.
     *
     * @param string $message Message to write
     */
    public function warn($message)
    {
        $this->log(func_get_args(), self::WARN);
    }

    /**
     * Logs the given message based on stated priority level,
     *
     * @param mixed $messageArgs Message with optional arguments to write
     * @param mixed $priority Priority of this message
     */
    private function log($messageArgs, $priority)
    {
        if($priority <= $this->priority)
            file_put_contents($this->logFile, '[' . $this->getPriority($priority) . '] ' . date('r') . ' [' . sprintf('%-15s', $_SERVER['REMOTE_ADDR']) .  ']: ' .  vsprintf($messageArgs[0], array_slice($messageArgs, 1)) . "\n", FILE_APPEND | LOCK_EX);
    }

    /**
     * Converts and returns a log level to string.
     *
     * @param int $priority Log level
     * @return string String representation
     */
    private function getPriority($priority)
    {
        switch($priority)
        {
            case self::ERROR:
            return 'ERROR';

            case self::WARN:
            return 'WARN ';

            case self::INFO:
            return 'INFO ';

            case self::DEBUG:
            return 'DEBUG';
        }
    }
}
?>