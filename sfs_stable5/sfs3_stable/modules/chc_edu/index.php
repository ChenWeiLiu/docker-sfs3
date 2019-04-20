<?php
//$Id: index.php 9096 2017-06-20 07:47:05Z chiming $
include "config.php";

//認證
sfs_check();

//秀出網頁布景標頭
head("彰化縣報表");
//顯示SFS連結選單(欲使用請拿開註解)
echo make_menu($school_menu_p);
//建立物件
$obj= new score_ss($CONN,$smarty);
//初始化
$obj->init();
//處理程序,有時程序內有header指令,故本程序宜於head("score_ss模組");之前
$obj->process();

$tpl=dirname(__file__)."/templates/chc_index.htm";

$smarty->assign("obj",$obj);
$smarty->display($tpl);
//主要內容


//佈景結尾
foot();



//物件class
class score_ss{
	var $CONN;//adodb物件
	var $smarty;//smarty物件
	var $size=10;//每頁筆數
	var $page;//目前頁數
	var $tol;//資料總筆數
   var $IS_JHORES;
	var $year;
   var $seme;
   var $YS='year_seme';//下拉式選單學期的奱數名稱
   var $year_seme;//下拉式選單班級的奱數值
   var $Sclass='class_id';//下拉式選單班級的奱數名稱
   var $grade_name='Grade';//下拉式選單年級的奱數名稱
   var $Grade;

	//建構函式
	function score_ss($CONN,$smarty){
		$this->CONN=&$CONN;
		$this->smarty=&$smarty;
      global $IS_JHORES;
      
      //6年級
      $this->Grade=6;
      //103_2
      $this->year_seme=curr_year()."_".curr_seme();
      //學年度
      $this->year=curr_year();
      //學期 
      $this->seme=curr_seme();
	}
	//初始化
	function init() {}
	//程序
	function process() {
		$this->all();
	}

	//擷取資料
	function all(){
		$SQL="select year,semester,count(*) as tol from score_ss 
		group by year,semester ";
		$rs=$this->CONN->Execute($SQL) or die($SQL);
		$this->aTol=$rs->GetArray();

		if ($this->year=='') return ;
		if ($this->seme=='') return ;
		if ($this->Grade=='') return ;
		//所有課程設定
		$SQL="select * from score_ss where year='{$this->year}' 	and  semester='{$this->seme}' and class_year='{$this->Grade}' and enable=1 order by ss_id";
		$rs=$this->CONN->Execute($SQL) or die($SQL);
		$arr=$rs->GetArray();
		$this->all=$arr;//return $arr;
		// echo "<pre>";print_r($this->all);
		$this->tol=count($arr);
		/*取課程中文名稱*/
  		$SQL="select subject_id,subject_name from score_subject ";
		$rs=$this->CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
		if ($rs->RecordCount()==0) return "尚未設定任何科目資料！";
		$obj=$rs->GetArray();
		foreach($obj as $ary){
			$id=$ary['subject_id'];
			$this->Subj[$id]=$ary['subject_name'];
		}
		//echo "<pre>";print_r($this->Subj);
		$this->ScoTol();
		$this->SsidToName=$this->SsidToName();
		//echo "<pre>";print_r($this->SsidToName);
	}
	function SsidToName(){
		foreach ($this->all as $ary){
			$id=$ary[ss_id];
			$scope=$ary[scope_id];
			$subject=$ary[subject_id];
			$AA[$id]=$id.'.'.$this->Subj[$scope].':'.$this->Subj[$subject].'(有'.($this->ScoTol2[$id]+0).'筆成績)';
		}	
	
	return $AA;
	
	}



	//取所有成績統計 by ss_id
	function ScoTol(){
		
		$TB='score_semester_'.$this->year.'_'.$this->seme;
  		$SQL="SELECT class_id ,ss_id,count(*)  as  stol  FROM  {$TB}  group  by class_id,ss_id ";
		$rs=$this->CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
		if ($rs->RecordCount()==0) return "尚未設定任何科目資料！";
		$obj=$rs->GetArray();
		foreach ($obj as $ary){
			$cla=$ary[class_id];
			$ssid=$ary[ss_id];		
			$this->ScoTol[$cla][$ssid]=$ary[stol];
			$this->ScoTol2[$ssid]=$this->ScoTol2[$ssid]+$ary[stol];
		}
	
	
	}

} 
