<?php
namespace com\chrissyx\newsscript;
use \InvalidArgumentException as InvalidArgumentException;

/**
 * Template for every implementing module which can be called "directly" from an user.
 * These public accessible modules have a corresponding language file.
 *
 * @author Chrissyx <chris@chrissyx.com>
 * @package CHSNewsscript
 */
abstract class PublicModule
{
    use Singleton;

    /**
     * Loads language file for callee.
     *
     * @return PublicModule Instance of this class
     */
    protected function __construct()
    {
        try
        {
            Language::getInstance()->parseFile(basename(debug_backtrace(null, 1)[0]['file'], '.php'));
        }
        catch(InvalidArgumentException $e)
        {
            Logger::getInstance()->error(get_class($e) . ': ' . $e->getMessage());
        }
    }

    /**
     * Called publicly from an user.
     */
    public abstract function publicCall();
}
?>