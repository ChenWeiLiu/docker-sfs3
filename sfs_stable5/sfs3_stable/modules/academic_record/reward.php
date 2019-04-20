<?php

// $Id: reward.php 8833 2016-03-03 08:12:03Z brucelyc $

// 取得設定檔
include "config.php";

include_once ("../reward/my_fun.php"); //將 reward 的函式載入 , 會用到  cal_rew

$view=$_GET['view'];

sfs_check();

if ($adm==1) $school_menu_p = $student_menu_p;

//程式檔頭
head("觀看獎懲記錄");
$tool_bar=&make_menu($school_menu_p);
echo "$tool_bar<table border=0 cellspacing=1 cellpadding=2 width=100% bgcolor=#cccccc><tr><td bgcolor='#FFFFFF'>";

//取得學年學期
$sel_year = curr_year(); //目前學年
$sel_seme = curr_seme(); //目前學期

if ($adm==1) {
	if ($_POST['class_id']<>"") {
		$c_arr = explode("_",$_POST['class_id']);
		$class_name[0] = intval($c_arr[2].$c_arr[3]);
	} else
		$class_name = array("701");
} else {
//找出任教班級
	$teacher_sn=$_SESSION['session_tea_sn'];//取得登入老師的id
	$class_name=teacher_sn_to_class_name($teacher_sn);
}
$seme_year_seme=sprintf("%03d",curr_year()).curr_seme();

//統計班級現有人數
$sql="select count(student_sn) from stud_seme where seme_year_seme='$seme_year_seme' and seme_class='$class_num'";
$res=$CONN->Execute($sql);
$student_number=$res->fields[0];

//取得學生名單
$sql="select student_sn from stud_base where curr_class_num like '$class_name[0]%' and stud_study_cond='0' order by curr_class_num";
$rs=$CONN->Execute($sql) or die($sql);;
while (!$rs->EOF) {
	$stud_sn[]=$rs->fields["student_sn"];
	$rs->MoveNext();
}

if ($view=="One") {
	$query="select stud_name,curr_class_num from stud_base where student_sn='".$_GET['student_sn']."'";
	$res=$CONN->Execute($query) or die($query);
	$stud_name=$res->fields['stud_name'];
	$site_num=intval(substr($res->fields['curr_class_num'],-2,2));
	
	$main="<table bgcolor=#ffffff border=0 cellpadding=2 cellspacing=1 class='small'>
		<tr bgcolor='#ffffff'><td>
		<table bgcolor='#9ebcdd' cellspacing='1' cellpadding='4' class='small'>
		<tr bgcolor='#c4d9ff'><td align='center'>姓名</td><td bgcolor='#ffffff' colspan='4'>$stud_name</td></tr>
		<tr bgcolor='#c4d9ff'><td align='center'>座號</td><td bgcolor='#ffffff' colspan='4'>$site_num</td></tr>
		<tr bgcolor='#c4d9ff'>
		<td align='center'>獎懲類別</td>
		<td align='center'>獎懲事由</td>
		<td align='center'>獎懲依據</td>
		<td align='center'>獎懲日期</td>
		<td align='center'>銷過日期</td>
		</tr>
		";
	$reward_year_seme=$sel_year.$sel_seme;
	$query="select * from reward where student_sn='".$_GET['student_sn']."' order by reward_div,reward_date desc";
	$res=$CONN->Execute($query) or die($query);
	$i=0;
	if ($res)
		while (!$res->EOF) {
			$reward_kind=intval($res->fields['reward_kind']);
			$reward_cancel_date=$res->fields['reward_cancel_date'];
			if ($reward_kind>0) {
				$bgcolor="#FFE6D9";
				$reward_cancel_date="-----";
			} else {
				$bgcolor="#E6F2FF";
				if ($reward_cancel_date=="0000-00-00") $reward_cancel_date="未銷過";
			}
			$main.="<tr bgcolor='$bgcolor'><td align='center'>".$reward_arr[$reward_kind]."</td><td>".$res->fields['reward_reason']."</td><td>".$res->fields['reward_base']."</td><td align='center'>".$res->fields['reward_date']."</td><td align='center'>".$reward_cancel_date."</td></tr>\n";
			$i++;
			$res->MoveNext();
		}
	if ($i==0) $main.="<tr bgcolor='#ffffff'><td align='center' colspan='5'>無獎懲記錄</td></tr>\n";
	$main.="</table><br><a href={$_SERVER['SCRIPT_NAME']}?view=All>觀看全班記錄</a>";
} else {
	//顯示表頭資料
	?>
	<table border="0" width="100%">
	  <tr>
	   <td style="font-size:9pt;color=#FF0000"><input type="button" style="bgcolor:#FFCCCC;font-size:9pt" value="即時更新" onclick="window.location='<?php echo $_SERVER['SCRIPT_NAME']; ?>?recal=1'">※說明:發現本表統計資料有誤, 請按此鈕進行資料重新統計。</td>
	  </tr>
	</table>
	<?php
	if ($adm==1) {
		$class_select=&classSelect($sel_year,$sel_seme,"","class_id",$class_id,true);
		echo "
		<form method=\"post\">
		$class_select
		<inpput type=\"submit\" value=\"確定\">
		</form>
		";
	}
	
	//顯示表頭資料
	$main="<table bgcolor=#ffffff border=0 cellpadding=2 cellspacing=1>
		<tr bgcolor='#ffffff'><td>
		<table bgcolor='#9ebcdd' cellspacing='1' cellpadding='4' class='small'>
		<tr bgcolor='#c4d9ff'>
		<td align='center' rowspan='2'>座號</td>
		<td align='center' rowspan='2'>姓名</td>
		<td align='center' colspan='6'>本學期</td>
		<td align='center' colspan='6'>總計</td>
		</tr>
		<tr bgcolor='#c4d9ff'>
		<td align='center'>大功</td>
		<td align='center'>小功</td>
		<td align='center'>嘉獎</td>
		<td align='center'>大過</td>
		<td align='center'>小過</td>
		<td align='center'>警告</td>
		<td align='center'>大功</td>
		<td align='center'>小功</td>
		<td align='center'>嘉獎</td>
		<td align='center'>大過</td>
		<td align='center'>小過</td>
		<td align='center'>警告</td>
		</tr>
		";

	//顯示成績
	for ($m=0;$m<count($stud_sn);$m++){
		$rs=&$CONN->Execute("select stud_name,stud_id from stud_base where student_sn='$stud_sn[$m]'");

		//取得座號及姓名
		$stud_name=$rs->fields['stud_name'];
		$stud_id[$m]=$rs->fields['stud_id'];
		$site_num=student_sn_to_site_num($stud_sn[$m]);
    
    if ($_GET['recal']==1) {
    //即時統計, by smallduh 2013.1.21 把學生就學的每個學期的資料全部再統計一次    
     $query_rew="select distinct reward_year_seme from reward where student_sn='".$stud_sn[$m]."'";
     $res_rew=mysql_query($query_rew);
     while ($row_rew=mysql_fetch_array($res_rew)) {
      //cal_rew(substr($row_rew['reward_year_seme'],0,3),substr($row_rew['reward_year_seme'],3,1),$stud_id[$m]); //即時統計總表 by smallduh 2013.1.8
      cal_rew(substr($row_rew['reward_year_seme'],0,strlen($row_rew['reward_year_seme'])-1),substr($row_rew['reward_year_seme'],-1),$stud_id[$m]); //即時統計總表 by smallduh 2013.1.8
     }
    } // end if $_GET['recal'];
		$main.="<tr bgcolor='#ffffff'><td>$site_num</td><td><a href={$_SERVER['SCRIPT_NAME']}?view=One&student_sn=$stud_sn[$m]>$stud_name</a></td>";
		$query="select * from stud_seme_rew where seme_year_seme='$seme_year_seme' and student_sn='$stud_sn[$m]' and stud_id='$stud_id[$m]' order by sr_kind_id";
		$res=$CONN->Execute($query) or die($query);
		$rew=array();
		if ($res)
			while (!$res->EOF) {
				$rew[$res->fields['sr_kind_id']]=$res->fields['sr_num'];
				$res->MoveNext();
			}
		for ($i=1;$i<=6;$i++) $main.="<td>".intval($rew[$i])."</td>";
		$query="select sr_kind_id,sum(sr_num) from stud_seme_rew where student_sn='$stud_sn[$m]' and stud_id='$stud_id[$m]' group by sr_kind_id order by sr_kind_id";
		$res=$CONN->Execute($query) or die($query);
		$rew=array();
		if ($res)
			while (!$res->EOF) {
				$rew[$res->fields['sr_kind_id']]=$res->fields[1];
				$res->MoveNext();
			}
		for ($i=1;$i<=6;$i++) $main.="<td style='background-color:cornsilk;'>".intval($rew[$i])."</td>";
		$main.="</tr>\n";
	}
	$main.="</table>";
}

echo $main;
echo "</td></tr></table></tr></table>";
foot();
?>
