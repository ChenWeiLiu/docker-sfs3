<?php
//$Id: stud_sta_new.php 6815 2012-06-22 08:27:11Z smallduh $
include "config.php";
//認證
sfs_check();

//引入換頁物件(學務系統用法)
include_once "../../include/chi_page2.php";
include_once "../../include/sfs_oo_dropmenu.php";

//程式使用的Smarty樣本檔
$template_file = dirname (__file__)."/templates/stud_sta.htm";

//建立物件
$obj= new stud_sta($CONN,$smarty);
$obj->sfs_url=$SFS_PATH_HTML;
//初始化
$obj->init();
//處理程序,有時程序內有header指令,故本程序宜於head("stud_sta模組");之前
$obj->process();

//秀出網頁布景標頭
head("stud_sta模組");

//顯示SFS連結選單(欲使用請拿開註解)
echo make_menu($school_menu_p);

//顯示內容
$obj->display($template_file);
//佈景結尾
foot();


//物件class
class stud_sta{
	var $CONN;//adodb物件
	var $smarty;//smarty物件
	var $size=20;//每頁筆數
	var $page;//目前頁數
	var $tol;//資料總筆數
	var $select;
	var $sfs_url;

	//建構函式
	function stud_sta($CONN,$smarty){
		$this->CONN=&$CONN;
		$this->smarty=&$smarty;
	}
	//初始化
	function init() {
		$ob=new drop($this->CONN);
		$this->select=&$ob;
		$this->year_seme=&$this->select->year_seme;
		}
	//程序
	function process() {
		if($_POST[form_act]=='add') $this->add();
		if($_POST[form_act]=='remove') $this->update();
		if($_GET[form_act]=='del') $this->del();
		if($_POST[form_act]=='add_DB') $this->add_DB();
		$this->all();$this->SEL();
	}
	//顯示
	function display($tpl){
		$this->smarty->assign("this",$this);
		$this->smarty->display($tpl);
	}
	//擷取資料
	function all(){
		if ($_GET[class_id]=='') return;
		$SEME=split("_",$_GET[year_seme]);
		$SEME4=sprintf("%03d",$SEME[0]).$SEME[1];
		$Class=split("_",$_GET[class_id]);//095_1_01_03
		$Class3=($Class[2]+0).sprintf("%02d",$Class[3]);
		$SQL="select  a.stud_id, a.student_sn,b.seme_num,a.stud_name, a.stud_sex from stud_base a  , stud_seme b where b.seme_year_seme ='{$SEME4}' and b.seme_class ='{$Class3}' and a.student_sn=b.student_sn order by b.seme_class, b.seme_num  ";
		$rs=&$this->CONN->Execute($SQL) or die($SQL);
		$this->stu=$this->Full_TD($rs->GetArray(),5);
	}
	//新增
	function add(){
		if ($_POST[stu]=='') return;
		foreach ($_POST[stu] as $sn =>$value){$stu[]=$sn;}
		if ($_SESSION[sel_stu]=='') {
			//session_register("sel_stu");
			$_SESSION[sel_stu]=$stu;
		}	else {
			$_SESSION[sel_stu]=array_unique(array_merge($_SESSION[sel_stu],$stu));
		}
	}
	//更新
	function update(){
		if ($_POST[stu]=='') return;
		foreach ($_POST[stu] as $sn =>$val_1){
			foreach ($_SESSION[sel_stu] as $key =>$val_2){
				if ($sn==$val_2) unset($_SESSION[sel_stu][$key]);
				}
		}
	}
	//新增
	function add_DB(){
		if ($_POST[year_seme]=='') return;
		if ($_POST[purpose]=='') return;
		if ($_POST[prove_date]=='') return;
		$a=join(",",$_SESSION[sel_stu]);
		if ($a=='')  {unset($_SESSION[sel_stu]);return;}
		$SEME=split("_",$_POST[year_seme]);
		$seme=$SEME[0].$SEME[1];
		$IP=$_SERVER['REMOTE_ADDR'];
		$USER=$_SESSION[session_log_id];
		$SQL="select  stud_id,student_sn from stud_base where student_sn in ($a) ";
		$rs=&$this->CONN->Execute($SQL) or die($SQL);
		$aa=&$rs->GetArray();
		foreach ($aa as $ary){
			$SQL="INSERT INTO stud_sta(stud_id,student_sn,prove_year_seme,purpose,prove_date,set_id,set_ip,prove_cancel)  values ('{$ary['stud_id']}','{$ary['student_sn']}' ,'{$seme}' ,'{$_POST['purpose']}' ,'{$_POST['prove_date']}' ,'{$USER}' ,'{$IP}' ,'0' )";
			$rs=&$this->CONN->Execute($SQL) or die($SQL);
		}
		unset($_SESSION[sel_stu]);
//		$URL=$_SERVER[PHP_SELF]."sta_view.php?page=0".$_POST[page];
		$URL="sta_view.php?page=0";
		Header("Location:$URL");
	}
	//刪除
	function del(){
	}
	function Full_TD($data,$num) {
		$all=count($data);
		$loop=ceil($all/$num);
		$all_td=($loop*$num)-1;//最大值小1
		for ($i=0;$i<($loop*$num);$i++){
		(($i%$num)==($num-1) && $i!=0 && $i!=$all_td) ? $data[$i][next_line]='yes':$data[$i][next_line]='';
		}
		return $data;
		}
	function SEX($a){
		if($a=='1'){return "<img src='images/boy.gif'>";}
		else {return "<img src='images/girl.gif'>";}
	}
	function CLA($a){
		$year=substr($a,0,1);
		$class=substr($a,1,2);
		if ($year>6)$year=$year-6;
		return $year."年".$class."班";
	}

	function SEL(){
		if ($_SESSION[sel_stu]=='') {unset($_SESSION[sel_stu]);return;}
		$a=join(",",$_SESSION[sel_stu]);
		if ($a=='')  {unset($_SESSION[sel_stu]);return;}
		$SEME=split("_",$this->year_seme);
		$SEME4=sprintf("%03d",$SEME[0]).$SEME[1];
		$SQL="select  a.stud_id, a.student_sn,b.seme_class,b.seme_num,a.stud_name, a.stud_sex from stud_base a  , stud_seme b 
		where a.student_sn in ($a) and b.seme_year_seme ='{$SEME4}' and  a.student_sn =b.student_sn order by  b.seme_class,b.seme_num  ";
		$rs=&$this->CONN->Execute($SQL) or die($SQL);
		$this->s_stu=$this->Full_TD($rs->GetArray(),5);
		}
	
}
?>
