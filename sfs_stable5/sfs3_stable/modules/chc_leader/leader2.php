<?php
//$Id: chc_seme.php 5310 2009-01-10 07:57:56Z hami $
include "config.php";
//認證
sfs_check();

//引入換頁物件(學務系統用法)
include_once "../../include/sfs_oo_dropmenu.php";
//程式使用的Smarty樣本檔
$template_file1 = dirname (__file__)."/templates/leader2.htm";
$template_file2 = dirname (__file__)."/templates/leader2add.htm";
if ($_GET['act']=='add') {$template_file=$template_file2;}else {$template_file=$template_file1;} 
//建立物件
$obj= new chc_seme($CONN,$smarty);
//初始化
$obj->init();
//處理程序,有時程序內有header指令,故本程序宜於head("score_semester_91_2模組");之前
$obj->process();
//echo '<pre>';print_r($_POST);die();
//秀出網頁布景標頭
head("[彰]班級幹部管理");

//顯示SFS連結選單(欲使用請拿開註解)
echo make_menu($school_menu_p);
//$ob=new drop($this->CONN,$IS_JHORES);
//		$this->select=$ob->select();
//echo $ob->select();
//顯示內容
$obj->display($template_file);
//佈景結尾
foot();


//物件class
class chc_seme{
	var $CONN;//adodb物件
	var $smarty;//smarty物件
	var $stu;//學生資料
	var $class_id;//科目陣列
	var $StuTitle;
	var $kind=array('0'=>'未擔任','1'=>'社團幹部','2'=>'全校性幹部');
	//var $kind0=array('班長','副班長','康樂股長','學藝股長','事務股長','衛生股長','風紀股長','輔導股長','環保股長','資訊股長');


	//建構函式
	function chc_seme($CONN,$smarty){
		$this->CONN=&$CONN;
		$this->smarty=&$smarty;
	}
	//初始化
	function init() {
		$YS=''; 
		if (isset($_POST['year_seme'])) $YS=$_POST['year_seme'];
		if ($YS=='' && isset($_GET['year_seme'])) $YS=$_GET['year_seme'];
		if ($YS=='') $YS=curr_year()."_".curr_seme();
		$this->year_seme=$YS;
		$aa=split("_",$this->year_seme);
		$this->year=$aa[0];
		$this->seme=$aa[1];		
		
		}
	//程序
	function process() {
		if(isset($_POST['form_act']) && $_POST['form_act']=='add') $this->add();
		if(isset($_POST['form_act']) && $_POST['form_act']=='update') $this->update();
		if(isset($_GET['form_act']) && $_GET['form_act']=='del') $this->del();
		$this->all();
	}
	//顯示
	function display($tpl){
		$ob=new drop($this->CONN);
		$this->select=&$ob->select();
		$this->smarty->assign("this",$this);
		$this->smarty->display($tpl);
	}
	//刪除
	function del(){
		$id=(int)$_GET['id'];
		$SQL="Delete from  chc_leader  where  id='{$id}' ";
		$rs=$this->CONN->Execute($SQL) or die($SQL);
		$URL=$_SERVER['SCRIPT_NAME']."?year_seme=".$this->year_seme."&class_id=".$_GET['class_id'];
		Header("Location:$URL");
	}


	//更新
	function update(){
		//echo '<pre>'.$this->year_seme;print_r($_POST);die();
		$title=strip_tags(trim($_POST['title']));
		$org_name=strip_tags(trim($_POST['org_name']));
		$kind=(int) $_POST['kind'];
		$stuSN =(int) $_POST['student_sn'];
		$id =(int) $_POST['id'];
		if ($kind==0 or $kind=='') backe('!!輸入錯誤!!');
		if ($org_name=='' or $title=='') backe('!!輸入錯誤!!');

		$cr_time=date("Y-m-d H:i:s");
		$tea_sn=$_SESSION['session_tea_sn'];
		$memo=strip_tags(trim($_POST['memo']));
		$SQL="update  chc_leader set   kind ='{$kind}', org_name ='{$org_name}', title ='{$title}',memo='{$memo}'
		, update_sn ='{$tea_sn}', cr_time ='{$cr_time}' where id ='{$id}' and  student_sn ='{$stuSN}' ";
		$rs=$this->CONN->Execute($SQL) or die($SQL);
		
		$URL=$_SERVER['SCRIPT_NAME']."?year_seme=".$this->year_seme."&class_id=".$_POST['class_id'];
		Header("Location:$URL");
	}

	//新增資料
	function add(){
		//echo '<pre>';print_r($_POST);die();
		$tea_sn=$_SESSION['session_tea_sn'];
		$c_id=explode('_',$_POST['class_id']);//切開 101_2_06_01
		$seme=$c_id[0].$c_id[1];
		$cla=($c_id[2]+0).$c_id[3];
		//$kind='0';
		$cr_time=date("Y-m-d H:i:s");
		
		foreach ($_POST['kind'] as $SN=>$kind){
			$title=strip_tags(trim($_POST['title'][$SN]));
			$org_name=strip_tags(trim($_POST['org_name'][$SN]));
			if ($kind==0 or $kind=='' or $title=='' or $org_name=='') continue;
			$SQL="INSERT INTO chc_leader(student_sn,seme,kind,org_name,title,update_sn,cr_time)  
		values ('{$SN}' ,'{$seme}' ,'{$kind}' ,'{$org_name}' ,'{$title}' ,'{$tea_sn}' ,'{$cr_time}' )";
			$rs=$this->CONN->Execute($SQL) or backe('相同社團不可以擔任兩種幹部!!');//;die($SQL);
		}
		
		//$Insert_ID= $this->CONN->Insert_ID();
		$URL=$_SERVER['SCRIPT_NAME']."?year_seme=".$this->year_seme."&class_id=".$_POST['class_id'];
		Header("Location:$URL");
	}
	//擷取資料
	function all(){
		if ($_GET['class_id']=='') return;
		$this->class_id=$_GET['class_id'];
		$this->stu=$this->get_stu();
	}



/* 取學生陣列,取自stud_base表與stud_seme表*/
	function get_stu(){
		$CID=split("_",$this->class_id);//093_1_01_01
		$year=$CID[0];
		$seme=$CID[1];
		$grade=$CID[2];//年級
		$class=$CID[3];//班級
		$CID_1=$year.$seme;
		$CID_2=sprintf("%03d",$grade.$class);
		$SQL="select 	a.stud_id,a.student_sn,a.stud_name,a.stud_sex,b.seme_year_seme,b.seme_class,b.seme_num,a.stud_study_cond  from stud_base a,stud_seme b where a.student_sn=b.student_sn and b.seme_year_seme='$CID_1' and b.seme_class='$CID_2' $add_sql order by b.seme_num ";
		$rs=&$this->CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
		$obj_stu=array();
		while ($rs and $ro=$rs->FetchNextObject(false)) {
			$obj_stu[$ro->student_sn] = get_object_vars($ro);
		}

		

		//$SQL="select id,student_sn,seme,kind,org_name,title from chc_leader where kind!='0'  and seme='$CID_1' and org_name !='$CID_2'  ";
		$SQL="select a.stud_name,a.stud_sex,b.seme_year_seme,b.seme_class,b.seme_num,c.*  
		from stud_base a,stud_seme b, chc_leader c 
		where a.student_sn=b.student_sn and  a.student_sn=c.student_sn  and b.seme_year_seme='{$CID_1}' 
		and b.seme_class='{$CID_2}'  and c.seme='{$CID_1}' and c.kind!='0' and org_name !='$CID_2'  
		order by b.seme_num,c.org_name, c.title ";
		// echo $SQL;
		$rs=&$this->CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
		$All=$rs->GetArray();
		if ($rs->RecordCount()==0) {$this->StuB='';}
		else {$this->StuB=$All;}
		
		//print_r($this->StuB);
		return $obj_stu;	
	}




	function get_Title($sn){
	return $this->StuTitle[$sn]['title'];
}




function getLeaderKind($K){
	return getLeaderKind($K);
}




}


