<?php

// $Id: ss_setup.php 9118 2017-08-10 00:49:16Z infodaes $

/* 取得基本設定檔 */
include "config.php";

sfs_check();
$m_arr = &get_module_setup("every_year_setup");
extract($m_arr);

//若有選擇學年學期，進行分割取得學年及學期
if(!empty($_REQUEST[year_seme])){
	$ys=explode("-",$_REQUEST[year_seme]);
	$sel_year=$ys[0];
	$sel_seme=$ys[1];
}
else {
	// 避免 cookie 錯誤
	if ($_GET[sel_year]) $_POST[sel_year] = $_GET[sel_year];
        if ($_GET[sel_seme]) $_POST[sel_seme] = $_GET[sel_seme];
        $sel_year=(empty($_POST[sel_year]))?curr_year():$_POST[sel_year]; //目前學年
        $sel_seme=(empty($_POST[sel_seme]))?curr_seme():$_POST[sel_seme]; //目前學期
}

$Cyear=$_REQUEST[Cyear];
$Cyear=$Cyear?$Cyear:($IS_JHORES+1);
$class_id=$_REQUEST[class_id];
$act=$_REQUEST[act];
$ss_id=$_REQUEST[ss_id];
$scope_id=$_REQUEST[scope_id];
$subject_id=$_REQUEST[subject_id];
$copy_set = $_REQUEST[copy_set];
//錯誤設定
if($error==1){
	$act="error";
	$error_title="無年級設定";
	$error_main="找不到 $sel_year 學年度，第 $sel_seme 學期的年級設定，故您無法使用此功能。<ol><li>請先到『<a href='".$SFS_PATH_HTML."school_affairs/every_year_setup/class_year_setup.php'>班級設定</a>』設定年級以及班級資料。<li>以後記得每一學期的學期出都要設定一次喔！</ol>";
}

//執行動作判斷
if($act=="error"){
	$main=&error_tbl($error_title,$error_main);
}elseif($act=="新增" or $act=="加入分科"){
	add_ss($_REQUEST[subject_id],$_REQUEST[subject_name],$_REQUEST[subject_kind],$sel_year,$sel_seme,$_REQUEST[scope_id],$_REQUEST[need_exam],$_REQUEST[rate],$Cyear,$class_id,$_REQUEST['print'],$_REQUEST['sort'],$_REQUEST[sub_sort]);
	header("location: {$_SERVER['PHP_SELF']}?sel_year=$sel_year&sel_seme=$sel_seme&act=set_ss&Cyear=$Cyear&class_id=$class_id");
}elseif($act=="儲存"){
	update_ss($ss_id,$scope_id,$subject_id,$sel_year,$sel_seme);
	header("location: {$_SERVER['PHP_SELF']}?sel_year=$sel_year&sel_seme=$sel_seme&act=set_ss&Cyear=$Cyear&class_id=$class_id");
}elseif($act=="del"){
	$have_course=&have_course($sel_year,$sel_seme,$ss_id,$Cyear,$class_id);
	if($have_course){
		$main=&show_ss_id_course($sel_year,$sel_seme,$ss_id,$Cyear,$class_id);
	}else{
		header("location: {$_SERVER['PHP_SELF']}?sel_year=$sel_year&sel_seme=$sel_seme&act=do_del&ss_id=$ss_id&Cyear=$Cyear&class_id=$class_id");
	}
}elseif($act=="do_del"){
	del_ss($ss_id);
	header("location: {$_SERVER['PHP_SELF']}?sel_year=$sel_year&sel_seme=$sel_seme&act=set_ss&Cyear=$Cyear&class_id=$class_id");
}elseif($act=="del_all_ss"){
	del_all_ss($sel_year,$sel_seme,$Cyear,$class_id);
	header("location: {$_SERVER['PHP_SELF']}?sel_year=$sel_year&sel_seme=$sel_seme&act=set_ss&Cyear=$Cyear&class_id=$class_id");
}elseif($act=="儲存設定"){
	update_exam_rate_set($ss_id,$scope_id,$subject_id,$_REQUEST[need_exam],$_REQUEST[rate],$_REQUEST['print'],$_REQUEST[link_ss],$_REQUEST['sort'],$_REQUEST[sub_sort],$_REQUEST[pre_scope_sort],$sel_year,$sel_seme,$_REQUEST[nor_item_kind],$_REQUEST[sections],$_REQUEST[k12ea_category],$_REQUEST[k12ea_area],$_REQUEST[k12ea_subject],$_REQUEST[k12ea_language]);
	header("location: {$_SERVER['PHP_SELF']}?sel_year=$sel_year&sel_seme=$sel_seme&act=set_ss&Cyear=$Cyear&class_id=$class_id");
}elseif($act=="add_ss" or $act=="view" or $act=="開始設定" or $act=="set_ss" or $act=="modify_exam" or $act=="add_subject"){
	if($act=="開始設定")$act="set_ss";
	$main=&list_ss($sel_year,$sel_seme,$Cyear,$class_id,"",$ss_id,$scope_id,$subject_id,$act);
}elseif($act=="觀看課程規劃表"){
	$main=&list_ss($sel_year,$sel_seme,$Cyear,$class_id,"view",$ss_id,$scope_id,$subject_id,$act);
}elseif($act=="列出所有年級課程規劃表" or $act=="viewall"){
	$main=&list_all_ss($sel_year,$sel_seme);
}elseif($act=="fast_copy"){
	$main=&fast_copy($sel_year,$sel_seme,$Cyear,$show_Cyear);
}elseif($act=="copy"){
	copy_ss($copy_set,$sel_year,$sel_seme,$Cyear);
	header("location: {$_SERVER['PHP_SELF']}?act=view&sel_year=$sel_year&sel_seme=$sel_seme&Cyear=$Cyear&class_id=$class_id");
}elseif($act=="auto9"){
	auto_copy($sel_year,$sel_seme,$Cyear,$class_id,"九年一貫");
	header("location: {$_SERVER['PHP_SELF']}?act=view&sel_year=$sel_year&sel_seme=$sel_seme&Cyear=$Cyear&class_id=$class_id");
}elseif($act=="setup_view"){
	$main=&setup_view();
}else{
	$main=&ss_form($sel_year,$sel_seme,$Cyear,$class_id);
}


//秀出網頁
head("課程設定");
echo $main;
foot();

/*
函式區
*/

//基本設定表單
function &ss_form($sel_year,$sel_seme,$Cyear="",$class_id=""){
	global $school_menu_p,$IS_CLASS_SUBJECT;
	//相關功能表
	$tool_bar=&make_menu($school_menu_p);

	//說明
	$help_text="
	請選擇欲設定之『學年度』、『學期』。||
	課程設定可以『年級』為單位來做課程設定。（若欲以「年級」為單位，<font color=red>班級勿選</font>）||
	亦可以「班級」為單位來做課程設定。（直接選班級，或先選年級再選班級亦可）||
	<span class='like_button'>開始設定</span> 就是開始設定該年級的課程規劃表。||
	<span class='like_button'>觀看課程規劃表</span> 會列出該年級該學期的課程規劃表。||
	<span class='like_button'>列出所有年級課程規劃表</span> 會列出該學期所有年級的課程規劃表（可不選『年級』）。||
	<span style='color:red;'>設定班級課程將導致同年級成績無法排序。</span>
	";
	$help=&help($help_text);

	//取得年度與學期的下拉選單
	//$date_select=&date_select($sel_year,$sel_seme);
	$date_select=&class_ok_setup_year($sel_year,$sel_seme,"year_seme","jumpMenu");
	
	//取得年級選單
	$class_year_list=&get_class_year_select($sel_year,$sel_seme,$Cyear,"jumpMenu1");
	
	//年級與班級選單
	if($IS_CLASS_SUBJECT) $class_select=&get_class_select($sel_year,$sel_seme,$Cyear,"class_id","jumpMenu1",$class_id,"","該年級所有班級(普通班)");
		else $class_select="<select name='class_id'><option selected>該年級所有班級(普通班)</option></select> <font size=1 color='red'>*模組變數未設定您可以設定個別的班級(特教班)課程</font>";
	
	
	$main="
	<script language='JavaScript'>
	function jumpMenu(){
		if(document.myform.year_seme.options[document.myform.year_seme.selectedIndex].value!=''){
			location=\"{$_SERVER['PHP_SELF']}?act=$act&year_seme=\" + document.myform.year_seme.options[document.myform.year_seme.selectedIndex].value;
		}
	}
	
	function jumpMenu1(){
		if(document.myform.Cyear.options[document.myform.Cyear.selectedIndex].value!=''){
			location=\"{$_SERVER['PHP_SELF']}?act=$act&year_seme=\" + document.myform.year_seme.options[document.myform.year_seme.selectedIndex].value + \"&Cyear=\" + document.myform.Cyear.options[document.myform.Cyear.selectedIndex].value;
		}
	}

	</script>
	$tool_bar
	<table bgcolor='#9EBCDD' cellspacing=1 cellpadding=4>
	<tr bgcolor='#FFFFFF'><td>
		<table>
		<form action='{$_SERVER['PHP_SELF']}' method='post' name='myform'>
  		<tr><td>請選擇欲設定的學年度：</td>
		<td>$date_select
		<inpjut type='hidden' name='Cyear' value=''>
		<inpjut type='hidden' name='class_id' value=''>
		</td>
		</tr>
		<tr><td colspan='2'><input type='submit' name='act' value='開始設定' class='b1'>
		<input type='submit' name='act' value='觀看課程規劃表' class='b1'>
		<input type='submit' name='act' value='列出所有年級課程規劃表' class='b1'>
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
function &list_ss($sel_year,$sel_seme,$Cyear="",$class_id="",$mode="",$id=0,$add_scope_id=0,$subject_id=0,$act=""){
	global $CONN,$school_kind_name,$school_menu_p,$class9,$IS_CLASS_SUBJECT,$show_nor_items;
	// 如果是班級課程時,取得年級值
	if (!empty($class_id)){
		$temp_arr = & class_id_2_old($class_id)	;
		$Cyear = $temp_arr[3];
	}
	
	
	//國教署課程對應ARRAY
	$k12ea_category_array = k12ea_category();
	$k12ea_area_array = k12ea_area();
	$k12ea_subject_array = k12ea_subject();
	$k12ea_language_array = k12ea_language();

	//取得年級選單
	$class_year_list=&get_class_year_select($sel_year,$sel_seme,$Cyear,"jumpMenu");

	//年級與班級選單
	if($IS_CLASS_SUBJECT) $class_select=&get_class_select($sel_year,$sel_seme,$Cyear,"class_id","jumpMenu1",$class_id,"","該年級所有班級(普通班)");
		else $class_select="<select name='class_id'><option selected>該年級所有班級(普通班)</option></select> <font size=1 color='red'>*模組變數未設定您可以設定個別的班級(特教班)課程</font>";
		//$class_select=&get_class_select($sel_year,$sel_seme,$Cyear,"class_id","jumpMenu1",$class_id,"","該年級所有班級(普通班)");

	$nor_item_array=sfs_text('平時成績選項');
	
	//找出該表中所有的年度與學期，要拿來作選單
	$other_link="act=$act&Cyear=$Cyear&class_id=$class_id";
	$tmp=&get_ss_year($sel_year,$sel_seme,$other_link);
	$other_ss_text=($mode=="clear_view")?"":$tmp;

	//取出該年級或班級、該學年、該學期的不隱藏學科，$ssid[$i][ss_id]，$ssid[$i][scope_id]，$ssid[$i][subject_id]
	$ssid=&get_all_ss($sel_year,$sel_seme,$Cyear,$class_id);
	
	$scope_have_subject=array();
	
	$no_data=true;
	
	//所有科目的數量
	$ss_id_n=sizeof($ssid);
		
	//先找出有分科的科目
	for($i=0;$i<$ss_id_n;$i++){
		$ss_id=$ssid[$i]['ss_id'];
		$scope_id=$ssid[$i]['scope_id'];
		$subject_id=$ssid[$i]['subject_id'];
		$subject_rate=$ssid[$i]['rate'];
		$subject_need_exam=$ssid[$i]['need_exam'];
		$k12ea_category=$ssid[$i]['k12ea_category'];
		$k12ea_area=$ssid[$i]['k12ea_area'];
		$k12ea_subject=$ssid[$i]['k12ea_subject'];
		$k12ea_frequency=$ssid[$i]['k12ea_frequency'];

		if(!empty($subject_id)){
			//把有分科的項目加到陣列中
			$scope_have_subject[]=$scope_id;
			if($subject_need_exam=='1')	$s_rate[$scope_id][$i]=$subject_rate;
			//計算該科所分的科目的數目
			$subject_num[$scope_id]++;
		}

		//若已經有資料，不秀出快速複製鍵
		$no_data=false;
	}

	
	//新增合科按鈕
	$add_button=($act=="add_ss" or $mode!="")?"":"<tr><td><input type='button' value='新增科目' onclick=\"window.location.href='{$_SERVER['PHP_SELF']}?act=add_ss&sel_year=$sel_year&sel_seme=$sel_seme&Cyear=$Cyear&class_id=$class_id'\" class='b1'></td></tr>";
	
	//編輯按鈕
	$edit_button=($mode=="")?"":"<tr><td><input type='button' value='進行編輯' onclick=\"window.location.href='{$_SERVER['PHP_SELF']}?act=set_ss&sel_year=$sel_year&sel_seme=$sel_seme&Cyear=$Cyear&class_id=$class_id'\" class='b1'></td></tr>";
	
	
	//刪除按鈕
	$del_button=($no_data or $mode!="")?"":"<tr><td><input type='button' value='清除重設' onclick=\"if(confirm('確定要清除重設？'))window.location.href='{$_SERVER['PHP_SELF']}?act=del_all_ss&sel_year=$sel_year&sel_seme=$sel_seme&Cyear=$Cyear&class_id=$class_id'\" class='b2'></td></tr>";
	
	//自動加入九年一貫課程
	$auto_button=($no_data)?"<tr><td><input type='button' value='自動加入' onclick=\"window.location.href='{$_SERVER['PHP_SELF']}?act=auto9&sel_year=$sel_year&sel_seme=$sel_seme&Cyear=$Cyear&class_id=$class_id'\" class='b1'></td></tr>":"";
	
	
	//快速複製按鈕
	$fast_copy_button=($no_data)?"<tr><td><input type='button' value='快速複製' onclick=\"window.location.href='{$_SERVER['PHP_SELF']}?act=fast_copy&sel_year=$sel_year&sel_seme=$sel_seme&Cyear=$Cyear&class_id=$class_id'\" class='b1'></td></tr>":"";
	
	
	
	//按鈕集
	$button="<table cellspacing=1 cellpadding=0 border='0' align='center'>
	$fast_copy_button
	$add_button
	$del_button
	$edit_button
	$auto_button
	</table>";


	$scope_id_array=array();
	
	//所有科目的數量
	$ss_id_n=sizeof($ssid);
	for($i=0;$i<$ss_id_n;$i++){
		$ss_id=$ssid[$i][ss_id];
		$scope_id=$ssid[$i][scope_id];
		$subject_id=$ssid[$i][subject_id];
		$need_exam=$ssid[$i][need_exam];
		$rate=$ssid[$i][rate];
		$subject_name=&get_subject_name($subject_id);
		$subject_print=$ssid[$i]['print'];
		$subject_sort=$ssid[$i]['sort'];
		$subject_sub_sort=$ssid[$i]['sub_sort'];		
		$subject_link_ss=$ssid[$i]['link_ss'];
		$nor_item_kind=$ssid[$i]['nor_item_kind'];
		$sections=$ssid[$i]['sections'];
		$k12ea_category=$ssid[$i]['k12ea_category'];
		$k12ea_area=$ssid[$i]['k12ea_area'];
		$k12ea_subject=$ssid[$i]['k12ea_subject'];
		$k12ea_language=$ssid[$i]['k12ea_language'];
		
		//若是無分科，那麼合併合科儲存格
		
		if(empty($subject_id)){
			$td2="";
			$colspan="colspan='2'";
		}else{
			$td2="<td>$subject_name</td>";
			$colspan="";
		}

		//判斷是否該科已經出現過
		if(!in_array($scope_id,$scope_id_array)){
			$scope_id_array[]=$scope_id;
			$scope_name=&get_subject_name($scope_id);

			//假如該科目有分科，則加入rowspan屬性
			$rowspan=(in_array($scope_id,$scope_have_subject))?"rowspan='$subject_num[$scope_id]'":"";

			//計算分科排序的編號
			$the_sub_sort=$subject_num[$scope_id]+1;
			$add_subject_pic=($mode=="view" or $mode=="clear_view")?"":"<a href='{$_SERVER['PHP_SELF']}?act=add_subject&scope_id=$scope_id&sel_year=$sel_year&sel_seme=$sel_seme&Cyear=$Cyear&class_id=$class_id&ss_sort=$subject_sort&ss_sub_sort=$the_sub_sort'>
			<img src='images/explode.png' alt=\"在 $scope_name 底下新增一分科\" border=0>
			</a>";
			
			$td="<td $rowspan $colspan align='right' nowrap>
			$scope_name
			$add_subject_pic
			</td>
			$td2";
   		}else{
			$scope_name="";
			$rowspan="";
			$td=$td2;
		}
		
		//功能表（若是觀看狀態，則不秀出表單）
		$modify_tool=($mode=="view" or $mode=="clear_view")?"":"<td class='small' nowrap>
		<a href='{$_SERVER['PHP_SELF']}?act=modify_exam&sel_year=$sel_year&sel_seme=$sel_seme&ss_id=$ss_id&Cyear=$Cyear&class_id=$class_id'>
		<img src='images/edit.png' border=0 hspace=3>修改</a>
		<a href=\"javascript:func($ss_id);\">
		<img src='images/del.png' border=0 hspace=3>刪除
		</a></td>";

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
		
		
		//假如是分科的話，排序欄位設為sub_sort
		if(empty($subject_id)){
			//合科
			$sort_col_name="sort";
			$sort_col_name_val=$subject_sort;
			$sort_other="sub_sort";
			$sort_other_val=0;
			$show_sort=$subject_sort;
			$sort_col_kind="hidden";
		}else{
			//分科
			$sort_col_name="sub_sort";
			$sort_col_name_val=$subject_sub_sort;
			$sort_other="sort";
			$sort_other_val=$subject_sort;
			$show_sort=$subject_sort."-".$subject_sub_sort;
			$sort_col_kind="text";
		}
		
		$array2[]=$subject_link_ss;
		
	
		$c29=compare_ss($sel_year,$sel_seme,$class9[$Cyear],$subject_link_ss);
		
		//產生平時項目的SELECT
		$nor_item_select="<select name='nor_item_kind'><option value=''>*不指定*</option>";
		foreach($nor_item_array as $key=>$value){
			if($nor_item_kind==$key) $selected='selected'; else $selected='';
			$show_value=$show_nor_items?"$key ($value)":$key;
			$nor_item_select.="<option value='$key' $selected>$show_value</option>";		
		}		
		$nor_item_select.="<select>";
		
		
		
		//產生國教署課程對應的SELECT
		if($act=="modify_exam" && $ss_id==$id) {
		
			//檢查是否已經有設定國教署課程對應  若無 自動抓取以前同領域同科目的資料做為預設值
			if(!$k12ea_category) {
				$query = "SELECT k12ea_category,k12ea_area,k12ea_subject,k12ea_language,k12ea_frequency from score_ss WHERE scope_id=$scope_id AND $subject_id=subject_id AND ss_id<>$ss_id AND k12ea_category>0 ORDER BY ss_id limit 1";
				$res_ref = $CONN->Execute($query);// or trigger_error("系統錯誤! $query",E_USER_ERROR);
				
				$k12ea_category = $res_ref->fields['k12ea_category'];
				$k12ea_area = $res_ref->fields['k12ea_area'];
				$k12ea_subject = $res_ref->fields['k12ea_subject'];
				$k12ea_language = $res_ref->fields['k12ea_language'];
				$k12ea_frequency = $res_ref->fields['k12ea_frequency'];
			}
			
			$k12ea_category_select="<select name='k12ea_category'><option value=''></option>";
			foreach($k12ea_category_array as $key=>$value){
				if($k12ea_category==$key) $selected='selected'; else $selected='';
				$k12ea_category_select.="<option value='$key' $selected>$value</option>";		
			}
			
			$k12ea_area_select="<select name='k12ea_area'><option value=''></option>";
			foreach($k12ea_area_array as $key=>$value){
				if($k12ea_area==$key) $selected='selected'; else $selected='';
				$k12ea_area_select.="<option value='$key' $selected>$value</option>";		
			}
			
			$k12ea_subject_select="<select name='k12ea_subject'><option value=''></option>";
			foreach($k12ea_subject_array as $key=>$value){
				if($k12ea_subject==$key) $selected='selected'; else $selected='';
				$k12ea_subject_select.="<option value='$key' $selected>$value</option>";		
			}
			
			$k12ea_language_select="<select name='k12ea_language'><option value=''></option>";
			foreach($k12ea_language_array as $key=>$value){
				if($k12ea_language==$key) $selected='selected'; else $selected='';
				$k12ea_language_select.="<option value='$key' $selected>$value</option>";		
			}
		}
		
		
		
		//科目主要內容
		$ss.=($act=="modify_exam" && $ss_id==$id)?"
		<tr bgcolor='white'>
		<form action='{$_SERVER['PHP_SELF']}' method='post'>
			$td
			<td align='center'>$ss_id</td>
			<td nowrap><input type='text' name='sections' value='$sections' size='1'></td>
			<td align='center'><input type='checkbox' name='need_exam' value=1 $checked></td>
			<td align='center'><input type='checkbox' name='print' value=1 $print_checked></td>
			<td nowrap><input type='text' name='rate' value='$rate' size='1'></td>
			<td align='center'>
			<input type='$sort_col_kind' name='".$sort_other."' value='".$sort_other_val."' size='1'>
			<input type='text' name='".$sort_col_name."' value='".$sort_col_name_val."' size='1'></td>
			<td><select name='link_ss'>".select_class9($class9[$Cyear],$c29)."</select></td>
			<td>$nor_item_select</td>
			<td>$k12ea_category_select</td>
			<td>$k12ea_area_select</td>
			<td>$k12ea_subject_select</td>
			<td>$k12ea_language_select</td>
			<td class='small'>
			<input type='hidden' name='ss_id' value='$ss_id'>
			<input type='hidden' name='scope_id' value='$scope_id'>
			<input type='hidden' name='subject_id' value='$subject_id'>
			<input type='hidden' name='pre_scope_sort' value='$sort_other_val'>
			<input type='hidden' name='sel_year' value='$sel_year'>
			<input type='hidden' name='sel_seme' value='$sel_seme'>
			<input type='hidden' name='Cyear' value='$Cyear'>
			<input type='hidden' name='class_id' value='$class_id'>
			<input type='submit' name='act' value='儲存設定'  class='b1'>
			</td>
		</form>
		</tr>
		":"
		<tr bgcolor='white'>
			$td
			<td align='center'>$ss_id</td>
			<td nowrap align='center'><font color='#A23B32' face='arial'>$sections</font></td>
			<td align='center'>$exam_pic</td>
			<td align='center'>$print_pic</td>			
			<td nowrap align='center'><font color='#A23B32' face='arial'>$rate</font></td>
			<td align='center' class='small'nowrap>
			<font color='#A7C0EF'>$show_sort</font>
			<td>$c29</td>
			<td>$nor_item_kind</td>
			<td bgcolor='#ffeecc'>$k12ea_category_array[$k12ea_category]</td>
			<td bgcolor='#ffeecc'>$k12ea_area_array[$k12ea_area]</td>
			<td bgcolor='#ffeecc'>$k12ea_subject_array[$k12ea_subject]</td>
			<td bgcolor='#ffeecc'>$k12ea_language_array[$k12ea_language]</td>
			</td>
			$modify_tool
		</tr>
		";
	}

	$semester_name=($sel_seme=='2')?"下":"上";
	
	//功能表（若是觀看狀態，則不秀出表單）
	$modify_tool_title=($mode=="view" or $mode=="clear_view")?"":"<td align='center' rowspan=2>功能</td>";

	//若已排定課程或有成績時,不允許更改
	//print_r($_REQUEST);
	$limit_memo ='';
	if ($_REQUEST[sel_year]<>''){
		$query = "select count(*) from score_semester_$_REQUEST[sel_year]_$_REQUEST[sel_seme] where class_id='$_REQUEST[class_id]'";
		$res_con = $CONN->Execute($query);// or trigger_error("系統錯誤! $query",E_USER_ERROR);
		if ($res_con->fields[0]>0){
			$limit_memo = "<font color='red'>該班先前已設定為年級課程,並已有成績紀錄,不允許重設為班級課程</font>";
			$button='';	
		}
	}
	if ($limit_memo<>'')
		$no_content="<tr bgcolor='white'><td colspan=10>$limit_memo</td></tr>";
	else
		$no_content=($no_data and !empty($class_id))?"<tr bgcolor='white'><td colspan=11>目前沒有該班的課程設定，該班課程會以該年級課程設定為準。</td></tr>":"";
	
	$ss_table="
	<table bgcolor='#9EBCDD' cellspacing=1 cellpadding=4 class='small'>
	<form action='{$_SERVER['PHP_SELF']}' method='post' name='myform'>
	<tr><td colspan='15' align='center' bgcolor='#E1ECFF'>
	<font color='#607387'>
	<font color='#000000'>$sel_year</font> 學年
	<font color='#000000'>$semester_name</font>學期
	$class_year_list $class_select 
	</font>
	</td></tr>
	</form>
	<tbody>
	<tr bgcolor='#E1ECFF'>
		<td align='center' nowrap rowspan=2>科目</td>
		<td align='center' nowrap rowspan=2>分科</td>
		<td align='center' nowrap rowspan=2>課程代碼</td>
		<td align='center' nowrap rowspan=2>節數</td>		
		<td align='center' nowrap rowspan=2>計分</td>
		<td align='center' nowrap rowspan=2>完整</td>		
		<td align='center' nowrap rowspan=2>加權</td>
		<td align='center' nowrap rowspan=2>排序</td>
		<td align='center' nowrap rowspan=2>九年一貫對應</td>
		<td align='center' nowrap rowspan=2>平時成績項目指定</td>
		<td align='center' nowrap colspan=4 bgcolor='#ffeecc'>國教署人力資源網課程對應</td>
		$modify_tool_title
	</tr>
	<tr bgcolor='#E1ECFF'>
		<td align='center' nowrap bgcolor='#ffeecc'>類別</td>
		<td align='center' nowrap bgcolor='#ffeecc'>領域</td>
		<td align='center' nowrap bgcolor='#ffeecc'>科目</td>
		<td align='center' nowrap bgcolor='#ffeecc'>語言別</td>	
	</tr>
	$ss
	$no_content
	</tbody>
	</table>";
 ///(本土語言才需填寫)

	//尚未有資料時，也做一個選單
	if(empty($select_scope)){
		//取得領域名稱
		$select_scope=&select_subject($scope_id,'1','scope');

		//取得學科名稱
		$select_subject=&select_subject($subject_id,'1','subject');
	}


	//取得領域名稱
	$select_scope=&select_subject("",'1','scope');
	$select_subject=&select_subject("",'1','subject');



	//新增表單（若是觀看狀態，則不秀出表單）
	if($act=="add_ss"){
		$scope_num=sizeof($scope_id_array)+1;	
		$add_form=&add_form($sel_year,$sel_seme,$Cyear,$class_id,$scope_num);
	}elseif($act=="add_subject"){
		$add_form=&add_subject_form($add_scope_id,$sel_year,$sel_seme,$Cyear,$class_id,$_REQUEST[ss_sort],$_REQUEST[ss_sub_sort]);
	}

	//相關功能表
	$tool_bar = ($mode=="clear_view")?"":make_menu($school_menu_p);

	//說明（若是觀看狀態，則不秀出表單）
	$help_text="
	<span style='color:red;'>設定前請先參考各縣市學生成績考查規定！</span>
	||<a href='{$_SERVER['SCRIPT_NAME']}?act=setup_view'>課程設定要訣</a>（第一次設定者，強烈建議觀看！）
	||<img src='images/explode.png' alt='在 $scope_name 底下新增一分科' border=0 hspace='5'>是新增分科的按鈕。
	||所謂「科目」，就是指成績單會秀出單一成績的科目。
	||所謂「分科」，是指某科目底下的其中一科，數個分科按加權比例所組成一個科目的成績。<br>
	<font color='#8FAAC8'>例如：「自然與生活科技」<font color='darkYellow'>（科目）</font>
	可能是由「物理」、「化學」、「生物」、「地球科學」<font color='darkYellow'>（四個分科）</font>所組成。
	考試時，可能四科都有各自的成績，但列印成績單時，這四科成績會依照加權比例，計算成「自然與生活科技」的成績，
	成績單只會秀出「自然與生活科技」的成績。</font>
	||<span style='color:brown;'>「課程代碼」：系統記錄成績的依據，號碼為自動產生，使用者無法修改。刪除課程後重設同名稱的課程，系統視為不一樣的學習科目。
	||「節數」：指每週的上課節數，係為方便您設定加權對照用，與成績計算無關，不設定留空即可。</span>
	||「計分」：教師要對該科輸入成績，而且該科目會印在成績單上。
	||「完整」：教師輸入該科成績時，段考等成績要依次輸入，若沒選，則可以只輸入總成績，需依次不輸入平時或段考成績。
	||「加權」：該科的成績計算加權。
	||『快速複製』按鈕，可以由其他年級的課程規劃表來複製並新增成新的課程規劃表。注意！只有在該年級還未設定任何科目時才會出現。
	";

	$helptmp=&help($help_text);
	$help=($mode=="view" or $mode=="clear_view") ? "" : $helptmp;
	
	if($Cyear=="" and $class_id="")$button="";
	
	
	$none_compare_ss=none_compare_ss($sel_year,$sel_seme,$class9[$Cyear],$array2);
	
	
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
	<p>
	$none_compare_ss
	</p>
	$help
	";
	return $main;
}


//快速複製介面
function &fast_copy($sel_year,$sel_seme,$Cyear,$show_Cyear){
	global $school_kind_name,$school_menu_p;
	//相關功能表
	$tool_bar=&make_menu($school_menu_p);
	$semester_name=($sel_seme=='2')?"下":"上";
	$ss_list=&get_all_year_ss_list($sel_year,$sel_seme,$Cyear,$show_Cyear);

	//說明
	$help_text="
	先點選某一個之前已經設定好的課程設計表（可點選觀看連結，看該課程規劃表的內容）。||
	按下最下方按鈕就完成複製了！
	";
	$help=&help($help_text);

	$main="
	$tool_bar
	<table>
	<tr><td>請選擇一個之前設定好的課程，
	系統會將該課程複製並新增成：<br>
	『".$sel_year." 學年".$semester_name."學期".$school_kind_name[$Cyear]."』
	的課程設定表</td></tr>
	<tr><td>
	$ss_list
	</td></tr>
	</table>
	<br>
	$help
	";

	return $main;
}


//新增年度科目
function add_ss($id="",$name="",$kind="",$sel_year="",$sel_seme="",$add_scope_id="",$need_exam='1',$rate='1',$Cyear="",$class_id="",$print="",$sort="",$sub_sort=""){
	global $CONN;
	
	if($kind=="scope"){
		$ss_scope_id=$id;
		$ss_scope_name=$name;
		$ss_subject_id="";
		$ss_subject_name="";
	}elseif($kind=="subject"){
		$ss_scope_id=$add_scope_id;
		$ss_scope_name="";
		$ss_subject_id=$id;
		$ss_subject_name=$name;
	}


	//假如完全沒有科目資料則退出
	if(empty($name) && empty($id)){
		return;
	}elseif(check_in_ss($ss_scope_id,$ss_scope_name,$ss_subject_id,$ss_subject_name,$sel_year,$sel_seme,$Cyear,$class_id)){
		//檢查看看是否已經有該科目
		return;
	}

	
	if($kind=="scope"){
		//如果輸入的是名稱，看看名稱在不在清單中，若不在則加入。
		if(!empty($name)){
			//檢查$subject_name在不在科目清單中
			$sid=in_subject($name,$kind);
			$scope_id=(empty($sid))?add_subject($name,$kind):$sid;
		}elseif(!empty($id)){
			$scope_id=$id;
		}
		//取得科目名稱
		$link_ss=(empty($name))?get_subject_name($scope_id):$name;
	}elseif($kind=="subject"){
		if(!empty($name)){
			//檢查$subject_name在不在科目清單中
			$sid=in_subject($name,$kind);
			$subject_id=(empty($sid))?add_subject($name,$kind):$sid;
		}elseif(!empty($id)){
			$subject_id=$id;
		}
		$scope_id=$add_scope_id;
		//取得科目名稱
		$link_ss=(empty($name))?get_subject_name($scope_id)."-".get_subject_name($subject_id):get_subject_name($scope_id)."-".$name;
	}

	//加入一課程資料
	$sql_insert = "insert into score_ss (scope_id,subject_id,year,semester,class_year,class_id,enable,need_exam,rate,print,sort,sub_sort,link_ss) values ('$scope_id','$subject_id','$sel_year','$sel_seme','$Cyear','$class_id','1','$need_exam','$rate','$print','$sort','$sub_sort','$link_ss')";
	$CONN->Execute($sql_insert) or user_error($sql_insert,256);
	
	//若是分科的話，把原課程隱藏起來
	if($add_scope_id){
		if(hidden_ss($scope_id,$sel_year,$sel_seme,$Cyear,$class_id))	return true;
	}

	return ;
}

//修改領域
function update_ss($ss_id,$scope_id,$subject_id,$sel_year,$sel_seme){
	global $CONN;
	$sql_update = "update score_ss set scope_id='$scope_id',subject_id='$subject_id',year='$sel_year',semester ='$sel_seme',nor_item_kind='$nor_item_kind',sections='$sections',k12ea_category='$k12ea_category',k12ea_area='$k12ea_area',k12ea_subject='$k12ea_subject',k12ea_language='$k12ea_language' where ss_id = '$ss_id'";
	if($CONN->Execute($sql_update))		return true;
	return  false;
}

//更新一筆考試設定
function update_exam_rate_set($ss_id="",$scope_id="",$subject_id="",$need_exam="",$rate="",$print="",$link_ss="",$sort="",$sub_sort="",$pre_scope_sort="",$sel_year="",$sel_seme="",$nor_item_kind="",$sections="",$k12ea_category="",$k12ea_area="",$k12ea_subject="",$k12ea_language=""){
	global $CONN;
	
	$sql_update = "update score_ss set need_exam='$need_exam',rate='$rate',print='$print',sort='$sort',sub_sort='$sub_sort',link_ss='$link_ss',nor_item_kind='$nor_item_kind',sections='$sections',k12ea_category='$k12ea_category',k12ea_area='$k12ea_area',k12ea_subject='$k12ea_subject',k12ea_language='$k12ea_language' where ss_id =$ss_id";
	$CONN->Execute($sql_update) or trigger_error("SQL語法執行失敗，SQL語法如下： $sql_update", E_USER_ERROR);
	
	//假如分科的領域排序有更變，一但領域的排序變更，那麼其他同領域的分科的領域排序也要一起變化
	if($pre_scope_sort!=$sort){
		$sql_update = "update score_ss set sort='$sort' where scope_id =$scope_id and subject_id!=0 and year='$sel_year' and semester='$sel_seme' and enable='1'";
		if($CONN->Execute($sql_update)) return true;
	}
	
	return false;
}

//刪除領域
function del_ss($ss_id){
	global $CONN,$sel_year,$sel_seme;
	$sql_update = "update score_ss set enable='0' where ss_id = '$ss_id'";
	if($CONN->Execute($sql_update))		return true;
	return  false;
}


//刪除該年級，該學期所有領域
function del_all_ss($sel_year,$sel_seme,$Cyear,$class_id=""){
	global $CONN;
	if(!empty($class_id)){
		$cls="and class_id='$class_id'";
	}
	$sql_update = "update score_ss set enable='0' where year='$sel_year' and semester ='$sel_seme' and class_year='$Cyear' $cls";
	//echo $sql_update ;
	//$sql_update = "delete  from score_ss  where year='$sel_year' and semester ='$sel_seme' and class_year='$Cyear' $cls";
	
	if($CONN->Execute($sql_update))		return true;
	return  false;
}

//刪除領域(需判斷領域有，但分科沒有，通常用在分科後，把原來的隱藏起來)
function hidden_ss($scope_id,$sel_year,$sel_seme,$Cyear,$class_id=""){
	global $CONN;
	if(!empty($class_id)){
		$cls="and class_id='$class_id'";
	}
	$sql_update = "update score_ss set enable='0' where scope_id = '$scope_id' and subject_id='0' and year='$sel_year' and semester='$sel_seme' and class_year='$Cyear' $cls";
	if($CONN->Execute($sql_update))		return true;
	return  false;
}

//看科目在不在科目清單中
function in_subject($subject_name,$subject_kind){
	global $CONN,$sel_year,$sel_seme;
	$sql_select = "select subject_id from score_subject where subject_name = '$subject_name' and subject_kind='$subject_kind'";
	$recordSet=$CONN->Execute($sql_select);

	while (!$recordSet->EOF) {
		$subject_id = $recordSet->fields["subject_id"];
		$recordSet->MoveNext();
	}
	return $subject_id;
}



//新增合科的表單
function &add_form($sel_year,$sel_seme,$Cyear,$class_id="",$sort=""){

	$select_scope=&select_subject($scope_id,'1','scope');
	//自動選擇學期
	$selected1=($sel_seme=='1')?"selected":"";
	$selected2=($sel_seme=='2')?"selected":"";
	$ss_form="
	<table cellspacing='4' cellpadding='2' bgcolor='#AFD378'>
	<form action='{$_SERVER['PHP_SELF']}' method='post'>
	<tr bgcolor='#1D6718'><td align='center'><font color='#FFFFFF'>新增科目</font></td></tr>
	<tr><td class='small' align='center'>
	<p>請選擇科目名稱：</p>
	$select_scope
	<p>亦可自行填入：</p>
	<input type='text' name='subject_name' size='14'>
	<p>
	<input type='checkbox' name='need_exam' value='1' checked>計分
	<input type='checkbox' name='print' value='1' checked>完整
	</p>
	<input type='hidden' name='sel_year' value='$sel_year'>
	<input type='hidden' name='sel_seme' value='$sel_seme'>
	<input type='hidden' name='rate' value='1'>
	<input type='hidden' name='subject_kind' value='scope'>
	<input type='hidden' name='Cyear' value='$Cyear'>
	<input type='hidden' name='class_id' value='$class_id'>
	<input type='hidden' name='sort' value='$sort'>
	<input type='hidden' name='sub_sort' value='0'>
	<input type='submit' name='act' value='新增' class='b1'></td></tr>
	</form>
	</table>";
	return $ss_form;
}

//新增分科的表單
function &add_subject_form($scope_id,$sel_year,$sel_seme,$Cyear,$class_id="",$ss_sort="",$ss_sub_sort=""){

	$scope_name=&get_subject_name($scope_id);
	$select_subject=&select_subject('','1','subject');
	$ss_form="
	<table cellspacing='4' cellpadding='2' bgcolor='#FFE0C1'>
	<form action='{$_SERVER['PHP_SELF']}' method='post'>
	<tr><td class='small' align='center'>
	在<font color='#0000FF'>$scope_name</font>科<br>下新增分科：
	<p>$select_subject</p>
	亦可自行填入：
	<p><input type='text' name='subject_name' size='10'></p>
	
	<input type='checkbox' name='need_exam' value='1' checked>計分
	<input type='checkbox' name='print' value='1' checked>完整
	
	<input type='hidden' name='subject_kind' value='subject'>
	<input type='hidden' name='scope_id' value='$scope_id'>
	<input type='hidden' name='sel_year' value='$sel_year'>
	<input type='hidden' name='sel_seme' value='$sel_seme'>
	<input type='hidden' name='Cyear' value='$Cyear'>
	<input type='hidden' name='class_id' value='$class_id'>
	<input type='hidden' name='need_exam' value='1'>
	<input type='hidden' name='rate' value='1'>
	<input type='hidden' name='sort' value='".$ss_sort."'>
	<input type='hidden' name='sub_sort' value='".$ss_sub_sort."'>
	
	<p><input type='submit' name='act' value='加入分科' class='b1'></p>
	</td></tr>
	</form>
	</table>";
	return $ss_form;
}

//查看要新增的合科或分科名稱是否已經有在裡面
function check_in_ss($scope_id="",$scope_name="",$subject_id="",$subject_name="",$sel_year="",$sel_seme="",$Cyear="",$class_id=""){
	global $CONN;

	if(!empty($scope_id) && !empty($subject_name)){
		$subject_id=get_subject_id($subject_name,'1');
		if(empty($subject_id))return false;
		$and="and scope_id=$scope_id and subject_id=$subject_id";
	}elseif(!empty($scope_id) && !empty($subject_id)){
		$and="and scope_id=$scope_id and subject_id=$subject_id";
	}elseif(!empty($scope_name)){
		$scope_id=get_subject_id($scope_name,'1');
		if(empty($scope_id))return false;
		$and="and scope_id=$scope_id";
	}elseif(!empty($scope_id)){
		$and="and scope_id=$scope_id";
	}else{
		return false;
	}
	
	if(!empty($class_id)){
		$cls="and class_id='$class_id'";
	} else {
		$cls="and class_id=''";
	}

	$sql_select = "select ss_id  from score_ss where enable='1' and year='$sel_year' and semester='$sel_seme' and class_year='$Cyear' $cls $and";

	$recordSet=$CONN->Execute($sql_select);
	$i=0;
	while (!$recordSet->EOF) {
		$id=$recordSet->fields["ss_id"];
		if(!empty($id))return true;
		$recordSet->MoveNext();
	}
	return false;
}


//取得課程表中所有年度及年級
function &get_all_year_ss_list($sel_year,$sel_seme,$nowCyear,$show_Cyear=""){
	global $CONN,$school_kind_name;

	//找出該表中所有的年度與學期，要拿來作選單
	$sql_select = "select year,semester,class_year from score_ss where enable='1' order by year,semester,class_year";
	$recordSet=$CONN->Execute($sql_select);
	$other_ss=array();
	while (!$recordSet->EOF) {
		$year = $recordSet->fields["year"];
		$semester = $recordSet->fields["semester"];
		$Cyear = $recordSet->fields["class_year"];

		$semester_name=($semester=='2')?"下":"上";
		$other_ss_name="
		&nbsp;".$year."".$semester_name."，".$school_kind_name[$Cyear]." 課程設定表
		<a href='{$_SERVER['PHP_SELF']}?act=fast_copy&show_Cyear=$Cyear&sel_year=$year&sel_seme=$semester&Cyear=$nowCyear'>『觀看』</a><br>";

		//製作其他學年學期的選單
		if(!in_array($other_ss_name,$other_ss)){
			$other_ss[$i]=$other_ss_name;
			$other_ss_text.="<tr>
			<td><input type='radio' name='copy_set' value='".$year."-".$semester."-".$Cyear."'></td>
			<td>$other_ss_name</td></tr>";
			$i++;
		}

		$recordSet->MoveNext();
	}

	//若是有指定觀看，則秀出查閱內容
	if(!empty($show_Cyear)){
		$show_other_class=&list_ss($sel_year,$sel_seme,$show_Cyear,$class_id,"clear_view");
	}
	$semester_name=($sel_seme=='2')?"下":"上";

	$main="
	<table><tr><td valign='top'>
		<table>
		<form action='{$_SERVER['PHP_SELF']}'>
		$other_ss_text
		<input type='hidden' name='act' value='copy'>
		<input type='hidden' name='Cyear' value='$nowCyear'>
		<input type='hidden' name='sel_year' value='$sel_year'>
		<input type='hidden' name='sel_seme' value='$sel_seme'>
		<tr><td colspan='2'>
		<input type='submit' value='複製成「".$sel_year."".$semester_name."，".$school_kind_name[$nowCyear]."」課程設定表' class='b1'></td></tr>
		</form>
		</table>
	</td><td valign='top'>$show_other_class</td></tr></table>
	";
	return $main;
}

//複製某一學年學期年級的課程安排給另一個
function copy_ss($copy_set,$sel_year,$sel_seme,$Cyear){
	global $CONN;
	
	$c=explode("-",$copy_set);

	$sql_select = "select * from score_ss where enable='1' and year='$c[0]' and semester='$c[1]' and class_year='$c[2]' order by sort,sub_sort";
	$recordSet=$CONN->Execute($sql_select);
	while (!$recordSet->EOF) {
		$scope_id=$recordSet->fields["scope_id"];
		$subject_id=$recordSet->fields["subject_id"];
		$need_exam=$recordSet->fields["need_exam"];
		$rate=$recordSet->fields["rate"];
		$print=$recordSet->fields["print"];
		$sort=$recordSet->fields["sort"];
		$sub_sort=$recordSet->fields["sub_sort"];
		$link_ss=$recordSet->fields["link_ss"];
		$nor_item_kind=$recordSet->fields["nor_item_kind"];
		$sections=$recordSet->fields["sections"];
		$k12ea_category=$recordSet->fields["k12ea_category"];
		$k12ea_area=$recordSet->fields["k12ea_area"];
		$k12ea_subject=$recordSet->fields["k12ea_subject"];
		$k12ea_language=$recordSet->fields["k12ea_language"];

		//加入一課程資料
		$sql_insert = "insert into score_ss (scope_id,subject_id,year,semester,class_year,enable,need_exam,rate,print,sort,sub_sort,link_ss,nor_item_kind,sections,k12ea_category,k12ea_area,k12ea_subject,k12ea_language) values ('$scope_id','$subject_id','$sel_year','$sel_seme','$Cyear','1','$need_exam',$rate,'$print','$sort','$sub_sort','$link_ss','$nor_item_kind','$sections','$k12ea_category','$k12ea_area','$k12ea_subject','$k12ea_language')";
		$CONN->Execute($sql_insert);

		$recordSet->MoveNext();
	}
	return;
}

//列出所有年級的課程規劃表
function &list_all_ss($sel_year,$sel_seme){
	global $CONN,$school_menu_p,$SFS_PATH_HTML;
	//列出課程設定中有設定年級與班級
	$yc_array=get_ss_yc($sel_year,$sel_seme);
	
	foreach($yc_array as $yc){
		$main.=list_ss($sel_year,$sel_seme,$yc[Cyear],$yc[class_id],"clear_view");
	}
	
	//相關功能表
	$tool_bar = ($mode=="clear_view")?"":make_menu($school_menu_p);

	return $tool_bar.$main;
}


//設定要訣
function &setup_view(){
	global $school_menu_p,$SFS_PATH_HTML;
	//相關功能表
	$tool_bar=&make_menu($school_menu_p);
	$main="
	$tool_bar
	<table bgcolor='#9EBCDD' cellspacing=1 cellpadding=4>
	<tr bgcolor='#FFFFFF'><td style='line-height: 1.6; '>
	設定課程和底下這兩者息息相關：<p>『課表』與『成績單』</p>

	<p>
	以國小一年級為例，假設成績單上要出現：語文、健康與體育、生活、數學、綜合活動...等領域，
	那麼您的『科目』，至少就要有『語文、健康與體育、生活、數學、綜合活動』這幾科。例如：</p>
	<p><img src='images/help1.png' width=300 height=189 border=0></p>
	<p>
	可是課表上，不可能只用領域去安排課表啊？沒錯！有些領域是由好幾科組成的，因此，必須用到『分科』的功能。
	</p>
	<p>例如：語文領域，實際上課可能是有『國語、鄉土語文、英語』等科目所組成。那麼，可以用『分科』功能，把這些相關科目加進去：	</p>
	<p><img src='images/help2.png' width=330 height=243 border=0></p>

	<p>什麼是『計分』呢？『加權』又是什麼？</p>
	<p>『計分』=該科目要列入學期總分，換言之，老師必須輸入該科成績。</p>
	<p>『加權』=由於學期成績『語文』領域可能只有一個學期總分，然而，語文可能又是由『國語、鄉土語文、英語』等科目所組成，
	因此，必須有個『加權』來計算這三個科目在『語文』領域中，佔學期總分的計算比例。</p>
	
	<p>萬一排課時，需要加入如：『 輔導活動』、『補救教學』、『導師時間』...等類似不計分的課程，且不希望出現在成績單上，那怎麼辦？</p>
	
	<p>很簡單，把他們當作科目加進去（當作分科亦可），並利用『修改』功能把『計分』取消即可。這樣就可以排入功課表，但成績單不出現。</p>

	<p><img src='images/help3.png' width=332 height=326 border=0></p>
	<p>又假設課表中想要有：『生活』、『社會』、『美勞』、『禮儀』這幾科，但是這幾科都屬於『生活』領域（變成某一分科和科目的名稱相同），且『禮儀』不想計分，那麼，可以這麼做：</p>
	<p><img src='images/help4.png' width=334 height=404 border=0></p>
	<p>安排至此，已經可以大致了解『功課表』有以下科目可以排進去：</p>
	<ul>
	<li><font color='blue'>國語、鄉土語文、英語、健康與體育、社會、生活、美勞、<font color='Red'>禮儀</font>、數學、綜合活動、<font color='Red'>輔導活動</font>、<font color='Red'>補救教學</font>、<font color='Red'>導師時間</font></font>
	</ul>
	<p>而需要教師輸入成績以便計算的科目有（不計分的科目自然就不需要啦！）：</p>
	<ul>
	<li><font color='blue'>國語、鄉土語文、英語、健康與體育、社會、生活、美勞、數學、綜合活動</font>
	</ul>

	在成績單上會出現的科目有（就是所有『科目』的名稱啦！）：</p>
	<ul>
	<li><font color='Green'>語文</font>（由<font color='blue'>國語、鄉土語文、英語</font>計算出一個成績）、<br>
	<li><font color='Green'>健康與體育</font>、<br>
	<li><font color='Green'>生活</font>（由<font color='blue'>社會、生活、美勞</font>計算出一個成績）、<br>
	<li><font color='Green'>數學</font>、<br>
	<li><font color='Green'>綜合活動</font></ul>

	整理一下：

	<table bgcolor='#9EBCDD' cellspacing=1 cellpadding=4>
	<tr bgcolor='#CDD5FF'><td>成績單上會出現的科目</td><td>功課表可排課的科目</td><td>教師需輸入成績科目</td></tr>

	<tr bgcolor='#FFFFFF'><td valign='top'>語文</td><td valign='top'>國語<br>鄉土語文<br>英語</td><td valign='top'>國語<br>鄉土語文<br>英語</td></tr>
	<tr bgcolor='#FFFFFF'><td>健康與體育</td><td>健康與體育</td><td>健康與體育</td></tr>
	<tr bgcolor='#FFFFFF'><td>生活</td><td>社會<br>生活<br>美勞<br>禮儀</td><td>社會<br>生活<br>美勞</td></tr>
	<tr bgcolor='#FFFFFF'><td>數學</td><td>數學</td><td>數學</td></tr>
	<tr bgcolor='#FFFFFF'><td>綜合活動</td><td>綜合活動</td><td>綜合活動</td></tr>
	<tr bgcolor='#FFFFFF'><td></td><td>輔導活動<br>補救教學<br>導師時間</td><td></td></tr>
	</table>
	
	<p>最後，注意！請先確認好課程，再來設定功課表。若是設定完功課表，然後又去更動課程設定（例如：新增、刪除科目或分科）可能會導致功課表上原有的課程消失（重設即可）。</p>
	
	</td></tr>
	</table>
	";
return $main;
}


//該課程是否已經有安排了課程
function &have_course($sel_year,$sel_seme,$ss_id,$Cyear,$class_id=""){
	global $CONN;
	if(!empty($class_id)){
		$cls="and class_id='$class_id'";
	}
	//找出某班所有課程
	$sql_select = "select count(*) from score_course where ss_id='$ss_id' and  year=$sel_year and semester='$sel_seme' $cls";
	$recordSet=$CONN->Execute($sql_select) or trigger_error("錯誤訊息： $sql_select", E_USER_ERROR);
	list($count)= $recordSet->FetchRow();
	return $count;
}

//該課程所影響到的課程
function &show_ss_id_course($sel_year,$sel_seme,$ss_id,$Cyear,$class_id=""){
	global $CONN,$SFS_PATH_HTML;
	if(!empty($class_id)){
		$cls="and class_id='$class_id'";
	}
	$ss_name=&get_ss_name("","","長",$ss_id);
	$C_day=array("1"=>"星期一","星期二","星期三","星期四","星期五","星期六","星期日");
	//找出某班所有課程
	$sql_select = "select class_id,teacher_sn,day,sector,room from score_course where ss_id='$ss_id' and  year=$sel_year and semester='$sel_seme' $cls order by day,sector";
	$recordSet=$CONN->Execute($sql_select) or trigger_error("錯誤訊息： $sql_select", E_USER_ERROR);
	while (list($class_id,$teacher_sn,$day,$sector,$room)= $recordSet->FetchRow()) {
		$man=get_teacher_name($teacher_sn);
		$class_name=class_id_2_old($class_id);

		$main.="<tr bgcolor='white'><td>$class_name[5]</td><td>$C_day[$day]</td><td>第 $sector 節</td><td><a href='".$SFS_PATH_HTML."school/new_course/teacher_class.php?sel_year=$sel_year&sel_seme=$sel_seme&view_tsn=$teacher_sn'>$man</a></td></tr>";
	}
	$main="
	$error_msg
	<table cellspacing=1 cellpadding=4 bgcolor='#9EBCDD'>
	<tr bgcolor='#E1E6FF'><td>班級</td><td>日期</td><td>節數</td><td>教師</td></tr>$main</table>";

	$error_msg=&error_tbl("請注意！！","您要刪除或改變的『".$ss_name."』課程，會影響到底下課表中的設定：<br>$main<br><form action='{$_SERVER['PHP_SELF']}'>其實刪除或改變並不會嚴重影響到系統運作，但是請您務必在修改後，也必須修改教師的配課設定，不然會導致已排好的課表找不到被刪掉的課程，如此，會連帶影響到成績計算。<p>確定要刪除或變更『".$ss_name."』？
	<input type='hidden' name='ss_id' value='$ss_id'><input type='hidden' name='Cyear' value='$Cyear'>
	<input type='hidden' name='act' value='do_del'><input type='submit' value='確定' class='b1'></form>");
	return $error_msg;
}


//自動加入九年一貫課程
function auto_copy($sel_year,$sel_seme,$Cyear,$class_id,$kind=""){
	global $class9,$IS_JHORES,$local_language;
  			  $need_exam='1';
  			  $rate='1';
  			  $print='1';	
	if($kind=="九年一貫"){
		if(!empty($class_id)){
			$class=get_class_all($class_id);
			$Cyear=$class[year];			
		}
		$The_Class=$class9[$Cyear];
		
		if(sizeof($The_Class)>0){
			$i=1;

			foreach($The_Class as $scope_name => $subject_name){
				//抓取國教署課程對應
				$k12ea_category = '1';  //通通預設為領域學習
				$k12ea_area = ($scope_name == '生活') ? '生活課程' : array_search($scope_name.'領域', k12ea_area());
				
				//檢查科目名稱是否已經在資料庫中
				$scope_id=chk_subject_id($scope_name,"scope",1);
				if(is_array($subject_name)){
					$j=1;
					foreach($subject_name as $sub_subject_s){
						//國教署課程對應
						$k12ea_language = '';
						switch($sub_subject_s) {
							case '本國語文':
								$k12ea_subject = '國語/文';
								break;
							case '鄉土語文':
								$k12ea_subject = '本土語言';
								$k12ea_language = array_search($local_language, k12ea_language());					
								$k12ea_language = $k12ea_language ? $k12ea_language : '11';
								//echo $local_language.'--'.$k12ea_language.'<br>'; exit;
								//print_r(k12ea_language()); exit;
								break;
							case '英語':
								$k12ea_subject = '英語/文';
								break;
						} 
						$k12ea_subject = array_search($k12ea_subject, k12ea_subject());					

						//科目是否有設定要輸入成績
						$need_exam ='1' ;
						$print = '1' ;    				
						list($sub_subject, $need_exam_t , $print_t) = split ('-', $sub_subject_s) ;

						if ($need_exam_t =='0' ) { 
							$need_exam='' ;
							$print = '' ;
						}  
						if ($print_t =='0') {  
						   $print = '' ;
						}
			
    			  //echo "$sub_subject_s , $sub_subject, $need_exam , $print <br>" ;				
						$subject_id=chk_subject_id($sub_subject,"subject",1);
						$sid=autoadd_ss($sel_year,$sel_seme,$scope_id,$subject_id,$need_exam,$rate,$print,$Cyear,$class_id,"1",$i,$j,$scope_name."-".$sub_subject,$k12ea_category,$k12ea_area,$k12ea_subject,$k12ea_language);
						$j++;
						$ss_id[]=$sid;
					}
				} else {
					//國教署課程對應
					$k12ea_language = '';
					switch($subject_name) {
						case '健康與體育':
							$k12ea_subject = $IS_JHORES ? $subject_name.'合科' : $subject_name;
							break;
						case '社會':
							$k12ea_subject = $IS_JHORES ? $subject_name.'合科' : $subject_name;
							break;
						case '藝術與人文':
							$k12ea_subject = $IS_JHORES ? $subject_name.'合科' : $subject_name;
							break;
						case '自然與生活科技':
							$k12ea_subject = $subject_name.'合科';
							break;
						case '數學':
							$k12ea_subject = $subject_name;
							break;
						case '綜合活動':
							$k12ea_subject = $IS_JHORES ? '綜合合科' : $subject_name;
							break;
					}
					$k12ea_subject = array_search($k12ea_subject, k12ea_subject());

				
    				$need_exam ='1' ;
    				$print = '1' ;   				
					$sid=autoadd_ss($sel_year,$sel_seme,$scope_id,"0",$need_exam,$rate,$print,$Cyear,$class_id,"1",$i,"",$scope_name,$k12ea_category,$k12ea_area,$k12ea_subject,$k12ea_language);
					$ss_id[]=$sid;
				}
				$i++;
			}
			
		}
	}
	return $ss_id;
}

//找出科目編號，由科目名稱找出 $subject_id
function chk_subject_id($subject_name,$kind,$enable='1'){
	global $CONN;

	if (!$subject_name)  user_error("沒有傳入科目名稱！請檢查！",256);

	// 確定連線成立
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);

	$where_enable=($enable)?"and enable='1'":"";
	$sql_select = "select subject_id from score_subject where subject_name='$subject_name' and subject_kind='$kind' $where_enable";
	$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);

	while (list($subject_id)=$recordSet->FetchRow()) {
		return  $subject_id;
	}
	
	if(empty($subject_id)){
		$subject_id=add_subject($subject_name,$kind);
	}
	return  $subject_id;
}

//新增年度科目
function autoadd_ss($sel_year,$sel_seme,$scope_id,$subject_id,$need_exam,$rate,$print,$Cyear="",$class_id="",$enable,$sort,$sub_sort,$link_ss,$k12ea_category,$k12ea_area,$k12ea_subject,$k12ea_language){
	global $CONN;
	//假如完全沒有科目資料則退出
	if(empty($scope_id)){
		return;
	}
	//加入一課程資料
//echo $sql_insert; exit;	
	$sql_insert = "insert into score_ss (scope_id,subject_id,year,semester,class_year,class_id,enable,need_exam,rate,print,sort,sub_sort,link_ss,k12ea_category,k12ea_area,k12ea_subject,k12ea_language) values ('$scope_id','$subject_id','$sel_year','$sel_seme','$Cyear','$class_id','1','$need_exam','$rate','$print','$sort','$sub_sort','$link_ss','$k12ea_category','$k12ea_area','$k12ea_subject','$k12ea_language')";
	$CONN->Execute($sql_insert) or user_error($sql_insert,256);	

	return mysql_insert_id();
}

//九年一貫科目對應
function compare_ss($sel_year,$sel_seme,$The_Class,$subject_link_ss){
	global $CONN,$school_kind_name;
	foreach($The_Class as $scope_name => $subject_name){
		if(is_array($subject_name)){
			foreach($subject_name as $sub_subject){
			  list($sub_subject, $need_exam_t , $print_t) = split ('-', $sub_subject) ;
				$subject=$scope_name."-".$sub_subject;			
				if($subject==$subject_link_ss){
					return $subject;
				}
			}
		}else{	
			if($subject_name==$subject_link_ss){
				return $subject_link_ss;
			}
		}
	}
	return "非預設領域科目";
}

//尚未完成的九年一貫科目
function none_compare_ss($sel_year,$sel_seme,$The_Class,$array2){
	global $CONN,$school_kind_name;
	foreach($The_Class as $scope_name => $subject_name){
		if(is_array($subject_name)){
			foreach($subject_name as $sub_subject){
				list($sub_subject, $need_exam_t , $print_t) = split ('-', $sub_subject) ;
				$subject=$scope_name."-".$sub_subject;
			}
		}else{	
			$subject=$subject_name;
		}			
		$array1[]=$subject;
	}
	
	$diff=array_diff($array1, $array2);
	$all="";
	foreach($diff as $d){
		$all.="<td>".$d."</td>";
	}
	
	if(empty($all))return "";
	
	$main="<table cellspacing=1 cellpadding=4 bgcolor='red' class='small'><tr bgcolor='#FFFFFF'><td bgcolor='#FF0000'><font color='white'>尚未完成對應的科目</font></td>$all</tr></table>";
	
	return $main;
}


//九年一貫科目選單
function select_class9($The_Class=array(),$link_ss=""){
	$subject="<option value=''>非預設領域科目</option>";
	foreach($The_Class as $scope_name => $subject_name){
		if(is_array($subject_name)){
			foreach($subject_name as $sub_subject){
				$selected=(!empty($link_ss) and $link_ss==$scope_name."-".$sub_subject)?"selected":"";
				$subject.="<option value='".$scope_name."-".$sub_subject."' $selected>".$scope_name."-".$sub_subject."</option>";
			}
		}else{	
			$selected=(!empty($link_ss) and $link_ss==$subject_name)?"selected":"";
			$subject.= "<option value='".$subject_name."' $selected>".$subject_name."</option>";
		}
	}
	return $subject;
}
	
?>
