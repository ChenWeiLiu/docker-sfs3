<?php
//$Id: PHP_tmp.html 5310 2009-01-10 07:57:56Z hami $
include "config.php";
//�{��
sfs_check();

//�ޤJ��������(�ǰȨt�ΥΪk)
include_once "../../include/chi_page2.php";
//�{���ϥΪ�Smarty�˥���
$template_file = dirname (__file__)."/reservation.htm";

//�إߪ���
$obj= new course_room($CONN,$smarty);
//��l��
$obj->init();
//�B�z�{��,���ɵ{�Ǥ���header���O,�G���{�ǩy��head("course_room�Ҳ�");���e
$obj->process();
$obj->weekN7=$weekN7;
//�q�X�����������Y
head("�M��Ыǹw��");

//���SFS�s�����(���ϥνЮ��}����)
echo make_menu($school_menu_p);
//��ܤ��e
$obj->display($template_file);
//�G������
foot();


//����class
class course_room{
	var $CONN;//adodb����
	var $smarty;//smarty����
	var $size=20;//�C������
	var $page;//�ثe����
	var $tol;//����`����
	var $weekN7;//����`����
	var $sections=array('0'=>'����','1'=>'��1�`',
	'2'=>'��2�`','3'=>'��3�`','4'=>'��4�`','100'=>'�ȭ�',
	'5'=>'��5�`','6'=>'��6�`','7'=>'��7�`'	);
	var $wk=array('0'=>'�g��','1'=>'�P���@','2'=>'�P���G',
	'3'=>'�P���T','4'=>'�P���|','5'=>'�P����','6'=>'�P����',);

	//�غc�禡
	function course_room($CONN,$smarty){
		$this->CONN=&$CONN;
		$this->smarty=&$smarty;
	}
	//��l��
	function init() {
		$this->page=($_GET[page]=='') ? 0:$_GET[page];
		$this->gYM();
		}
	//�{��
	function process() {
		$this->init();
		if($_POST[form_act]=='add') $this->add();
		if($_GET[form_act]=='del') $this->del();
		$this->all();
	}



	//���
	function display($tpl){
		$this->smarty->assign("this",$this);
		$this->smarty->display($tpl);
	}
	//�^�����
	function all(){
		$SQL="select crsn from course_room ";
		$rs=$this->CONN->Execute($SQL) or die($SQL);
		$this->tol=$rs->RecordCount();
		$SQL="select * from course_room  order by date desc,sector  limit ".($this->page*$this->size).", {$this->size}  ";
		$rs=$this->CONN->Execute($SQL) or die($SQL);
		$arr=$rs->GetArray();
		$this->all=$arr;//return $arr;
	//���ͳs������
		$this->links= new Chi_Page($this->tol,$this->size,$this->page);
	}
	//�s�W
	function add(){
		if (!isset($_POST['sector'])) backe('����ܸ`���I');
		if (count($_POST['day'])==0) backe('�������I');
		if (empty($_POST['room'])) backe('����Ы�/�a�I�I');
		if (empty($_POST['class_kind'])) backe('����ϥγ��/�B�ǡI');
		//echo '<pre>';print_r($_POST);die();
		$room=strip_tags($_POST['room']);
		$sector=(int)$_POST['sector'];
		$class_kind=(int)$_POST['class_kind'];
		switch ($class_kind) {
			case 0:$seme_class=strip_tags($_POST['class0']);break;
			case 1:$seme_class=strip_tags($_POST['class1']);break;
			case 2:$seme_class=strip_tags($_POST['class2']);break;
		}
		$sign_date=date("Y-m-d H:i:s");
		$teacher_sn=$_SESSION['session_tea_sn'];
		foreach ($_POST['day'] as $date=>$day){
			$date=strip_tags($date);
			$day=strip_tags($day);
			$SQL="INSERT INTO course_room(date,day,sector,room,teacher_sn,sign_date,seme_class)  values ('{$date}' ,'{$day}' ,'{$sector}' ,'{$room}' ,'{$teacher_sn}' ,'{$sign_date}' ,'{$seme_class}' )";
			$rs=$this->CONN->Execute($SQL);
		}
		//$Insert_ID= $this->CONN->Insert_ID();
		//$URL=$_SERVER['SCRIPT_NAME']."?page=".$this->page;
		$URL='index.php?room='.$room;
		Header("Location:$URL");
	}
	//�R���w��
	function del(){
		$ID=(int)$_GET['crsn'];
		$teacher_sn=$_SESSION['session_tea_sn'];
		$SQL="Delete from  course_room  where  crsn='{$ID}' and teacher_sn='{$teacher_sn}' ";
		$rs=$this->CONN->Execute($SQL) or die($SQL);
		$URL=$_SERVER['SCRIPT_NAME']."?page=".$this->page;
		Header("Location:$URL");
	}

	function tea_name($SN){
		return get_teacher_name($SN);
		}

	function Sector($SN){
		switch ($SN) {
			case 0:	$j_title='����';	break;
			case 100:$j_title='�ȥ�';break;
			default: $j_title='��'.$SN.'�`';break;
			}
		return $j_title;
	}

	function dayAry($date){
		$Ya=date("Y");$Ma=date("n");
		$Today='';
		if ($date=='') {$Y=$Ya;$M=$Ma;}
		else {
		$date_array = explode('-',$date);
		$Y = $date_array[0]+0;
		$M = $date_array[1]+0;}
		//���Ӥ몺�`�Ѽ�
		$days = cal_days_in_month(CAL_GREGORIAN,$M,$Y);  
		//�Ӥ몺��1�ѡA�O�g�H
		$date=$Y.'-'.$M.'-01';
		$WK=date("w",strtotime($date));//�Ӥ몺��1�ѡA�O�P���X�H
		$Mx=$days+$WK;//�`�@���n����l��
		$Mx2=(ceil($Mx/7))*7;//�C�C7��A��������l��
		//�ثe�Ҧb����ܡH
		if ($Ya==$Y && $Ma==$M) $Today=date("j");
		
		for ($i=0;$i<$Mx2;$i++){
			$A[$i]['W']=$i%7;//���l�ơA�N�O�P���X�H
			if ($A[$i]['W']==0) $A[$i]['color']='D';//�g���C�⤣�P
			if ($i==$WK) $D='1';//�Ĥ@�ưj��ɡA���K�a���$D�i�h�A
			if ($i>=$WK && $i<$Mx){ //�A�ӴN�O���$D�[1�N�n
			$A[$i]['d']=$Y.'-'.$M.'-'.$D; //�զX������榡2016-11-2
			$A[$i]['D']=$D;//�ĴX��
			if ($Today==$D) $A[$i]['Td']='Y';//�O���O����
			$D++;//���$D�[1
			}	
		}
    return $A;
	}

	//�M�w��ܤ���A�W�@��A�U�@�뵥�Ѽƨ禡,by ���J 105.11.07
	function gYM() {
		if ($_GET['YM']!='')$YM=strip_tags($_GET['YM']);
		if ($_POST['YM']!='')$YM=strip_tags($_POST['YM']);
		if ($YM=='') $YM=date("Y-m");
		$date= explode('-',$YM);
		$Y = $date[0];
		$M = $date[1]+0;
		switch ($M)	{
		case 1: $uY=$Y-1;$uM=12;$nY=$Y;$nM=$M+1; break;
		case 12:$uY=$Y;$uM=$M-1;$nY=$Y+1;$nM=1; break;
		default:$nY=$Y;$uY=$Y;$nM=$M+1;$uM=$M-1;
		}
		$this->YM=$YM;
		$this->uYM=$uY.'-'.sprintf("%02d",$uM);
		$this->nYM=$nY.'-'.sprintf("%02d",$nM);
		}

	//�M�w��ܳ��a�禡
	function gPlace() {
		$SQL = "select room_name from spec_classroom where enable='1'";
		$rs=$this->CONN->Execute($SQL) or die($SQL);
		$arr=$rs->GetArray();
		foreach ($arr as $ary){$K=$ary['room_name'];$A[$K]=$K;}
		$this->room=$A;
		return $A;
		}
	//�M�w�O�_�L���F
	function chkToday($day) {
		$d=explode('-',$day);
		$d0=$d[0]+0;$d1=$d[1]+0;$d2=$d[2]+0;
		if ($d0 > date("Y")) return 'Y';
		if (date("Y")==$d0 && $d1>date("n")) return 'Y';
		if (date("Y")==$d0 && $d1==date("n") && $d[2]>=date("j")) return 'Y';
		}

function gPerson(){
	///$teacher_sn=$_SESSION['session_tea_sn'];
	//���o���ЯZ�ťN��
	$class_num=get_teach_class();
	//�Юv�m�W
	$A['name']=get_teacher_name($_SESSION['session_tea_sn']);
	//�Z�ŦC��
	$A['class_ary']=class_base();
	if ($class_num!=''){
		$A['me_class']=$class_num;
		$A['me_class_name']=$A['class_ary'][$class_num];
	}
	//echo $class_num;print_r($class_arr);

    //�B�ǦC��
	$SQL = "select room_id,room_name from school_room where enable='1'";
	$rs=$this->CONN->Execute($SQL) or die($SQL);
	$arr=$rs->GetArray();
	foreach ($arr as $ary){$K=$ary['room_name'];$B[$K]=$K;}
	$A['office']=$B;
	return $A;
	}
}


// �����Ÿ��i��
// 


//----- �^�W���禡 -----//
function backe($value= "BACK"){
	$str="<html><head>\n<meta http-equiv=\"CONTENT-TYPE\" content=\"text/html; charset=big5\">\n<title>�G�G���~�T���G�G</title>\n<body><CENTER><br><br><br>\n<H2 style='color:red;'>".$value."</H2><B onclick=\"history.back()\" >[[���U���^]]</B>\n</CENTER></body>\n</html>";
	echo $str;
	exit;
}
