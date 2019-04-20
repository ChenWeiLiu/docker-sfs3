<?php
// $Id: list.php 5310 2009-01-10 07:57:56Z hami $

include "config.php";

sfs_check();

//秀出網頁
head("班級學生獎懲紀錄");
print_menu($menu_p);

if($is_rewrad) {
	$teacher_sn=$_SESSION['session_tea_sn']; //取得登入老師的id
	//找出任教班級
	$class_name=teacher_sn_to_class_name($teacher_sn);
	$class_id=$class_name[0];
	
	//學期別
	$curr_year_seme=sprintf("%03d%d",curr_year(),curr_seme());
	if($class_id)
	{
		$studentdata='';
		//取得stud_base中班級學生列表並據以與前sql對照後顯示
		$sql="SELECT a.student_sn,a.seme_num,b.stud_name,b.stud_sex FROM stud_seme a LEFT JOIN stud_base b on a.student_sn=b.student_sn WHERE a.seme_class='$class_id' AND a.seme_year_seme='$curr_year_seme' AND b.stud_study_cond=0 ORDER BY a.seme_num";
		$rs=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);		
		//以radio呈現
		while(list($student_sn,$seme_num,$stud_name,$stud_sex)=$rs->FetchRow()) {
			$_POST['student_sn']=$_POST['student_sn']?$_POST['student_sn']:$student_sn;
			$sex_color=($stud_sex==1)?'#0000ff':'#ff0000';
			$checked=($student_sn==$_POST['student_sn'])?'checked':'';
			$seme_num=sprintf('%02d',$seme_num);
			$studentdata.="<input type='radio' name='student_sn' value='$student_sn' onclick=\"this.form.submit();\" $checked><font color='$sex_color'>($seme_num) $stud_name</font><br>";
		}
		$class_list="<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size:9pt;' bordercolor='#111111' id='AutoNumber1'><tr><td bgcolor='#ccffff' align='center'>◎{$class_name[1]}◎</td></tr><tr><td>$studentdata</td></tr></table>";
		
		$reward_data="<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size:9pt;' bordercolor='#111111'><tr align='center' bgcolor='#ccccff'><td>NO.</td><td>學期別</td><td>獎懲日期</td><td>獎懲類別</td><td>獎懲事由</td><td>獎懲依據</td><td>銷過日期</td></tr>";
		
		$reward_arr=array("1"=>"嘉獎一次","2"=>"嘉獎二次","3"=>"小功一次","4"=>"小功二次","5"=>"大功一次","6"=>"大功二次","7"=>"大功三次","-1"=>"警告一次","-2"=>"警告二次","-3"=>"小過一次","-4"=>"小過二次","-5"=>"大過一次","-6"=>"大過二次","-7"=>"大過三次");
		//抓取指定學生的獎懲紀錄
		$sql="SELECT * FROM reward WHERE student_sn={$_POST['student_sn']} ORDER BY reward_year_seme,reward_date";
		$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
		while(!$res->EOF)
		{
			$reward_kind=$res->fields['reward_kind'];
			$reward_cancel_date=$res->fields['reward_cancel_date'];
			$reward_year_seme=substr($res->fields['reward_year_seme'],0,-1).'-'.substr($res->fields['reward_year_seme'],-1);
			$recno++;
			$bgcolor=($reward_kind>0)?'#ccffcc':'#ffcccc';
			if($reward_cancel_date=='0000-00-00') $reward_cancel_date=''; else $bgcolor='#cccccc';
			$reward_data.="<tr bgcolor='$bgcolor' align='center'><td>$recno</td><td>$reward_year_seme</td><td>{$res->fields['reward_date']}</td><td>{$reward_arr[$res->fields['reward_kind']]}</td><td align='left'>{$res->fields['reward_reason']}</td><td align='left'>{$res->fields['reward_base']}</td><td>$reward_cancel_date</td></tr>";
			$res->MoveNext();
		}
		$reward_data.="</table>";
		
		$main="<form name='myform' method='post' action='$_SERVER[SCRIPT_NAME]'><table><tr valign='top'><td>$class_list</td><td>$reward_data</td></tr></table></form>";
		echo $main; 
	} else echo "您並非班級導師！";	
} else echo "系統管理者未設定班級導師可觀視學生獎懲記錄！";	
foot();

?>