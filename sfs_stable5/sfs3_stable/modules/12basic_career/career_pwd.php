<?php

// $Id:  $

//取得設定檔
include_once "config.php";

sfs_check();

//秀出網頁
head("更改學生登入密碼");

//模組選單
print_menu($menu_p,$linkstr);

if($student_sn){
	//儲存紀錄處理
	if($_POST['go']=='更改密碼'){
		$login_pass=$_POST[login_pass];
		if(strlen($login_pass)>=6){
			$login_pass2=$_POST[login_pass2];
			if($login_pass and $login_pass==$login_pass2){
				$query="update stud_base set email_pass ='$login_pass' where student_sn=$student_sn";
				$res=$CONN->Execute($query) or die("SQL錯誤:$query");
				$msg='密碼更改成功！';
			} else $msg='新密碼、確認新密碼不同，更改失敗！';
		} else $msg='新密碼長度太短，更改失敗！  請至少輸入6個字元';
	}

	$mydata="<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' width='100%'>
		<tr align='center'><td bgcolor='#ccccff'>輸入新密碼</td><td><input type='password' size='32' maxlength='32' name='login_pass'></td></tr>
		<tr align='center'><td bgcolor='#ccccff'>確認新密碼</td><td><input type='password' size='32' maxlength='32' name='login_pass2'></td></tr>
		<tr bgcolor='#ccccff' align='center'>
			<td colspan=2>
				<input type='submit' value='更改密碼' name='go' onclick='return confirm(\"確定要\"+this.value+\"?\")' style='border-width:1px; cursor:hand; color:white; background:#ff5555; font-size:16px; height=42'>
			</td>
		</tr></table>";
}

$main="<font size=2><form method='post' action='$_SERVER[SCRIPT_NAME]' name='myform'>
	<table style='border-collapse: collapse; font-size=12px;'><tr><td valign='top'>$class_select<br>$student_select</td><td valign='top'>$mydata<br>$msg</td></tr></table><font>";

echo $main;

foot();

?>
