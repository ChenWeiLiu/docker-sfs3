<?php

// $Id:  $

// 載入設定檔
include "stud_move_config.php";
include "../../include/sfs_case_dataarray.php";


// 認證檢查
sfs_check();
//學籍狀態
$study_cond_array=study_cond();
unset($study_cond_array[0]);
unset($study_cond_array[1]);

/*
$m_arr = get_sfs_module_set();
extract($m_arr, EXTR_OVERWRITE);
*/

// 不需要 register_globals
if (!ini_get('register_globals')) {
	ini_set("magic_quotes_runtime", 0);
	extract( $_POST );
	extract( $_GET );
	extract( $_SERVER );
}

/*	$sel_year = curr_year(); //選擇學年
	$sel_seme = curr_seme(); //選擇學期
	$curr_seme = curr_year().curr_seme(); //現在學年學期
*/	
$today = date("Y-m-d") ;

//未指定日期，取得前一年
if (!$beg_date) {
	 $beg_date =GetMonthAdd( $today ,-12) ;
	 list($ty,$tm,$td) = split('[/-]' , $beg_date) ;
	 $beg_date= "$ty-$tm-01" ;
}	
if (!$end_date) {
	 $end_date = $today  ;
}	

if($_POST['move_kind'])
{
	$kind_list=implode(',',$_POST['move_kind']);

	//取得資料---------------------------------------------------------------------------
	$class_list_p = class_base($curr_seme);
	$query = "select a.*,b.stud_name,b.stud_birthday,b.curr_class_num from stud_move a inner join stud_base b on a.student_sn=b.student_sn where a.move_kind IN ($kind_list) and (a.move_date between '$beg_date' and '$end_date') order by a.move_date";
	$result = $CONN->Execute($query) or die ($query);
	while(!$result->EOF) {
		$move_id = $result->fields["move_id"];
		$move_kind=$result->fields["move_kind"];
		$arr[$move_id][move_kind] = $study_cond_array[$move_kind];
		//$arr[$move_id][stud_name] = "<a href='../stud_search/stu_list.php?student_sn=$student_sn' target='_$student_sn'>{$result->fields[stud_name]}</a>";
		$arr[$move_id][stud_name] = $result->fields[stud_name];
		$arr[$move_id][stud_birthday] = $result->fields[stud_birthday];
		$arr[$move_id][move_date] = $result->fields["move_date"];
		$arr[$move_id][stud_id] = $result->fields["stud_id"];

		$arr[$move_id][school] = $result->fields["school"];
		$arr[$move_id][reason] = $result->fields["reason"];
		$arr[$move_id][school_id] = $result->fields["school_id"];
		$arr[$move_id][move_year] = substr($result->fields["move_year_seme"],0,-1);
		$arr[$move_id][move_semester] = substr($result->fields["move_year_seme"],-1);
		//調校時的班級
		//$arr[$move_id][curr_class_num] = $result->fields["curr_class_num"];
		$student_sn=$result->fields['student_sn'];
		$sql = "select seme_class,seme_num from stud_seme where student_sn='$student_sn' and seme_year_seme='{$result->fields["move_year_seme"]}'";
		$rs = $CONN->Execute($sql);
		$arr[$move_id][curr_class_num]=sprintf("%3d%02d",$rs->fields['seme_class'],$rs->fields['seme_num']);
		$result->MoveNext();
	}
	
	
	if($_POST[go]=='HTML輸出'){
		//抓取資料製成表格
		$main="<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111' id='AutoNumber1' width='100%'>
				<tr align='center' bgcolor='#ffffcc'><td>NO.</td><td>學期</td><td>類別</td><td>學號</td><td>年級</td><td>姓名</td><td>出生年月日</td><td>異動日期</td><td>異動原因</td><td>轉出/轉入學校</td></tr>";
		foreach($arr as $move_id=>$data){
			$grade=substr($data[curr_class_num],0,-4);
			$i++;
			$main.="<tr align='center'><td>$i</td><td>{$data[move_year]}-{$data[move_semester]}</td><td>{$data[move_kind]}</td><td>{$data[stud_id]}</td><td>$grade</td><td>{$data[stud_name]}</td><td>{$data[stud_birthday]}</td><td>{$data[move_date]}</td><td>{$data[reason]}</td><td>{$data[school_id]}{$data[school]}</td></tr>";
		}
		$main.="</table>";		
		foreach($_POST['move_kind'] as $key) $kind_name_list.="[{$study_cond_array[$key]}]";
		$date_list=sprintf("%d年%02d月%02d日",date("Y")-1911,date("m"),date("d"));
		$main="<center><font size=5>{$school_long_name}異動記錄列表</font><br>◎異動類別：$kind_name_list 　　　◎日期區間：$beg_date~$end_date<br>◎填報日期：$date_list<br>$main<br>業務承辦：　　　　　　註冊組長：　　　　　　教務主任：　　　　　　校長：　　　　　　</center>";

		echo $main;
		exit;
	}	
}		


//---------------------------------------------------------------------------
head("異動記錄列表");
print_menu($student_menu_p);
//echo $beg_date ;

$template_dir = $SFS_PATH."/".get_store_path()."/templates";
// 使用 smarty tag
$smarty->left_delimiter="{{";
$smarty->right_delimiter="}}";
//$smarty->debugging = true;


$smarty->assign("beg_date",$beg_date);
$smarty->assign("end_date",$end_date);
$smarty->assign("arr",$arr);
$smarty->assign("move_kind",$_POST['move_kind']);
$smarty->assign("study_cond_array",$study_cond_array); //調動類別
//$smarty->assign("move_kind",$study_cond_array[$_POST[move_kind]]);

$smarty->assign("template_dir",$template_dir);

$smarty->display("$template_dir/stud_move_list2.htm");

foot();

?>