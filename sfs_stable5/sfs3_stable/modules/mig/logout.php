<?php
                                                                                                                             
// $Id: logout.php 5310 2009-01-10 07:57:56Z hami $

 if ($_GET[logout])

   {

    session_start();
    session_destroy();	
    Header("Location: index.php");

  }

?>   
