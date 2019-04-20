<?php

// $Id: chart_j.php 9240 2018-05-04 03:25:04Z igogo $

/* 取得設定檔 */
include "config.php";

sfs_check();

if (($IS_JHORES=='0')&&($use_both=='0')) header("location: chart_e.php");

$year_seme=($_POST[year_seme])?$_POST[year_seme]:$_GET[year_seme];
$class_id=($_POST[class_id])?$_POST[class_id]: $_GET[class_id];
$stud_id=($_POST[stud_id])?$_POST[stud_id]:$_GET[stud_id];
$student_sn=($_POST[student_sn])?$_POST[student_sn]:$_GET[student_sn];
$act=($_POST[act])?$_POST[act]:$_GET[act];
$stu_num=($_POST[stu_num])?$_POST[stu_num]:$_GET[stu_num];

//取得任教班級代號
$class_num=get_teach_class();
$class_all=class_num_2_all($class_num);
//努力程度
$oth_arr_score = array("表現優異"=>5,"表現良好"=>4,"表現尚可"=>3,"需再加油"=>2,"有待改進"=>1);
$oth_arr_score_2 = array(5=>"表現優異",4=>"表現良好",3=>"表現尚可",2=>"需再加油",1=>"有待改進");


if(empty($class_num)){
	$act="error";
	$error_title="無班級編號";
	$error_main="找不到您的班級編號，故您無法使用此功能。<ol>
	<li>請確認您兼任導師。
	<li>請確認教務處已經將您的任教資料輸入系統中。
	</ol>";
}elseif($error==1){
	$act="error";
	$error_title="該班級無學生資料";
	$error_main="找不到您的班級學生，故您無法使用此功能。<ol>
	<li>請確認您兼任導師。
	<li>請確認教務處已經將您的學生資料輸入系統中。
	<li>匯入學生資料：『學務系統首頁>教務>註冊組>匯入資料』(<a href='".$SFS_PATH_HTML."modules/create_data/mstudent2.php'>".$SFS_PATH_HTML."modules/create_data/mstudent2.php</a>)</ol>";
}

if(empty($sel_year))$sel_year = curr_year(); //目前學年
if(empty($sel_seme))$sel_seme = curr_seme(); //目前學期

//取得班級代號
$class_id=old_class_2_new_id($class_num,$sel_year,$sel_seme);
$seme_year_seme = sprintf("%03d%d",$sel_year,$sel_seme);

if ($class_num<>''){
	//取得本學期上課總日數
	$query = "select days from seme_course_date where seme_year_seme='$seme_year_seme' and class_year='".substr($class_num,0,1)."'";
	$res= $CONN->Execute($query) or die($query);
	$TOTAL_DAYS = $res->fields[0];
}

//取得考試樣板編號
$exam_setup=&get_all_setup("",$sel_year,$sel_seme,$class_all[year]);
$interface_sn=$exam_setup[interface_sn];
if ($chknext)	$ss_temp = "&chknext=$chknext&nav_next=$nav_next";

//執行動作判斷
if($act=="error"){
	$main=&error_tbl($error_title,$error_main);
}else{
	$main=&main_form($interface_sn,$sel_year,$sel_seme,$class_id,$student_sn);
}


//秀出網頁
head("製作成績單");

?>

<script language="JavaScript">
<!-- Begin
function jumpMenu(){
	location="<?php echo $_SERVER['SCRIPT_NAME']?>?act=<?php echo $act;?>&student_sn=" + document.col1.student_sn.options[document.col1.student_sn.selectedIndex].value;
}
//  End -->
</script>

<?php


echo $main;
foot();


//觀看模板
function &main_form($interface_sn="",$sel_year="",$sel_seme="",$class_id="",$student_sn=""){
	global $CONN,$input_kind,$school_menu_p,$cq,$comm,$chknext,$nav_next,$edit_mode,$submit,$chk_menu_arr;

	$year_seme=sprintf("%03s%1s",$sel_year,$sel_seme);
	$c=explode("_",$class_id);
	$seme_class=$c[2].$c[3];
	if (substr($seme_class,0,1)=="0") $seme_class=substr($seme_class,1,strlen($seme_class)-1);

	//轉換班級代碼
	$class=class_id_2_old($class_id);
	
	//假如沒有指定學生，取得第一位學生
	if(empty($student_sn)) {
		$sql="select student_sn from stud_seme where seme_year_seme='$year_seme' and seme_class='$seme_class' order by seme_num";
		$rs=$CONN->Execute($sql);
		$student_sn=$rs->fields['student_sn'];
	}

	//若仍是沒有$stud_id，則秀出錯誤訊息
	if(empty($student_sn))header("location:{$_SERVER['SCRIPT_NAME']}?error=1");
	
	if ($chknext && $nav_next<>'')	$student_sn = $nav_next;
	
	//求得學生ID
	$query="select stud_id from stud_base where student_sn='$student_sn'";
	$res=$CONN->Execute($query);
	$stud_id=$res->fields['stud_id'];

	//取得該學生日常生活表現評量值
	$oth_data=&get_oth_value($stud_id,$sel_year,$sel_seme);
	
	//取得學生日常生活表現分數及導師評語建議
	$nor_data=get_nor_value($student_sn,$sel_year,$sel_seme,"",($chk_menu_arr)?1:0);

	//取得學生缺席情況
	$abs_data=get_abs_value($stud_id,$sel_year,$sel_seme);
	
	//學生獎懲情況
	$reward_data = get_reward_value($stud_id,$sel_year,$sel_seme);	

	//取得學生成績檔
	$score_data = &get_score_value($stud_id,$student_sn,$class_id,$oth_data);

	//取得詳細資料
	$html=&html2code2($class,$sel_year,$sel_seme,$oth_data,$nor_data,$abs_data,$reward_data,$score_data,$student_sn,($chk_menu_arr)?1:0);
	
	$gridBgcolor="#DDDDDC";
	//已製作顯示顏色
	$over_color = "#223322";
	//左選單女生顯示顏色
	$non_color = "blue";

	$grid1 = new ado_grid_menu($_SERVER['SCRIPT_NAME'],$URI,$CONN);  //建立選單	   	
	$grid1->key_item = "student_sn";  // 索引欄名  	
	$grid1->display_item = array("sit_num","stud_name");  // 顯示欄名   
	$grid1->bgcolor = $gridBgcolor;
	$grid1->display_color = array("1"=>"blue","2"=>"red");
	$grid1->color_index_item ="stud_sex" ; //顏色判斷值
	$grid1->class_ccs = " class=leftmenu";  // 顏色顯示
	$grid1->sql_str = "select student_sn,stud_name,stud_sex,substring(curr_class_num,4,2)as sit_num  from stud_base where curr_class_num like '$class[2]%' and stud_study_cond=0 order by curr_class_num";   //SQL 命令
	$grid1->do_query(); //執行命令 

	$stud_select = $grid1->get_grid_str($stud_id,$upstr,$downstr); // 顯示畫面

	//取得指定學生資料
	$stu=get_stud_base($student_sn,"");

	//座號
	$stu_class_num=curr_class_num2_data($stu['curr_class_num']);

	//取得學校資料
	$s=get_school_base();
	$tool_bar=&make_menu($school_menu_p);
	$checked=($chknext)?"checked":"";

	$main="
	$tool_bar
	<table bgcolor='#DFDFDF' cellspacing=1 cellpadding=4>
	<tr class='small'><td valign='top'>$stud_select
	</td><td bgcolor='#FFFFFF' valign='top'>
	<p align='center'>
	<font size=3>".$s[sch_cname]." ".$sel_year."學年度第".$sel_seme."學期成績單</p>
	<table align=center cellspacing=4>
	<tr>
	<td>班級：<font color='blue'>$class[5]</font></td><td width=40></td>
	<td>座號：<font color='green'>$stu_class_num[num]</font></td><td width=40></td>
	<td>姓名：<font color='red'>$stu[stud_name]</font></td>
	</tr></table></font>
	$html
	</td></tr></table>
	";

	return $main;
}
?>
