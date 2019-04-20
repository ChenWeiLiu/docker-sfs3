<?php
//$Id: chc_seme.php 5310 2009-01-10 07:57:56Z hami $
include "config.php";
//認證
sfs_check();


//程式使用的Smarty樣本檔
$template_file = dirname (__file__)."/templates/leader_var.htm";
//建立物件
$obj= new chc_seme($CONN,$smarty);
//初始化
$obj->init();
//處理程序,有時程序內有header指令,故本程序宜於head("score_semester_91_2模組");之前
$obj->process();

//秀出網頁布景標頭
head("[彰]班級幹部管理");

//顯示SFS連結選單(欲使用請拿開註解)
echo make_menu($school_menu_p);
//$ob=new drop($this->CONN,$IS_JHORES);
//		$this->select=$ob->select();
//echo $ob->select();
//顯示內容
$obj->display($template_file);
//佈景結尾
foot();


//物件class
class chc_seme{
	var $CONN;//adodb物件
	var $smarty;//smarty物件
	var $stu;//學生資料
	var $ufile;//科目陣列
	var $StuTitle;
	
	var $kind1=array('班長','副班長','康樂股長','學藝股長','事務股長','衛生股長','風紀股長','輔導股長','環保股長','資訊股長');
	var $kind2=array('國樂社','弦樂團','管樂團','直笛隊','籃球隊');
	var $kind3=array('社長','副社長','隊長','副隊長');
	//建構函式
	function chc_seme($CONN,$smarty){
		global $UPLOAD_PATH;
		$this->CONN=&$CONN;
		$this->smarty=&$smarty;
		$this->uPath=$UPLOAD_PATH;
		$this->ufile=$UPLOAD_PATH.'school/chc_leader/var.txt';
		if (!file_exists($UPLOAD_PATH.'school/')) @mkdir($UPLOAD_PATH.'school/');
		if (!file_exists($UPLOAD_PATH.'school/chc_leader/')) @mkdir($UPLOAD_PATH.'school/chc_leader/');
		//if (!file_exists($this->ufile)) mkdir($UPLOAD_PATH.'school/');
	}
	//初始化
	function init() {

		
		}
	//程序
	function process() {
		if(isset($_POST['form_act']) && $_POST['form_act']=='update') $this->update();
		if(isset($_POST['form_act']) && $_POST['form_act']=='resetvar') $this->reSetvar();
		$this->all();
	}
	//顯示
	function display($tpl){
		$this->smarty->assign("this",$this);
		$this->smarty->display($tpl);
	}
	//初始化
	function reSetvar() {
			$data['A']=$this->kind1;
			$data['B']=$this->kind2;
			$data['C']=$this->kind3;
			$word=serialize($data);
			$chk=$this->upload_write($word);
			if ($chk=='N') backe('!!無法寫入檔案!!');
			
			$URL=$_SERVER['SCRIPT_NAME'];
			Header("Location:$URL");
		}



	//擷取資料
	function update(){
		//班級幹部名稱text欄位處理
		$A=explode("\n",$_POST['kindA']);
		foreach($A as $a){
			$a=strip_tags(trim($a));
			if ($a=='') continue;
			$A1[]=$a;unset($a);
		}	

		//社團名稱text欄位處理
		$B=explode("\n",$_POST['kindB']);
		foreach($B as $b){
			$b=strip_tags(trim($b));
			if ($b=='') continue;
			$B1[]=$b;unset($b);
		}	

		//社團幹部名稱text欄位處理
		$C=explode("\n",$_POST['kindC']);
		foreach($C as $c){
			$c=strip_tags(trim($c));
			if ($c=='') continue;
			$C1[]=$c;unset($c);
		}	
		$ALL['A']=$A1;$ALL['B']=$B1;$ALL['C']=$C1;
		$word=serialize($ALL);
		$chk=$this->upload_write($word);
		if ($chk=='N') backe('!!無法寫入檔案!!');
		


		//$Insert_ID= $this->CONN->Insert_ID();
		$URL=$_SERVER['SCRIPT_NAME'];
		Header("Location:$URL");
	}
	//擷取資料
	function all(){
		if (!file_exists($this->ufile)) :
			$this->data['A']=join("\r",$this->kind1);
			$this->data['B']=join("\r",$this->kind2);
			$this->data['C']=join("\r",$this->kind3);
		else:
			$str=file_get_contents ($this->ufile);
			$data=unserialize($str);
			$this->data['A']=join("\r",$data['A']);
			$this->data['B']=join("\r",$data['B']);
			$this->data['C']=join("\r",$data['C']);
		endif;
		
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
