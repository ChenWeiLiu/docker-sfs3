<?php
// $Id: rand_set.php 5310 2009-01-10 07:57:56Z hami $
class rand_set{
	var $CONN;//adodb物件
	var $smarty;//smarty物件
	var $options;//公用選項..不含資料庫
	var $SFS_PATH;
	var $UPLOAD_PATH;
	var $IS_JHORES;
	var $mPath;//模組檔案路徑
   public function __construct()   {
   }
   
	function init(){
		$this->Seme=chkStr('Seme');
		$this->Grade=chkStr('Grade');	
	}
	function process(){
		$this->sGrade=$this->sGrade();
		if ($_GET[del]!='' ) $this->delfile();
		$this->List=$this->gList();	
		$this->sYear=$this->sYear();

		if ($this->Seme!='' && $this->Grade!='') {
			$this->rWord=$this->rWord($this->Seme,$this->Grade);
			if ($_POST[form_act]=='setSave') $this->setSave();
			$this->sClass=$this->gClass($this->Seme,$this->Grade);
			}

	}

  	//顯示
	function display(){
		head("在籍生亂數編班");
		print_menu($this->sfs_menu);
		$tpl = __My_Path.'templates/'.$this->mod.'.htm';
		$this->smarty->assign("this",$this);
		$this->smarty->display($tpl);
		foot();//佈景結尾
	}
	##################  學期下拉式選單函式  ##########################
	function sYear() {
		$SQL = "select * from school_day where  day<=now() and day_kind='start' order by day desc ";
		$rs=$this->CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
		while(!$rs->EOF){
		$ro = $rs->FetchNextObject(false);
		// 亦可$ro=$rs->FetchNextObj()，但是僅用於新版本的adodb，目前的SFS3不適用
		$year_seme=$ro->year."_".$ro->seme;
		$obj_stu[$year_seme]=$ro->year."學年度第".$ro->seme."學期";
		}
		return $obj_stu;
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
				break;//有成績的
			case '2':
				$rWord[Y]=$ss[0]+1;
				$rWord[S]=1;
				$rWord[G]=$Grade+1;
				$rWord[Gw]=$GG[$Grade+1];
				break;//有成績的
		} 
		return $rWord;
	}

	###########################################################
	##  傳入年級,學年度,學期 預設值為all表示將傳出所有年級與班級
	##  傳出以  class_id  為索引的陣列  
	function gClass($year_seme,$grade) {
		$CID=split("_",$year_seme);//093_1
		//$curr_year=sprintf("%03d",$CID[0]);
		$curr_year=$CID[0];
		$curr_seme=$CID[1];
		$ADD_SQL=" and c_year='$grade'  ";
		$SQL="select class_id,c_name,teacher_1 from  school_class where 
		year='$curr_year' and semester='$curr_seme' and enable=1  $ADD_SQL order by class_id  ";
		$rs=$this->CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
		//echo $SQL;
		if ($rs->RecordCount()==0) return"尚未設定班級資料！";
		$obj_stu=$rs->GetArray();
		return $obj_stu;
	}


	function gList() {
		//96_2_1		$a=dir($this->mPath) or backe("！！路徑誤錯，無法讀取！！");
		while($file=$a->read()) {
		if( $file=='.' ||$file=='..' || $file=='.htaccess') continue;
		if (ereg("stu",$file))  continue;
		$f=explode('_',$file);
		$Seme=$f[0].'_'.$f[1];
		$Grade=$f[2];
		$AA[file]=$file;
		$AA[rWord]=$this->rWord($Seme,$Grade);
		$ary[]=$AA;
		unset($AA);
		}		
		return $ary;
	}
	function setSave() {
		if (count($_POST[Rand][class_id])==0) backe('未選參加班級！');
		$AA[Rand]=$_POST[Rand];
		$AA[Rand][Seme]=$this->Seme;
		$AA[Rand][Grade]=$this->Grade;
		$AA[Rand][gName]=$this->sGrade($this->Grade);
		$AA[Rand][oldTol]=count($_POST[Rand][class_id]);
		$AA[Rand][rWord]=$this->rWord;
		
		
		
		$file=$this->mPath.$this->Seme.'_'.$this->Grade;
		if (!file_exists($file)) {
			$str=serialize($AA);
			$fpWrite=fopen($file,"a");//打開
			fwrite($fpWrite,$str);//寫入
			fclose($fpWrite);//關上
			$URL=$_SERVER[PHP_SELF]."?step=".$this->mod;
			Header("Location:$URL");
		}else{ backe('己有設定！無法再寫入！');}

	}


	function delfile() {
		$delfile=$_GET[del];
		$info=$this->gFile($delfile);
		if ($info[Rand][Test]=='N') backe('非一般操作模式，無法刪除！');
		
		
		$file=$this->mPath.$delfile;
		if (file_exists($file)) {
			$f2=$this->mPath.$delfile.'_stu';
			$f3=$this->mPath.$delfile.'_stu_OK';
			if (file_exists($f2)) backe('己有學生記錄');
			if (file_exists($f3)) backe('己有編班結果');
			unlink($file);
			$URL=$_SERVER[PHP_SELF]."?step=".$this->mod;
			Header("Location:$URL");
		}
	}
	//讀取名冊檔案
	function gFile($file) {
		//$file=$this->Fi;
		$f1=$this->mPath.$file;
		$f2=$this->mPath.$file.'_stu';//基本資料檔
		$f3=$this->mPath.$file.'_stu_OK';//完成結果檔
		//if (file_exists($f3)) backe('己完成亂數編班程序，禁止重編！');
		if (file_exists($f1)) {
			$aa=file_get_contents($f1);
			$aa=unserialize($aa);
			}
		return $aa;
	}




//end class
}



