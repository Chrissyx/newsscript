<?php
namespace com\chrissyx\newsscript;

/**
 * Various static functions and wrappers.
 *
 * @author Chrissyx <chris@chrissyx.com>
 * @package CHSNewsscript
 */
abstract class CoreFunctions
{
    /**
     * Hidden constructor to prevent instances of this class.
     */
    private function __construct()
    {
    }

    /**
     * Returns a value from superglobals in order GET and POST.
     *
     * @param string $key Key identifier for array access in superglobals
     * @return string Value from one of the superglobals or empty string if it was not found
     */
    public static function getFromGlobals($key)
    {
        return isset($_GET[$key]) ? $_GET[$key] : (isset($_POST[$key]) ? $_POST[$key] : '');
    }

    /**
     * Returns hash value for stated string.
     * If supported, blowfish or SHA-2 (SHA-512) will be used, some server dependent algorithm as fallback otherwise.
     *
     * @param string $string String to hash with blowfish, SHA-2 or other
     * @return string Hash value of string
     */
    public static function getHash($string)
    {
        return password_hash($string, PASSWORD_BCRYPT);
    }

    /**
     * Generates a 10-character random password incl. special chars.
     *
     * @return string Random password
     */
    public static function getRandomPass()
    {
        for($i=0,$newPass=''; $i<10; $i++)
            $newPass .= chr(mt_rand(33, 126));
        return $newPass;
    }

    /**
     * Extending PHP's {@link glob()} to handle invalid return values.
     *
     * @param string $pattern The pattern to use
     * @param int $flags Optional flags to use
     * @return array Matched files/directories or empty array
     */
    public static function glob($pattern, $flags=null)
    {
        return \glob($pattern, $flags) ?: array();
    }
}