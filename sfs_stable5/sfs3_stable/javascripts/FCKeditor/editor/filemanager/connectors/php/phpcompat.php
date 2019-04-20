<?php
// $Id: phpcompat.php 5310 2009-01-10 07:57:56Z hami $
if ( !isset( $_SERVER ) ) {
    $_SERVER = $HTTP_SERVER_VARS ;
}
if ( !isset( $_GET ) ) {
    $_GET = $HTTP_GET_VARS ;
}
if ( !isset( $_FILES ) ) {
    $_FILES = $HTTP_POST_FILES ;
}

if ( !defined( 'DIRECTORY_SEPARATOR' ) ) {
    define( 'DIRECTORY_SEPARATOR',
        strtoupper(substr(PHP_OS, 0, 3) == 'WIN') ? '\\' : '/'
    ) ;
}
