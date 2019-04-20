<?php
// $Id: rand_run.php 5310 2009-01-10 07:57:56Z hami $
class rand_run{
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
		//$this->class_id=chkStr('class_id');
	}
	function process(){
		$this->sGrade=$this->sGrade();//年級陣列
		$this->List=$this->gList();//檔案列表	
		//亂數編班
		if ($_POST[form_act]=='start' && $this->Fi!='') $this->startRand();
		//班序調整 
		if ($_POST[form_act]=='startOrd' && $this->Fi!='') $this->startOrd();
		
		if ($this->Fi!='') {
			$this->Info= $this->gInfo();//讀取資料,也帶入$this->allStu
			if ($_GET[act]=='del') $this->delFile();			
			if ($_GET[act]=='view' ||$_GET[act]=='prt'||$_GET[act]=='ord') 	$this->New=$this->gNew();//讀取資料
			
			}
	//echo "<pre>";print_r($this->Info);
	}
	function gNew(){
		$file=$this->Fi;
		$f3=$this->mPath.$file.'_stu_OK';//完成結果檔
		if (!file_exists($f3)) backe('作業錯誤！！資料不存在！！'); 
			$aa=file_get_contents($f3);
			$aa=unserialize($aa);
			
			$tol[all]=0;
		foreach ($aa as $k=>$cla){
			$cla_ary[$k]=$k;
			foreach ($cla as $stu){
				($stu[stud_sex]==1) ? $tol[$k][boy]++:$tol[$k][girl]++;
				//特殊男,女
				if ($stu[type]==3 && $stu[stud_sex]==1) $tol[$k][sboy]++;
				if ($stu[type]==3 && $stu[stud_sex]==2) $tol[$k][sgirl]++;
				//($stu[type]==1) ? $tol[$k][boy]++:$tol[$k][girl]++;
				$S_tmp=$stu;
				$S_tmp[ncla]=$k;
				$T_cla[$k][]=$S_tmp;
				unset($S_tmp);
			}
			$tol[$k][tol]=$tol[$k][boy]+$tol[$k][girl];
			$tol[$k][stol]=$tol[$k][sboy]+$tol[$k][sgirl];
			$tol[all]=$tol[all]+$tol[$k][tol];
		}
			$kk[stu]=&$T_cla;
			$kk[tol]=&$tol;
			$kk[k1]=$cla_ary;
			$kk[k2]=join(',',$cla_ary);
		return $kk;
	}

//開始亂數編班
	function startRand(){
		$this->Info= $this->gInfo();//讀取資料,也帶入$this->allStu
		$stu=$this->gStu();//取得全部學生資料
		$file=$this->Fi;
		$f2=$this->mPath.$file.'_stu';//基本資料檔
		$f3=$this->mPath.$file.'_stu_OK';//完成結果檔
		if (file_exists($f3)) backe('己完成亂數編班程序，禁止重編！');

		$RR= new Cla_rand();
		$RR->data=&$stu;//學生資料
		$RR->Num=&$this->Info[Rand][newTol];//新編班數
		$aa=$RR->run();
		$str=serialize($aa);
		$fpWrite=fopen($f3,"w");//打開
		fwrite($fpWrite,$str);//寫入
		fclose($fpWrite);//關上
		$URL=$_SERVER[PHP_SELF]."?step=".$this->mod.'&Fi='.$this->Fi.'&act=view';
		Header("Location:$URL");
	
	}//end Rand

//開始亂數編班
	function startOrd(){
		$this->Info= $this->gInfo();//讀取資料
		$f3=$this->mPath.$this->Fi.'_stu_OK';//完成結果檔
		if (!file_exists($f3)) backe('尚未編班，無法排班序！！');
		$stu=$this->gNew();//取得全部學生資料
		
		foreach ($stu[stu] as $key=>$ary){
			$New=$_POST[Ordclass][$key];
			$NewClass[$New]=$ary;
		}

		ksort($NewClass);
		$str=serialize($NewClass);
		$fpWrite=fopen($f3,"w");//打開
		fwrite($fpWrite,$str);//寫入
		fclose($fpWrite);//關上
		$URL=$_SERVER[PHP_SELF]."?step=".$this->mod.'&Fi='.$this->Fi.'&act=view';
		Header("Location:$URL");
	}//end Rand

  	//顯示
	function display(){
		$this->smarty->assign("this",$this);
		$this->header_tpl=__My_Path.'templates/'.$this->mod.'_header.htm';
		switch ($_GET[act]) {
			case 'prt':
				$tpl = __My_Path.'templates/'.$this->mod.'_prt.htm';
				$this->smarty->display($tpl);
				break;
			case 'view':
				head("在籍生亂數編班");
				print_menu($this->sfs_menu);
				$tpl = __My_Path.'templates/'.$this->mod.'_view.htm';
				$this->smarty->display($tpl);
				foot();//佈景結尾				
				break;
			case 'ord':
				head("在籍生亂數編班");
				print_menu($this->sfs_menu);
				$tpl = __My_Path.'templates/'.$this->mod.'_ord.htm';
				$this->smarty->display($tpl);
				foot();//佈景結尾				
				break;
			default:
				head("在籍生亂數編班");
				print_menu($this->sfs_menu);
				$tpl = __My_Path.'templates/'.$this->mod.'.htm';
				$this->smarty->display($tpl);
				foot();//佈景結尾	
		}
		
		
		
		
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
		$ary[]=$AA;
		unset($AA);
		}		
		return $ary;
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
	//讀取名冊檔案
	function gStu() {
		$file=$this->Fi;
		$f1=$this->mPath.$file;
		$f2=$this->mPath.$file.'_stu';//基本資料檔
		$f3=$this->mPath.$file.'_stu_OK';//完成結果檔
		//if (file_exists($f3)) backe('己完成亂數編班程序，禁止重編！');
		if (!file_exists($f1))  backe('系統錯誤！！無法讀取！！');
		if (!file_exists($f2))  backe('系統錯誤！！無法讀取！！');
		$ss=file_get_contents($f2);
		$ss=unserialize($ss);//echo "<pre>";print_r($this->Stu);
		return $ss;
	}
	//刪除名冊
	function delFile(){
		if ($this->Info[Rand][Test]=='N') backe('非一般操作模式，無法刪除！');
		$file=$this->Fi;
		$f2=$this->mPath.$file.'_stu';//基本資料檔
		$f3=$this->mPath.$file.'_stu_OK';//完成結果檔
		//if (file_exists($f3)) backe('己完成亂數編班程序，禁止重編！');
		unlink($f3);
		$URL=$_SERVER[PHP_SELF]."?step=".$this->mod;
		Header("Location:$URL");
	}

/////-------------補齊顯示用函式2---------------///////
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



