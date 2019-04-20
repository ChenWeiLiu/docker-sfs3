<?php
//$Id: index2.php 8147 2014-09-23 08:28:28Z smallduh $
include"config.php";
include_once "cal_elps_class.php";

//sfs_check();
if (!$_GET[syear]) {
	$now_Syear=sprintf("%03d",curr_year()).curr_seme();
	header("Location:$_SERVER[PHP_SELF]?syear=$now_Syear");
	}
class cal_index extends cal_elps{
	//初始化
	function init() {
		$this->seme=$_GET[syear];	
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
		?>
		<html><meta http-equiv="Content-Type" content="text/html; Charset=Big5"><head>
		<title><?php echo $this->cal_name; ?></title></head><body>	
		<?php
		$tpl=dirname(__file__)."/templates/ind.html";
		$this->smarty->assign("this",$this);
		$this->smarty->display($tpl);
	}

}
//建立物件
$obj= new cal_index();
$obj->CONN=&$CONN;
$obj->smarty=&$smarty;
$obj->process();

//myheader();
//顯示內容
$obj->display();
?>
</body>
</html>
