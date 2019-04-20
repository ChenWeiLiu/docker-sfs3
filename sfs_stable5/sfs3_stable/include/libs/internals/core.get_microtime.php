<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */
//$Id: core.get_microtime.php 9240 2018-05-04 03:25:04Z igogo $
/**
 * Get seconds and microseconds
 * @return double
 */
function smarty_core_get_microtime($params, &$smarty)
{
    $mtime = microtime();
    $mtime = explode(" ", $mtime);
    $mtime = (double)($mtime[1]) + (double)($mtime[0]);
    return ($mtime);
}


/* vim: set expandtab: */

?>
