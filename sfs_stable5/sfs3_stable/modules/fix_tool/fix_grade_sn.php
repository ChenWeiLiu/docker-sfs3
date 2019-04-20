<?php
//$Id:
include_once "config.php";
//session_start();
sfs_check();

//樣版檔案
$template_dir = $SFS_PATH."/".get_store_path()."/templates";
$template_file=$template_dir."/fix_grade_sn.htm";

// 建立物件
$obj = new seme_chk($CONN,$smarty);

//執行物件程序
$obj->process();

head("修正畢業字號SN錯置");
print_menu($school_menu_p);
//顯示物件,檔案結束
$obj->display($template_file);

foot();




class seme_chk {
	var $CONN;
	var $smarty;
	var $seme;
	var $seme_chk;
	var $sel;

	function seme_chk($CONN,$smarty){
		$this->CONN=&$CONN;
		$this->smarty=&$smarty;	
	}

	function process() {
		if ($_GET['form_act']=='update') $this->update();
		$this->all();
		}

function all(){
	$seme=split("_",$this->seme);
	$seme2=sprintf("%03d",$seme[0]).$seme[1];
	$SQL = "select seme_class,count(student_sn) as tol from stud_seme where  seme_year_seme='{$seme2}' group by  seme_class	  order by seme_class ";
	$SQL = "SELECT student_sn FROM `grad_stud` ";
	//echo$SQL; 
	$rs=$this->CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
	$arr=$rs->GetArray();
	$A=array();
	foreach ($arr as $ary ){
		$SN=$ary['student_sn'];
		$A[$SN]++;
		}

	foreach ($A as $K =>$tol ){
		if ($tol>1) $New[]=$K;
		}
	$snstr=join(',',$New);
	//echo $snstr;
	$SQL = "SELECT a.*, b.stud_name, b.stud_sex, b.stud_birthday , 	b.stud_study_year 
	   FROM `grad_stud` a,stud_base b where a.student_sn=b.student_sn and a.student_sn in ($snstr) order by  a.student_sn,  a.grad_sn ";
	$SQL = "SELECT * FROM `grad_stud` where student_sn in ($snstr) order by student_sn,  grad_sn ";

	//echo$SQL; 
	$rs=$this->CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
	$arr=$rs->GetArray();
	if (count($arr)==0) return ;
	$this->all=$arr;
	$id_ary=array();
	foreach ($arr as $ary){
		$ID=$ary['stud_id'];
		$id_ary[$ID]='';
		}
	foreach ($id_ary as $key=>$null){$New2[]=$key;}
	$idstr=join(',',$New2);
	$SQL = "SELECT stud_id,student_sn,stud_name, stud_sex, stud_birthday ,stud_study_year
	 FROM `stud_base` where stud_id in ($idstr) order by stud_id , student_sn ";
	$rs=$this->CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
	$arr=$rs->GetArray();
	foreach ($arr as $ary){
		$ID=$ary['stud_id'];$SN=$ary['student_sn'];
		$this->idsn[$ID][$SN]=$ary;
		}
	}

function GetID($ID){return $this->idsn[$ID];}
   	  

function display($tpl){
		//----Smarty物件----------//
		$this->smarty->assign("this",$this);
		$this->smarty->display($tpl);
	}

function update(){
	$data=strip_tags($_GET['data']);
	if ($data=='') backe2();
	list($grad_sn,$stud_id,$SN)=explode('_',$data);
	$SQL = "update `grad_stud` set student_sn='$SN' where  grad_sn='$grad_sn' and stud_id='$stud_id' ";
	$rs=$this->CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
	//echo $_SERVER['SCRIPT_NAME'];
	header("Location:".$_SERVER['SCRIPT_NAME']);
	//header("Location:".$_SERVER['PHP_SELF']);
	}

}//end class


function backe2($st="未填妥!按下後回上頁重填!") {
echo "<html><head><meta http-equiv='Content-Type' content='text/html; Charset=Big5'><BR><BR><BR><CENTER><form>
	<input type='button' name='b1' value='$st' onclick=\"history.back()\" style='font-size:16pt;color:red'>
	</form></CENTER>";
	exit;
	}






