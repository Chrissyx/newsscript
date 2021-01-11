<?php
namespace com\chrissyx\newsscript;

/**
 * Wraps PHP's normal string functions to its Multibyte counterparts and defining the final feature set of Functions class with mbstring extension support enabled.
 *
 * @author Chrissyx <chris@chrissyx.com>
 * @package CHSNewsscript
 */
class Functions extends CoreFunctions
{
    /**
     * Wraps PHP's {@link stripos()} to Multibyte's {@link mb_stripos()}, if PHP >= 5.2.
     */
    public static function stripos($haystack, $needle, $offset=null)
    {
        return mb_stripos($haystack, $needle, $offset);
    }
}
?>