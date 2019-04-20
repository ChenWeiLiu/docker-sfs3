<?php
// $Id: rand_set_stu.php 5310 2009-01-10 07:57:56Z hami $
class rand_set_stu{
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
		$this->class_id=chkStr('class_id');
	}
	function process(){
		$this->sGrade=$this->sGrade();//年級陣列
		$this->List=$this->gList();//檔案列表	
		$this->sYear=$this->sYear();//學期陣列
		//$this->sClass=$this->gClass($this->Seme,$this->Grade);//班級陣列

		if ($this->Fi!='') $this->Info=$this->gFile();//讀取資料
		if ($this->Fi!='' && $_GET[act]=='mkFile' ) $this->mkFile();
		if ($this->Fi!='' && $_GET[act]=='delFile' ) $this->delFile();
		if ($this->Fi!='' && $this->class_id!='' && $_POST[act]=='stuSave') $this->stuSave();
		if ($this->Fi!='' && $this->class_id!='') $this->Stu=$this->allStu[$this->class_id];
	

	}

	//產生名冊
	function mkFile(){
		$file=$this->Fi;
		
		$f2=$this->mPath.$file.'_stu';//基本資料檔
		$f3=$this->mPath.$file.'_stu_OK';//完成結果檔

		foreach ($this->Info[Rand][class_id] as $class_id =>$class_name ){
		//echo $class_id.':'.$class_name.'<br>';
			$tmp=$this->gStu($class_id);
			foreach ($tmp as $sn=>$ary){
				$stu[$class_id][$sn][class_name]=$class_name;
				$stu[$class_id][$sn][sn]=$ary[student_sn];
				$stu[$class_id][$sn][stud_id]=$ary[stud_id];
				$stu[$class_id][$sn][seme_num]=$ary[seme_num];
				$stu[$class_id][$sn][stud_name]=$ary[stud_name];
				$stu[$class_id][$sn][stud_sex]=$ary[stud_sex];
				$stu[$class_id][$sn][type]='1';
				$stu[$class_id][$sn][ncla]='';
				$stu[$class_id][$sn][nnum]='';
				unset($ary);
				}					
			unset($class_id);unset($tmp);
		}
	
		$str=serialize($stu);
		$fpWrite=fopen($f2,"w");//打開
		fwrite($fpWrite,$str);//寫入
		fclose($fpWrite);//關上
		$URL=$_SERVER[PHP_SELF]."?step=".$this->mod;
		Header("Location:$URL");
	}

	//儲存變更
	function stuSave(){
		$file=$this->Fi;
		$class_id=$this->class_id;
		$stu=&$this->allStu;
		$f2=$this->mPath.$file.'_stu';//基本資料檔
		foreach ($_POST[type] as $sn=>$val){
			$stu[$class_id][$sn][type]=$val;
		}
		$str=serialize($stu);
		$fpWrite=fopen($f2,"w");//打開
		fwrite($fpWrite,$str);//寫入
		fclose($fpWrite);//關上
		$URL=$_SERVER[PHP_SELF]."?step=".$this->mod.'&Fi='.$file.'&class_id='.$this->class_id;
		Header("Location:$URL");
	}
	//刪除名冊
	function delFile(){
		if ($this->Info[Rand][Test]=='N') backe('非一般操作模式，無法刪除！');
		$file=$this->Fi;
		$f2=$this->mPath.$file.'_stu';//基本資料檔
		$f3=$this->mPath.$file.'_stu_OK';//完成結果檔
		if (file_exists($f3)) backe('己完成亂數編班程序，禁止重編！');
		unlink($f2);
		$URL=$_SERVER[PHP_SELF]."?step=".$this->mod;
		Header("Location:$URL");
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
		$ary[]=$AA;
		unset($AA);
		}		
		return $ary;
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


	
/* 取學生陣列,取自stud_base表與stud_seme表*/
	function gStu($class_id){
		$CID=split("_",$class_id);//093_1_01_01
		$year=$CID[0];
		$seme=$CID[1];
		$grade=$CID[2];//年級
		$class=$CID[3];//班級
		$CID_1=$year.$seme;
		$CID_2=sprintf("%03d",$grade.$class);
		$SQL="select 	a.student_sn,a.stud_id,b.seme_num,
		a.stud_name,a.stud_sex,
		b.seme_year_seme,b.seme_class,a.stud_study_cond  
		from stud_base a,stud_seme b where 
		a.student_sn=b.student_sn	
		and b.seme_year_seme='$CID_1'
		and (a.stud_study_cond='0' or a.stud_study_cond='15')
		and b.seme_class='$CID_2'  
		order by b.seme_num ";
		$rs=&$this->CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
		$obj_stu=array();
		while ($rs and $ro=$rs->FetchNextObject(false)) {
			$obj_stu[$ro->student_sn] = get_object_vars($ro);
		}
		return $obj_stu;	
	}

//---end class
}



