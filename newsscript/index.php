<?php
namespace com\chrissyx\newsscript;

/**
 * Runs CHS Newsscript admin panel.
 *
 * @author Chrissyx <chris@chrissyx.com>
 * @package CHSNewsscript
 */
require('core.php');

$_GET['action'] = 'adminIndex';
Core::getInstance()->run();
?>