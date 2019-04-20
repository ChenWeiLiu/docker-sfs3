<?php

// $Id: parent_manage.php 6707 2012-03-01 02:44:10Z infodaes $

/*引入學務系統設定檔*/
include "config.php";

$act=($_GET['act'])?$_GET['act']:$_POST['act'];
$id=($_GET['id'])?$_GET['id']:$_POST['id'];
$stid=($_GET['stid'])?$_GET['stid']:$_POST['stid'];
$Submit1=($_GET['Submit1'])?$_GET['Submit1']:$_POST['Submit1'];
$enable_stat=($_GET['enable_stat'])?$_GET['enable_stat']:$_POST['enable_stat'];
$parent_password=($_GET['parent_password'])?$_GET['parent_password']:$_POST['parent_password'];
$parent_id=($_GET['parent_id'])?$_GET['parent_id']:$_POST['parent_id'];

//使用者認證
sfs_check();
if(empty($sel_year))$sel_year = curr_year(); //目前學年
if(empty($sel_seme))$sel_seme = curr_seme(); //目前學期
$teacher_sn=$_SESSION['session_tea_sn'];//取得登入老師的id

//秀出網頁
head("班級事務");
print_menu($menu_p);
//設定主網頁顯示區的背景顏色
echo "<table border=0 cellspacing=1 cellpadding=2 width=100% bgcolor=#cccccc><tr><td bgcolor='#FFFFFF'>";

if($Submit1=="儲存"){
	//執行replace
	if($enable_stat=="0") $sql_replace = "delete from parent_auth where parent_id='$parent_id' and parent_pass='$parent_password'";
	else $sql_replace = "replace into parent_auth(parent_id,parent_pass,enable) values ('$parent_id','$parent_password','$enable_stat')";
	$CONN->Execute($sql_replace);
	//echo $parent_pass.$parent_id."-----------".$enable_stat;
}

//列出家長帳號名單

//找出任教班級
$seme_year_seme=sprintf("%03d%d",$sel_year,$sel_seme);
$seme_class=teacher_sn_to_class_name($teacher_sn);
$sql="select stud_id,student_sn from stud_seme  where seme_year_seme='$seme_year_seme' and seme_class='$seme_class[0]'";
$rs=$CONN->Execute($sql);
$m=0;
while(!$rs->EOF){
        $stud_id[$m] = $rs->fields["stud_id"];
        $student_sn[$m] = $rs->fields["student_sn"];
	//還在藉嗎?
	if(!stud_id_live($stud_id[$m])) {$m++; $rs->MoveNext(); continue;};
	$stud_name[$m]=stud_id_to_stud_name($stud_id[$m]);
	$sql_guar="select sd.guardian_name , sd.guardian_p_id from stud_domicile as sd , stud_seme as ss where ss.stud_id='$stud_id[$m]' and sd.stud_id='$stud_id[$m]' ";
	$rs_guar=$CONN->Execute($sql_guar);		
	$guardian_name[$m] = $rs_guar->fields["guardian_name"];
	$guardian_p_id[$m] = $rs_guar->fields["guardian_p_id"];	
	$sql_parent="select * from parent_auth where parent_id='$guardian_p_id[$m]' ";
	//echo $sql_parent;
	$rs_parent=$CONN->Execute($sql_parent);
	$parent_sn[$m] = $rs_parent->fields["parent_sn"];
	//若這位家長的id尚未存在於parent_auth的話就幫他建立並給一個啟動碼
	if($parent_sn[$m]=="" && $guardian_p_id[$m]) {
		//隨機製造一個密碼
		$new_code[$m]=creat_code();
		//寫入parent_auth資料表
		$CONN->Execute("insert into parent_auth(parent_id,start_code,enable) values('$guardian_p_id[$m]','$new_code[$m]','1')");		
		//重新取一次新值
		$rs_parent = $CONN->Execute("select * from parent_auth where parent_id='$guardian_p_id[$m]' ");		
		$parent_sn[$m] = $rs_parent->fields["parent_sn"];					
	}
	$login_id[$m]=$rs_parent->fields['login_id'];
	$parent_pass[$m]=$rs_parent->fields['parent_pass'];
	$start_code[$m]=$rs_parent->fields['start_code'];
	$email[$m]=$rs_parent->fields['email'];	
	$date[$m]=$rs_parent->fields['date'];
	$enable[$m]=$rs_parent->fields["enable"];	
	if($enable[$m]==2) $C_enable[$m]="<font color='#456AEE'>啟用</font>"; 
	elseif($enable[$m]==3) $C_enable[$m]="<font color='#D844EB'>停用</font>"; 
	elseif($enable[$m]==1) $C_enable[$m]="<font color='#C11212'>尚未啟動</font>"; 
	else $C_enable[$m]="<font color='#FF0000'><a href='../stud_class/stud_dom1.php?stud_id=$stud_id[$m]'>監護人資料未建立</a></font>"; 
	//若這位家長的id尚未存在於parent_auth的話就幫他建立並給一個啟動碼
	
	
	
	
	
	//if($parent_sn[$m]) $check="yes";
	//else $check="no";		
	/*
	if($act=="edit" && $id==$guardian_p_id[$m] && $stid==$stud_id[$m]) {
		if($parent_pass[$m]=="") $parent_pass[$m]=$guardian_p_id[$m];
		if($guardian_p_id[$m]=="") $main.="<tr bgcolor='#FFFFFF'><td colspan='6'>$stud_name[$m]的監護人身份尚未建立完整，<a href='../stud_class/stud_dom1.php'>前往建立</a></td></tr>";		
		else $main.="<form name='form1' method='post' action='{$_SERVER['PHP_SELF']}' >
								<input type='hidden' name='parent_id' value='$guardian_p_id[$m]'>
								<tr bgcolor='#FFFFFF'>
									<td>$stud_name[$m]</td>
									<td>$guardian_name[$m]</td>
									<td>$guardian_p_id[$m]</td>
									<td><input type='text' name='parent_password' size=10 maxlength=20 value='$parent_pass[$m]'></td>
									<td><select name='enable_stat'>
											<option value='1' $selected1[$m]>啟用</option>
											<option value='2' $selected2[$m]>停用</option>
											<option value='0' $selected3[$m]>刪除</option>
											</select></td>
									<td><input type='submit' name='Submit1' value='儲存'></td>
								</tr>
							</form>";
	}
	else $main.="<tr bgcolor='#FFFFFF'><td >$stud_name[$m]</td><td>$guardian_name[$m]</td><td>$guardian_p_id[$m]</td><td>$parent_pass[$m]</td><td>$C_enable[$m]</td><td><a href='{$_SERVER['PHP_SELF']}?act=edit&id={$guardian_p_id[$m]}&stid={$stud_id[$m]}'><button>編輯</button></a></td></tr>";
	*/
	$main.="<tr bgcolor='#FFFFFF'><td >$stud_name[$m]</td><td>$guardian_name[$m]</td><td>$guardian_p_id[$m]</td><td>$login_id[$m]</td><td>$parent_pass[$m]</td><td>$start_code[$m]</td><td>$C_enable[$m]</td><td>$email[$m]</td></tr>";
	$m++;
	$rs->MoveNext();
}
echo "<table  bgcolor='#000000' border=0 cellspacing=1 cellpadding=2>
		<tr bgcolor='#EEE726'><td>學生姓名</td><td>家長姓名</td><td>身分證字號</td><td>帳號</td><td>密碼</td><td>啟動碼</td><td>狀態</td><td>E-MAIL</td></tr>
		$main</table>";
	
	
//結束主網頁顯示區
echo "</td>";
echo "</tr>";
echo "</table>";
//程式檔尾
foot();

function creat_code($level="",$many_char=""){		
		$number="1234567890";
		$small="abcdefghijklmnopqrstuvwxyz";
		$big="ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$special="!@$%^&*()_+|=-[]{}?/";
		$much=0;
		if($level=="") $level=4;
		if($level>="1") {$passwordsource.=$number; $much=$much+10;}
		if($level>="2") {$passwordsource.=$small; $much=$much+26;}
		if($level>="3") {$passwordsource.=$big; $much=$much+26;}
		if($level>="4") {$passwordsource.=$special; $much=$much+21;}
		if($many_char=="") $many_char=10;
		for ($i=0;$i<$many_char;$i++){
			srand ((double) microtime() * 1000000);
			$value=rand(0,$much-1);
			$password[$i]=substr($passwordsource,$value,1);
		}
		$password=implode("",$password);
	return $password;	
}	
?>
