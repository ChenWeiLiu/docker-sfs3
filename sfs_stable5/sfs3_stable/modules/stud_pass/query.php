<?php
//$Id: query.php 8161 2014-10-07 14:57:17Z smallduh $
include "config.php";
include "../../include/sfs_case_dataarray.php";

//認證
sfs_check();

//秀出網頁布景標頭
head("學生密碼管理");

//主要內容
print_menu($school_menu_p);
$stud_id=$_POST[stud_id];
$mode=$_POST[mode];
$passwd=$_POST[email_pass];
$stud_study_cond=study_cond();
$change="修改密碼";
$finish="修改完成";
if (count($passwd)>0 && $mode==$finish) {
	while(list($student_sn,$email_pass)=each($passwd)) {
	  //更新學生 sha key 2014.10.07 *********************/
			$query = "SELECT stud_person_id FROM stud_base where student_sn='$student_sn' ";
			$res = $CONN->Execute($query) or die($query);
			$row=$res->FetchRow();
      if ($row['stud_person_id']!="") {
				 	$stud_person_id = hash('sha256', strtoupper($row['stud_person_id']));
				 	$sql = "UPDATE stud_base SET edu_key='$stud_person_id' WHERE student_sn='$student_sn'";
					$CONN->Execute($sql) ;
			 }
		
		$ldap_password = createLdapPassword($email_pass);
		$query="update stud_base set email_pass='$email_pass' , ldap_password='$ldap_password'  where student_sn='$student_sn'";
		
		$CONN->Execute($query) or die($query);
	}
	$mode="";
}

//顯示選項
$edit_content=($mode)?$finish:$change;
$edit_submit=($stud_id)?"<input type='submit' name='mode' value='$edit_content'>":"";
$main="	<table border=0 cellspacing=1 cellpadding=2 width=100% bgcolor=#cccccc class=small>\n
	<form method='post' action='{$_SERVER['PHP_SELF']}'>\n
	<tr><td bgcolor='#FFFFFF'>&nbsp;學生學號<input type='text' maxlength='10' size='10' name='stud_id' value='$stud_id'><input type='submit' name='set' value='開始查詢'>$edit_submit<br>\n";
if ($stud_id) {
	$query="select * from stud_base where stud_id='$stud_id' order by student_sn";
	$res=$CONN->Execute($query) or die($query);
	$data_str="";
	while(!$res->EOF) {
		$email_pass=$res->fields[email_pass];
		$student_sn=$res->fields[student_sn];
		$pass=($mode)?"<input type=text name='email_pass[$student_sn]' value='$email_pass' size='10' maxlength='10'>":$email_pass; 
		$data_str.="<tr bgcolor=#FFFFFF><td align='center'>".$res->fields[stud_id]."</td><td align='center'>".$res->fields[stud_name]."</td><td align='center'>".$pass."</td><td align='center'><font color=#000088>".$stud_study_cond[$res->fields[stud_study_cond]]."</font></td></tr>\n";
		$res->MoveNext();
	}
	$main.="<table border=0 cellspacing=1 cellpadding=2 bgcolor=#9ebcdd class=small>\n
		<tr bgcolor=#c4d9ff><td align='center'>學號</td><td align='center'>姓名</td><td align='center'>密碼</td><td align='center'>就學狀態</td></tr>\n";
	if ($data_str) {
		$main.=$data_str."</table>";
	} else {
		$main.="<tr bgcolor=#FFFFFF><td align='center' colspan='4'>查無此學生</td></tr></table><input type='hidden' name='error' value='1'>\n";
	}
}
$main.="</tr></form></table>\n";
echo $main;

//佈景結尾
foot();
?>
