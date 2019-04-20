<?php
                                                                                                                             
// $Id: stud_status.php 8952 2016-08-29 02:23:59Z infodaes $

/*
=====================================================
程式：班級學生人數統計名冊(stud_status.php)  ver1.0
作者：何至剛  ufjgh368@ms14.hinet.net
日期：2001年4月14日
網站：思影藝術教育
http://www.hyes.tyc.edu.tw/~art
學校：桃園縣信義國小
http://www.hyes.tyc.edu.tw/
=====================================================
*/

/* 學務系統設定檔 */
include "../../include/config.php";  
include "../../include/sfs_case_PLlib.php";  

head();
?>
<center><br><font size=5><b>班級學生人數統計名冊</b></font> <a href=stud_status.php>詳細</a> <a href=stud_status.php?status=1>簡表</a><p>
<?php

if (!isset($curr_year)) //預設學年
	$curr_year =  curr_year();

if (!isset($curr_seme)) //預設學期
	$curr_seme = curr_seme();

$i=1;
for ($c=1 ; $c<=6 ; $c++) {
?>
<table width=80% BGCOLOR="#FDDDAB" border=1 cellSpacing=0 cellpadding=2  bordercolor=#008080  bordercolorlight=#666666 bordercolordark=#FFFFFF>
<tr><td align=center><b><?php echo $class_year[$c];?>級</b></td><td align=center><b>男生</b></td><td align=center><b>女生</b></td><td align=center><b>合計</b></td></tr>
<?php

	$curr_class_year = $c ;
	$num=0;
	$num1=0;
	$num2=0;
	$num_m=0;
	$num_w=0;
	$num_both=0;
	$num_class=0;

        for ($d=1 ; $d<=13 ; $d++) {
   		
		$curr_class_name = substr($d+100,1) ; //10班以上學校專用

		if (ereg("^[0-9]$",$curr_class_year) )
    			$class_num = ($curr_class_year-1) * 2 +$curr_seme  ;//目前班級座號欄位
		else
    			$class_num ="spe"; //特殊班級

    		$cuur_class = "class_num_".$class_num ; 
		$class_id=$class_year[$curr_class_year].$class_name[$curr_class_name]."班";
    
    		$sql_select = "select * from stud_base";
    		$sql_select .= " where stud_study_cond= 0 and stud_id like '".($curr_year-$curr_class_year+1)."%'";
    		$sql_select .= " and  $cuur_class like '".($curr_class_year.$curr_class_name)."%'"; 
		$sql = $sql_select." and stud_sex=1 ";
    		$sql1 = $sql_select." and stud_sex=2 ";
		$ret = mysql_query ($sql,$conID)or die ($sql);
		$num = mysql_num_rows($ret);
		$num_m=$num_m+$num;
    		$ret1 = mysql_query ($sql1,$conID)or die ($sql1);
		$num1 = mysql_num_rows($ret1);
		$num_w=$num_w+$num1;
		$num2 = $num+$num1;
		$num_both=$num_both+$num2;

		if ($num<>0 and $num1<>0) 
		{
	  		//$query =" replace into class_total set class_id='$class_id', man='$num' ,woman='$num1' ";
			//$result = mysql_query($query)or die ($query);
			$num_class=$num_class+1;
                      if ($status<>1){
			if ($i % 2 == 1 ) echo "<tr bgcolor=#FFFF80>";
			else echo "<tr>";
			echo sprintf("<td  align=center >%s</td><td  align=center >%s人</td><td align=center>%s人</td><td align=center>%s人</td></tr>",$class_id,$num,$num1,$num2);
			}
   		}
	}	
	echo sprintf("<tr><td  align=center ><b><font color=red>%s</font>班</b></td><td  align=center ><b><font color=red>%s</font>人</b></td><td align=center><b><font color=red>%s</font>人</b></td><td align=center><b><font color=red>%s</font>人</b></td></tr>",$num_class,$num_m,$num_w,$num_both);
	?>
	</table><br>
	<?php
	$num_total=$num_total+$num_class;
	$num_am=$num_am+$num_m;
	$num_aw=$num_aw+$num_w;
}
?>
<table width=80% BGCOLOR="#FDDDAB" border=1 cellSpacing=0 cellpadding=2  bordercolor=#008080  bordercolorlight=#666666 bordercolordark=#FFFFFF>
<tr><td align=center><b>全　校</b></td><td align=center><b>男生</b></td><td align=center><b>女生</b></td><td align=center><b>總計</b></td></tr>
<?
echo sprintf("<tr><td  align=center ><b><font color=red>%s</font>班</b></td><td  align=center ><b><font color=red>%s</font>人</b></td><td align=center><b><font color=red>%s</font>人</b></td><td align=center><b><font color=red>%s</font>人</b></td></tr>",$num_total,$num_am,$num_aw,$num_am+$num_aw);
echo "</table>";
echo "<p></center>";

foot();	

?>
