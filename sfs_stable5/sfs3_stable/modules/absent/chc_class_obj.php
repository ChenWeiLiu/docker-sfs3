<?php
// $Id: chc_class_obj.php 9208 2018-03-02 03:52:55Z smallduh $

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

	global $CONN;


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
//		echo "<PRE>";
//		print_r($stud_seme_score);
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
			'VAL'=>'','MEMO'=>$ro->SS_SCORE_MEMO,
			'LEVEL'=>score2level($ro->SS_SCORE, $ary_level));
				}
//print_r($stud_seme_score);
		return $stud_seme_score;
		}

}


////////////////////////////////////////////////////////////////////////
/////  93.12.16 新增   by tienho.chiang
//-----------------------------------------------------------------------------------
function maxdays($year_mon_day='') {
	if (!empty($year_mon_day)) {
		list($year,$mon,$day)=explode('-',$year_mon_day);
		$day = 31;
		while (!checkdate($mon,$day,$year) and $day>0) --$day; 
	}
	return ($day>0?$day:false);
}

//---計算日期
function date_skip($year_mon_day='',$skip_days=0) {
	if (empty($year_mon_day)) $year_mon_day=date("Y-m-d");
	list($year,$mon,$day)=explode('-',$year_mon_day);
	if (checkdate($mon,$day,$year) and $skip_days != 0) {
		$date_time = mktime(11,30,0,$mon,$day,$year);
		$date_time += 86400*$skip_days;
		$day_ary = getdate($date_time);
		$date_calc = sprintf("%04d-%02d-%02d",$day_ary[year]
			,$day_ary[mon],$day_ary[mday]);
		
	}
	
	return $date_calc;

}

function wday($year_mon_day='') {
	if (!empty($year_mon_day)) {
		list($year,$mon,$day)=explode('-',$year_mon_day);
		if (checkdate($mon,$day,$year)) {
			$day_ary = getdate(mktime(0,0,0,$mon,$day,$year));
		}
	}
	return (empty($day_ary)?false:$day_ary[wday]);
}

//--- 取得該週的起始及結束日
function weekdate($year_mon_day='') {
	if (empty($year_mon_day)) $year_mon_day=date("Y-m-d");
	list($year,$mon,$day)=explode('-',$year_mon_day);
	if (checkdate($mon,$day,$year)) {
		$day1 = wday($year_mon_day);
		$date_time = mktime(11,30,0,$mon,$day,$year);
		$date_time -= 86400*$day1;
		$day_ary = getdate($date_time);
		$date1 = sprintf("%04d-%02d-%02d",$day_ary[year]
			,$day_ary[mon],$day_ary[mday]);
		$date_time += 86400*6;
		$day_ary = getdate($date_time);
		$date2 = sprintf("%04d-%02d-%02d",$day_ary[year]
			,$day_ary[mon],$day_ary[mday]);
		$weekstr = $date1.'/'.$date2;
			
	}
	return $weekstr;
}

function sch_week($year,$seme,$stop_date='') {
	global $CONN;
	
	if (empty($year)) $year=curr_year();
	if (empty($seme)) $seme=curr_seme();
	$sql = "select * from school_day where year='{$year}' and seme='{$seme}'";
	$rs = $CONN->Execute($sql);
	while ($rs and $ro=$rs->FetchNextObject()) {
		if ($ro->DAY_KIND=='st_start') $start_date = $ro->DAY;
		elseif ($ro->DAY_KIND=='st_end') $end_date = $ro->DAY;
	}
	if (empty($stop_date)) $stop_date=$end_date;
	$curr_date = $start_date;
	$weekno=0;
	while ($curr_date < $stop_date) {
		++$weekno;
		//---- 若要增加第 ?? 週時應在 SMARTY  的樣版檔中增加即可
		//--- 或是在 chc_prn_week.php 中作增加的動作
		//--$week_ary["$weekno"]="第".sprintf("%02d",$weekno)."週 (".weekdate($curr_date).")";
		$week_ary["$weekno"]=weekdate($curr_date);
		$curr_date = date_skip($curr_date, 7);
	
	}
	arsort($week_ary);
	
	return $week_ary;	
}


//--- 取得指定學年及學期的年級及班級陣列
function sch_class_all($curr_year, $curr_seme) {
	global $CONN, $IS_JHORES;
	if (empty($curr_year)) $curr_year=curr_year();
	if (empty($curr_seme)) $curr_seme=curr_seme();
	//---取得各年級的班級名稱陣列
	$sql = "select class_id from school_class where year='{$curr_year}' and semester='{$curr_seme}' order by class_id";

	$rs = $CONN->Execute($sql);
	while ($rs and $ro=$rs->FetchNextObject() ){
		$arys[$ro->CLASS_ID]=$ro->CLASS_ID;
	}
	$rs->Close();
	return $arys;

}

//---傳入sql語法，取得資料後以陣列的格式傳回
function sql2array($sql='',$key_fld='',$debug=false) {
	global $CONN;
	$target = false;
	if (!empty($sql)) {
		$CONN->debug = $debug;
		$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
		$rs = $CONN->Execute($sql);
		if ($rs or die($sql));
		$target = array();
		while ($ro=$rs->FetchNextObject()) {
			$key_val = '';
			if (!empty($key_fld)) {
				$key_fld = strtoupper($key_fld);
				$key_val = $ro->{$key_fld};
			}
			if (!empty($key_val)) $target[$key_val] = get_object_vars($ro);
			else $target[] = get_object_vars($ro);
		}
		$CONN->debug = false;	
	}
	
	return $target;
}


class data_class{
	var $class_id; //-- 班級代號
	var $year;
	var $seme;
	var $grade;
	var $clano;

//--- 取得某個學期的班級學生名單
function stud_base($class_id) {
	global $CONN;
	//$CONN->debug=true;
	if (empty($class_id)) $class_id=$this->class_id;
	list($year,$seme,$grade,$clano)=explode("_",$class_id);
	$seme_year_seme = sprintf("%03d%d",$year,$seme);
	$seme_class = sprintf("%d%02d",$grade,$clano);

	$sql = "select stud_seme.student_sn,stud_seme.seme_num,stud_base.stud_id,stud_base.stud_name from stud_seme left join stud_base on (stud_seme.student_sn=stud_base.student_sn) where stud_seme.seme_year_seme='{$seme_year_seme}' and stud_seme.seme_class='{$seme_class}' AND stud_base.stud_name IS NOT NULL  and (stud_base.stud_study_cond='0' or stud_base.stud_study_cond='2') order by seme_num";

	/*
	print "<pre>";
	print_r($arys);
	print "</pre>";
	*/
	return sql2array($sql,'student_sn');
}


//--統計班級在某一段期間的缺席資料
function absent_sum($sdate='',$edate='',$daily=true,$class_id='') {
	global $CONN;
	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;

	if (empty($class_id)) $class_id = $this->class_id;
	if (empty($sdate)) $sdate = date("Y-m-d");
	if (empty($edate)) $edate = date("Y-m-d");
	
	list($year,$seme,$grade,$clano)=explode("_",$class_id);
	$seme_year_seme=sprintf("%03d%d",$year,$seme);
	$seme_class = sprintf("%d%02d",$grade,$clano);

	$query="select sections from score_setup where year='".intval(substr($this->class_id,0,3))."' and semester='".intval(substr($this->class_id,4,2))."' and class_year='".intval(substr($this->class_id,7,2))."'";
	$res=$CONN->Execute($query);
	$SECTIONS=$res->fields['sections'];

	$sql = "select stud_seme.student_sn, stud_absent.stud_id
		,stud_absent.date ,stud_absent.absent_kind, stud_absent.section 
		from stud_absent
		left join stud_seme 
			on (stud_absent.stud_id=stud_seme.stud_id 
				and stud_seme.seme_year_seme='{$seme_year_seme}' )
		where stud_absent.year='{$year}'	and stud_absent.semester='{$seme}' 
			and stud_absent.date<='{$edate}'  
			and stud_absent.absent_kind!='' 
			and stud_seme.seme_class='{$seme_class}' 
		order by stud_seme.student_sn,stud_absent.date ";
		
	//$CONN->debug=true;
	$rs = $CONN->Execute($sql);
	$abs_data=array();
	while ($rs and $ro=$rs->FetchNextObject()) {
		if (!empty($ro->SECTION)) {
			if ($ro->SECTION=='allday') {
				$ADD_SECTIONS=$SECTIONS;
			} else {
				$ADD_SECTIONS=1;
			}
			if ($ro->SECTION=='uf' or $ro->SECTION=='df') {
				if ($ro->ABSENT_KIND=='曠課') {
					$abs_data[$ro->STUDENT_SN][ABS4_SEME]++;
					if ($ro->DATE>=$sdate and $ro->DATE<=$edate) {
						$abs_data[$ro->STUDENT_SN][ABS4]++;
						if ($daily) 
							$abs_data[$ro->STUDENT_SN][$ro->DATE][ABS4]++;
					}
				}
			}
			elseif ($ro->ABSENT_KIND=='事假') {
				$abs_data[$ro->STUDENT_SN][ABS1_SEME]+=$ADD_SECTIONS;
				if ($ro->DATE>=$sdate and $ro->DATE<=$edate) {
					$abs_data[$ro->STUDENT_SN][ABS1]+=$ADD_SECTIONS;
					if ($daily) $abs_data[$ro->STUDENT_SN][$ro->DATE][ABS1]+=$ADD_SECTIONS;
				}
			}
			elseif ($ro->ABSENT_KIND=='病假') {
				$abs_data[$ro->STUDENT_SN][ABS2_SEME]+=$ADD_SECTIONS;
				if ($ro->DATE>=$sdate and $ro->DATE<=$edate) {
					$abs_data[$ro->STUDENT_SN][ABS2]+=$ADD_SECTIONS;
					if ($daily) $abs_data[$ro->STUDENT_SN][$ro->DATE][ABS2]+=$ADD_SECTIONS;
				}
			}
			elseif ($ro->ABSENT_KIND=='曠課') {
				$abs_data[$ro->STUDENT_SN][ABS3_SEME]+=$ADD_SECTIONS;
				if ($ro->SECTION=='allday') {
					$abs_data[$ro->STUDENT_SN][ABS4_SEME]+=2;
				}
				if ($ro->DATE>=$sdate and $ro->DATE<=$edate) {
					$abs_data[$ro->STUDENT_SN][ABS3]+=$ADD_SECTIONS;
					if ($ro->SECTION=='allday') {
						$abs_data[$ro->STUDENT_SN][ABS4]+=2;
					}
					if ($daily) {
						$abs_data[$ro->STUDENT_SN][$ro->DATE][ABS3]+=$ADD_SECTIONS;
						if ($ro->SECTION=='allday') {
							$abs_data[$ro->STUDENT_SN][$ro->DATE][ABS4]+=2;
						}
					}
				}
			}
			elseif ($ro->ABSENT_KIND=='公假') {
				$abs_data[$ro->STUDENT_SN][ABS5_SEME]+=$ADD_SECTIONS;
				if ($ro->DATE>=$sdate and $ro->DATE<=$edate) {
					$abs_data[$ro->STUDENT_SN][ABS5]+=$ADD_SECTIONS;
					if ($daily) $abs_data[$ro->STUDENT_SN][$ro->DATE][ABS5]+=$ADD_SECTIONS;
				}
			}
			elseif ($ro->ABSENT_KIND=='其他') {
				$abs_data[$ro->STUDENT_SN][ABS6_SEME]+=$ADD_SECTIONS;
				if ($ro->DATE>=$sdate and $ro->DATE<=$edate) {
					$abs_data[$ro->STUDENT_SN][ABS6]+=$ADD_SECTIONS;
					if ($daily) $abs_data[$ro->STUDENT_SN][$ro->DATE][ABS6]+=$ADD_SECTIONS;
				}
			}
		}
	}
	//$CONN->debug=false;
	$rs->Close();
	$stud = $this->stud_base();
	foreach ($stud as $student_sn=>$stud_data) {
		if ($abs_data[$student_sn][ABS1]+$abs_data[$student_sn][ABS2]
			+$abs_data[$student_sn][ABS3]+$abs_data[$student_sn][ABS4]
			+$abs_data[$student_sn][ABS5]+$abs_data[$student_sn][ABS6]>0) {
			$stud_ary[$student_sn]=$stud_data;
			$stud_ary[$student_sn]['ABSENT']=$abs_data[$student_sn];
		}
	}
	//$arys = sql2array($sql,'' ,true);
	//print "<pre>";
	//print_r($abs_data);
	//print_r($stud_ary);
	//print "</pre>";
	
	return $stud_ary;
}


//--- 建構函數一定要放在最後面
	function data_class($class_id='') {
		global $IS_JHORES;
		$this->class_id=$class_id;
		if (!empty($this->class_id)) {
			list($this->year,$this->seme,$this->grade,$this->clano)=explode("_",$this->class_id);
			
			$this->grade -= $IS_JHORES;
			$this->year=Num2CNum($this->year+0);
			$this->seme=Num2CNum($this->seme+0);
			$this->grade=Num2CNum($this->grade+0);
			$this->clano=Num2CNum($this->clano+0);
		}
	}

}

//---- 94.01.07
//---取得某段期間有缺課的班級資料
function absent_class($sdate, $edate, $year,$seme) {
	global $CONN;
	if (empty($year)) $year=curr_year();
	if (empty($seme)) $seme=curr_seme();
	$class_id = sprintf("%03d_%d_",$year,$seme);

	$sql = "select class_id from stud_absent where date>='{$sdate}' and date<='{$edate}' 
		and class_id like '{$class_id}%' group by class_id order by class_id";

	$rs = $CONN->Execute($sql);
	while ($rs and $ro=$rs->FetchNextObject()) {
		$arys[$ro->CLASS_ID]=$ro->CLASS_ID;
	}
	$rs->Close();
	return $arys;
}


?>
