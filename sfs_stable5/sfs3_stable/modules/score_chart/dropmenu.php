<?php
// $Id: sfs_oo_dropmenu.php 5351 2009-01-20 00:39:21Z brucelyc $
//---Class用法-------------//
/*
$ob=new drop($CONN);
echo $ob->select();
$ob=new drop($this->CONN);
$this->select=&$ob->select();
*/

/* 功能 :自動產生學年度與班級的下拉式選單
可產生class_id=095_1_02_02的值供程式應用
本class取通用變數 $IS_JHORES,也須傳入ADO物件$CONN
*/ 
class drop {
	var $CONN;//ADO物件
	var $IS_JHORES;//國中小
	var $year;//學年
	var $seme;//學期
	var $YS='year_seme';//下拉式選單學期的奱數名稱
	var $year_seme;//下拉式選單班級的奱數值
	var $Sclass='class_id';//下拉式選單班級的奱數名稱
	var $Skind='kind';//下拉式選單班級的奱數名稱

	function drop($CONN){
		global $IS_JHORES;
		$this->CONN=&$CONN;
		$this->IS_JHORES=$IS_JHORES;
		($_GET[$this->YS]=='') ? $this->year_seme=curr_year()."_".curr_seme():$this->year_seme=$_GET[$this->YS];
		$aa=split("_",$this->year_seme);
		$this->year=$aa[0];
		$this->seme=$aa[1];
	}

##################  學期下拉式選單函式  ##########################
function select() {
	$SQL = "select * from school_day where  day<=now() and day_kind='start' order by day desc ";
	$rs=&$this->CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
	while(!$rs->EOF){
		$ro = $rs->FetchNextObject(false);
		// 亦可$ro=$rs->FetchNextObj()，但是僅用於新版本的adodb，目前的SFS3不適用
		$year_seme=$ro->year."_".$ro->seme;
		$obj_stu[$year_seme]=$ro->year."學年度第".$ro->seme."學期";
	}
	$str="<select name='".$this->YS."' onChange=\"location.href='".$_SERVER[SCRIPT_NAME]."?".$this->YS."='+this.options[this.selectedIndex].value;\">\n";
		//$str.="<option value=''>-未選擇-</option>\n";
	foreach($obj_stu as $key=>$val) {
		($key==$this->year_seme) ? $bb=' selected':$bb='';
		$str.= "<option value='$key' $bb>$val</option>\n";
		}
	$str.="</select>";
	$str.=$this->grade();
	$str.=$this->grade2();
	return $str;
}
##################陣列列示函式2##########################
function grade() {
	//名稱,起始值,結束值,選擇值
	$url="?".$this->YS."=". $this->year_seme."&".$this->Sclass."=";
	($this->IS_JHORES==6) ? $grade=array(7=>"一年",8=>"二年",9=>"三年"):$grade=array(1=>"一年",2=>"二年",3=>"三年",4=>"四年",5=>"五年",6=>"六年");

	$SQL="select class_id,c_year,c_name,teacher_1 from  school_class where year='".$this->year."' and semester='".$this->seme."' and enable=1  order by class_id  ";
	$rs=&$this->CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
	if ($rs->RecordCount()==0) return"尚未設定班級資料！";
	$All=$rs->GetArray();
	$str="<select name='".$this->Sclass."' onChange=\"location.href='".$url."'+this.options[this.selectedIndex].value;\">\n";
	$str.= "<option value=''>-未選擇-</option>\n";
	foreach($All as $ary) {
		($ary[class_id]==$_GET[$this->Sclass]) ? $bb=' selected':$bb='';
		$str.= "<option value='".$ary[class_id]."' $bb>".$grade[$ary[c_year]].$ary[c_name]."班 (".$ary[teacher_1].")</option>\n";
		}
	$str.="</select>";
	
	
	return $str;
	}


##################陣列列示函式2##########################
function grade2() {

	//名稱,起始值,結束值,選擇值
	
	
//echo "<pre>";	
//print_r($_GET);
//echo "</pre>";	
//exit;

	$url="?".$this->YS."=". $this->year_seme."&".$this->Sclass."=".$_GET['class_id']."&".$this->Skind."=";
	
	$array1=array("1"=>"定期","2"=>"平時","3"=>"定期+平時");
	
	$str="<select name='".$this->Skind."' onChange=\"location.href='".$url."'+this.options[this.selectedIndex].value;\">\n";
	$str.= "<option value=''>-未選擇-</option>\n";
	
	foreach($array1 as $key=>$val) {
		($key==$_GET[$this->Skind]) ? $bb=' selected':$bb='';
		$str.= "<option value='".$key."' $bb>".$val."</option>\n";
		}

	$str.="</select>";
	

	return $str;
	}




	
}

?>
