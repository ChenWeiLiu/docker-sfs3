<?php
//$Id: chc_seme.php 5310 2009-01-10 07:57:56Z hami $
// ini_set('display_errors', '1');
include "config.php";
include "chc_func_class.php";
//認證
sfs_check();

if ($_SESSION['session_who']!='學生') exit;
if ($_SESSION['session_tea_sn']=='') exit;
// print_r($_SESSION);


//$school=get_school_base();print_r(	$school);$school['sch_sheng']='彰化縣';
//print_r($_SESSION);
//引入換頁物件(學務系統用法)
// include_once "../../include/sfs_oo_dropmenu.php";
//程式使用的Smarty樣本檔
$template_file = dirname (__file__)."/templates/stud_per.htm";

//建立物件
$obj= new chc_seme($CONN,$smarty);
//初始化
$obj->init();
//判別國中6/國小0 變數
//$obj->IS_JHORES=$IS_JHORES;

//處理程序,有時程序內有header指令,故本程序宜於head("score_semester_91_2模組");之前
$obj->process();

//秀出網頁布景標頭
//head("彰化區免試入學");

//顯示SFS連結選單(欲使用請拿開註解)
//echo make_menu($school_menu_p);
//$ob=new drop($this->CONN,$IS_JHORES);
//		$this->select=$ob->select();
//echo $ob->select();
//顯示內容
$obj->display($template_file);
//佈景結尾
//foot();


//物件class
class chc_seme{
	var $CONN;//adodb物件
	var $smarty;//smarty物件
	var $stu;//學生資料
	var $subj;//科目陣列
	var $rule;//等第
	var $Stu_Seme;//學生的學期陣列
	var $IS_JHORES;//國中小
	var $year;//學年
	var $seme;//學期
	var $YS='year_seme';//下拉式選單學期的奱數名稱
	var $year_seme;//下拉式選單班級的奱數值
	var $Sclass='class_id';//下拉式選單班級的奱數名稱
	var $reward_kind =array("1"=>"嘉獎一次","2"=>"嘉獎二次","3"=>"小功一次","4"=>"小功二次","5"=>"大功一次","6"=>"大功二次","7"=>"大功三次","-1"=>"警告一次","-2"=>"警告二次","-3"=>"小過一次","-4"=>"小過二次","-5"=>"大過一次","-6"=>"大過二次","-7"=>"大過三次");

	var $race_area=array('1'=>'國際','2'=>'全國 臺灣區','3'=>'區域性(跨縣市)','4'=>'省 直轄市','5'=>'縣市區(鄉鎮)','6'=>'校內');
	var $race_kind=array('1'=>'個人賽','2'=>'團體賽');
//系統獎懲設定參考
	//$reward_good_arr=array("1"=>"嘉獎一次","2"=>"嘉獎二次","3"=>"小功一次","4"=>"小功二次","5"=>"大功一次","6"=>"大功二次","7"=>"大功三次");
	//$reward_bad_arr=array("-1"=>"警告一次","-2"=>"警告二次","-3"=>"小過一次","-4"=>"小過二次","-5"=>"大過一次","-6"=>"大過二次","-7"=>"大過三次");
	//$reward_arr=array("1"=>"嘉獎一次","2"=>"嘉獎二次","3"=>"小功一次","4"=>"小功二次","5"=>"大功一次","6"=>"大功二次","7"=>"大功三次","-1"=>"警告一次","-2"=>"警告二次","-3"=>"小過一次","-4"=>"小過二次","-5"=>"大過一次","-6"=>"大過二次","-7"=>"大過三次");
	
	
	//建構函式
	function chc_seme($CONN,$smarty){
		global $IS_JHORES;
		$this->CONN=&$CONN;
		$this->smarty=&$smarty;
		$this->IS_JHORES=$IS_JHORES;
		$this->Sn=$_SESSION['session_tea_sn'];
	}
	//初始化
	function init() {}
	//程序
	function process() {
		//if(isset($_POST['form_act']) && $_POST['form_act']=='update') $this->update();
		
		$this->all();
		//echo $this->year;
	}
	//顯示
	function display($tpl){
		//$ob=new drop($this->CONN);
		//$this->select=$this->select();
		$this->smarty->assign("this",$this);
		$this->smarty->display($tpl);
	}
	//擷取資料
	function all(){
		$this->sch=get_school_base();
		$this->get_stu();
		
//		print_r($this->sco);		
	}
/* 取學生陣列,取自stud_base表與stud_seme表*/
	function get_stu(){
		$SQL="select a.stud_id,a.stud_name,a.stud_birthday ,a.stud_sex, a.stud_person_id ,a.curr_class_num,
		b.seme_class,b.seme_num,a.stud_study_cond ,c.*  
		from stud_base a, stud_seme b, chc_basic12 c  where 
		c.student_sn='{$this->Sn}' and c.student_sn=a.student_sn and c.student_sn=b.student_sn 
		 order by b.seme_year_seme desc limit 1 ";
		//-- 102.10.29 加入可以讓1-2年級查詢的語法
		$SQL="select a.stud_id,a.stud_name,a.stud_birthday ,a.stud_sex, a.stud_person_id ,a.curr_class_num,
		b.seme_class,b.seme_num,a.stud_study_cond , c.*  
		from stud_base a, stud_seme b 
		LEFT JOIN chc_basic12 c 
		ON ( b.student_sn=c.student_sn  )  
		where a.student_sn='{$this->Sn}' 
		and a.student_sn=b.student_sn  
		order by b.seme_year_seme desc limit 1 ";
		//-- 102.10.29
		$rs=&$this->CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
		if ($rs->RecordCount()==0) backe("01.系統暫無您的資料！");
		$All=$rs->GetArray();
		$this->stu=$All[0];
		$this->stu[student_sn]=$this->Sn;//--106.04.28 新增此行,讓一二年級可網路查詢自己的檢核表		
		$SQL="select a.student_sn,a.stud_id,a.stud_study_cond,
		b.seme_year_seme,b.seme_class,b.seme_num 
		from stud_base a, stud_seme b, chc_basic12 c  where 
		c.student_sn='{$this->Sn}' and c.student_sn=a.student_sn and c.student_sn=b.student_sn 
		order by b.seme_year_seme asc ";
		//-- 102.10.29 修改為不須 chc_basic12 資料語法
		$SQL="select a.student_sn,a.stud_id,a.stud_study_cond,b.seme_year_seme,b.seme_class,b.seme_num 
		      from stud_base a, stud_seme b  
		      where  a.student_sn='{$this->Sn}' 
		      and b.student_sn=a.student_sn  
		      order by b.seme_year_seme asc ";
		$rs=&$this->CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
		if ($rs->RecordCount()==0) backe("02.系統暫無您的資料！");
		$this->Stu_Seme=$rs->GetArray();
	}

	//服務時數
	function get_service( $student_sn ){
		//echo $student_sn;
	$SQL = "select a.year_seme,a.item,a.memo,a.service_date,a.sponsor,b.*
	 from stud_service a ,stud_service_detail b  
	 where b.student_sn='{$student_sn}'  and b.item_sn =a.sn order by a.service_date asc 	 ";
	$rs=&$this->CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
	if ($rs->RecordCount()==0) return;
	return $rs->GetArray(); 
	}

	//取競賽記錄
	function get_race($sn){
	$SQL = "SELECT  *  FROM `career_race`  where student_sn='{$sn}' order by  certificate_date  asc 	 ";
	$rs=&$this->CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
	if ($rs->RecordCount()==0) return ;
	return $rs->GetArray(); 
	}


	//取獎勵
	function get_reward($sn){
	$SQL = "SELECT  *  FROM `reward`  where reward_kind >0 and student_sn='{$sn}' order by  (abs(reward_year_seme)+10000),reward_date ";
	$rs=&$this->CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
	if ($rs->RecordCount()==0) return ;
	return $rs->GetArray(); 
	}
	//取懲罰
	function get_reward2($sn){
	// 101學年度才開始採計,且未銷過者
	//$SQL = "SELECT  *  FROM `reward`  where reward_kind < 0 and student_sn='{$sn}' and reward_cancel_date='0000-00-00' and reward_year_seme >='1011' order by  (abs(reward_year_seme)+10000),reward_date ";
// 取消101學年度限制,改為自行判斷 by 103.03.20
	$SQL = "SELECT  *  FROM `reward`  where reward_kind < 0 and student_sn='{$sn}' and reward_cancel_date='0000-00-00'  order by  (abs(reward_year_seme)+10000),reward_date ";
	$rs=&$this->CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
	if ($rs->RecordCount()==0) return ;
	return $rs->GetArray(); 
	}
		
	//取缺曠課
	function get_abs(){

	$stud_id='';
	foreach ($this->Stu_Seme as $ary){
		//學期所有
		// 取消1011學年度限制,改為自行判斷 by 103.03.20
		//if ($ary['seme_year_seme'] < '1011') continue;
		$seme[]=$ary['seme_year_seme'];
		$stud_idA=$ary['stud_id'];
		// 檢查學號
		if ($stud_id=='')$stud_id=$stud_idA;
		if ($stud_id != $stud_idA) backe('!!同一位學生有不同學號!!');
		}
	//$SQL="SELECT * FROM `stud_absent`  where   stud_id='{$stud_id}' order by `date` ";
// 取消101學年度限制,改為自行判斷 by 103.03.20
	$SQL="SELECT * FROM `stud_absent`  where `year` >='100' and  stud_id='{$stud_id}' 	and  absent_kind ='曠課' ";
	//$SQL="SELECT * FROM `stud_absent`  where  stud_id='{$stud_id}' 	and  absent_kind ='曠課' ";
	$rs=&$this->CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
	if ($rs->RecordCount()==0) return ;
	$A=$rs->GetArray();
	foreach ($A as $ary){
		$K=$ary['date'];
		$stu[$K]['year']=$ary['year'];
		$stu[$K]['semester']=$ary['semester'];
		$stu[$K]['class_id']=$ary['class_id'];
		$stu[$K]['date']=$ary['date'];
		$stu[$K]['stud_id']=$ary['stud_id'];
		$stu[$K]['absent_kind']=$ary['absent_kind'];
		if ($ary['section']=='uf') $ary['section']='升';
		if ($ary['section']=='df') $ary['section']='降';
		//if ($ary['section']=='allday') $ary['section']='全日';
		$stu[$K]['section']=$ary['section'];
		$stu[$K]['section2']=$stu[$K]['section2']." ".$ary['section'];
	}
	return $stu; 
	}	

	//取均衡學習
	function get_balance($SN){

		$student_data=new data_student($SN);
		//print "<pre>";
		$tmp=array();$A=array();
		//$tmp= $student_data->all_score;
		$tmp['健康與體育'] =$student_data->all_score['健康與體育'];
		$tmp['藝術與人文'] =$student_data->all_score['藝術與人文'];
		$tmp['綜合活動'] =$student_data->all_score['綜合活動'];
		if($tmp['健康與體育']['items'] > 1 ) {$A['H'] =$tmp['健康與體育']['sub_arys']['平均'];}
		else{$A['H']=$tmp['健康與體育']['sub_arys']['健康與體育'];}
		
		if($tmp['藝術與人文']['items'] > 1 ) {$A['A'] =$tmp['藝術與人文']['sub_arys']['平均'];}
		else{$A['A']=$tmp['藝術與人文']['sub_arys']['藝術與人文'];}
		
		if($tmp['綜合活動']['items'] > 1 ) {$A['B'] =$tmp['綜合活動']['sub_arys']['平均'];}
		else{$A['B']=$tmp['綜合活動']['sub_arys']['綜合活動'];}
		foreach ($A['H'] as $seme=>$sco){
			if ($A['H'][$seme]['score']>=60 &&$A['A'][$seme]['score']>=60 &&$A['B'][$seme]['score']>=60) 
			{$A['Tol'][$seme]='符合';$A['Tol']['Pass']++;}
			else{$A['Tol'][$seme]='--';}
		}
		// if ($A['Tol']['Pass'] >=2 )$A['Tol']['Sco']=2;
		// if ($A['Tol']['Pass'] >=4 )$A['Tol']['Sco']=4;
		// if ($A['Tol']['Pass'] >=5 )$A['Tol']['Sco']=7;



		return $A;
	}
	
	//社團參與
	function get_club($student_sn) {
	$SQL = "SELECT  *  
	        FROM `association`  
	        where student_sn='{$student_sn}' 
	        order by  seme_year_seme  asc 	 ";	
	$rs=&$this->CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
	if ($rs->RecordCount()==0) return;
/*
	echo "<pre>";
	print_r($rs->GetArray());
	echo "</pre>";
*/
	return $rs->GetArray();         	
	}

	
	//取均衡學習
	function gCH($seme){
		$A=array(1=>'一',2=>'二',3=>'三',4=>'四',5=>'五',6=>'六',7=>'一',8=>'二',9=>'三');
		$B=array(1=>'上',2=>'下');
		$tmp=explode('_',$seme);
		return $A[$tmp[0]].$B[$tmp[1]];
	}

	//取均衡學習
	function gLeader($sn){
		$SQL="SELECT * FROM `chc_leader`  where `student_sn`='{$sn}' order by seme,kind,org_name ";
		$rs=&$this->CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
		if ($rs->RecordCount()==0) return ;
		$A=$rs->GetArray();
		
		// $A=array(1=>'一',2=>'二',3=>'三',4=>'四',5=>'五',6=>'六',7=>'一',8=>'二',9=>'三');
		// $B=array(1=>'上',2=>'下');
		// $tmp=explode('_',$seme);

		return $A;
	}

	//計算學期
	function gSeme($day){
		//$day="2014-08-02";
		$D=explode('-',$day);
		if ($D[1]<=7){$Y=$D[0]-1912;$S=2;}else{$Y=$D[0]-1911;$S=1;}
		return $Y.$S;
	}
function tol20($max,$a) {
	if ($a>$max) return $max;
	return $a;
}
//取異動記錄
function  get_move($SN){
	$stud_coud=study_cond();//學籍資料代碼
	$SQL = "select * from stud_move where student_sn='$SN' order by move_date";
	$rs=$this->CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
	if ($rs->RecordCount()==0) return "查無資料";
	$stu=$rs->GetArray();
	for ($i=0;$i<$rs->RecordCount();$i++){
		$stu[$i]['c_move_kind']=$stud_coud[$stu[$i]['move_kind']];
	}
	return $stu;
}


}//end class
