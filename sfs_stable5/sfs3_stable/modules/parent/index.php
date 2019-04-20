<?php

// $Id: index.php 5310 2009-01-10 07:57:56Z hami $

/*引入學務系統設定檔*/
require "config.php";

if($_GET['class_year_b']) $class_year_b=$_GET['class_year_b'];
else $class_year_b=$_POST['class_year_b'];

$new_loginid=($_GET['new_loginid'])?$_GET['new_loginid']:$_POST['new_loginid'];
$new_pass=($_GET['new_pass'])?$_GET['new_pass']:$_POST['new_pass'];
$new_pass2=($_GET['new_pass2'])?$_GET['new_pass2']:$_POST['new_pass2'];
$act=($_GET['act'])?$_GET['act']:$_POST['act'];
$submit_passt=($_GET['submit_pass'])?$_GET['submit_pass']:$_POST['submit_pass'];
$stud_id=($_GET['stud_id'])?$_GET['stud_id']:$_POST['stud_id'];

//使用者認證
sfs_check();
//程式檔頭
head("親職通聯");

print_menu($menu_p);
//設定主網頁顯示區的背景顏色


//網頁內容請置於此處
if(empty($sel_year))$sel_year = curr_year(); //目前學年
if(empty($sel_seme))$sel_seme = curr_seme(); //目前學期

//執行動作判斷
if($act=="score"){
	$main=&sea_score();	
}elseif($act=="absent"){
	$main=&sea_absent();
}elseif($act=="chpass"){
	$main=&sea_chpass();
}elseif($act=="chpass_a"){
	$main=&chpass_a();
}elseif($act=="show_score"){
	$main=&show_score($stud_id);	
}elseif($act=="show_absent"){
	$main=&show_absent($stud_id);	
}else{
	$main=&homebook();
}


//秀出網頁

echo $main;
foot();

function &homebook(){
	global $CONN,$parent_menu_p;
	
	// 確定連線成立
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);	
	
	if(empty($sel_year))$sel_year = curr_year(); //目前學年
	if(empty($sel_seme))$sel_seme = curr_seme(); //目前學期
	$tool_bar=&make_menu($parent_menu_p);
	 $child_A=&get_child();
	 //echo $child_A[0][name];
	 foreach($child_A as $v){
	 	$C_v[num]=intval($v[num]);
		$C_v[Cclass]=class_id2big5($v[Cclass],$sel_year,$sel_seme);
	 	$child_list.="<tr  bgcolor='#FFCAF8'><td>$v[id]</td><td>$C_v[Cclass]</td><td>$C_v[num]</td><td><a href='home_book.php?stud_id=$v[id]'>$v[name]</a></td></tr>";
	 }
	$main="
	$tool_bar
	<table width='100%' cellspacing=1 cellpadding='6' bgcolor='#E1B2DB'><tr bgcolor='#FFCAF8'><td>
		<table width='30%' cellspacing=1 cellpadding='2' bgcolor='#E1B2DB'>
			<tr bgcolor='#FFCAF8'><td>學號</td><td>班級</td><td>座號</td><td>姓名</td></tr>
			$child_list
		</table>	</td></tr>	
	</table>	
	";
	return $main;	
}

function &sea_score(){
	global $CONN,$parent_menu_p;
	
	// 確定連線成立
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);	
	
	if(empty($sel_year))$sel_year = curr_year(); //目前學年
	if(empty($sel_seme))$sel_seme = curr_seme(); //目前學期
	$tool_bar=&make_menu($parent_menu_p);
	 $child_A=&get_child();
	 //echo $child_A[0][name];
	 foreach($child_A as $v){
	 	$C_v[num]=intval($v[num]);
		$C_v[Cclass]=class_id2big5($v[Cclass],$sel_year,$sel_seme);
	 	$child_list.="<tr  bgcolor='#FFCAF8'><td>$v[id]</td><td>$C_v[Cclass]</td><td>$C_v[num]</td><td><a href='{$_SERVER['PHP_SELF']}?act=show_score&stud_id=$v[id]'>$v[name]</a></td></tr>";
	 }
	$main="
	$tool_bar
	<table width='100%' cellspacing=1 cellpadding='6' bgcolor='#E1B2DB'><tr bgcolor='#FFCAF8'><td>
		<table width='30%' cellspacing=1 cellpadding='2' bgcolor='#E1B2DB'>
			<tr bgcolor='#FFCAF8'><td>學號</td><td>班級</td><td>座號</td><td>姓名</td></tr>
			$child_list
		</table>	</td></tr>	
	</table>	
	";
	return $main;
}

function &sea_absent(){
	global $CONN,$parent_menu_p;
	
	// 確定連線成立
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);		
	
	if(empty($sel_year))$sel_year = curr_year(); //目前學年
	if(empty($sel_seme))$sel_seme = curr_seme(); //目前學期
	$tool_bar=&make_menu($parent_menu_p);
	 $child_A=&get_child();
	 //echo $child_A[0][name];
	 foreach($child_A as $v){
	 	$C_v[num]=intval($v[num]);
		$C_v[Cclass]=class_id2big5($v[Cclass],$sel_year,$sel_seme);
	 	$child_list.="<tr  bgcolor='#FFCAF8'><td>$v[id]</td><td>$C_v[Cclass]</td><td>$C_v[num]</td><td><a href='{$_SERVER['PHP_SELF']}?act=show_absent&stud_id=$v[id]'>$v[name]</a></td></tr>";
	 }
	$main="
	$tool_bar
	<table width='100%' cellspacing=1 cellpadding='6' bgcolor='#E1B2DB'><tr bgcolor='#FFCAF8'><td>
		<table width='30%' cellspacing=1 cellpadding='2' bgcolor='#E1B2DB'>
			<tr bgcolor='#FFCAF8'><td>學號</td><td>班級</td><td>座號</td><td>姓名</td></tr>
			$child_list
		</table>	</td></tr>	
	</table>	
	";
	return $main;
}

function &sea_chpass(){
	global $CONN,$parent_menu_p;
	
	// 確定連線成立
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);		
	
	$tool_bar=&make_menu($parent_menu_p);

	$main="
	$tool_bar
	<table width='100%' cellspacing=1 cellpadding='6' bgcolor='#E1B2DB'><tr bgcolor='#FFCAF8'><td>更改（{$_SESSION['session_tea_name']}）的帳號密碼<p></p>
		<table width='30%' cellspacing=1 cellpadding='2' bgcolor='#E1B2DB'>
			<form name='chpass_form' method='post' action='{$_SERVER['PHP_SELF']}'>
			<tr bgcolor='#FFCAF8'><td>帳號</td><td><input type='text' name='new_loginid' size=20 maxlength=10 value='{$_SESSION['session_log_id']}'></td></tr>
			<tr bgcolor='#FFCAF8'><td>密碼</td><td><input type='password' name='new_pass' size=20 maxlength=10></td></tr>
			<tr bgcolor='#FFCAF8'><td>密碼確認</td><td><input type='password' name='new_pass2' size=20 maxlength=10></td></tr>
			<input type='hidden' name='act' value='chpass_a'>
			<tr bgcolor='#FFCAF8'><td colspan=='2'><input type='submit' name='submit_pass' value='送出'></td></tr>
			</form>
		</table>			
	</table>	
	";
	return $main;
}

function &chpass_a(){
	global $CONN,$parent_menu_p,$new_loginid,$new_pass,$new_pass2;	
	
	// 確定連線成立
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);		
	
	$tool_bar=&make_menu($parent_menu_p);
	if($new_loginid==$_SESSION['session_log_id']) $A1=0;
	else{
		//新帳號是否已被用了
		$sql1="select count(*) from parent_auth where login_id='$new_loginid'";
		$rs1=$CONN->Execute($sql1);
		$A1=$rs1->fields[0];
	}
	if ($A1==0) {
		//新帳號是否已被用了(檢查是否使用別人的身份證字號)
		$sql1="select count(*) from parent_auth where parent_id='$new_loginid'";
		$rs1=$CONN->Execute($sql1);
		$A1=$rs1->fields[0];
	}

	//密碼是否相符
	if($new_loginid=="") $msg= "帳號不可為空值<span class='button'><a href='{$_SERVER['PHP_SELF']}?act=chpass'>重新更改</a></span>";
	elseif($new_pass!=$new_pass2) $msg= "兩次所輸入的密碼不相符<span class='button'><a href='{$_SERVER['PHP_SELF']}?act=chpass'>重新更改</a></span>";
	elseif(strlen($new_pass)<=3 || strlen($new_pass2)<=3) $msg= "密碼字元數不得低於4位<span class='button'><a href='{$_SERVER['PHP_SELF']}?act=chpass'>重新更改</a></span>";
	elseif($A1!=0) $msg= "該帳號已經有人使用，請另取帳號<span class='button'><a href='{$_SERVER['PHP_SELF']}?act=chpass'>重新更改</a></span>";
	else {//更新帳號密碼
		$sql="update parent_auth set login_id='$new_loginid',parent_pass='$new_pass' where  login_id='{$_SESSION['session_log_id']}'";
		$CONN->Execute($sql) or trigger_error("新帳號密碼更新失敗" ,E_USER_ERROR);
		if($new_loginid!=$_SESSION['session_log_id']) {
			$_SESSION['session_log_id'] = "";
			$_SESSION['session_tea_sn'] = "";
			$_SESSION['session_tea_name'] = "";
			$_SESSION['session_who'] = "";
			$_SESSION['session_prob'] = "";				
			$msg="您的帳號密碼已經更新成功！請以新帳號重新登入。";
		} 
		else $msg="您的密碼已經更新成功！";
	}	
	$main="
	$tool_bar
	$msg
	";
	return $main;
}

function &show_score($stud_id){
	global $CONN,$parent_menu_p,$test_sort_name;
	
	// 確定連線成立
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);		
	
	$tool_bar=&make_menu($parent_menu_p);
	//本學期目前該生已有的成績（已經送到教務處的）
	if(empty($sel_year))$sel_year = curr_year(); //目前學年
	if(empty($sel_seme))$sel_seme = curr_seme(); //目前學期	
	$score_semester="score_semester_".$sel_year."_".$sel_seme;
	//學號變序號
    $rs_sn=$CONN->Execute("select  student_sn  from  stud_base where stud_id='$stud_id'")  or trigger_error("SQL語法錯誤", E_USER_ERROR);;    
	$student_sn=$rs_sn->fields['student_sn'];	

	$sql="select * from $score_semester where 1=0";	
	if(!$CONN->Execute($sql)) {
		$msg="本學期成績尚未建立！";
		$main="
		$tool_bar
		<table width='100%' cellspacing=1 cellpadding='6' bgcolor='#E1B2DB'><tr bgcolor='#FFCAF8'><td>
			<table width='30%' cellspacing=1 cellpadding='2' bgcolor='#E1B2DB'>
				<tr bgcolor='#FFCAF8'>$msg</tr>			
			</table>	</td></tr>	
		</table>";
		return $main;
	}
	
	//找科目階段成績
	$sql="select * from $score_semester where student_sn='$student_sn' and sendmit='0' order by test_sort";	
	$rs=$CONN->Execute($sql) or trigger_error("SQL語法錯誤： $sql", E_USER_ERROR);
	$i=0;
	//找學生姓名
	$sql_cn="select stud_name from stud_base where stud_id='$stud_id'";
	$rs_cn=$CONN->Execute($sql_cn);
	$child_name=$rs_cn->fields['stud_name'];
	$msg.="<span style='background-color: rgb(255, 255, 153);'>".$child_name." 本學期的成績</span><p>";
	while(!$rs->EOF){
		$ST[$i][ss_id]=$rs->fields['ss_id'];
		//科目轉中文/
		$Ch_ST[$i][ss_id]=ss_id_to_subject_name($ST[$i][ss_id]);
		
		$ST[$i][test_sort]=$rs->fields['test_sort'];
		//階段轉中文		
		$Ch_ST[$i][test_sort]=$test_sort_name[$ST[$i][test_sort]];
		
		$ST[$i][score]=$rs->fields['score'];
		if($ST[$i][score]<0 || $ST[$i][score]>255) $ST[$i][score]="";
		$msg.=$Ch_ST[$i][test_sort]."的".$Ch_ST[$i][ss_id]."科成績： <span style='background-color: rgb(185, 194, 253);'>".$ST[$i][score]."</span> 分<p>";		
		$i++;
		$rs->MoveNext();
	}
	$main="
	$tool_bar
	<table width='100%' cellspacing=1 cellpadding='6' bgcolor='#E1B2DB'><tr bgcolor='#FFCAF8'><td>
		<table width='30%' cellspacing=1 cellpadding='2' bgcolor='#E1B2DB'>
			<tr bgcolor='#FFCAF8'>$msg</tr>			
		</table>	</td></tr>	
	</table>	
	";
	return $main;	
}

function &show_absent($stud_id){
	global $CONN,$parent_menu_p;
	
	// 確定連線成立
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);		
	
	$tool_bar=&make_menu($parent_menu_p);
	//本學期目前該生的出缺席情形
	if(empty($sel_year))$sel_year = curr_year(); //目前學年
	if(empty($sel_seme))$sel_seme = curr_seme(); //目前學期	
	$sql_absent="select * from stud_absent where stud_id='$stud_id' and year='$sel_year' and semester='$sel_seme' order by date,section";
	$rs_absent=$CONN->Execute($sql_absent) or user_error("讀取失敗！<br>$sql_absent",256);
	$i=0;
	$section_name_A=array("uf"=>"升旗","1"=>"第一節","2"=>"第二節","3"=>"第三節","4"=>"第四節","5"=>"第五節","6"=>"第六節","7"=>"第七節","8"=>"第八節","df"=>"降旗","allday"=>"整天",);
	while(!$rs_absent->EOF){
		$date[$i]=$rs_absent->fields['date'];
		$absent_kind[$i]=$rs_absent->fields['absent_kind'];
		$section[$i]=$rs_absent->fields['section'];
		$msg.="<tr bgcolor='#FFD5FB'><td>".$date[$i]."</td><td>".$section_name_A[$section[$i]]."</td><td>".$absent_kind[$i]."</td></tr>";
		$i++;
		$rs_absent->MoveNext();
	}
		
	//找學生姓名
	$sql_cn="select stud_name from stud_base where stud_id='$stud_id'";
	$rs_cn=$CONN->Execute($sql_cn);
	$child_name=$rs_cn->fields['stud_name'];
	if($msg=="") $msg="目前無任何缺席紀錄";
	$msg=$child_name."（".$sel_year."學年度".$sel_seme."學期 缺席情形）<br>".$msg;
	$main="
	$tool_bar
	<table width='100%' cellspacing=1 cellpadding='6' bgcolor='#E1B2DB'><tr bgcolor='#FFCAF8'><td>
		<table width='30%' cellspacing=1 cellpadding='2' bgcolor='#E1B2DB'>
			$msg
		</table></td></tr>	
	</table>	
	";
	return $main;	
}



//由ss_id找出科目名稱的函數
function  ss_id_to_subject_name($ss_id){
    global $CONN;
    
	// 確定連線成立
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);		
	
	$sql1="select subject_id from score_ss where ss_id=$ss_id";
    $rs1=$CONN->Execute($sql1);
    $subject_id = $rs1->fields["subject_id"];
    if($subject_id!=0){
        $sql2="select subject_name from score_subject where subject_id=$subject_id";
        $rs2=$CONN->Execute($sql2);
        $subject_name = $rs2->fields["subject_name"];
    }
    else{
        $sql3="select scope_id from score_ss where ss_id=$ss_id";
        $rs3=$CONN->Execute($sql3);
        $scope_id = $rs3->fields["scope_id"];
        $sql4="select subject_name from score_subject where subject_id=$scope_id";
        $rs4=$CONN->Execute($sql4);
        $subject_name = $rs4->fields["subject_name"];
    }
    return $subject_name;
}
?>
