<?php
//$Id: chc_seme.php 5310 2009-01-10 07:57:56Z hami $

//�w�]���ޤJ�ɡA���i�����C
require_once "./module-cfg.php";
include_once "../../include/config.php";

sfs_check();

//���y�N�X���
include_once "../../include/sfs_case_dataarray.php";
//�ޤJ�U�Ԧ��Z�ſ�檫��(�ǰȨt�ΥΪk)
include_once "../../include/sfs_oo_dropmenu.php";

//�{���ϥΪ�Smarty�˥���
$template_file = dirname (__file__)."/chc_pass.htm";
//�إߪ���
$obj= new chc_seme($CONN,$smarty);
//��l��
$obj->init();
//�B�z�{��,���ɵ{�Ǥ���header���O,�G���{�ǩy��head("score_semester_91_2�Ҳ�");���e
$obj->process();

//�q�X�����������Y
head("�ǥͱK�X�s���PeduKey");

//���SFS�s�����(���ϥνЮ��}����)
echo make_menu($school_menu_p);
//$ob=new drop($this->CONN,$IS_JHORES);
//		$this->select=$ob->select();
//echo $ob->select();
//��ܤ��e
$obj->display($template_file);
//�G������
foot();


//����class
class chc_seme{
	var $CONN;//adodb����
	var $smarty;//smarty����
	var $stu;//�ǥ͸��
	var $subj;//��ذ}�C
	var $rule;//����

	//�غc�禡
	function chc_seme($CONN,$smarty){
		$this->CONN=&$CONN;
		$this->smarty=&$smarty;
	}
	//��l��
	function init() {}
	//�{��
	function process() {
		//�Ǧ~��,�Ǵ�
		$this->year_seme=strip_tags($_GET['year_seme']);
		//�Z��
		$this->class_id=strip_tags($_GET['class_id']);
		if ($_GET['act']=='Make') $this->add();
		if ($_GET['act']=='Del') $this->del();
		$this->cond=study_cond();//���y��ƥN�X
		$this->all();
	}
	//���
	function display($tpl){
		$ob=new drop($this->CONN);
		$this->select=&$ob->select();
		$this->smarty->assign("this",$this);
		$this->smarty->display($tpl);
	}
	//�^�����
	function all(){
		if ($this->class_id=='') return;
		$this->stu=$this->get_stu();

	}
/* ���ǥͰ}�C,����stud_base��Pstud_seme��*/
	function get_stu(){
		$CID=split("_",$this->class_id);//093_1_01_01
		$year=$CID[0];
		$seme=$CID[1];
		$grade=$CID[2];//�~��
		$class=$CID[3];//�Z��
		$CID_1=$year.$seme;
		$CID_2=sprintf("%03d",$grade.$class);
		$SQL="select 	a.stud_id,a.student_sn, a.stud_name, a.stud_sex, a.stud_person_id , b.seme_year_seme,b.seme_class,b.seme_num,a.stud_study_cond, a.edu_key,a.email_pass   from stud_base a,stud_seme b where a.student_sn=b.student_sn and b.seme_year_seme='$CID_1' and b.seme_class='$CID_2' $add_sql order by b.seme_num ";
		$rs=&$this->CONN->Execute($SQL) or die("�L�k�d�ߡA�y�k:".$SQL);
		$obj_stu=array();
		while ($rs and $ro=$rs->FetchNextObject(false)) {
			$obj_stu[$ro->student_sn] = get_object_vars($ro);
		}
		return $obj_stu;	
	}


	function add(){
		$sn=(int)$_GET['sn'];
		if ($sn==0) return ;
		//1.���Юv���
		$SQL="SELECT * FROM stud_base  where student_sn='$sn' ";
		$rs=$this->CONN->Execute($SQL) or die($SQL);
		$arr=$rs->GetArray();
		$perID=$arr[0]['stud_person_id'];
		if ($perID=='') return ;
		$edukey=hash('sha256',$perID);
		$SQL="update  stud_base set edu_key='{$edukey}'  where   student_sn='$sn' ";
		$rs=$this->CONN->Execute($SQL) or die($SQL);
		$URL=$_SERVER['SCRIPT_NAME'].'?year_seme='.$this->year_seme.'&class_id='.$this->class_id;
		header("Location:".$URL);
	//echo '<pre>';print_r($New);year_seme=105_1&class_id=105_1_02_05
	}

	function del(){
		$sn=(int)$_GET['sn'];
		if ($sn==0) return ;
		//1.���Юv���
		$SQL="update  stud_base set edu_key=''  where  student_sn='$sn' ";
		$rs=$this->CONN->Execute($SQL) or die($SQL);
		$URL=$_SERVER['SCRIPT_NAME'].'?year_seme='.$this->year_seme.'&class_id='.$this->class_id;
		header("Location:".$URL);
	//echo '<pre>';print_r($New);
	}




}

