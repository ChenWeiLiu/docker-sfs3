<?php

// $Id: config.php 5310 2009-01-10 07:57:56Z hami $

require_once "./module-cfg.php";

include_once "../../include/config.php";

function creat_code($level="",$many_char=""){		
		$number="1234567890";
		$small="abcdefghijklmnopqrstuvwxyz";
		$big="ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$special="!@$%^&*()_+|=-[]{}?/";
		$much=0;
		if($level=="") $level=4;
		if($level>="1") {$passwordsource.=$number; $much=$much+10;}
		if($level>="2") {$passwordsource.=$small; $much=$much+26;}
		if($level>="3") {$passwordsource.=$big; $much=$much+26;}
		if($level>="4") {$passwordsource.=$special; $much=$much+21;}
		if($many_char=="") $many_char=10;
		for ($i=0;$i<$many_char;$i++){
			srand ((double) microtime() * 1000000);
			$value=rand(0,$much-1);
			$password[$i]=substr($passwordsource,$value,1);
		}
		$password=implode("",$password);
	return $password;	
}	
?>

