<?php
// $Id: rand_input.php 5310 2009-01-10 07:57:56Z hami $
class rand_input{
	var $CONN;//adodb物件
	var $smarty;//smarty物件
	var $options;//公用選項..不含資料庫
	var $SFS_PATH;
	var $UPLOAD_PATH;
	var $IS_JHORES;
	var $mPath;//模組檔案路徑
	var $sex=array(1=>'男',2=>'女');
	var $sex1=array(1=>'<font color=#0000FF>男</font>',2=>'<font color=#FF0000>女</font>');
	var $join1=array(0=>'不參加',1=>'參加',2=>'行為偏差',3=>'特殊生');
   public function __construct()   {
   }
   
	function init(){
		$this->Fi=chkStr('Fi');
	}
	function process(){
		$this->sGrade=$this->sGrade();//年級陣列
		$this->List=$this->gList();//檔案列表	
		if ($this->Fi!='') {
			$this->Info= $this->gInfo();//讀取資料,也帶入$this->allStu
			if ($_POST[form_act]=='Rand_write') $this->Rand_write();			
			}
	//echo "<pre>";print_r($this->List);
	}
	function Rand_write(){
		//$this->Fi;echo "<pre>";//print_r($this->Info);
		$Y=$this->Info[Rand][rWord][Y];//學年
		$S=$this->Info[Rand][rWord][S];//學期
		$G=$this->Info[Rand][rWord][G];//年級
		$CK=$this->chkSemestu($Y,$S,$G);
		if ($CK[tol]=='N') backe('設定不合！！無法寫入！！');//print_r($CK);

		
		$f3=$this->mPath.$this->Fi.'_stu_OK';//完成結果檔
		if (!file_exists($f3)) backe('作業錯誤！！資料不存在！！'); 
		$aa=file_get_contents($f3);
		$aa=unserialize($aa);
		
		$seme=sprintf("%03d",$Y).$S;//0931
		$semeG=sprintf("%03d",$Y).'_'.$S.'_'.sprintf("%02d",$G);//093_1_07
		
		$ClassName=$this->gClassName($Y,$S,$G);//print_r($ClassName);
		
		foreach ($aa as $No=>$sAry){
			$GG1=$G.sprintf("%02d",$No);//503
			$GK=$semeG.'_'.sprintf("%02d",$No);
			$cName=$ClassName[$GK];	//中文班名	
			if ($cName=='')  backe('作業錯誤！！班級資料不完整！！'); 
			foreach ($sAry as $k=>$stu){
				$Num=$k+1;//座號
				$curr=$GG1.sprintf("%02d",$Num);//50302
				$SQLA="update stud_base set curr_class_num='$curr' where
				stud_id='$stu[stud_id]' and student_sn='$stu[sn]' ";
				$SQLB="INSERT INTO stud_seme 
				(stud_id,seme_year_seme,seme_class,seme_class_name,seme_num,student_sn)
				VALUES ('$stu[stud_id]','$seme','$GG1','$cName','$Num','$stu[sn]') ";
			//	echo $SQLA."<br>".$SQLB."<br>";
				$rs=$this->CONN->Execute($SQLA) or die("無法查詢，語法:".$SQLA);
				$rs=$this->CONN->Execute($SQLB) or die("無法查詢，語法:".$SQLB);
				}
			}
		$URL=$_SERVER[PHP_SELF]."?step=".$this->mod;
		Header("Location:$URL");
	}
//取中文班名陣列
	function gClassName($year,$seme,$grade){
		$SQL="select class_id,c_name from  school_class where 
		year='$year' and semester='$seme' and c_year='$grade'  and enable=1    ";
		$rs=$this->CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
		while(!$rs->EOF){
			$ro = $rs->FetchNextObject(false);
			$obj[$ro->class_id]=$ro->c_name;
		}
		return $obj; 

	}
  	//顯示
	function display(){
		head("在籍生亂數編班");
		print_menu($this->sfs_menu);
		$this->smarty->assign("this",$this);
		$tpl = __My_Path.'templates/'.$this->mod.'.htm';
		$this->smarty->display($tpl);
		foot();//佈景結尾	
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



	//讀取名冊列表檔案
	function gList() {
		//96_2_1//
		$a=@dir($this->mPath) or backe("！！路徑誤錯，無法讀取！！");
		while($file=$a->read()) {
		if( $file=='.' ||$file=='..' || $file=='.htaccess') continue;
		if (ereg("stu",$file))  continue;
		$f=explode('_',$file);
		$Seme=$f[0].'_'.$f[1];
		$Grade=$f[2];
		$AA[file]=$file;

		$f2=$this->mPath.$file.'_stu';//基本資料檔
		$f3=$this->mPath.$file.'_stu_OK';//完成結果檔
		if (file_exists($f3)) {$AA[f3]='Y';} else {$AA[f3]='';}
		if (file_exists($f2)) {$AA[f2]='Y';} else {$AA[f2]='';} 
		$AA[rWord]=$this->rWord($Seme,$Grade);
		if ($AA[f3]=='Y'){
			$AA[C4]=$this->chkSemestu($AA[rWord][Y],$AA[rWord][S],$AA[rWord][G]);
			//學年,上/下學期,年級
			}
			else{$AA[C4]='';}
		$ary[]=$AA;
		unset($AA);
		}		
		return $ary;
	}
	function chkSemestu($year,$seme,$grade) {
		$T[tol]='Y';
	//1.檢查開學日
		$SQL = "select * from school_day where  year='$year' 	and seme='$seme'  ";
		$rs=$this->CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
		$tmp[day]=$rs->RecordCount();
		($tmp[day]==0) ? $T[chk_day]='N':$T[chk_day]='Y';
		if ($tmp[day]==0) $T[tol]='N';
		
	//2.檢查班級設定
		$SQL="select class_id,c_name,teacher_1 from  school_class where 
		year='$year' and semester='$seme' and c_year='$grade'  and enable=1    ";
		$rs=$this->CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
		$tmp[sclass]=$rs->RecordCount();
		($tmp[sclass]==0) ? $T[chk_class]='N':$T[chk_class]='Y';
		if ($tmp[sclass]==0) $T[tol]='N';
	//3.檢查學生
		$seme_year_seme=sprintf("%03d",$year).$seme;
		$SQL="SELECT stud_id,student_sn FROM `stud_seme` where seme_year_seme='$seme_year_seme' 
		 and seme_class like '$grade%'  ";
		$rs=$this->CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
		$tmp[stu]=$rs->RecordCount();
		($tmp[stu]==0) ? $T[chk_stu]='Y':$T[chk_stu]='N';
		if ($tmp[stu] > 0) $T[tol]='N';
	//4.檢查是否為本學期
		if ($year==curr_year() && curr_seme()==$seme) {$T[chk_now]='Y';}
		else{$T[chk_now]='N';$T[tol]='N';}

		return $T;
	}
	
	//讀取讀取資料檔名冊檔案
	function gInfo() {
		$file=$this->Fi;
		$f1=$this->mPath.$file;
		$f2=$this->mPath.$file.'_stu';//基本資料檔
		$f3=$this->mPath.$file.'_stu_OK';//完成結果檔
		if (!file_exists($f1)) backe('系統錯誤！！無法讀取資料檔！！');
			$aa=file_get_contents($f1);
			$aa=unserialize($aa);
		return $aa;
	}






//---end class
}



