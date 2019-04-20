<?php

// $Id: chk.php 7340 2013-07-11 06:02:08Z hami $

// 取得設定檔
include "config.php";
if (empty($chk_menu_arr)) header("location:index.php");

sfs_check();


if(checkid($_SERVER['SCRIPT_FILENAME'],1)){ $selectable=1; }

$semester_reset=$_POST['semester_reset'];
if($semester_reset) { $_POST['class_id']=''; $_POST['student_sn']=''; }

$class_reset=$_POST['class_reset'];
if($class_reset) { $_POST['student_sn']=''; }

$student_sn=$_POST['nav_next']?$_POST['nav_next']:$_POST['student_sn'];
$class_id=$_POST['class_id'];

//導師取其任教班級
if(!$selectable) {
$class_num=get_teach_class();
$class_id=sprintf("%03d_%d_%02d_%02d",curr_year(),curr_seme(),substr($class_num,-3,strlen($class_num)-2),substr($class_num,-2));
}

$smarty->assign("class_id",$class_id);

if(!class_id) $student_sn=0;
$smarty->assign("student_sn",$student_sn);


if($_POST['year_seme']=="") $_POST['year_seme']=sprintf("%03d",curr_year()).curr_seme();
$sel_year=intval(substr($_POST['year_seme'],0,-1));
$sel_seme=intval(substr($_POST['year_seme'],-1,1));
$seme_year_seme = sprintf("%03d%d",$sel_year,$sel_seme);

//取得年度與學期的下拉選單
$sql="select DISTINCT year,semester from school_class where enable='1' order by year DESC,semester";
$res=$CONN->Execute($sql) or user_error("取得年度與學期失敗！<br>$sql",256);
$date_select="<select name='year_seme' onchange='this.form.semester_reset.value=\"Y\"; this.form.submit();'".($selectable?'':' disabled').">";
while(!$res->EOF) {
	$curr_semester=sprintf("%03d",$res->fields['year']).$res->fields['semester'];
	if($curr_semester==$_POST['year_seme']) $selected_seme='selected'; else $selected_seme='';
	$semester_name=$res->fields['year'].'學年度第'.$res->fields['semester'].'學期';
	$date_select.="<option value='$curr_semester' $selected_seme>$semester_name</option>";
	$res->MoveNext();
}
$date_select.="</select>";

//$date_select=&class_ok_setup_year($sel_year,$sel_seme,"year_seme","this.form.semester_reset.value=\"Y\"; this.form.submit");
$smarty->assign("date_select",$date_select);

//年級與班級選單
$sql="select class_id,c_year,c_name from school_class where year='$sel_year' and semester = '$sel_seme' and enable='1' order by c_year,c_sort";
$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
$class_select="<select name='class_id' onchange='this.form.class_reset.value=\"Y\"; this.form.submit();'".($selectable?'':' disabled').">";
while(!$res->EOF) {
	if(! $class_id) $class_id=$res->fields['class_id'];  //若未指定班級  則以第一班代表
	if($class_id==$res->fields['class_id']) $curr_class='selected'; else $curr_class='';
	$class_name=$school_kind_name[$res->fields['c_year']].$res->fields['c_name'].'班';
	$class_select.="<option $curr_class value='".$res->fields['class_id']."'>$class_name</option>";
	$res->MoveNext();
}
$class_select.="</select>";
$smarty->assign("class_select",$class_select);

//將class_id轉為class_num
$class_id_arr=explode('_',$class_id);
$class_num=($class_id_arr[2]+0).$class_id_arr[3];

$smarty->assign("selectable",$selectable);
$smarty->assign("class_id",$class_id);
$smarty->assign("class_num",$class_num);

if(!$selectable){
	//取得任教班級代號
	$class_num=get_teach_class();
	$class_all=class_num_2_all($class_num);

	if(empty($class_num)){
		$act="error";
		$error_title="無班級編號";
		$error_main="找不到您的班級編號，故您無法使用此功能。<ol>
		<li>請確認您兼任導師。
		<li>請確認教務處已經將您的任教資料輸入系統中。
		</ol>";
	} elseif ($_GET[error]==1){
		$act="error";
		$error_title="該班級無學生資料";
		$error_main="找不到您的班級學生，故您無法使用此功能。<ol>
		<li>請確認您兼任導師。
		<li>請確認教務處已經將您的學生資料輸入系統中。
		<li>匯入學生資料：『學務系統首頁>教務>註冊組>匯入資料』(<a href='".$SFS_PATH_HTML."modules/create_data/mstudent2.php'>".$SFS_PATH_HTML."modules/create_data/mstudent2.php</a>)</ol>";
	}
}

if ($_POST[save]) {
	//寫入前預先進行 < > ' " &字元替換  避免HTML特殊字元造成顯示或sxw報表錯誤
	//修正 unicode 字被排除問題 
	//$char_replace=array("<"=>"＜",">"=>"＞","'"=>"’","\""=>"”","&"=>"＆");
	//while(list($sn,$v)=each($_POST[chk])) {
	foreach($_POST[chk] as $sn=>$v){
		//while(list($main,$vv)=each($v)) {
		foreach($v as $main=>$vv){
			//while(list($sub,$s)=each($vv)) {
			foreach($vv as $sub=>$s){
				if ($sub=="memo") {
				//	foreach($char_replace as $key=>$value)	$s=str_replace($key,$value,$s);
					$CONN->Execute("replace into stud_seme_score_nor_chk (seme_year_seme,student_sn,main,sub,ms_memo) values ('$seme_year_seme','$sn','$main','0','$s')");
					}
				else
					$CONN->Execute("replace into stud_seme_score_nor_chk (seme_year_seme,student_sn,main,sub,ms_score) values ('$seme_year_seme','$sn','$main','$sub','$s')");
			}
		}
	}
	//while(list($sn,$v)=each($_POST[nor_memo])) {
	foreach($_POST[nor_memo] as $sn=>$v){
		//while(list($ss_id,$memo)=each($v)) {
		foreach($v as $ss_id=>$memo){		
			foreach($char_replace as $key=>$value)	$memo=str_replace($key,$value,$memo);
			$CONN->Execute("replace into stud_seme_score_nor (seme_year_seme,student_sn,ss_id,ss_score_memo) values ('$seme_year_seme','$sn','$ss_id','$memo')");
		}
	}
}

//快貼全班明細記錄===========================================================
if ($_POST['mode']=="pastALL" and $_POST['stud_data']) {
	//寫入前預先進行 < > ' " &字元替換  避免HTML特殊字元造成顯示或sxw報表錯誤
	$char_replace=array("<"=>"＜",">"=>"＞","'"=>"’","\""=>"”","&"=>"＆");
	$seme_class_num=substr($_POST['class_id'],7,1).substr($_POST['class_id'],9,2);
	$data_arr=explode("\n",$_POST['stud_data']);
 //開始處理
 	for ($i = 0 ; $i < count($data_arr); $i++ ) {
		//去掉前後空白
	 //$data_arr[$i] = trim($data_arr[$i]);
	 //去掉跟隨別的擠在一塊的空白
   //$data_arr[$i] = preg_replace('/\s(?=\s)/','', $data_arr[$i]);
   //$data_arr[$i] = preg_replace('/[\n\r\t]/', ' ', $data_arr[$i]);
   //echo $data_arr[$i]."<br>";
   //變成二維陣列
   $student=explode("\t",$data_arr[$i]);  //某筆學生的資料
   if (count($student)==7) { //7個欄位都有資料再處理,依序 : 座號,姓名,團體記錄1,校內服務2,社區服務3,校內特殊4,校外特殊5
   	foreach ($student as $k=>$v) {
   	 $student[$k]=trim($v);  //去掉前後空白
   	}   	 

   	 //取得學生的 student_sn
   	 $query="select a.student_sn from stud_seme a,stud_base b where a.student_sn=b.student_sn and a.seme_class='".$seme_class_num."' and a.seme_num='".$student[0]."' and b.stud_name='".$student[1]."'";
   	 $res_sn=mysql_query($query);
   	 if (mysql_num_rows($res_sn)>0) {   								  	 
   	 	 list($student_sn)=mysql_fetch_row($res_sn);
   				for ($j=2;$j<=6;$j++) {
   					$ss_id=$j-1;
   					$memo=($student[$j]=="*")?"":$student[$j];
   					foreach($char_replace as $key=>$value)	$memo=str_replace($key,$value,$memo);
   					$CONN->Execute("replace into stud_seme_score_nor (seme_year_seme,student_sn,ss_id,ss_score_memo) values ('$seme_year_seme','$student_sn','$ss_id','$memo')");   				  
   				}
     }
   }
	} // end for	
}
//=================================================================================

if ($act) {
	head("填寫學生日常生活表現檢核表");
	echo error_tbl($error_title,$error_main);
	foot();
	exit;
}

$s=get_school_base();

//if($class_num) {
	//顯示班級學生資料
	$style_color[1]="#5555FF";
	$style_color[2]="#FF5555";
	$sql="select a.student_sn,a.stud_name,a.stud_sex,b.seme_num as sit_num from stud_base a,stud_seme b where a.student_sn=b.student_sn and (a.stud_study_cond=0 or a.stud_study_cond=5) and  b.seme_year_seme='$seme_year_seme' and b.seme_class='$class_num' order by b.seme_num ";   //SQL 命令   
	$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
	$student_count=$res->recordcount();
	$stud_select="<select size='$student_count' name='student_sn' onchange='this.form.submit();'>";
	while(!$res->EOF) {
		if(! $student_sn) $student_sn=$res->fields['student_sn'];  //若未指定學生  則以第一位代表
		$stud_sex_arr[$res->fields['stud_sex']]++;
		//echo "<BR>".$res->fields['student_sn'];
		if($curr_student) $next_student_sn=$res->fields['student_sn'];
		if($student_sn==$res->fields['student_sn']) {
			$curr_student='selected';
			$stu_class_num=$res->fields['sit_num'];
		} else $curr_student='';
		$stud_select.="<option $curr_student STYLE=\"color:".$style_color[$res->fields['stud_sex']]."\" value='".$res->fields['student_sn']."'>(".$res->fields['sit_num'].")".$res->fields['stud_name']."</option>";
	$res->MoveNext();
	}
	$stud_select.="</select>";
	$stud_select.="<BR>學生數： $student_count";
	$stud_select.="<BR>男：".$stud_sex_arr[1];
	$stud_select.="<BR>女：".$stud_sex_arr[2];
	$stud_select.="<BR>其他：".($student_count-$stud_sex_arr[1]-$stud_sex_arr[2]);
	$smarty->assign("stud_select",$stud_select);
	if ($_POST['chknext']) $smarty->assign("next_student_sn",$next_student_sn);
	
	//取得指定學生資料
	$stu=get_stud_base($student_sn,"");
	$stud_id=$stu['stud_id'];
	
	//轉換班級代碼
	//if(!$class_id) $class_id=sprintf("%03d_%d_%02d_%02d",$sel_year,$sel_seme,substr($class_num,0,2),substr($class_num,0,2),substr($class_num,3));
	$class=class_id_2_old($class_id);
	
	//print_r($class);
	
	$smarty->assign("stu",$stu);
	$smarty->assign("class_name",$class[5]);

	//座號
	$smarty->assign("stu_class_num",$stu_class_num);

	//檢核表項目
	$smarty->assign("itemdata",get_chk_item($sel_year,$sel_seme));

	//檢核表值
	$chk_item=chk_kind();
	$chk_value=get_chk_value($student_sn,$sel_year,$sel_seme,$chk_item,"input");
	$nor_memo = $_POST['nor_memo'][$student_sn][0];
	//if ($_POST[save]) merge_chk_text($sel_year,$sel_seme,$student_sn,$chk_value);

	//其他表現文字
	$query="select * from stud_seme_score_nor where seme_year_seme='$seme_year_seme' and student_sn='$student_sn' order by ss_id";
	$res=$CONN->Execute($query);
	$r=array();
	while(!$res->EOF) {
		$r[$res->fields['ss_id']]=$res->fields['ss_score_memo'];
		$res->MoveNext();
	}
	$smarty->assign("nor_memo",$r);
	
	if($_POST[auto_spe]){
		$sql="SELECT * FROM stud_seme_spe WHERE seme_year_seme='$seme_year_seme' AND stud_id='$stud_id'";
		$res=$CONN->Execute($sql) or user_error("讀取輔導特殊表現失敗！<br>$sql",256);
		$spe_data=array();
		while(!$res->EOF) {
			$outside=$res->fields['outside'];
			$spe_data[$outside].='['.$res->fields['sp_date']."]".$res->fields['sp_memo']."\r\n";
			$res->MoveNext();
		}
		
		//echo "<PRE>";
		//print_r($spe_data);
		//echo "</PRE>";
		
	}
	$smarty->assign("spe_data_0",$spe_data[0]);
	$smarty->assign("spe_data_1",$spe_data[1]);
	$smarty->assign("spe_data_2",$spe_data[2]);

//}

$smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE);
$smarty->assign("module_name","填寫學生日常生活表現檢核表");
$smarty->assign("SFS_MENU",$school_menu_p);
$smarty->assign("sel_year",$sel_year);
$smarty->assign("sel_seme",$sel_seme);
$smarty->assign("stud_id",$stud_id);
$smarty->assign("student_sn",$student_sn);
$smarty->assign("sch_cname",$s[sch_cname]);
$smarty->assign("chk_value",$chk_value);
$smarty->assign("chk_item",$chk_item);
$smarty->display("academic_record_chk.tpl");
?>
