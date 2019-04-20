<?php
header('Content-type: text/html; charset=utf-8');
include_once ('config.php');
include_once ('my_functions.php');
?>
<html>
<head>
<title>校園MSN
<?php

if ($_SESSION['MSN_LOGIN_ID']!="") {
 //$MyName=big52utf8(get_teacher_name_by_id($_SESSION['MSN_LOGIN_ID']));
 	 mysql_query("SET NAMES 'utf8'");
   $query="select * from sc_msn_online where teach_id='".$_SESSION['MSN_LOGIN_ID']."'";
   $result=mysql_query($query);
	 $row=mysql_fetch_array($result,1);
 echo "-[".$row['name']."]-上線";
}else{
 echo "- 未登入";
}
?>	
	</title>

</head>

<frameset framespacing="0" border="0" frameborder="0" rows="*,170,25">
	<frame name="main1" src="main_top.php" scrolling="no"  target="contents" noresize>
	<frame name="main2" src="main_buttom.php" scrolling="no"  target="contents" noresize>
	<frameset cols="300,*">
		<frame name="menu" src="main_menu.php" target="newopen"  scrolling="no" noresize>
		<frame name="chk" src="main_check.php"  scrolling="no" noresize>
	</frameset>

	<noframes>
	<body>
	<p>此網頁使用框架，但是您的瀏覽器不支援框架。</p>

	</body>
	</noframes>
</frameset>

</html>
