<?php

// $Id:  $

//取得設定檔
include_once "config.php";

sfs_check();

//秀出網頁
head("更改學生登入密碼");

//模組選單
print_menu($menu_p,$linkstr);


if($is_pwd){
	$student_sn=$_POST['student_sn'];
	//儲存紀錄處理
	if($student_sn and $_POST['go']=='更改密碼'){
		$login_pass=$_POST[login_pass];
		if(strlen($login_pass)>=6){
			$login_pass2=$_POST[login_pass2];
			if($login_pass and $login_pass==$login_pass2){
				$ldap_password = createLdapPassword($login_pass);
				$query="update stud_base set email_pass ='$login_pass', ldap_password='$ldap_password' where student_sn=$student_sn";
				$res=$CONN->Execute($query) or die("SQL錯誤:$query");
				$msg='密碼更改成功！';
				
				//更新學生 sha key 2014.10.07 *********************/
				$query = "SELECT stud_person_id FROM stud_base where student_sn='$student_sn' ";
				$res = $CONN->Execute($query) or die($query);
				$row=$res->FetchRow();
      	if ($row['stud_person_id']!="") {
				 	$stud_person_id = hash('sha256', strtoupper($row['stud_person_id']));
				 	$sql = "UPDATE stud_base SET edu_key='$stud_person_id' WHERE student_sn='$student_sn'";
					$CONN->Execute($sql) ;
			 	}			
				
			} else $msg='新密碼、確認新密碼不同，更改失敗！';
		} else $msg='新密碼長度太短，更改失敗！  請至少輸入6個字元';
	}
		
		
	$teacher_sn=$_SESSION['session_tea_sn'];//取得登入老師的id
	//找出任教班級
	$class_name=teacher_sn_to_class_name($teacher_sn);
	$class_id=$class_name[0];
	$curr_year_seme=sprintf('%03d%d',curr_year(),curr_seme());

	if($class_id){
		//產生學生名單
		$query="select a.student_sn,a.seme_num,b.stud_name,b.stud_sex from `stud_seme` a inner join stud_base b on a.student_sn=b.student_sn where seme_year_seme='$curr_year_seme' and seme_class='{$class_id}' order by a.seme_num";
		$res=$CONN->Execute($query) or die("SQL錯誤：<br>$query");
		$student_select="<select name='student_sn' onchange='this.form.submit()'><option value=''>- 請選擇學生 -</option>";
		while(!$res->EOF){
			$selected=($student_sn==$res->fields['student_sn'])?'selected':'';
			$color=($res->fields['stud_sex']==1)?'#0000ff':'#ff0000';
			$color=($student_sn==$res->fields['student_sn'])?'#00ff00':$color;
			$student_select.="<option value='{$res->fields['student_sn']}' $selected bgcolor='$color'>({$res->fields['seme_num']}) {$res->fields['stud_name']}</option>";
			$res->MoveNext();
		}
		$student_select.="</select>";
	}

	if($student_sn){
	$mydata="<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111'>
		<tr align='center'><td bgcolor='#ccccff'>輸入新密碼</td><td><input type='password' size='32' maxlength='32' name='login_pass'></td></tr>
		<tr align='center'><td bgcolor='#ccccff'>確認新密碼</td><td><input type='password' size='32' maxlength='32' name='login_pass2'></td></tr>
		<tr bgcolor='#ccccff' align='center'>
			<td colspan=2>
				<input type='submit' value='更改密碼' name='go' onclick='return confirm(\"確定要\"+this.value+\"?\")' style='border-width:1px; cursor:hand; color:white; background:#ff5555; font-size:16px; height=42'>
			</td>
		</tr></table>";
	}

	$main="<font size=2><form method='post' action='$_SERVER[SCRIPT_NAME]' name='myform'>要更改密碼的學生： $student_select <br> $mydata<br>$msg</font></form>";
} else $main="<BR><center><font color=red size=4>模組變數尚未開放本功能，請洽詢學校系統管理者！</font></center>";

echo $main;

foot();

?>
