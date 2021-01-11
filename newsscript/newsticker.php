<?php
namespace com\chrissyx\newsscript;

/**
 * Displays newsticker.
 *
 * @author Chrissyx <chris@chrissyx.com>
 * @package CHSNewsscript
 */
require_once('core.php');

runInCwd(function()
{
    #Newsticker::getInstance()->publicCall();
});
?>