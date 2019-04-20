<?php

// $Id: stud_drop.php 9030 2017-01-17 03:54:07Z infodaes $

// 載入設定檔
include "stud_reg_config.php";

// 認證檢查
sfs_check();


// 不需要 register_globals
if (!ini_get('register_globals')) {
	ini_set("magic_quotes_runtime", 0);
	extract( $_POST );
	extract( $_GET );
	extract( $_SERVER );
}



//印出檔頭
head();

if (!$modify_flag){
	trigger_error('未獲授權操作',256);
}
//欄位資訊
$field_data = get_field_info("stud_base");
//選單連結字串
$linkstr = "stud_id=$stud_id&c_curr_class=$c_curr_class&c_curr_seme=$c_curr_seme";
//模組選單
print_menu($menu_p,$linkstr);
if($sure_del=="yes"){
	foreach($choice as $del_student_sn){		
		//找出學號
		$stud_id=student_sn2stud_id($del_student_sn);
		
		//刪除前檢查該生的成績資料是否有存在，有則不准刪除
		$sql_score="select count(*) from stud_seme_score where student_sn='$del_student_sn' ";
		$rs_score=$CONN->Execute($sql_score) or trigger_error($sql,256);
		$count_score=$rs_score->fields[0];
		if($count_score==0){  				

			//將該生stud_base給刪除掉		
			$del_msg.="---執行日期：".date("l dS of F Y h:i:s A")."---執行人：".$_SESSION['session_tea_name'] ."(".$_SESSION['session_log_id'] .")\n";
			$sql_del="delete from stud_base where student_sn='$del_student_sn' ";
			$rs_del=$CONN->Execute($sql_del) or trigger_error($sql_del,256);
			if($rs_del) {			
				$del_msg.="刪除 『流水號：$del_student_sn 』，『學號：$stud_id 』 的學生資料表\n";
			}

			//將該生stud_seme給刪除掉
			//$seme_year_seme=sprintf("%03d%d",$curr_year,$curr_seme);
			$sql_del2="delete from stud_seme where student_sn='$del_student_sn' ";
			$rs_del2=$CONN->Execute($sql_del2) or trigger_error($sql_del2,256);
			if($rs_del2) {			
				$del_msg.="刪除 『流水號：$del_student_sn 』，『學號：$stud_id 』 的學期資料表\n";
			}

			//將該生stud_domicile給刪除掉
			$sql_del3="delete from stud_domicile where stud_id='$stud_id' and student_sn='$del_student_sn' ";
			$rs_del3=$CONN->Execute($sql_del3) or trigger_error($sql_del3,256);
			if($rs_del3) {			
				$del_msg.="刪除 『流水號：$del_student_sn 』，『學號：$stud_id 』 的戶籍資料表\n";
			}

			$del_msg.="\n\n";


			//順便寫入紀錄檔YouKill.log
			$dir_name= $UPLOAD_PATH."/log";	
			if(!is_dir ($dir_name)) mkdir ("$dir_name", 0777);	
			$file_name= $dir_name."/YouKill.log..";	
			$FD=fopen ($file_name, "a");
			fwrite ($FD, $del_msg);	
			fclose ($FD);
			
			//提供該生可以還原的sql檔案
		}
		else $main.="學號 $stud_id 的成績資料存在，不准刪除！<br>";
	}
}

//建立學期，班級選單
/*
$class_seme_array=get_class_seme();
$class_seme_select.="<form action='{$_SERVER['PHP_SELF']}' method='POST' name='form1'>\n<select  name='class_seme' onchange='this.form.submit()'>\n";
$i=0;
foreach($class_seme_array as $k => $v){
	if(!$class_seme) $class_seme=sprintf("%03d%d",curr_year(),curr_seme());
	$selected[$i]=($class_seme==$k)?" selected":" ";	
	$class_seme_select.="<option value='$k'$selected[$i] >$v</option> \n";
	$i++;
}
$class_seme_select.="</select></form>\n";
*/

$class_base_array=class_base($class_seme);

$class_seme=sprintf("%03d%d",curr_year(),curr_seme());
$class_base_select.="<form action='{$_SERVER['PHP_SELF']}' method='POST' name='form2'>\n<select  name='class_base' onchange='this.form.submit()'>\n";
$j=0;
foreach($class_base_array as $k2 => $v2){
	if(!$class_base) $class_base=$k2;
	$selected2[$j]=($class_base==$k2)?" selected":" ";	
	$class_base_select.="<option value='$k2'$selected2[$j] >$v2</option> \n";
	$j++;
}
$class_base_select.="</select><input type='hidden' name='class_seme' value='$class_seme'></form>\n";
$menu="<td nowrap width='1%'>$class_seme_select</td><td nowrap>班級：$class_base_select </td>";

//列出基本資料以供刪除

//1.找出流水號和學號
$class_id=sprintf("%03d_%d_%02d_%02d",substr($class_seme,0,-1),substr($class_seme,-1),substr($class_base,0,-2),substr($class_base,-2));
$student_sn_array=class_id_to_student_sn($class_id);

$total=count($student_sn_array);
$main.="<table cellspacing=1 cellpadding=6 border=0  bgcolor='#00AB00' >
<form action='{$_SERVER['PHP_SELF']}' method='POST' name='D1'>
<tr bgcolor='#C4FAAE'><td><a href='{$_SERVER['PHP_SELF']}?choice_all=1&class_seme=$class_seme&class_base=$class_base'><font class='button'>全選</font></a></td><td>學號</td><td>姓名</td><td>座號</td></tr>
";
$checked=($_GET['choice_all']==1)?" checked":"";
$i=1;
foreach($student_sn_array as $sn_val){
	//找出姓名和學號，座號
	$st_data=student_sn_to_name_num($sn_val);		
	$main.="<tr bgcolor='#FFFFFF'><td><input type='checkbox' name='choice[$i]' value='$sn_val'$checked></td><td>".$st_data[0]."</td><td>".$st_data[1]."</td><td>".$st_data[2]."</td></tr>";
	$i++;
}
$main.="<tr bgcolor='#C4FAAE'><td colspan='4'>
<input type='hidden' name='sure_del' value='yes'>
<input type='hidden' name='class_seme' value='$class_seme'>
<input type='hidden' name='class_base' value='$class_base'>
<input type='button' value='刪除' onclick=\"if(confirm('您確定要刪除？')) this.form.submit()\">
</td></tr></form></table>";
//設定主網頁顯示區的背景顏色
$back_ground="
	<table cellspacing=1 cellpadding=0 border=0  bgcolor='#BBBBBB' width='100%'><tr><td>
	<table cellspacing=1 cellpadding=6 border=0 bgcolor='#FFFFFF' width='100%'>
		<tr>
			$menu
		</tr>
		<tr>
			<td colspan='2'>
				$main
			</td>
		</tr>		
	</table></td></tr></table>";
echo $back_ground;

//印出尾頭
foot();
?> 
