<?php
//$Id: chc_seme.php 5310 2009-01-10 07:57:56Z hami $
ini_set('display_errors', '0');
include "config.php";
include "chc_func_class.php";
//�{��
sfs_check();

if ($_SESSION['session_who']=='�ǥ�') Header("Location:stu.php");

//�{���ϥΪ�Smarty�˥���
$template_file = dirname (__file__)."/templates/stud_per3.htm";

//�إߪ���
$obj= new chc_seme($CONN,$smarty);

//��l��
$obj->init();

//�B�z�{��,���ɵ{�Ǥ���header���O,�G���{�ǩy��head("score_semester_91_2�Ҳ�");���e
$obj->process();

//�q�X�����������Y
//head("���ưϧK�դJ��");

//���SFS�s�����(���ϥνЮ��}����)
//echo make_menu($school_menu_p);
//$ob=new drop($this->CONN,$IS_JHORES);
//		$this->select=$ob->select();
//echo $ob->select();
//��ܤ��e
$obj->display($template_file);
//�G������
//foot();


//����class
class chc_seme{
	var $CONN;//adodb����
	var $smarty;//smarty����
	var $stu;//�ǥ͸��
	var $IS_JHORES;//�ꤤ�p
	var $year;//�Ǧ~
	var $seme;//�Ǵ�
	var $YS='year_seme';//�U�Ԧ����Ǵ����ڼƦW��
	var $year_seme;//�U�Ԧ����Z�Ū��ڼƭ�
	var $Sclass='class_id';//�U�Ԧ����Z�Ū��ڼƦW��
	var $reward_kind =array("1"=>"�ż��@��","2"=>"�ż��G��","3"=>"�p�\�@��","4"=>"�p�\�G��","5"=>"�j�\�@��","6"=>"�j�\�G��","7"=>"�j�\�T��","-1"=>"ĵ�i�@��","-2"=>"ĵ�i�G��","-3"=>"�p�L�@��","-4"=>"�p�L�G��","-5"=>"�j�L�@��","-6"=>"�j�L�G��","-7"=>"�j�L�T��");
	var $race_area=array('1'=>'���','2'=>'���� �O�W��','3'=>'�ϰ��(�󿤥�)','4'=>'�� ���ҥ�','5'=>'������(�m��)','6'=>'�դ�');
	var $race_kind=array('1'=>'�ӤH��','2'=>'������');
//�t�μ��g�]�w�Ѧ�
	//$reward_good_arr=array("1"=>"�ż��@��","2"=>"�ż��G��","3"=>"�p�\�@��","4"=>"�p�\�G��","5"=>"�j�\�@��","6"=>"�j�\�G��","7"=>"�j�\�T��");
	//$reward_bad_arr=array("-1"=>"ĵ�i�@��","-2"=>"ĵ�i�G��","-3"=>"�p�L�@��","-4"=>"�p�L�G��","-5"=>"�j�L�@��","-6"=>"�j�L�G��","-7"=>"�j�L�T��");
	//$reward_arr=array("1"=>"�ż��@��","2"=>"�ż��G��","3"=>"�p�\�@��","4"=>"�p�\�G��","5"=>"�j�\�@��","6"=>"�j�\�G��","7"=>"�j�\�T��","-1"=>"ĵ�i�@��","-2"=>"ĵ�i�G��","-3"=>"�p�L�@��","-4"=>"�p�L�G��","-5"=>"�j�L�@��","-6"=>"�j�L�G��","-7"=>"�j�L�T��");
	
	
	//�غc�禡
	function chc_seme($CONN,$smarty){
		global $IS_JHORES;
		$this->CONN=&$CONN;
		$this->smarty=&$smarty;
		$this->IS_JHORES=$IS_JHORES;

	}
	//��l��
	function init() {}

	//�{��
	function process() {
		$this->sch=get_school_base();
		$this->all();
		// $this->get_stu($this->Sn);
		//echo $this->year;
	}
	//�ǤJclass_id,�ǥX�ǥ�sn�}�C(�@�ӯZ)
	function get_stsn($class_id){
		$st_sn=array();
		$class_ids=split("_",$class_id);
		$seme=$class_ids[0].$class_ids[1];
		$the_class=($class_ids[2]+0).$class_ids[3];
		$SQL="select student_sn 
		      from stud_seme 
		      where  seme_year_seme ='$seme' 
		      and seme_class='$the_class' 
		      order by seme_num ";
		//echo $SQL;
		$rs = $this->CONN->Execute($SQL);
		if ($rs->RecordCount()===0) return '';
		$all=$rs->GetArray();
		foreach ($all as $ary){
			$st_sn[]=$ary['student_sn'];
			}
		return $st_sn;
	}

	//�^�����,�u�n���;ǥ�SN��� �� $this->allStu �Ѽ˪��ϥΧY�i
	function all(){
		//$class_id='102_1_06_01';
		if (isset($_GET['Sn'])) :
			$SN=(int)$_GET['Sn'];
			$this->allStu=array($SN);
		endif;
		if (isset($_POST['class_id']) && $_POST['form_act']=='OK') :
			$class_id=strip_tags($_POST['class_id']);
			$this->allStu=$this->get_stsn($class_id);
		endif;
	}


	//���
	function display($tpl){
		//$ob=new drop($this->CONN);
		//$this->select=$this->select();
		$this->smarty->assign("this",$this);
		$this->smarty->display($tpl);
	}

/* ���ǥͰ}�C,����stud_base���Pstud_seme��*/
	function get_stu($Sn){
		$SQL="select a.stud_id,a.stud_name,a.stud_birthday ,a.stud_sex, a.stud_person_id ,a.curr_class_num,
		b.seme_class,b.seme_num,a.stud_study_cond ,c.*  
		from stud_base a, stud_seme b, chc_basic12 c  
		where c.student_sn='{$Sn}' 
		and c.student_sn=a.student_sn 
		and c.student_sn=b.student_sn 
		order by b.seme_year_seme desc limit 1 ";//and left(b.seme_year_seme,3)='{$this->Year}' 
		//-- 102.10.29 �[�J�i�H��1-2�~�Ŭd�ߪ��y�k
		$SQL="select a.stud_id,a.stud_name,a.stud_birthday ,a.stud_sex, a.stud_person_id ,a.curr_class_num,
		b.seme_class,b.seme_num,a.stud_study_cond , c.*  
		from stud_base a, stud_seme b 
		LEFT JOIN chc_basic12 c 
		ON ( b.student_sn=c.student_sn  )  
		where a.student_sn='{$Sn}' 
		and a.student_sn=b.student_sn  
		order by b.seme_year_seme desc limit 1 ";
		//-- 102.10.29
		$rs=&$this->CONN->Execute($SQL) or die("�L�k�d�ߡA�y�k:".$SQL);
		if ($rs->RecordCount()==0) return "�䤣��ǥ͡I";
		$All=$rs->GetArray();
		$Stu['base']=$All[0];
		$SQL="select a.student_sn,a.stud_id,a.stud_study_cond,b.seme_year_seme,b.seme_class,b.seme_num 
		      from stud_base a, stud_seme b, chc_basic12 c  
		      where c.student_sn='{$Sn}' 
		      and c.student_sn=a.student_sn 
		      and c.student_sn=b.student_sn 
		      order by b.seme_year_seme asc ";
		//-- 102.10.29 �קאּ���� chc_basic12 ��ƻy�k
		$SQL="select a.student_sn,a.stud_id,a.stud_study_cond,b.seme_year_seme,b.seme_class,b.seme_num 
		      from stud_base a, stud_seme b  
		      where  a.student_sn='{$Sn}' 
		      and b.student_sn=a.student_sn  
		      order by b.seme_year_seme asc ";
		$rs=&$this->CONN->Execute($SQL) or die("�L�k�d�ߡA�y�k:".$SQL);
		if ($rs->RecordCount()==0) return "�䤣��ǥ͡I";
		$Stu['Seme']=$rs->GetArray();
/*
        echo "<pre>";
		print_r($All);
		echo "</pre>";
*/	
		return $Stu;	
		//echo "<pre>";print_r($this->Stu_Seme);
	}

	//�A�Ȯɼ�
	function get_service( $student_sn ){
	$SQL = "select a.year_seme,a.item,a.memo,a.service_date,a.sponsor,b.*
	        from stud_service a ,stud_service_detail b  
	        where b.student_sn='{$student_sn}'  
	        and b.item_sn =a.sn 
	        order by a.service_date asc 	 ";
	$rs=&$this->CONN->Execute($SQL) or die("�L�k�d�ߡA�y�k:".$SQL);
	if ($rs->RecordCount()==0) return;
	return $rs->GetArray(); 
	}

	//���v�ɰO��
	function get_race($sn){
	$SQL = "SELECT  *  
	        FROM `career_race`  
	        where student_sn='{$sn}' 
	        order by  certificate_date  asc 	 ";
	$rs=&$this->CONN->Execute($SQL) or die("�L�k�d�ߡA�y�k:".$SQL);
	if ($rs->RecordCount()==0) return ;
	return $rs->GetArray(); 
	}


	//�����y
	function get_reward($sn){
	$SQL = "SELECT  *  
	        FROM `reward`  
	        where reward_kind >0 
	        and student_sn='{$sn}' 
	        order by  (abs(reward_year_seme)+10000),reward_date ";
	$rs=&$this->CONN->Execute($SQL) or die("�L�k�d�ߡA�y�k:".$SQL);
	if ($rs->RecordCount()==0) return ;
	return $rs->GetArray(); 
	}
	//���g�@
	function get_reward2($sn){
	// 101�Ǧ~�פ~�}�l�ĭp,�B���P�L��
	//$SQL = "SELECT  *  FROM `reward`  where reward_kind < 0 and student_sn='{$sn}' and reward_cancel_date='0000-00-00' and reward_year_seme >='1011' order by  (abs(reward_year_seme)+10000),reward_date ";
	// ����101�Ǧ~�׭���,�אּ�ۦ�P�_ by 103.03.20
	$SQL = "SELECT  *  
	        FROM `reward`  
	        where reward_kind < 0 
	        and student_sn='{$sn}' 
	        and reward_cancel_date='0000-00-00'  
	        order by  (abs(reward_year_seme)+10000),reward_date ";
	$rs=&$this->CONN->Execute($SQL) or die("�L�k�d�ߡA�y�k:".$SQL);
	if ($rs->RecordCount()==0) return ;
	return $rs->GetArray(); 
	}
		
	//�����m��
	function get_abs($Stu_Seme){
	//$Stu_Seme=$this->Stu_Seme[$Sn];
	$stud_id='';
	foreach ($Stu_Seme as $ary){
		//�Ǵ��Ҧ�
		// ����1011�Ǧ~�׭���,�אּ�ۦ�P�_ by 103.03.20
		// if ($ary['seme_year_seme'] < '1011') continue;
		$seme[]=$ary['seme_year_seme'];
		$stud_idA=$ary['stud_id'];
		// �ˬd�Ǹ�
		if ($stud_id=='')$stud_id=$stud_idA;
		if ($stud_id != $stud_idA) backe('!!�P�@��ǥͦ����P�Ǹ�!!');
		}
//	$SQL="SELECT * FROM `stud_absent`  where   stud_id='{$stud_id}' order by `date` ";
	// ����101�Ǧ~�׭���,�אּ�ۦ�P�_ by 103.03.20
	$SQL="SELECT * 
	      FROM `stud_absent`  
	      where `year` >='100' 
	      and  stud_id='{$stud_id}' 	
	      and  absent_kind ='�m��' ";
// $SQL="SELECT * FROM `stud_absent`  where  stud_id='{$stud_id}' 	and  absent_kind ='�m��' ";
	$rs=&$this->CONN->Execute($SQL) or die("�L�k�d�ߡA�y�k:".$SQL);
	if ($rs->RecordCount()==0) return ;
	$A=$rs->GetArray();
	foreach ($A as $ary){
		$K=$ary['date'];
		$stu[$K]['year']=$ary['year'];
		$stu[$K]['semester']=$ary['semester'];
		$stu[$K]['class_id']=$ary['class_id'];
		$stu[$K]['date']=$ary['date'];
		$stu[$K]['stud_id']=$ary['stud_id'];
		$stu[$K]['absent_kind']=$ary['absent_kind'];
		if ($ary['section']=='uf') $ary['section']='��';
		if ($ary['section']=='df') $ary['section']='��';
		//if ($ary['section']=='allday') $ary['section']='����';
		$stu[$K]['section']=$ary['section'];
		$stu[$K]['section2']=$stu[$K]['section2']." ".$ary['section'];
	}
	return $stu; 
	}	

	//�����žǲ�
	function get_balance($sn){
		$student_data=new data_student($sn);
		//print "<pre>";
		$tmp=array();
		$A=array();
		//$tmp= $student_data->all_score;
		$tmp['���d�P��|'] =$student_data->all_score['���d�P��|'];
		$tmp['���N�P�H��'] =$student_data->all_score['���N�P�H��'];
		$tmp['��X����'] =$student_data->all_score['��X����'];
		if($tmp['���d�P��|']['items'] > 1 ) {$A['H'] =$tmp['���d�P��|']['sub_arys']['����'];}
		else{$A['H']=$tmp['���d�P��|']['sub_arys']['���d�P��|'];}
		
		if($tmp['���N�P�H��']['items'] > 1 ) {$A['A'] =$tmp['���N�P�H��']['sub_arys']['����'];}
		else{$A['A']=$tmp['���N�P�H��']['sub_arys']['���N�P�H��'];}
		
		if($tmp['��X����']['items'] > 1 ) {$A['B'] =$tmp['��X����']['sub_arys']['����'];}
		else{$A['B']=$tmp['��X����']['sub_arys']['��X����'];}
		foreach ($A['H'] as $seme=>$sco){
			if ($A['H'][$seme]['score']>=60 &&$A['A'][$seme]['score']>=60 &&$A['B'][$seme]['score']>=60) 
			{$A['Tol'][$seme]='�ŦX';$A['Tol']['Pass']++;}
			else{$A['Tol'][$seme]='--';}
		}
		// if ($A['Tol']['Pass'] >=2 )$A['Tol']['Sco']=2;
		// if ($A['Tol']['Pass'] >=4 )$A['Tol']['Sco']=4;
		// if ($A['Tol']['Pass'] >=5 )$A['Tol']['Sco']=7;        
		return $A;
	}
	
	function get_club($student_sn) {
	$SQL = "SELECT  *  
	        FROM `association`  
	        where student_sn='{$student_sn}' 
	        order by  seme_year_seme  asc 	 ";	
	$rs=&$this->CONN->Execute($SQL) or die("�L�k�d�ߡA�y�k:".$SQL);
	if ($rs->RecordCount()==0) return;
/*
	echo "<pre>";
	print_r($rs->GetArray());
	echo "</pre>";
*/
	return $rs->GetArray();         	
	}
	
	
	//�����žǲ�
	function gCH($seme){
		$A=array(1=>'�@',2=>'�G',3=>'�T',4=>'�|',5=>'��',6=>'��',7=>'�@',8=>'�G',9=>'�T');
		$B=array(1=>'�W',2=>'�U');
		$tmp=explode('_',$seme);
		return $A[$tmp[0]].$B[$tmp[1]];
	}

	//�����žǲ�
	function gLeader($sn){
		$SQL="SELECT * 
		      FROM `chc_leader`  
		      where `student_sn`='{$sn}' 
		      order by seme,kind,org_name ";
		$rs=&$this->CONN->Execute($SQL) or die("�L�k�d�ߡA�y�k:".$SQL);
		if ($rs->RecordCount()==0) return ;
		$A=$rs->GetArray();
		/*
		echo "<pre>";
		print_r($A);
		echo "</pre>";
		*/ 
		return $A;
	}


	//�p��Ǵ�
	function gSeme($day){
		//$day="2014-08-02";
		$D=explode('-',$day);
		if ($D[1]<=7){$Y=$D[0]-1912;$S=2;}else{$Y=$D[0]-1911;$S=1;}
		return $Y.$S;
	}

function tol20($max,$a) {
	/*
	echo "<br>";
	echo $max;
	echo "<br>";
	echo $a;
	*/ 
	if ($a>$max) return $max;
	return $a;
}
//�����ʰO��
function  get_move($SN){
	$stud_coud=study_cond();//���y��ƥN�X
	$SQL = "select * 
	        from stud_move 
	        where student_sn='$SN' 
	        order by move_date";
	$rs=$this->CONN->Execute($SQL) or die("�L�k�d�ߡA�y�k:".$SQL);
	if ($rs->RecordCount()==0) return "�d�L���";
	$stu=$rs->GetArray();
	for ($i=0;$i<$rs->RecordCount();$i++){
		$stu[$i]['c_move_kind']=$stud_coud[$stu[$i]['move_kind']];
	}
	/*
	echo "<pre>";
	print_r($stu);
	echo "</pre>";
	*/ 
	return $stu;
}

}//end class