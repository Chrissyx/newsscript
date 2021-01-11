<?php
namespace com\chrissyx\newsscript;

/**
 * Displays news entries.
 *
 * @author Chrissyx <chris@chrissyx.com>
 * @package CHSNewsscript
 */
require_once('core.php');

runInCwd(function()
{
    News::getInstance()->publicCall();
});
?>