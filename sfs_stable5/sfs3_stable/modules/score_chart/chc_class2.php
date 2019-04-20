<?php
//////// 94.01.04 整班的 class
######################    score_level函式     #############################
//$Id: chc_class2.php 9056 2017-04-05 23:36:54Z chiming $//

function score_level($class_id) {

	global $CONN;
	$aryrules = array();
	list($year, $seme, $grade, $clano)=explode("_",$class_id);
	 $sql = "select rule from score_setup where year='{$year}' and  semester='{$seme}' and class_year='{$grade}'";

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

	return $obj_stu;
}


########- get_subj 取得該班所有在籍學生以學生流水號為key 的函式 --------#################
########-      get_subj  取得該班所有SS_ID科目名稱函式 --------#################
##   $type=all全部,seme有學期成績,stage須段考,no_test不須段考
##    計分need_exam  完整print  加權
function get_subj($class_id,$type='') {
global $CONN;
	switch ($type) {
		case 'all':
		$add_sql=" ";break;
		case 'seme':
		$add_sql=" and need_exam='1' and enable='1' and  rate > 0  ";break;//有成績的
		case 'stage':
		$add_sql=" and need_exam='1'  and print='1' and enable='1' and  rate > 0  ";break;//有段考,完整
		case 'no_test':
		$add_sql=" and need_exam='1'  and print!='1' and enable='1'  and  rate > 0  ";break; //不用段考的
		default:
		$add_sql=" and enable='1'  and  rate > 0  ";break;
	} 
	$CID=split("_",$class_id);//093_1_01_01
	$year=$CID[0];
	$seme=$CID[1];
	$grade=$CID[2];
	$class=$CID[3];
	$CID_1=$year."_".$seme."_".$grade."_".$class;

	$SQL="select * from score_ss where class_id='$CID_1' $add_sql and enable='1' and need_exam='1' order by sort,sub_sort";
	$rs=$CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);

	if ($rs->RecordCount()==0){
		$SQL="select * from score_ss where class_id='' and year='".intval($year)."' and semester='".intval($seme)."' and  class_year='".intval($grade)."' $add_sql and enable='1' and need_exam='1' order by sort,sub_sort ";
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
		,"nor3"=>"公共服務"
		,"nor4"=>"校外表現"); //--- 生活表現的代號及文字列
	var $subject_abs=array(
		"abs1"=>"事假"
		,"abs2"=>"病假"
		,"abs3"=>"曠課"
		,"abs4"=>"升降旗"
		,"abs5"=>"公假"
		,"abs6"=>"其它"); //--- 缺曠課的代號及文字列
	var $subject_rew=array(
		"sr1"=>"大功\"
		,"sr2"=>"小功\"
		,"sr3"=>"嘉獎"
		,"sr4"=>"大過"
		,"sr5"=>"小過"
		,"sr6"=>"警告"); //--- 獎懲的代號及文字列
	
        //加入判斷是否顥示文字描述分科名稱
        var $disable_subject_memo_title;

	function seme_score($class_id) {
		global $CONN,$ceilox;
	
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
			$score=$ceilox?ceil($ro->ss_score):$ro->ss_score;
			$arys[$ro->student_sn][$ro->ss_id][score]=$score;
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
		$ss_id_str="'".implode("','",array_keys($sub_ary))."'";
		$query = "select ss_id,count(*) as num from score_course where year='".intval($year)."' and semester='$seme' and class_id='$class_id' and ss_id in ($ss_id_str) group by ss_id";
		$res = $CONN->Execute($query);
		$sector_ary=array();
		while(!$res->EOF) {
			$sector_ary[$res->fields['ss_id']]=$res->fields['num'];
			$res->MoveNext();
		}
		//print "<br><pre>  1234";
		//print_r($sub_ary);
		 
		foreach ($this->stud_base as $student_sn=>$stud) {
			foreach ($sub_ary as $ss_id=>$sub) {
				//$arys[$student_sn][$ss_id][rate]=$sub_ary[$ss_id][rate];
				$arys[$student_sn][$ss_id][sector]=$sector_ary[$ss_id];
				$arys2[$student_sn][$ss_id]=$arys[$student_sn][$ss_id];
			}
		}
		
		
		return $arys2;
	}

	function seme_scope($class_id) {
		global $school_long_name, $_REQUEST,$ceilox;
		
		//努力程度陣列
		$oth_arr_score = array("表現優異"=>5,"表現良好"=>4,"表現尚可"=>3,"需再加油"=>2,"有待改進"=>1);
		$oth_arr_score_2 = array(5=>"表現優異",4=>"表現良好",3=>"表現尚可",2=>"需再加油",1=>"有待改進",0=>"--");

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
				$sector = 0;
				$items = 0;
				$oth = 0;
				$memo = "";
				$ary2 = array();
				foreach ($sc_ary as $ss_id) {
					$ary2[$ss_id]=$score_ary[$student_sn][$ss_id];
					$ary2[$ss_id][rate]=$sub_ary[$ss_id][rate];
					$ary2[$ss_id][sector]=$score_ary[$student_sn][$ss_id][sector];
					$ary2[$ss_id][sb]=$sub_ary[$ss_id][sb];
					$ary2[$ss_id][oth]=$score_ary[$student_sn][$ss_id][oth];
					$rate += $sub_ary[$ss_id][rate];
					$sector += $ary2[$ss_id][sector];
					$sum += $score_ary[$student_sn][$ss_id][score]*$sub_ary[$ss_id][rate];
					$oth += $oth_arr_score[$ary2[$ss_id][oth]]*$sub_ary[$ss_id][rate];
                                            //加入描述文字分科是否顥示判斷
                                        if ($this->disable_subject_memo_title){
                                            $memo .= (($memo=="")?"":"<br>").(($ary2[$ss_id][memo])?$ary2[$ss_id][sb].":".$ary2[$ss_id][memo]:"");
                                        }else{
                                            $memo .= $ary2[$ss_id][memo].'<br>';
                                        }
					
					$items++;
				}
				//--- 該領域超過一個科目的才計算平均
				if ($items>1) {
					//$ary2[avg][score]=ceil($sum/$rate);
					$ary2[avg][score]=$ceilox?ceil($sum/$rate):sprintf("%3.2f",$sum/$rate);
					
					$ary2[avg][rate]=$rate;
					$ary2[avg][sector]=$sector;
					$ary2[avg][sb]='平均';
					$ary2[avg][oth]=$oth_arr_score_2[ceil($oth/$rate)];
					//$temp_score=$ceilox?ceil($oth/$rate):sprintf("%3.2f",$oth/$rate);
					//$ary2[avg][oth]=$oth_arr_score_2[$temp_score];
					//$ary2[avg][level]=score2level(ceil($sum/$rate),$this->level_ary);
					$ary2[avg][level]=score2level($ary2[avg][score],$this->level_ary);
					$ary2[avg][memo]=$memo;
					//彰化縣學校不顯示領域努力程度的平均及各分科評語  98.09.24修正
					$pos=strpos($school_long_name, "彰化縣");
					if($pos!==false and $_REQUEST['chart_kind']=='1'){
						$ary2[avg][oth]="--";
			            $ary2[avg][level]="--";
			            $ary2[avg][memo]="--";
			        }
				}
				$score[$sc][scope_name]=$sc;
				$score[$sc][items]=$items;
				$score[$sc][detail]=$ary2;
				
			}
			$ary3[$student_sn]=$score;
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
	global $CONN,$ceilox;

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
		$temp_score=$ceilox?ceil($ro->SS_SCORE):sprintf("%3.2f",$ro->SS_SCORE);
		$arys["$ro->STUDENT_SN"] = array(
			"nor_score"=>($ro->SS_SCORE > 100 ? 100 :$temp_score)
			,"nor_level"=>score2level($temp_score,$this->level_ary) 
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

	$query = "select 
	stud_seme.student_sn,stud_seme_score_nor.*
	from stud_seme 
	left join stud_seme_score_nor 
	on (stud_seme.student_sn=stud_seme_score_nor.student_sn 
	and stud_seme_score_nor.seme_year_seme='{$seme_year_seme}')
	where stud_seme.seme_year_seme='{$seme_year_seme}' and stud_seme.seme_class='{$seme_class}' order by stud_seme.student_sn";
	$res=$CONN->Execute($query);
	while ($ro=$res->FetchNextObject()) {
		$arys["$ro->STUDENT_SN"]["chk"."$ro->SS_ID"]=$ro->SS_SCORE_MEMO;
	}

	return $arys;
}   



	function data_class($class_id,$disable_subject_memo_title){
		if (!empty($class_id)) {
			$this->class_id = $class_id;
                            $this->disable_subject_memo_title = $disable_subject_memo_title;
//		print "<br> test $class_id";
			$this->stud_base = get_stu($class_id);
			$this->level_ary=score_level($class_id);
		}
	}
	
}




###########################################################
##  傳入年級,學年度,學期 預設值為all表示將傳出所有年級與班級
##  傳出以  class_id  為索引的陣列  
function get_class_info($grade='all',$year_seme='') {
	global $CONN ,$IS_JHORES;
if ($year_seme=='') {
	$curr_year=curr_year(); $curr_seme=curr_seme();}
else {
	$CID=split("_",$year_seme);//093_1
	$curr_year=$CID[0]; $curr_seme=$CID[1];}
	($grade=='all') ? $ADD_SQL='':$ADD_SQL=" and c_year='$grade'  ";
	$SQL="select class_id,year,semester,c_year,c_name,teacher_1 from  school_class where year='$curr_year' and semester='$curr_seme' and enable=1  $ADD_SQL order by class_id  ";
	$rs=$CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);

	if ($rs->RecordCount()==0) die("尚未設定班級資料！");
	$All_class=$rs->GetArray();
	$obj_class=array();
	for($i=0;$i<$rs->RecordCount();$i++){
		$key=$All_class[$i][class_id];
		$obj_class[$key][class_id]=$All_class[$i][class_id];
		$obj_class[$key][year]=$All_class[$i][year];
		$obj_class[$key][ch_year]=$All_class[$i][year]."學年度";
		$obj_class[$key][seme]=$All_class[$i][semester];
		$obj_class[$key][c_seme]="第".$All_class[$i][semester]."學期";
		$obj_class[$key][c_year]=$All_class[$i][c_year];
		//判斷國中小
		($IS_JHORES==6) ? $tmp_year=$obj_class[$key][c_year]-6:$tmp_year=$obj_class[$key][c_year];
		$obj_class[$key][c_year2]=num_tw($tmp_year)."年";
		$obj_class[$key][c_name]=$All_class[$i][c_name]."班";
		$obj_class[$key][teacher_1]=$All_class[$i][teacher_1];
		}

	return $obj_class;
}
###########################################################
##  傳出中文數字函數

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

#######========== 寫檔函式  ===================#######################
function mywrite($fname, $ftxt) {
	global $UPLOAD_PATH;
	$write_ok=false;
	if (!empty($fname) and !empty($ftxt)) {
		$mod_dir = dirname($_SERVER[PHP_SELF]);
		$ary = explode("/",$mod_dir);
		$upload_dir = $UPLOAD_PATH.'school';
		if (!file_exists($upload_dir)) mkdir($upload_dir, 0777);
		if (file_exists($upload_dir)) {
			$upload_dir .= '/'.end($ary);
			if (!file_exists($upload_dir)) mkdir($upload_dir, 0777);
			if (file_exists($upload_dir)) {
				$upload_file = $upload_dir.'/'.$fname;
				if ($handle = fopen($upload_file, 'w+')) {
					if (fwrite($handle, $ftxt) !== FALSE) $write_ok=true;
				}
			}
		}
	}
}
function chk_file($fname) {
	global $UPLOAD_PATH;
		$mod_dir = dirname($_SERVER[PHP_SELF]);
		$ary = explode("/",$mod_dir);
		$upload_dir = $UPLOAD_PATH.'school';
		$upload_dir .= '/'.end($ary);
		$upload_file = $upload_dir.'/'.$fname;
	return $upload_file;
	}

function chk_upload_file($fname) {
	global $UPLOAD_PATH;
	//--- 取得目前 php 所在的目錄
	$php_dir = dirname($_SERVER[PHP_SELF]);
	$ary = explode('/',$php_dir);
	$fname = $UPLOAD_PATH.'school/'.end($ary).'/'.$fname;
	//-- 將 $fname 中的 '//' 修正成 '/' 以避免錯誤
	while (strpos($fname,'//')!==false) 
		$fname = str_replace('//','/',$fname);
	//--- 取得實際上要存檔的目錄陣列
//	print "<br> test1 fname={$fname}";
	$ary = explode('/',dirname(substr($fname,strlen($UPLOAD_PATH))));
//	print_r($ary);
	//$fname = substr($fname,strrpos($fname,'/'));
	$fname=basename($fname);
	$upload_dir = substr($UPLOAD_PATH,0,-1); //--- 最後的 / 不要
//	print "<br> test2 fname={$fname} uplod_dir={$upload_dir}";
	foreach ($ary as $next_dir) {
		if (!empty($next_dir)) {
			$upload_dir .= '/'.$next_dir;
//			print "<br> test3 uplod_dir={$upload_dir}";
			//--- 檢查是否為一個目錄
			if (filetype($upload_dir)!=='dir') {
				//--- 檢查檔案是否存在，若存在檔案時先將其刪除
				if (file_exists($upload_dir)) unlink($upload_dir);
				//-- 建立目錄 失敗立刻中斷
//				print "<br> test4 uplod_dir={$upload_dir}";
				//mkdir($upload_dir);
				if (mkdir($upload_dir)===false) break;
			}
		}
	}
	if (filetype($upload_dir)==='dir') $fname = $upload_dir.'/'.$fname;
	else $fname = false;
//	print "<br> upload_dir={$upload_dir} fname={$fname}";
	return $fname;
}

//--- 將資料寫至上傳目錄中的檔案
function upload_write($fname, $ftxt) {
	$fname = chk_upload_file($fname);
	if ($fname !== false) { //---目錄檢查或建立成功
		//print "<br> 1234 fname={$fname}";
		$handle=fopen($fname,"w+");
		if ($handle) {
			$bytes = fwrite($handle,$ftxt);
			fclose($handle);
		}
	}
	return $bytes;
}

//--- 讀取上傳檔案的內容
function upload_read($fname) {
	$fname = chk_upload_file($fname);
	if ($fname !== false) { //---目錄檢查或建立成功
		$handle=fopen($fname,"r");
		if ($handle) {
			$bytes = fread($handle,filesize($fname));
			fclose($handle);
		}
	}
	
	return $bytes;
}

//---取得學校的職稱資料及上傳的圖章位址，有上傳圖章的才傳回
function get_title_pic() {
	global $CONN,$UPLOAD_PATH,$UPLOAD_URL;
	
	$sql = "select * from teacher_title where enable='1' ";
	$rs = $CONN->Execute($sql);
	while ($rs and $ro=$rs->FetchNextObject(false)) {
		if (!empty($ro->title_name)) {
			//---檢查是否有圖章存在
			$pic_file = $UPLOAD_PATH."school/title_img/title_{$ro->teach_title_id}";
			if (file_exists($pic_file)) {
				$arys[$ro->title_name]="{$UPLOAD_URL}school/title_img/title_{$ro->teach_title_id}";
			}
		}
	}
	return $arys;
}

function get_seme_dates($seme,$grade) {
	global $CONN;
	$seme1=split("_",$seme);//093_1
	$year_seme=sprintf("%03d",$seme1[0]).$seme1[1];
	$SQL="SELECT days FROM seme_course_date where seme_year_seme='$year_seme' and class_year='$grade' ";
//	echo$SQL; 
	$rs = $CONN->Execute($SQL);
	$obj=$rs->GetArray();
	return $obj[0][0];
}


