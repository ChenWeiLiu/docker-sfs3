<?php

//$Id: stud_eduh_report.php 6515 2011-09-13 03:08:33Z infodaes $
include "config.php";
sfs_check();

//秀出網頁
head("家庭狀況統計");

//目前學年學期
$this_seme_year_seme = sprintf("%03d%d",curr_year(),curr_seme());
$c_curr_seme = $_REQUEST[c_curr_seme];
if ($c_curr_seme=='')
	$c_curr_seme = $this_seme_year_seme;
	
//顯示學期
$class_seme_p = get_class_seme(); //學年度	
$upstr = "<select name=\"c_curr_seme\" onchange=\"this.form.submit()\">\n";
while (list($tid,$tname)=each($class_seme_p)){
	if ($c_curr_seme== $tid)
      		$upstr .= "<option value=\"$tid\" selected>$tname</option>\n";
      	else
      		$upstr .= "<option value=\"$tid\">$tname</option>\n";
}
$upstr .= "</select><br>"; 
	

//選單連結字串
$linkstr = "stud_id=$stud_id&c_curr_class=$c_curr_class&c_curr_seme=$c_curr_seme";
//模組選單
print_menu($menu_p,$linkstr);

$selection=$_POST['selection']?$_POST['selection']:'父母關係';

$curr_year_seme = sprintf("%03d%d",curr_year(),curr_seme());

$status_arr=array();
/*
$status_arr['父母關係']="SELECT sse_relation as `關係`,count(*) as `統計` FROM stud_seme_eduh WHERE seme_year_seme='$c_curr_seme' GROUP BY sse_relation";
$status_arr['家庭類型']="SELECT sse_family_kind as `家庭類型`,count(*) as `統計` FROM stud_seme_eduh WHERE seme_year_seme='$c_curr_seme' GROUP BY sse_family_kind";
$status_arr['家庭氣氛']="SELECT sse_family_air as `家庭氣氛`,count(*) as `統計` FROM stud_seme_eduh WHERE seme_year_seme='$c_curr_seme' GROUP BY sse_family_air";
$status_arr['父管教方式']="SELECT sse_farther as `父管教方式`,count(*) as `統計` FROM stud_seme_eduh WHERE seme_year_seme='$c_curr_seme' GROUP BY sse_farther";
$status_arr['母管教方式']="SELECT sse_mother as `母管教方式`,count(*) as `統計` FROM stud_seme_eduh WHERE seme_year_seme='$c_curr_seme' GROUP BY sse_mother";
$status_arr['居住情形']="SELECT sse_live_state as `居住情形`,count(*) as `統計` FROM stud_seme_eduh WHERE seme_year_seme='$c_curr_seme' GROUP BY sse_live_state";
$status_arr['經濟狀況']="SELECT sse_rich_state as `經濟狀況`,count(*) as `統計` FROM stud_seme_eduh WHERE seme_year_seme='$c_curr_seme' GROUP BY sse_rich_state";
*/


$status_arr['父母關係']="SELECT a.sse_relation as `關係`,sum((1-abs(sign(b.stud_sex-1)))) as `男`,sum((1-abs(sign(b.stud_sex-2)))) as `女` FROM stud_seme_eduh a,stud_base b WHERE a.stud_id=b.stud_id and a.seme_year_seme='$c_curr_seme' GROUP BY sse_relation";
$status_arr['家庭類型']="SELECT sse_family_kind as `家庭類型`,sum((1-abs(sign(b.stud_sex-1)))) as `男`,sum((1-abs(sign(b.stud_sex-2)))) as `女` FROM stud_seme_eduh a,stud_base b WHERE a.stud_id=b.stud_id and a.seme_year_seme='$c_curr_seme' GROUP BY sse_family_kind";
$status_arr['家庭氣氛']="SELECT sse_family_air as `家庭氣氛`,sum((1-abs(sign(b.stud_sex-1)))) as `男`,sum((1-abs(sign(b.stud_sex-2)))) as `女` FROM stud_seme_eduh a,stud_base b WHERE a.stud_id=b.stud_id and a.seme_year_seme='$c_curr_seme' GROUP BY sse_family_air";
$status_arr['父管教方式']="SELECT sse_farther as `父管教方式`,sum((1-abs(sign(b.stud_sex-1)))) as `男`,sum((1-abs(sign(b.stud_sex-2)))) as `女` FROM stud_seme_eduh a,stud_base b WHERE a.stud_id=b.stud_id and a.seme_year_seme='$c_curr_seme' GROUP BY sse_farther";
$status_arr['母管教方式']="SELECT sse_mother as `母管教方式`,sum((1-abs(sign(b.stud_sex-1)))) as `男`,sum((1-abs(sign(b.stud_sex-2)))) as `女` FROM stud_seme_eduh a,stud_base b WHERE a.stud_id=b.stud_id and a.seme_year_seme='$c_curr_seme' GROUP BY sse_mother";
$status_arr['居住情形']="SELECT sse_live_state as `居住情形`,sum((1-abs(sign(b.stud_sex-1)))) as `男`,sum((1-abs(sign(b.stud_sex-2)))) as `女` FROM stud_seme_eduh a,stud_base b WHERE a.stud_id=b.stud_id and a.seme_year_seme='$c_curr_seme' GROUP BY sse_live_state";
$status_arr['經濟狀況']="SELECT sse_rich_state as `經濟狀況`,sum((1-abs(sign(b.stud_sex-1)))) as `男`,sum((1-abs(sign(b.stud_sex-2)))) as `女` FROM stud_seme_eduh a,stud_base b WHERE a.stud_id=b.stud_id and a.seme_year_seme='$c_curr_seme' GROUP BY sse_rich_state";

foreach($status_arr as $key=>$value){
	$menu.="<input type='radio' value='$key' name='selection' onclick='this.form.submit()'".($selection==$key?' checked':'').">$key<BR>";
}


$sql=$status_arr[$selection];
$res=$CONN->Execute($sql) or user_error("執行統計分析失敗！<br>$sql",256);


$showdata="<table border='2' cellpadding='8' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111' id='AutoNumber1'>";
$showdata.="<tr bgcolor=$Tr_BGColor>";
for($i=0;$i<$res->FieldCount();$i++){
	$r=$res->fetchfield($i);
	$showdata.="<td align='center'>".$r->name."</td>";	
}
$showdata.="<td>統計</td><td>CSV輸出</td></tr>";

$selection2=str_replace("父管", "管",$selection);
$selection2=str_replace("母管", "管",$selection2);
$tran_arr=sfs_text($selection2);

//print_r($tran_arr);

while(!$res->EOF) {
	$showdata.="<tr align='center'>";
	
	for($i=0;$i<$res->FieldCount();$i++){
		if($i) $target=$res->fields[$i]; else $target=$tran_arr[$res->fields[$i]];
		$showdata.="<td>$target</td>";
	}
	$key=$res->fields[0];
	$value=$tran_arr[$key];
	$summary=$res->fields[1]+$res->fields[2];
	$showdata.="<td>$summary</td><td align='center'><a href='csv_export.php?item=$selection&key=$key&value=$value&semester=$c_curr_seme'><img src='images/csv.png' border=0></a></td></tr>";
	$res->MoveNext();
}
$showdata.="</table>";

$main="<table cellpadding='5' cellspacing='5'>
	<form name='myform' method='post' action='$_SERVER[PHP_SELF]'>
	<tr><td valign='top'>$upstr$menu</td><td valign='top'>$showdata</td></tr></table></form>";
echo $main;
foot();

?>
