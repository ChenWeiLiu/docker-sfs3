<?php
//$Id: index.php 9094 2017-06-19 03:42:56Z infodaes $
include "config.php";

//認證
sfs_check();

//秀出網頁布景標頭
head("學生密碼管理");

//主要內容
$num=intval($_POST[num]);
$stud_study_year=$_POST[stud_study_year];
$rsel[intval($_POST[range])]="checked";
$law=$_POST[law];
$lsel[intval($law)]="checked";
$str=$_POST[str];
if ($num==0) $num=9;
print_menu($school_menu_p);

//開始設定密碼
if ($_POST[set] && $stud_study_year) {
	  
	  //更新學生 sha key 2014.10.07 *********************/
			$query = "SELECT stud_person_id,student_sn FROM stud_base where stud_study_year='$stud_study_year' and stud_study_cond in ('0','15') ";
			$res = $CONN->Execute($query) or die($query);
			foreach($res as $row) {
			 if ($row['stud_person_id']!="") {
				 	$stud_person_id = hash('sha256', strtoupper($row['stud_person_id']));
				 	$sql = "UPDATE stud_base SET edu_key='$stud_person_id' WHERE student_sn='{$row['student_sn']}'";
					$CONN->Execute($sql) ;
			 }
		  } // end foreach
		  /*******************************************/
	$chk_str="";
	if ($rsel[0]) $chk_str=" stud_study_year='$stud_study_year'";
	if ($rsel[1]) $chk_str=" stud_study_year='$stud_study_year' and stud_study_cond in ('0','15')";
	if ($rsel[2]) $chk_str=" stud_study_cond in ('0','15') AND (ISNULL(ldap_password) OR ldap_password='')";
	$pass="NULL";
	switch ($law) {
		case 0:
			$query="update stud_base set email_pass='', ldap_password=''  where $chk_str";
			$CONN->Execute($query) or die($query);
			break;
		case 1:
			$ldap_password = createLdapPassword($str);
			$query="update stud_base set email_pass='$str' , ldap_password='$ldap_password' where $chk_str";
            $CONN->Execute($query) or die($query);
			break;
		case 2:
			$c=$_POST[content1];
			$query = "SELECT stud_birthday, student_sn, email_pass, ldap_password FROM stud_base
					where $chk_str ";
			$res = $CONN->Execute($query) or die($query);
			foreach($res as $row) {
				if ($c==0) $pass=$row['stud_birthday'];				
				if ($c==1) $pass=substr($row['stud_birthday'],2);
				if ($c==2) {
					$tempArr = explode("-", $row['stud_birthday']);
					$pass= $tempArr[1].$tempArr[2];
				}
								
				$ldap_password = createLdapPassword($pass); 
				$query="update stud_base set email_pass='$pass' , ldap_password='$ldap_password'  WHERE student_sn={$row['student_sn']}";
				
				$CONN->Execute($query) or die($query);
			}
			break;
		case 3:
			$c=$_POST[content2];
			$query = "SELECT stud_person_id, student_sn, email_pass, ldap_password FROM stud_base
			where $chk_str ";
			$res = $CONN->Execute($query) or die($query);
			foreach($res as $row) {
				if ($c==0 && $num) 
					$pass= substr($row['stud_person_id'],1,$num);
				if ($c==1 && $num)
					$pass= substr($row['stud_person_id'],-$num);
				
				$ldap_password = createLdapPassword($pass);
				$query="update stud_base set email_pass='$pass'  , ldap_password='$ldap_password'
				WHERE student_sn={$row['student_sn']}";
				$CONN->Execute($query) or die($query);
			}
			break;
		case 4:
			$query="SELECT student_sn FROM stud_base WHERE (email_pass IS NULL or email_pass='') AND $chk_str";
			$res = $CONN->Execute($query) or die($query);
			while(!$res->EOF) {
				$randval = "";
				for ($i=0;$i<$_POST['s_eng_num'];$i++)	
				$randval .= chr(rand(97,122));
				for ($i=0;$i<$_POST['s_math_num'];$i++)	
					$randval .= chr(rand(49,57));
				$ldap_password = createLdapPassword($randval);
				$query = "UPDATE stud_base SET email_pass='$randval' , ldap_password='$ldap_password' WHERE student_sn=".$res->fields['student_sn'];
				$CONN->Execute($query) or die($query);
				$res->MoveNext();
			}
			break;		
	}
	
}

//顯示選項
$main="	<table border=0 cellspacing=1 cellpadding=2 width=100% bgcolor=#cccccc class=small>\n
	<form method='post' action='{$_SERVER['PHP_SELF']}'><tr><td bgcolor='#FFFFFF'>
	<br><fieldset>\n<legend>學生入學學年度</legend>\n";
$query="select distinct stud_study_year,count(student_sn) as nums from stud_base group by stud_study_year order by stud_study_year";
$res=$CONN->Execute($query) or die($query);
while(!$res->EOF) {
	$study_year=$res->fields[stud_study_year];
	$query_count="select count(student_sn) as nums from stud_base where stud_study_year='$study_year' and (email_pass is NULL or email_pass='')";
	$res_count=$CONN->Execute($query_count) or die($query_count);
	$checked=($stud_study_year==$study_year)?"checked":"";
	$main.="<input type='radio' name='stud_study_year' value='$study_year' $checked>".$study_year."學年度(<font color='#000088'>共".$res->fields[nums]."人</font><-><font color='#ff0000'>未設定密碼".$res_count->fields[nums]."人</font>)<br>\n";
	$res->MoveNext();
}
$main.="</fieldset>\n<br>";
$onchg="onchange='this.form.submit();'";
$main.="<fieldset>\n
	<legend>設定學生範圍</legend>\n
	<input type='radio' name='range' value='0' $rsel[0]>該學年度所有學生<br>\n
	<input type='radio' name='range' value='1' $rsel[1]>該學年度目前在學學生(含在家自學學生)<br>\n
	<input type='radio' name='range' value='2' $rsel[2]>所有在籍與在家自學但尚未設定密碼學生 ( 跨入學年 ) <br>\n
	</fieldset>\n<br>
	<fieldset>\n
	<legend>密碼設定規則</legend>\n
	<input type='radio' name='law' value='0' $lsel[0] $onchg>清除<br>\n
	<input type='radio' name='law' value='1' $lsel[1] $onchg>設定成相同密碼<br>\n
	<input type='radio' name='law' value='2' $lsel[2] $onchg>設定成學生出生年月日<br>\n
	<input type='radio' name='law' value='3' $lsel[3] $onchg>設定成學生身分證字號<br>\n
	<input type='radio' name='law' value='4' $lsel[4] $onchg>設定成英文加數字<br>\n
	</fieldset>\n";
if ($law>0) {
	$main.="<br><fieldset>\n<legend>密碼內容</legend>\n";
	switch($law) {
		case 1:
			$main.="統一將密碼設定成<input type='text' name='str' value='$str' maxlength='10' size='10'>";
			break;
		case 2:
			$csel[intval($content1)]="checked";
			$main.="<input type='radio' name='content1' value='0' $csel[0]>西元年-月-日(yyyy-mm-dd)<br>\n
				<input type='radio' name='content1' value='1' $csel[1]>西元年後兩碼-月-日(yy-mm-dd)<br>\n
				<input type='radio' name='content1' value='2' $csel[2]>月日(mmdd)<br>\n";
			break;
		case 3:
			$csel[intval($content2)]="checked";
			$main.="<table border=0 class=small><tr><td>
				<input type='radio' name='content2' value='0' $csel[0]>學生身分證字號前<br>\n
				</td><td rowspan=2 valign=middle>\n
				&nbsp;<input type='text' name='num' value='$num' maxlength='1' size='1'>碼(不含英文字)<br>\n
				</td></tr>\n
				<tr><td>\n
				<input type='radio' name='content2' value='1' $csel[1]>學生身分證字號後<br>\n
				</td></tr></table>\n";
			break;
		case 4:
		$csel[intval($content3)]="checked";
		$eng_num = 2;
		if (isset($_POST['s_eng_num']))
			$eng_num =$_POST['s_eng_num'];
		$math_num = 3;
		if (isset($_POST['s_meth_num']))
			$math_num =$_POST['s_math_num'];
			
		$main .= "	密碼起始英文字數：<input type=text name='s_eng_num'  size=2 maxlength=10 value='$eng_num'><br>\n
									密碼起始數字字數：<input type=text name='s_math_num' size=2 maxlength=10  value='$math_num'>";
		break;
	}
	
	$main.="</fieldset>\n";
}
$main.="<br><input type='submit' name='set' value='開始設定'>\n
	</tr></form></table>\n";
echo $main;

//佈景結尾
foot();
?>
