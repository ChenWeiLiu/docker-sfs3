<?php
                                                                                                                             
// $Id: stud_age.php 6767 2012-05-23 08:45:54Z hami $


/*
=====================================================
程式：學生年齡統計表
ver1.0 -- prolin
ver2.0 -- hami
=====================================================
*/

/* 學務系統設定檔 */
include "stud_query_config.php";  

$sd=intval($_POST[sel_date]);
$date_arr=array("0"=>"9-1","1"=>"9-2");
$order_arr=array("stud_birthday"=>"生日","curr_class_num"=>"班級座號");
if ($_POST[order]=="") $_POST[order]="stud_birthday";

//歲數計算日期設定為9月2日
for ($i=6;$i<=18;$i++) $class_set[$i] =$date_arr[$sd];


//if (!isset($curr_year)) //預設學年
//$curr_year =  curr_year();
$curr_year = date('Y')-1911;

$sex_name = array("計","男","女") ;


//上次歲數
$pre_yy = '';
$sex_arr = array("1"=>"男生","2"=>"女生");

while (list($id,$val) = each($class_set)) {
	//轉換為年月日
	$yy = (date("Y")- $id)."-".$val ;
	
	if ($pre_yy =='' ) { //未滿計算
		$query = "select LEFT(curr_class_num,1) as bb ,stud_sex,count(stud_id)as cc from stud_base where stud_birthday >= '$yy' and (stud_study_cond='0' or stud_study_cond='15') group by bb,stud_sex";
		$result= $CONN->Execute($query) or die ($query);
		while (!$result->EOF) {
			$list_arr[$result->fields["bb"]][$result->fields["stud_sex"]][$id]=$result->fields["cc"];
			//人數加總
			$tol_all["all"]+=$result->fields["cc"];  
			$tol_all[$result->fields["stud_sex"]]+=$result->fields["cc"];   
			//歲數加總
			$tol_year["all"]+=$result->fields["cc"];  
			$tol_year[$id]+=$result->fields["cc"];  
			$tol_year_class[$result->fields["bb"]][$result->fields["stud_sex"]]+=$result->fields["cc"];  
			$tol_sex[$id][$result->fields["stud_sex"]]+=$result->fields["cc"];
			$result->MoveNext();
		}		
	}
	else {
			$query = "select LEFT(curr_class_num,1) as bb ,stud_sex,count(stud_id)as cc  from stud_base where  stud_birthday >= '$yy' and stud_birthday < '$pre_yy' and (stud_study_cond='0' or stud_study_cond='15') group by bb,stud_sex;";
		$result= $CONN->Execute($query) or die ($query);
		while (!$result->EOF) {
			$list_arr[$result->fields["bb"]][$result->fields["stud_sex"]][$id]=$result->fields["cc"];
			//人數加總
			$tol_all["all"]+=$result->fields["cc"];    
			$tol_all[$result->fields["stud_sex"]]+=$result->fields["cc"];
			
			//歲數加總
			$tol_year["all"]+=$result->fields["cc"];  
			$tol_year[$id]+=$result->fields["cc"];
			$tol_year_class[$result->fields["bb"]][$result->fields["stud_sex"]]+=$result->fields["cc"];  
			$tol_sex[$id][$result->fields["stud_sex"]]+=$result->fields["cc"];
			$result->MoveNext();
		}
	}
	
	$pre_yy = $yy;
	$last_id = $id;
	$last_val = $val;
}



//大於計算
$query = "select LEFT(curr_class_num,1) as bb,stud_sex,count(stud_id) as cc from stud_base where  stud_birthday < '$yy' and (stud_study_cond='0' or stud_study_cond='15') group by bb,stud_sex;";

$result= $CONN->Execute($query) or die ($query);
while (!$result->EOF) {
	$list_arr[$result->fields["bb"]][$result->fields["stud_sex"]][$last_id]=$result->fields["cc"];
	//人數加總
	$tol_all["all"]+=$result->fields["cc"];  
	$tol_all[$result->fields["stud_sex"]]+=$result->fields["cc"];
	//歲數加總
	$tol_year["all"]+=$result->fields["cc"];
	$tol_year[$last_id]+=$result->fields["cc"];
	$tol_year_class[$result->fields["bb"]][$result->fields["stud_sex"]]+=$result->fields["cc"];  
	$tol_sex[$last_id][$result->fields["stud_sex"]]+=$result->fields["cc"];
	$result->MoveNext();
}
ksort($list_arr);
reset($list_arr);

head("學生年齡統計");
print_menu($menu_p);

?>

<style>
<!--
.trr         { text-align: right; font-size: 10pt ; background-color: #FFFF00 ; }
.trr	a:link {color: red ; }
.trl         { text-align: left; font-size: 8pt ; background-color: #FDDDAB }
-->
</style>

<table border="0" cellpadding="2" cellspacing="0"  bordercolorlight="#333354" bordercolordark="#FFFFFF" CLASS="tableBg" width="100%">
	<form name ="base_form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
  <tr>
  <td class=title_mbody colspan=5 align=center>統計日期<font color=red>(月-日)</font>
<?php
$sel1=new drop_select();
$sel1->s_name="sel_date";
$sel1->id=$_POST[sel_date];
$sel1->arr=$date_arr;
$sel1->has_empty=false;
$sel1->is_submit=true;
$sel1->do_select();
?>
　細目依
<?php
$sel1=new drop_select();
$sel1->s_name="order";
$sel1->id=$_POST[order];
$sel1->arr=$order_arr;
$sel1->has_empty=false;
$sel1->is_submit=true;
$sel1->do_select();
?>排序<br>
<?php
$s_id=0;
//reset($class_set);
//while(list($k,$v)=each($class_set)) if ($s_id==0 && $tol_year[$k]>0) $s_id=$k;
//$e_id=$s_id;
reset($class_set);
while(list($k,$v)=each($class_set)) {
	if ($k>$s_id && $tol_year[$k]==0) $e_id=$k-1;
	if ($k>$s_id && $tol_year[$k]>0) $e_id=$k;
}
//echo $s_id."---".$e_id."<BR>";
//列出標題
reset($class_set);
echo "<table width = \"100%\" cellspacing=\"0\" border=\"1\" class=\"trr\"   bordercolor=#008080  bordercolorlight=#666666 bordercolordark=#FFFFFF>" ;
echo "<tr class=\"trl\"><td  colspan=\"2\">&nbsp</td><td>總計</td>" ; 
while (list($id,$val)= each($class_set)) {
	$t_year = $curr_year-$id;
	$temp_md = explode("-",$val);
	$temp_next = date ("Y-m-d", mktime (0,0,0,$temp_md[0],$temp_md[1]-1,$t_year+1912));
	
	$next_year  = explode("-",$temp_next);
	
	
	if ($ii++ == 0 )
		$temp_ymd = sprintf("未滿%d足歲<br>%d年%d月%d日<br>以後出生",$id,$t_year,$temp_md[0],$temp_md[1]);
	else if ($ii == ($e_id-$s_id+1))
		$temp_ymd = sprintf("超過%d歲<br>%d年%d月%d日<br>以前出生",$id-1,$t_year+1,$next_year[1],$next_year[2]);
	else
		$temp_ymd = sprintf("%d至未滿%d歲<br>%d年%d月%d日<br>%d年%d月%d日", $id-1,$id,$t_year,$temp_md[0],$temp_md[1],$t_year+1,$next_year[1],$next_year[2]);
	
	echo "<td nowrap align=center>$temp_ymd<br><a href=\"javascript:var aa=window.open('stud_age_explode.php?sdate=".(1911+$t_year)."-$val&edate=$temp_next&order=".$_POST[order]."', 'external', 'width=600,toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1,copyhistory=0')\"><img src=\"images/explode.png\" border=0></a></td>" ;
}
echo "</tr></form>" ;	

//總計部份
for ( $sex=0 ; $sex<=2 ; $sex++) {
	if ($sex==0)
		echo "<tr><td rowspan=\"3\" class=\"trl\">合計</td><td class=\"trl\">" .$sex_name[$sex]. "</td> <td>$tol_all[all]</td>" ;
	else
	echo "<tr><td class=\"trl\">" . $sex_name[$sex] . "</td> <td>$tol_all[$sex]</td>" ;
	reset($class_set);
	while (list($id,$val) = each($class_set)) {
		//總計部分
		if ($sex==0) {
			if ($tol_year[$id]) 
				echo "<td>".$tol_year[$id]. "</td>" ;  		
			else
				echo "<td>&nbsp</td> " ;  
		}
		//男女分計
		else {
			if ($tol_sex[$id][$sex]) 
				echo "<td>".$tol_sex[$id][$sex]. "</td>" ;  		
			else
				echo "<td>&nbsp</td> " ;  
		}
	}
	echo "</tr>" ;
}
    
//各年級部份
reset($list_arr);
while (list($id,$arr) = each($list_arr)) {
	for ( $sex=1 ; $sex<=2 ; $sex++) {
		if ($sex==1)
			echo "<tr ><td rowspan=\"2\" class=\"trl\" nowrap >" . $class_year[$id] . "級</td><td class=\"trl\">" .$sex_name[$sex]."</td><td>" . $tol_year_class[$id][$sex]. "</td>" ;
		else
			echo "<tr ><td class=\"trl\"  >" .$sex_name[$sex] ."</td><td>" . $tol_year_class[$id][$sex]. "</td>" ;
		reset($class_set);
		while (list($iid,$vval)= each($class_set)) {
			if ($list_arr[$id][$sex][$iid]) 
				echo "<td><a href=\"stud_age.php?q=show&y=$id&sex=$sex&age=$iid\">" . $list_arr[$id][$sex][$iid] . "</a></td>" ;
			else
				echo "<td>&nbsp</td>"  ;
		}	
		echo "</tr>" ;
	}
	    
} 
echo "</table><br>" ;

if ($temp2) {
	  echo $temp2 ;
}	

//檢查生日為 null 值
$query = "select stud_id,stud_name,curr_class_num from stud_base where (stud_study_cond='0' or stud_study_cond='15') and (stud_birthday is null  or stud_birthday = '0000-00-00' )";
$result= $CONN->Execute($query) or die ($query);
$temp ='';
while (!$result->EOF) {
	$temp .= $class_year[substr($result->fields["curr_class_num"],0,1)] . $class_name[substr($result->fields["curr_class_num"],1,2)]."班";
	$temp .= "-- 學號: ".$result->fields[0]. "-- 姓名: ".$result->fields[1]." <BR>";
	$result->MoveNext();
}
if ($temp<>'') {
	echo "未鍵入生日學生 :<br>";
	echo $temp;
}



?>
</td>
</tr>
</table>
<?php
foot();
?>
