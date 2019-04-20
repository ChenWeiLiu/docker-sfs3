<?php
                                                                                                                             
// $Id: header.php 5310 2009-01-10 07:57:56Z hami $
?>
<html>
<head>
<title>佈告欄</title>
<meta http-equiv="Content-Type" content="text/html; charset=big5" >
<style type="text/css">
<!--
A:visited
{
    COLOR: #4433aa;    
}
A:link
{
    COLOR: #4433dd;
}
A:active
{
    COLOR: #4433dd;
}
A:hover
{
    COLOR: #ff6666;
}

.td_sbody1 {
	font-size: 12px; 
	background-color: #F7F7F7 ;
	text-align: center;
}

-->
</style>
</head>
<?php
if ($show_calendar ==1 && $bk_id == "")
	echo "<body onload=init() bgcolor=\"#FFFFFF\" BACKGROUND=\"images/backg.gif\" >";
else
	echo "<body bgcolor=\"#FFFFFF\" BACKGROUND=\"images/backg.gif\" >";
?>