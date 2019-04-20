<?php
//$Id: logout.php 6806 2012-06-22 08:02:16Z smallduh $
if($logout){
	//session_start();
	//session_register("session_log_id");
	$session_log_id="";
	$session_tea_name="";
	Header("Location: index.php");
}
?>
