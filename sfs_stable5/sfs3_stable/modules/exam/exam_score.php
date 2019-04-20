<?php                                                                                                                             
// $Id: exam_score.php 8743 2016-01-08 14:02:58Z qfon $

//載入設定檔
include "exam_config.php";
session_start();

if(!checkid(substr($_SERVER[PHP_SELF],1))){
	$go_back=1; //回到自已的認證畫面  
	include "header.php";
	include "$rlogin";
	include "footer.php"; 
	exit;
}
	
//判別是否為系統管理者
//$man_flag = check_is_man();

//取得年級班級
if (isset($_SESSION[session_curr_class_num]))
	$curr_class = substr($session_curr_class_num,1,2 );
if ($curr_class_id == ""){
	//目前學年
	$curr_year = sprintf("%03s",curr_year());
	//目前學期
	$curr_seme = curr_seme();
	$curr_class_id =$curr_year.$curr_seme;
}
//更改班級
if ($_SESSION[session_e_kind_id] == $_POST[c_e_kind_id] or  $_POST[c_e_kind_id]=='')
	$e_kind_id = $_SESSION[session_e_kind_id];
else {
	$e_kind_id = $_POST[c_e_kind_id];
	$_SESSION[session_e_kind_id] = $_POST[c_e_kind_id];
}

//取得班級名稱陣列
$class_name = class_base();
	
//目前有作業的班級
$e_kind_id=intval($e_kind_id);
$sql_select  = "select exam.exam_id ,exam.exam_name from exam,exam_kind ";
$sql_select .= " where exam.e_kind_id=exam_kind.e_kind_id ";
$sql_select .= " and exam_kind.class_id like '$curr_class_id%' ";
$sql_select .= " and exam.e_kind_id='$e_kind_id' ";
$sql_select .= " and exam.teach_id ='$_SESSION[session_log_id]' ";
$sql_select .= " order by exam.exam_id  ";

$result = $CONN->Execute ($sql_select) or die ($sql_select);
while (!$result->EOF) {
	$exam_array[0][]= $result->fields[0];
	$exam_array[1][]= $result->fields[1];
	$result->MoveNext();
}

if ($_POST[print_key] == "轉成Excel檔") {
	$filename = "exam.xls"; 
	header("Content-disposition: filename=$filename");
	header("Content-type: application/octetstream");
	//header("Pragma: no-cache");
					//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");

	header("Expires: 0");
	echo '<html>
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=big5">
	<title>學生作業展示</title>
	</head>

	<body  >\n';
}
else
{
	//目前有作業的班級
	$sql_select = "select exam_kind.class_id,exam_kind.e_kind_id  from exam,exam_kind ";
	$sql_select .=" where exam.e_kind_id=exam_kind.e_kind_id and exam.teach_id ='$_SESSION[session_log_id]' and exam_kind.class_id like '$curr_year_seme%' group by exam_kind.class_id order by exam_kind.class_id  ";
	//echo $sql_select ;
	$result = $CONN->Execute($sql_select) or trigger_error("SQL 錯誤",E_USER_ERROR);

	$class_select_arr[-1]="所有班級";
	while(!$result->EOF){
		$temp_class = substr($result->fields[class_id],-3);
		$class_select_arr[$result->fields[e_kind_id]] = substr($result->fields[class_id],0,4) .'--' . $class_name[$temp_class];
		$result->MoveNext();
	}
	$sel = new drop_select();
	$sel->s_name="c_e_kind_id";
	$sel->id=$e_kind_id;
	$sel->has_empty = false;
	$sel->is_submit = true;
	$sel->arr= $class_select_arr;
	$class_select= $sel->get_select();

	
	include "header.php";
	echo "<h3>$exam_title</h3>\n";
	echo "<center><form name=myform action=\"$_SERVER[PHP_SELF]\" method=post >"; //班級選項
	echo "歡迎 $_SESSION[session_tea_name] 登入 ";
	echo "&nbsp;｜&nbsp; $class_select \n";	
	if ($e_kind_id !="")
		echo "&nbsp;｜&nbsp;<input type=submit name=\"print_key\" value=\"轉成Excel檔\">";
	echo "&nbsp;｜&nbsp; <a href=\"exam_list.php\">回作業區</a>";			
	echo "&nbsp;｜&nbsp; <a href=\"checkid.php?logout=1&exename=$_SERVER[PHP_SELF]\">登出系統</a>";	
	
	echo "</form></center>";
}
echo "<center>";
echo "<table  border=1 >";
echo "<tr><td>座號</td><td>姓名</td>\n";

for($i=0 ; $i< count($exam_array[0]); $i++)
	echo "<td>第 ".($i+1)." 次</td>";//<BR>".$exam_array[1][$i]."</td>";

echo "</tr>\n";

//取得學生姓名
$e_kind_id=intval($e_kind_id);
$sql_select = "select exam_stud.stud_num,exam_stud.stud_name from exam_kind,exam,exam_stud where exam.e_kind_id = exam_kind.e_kind_id and exam.exam_id=exam_stud.exam_id and exam.e_kind_id='$e_kind_id' and exam_stud.stud_id not like 'demo%'  group by exam_stud.stud_num order by exam_stud.stud_num ";
$result = $CONN->Execute($sql_select); //學生
//echo $sql_select ;

while (!$result->EOF) {	
	$score_stud[$result->fields[0]] = $result->fields[1];	
	$result->MoveNext();
}

//取得各次的成績
for($i=0 ; $i< count($exam_array[0]); $i++) {
	$sql_select = "select exam_stud.stud_num,exam_stud.tea_grade from exam LEFT JOIN exam_stud on exam_stud.exam_id = exam.exam_id where  exam.exam_id = '".$exam_array[0][$i]."' and exam_stud.stud_id not like 'demo%' order by exam_stud.stud_num  ";

	$result = $CONN->Execute ($sql_select) or die ($sql_select);
	while (!$result->EOF) {
		$stud_num = $result->fields["stud_num"];
		$score_grade[$i][$stud_num] = $result->fields["tea_grade"]; //成績
		$result->MoveNext();
	}
}


foreach ( $score_stud as $sit_no => $stud_name )   {
		echo "<tr><td>".$sit_no."</td><td>".$stud_name."</td>";
		for($j=0 ; $j< count($exam_array[0]); $j++) {
			echo "<td>".$score_grade[$j][$sit_no]."</td>";
		}
		echo "</tr>";

		
}
?>
</table>

<?php include "footer.php"; ?>
