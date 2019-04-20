<?
//$Id: chi_make_v1.php 5310 2009-01-10 07:57:56Z hami $
include "config.php";
//認證
sfs_check();

/*
//模組設定
include_once "module-cfg.php";
//檢查更新指令
include_once "module-upgrade.php";
*/

//-----------設定檔---------------------//

//include_once("CreateZipFile.inc.php");



$template_file = dirname (__file__)."/template/PHP_tmp.html";


//秀出網頁布景標頭
head("程式工具箱");
echo make_menu($school_menu_p);

$obj= new chi_make($smarty);//$CONN,
$obj->init($mysql_host,$mysql_user,$mysql_pass);
$obj->process();
$obj->display($template_file);


//佈景結尾
foot();


class chi_make{
//	var $CONN;//adodb物件
	var $smarty;//smarty物件

	var $link;
	var $all_db;
	var $all_tb;

	var $DB;//db資料庫
	var $TB;//資料表
	var $field;//欄位


	function chi_make($smarty){
//		$this->CONN=&$CONN;
		$this->smarty=&$smarty;
	}

	function init($mysql_host,$mysql_user,$mysql_pass) {
		$this->link = mysql_connect($mysql_host,$mysql_user,$mysql_pass);
		$this->list_db();
	}

	function process() {
		if($_GET[DB]!='' && $_GET[DB]!='mysql') {$this->DB=$_GET[DB];$this->list_tb();}
		if($_GET[DB]!='' && $_GET[TB]!='') {$this->TB=$_GET[TB];$this->list_field();}
	}

	function display($tpl){
		$this->smarty->assign("this",$this);
		$this->smarty->display($tpl);
	}

	function list_db(){
		$Query="SHOW DATABASES ";
		$data = mysql_query( $Query ); //執行指令取出資料
		while ($row = mysql_fetch_array ($data)) {
			if($row[0]=='mysql') continue;
//			$all_db[][database]=$row[0];
			$all_db[]=$row[0];
		}
		$this->all_db=$all_db;
//		$this->all_db=$this->add_to_td($all_db,6);
	}

	function list_tb(){
		$Query=" SHOW TABLES FROM  `{$this->DB}`  ";
		$data = mysql_query( $Query ) or die($Query); //執行指令取出資料
		while ($row = mysql_fetch_array ($data)) {
//			$tb[][tb]=$row[0];
			$tb[]=$row[0];
		}
		$this->all_tb= $tb;
	}
	function list_field(){
		$Query=" SHOW FIELDS FROM `{$this->TB}`  ";
//		$Query=" SHOW COLUMNS FROM  `{$this->TB}`  ";
		$data = mysql_db_query ($this->DB,$Query);
		while ($row = mysql_fetch_array ($data)) {
			$field[]=$row[0];
			$field_info[]=$row;
		}
		$this->field= $field;
		$this->field_info=$field_info;
//		echo "<PRE>";print_r($field_info);
	}

	function select_db(){
		$word="<select name='ch_page' size='1' class='small' onChange=\"location.href='".$_SERVER[PHP_SELF]."?DB='+this.options[this.selectedIndex].value;\" style='border:2px; background-color:#E5E5E5; font-size:10pt;color:#A52A2A' >";
		$word.= "<option value=''>--未選擇--</option>\n";
		foreach($this->all_db as $key ) {
			($key==$_GET[DB]) ? $bb=' selected':$bb='';
			$str.= "<option value='$key' $bb>$key</option>\n";
			}
		$str.="</select>";
		return $word.$str;
		}

	function select_tb(){
		$word="<select name='ch_page2' size='1' class='small' onChange=\"location.href='".$_SERVER[PHP_SELF]."?DB=".$this->DB."&TB='+this.options[this.selectedIndex].value;\" style='border:2px; background-color:#E5E5E5; font-size:10pt;color:#A52A2A' >";
		$word.= "<option value=''>--未選擇--</option>\n";
		foreach($this->all_tb as $key ) {
			($key==$_GET[TB]) ? $bb=' selected':$bb='';
			$str.= "<option value='$key' $bb>$key</option>\n";
			}
		$str.="</select>";
		return $word.$str;
		}
	function SQL_S($ary){
		foreach($ary as $aa){
		$str[]=" $aa ='{\$_POST['$aa']}'";
		}
		$STR=join(",",$str);
		return $STR;
	}

//------------------語法區------------------------//

	function add(){
		$word="\$SQL=\"INSERT INTO ".$this->TB. "(". join(",",$this->field).")  values ('{\$_POST['".join("']}' ,'{\$_POST['",$this->field)."']}' )\";";
		return $word;
	}
	function add1(){
		$word="\$SQL=\"INSERT INTO ".$this->TB. "(". join(",",$this->field).")  values ('{\$_POST[".join("]}' ,'{\$_POST[",$this->field)."]}' )\";";
		return $word;
	}
	function add2(){
		$word="\$SQL=\"INSERT INTO ".$this->TB. "(". join(",",$this->field).")  values ('{\$".join("}' ,'{\$",$this->field)."}' )\";";
		return $word;
	}	
	
	function up(){
			foreach($this->field as $aa){
				$str[]=" $aa ='{\$_POST['$aa']}'";
				}
			$STR=join(",",$str);
			$word="\$SQL=\"update  ".$this->TB." set  ".$STR." where ".$this->field[0]." ='{\$_POST['".$this->field[0]."']}'\";";
			return $word;	
	}
	function up1(){
			foreach($this->field as $aa){
				$str[]=" $aa ='{\$_POST[$aa]}'";
				}
			$STR=join(",",$str);
			$word="\$SQL=\"update  ".$this->TB." set  ".$STR." where ".$this->field[0]."='{\$_POST[".$this->field[0]."]}' \"; ";
			return $word;	
	}
	function del(){
			$word="\$SQL=\"Delete from  ".$this->TB."  where  ".$this->field[0]."='{\$_POST['".$this->field[0]."']}' \";";
			return $word;	
	}	
	function del1(){
			$word="\$SQL=\"Delete from  ".$this->TB."  where  ".$this->field[0]."='{\$_POST[".$this->field[0]."]}' \";";
			return $word;	
	}	
	function del2(){
			$word="\$SQL=\"Delete from  ".$this->TB."  where  ".$this->field[0]."='{\$_GET['".$this->field[0]."']}' \";";
			return $word;	
	}	
	function select(){
		$word1="\$SQL=\"select * from  ".$this->TB."  \"; ";
		return $word1.$word2.$word3.$word4;
	}
	function select1(){
		$word2="\$SQL=\"select * from  ".$this->TB."  where  ".$this->field[0]."='{\$_GET['".$this->field[0]."']}'\"; ";
		return $word1.$word2.$word3.$word4;
	}
	function select2(){
		$word3="\$SQL=\"select * from  ".$this->TB."  where  ".$this->field[0]."='{\$_POST['".$this->field[0]."']}'\";  ";	
		return $word1.$word2.$word3.$word4;
	}
	function select3(){
		$word4="\$SQL=\"select ".join(",",$this->field)."  from ".$this->TB."   where  ".$this->field[0]."='{\$_POST[".$this->field[0]."]}' \";";	
		return $word1.$word2.$word3.$word4;
	}
	function select4(){
		$word1="\$SQL=\"select ".join(",",$this->field)."&nbsp; from&nbsp;".$this->TB."   where  ".$this->field[0]."='{\$_GET[".$this->field[0]."]}'&nbsp;\"; ";	
		return $word1.$word2.$word3;	
	}
	function select5(){
		$word2="\$SQL=\"select ".join(",",$this->field)."&nbsp; from&nbsp;".$this->TB."   where  ".$this->field[0]."='{\$_POST[".$this->field[0]."]}'&nbsp;\"; ";
		return $word1.$word2.$word3;	
	}
	function select6(){
		$word3="\$SQL=\"select {$this->TB}.".join(", {$this->TB}.",$this->field)."&nbsp; from&nbsp;".$this->TB."   &nbsp;\"; ";
		return $word1.$word2.$word3;	
	}


	function Serch(){
		$word="\$SQL=\"select ".join(",",$this->field)."  from ".$this->TB." \$ADD_SQL \";" ;
		return $word;
	}	
	function serch1($key,$value){
		$word="\$SQL=\"select ".join(",",$this->field)."  from ".$this->TB."   where  ".$this->field[$key]." like '$value%'  ";
		return $word;
	}	
	function tol_field(){
			return count($this->field);
	}	

	function tol_field1(){
			return count($this->field)+1;
	}	

//=================//









	function add_to_td($data,$num) {
		$all=count($data);
		$loop=ceil($all/$num);
		$all_td=($loop*$num)-1;//最大值小1
		for ($i=0;$i<($loop*$num);$i++){
		(($i%$num)==($num-1) && $i!=0 && $i!=$all_td) ? $data[$i][next_line]='yes':$data[$i][next_line]='';
		}
		return $data;
	}

}

?>