<?php
//$Id: list_chk_class.php 5671 2009-09-25 06:18:03Z infodaes $
include "config.php";
include "../../include/sfs_case_PLlib.php";

sfs_check();
?>
<script type="text/javascript" src="../stud_service/include/functions.js"></script>
<script type="text/javascript" src="../stud_service/include/JSCal2-1.9/src/js/jscal2.js"></script>
<script type="text/javascript" src="../stud_service/include/JSCal2-1.9/src/js/lang/b5.js"></script>
<link type="text/css" rel="stylesheet" href="../stud_service/include/JSCal2-1.9/src/css/jscal2.css">
<?php
$year_seme=$_GET['year_seme'];

//若有選擇學年學期，進行分割取得學年及學期
if(!empty($year_seme)){
	$ys=explode("-",$year_seme);
	$sel_year=$ys[0];
	$sel_seme=$ys[1];
}

if(empty($sel_year))$sel_year = curr_year(); //目前學年
if(empty($sel_seme))$sel_seme = curr_seme(); //目前學期

head();
$tool_bar=&make_menu($school_menu_p);
echo $tool_bar ;

//取得學年度
if (!isset($_GET[curr_seme]))
	$_GET[curr_seme] = sprintf("%03d%d",curr_year(),curr_seme());
$class_seme_p = get_class_seme(); //學年度	
$sel =new  drop_select();
$sel->id =$_GET[curr_seme];
$sel->s_name= "curr_seme";
$sel->arr = $class_seme_p;
$sel->is_submit = true;
$sel_seme_str = $sel->get_select();

$year = intval(substr($_GET[curr_seme],0,-1));
$semester = intval(substr($_GET[curr_seme],-1));

//取得班級名稱陣列
$class_base_arr = class_base($_GET[curr_seme]);

//取得科目名稱陣列
$subject_name_arr = &get_subject_name_arr();
$query = "select * from score_ss where  year='$year' and semester='$semester' ";
$res = $CONN->Execute($query);
$res_arr = array();
while (!$res->EOF) {
	$ss_id=$res->fields[ss_id];
	$scope_id=$res->fields[scope_id];
	$subject_id=$res->fields[subject_id];

	//取得領域名稱
	$scope_name=$subject_name_arr[$scope_id][subject_name];
	//取得學科名稱
	$subject_name=(!empty($subject_id))?$subject_name_arr[$subject_id][subject_name]:"";

	if($mode=="長"){
		$show_ss=(empty($subject_name))?$scope_name:$scope_name."-".$subject_name;
	}else{
		$show_ss=(empty($subject_name))?$scope_name:$subject_name;
	}
	$res_arr[$ss_id] = $show_ss;
	$res->MoveNext();
}

//取得教師陣列
$sql_select = "select name,teacher_sn from teacher_base ";
$res= $CONN->Execute($sql_select);
while(!$res->EOF){
	$teacher_arr[$res->fields[1]]=$res->fields[0];
	$res->MoveNext();
}


//取得所有兼課課表資料
$addition_array=array();
//course_id year semester class_id teacher_sn class_year class_name day sector ss_id room allow c_kind 
$query = "select * from score_course where year=$year and semester=$semester and c_kind=1 order by day,sector";
$res = $CONN->Execute($query) or trigger_error("SQL 錯誤",E_USER_ERROR);
while(!$res->EOF){
	//$year_seme = sprintf("%d%02d", $res->fields[class_year],$res->fields[class_name]);
	$teacher_sn=$res->fields['teacher_sn'];
	$day=$res->fields['day'];
	$sector=$res->fields['sector'];
	$class_id=sprintf("%d%02d",$res->fields['class_year'],$res->fields['class_name']);
	$ss_id=$res->fields['ss_id'];
	$addition_array[$teacher_sn]['detail'][$day][$sector]['class_id']=$class_id;
	$addition_array[$teacher_sn]['detail'][$day][$sector]['ss_id']=$ss_id;
	$addition_array[$teacher_sn]['counter']++;
	
	$res->MoveNext();
}

$start=$_POST['start']?$_POST['start']:date("Y-m-01");;
$end=$_POST['end']?$_POST['end']:date("Y-m-t");

//計算每日有幾天
$dow_array=array();
//開始時間
$s_date=explode("-",$start);
$sy=$s_date[0]; //年
$sm=$s_date[1]; //月
$sd=$s_date[2]; //日

//結束時間
$e_date=explode("-",$end);
$ey=$e_date[0]; //年
$em=$e_date[1]; //月
$ed=$e_date[2]; //日

//使用迴圈計算
$stime=mktime(0,0,0,$sm,$sd,$sy);
$etime=mktime(0,0,0,$em,$ed,$ey);
for($i=$stime;$i<=$etime;$i=$i+86400){
	$dat=date("m/d",$i);
	$dow=date("N",$i);
	$dow_array[$dow]['date'].=" <u>$dat</u>";
	$dow_array[$dow]['counter']++;
}
/*
echo "<pre>";
print_r($dow_array);
echo "</pre>";
*/


//抓取請假排代資料&代課資料
$query = "select * from teacher_absent_course where status='1' and class_dis='2' and deputy_date between '$start' and '$end'";
$res = $CONN->Execute($query) or trigger_error("SQL 錯誤",E_USER_ERROR);
//echo $query.'<br><pre>';
//print_r($res->getrows());
//echo '</pre>';
$minus=array();
$add=array();
while(!$res->EOF){
	$teacher_sn=$res->fields['teacher_sn'];
	$deputy_sn=$res->fields['deputy_sn'];
	$times=$res->fields['times'];
	$class_name=$res->fields['class_name'];
	$unit=$times_kind_arr[$res->fields['d_kind']];
	$my_date=substr($res->fields['deputy_date'],5,2).'/'.substr($res->fields['deputy_date'],8,2);

	$minus[$teacher_sn]['content'].="$my_date:$class_name*$times$unit({$teacher_arr[$deputy_sn]})<br>";
	$minus[$teacher_sn]['counter']+=$times;
	//$class_name='_'.$class_name;
	if(strpos($class_name,'★')!==false) $minus[$teacher_sn]['real']+=$times;
	
	$add[$deputy_sn]['content'].="$my_date:$class_name*$times$unit({$teacher_arr[$teacher_sn]})<br>";
	$add[$deputy_sn]['counter']+=$times;	
	
	$res->MoveNext();
}

//echo '<br><pre>';
//print_r($minus);
//echo '</pre>';
/*echo '<br><pre>';
print_r($add);
echo '</pre>';
*/

$addition_data="<table border='2' cellpadding='6' cellspacing='0' style='border-collapse: collapse; font-size:10pt;' bordercolor='#111111' id='AutoNumber1'>
		<tr bgcolor='#ccffcc' align='center'><td>NO.</td><td>教師姓名</td><td>節/週</td><td>統計區間兼課節次列表</td><td>(a)</td><td>公費排代未兼課節次</td><td>(b)</td><td>代課節次</td><td>(c)</td><td>合計(a-b+c)</td></tr>";
$i=0;
foreach($addition_array as $teacher_sn=>$data){
	$i++;
	$detail=$data['detail'];
	$additional='';
	$sum=0;
	foreach($detail as $day=>$sector_data){
		foreach($sector_data as $sector=>$value){
			$class_name=$class_base_arr[$value['class_id']];
			$course_name=$res_arr[$value['ss_id']];
			$additional.="[$day-$sector][$class_name][$course_name]:{$dow_array[$day]['date']}<br>";
			$sum+=$dow_array[$day]['counter'];
		}
		$addition_array[$teacher_sn]['list']=$additional;
	}
	$addition_array[$teacher_sn][sum]=$sum;
	$total=$sum-$minus[$teacher_sn][real]+$add[$teacher_sn][counter];
	$addition_data.="<tr align='center'><td>$i</td><td>{$teacher_arr[$teacher_sn]}</td><td>{$data['counter']}</td><td align='left'>$additional</td><td bgcolor='#ddffff'>$sum</td><td align='left'>{$minus[$teacher_sn][content]}</td><td bgcolor='#ddffff'>{$minus[$teacher_sn][real]}</td><td align='left'>{$add[$teacher_sn][content]}</td><td bgcolor='#ddffff'>{$add[$teacher_sn][counter]}</td><td bgcolor='#ffffdd'>$total</td></tr>";
}
$addition_data.="</table>";

echo "<form name='myform' action='$_SERVER[SCRIPT_NAME]' method='POST'>※學期：$sel_seme_str 　　※統計區間：
	<input type='text' name='start' value='$start' size=10 id='start'>
 		<script type=\"text/javascript\">
		new Calendar({
  		    inputField: \"start\",
   		    dateFormat: \"%Y-%m-%d\",
    	    trigger: \"start\",
    	    bottomBar: true,
    	    weekNumbers: false,
    	    showTime: 24,
    	    onSelect: function() {this.hide();}
		    });
		</script>
	~
	<input type='text' name='end' value='$end' size=10 id='end'>
		<script type=\"text/javascript\">
		new Calendar({
  		    inputField: \"end\",
   		    dateFormat: \"%Y-%m-%d\",
    	    trigger: \"end\",
    	    bottomBar: true,
    	    weekNumbers: false,
    	    showTime: 24,
    	    onSelect: function() {this.hide();}
		    });
		</script>
	<input type='submit' name='go' value='統計列示'>
	$addition_data<font color='bule' size=1>◎公費排代未兼課節次紀錄時須以「節」為單位，計算才會正確。<br>◎節次課程名稱需有★，才會被列入未兼課節次計算對象。</font></form>";

/*
echo "<pre>";
print_r($addition_array);
echo "</pre>";
*/
foot();
?>
