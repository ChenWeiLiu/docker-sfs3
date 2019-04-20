<?php

include "config.php";
//�{��
sfs_check();

//���w�˥�
$template_file = dirname (__file__)."/templates/fix_edukey.htm";

//�إߪ���
$obj= new fix_teacherID($CONN,$smarty);


//��l��
$obj->init();

//�B�z�{��,���ɵ{�Ǥ���header���O,�G���{�ǩy��head("chc_basic12�Ҳ�");���e
$obj->process();

//�q�X�����������Y
head("edukey����");

//���SFS�s�����(���ϥνЮ��}����)
echo make_menu($school_menu_p);
//~ echo "<pre>";
//~ print_r($obj);
//��ܤ��e
$obj->display($template_file);

//�G������
foot();


//����class
class fix_teacherID{
	var $CONN;//adodb����
	var $smarty;//smarty����
	var $sfsURL;
	//�غc�禡
	function fix_teacherID($CONN,$smarty){
		$this->CONN=$CONN;
		$this->smarty=$smarty;
	}
	//��l��
	function init() {}
	//�{��
	function process() {
		if ($_GET['act']=='Make') $this->add();
		if ($_GET['act']=='Del') $this->del();
		$this->check1();
	}
	//���
	function display($tpl){
		
		$this->smarty->assign("this",$this);
		$this->smarty->display($tpl);
	}
	//�^�����
	function check1(){
		//1.���Юv���
		$SQL="SELECT teach_person_id as perID,name as cname,teacher_sn as SN,sex ,edu_key ,ldap_password  FROM teacher_base  where teach_condition ='0' ";
		$rs=$this->CONN->Execute($SQL) or die($SQL);
		$arr=$rs->GetArray();
		$this->all=$arr;
	//echo '<pre>';print_r($New);
	}

	function add(){
		$sn=(int)$_GET['sn'];
		if ($sn==0) return ;
		//1.���Юv���
		$SQL="SELECT teach_person_id,name as cname,teacher_sn 
		 FROM teacher_base  where  teacher_sn='$sn' ";
		$rs=$this->CONN->Execute($SQL) or die($SQL);
		$arr=$rs->GetArray();
		$perID=$arr[0]['teach_person_id'];
		if ($perID=='') return ;
		$edukey=hash('sha256',$perID);
		$SQL="update  teacher_base set edu_key='{$edukey}'  where  teacher_sn='$sn' ";
		$rs=$this->CONN->Execute($SQL) or die($SQL);
		header("Location:".$_SERVER['SCRIPT_NAME']);
	//echo '<pre>';print_r($New);
	}

	function del(){
		$sn=(int)$_GET['sn'];
		if ($sn==0) return ;
		//1.���Юv���
		$SQL="update  teacher_base set edu_key=''  where  teacher_sn='$sn' ";
		$rs=$this->CONN->Execute($SQL) or die($SQL);
		header("Location:".$_SERVER['SCRIPT_NAME']);
	//echo '<pre>';print_r($New);
	}



}


