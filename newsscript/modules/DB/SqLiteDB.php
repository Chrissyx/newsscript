<?php
namespace com\chrissyx\newsscript;
use \PDOException as PDOException;

/**
 * Concrete DB implementation using SQLite3 as data storage.
 *
 * @author Chrissyx <chris@chrissyx.com>
 * @package CHSNewsscript
 */
class SqLiteDB extends DB
{
    /**
     * Connects to the SQLite3 database file.
     *
     * @return SqLiteDB New instance of this class
     */
    function __construct()
    {
        try
        {
            parent::__construct('sqlite:modules/DB/db.sq3');
        }
        catch(PDOException $e)
        {
            exit($e->getMessage());
        }
    }
}
?>