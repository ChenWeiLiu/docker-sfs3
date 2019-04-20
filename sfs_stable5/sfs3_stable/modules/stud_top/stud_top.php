<?php

// $Id: stud_top.php 5310 2009-01-10 07:57:56Z hami $

/* 取得設定檔 */
include "config.php";

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
if ($tops==0) $tops=3;

if ($year_seme && $year_name && count($sel)==0) {
	echo "	
		<table bgcolor=#ffffff border=0 cellpadding=0 cellspacing=0>
		<tr bgcolor='#ffffff'>
		<td>
		<table bgcolor='#9ebcdd' cellspacing='1' cellpadding='4' class='small'>
		<form name='form4' method='post' action='{$_SERVER['PHP_SELF']}'>
		<tr bgcolor='#c4d9ff'>
		<td align='center'>排序</td>
		<td bgcolor='#ffffff' align='center'>各班前<input type='text' size='2' name='tops' value='3'>名</td>
		</tr>
		<tr bgcolor='#c4d9ff'>
		<td align='center'>選取</td>
		<td align='center'>科目</td>
		</tr>
		";
	$sql="select ss_id,scope_id,subject_id,rate from score_ss where year='$sel_year' and semester='$sel_seme' and class_year='$year_name' and enable='1' and need_exam='1' order by sort";
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
	echo "
		<table bgcolor=#ffffff border=0 cellpadding=0 cellspacing=0>
		<tr bgcolor='#ffffff'>
		<td>
		<table bgcolor='#9ebcdd' cellspacing='1' cellpadding='4' class='small'>
		<tr bgcolor='#c4d9ff'>
		<td align='center'>學生姓名</td>
		<td align='center'>班級</td>
                <td align='center'>座號</td>
		";
	$temp_ss_id='';
	while (list($k,$v)=each($sel)) {
		echo "<td align='center'>".$subject[$v]."<br><font color='#000088'>(".$rate[$v].")</font>";
		$temp_ss_id .= $v.",";
	}
		$temp_ss_id = substr($temp_ss_id,0,-1);
	echo "<td align='center'>總平均<td align='center'>名次</tr>";
	
	for ($i=0;$i<count($seme_class);$i++) {
		echo "<tr></tr>";
		$sql = " SELECT a.seme_num,a.student_sn,b.stud_name FROM stud_seme a LEFT JOIN stud_base b ON a.student_sn=b.student_sn where a.seme_year_seme='$seme_year_seme' and a.seme_class='$seme_class[$i]'";
		$rs=$CONN->Execute($sql);
		//先將 student_sn 放在陣列中
		$temp_num = array();
		$student_sn_all = '';
		while (!$rs->EOF) {
			$temp_sn = "s_".$rs->fields[student_sn];
			$temp_num[$temp_sn][stud_num] = $rs->fields[seme_num];
			$temp_num[$temp_sn][stud_name] = $rs->fields[stud_name];
			$student_sn_all .= $rs->fields[student_sn].",";
			$rs->MoveNext();
		}
		//計算排名
		if ($student_sn_all<>'') {
			$student_sn_all = substr($student_sn_all,0,-1);
			$sql_s = "select sum(a.ss_score*b.rate) as AA, sum(b.rate) as BB, a.student_sn from stud_seme_score a , score_ss b where a.ss_id=b.ss_id and a.ss_id in ($temp_ss_id) and a.seme_year_seme='$seme_year_seme' and a.student_sn in ($student_sn_all) group by a.student_sn order by AA desc limit 0,$tops";	
			$rs_s=$CONN->Execute($sql_s) or die($sql_s);
			$j=0;
			while(!$rs_s->EOF){
				$student_sn = $rs_s->fields[student_sn];
				$AA = $rs_s->fields[AA];
				$BB = $rs_s->fields[BB];
				//印出學生成績
				echo "<tr><td class='title_sbody2'>".$temp_num["s_$student_sn"]['stud_name']."</td><td bgcolor='#ffffff' align='center'>".$seme_class[$i]."</td><td bgcolor='#ffffff' align='center'>".$temp_num["s_$student_sn"]['stud_num']."</td>";
				$query = "select a.ss_id,a.ss_score from stud_seme_score a,score_ss b where a.ss_id=b.ss_id and a.ss_id in ($temp_ss_id) and a.student_sn='$student_sn' order by b.sort";
//				echo $query."<BR>";
				$rs_ss = $CONN->Execute($query) or die($query);
				$ttt_arr = array();
				while(!$rs_ss->EOF) {
					$ttt_arr[$rs_ss->fields[ss_id]]=round(doubleval($rs_ss->fields[ss_score]));
					$rs_ss->MoveNext();
				}
				reset($sel);
				while(list($id,$val) = each($sel)){
					if ($ttt_arr[$val]=='') $ttt_arr[$val] = '-';
					echo "<td bgcolor='#ffffff' align='center'>$ttt_arr[$val]</td>";
				}
				$j++;
				echo "<td bgcolor='#ffffff' align='center'>".number_format($AA/$BB,2)."<td bgcolor='#ffffff' align='center'>$j</tr>";
				$rs_s->MoveNext();				
			}
		
		}

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
