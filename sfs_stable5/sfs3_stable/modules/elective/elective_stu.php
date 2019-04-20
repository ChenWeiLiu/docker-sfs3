<?php
// $Id: elective_stu.php 8510 2015-09-01 15:29:21Z smallduh $

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
$choice=$_POST['choice'];
$stu_arr=$_POST['stu_arr'];

// 叫用 SFS3 的版頭
head("分組課程設定");

// 您的程式碼由此開始
print_menu($menu_p);
$curr_year = curr_year();
$curr_seme = curr_seme();
if($act=="save1" && $group_id){//將勾選的學生儲存，和沒勾選的取消
	//先計算出有多少人已經屬於該分組
	$q_count=now_mem($group_id);

	foreach($stu_arr as $student_sn){
		if(in_array($student_sn,$choice)){//該生有勾選這個分組課程
			if(!is_mem($student_sn,$group_id)){
				$sql_r.="insert into elective_stu(group_id,student_sn) values('$group_id','$student_sn')###";
				$q_count++;

			}
		}else{//該生沒有勾選這個分組課程
			if(is_mem($student_sn,$group_id)){
				$sql_r.="delete from elective_stu where group_id='$group_id' and student_sn='$student_sn'###";
				$q_count--;

			}
		}
	}

	//計算是否超收，超收多少
	$member=member($group_id);
	if(($member>=$q_count) || ($member==0)) {
		if($sql_r){
			$sql_r=substr($sql_r,0,-3);
			$sql_r_arr=explode("###",$sql_r);
			foreach($sql_r_arr as $sa) $CONN->Execute($sa) or trigger_error($sa,256);
		}else $msg="您沒有做任何的選取，請重新選取<br>";
	}
	else $msg="您所選的人數超過上限！故上次選取無效，請重新選取<br>";
}
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
		$group_selecter="<form action='{$_SERVER[PHP_SELF]}' method='POST'><input type='hidden' name='c_year' value='$c_year'><input type='hidden' name='ss_id' value='$ss_id'><select name='group_id' onchange='this.form.submit()'><option value='0'>選擇分組</option>\n$option_group_id</select>\n";
		if($group_id){
			$on=over_mem($group_id);
			if($on==999999) $group_selecter.="不限人數";
			elseif($on>=1) $group_selecter.="目前剩餘人數". $on."人";
			else $group_selecter.="已經額滿";
		}
		$group_selecter.="<a href='elective_tea.php?c_year=$c_year&ss_id=$ss_id'><img src='images/back.png' border='0'></a><a href='elective_paper.php?c_year=$c_year&ss_id=$ss_id&group_id=$group_id'><img src='images/forward.png' border='0'></a></form>";
	}

	if($group_id){//班級選單，秀出班級名條提供勾選

		$class_seme=sprintf("%03d%d",$curr_year,$curr_seme);
		$sel_year_arr = array($c_year);
		$class_base_array=class_base($class_seme,$sel_year_arr);
		$class_base_select.="<form action='{$_SERVER['PHP_SELF']}' method='POST' name='form2'>\n<select  name='class_base' onchange='this.form.submit()'>\n";
		$j=0;
		foreach($class_base_array as $kkkk => $vvvv){
			if(!$class_base) $class_base=$c_year."01";
			$selected3[$j]=($class_base==$kkkk)?" selected":" ";
			$class_base_select.="<option value='$kkkk'$selected3[$j] >$vvvv</option> \n";
			$j++;
		}
		$class_base_select.="</select>
		<input type='hidden' name='class_seme' value='$class_seme'>
		<input type='hidden' name='c_year' value='$c_year'>
		<input type='hidden' name='ss_id' value='$ss_id'>
		<input type='hidden' name='group_id' value='$group_id'>
		</form>\n";

		$class_id=sprintf("%03d_%d_%02d_%02d",substr($class_seme,0,-1),substr($class_seme,-1),substr($class_base,0,-2),substr($class_base,-2));
		$student_sn_array=class_id_to_student_sn($class_id);
		$total=count($student_sn_array);
		$stud_list.="<table cellspacing=1 cellpadding=6 border=0  bgcolor='#00AB00' >
		<form action='{$_SERVER['PHP_SELF']}' method='POST' name='D1'>
		<tr bgcolor='#C4FAAE'><td><a href='{$_SERVER['PHP_SELF']}?c_year=$c_year&ss_id=$ss_id&group_id=$group_id&choice_all=1&class_seme=$class_seme&class_base=$class_base'><font class='button'>全選</font></a></td><td>學號</td><td>姓名</td><td>座號</td></tr>
		";

		$i=0;
		foreach($student_sn_array as $sn_val){
			//檢查該生是否已選了該科目的其他分組
			$sql_u="select et.group_id from elective_stu as es , elective_tea as et where es.group_id=et.group_id and es.student_sn='$sn_val' and et.ss_id='$ss_id' ";
			$rs_u=$CONN->Execute($sql_u) or trigger_error($sql,256);
			$u=0;
			$group_u=array();
			while(!$rs_u->EOF){
				$group_u[$u]=$rs_u->fields['group_id'];
				$u++;
				$rs_u->MoveNext();
			}


			//檢查該生原先是否已經選修了
			//$sql_s="select count(*) from elective_stu where group_id='$group_id' and student_sn='$sn_val' ";
			//$rs_s=$CONN->Execute($sql_s) or trigger_error($sql_s,256);
			//$c[$i]=$rs_s->fields[0];
			//$checked[$i]=($c[$i]==1)?" checked":"";
			$checked[$i]=(in_array($group_id,$group_u))?" checked":"";
			//全選時
			$checked[$i]=($_GET['choice_all']==1)?" checked":"$checked[$i]";
			//找出姓名和學號，座號
			$st_data=student_sn_to_name_num($sn_val);
			//去除掉目前的group_id
			foreach($group_u as $uk => $uv){
				if($uv==$group_id) array_splice($group_u,$uk,1);
			}
			if(count($group_u)>=1){
				$stud_list.="<tr bgcolor='#DBDBDB' onmouseover=\"style.background='#B9B9B9'\" onmouseout=\"style.background='#DBDBDB'\"><td>
				<input type='hidden' name='stu_arr[$i]' value='$sn_val'>
				</td><td>".$st_data[0]."</td><td>".$st_data[1]."</td><td>".$st_data[2]."</td></tr>\n";
			}else{
				$stud_list.="<tr bgcolor='#FFFFFF' onmouseover=\"style.background='#FFF6BA'\" onmouseout=\"style.background='#FFFFFF'\"><td>
				<input type='checkbox' name='choice[$i]' value='$sn_val'{$checked[$i]}>
				<input type='hidden' name='stu_arr[$i]' value='$sn_val'>
				</td><td>".$st_data[0]."</td><td>".$st_data[1]."</td><td>".$st_data[2]."</td></tr>\n";
			}
			$i++;
		}
		$stud_list.="<tr bgcolor='#C4FAAE'><td colspan='4'>
		<input type='hidden' name='c_year' value='$c_year'>
		<input type='hidden' name='ss_id' value='$ss_id'>
		<input type='hidden' name='group_id' value='$group_id'>
		<input type='hidden' name='act' value='save1'>
		<input type='hidden' name='class_seme' value='$class_seme'>
		<input type='hidden' name='class_base' value='$class_base'>
		<input type='button' value='儲存' onclick=this.form.submit()>
		</td></tr></form></table>";

		$student_area="<table cellspacing=1 cellpadding=6 border=0 bgcolor='#211BC7' width='99%' align='center'><tr bgcolor='#FFFFFF'><td><table><tr><td valign='top' width='1%'>$class_base_select</td><td>$msg $stud_list</td></tr></table></td></tr></table>";
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
