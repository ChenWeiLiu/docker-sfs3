<?php

// $Id: class_top.php 7502 2013-09-09 01:01:58Z chiming $

/* 取得設定檔 */
include "config.php";

//算PR值函式---- by 和東 王麒富
function show_PR($seme_year_seme,$year_name,$j){
	global $CONN;
	$sql = "select count(*) from stud_seme where seme_year_seme='$seme_year_seme' and seme_class like '$year_name%' ";
	$rs=$CONN->Execute($sql);
	list($total)=$rs->FetchRow();
	if($total<100){
	$PR = intval(98*($total-$j)/($total-1))+1;
	}else{
	$PR = intval(99*($total-$j)/$total)+1;
	}
	return $PR;
}

sfs_check();

//取得傳遞資料
$year_seme=($_POST['year_seme'])?$_POST['year_seme']:$_GET['year_seme'];
$year_name=($_POST['year_name'])?$_POST['year_name']:$_GET['year_name'];
$act=$_POST['act'];
$sel=$_POST['sel'];
$rate=$_POST['rate'];
$subject=$_POST['subject'];
$class=$_POST['class'];
$tops=$_POST['tops'];

//秀出網頁
head("各班獎勵名單");
print_menu($student_menu_p);

echo "<table border=0 cellspacing=1 cellpadding=2 width=100% bgcolor=#cccccc><tr><td bgcolor='#FFFFFF'>";

//若有選擇學年學期，進行分割取得學年及學期
if(!empty($year_seme)){
	$ys=explode("_",$year_seme);
	$sel_year=$ys[0];
	$sel_seme=$ys[1];
} else {
	$sel_year = curr_year(); //目前學年
	$sel_seme = curr_seme(); //目前學期
	$year_seme=$sel_year."_".$sel_seme;
}
$seme_year_seme=sprintf("%03d%01d",$sel_year,$sel_seme);

//學期選單
$col_name="year_seme";
$id=$year_seme;    
$show_year_seme=select_year_seme($id,$col_name);
$year_seme_menu="
	<form name='form0' method='post' action='{$_SERVER['PHP_SELF']}'>
		<select name='$col_name' onChange='jumpMenu0()'>
			$show_year_seme
		</select>
	</form>";
	
//年級選單
if($year_seme){
	$col_name="year_name";
	$id=$year_name;
	$show_class_year=select_school_class($id,$col_name,$sel_year,$sel_seme);
	$class_year_menu="
	<form name='form1' method='post' action='{$_SERVER['PHP_SELF']}'>
		<select name='$col_name' onChange='jumpMenu1()'>
			$show_class_year
		</select>
		<input type='hidden' name='year_seme' value='$year_seme'>
	</form>";
}

//班級選單
//if($year_seme && $year_name){
//	$col_name="me";
//	$id=$me;
//	$show_class_year_name=select_school_class_name($year_name,$id,$col_name,$sel_year,$sel_seme);
//	$class_year_name_menu="
//	<form name='form2' method='post' action='{$_SERVER['PHP_SELF']}'>
//		<select name='$col_name' onChange='jumpMenu2()'>
//			$show_class_year_name
//		</select>
//		<input type='hidden' name='year_name' value='$year_name'>
//		<input type='hidden' name='year_seme' value='$year_seme'>
//	</form>";
//}

$menu="
	<table cellspacing=0 cellpadding=0>
	<tr>
	<td>$year_seme_menu</td><td>$class_year_menu</td>
	</tr>
	</table>";
echo $menu;

if ($year_seme && $year_name) {
	$sql="select distinct seme_class from stud_seme where seme_year_seme='$seme_year_seme' and seme_class like '$year_name%' order by seme_class";
	$rs=$CONN->Execute($sql);
	$i=0;
	while (!$rs->EOF) {
		$seme_class[$i]=$rs->fields['seme_class'];
		$i++;
		$rs->MoveNext();
	}
}
if ($tops==0) $tops=50;

if ($year_seme && $year_name && count($sel)==0) {
	echo "	
		<table bgcolor=#ffffff border=0 cellpadding=0 cellspacing=0>
		<tr bgcolor='#ffffff'>
		<td>
		<table bgcolor='#9ebcdd' cellspacing='1' cellpadding='4' class='small'>
		<form name='form4' method='post' action='{$_SERVER['PHP_SELF']}'>
		<tr bgcolor='#c4d9ff'>
		<td align='center'>排序</td>
		<td bgcolor='#ffffff' align='center'>全校前<input type='text' size='2' name='tops' value='$tops'>名</td>
		</tr>
		<tr bgcolor='#c4d9ff'>
		<td align='center'>選取</td>
		<td align='center'>科目</td>
		</tr>
		";
	$sql="select ss_id,scope_id,subject_id,rate from score_ss where year='$sel_year' and semester='$sel_seme' and class_year='$year_name' and enable='1' and need_exam='1' order by ss_id";
	$rs=$CONN->Execute($sql);
	$i=0;
	while (!$rs->EOF) {
		$ss_id[$i]=$rs->fields['ss_id'];
		$sj=$rs->fields['subject_id'];
		$rate[$ss_id[$i]]=$rs->fields['rate'];
		if (!$sj) $sj=$rs->fields['scope_id'];
		$sql="select subject_name from score_subject where subject_id='$sj'";
		$rs2=$CONN->Execute($sql);
		$subject[$ss_id[$i]]=$rs2->fields['subject_name'];
		if (count($sel)=="0") 
			$checked=($rs->fields['scope_id']!="8")?"checked":"";
		else
			$checked=($sel[$ss_id[$i]])?"checked":"";
		echo "
			<tr bgcolor='#ffffff'>
			<td align='center'><input type='checkbox' name='sel[".$ss_id[$i]."]' value='".$ss_id[$i]."' $checked></td>
			<td align='center'>".$subject[$ss_id[$i]]."</td>
			<input type='hidden' name='rate[".$ss_id[$i]."]' value='".$rate[$ss_id[$i]]."'>
			<input type='hidden' name='subject[".$ss_id[$i]."]' value='".$subject[$ss_id[$i]]."'>
			</tr>
			";
		$i++;
		$rs->MoveNext();
	}
		echo "
			</table>
			<input type='hidden' name='year_seme' value='$year_seme'>
			<input type='hidden' name='year_name' value='$year_name'>
			<input type='hidden' name='act' value='sel'>
			<input type='submit' value='開始處理'>
			</form>
			</tr>
			</table>
			";
}

if ($year_seme && $year_name && count($sel)>0) {
	mysql_query("DROP TABLE IF EXISTS score_temp");  
	$Create_db="
		CREATE temporary TABLE score_temp (
		student_sn int(10) unsigned NOT NULL default '0' ,
		seme_class varchar(3) NULL default '',
		seme_num varchar(3) NULL default '',
		score float unsigned NOT NULL default '0' ,
		ss text NOT NULL default '' ,
		PRIMARY KEY  (student_sn))";
	mysql_query($Create_db);  
	
	echo "
		<table bgcolor=#ffffff border=0 cellpadding=0 cellspacing=0>
		<tr bgcolor='#ffffff'>
		<td>
		<table bgcolor='#9ebcdd' cellspacing='1' cellpadding='4' class='small'>
		<tr bgcolor='#c4d9ff'>
		<td align='center'>學生姓名</td>
		<td align='center'>身分證</td>
		<td align='center'>班級</td>
		<td align='center'>座號</td>
		";
	while (list($k,$v)=each($sel)) {
		echo "<td align='center'>".$subject[$v]."<br><font color='#000088'>(".$rate[$v].")</font>";
	}
	echo "<td align='center'>總平均</td><td align='center'>名次</td><td align='center'>PR值</td><td align='center'>目前年班</td></tr>";
	
	for ($i=0;$i<count($seme_class);$i++) {
		$sql="select seme_num,student_sn from stud_seme where seme_year_seme='$seme_year_seme' and seme_class='$seme_class[$i]'";
		$rs=$CONN->Execute($sql);
		while (!$rs->EOF) {
			$seme_num=$rs->fields['seme_num'];
			$student_sn=$rs->fields['student_sn'];
			$sql_s="select ss_id,ss_score from stud_seme_score where seme_year_seme='$seme_year_seme' and student_sn='$student_sn' order by ss_id";
			$rs_s=$CONN->Execute($sql_s);
			$sum=0;
			$sst="";
			$sum_rate=0;
			while (!$rs_s->EOF) {
				$ssid=$rs_s->fields['ss_id'];
				if (in_array($ssid,$sel)) {
					$ss_score=$rs_s->fields['ss_score'];
					$sum+=$ss_score*$rate[$ssid];
					$sst.="_".sprintf("%03d",round($ss_score));
					$sum_rate+=$rate[$ssid];
				}
				$rs_s->MoveNext();
			}
			$sql_s="insert into score_temp (student_sn,seme_class,seme_num,score,ss) values ('$student_sn','$seme_class[$i]','$seme_num','$sum','$sst')";
			$rs_s=$CONN->Execute($sql_s);
			$rs->MoveNext();
		}
	}
	$sql_s="select student_sn,score,ss,seme_class,seme_num from score_temp order by score desc";
	$rs_s=$CONN->Execute($sql_s);
	for ($j=1;$j<=$tops;$j++) {
		//算PR值 -- by 和東 王麒富
		$PR = show_PR($seme_year_seme,$year_name,$j);

		if (($j % 5)==1) echo "<tr></tr>";
		$s=explode("_",$rs_s->fields['ss']);
		$student_sn=$rs_s->fields['student_sn'];
		$seme_class=$rs_s->fields['seme_class'];
		$seme_num=$rs_s->fields['seme_num'];
		$sql_st="select stud_name,stud_person_id,curr_class_num from stud_base where student_sn='$student_sn'";
		$rs_st=$CONN->Execute($sql_st);
		echo "<tr><td class='title_sbody2'>".$rs_st->fields['stud_name']."<td bgcolor='#ffffff' align='center'>".$rs_st->fields['stud_person_id']."<td bgcolor='#ffffff' align='center'>$seme_class"."<td bgcolor='#ffffff' align='center'>$seme_num";
		for ($k=1;$k<count($s);$k++) echo "<td bgcolor='#ffffff' align='center'>".intval($s[$k]);
		echo "<td bgcolor='#ffffff' align='center'>".number_format($rs_s->fields['score']/$sum_rate,2)."<td bgcolor='#ffffff' align='center'>$j</td><td bgcolor='#ffffff' align='center'>".$PR."</td><td bgcolor='#ffffff' align='center'>".$rs_st->fields['curr_class_num']."</td></tr>";
		$rs_s->MoveNext();
	}
	echo "</table></tr></table>";
}

echo $main;
echo "</tr></table>";
foot();

?>

<script language="JavaScript">
<!-- Begin
function jumpMenu0(){
	var str, classstr ;
 if (document.form0.year_seme.options[document.form0.year_seme.selectedIndex].value!="") {
	location="<?php echo $_SERVER['PHP_SELF'] ?>?year_seme=" + document.form0.year_seme.options[document.form0.year_seme.selectedIndex].value;
	}
}

function jumpMenu1(){
	var str, classstr ;
 if ((document.form1.year_name.value!="") & (document.form1.year_name.options[document.form1.year_name.selectedIndex].value!="")) {
	location="<?php echo $_SERVER['PHP_SELF'] ?>?year_seme=" + document.form1.year_seme.value + "&year_name=" + document.form1.year_name.options[document.form1.year_name.selectedIndex].value;
	}
}

function jumpMenu2(){
	var str, classstr ;
 if ((document.form2.year_name.value!="") & (document.form2.me.options[document.form2.me.selectedIndex].value!="")) {
	location="<?php echo $_SERVER['PHP_SELF'] ?>?year_seme=" + document.form2.year_seme.value + "&year_name=" + document.form2.year_name.value + "&me=" + document.form2.me.options[document.form2.me.selectedIndex].value;
	}
}

//  End -->
</script>
