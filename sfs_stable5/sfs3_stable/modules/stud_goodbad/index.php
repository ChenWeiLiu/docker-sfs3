<?php
// $Id: index.php 5310 2009-01-10 07:57:56Z hami $
//取得設定檔
include_once "config.php";
sfs_check();
head("失效模組");

$trans_arr= array("big_good1"=>5,"big_good2"=>6,"big_good3"=>7,"s_good1"=>3,"s_good2"=>4,"s_good3"=>99,"ss_good1"=>1,"ss_good2"=>2,"ss_good3"=>99,"big_bad1"=>-5,"big_bad2"=>-6,"big_bad3"=>-7,"s_bad1"=>-3,"s_bad2"=>-4,"s_badd3"=>99,"ss_bad1"=>-1,"ss_bad2"=>-2,"ss_bad3"=>99);

if ($_POST[dels]) {
	$query="delete from stud_good_bad";
	$res=$CONN->Execute($query) or trigger_error($query,E_USER_ERROR);
}
if ($_REQUEST[trans]) {
	$trans_msg="";
	$query="select * from stud_good_bad";
	$res=$CONN->Execute($query) or trigger_error($query,E_USER_ERROR);
	while (!$res->EOF) {
		$gb_id=$res->fields[gb_id];
		$gb_year=$res->fields[gb_year];
		$gb_seme=$res->fields[gb_seme];
		$stud_id=$res->fields[stud_id];
		$gb_add_date=$res->fields[gb_add_date];
		$gb_cancel_date=$res->fields[gb_cancel_date];
		$gb_why=$res->fields[gb_why];
		$gb_dep=$res->fields[gb_dep];
		$gb_kind=$res->fields[gb_kind];
		$reward_year_seme=$gb_year.$gb_seme;
		$seme_year_seme=sprintf("%03d",$gb_year).$gb_seme;
		$reward_kind=$trans_arr[$gb_kind];
		$query_trans="select a.student_sn,b.stud_name from stud_seme a,stud_base b where a.seme_year_seme='$seme_year_seme' and a.stud_id='$stud_id' and a.student_sn=b.student_sn";
		$res_trans=$CONN->Execute($query_trans) or trigger_error($query_trans,E_USER_ERROR);
		$student_sn=$res_trans->fields[student_sn];
		$stud_name=$res_trans->fields[stud_name];
		if ($reward_kind==99) {
			$trans_msg.="<tr bgcolor='#ffffff'><td>$stud_id</td><td>$stud_name</td><td>$gb_add_date</td><td>$gb_kind_arr[$gb_kind]</td><td>$gb_why</td><td>無法轉移</td></tr>";
		} else {
			$query_chk="select * from reward where reward_year_seme='$reward_year_seme' and reward_date='$gb_add_date' and stud_id='$stud_id' and reward_reason='$gb_why' and reward_kind='$reward_kind'";
			$res_chk=$CONN->Execute($query_chk) or trigger_error($query_chk,E_USER_ERROR);
			if ($res_chk->RecordCount()==0) {
				$reward_div=($reward_kind>0)?"1":"2";
				$reward_c_date=date("Y-m-j");
				$reward_ip=getip();
				$reward_sub=($gb_cancel_date=="0000-00-00")?"1":"2";
				$query_trans="insert into reward (reward_div,stud_id,reward_kind,reward_year_seme,reward_date,reward_reason,reward_c_date,reward_base,reward_cancel_date,update_id,update_ip,reward_sub,dep_id,student_sn) values ('$reward_div','$stud_id','$reward_kind','$reward_year_seme','$gb_add_date','$gb_why','$reward_c_date','$gb_dep','$gb_cancel_date','$_SESSION[session_log_id]','$reward_ip','$reward_sub','0','$student_sn')";
				$CONN->Execute($query_trans) or trigger_error($query_trans,E_USER_ERROR);
				$dep_id=$CONN->Insert_ID();
				$query_update="update reward set dep_id='$dep_id' where reward_id='$dep_id'";
				$CONN->Execute($query_update);
				$query_del="delete from stud_good_bad where gb_id='$gb_id'";
				$CONN->Execute($query_del) or trigger_error($query_del,E_USER_ERROR);
			} else {
				$trans_msg.="<tr bgcolor='#ffffff'><td>$stud_id</td><td>$stud_name</td><td>$gb_add_date</td><td>$gb_kind_arr[$gb_kind]</td><td>$gb_why</td><td>已有重覆資料</td></tr>";
			}
		}
		$res->MoveNext();
	}
	if ($trans_msg) {
		$trans_msg="	<table cellspacing=1 cellpadding=6 border=0 bgcolor='#FFF829' width='80%' align='center' class='small'>\n
				<tr bgcolor='#ffffff'><td>學號</td><td>學生姓名</td><td>獎懲日期</td><td>獎懲類別</td><td>獎懲事由</td><td>狀態</td></tr>\n
				".$trans_msg."
				<tr bgcolor='#ffffff'><td colspan='6' align='center'><font color='#ff0000'>請將以上資料印列後手動輸入。</font></td></table>\n";
	}
}
$message="	<table cellspacing=1 cellpadding=6 border=0 bgcolor='#FFF829' width='80%' align='center'>\n
		<tr><td align='center'><h1><img src='../../images/warn.png' align='middle' border=0>模組失效訊息</h1></font>
		<tr><td align='center' bgcolor='#FFFFFF' width='90%'>本模組已不維護，請改用「學生獎懲」(reward)模組。<br>";
$query="select * from stud_good_bad";
$res=$CONN->Execute($query) or trigger_error($query,E_USER_ERROR);
if ($res->RecordCount()>0) {
	$message.="	<font color='#ff0000'>資料庫內現有學生獎懲資料".$res->RecordCount()."筆，是否進行轉移？</font></td></tr>
			<form name='form1' method='post' action='{$_SERVER['PHP_SELF']}'>
			<tr><td align=center><br><input type='submit' name='trans' value='轉移'><input type='submit' name='dels' value='直接刪除'><br></td></tr>\n
			</form></table>\n";
} else {
	$message.="	</td></tr>\n
			<tr><td align=center><br></td></tr></table>\n";
} 
echo $message;
if ($trans_msg) echo $trans_msg;
foot();
?>

