<?php
//$Id: cal_print.php 5310 2009-01-10 07:57:56Z hami $

if ($_GET[syear]=='') die("<div align='center'><h1>無學年度資料</h1></div>");
if ($_GET[mode]=='') die("<div align='center'><h1>無列印參數資料</h1></div>");

/*引入學務系統設定檔*/
include"config.php";
include_once "cal_elps_class.php";
sfs_check();


class cal_index extends cal_elps{
	//初始化
	function init() {
		$this->seme=$_GET[syear];
		$this->mod=$_GET[mode];
	}
	//程序
	function process() {
		$this->init();
		$this->get_all_set();//取全部學期行事曆設定
		$this->get_use_set();//取使用中行事曆設定
		$this->get_all_event();//加入所有行事資料
		//$this->all();
	}
	//顯示
	function display(){
		$tpl=dirname(__file__)."/templates/cal_print.html";
		$this->smarty->assign("this",$this);
		$content= $this->smarty->fetch($tpl);
		if ($this->mod=="doc" || $this->mod=="sxw" ) {
			$filename=$this->seme."_cal.".$this->mod;
			header("Content-type: application/x-download");
			header("Content-disposition: filename=$filename");
			echo $content;
			}
		else{
			echo  $content;
		}

	}

}


//建立物件
$obj= new cal_index();
$obj->CONN=&$CONN;
$obj->smarty=&$smarty;
$obj->process();
$obj->display();

?>