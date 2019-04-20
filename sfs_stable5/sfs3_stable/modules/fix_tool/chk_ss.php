<?php

require_once("config.php");
//使用者認證
sfs_check();

$obj=new chk_SS();
$obj->CONN=&$CONN;
$obj->smarty=&$smarty;
$obj->process();
head("錯誤課程檢查");
print_menu($school_menu_p);
$obj->display();
foot();


class chk_SS{
	var $CONN;
	var $smarty;
	var $Err_id;//僅錯誤的SS_ID
	var $Err;//僅錯誤的SS_ID陣列含詳細資料
	var $show;//整合為給網頁顯示的資料

	function process(){
		if ($_POST[form_act]=='updata') $this->update();
		$this->chk_ss_id();
		if (count($this->Err_id)==0) return ;
		$this->show=$this->get_err_info();
		//$this->display();
		//echo "<pre>";print_r($aa);
	}

	function update(){
		if (count($_POST[delete_id])==0) return ;
		foreach ($_POST[delete_id] as $key =>$NULL){
			$KK[]=$key;		
		}
		if (count($KK)==0) return ;
		$ssid=join(',',$KK);
		
		$SQL="update score_ss set enable='0' where ss_id in ($ssid) ";		
		$rs=&$this->CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
		
		$URL=$_SERVER[PHP_SELF];
		header("Location:$URL");
	}

	function get_err_info(){
		$tol=$this->chk_scoure();
		$name=$this->subj();

		foreach($this->Err as $ss_id=>$ary){
			$AA[$ss_id]=$ary;
			$AA[$ss_id][scope]=$name[$ary[scope_id]];
			$AA[$ss_id][subject]=$name[$ary[subject_id]];
			$AA[$ss_id][tol]=$tol[$ss_id];		
		}
		return $AA;
	}
	
	function chk_ss_id(){
		$SQL="SELECT * FROM `score_ss` where enable ='1' and class_id !='' order by  ss_id  ";
		//$SQL="SELECT * FROM `score_ss` where  class_id !='' order by  ss_id  ";
		$rs=&$this->CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
		if ($rs->RecordCount()==0) return;
		$All=$rs->GetArray();//echo "<pre>";print_r($All);
		foreach($All as $ary){
			$a=sprintf("%03d",$ary[year])."_".$ary[semester]."_".sprintf("%02d",$ary[class_year]);//095_2_01
			$b=substr($ary[class_id],0,8);
			if ($a!=$b) {
				$Err[$ary[ss_id]]=$ary;
				$all_Err[]=$ary[ss_id];
				}
			unset($ary);		
		}
		$this->Err_id=$all_Err;//僅SS_ID
		$this->Err=$Err;//全部問題資料陣列
	}


	function subj(){
		$SQL="SELECT subject_id ,subject_name  FROM  score_subject  where enable ='1' order by  subject_id   ";
		$rs=&$this->CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
		$All=&$rs->GetArray();
		foreach ($All as $ary){	$Subj[$ary[subject_id]]=$ary[subject_name];}
		return $Subj; 
	} 

	function chk_scoure(){
		if (count($this->Err_id)==0) return ;
		$Err_ss=join(",",$this->Err_id); 
		$SQL="SELECT ss_id ,count(sss_id) as tol FROM  stud_seme_score  
		where ss_id in ($Err_ss) group  by ss_id order by ss_id ";
		$rs=&$this->CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
		if ($rs->RecordCount()==0) return;
		$All=&$rs->GetArray();
		foreach ($All as $ary){	$SS_tol[$ary[ss_id]]=$ary[tol];}
		return $SS_tol; 
	} 


function display(){
	if ($this->tpl=='') $this->tpl=dirname(__file__)."/templates/chk_ss.htm";
		$this->smarty->assign("this",$this);
		$this->smarty->display($this->tpl);
}


}
//end class
