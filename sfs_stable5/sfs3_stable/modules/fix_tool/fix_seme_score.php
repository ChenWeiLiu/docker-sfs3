<?php
//$Id:  $
include "config.php";
//�{��
sfs_check();

//�ޤJ��������(�ǰȨt�ΥΪk)
include_once "../../include/chi_page2.php";
//�{���ϥΪ�Smarty�˥���
$template_file = dirname (__file__)."/templates/fix_seme_score.htm";
//$template_file = dirname (__file__)."/fix_seme_score.htm";

//�إߪ���
$obj= new score_ss($CONN,$smarty);
//��l��
//�B�z�{��,���ɵ{�Ǥ���header���O,�G���{�ǩy��head("score_ss�Ҳ�");���e
$obj->process();

//�q�X�����������Y
head("�Ǵ����Z�d��");

//���SFS�s�����(���ϥνЮ��}����)
echo make_menu($school_menu_p);

//��ܤ��e
$obj->display($template_file);
//�G������
foot();


//����class
class score_ss{
	var $CONN;//adodb����
	var $smarty;//smarty����
	var $size=30;//�C������
	var $page;//�ثe����
	var $tol;//����`����
   var $IS_JHORES;
   var $Teach;//�����Юv
   var $YesNo=array('0'=>'�_','1'=>'�O');
   var $Grade;

	//�غc�禡
	function score_ss($CONN,$smarty){
		$this->CONN=&$CONN;
		$this->smarty=&$smarty;
      global $IS_JHORES;
      $this->IS_JHORES=$IS_JHORES;

	}
	//��l��
	function init() {
		//($_GET['YS']=='') ? $this->YS=curr_year().curr_seme():$this->YS=strip_tags($_GET[$this->YS]);
		$this->page=($_GET['page']=='') ? 0:$_GET['page'];
		}
	//�{��
	function process() {
		$this->init();
		if($_GET['act']=='del') $this->del();
		$this->Search();
		$this->all();
		$this->getTeach();//�Юv��ư}�C $this->TeaName
	}


	//���
	function display($tpl){
		$this->smarty->assign("this",$this);
		$this->smarty->display($tpl);
		//echo "<pre>";print_r($this->Course);
	}
	//�^�����
	function all(){
		//echo $this->Search;
		//���B�z�j�M����
		$Search_syntax=$this->Search;
		if ($Search_syntax=='') return ;
		//�����`����
		$SQL="select sss_id  from  stud_seme_score  $Search_syntax ";
		$rs=$this->CONN->Execute($SQL) or die($SQL);
		//$arr=$rs->GetArray();
		$this->tol=$rs->rowCount();

		//�B�z�ƧǨ̾�
		$Order_syntax=' order by student_sn,seme_year_seme,ss_id ,sss_id ';

		//�u���������
		$SQL="select * from  stud_seme_score  $Search_syntax  $Order_syntax  limit ".($this->page*$this->size).", {$this->size}  ";
		$rs=$this->CONN->Execute($SQL) or die($SQL);
		//echo$SQL; 
		$arr=$rs->GetArray();
		$this->all=$arr;//return $arr;
		//���ͳs������
		$this->links=new Chi_Page($this->tol,$this->size,$this->page);
		

		/*���ҵ{����W��*/
  		$SQL="select subject_id,subject_name from score_subject ";
		$rs=$this->CONN->Execute($SQL) or die("�L�k�d�ߡA�y�k:".$SQL);
		if ($rs->RecordCount()==0) return "�|���]�w�����ظ�ơI";
		$obj=$rs->GetArray();
		foreach($obj as $ary){
			$id=$ary[subject_id];
			$this->Subj[$id]=$ary[subject_name];
		}

		$this->SsidToName=$this->SsidToName();
		//print_r($this->SsidToName);
	}

	function SsidToName(){
		$SQL="select subject_id, subject_name from score_subject order by  subject_id ";
	
		//�Ҧ��ҵ{�]�w
		$SQL="select * from score_ss where  enable='1'  order by class_year ";
		$rs=$this->CONN->Execute($SQL) or die($SQL);
		$arr=$rs->GetArray();
		//$this->all=$arr;//return $arr;		
		foreach ($arr as $ary){
			$id=$ary[ss_id];
			$scope=$ary[scope_id];
			$subject=$ary[subject_id];
			$AA[$id]=$ary['class_year'].$this->Subj[$scope].':'.$this->Subj[$subject]."($id)";
		}	
	return $AA;
	}

/*�B�z�j�M*/
function Search(){
		//���j�M,�M�ŤW���j�Msession,�æ^�Ǫŭ�
		if ($_GET['form_act']=='noSearch') {
			//session_unregister('Search');
			$_SESSION['Search']='';
			$this->Search='';
			$URL=$_SERVER['SCRIPT_NAME'];
			Header("Location:$URL");
			return;
		}

		//�~��W���j�M,�����^�ǤW���O��
		if ($_POST['form_act']!='�j�M') {
			$this->Search=$_SESSION['Search'];
			return $this->Search;		
		}

		$SQL=array();
		/*�s���j�M�B�z$_POST['form_act']=='Search', ���B�z�r�� */
		if ($_POST['SN']!='') {
			$SN=(int)$_POST['SN'];
			$SQL[]=" student_sn='{$SN}' ";
		}
		if ($_POST['SSID']!='') {
			$SSID=(int)$_POST['SSID'];
			$SQL[]=" ss_id='{$SSID}' ";
		}

		if ($_POST['YS']!='') {
			$YS=strip_tags($_POST['YS']);
			$SQL[]=" seme_year_seme='{$YS}' ";
		}


		/* �S����Ʈ�,�M�ŤW���j�Msession,�æ^�Ǫŭ�  */
		if (count($SQL)==0){
			$_SESSION['Search']='';//session_unregister('Search');
			$this->Search='';
			return;
		}

		/* �զX�s���d��,�O���� session,�æ^�ǭ�  */
		//session_register('Search');
		$_SESSION['Search']="where ".join(' and ',$SQL).' ';  
		$this->Search=$_SESSION['Search'];
		return $this->Search ;
	}

/*�^���Юv�W��*/
function getTeach() {
		$SQL="select  teacher_sn,name  from teacher_base  ";
		$rs=$this->CONN->Execute($SQL) or die($SQL);
		$arr=$rs->GetArray();	
		foreach($arr as $ary) {
		$K=$ary['teacher_sn'];
		//$T[$K]=$ary;
		$this->TeaName[$K]=$ary['name'];
		}
	//return $T;

}

	//�R���O��
	function del(){
		$YS=strip_tags($_GET['YS']);
		$SN=(int)$_GET['sn'];
		$sss_id =(int)$_GET['id'];
		if ($YS=='' or $YS==0) backe('�Ѽƿ��~�I');
		if ($SN=='' or $SN==0) backe('�Ѽƿ��~�I');
		if ($sss_id=='' or $sss_id==0) backe('�Ѽƿ��~�I');
		
		$SQL="DELETE FROM `stud_seme_score` WHERE `seme_year_seme` = '{$YS}' AND `student_sn` = '{$SN}' AND `sss_id` = '{$sss_id}' LIMIT 1  ";
		$rs=$this->CONN->Execute($SQL) or die($SQL);

		$URL=$_SERVER['SCRIPT_NAME']."?page=".$this->page;
		Header("Location:$URL");
		}




//end class
} 





function backe($st="����!���U��^�W������!") {
echo "<BR><BR><BR><CENTER><form>
	<input type='button' name='b1' value='$st' onclick=\"history.back()\" style='font-size:16pt;color:red'>
	</form></CENTER>";
	exit;
	}
