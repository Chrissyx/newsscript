<?php
namespace com\chrissyx\newsscript;

/**
 * Provides singleton pattern. The constructor should obviously be private or at least protected.
 */
trait Singleton
{
    /**
     * Returns the singleton instance of this class.
     *
     * @return Singleton Instance of this class
     */
    public static function getInstance()
    {
        static $instance = null;
        //Late static binding magic *zomg*
        return $instance ?: $instance = new static();
    }
}

/**
 * Provides the $mode variable.
 */
trait Mode
{
    /**
     * Contains mode to execute.
     *
     * @var string Mode to execute
     */
    private $mode;
}
?>