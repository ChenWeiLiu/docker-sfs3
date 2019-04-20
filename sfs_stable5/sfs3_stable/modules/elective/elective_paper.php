<?php
// $Id: elective_paper.php 8514 2015-09-02 06:17:01Z smallduh $

// 引入您自己的 config.php 檔
require "config.php";

// 認證
sfs_check();

//轉換成全域變數
$act=($_POST['act'])?"{$_POST['act']}":"{$_GET['act']}";
$c_year=($_POST['c_year'])?"{$_POST['c_year']}":"{$_GET['c_year']}";
$ss_id=($_POST['ss_id'])?"{$_POST['ss_id']}":"{$_GET['ss_id']}";
$teacher_sn=($_POST['teacher_sn'])?"{$_POST['teacher_sn']}":"{$_GET['teacher_sn']}";
$group_name=($_POST['group_name'])?"{$_POST['group_name']}":"{$_GET['group_name']}";
$e_group_name=($_POST['e_group_name'])?"{$_POST['e_group_name']}":"{$_GET['e_group_name']}";
$group_id=($_POST['group_id'])?"{$_POST['group_id']}":"{$_GET['group_id']}";
$class_base=($_POST['class_base'])?"{$_POST['class_base']}":"{$_GET['class_base']}";
$elective_stu_sn=($_POST['elective_stu_sn'])?"{$_POST['elective_stu_sn']}":"{$_GET['elective_stu_sn']}";


// 叫用 SFS3 的版頭
head("分組課程設定");

// 您的程式碼由此開始
print_menu($menu_p);
$curr_year = curr_year();
$curr_seme = curr_seme();

//年級選單
$sql="select c_year from school_class where year='$curr_year' and semester='$curr_seme' and enable='1' order by c_year ";
$rs=$CONN->Execute($sql) or trigger_error($sql,256);
$i=0;
$ctrl_A=array();
while(!$rs->EOF){
	$c_y[$i]=$rs->fields['c_year'];
	if(!in_array($c_y[$i],$ctrl_A)) array_push($ctrl_A,$c_y[$i]);
	$i++;
	$rs->MoveNext();
}

foreach($ctrl_A as $v){
	if($c_year==$v) $selected=" selected";
	else $selected="";
	$option_c_year.="<option value='$v'$selected>$v 年級</option>\n";
}

$class_selecter="<form action='{$_SERVER[PHP_SELF]}' method='POST'><select name='c_year' onchange='this.form.submit()'>$option_c_year</select></form>\n";

if($c_year=="") { $c_year=$IS_JHORES+1; }

//年級科目選單
$class=array($curr_year,$curr_seme,"",$c_year);
$ss_name_arr=&get_ss_name_arr($class,$mode="長");

foreach($ss_name_arr as $kk => $vv){
	if($ss_id==$kk) $selected1=" selected";
	else $selected1="";
	$option_ss_id.="<option value='$kk'$selected1>$vv</option>\n";
}
if($option_ss_id=="") $ss_selecter="本學期的課程尚未設定";
else $ss_selecter="<form action='{$_SERVER[PHP_SELF]}' method='POST'><input type='hidden' name='c_year' value='$c_year'><select name='ss_id' onchange='this.form.submit()'><option value='0'>選擇課程</option>\n$option_ss_id</select></form>\n";

//分組名稱選單
if($ss_id){
	$group_name_arr=&get_group_name_arr($ss_id);
	foreach($group_name_arr as $kkk => $vvv){
		if($group_id==$kkk) $selected2=" selected";
		else $selected2="";
		$option_group_id.="<option value='$kkk'$selected2>$vvv[0] -- ".get_teacher_name($vvv[1])."</option>\n";
	}
	if($option_group_id=="") $group_selecter="本課程的分組尚未設定<a href='elective_tea?c_year=$c_year&ss_id=$ss_id'><img src='images/back.png' border='0'></a>";
	else {
		$group_selecter="<form action='{$_SERVER[PHP_SELF]}' method='POST'><input type='hidden' name='c_year' value='$c_year'><input type='hidden' name='ss_id' value='$ss_id'><select name='group_id' OnChange='this.form.submit()'><option value='0'>選擇分組</option>\n$option_group_id</select>\n";
		if($group_id){
			$now_mem=now_mem($group_id);
			$member=member($group_id);
			if($member==0) $member="不限";
			$group_selecter.=$now_mem." / ".$member;
		}
		$group_selecter.="<a href='elective_stu.php?c_year=$c_year&ss_id=$ss_id&group_id=$group_id'><img src='images/back.png' border='0'></a></form>";
	}

	if($group_id || $class_base){//本分組或本班學生名單
		$class_seme=sprintf("%03d%d",$curr_year,$curr_seme);
		$sel_year_arr = array($c_year);
		$class_base_array=class_base($class_seme,$sel_year_arr);
		$class_base_select.="<form action='{$_SERVER['PHP_SELF']}' method='POST' name='form2'>\n<select  name='class_base'  OnChange='this.form.submit()'>\n";
		$class_base_select.="<option value='0'>以班級顯示</option> \n";
		$j=0;
		foreach($class_base_array as $kkkk => $vvvv){
			//if(!$class_base) $class_base=$c_year."01";
			$selected3[$j]=($class_base==$kkkk)?" selected":" ";
			$class_base_select.="<option value='$kkkk'$selected3[$j] >$vvvv</option> \n";
			$j++;
		}
		$class_base_select.="</select>
		<input type='hidden' name='class_seme' value='$class_seme'>
		<input type='hidden' name='c_year' value='$c_year'>
		<input type='hidden' name='ss_id' value='$ss_id'>
		<input type='hidden' name='group_id' value='$group_id'>
		<input type='hidden' name='act' value='by_class'>
		</form>\n";

		if($act=="kill" && $elective_stu_sn){
			$sql_b="delete from elective_stu where elective_stu_sn='$elective_stu_sn' ";
			$CONN->Execute($sql_b) or trigger_error($sql_b,256);
		}
		//if($act=="by_class"){//以班級顯示
		if($class_base){
			$class_id=sprintf("%03d_%d_%02d_%02d",substr($class_seme,0,3),substr($class_seme,3),substr($class_base,0,-2),substr($class_base,-2));
			//由班級找出學生流水號
			$student_sn_array=seme_class_id_to_student_sn($class_id);
			$student_sn_str=implode(",",$student_sn_array);
			//找出該生該科目所選修的分組
			if($student_sn_str){
				$sql_c="select es.elective_stu_sn,es.student_sn,et.group_id,et.group_name from elective_tea as et, elective_stu as es where et.group_id=es.group_id and et.ss_id='$ss_id' and es.student_sn in ( $student_sn_str ) ";
				//echo $sql_c;
				$rs_c=$CONN->Execute($sql_c);
				$c=0;
				$group_id=array();
				$group_name=array();
				$one_student_sn=array();
				$elective_stud_sn=array();
				while(!$rs_c->EOF){
					$elective_stu_sn[$c]=$rs_c->fields['elective_stu_sn'];
					$gorup_id[$c]=$rs_c->fields['group_id'];
					$group_name[$c]=$rs_c->fields['group_name'];
					$one_student_sn[$c]=$rs_c->fields['student_sn'];
					//echo $gorup_id[$c].$group_name[$c]."<br>";
					$st_data=student_sn_to_classinfo($one_student_sn[$c]);
					if($c%2==0) $bg="#FFE2E8";
					else $bg="#DAF7CD";
					$one_student.="<tr bgcolor=$bg onmouseover=\"style.background='#FFF6BA'\" onmouseout=\"style.background='$bg'\"><td>{$st_data[2]}</td><td>{$st_data[4]}</td><td>{$group_name[$c]}</td><td>
					<form action='{$_SERVER['PHP_SELF']}' method='POST' name='KL'>
					<input type='hidden' name='c_year' value='$c_year'>
					<input type='hidden' name='ss_id' value='$ss_id'>
					<input type='hidden' name='group_id' value='$group_id[$c]'>
					<input type='hidden' name='act' value='kill'>
					<input type='hidden' name='class_base' value='$class_base'>
					<input type='hidden' name='elective_stu_sn' value='{$elective_stu_sn[$c]}'>
					<input type='button' value='刪除' onclick=this.form.submit()>
					</form></td></tr>";
					$c++;
					$rs_c->MoveNext();
				}
				if($rs_c->RecordCount()>0){
					$stud_list="<table cellspacing=1 cellpadding=6 border=0 ><tr bgcolor='#CFD4FF' ><td>座號</td><td>姓名</td><td>分組名稱</td><td></td></tr>$one_student</table>";
				}
			}else{
				$stud_list="該班目前沒有任何學生資料！";

			}
		}else{
			$sql_a="select * from elective_stu where group_id='$group_id' order by student_sn";
			$rs_a=$CONN->Execute($sql_a) or trigger_error($sql_a,256);
			$a=0;
			$elective_stu_sn=array();
			$student_sn=array();
			while(!$rs_a->EOF){
				$elective_stu_sn[$a]=$rs_a->fields['elective_stu_sn'];
				$student_sn[$a]=$rs_a->fields['student_sn'];
				$st_data=student_sn_to_classinfo($student_sn[$a]);
				if($a%2==0) $bg="#FFE2E8";
				else $bg="#DAF7CD";
				$one_student.="<tr bgcolor=$bg onmouseover=\"style.background='#FFF6BA'\" onmouseout=\"style.background='$bg'\"><td>{$st_data[0]} 年 {$st_data[1]} 班</td><td>{$st_data[2]}</td><td>{$st_data[4]}</td><td>
				<form action='{$_SERVER['PHP_SELF']}' method='POST' name='KL'>
				<input type='hidden' name='c_year' value='$c_year'>
				<input type='hidden' name='ss_id' value='$ss_id'>
				<input type='hidden' name='group_id' value='$group_id'>
				<input type='hidden' name='act' value='kill'>
				<input type='hidden' name='elective_stu_sn' value='{$elective_stu_sn[$a]}'>
				<input type='button' value='刪除' onclick=this.form.submit()>
				</form></td></tr>";
				$a++;
				$rs_a->MoveNext();
			}
			if($rs_a->RecordCount()>0){
				$stud_list="<table cellspacing=1 cellpadding=6 border=0 ><tr bgcolor='#CFD4FF' ><td>班級</td><td>座號</td><td>姓名</td><td></td></tr>$one_student</table>";
			}
		}
		$student_area="<table cellspacing=1 cellpadding=6 border=0 bgcolor='#211BC7' width='99%' align='center'><tr bgcolor='#FFFFFF'><td><table><tr><td valign='top' width='1%'>$class_base_select</td><td>$stud_list</td></tr></table></td></tr></table>";
	}
}




$main="<table align='center' width='99%'><tr><td width='1%' nowrap> $class_selecter</td><td width='1%' >$ss_selecter</td><td>$group_selecter</td></tr><tr><td colspan='3'>$student_area</td></tr></table>";
//設定主網頁顯示區的背景顏色
$back_ground="
	<table cellspacing=1 cellpadding=6 border=0 bgcolor='#B0C0F8' width='100%'>
		<tr bgcolor='#FFF6BA'>
			<td>
				$main
			</td>
		</tr>
	</table>";
echo $back_ground;

// SFS3 的版尾
foot();

?>
