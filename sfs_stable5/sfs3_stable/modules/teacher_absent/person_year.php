<?php
//$Id: PHP_tmp.html 5310 2009-01-10 07:57:56Z hami $
include "config.php";
//�{��
sfs_check();

//�ޤJ��������(�ǰȨt�ΥΪk)
//include_once "../../include/chi_page2.php";
//include_once "../../include/sfs_case_PLlib.php";
//�{���ϥΪ�Smarty�˥���
$template_file = dirname (__file__)."/templates/person_year.htm";

//�إߪ���
$obj= new teacher_absent_course($CONN,$smarty);
$obj->UPLOAD_URL=$UPLOAD_URL;
//��l��
$obj->init();
//�B�z�{��,���ɵ{�Ǥ���header���O,�G���{�ǩy��head("teacher_absent_course�Ҳ�");���e
$obj->process();

//�q�X�����������Y
head("�t�ȶO�C�L");

//���SFS�s�����(���ϥνЮ��}����)
echo make_menu($school_menu_p);

//��ܤ��e
$obj->display($template_file);
//�G������
foot();


//����class
class teacher_absent_course{
	var $CONN;//adodb����
	var $smarty;//smarty����
	var $size=10;//�C������
	var $page;//�ثe����
	var $tol;//����`����
	var $SN;//�Юv�N�X

	//�غc�禡
	function teacher_absent_course($CONN,$smarty){
		$this->CONN=&$CONN;
		$this->smarty=&$smarty;
		
	}
	//��l��
	function init() {
		$this->SN=(int)$_SESSION['session_tea_sn'];//�Юv
		//$this->SN='300';//���ե�
		$this->Sch=get_school_base();//�Ǯո��
		$this->getTeach();//�Юv���
		}
	//�{��
	function process() {
		if (isset($_GET['Y']) && preg_match("/[0-9]{4}/",$_GET['Y'])){
			$this->Y=(int)$_GET['Y'];
			}
		else{$this->Y=date("Y");}

		//�W�Ǫ���
		if (isset($_POST['form_act']) &&$_POST['form_act']=='add_file') $this->add_file();
		
		//���o�N�X�P���O�}�C
		$this->ABS=tea_abs_kind();
		$this->all();
	}
	//���
	function display($tpl){
		$this->smarty->assign("this",$this);
		$this->smarty->assign("SN",$this->SN);
		$this->smarty->display($tpl);
	}
	//�^�����
	function all(){
		$SQL="select a.*,count(c_id) as Num from teacher_absent a
		left join teacher_absent_course b  
		ON a.id=b.a_id and b.teacher_sn=a.teacher_sn and b.travel='1' 
		where a.teacher_sn='{$this->SN}'  and  a.start_date like '{$this->Y}%' 
		group by a.id  ";
		//and check4_sn >'0' "; //�w�ֳ�
		//$SQL="select a.* from teacher_absent a
		//where a.teacher_sn='{$SN}'  and a.abs_kind='52' ";
		$SQL.=" order by a.start_date desc";
		$rs=$this->CONN->Execute($SQL) or die($SQL);
		$arr=$rs->GetArray();
		$this->all=$arr;//return $arr;
		$tmp=array();
		foreach ($arr as $ary){
			$K=$ary['abs_kind'];
			$tmp[$K]['day']=$tmp[$K]['day']+$ary['day'];
			$tmp[$K]['hour']=$tmp[$K]['hour']+$ary['hour'];
			if ($tmp[$K]['hour']>=8){
				$tmp[$K]['day']++;
				$tmp[$K]['hour']=$tmp[$K]['hour']-8;}
		}
		$this->absTol=$tmp;

		
	//���ͳs������
	//$this->links= new Chi_Page($this->tol,$this->size,$this->page);
	}

function add_file(){
	//print_r($_POST);	print_r($_FILES);
	$id=(int)$_POST['id'];
	$start_date=strip_tags($_POST['start_date']);
	$SQL="select * from teacher_absent 
	where id='{$id}' and teacher_sn='{$this->SN}'  and  start_date='{$start_date}' ";
	$rs=$this->CONN->Execute($SQL) or die($SQL);
	$arr=$rs->GetArray();
	if (count($arr)!=1) backe('�L������ơI');//�L���
	$ary=$arr[0];
	if ($ary['check4_sn']!='0') backe('�H�Ƥv�g�ֳ��I����A�ܧ�I');
	if ($this->chkDay($ary['start_date'])!='Y') backe('�W�L�W�Ǵ����I');//�W�L�W�Ǵ���
	if ($_FILES['ufile']['error']!=0) backe('�ɮפW�ǿ��~�I');
	if ($_FILES['ufile']['size']==0) backe('�ɮפW�ǿ��~�I');
	if (check_is_php_file($_FILES['ufile']['name'])) backe('���i�W��PHP�ɮסI');
	$temp = explode('.',$_FILES['ufile']['name']);
	$fileName = time().'.'.end($temp);
	$filePath = set_upload_path("/school/teacher_absent");
	//echo $filePath.$fileName;
	//echo $filePath.$ary['note_file'];
	if (copy($_FILES['ufile']['tmp_name'],$filePath.$fileName))	{
		if ($ary['note_file']!='')	unlink($filePath.$ary['note_file']);
		$SQL="update teacher_absent set note_file='{$fileName}' where id='{$id}' ";
		$rs=$this->CONN->Execute($SQL) or die($SQL);
	}
	$URL=$_SERVER['SCRIPT_NAME'];
	Header("Location:$URL");
}



//�ثe���,�ק����,��^�ŭȩ�Y
function chkDay($day,$mx=20) {
	$old = strtotime($day);
	$now = mktime();
	//�X�Ѥ��e
	$diff=floor(($now-$old)/(60*60*24));
	//if ($mx>$diff) echo '�����'.$diff;
	if ($mx > $diff) return 'Y';	
}



/*�^���Юv�W�U*/
function getTeach() {
	$SQL = "SELECT a.teacher_sn, a.name, a.birthday, a.address, a.home_phone, a.cell_phone , d.title_name ,b.class_num,b.post_class FROM  teacher_base a , teacher_post b, teacher_title d where  a.teacher_sn =b.teacher_sn  and b.teach_title_id = d.teach_title_id $teach_cond order by class_num, post_kind , post_office , a.teach_id ";
	$rs=$this->CONN->Execute($SQL) or die($SQL);
	$arys=array();
	while ($rs and $ro=$rs->FetchNextObject(false)) {
		$key=$ro->teacher_sn;
		$arys[$key] = get_object_vars($ro);
		}
	$this->Tea=$arys;
}




}
// �����Ÿ��i��


function backe($value= "BACK"){
	echo "<html><head>
<meta http-equiv='content-type' content='text/html; charset=Big5'>
<title>�I�I���~�T���I�I</title>
<META NAME='ROBOTS' CONTENT='NOARCHIVE'>
<META NAME='ROBOTS' CONTENT='NOINDEX, NOFOLLOW'>
<META HTTP-EQUIV='Pargma' CONTENT='no-cache'>
<center style='margin-top: 120px'>
<b style='color:red'>�I�I���~�T���I�I</b><br>
<h1 onclick='window.history.back();' title='���U���^'>$value</h1><br><br>
</center></body></html>";
exit;
}
