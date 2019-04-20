<?php

include "config.php";
include "chc_func_class.php";

//引入換頁物件(學務系統用法)
include_once "../../include/sfs_oo_dropmenu.php";
include_once "../../include/sfs_case_menu.php";

//【生涯輔導班級幹部管理】模組函式
include_once "../career_leader/my_functions.php";
//認證
sfs_check();

//顯示內容
if(isset($_POST) and count($_POST)>0){
	if($_POST['form_act']=='add'){
		$aa=get_stu($_POST["year_seme"],$_POST["year_name"]);
		$INFO=save_record($aa, $_POST["year_seme"],$_POST["year_name"]);
	}
}

//秀出網頁布景標頭
head("匯出資料");
print_menu($menu_p);


echo make_menu($school_menu_p);

display();

if($INFO!=''){
	echo '<br><br>'.$INFO.'<br><br>';
}

function save_record($stu, $year_seme,$year_name) {
	global $CONN;

	list($my_year, $my_seme)=explode("_", $year_seme);  //分解傳入的學年度與學期
	$seme_key=$year_name.'-'.$my_seme;

	foreach ($stu as $student_sn=>$val) {
		$seme_num=$val['seme_num'];
		$stud_name=$val['stud_name'];
		$class_num=$val['seme_class'];
		$c_curr_seme=$val['seme_year_seme'];

		/***
 		陣列資料說明:
 		  $ponder_array[學期7-1,7-2,8-,8-2,9-1,9-2等][1幹部][1,2] 兩欄
 		  $ponder_array[學期7-1,7-2,8-,8-2,9-1,9-2等][2小老師][1,2] 兩欄
 		*/
		//檢查是否已有舊紀錄
		$query="select * from career_self_ponder where student_sn=$student_sn and id='3-2'";
		$res=$CONN->Execute($query) or die("SQL錯誤:$query");
		$sn=$res->fields['sn'];
		if($sn) {
			$ponder_array=unserialize($res->fields['content']); //解開成二維陣列
			//幹部
			$ponder_array[$seme_key][1][1]=$val['title'][0];
			$ponder_array[$seme_key][1][2]=$val['title'][1];
			//小老師
			//$ponder_array[$seme_key][2][1]=$data_arr[4];
			//$ponder_array[$seme_key][2][2]=$data_arr[5];
			//備註
			$ponder_array[$seme_key]['memo']=$val['memo'];
			//debug_msg("第".__LINE__."行 seme_key ", $seme_key);
			//debug_msg("第".__LINE__."行 ponder_array ", $ponder_array);
			$content=serialize($ponder_array);
			$query="update career_self_ponder set id='3-2',content='$content' where sn=$sn";
		}else{
			//幹部
			$ponder_array[$seme_key][1][1]=$val['title'][0];
			$ponder_array[$seme_key][1][2]=$val['title'][1];
			//小老師
			$ponder_array[$seme_key][2][1]='';//彰縣版目前無「小老師1」欄位
			$ponder_array[$seme_key][2][2]='';//彰縣版目前無「小老師1」欄位
			//備註
			$ponder_array[$seme_key]['memo']=$val['memo'];
			$ponder_array[$seme_key][data]="";
			//debug_msg("第".__LINE__."行 seme_key ", $seme_key);
			//debug_msg("第".__LINE__."行 ponder_array ", $ponder_array);
			$content=serialize($ponder_array);
			$query="insert into career_self_ponder set student_sn=$student_sn,id='3-2',content='$content'";

		} // end if else
		$res=$CONN->Execute($query) or die("SQL錯誤:$query");
		$mycount++;

	} // end foreach
	//$INFO="己於".date("Y-m-d H:i:s")."儲存 $mycount 筆資料成功!";
	$INFO="儲存 $mycount 筆資料成功!";

	return $INFO;
}




/* 取學生陣列,取自stud_base表與stud_seme表*/
function get_stu($year_seme, $year_name){
	global $CONN;

	$CID=split("_",$year_seme);//093_1
	$year=$CID[0];
	$seme=$CID[1];
	$grade=$year_name;//年級

	$CID_1=$year.$seme;

	$SQL="select a.stud_id,a.student_sn,a.stud_name,a.stud_sex,b.seme_year_seme,b.seme_class,b.seme_num,a.stud_study_cond  from stud_base a,stud_seme b where a.student_sn=b.student_sn and b.seme_year_seme='$CID_1' and b.seme_class LIKE '".$grade."__' order by b.seme_class, b.seme_num ";

	$rs=$CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
	$obj_stu=array();
	while ($rs and $ro=$rs->FetchNextObject(false)) {
		$obj_stu[$ro->student_sn] = get_object_vars($ro);
	}

	$SQL="select id,student_sn,seme,kind,org_name,title,memo from chc_leader 
	where kind='0'  and seme='$CID_1' and org_name LIKE '".$grade."__' order by id,  org_name ";
	$rs=$CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
	$All=$rs->GetArray();
	foreach ($All as $ary){
		$Sn=$ary['student_sn'];
		$obj_stu[$Sn]['title'][]=$ary['title'];
		if($ary['memo']!=''){
			$obj_stu[$Sn]['memo'][].=' '.$ary['memo'];
		}
	}
	return $obj_stu;
}


//顯示
function display(){

	$menu='';
	$year_seme=$_REQUEST['year_seme'];
	$year_name=$_REQUEST['year_name'];
	$stage=$_REQUEST['stage'];
	$score_sort=$_REQUEST['score_sort'];
	$sel=$_POST['sel'];
	$sel_class=$_POST['sel_class'];
	$print_special=$_POST['print_special'];

	//設定主網頁顯示區的背景顏色
	$menu="<table border=0 cellspacing=0 cellpadding=2 width=100% bgcolor=#cccccc><tr><td>";

	if (empty($year_seme)) {
		$sel_year = curr_year(); //目前學年
		$sel_seme = curr_seme(); //目前學期
		$year_seme=$sel_year."_".$sel_seme;
	} else {
		$ys=explode("_",$year_seme);
		$sel_year=$ys[0];
		$sel_seme=$ys[1];
	}
	$year_seme_menu=year_seme_menu($sel_year,$sel_seme);
	$class_year_menu =class_year_menu($sel_year,$sel_seme,$year_name);
	$menu.="<form name=\"myform\" method=\"post\" action=\"$_SERVER[PHP_SELF]\">
		<table>
		<tr>
		<td>$year_seme_menu</td><td>$class_year_menu</td><td></td>
		</tr>
		</table></form>";

	$menu.="</tr></table>";

	$check_js='onclick="if( window.confirm(\'確定轉存？會將整年級的「擔任幹部」資料覆蓋！\')){this.form.form_act.value=\'add\';this.form.submit();}" ';

	echo '<table  width="100%"  border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="#9EBCDD" style="table-layout: fixed;word-wrap:break-word;font-size:10pt">
<tr style="font-size:11pt" bgcolor="#9EBCDD"><td>
說明：<br>
1.配合彰化縣「12年國教五專免試入學」作業，本程式可將「班級幹部」資料檔轉存到【<a href="/test_sfs3/modules/career_leader/leader_input.php" target="_blank">生涯輔導班級幹部管理</a>】。<br>
2.使用本程式一次處理一整個年級的資料，會將【<a href="/test_sfs3/modules/career_leader/leader_input.php" target="_blank">生涯輔導班級幹部管理</a>】中的「擔任幹部」資料覆蓋，使用前請三思。<br>
<br>
</td></tr></table>';
echo '<table  width="100%"  border="1" align="center" cellpadding="1" cellspacing="1" style="table-layout: fixed;word-wrap:break-word;font-size:10pt">
<tr style="font-size:11pt">

<td></td></tr>';
	echo '<tr style="font-size:11pt">

<td>';
	echo '<form name="form1" method="post" action="">'.$menu.'
 <br> 「班級幹部」資料檔　
 <INPUT TYPE="hidden" NAME="form_act" Value="">
<input type="submit" name="leader_save" value="轉存到【生涯輔導班級幹部管理】模組" '.$check_js.'>
</form>';
	echo '</td></tr>';
	echo '</table>';

}

function debug_msg($title, $showarry){
	echo "<pre>";
	echo "<br>$title<br>";
	print_r($showarry);
}
