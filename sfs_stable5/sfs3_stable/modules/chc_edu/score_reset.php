<?php
//$Id: index.php 5310 2009-01-10 07:57:56Z hami $
include "config.php";

//認證
sfs_check();

//程式使用的Smarty樣本檔
$tpl=dirname(__file__)."/templates/score_reset.htm";

//建立物件
$obj= new score_reset($CONN,$smarty);

//初始化
$obj->init();

//處理程序,有時程序內有header指令,故本程序宜於head之前
$obj->process();

//秀出網頁布景標頭
head(" 學期成績無條件進位結算");

//顯示SFS連結選單
echo make_menu($school_menu_p);

//主要內容
$obj->display($tpl);

//佈景結尾
foot();

//物件class
class score_reset{
	var $CONN;//adodb物件
	var $smarty;//smarty物件
	
	//建構函式
	function score_reset($CONN,$smarty){
		$this->CONN=&$CONN;
		$this->smarty=&$smarty;
	}
	
	//初始化
	function init() {
		$this->seme_select=$this->year_seme_menu($sel_year,$sel_seme,"year_seme");
		$this->act=$_REQUEST['act'];	
	}

	//程序
	function process() {
		$this->resetdata();
	}
	
	//顯示
	function display($tpl){
		$this->smarty->assign("this",$this);
		$this->smarty->display($tpl);
	}

	// 寫入資料表
	function resetdata() {
	    if ($this->act=="send") {
			$select_year_seme=explode("-",$this->year_seme=$_REQUEST[year_seme]);
			$sel_year=sprintf("%03d", $select_year_seme[0]);
			$sel_seme=$select_year_seme[1];
			$year_seme=$sel_year.$sel_seme;
			//~ print_r($year_seme);
			$SQL="UPDATE stud_seme_score SET ss_score=CEIL(ss_score) WHERE seme_year_seme='{$year_seme}'";
			$this->CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
			echo "<div align='center'><p>{$sel_year}學年度第{$sel_seme}學期學期成績已結算完成！</p><br /><input type='button' onclick='history.back()' value='回到上一頁'></input></div>";
			die();
		}
	}
	
	//下拉式選單
	function year_seme_menu($sel_year,$sel_seme,$name="year_seme"){
		global $CONN;
		if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);
		$sql="SELECT year,semester FROM school_class WHERE enable='1' ORDER BY year DESC,semester DESC";
		$recordSet=$CONN->Execute($sql) or user_error($sql, 256);
		$other_year=array();
		$option="";
		while(list($year,$semester)=$recordSet->FetchRow()){
			$ys=sprintf("%03d", $year)."學年度"."第".$semester."學期";
			if(!in_array($ys,$other_year)){
				$other_year[$i]=$ys;
				$selected=($year==$sel_year and $semester==$sel_seme)?"selected":"";
				$option.="<option value='".$year."-".$semester."' $selected>$ys</option>";
				$i++;
			}
		}
		if(empty($option))trigger_error("查無任何學期資料", 256);
		$main="<select name='$name'>
		$option
		</select>";
		return $main;
	}

}
