<?php
//$Id: chc_view.php 7979 2014-04-15 14:19:13Z chiming $

require "config.php";
sfs_check();

//$stu=stu();
//echo "<pre>";print_r($stu);
//echo "hi";die();

//�ޤJ���

$template_file = dirname (__file__)."/chc_view.htm";

$obj= new chc_view($CONN,$smarty);
$obj->IS_JHORES=&$IS_JHORES;
$obj->process();

//1.�q�X�����������Y
head("�¯Z�s�Z����˵�");

//���SFS�s�����(���ϥνЮ��}����)
//echo make_menu($school_menu_p);

//myheader();
//2.��ܤ��e
$obj->display($template_file);

//3.�G������
foot();



class chc_view{ //�إ����O
  var $CONN;    //adodb����
  var $Smarty;  //smarty����
//  var $seme;    //�Ǵ�    
  var $rs;      //�Ҧ��ǥ�
  var $stu;      //���Ǵ��Ҧ��ǥͰ}�C
	//�غc�禡
	function chc_view($CONN,$smarty){
		global $IS_JHORES;
		$this->CONN=&$CONN;
		$this->smarty=&$smarty;
		$this->IS_JHORES=$IS_JHORES;

	}
  //��l��
  function init() {
		if ($this->IS_JHORES==6){
			$this->grade=array(7=>" �@�~��",8=>" �G�~��",9=>" �T�~��");
			$this->sgrade=array(7=>" �@�~",8=>" �G�~",9=>" �T�~");
			}
		else{
			$this->grade=array(1=>" �@�~��",2=>" �G�~��",3=>" �T�~��",4=>" �|�~��",5=>"���~��",6=>"���~��");
			$this->sgrade=array(1=>" �@�~",2=>" �G�~",3=>" �T�~",4=>" �|�~",5=>"���~",6=>"���~");
		}
	}

  //�ҥε{��
  function process(){
		$this->init();
		if (isset($_GET['year']) && $_GET['year']!='')  $this->all();
  }

	//���
	function display($tpl){
		//$ob=new drop($this->CONN);
		//$this->select=$this->select();
		$this->smarty->assign("this",$this);
		$this->smarty->display($tpl);
	}
	//�^�����
	function all(){
		$Y=(int)$_GET['year'];
		if ($Y=='' ||$Y=='0' ) return;
		$SQL="select a.*,b.stud_name,b.stud_id,b.stud_sex, b.stud_study_cond   
		from stud_compile a,stud_base b  where 
		a.student_sn=b.student_sn and a.old_class like '{$Y}%' order by a.old_class ";
		
		$rs=$this->CONN->Execute($SQL) or die("�L�k�d�ߡA�y�k:".$SQL);
		if ($rs->RecordCount()==0) return;
		//if ($rs->RecordCount()==0) backe("01.�t�μȵL�z����ơI");
		$all=$rs->GetArray();
		foreach ($all as $ary){
			$cla=substr($ary['old_class'],0,3);
			$No=substr($ary['old_class'],3,2);
			$A[$cla][$No]=$ary;	
			}
			ksort($A);//�̯Z�ű�
			$this->Old=$A;
		unset($A);
		
		foreach ($all as $ary){
			$cla=$ary['new_class'];
			$No=$ary['site_num'];
			$A[$cla][$No]=$ary;			
			}
			ksort($A);//�̯Z�ű�
		$this->New=$A;



	}



}
//  end class
