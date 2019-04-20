<?php

// $Id: sfs_core_menu.php 7772 2013-11-15 07:07:28Z smallduh $


//將陣列內容加入系統選單
// $gid -- 選項類別
// $text_name -- 選項名稱
// $temp_arr -- 選項內容 
function join_sfs_text($gid,$text_name,$temp_arr) {
	global $CONN,$DATA_VAR;
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);

	if ($text_name=='')
		return false;

	$query = "select t_kind from sfs_text where t_kind='$text_name'";
	$result = $CONN->Execute($query) or trigger_error($CONN->ErrorMsg(), E_USER_ERROR) ;
	if ($result->EOF) {
		$query = "insert into sfs_text(t_kind,g_id,d_id,t_name,p_id) values('$text_name','$gid',0,'$text_name',0)";
		
		$CONN->Execute($query) or trigger_error($query, E_USER_ERROR);
		$query = "select t_id from sfs_text where t_kind='$text_name'";
		$res = $CONN->Execute($query) or trigger_error($query, E_USER_ERROR);
		$p_id = $res->fields[0];
		while (list($tid,$val) = each($temp_arr)) {
			$i++;
			//if (strtolower($DATA_VAR[character_set]) == 'big5')
			//$val = myAddSlashes($val);
			$val = AddSlashes($val);	
			$query = "insert into sfs_text(t_kind,g_id,d_id,t_name,t_parent,p_id,p_dot,t_order_id) values('$text_name',$gid,'$tid','$val','$p_id,',$p_id,'.',$i)";
			
			$CONN->Execute($query) or trigger_error($query, E_USER_ERROR);
		}
	}
	return true;
}

function myAddSlashes($st) {
if (get_magic_quotes_gpc()) {
return $st;
} else {
return AddSlashes($st);
}
} 

//下拉選單類


//取得今年教師的下拉選單
function &select_teacher($col_name="teacher_sn",$teacher_sn="",$enable='1',$sel_year="",$sel_seme="",$jump_fn="",$day="",$sector=""){
	global $CONN;
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);
	if(empty($sel_year))$sel_year = curr_year(); //目前學年
	if(empty($sel_seme))$sel_seme = curr_seme(); //目前學期
	$where=($enable=='1')?"where teach_condition='0'":"";
	$sql_select = "select name,teacher_sn from teacher_base $where";
	$recordSet=$CONN->Execute($sql_select) or trigger_error($query, E_USER_ERROR);
	$option="<option value='0'></option>";

	$jump=(!empty($jump_fn))?" onChange='$jump_fn()'":"";

	while (list($name,$tsn) = $recordSet->FetchRow()) {
		//教師在該堂的授課次數，若>2表示衝堂。
		if(!empty($day) and !empty($sector)){
			$tcn=get_teacher_course_num($sel_year,$sel_seme,$tsn,$day,$sector);
		}
		//若已經有課，以灰色顯示
		$color=($tcn>=1)?"#D7D7D7":"#000000";
		$selected=($tsn==$teacher_sn)?"selected":"";
		$option.="<option value='$tsn' $selected style='color: $color'>$name</option>\n";
	}

	$select_teacher="
	<select name='$col_name' $jump>
	$option
	</select>";
	return $select_teacher;
}

//取得今年教師的陣列
function &select_teacher_arr($enable='1',$sel_year="",$sel_seme="",$sector=""){
	global $CONN;
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);
	if(empty($sel_year))$sel_year = curr_year(); //目前學年
	if(empty($sel_seme))$sel_seme = curr_seme(); //目前學期
	$where=($enable=='1')?"where teach_condition='0'":"";
	$sql_select = "select name,teacher_sn from teacher_base $where";
	$recordSet=$CONN->Execute($sql_select) or trigger_error($query, E_USER_ERROR);

	while (list($name,$tsn) = $recordSet->FetchRow()) {
		//教師在該堂的授課次數，若>2表示衝堂。
		if(!empty($day) and !empty($sector)){
			$tcn=get_teacher_course_num($sel_year,$sel_seme,$tsn,$day,$sector);
		}
		//若已經有課，以灰色顯示
		$color=($tcn>=1)?"#D7D7D7":"#000000";
		$selected=($tsn==$teacher_sn)?"selected":"";
		$option.="<option value='$tsn' $selected style='color: $color'>$name</option>\n";
	}

	$select_teacher="
	<select name='$col_name' $jump>
	$option
	</select>";
	return $select_teacher;
}


//製作年級下拉選單
function &get_class_year_select($sel_year="",$sel_seme="",$Cyear="",$jump_fn="",$col_name="Cyear"){
	global $CONN,$school_kind_name,$school_kind_color;

	$class_year_array=get_class_year_array($sel_year,$sel_seme);

	if(sizeof($class_year_array)<1){
		$msg="資料庫中找不到 $sel_year 學年，第 $sel_seme 學期的班及資料。<p>
		請先進行 $sel_year 學年，第 $sel_seme 學期的
		<a href='".$SFS_PATH."/modules/every_year_setup/class_year_setup.php?act=setup&sel_year=$sel_year&sel_seme=$sel_seme'>
		班級設定</a>，才能繼續進行。</p>";
		trigger_error("無法取得該年級的班級設定： $msg", 256);
	}
	
	$class_option="";
	//取得年級陣列
	reset($class_year_array);
	while(list($i,$v)=each($class_year_array)){
		$selected=($Cyear==$class_year_array[$i])?"selected":"";
		$c_year=$class_year_array[$v];
    	$class_option.="<option value='$c_year' $selected style='background-color: $school_kind_color[$c_year];'>$school_kind_name[$c_year]</option>\n";
	}
	
	if(empty($class_option))trigger_error("查無年級資料", E_USER_ERROR);
	
	$jump=(!empty($jump_fn))?" onChange='$jump_fn()'":"";

	//製作年級選單
	$class_year_list="
	<select name='$col_name' $jump>
	<option value=''>請選年級</option>
	$class_option
	</select>
	";
	return $class_year_list;
}


//年級或班級下拉選單
function &get_class_select($sel_year="",$sel_seme="",$Cyear="",$col_name="class_id",$jump_fn="",$curr_class_id="",$mode="長",$option1="請選擇班級"){
	global $CONN,$school_kind_name,$school_kind_color;

	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);

	//假如年級位置不是空的，則僅列出該年級選單
	$and_Cyear=($Cyear == '')?"":" and c_year='$Cyear'";
	$sql_select = "select class_id,c_year,c_name from school_class where year='$sel_year' and semester = '$sel_seme' and enable='1' $and_Cyear order by c_year,c_sort";
	$class_name_option="";
	$recordSet=$CONN->Execute($sql_select)  or trigger_error($sql_select, E_USER_ERROR);
	while(list($class_id,$c_year,$c_name) = $recordSet->FetchRow()){
		$selected=($curr_class_id==$class_id)?"selected":"";
		$class_name_option.=($mode=="短")?"<option value='$class_id' $selected style='background-color: $school_kind_color[$c_year]'></option>\n":"<option value='$class_id' $selected style='background-color: $school_kind_color[$c_year];'>".$school_kind_name[$c_year]."".$c_name."班</option>\n";
	}
	if(empty($class_name_option))trigger_error("查無班級資料", E_USER_ERROR);

	$jump=(!empty($jump_fn))?" onChange='$jump_fn()'":"";

	$class_name_list="
	<select name='$col_name' $jump>
	<option value=''>$option1
	$class_name_option
	</select>";
	return $class_name_list;
}


//某年或各年班級下拉選單（利用物件的方式）
function &classSelect($sel_year="",$sel_seme="",$Cyear="",$col_name="class_id",$curr_class_id="",$is_submit=true){
	global $CONN,$school_kind_name,$school_kind_color;

	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);

	//假如年級位置不是空的，則僅列出該年級選單
	$and_Cyear=(!is_int($Cyear))?"":" and c_year='$Cyear'";
	$sql_select = "select class_id,c_year,c_name from school_class where year='$sel_year' and semester = '$sel_seme' and enable='1' $and_Cyear order by c_year,c_sort";
	$recordSet=$CONN->Execute($sql_select)  or user_error($sql_select, 256);
	while(list($class_id,$c_year,$c_name) = $recordSet->FetchRow()){
		$class_array[$class_id]=$school_kind_name[$c_year]."".$c_name."班";
	}
	
	if(sizeof($class_array)<=0){
		user_error("查無班級資料", 256);
	}
	$ds=new drop_select();
	$ds->s_name=$col_name; //選單名稱
	$ds->id=$curr_class_id;	//索引ID
	$ds->arr = $class_array; //內容陣列
	$ds->has_empty = true; //先列出空白
	$ds->top_option = "請選擇班級";
	$ds->bgcolor = "#FFFFFF";
	$ds->font_style = "font-size:12px";
	$ds->is_submit = $is_submit; //更動時送出查詢
	$class_name_list=$ds->get_select();

	return $class_name_list;
}


//取得年度與學期的下拉選單
function &date_select($sel_year,$sel_seme,$year_name="sel_year",$seme_name="sel_seme",$jump_fn=""){
	global $CONN,$class_year;
	
	//自動選擇學年，學期
	$selected1=($sel_seme=='1')?"selected":"";
	$selected2=($sel_seme=='2')?"selected":"";
	$jump=(!empty($jump_fn))?" onChange='$jump_fn()'":"";
	$main="
	<input type='text' name='$year_name' size='3' value='$sel_year'> 學年度，
	<select name='$seme_name' $jump>
	<option value='1' $selected1>上</option>
	<option value='2' $selected2>下</option>
	</select>學期
	";
	return $main;
}


//從班級設定中，找出已經設定好的學期和學年的下拉選單
function &class_ok_setup_year($sel_year,$sel_seme,$name="year_seme",$jump_fn=""){
	global $CONN;

	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);

	$jump=(!empty($jump_fn))?" onChange='$jump_fn()'":"";
	$sql_select = "select year,semester from school_class where enable='1' order by year,semester";
	$recordSet=$CONN->Execute($sql_select) or user_error($sql_select, 256);
	$other_year=array();
	$option="";
	while(list($year,$semester)=$recordSet->FetchRow()){
		$semester_name=($semester=='2')?"下":"上";
		$ys=$year."學年".$semester_name."學期";

		//製作其他學年學期的選單
		if(!in_array($ys,$other_year)){
			$other_year[$i]=$ys;
			$selected=($year==$sel_year and $semester==$sel_seme)?"selected":"";
			$option.="<option value='".$year."-".$semester."' $selected>$ys</option>";
			$i++;
		}
	}
	if(empty($option))trigger_error("查無任何學期資料", 256);
	
	$main="<select name='$name' $jump>
	$option
	</select>";
	return $main;
}

?>
