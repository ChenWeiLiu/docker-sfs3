<?php

// $Id: lunch.php 5514 2009-06-25 07:41:38Z infodaes $


include "config.php";
$direction=$_GET['direction']?$_GET['direction']:'up';
$scrolldelay=$_GET['scrolldelay']?$_GET['scrolldelay']:300;
$scrollamount=$_GET['scrollamount']?$_GET['scrollamount']:5;
$width=$_GET['width']?$_GET['width']:400;
$height=$_GET['height']?$_GET['height']:100;
$bgcolor=$_GET['bgcolor']?$_GET['bgcolor']:'white';


$sql="select * from lunchtb where pDate=curdate()";
$res=$CONN->Execute($sql) or user_error("Ū�����ѡI<br>$sql",256);
if($res->recordCount()){
$days_list=$res->GetRows();
	$main="<marquee direction=$direction scrolldelay=$scrolldelay scrollamount=$scrollamount behavior=scroll bgcolor=$bgcolor height=$height width=$width><center>��������\��".date()."</center><table border='1' cellpadding='3' cellspacing='0' style='border-collapse: collapse' bordercolor='#008000' width='100%'>";
	foreach($days_list as $day) {
		$pDesign=$day['pDesign'];
		$pFood=$day['pFood'];
		$pMenu='';
		$pMenu_array=explode("\r\n",$day['pMenu']);
		foreach($pMenu_array as $item) $pMenu.=$item."�B";
		$pMenu=substr($pMenu,0,-2);
		
		$pFruit=$day['pFruit'];
		$pPs=$day['pPs'];
		$main.="<tr bgcolor='#FFCC88'><td>�������t�ӡG$pDesign</td></tr><tr><td>�D���G$pFood<br>�ƭ��G$pMenu<br>���G�G$pFruit<br>�Ƶ��G$pPs</td></tr>";
	}
	$main.="</tr></table></marquee>";
	echo $main;
} else echo "<center><font color=red>���o�{<br>������\���СI</font></center>";

?>