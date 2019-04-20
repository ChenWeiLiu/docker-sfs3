<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */
//$Id: modifier.count_characters.php 9240 2018-05-04 03:25:04Z igogo $
/**
 * Smarty count_characters modifier plugin
 *
 * Type:     modifier<br>
 * Name:     count_characteres<br>
 * Purpose:  count the number of characters in a text
 * @link http://smarty.php.net/manual/en/language.modifier.count.characters.php
 *          count_characters (Smarty online manual)
 * @author   Monte Ohrt <monte at ohrt dot com>
 * @param string
 * @param boolean include whitespace in the character count
 * @return integer
 */
function smarty_modifier_count_characters($string, $include_spaces = false)
{
    if ($include_spaces)
       return(strlen($string));

    return preg_match_all("/[^\s]/",$string, $match);
}

/* vim: set expandtab: */

?>
