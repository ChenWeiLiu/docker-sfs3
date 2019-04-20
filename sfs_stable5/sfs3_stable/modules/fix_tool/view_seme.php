<?php
//$Id:
include_once "config.php";
//session_start();
sfs_check();
head("課程修正");
print_menu($school_menu_p);


$template_dir = $SFS_PATH."/".get_store_path()."/templates";

$template_file=$template_dir."/view_seme.htm";

// 建立物件
$obj = new seme_chk($CONN,$smarty);

//執行物件程序

$obj->process();

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
		if ($_GET[seme]==''){$this->seme=curr_year()."_".curr_seme();}
		else{	$this->seme=$_GET[seme];}
		
		$this->sel_year($this->seme);
		//echo "AAAAAA---AA";echo $this->sel;
		$this->get_tol();
		}

		   	  //seme_year_seme   	  seme_class   	  seme_class_name   	  seme_num   	  seme_class_year_s   	  seme_class_s   	  seme_num_s   	  student_sn
function get_tol(){
	$seme=split("_",$this->seme);
	
	$seme2=sprintf("%03d",$seme[0]).$seme[1];
	$SQL = "select seme_class,count(student_sn) as tol from stud_seme where  seme_year_seme='{$seme2}' group by  seme_class	  order by seme_class ";
	//echo$SQL; 
	$rs=$this->CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
	$ary=$rs->GetArray();
	$this->seme_chk=$this->add_to_td($ary,5);
	
	}
		   	  

function display($tpl){
		//----Smarty物件----------//
		$this->smarty->assign("this",$this);
		$this->smarty->display($tpl);
	}


##################  學期下拉式選單函式  ##########################
function sel_year($select_t='') {
	//global $CONN ;
	$name='seme';
	$SQL = "select * from school_day where  day<=now() and day_kind='start' order by day desc ";
	$rs=$this->CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
	while(!$rs->EOF){
	$ro = $rs->FetchNextObject(false);
	// 亦可$ro=$rs->FetchNextObj()，但是僅用於新版本的adodb，目前的SFS3不適用
	$year_seme=$ro->year."_".$ro->seme;
	$obj_stu[$year_seme]=$ro->year."學年度第".$ro->seme."學期";
	}
	$str="<select name='$name' onChange=\"location.href='".$_SERVER[PHP_SELF]."?".$name."='+this.options[this.selectedIndex].value;\">\n";
		//$str.="<option value=''>-未選擇-</option>\n";
	foreach($obj_stu as $key=>$val) {
		($key==$select_t) ? $bb=' selected':$bb='';
		$str.= "<option value='$key' $bb>$val</option>\n";
		}
	$str.="</select>";
	$this->sel=$str;
	}



function add_to_td($data,$num) {
	$all=count($data);
	$loop=ceil($all/$num);
	$all_td=($loop*$num)-1;//最大值小1
	for ($i=0;$i<($loop*$num);$i++){
	(($i%$num)==($num-1) && $i!=0 && $i!=$all_td) ? $data[$i][next_line]='yes':$data[$i][next_line]='';
	}
	return $data;
} 


}//end class








?>