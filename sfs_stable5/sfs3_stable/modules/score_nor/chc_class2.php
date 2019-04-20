<?php
//$Id: chc_class2.php 5310 2009-01-10 07:57:56Z hami $
//////// 94.01.04 整班的 class
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

#################### 93.12.30  新增 ####################
//--- 取得學年度與學期之陣列
//--- 陣列格式 array('0922'=>'92學年度第二學期')
function year_seme_ary() {
	global $CONN;
	$sql = "select year,seme from school_day group by year,seme order by year desc,seme desc";
	$rs = $CONN->Execute($sql);
	while ($rs and $ro=$rs->FetchNextObject()) {
		$key = sprintf("%03d%1d", $ro->YEAR, $ro->SEME);
		$val = sprintf("%d 學年度第 %d 學期", $ro->YEAR, $ro->SEME);
		$arys[$key]=$val;
	}
	return $arys;
}
//--- 取得指定學年及學期的年級及班級陣列
function sch_class_name($curr_year, $curr_seme,$c_year='') {
	global $CONN, $IS_JHORES;
	if (empty($curr_year)) $curr_year=curr_year();
	if (empty($curr_seme)) $curr_seme=curr_seme();
	//---取得各年級的班級名稱陣列
	$sql = "select class_id,c_name from school_class where year='{$curr_year}' and semester='{$curr_seme}' order by class_id";
	if (!empty($c_year)) {
		$sql = "select class_id,c_name from school_class where year='{$curr_year}' and 		semester='{$curr_seme}' and c_year='{$c_year}' order by class_id";
	}

	$rs = $CONN->Execute($sql);
	while ($rs and $ro=$rs->FetchNextObject() ){
		$arys[$ro->CLASS_ID]=$ro->C_NAME;
	}
	$rs->Close();
	return $arys;

}


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

########- get_subj 取得該班所有在籍學生以學生流水號為key 的函式 --------#################

function get_stu($class_id,$type="0") {
	global $CONN ;
	
//判斷是否在籍
	($type==0) ? $add_sql=" and a.stud_study_cond=0 ":$add_sql=" ";


	$CID=split("_",$class_id);//093_1_01_01
	$year=$CID[0];
	$seme=$CID[1];
	$grade=$CID[2];//年級
	$class=$CID[3];//班級
	$CID_1=$year.$seme;
	$CID_2=sprintf("%03d",$grade.$class);
	$SQL="select 	a.stud_id,a.student_sn,a.stud_name,a.stud_sex,b.seme_year_seme,b.seme_class,b.seme_num from stud_base a,stud_seme b where a.student_sn=b.student_sn and b.seme_year_seme='$CID_1' and b.seme_class='$CID_2' $add_sql order by b.seme_num ";
	$rs=$CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
	$obj_stu=array();
	while ($rs and $ro=$rs->FetchNextObject(false)) {
		$obj_stu[$ro->student_sn] = get_object_vars($ro);
	}
	
	/*
	$All_stu=$rs->$rs->GetArray();
	$obj_stu=array();
	for($i=0;$i<count($All_stu);$i++){
		$key=$All_stu[$i][student_sn];
		$obj_stu[$key]=$All_stu[$i];
	}
	print "<pre>";
	print_r($obj_stu);
	print "</pre>";
	*/
	return $obj_stu;
}


########- get_subj 取得該班所有在籍學生以學生流水號為key 的函式 --------#################
########-      get_subj  取得該班所有SS_ID科目名稱函式 --------#################
##   $type=all全部,seme有學期成績,stage須段考,no_test不須段考
##    計分need_exam  完整print  加權
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
		$obj_SS[$key][rate]=$All_ss[$i][rate];//加權
		$obj_SS[$key][sc]=$subj_name[$All_ss[$i][scope_id]];//領域名稱
		$obj_SS[$key][sb]=$subj_name[$All_ss[$i][subject_id]];//科目名稱
		($obj_SS[$key][sb]=='') ? $obj_SS[$key][sb]=$obj_SS[$key][sc]:"";
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



class data_class  {
	var $class_id;
	var $stud_base;
	var $subject_nor=array(
		"nor_score"=>"生活表現(分)"
		,"nor_level"=>"生活表現(等)"
		,"nor_memo"=>"生活表現(評)"
		,"nor1"=>"日常行為"
		,"nor2"=>"團體活動"
		,"nor3"=>"公共事務"
		,"nor4"=>"校外表現"); //--- 生活表現的代號及文字列
	var $subject_abs=array(
		"abs1"=>"事假"
		,"abs2"=>"病假"
		,"abs3"=>"曠課"
		,"abs4"=>"升降旗"
		,"abs5"=>"公假"
		,"abs6"=>"喪假(其它)"); //--- 缺曠課的代號及文字列
	var $subject_rew=array(
		"sr1"=>"大功\"
		,"sr2"=>"小功\"
		,"sr3"=>"嘉獎"
		,"sr4"=>"大過"
		,"sr5"=>"小過"
		,"sr6"=>"警告"); //--- 獎懲的代號及文字列
	var $subject_score_nor=array( //-- score2--5  為團體活動成績
		"score1"=>"導師評分"
		,"score2"=>"班級活動"
		,"score3"=>"社團活動"
		,"score4"=>"自治活動"
		,"score5"=>"例行活動"
		,"score6"=>"公共服務"
		,"score7"=>"校外特殊表現");

	function seme_score_nor($class_id) {
		global $CONN;
		
		if (empty($class_id)) $class_id=$this->class_id;
		list($year,$seme,$grade,$clano)=explode("_",$class_id);
		$year_seme=sprintf("%03d%1d",$year,$seme);
		$seme_class = sprintf("%d%02d",$grade,$clano);
		$sql =  "select stud_seme.student_sn, score1,score2,score3,score4,score5,score6,score7 
			from stud_seme
			left join seme_score_nor on(stud_seme.stud_id=seme_score_nor.stud_id
				and stud_seme.seme_year_seme=seme_score_nor.seme_year_seme)
			where stud_seme.seme_year_seme='{$year_seme}' 
				and stud_seme.seme_class='{$seme_class}' 
			order by stud_seme.student_sn ";

		$rs = $CONN->Execute($sql) or die("die:".$sql);
		while ($rs and $ro = $rs->FetchNextObject(false)) {
			$arys[$ro->student_sn]=get_object_vars($ro);
		}
		$rs->Close();
		
		return $arys;

		
}
	
	function seme_score($class_id) {
		global $CONN;
		
		if (empty($class_id)) $class_id=$this->class_id;
		list($year,$seme,$grade,$clano)=explode("_",$class_id);
		$year_seme=sprintf("%03d%1d",$year,$seme);
		$seme_class = sprintf("%d%02d",$grade,$clano);
		
		$sql =  "select stud_seme.student_sn, ss_id,ss_score,ss_score_memo
			from stud_seme
			left join stud_seme_score on(stud_seme.student_sn=stud_seme_score.student_sn
				and stud_seme.seme_year_seme=stud_seme_score.seme_year_seme)
			where stud_seme.seme_year_seme='{$year_seme}' 
				and stud_seme.seme_class='{$seme_class}' 
			order by stud_seme.student_sn ";
		//print "<br> $sql";
		//$CONN->debug=true;
		$rs = $CONN->Execute($sql) or die("die:".$sql);
		while ($rs and $ro = $rs->FetchNextObject(false)) {
			$arys[$ro->student_sn][$ro->ss_id][score]=ceil($ro->ss_score);
			$arys[$ro->student_sn][$ro->ss_id][memo]=$ro->ss_score_memo;
		}
		$rs->Close();

		$sql =  "select stud_seme.student_sn, ss_id,ss_val
			from stud_seme
			left join stud_seme_score_oth on(stud_seme.stud_id=stud_seme_score_oth.stud_id
				and stud_seme.seme_year_seme=stud_seme_score_oth.seme_year_seme)
			where stud_seme.seme_year_seme='{$year_seme}' 
				and stud_seme.seme_class='{$seme_class}' and ss_kind='努力程度'  
			order by stud_seme.student_sn ";

		$rs = $CONN->Execute($sql);
		while ($rs and $ro = $rs->FetchNextObject(false)) {
			$arys2[$ro->student_sn][$ro->ss_id][oth]=$ro->ss_val;
		}
		
		foreach($arys as $student_sn=>$stud) {
			foreach($stud as $ss_id=>$data) {
				$arys[$student_sn][$ss_id][oth]=$arys2[$student_sn][$ss_id][oth];
				$arys[$student_sn][$ss_id][level]=score2level($arys[$student_sn][$ss_id][score],$this->level_ary);
			}
		}
		
		//print "<pre>";
		//print_r($arys2);
		
		$arys2 = array();
		$sub_ary = get_subj($class_id,'seme');
		//print "<br><pre>  1234";
		//print_r($sub_ary);
		 
		foreach ($this->stud_base as $student_sn=>$stud) {
			foreach ($sub_ary as $ss_id=>$sub) {
				//$arys[$student_sn][$ss_id][rate]=$sub_ary[$ss_id][rate];
				$arys2[$student_sn][$ss_id]=$arys[$student_sn][$ss_id];
			}
		}
		
		
		return $arys2;
	}

	function seme_scope($class_id) {
		if (empty($class_id)) $class_id = $this->class_id;
		$score_ary = $this->seme_score();
		$sub_ary = get_subj($class_id,'seme');
		//--- 取得每個領域的科目陣列
		foreach ($sub_ary as $ss_id=>$sub) {
			$arys[$sub[sc]][]=$ss_id;
		}
		
		foreach ($score_ary as $student_sn=>$stud) {
			$score = '';
			foreach($arys as $sc=>$sc_ary) {
				//--- 該領域超過一個科目的才計算平均
				$sum = 0;
				$rate = 0;
				$items = 0;
				$ary2 = array();
				foreach ($sc_ary as $ss_id) {
					$ary2[$ss_id]=$score_ary[$student_sn][$ss_id];	
					$ary2[$ss_id][rate]=$sub_ary[$ss_id][rate];	
					$ary2[$ss_id][sb]=$sub_ary[$ss_id][sb];	
					$rate += $sub_ary[$ss_id][rate];
					$sum += $score_ary[$student_sn][$ss_id][score]*$sub_ary[$ss_id][rate];
					$items++;
				}
				//--- 該領域超過一個科目的才計算平均
				if ($items>1) {
					$ary2[avg][score]=ceil($sum/$rate);
					$ary2[avg][rate]=$rate;
					$ary2[avg][sb]='平均';
					$ary2[avg][level]=score2level(ceil($sum/$rate),$this->level_ary);
				}
				$score[$sc][scope_name]=$sc;
				$score[$sc][items]=$items;
				$score[$sc][detail]=$ary2;
				
			}
			$ary3[$student_sn]			=$score;
		}
		
		return $ary3;	
	}

  function seme_absent($class_id) {
  		global $CONN;
  	
		if (empty($class_id)) $class_id=$this->class_id;
		list($year,$seme,$grade,$clano)=explode("_",$class_id);
		
		$seme_year_seme = sprintf("%03d%1d",$year,$seme);
		$seme_class = sprintf("%1d%02d",$grade,$clano);
		
		$sql = "select stud_seme.student_sn , stud_seme_abs.abs_kind , stud_seme_abs.abs_days
		        from stud_seme 
		        left join stud_seme_abs on(stud_seme.stud_id=stud_seme_abs.stud_id 
		        	and stud_seme.seme_year_seme=stud_seme_abs.seme_year_seme)
		        where stud_seme_abs.seme_year_seme='{$seme_year_seme}' 
		        	and stud_seme.seme_class='{$seme_class}' 
		        order by stud_seme.student_sn  , stud_seme_abs.abs_kind ";

		//$CONN->debug=true;
		$rs = $CONN->Execute($sql);
		//$CONN->debug=false;
		while ($rs and $ro = $rs->FetchNextObject(false)) {
			$arys[$ro->student_sn]["abs{$ro->abs_kind}"]=$ro->abs_days;
		}		
		//print_r($arys);
		return $arys ;
 	
  }

//--取得某一班的獎懲資料
//--$class_id 班級代號
function seme_rew($class_id) {
	global $CONN;

	if (empty($class_id)) $class_id = $this->class_id;
	list($year,$seme,$grade,$clano)=explode("_",$class_id);
	$seme_year_seme=sprintf("%03d%d",$year,$seme);
	$seme_class = sprintf("%d%02d",$grade,$clano);
	$sql = "select 
	stud_seme.student_sn,stud_seme_rew.sr_kind_id
	,stud_seme_rew.sr_num
	from stud_seme 
	left join stud_seme_rew 
	on (stud_seme.stud_id=stud_seme_rew.stud_id 
	and stud_seme_rew.seme_year_seme='{$seme_year_seme}' ) 
	left join stud_base on (stud_seme.student_sn=stud_base.student_sn) 
	
	where stud_seme.seme_year_seme='{$seme_year_seme}' and stud_seme.seme_class='{$seme_class}' and (stud_base.stud_study_cond='0' or stud_base.stud_study_cond='2')  
	order by stud_seme.student_sn, stud_seme_rew.sr_kind_id";

	//$CONN->debug=true;
	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
	$rs = $CONN->Execute($sql);

	while ($ro = $rs->FetchNextObject()) {
		if (!empty($ro->SR_KIND_ID) and $ro->SR_KIND_ID>='1' and $ro->SR_KIND_ID<='6') 
			$arys["$ro->STUDENT_SN"]["sr".$ro->SR_KIND_ID] =$ro->SR_NUM; 
	}
	return $arys;
}



function seme_nor($class_id) {
	global $CONN;

	$level_arys=score_level($class_id);

	if (empty($class_id)) $class_id = $this->class_id;
	list($year,$seme,$grade,$clano)=explode("_",$class_id);
	$seme_year_seme=sprintf("%03d%d",$year,$seme);
	$seme_class = sprintf("%d%02d",$grade,$clano);
	$sql = "select 
	stud_seme.student_sn,stud_seme_score_nor.ss_score
	,stud_seme_score_nor.ss_score_memo
	from stud_seme 
	left join stud_seme_score_nor 
	on (stud_seme.student_sn=stud_seme_score_nor.student_sn 
	and stud_seme_score_nor.ss_id='0' 
	and stud_seme_score_nor.seme_year_seme='{$seme_year_seme}') 
	left join stud_base on (stud_seme.student_sn=stud_base.student_sn) 

	where stud_seme.seme_year_seme='{$seme_year_seme}' and stud_seme.seme_class='{$seme_class}'  and (stud_base.stud_study_cond='0' or stud_base.stud_study_cond='2') order by stud_seme.student_sn";

	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
	$rs = $CONN->Execute($sql);
   // $level_arys=score_level($class_id);
	while ($ro = $rs->FetchNextObject()) {
		$arys["$ro->STUDENT_SN"] = array(
			"nor_score"=>($ro->SS_SCORE > 100 ? 100 : ceil($ro->SS_SCORE))
			,"nor_level"=>score2level(ceil($ro->SS_SCORE),$this->level_ary) 
			,"nor_memo"=>$ro->SS_SCORE_MEMO);
    }
	
	for ($ss_id=1; $ss_id<=4; $ss_id++) {
		$sql = "select
		stud_seme.student_sn,stud_seme_score_oth.ss_val
		from stud_seme 
		left join stud_seme_score_oth 
		on (stud_seme.stud_id=stud_seme_score_oth.stud_id 
		and stud_seme_score_oth.ss_id='{$ss_id}' 
		and stud_seme_score_oth.seme_year_seme='{$seme_year_seme}') 
		where stud_seme.seme_year_seme='{$seme_year_seme}' and stud_seme.seme_class='{$seme_class}' order by stud_seme.student_sn";

		$rs=$CONN->Execute($sql);
		while ($ro=$rs->FetchNextObject()) {
			//print "<br> test $ro->STUDENT_SN -- $ro->SS_VAL";
			$arys["$ro->STUDENT_SN"]["nor".$ss_id]=$ro->SS_VAL;
		}

	}

	return $arys;
}   



	function data_class($class_id)  {
		if (!empty($class_id)) {
			$this->class_id = $class_id;
			$this->stud_base = get_stu($class_id);
			$this->level_ary=score_level($class_id);
		}
	}
	
}
?>