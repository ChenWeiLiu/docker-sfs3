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
-->
</style>
</head>
<?php
if ($bg_color=="") $bg_color="#CCFFCC";
if ($show_calendar ==1 && $bk_id == "")
	echo "<body onload=init() bgcolor=\"$bg_color\" BACKGROUND=\"$bg_img\" >";
else
	echo "<body bgcolor=\"$bg_color\" BACKGROUND=\"$bg_img\" >";
?>
