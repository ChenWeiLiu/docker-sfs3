<?php
                                                                                                                             
// $Id: stud_kind_rep.php 6933 2012-10-08 07:39:13Z infodaes $

/*
=====================================================
程式：學生身分別統計表
ver1.0 -- hami
=====================================================
*/

/* 學務系統設定檔 */
include "stud_query_config.php";  

//認證檢查
sfs_check();


if (!isset($curr_year)) //預設學年
	$curr_year =  curr_year();

$sex_name = array("計","男","女") ;
$sex_arr = array("1"=>"男生","2"=>"女生");
$temp_arr = array();
//取得學生身分別代號
$stud_kind_arr = stud_kind();
while (list($id,$val) = each($stud_kind_arr)) {
	if ($id ==0 )
		$query = "select LEFT(curr_class_num,1) as class_year ,stud_sex , count(*) as tol from stud_base where stud_study_cond=0 and (stud_kind like '%,$id,%' or stud_kind ='0') group by class_year , stud_sex ";
	else
		$query = "select LEFT(curr_class_num,1) as class_year ,stud_sex , count(*) as tol from stud_base where stud_study_cond=0 and (stud_kind like '%,$id,%' or stud_kind ='$id') group by class_year ,stud_sex ";
	//echo $query ."<br>";
	$res = $CONN->Execute($query) or die($query);
	while (!$res->EOF) {
	        
	        $sex = $res->fields[stud_sex] ;
	        //echo "$id  - " . $res->fields[class_year] ."- $sex -" . $res->fields[tol] ."<br>" ; 
		//身分別/年級/男生人數
		$temp_arr[$id][$res->fields[class_year]][$sex] = $res->fields[tol];
		
		//身分別/年級/女生人數
		//$temp_arr[$id][$res->fields[class_year]]["girl"] = $res->fields[girl];
		
		$res->MoveNext();	
	}

}





head("學生身分統計");

print_menu($menu_p);

?>

<style>
<!--
.trr         { text-align: right; font-size: 10pt ; background-color: #FFFF00 }
.trl         { text-align: left; font-size: 8pt ; background-color: #FDDDAB }
-->
</style>

<table border="0" cellpadding="2" cellspacing="0"  bordercolorlight="#333354" bordercolordark="#FFFFFF" CLASS="tableBg" width="100%">
  <tr>
  <td class=title_mbody colspan=5 align=center >
<br>
<?php
echo "<table width = \"100%\" cellspacing=\"0\" border=\"1\" class=\"trr\"   bordercolor=#008080  bordercolorlight=#666666 bordercolordark=#FFFFFF>" ;

echo "<tr class=\"trl\"><td rowspan=2 align=center>學生身分別</td>";
while(list($id,$val) = each($class_year)) {
	echo "<td colspan=2 align=center>$val 級</td>";
}
echo "</tr>";

reset($class_year);
echo "<tr class=\"trl\">";
while(list($id,$val) = each($class_year)) {
	echo "<td align=center>男生</td><td align=center>女生</td>";
}
echo "</tr>";

ksort($temp_arr);
reset($temp_arr);

	while (list($id,$arr1)=each($temp_arr)){
		
		echo "<tr ><td><a href='csv_export.php?type_id=$id'>{$stud_kind_arr[$id]}</a> <a href=\"javascript:var aa=window.open('stud_kind_explode.php?stud_kind=$id', 'external', 'width=600,toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=1,resizable=1,copyhistory=0')\"><img src=\"images/explode.png\" border=0></a></td>\n";
		reset($class_year);
		while(list($i,$val) = each($class_year)) {
			if ($temp_arr[$id][$i][1])
				echo "<td>".$temp_arr[$id][$i][1]."</td>";	
			else
				echo "<td>&nbsp;</td>";
				
			if ($temp_arr[$id][$i][2])
				echo "<td>".$temp_arr[$id][$i][2]."</td>";	
			else
				echo "<td>&nbsp;</td>";	
			
		}
		echo "</tr>";
		
		
	}

?>
<tr>
<td colspan=19><center>
<?php
//取得學生身分別代號
$stud_kind_arr = implode (",",stud_kind());
//echo $stud_kind_arr."|<BR>\n";
echo "<A HREF='./stud_kind_all.php'>所有類別的csv檔下載</A>\n";
?>
</td>
</tr>
</table>
</td>
</tr>
</table>
<?php
foot();
?>
