<?php
namespace com\chrissyx\newsscript;

/**
 * Wraps PHP's normal string functions to itself and defining the final feature set of Functions class with mbstring extension support disabled.
 *
 * @author Chrissyx <chris@chrissyx.com>
 * @package CHSNewsscript
 */
class Functions extends CoreFunctions
{
    /**
     * PHP's {@link strripos()}.
     */
    public static function strripos($haystack, $needle, $offset=null)
    {
        return \strripos($haystack, $needle, $offset);
    }
}
?>