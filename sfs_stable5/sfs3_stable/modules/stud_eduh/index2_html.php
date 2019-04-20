<?php

// $Id: index2.php 6890 2012-09-14 08:43:51Z smallduh $

include "config.php";
include "../stud_report/report_config.php";
include_once "../../include/sfs_case_dataarray.php";
//認證檢查
sfs_check();

// 不需要 register_globals
if (!ini_get('register_globals')) {
	ini_set("magic_quotes_runtime", 0);
	extract( $_POST );
	extract( $_GET );
	extract( $_SERVER );
}

$postBtn = "網頁輸出";
$template=$_POST[template];
$sel_stud=$_POST[sel_stud];

//個資記錄
//班級陣列
$class_arr = class_base();
//使用者選取的學生
$stud_id_list=implode(',',$sel_stud);

if ($_REQUEST[year_seme]=='')
  	         $_REQUEST[year_seme] = sprintf("%03d%d",curr_year(),curr_seme());

if (count($sel_stud) >0 )
//個資記錄
//學期
$year_seme=$_POST['year_seme'];
//班級(先取得SFS3中的內定班級代碼例如101,再轉換成學校自訂名稱例一年甲班)
$class_id=$_POST['class_id'];
$class_name=$class_arr[$class_id];

//限制入學期間的資料 ==2012.09.14 by smallduh==================================
// 某些資料表沒有 student_sn 這個欄位, 但有 seme_year_seme 等學年資料欄位, 故取入學年間的資料
if ($class_id>700) {
 $STUD_STUDY_YEAR=sprintf("%d",substr($year_seme,0,3))-(sprintf("%d",substr($class_id,0,1))-7);
 $min_year_seme=sprintf("%03d",$STUD_STUDY_YEAR)."1";
 $max_year_seme=sprintf("%03d",$STUD_STUDY_YEAR+2)."2"; //國中
} else {
 $STUD_STUDY_YEAR=sprintf("%d",substr($year_seme,0,3))-(sprintf("%d",substr($class_id,0,1))-1);
 $min_year_seme=sprintf("%03d",$STUD_STUDY_YEAR)."1";
 $max_year_seme=sprintf("%03d",$STUD_STUDY_YEAR+5)."2"; //國小
}
//============================================================================

if($_POST['do_key']==$postBtn) {
	//個資記錄
	$test=pipa_log("印網頁式輔導記錄表\r\n學期：$year_seme\r\n班級：$class_id $class_name\r\n 學生列表：$stud_id_list");	
	
	$min=1+$IS_JHORES;
	$max=6+$IS_JHORES;
	//出生地
	$birth_state_arr = birth_state();
//print_r($birth_state_arr);		
//exit;
	//性別
	$sex_arr = array("1"=>"男","2"=>"女");	
	
	//產生輔導記錄A表選項參照陣列
	$eduh_item_list_arr=get_eduh_item_list();
	
	//取得選定學生流水號準備資料
	$data_arr=array();
	$stud_id_list=implode(',',$sel_stud);
	//取得stud_base基本資料
	//增加限制入學年來取得資料 2012.09.14 by smallduh
	//$sql="select student_sn,stud_id,stud_name,stud_sex,stud_study_year,stud_person_id,stud_birth_place,stud_addr_1,stud_addr_2,stud_birthday,stud_tel_1,stud_tel_2,enroll_school from stud_base where stud_id in ($stud_id_list) order by curr_class_num";
$sql="select student_sn,stud_id,stud_name,stud_sex,stud_study_year,stud_person_id,stud_birth_place,stud_addr_1,stud_addr_2,stud_birthday,stud_tel_1,stud_tel_2,enroll_school from stud_base where stud_id in ($stud_id_list) and stud_study_year='$STUD_STUDY_YEAR' order by curr_class_num"; 

	$res=$CONN->Execute($sql) or user_error("讀取stud_base資料失敗！<br>$sql",256);
	$student_sn_arr=array();
	while(!$res->EOF)
	{
		$stud_id=$res->fields['stud_id'];
		$student_sn=$res->fields['student_sn'];
		$stud_study_year=$res->fields['stud_study_year'];
		$student_sn_list.=$res->fields['student_sn'].',';
		$student_sn_arr[$res->fields['student_sn']]=$stud_id;
		for($i=0;$i<$res->FieldCount();$i++)
		{
			$r=$res->fetchfield($i);
			$data_arr[$stud_id][$r->name]=$res->fields[$i];
		}
		$stud_birthday=$res->fields['stud_birthday'];
		$bir_temp_arr = explode("-",DtoCh($stud_birthday));		
		$data_arr[$stud_id]["stud_birthday"]=sprintf("%d年%d月%d日",$bir_temp_arr[0],$bir_temp_arr[1],$bir_temp_arr[2]);
		
		//處理入學資料
		$data_arr[$stud_id]["stud_study_year"]=$stud_study_year;
		$data_arr[$stud_id]["enroll_school"]=$res->fields['enroll_school'];
		$data_arr[$stud_id]["enroll_date"]=$res->fields['stud_study_year'].'年08月';
		
		//假使stud_base無資料 則自異動紀錄(stud_move)->新生入學中搜尋
		if(! $res->fields['enroll_school'])
		{
			$sql_enroll="select year(move_date) as enroll_year,month(move_date) as enroll_month,school from stud_move where move_kind=13 and student_sn=$student_sn;";
			$res_enroll=$CONN->Execute($sql_enroll) or user_error("讀取stud_move資料失敗！<br>$sql_enroll",256);
			if($res_enroll->EOF)
			{
				//自stud_move_import->新生入學中搜尋, 應該是用不到(因為XML匯入會寫到stud_base->enroll_school)
				$sql_enroll_import="select year(move_date) as enroll_year,month(move_date) as enroll_month,school from stud_move_import where move_kind=13 and student_sn=$student_sn;";
				$res_enroll_import=$CONN->Execute($sql_enroll_import) or user_error("讀取stud_move_import資料失敗！<br>$sql_enroll_import",256);
				if($res_enroll_import->EOF)
				{
					if(! $data_arr[$stud_id]["enroll_date"]) $data_arr[$stud_id]["enroll_date"]=($res_enroll_import->fields['enroll_year']-1911).'年'.$res_import->fields['enroll_month'].'月';
					$data_arr[$stud_id]["enroll_school"]=$res_enroll_import->fields['school'];
				}
			} else
			{
				if(! $data_arr[$stud_id]["enroll_date"]) $data_arr[$stud_id]["enroll_date"]=($res_enroll->fields['enroll_year']-1911).'年'.$res_enroll->fields['enroll_month'].'月';
				$data_arr[$stud_id]["enroll_school"]=$res_enroll->fields['school']?$res_enroll->fields['school']:$school_long_name;
			}
		}
		
		//轉譯資料
		$data_arr[$stud_id]['stud_sex']=$sex_arr[$data_arr[$stud_id]['stud_sex']];
		//$data_arr[$stud_id]['stud_birth_place']=$birth_state_arr[$data_arr[$stud_id]['stud_birth_place']];
		$data_arr[$stud_id]['stud_birth_place']=$birth_state_arr[sprintf('%02d',$res->fields['stud_birth_place'])];

		//加入學校抬頭
		$data_arr[$stud_id]['school_long_name']=$school_long_name;

		//照片  http://localhost/sfs3/data/photo/student/90/90002
		$stud_photo_file="$UPLOAD_PATH/photo/student/$stud_study_year/$stud_id";
		if(file_exists($stud_photo_file)){
			$data_arr[$stud_id]['photo']="<img src='$UPLOAD_URL//photo/student/$stud_study_year/$stud_id' width=120>";
		} else {
			$data_arr[$stud_id]['photo']='';
		}
//echo "<textarea rows=50 cols=80>".$data_arr[$stud_id]["photo"]."</textarea>";
		$res->MoveNext();
	}
//print_r($student_sn_arr);
	

	//取得新生入學的紀錄(這個部份  對於轉學生可能會有問題)
	$student_sn_list=substr($student_sn_list,0,-1);
	//抓取學年班級導師陣列
	$class_teacher_arr=array();
	$sql="select class_id,teacher_1 from school_class";
	$res=$CONN->Execute($sql) or user_error("讀取school_class資料失敗！<br>$sql",256);
	while(!$res->EOF) {
		$teacher_class_id=$res->fields['class_id'];
		$class_teacher_arr[$teacher_class_id]=$res->fields['teacher_1'];
		$res->MoveNext();
	}
	
	//取得歷年就讀班級、座號與導師
	//在本校的學期編班紀錄
	$sql="select stud_id,seme_year_seme,seme_class,left(seme_class,1) as grade,right(seme_year_seme,1) as semester,seme_num,seme_class_name from stud_seme where stud_id in ($stud_id_list) and seme_year_seme>='$min_year_seme' and seme_year_seme<='$max_year_seme'";
	$res=$CONN->Execute($sql) or user_error("讀取stud_seme資料失敗！<br>$sql",256);
	while(!$res->EOF) {
		//組成school_class格式的class_id
		$stud_class_id=sprintf("%03d_%d_%02d_%02d",substr($res->fields['seme_year_seme'],0,3),substr($res->fields['seme_year_seme'],-1),$res->fields['grade'],substr($res->fields['seme_class'],-2));
		$stud_id=$res->fields['stud_id'];
		$grade=$res->fields['grade'];
		$semester=$res->fields['semester'];
		$k=$grade.'-'.$semester;
		
		$data_arr[$stud_id]['class'][$grade][$semester]['semester']=$k;
		$data_arr[$stud_id]['class'][$grade][$semester]['name']=$class_name_kind_1[$grade].'年'.$res->fields['seme_class_name'].'班';
		$data_arr[$stud_id]['class'][$grade][$semester]['seme_num']=$res->fields['seme_num'];
		$data_arr[$stud_id]['class'][$grade][$semester]['teacher']=$class_teacher_arr[$stud_class_id];
		
		//產生選取學生學期年級對照陣列 以便後面輔導訪談記錄使用
		$stud_grade[$stud_id][$res->fields['seme_year_seme']]=$grade;

		$res->MoveNext();
	}
	
	
	//轉入匯入的學期編班紀錄
	//限制入學期間的資料 2012.09.14 by smallduh
	//$sql="select stud_id,seme_year_seme,seme_class_grade,seme_class_grade as grade,seme_num,seme_class_name from stud_seme_import where stud_id in ($stud_id_list)";
	$sql="select stud_id,seme_year_seme,seme_class_grade,seme_class_grade as grade,seme_num,seme_class_name from stud_seme_import where stud_id in ($stud_id_list) and seme_year_seme>='$min_year_seme' and seme_year_seme<='$max_year_seme'";
	$res=$CONN->Execute($sql) or user_error("讀取stud_seme_import資料失敗！<br>$sql",256);
	while(!$res->EOF) {
		//組成school_class格式的class_id
		//$stud_class_id=sprintf("%03d_%d_%02d_%02d",substr($res->fields['seme_year_seme'],0,3),substr($res->fields['seme_year_seme'],-1),$res->fields['grade'],substr($res->fields['seme_class'],-2));
		$stud_class_id=sprintf("%03d_%d_%02d_%02d",substr($res->fields['seme_year_seme'],0,3),substr($res->fields['seme_year_seme'],-1),$res->fields['grade'],0);
		$stud_id=$res->fields['stud_id'];
		$grade=$res->fields['grade'];
		$year=substr($res->fields['seme_year_seme'],0,3);
		$semester=substr($res->fields['seme_year_seme'],-1);
		$k=$grade.'_'.$semester;

		$data_arr[$stud_id]['class'][$grade][$semester]['semester']=$k;
		$data_arr[$stud_id]['class'][$grade][$semester]['name']=$class_name_kind_1[$grade].'年'.$res->fields['seme_class_name'].'班';
		$data_arr[$stud_id]['class'][$grade][$semester]['seme_num']=$res->fields['seme_num'];
		
		$sql_teacher_name="SELECT teacher_name FROM stud_seme_import WHERE stud_id='$stud_id' AND seme_class_grade='$grade' AND seme_year_seme='".$res->fields['seme_year_seme']."';";
		$res_teacher_name=$CONN->Execute($sql_teacher_name);
		$data_arr[$stud_id]['class'][$grade][$semester]['teacher']=$res_teacher_name->fields['teacher_name'];

		$stud_semester='grade_'.$grade.'_'.$semester;
		$data_arr[$stud_id][$stud_semester]=$year+0;

		//產生選取學生學期年級對照陣列
		$stud_grade[$stud_id][$res->fields['seme_year_seme']]=$grade;
		$stud_grade_semester[$stud_id][$k]=$res->fields['seme_year_seme'];

		$res->MoveNext();
	}
	
	
	//處理心理測驗(新表)

	$sql="select student_sn,item,test_date,score,model,standard,pr,explanation from stud_psy_test where student_sn in ($student_sn_list) order by student_sn,year,semester";
	$res=$CONN->Execute($sql) or user_error("讀取stud_psy_test資料失敗！<br>$sql",256);
	$no=0;
	$current_sn=0;
	while(!$res->EOF) {
		if($current_sn<>$res->fields['student_sn']){
			$no=1;
			$current_sn=$res->fields['student_sn'];
		} else $no++;
		
		$stud_id=$student_sn_arr[$current_sn];
		
		$date_temp_arr = explode("-",DtoCh($res->fields['test_date']));
		
		$data_arr[$stud_id]['psy'][$no]['item']=$res->fields['item'];
		if($res->fields['test_date']) $data_arr[$stud_id]['psy'][$no]['date']=sprintf("%d/%02d/%02d",$date_temp_arr[0],$date_temp_arr[1],$date_temp_arr[2]);
		$data_arr[$stud_id]['psy'][$no]['score']=$res->fields['score'];
		$data_arr[$stud_id]['psy'][$no]['model']=$res->fields['model'];
		$data_arr[$stud_id]['psy'][$no]['standard']=$res->fields['standard'];
		$data_arr[$stud_id]['psy'][$no]['pr']=$res->fields['pr'];
		$data_arr[$stud_id]['psy'][$no]['explanation']=$res->fields['explanation'];
		$res->MoveNext();
	}
	//$sql="select a.*,b.name as teacher from stud_seme_talk a LEFT JOIN teacher_base b ON a.teach_id=b.teacher_sn WHERE a.stud_id in ($stud_id_list) order by a.stud_id,a.seme_year_seme,a.sst_date";
	//改為限制入學期間的同 stud_id 的資料 2012.09.14 by smallduh
	//$sql="select * from stud_seme_talk where stud_id in ($stud_id_list) order by stud_id,seme_year_seme,sst_date;";
	$sql="select * from stud_seme_talk where stud_id in ($stud_id_list) and seme_year_seme>='$min_year_seme' and seme_year_seme<='$max_year_seme' order by stud_id,seme_year_seme,sst_date;";
	$res=$CONN->Execute($sql) or user_error("讀取stud_seme_talk資料失敗！<br>$sql",256);
	$no=0;
	$current_id='';
	while(!$res->EOF) {
		$stud_id=$res->fields['stud_id'];
		if($current_id<>$stud_id){
			$no=1;
			$current_id=$stud_id;
		} else $no++;
		
		$date_temp_arr = explode("-",DtoCh($res->fields['sst_date']));
		
		$data_arr[$stud_id]['guid'][$no]['grade']=$class_name_kind_1[$stud_grade[$stud_id][$res->fields['seme_year_seme']]];
		$data_arr[$stud_id]['guid'][$no]['date']=sprintf("%d/%02d/%02d",$date_temp_arr[0],$date_temp_arr[1],$date_temp_arr[2]);
		$data_arr[$stud_id]['guid'][$no]['sst_name']=$res->fields['sst_name'];
		$data_arr[$stud_id]['guid'][$no]['sst_main']=$res->fields['sst_main'];
		$data_arr[$stud_id]['guid'][$no]['sst_memo']=$res->fields['sst_memo'];
		//$data_arr[$stud_id]["guid_teacher_$no"]=get_teacher_name($res->fields['teach_id']);  //舊的抓取建檔者  20110503改下面為~~~訪談者
		$data_arr[$stud_id]['guid'][$no]['interview']=$res->fields['interview'];
		
		//假使未找到輔導教師，則往轉學匯入的資料表尋找
		if(! $data_arr[$stud_id]['guid'][$no]['interview']) {
			$seme_year_seme=$res->fields['seme_year_seme'];
			$sql_teacher="select teacher_name from stud_seme_import WHERE stud_id='$stud_id' and seme_year_seme='$seme_year_seme';";
			$res_teacher=$CONN->Execute($sql_teacher);
			$data_arr[$stud_id]['guid'][$no]['interview']=$res_teacher->fields['teacher_name'];
		}

		$res->MoveNext();
	}
	
	//處理特殊表現紀錄
	//$sql="select a.*,b.name as teacher from stud_seme_spe a,teacher_base b where a.stud_id in ($stud_id_list) and a.teach_id=b.teacher_sn order by a.stud_id,a.seme_year_seme";
	//改為限制入學期間的同 stud_id 的資料 2012.09.14 by smallduh
	$sql="select a.*,b.name as teacher from stud_seme_spe a,teacher_base b where a.stud_id in ($stud_id_list) and a.seme_year_seme>='$min_year_seme' and a.seme_year_seme<='$max_year_seme' and a.teach_id=b.teacher_sn order by a.stud_id,a.seme_year_seme";
	$res=$CONN->Execute($sql) or user_error("讀取stud_seme_spe資料失敗！<br>$sql",256);
	$no=0;
	$current_id='';
	while(!$res->EOF) {
		$stud_id=$res->fields['stud_id'];
		if($current_id<>$stud_id){
			$no=1;
			$current_id=$stud_id;
		} else $no++;
		
		$date_temp_arr = explode("-",DtoCh($res->fields['sp_date']));
		
		$data_arr[$stud_id]['sp'][$no]["grade"]=$class_name_kind_1[$stud_grade[$stud_id][$res->fields['seme_year_seme']]];
		$data_arr[$stud_id]['sp'][$no]["sp_semester"]=$res->fields['seme_year_seme'];
		$data_arr[$stud_id]['sp'][$no]["sp_date"]=sprintf("%d/%02d/%02d",$date_temp_arr[0],$date_temp_arr[1],$date_temp_arr[2]);
		$data_arr[$stud_id]['sp'][$no]["sp_memo"]=$res->fields['sp_memo'];
		$data_arr[$stud_id]['sp'][$no]["sp_teacher"]=$res->fields['teacher'];
			
		$res->MoveNext();
	}
	
	
	//畢業年月與升入學校
	//先產生一個空陣列  以免尚未畢業出現標籤
	foreach($sel_stud as $stud_id){
			$data_arr[$stud_id]["stud_grad_year"]='';
			$data_arr[$stud_id]["new_school"]='';
	}
//改以 student_sn 2012.09.14 by smallduh
	//$sql="select stud_id,stud_grad_year,new_school,YEAR(grad_date) as grade_year,MONTH(grad_date) as grade_month from grad_stud where stud_id in ($stud_id_list)";
	$sql="select stud_id,stud_grad_year,new_school,YEAR(grad_date) as grade_year,MONTH(grad_date) as grade_month from grad_stud where student_sn in ($student_sn_list)";
	$res=$CONN->Execute($sql) or user_error("讀取grad_stud資料失敗！<br>$sql",256);
	while(!$res->EOF) {
		$stud_id=$res->fields['stud_id'];
		$stud_grad_year=$res->fields['stud_grad_year'];
		$grade_month=$res->fields['grade_month'];
		$grade_year=($res->fields['grade_year'])-1911;
			
		$data_arr[$stud_id]["stud_grad_year"]=($grade_year?"$grade_year 年":"").($grade_month?" $grade_month 月":"");
		$data_arr[$stud_id]["new_school"]=$res->fields['new_school'];
			
		$res->MoveNext();
	}
	
	//處理輔導A表
  //改為限制入學期間的資料 2012.09.14 by smallduh
	//$query = "select * from stud_seme_eduh where stud_id in ($stud_id_list) order by seme_year_seme,stud_id";
$query = "select * from stud_seme_eduh where stud_id in ($stud_id_list) and seme_year_seme>='$min_year_seme' and seme_year_seme<='$max_year_seme' order by seme_year_seme,stud_id";
	$recordSet = $CONN->Execute($query) or user_error("讀取stud_seme_eduh資料失敗！<br>$query",256);
	while (!$recordSet->EOF) {
		$seme_year_seme=$recordSet->fields["seme_year_seme"];
		$stud_id = $recordSet->fields["stud_id"];
		$i=$stud_grade[$stud_id][$seme_year_seme];
		$j=substr($seme_year_seme,-1);
		$k=$i."_".$j;
		
		$data_arr[$stud_id]["eduh_relation_item"][$i][$j]=$recordSet->fields["sse_relation"];
		$data_arr[$stud_id]["eduh_kind_item"][$i][$j]=$recordSet->fields["sse_family_kind"];
		$data_arr[$stud_id]["eduh_air_item"][$i][$j]=$recordSet->fields["sse_family_air"];
		$data_arr[$stud_id]["eduh_father_item"][$i][$j]=$recordSet->fields["sse_farther"];
		$data_arr[$stud_id]["eduh_mother_item"][$i][$j]=$recordSet->fields["sse_mother"];
		$data_arr[$stud_id]["eduh_live_item"][$i][$j]=$recordSet->fields["sse_live_state"];
		$data_arr[$stud_id]["eduh_rich_item"][$i][$j]=$recordSet->fields["sse_rich_state"];
		
		$data_arr[$stud_id]["eduh_s1_item"][$i][$j]=substr(substr($recordSet->fields["sse_s1"],0,-1),1);
		$data_arr[$stud_id]["eduh_s2_item"][$i][$j]=substr(substr($recordSet->fields["sse_s2"],0,-1),1);
		$data_arr[$stud_id]["eduh_s3_item"][$i][$j]=substr(substr($recordSet->fields["sse_s3"],0,-1),1);
		$data_arr[$stud_id]["eduh_s4_item"][$i][$j]=substr(substr($recordSet->fields["sse_s4"],0,-1),1);
		$data_arr[$stud_id]["eduh_s5_item"][$i][$j]=substr(substr($recordSet->fields["sse_s5"],0,-1),1);
		$data_arr[$stud_id]["eduh_s6_item"][$i][$j]=substr(substr($recordSet->fields["sse_s6"],0,-1),1);
		$data_arr[$stud_id]["eduh_s7_item"][$i][$j]=substr(substr($recordSet->fields["sse_s7"],0,-1),1);
		$data_arr[$stud_id]["eduh_s8_item"][$i][$j]=substr(substr($recordSet->fields["sse_s8"],0,-1),1);
		$data_arr[$stud_id]["eduh_s9_item"][$i][$j]=substr(substr($recordSet->fields["sse_s9"],0,-1),1);
		$data_arr[$stud_id]["eduh_s10_item"][$i][$j]=substr(substr($recordSet->fields["sse_s10"],0,-1),1);
		$data_arr[$stud_id]["eduh_s11_item"][$i][$j]=substr(substr($recordSet->fields["sse_s11"],0,-1),1);
	
		$recordSet->MoveNext();
	}
		
	$student_data='';
	
	//開始產生網頁
	$year_title='';
	for($i=$min;$i<=$max;$i++){
		$year_title.="<td>{$class_year[$i]}</td>";
	}

	foreach($data_arr as $key=>$data){
		//抬頭
		$student_data.="<center><font size='5' face='標楷體'>$school_long_name 學生輔導資料紀錄表</font></center>";
	
		//基本資料
		$student_data.="<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size:9pt;' bordercolor='#111111' id='AutoNumber1' width=100%>";
		$student_data.="<tr align='center'><td bgcolor='#ffcccc'>姓　　名</td><td>{$data['stud_name']}</td><td bgcolor='#ffcccc'>性　　別</td><td>{$data['stud_sex']}</td><td bgcolor='#ffcccc'>學號</td><td>{$data['stud_id']}</td><td width=126 rowspan=6>{$data['photo']}</td></tr>";
		$student_data.="<tr align='center'><td bgcolor='#ffcccc'>入學年月</td><td>{$data['enroll_date']}</td><td bgcolor='#ffcccc'>入學學校</td><td colspan=3>{$data['enroll_school']}</td></tr>";

		$class_data="<table border=1 cellpadding=1 cellspacing='0' style='border-collapse: collapse; font-size:9pt;' bordercolor='#111111' id='AutoNumber1' width=100%>
			<tr align='center' bgcolor='#ddffff'><td>學期</td><td>班級</td><td>座號</td><td>導師姓名</td><td></td><td>學期</td><td>班級</td><td>座號</td><td>導師姓名</td></tr>";
		for($i=$min;$i<=$max;$i++){
			$class_data.="<tr align='center'><td>{$data['class'][$i][1]['semester']}</td><td>{$data['class'][$i][1]['name']}</td><td>{$data['class'][$i][1]['seme_num']}</td><td>{$data['class'][$i][1]['teacher']}</td><td></td><td>{$data['class'][$i][2]['semester']}</td><td>{$data['class'][$i][2]['name']}</td><td>{$data['class'][$i][2]['seme_num']}</td><td>{$data['class'][$i][2]['teacher']}</td></tr>";
		}
		$class_data.="</table>";
		$student_data.="<tr align='center'><td bgcolor='#ffcccc'>就<br>讀<br>班<br>級</td><td colspan=5>$class_data</td></tr></table>";
		
		//本人概況		
		$student_data.="<font size='3' face='標楷體'>一、本人概況
			<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size:9pt;' bordercolor='#111111' id='AutoNumber1' width=100%>
			<tr align='center'><td colspan=2 bgcolor='#ffcccc'>身分證字號</td><td colspan=3>{$data['stud_person_id']}</td></tr>
			<tr align='center'><td bgcolor='#ffcccc'>出生</td><td bgcolor='#ffcccc'>出生地</td><td>{$data['stud_birth_place']}</td><td bgcolor='#ffcccc'>生日</td><td>{$data['stud_birthday']}</td></tr>
			<tr align='center'><td rowspan=2 bgcolor='#ffcccc'>地址</td><td bgcolor='#ffcccc'>戶籍地址</td><td align='left'>{$data['stud_addr_1']}</td><td rowspan=2 bgcolor='#ffcccc'>電話</td><td>{$data['stud_tel_1']}</td></tr>
			<tr align='center'><td bgcolor='#ffcccc'>通訊地址</td><td align='left'>{$data['stud_addr_2']}</td><td>{$data['stud_tel_2']}</td></tr>
			</table>";

		//二、家庭狀況
		$student_data.="二、家庭狀況
			<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size:9pt;' bordercolor='#111111' id='AutoNumber1' width=100%>
			<tr align='center' bgcolor='#ffcccc'><td width=80>項　　目</td><td>內容選項</td>$year_title</tr>";
		$family_item_arr=array_slice($eduh_item_list_arr,0,7);
		foreach($family_item_arr as $item=>$value){
			$year_data='';
			for($i=$min;$i<=$max;$i++) $year_data.="<td>{$data[$item][$i][1]}<br>{$data[$item][$i][2]}</td>";
			$student_data.="<tr align='center'><td>{$value['title']}</td><td align='left'>{$value['items']}</td>$year_data</tr>";
		}
		$student_data.="</table>";
		
		//三、學習狀況
		$student_data.="三、學習狀況
		<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size:9pt;' bordercolor='#111111' id='AutoNumber1' width=100%>
		<tr align='center' bgcolor='#ffcccc'><td width=80>項　　目</td><td>內容選項</td>$year_title</tr>";
		$study_item_arr=array_slice($eduh_item_list_arr,7,4);
		foreach($study_item_arr as $item=>$value){
			$year_data='';
			for($i=$min;$i<=$max;$i++) $year_data.="<td>{$data[$item][$i][1]}<br>{$data[$item][$i][2]}</td>";
			$student_data.="<tr align='center'><td>{$value['title']}</td><td align='left'>{$value['items']}</td>$year_data</tr>";
		}
		$student_data.="</table>";
		
		
		//四、生活適應
		$student_data.="四、生活適應
		<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size:9pt;' bordercolor='#111111' id='AutoNumber1' width=100%>
		<tr align='center' bgcolor='#ffcccc'><td width=80>項　　目</td><td>內容選項</td>$year_title</tr>";
		$livily_item_arr=array_slice($eduh_item_list_arr,11);
		foreach($livily_item_arr as $item=>$value){
			$year_data='';
			for($i=$min;$i<=$max;$i++) $year_data.="<td>{$data[$item][$i][1]}<br>{$data[$item][$i][2]}</td>";
			$student_data.="<tr align='center'><td>{$value['title']}</td><td align='left'>{$value['items']}</td>$year_data</tr>";
		}
		$student_data.="</table>";
		
		
		//五、心理測驗
		$student_data.="五、心理測驗記錄
		<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size:9pt;' bordercolor='#111111' id='AutoNumber1' width=100%>
		<tr align='center' bgcolor='#ffcccc'><td>測驗名稱</td><td>測驗日期</td><td>原始分數</td><td>常模樣本</td><td>標準分數</td><td>百分等級</td><td>解釋</td></tr>";
		foreach($data['psy'] as $item=>$value){
			$student_data.="<tr align='center'><td>{$value['item']}</td><td>{$value['date']}</td><td>{$value['score']}</td><td>{$value['model']}</td><td>{$value['standard']}</td><td>{$value['pr']}</td><td>{$value['explanation']}</td></tr>";
		}
		$student_data.="</table>";
		
		
		//六、重要輔導紀錄  
		$student_data.="六、重要輔導記錄
		<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size:9pt;' bordercolor='#111111' id='AutoNumber1' width=100%>
		<tr align='center' bgcolor='#ffcccc'><td width=30>年級</td><td width=60>日期</td><td width=60>連絡對象</td><td>連絡事項</td><td>內容要點</td><td width=60>輔導者</td></tr>";
		foreach($data['guid'] as $item=>$value){
			$student_data.="<tr align='center'><td>{$value['grade']}</td><td>{$value['date']}</td><td>{$value['sst_name']}</td><td>{$value['sst_main']}</td><td align='left'>{$value['sst_memo']}</td><td>{$value['interview']}</td></tr>";
		}
		$student_data.="</table>";
		
		//七、特殊表現
		$data_arr[$stud_id]['sp'][$no]["grade"]=$class_name_kind_1[$stud_grade[$stud_id][$res->fields['seme_year_seme']]];
		$data_arr[$stud_id]['sp'][$no]["sp_semester"]=$res->fields['seme_year_seme'];
		$data_arr[$stud_id]['sp'][$no]["sp_date"]=sprintf("%d/%02d/%02d",$date_temp_arr[0],$date_temp_arr[1],$date_temp_arr[2]);
		$data_arr[$stud_id]['sp'][$no]["sp_memo"]=$res->fields['sp_memo'];
		$data_arr[$stud_id]['sp'][$no]["sp_teacher"]=$res->fields['teacher'];
		
		$student_data.="七、特殊表現記錄
		<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size:9pt;' bordercolor='#111111' id='AutoNumber1' width=100%>
		<tr align='center' bgcolor='#ffcccc'><td width=30>年級</td><td width=60>學期</td><td width=60>日期</td><td>表現事由</td><td width=60>記錄者</td></tr>";
		foreach($data['sp'] as $item=>$value){
			$student_data.="<tr align='center'><td>{$value['grade']}</td><td>{$value['sp_semester']}</td><td>{$value['sp_date']}</td><td align='left'>{$value['sp_memo']}</td><td>{$value['sp_teacher']}</td></tr>";
		}
		$student_data.="</table>";
		
		//八、畢業
		$student_data.="八、畢業
		<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size:9pt;' bordercolor='#111111' id='AutoNumber1' width=100%>
		<tr align='center'><td bgcolor='#ffcccc' width=60>畢業年月</td><td width=160>{$data['stud_grad_year']}</td><td bgcolor='#ffcccc' width=60>升入學校</td><td>{$data['new_school']}</td></tr></table>";
		
		//簽章
		$student_data.="<br>
		<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size:9pt;' bordercolor='#111111' id='AutoNumber1' width=100%>
		<tr align='center'><td bgcolor='#ffcccc' width=40>業務<br>承辦</td><td width=120></td><td bgcolor='#ffcccc' width=40>資料<br>組長</td><td width=120></td><td bgcolor='#ffcccc' width=40>輔導<br>主任</td><td width=120></td><td bgcolor='#ffcccc' width=40>校長</td><td></td></tr></table>";
		
		
		$student_data.="<P style='page-break-after:always'></P>";
	}
/*	
echo "<PRE>";
print_r($data_arr);
echo "</PRE>";
*/
echo $student_data;
exit;		
}

//選擇班級

head();

print_menu($menu_p);
echo <<<HERE
<script>
function tagall(status) {
  var i =0;

  while (i < document.myform.elements.length)  {
    if (document.myform.elements[i].name=='sel_stud[]') {
      document.myform.elements[i].checked=status;
    }
    i++;
  }
}
</script>
HERE;

echo "<form enctype='multipart/form-data' action=\"{$_SERVER['PHP_SELF']}\" method=\"post\" name=\"myform\">";
$sel1 = new drop_select();
$sel1->top_option =  "選擇學年";
$sel1->s_name = "year_seme";
$sel1->id = $_REQUEST[year_seme];
$sel1->is_submit = true;
$sel1->arr = get_class_seme();
$sel1->other_script="this.form.target='';";
$sel1->do_select();
 	 
echo "&nbsp;&nbsp;";
$sel1 = new drop_select();
$sel1->top_option =  "選擇班級";
$sel1->s_name = "class_id";
$sel1->id = $class_id;
$sel1->is_submit = true;
$sel1->arr = class_base($_REQUEST[year_seme]);
$sel1->other_script="this.form.target=''";
$sel1->do_select();

if($class_id<>'') {
 $query = "select a.stud_id,a.stud_name,b.seme_num,a.stud_study_cond from stud_base a , stud_seme b where a.student_sn=b.student_sn and b.seme_year_seme='$_REQUEST[year_seme]' and seme_class='$_REQUEST[class_id]' order by b.seme_num";
	$result = $CONN->Execute($query) or die ($query);
	if (!$result->EOF) {		
 		echo '&nbsp;<input type="button" value="全選" onClick="javascript:tagall(1);">';
 		echo '<input type="button" value="取消全選" onClick="javascript:tagall(0);">';
		echo "&nbsp;<input type='submit' name='do_key' value='$postBtn' onclick='this.form.target=\"$class_id\"'>";
		echo "<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size:9pt;' bordercolor='#111111' id='AutoNumber1'>";
		$ii=0;
		while (!$result->EOF) {
			$stud_id = $result->fields[stud_id];
			$stud_name = $result->fields[stud_name];
			$curr_class_num = sprintf('%02d',$result->fields[seme_num]);
			$stud_study_cond = $result->fields[stud_study_cond];
			$move_kind ='';
			if ($stud_study_cond >0)
				$move_kind= "<font color=red>(".$move_kind_arr[$stud_study_cond].")</font>";

			if ($ii %2 ==0)
				$tr_class = "class=title_sbody1";
			else
				$tr_class = "class=title_sbody2";
			
			if ($ii % 5 == 0)
				echo "<tr $tr_class >";
			echo "<td ><input id=\"c_$stud_id\" type=\"checkbox\" name=\"sel_stud[]\" value=\"$stud_id\"><label for=\"c_$stud_id\">$curr_class_num. $stud_name $move_kind</label></td>\n";
				
			if ($ii % 5 == 4)
				echo "</tr>";
			$ii++;
			$result->MoveNext();
		}
		echo"</table>";
	}
}

echo "<ul>
<li>
<p style='margin-top: 3; margin-bottom: 0'><font size='2' color='#3333CC'>
注意事項：</font></p>
<p style='margin-top: 3; margin-bottom: 0'><font color='#3333CC'>
<span style='font-family: 新細明體'><font size='2'>※</font></span><font size='2'>網頁式輔導記錄表係參照自95國小A4格式。</font></font></p>
<p style='margin-top: 3; margin-bottom: 0'><font color='#3333CC'>
<span style='font-family: 新細明體'><font size='2'>※</font></span><font size='2'>本版本不限制訪談記錄筆數與字數。</font></font></p>
<p style='margin-top: 3; margin-bottom: 0'><font color='#3333CC'>
<span style='font-family: 新細明體'><font size='2'>※</font></span><font size='2'>本版本增加特殊表現記錄。</font></font></p>
</li>
</ul>";

foot();



function get_eduh_item_list() {
	$edu_item_arr['eduh_relation_item']='父母關係';
	$edu_item_arr['eduh_kind_item']='家庭類型';
	$edu_item_arr['eduh_air_item']='家庭氣氛';
	$edu_item_arr['eduh_father_item']='管教方式';
	$edu_item_arr['eduh_mother_item']='管教方式';
	$edu_item_arr['eduh_live_item']='居住情形';
	$edu_item_arr['eduh_rich_item']='經濟狀況';
	$edu_item_arr['eduh_s1_item']='喜愛困難科目';
	$edu_item_arr['eduh_s2_item']='喜愛困難科目';
	$edu_item_arr['eduh_s3_item']='特殊才能';
	$edu_item_arr['eduh_s4_item']='興趣';
	$edu_item_arr['eduh_s5_item']='生活習慣';
	$edu_item_arr['eduh_s6_item']='人際關係';
	$edu_item_arr['eduh_s7_item']='外向行為';
	$edu_item_arr['eduh_s8_item']='內向行為';
	$edu_item_arr['eduh_s9_item']='學習行為';
	$edu_item_arr['eduh_s10_item']='不良習慣';
	$edu_item_arr['eduh_s11_item']='焦慮行為';
	
	foreach($edu_item_arr as $key=>$value){
		$result_arr=array();
		$result_arr=sfs_text($value);
		$data_list='';
		foreach($result_arr as $key2=>$value2)	$data_list.="$key2.$value2 ";
		$eduh_item_list[$key]['title']=$value;
		$eduh_item_list[$key]['items']=$data_list;	
	}
	return $eduh_item_list;
}


?>
