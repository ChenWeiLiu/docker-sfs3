<?php
//$Id: PHP_tmp.html 5310 2009-01-10 07:57:56Z hami $
include "config.php";
//�{��
sfs_check();


//�ޤJ��������(�ǰȨt�ΥΪk)
include_once "../../include/chi_page2.php";
//�{���ϥΪ�Smarty�˥���
$template_file = dirname (__file__)."/templates/statis_chi.htm";

$isAdmin = (int)checkid($_SERVER['SCRIPT_FILENAME'],1);


//�إߪ���
$obj= new teacher_absent($CONN,$smarty);
//��l��
$obj->init();
//¾�ٰ}�C
$obj->post_kind=post_kind();
//�޲z��
$obj->isAdmin=$isAdmin;

//�B�z�{��,���ɵ{�Ǥ���header���O,�G���{�ǩy��head("teacher_absent�Ҳ�");���e
$obj->process();


head("�~�׮t���έp");				//�q�X�����������Y
echo make_menu($school_menu_p);	//���SFS�s�����(���ϥνЮ��}����)
$obj->display($template_file);	//��ܤ��e
foot();							//�G������

//����class
class teacher_absent{
	var $CONN;//adodb����
	var $smarty;//smarty����
	var $size=10;//�C������
	var $page;//�ثe����
	var $tol;//����`����
	var $post_kind;//¾�ٰ}�C
	var $Y;//�~��
/*
	var $ABS=array(
	'11'=>'�ư�','12'=>'�a�x���U��','21'=>'�f��','22'=>'�Ͳz��','31'=>'���t',
	'41'=>'�B��','42'=>'���e��','43'=>'�Y��','44'=>'�y����','45'=>'������',
	'46'=>'�ల','47'=>'����','52'=>'���t��','53'=>'���X','54'=>'���{��',
	'55'=>'���Ұ�','56'=>'���˰�','61'=>'��L','23'=>'�����f��','81'=>'��',
	'82'=>'�[�Z�ɥ�','84'=>'�Ȥ�ɥ�','91'=>'���讽��','92'=>'���x����','93'=>'�a����');
*/
	//�غc�禡
	function teacher_absent($CONN,$smarty){
		$this->CONN=&$CONN;
		$this->smarty=&$smarty;
		$this->ABS=tea_abs_kind();
	}
	//��l��
	function init() {
		$year=(int)$_GET['Y'];
		if ($year==0 || $year=='') $year=date("Y");
		$this->Y=$year;
		$this->page=($_GET[page]=='') ? 0:$_GET[page];}
	//�{��
	function process() {

		$this->all();
	}
	//���
	function display($tpl){
		$this->smarty->assign("this",$this);
		$this->smarty->display($tpl);
	}
	//�^�����
	function all(){

		//�w�]�u�C�X���H
		$ADD=" a.teacher_sn='{$_SESSION['session_tea_sn']}'  AND ";
		if ($this->isAdmin==1) $ADD='';
		
		$SQL="select a.teacher_sn,a.name,d.title_name,c.post_kind from teacher_base a,teacher_post c, teacher_title d WHERE $ADD 
		a.teach_condition=0  AND c.teacher_sn=a.teacher_sn AND c.teach_title_id=d.teach_title_id  order by  d.rank";
		//echo $SQL;
		$rs=$this->CONN->Execute($SQL) or die($SQL);
		$arr=$rs->GetArray();
		$this->all=$arr;//return $arr;


		$SQL="SELECT * FROM teacher_absent WHERE check4_sn>0 and left(start_date,4)='{$this->Y}' ";
		$rs=$this->CONN->Execute($SQL) or die($SQL);
		$arr=$rs->GetArray();
		foreach ($arr as $A){
			$SN=$A['teacher_sn'];
			$abs[$SN][]=$A;
			}
		$this->Tea_abs=$abs;//return $arr;
	}
	//�s�W
	function getABS($SN){
		$tmp=array();
		foreach ($this->Tea_abs[$SN] as $ary){
			$K=$ary['abs_kind'];
			$tmp[$K]['day']=$tmp[$K]['day']+$ary['day'];
			$tmp[$K]['hour']=$tmp[$K]['hour']+$ary['hour'];
			if ($tmp[$K]['hour']>=8){
				$tmp[$K]['day']++;
				$tmp[$K]['hour']=$tmp[$K]['hour']-8;}
		}
		return $tmp;

	}


}
