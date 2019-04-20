<?php

// $Id: index2.php 7843 2014-01-07 05:56:23Z infodaes $

include "report_config.php";
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


//處理上傳自訂的格式
if($_POST['do_key']=='上傳') {
        $default_filename='myown_guidance.sxw';
		$is_win=ereg('win', strtolower($_SERVER['SERVER_SOFTWARE']))?true:false;
        //利用score_paper模組裡已經有的unzip.exe
		$zipfile=($is_win)?"$SFS_PATH/modules/score_paper/UNZIP32.EXE":"/usr/bin/unzip";

        $arg1=($is_win)?"START /min cmd /c ":"";
        $arg2=($is_win)?"-d":"-d";

        if($_FILES['myown']['type'] == "application/vnd.sun.xml.writer"){
                $filename=$default_filename;
        }elseif(strtolower(substr($_FILES['myown']['name'],-3))=="sxw"){
                $filename=$default_filename;
        }else{
                die("請上傳sxw類型檔案!!");
        }

        if (!is_dir($UPLOAD_PATH)) {
                die("上傳目錄 $UPLOAD_PATH 不存在！");
        }


        //統一上傳目錄
        $upath=$UPLOAD_PATH."stud_report";
        if (!is_dir($upath)) {
                mkdir($upath) or die($upath."建立失敗！");
        }

        //上傳目的地
		$todir=$upath;
		$the_file=$todir.'/'.$filename;
		copy($_FILES['myown']['tmp_name'],$the_file);
        unlink($_FILES['myown']['tmp_name']);

		$todir=$upath."/guidance/";
        if (is_dir($todir)) {
                deldir($todir);
        } else { mkdir($todir) or die($todir."目的目錄建立失敗！"); }
       

        if (!file_exists($zipfile)) {
                die($zipfile."不存在！");
        }elseif(!file_exists($the_file)) {
                die($the_file."不存在！");
        }

        $cmd=$arg1." ".$zipfile." ".$the_file." ".$arg2." ".$todir;
        exec($cmd,$output,$rv);
}

$postBtn = "確定";
$template=$_POST[template];
$sel_stud=$_POST[sel_stud];
//個資記錄
//班級陣列
$class_arr = class_base();
//使用者選取的學生
$stud_id_list=implode(',',$sel_stud);

if ($_REQUEST[year_seme]=='')
  	         $_REQUEST[year_seme] = sprintf("%03d%d",curr_year(),curr_seme());

//不需iconv 轉換陣列
$no_iconv_arr = array();




if (count($sel_stud) >0 )
//個資記錄
//學期
$year_seme=$_POST['year_seme'];
//班級(先取得SFS3中的內定班級代碼例如101,再轉換成學校自訂名稱例一年甲班)
$class_id=$_POST['class_id'];
$class_name=$class_arr[$class_id];
	

switch($do_key) {
	case $postBtn:
	//個資記錄
	$test=pipa_log("印輔導記錄表\r\n學期：$year_seme\r\n班級：$class_id $class_name\r\n樣式：$template\r\n學生列表：$stud_id_list");	
	if(substr($template,0,5)=='tcc95' or substr($template,-8)=='guidance')
	{		
		$min=1+$IS_JHORES;
		$max=6+$IS_JHORES;
		//出生地
		$birth_state_arr = birth_state();
		//性別
		$sex_arr = array("1"=>"男","2"=>"女");	
		
		//產生輔導記錄A表選項參照陣列
		$eduh_item_list_arr=get_eduh_item_list();

		//取得選定學生流水號準備資料
		$data_arr=array();
		$stud_id_list=implode(',',$sel_stud);
		//取得stud_base基本資料
		$sql="select student_sn,stud_id,stud_name,stud_sex,stud_study_year,stud_person_id,stud_birth_place,stud_addr_1,stud_addr_2,stud_birthday,stud_tel_1,stud_tel_2,enroll_school from stud_base where stud_id in ($stud_id_list) order by curr_class_num";

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

			$data_arr[$stud_id]['stud_birth_place']=$birth_state_arr[sprintf('%02d',$res->fields['stud_birth_place'])];

			//加入學校抬頭
			$data_arr[$stud_id]['school_long_name']=$school_long_name;

			//照片  http://localhost/sfs3/data/photo/student/90/90002
			//原SXW內的XML定義==>  <draw:image draw:style-name="fr1" draw:name="Graphic1" text:anchor-type="as-char" svg:width="1.956cm" svg:height="2.565cm" draw:z-index="0" xlink:href="#Pictures/sample.jpg" xlink:type="simple" xlink:show="embed" xlink:actuate="onLoad"/>
			$stud_photo_file="$UPLOAD_PATH/photo/student/$stud_study_year/$stud_id";
	//echo "<BR>".$stud_photo_file.'====>'.file_exists($stud_photo_file);
			if(file_exists($stud_photo_file)){
				$data_arr[$stud_id]['photo']="$stud_id.jpg";
				//'<draw:image draw:style-name="fr1" draw:name="Graphic1" text:anchor-type="as-char" svg:width="1.956cm" svg:height="2.565cm" draw:z-index="0" xlink:href="#Pictures/'.$stud_id.'.jpg" xlink:type="simple" xlink:show="embed" xlink:actuate="onLoad"/>';
	
			} else {
				$data_arr[$stud_id]['photo']='sample.jpg';
				//'<draw:image draw:style-name="fr1" draw:name="Graphic1" text:anchor-type="as-char" svg:width="1.956cm" svg:height="2.565cm" draw:z-index="0" xlink:href="#Pictures/sample.jpg" xlink:type="simple" xlink:show="embed" xlink:actuate="onLoad"/>';
			}
//echo "<textarea rows=50 cols=80>".$data_arr[$stud_id]["photo"]."</textarea>";
			$res->MoveNext();
		}
//print_r($student_sn_arr);
		

		//取得新生入學的紀錄(這個部份  對於轉學生可能會有問題)
		//先產生一個空陣列  以免系統未設定出現標籤

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
		//先產生一個空陣列  以免系統未設定出現標籤
		$stud_grade=array();

		foreach($sel_stud as $stud_id){
			for($i=$min;$i<=$max;$i++){
				for($j=1;$j<=2;$j++){
					$k=$i.'_'.$j;
					$data_arr[$stud_id]["class_$k"]='';
					$data_arr[$stud_id]["seme_num_$k"]='';
					$data_arr[$stud_id]["teacher_$k"]='';
					
					//避免轉學生無法呈現，先以入學年推算就讀學年
					$defaule_seme_year_seme=sprintf('%03d%1d',$data_arr[$stud_id]["stud_study_year"]+$i-$IS_JHORES-1,$j);
					$stud_grade[$stud_id][$defaule_seme_year_seme]=$i;
					$stud_grade_semester[$stud_id][$k]=$defaule_seme_year_seme;
				}
			}
		}

		
		
		//轉入匯入的學期編班紀錄
		$sql="select stud_id,seme_year_seme,seme_class_grade,seme_class_grade as grade,seme_num,seme_class_name from stud_seme_import where stud_id in ($stud_id_list)";
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
	
			$data_arr[$stud_id]["class_$k"]=$class_name_kind_1[$grade].'年'.$res->fields['seme_class_name'].'班';
			$data_arr[$stud_id]["seme_num_$k"]=$res->fields['seme_num'];
			
			$sql_teacher_name="SELECT teacher_name FROM stud_seme_import WHERE stud_id='$stud_id' AND seme_class_grade='$grade' AND seme_year_seme='".$res->fields['seme_year_seme']."';";
			$res_teacher_name=$CONN->Execute($sql_teacher_name);
			$data_arr[$stud_id]["teacher_$k"]=$res_teacher_name->fields['teacher_name'];

			$stud_semester='grade_'.$grade.'_'.$semester;
			$data_arr[$stud_id][$stud_semester]=$year+0;

			//產生選取學生學期年級對照陣列
			$stud_grade[$stud_id][$res->fields['seme_year_seme']]=$grade;
			$stud_grade_semester[$stud_id][$k]=$res->fields['seme_year_seme'];
	
			$res->MoveNext();
		}
		
		//在本校的學期編班紀錄
		$sql="select stud_id,seme_year_seme,seme_class,left(seme_class,1) as grade,right(seme_year_seme,1) as semester,seme_num,seme_class_name from stud_seme where stud_id in ($stud_id_list)";
		$res=$CONN->Execute($sql) or user_error("讀取stud_seme資料失敗！<br>$sql",256);
		while(!$res->EOF) {
			//組成school_class格式的class_id
			$stud_class_id=sprintf("%03d_%d_%02d_%02d",substr($res->fields['seme_year_seme'],0,3),substr($res->fields['seme_year_seme'],-1),$res->fields['grade'],substr($res->fields['seme_class'],-2));
			$stud_id=$res->fields['stud_id'];
			$grade=$res->fields['grade'];
			$semester=$res->fields['semester'];
			$k=$grade.'_'.$semester;
			
			$data_arr[$stud_id]["class_$k"]=$class_name_kind_1[$grade].'年'.$res->fields['seme_class_name'].'班';
			$data_arr[$stud_id]["seme_num_$k"]=$res->fields['seme_num'];
			$data_arr[$stud_id]["teacher_$k"]=$class_teacher_arr[$stud_class_id];
			
			//產生選取學生學期年級對照陣列 以便後面輔導訪談記錄使用
			$stud_grade[$stud_id][$res->fields['seme_year_seme']]=$grade;
			
			$res->MoveNext();
		}
		
		
		//處理心理測驗(新表)
		//先產生一個空陣列  以免系統未設定出現標籤
		foreach($sel_stud as $stud_id){
			for($i=1;$i<=20;$i++){
				$data_arr[$stud_id]["psy_item_$i"]='';
				$data_arr[$stud_id]["psy_test_date_$i"]='';
				$data_arr[$stud_id]["psy_score_$i"]='';
				$data_arr[$stud_id]["psy_model_$i"]='';
				$data_arr[$stud_id]["psy_standard_$i"]='';
				$data_arr[$stud_id]["psy_pr_$i"]='';
				$data_arr[$stud_id]["psy_explanation_$i"]='';
			}
		}
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
			
			$data_arr[$stud_id]["psy_item_$no"]=$res->fields['item'];
			if($res->fields['test_date']) $data_arr[$stud_id]["psy_test_date_$no"]=sprintf("%d/%02d/%02d",$date_temp_arr[0],$date_temp_arr[1],$date_temp_arr[2]);
			$data_arr[$stud_id]["psy_score_$no"]=$res->fields['score'];
			$data_arr[$stud_id]["psy_model_$no"]=$res->fields['model'];
			$data_arr[$stud_id]["psy_standard_$no"]=$res->fields['standard'];
			$data_arr[$stud_id]["psy_pr_$no"]=$res->fields['pr'];
			$data_arr[$stud_id]["psy_explanation_$no"]=$res->fields['explanation'];
			$res->MoveNext();
		}
/*
echo $sql;
echo "<PRE>";
print_r($data_arr);
echo "</PRE>";
exit;	
*/	
		//處理輔導訪談紀錄
		//先產生一個空陣列  以免系統未設定出現標籤
		foreach($sel_stud as $stud_id){
			for($i=1;$i<=20;$i++){
				$data_arr[$stud_id]["guid_grade_$i"]='';
				$data_arr[$stud_id]["guid_date_$i"]='';
				$data_arr[$stud_id]["guid_name_$i"]='';
				$data_arr[$stud_id]["guid_main_$i"]='';
				$data_arr[$stud_id]["guid_memo_$i"]='';
				$data_arr[$stud_id]["guid_teacher_$i"]='';
			}
		}
	
		//$sql="select a.*,b.name as teacher from stud_seme_talk a LEFT JOIN teacher_base b ON a.teach_id=b.teacher_sn WHERE a.stud_id in ($stud_id_list) order by a.stud_id,a.seme_year_seme,a.sst_date";
		$sql="select * from stud_seme_talk where stud_id in ($stud_id_list) order by stud_id,seme_year_seme,sst_date;";
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
			
			$data_arr[$stud_id]["guid_grade_$no"]=$class_name_kind_1[$stud_grade[$stud_id][$res->fields['seme_year_seme']]];
			$data_arr[$stud_id]["guid_date_$no"]=sprintf("%d/%02d/%02d",$date_temp_arr[0],$date_temp_arr[1],$date_temp_arr[2]);
			$data_arr[$stud_id]["guid_name_$no"]=$res->fields['sst_name'];
			$data_arr[$stud_id]["guid_main_$no"]=$res->fields['sst_main'];
			$data_arr[$stud_id]["guid_memo_$no"]=$res->fields['sst_memo'];
			//$data_arr[$stud_id]["guid_teacher_$no"]=get_teacher_name($res->fields['teach_id']);  //舊的抓取建檔者  20110503改下面為~~~訪談者
			$data_arr[$stud_id]["guid_teacher_$no"]=$res->fields['interview'];
			
			//假使未找到輔導教師，則往轉學匯入的資料表尋找
			//if(! $res->fields['teacher']) {
			if(! $data_arr[$stud_id]["guid_teacher_$no"]) {
				$seme_year_seme=$res->fields['seme_year_seme'];
				$sql_teacher="select teacher_name from stud_seme_import WHERE stud_id='$stud_id' and seme_year_seme='$seme_year_seme';";
				$res_teacher=$CONN->Execute($sql_teacher);
				$data_arr[$stud_id]["guid_teacher_$no"]=$res_teacher->fields['teacher_name'];
			}
	
			$res->MoveNext();
		}
		
		//處理特殊表現紀錄
		//先產生一個空陣列  以免系統未設定出現標籤
		foreach($sel_stud as $stud_id){
			for($i=1;$i<=10;$i++){
				$data_arr[$stud_id]["sp_date_$i"]='';
				$data_arr[$stud_id]["sp_memo_$i"]='';
				$data_arr[$stud_id]["sp_semester_$i"]='';
				$data_arr[$stud_id]["sp_teacher_$i"]='';
			}
		}
		$sql="select a.*,b.name as teacher from stud_seme_spe a,teacher_base b where a.stud_id in ($stud_id_list) and a.teach_id=b.teacher_sn order by a.stud_id,a.seme_year_seme";
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
			
			$data_arr[$stud_id]["sp_semester_$no"]=$res->fields['seme_year_seme'];
			$data_arr[$stud_id]["sp_date_$no"]=sprintf("%d/%02d/%02d",$date_temp_arr[0],$date_temp_arr[1],$date_temp_arr[2]);
			$data_arr[$stud_id]["sp_memo_$no"]=$res->fields['sp_memo'];
			$data_arr[$stud_id]["sp_teacher_$no"]=$res->fields['teacher'];
				
			$res->MoveNext();
		}
		
		
		//畢業年月與升入學校
		//先產生一個空陣列  以免尚未畢業出現標籤
		foreach($sel_stud as $stud_id){
				$data_arr[$stud_id]["stud_grad_year"]='';
				$data_arr[$stud_id]["new_school"]='';
		}

		$sql="select stud_id,stud_grad_year,new_school,YEAR(grad_date) as grade_year,MONTH(grad_date) as grade_month from grad_stud where stud_id in ($stud_id_list)";
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
		//先產生一個空陣列  以免尚未畢業卻出現標籤
		foreach($sel_stud as $stud_id){
			//加入輔導A表選項替換
			foreach($eduh_item_list_arr as $key=>$value) $data_arr[$stud_id]["$key"]=$value;

			for($i=1;$i<=9;$i++){
				for($j=1;$j<=2;$j++){
					$k=$i."_".$j;
					$data_arr[$stud_id]["eduh_relation_$k"]='';
					$data_arr[$stud_id]["eduh_kind_$k"]='';
					$data_arr[$stud_id]["eduh_air_$k"]='';
					$data_arr[$stud_id]["eduh_father_$k"]='';
					$data_arr[$stud_id]["eduh_mother_$k"]='';
					$data_arr[$stud_id]["eduh_live_$k"]='';
					$data_arr[$stud_id]["eduh_rich_$k"]='';
					
					$data_arr[$stud_id]["eduh_s1_$k"]='';
					$data_arr[$stud_id]["eduh_s2_$k"]='';
					$data_arr[$stud_id]["eduh_s3_$k"]='';
					$data_arr[$stud_id]["eduh_s4_$k"]='';
					$data_arr[$stud_id]["eduh_s5_$k"]='';
					$data_arr[$stud_id]["eduh_s6_$k"]='';
					$data_arr[$stud_id]["eduh_s7_$k"]='';
					$data_arr[$stud_id]["eduh_s8_$k"]='';
					$data_arr[$stud_id]["eduh_s9_$k"]='';
					$data_arr[$stud_id]["eduh_s10_$k"]='';
					$data_arr[$stud_id]["eduh_s11_$k"]='';
				}
			}
		}
	
		$query = "select * from stud_seme_eduh where stud_id in ($stud_id_list) order by seme_year_seme,stud_id";

		$recordSet = $CONN->Execute($query) or user_error("讀取stud_seme_eduh資料失敗！<br>$query",256);
		while (!$recordSet->EOF) {
			$seme_year_seme=$recordSet->fields["seme_year_seme"];
			$stud_id = $recordSet->fields["stud_id"];
			$i=$stud_grade[$stud_id][$seme_year_seme];
			$j=substr($seme_year_seme,-1);
			$k=$i."_".$j;
			
			$data_arr[$stud_id]["eduh_relation_$k"]=$recordSet->fields["sse_relation"];
			$data_arr[$stud_id]["eduh_kind_$k"]=$recordSet->fields["sse_family_kind"];
			$data_arr[$stud_id]["eduh_air_$k"]=$recordSet->fields["sse_family_air"];
			$data_arr[$stud_id]["eduh_father_$k"]=$recordSet->fields["sse_farther"];
			$data_arr[$stud_id]["eduh_mother_$k"]=$recordSet->fields["sse_mother"];
			$data_arr[$stud_id]["eduh_live_$k"]=$recordSet->fields["sse_live_state"];
			$data_arr[$stud_id]["eduh_rich_$k"]=$recordSet->fields["sse_rich_state"];
			
			$data_arr[$stud_id]["eduh_s1_$k"]=substr(substr($recordSet->fields["sse_s1"],0,-1),1);
			$data_arr[$stud_id]["eduh_s2_$k"]=substr(substr($recordSet->fields["sse_s2"],0,-1),1);
			$data_arr[$stud_id]["eduh_s3_$k"]=substr(substr($recordSet->fields["sse_s3"],0,-1),1);
			$data_arr[$stud_id]["eduh_s4_$k"]=substr(substr($recordSet->fields["sse_s4"],0,-1),1);
			$data_arr[$stud_id]["eduh_s5_$k"]=substr(substr($recordSet->fields["sse_s5"],0,-1),1);
			$data_arr[$stud_id]["eduh_s6_$k"]=substr(substr($recordSet->fields["sse_s6"],0,-1),1);
			$data_arr[$stud_id]["eduh_s7_$k"]=substr(substr($recordSet->fields["sse_s7"],0,-1),1);
			$data_arr[$stud_id]["eduh_s8_$k"]=substr(substr($recordSet->fields["sse_s8"],0,-1),1);
			$data_arr[$stud_id]["eduh_s9_$k"]=substr(substr($recordSet->fields["sse_s9"],0,-1),1);
			$data_arr[$stud_id]["eduh_s10_$k"]=substr(substr($recordSet->fields["sse_s10"],0,-1),1);
			$data_arr[$stud_id]["eduh_s11_$k"]=substr(substr($recordSet->fields["sse_s11"],0,-1),1);
		
			$recordSet->MoveNext();
		}
		
/*		
echo "<PRE>";
print_r($data_arr);
echo "</PRE>";
exit;		
*/
		//Openoffice檔案的路徑
		$oo_path = $template;
		//檔名
		$filename=$work_year_seme."輔導紀錄表_".$_REQUEST[year_seme]."_".$class_id.".sxw";
		//新增一個 zipfile 實例
		$ttt = new EasyZip;
		$ttt->setPath($oo_path);
		// 加入整個目錄
		//$ttt->addDir("META-INF");
		// 加入檔案
		//$ttt -> addFile("styles.xml");
		//$ttt -> addFile("meta.xml");
		//$ttt -> addFile("settings.xml");
		//加入 xml 檔案到 zip 中，共有五個檔案 
		//第一個參數為原始字串，第二個參數為 zip 檔案的目錄和名稱
		if (is_dir($oo_path)) { 
			if ($dh = opendir($oo_path)) { 
				while (($file = readdir($dh)) !== false) { 
					if($file=="." or $file==".." or $file=="content.xml" or $file=="Configurations2" or $file=="Thumbnails" or strtoupper(substr($file,-4))=='.SXW') {
						continue;
					}elseif(is_dir($oo_path."/".$file)){
						if ($dh2 = opendir($oo_path."/".$file)) { 
							while (($file2 = readdir($dh2)) !== false) { 
								if($file2=="." or $file2==".."){
									continue;
								}else{
									$data = $ttt->read_file($oo_path."/".$file."/".$file2);
									$ttt->add_file($data,$file."/".$file2);
								}
							} 
							closedir($dh2); 
						} 
					}else{
						$data = $ttt->read_file($oo_path."/".$file);
						$ttt->add_file($data,$file);
					}
				} 
				closedir($dh); 
			} 
		}
		
		//加入圖片到SXW檔案中
		foreach($sel_stud as $stud_id){
			$stud_study_year=$data_arr[$stud_id]["stud_study_year"];
			if($data_arr[$stud_id]["photo"]<>'sample.jpg'){
				$stud_photo_file="$UPLOAD_PATH/photo/student/$stud_study_year/$stud_id";
				//if(file_exists($stud_photo_file)){
					$data = $ttt->read_file($stud_photo_file);
					$ttt->add_file($data,"Pictures/$stud_id.jpg");
				//}
			}
		}
		
		//讀出 content.xml 
		$data = $ttt->read_file($oo_path."/content.xml");
		// 加入換頁 tag
		$data = str_replace("<office:automatic-styles>",'<office:automatic-styles><style:style style:name="BREAK_PAGE" style:family="paragraph" style:parent-style-name="Standard"><style:properties fo:break-before="page"/></style:style>',$data);
		//拆解 content.xml
		$arr1 = explode("<office:body>",$data);
		//檔頭
		$doc_head = $arr1[0]."<office:body>";
		$arr2 = explode("</office:body>",$arr1[1]);
		//資料內容
		$content_body = $arr2[0];
		//檔尾
		$doc_foot = "</office:body>".$arr2[1];
		$replace_data ="";
		
		foreach($data_arr as $key=>$temp_arr){
			$my_content_body=$content_body;
			//將學生照片換掉
			//if($temp_arr['photo']) $my_content_body=str_replace('sample.jpg',$temp_arr['photo'],$my_content_body);
			// change_temp 會將陣列中的 big5 轉為 UTF-8 讓 openoffice 可以讀出
			$replace_data.=$ttt->change_temp($temp_arr,$my_content_body,0);
			
			//$replace_data.="<text:p text:style-name=\"break_page\"/>";  //換頁
		}
		//讀出 XML 檔頭
		$replace_data =$doc_head.$replace_data.$doc_foot;
		// 加入 content.xml 到zip 中
		$ttt->add_file($replace_data,"content.xml");
		//產生 zip 檔
		$sss = & $ttt->file();
		//以串流方式送出 sxw
		header("Content-disposition: attachment; filename=$filename");
		header("Content-type: application/vnd.sun.xml.writer");
		//header("Pragma: no-cache");
				//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");
		header("Expires: 0");

		echo $sss;
		exit;
	}

	if($template=='ooo2'){
		$oo_path = $template;
		$ttt = new EasyZIP;
		$ttt->setPath($template);
		$break ="<text:p text:style-name=\"P14\"/>";
		$doc_head = $ttt->read_file (dirname(__FILE__)."/$oo_path/con_head");
		$doc_foot = $ttt->read_file(dirname(__FILE__)."/$oo_path/con_foot");
		$doc_main = $ttt->read_file(dirname(__FILE__)."/$oo_path/con_main");
		$doc_brother_sister = $ttt->read_file(dirname(__FILE__)."/$oo_path/brother_sister");
		$doc_sss_data = $ttt->read_file (dirname(__FILE__)."/$oo_path/sss_data");
		$doc_sse_list_memo = $ttt->read_file (dirname(__FILE__)."/$oo_path/sse_list_memo");
		$doc_sse_list_spe = $ttt->read_file (dirname(__FILE__)."/$oo_path/sse_list_spe");
		
		
		$ttt->addDir("META-INF");
		$ttt->addFile('settings.xml');
		$ttt->addFile('styles.xml');
		$ttt->addFile('meta.xml');

		//血型 
		$blood_arr = blood();
		//出生地
		$birth_state_arr = birth_state();
		//性別
		$sex_arr = array("1"=>"男","2"=>"女");	
		//存歿
		$is_live_arr = is_live();
		//與監護人關係
		$guardian_relation_arr = guardian_relation();
		//學歷
		$edu_kind_arr  = edu_kind();
		//學生身分別 
		$stud_kind_arr = stud_kind();
		//與監護人關係
		$guardian_relation_arr = guardian_relation();
		//稱謂
		$bs_calling_kind_arr = bs_calling_kind();
		//父母關係
		$sse_relation_arr = sfs_text("父母關係");
		while(list($id,$val)= each($sse_relation_arr))
			$sse_relation_str .= "$id-$val,";
		//家庭類型
		$sse_family_kind_arr = sfs_text("家庭類型");
		while(list($id,$val)= each($sse_family_kind_arr))
			$sse_family_kind_str .= "$id-$val,";
		//家庭氣氛
		$sse_family_air_arr = sfs_text("家庭氣氛");
		while(list($id,$val)= each($sse_family_air_arr))
			$sse_family_air_str .= "$id-$val,";
		//管教方式
		$sse_farther_arr = sfs_text("管教方式");
		while(list($id,$val)= each($sse_farther_arr))
			$sse_farther_str .= "$id-$val,";

		//居住情形
		$sse_live_state_arr = sfs_text("居住情形");
		while(list($id,$val)= each($sse_live_state_arr))
			$sse_live_state_str .= "$id-$val,";
		//經濟狀況
		$sse_rich_state_arr = sfs_text("經濟狀況");
		while(list($id,$val)= each($sse_rich_state_arr))
			$sse_rich_state_str .= "$id-$val,";

		$sse_arr= array("1"=>"喜愛困難科目","2"=>"喜愛困難科目","3"=>"特殊才能","4"=>"興趣","5"=>"生活習慣","6"=>"人際關係","7"=>"外向行為","8"=>"內向行為","9"=>"學習行為","10"=>"不良習慣","11"=>"焦慮行為");
		
		while(list($id,$val)= each($sse_arr)){
			$temp_sse_arr = sfs_text("$val");
			${"sse_arr_$id"} = $temp_sse_arr;
			$temp_str ='';
			while(list($idd,$vall)= each($temp_sse_arr))
				$temp_str .= "$idd-$vall,";
			${"sse_str_$id"} = $temp_str;
		}

		//列印時間
		$print_time = $now;
		

		$temp_arr["sch_cname"]= $sch_cname;

		$sql_select = "select a.*,b.fath_name,b.fath_birthyear,b.fath_alive,b.fath_education,b.fath_occupation,b.fath_unit,b.fath_phone,b.fath_work_name,b.fath_hand_phone,b.moth_name,b.moth_birthyear,moth_work_name,b.moth_alive,b.moth_education,b.moth_occupation,b.moth_unit,b.moth_phone,b.moth_hand_phone,b.guardian_name,b.guardian_relation,b.guardian_unit,b.guardian_hand_phone,b.guardian_phone,b.guardian_address,b.grandfath_name,b.grandfath_alive,b.grandmoth_name,b.grandmoth_alive  from stud_base a left join stud_domicile b on a.stud_id=b.stud_id  ";
		for ($ss=0;$ss < count ($sel_stud);$ss++)
			$temp_sel .= "'".$sel_stud[$ss]."',";
		$sql_select .= "where a.stud_id in (".substr($temp_sel,0,-1).") ";
		
		$sql_select .= " order by a.curr_class_num ";	
		$recordSet = $CONN->Execute($sql_select)or die ($sql_select);	
		$i =0;
		$data = '';

		while (!$recordSet->EOF) {
			$stud_id = $recordSet->fields["stud_id"];
			$student_sn = $recordSet->fields["student_sn"];
			$stud_name = $recordSet->fields["stud_name"];
			$stud_sex = $recordSet->fields["stud_sex"];
			$stud_birthday = $recordSet->fields["stud_birthday"];
			$stud_blood_type = $recordSet->fields["stud_blood_type"];
			$stud_birth_place = $recordSet->fields["stud_birth_place"];
			$stud_kind = $recordSet->fields["stud_kind"];
			$stud_country = $recordSet->fields["stud_country"];
			$stud_country_kind = $recordSet->fields["stud_country_kind"];
			$stud_person_id = $recordSet->fields["stud_person_id"];
			$stud_country_name = $recordSet->fields["stud_country_name"];
			$stud_addr_1= $recordSet->fields["stud_addr_1"];
			$stud_addr_2 = $recordSet->fields["stud_addr_2"];
			$stud_tel_1 = $recordSet->fields["stud_tel_1"];
			$stud_tel_2 = $recordSet->fields["stud_tel_2"];
			$stud_tel_3 = $recordSet->fields["stud_tel_3"];
			$stud_mail = $recordSet->fields["stud_mail"];
			$stud_class_kind = $recordSet->fields["stud_class_kind"];
			$stud_spe_kind = $recordSet->fields["stud_spe_kind"];
			$stud_spe_class_kind = $recordSet->fields["stud_spe_class_kind"];
			$stud_spe_class_id = $recordSet->fields["stud_spe_class_id"];
			$stud_preschool_status = $recordSet->fields["stud_preschool_status"];
			$stud_preschool_id = $recordSet->fields["stud_preschool_id"];
			$stud_preschool_name = $recordSet->fields["stud_preschool_name"];
			$stud_mschool_status = $recordSet->fields["stud_mschool_status"];
			$stud_mschool_id = $recordSet->fields["stud_mschool_id"];
			$stud_mschool_name = $recordSet->fields["stud_mschool_name"];
			$stud_study_year = $recordSet->fields["stud_study_year"];
			$curr_class_num = $recordSet->fields["curr_class_num"];
			$fath_name = $recordSet->fields["fath_name"];
			$fath_birthyear = $recordSet->fields["fath_birthyear"];
			$fath_alive = $recordSet->fields["fath_alive"];
			$fath_education = $recordSet->fields["fath_education"];
			$fath_occupation = $recordSet->fields["fath_occupation"];
			$fath_work_name = $recordSet->fields["fath_work_name"];
			$fath_unit = $recordSet->fields["fath_unit"];
			$fath_phone = $recordSet->fields["fath_phone"];		
			$fath_hand_phone = $recordSet->fields["fath_hand_phone"];
			$moth_name = $recordSet->fields["moth_name"];
			$moth_birthyear = $recordSet->fields["moth_birthyear"];
			$moth_alive = $recordSet->fields["moth_alive"];
			$moth_relation = $recordSet->fields["moth_relation"];
			$moth_education = $recordSet->fields["moth_education"];	
			$moth_occupation = $recordSet->fields["moth_occupation"];
			$moth_work_name = $recordSet->fields["moth_work_name"];
			$moth_unit = $recordSet->fields["moth_unit"];
			$moth_work_name = $recordSet->fields["moth_work_name"];
			$moth_phone = $recordSet->fields["moth_phone"];
			$moth_hand_phone = $recordSet->fields["moth_hand_phone"];
			$guardian_name = $recordSet->fields["guardian_name"];
			$guardian_phone = $recordSet->fields["guardian_phone"];
			$guardian_relation = $recordSet->fields["guardian_relation"];
			$guardian_unit = $recordSet->fields["guardian_unit"];
			$guardian_work_name = $recordSet->fields["guardian_work_name"];
			$guardian_hand_phone = $recordSet->fields["guardian_hand_phone"];
			$guardian_guardian_address = $recordSet->fields["guardian_address"];
			$grandfath_name = $recordSet->fields["grandfath_name"];
			$grandfath_alive = $recordSet->fields["grandfath_alive"];
			$grandmoth_name = $recordSet->fields["grandmoth_name"];
			$grandmoth_alive = $recordSet->fields["grandmoth_alive"];

			//學生身分別
			$stud_kind_temp='';
			$stud_kind_temp_arr = explode(",",$stud_kind);
			for ($iii=0;$iii<count($stud_kind_temp_arr);$iii++) {
				if ($stud_kind_temp_arr[$iii]<>'')
					$stud_kind_temp .= $stud_kind_arr[$stud_kind_temp_arr[$iii]].",";
			}
		
			$temp_arr["stud_kind"]= substr($stud_kind_temp,0,-1);
		
		
			//學生基本資料	
			$bir_temp_arr = explode("-",DtoCh($stud_birthday));		
			$temp_arr["stud_birthday"]=sprintf("民國%d年%d月%d日",$bir_temp_arr[0],$bir_temp_arr[1],$bir_temp_arr[2]);
			$temp_arr["stud_blood_type"]=$blood_arr[$stud_blood_type];
			$temp_arr["stud_sex"]=$sex_arr[$stud_sex];
			$temp_arr["stud_name"]=$stud_name;
			$temp_arr["stud_id"]=$stud_id;
			$temp_arr["study_begin_date"]=$study_begin_date;		
			$temp_arr["stud_person_id"]=$stud_person_id;
			$temp_arr["stud_birth_place"]=$birth_state_arr[sprintf("%02d",$stud_birth_place)];
			$temp_arr["curr_year"]= Num2CNum(substr($curr_class_num,0,1));
			$temp_arr["curr_class"] = $class_name[substr($curr_class_num,1,2)];
			$temp_arr["curr_num"] = intval(substr($curr_class_num,-2))."號";
			$temp_arr["sch_cname"] = $SCHOOL_BASE[sch_cname];
			$temp_arr["stud_addr_1"] = $stud_addr_1;
			$temp_arr["stud_addr_2"] = $stud_addr_2;
			$temp_arr["stud_tel_1"] = $stud_tel_1;
			$temp_arr["stud_tel_2"] = $stud_tel_2;
		
			//直系血親	
			$temp_arr[stud_parent] = "父: $fath_name($is_live_arr[$fath_alive])($fath_birthyear 生), 母:$moth_name($is_live_arr[$moth_alive])($moth_birthyear 生), 祖父:$grandfath_name($is_live_arr[$grandfath_alive]), 祖母:$grandmoth_name($is_live_arr[$grandmoth_alive])";
			
			//父母教育程度
			$temp_arr[stud_parent_edu]= "父 :$edu_kind_arr[$fath_education] ,母: $edu_kind_arr[$moth_education]";
			
			//監護人
			$temp_arr["aaaa"]= $guardian_name;
			$temp_arr["bbbb"]= $guardian_relation_arr[$guardian_relation];
			$temp_arr["cccc"]= $guardian_address;
			$temp_arr["dddd"]= "$guardian_phone $guardian_hand_phone";
			
			//家長
			$temp_arr[f_1]=$fath_name;
			$temp_arr[f_2]=$fath_occupation;
			$temp_arr[f_3]=$fath_work_name;
			$temp_arr[f_4]=$fath_unit;
			$temp_arr[f_5]=$fath_phone;
			$temp_arr[m_1]=$moth_name;
			$temp_arr[m_2]=$moth_occupation;
			$temp_arr[m_3]=$moth_work_name;
			$temp_arr[m_4]=$moth_unit;
			$temp_arr[m_5]=$moth_phone;
		
			$temp_arr[stud_study_year] = $stud_study_year;	
			//兄弟姐妹
			$query = "select * from stud_brother_sister where stud_id='$stud_id' order by bs_birthyear";
			$bs_res = $CONN->Execute($query);
			
			$bs_data = '';
			$bs_arr = array();
			if($bs_res->EOF) {
				$bs_arr[b_1] = "-";
				$bs_arr[b_2] = "-";
				$bs_arr[b_3] = "-";
				$bs_arr[b_4] = "-";
				$bs_data .= change_temp($bs_arr,array(),$doc_brother_sister);

			}
			else {
				while(!$bs_res->EOF){
					$bs_arr[b_1] = $bs_calling_kind_arr[$bs_res->fields[bs_calling]];
					$bs_arr[b_2] = $bs_res->fields[bs_name];
					$bs_arr[b_3] = $bs_res->fields[bs_gradu];
					$bs_arr[b_4] = $bs_res->fields[bs_birthyear];
					$bs_data .= change_temp($bs_arr,array(),$doc_brother_sister);
					$bs_res->MoveNext();
				}
			}
			$temp_arr[brother_sister] = $bs_data;

			//取得學生輔導資料
			$stud_seme_arr = array();
			$sql_select = "select seme_year_seme,stud_id,sse_relation,sse_family_kind,sse_family_air,sse_farther,sse_mother,sse_live_state,sse_rich_state,sse_s1,sse_s2,sse_s3,sse_s4,sse_s5,sse_s6,sse_s7,sse_s8,sse_s9,sse_s10,sse_s11 from stud_seme_eduh where stud_id='$stud_id' order by seme_year_seme";
			$res_seme = $CONN->Execute($sql_select);
			while(!$res_seme->EOF){
				$temp_seme = $res_seme->fields[seme_year_seme];
				$stud_seme_arr[$temp_seme][sse_relation] = $res_seme->fields[sse_relation];
				$stud_seme_arr[$temp_seme][sse_family_kind] = $res_seme->fields[sse_family_kind];
				$stud_seme_arr[$temp_seme][sse_family_air] = $res_seme->fields[sse_family_air];
				$stud_seme_arr[$temp_seme][sse_farther] = $res_seme->fields[sse_farther];
				$stud_seme_arr[$temp_seme][sse_mother] = $res_seme->fields[sse_mother];
				$stud_seme_arr[$temp_seme][sse_live_state] = $res_seme->fields[sse_live_state];
				$stud_seme_arr[$temp_seme][sse_rich_state] = $res_seme->fields[sse_rich_state];
				$stud_seme_arr[$temp_seme][sse_s1] = $res_seme->fields[sse_s1];
				$stud_seme_arr[$temp_seme][sse_s2] = $res_seme->fields[sse_s2];
				$stud_seme_arr[$temp_seme][sse_s3] = $res_seme->fields[sse_s3];
				$stud_seme_arr[$temp_seme][sse_s4] = $res_seme->fields[sse_s4];
				$stud_seme_arr[$temp_seme][sse_s5] = $res_seme->fields[sse_s5];
				$stud_seme_arr[$temp_seme][sse_s6] = $res_seme->fields[sse_s6];
				$stud_seme_arr[$temp_seme][sse_s7] = $res_seme->fields[sse_s7];
				$stud_seme_arr[$temp_seme][sse_s8] = $res_seme->fields[sse_s8];
				$stud_seme_arr[$temp_seme][sse_s9] = $res_seme->fields[sse_s9];
				$stud_seme_arr[$temp_seme][sse_s10] = $res_seme->fields[sse_s10];
				$stud_seme_arr[$temp_seme][sse_s11] = $res_seme->fields[sse_s11];
				$res_seme->MoveNext();
			}

		
			//父母關係
			$bs_data ='';
			$bs_arr = array();
			$sssss ='';	
			$no_iconv_arr[sse_list]=1; 
			$no_iconv_arr[sss_data] =1; //不需轉換
			$no_iconv_arr[sse_memo_list] =1; //不需轉換
			$no_iconv_arr[sse_list_spe] =1; //不需轉換
			$no_iconv_arr[brother_sister] =1; //不需轉換

			reset($stud_seme_arr);
			$sssss ='<text:p text:style-name="P8">';
			while(list($vid,$vval) = each($stud_seme_arr)){
				$this_year = (substr($vid,0,-1)-$stud_study_year)+1;
				$semester = substr($vid,-1);
				$seme_name = ($semester==1)?"上":"下";
				$seme_name = Num2CNum($this_year).$seme_name;
				$temp_ss = $seme_name.'(<text:span text:style-name="T6">'. $sse_relation_arr[$vval[sse_relation]]."</text:span>)";
				$sssss .= iconv("Big5","UTF-8//IGNORE",$temp_ss).", ";
			}
			$sssss .='</text:p>';

			$bs_arr[sse_kind] = "父母關係"; 
			$bs_arr[sse_detail] = $sse_relation_str; 
			$bs_arr[sse_list] = $sssss;
			$bs_data = change_temp($bs_arr,$no_iconv_arr,$doc_sss_data);
			
			//=================================
			reset($stud_seme_arr);
			$sssss ='<text:p text:style-name="P8">';
			while(list($vid,$vval) = each($stud_seme_arr)){
				$this_year = (substr($vid,0,-1)-$stud_study_year)+1;
				$semester = substr($vid,-1);
				$seme_name = ($semester==1)?"上":"下";
				$seme_name = Num2CNum($this_year).$seme_name;
				$temp_ss = $seme_name.'(<text:span text:style-name="T6">'. $sse_family_kind_arr[$vval[sse_family_kind]]."</text:span>)";
				$sssss .= iconv("Big5","UTF-8//IGNORE",$temp_ss).", ";
			}
			$sssss .='</text:p>';

			$bs_arr[sse_kind] = "家庭類型"; 
			$bs_arr[sse_detail] = $sse_family_kind_str; 
			$bs_arr[sse_list] = $sssss;
			$bs_data .= change_temp($bs_arr,$no_iconv_arr,$doc_sss_data);
		
			//=================================
			reset($stud_seme_arr);
			$sssss ='<text:p text:style-name="P8">';
			while(list($vid,$vval) = each($stud_seme_arr)){
				$this_year = (substr($vid,0,-1)-$stud_study_year)+1;
				$semester = substr($vid,-1);
				$seme_name = ($semester==1)?"上":"下";
				$seme_name = Num2CNum($this_year).$seme_name;
				$temp_ss = $seme_name.'(<text:span text:style-name="T6">'. $sse_family_air_arr[$vval[sse_family_kind]]."</text:span>)";
				$sssss .= iconv("Big5","UTF-8//IGNORE",$temp_ss).", ";
			}
			$sssss .='</text:p>';

			$bs_arr[sse_kind] = "家庭氣氛"; 
			$bs_arr[sse_detail] = $sse_family_air_str; 
			$bs_arr[sse_list] = $sssss;
			$bs_data .= change_temp($bs_arr,$no_iconv_arr,$doc_sss_data);
		
			//=================================
			reset($stud_seme_arr);
			$sssss ='<text:p text:style-name="P8">';
			while(list($vid,$vval) = each($stud_seme_arr)){
				$this_year = (substr($vid,0,-1)-$stud_study_year)+1;
				$semester = substr($vid,-1);
				$seme_name = ($semester==1)?"上":"下";
				$seme_name = Num2CNum($this_year).$seme_name;
				$temp_ss = $seme_name.'(<text:span text:style-name="T6">'. $sse_farther_arr[$vval[sse_farther]]."</text:span>)";
				$sssss .= iconv("Big5","UTF-8//IGNORE",$temp_ss).", ";
			}
			$sssss .='</text:p>';

			$bs_arr[sse_kind] = "父管教方式"; 
			$bs_arr[sse_detail] = $sse_farther_str; 
			$bs_arr[sse_list] = $sssss;
			$bs_data .= change_temp($bs_arr,$no_iconv_arr,$doc_sss_data);
		
			//=================================
			reset($stud_seme_arr);
			$sssss ='<text:p text:style-name="P8">';
			while(list($vid,$vval) = each($stud_seme_arr)){
				$this_year = (substr($vid,0,-1)-$stud_study_year)+1;
				$semester = substr($vid,-1);
				$seme_name = ($semester==1)?"上":"下";
				$seme_name = Num2CNum($this_year).$seme_name;
				$temp_ss = $seme_name.'(<text:span text:style-name="T6">'. $sse_farther_arr[$vval[sse_mother]]."</text:span>)";
				$sssss .= iconv("Big5","UTF-8//IGNORE",$temp_ss).", ";
			}
			$sssss .='</text:p>';

			$bs_arr[sse_kind] = "母管教方式"; 
			$bs_arr[sse_detail] = $sse_farther_str; 
			$bs_arr[sse_list] = $sssss;
			$bs_data .= change_temp($bs_arr,$no_iconv_arr,$doc_sss_data);
		
			reset($stud_seme_arr);
			$sssss ='<text:p text:style-name="P8">';
			while(list($vid,$vval) = each($stud_seme_arr)){
				$this_year = (substr($vid,0,-1)-$stud_study_year)+1;
				$semester = substr($vid,-1);
				$seme_name = ($semester==1)?"上":"下";
				$seme_name = Num2CNum($this_year).$seme_name;
				$temp_ss = $seme_name.'(<text:span text:style-name="T6">'. $sse_live_state_arr[$vval[sse_live_state]]."</text:span>)";
				$sssss .= iconv("Big5","UTF-8//IGNORE",$temp_ss).", ";
			}
			$sssss .='</text:p>';
			
			$bs_arr[sse_kind] = "居住情形"; 
			$bs_arr[sse_detail] = $sse_live_state_str; 
			$bs_arr[sse_list] = $sssss;
			$bs_data .= change_temp($bs_arr,$no_iconv_arr,$doc_sss_data);


			//=================================
			reset($stud_seme_arr);
			$sssss ='<text:p text:style-name="P8">';
			while(list($vid,$vval) = each($stud_seme_arr)){
				$this_year = (substr($vid,0,-1)-$stud_study_year)+1;
				$semester = substr($vid,-1);
				$seme_name = ($semester==1)?"上":"下";
				$seme_name = Num2CNum($this_year).$seme_name;
				$temp_ss = $seme_name.'(<text:span text:style-name="T6">'. $sse_rich_state_arr[$vval[sse_rich_state]]."</text:span>)";
				$sssss .= iconv("Big5","UTF-8//IGNORE",$temp_ss).", ";
			}
			$sssss .='</text:p>';

			$bs_arr[sse_kind] = "經濟狀況"; 
			$bs_arr[sse_detail] = $sse_rich_state_str; 
			$bs_arr[sse_list] = $sssss;
			$bs_data .= change_temp($bs_arr,$no_iconv_arr,$doc_sss_data);
		
			$temp_arr[sss_data] = $bs_data;

			//=================================
			$bs_data = '';
			for($si=1;$si<=11;$si++){

				reset($stud_seme_arr);			
				$sssss ='<text:p text:style-name="P8">';
				while(list($vid,$vval) = each($stud_seme_arr)){
					$temp_sse_arr = ${"sse_arr_$si"};
					$temp_str = ${"sse_str_$si"};
					$temp_id  = "sse_s$si";
					$this_year = (substr($vid,0,-1)-$stud_study_year)+1;
					$semester = substr($vid,-1);
					$seme_name = ($semester==1)?"上":"下";
					$seme_name = Num2CNum($this_year).$seme_name;
					$tt_arr = explode(",",$vval[$temp_id]);
					$temp_ss='';
					foreach ($tt_arr as $VAL){
						if ($VAL<>'')
							$temp_ss .= $temp_sse_arr[$VAL].",";
					}
					if($temp_ss<>'')
						$temp_ss = substr($temp_ss,0,-1);
					$temp_ss = $seme_name.'(<text:span text:style-name="T6">'.$temp_ss.'</text:span>)';
					$sssss .= iconv("Big5","UTF-8//IGNORE",$temp_ss).", ";
				}
				$sssss .='</text:p>';
				if($si==1)
					$bs_arr[sse_kind] = "最喜愛科目"; 
				else if($si==2)
					$bs_arr[sse_kind] = "最困難科目"; 
				else
					$bs_arr[sse_kind] = $sse_arr[$si]; 
				$bs_arr[sse_detail] = $temp_str; 
				$bs_arr[sse_list] = $sssss;
				$bs_data .= change_temp($bs_arr,$no_iconv_arr,$doc_sss_data);
			}
			$temp_arr[sss_data] .= $bs_data; //by misser,修改2 ,原本少 .，造成上面資料(家庭狀況等)未顯示
			
			//以下 by misser,修改3(新增) ,取得出缺席紀錄 ,原無
			//=================================
			if ($IS_JHORES==6){//國中，6學期
			$stud_seme_new_arr=array("$stud_study_year"."1","$stud_study_year"."2","$stud_study_year"+"1"."1","$stud_study_year"+"1"."2","$stud_study_year"+"2"."1","$stud_study_year"+"2"."2");
			}
			else{//應該是小學，所以有12學期
			$stud_seme_new_arr=array("$stud_study_year"."1","$stud_study_year"."2","$stud_study_year"+"1"."1","$stud_study_year"+"1"."2","$stud_study_year"+"2"."1","$stud_study_year"+"2"."2","$stud_study_year"+"3"."1","$stud_study_year"+"3"."2","$stud_study_year"+"4"."1","$stud_study_year"+"4"."2","$stud_study_year"+"5"."1","$stud_study_year"+"5"."2");
			}
			$bs_data = '';
			//reset($stud_seme_arr); 不用$stud_seme_arr，因為若某學期尚未建輔導紀錄，則
			//$stud_seme_arr就會少了某學期的資料，也就無法找出該學期的缺曠課。
			//所以用 上面的 $stud_seme_new_arr 代替。
			//以下獎懲亦同

			//取得假別
			$asb_arr=sfs_text("缺曠課類別");
			$asb_str="";
			while(list($id,$val)= each($asb_arr))
				$asb_str .= "$id-$val,";

			//取得absent資料表中出缺席紀錄
			$sssss ='<text:p text:style-name="P8">';
			$temp_ss='';
			//下面$vval及$vid 位置和原先是對調的
			while(list($vval,$vid) = each($stud_seme_new_arr)){//依學期別
				$year=(substr($vid,0,1)=='0')?substr($vid,1,-1):substr($vid,0,-1);
				$this_year = (substr($vid,0,-1)-$stud_study_year)+1;
				$semester = substr($vid,-1);
				$seme_name = ($semester==1)?"上":"下";
				$seme_name = Num2CNum($this_year).$seme_name;
				$temp_ss .= $seme_name.'(<text:span text:style-name="T6">';
				foreach ($asb_arr as $temp_kind){//再依假別尋找次數
				$sql_select = "select * from stud_absent where stud_id='$stud_id' and absent_kind='$temp_kind' and year='$year' and semester='$semester' order by year,semester";
				$record=$CONN->Execute($sql_select) or die($sql_select);
				$num=$record->RecordCount();
				if ($num>0){;//如果找到，則傳回假別次數
					$temp_ss.=$temp_kind.":".$num."節。 ";
				}
				}
				$temp_ss.='</text:span>)';

			}
			$sssss .= iconv("Big5","UTF-8//IGNORE",$temp_ss).", ";
			$sssss .='</text:p>';
			$bs_arr[sse_kind] = "缺曠課紀錄";
			$bs_arr[sse_detail] = $asb_str;
			$bs_arr[sse_list] = $sssss;
			$bs_data .= change_temp($bs_arr,$no_iconv_arr,$doc_sss_data);
			$temp_arr[sss_data] .= $bs_data;
			// by misser 修改3(新增) 結束

			//以下 by misser,修改4(新增) ,取得獎懲紀錄 ,原無
			//=================================
			$bs_data = '';
			reset($stud_seme_new_arr);
			//取得獎懲類別 ,取自 reward 模組的config.php
			$reward_arr=array("1"=>"嘉獎一次","2"=>"嘉獎二次","3"=>"小功一次","4"=>"小功二次","5"=>"大功一次","6"=>"大功二次","7"=>"大功三次","-1"=>"警告一次","-2"=>"警告二次","-3"=>"小過一次","-4"=>"小過二次","-5"=>"大過一次","-6"=>"大過二次","-7"=>"大過三次");
			$reward_str="";
			while(list($id,$val)= each($reward_arr))
				$reward_str .= "$id-$val,";

			//取得reward資料表中獎懲紀錄
			$sssss ='<text:p text:style-name="P8">';
			$temp_ss='';

			while(list($vval,$vid) = each($stud_seme_new_arr)){
				$year=(substr($vid,0,1)=='0')?substr($vid,1,-1):substr($vid,0,-1);
				$this_year = (substr($vid,0,-1)-$stud_study_year)+1;
				$semester = substr($vid,-1);
				$seme_name = ($semester==1)?"上":"下";
				$seme_name = Num2CNum($this_year).$seme_name;
				$temp_ss .= $seme_name.'(<text:span text:style-name="T6">';

				$sql_select = "select * from reward where stud_id='$stud_id' and reward_year_seme='$year$semester' order by reward_date";
				$re_record=$CONN->Execute($sql_select) or die($sql_select);

				while(!$re_record->EOF){
					$temp_ss.= $reward_arr[$re_record->fields[reward_kind]];
					$temp_ss.=":";
					$temp.=$re_record->fields[reward_reason];
					if ($re_record->fields[reward_cancel_date]!="" and $re_record->fields[reward_cancel_date]!="0000-00-00")
					$temp_ss.="**已銷過**";

					$temp_ss.="　,";
					$re_record->MoveNext();
				}
	
				$temp_ss.="</text:span>)";
			}
			$sssss .= iconv("Big5","UTF-8//IGNORE",$temp_ss).", ";
			$sssss .='</text:p>';
			$bs_arr[sse_kind] = "獎懲紀錄";
			$bs_arr[sse_detail] = $reward_str;
			$bs_arr[sse_list] = $sssss;
			$bs_data .= change_temp($bs_arr,$no_iconv_arr,$doc_sss_data);
			$temp_arr[sss_data] .= $bs_data;
			// by misser 修改4(新增) 結束


			$bs_data = '';
			//輔導訪談記錄
			$query = "select seme_year_seme,sst_date,sst_name,sst_main,sst_memo,teach_id from stud_seme_talk where stud_id='$stud_id' order by seme_year_seme";
			$res_talk = $CONN->Execute($query) or die($query);
			$memo_arr = array();
			while(!$res_talk->EOF){
				$memo_arr[w_2]= $res_talk->fields[sst_date];
				$memo_arr[w_3]= $res_talk->fields[sst_name];
				$memo_arr[w_4]= $res_talk->fields[sst_main].":".$res_talk->fields[sst_memo];
				$memo_arr[w_5]= get_teacher_name($res_talk->fields[teach_id]);
				$this_year = (substr($res_talk->fields[seme_year_seme],0,-1)-$stud_study_year)+1;
				$semester = substr($res_talk->fields[seme_year_seme],-1);
				$seme_name = ($semester==1)?"上":"下";
				$memo_arr[w_1] = Num2CNum($this_year).$seme_name;
				$bs_data .= change_temp($memo_arr,array(),$doc_sse_list_memo);
				$res_talk->MoveNext();
			}

			$temp_arr[sse_memo_list] = $bs_data;

			$bs_data = '';
			//特殊表現記錄
			$query = "select seme_year_seme,sp_date,sp_memo,teach_id from stud_seme_spe where stud_id='$stud_id' order by seme_year_seme";
			$res_talk = $CONN->Execute($query) or die($query);
			$memo_arr = array();
			while(!$res_talk->EOF){
				$memo_arr[s_2]= $res_talk->fields[sp_date];
				$memo_arr[s_3]= $res_talk->fields[sp_memo];
				$memo_arr[s_4]= get_teacher_name($res_talk->fields[teach_id]);
				$this_year = (substr($res_talk->fields[seme_year_seme],0,-1)-$stud_study_year)+1;
				$semester = substr($res_talk->fields[seme_year_seme],-1);
				$seme_name = ($semester==1)?"上":"下";
				$memo_arr[s_1] = Num2CNum($this_year).$seme_name;
				$bs_data .= change_temp($memo_arr,array(),$doc_sse_list_spe);
				$res_talk->MoveNext();
			}
			

			$temp_arr[sse_list_spe] = $bs_data;
			
			//入學學校 (尚未判斷國中小)
			$temp_arr["stud_mschool_name"]="";
			//畢業日期 (尚未判斷)
			$temp_arr["stud_grade_date"]="";
			//列印時間
			$temp_arr["print_time"]="列印時間: $now";
			$temp_arr["test_1"]="misser測試";
			//取代基本資料
			$data .= change_temp($temp_arr,$no_iconv_arr,$doc_main);
		
			$recordSet->MoveNext();	
			//換頁
			if (!$recordSet->EOF)
				$data .= $break;
		}
		$sss = $doc_head.$data.$doc_foot;
		$ttt->add_file($sss,"content.xml");

		header("Content-disposition: attachment; filename=$filename");
		header("Content-type: application/vnd.sun.xml.writer");
		//header("Pragma: no-cache");
				//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");
		header("Expires: 0");
		echo  $ttt->file();

		exit;	
		break;
	}
	
	

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
$sel1->do_select();
 	 
echo "&nbsp;&nbsp;";
$sel1 = new drop_select();
$sel1->top_option =  "選擇班級";
$sel1->s_name = "class_id";
$sel1->id = $class_id;
$sel1->is_submit = true;
$sel1->arr = class_base($_REQUEST[year_seme]);
$sel1->do_select();

if($class_id<>'') {
 $query = "select a.stud_id,a.stud_name,b.seme_num,a.stud_study_cond from stud_base a , stud_seme b where a.student_sn=b.student_sn and b.seme_year_seme='$_REQUEST[year_seme]' and seme_class='$_REQUEST[class_id]' order by b.seme_num";
	$result = $CONN->Execute($query) or die ($query);
	if (!$result->EOF) {
		
 		echo '&nbsp;<input type="button" value="全選" onClick="javascript:tagall(1);">&nbsp;';
 		echo '<input type="button" value="取消全選" onClick="javascript:tagall(0);">';
		echo "<table border=1>";
		$ii=0;
		while (!$result->EOF) {
			$stud_id = $result->fields[stud_id];
			$stud_name = $result->fields[stud_name];
			$curr_class_num = $result->fields[seme_num];
			$stud_study_cond = $result->fields[stud_study_cond];
			$move_kind ='';
			if ($stud_study_cond >0)
				$move_kind= "<font color=red>(".$move_kind_arr[$stud_study_cond].")</font>";
			if($stud_study_cond==5) {
					$query = "select grad_kind from grad_stud where student_sn=$student_sn";
					$rt = $CONN->Execute($query) or die ($query);
					if($rt->fields[0]==2) $move_kind= "<font color='#cccccc'>修業</font>";				
			}
				

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
		
		//檢查是否有上傳格式
		$myown_dir=$UPLOAD_PATH."stud_report/guidance";
		if(file_exists("$myown_dir/content.xml")) $myown_style="<option value='$myown_dir' selected>自訂上傳的格式";

		echo " Open Office 文件輸出(.sxw)：";
		echo "<select name='template'>$myown_style
		<option value='ooo2'".($template=='ooo2'?' selected':'').">傳統格式
		<option value='tcc95_guidance_ps'".($template=='tcc95_guidance_ps'?' selected':'').">95國小A4格式
		<option value='tcc95_guidance_jh'".($template=='tcc95_guidance_jh'?' selected':'').">95國中A4格式</select>";
		echo "<input type=\"submit\" name=\"do_key\" value=\"$postBtn\">";
		echo "<input type=\"hidden\" name=\"filename\" value=\"reg2_class{$class_id}.sxw\">";
		echo '<br><font color=green size=2><a href='.$UPLOAD_URL.'stud_report/myown_guidance.sxw>◎上傳自訂格式：</a><input type="file" name="myown"><input type="submit" name="do_key" value="上傳" onclick="if(this.form.myown.value) { return confirm(\'上傳後會將原上傳格式替換，您確定要這樣做嗎?\'); } else return false;"></font>';
		echo "<ul>
  <li>
  <p style='margin-top: 3; margin-bottom: 0'><font size='2' color='#3333CC'>
  注意事項：</font></p>
  <p style='margin-top: 3; margin-bottom: 0'><font color='#3333CC'>
  <span style='font-family: 新細明體'><font size='2'>※</font></span><font size='2'>95國小A4格式係台中縣政府於95學年度下學期期末所頒布之修訂版本。</font></font></p>
  <p style='margin-top: 3; margin-bottom: 0'><font color='#3333CC'>
  <span style='font-family: 新細明體'><font size='2'>※</font></span><font size='2'>該版本乃依據教育部新公佈之學籍輔導健康資料交換標準(XML 
  3.0)並整合縣內學校意見而來。</font></font></p>
  <p style='margin-top: 3; margin-bottom: 0'><font color='#3333CC'>
  <span style='font-family: 新細明體'><font size='2'>※</font></span><font size='2'>因教育部的新標準，在輔導資料表上變化較大，使用本格式列印前，學校必須確定~~</font></font></p>
  <p style='margin-top: 3; margin-bottom: 0'><font size='2' color='#3333CC'>
  　1.心理測驗統計已經做好XML 3.0 新標準的轉表工作。</font></p>
  <p style='margin-top: 3; margin-bottom: 0'><font size='2' color='#3333CC'>
  　2.輔導A表參照選項依照XML 3.0 標準更新，並自95學年度起入學學生改用新標準紀錄。</font></p>
  <p style='margin-top: 3; margin-bottom: 0'><font color='#3333CC'>
  <span style='font-family: 新細明體'><font size='2'>※</font></span><font size='2'>XML 
  3.0 新標準心理測驗統計轉表程式已經完成，模組為[ 全校學生輔導記錄 ]-[- &gt;&gt;轉表&gt;&gt; ]，模組英文名為... stud_eduh。</font></font></p>
  <p style='margin-top: 3; margin-bottom: 0'><font color='#3333CC'>
  <span style='font-family: 新細明體'><font size='2'>※</font></span><font size='2'>輔導A表參照選項批次轉換程式已完成，請進行轉換後再列印。</font></font></p>
  <p style='margin-top: 3; margin-bottom: 0'><font color='#3333CC'>
  <span style='font-family: 新細明體'><font size='2'>※</font></span><font size='2'>若未做好前述兩項準備工作，列印後不會得到正確的結果( 
  資料闕</font></font><font size='2' color='#3333CC'>漏或代碼錯誤 )。</font></p>
  <p style='margin-top: 3; margin-bottom: 0'><font color='#3333CC'>
  <span style='font-family: 新細明體'><font size='2'>※入學年月與入學學校抓取自學生異動紀錄；國民小學版本另須紀錄</font></span></font><font size=2 color='#3333CC'>畢業年月與升學學校，此兩項須正確做好紀錄方能正確顯示。</font></p>
  <p style='margin-top: 3; margin-bottom: 0'><font color='#3333CC'>
  <span style='font-family: 新細明體'><font size='2'>※新表格具有列印學生照片功能，未上傳學生照片，會以預設圖片取代。</font></span></font></p>
  <p align='center'><font color='#3333CC' size='2'>by infodaes.&nbsp; 2008/1/23</font></li>
</ul>";
	}

}



foot();




function change_temp($arr,$no_iconv_arr,$source) {
	global $ttt;
	$temp_str = $source;
	while(list($id,$val) = each($arr)){
		if (!$no_iconv_arr[$id])
			$val = $ttt->change_str($val);
//			$val =iconv("Big5","UTF-8//IGNORE",$val);
		$temp_str = str_replace("{".$id."}", $val,$temp_str);
	}
	return $temp_str;
}

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
		$eduh_item_list[$key]=$data_list;	
	}
	return $eduh_item_list;
}

?>
