<?php

// $Id: index.php 5310 2009-01-10 07:57:56Z hami $

/*引入學務系統設定檔*/
include "../../include/config.php";
//引入函式庫
include "../../include/sfs_case_PLlib.php";
//引入函數
include "./my_fun.php";
//使用者認證
sfs_check();

//學年學期
$year_seme=$_GET['year_seme'];
//年級
$year_name=$_GET['year_name'];


//判斷是否為管理者
$is_man = checkid($_SERVER['SCRIPT_FILENAME'],1);

//if (!$is_man) {
//    header("Location: error.php");
//}

//是否每一次月考要配合一次平時成績
$rs_yorn=$CONN->Execute("SELECT pm_value FROM pro_module WHERE pm_name='score_input' AND pm_item='yorn'");
$yorn=$rs_yorn->fields['pm_value'];

//程式檔頭
head("成績管理");
//列出橫向的連結選單模組
$menu_p = array("index.php"=>"成績管理", "score_error.php"=>"成績檢查");
print_menu($menu_p);
//整體變數處理
$class_id = $_GET[class_id];
if (empty($class_id))
	$class_id = $_POST[class_id];
if($class_id == $_POST[old_class_id] or $_GET[is_open]==1) {
	$stage = $_GET[stage];
	if (empty($stage))
		$stage = $_POST[stage];
}
else{
	$stage = '';
}


echo "<form name='myform' method='post' action='{$_SERVER['PHP_SELF']}'>\n";
//設定主網頁顯示區的背景顏色
echo "<table border=0 cellspacing=1 cellpadding=2 width=100% bgcolor=#cccccc><tr><td bgcolor=#FFFFFF>";

//網頁內容請置於此處
/***********************************************************************************/
$year_seme = ($_GET[year_seme])?"$_GET[year_seme]":"$_POST[year_seme]";
if($year_seme == '')
	$year_seme = sprintf("%03d%d",curr_year(),curr_seme());
$class_seme_arr = get_class_seme();
$sel1 = new drop_select();
$sel1->s_name="year_seme";
$sel1->id= $year_seme;
$sel1->arr= $class_seme_arr;
$sel1->top_option="選擇學年度";
$sel1->is_submit = true;
$year_seme_menu = $sel1->get_select();

$sel_year=substr($year_seme,0,-1);
$sel_seme=substr($year_seme,-1);
$score_semester="score_semester_".intval($sel_year)."_".intval($sel_seme);
$score_semester="score_semester_".intval($sel_year)."_".intval($sel_seme);
$this_year_seme = sprintf("%03d_%d_",$sel_year,$sel_seme);

//目前的班級資料
$curr_class_base = class_base($year_seme);

$query = "select class_id from $score_semester where class_id like '$this_year_seme%' group by class_id order by class_id ";

$res = $CONN->Execute($query) or trigger_error("$sel_year 學年第 $sel_seme 學期成績未建立" ,E_USER_ERROR);
//如果未設班級時，以第一班為初始
if (empty($class_id)){
	$class_id = $res->fields[class_id];
	$temp_arr = explode("_",$res->fields[class_id]);
	$class_id=sprintf("%d%02d", $temp_arr[2],$temp_arr[3]);
}

//將成績已傳至教務處的班級放入陣列中
while(!$res->EOF) {
	$temp_arr = explode("_",$res->fields[class_id]);
	$tid =sprintf("%d%02d", $temp_arr[2],$temp_arr[3]);
	$temp_name = $curr_class_base[$tid];
	$class_in_arr[$tid] = $temp_name;
	$res->MoveNext();
}

//產生班級的選單表
$sel1 = new drop_select();
$sel1->s_name='class_id';
$sel1->id=$class_id;
$sel1->has_empty= false;
$sel1->arr = $class_in_arr;
$sel1->is_submit =true;
if (count($class_in_arr)<=10)
	$sel1->size= count($class_in_arr);
else 
	$sel1->size =10;

$class_menu = $sel1->get_select();
//作為判斷是否改變班級用
$class_menu .= "<input type=hidden name=old_class_id value=\"$class_id\">";

//選擇階段 
$temp_class_id = sprintf("%03d_%d_%02d_%02d",$sel_year,$sel_seme,substr($class_id,0,1),substr($class_id,1));

if ($yorn=='y')
	$query = "select test_sort from $score_semester where class_id='$temp_class_id' and test_sort<>254 group by test_sort";
else
	$query = "select test_sort from $score_semester where class_id='$temp_class_id' group by test_sort";
//echo $query; 
$res = $CONN->Execute($query) or trigger_error("SQL 錯誤",E_USER_ERROR);

if (empty($stage))
	$stage = $res->fields[0];
while(!$res->EOF){
	if ($res->fields[0]==254)
		$stage_arr[$res->fields[0]] = "平時成績";
	else if ($res->fields[0]==255)
		$stage_arr[$res->fields[0]] = "不分階段";
	else
		$stage_arr[$res->fields[0]] = "第".$res->fields[0]."階段";	
	$res->MoveNext();
}
//顯示階段 下拉選單
$sel1 = new drop_select();
$sel1->s_name = "stage";
$sel1->id = $stage;
$sel1->arr = $stage_arr;
$sel1->is_submit = true;
$sel1->has_empty = false;
$stage_menu = $sel1->get_select();

$menu="
 <table cellpadding='5' cellspacing='1' border='0' bgcolor='#0000ff' align='left'>
    <tbody bgcolor='#FFFFFFFF'>
        <tr >
            <td>$year_seme_menu</td>
	</tr>
	<tr>
	   <td>$class_menu</td>
        </tr>
   </tbody>
    </table>
<table align=left><tr><td width=10>&nbsp;&nbsp;</td></tr></table>
";

echo $menu;
//以上為選單bar
/******************************************************************************************/


settype($year_name,integer);
settype($me,integer);

$hello="<font color=red> <B>".$sel_year."</b> </font>學年度第 <font color=red><B>".$sel_seme."</b></font> 學期 ";
$hello.="<font color=red><b>".$class_in_arr[$class_id]."</b></font>&nbsp;";

//if($stage==254) $hello.="平時階段";
//elseif($stage==255) $hello.="不分階段";
$hello.=$stage_menu;
$hello.=" 的成績<br><br>";

$score_semester="score_semester_".intval($sel_year)."_".intval($sel_seme);
$score_semester="score_semester_".intval($sel_year)."_".intval($sel_seme);


$sql="select a.ss_id,b.print from $score_semester a, score_ss b where a.ss_id=b.ss_id and b.enable=1 and  a.class_id='$temp_class_id' and test_sort='$stage' group by a.ss_id";
$rs=$CONN->Execute($sql);
$i=0;
while (!$rs->EOF) {
	$ss_id[$i++]=$rs->fields["ss_id"];
	$print[$i]=$rs->fields["print"];
	$rs->MoveNext();
}

for($i=0;$i<count($ss_id);$i++){
	$sql2="select count(*) from $score_semester where class_id='$temp_class_id' and test_sort='$stage' and ss_id='$ss_id[$i]' and sendmit='0'";
	$rs2=$CONN->Execute($sql2);
	$k = $rs2->fields[0];		
	if($k>0)
		$send="<img src='images/yes.png'>";
	else
		$send="<img src='images/no.png'>";
	
	if($yorn=="n" && $stage=="254")
		 $sql3="select sum(sendmit  in (0)) as ss,count(*) as cc from $score_semester where class_id='$temp_class_id' and ss_id='$ss_id[$i]' and test_kind='平時成績'";
	elseif($yorn=="n" && $stage!="254") {
		$temp_kind=($stage==255)?"全學期":"定期評量"; 
		$sql3="select sum(sendmit  in (0)) as ss,count(*) as cc from $score_semester where class_id='$temp_class_id' and test_sort='$stage' and ss_id='$ss_id[$i]' and test_kind='$temp_kind'";
	}
	else
		$sql3="select sum(sendmit  in (0)) as ss,count(*) as cc from $score_semester where class_id='$temp_class_id' and test_sort='$stage' and ss_id='$ss_id[$i]'";
	
	$rs3=$CONN->Execute($sql3) or die($sql3);
	//echo $sql3."  $yorn ==n && $stage==254 <BR>";		
	//總數
	$sendmit_tol = $rs3->fields[cc];
	//已上傳數
	$sendmit_num= $rs3->fields[ss];

	//已上傳鎖定
	if ($sendmit_tol>0 && $sendmit_tol == $sendmit_num) {
		$mit="<img src='images/lock.png'>";
		$open="<a href='./openlock.php?score_semester=$score_semester&score_semester=$score_semester&class_id=$temp_class_id&test_sort=$stage&ss_id=$ss_id[$i]&year_seme=$year_seme&year_name=$year_name&me=$me&stage=$stage&temp_class=$class_id'><img src='images/key.png' border='0'></a>";
	}
	//已有資料尚未上傳
	else{
		$mit="<img src='images/unlock.png'>";
		$open ="<a href='./closelock.php?score_semester=$score_semester&score_semester=$score_semester&class_id=$temp_class_id&test_sort=$stage&ss_id=$ss_id[$i]&year_seme=$year_seme&year_name=$year_name&me=$me&stage=$stage&temp_class=$class_id'><img src='images/door.png' border='0'></a>";
	}
	$delete="<a href='./delete.php?score_semester=$score_semester&score_semester=$score_semester&class_id=$temp_class_id&test_sort=$stage&ss_id=$ss_id[$i]&year_seme=$year_seme&year_name=$year_name&me=$me&stage=$stage&temp_class=$class_id'><img src='images/cancel.png' border='0'></a>";
	//取出科目名稱
	$subject_name=ss_id_to_subject_name($ss_id[$i]);
	$content.="<tr bgcolor='#FFFFFF'><td>$subject_name</td><td align=center>$send</td><td align=center>$mit</td><td align=center>$open</td><td align=center>$delete</td></tr>";
}
    $main="
    <table cellpadding='5' cellspacing='1' border='0' width='440' bgcolor='#0000ff' align='left'>
    <caption>$hello</caption>
    <tbody>
        <tr bgcolor='#B8BEF6'>
        <td width='110' align=center>科目名稱</td>
        <td width='110' align=center>傳送情形</td>
        <td width='110' align=center>鎖定狀況</td>
        <td width='110' align=center>開鎖</td>
		<td width='110' align=center>刪除</td>
        </tr>
        $content
    </tbody>
    </table>";
    $description="
    <table cellpadding='5' cellspacing='0' border='0' width='440' align='left'>
    <tbody>
        <tr><td><img src='images/yes.png'>學生成績已經傳送到教務處</td></tr>
        <tr><td><img src='images/no.png'>學生成績還未傳送到教務處</td></tr>
        <tr><td><img src='images/oh.png'>傳送到教務處的成績不完整需打開鎖定重傳</td></tr>
        <tr><td><img src='images/lock.png'>該科目成績以被鎖定，老師無法上傳</td></tr>
        <tr><td><img src='images/unlock.png'>該科目未被鎖定，老師可以上傳</td></tr>
        <tr><td><img src='images/key.png'>按鑰匙打開鎖定，讓老師能重新上傳成績</td></tr>
		<tr><td><img src='images/door.png'>按救生圈將資料表鎖定，讓老師無法上傳成績</td></tr>
    </tbody>
    </table>";
    echo "<table><tr><td>";
    echo $main;
    echo "</td></tr><tr><td>";
    echo $description;
    echo "</td></tr></table>";

//}
//結束主網頁顯示區
echo "</td>";
echo "</tr>";
echo "</table>";
echo "</form>";
//程式檔尾
foot();
?>
