<?php
//$Id: PHP_tmp.html 5310 2009-01-10 07:57:56Z hami $
include "config.php";
//認證
sfs_check();


//程式使用的Smarty樣本檔
$template_file = dirname (__file__)."/templates/ind.htm";

//建立物件
$obj= new basic_chc($CONN,$smarty);
//初始化
$obj->init();
//處理程序,有時程序內有header指令,故本程序宜於head("12basic_chc模組");之前
$obj->process();


//秀出網頁布景標頭
head("[彰]班級幹部管理");


//顯示SFS連結選單(欲使用請拿開註解)
echo make_menu($school_menu_p);

//顯示內容
$obj->display($template_file);
//佈景結尾
foot();


//物件class
class basic_chc{
	var $CONN;//adodb物件
	var $smarty;//smarty物件
	var $size=10;//每頁筆數
	var $page;//目前頁數
	var $tol;//資料總筆數

	//建構函式
	function basic_chc($CONN,$smarty){
		global $UPLOAD_PATH,$SFS_PATH_HTML;
		$this->SFS_PATH_HTML=$SFS_PATH_HTML;
		$this->CONN=&$CONN;
		$this->smarty=&$smarty;
		$this->uPath=$UPLOAD_PATH;
		$this->ufile=$UPLOAD_PATH.'school/chc_leader/tol.cache';
		if (!file_exists($UPLOAD_PATH.'school/')) @mkdir($UPLOAD_PATH.'school/');
		if (!file_exists($UPLOAD_PATH.'school/chc_leader/')) @mkdir($UPLOAD_PATH.'school/chc_leader/');
	}
	//初始化
	function init() {$this->page=($_GET[page]=='') ? 0:$_GET[page];}
	//程序
	function process() {
		if ($_GET['act']=='update') $this->updateDate();
		$this->all();
	}

	function updateDate() {
		@unlink($this->ufile);
		$URL=$_SERVER['SCRIPT_NAME'];
		Header("Location:$URL");
	}


	//顯示
	function display($tpl){
		$this->smarty->assign("this",$this);
		$this->smarty->display($tpl);
	}
	//擷取資料
	function all(){
		if (!file_exists($this->ufile)) :
			
			$SQL="select id,student_sn,seme,kind,org_name from chc_leader  ";
			$rs=$this->CONN->Execute($SQL) or die($SQL);
			$tol=$rs->RecordCount();
			if ($tol==0) return;
			$arr=$rs->GetArray();
			foreach($arr as $ary){
				$seme=$ary['seme'];
				$kind=$ary['kind'];
				$Tol[$seme][$kind]++;
				$Tol[$seme]['Tol']++;
				$KK[$kind]++;
				$KK['Tol']++;
			}
			$All['data']=$Tol;
			$All['kind']=$KK;
			$All['utime']=date('Y-m-d H:i:s');
			$word=serialize($All);
			$chk=$this->upload_write($word);
			if ($chk=='N') backe('!!無法寫入檔案!!');
			$this->all=$All;
		
		else:
			$str=file_get_contents ($this->ufile);
			$this->all=unserialize($str);
		endif;

	//產生連結頁面
	}


//--- 將資料寫至上傳目錄中的檔案
function upload_write($ftxt) {
	
	$fname = $this->ufile;
	//print "<br> 1234 fname={$fname}";
	$handle=fopen($fname,"w+");
	if ($handle) {
		$bytes = fwrite($handle,$ftxt);
		fclose($handle);
	}else{
		return 'N';		
		}
	
	return 'Y';
}





}


