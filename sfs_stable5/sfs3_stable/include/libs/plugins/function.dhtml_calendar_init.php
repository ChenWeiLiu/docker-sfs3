<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {dhtml_calendar_init} function plugin
 *
 * Type:     function<br>
 * Name:     dhtml_calendar_init<br>
 * Purpose:  interface to jscalendar-1.0
 * Author:   boots
 * Version:  1.1.5
 * @link http://www.dynarch.com/projects/calendar/ {dhtml calendar}
 *          (dynarch.com)
 * @param array
 * @param Smarty
 * @return string
 */
function smarty_function_dhtml_calendar_init($params, &$smarty)
{
    $defaults = array(
      'css'       => 'calendar-win2k-1.css'
    , 'src'       => 'calendar_stripped.js'
    , 'lang'      => 'lang/calendar-big5.js'
    , 'setup_src' => 'calendar-setup_stripped.js'
    );

    foreach( $defaults as $field=>$default ) {
        $_field = '_' . $field;

        if( array_key_exists( $field, $params ) ) {
            $$_field = ( empty( $params[$field] ) )
                ? $default 
                : $params[$field];

        } else {
            $$_field = $default;
        }
    }

$_out = <<<EOF
    <link rel="stylesheet" type="text/css" media="all" href="{$_css}">
    <script src="{$_src}"></script>
    <script src="{$_lang}"></script>
    <script src="{$_setup_src}"></script>
EOF;

    return( $_out );
}
?>
