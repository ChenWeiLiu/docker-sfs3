<?php
//$Id: index.php 5310 2009-01-10 07:57:56Z hami $
include "config.php";

//�{��
sfs_check();

//�{���ϥΪ�Smarty�˥���
$tpl=dirname(__file__)."/templates/score_reset.htm";

//�إߪ���
$obj= new score_reset($CONN,$smarty);

//��l��
$obj->init();

//�B�z�{��,���ɵ{�Ǥ���header���O,�G���{�ǩy��head���e
$obj->process();

//�q�X�����������Y
head(" �Ǵ����Z�L����i�쵲��");

//���SFS�s�����
echo make_menu($school_menu_p);

//�D�n���e
$obj->display($tpl);

//�G������
foot();

//����class
class score_reset{
	var $CONN;//adodb����
	var $smarty;//smarty����
	
	//�غc�禡
	function score_reset($CONN,$smarty){
		$this->CONN=&$CONN;
		$this->smarty=&$smarty;
	}
	
	//��l��
	function init() {
		$this->seme_select=$this->year_seme_menu($sel_year,$sel_seme,"year_seme");
		$this->act=$_REQUEST['act'];	
	}

	//�{��
	function process() {
		$this->resetdata();
	}
	
	//���
	function display($tpl){
		$this->smarty->assign("this",$this);
		$this->smarty->display($tpl);
	}

	// �g�J��ƪ�
	function resetdata() {
	    if ($this->act=="send") {
			$select_year_seme=explode("-",$this->year_seme=$_REQUEST[year_seme]);
			$sel_year=sprintf("%03d", $select_year_seme[0]);
			$sel_seme=$select_year_seme[1];
			$year_seme=$sel_year.$sel_seme;
			//~ print_r($year_seme);
			$SQL="UPDATE stud_seme_score SET ss_score=CEIL(ss_score) WHERE seme_year_seme='{$year_seme}'";
			$this->CONN->Execute($SQL) or die("�L�k�d�ߡA�y�k:".$SQL);
			echo "<div align='center'><p>{$sel_year}�Ǧ~�ײ�{$sel_seme}�Ǵ��Ǵ����Z�w���⧹���I</p><br /><input type='button' onclick='history.back()' value='�^��W�@��'></input></div>";
			die();
		}
	}
	
	//�U�Ԧ����
	function year_seme_menu($sel_year,$sel_seme,$name="year_seme"){
		global $CONN;
		if (!$CONN) user_error("��Ʈw�s�u���s�b�I���ˬd�����]�w�I",256);
		$sql="SELECT year,semester FROM school_class WHERE enable='1' ORDER BY year DESC,semester DESC";
		$recordSet=$CONN->Execute($sql) or user_error($sql, 256);
		$other_year=array();
		$option="";
		while(list($year,$semester)=$recordSet->FetchRow()){
			$ys=sprintf("%03d", $year)."�Ǧ~��"."��".$semester."�Ǵ�";
			if(!in_array($ys,$other_year)){
				$other_year[$i]=$ys;
				$selected=($year==$sel_year and $semester==$sel_seme)?"selected":"";
				$option.="<option value='".$year."-".$semester."' $selected>$ys</option>";
				$i++;
			}
		}
		if(empty($option))trigger_error("�d�L����Ǵ����", 256);
		$main="<select name='$name'>
		$option
		</select>";
		return $main;
	}

}
