<?php
//$Id: supply.php 5310 2009-01-10 07:57:56Z hami $
include "config.php";
include_once "../../include/config.php";
include_once "../../include/sfs_case_score.php";
require_once "../../include/sfs_core_globals.php";
include_once "../../include/sfs_case_studclass.php";
include_once "../../include/sfs_case_subjectscore.php";

//認證
sfs_check();

//若沒有選擇假單，則回到列表
if(empty($id)){
	header("Location: deputy.php?act='err'");
}

head("課務處理");
$tool_bar=make_menu($school_menu_p);
echo $tool_bar;
	
//秀出網頁
//假單資料
$main=teacher_absent($id);
echo $main;

$deputy_class="";  //代課教師課表
// 不需要 register_globals
if (!ini_get('register_globals')) {
	ini_set("magic_quotes_runtime", 0);
	extract( $_POST );
	extract( $_GET );
	extract( $_SERVER );
}
//刪除代課
if ($_POST[del]) {
	list($c_id,$v)=each($_POST[del]);
	$query = "delete from teacher_absent_course where c_id ='$c_id'";
	$CONN->Execute($query);
	$main=&room_setup_form();

}

if ($act == "修改確定") {
	$sql_update = "update teacher_absent_course set 
	d_kind='$c_d_kind',start_date='$start_date',end_date='$end_date',class_name='$class_name',deputy_sn='$deputy_sn' ,times='$times' ,class_dis='$class_dis' where c_id=$c_id";
	$CONN->Execute($sql_update);
	$main=&room_setup_form();
}elseif ($act=="新增確定") {
	$sql_insert = "insert into teacher_absent_course (a_id,teacher_sn,class_dis,d_kind,deputy_sn,class_name, times,start_date,end_date) values 
								('$id','$teacher_sn','$class_dis','$c_d_kind','$deputy_sn','$class_name', '$times','$start_date','$end_date')";
	$CONN->Execute($sql_insert);
 	$main=&room_setup_form();

}elseif ($_POST[edit]) {	//修改代課
	list($c_id,$v)=each($_POST[edit]);	
	$main=&room_setup_form("edit",$c_id);
}elseif ($act=="新增") {
	$main=&room_setup_form("add",$c_id,$c_d_kind);

}elseif ($_POST[deputy]) {
	list($c_id,$v)=each($_POST[deputy]);
		$query="update teacher_absent_course set status='1',deputy_date='".date("Y-m-d H:i:s")."' where c_id='$c_id'";
		$CONN->Execute($query);
	$main=&room_setup_form();

} elseif ($_POST[deputy_c]) {
	list($c_id,$v)=each($_POST[deputy_c]);
		$query="update teacher_absent_course set status='0',deputy_date='".date("Y-m-d H:i:s")."' where c_id='$c_id'";
		$CONN->Execute($query);
	$main=&room_setup_form();

}else{
	$main=&room_setup_form();
}




echo $main;

//課表資料
$main=class_form_search($teacher_sn);
echo $main;
echo $deputy_class;
/*
函式區
*/

//代課資料
function &room_setup_form($mode="",$cc_id,$c_d_kind){
	global $CONN,$id,$d_kind_arr,$times_kind_arr,$teacher_sn,$course_kind,$check2_sn,$c_start_date,$c_end_date,$class_dis,$deputy_class,$week_array;
	
	if ($check2_sn==0 and $class_dis<>0){
		$add_button="<input type=submit name='act' value='新增'>";
		$view_button="<input type=submit name='act' value='瀏覽'>";
	}

	$modify_submit_button="<input type='submit' name='act' value='修改確定'>";
	

	if ($mode=="edit"){
		$b0="$view_button $add_button $modify_submit_button";
		$b1="$modify_submit_button";
	}elseif($mode=="add"){
		if($c_d_kind){
			
			$hidden="";
		}else{
			
			$hidden="<input type='hidden' name='act' value= '新增'>";

		}

		$d_kind_menu=d_make_menu("選擇單位",$c_d_kind,$d_kind_arr,"c_d_kind",1);
		$d_class_menu=d_class_menu();

		$teacher_menu=deputy_teacher_menu("deputy_sn",0,$teacher_sn);
		if($c_d_kind==1 or $c_d_kind==2){
			$end_date_menu=$d_class_menu;
		}else{
			$end_date_menu="<input type='text' style='font-size: 18pt' size='10' maxlength='10' name='end_date'  value='$c_end_date' >";
		}
		$add_form="<tr class='title_mbody'>
		<td><br></td>
		<td>$d_kind_menu</td>
		<td><input type='text' style='font-size: 18pt' size='10' maxlength='10' name='start_date' value='$c_start_date'></td>
		<td align='center' >$end_date_menu</td>
		<td><input type='text' size='20' maxlength='20' name='class_name'></td>
		<td align='center' >$teacher_menu</td>
		<td align='center' ><input type='text' style='font-size: 18pt' size='2'  name='times' value='1'></td>
		<td align='center' ><input type='submit' name='act' value='新增確定'></td>		
		</tr>
		$hidden

		";

	}
	
	$button0="<tr  class='title_sbody2'><td colspan='5'>$b0</td></tr>";
	$button1=(!empty($b1))?"<tr  class='title_sbody2'><td colspan='5'>$b1</td></tr>":$button0;

	//讀取資料
	$sql_select = "select * from teacher_absent_course  where a_id='$id' and travel='0' order by c_id";
	$result = $CONN->Execute ($sql_select) or die($sql_select) ;
	$i=0;
	$d_sn_arr=array();
	while (!$result->EOF) {

		$c_id = $result->fields["c_id"];

		if ($check2_sn==0 and $class_dis<>0 ){
			$cancel_button="<input type='image' src='images/del.png' name='deputy_c[$c_id]' alt='取消'>";
			$check_button="<input type='image' src='images/edit.png' name='deputy[$c_id]' alt=' 確定'>";
		}

		$d_kind = $result->fields["d_kind"];
		$start_date = $result->fields["start_date"];

//取得星期幾
	
		$nw=d_week($start_date);
		
		$end_date = $result->fields["end_date"];
		$class_name = $result->fields["class_name"];
		$deputy_sn = $result->fields["deputy_sn"];
		$times = $result->fields["times"];
		$status = $result->fields["status"];
		
		$class_dis=$result->fields["class_dis"];
		$d_name=get_teacher_name($deputy_sn);
		
		$n_class_dis=$course_kind["$class_dis"];
	
		$ti = ($i++%2)+1;
		if($status ==0 and $check2_sn==0){
			$modify_button="<input type='image' src='images/edit.png' name='edit[$c_id]' alt='修改'>";
			$del_button="<input type='image' src='images/del.png' name='del[$c_id]' alt='刪除'>";
		}else{
			$modify_button="";
			$del_button="";

		}
		
		if ( $deputy_sn==0 ){
			$check_button="";
		}

		$check=($status=="0") ?
		"<font size=2 color=red>待確定</font>
		$check_button
		":"
		$cancel_button
		";

		
	//選單
		$d_kind_menu=d_make_menu("選擇單位",$d_kind,$d_kind_arr,"c_d_kind",0);

		$teacher_menu=deputy_teacher_menu("deputy_sn",$deputy_sn,$teacher_sn);
	
		$room=($mode=="edit" and $c_id==$cc_id)?
		"<td align='center' >
		$del_button
		$n_class_dis 
		$modify_button
		</td>
		<td align='center' >$d_kind_menu</td>
		<td align='center' ><input type='text' style='font-size: 16pt' size='10' maxlength='10' name='start_date' value='$start_date'></td>
		<td align='center' ><input type='text' style='font-size: 16pt' size='10' maxlength='10' name='end_date' value='$end_date'></td>
		<td><input type='text' size='20' maxlength='20' name='class_name' value='$class_name'></td>
		<td align='center' >$teacher_menu</td>
		<td align='center' ><input type='text' style='font-size: 16pt' size='2'  name='times' value='$times'></td>
		<td>$modify_submit_button</td>
		<input type='hidden' name='c_id' value= $c_id >
		":"
		<td align='center'>
		$del_button
		$n_class_dis 
		$modify_button
		</td>
		<td align='center'>		
		 $d_kind_arr[$d_kind] 
		<td align='center' ><font size=3>$start_date $nw</font></td>
		<td align='center'><font size=3>$end_date</font></td>		
		<td align='center'>$class_name</td>
		<td align='center'>
		$d_name $check
		</td>
		<td align='center'>$times</td>
		<td align='center'>$times_kind_arr[$d_kind]</td>
		";

		$room_data.="
		<tr class=nom_$ti>
		$room
		</tr>";
//代課老師課表資料
		$p_dc=1;
		for($j=0;$j<$i;$j++){
			if($deputy_sn==$d_sn_arr[$j-1]){
				$p_dc=0;	
			}
		}
		if($deputy_sn >0 and $p_dc){
			$deputy_class .=class_form_search($deputy_sn);	
		}
   		$d_sn_arr[$i]=$deputy_sn;
	


		$result->MoveNext();
	}


	//相關功能表

	$main="	
	<table border='1' cellPadding='3' cellSpacing='0' class='main_body' width=100%>
	<form name ='myform' action='{$_SERVER['PHP_SELF']}' method='post'>
	<tr class='title_mbody'>
	<td  align='center'width=15%> $add_button 課務 $view_button</td>
	<td  align='center' width=10%>代課方式  </td>
	<td  align='center'width=20%>代課日期</td>
	<td align='center'width=15%>結束日期或節次</td>
	<td align='center'width=15%>科目班級</td>
	<td align='center'width=15%>代理人</td>
	<td align='center'width=5%>數量</td>
	<td align='center'width=5%>單位</td>
	
	</tr>	
	$room_data
	$add_form
	</table>
	<input type='hidden' name='id' value= $id >
	</form>
	";

	return $main;
}







//假單資料
function teacher_absent($id){
	global $CONN,$course_kind,$view_tsn,$sel_year,$sel_seme,$teacher_sn,$class_dis,$check2_sn,$c_start_date,$c_end_date,$check1,$check2,$check3,$check4;

		$query="select * from teacher_absent where id='".$id."'";
		$result = mysql_query($query) or die ($query);
		$row = mysql_fetch_array($result);
		
		$view_tsn=$row["teacher_sn"];


		if($view_tsn <> $_SESSION[session_tea_sn]) exit();

		$sel_year=$row["year"];
		$sel_seme=$row["semester"];
		$teacher_sn=$row["teacher_sn"];
		$class_dis=$row["class_dis"];

		$t_name=get_teacher_name($row["teacher_sn"]);
		$reason=$row["reason"];
		$note=$row["note"];
		$locale=$row["locale"];

		$abs_kind_arr=tea_abs_kind();
		$abs_kind=$abs_kind_arr[$row["abs_kind"]];
		$n_class_dis=$course_kind[$row["class_dis"]];
		$start_date=substr($row["start_date"],0,16);
		$c_start_date=substr($start_date,0,10);

		$end_date=substr($row["end_date"],0,16);
		$c_end_date=substr($end_date,0,10);
	
		$day_hour=($row["day"]==0)?"":$row["day"] ."日";
		$day_hour.=($row["hour"]==0)?"":$row["hour"] ."時";

		$check2_sn=$row["check2_sn"];
		$de_name=get_teacher_name($row["deputy_sn"]);
		$c1_name=get_teacher_name($row["check1_sn"]);
		$c2_name=get_teacher_name($row["check2_sn"]);
		$c3_name=get_teacher_name($row["check3_sn"]);
		$c4_name=get_teacher_name($row["check4_sn"]);
		
		$main= "<table border=0 cellspacing=1 cellpadding=4 width=100% bgcolor=#cccccc >
		<tr bgcolor=#E1ECFF align=center>
		<td width=4%>序號</td><td width=8%>請假人</td><td width=6%>假別</td>	<td width=12%>事由</td><td width=16%>開始時間<br>結束時間</td><td width=6% >日數</td>
		<td width=6%>課務</td>	<td width=8%>職務代理人</td><td width=8%>$check1</td><td width=8%>$check2</td><td width=8%>$check3</td><td width=8%>$check4</td></tr>";

		$main.= "<tr bgcolor=#ffffff align=center>
		<td>$id</td>
		<td>$t_name</td>
		<td><font size=2>$abs_kind</font><br>
			<font size=2 color=blue>$note</font></td>	
		<td><font size=2>$reason</font><br><font size=2 color=blue>$locale</font></td>		
		<td>$start_date<br>$end_date</td>
		<td>$day_hour</td>
		<td><font size=2>$n_class_dis</font></td>	
		<td>$de_name</td>
		<td>$c1_name</td>
		<td>$c2_name</td>
		<td>$c3_name</td>
		<td>$c4_name</td></tr>";

	return $main;
}

function class_form_search($view_tsn){
	global $school_menu_p,$PHP_SELF,$class_id,$sel_year,$sel_seme;
	
	$list_class_table=search_teacher_class_table($sel_year,$sel_seme,$view_tsn);

	$main="
	$list_class_table
	";
	return $main;
}

//教師的任課表
function search_teacher_class_table($sel_year="",$sel_seme="",$view_tsn=""){
	global $CONN,$PHP_SELF,$class_year,$conID,$weekN,$school_menu_p,$sections;
	
	$main=teacher_all_class($sel_year,$sel_seme,$view_tsn)."<br>";

	return $main;
}


//教師的任課總表
function teacher_all_class($sel_year="",$sel_seme="",$tsn=""){
	global $CONN,$PHP_SELF,$class_year,$conID,$weekN,$school_menu_p,$sections;

	$teacher_name=get_teacher_name($tsn);

	$double_class=array();
	$kk=array();
	
	//每週的日數
	$dayn=sizeof($weekN)+1;

	//找出教師該年度所有課程
	$sql_select = "select course_id,class_id,day,sector,ss_id,room from score_course where teacher_sn='$tsn' and year='$sel_year' and semester='$sel_seme' order by day,sector";

	$recordSet=$CONN->Execute($sql_select) or user_error("錯誤訊息：",$sql_select,256);
	while (list($course_id,$class_id,$day,$sector,$ss_id,$room)= $recordSet->FetchRow()) {
		$k=$day."_".$sector;
		$a[$k]=$ss_id;
		$b[$k]=$class_id;
		$room[$k]=$room;
		
		//若是日期節數有重複的紀錄起來
		if(in_array($k,$kk))$double_class[]=$k;

		//把所有日期節數放日陣列
		$kk[]=$k;
	}

	//取得星期那一列
	for ($i=1;$i<=count($weekN); $i++) {
		$main_a.="<td align='center' >星期".$weekN[$i-1]."</td>";
	}

	//取得節數的最大值
	$sections=get_most_class($sel_year,$sel_seme);


	//取得課表
	for ($j=1;$j<=$sections;$j++){

		if ($j==5){
			$all_class.= "<tr bgcolor='white'><td colspan='$dayn' align='center'>午休</td></tr>\n";
		}


		$all_class.="<tr bgcolor='#FBEC8C'><td align='center'>$j</td>";

		//列印出各節
		for ($i=1;$i<=count($weekN); $i++) {

			$k2=$i."_".$j;

			//取得班級資料
			$the_class=get_class_all($b[$k2]);
			$class_name=($the_class[name]=="班")?"":$the_class[name];

			//科目
			$subject_show="<font size=3>".get_ss_name("","","短",$a[$k2])."</font>";

			//班別
			$class_show="<font size=2> $class_name </font>";

			//若是該日期節數有在重複陣列理，秀出紅色底色
			$d_color=(in_array($k2,$double_class))?"red":"white";

			//每一格
			$all_class.="<td align='center'  width=18% bgcolor='$d_color'>
			 $sub$subject_show   $class_show
			
			<!--<input type='text' name='room' value='".$room[$i][$j]."' size='10'>-->
			</td>\n";
		}

		$all_class.= "</tr>\n" ;
	}


	//該班課表
	$main_class_list="
	<tr bgcolor='#FBDD47'><td colspan=6>『".$teacher_name."』授課總表</td></tr>
	<tr bgcolor='#FBF6C4'><td align='center' width=10%>節</td>$main_a</tr>
	$all_class";

	$main="
	<table border='0' cellspacing='1' cellpadding='4' bgcolor='#D06030' width='100%'>
	$main_class_list
	</table>
	";
	return  $main;
}


function deputy_teacher_menu($s_name,$teacher_sn,$agent_sn) {
	$tm = new drop_select();
	$tm->s_name =$s_name;
	$tm->top_option = "選擇教師";
	$tm->id = $teacher_sn;
	$tm->arr = my_teacher_array($agent_sn);
	//$tm->is_submit = true;
	return $tm->get_select();
}

function d_class_menu() {
	$arr=array("導師時間"=>"導師時間","導師時間(上)"=>"導師時間(上)","第1節"=>"第1節","第2節"=>"第2節","第3節"=>"第3節","第4節"=>"第4節","第5節"=>"第5節","第6節"=>"第6節","第7節"=>"第7節","導師時間(下)"=>"導師時間(下)");
	$mon = new drop_select();
	$mon->s_name ="end_date";
	$mon->top_option = "選擇節次";
	//$mon->id = $d_kind;
	$mon->arr = $arr;
	//$mon->is_submit = $true;
	return $mon->get_select();
}



?>