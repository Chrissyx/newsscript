<?php
namespace com\chrissyx\newsscript;

/**
 * Deploys the script core.
 *
 * @author Chrissyx <chris@chrissyx.com>
 * @package CHSNewsscript
 */
require('vendor/autoload.php');
require('core/Constants.php');
require('core/CoreFunctions.php');
require('core/Interfaces.php');
require('core/Traits.php');
require('core/Core.php');

/**
 * Runs given function in proper working directory.
 *
 * @param callback $function Passed function to execute
 */
function runInCwd($function)
{
    //Fix working directory for upcoming relative paths
    $cwd = getcwd();
    chdir(__DIR__);
    //Continue normally with passed code
    $function();
    //Revert changed working directory
    chdir($cwd);
}

runInCwd(function()
{
    //Init core with autoloader
    Core::getInstance();
});
?>