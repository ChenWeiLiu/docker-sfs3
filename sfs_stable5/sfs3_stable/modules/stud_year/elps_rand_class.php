<?php
//$Id: elps_rand_class.php 5310 2009-01-10 07:57:56Z hami $
include "stud_year_config.php";
include_once "rand/rand_tool.php";
//認證
sfs_check();

//秀出網頁布景標頭


$smarty->left_delimiter="{{";
$smarty->right_delimiter="}}";

//建立物件
$obj= new My_rand();
//$obj->init();
$obj->CONN=&$CONN;
$obj->smarty=&$smarty;
$obj->SFS_PATH=&$SFS_PATH;
$obj->sfs_menu=&$menu_p;
$obj->UPLOAD_PATH=&$UPLOAD_PATH;
$obj->IS_JHORES=&$IS_JHORES;
//處理程序
$obj->run();


//物件class
class My_rand{
	var $CONN;//adodb物件
	var $smarty;//smarty物件
	var $options;//公用選項..不含資料庫
	var $SFS_PATH;
	var $UPLOAD_PATH;
	var $IS_JHORES;
	var $My_Path;//程式目錄
	var $mPath;//模組檔案路徑
	var $action;
	private $mod; //使用的class模組
   public function __construct()   {  }
	 function init()   {
		$dir = dirname($_SERVER[PHP_SELF]);
		$dir_ary = explode('/',$dir);
		$dir_name=end($dir_ary);
		$mPath=$this->UPLOAD_PATH.'school/'.$dir_name.'/';
		if (!file_exists($mPath)) mkdir($mPath, 0777);
		if (!file_exists($mPath)) backe('無法建立儲存目錄<br>'.$mPath);
		$this->mPath=&$mPath;
		define('__My_Path', $this->SFS_PATH.'/modules/'.$dir_name.'/');//程式路徑
	}
	//程序
	function run() {
		$this->init();
		$this->load_mod();
		if ($this->action!=''){
			if (! class_exists($this->action)) backe('物件不存在！');
			$this->module= new $this->action();
			$this->module->CONN=&$this->CONN;//載入--子類別要用到的資料庫物件
			$this->module->SFS_PATH=&$this->SFS_PATH;
			$this->module->mPath=&$this->mPath;
			$this->module->sfs_menu=&$this->sfs_menu;
			$this->module->mSch=get_school_base();//print_r($this->module->mSch);
			$this->module->SFS_PATH=&$this->SFS_PATH;
			$this->module->mod=&$this->action;
			$this->module->IS_JHORES=&$this->IS_JHORES;
			$this->module->init();//載入--子類別要用到的物件
			$this->module->process();//子類別程序--程式動作
			$this->module->smarty=&$this->smarty;//子類別程序--啟用Smarty物件
			$this->module->display();//子類別程序--樣版檔相關

		}else {
			$this->display();
		}


	}
	//載入模組
	private function load_mod(){
		$action=chkStr('step');
		if ($action=='') return ;
		$file=__My_Path.'/rand/'.$action.'.php';//echo$file; 
		if ($action!='' && file_exists($file)) {
			include_once($file);
			$this->action=$action;	//echo$this->action; 
			}else{
			backe('非法執行！');
			}
		}	
	//顯示
	function display(){
		head("在籍生亂數編班");
		print_menu($this->sfs_menu);
		$tpl = __My_Path."templates/rand_index.htm";
		$this->smarty->assign("this",$this);
		$this->smarty->display($tpl);
		foot();//佈景結尾
	}
	


}//end class

