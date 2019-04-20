<?php
//$Id: view_1.php 8952 2016-08-29 02:23:59Z infodaes $
include "stud_year_config.php";
//認證
##################取資料函式###########################
function get_order2($SQL) {
	//項目,方式,(第幾組,每組人數,排序依)
	global $CONN ;
$rs=$CONN->Execute($SQL) or die($SQL);
$arr = $rs->GetArray();
return $arr ;
}
###############################################
sfs_check();

//秀出網頁布景標頭
if($_POST[sbase]!=''  ) {
	$sbase=$_POST[sbase];
	($_POST[check_cond]=='Y') ? $add_sql=" and stud_study_cond='0' ":$add_sql="";
	$SQL1="select stud_id ,student_sn ,stud_name ,stud_sex ,stud_study_year ,curr_class_num ,stud_study_cond  from stud_base where curr_class_num  like '$sbase%' $add_sql order by curr_class_num ";
	$arr1=get_order2($SQL1);
	}
if($_POST[syear]!='' && $_POST[sclass]!='') {
	$syear=$_POST[syear];
	$sclass=$_POST[sclass];
	($_POST[check_cond]=='Y') ? $add_sql=" and stud_study_cond='0' ":$add_sql="";
	$SQL2="select  a.student_sn, a.stud_name, a.curr_class_num, b.seme_year_seme, b.seme_class, b.seme_class_name, b.seme_num from  stud_base a , stud_seme b where  a.student_sn= b.student_sn  and b.seme_year_seme='$syear' and b.seme_class='$sclass' $add_sql order by  b.seme_num ";
	$arr2=get_order2($SQL2);
	} 

head("學籍查核1");
print_menu($menu_p);
?>
<style type="text/css">
.blue{color:blue}
.red{color:red}
</style>

<table border=0 width='100%' style='font-size:10pt;'  cellspacing=0 cellpadding=0 bgcolor=silver>
<TR bgcolor='white'><TD colspan=2>
<FONT SIZE='' COLOR='red'>※本程式僅供學籍資料核對之用。</FONT></td></tr>

<FORM ACTION="<?=$PHP_SELF?>" METHOD="POST" name=f1>
<TR bgcolor='white'><TD width=50% style='color:#800000;'>
<B>查學籍資料表</B>：班別<INPUT TYPE="TEXT" NAME="sbase" VALUE="<?=$_POST[sbase]?>" Size="5">
<INPUT TYPE="SUBMIT" NAME="" VALUE="填好送出" style="border-style: groove;border-width:1px: groove;background-color:#FFFFFF;font-size:12px;Padding-left:0 px;Padding-right:0 px;">
<BR><?php if ($_POST[check_cond]=='Y'){
	echo "<INPUT TYPE='checkbox' NAME='check_cond' value='Y' checked>僅例在籍";}
	else{
	echo "<INPUT TYPE='checkbox' NAME='check_cond' value='Y' >僅例在籍";}

?>
<FONT COLOR='blue'>(1年7班國小107,國中707)</FONT>
</TD>
<TD width=50% style='color:#800000;'>
<B>查學期資料表</B>：
學年度<INPUT TYPE="TEXT" NAME="syear" VALUE="<?=$_POST[syear]?>" Size="5">
班級<INPUT TYPE="TEXT" NAME="sclass" VALUE="<?=$_POST[sclass]?>" Size="5">
<INPUT TYPE="SUBMIT" NAME="" VALUE="填好送出" style="border-style: groove;border-width:1px: groove;background-color:#FFFFFF;font-size:12px;Padding-left:0 px;Padding-right:0 px;">
</TD></TR></form><tr bgcolor=white><TD valign=top>
<!--學籍資料-->
<table border=0 width='100%' style='font-size:11pt;'  cellspacing=1 cellpadding=0  bgcolor=silver>
<tr bgcolor=white>
	<td colspan=7 style='font-size:9pt;color:silver;'>stud_id學號,student_sn流水號,stud_name姓名,stud_sex性別<BR>
	,stud_study_year入學年,curr_class_num,目前年班,stud_study_cond就學狀況</td>
</tr>
<tr bgcolor=#EFEFEF>
	<td>學號</td>
	<td>流水號</td>
	<td>姓名</td>
	<td>性別</td>
	<td>入學年</td>
	<td>目前年班</td>
	<td>就學狀況</td>
</tr>
<?
for($i=0; $i<count($arr1); $i++) {
($arr1[$i][stud_sex]=='1' )? $arr1[$i][stud_sex]="<B class=blue>".$arr1[$i][stud_sex]."</B>":$arr1[$i][stud_sex]="<B class=red>".$arr1[$i][stud_sex]."</B>";
($arr1[$i][stud_study_cond]!='0' )? $arr1[$i][stud_study_cond]="<B class=red>".$arr1[$i][stud_study_cond]."</B>":"";

echo "<tr bgcolor=white>
	<td>".$arr1[$i][stud_id]."</td>
	<td>".$arr1[$i][student_sn]."</td>
	<td>".$arr1[$i][stud_name]."</td>
	<td>".$arr1[$i][stud_sex]."</td>
	<td>".$arr1[$i][stud_study_year]."</td>
	<td>".$arr1[$i][curr_class_num]."</td>
	<td>".$arr1[$i][stud_study_cond]."</td>
</tr>";
}


?>
</table>
</TD><TD valign=top>
<!--學期資料-->
<table border=0 width='100%' style='font-size:11pt;'  cellspacing=1 cellpadding=0 bgcolor=silver>
<tr bgcolor=white>
<td colspan=7 style='font-size:9pt;color:silver;'>student_sn流水號,stud_name姓名,curr_class_num目前年班,<BR>
seme_year_seme學期,seme_class班級,seme_class_name班名,seme_num座號
</td>
</tr>
<tr bgcolor=#EFEFEF>
	<td>a流水號</td>
	<td>a姓名</td>
	<td>a目前年班</td>
	<td>b學期</td>
	<td>b班級</td>
	<td>b班名</td>
	<td>b座號</td>
</tr>

<?
for($i=0; $i<count($arr2); $i++) {
echo "<tr bgcolor=white>
	<td>".$arr2[$i][student_sn]."</td>
	<td>".$arr2[$i][stud_name]."</td>
	<td>".$arr2[$i][curr_class_num]."</td>
	<td>".$arr2[$i][seme_year_seme]."</td>
	<td>".$arr2[$i][seme_class]."</td>
	<td>".$arr2[$i][seme_class_name]."</td>
	<td>".$arr2[$i][seme_num]."</td>
</tr>";
}
?>
</table>


</TD></TR></TABLE>
<?
//佈景結尾
foot();
?>
