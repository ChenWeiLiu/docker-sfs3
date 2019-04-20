<?php
require_once("../../include/sfs_case_dataarray.php");
require_once ("../../include/sfs_case_score.php");


########-      get_subj  取得該班所有SS_ID科目名稱函式 --------#################
##   $type=all全部,seme有學期成績,stage須段考,no_test不須段考
##    計分need_exam  完整print  加權
###########################################################################
function get_subj($class_id,$type='') {
global $CONN ;
	switch ($type) {
		case 'all':
		$add_sql=" ";break;
		case 'seme':
		$add_sql=" and need_exam='1' and enable='1' ";break;//有成績的
		case 'stage':
		$add_sql=" and need_exam='1'  and print='1' and enable='1' ";break;//有段考,完整
		case 'no_test':
		$add_sql=" and need_exam='1'  and print!='1' and enable='1' ";break; //不用段考的
		default:
		$add_sql=" and enable='1' ";break;
	}
	$CID=split("_",$class_id);//093_1_01_01
	$year=$CID[0];
	$seme=$CID[1];
	$grade=$CID[2];
	$class=$CID[3];
	$CID_1=$year."_".$seme."_".$grade."_".$class;

	$SQL="select * from score_ss where class_id='$CID_1' $add_sql  and  rate > 0  order by sort,sub_sort ";
	$rs=$CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);

	if ($rs->RecordCount()==0){
		$SQL="select * from score_ss where class_id='' and year='".intval($year)."' and semester='".intval($seme)."' and  class_year='".intval($grade)."' $add_sql order by sort,sub_sort ";
		$rs=$CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
		$All_ss=$rs->GetArray();
	}
	else{$All_ss=$rs->GetArray();}

	$subj_name=initArray("subject_id,subject_name","select * from score_subject ");
	$obj_SS=array();

	for($i=0;$i<count($All_ss);$i++){
		$key=$All_ss[$i][ss_id];//索引
		// $obj_SS[$key]=$All_ss[$i];//全部陣列,暫不用
		$obj_SS[$key][rate]=$All_ss[$i][rate];//加權
		$obj_SS[$key][sc]=$subj_name[$All_ss[$i][scope_id]];//領域名稱
		$obj_SS[$key][sb]=$subj_name[$All_ss[$i][subject_id]];//科目名稱
		($obj_SS[$key][sb]=='') ? $obj_SS[$key][sb]=$obj_SS[$key][sc]:"";

//--- 2013-09-27 修正 sc 領域名稱 改從 link_ss 取得 ,   因為北斗國中的均衡學習出不來--By 江添河
		$obj_SS[$key][sc]=$All_ss[$i][link_ss];//領域名稱
	}
	//die("無法查詢，語法:".$SQL);
	return $obj_SS;
}

##################  基本工具 initArray轉化資料為索引與帶值函式 #######################
## 選取資料的欄A為索引,欄B為值,欄A須是唯一
## 使用時 傳入 $F1為字串==>subject_id,subject_name
## 使用時 傳入 $SQL為資料庫語法
##################  基本工具 initArray轉化資料為索引與帶值函式 #######################

function initArray($F1,$SQL){
	global $CONN ;
	$col=split(",",$F1);
	$key_field=$col[0];
	$value_field=$col[1];

	$rs = $CONN->Execute($SQL) or die($SQL);
	$sch_all = array();
	if (!$rs) {
		Return $CONN->ErrorMsg();
	} else {
		while (!$rs->EOF) {
		$sch_all[$rs->fields[$key_field]]=$rs->fields[$value_field];
		$rs->MoveNext(); // 移至下一筆記錄
		}
	}
	Return $sch_all;
}


##################  轉化學期分數陣列以利套印用函式 #######################
## //傳入學期分數陣列、班級id，得到所有該學期成績以套印
##########################################################################

function seme_score2smarty($stud_seme_score,$class_id){
	global $CONN ;

	$output = array_slice($stud_seme_score, 0, -1);

	foreach($output as $ss_id=>$data)
	{
		$sc=$data[sc];
		$arys[$sc][items]++;
		$arys[$sc][total]+=$data[score]*$data[rate];
		$arys[$sc][rate]+=$data[rate];
		$arys[$sc][item_detail][$data[ss_id]]=$data;
	}
	$ary_level=score_level($class_id);

	foreach($arys as $sc=>$data)
	{
		if($data[items]>1){
			$avg=ceil($data[total]/$data[rate]);
			$arys[$sc][item_detail][avg]=array('sb'=>'平均','score'=>$avg,'rate'=>$data[rate],'level'=>score2level($avg, $ary_level));
		}
	}
	$arys['日常生活表現'][item_detail] = array_slice($stud_seme_score, -1, 1);  //將日常生活成績接回
	return $arys;
}

##################  取得分數轉等第的陣列資料用函式 #######################
##
######################    score_level函式     #############################

function score_level($class_id) {

	global $CONN;

	$aryrules = array();

	list($year, $seme, $grade, $clano)=explode("_",$class_id);
	 $sql = "select rule from score_setup where year='{$year}' and
  semester='{$seme}' and class_year='{$grade}'";

	//print "<br> ".$sql;

	$rs = $CONN->Execute($sql);

	if ($ro=$rs->FetchNextObject()) {
		$rules = explode("\n", $ro->RULE);
		for ($i=0; $i<count($rules); $i++)
			$aryrules[$i]=explode("_", $rules[$i]);
	}

	return $aryrules;

}

##################  轉化分數為等第的函式 ##############################
function score2level($score, $aryrules) {

	$level='';
	for ($i=0; $i<count($aryrules); $i++) {

		$retstr=$aryrules[$i][0];
		$chkop=trim($aryrules[$i][1]);
		$score_level = (float) $aryrules[$i][2];
		$score = (float) $score;

	if ($chkop=='>='  && $score >= $score_level) return $retstr;
	elseif ($chkop=='>' && $score > $score_level) return $retstr;
	elseif (($chkop=='==' or $chkop=='=') &&$score == $score_level) return $retstr;
	elseif ($chkop=='<=' && $score <= $score_level) return $retstr;
	elseif ($chkop=='<' && $score < $score_level) return $retstr;

	}

	return $level;

	}


##################  取成績用函式 ##############################
//--- 取得段考或平時成績
function stage_score_chc($class_id, $student_sn, $test_kind='定期評量',$test_sort='1') {
	global $CONN;
	if (empty($class_id)) $class_id=$this->class_id;
	$sub_ary = get_subj($class_id,'stage');
	list($year,$seme,,) = explode('_',$class_id);
	$score_table = sprintf("score_semester_%d_%d",$year,$seme);
	//--- 94.01.27 修正只顯示成績介於0?100之間的資料
	//$sql = "select * from {$score_table} where student_sn='{$student_sn}' and (score>=0 and score<=100) and test_kind='{$test_kind}' and test_name='{$test_kind}' and test_sort='{$test_sort}' and class_id='{$class_id}' order by student_sn,ss_id";
	$sql = "select * from {$score_table} where student_sn='{$student_sn}' and (score>=0 and score<=100) and test_kind='{$test_kind}' and test_name='{$test_kind}' and class_id='{$class_id}' order by ss_id,test_sort";

	$test_times=3;
	//---初始化成績陣列
   foreach($sub_ary as $ss_id=>$sub) {
		$arys[$ss_id]=$sub;
		for ($i=1; $i<=$test_times; $i++)
		  $arys[$ss_id][score][$i]='**';
	}

	$rs = $CONN->Execute($sql);
	while ($rs and $ro=$rs->FetchNextObject()) {
		if ($arys[$ro->SS_ID][score][$ro->TEST_SORT]=='**')
			$arys[$ro->SS_ID][score][$ro->TEST_SORT]=$ro->SCORE;
	}
	$rs->Close();

	//print_r($sub_ary);
	//print_r($arys);
	//die();
	return $arys;
}


##################  取成績用函式 ##############################

function seme_score_chc($class_id, $student_sn, $stud_id) {

	global $CONN;


//	if (empty($student_sn)) $student_sn = $this->student_sn;
		//--- 確定有這個學生的序號 student_sn  & class_id 才取成績

	if (!empty($student_sn) and !empty($class_id)) {

		list($year,$seme,,) = explode("_",$class_id);
		$seme_year_seme = sprintf("%03d%1d", $year, $seme);
		//print_r($seme_year_seme);

		$ary_level = score_level($class_id);
		//print_r($ary_level);
		//  ----取所有科目成績、評語
		$sql = "select ss_id, ss_score, ss_score_memo from stud_seme_score
  where seme_year_seme='{$seme_year_seme}' and student_sn='{$student_sn}'";

		$rs = $CONN->Execute($sql);
	while ($rs and $ro=$rs->FetchNextObject()) {
			$ss_id=$ro->SS_ID;
			$arys["{$ss_id}"] = array('score'=>ceil($ro->SS_SCORE),'memo'=>$ro->SS_SCORE_MEMO);
		}

		//-------取日常生活表現--成績、評語
		$sql = "select * from stud_seme_score_nor where student_sn='{$student_sn}' and seme_year_seme='{$seme_year_seme}' and ss_id='0' ";
		$rs = $CONN->Execute($sql);

		//-------取所有科目---努力程度
		$sql2 = "select * from stud_seme_score_oth  where seme_year_seme='{$seme_year_seme}' and stud_id='{$stud_id}' and ss_kind='努力程度'";

		$rs = $CONN->Execute($sql2);
	while ($rs and $ro=$rs->FetchNextObject()) {
		$ss_id=$ro->SS_ID;
		$arys2["{$ss_id}"] = array('val'=>$ro->SS_VAL);
			}
		}

		$ary_level=score_level($class_id);
		$stud_seme_score=get_subj($class_id,'seme');
//		echo "<PRE>";
//		print_r($stud_seme_score);
	foreach($stud_seme_score as $ss_id=>$data){
		$stud_seme_score[$ss_id][ss_id]=$ss_id;
		$stud_seme_score[$ss_id][score]=$arys[$ss_id][score];   //分數
		$stud_seme_score[$ss_id][level]=score2level($arys[$ss_id][score], $ary_level);  //等第轉換
		$stud_seme_score[$ss_id][memo]=$arys[$ss_id][memo];   //文字描述
		$stud_seme_score[$ss_id][val]=$arys2[$ss_id][val];   //努力程度
		}
	$sql2 = "select * from stud_seme_score_nor  where seme_year_seme='{$seme_year_seme}' and student_sn='{$student_sn}' and ss_id='0' ";
//	echo $sql2 ;
	$rs = $CONN->Execute($sql2);
	$stud_seme_score['nor']=array();
	$stud_seme_score['nor']=array('sb'=>'日常生活表現','sc'=>'日常生活表現'
		,'rate'=>'--','score'=>'--','val'=>'--','memo'=>'--','level'=>'--');
	if ($rs and $ro=$rs->FetchNextObject()) {
		$stud_seme_score['nor']=array('sb'=>'日常生活表現',
			'sc'=>'日常生活表現',
			'rate'=>'',
			'score'=>($ro->SS_SCORE>=100?100:ceil($ro->SS_SCORE)),
			'val'=>'','memo'=>$ro->SS_SCORE_MEMO,
			'level'=>score2level($ro->SS_SCORE, $ary_level));
			}
//print_r($stud_seme_score);
		return $stud_seme_score;
	}




###########################################################
## 本函式須用到 include "../../include/sfs_case_dataarray.php";
##
############################################################
function stsn_get_move($SN){
	global $CONN;
	$stud_coud=study_cond();//學籍資料代碼
	$SQL = "select * from stud_move where student_sn='$SN' order by move_date";
	$rs=$CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
	if ($rs->RecordCount()==0) return "查無資料";
	$stu=$rs->GetArray();
	for ($i=0;$i<$rs->RecordCount();$i++){
		$stu[$i][c_move_kind]=$stud_coud[$stu[$i][move_kind]];
	}
	return $stu;
}
//---數字轉國字
function num_tw($num, $type=0) {
	$num_str[0] = "十百千";
	$num_str[1] = "拾佰仟";
	$num_type[0]='零一二三四五六七八九';
	$num_type[1]='零壹貳參肆伍陸柒捌玖';
	$num = sprintf("%d",$num);
	while ($num) {
		$num1 = substr($num,0,1);
		$num = substr($num,1);
		$target .= substr($num_type[$type], $num1*2, 2);
		if (strlen($num)>0) $target .= substr($num_str[$type],(strlen($num)-1)*2,2);
	}
	return $target;
}
function get_base_data($student_sn) {
 global $CONN, $UPLOAD_URL;

 $sex = array (1=>'男',2=>'女');
 $school_status = array("0"=>"本學區","1"=>"大學區","2"=>"隨父就讀","3"=>"隨母就讀");
 $birth_place = array("01"=>"台北市","02"=>"高雄市","03"=>"宜蘭縣","04"=>"基隆市","05"=>"台北縣","06"=>"桃園縣","07"=>"新竹縣","08"=>"新竹市","09"=>"苗栗縣","10"=>"台中縣","11"=>"台中市","12"=>"南投縣","13"=>"彰化縣","14"=>"雲林縣","15"=>"嘉義縣","16"=>"嘉義市","17"=>"台南縣","18"=>"台南市","19"=>"高雄縣","20"=>"屏東縣","21"=>"台東縣","22"=>"花蓮縣","23"=>"澎湖縣","24"=>"金門縣","25"=>"連江縣");
 $cond_ary = study_cond();

 $sql = "SELECT * FROM stud_base WHERE student_sn='$student_sn'";

 $result = $CONN->Execute($sql) or die("Sql error");
 if ($result->RecordCount()==0) {
 	echo "找不到該學生";
 	exit();
 }


 // 入學資格 => stud_preschool_status , 學校名稱 => stud_mschool_name , 學生狀況 => stud_study_cond
 $sql = "SELECT a.stud_id , a.stud_name , a.stud_sex , a.stud_birthday , a.stud_birth_place , a.stud_person_id ,
         a.stud_addr_1 , a.stud_addr_2 , a.stud_mschool_name , a.stud_Mschool_status , a.stud_study_cond ,
         a.stud_tel_1 , a.stud_tel_2 , a.stud_tel_3 , a.stud_study_year,
         b.fath_name , b.fath_relation , b.fath_phone , b.moth_name , b.moth_relation , b.moth_phone ,
         b.guardian_name , b.guardian_relation , b.guardian_phone
         FROM stud_base as a LEFT JOIN stud_domicile as b ON a.student_sn=b.student_sn
         WHERE a.student_sn='$student_sn' ";

 $result = $CONN->Execute($sql) or die("Sql error");
 $result_data = $result -> FetchNextObject();

 $data['stud_name']						= $result_data->STUD_NAME;							// 姓名
 $data['stud_id']							= $result_data->STUD_ID;								// 學號
 $data['stud_person_id'] 			= $result_data->STUD_PERSON_ID;					// 身分證字號
 $data['stud_birthday'] 			=	$result_data->STUD_BIRTHDAY;					// 出生年月日
 $data['stud_sex']						= $sex[$result_data->STUD_SEX];					// 性別
 $data['stud_birth_place']= $birth_place[sprintf("%02d",$result_data->STUD_BIRTH_PLACE)];		// 出生地
 $data['guardian_name']				= $result_data->GUARDIAN_NAME;					// 監護人姓名
 $data['guardian_relation']		= $result_data->GUARDIAN_RELATION;			// 與監護人關係
 $data['guardian_phone']			= $result_data->GUARDIAN_PHONE;					// 監護人電話
 $data['fath_name']						= $result_data->FATH_NAME;							// 父親姓名
 $data['fath_relation']				= $result_data->FATH_RELATION;					// 與父親關係
 $data['fath_phone']					= $result_data->FATH_PHONE;							// 父親電話
 $data['moth_name']						= $result_data->MOTH_NAME;							// 母親姓名
 $data['moth_relation']				= $result_data->MOTH_RELATION;					// 與母親關係
 $data['moth_phone']					= $result_data->MOTH_PHONE;							// 母親電話
 $data['stud_addr_1']					= $result_data->STUD_ADDR_1;						// 戶籍地址
 $data['stud_addr_2']					= $result_data->STUD_ADDR_2;						// 連絡地址
 $data['stud_tel_2']					= $result_data->STUD_TEL_2;							// 連絡電話
 $data['stud_Mschool_status'] = $school_status[$result_data->STUD_MSCHOOL_STATUS]; // 入學資格
 $data['stud_mschool_name']		=	$result_data->STUD_MSCHOOL_NAME;			// 入學前學校名稱
 $data['stud_study_cond']			= $result_data->STUD_STUDY_COND;				// 就學狀態
 $data['stud_cond']						=	$cond_ary[$result_data->STUD_STUDY_COND]; // 就學狀態
 $data['stud_study_year']						= $result_data->STUD_STUDY_YEAR;							// 入學年
 $data['stud_photo_src'] ="<img src=\"".$UPLOAD_URL. "photo/student/".$result_data->STUD_STUDY_YEAR."/".$result_data->STUD_ID."\" style='width:3.34cm;height:4.7cm'>";  //學生照片

 if ($result_data->STUD_TEL_1) {
 	$data['phone'] .= $result_data->STUD_TEL_1;
 }

 if ($result_data->STUD_TEL_2) {
 	if ($result_data->STUD_TEL_1) {
 	 $data['phone'] .= "，";
  }
 	$data['phone'] .= $result_data->STUD_TEL_2;
 }

 if ($result_data->STUD_TEL_3) {
 	if ($result_data->STUD_TEL_1||$result_data->STUD_TEL_2) {
 	 $data['phone'] .= "，";
  }
 	$data['phone'] .= $result_data->STUD_TEL_3;
 }

 $sql = "SELECT move_c_word , move_c_num FROM stud_move WHERE student_sn='$student_sn' AND move_kind='5'";
 $sql = "Select  a.grad_word,grad_num from grad_stud a,stud_base b where a.stud_id=b.stud_id and b.student_sn='$student_sn' ";
 $result = $CONN->Execute($sql) or die("Sql error");
 if ($result->RecordCount()==0) {
 	$data['grade_word_num'] = "";
 }
 else {
	$result_data = $result -> FetchNextObject(false);
	$data['grade_word_num']		= $result_data->grad_word ."<br>第". $result_data->grad_num."號"; // 畢業字號
 }

 $sql = "select * from stud_seme where student_sn='{$student_sn}' order by seme_year_seme desc ";
 $rs = $CONN->Execute($sql);
 if ($rs and $ro=$rs->FetchNextObject(false)) {
 	$class_id=sprintf("%03d_%1d_%02d_%02d",substr($ro->seme_year_seme,0,3),substr($ro->seme_year_seme,-1),substr($ro->seme_class,0,1),substr($ro->seme_class,1));
 	$data[class_id]=$class_id;
 	$data[cla_no]=substr($ro->seme_class,1);
 	$data[seme_num]=$ro->seme_num;
 }

 //print_r($data);

 return $data;

}



//整理學生的學籍成績資料
//$score=array('ss_id'=>$sub[ss_id],'score'=>$sub[score],'level'=>$sub[level],'memo'=>$sub[memo]);

function all_score($seme_ary) {
	//--print "<pre>";	
	//--print_r($seme_ary);
	$arys=array();
	foreach($seme_ary as $grade_seme=>$seme_data) {
		foreach( $seme_data[scope_score] as $sc=>$scope_data) {
			$score = array();
			$arys[$sc][items]=0;
			foreach($scope_data[item_detail] as $ss_id=>$sub) {
				if ($ss_id != 'avg' and !empty($sub[sb])) {
					$arys[$sc][sub_arys][$sub[sb]]=array();
				}
			}
		}
	}
	foreach ($arys as $sc=>$sub_arys) {
		if (count($sub_arys[sub_arys])>1) {
			$sub_arys[sub_arys][平均]=array();
			$arys[$sc]=$sub_arys;
		}
		foreach($sub_arys[sub_arys] as $sb=>$tmp) {
			foreach($seme_ary as $grade_seme=>$data) {
				$arys[$sc][sub_arys][$sb][$grade_seme]=array('ss_id'=>'--'
					,'score'=>'--','level'=>'--','memo'=>'--','rate'=>'--');
			}
			$arys[$sc][items]=count($arys[$sc][sub_arys]);
		}
	}
//--print "-----------------------<br>";
//--print_r($arys);
//---- 以上產生一個基礎的空陣列
	foreach($seme_ary as $grade_seme=>$seme_data) {
		foreach( $seme_data[scope_score] as $sc=>$scope_data) {
			foreach($scope_data[item_detail] as $ss_id=>$sub) {
				$sb=$sub[sb];
//--- 修正於陽明  該領域超過一個項目也就是有平均的那一項時，將平均設初值
					if (!empty($sub[score]) and $arys[$sc][items]>1) {
						$arys[$sc][sub_arys]['平均'][$grade_seme][score]=$sub[score];
					
					}
				//---$arys[$sc][sub_arys][‘平均'][$grade_seme] = $sub; //---這一行設定平均的初值

				if (!empty($sb) and !empty($arys[$sc][sub_arys][$sb][$grade_seme])) {
					if (!empty($ss_id))
						$arys[$sc][sub_arys][$sb][$grade_seme][ss_id]=$ss_id;
					if (!empty($sub[score]))
						$arys[$sc][sub_arys][$sb][$grade_seme][score]=$sub[score];
					if ($arys[$sc][sub_arys][$sb][$grade_seme][score]!='--' and !empty($sub[level]))
						$arys[$sc][sub_arys][$sb][$grade_seme][level]=$sub[level];
					if (!empty($sub[memo]))
						$arys[$sc][sub_arys][$sb][$grade_seme][memo]=$sub[memo];
					if (!empty($sub[rate]))
						$arys[$sc][sub_arys][$sb][$grade_seme][rate]=$sub[rate];
				}
			}
		}
	}
	return $arys;
}




function seme_num_detail($student_sn='') {
	global $CONN, $IS_JHORES;
	$max_grade=6;
	if ($IS_JHORES>0) $max_grade=3;
	$arys = array();
	for($i=1+$IS_JHORES;$i<=$max_grade+$IS_JHORES;$i++) {
		$arys["{$i}_1"]=array();
		$arys["{$i}_2"]=array();
	}
	if (!empty($student_sn)) {
		$sql = "select seme_year_seme,seme_class,seme_class_name,seme_num, stud_id from stud_seme where student_sn='{$student_sn}' order by seme_year_seme, seme_class";
		$rs = $CONN->Execute($sql);
		while ($rs and $ro=$rs->FetchNextObject(false)) {
			$year = substr($ro->seme_year_seme,0,3);
			$seme = substr($ro->seme_year_seme,-1);
			$grade = substr($ro->seme_class,0,-2);
			$cla_no = substr($ro->seme_class, -2);
			$class_id = sprintf("%03d_%d_%02d_%02d",$year,$seme,$grade,$cla_no);
			$sql2 = "select c_name,teacher_1 from school_class where class_id='{$class_id}'";
			$rs2 = $CONN->Execute($sql2);
			if ($rs2 and $ro2=$rs2->FetchNextObject(false)) {
				$class_title = num_tw($grade-$IS_JHORES)."年".$ro2->c_name."班".$ro->seme_num."號";
				$class_teacher=$ro2->teacher_1;
			}
			$seme_title = sprintf("%d學年度第%d學期",$year,$seme);
			$grade_seme = sprintf("%d_%d",$grade,$seme);
			$arys[$grade_seme][year]=$year;
			$arys[$grade_seme][seme]=$seme;
			$arys[$grade_seme][grade]=$grade;
			$arys[$grade_seme][cla_no]=$cla_no;
			$arys[$grade_seme][seme_num]=$ro->seme_num;
			$arys[$grade_seme][class_id]=$class_id;
			$arys[$grade_seme][seme_title]=$seme_title;
			$arys[$grade_seme][seme_title2]=sprintf("%d學年度<br>第%d學期",$year,$seme);
			$arys[$grade_seme][class_title]=$class_title;
			$arys[$grade_seme][teacher_1]=$class_teacher;
		}
	}
	//print "<pre>";
	//print_r($arys);
	//-- 以下修正 補空的 class_id 讓成績可以正確產生
	$arys2=array();
	foreach ($arys as $grade_seme=>$detail) {
		if (empty($detail)) {
			$arys2[]=$grade_seme;
		}
	}
	//print_r($arys2);
	$arys2 = array_reverse($arys2);
	//print_r($arys2);
	foreach($arys2 as $grade_seme) {
		$grade=substr($grade_seme,0,1);
		$seme=substr($grade_seme,-1);
		if ($seme==2) {
			$grade++;
			$seme=1;
		}
		elseif ($seme==1) {
			$seme=2;
		}
		$grade_seme_next="{$grade}_{$seme}";
		$class_id = $arys[$grade_seme_next][class_id];
		if (!empty($class_id)) {
			list($year,$seme,$grade,$clano)=explode('_',$class_id);
			if ($seme==2) $seme=1;
			elseif ($seme==1) {
				$grade--;
				$seme=2;
				$year--;
			}
			$class_id = sprintf("%03d_%d_%02d_%02d",$year,$seme,$grade,$cla_no);
			$seme_title = sprintf("%d學年度第%d學期",$year,$seme);
			$arys[$grade_seme][class_id]=$class_id;
			$arys[$grade_seme][seme_title]=$seme_title;
			$arys[$grade_seme][seme_title2]=sprintf("%d學年度<br>第%d學期",$year,$seme);
		}
	}
	//print_r($arys);
	//--- 修正結束

	return $arys;
}


class data_student {

	//
	function data_student($student_sn='') {
		global $CONN, $IS_JHORES, $_POST, $semes, $cid;
		if (!empty($student_sn)) {
			$this->student_sn = $student_sn;
			$this->base = get_base_data($student_sn);
			$this->move = stsn_get_move($student_sn);
			$this->seme_ary=seme_num_detail($student_sn);
			//領域學籍表
			if ($_POST[type]==1) {
				//取得學期陣列
				$semes=array();
				reset($this->seme_ary);
				while(list($k,$v)=each($this->seme_ary)) {
					$semes[]=$this->seme_ary[$k][year].$this->seme_ary[$k][seme];
					$cid[$this->seme_ary[$k][year].$this->seme_ary[$k][seme]]=$this->seme_ary[$k][class_id];
				}
				//取得成績陣列
				$this->all_score=cal_fin_score(array("1"=>$student_sn),$semes);
				$this->nor_score=cal_fin_nor_score(array("1"=>$student_sn),$semes,"word");
				//換算等第
				reset($this->seme_ary);
				while(list($k,$v)=each($this->seme_ary)) {
					$sm=&get_all_setup("",intval($this->seme_ary[$k][year]),$this->seme_ary[$k][seme],$this->seme_ary[$k][grade]);
					$rule=explode("\n",$sm[rule]);
					reset($this->all_score[$student_sn]);
					while(list($s,$v)=each($this->all_score[$student_sn])) {
						$this->all_score[$student_sn][$s][$this->seme_ary[$k][year].$this->seme_ary[$k][seme]][str]=score2str($this->all_score[$student_sn][$s][$this->seme_ary[$k][year].$this->seme_ary[$k][seme]][score],"",$rule);
					}
					$this->nor_score[$student_sn][$this->seme_ary[$k][year].$this->seme_ary[$k][seme]][str]=score2str($this->nor_score[$student_sn][$this->seme_ary[$k][year].$this->seme_ary[$k][seme]][score],"",$rule);
				}
			//科目學籍表
			} else {
				foreach ($this->seme_ary as $grade_seme=>$detail) {
					if (!empty($detail)) {
						$detail[seme_score]=seme_score_chc($detail[class_id], $student_sn, $this->base[stud_id]);//所有科目成績
						$detail[scope_score]=seme_score2smarty($detail[seme_score],$detail[class_id]);
						$this->seme_ary[$grade_seme]=$detail;
					}
				}
				$this->all_score=all_score($this->seme_ary);
			}
		}
	}
}

?>
