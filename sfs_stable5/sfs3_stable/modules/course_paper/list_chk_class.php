<?php

//$Id: list_chk_class.php 8103 2014-08-31 16:38:02Z infodaes $
include "config.php";
include "../../include/sfs_case_PLlib.php";

sfs_check();

$year_seme=$_GET['year_seme'];

//若有選擇學年學期，進行分割取得學年及學期
if(!empty($year_seme)){
	$ys=explode("-",$year_seme);
	$sel_year=$ys[0];
	$sel_seme=$ys[1];
}

if(empty($sel_year))$sel_year = curr_year(); //目前學年
if(empty($sel_seme))$sel_seme = curr_seme(); //目前學期

if ($_GET[print_mode]==1){
	echo "<html>
		<meta http-equiv=\"Content-Type\" content=\"text/html; Charset=Big5\">
		<style TYPE=\"text/css\">\n
			<!--\n
		        BODY {font-size:12pt;}\n
		        TABLE {font-size:8pt;}\n
			-->\n
			</style>\n

		</head>
		<body> ";
		echo "<H3><center>$SCHOOL_BASE[sch_cname_s] 教務處隨堂記錄表 記錄人：</center></H3>";
}
else {
	head();
	$tool_bar=&make_menu($school_menu_p);
	echo $tool_bar ;
}

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

                                                                                                               
echo "<form action=\"$_SERVER[PHP_SELF]\" method=\"GET\"><table cellspacing='1' cellpadding='4'  bgcolor=#9EBCDD width=100%>
        <tr bgcolor='#F7F7F7'>
        <td bgcolor='#FFFFFF'>
        $sel_seme_str ";
                                                                                                               

if(!$_GET[curr_seme]<>'') {

	echo "</td></tr></table>";
	foot();
	exit();
}

$year = intval(substr($_GET[curr_seme],0,-1));
$semester = intval(substr($_GET[curr_seme],-1));

//計算天數
$query = "select day from score_course where year=$year and semester=$semester group by day";
$res = $CONN->Execute($query);
$day_arr = array();
while(!$res->EOF){	
	$day_arr[$res->fields[0]] = "星期".Num2CNum($res->fields[0]);
	if ($_GET[curr_day]=='')
		$_GET[curr_day] = $res->fields[0];
	$res->MoveNext();
}

if (count($day_arr)==0){
	echo "</td></tr></table>";
	foot();
	exit();
}

$sel->id = $_GET[curr_day];
$sel->arr = $day_arr;
$sel->s_name = "curr_day";
$sel->top_option='';
$sel->has_empty = false;
$sel_day_str = $sel->get_select();

//查詢今年度節數
$most_class = get_most_class($year,$semester);
for($i=1;$i<=$most_class;$i++)
	$section_arr[$i] = "第 $i 節";

if($_GET[sections] == '')
	$_GET[sections] =1;

$sel->id = $_GET[sections];
$sel->arr = $section_arr;
$sel->s_name="sections";
$sel_sections_str = $sel->get_select();

// 教務處查堂記表項目
$class_chk_list_arr = explode(",",$class_chk_list);

//表頭
$temp_str="";
$table_str = "<table cellspacing='1' cellpadding='1' width=100% bgcolor='#FFFFFF' border=1>
        <tr >
        <td align=center >班級</td>
        <td align=center>課程名稱</td>
        <td align=center>任課教師</td>";
for($i=0;$i<count($class_chk_list_arr);$i++) {
	$table_str .="<td align=center>$class_chk_list_arr[$i]</td>";
	$temp_str .="<td  align=center>&nbsp;</td>";

}
$table_str .="<td>備註</td></tr>";
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



//取得任課教師陣列
$sql_select = "select name,teacher_sn from teacher_base ";
$res= $CONN->Execute($sql_select);
while(!$res->EOF){
	$teacher_arr[$res->fields[1]]=$res->fields[0];
	$res->MoveNext();
}

//課表
$query = "select class_year,class_name,ss_id,teacher_sn,cooperate_sn from score_course where year=$year and semester=$semester and  day='$_GET[curr_day]' and sector=$_GET[sections] order by class_year desc,class_name";
//echo $query;

$res = $CONN->Execute($query) or trigger_error("SQL 錯誤",E_USER_ERROR);
while(!$res->EOF){
	$year_seme = sprintf("%d%02d", $res->fields[class_year],$res->fields[class_name]);
	$ss_id = $res->fields[ss_id];
	$teacher_sn = $res->fields[teacher_sn];
	$cooperate_sn= $res->fields[cooperate_sn];
	$cooperater= $cooperate_sn?'、'.$teacher_arr[$cooperate_sn]:'';
	$table_str .= "
	<tr >
        <td align=center>$class_base_arr[$year_seme]</td>
        <td align=center>$res_arr[$ss_id]</td>
        <td align=center>$teacher_arr[$teacher_sn]$cooperater</td>
	$temp_str
	<td>&nbsp;</td>
	</tr>";
	$res->MoveNext();
}

$table_str .="</table>";

echo "&nbsp;&nbsp;$sel_day_str &nbsp;$sel_sections_str";
echo ($_GET[print_mode]==1)?"<input type=checkbox name=print_mode value=1 checked onchange=\"this.form.submit()\">":"<input type=checkbox name=print_mode  value=1 onchange=\"this.form.submit()\" >";
echo "列印模式";
echo "&nbsp;&nbsp; 記錄日期:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 年&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 月  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;日";

echo "  </td>
	</tr>
	<tr>
	<td>
	$table_str
	</td>
        </tr>";
echo "  </table>
        </form>" ;
echo "<div align=center>註記說明：　○(優或是)　　△(中等)　　Ⅹ(劣或否)</div>";
if ($_GET[print_mode]==1)
	echo "</body></html>";
else
	foot();
?>
