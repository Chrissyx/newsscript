<?php
use \com\chrissyx\newsscript\Core;

function smarty_modifier_utf8_encode($string)
{
    if(!Core::getInstance()->isUtf8Locale())
        $string = utf8_encode($string);
    return $string;
}
?>