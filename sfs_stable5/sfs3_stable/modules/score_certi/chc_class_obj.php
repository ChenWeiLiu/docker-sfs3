<?php

#####################   班級選單  ###########################
function link_a($Seme,$Sclass=''){
//		global $PHP_SELF;//$CONN,
	$class_name_arr = class_base($Seme) ;
	$ss="選擇班級：<select name='Sclass' size='1' class='small' onChange=\"location.href='$_SERVER[PHP_SELF]?Seme='+p2.Seme.value+'&Sclass='+this.options[this.selectedIndex].value;\">
	<option value=''>未選擇</option>\n ";
	foreach($class_name_arr as $key=>$val) {
	//$key1=substr($Seme,0,3)."_".substr($Seme,3,1)."_".sprintf("%02d",substr($key,0,1))."_".substr($key,1,2);
	$key1=$Seme."_".$key;
	($Sclass==$key1) ? $cc=" selected":$cc="";
	$ss.="<option value='$key1' $cc>$val </option>\n";
	}
	$ss.="</select>";
Return $ss;
}

########-      get_subj  取得該班所有SS_ID科目名稱函式 --------#################
##   $type=all全部,seme有學期成績,stage須段考,no_test不須段考
##    計分need_exam  完整print  加權
function get_subj($class_id,$type='') {
global $CONN ;
	switch ($type) {
		case 'all':
		$add_sql=" ";break;
		case 'seme':
		$add_sql=" and need_exam='1' and enable='1'  and  rate > 0 ";break;//有成績的
		case 'stage':
		$add_sql=" and need_exam='1'  and print='1' and enable='1'  and  rate > 0 ";break;//有段考,完整
		case 'no_test':
		$add_sql=" and need_exam='1'  and print!='1' and enable='1'  and  rate > 0 ";break; //不用段考的
		default:
		$add_sql=" and enable='1'  and  rate > 0 ";break;
	} 
	$CID=split("_",$class_id);//093_1_01_01
	$year=$CID[0];
	$seme=$CID[1];
	$grade=$CID[2];
	$class=$CID[3];
	$CID_1=$year."_".$seme."_".$grade."_".$class;

	$SQL="select * from score_ss where class_id='$CID_1' $add_sql order by sort,sub_sort ";
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
		$obj_SS[$key][RATE]=$All_ss[$i][rate];//加權
		$obj_SS[$key][SC]=$subj_name[$All_ss[$i][scope_id]];//領域名稱
		$obj_SS[$key][SB]=$subj_name[$All_ss[$i][subject_id]];//科目名稱
		($obj_SS[$key][SB]=='') ? $obj_SS[$key][SB]=$obj_SS[$key][SC]:"";
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
		$sc=$data[SC];
		$arys[$sc][ITEMS]++;
		$arys[$sc][TOTAL]+=$data[SCORE]*$data[RATE];
		$arys[$sc][RATE]+=$data[RATE];
		$arys[$sc][ITEM_DETAIL][$data[SS_ID]]=$data;
	}
	$ary_level=score_level($class_id);

	foreach($arys as $sc=>$data)
	{
		if($data[ITEMS]>1){
			$avg=ceil($data[TOTAL]/$data[RATE]);
			$arys[$sc][ITEM_DETAIL][AVG]=array('SB'=>'平均','SCORE'=>$avg,'RATE'=>$data[RATE],'LEVEL'=>score2level($avg, $ary_level));
		}	
	}
	$arys['日常生活表現'][ITEM_DETAIL] = array_slice($stud_seme_score, -1, 1);  //將日常生活成績接回
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


##################   data_stud  取學生資料物件     ##############################

class data_stud {
	var $stud_id;     //學號
	var $student_sn;  //學生之系統流水號
	var $stud_name;
	var $class_id;    //班級代號
	var $seme_num;
	var $study_cond;

	function data_stud($student_sn='') {
		global $CONN;
		
		$this->student_sn = $student_sn;
		if (!empty($student_sn)) {
			$sql = "select * from stud_base where student_sn = '{$this->student_sn}'";
			$rs = $CONN->Execute($sql) or die($sql);
			//--- 檢查基本資料中是否有這個學生
			if ($rs and $ro=$rs->FetchNextObject()) {   //如果有下一筆
				$this->stud_exists = true;
				$this->stud_id = $ro->STUD_ID;
				$this->stud_name = $ro->STUD_NAME;
				$this->study_cond=$ro->STUD_STUDY_COND;
				$this->person_id = $ro->STUD_PERSON_ID;
				$this->birthday = $ro->STUD_BIRTHDAY;
				$C_birth = split("-",$ro->STUD_BIRTHDAY);
				$this->C_birth=($C_birth[0]-1911)."年".($C_birth[1]+0)."月".($C_birth[2]+0)."日";
				$this->stud_study_year= $ro->STUD_STUDY_YEAR;//入學年
				//---- 從 stud_seme 取出所有的班級與座號
				$sql = "select * from stud_seme where student_sn = '{$this->student_sn}' order by seme_year_seme ";
				$rs = $CONN->Execute($sql);
				while ($rs and $ro=$rs->FetchNextObject()) {
					$year = substr($ro->SEME_YEAR_SEME,0,3);  //將資料庫中欄位的名稱取為大寫
					$seme = substr($ro->SEME_YEAR_SEME,-1);
					$clano = substr($ro->SEME_CLASS,-2);
					$grade = substr($ro->SEME_CLASS,0,-2);
					$seme_num = $ro->SEME_NUM;
					$class_detail[]=sprintf("%03d_%1d_%02d_%02d_%02d",$year,$seme,$grade,$clano,$seme_num);
				}

				$class_id_seat = end($class_detail);
				$this->class_id = substr($class_id_seat,0,11);
				$this->seme_num = substr($class_id_seat,-2);
				$this->class_detail = $class_detail;
			}
		}
	}


##################  取成績用函式 ##############################

function seme_score($class_id, $student_sn) {

	global $CONN, $IS_JHORES;


	if (empty($student_sn)) $student_sn = $this->student_sn;
		//--- 確定有這個學生的序號 student_sn  & class_id 才取成績

	if (!empty($student_sn) and !empty($class_id)) {

		list($year,$seme,,) = explode("_",$class_id);
		$seme_year_seme = sprintf("%03d%1d", $year, $seme);

		$ary_level = score_level($class_id);
		//  ----取所有科目成績、評語
		$sql = "select ss_id, ss_score, ss_score_memo from stud_seme_score
  where seme_year_seme='{$seme_year_seme}' and student_sn='{$student_sn}'";

		$rs = $CONN->Execute($sql);
	while ($rs and $ro=$rs->FetchNextObject()) {
			$ss_id=$ro->SS_ID;
			$arys["{$ss_id}"] = array('SCORE'=>ceil($ro->SS_SCORE),'MEMO'=>$ro->SS_SCORE_MEMO);
		}

		//-------取日常生活表現--成績、評語
		$sql = "select * from stud_seme_score_nor where student_sn='{$student_sn}' and seme_year_seme='{$seme_year_seme}' and ss_id='0' ";
		$rs = $CONN->Execute($sql);

		//-------取所有科目---努力程度
		$sql2 = "select * from stud_seme_score_oth  where seme_year_seme='{$seme_year_seme}' and stud_id='{$this->stud_id}' and ss_kind='努力程度'";
		$rs = $CONN->Execute($sql2);
	while ($rs and $ro=$rs->FetchNextObject()) {
		$ss_id=$ro->SS_ID;
		$arys2["{$ss_id}"] = array('VAL'=>$ro->SS_VAL);
			}
		}

		$ary_level=score_level($class_id);
		$stud_seme_score=get_subj($class_id,'seme');

	foreach($stud_seme_score as $ss_id=>$data){
		$stud_seme_score[$ss_id][SS_ID]=$ss_id;
		$stud_seme_score[$ss_id][SCORE]=$arys[$ss_id][SCORE];   //分數
		$stud_seme_score[$ss_id][LEVEL]=score2level($arys[$ss_id][SCORE], $ary_level);  //等第轉換
		$stud_seme_score[$ss_id][MEMO]=$arys[$ss_id][MEMO];   //文字描述
		$stud_seme_score[$ss_id][VAL]=$arys2[$ss_id][VAL];   //努力程度
		}
	$sql2 = "select * from stud_seme_score_nor  where seme_year_seme='{$seme_year_seme}' and student_sn='{$this->student_sn}' and ss_id='0' ";
//	echo $sql2 ;
	$rs = $CONN->Execute($sql2);
	$stud_seme_score['NOR']=array();
	if ($rs and $ro=$rs->FetchNextObject()) {
		$stud_seme_score['NOR']=array('SB'=>'日常生活表現',
			'SC'=>'日常生活表現',
			'RATE'=>'',
			'SCORE'=>($ro->SS_SCORE>=100?100:ceil($ro->SS_SCORE)),
			'VAL'=>'',
			'MEMO'=>$ro->SS_SCORE_MEMO,
			'LEVEL'=>score2level($ro->SS_SCORE, $ary_level));
				}
		//echo "<PRE>".__LINE__;
		//print_r($this->stud_study_year);
		$tmp=split("_",$class_id);
		$tmp2=$tmp[0]-($tmp[2]-$IS_JHORES);
		//95學年度以後的入學生不顯示日常生活等第成績等功能
		if($tmp2>=94){
			$stud_seme_score['NOR']['SCORE']='--';
			$stud_seme_score['NOR']['LEVEL']='--';
		}
//print_r($stud_seme_score);
		return $stud_seme_score;
		}

}
?>
