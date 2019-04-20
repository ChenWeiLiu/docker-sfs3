<?php
//$Id: cal_edit.php 6694 2012-02-21 04:02:28Z infodaes $
include"config.php";

sfs_check();

include_once "cal_elps_class.php";
//引入換頁物件(學務系統用法)
//include_once "../../include/chi_page2.php";

//sfs_check();
/*
if ($_GET[syear]=='' && $_POST[syear]=='') {
	$now_Syear=sprintf("%03d",curr_year()).curr_seme();
	header("Location:$_SERVER[PHP_SELF]?syear=$now_Syear");
	}
*/

//如果是要進行視覺化週次調整
if($_GET['scroll']=='up'){
	$SQL="UPDATE cal_elps SET week=week-1 WHERE id=".$_GET['id'];
	$rs=$CONN->Execute($SQL) or die($SQL);
}
if($_GET['scroll']=='down'){
	$SQL="UPDATE cal_elps SET week=week+1 WHERE id=".$_GET['id'];
	$rs=$CONN->Execute($SQL) or die($SQL);
}

class cal_edit extends cal_elps{
	var $UN;//選擇的類別
	var $color=array("#ADD8E6","#E6E6FA","#FFC0CB","#90EE90");
	//初始化
	function init() {
		($_GET[syear]=='') ? $this->seme=$_POST[syear]: $this->seme=$_GET[syear];
		if ($this->seme=='') {
			$now_Syear=sprintf("%03d",curr_year()).curr_seme();
			header("Location:$_SERVER[PHP_SELF]?syear=$now_Syear");	
		}
		//die("未傳值"); 
		$this->UN=$_GET[UN];

	}
	//程序
	function process() {
		$this->init();
		//if ($_POST[form_act] != '') {echo "<pre>";print_r($_POST);die();}
		if ($_POST[form_act]=='update') $this->update();//更新
		if ($_POST[form_act]=='add') $this->add();//新增
		if ($_GET[form_act]=='del') $this->del();//刪除

		$this->get_all_set();//取全部學期行事曆設定
		$this->get_use_set();//取使用中行事曆設定
		//$this->get_all_event();//加入所有行事資料
		if ($_GET[act]=='add_form') return ;
		$this->all();
	}
	//顯示
	function display(){
		$tpl=dirname(__file__)."/templates/edit.html";
		$this->smarty->assign("this",$this);
		$this->smarty->display($tpl);
	}
	//擷取資料
	function all(){
		if ($this->UN=='') return;
		$SQL="select * from cal_elps  where syear='$this->seme' and unit='{$this->UN}'  order by week asc,unit   ";
		$rs=$this->CONN->Execute($SQL) or die($SQL);
		$arr=$rs->GetArray();
		$this->all=$arr;//return $arr;
	//產生連結頁面
		//$this->links= new Chi_Page($this->tol,$this->size,$this->page);
	}
	//新增
	function add(){
		if ($_POST[wek]=='' || $_POST[syear]=='' || $_POST[unit]=='' || $_POST[event]=='') $this->BK("未傳值!");
		$day=date("Y-m-d H:i:s");
		$SN=$_SESSION[session_tea_sn];
		foreach( $_POST[wek] as $key=>$val) {
			$SQL="INSERT INTO cal_elps(syear,week,unit,event,user,day,important) VALUES ('$_POST[syear]', '$key', '$_POST[unit]', '$_POST[event]', '$SN','$day', '$_POST[important]')";
			$rs=&$this->CONN->Execute($SQL) or die($SQL);
		}
		//$Insert_ID= $this->CONN->Insert_ID();
		$URL=$_SERVER[PHP_SELF]."?syear=".$_POST[syear]."&UN=".$_POST[unit];
		Header("Location:$URL");
	}
	//更新
	function update(){
		$day=date("Y-m-d H:i:s");
		$SQL="update  cal_elps set week ='{$_POST['week']}', unit ='{$_POST['unit']}', day ='{$day}', event ='{$_POST['event']}', important='{$_POST['important']}' where id ='{$_POST['id']}'";
		$rs=$this->CONN->Execute($SQL) or die($SQL);
		$URL=$_SERVER[PHP_SELF]."?syear=".$_POST['syear']."&UN=".$_POST['unit'];
		Header("Location:$URL");
	}
	//刪除
	function del(){
		$SQL="Delete from  cal_elps  where  id='{$_GET['id']}' ";
		$rs=$this->CONN->Execute($SQL) or die($SQL);
		$URL=$_SERVER[PHP_SELF]."?syear=".$_GET['syear']."&UN=".$_GET['UN'];
		Header("Location:$URL");
	}
	function color($num){
		$k=$num % 4;
		return $this->color[$k];
	}

	function wk_checkbox(){
		$i=0;$tmp='';
		foreach ($this->WK as $wk){
			$str="<label><input type='checkbox' name='wek[".$wk[No]."]'>第".$wk[No]."週<font size='2' color='#BFBFBF'>".$wk[st_day]."--".$wk[en_day]."</font></label>&nbsp;\n";
			$tmp=$tmp.$str;
			if ($i%2==1) $tmp=$tmp."<br>";
			$i++;
		}
		return $tmp;
	}

	function wk_select($select=''){
		$tmp='';
		foreach ($this->WK as $wk){
			($wk[No]==$select) ? $t="selected": $t="";
			$str="<option value='".$wk[No]."' $t>第".$wk[No]."週 ".$wk[st_day]."--".$wk[en_day]."</option>\n";
			$tmp=$tmp.$str;
		}
		return $tmp;
	}
}
//建立物件
$obj= new cal_edit();
$obj->CONN=&$CONN;
$obj->smarty=&$smarty;
$obj->process();

//秀出網頁布景標頭
head("校務行事曆");

//顯示SFS連結選單(欲使用請拿開註解)
//echo make_menu($school_menu_p);
$link2="syear=$_GET[syear]";

if ($_SESSION[session_tea_sn]!='') print_menu($school_menu_p,$link2);
//myheader();
//顯示內容
$obj->display();
//佈景結尾
foot();

?>