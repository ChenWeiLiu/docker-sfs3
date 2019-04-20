<?php

// $Id: section_setup.php 5310 2009-01-10 07:57:56Z hami $

/* 取得基本設定檔 */
include "config.php";

sfs_check();

//若有選擇學年學期，進行分割取得學年及學期
if(!empty($_REQUEST[year_seme])){
	$ys=explode("-",$year_seme);
	$sel_year=$ys[0];
	$sel_seme=$ys[1];
}

$sel_year=(empty($_REQUEST[sel_year]))?curr_year():$_REQUEST[sel_year]; //目前學年
$sel_seme=(empty($_REQUEST[sel_seme]))?curr_seme():$_REQUEST[sel_seme]; //目前學期
$class_id=$_REQUEST[class_id];
$act=$_REQUEST[act];
$ss_id=$_REQUEST[ss_id];
$input_course_name=$_POST[input_course_name];
$input_sections=$_POST[input_sections];
$input_test_times=$_POST[input_test_times];
$input_score_mode=$_POST[input_score_mode];
$input_display_mode=$_POST[input_display_mode];
$rs=$_POST[rs];
$rn=$_POST[rn];
$cls=$_POST[cls];

//錯誤設定
if($error==1){
	$act="error";
	$error_title="無年級設定";
	$error_main="找不到 $sel_year 學年度，第 $sel_seme 學期的年級設定，故您無法使用此功能。<ol><li>請先到『<a href='".$SFS_PATH_HTML."school_affairs/every_year_setup/class_year_setup.php'>班級設定</a>』設定年級以及班級資料。<li>以後記得每一學期的學期出都要設定一次喔！</ol>";
}

//執行動作判斷
if($act=="error"){
	$main=&error_tbl($error_title,$error_main);
}elseif($act=="儲存設定"){
	update_section_set($ss_id,$sel_year,$sel_seme,$class_id);
	header("location: {$_SERVER['PHP_SELF']}?sel_year=$sel_year&sel_seme=$sel_seme&act=set_ss&class_id=$class_id");
}elseif($act=="copy_name"){
	update_course_name($ss_id,$sel_year,$sel_seme,$class_id,$_GET[course_name]);
	header("location: {$_SERVER['PHP_SELF']}?sel_year=$sel_year&sel_seme=$sel_seme&act=set_ss&class_id=$class_id");
}elseif($act=="add"){
	add_sn($ss_id,$sel_year,$sel_seme,$class_id);
	header("location: {$_SERVER['PHP_SELF']}?sel_year=$sel_year&sel_seme=$sel_seme&act=set_ss&class_id=$class_id");
}elseif($act=="del"){
	del_sn($class_id,$ss_id);
	header("location: {$_SERVER['PHP_SELF']}?sel_year=$sel_year&sel_seme=$sel_seme&act=set_ss&class_id=$class_id");
}elseif($act=="view" or ($act=="開始設定" && $class_id) or $act=="set_ss" or $act=="modify_exam"){
	if($act=="開始設定")$act="set_ss";
	$main=&list_sn($sel_year,$sel_seme,$class_id,"",$ss_id,$act);
}elseif($act=="觀看節數規劃表" && $class_id){
	$main=&list_sn($sel_year,$sel_seme,$class_id,"view",$ss_id,$act);
}elseif($act=="fast_copy"){
	fast_copy($sel_year,$sel_seme,$class_id);
	header("location: {$_SERVER['PHP_SELF']}?sel_year=$sel_year&sel_seme=$sel_seme&act=set_ss&class_id=$class_id");
}else{
	$main=&sn_form($sel_year,$sel_seme,$class_id);
}


//秀出網頁
head("節數設定");
echo $main;
foot();

//函式區

//基本設定表單
function &sn_form($sel_year,$sel_seme,$class_id=""){
	global $school_menu_p;
	//相關功能表
	$tool_bar=&make_menu($school_menu_p);

	//說明
	$help_text="
	請選擇欲設定之『學年度』、『學期』、『班級』。||
	<span class='like_button'>開始設定</span> 就是開始設定該班級的節數規劃表。||
	<span class='like_button'>觀看節數規劃表</span> 會列出該班級該學期的節數規劃表。
	";
	$help=&help($help_text);

	//取得年度與學期的下拉選單
	$date_select=&class_ok_setup_year($sel_year,$sel_seme,"year_seme","jumpMenu");
	
	//年級與班級選單
	$class_select=&get_class_select($sel_year,$sel_seme,"","class_id","",$class_id,"","請選擇班級");
	
	$main="
	<script language='JavaScript'>
	function jumpMenu(){
		if(document.myform.year_seme.options[document.myform.year_seme.selectedIndex].value!=''){
			location=\"{$_SERVER['PHP_SELF']}?act=$act&year_seme=\" + document.myform.year_seme.options[document.myform.year_seme.selectedIndex].value;
		}
	}
	
	</script>
	$tool_bar
	<table bgcolor='#9EBCDD' cellspacing=1 cellpadding=4>
	<tr bgcolor='#FFFFFF'><td>
		<table>
		<form action='{$_SERVER['PHP_SELF']}' method='post' name='myform'>
  		<tr><td>請選擇欲設定的學年度：</td><td>$date_select</td></tr>
		<tr><td>請選擇欲設定的班級：</td><td>$class_select</td></tr>
		<tr><td colspan='2'><input type='submit' name='act' value='開始設定' class='b1'>
		<input type='submit' name='act' value='觀看節數規劃表' class='b1'>
		</td></tr>
		</form>
		</table>
	</td></tr>
	</table>
	<br>
	$help
	";
	return $main;
}


//秀出所有課程，$mode=view（只有一個表，無新增或修改工具）、clear_view（只有那個表，連連結工具都不要）
function &list_sn($sel_year,$sel_seme,$class_id="",$mode="",$id=0,$act=""){
	global $CONN,$school_kind_name,$school_menu_p;

	//年級與班級選單
	$class_select=&get_class_select($sel_year,$sel_seme,$Cyear,"class_id","jumpMenu1",$class_id,"","請選擇班級");
	$class_name_arr = class_base();
	$c=explode("_",$class_id);
	$class_num=intval($c[2]).$c[3];
	$class_name=$class_name_arr[$class_num];
	
	//找出該表中所有的年度與學期，要拿來作選單
	$other_link="act=$act&class_id=$class_id";
	$tmp=&get_ss_year($sel_year,$sel_seme,$other_link);
	$other_ss_text=($mode=="clear_view")?"":$tmp;

	//取得年級
	$Cyear=intval(substr($class_id,6,2));

	//取出該年級或班級、該學年、該學期的不隱藏學科，$ssid[$i][ss_id]，$ssid[$i][scope_id]，$ssid[$i][subject_id]
	$ssid=&get_all_ss($sel_year,$sel_seme,"",$class_id);
	if (count($ssid)==0) {
		$ssid=&get_all_ss($sel_year,$sel_seme,$Cyear,"");
	}
	$cy=($Cyear=="")?$cy:$Cyear;
	if (count($ssid)>0) {
		$query = "select performance_test_times,test_ratio,score_mode from score_setup where year='$sel_year' and semester='$sel_seme' and class_year='$cy' and enable='1'";
		$res = $CONN->Execute($query);
		$performance_test_times=$res->fields['performance_test_times'];
		$test_ratio=$res->fields['test_ratio'];
		$score_mode=$res->fields['score_mode'];
		$query = "select * from course_table where year='$sel_year' and semester='$sel_seme' and class_id='$class_id'";
		$res = $CONN->Execute($query);
		if ($res)
			while (!$res->EOF) {
				$ss_id=$res->fields['ss_id'];
				$course_name[$ss_id]=$res->fields['course_name'];
				$sections[$ss_id]=$res->fields['sections'];
				$test_times[$ss_id]=$res->fields['test_times'];
				$ratio[$ss_id]=$res->fields['test_ratio'];
				$smode[$ss_id]=$res->fields['score_mode'];
				$dmode[$ss_id]=$res->fields['display_mode'];
				$have_data[$ss_id]=1;
				$res->MoveNext();
			}
		$query = "select ss_id,count(ss_id) from score_course where year='$sel_year' and semester='$sel_seme' and class_id='$class_id' group by ss_id";
		$res = $CONN->Execute($query);
		while (!$res->EOF) {
			$ss_id=$res->fields['ss_id'];
			if ($sections[$ss_id]==0) $sections[$ss_id]=$res->fields[1];
			$res->MoveNext();
		}
	}
	
	//所有科目的數量
	$ss_id_n=sizeof($ssid);
	
	//編輯按鈕
	$edit_button=($mode=="")?"":"<tr><td><input type='button' value='進行編輯' onclick=\"window.location.href='{$_SERVER['PHP_SELF']}?act=set_ss&sel_year=$sel_year&sel_seme=$sel_seme&class_id=$class_id'\" class='b1'></td></tr>";

	//快速複製班級選單
	$query = "select class_id,c_name from school_class where year='$sel_year' and semester='$sel_seme' and c_year='$Cyear'";
	$res = $CONN->Execute($query);
	$copy_menu="
		<table bgcolor='#9EBCDD' cellspacing=1 cellpadding=1 class='small'>
		<tbody bgcolor='#E1ECFF'><tr><td align='center'>將設定複製到</td></tr>";
	while (!$res->EOF) {
		$cid=$res->fields['class_id'];
		$cname=$res->fields['c_name'];
		if ($class_id != $cid) {
			$checked=($cls[$cid])?"checked":"";
			$copy_menu.="<tr bgcolor='white'><td><input type='checkbox' name='cls[".$cid."]' $checked>".$school_kind_name[$Cyear].$cname."班</td></tr>";
		}
		$res->MoveNext();
	}
	$copy_menu.="</tbody></table>";

	//快速複製按鈕
	$fast_copy_button=($mode=="")?"
	<tr><td align='center'><form action='{$_SERVER['PHP_SELF']}' method='post'>
	<input type='button' value='快速複製' onclick=\"this.form.submit();\" class='b1'><br>$copy_menu
	<input type='hidden' name='sel_year' value='$sel_year'>
	<input type='hidden' name='sel_seme' value='$sel_seme'>
	<input type='hidden' name='class_id' value='$class_id'>
	<input type='hidden' name='act' value='fast_copy'>
	</form></td></tr>
	":"";

	//按鈕集
	$button="<table cellspacing=1 cellpadding=0 border='0' align='center'>
	$edit_button
	$fast_copy_button
	</table>";

	//所有科目的數量
	$ss_id_n=sizeof($ssid);
	for($i=0;$i<$ss_id_n;$i++){
		$ss_id=$ssid[$i][ss_id];
		$scope_id=$ssid[$i][scope_id];
		$subject_id=$ssid[$i][subject_id];
		$need_exam=$ssid[$i][need_exam];
		$subject_name=(empty($subject_id))?get_subject_name($scope_id):get_subject_name($subject_id);
		$subject_print=$ssid[$i]['print'];
		if ($test_times[$ss_id]=="") $test_times[$ss_id]=0;
		
		$td="<td align='left' nowrap><font color='#000088'>$subject_name</font></td>";
		
		//功能表（若是觀看狀態，則不秀出表單）
		if ($mode=="view" or $mode=="clear_view") {
			$modify_tool="";
		} elseif ($have_data[$ss_id]==1) {
			$modify_tool="<td class='small' rowspan='2' nowrap>
			<a href='{$_SERVER['PHP_SELF']}?act=modify_exam&sel_year=$sel_year&sel_seme=$sel_seme&ss_id=$ss_id&class_id=$class_id'>
			<img src='images/edit.png' border=0 hspace=3>修改</a>
			<a href=\"javascript:func($ss_id);\">
			<img src='images/del.png' border=0 hspace=3>刪除</a>";
			if ($course_name[$ss_id]=="") $modify_tool.="<a href='{$_SERVER['PHP_SELF']}?act=copy_name&sel_year=$sel_year&sel_seme=$sel_seme&ss_id=$ss_id&class_id=$class_id&course_name=$subject_name'>
			<img src='images/paste.png' border=0 hspace=3>複製課程名稱</a>";
		} else {
			$modify_tool="<td class='small' nowrap>
			<a href='{$_SERVER['PHP_SELF']}?act=add&sel_year=$sel_year&sel_seme=$sel_seme&ss_id=$ss_id&class_id=$class_id'>
			<img src='images/edit.png' border=0 hspace=3>加入此課程</a></td>";
		}

		//需要計分的情況
		if($need_exam=='1'){
			$checked="checked";
			$exam_pic="<img src='images/ok.png' width=16 height=14 border=0>";
		}else{
			$checked="";
			$exam_pic="";
			$rate="";
		}
		
		//需要完整輸入成績的情況
		if($subject_print=='1'){
			$print_checked="checked";
			$print_pic="<img src='images/ok.png' width=16 height=14 border=0>";
		}else{
			$print_checked="";
			$print_pic="";
		}

		//取出考試比例
		$ratio_menu="";
		$all_ratio=0;
		if ($subject_print=='1') {
			if ($ratio[$ss_id]!="") {
				$t=explode(",",$ratio[$ss_id]);
				$m=($smode[$ss_id]=="all")?1:$test_times[$ss_id];
				for ($j=0;$j<$m;$j++) if ($t[$j]=="") $t[$j]="0-0";
				$ratio[$ss_id]="";
				while (list($k,$v)=each($t)){
					if ($k<$test_times[$ss_id]) {
						$ratio[$ss_id].=($smode[$ss_id]=="severally")?"(".($k+1).")".$v."<br>":$v;
						$vv=explode("-",$v);
						$ratio_menu.="<input type='text' name='rs[".($k+1)."]' value='".$vv[0]."' size='3'>-<input type='text' name='rn[".($k+1)."]' value='".$vv[1]."' size='3'><br>\n";
						$all_ratio+=intval($vv[0])+intval($vv[1]);
					}
				}
			}
		} else {
			$test_times[$ss_id]="";
			$ratio[$ss_id]="";
		}
		$rocolor=($all_ratio!=100 && $ratio_menu)?"bgcolor='#ff0000'":"";

		//比例模式
		$score_msg="";
		$sd_1="";
		$sd_2="";
		$mode_menu="";
		if ($subject_print=='1') {
			if ($smode[$ss_id]=="all") {
				$score_msg="每階段相同";
				$sd_1="selected";
			} else {
				$score_msg="每階段不同";
				$sd_2="selected";
			}
			$mode_menu="<select name='input_score_mode'><option value='all' $sd_1>每階段相同</option><option value='severally' $sd_2>每階段不同</option></select>";
		}

		//顯示模式
		$dd_1="";
		$dd_2="";
		if ($dmode[$ss_id]==0) {
			if ($course_name[$ss_id]!="") {
				$display_msg=$class_name.$course_name[$ss_id];
			} else {
				$display_msg="尚未設定課程名";
			}
			$dd_1="selected";
		} else {
			$display_msg=$course_name[$ss_id];
			$dd_2="selected";
		}
		$display_menu="<select name='input_display_mode'><option value='0' $dd_1>班級+課程名</option><option value='1' $dd_2>課程名</option></select>";

		//科目主要內容
		$r=($have_data[$ss_id]==1)?"rowspan='2'":"";
		$sss=($have_data[$ss_id]==1)?"
			<td align='left' class='small' $r nowrap>$display_msg
			<td align='center' $r>$exam_pic</td>
			<td align='center' $r>$print_pic</td>
			<td nowrap align='center' $r>
			<font color='#A23B32' face='arial'>".$sections[$ss_id]."</font></td>
			<td nowrap align='center' $r>
			<font color='#A7C0EF' face='arial'>".$test_times[$ss_id]."</font></td>
			<td align='center' class='small' $r nowrap>$score_msg
			<td align='center' class='small' $r nowrap $rocolor>".$ratio[$ss_id]."
			</td>
		":"
			<td align='center' colspan='7'><font color='#A23B32' face='arial'>本課程可能應設定但尚未設定</font></td>
		";

		//顯示課程名
		if ($course_name[$ss_id]=="") {
			$print_course_name="尚未設定";
		} else {
			$print_course_name=$course_name[$ss_id];
		}
		if ($have_data[$ss_id]==1) {
			$td2=($act=="modify_exam" && $ss_id==$id)?"<td align='left'><input type='text' name='input_course_name' value='$course_name[$ss_id]' size='14'></td></form>":"<td align='left'>$print_course_name</td>";
		} else {
			$td2="";
		}

		//完整內容
		$ss.=($act=="modify_exam" && $ss_id==$id)?"
		<tr bgcolor='white'>
		<form action='{$_SERVER['PHP_SELF']}' method='post'>
			$td
			<td align='center' $r>$display_menu</td>
			<td align='center' $r>$exam_pic</td>
			<td align='center' $r>$print_pic</td>
			<td align='center' $r><input type='text' name='input_sections' value='$sections[$ss_id]' size='1'></td>
			<td align='center' $r><input type='text' name='input_test_times' value='$test_times[$ss_id]' size='1'></td>
			<td align='center' $r>$mode_menu</td>
			<td align='center' $r $rocolor>$ratio_menu</td>
			<td class='small' $r>
			<input type='hidden' name='ss_id' value='$ss_id'>
			<input type='hidden' name='sel_year' value='$sel_year'>
			<input type='hidden' name='sel_seme' value='$sel_seme'>
			<input type='hidden' name='class_id' value='$class_id'>
			<input type='submit' name='act' value='儲存設定' class='b1'>
			</td>
		</tr>
		":"
		<tr bgcolor='white'>
			$td
			$sss
			$modify_tool
		</tr>
		";
		if (!empty($td2)) $ss.="<tr bgcolor='white'>$td2</tr>";
	}

	$semester_name=($sel_seme=='2')?"下":"上";
	
	//功能表（若是觀看狀態，則不秀出表單）
	$modify_tool_title=($mode=="view" or $mode=="clear_view")?"":"<td align='center' rowspan='2'>功能</td>";

	$ss_table="
	<table bgcolor='#9EBCDD' cellspacing=1 cellpadding=4 class='small'>
	<form action='{$_SERVER['PHP_SELF']}' method='post' name='myform'>
	<tr><td colspan='9' align='center' bgcolor='#E1ECFF'>
	<font color='#607387'>
	<font color='#000000'>$sel_year</font> 學年
	<font color='#000000'>$semester_name</font>學期
	$class_select 節數設定表
	</font>
	</td></tr>
	</form>
	<tbody>
	<tr bgcolor='#E1ECFF'>
		<td align='center' nowrap>科目</td>
		<td align='center' rowspan='2' nowrap>選單顯示</td>
		<td align='center' rowspan='2' nowrap>計分</td>
		<td align='center' rowspan='2' nowrap>完整</td>
		<td align='center' rowspan='2' nowrap>每週<br>節數</td>
		<td align='center' rowspan='2' nowrap>定考<br>次數</td>
		<td align='center' rowspan='2' nowrap>配分模式</td>
		<td align='center' rowspan='2' nowrap>配分比例<br>(定考-平時)</td>
		$modify_tool_title
	</tr>
	<tr bgcolor='#E1ECFF'>
		<td align='center' nowrap>課程名稱</td>
	</tr>
	$ss
	</tbody>
	</table>";

	//相關功能表
	$tool_bar = ($mode=="clear_view")?"":make_menu($school_menu_p);

	if($Cyear=="" and $class_id="")$button="";

	//主要秀出畫面
	$main="
	<script language='JavaScript'>
	function func(ss_id){
		var sure = window.confirm('確定要刪除？');
		if (!sure) {
			return;
		}
		location.href=\"{$_SERVER['PHP_SELF']}?act=del&sel_year=$sel_year&sel_seme=$sel_seme&Cyear=$Cyear&class_id=$class_id&ss_id=\" + ss_id;
	}

	function jumpMenu(){
		var dd, classstr ;
		location=\"{$_SERVER['PHP_SELF']}?act=set_ss&sel_year=$sel_year&sel_seme=$sel_seme&Cyear=\" + document.myform.Cyear.options[document.myform.Cyear.selectedIndex].value;
	}
	
	function jumpMenu1(){
		var dd, classstr ;
		if ((document.myform.class_id.options[document.myform.class_id.selectedIndex].value!='')) {
			location=\"{$_SERVER['PHP_SELF']}?act=set_ss&sel_year=$sel_year&sel_seme=$sel_seme&Cyear=$Cyear&class_id=\" + document.myform.class_id.options[document.myform.class_id.selectedIndex].value;
		}
	}
	</script>
	$tool_bar
	<table cellspacing=0 cellpadding=0 border='0'>
	<tr>
	<td valign='top'>$ss_table</td>
	<td width='5'></td>
	<td valign='top'>
	$add_form
	$button
	</td>
	<td width='5'></td>
	<td valign='top'>$other_ss_text</td>
	</tr>
	</table>
	";
	return $main;
}


//快速複製介面
function fast_copy($sel_year,$sel_seme,$class_id){
	global $CONN,$cls;
	//相關功能表
	$query = "select * from course_table where year='$sel_year' and semester='$sel_seme' and class_id='$class_id'";
	$res = $CONN->Execute($query) or trigger_error("SQL語法執行失敗，SQL語法如下： $query", E_USER_ERROR);
	while (!$res->EOF) {
		$ss_id[]=$res->fields['ss_id'];
		$course_name[]=$res->fields['course_name'];
		$test_ratio[]=$res->fields['test_ratio'];
		$sections[]=$res->fields['sections'];
		$test_times[]=$res->fields['test_times'];
		$score_mode[]=$res->fields['score_mode'];
		$display_mode[]=$res->fields['display_mode'];
		$res->MoveNext();
	}
	$query = "select class_id,ss_id from course_table where year='$sel_year' and semester='$sel_seme'";
	$res = $CONN->Execute($query) or trigger_error("SQL語法執行失敗，SQL語法如下： $query", E_USER_ERROR);
	while (!$res->EOF) {
		$chk_data[$res->fields['class_id']][$res->fields['ss_id']]=1;
		$res->MoveNext();
	}
	$ci=explode("_",$class_id);
	$Cyear=intval($ci[2]);
	$query = "select class_id from school_class where year='$sel_year' and semester='$sel_seme' and c_year='$Cyear'";
	$res = $CONN->Execute($query) or trigger_error("SQL語法執行失敗，SQL語法如下： $query", E_USER_ERROR);
	while (!$res->EOF) {
		$cs=$res->fields['class_id'];
		if ($cls[$cs]) $cid[]=$cs;
		$res->MoveNext();
	}
	while (list($k,$v)=each($ss_id)) {
		reset($cid);
		while (list($i,$c)=each($cid)) {
			if ($chk_data[$c][$v]==1) {
				$query = "update course_table set course_name='$course_name[$k]',test_ratio='$test_ratio[$k]',sections='$sections[$k]',test_times='$test_times[$k]',score_mode='$score_mode[$k]',display_mode='$display_mode[$k]' where class_id='$c' and ss_id='$v'";
			} else {
				$query = "insert into course_table (class_id,ss_id,course_name,test_ratio,sections,test_times,score_mode,display_mode,year,semester) values ('$c','$v','$course_name[$k]','$test_ratio[$k]','$sections[$k]','$test_times[$k]','$score_mode[$k]','$display_mode[$k]','$sel_year','$sel_seme')";
			}
			$CONN->Execute($query) or trigger_error("SQL語法執行失敗，SQL語法如下： $query", E_USER_ERROR);
		}
	}
	return;
}

//加入一筆節數設定
function add_sn($ss_id,$sel_year,$sel_seme,$class_id){
	global $CONN;
	$query = "select * from score_ss where ss_id='$ss_id'";
	$res = $CONN->Execute($query);
	$print=$res->fields['print'];
	if ($print==1) {
		$c=explode("_",$class_id);
		$class_year=intval($c[2]);
		$query = "select * from score_setup where year='$sel_year' and semester='$sel_seme' and class_year='$class_year' and enable='1'";
		$res = $CONN->Execute($query);
		$score_mode=$res->fields['score_mode'];
		$test_ratio=$res->fields['test_ratio'];
		$test_times=$res->fields['performance_test_times'];
	}
	$sql_insert = "insert into course_table (class_id,ss_id,course_name,test_ratio,sections,test_times,year,semester,score_mode) values ('$class_id','$ss_id','$course_name','$test_ratio','0','$test_times','$sel_year','$sel_seme','$score_mode')";
	if($CONN->Execute($sql_insert))		return true;
	return  false;
}

//更新課程名稱
function update_course_name($ss_id,$sel_year,$sel_seme,$class_id,$course_name){
	global $CONN;
	$sql_update = "update course_table set course_name='$course_name' where class_id='$class_id' and ss_id = '$ss_id'";
	if($CONN->Execute($sql_update))		return true;
	return  false;
}

//更新一筆節數設定
function update_section_set($ss_id="",$sel_year="",$sel_seme="",$class_id=""){
	global $CONN,$input_course_name,$input_sections,$input_test_times,$rs,$rn,$input_score_mode,$input_display_mode;

	if ($input_score_mode=="severally") {
		while (list($k,$v)=each($rs)) {
			$test_ratio.=$rs[$k]."-".$rn[$k].",";
		}
	} else {
		$test_ratio=$rs[1]."-".$rn[1].",";
	}
	$test_ratio=substr($test_ratio,0,-1);
	$query = "select * from course_table where year='$sel_year' and semester='$sel_seme' and ss_id='$ss_id' and class_id='$class_id'";
	$res = $CONN->Execute($query) or trigger_error("SQL語法執行失敗，SQL語法如下： $query", E_USER_ERROR);
	$course_id=$res->fields['course_id'];
	if (!empty($course_id))
		$query = "update course_table set course_name='$input_course_name',test_ratio='$test_ratio',sections='$input_sections',test_times='$input_test_times',score_mode='$input_score_mode',display_mode='$input_display_mode' where course_id='$course_id'";
	else
		$query = "insert into course_table (class_id,ss_id,course_name,test_ratio,sections,test_times,year,semester,score_mode,display_mode) values ('$class_id','$ss_id','$input_course_name','$test_ratio','$input_sections','$input_test_times','$sel_year','$sel_seme','$input_score_mode','$input_display_mode')";
	$CONN->Execute($query) or trigger_error("SQL語法執行失敗，SQL語法如下： $query", E_USER_ERROR);

	return false;
}

//刪除課程
function del_sn($class_id,$ss_id){
	global $CONN,$sel_year,$sel_seme;
	$sql_update = "delete from course_table where year='$sel_year' and semester='$sel_seme' and class_id='$class_id' and ss_id = '$ss_id'";
	if($CONN->Execute($sql_update))		return true;
	return  false;
}
?>
