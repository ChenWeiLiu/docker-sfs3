<?php
//$Id: index.php 9096 2017-06-20 07:47:05Z chiming $
include "config.php";

//�{��
sfs_check();

//�q�X�����������Y
head("���ƿ�����");
//���SFS�s�����(���ϥνЮ��}����)
echo make_menu($school_menu_p);
//�إߪ���
$obj= new score_ss($CONN,$smarty);
//��l��
$obj->init();
//�B�z�{��,���ɵ{�Ǥ���header���O,�G���{�ǩy��head("score_ss�Ҳ�");���e
$obj->process();

$tpl=dirname(__file__)."/templates/chc_index.htm";

$smarty->assign("obj",$obj);
$smarty->display($tpl);
//�D�n���e


//�G������
foot();



//����class
class score_ss{
	var $CONN;//adodb����
	var $smarty;//smarty����
	var $size=10;//�C������
	var $page;//�ثe����
	var $tol;//����`����
   var $IS_JHORES;
	var $year;
   var $seme;
   var $YS='year_seme';//�U�Ԧ����Ǵ����ڼƦW��
   var $year_seme;//�U�Ԧ����Z�Ū��ڼƭ�
   var $Sclass='class_id';//�U�Ԧ����Z�Ū��ڼƦW��
   var $grade_name='Grade';//�U�Ԧ����~�Ū��ڼƦW��
   var $Grade;

	//�غc�禡
	function score_ss($CONN,$smarty){
		$this->CONN=&$CONN;
		$this->smarty=&$smarty;
      global $IS_JHORES;
      
      //6�~��
      $this->Grade=6;
      //103_2
      $this->year_seme=curr_year()."_".curr_seme();
      //�Ǧ~��
      $this->year=curr_year();
      //�Ǵ� 
      $this->seme=curr_seme();
	}
	//��l��
	function init() {}
	//�{��
	function process() {
		$this->all();
	}

	//�^�����
	function all(){
		$SQL="select year,semester,count(*) as tol from score_ss 
		group by year,semester ";
		$rs=$this->CONN->Execute($SQL) or die($SQL);
		$this->aTol=$rs->GetArray();

		if ($this->year=='') return ;
		if ($this->seme=='') return ;
		if ($this->Grade=='') return ;
		//�Ҧ��ҵ{�]�w
		$SQL="select * from score_ss where year='{$this->year}' 	and  semester='{$this->seme}' and class_year='{$this->Grade}' and enable=1 order by ss_id";
		$rs=$this->CONN->Execute($SQL) or die($SQL);
		$arr=$rs->GetArray();
		$this->all=$arr;//return $arr;
		// echo "<pre>";print_r($this->all);
		$this->tol=count($arr);
		/*���ҵ{����W��*/
  		$SQL="select subject_id,subject_name from score_subject ";
		$rs=$this->CONN->Execute($SQL) or die("�L�k�d�ߡA�y�k:".$SQL);
		if ($rs->RecordCount()==0) return "�|���]�w�����ظ�ơI";
		$obj=$rs->GetArray();
		foreach($obj as $ary){
			$id=$ary['subject_id'];
			$this->Subj[$id]=$ary['subject_name'];
		}
		//echo "<pre>";print_r($this->Subj);
		$this->ScoTol();
		$this->SsidToName=$this->SsidToName();
		//echo "<pre>";print_r($this->SsidToName);
	}
	function SsidToName(){
		foreach ($this->all as $ary){
			$id=$ary[ss_id];
			$scope=$ary[scope_id];
			$subject=$ary[subject_id];
			$AA[$id]=$id.'.'.$this->Subj[$scope].':'.$this->Subj[$subject].'(��'.($this->ScoTol2[$id]+0).'�����Z)';
		}	
	
	return $AA;
	
	}



	//���Ҧ����Z�έp by ss_id
	function ScoTol(){
		
		$TB='score_semester_'.$this->year.'_'.$this->seme;
  		$SQL="SELECT class_id ,ss_id,count(*)  as  stol  FROM  {$TB}  group  by class_id,ss_id ";
		$rs=$this->CONN->Execute($SQL) or die("�L�k�d�ߡA�y�k:".$SQL);
		if ($rs->RecordCount()==0) return "�|���]�w�����ظ�ơI";
		$obj=$rs->GetArray();
		foreach ($obj as $ary){
			$cla=$ary[class_id];
			$ssid=$ary[ss_id];		
			$this->ScoTol[$cla][$ssid]=$ary[stol];
			$this->ScoTol2[$ssid]=$this->ScoTol2[$ssid]+$ary[stol];
		}
	
	
	}

} 
