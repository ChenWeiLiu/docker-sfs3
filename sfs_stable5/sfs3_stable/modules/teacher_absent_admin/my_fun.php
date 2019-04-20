<?php
// $Id: my_fun.php 5310 2009-01-10 07:57:56Z hami $

function year_seme_menu($sel_year,$sel_seme) {
	global $CONN;

	$sql="select year,semester from school_class where enable='1' order by year,semester";
	$rs=$CONN->Execute($sql);
	while (!$rs->EOF) {
		$year=$rs->fields["year"];
		$semester=$rs->fields["semester"];
		if ($year!=$oy || $semester!=$os)
			$show_year_seme[$year."_".$semester]=$year."學年度第".$semester."學期";
		$oy=$year;
		$os=$semester;
		$rs->MoveNext();
	}
	//增加下學期的選單項目
	if($semester==2) { $year++; $semester=1; } else $semester=2;  
	$show_year_seme[$year."_".$semester]=$year."學年度第".$semester."學期";	
	
	$scys = new drop_select();
	$scys->s_name ="year_seme";
	$scys->top_option = "選擇學期";
	$scys->id = $sel_year."_".$sel_seme;
	$scys->arr = $show_year_seme;
	$scys->is_submit = true;
	return $scys->get_select();
}


function month_menu($month,$arr) {
	$mon = new drop_select();
	$mon->s_name ="month";
	$mon->top_option = "選擇月份";
	$mon->id = $month;
	$mon->arr = $arr;
	$mon->is_submit = true;
	return $mon->get_select();
}



function class_menu($sel_year,$sel_seme,$c_year,&$class_id) {
	global $CONN,$class_year;
	
    $c_year=intval($c_year);
	$sel_year=intval($sel_year);
	$sel_seme=intval($sel_seme);
	$class_str=($c_year)?"and c_year='$c_year'":"";
	$sql="select class_id,c_year,c_name from school_class where enable='1' and year='$sel_year' and semester='$sel_seme' $class_str order by class_id";
	$rs=$CONN->Execute($sql);
	while (!$rs->EOF) {
		$class[$rs->fields[class_id]]=$class_year[$rs->fields[c_year]].$rs->fields[c_name]."班";
		$rs->MoveNext();
	}
	$class_keys=array_keys($class);
	if (!in_array($class_id,$class_keys)) $class_id="";
	$sc = new drop_select();
	$sc->s_name ="class_id";
	$sc->top_option = "選擇班級";
	$sc->id = $class_id;
	$sc->arr = $class;
	$sc->is_submit = true;
	return $sc->get_select();
}

function teacher_menu($s_name,$teacher_sn,$agent_sn,$is_submit=True) {
	$tm = new drop_select();
	$tm->s_name =$s_name;
	$tm->top_option = "選擇教師";
	$tm->id = $teacher_sn;
	$tm->arr = my_teacher_array($agent_sn);
	$tm->is_submit = $is_submit;
	return $tm->get_select();
}
function tea_abs($abs_kind,$arr) {
	$tam = new drop_select();
	$tam->s_name ="abs_kind";
	$tam->top_option = "選擇假別";
	$tam->id = $abs_kind;
	$tam->arr = $arr;
	$tam->is_submit = true;
	return $tam->get_select();
}

function status_menu($status) {
	global $status_kind;
	$tam = new drop_select();
	$tam->s_name ="status";
	$tam->top_option = "選擇狀態";
	$tam->id = $status;
	$tam->arr = $status_kind;
	$tam->is_submit = true;
	return $tam->get_select();
}

function course_menu($class_dis) {
	global $course_kind;
	$tam = new drop_select();
	$tam->s_name ="class_dis";
	$tam->top_option = "選擇課程處理";
	$tam->id = $class_dis;
	$tam->arr = $course_kind;
	$tam->is_submit = true;
	return $tam->get_select();
}

//教師名字陣列依姓名排列
function my_teacher_array($agent_sn){
	global $CONN,$sort_style;
	$sort_stylex=$sort_style==1?"a.name":"d.rank";
	$agent_sn=intval($agent_sn);
	$not_in=(empty($agent_sn))?"":"and a.teacher_sn <> '$agent_sn'";
	$query="select a.teacher_sn,a.name,d.title_name from teacher_base a,teacher_post c, teacher_title d WHERE 
	a.teach_condition=0  AND c.teacher_sn=a.teacher_sn AND c.teach_title_id=d.teach_title_id  order by $sort_stylex";

	$res=$CONN->Execute($query) or die($query);
	$temp_arr = array();
	while(!$res->EOF){
		$temp_arr[$res->fields['teacher_sn']] = $res->fields['title_name'].'--'.$res->fields['name'];
		$res->MoveNext();
	}
	return $temp_arr;
}

//教師名字陣列依姓名排列
function my_teacher_array2($agent_sn){
	global $CONN,$sort_style;
	$sort_stylex=$sort_style==1?"a.name":"d.rank";
	$agent_sn=intval($agent_sn);
	$not_in=(empty($agent_sn))?"":"and a.teacher_sn <> '$agent_sn'";
	$query="select a.teacher_sn,a.name,d.title_name from teacher_base a,teacher_post c, teacher_title d WHERE 
	c.teacher_sn=a.teacher_sn AND c.teach_title_id=d.teach_title_id  order by $sort_stylex";

	$res=$CONN->Execute($query) or die($query);
	$temp_arr = array();
	while(!$res->EOF){
		$temp_arr[$res->fields['teacher_sn']] = $res->fields['title_name'].'--'.$res->fields['name'];
		$res->MoveNext();
	}
	return $temp_arr;
}
//取得教師職稱
function teacher_post_k($teacher_sn){
	global $CONN;
	$teacher_sn=intval($teacher_sn);
	$query="select * from teacher_post where teacher_sn=$teacher_sn ";
		$result = mysql_query($query) or die ($query);
		$row = mysql_fetch_array($result);
		$post_k=$row["post_kind"];
	return $post_k;
}

function d_make_menu($top,$d_kind,$arr,$s_name,$true) {
//抬頭,預設,陣列,名稱,送出

	$mon = new drop_select();
	$mon->s_name =$s_name;
	$mon->top_option = $top;
	$mon->id = $d_kind;
	$mon->arr = $arr;
	$mon->is_submit = $true;
	return $mon->get_select();
}
//取得星期幾
function d_week($date) {
	$week_array=array("0"=>"(日)","1"=>"(一)","2"=>"(二)","3"=>"(三)","4"=>"(四)","5"=>"(五)","6"=>"(六)");

		$month=substr($date,5,2);
		$day=substr($date,8,2);
		$year=substr($date,0,4);
		$w=date ("w", mktime(0,0,0,$month,$day,$year));
		$nw=$week_array[$w];	
	return $nw;
	
}
//0改為空白
function ztos($i) {
	if($i==0) $i="";
	return $i;	
}





?>
