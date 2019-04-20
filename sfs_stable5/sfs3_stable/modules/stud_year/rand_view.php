<?php
//$Id: rand_view.php 5310 2009-01-10 07:57:56Z hami $
include "stud_year_config.php";
include_once "rand/rand_tool.php";


$smarty->left_delimiter="{{";
$smarty->right_delimiter="}}";

//建立物件
$obj= new rand_view();
//$obj->CONN=&$CONN;
$obj->smarty=&$smarty;
$obj->SFS_PATH=&$SFS_PATH;
$obj->UPLOAD_PATH=&$UPLOAD_PATH;
$obj->IS_JHORES=&$IS_JHORES;
$obj->mSch=get_school_base();
//處理程序
$obj->run();

class rand_view{
	var $CONN;//adodb物件
	var $smarty;//smarty物件
	var $options;//公用選項..不含資料庫
	var $SFS_PATH;
	var $UPLOAD_PATH;
	var $IS_JHORES;
	var $mPath;//模組檔案路徑
	var $sex=array(1=>'男',2=>'女');
	var $sex1=array(1=>'<font color=#0000FF>男</font>',2=>'<font color=#FF0000>女</font>');

   public function __construct() { }
   
	function init(){
		$dir = dirname($_SERVER[PHP_SELF]);
		$dir_ary = explode('/',$dir);
		$dir_name=end($dir_ary);
		$mPath=$this->UPLOAD_PATH.'school/'.$dir_name.'/';
		if (!file_exists($mPath)) backe('找不到目錄無法運作');
		$this->mPath=&$mPath;
		define('__My_Path', $this->SFS_PATH.'/modules/'.$dir_name.'/');//程式路徑
		
		$this->Fi=chkStr('Fi');
		//$this->class_id=chkStr('class_id');
	}
	function run(){
		$this->init();
		$this->sGrade=$this->sGrade();//年級陣列
		if ($this->Fi!='') {
			$this->Info=$this->gFile();//讀取資料,也帶入$this->allStu
			$this->New=$this->gNew();//讀取資料
			}
		$this->display();
	}
	function gNew(){
		$file=$this->Fi;
		$f3=$this->mPath.$file.'_stu_OK';//完成結果檔
		if (file_exists($f3)) {
			$aa=file_get_contents($f3);
			$aa=unserialize($aa);
			}
		foreach ($aa as $k=>$cla){
			foreach ($cla as $stu){
				($stu[stud_sex]==1) ? $tol[$k][boy]++:$tol[$k][girl]++;
				//特殊男,女
				if ($stu[type]==3 && $stu[stud_sex]==1) $tol[$k][sboy]++;
				if ($stu[type]==3 && $stu[stud_sex]==2) $tol[$k][sgirl]++;
				//($stu[type]==1) ? $tol[$k][boy]++:$tol[$k][girl]++;
			}
			$tol[$k][tol]=$tol[$k][boy]+$tol[$k][girl];
			$tol[$k][stol]=$tol[$k][sboy]+$tol[$k][sgirl];
		}
			$kk[stu]=&$aa;
			$kk[tol]=&$tol;
		return $kk;
	}

	function stuInfo(){
		foreach ($this->allStu as $cla_id=> $cla){
			foreach ($cla as $stu){
				$info[type][$stu[type]]++;
				if ($stu[stud_sex]=='1') {
					$info[boy]++;
					$info[type2][$stu[type]][boy]++;}
				if ($stu[stud_sex]=='2') {
					$info[girl]++;
					$info[type2][$stu[type]][girl]++;}
					unset($stu);
			}
		
		}
		return $info;
	}


  	//顯示
	function display(){
		//head("在籍生亂數編班");
		$tpl = __My_Path.'templates/rand_view.htm';
		$this->smarty->assign("this",$this);
		$this->smarty->display($tpl);
		//foot();//佈景結尾
	}

	##################陣列列示函式2##########################
	function sGrade($k='') {
		($this->IS_JHORES==6) ? $all_grade=array(7=>"一年級",8=>"二年級",9=>"三年級"):$all_grade=array(1=>"一年級",2=>"二年級",3=>"三年級",4=>"四年級",5=>"五年級",6=>"六年級");
		if ($k=='')	return $all_grade;
		else 	return $all_grade[$k];
	 }

	//傳入學期,年級
	function rWord($Seme,$Grade){
		$ss=split("_",$Seme);
		 $GG=&$this->sGrade;
		switch ($ss[1]) {
			case '1':
				$rWord[Y]=$ss[0];
				$rWord[S]=2;
				$rWord[G]=$Grade;
				$rWord[Gw]=$GG[$Grade];
				break;//上學期
			case '2':
				$rWord[Y]=$ss[0]+1;
				$rWord[S]=1;
				$rWord[G]=$Grade+1;
				$rWord[Gw]=$GG[$Grade+1];
				break;//下學期
		} 
		return $rWord;
	}



	//讀取名冊檔案
	function gFile() {
		$file=$this->Fi;
		$f1=$this->mPath.$file;
		$f2=$this->mPath.$file.'_stu';//基本資料檔
		$f3=$this->mPath.$file.'_stu_OK';//完成結果檔
		//if (file_exists($f3)) backe('己完成亂數編班程序，禁止重編！');
		if (file_exists($f1)) {
			$aa=file_get_contents($f1);
			$aa=unserialize($aa);
			}
		if ( file_exists($f2)) {
			$ss=file_get_contents($f2);
			$this->allStu=unserialize($ss);//echo "<pre>";print_r($this->Stu);
			}

		return $aa;
	}

function Full_TD($data,$num) {
//echo "XX--<br>";print_r($data);
	$all=count($data);
	$loop=ceil($all/$num);
	$flag=$num-1;//幾格的key
	$all_td=($loop*$num)-1;//最大值小1
	$show=array();$i=0;
	foreach ($data as $key=>$ary ){
		(($i%$num)==$flag && $i!=0 && $i!=$all_td ) ? $ary[next_line]='yes':$ary[next_line]='';
		$show[$key]=$ary;
		$i++;
		}
	if ($i<=$all_td ){
		for ($i;$i<=$all_td;$i++){
			$key='Add_Td_'.$i;
		(($i%$num)==$flag && $i!=0 && $i!=$all_td ) ? $show[$key][next_line]='yes':$show[$key][next_line]='';
		}
	}
		return $show;
}



//---end class
}



