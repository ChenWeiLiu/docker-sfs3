<?php
//檢查本教師是否有指定班級
function get_course_class_select2($year_seme,$curr_class_id=""){

	global $CONN,$school_kind_name,$school_kind_color,$this_seme_year_seme;
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);
  $query="select class_id from `score_eduh_teacher2` where year_seme='$year_seme' and teacher_sn='".$_SESSION['session_tea_sn']."' order by class_id";
  $res_class=mysql_query($query);
  //取出設定的課程
    $res_class=mysql_query($query);
    while ($row_class=mysql_fetch_row($res_class)) {
     list($class_id)=$row_class;
      $query="select c_year,c_name from school_class where class_id='$class_id'";
      $res_class_name=mysql_query($query);
      list($c_year,$c_name)=mysql_fetch_row($res_class_name);
      $selected=($curr_class_id==$class_id)?"selected":"";
     $class_name_option.="<option value='$class_id' $selected style='background-color: $school_kind_color[$c_year];'>".$school_kind_name[$c_year]."".$c_name."班</option>\n";
    } // end while
	return $class_name_option;
} 

//依科目製作<select><option>下拉選單
function get_course_class_select($sel_year="",$sel_seme="",$col_name="class_id",$jump_fn="",$curr_class_id=""){

	global $CONN,$school_kind_name,$school_kind_color,$this_seme_year_seme;
  $option1="請選擇班級";
  
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);
	
	$option_teacher=get_course_class_select2($this_seme_year_seme,$curr_class_id); //先檢查有沒有單獨特別指定給本教師的班級
	
  $query="select ss_id from `score_eduh_teacher` where year_seme='$this_seme_year_seme'";
  //echo $query."<br>";
  $res_course=mysql_query($query);
  //取出設定的課程
  while ($row_course=mysql_fetch_array($res_course)) {
  	//取出課表中有此課程的班級
    $query="select distinct class_id from score_course where year='".substr($this_seme_year_seme,0,3)."' and semester='".substr($this_seme_year_seme,-1)."' and ss_id='".$row_course['ss_id']."' and teacher_sn='".$_SESSION['session_tea_sn']."'";
    //echo $query."<br>";
    $res_class=mysql_query($query);
    while ($row_class=mysql_fetch_row($res_class)) {
     list($class_id)=$row_class;
     //檢查特別指定table中, 此師有無指定此班, 若有, 不要再列出
     // if .... continue
      if (check_class_id($this_seme_year_seme,$_SESSION['session_tea_sn'],$class_id)) continue; //在指定教師資料表裡已指定此班級, 不要重覆列出
      $query="select c_year,c_name from school_class where class_id='$class_id'";
      $res_class_name=mysql_query($query);
      list($c_year,$c_name)=mysql_fetch_row($res_class_name);
      $selected=($curr_class_id==$class_id)?"selected":"";
     $class_name_option.="<option value='$class_id' $selected style='background-color: $school_kind_color[$c_year];'>".$school_kind_name[$c_year]."".$c_name."班</option>\n";
    } // end while
  }// end while
 
  //if(empty($class_name_option))trigger_error("查無班級資料", E_USER_ERROR);

	$jump=(!empty($jump_fn))?" onChange='$jump_fn()'":"";

	$class_name_list="
	<select name='$col_name' $jump>
	<option value=''>$option1
	$option_teacher
	$class_name_option
	</select>";
	return $class_name_list;
} 

//檢驗某學期某教師是否已指定某班
function check_class_id($year_seme,$teacher_sn,$class_id) {
	$query="select * from `score_eduh_teacher2` where year_seme='$year_seme' and teacher_sn='$teacher_sn' and class_id='$class_id'";
	$result=mysql_query($query);
	if (mysql_num_rows($result)>0) {
		return true;
	} else {
	  return false;
	}	
}

?>