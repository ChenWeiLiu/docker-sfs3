<?php
//$Id:  $
include "config.php";
//認證
sfs_check();

//引入換頁物件(學務系統用法)
include_once "../../include/chi_page2.php";
//程式使用的Smarty樣本檔
$template_file = dirname (__file__)."/templates/fix_seme_score.htm";
//$template_file = dirname (__file__)."/fix_seme_score.htm";

//建立物件
$obj= new score_ss($CONN,$smarty);
//初始化
//處理程序,有時程序內有header指令,故本程序宜於head("score_ss模組");之前
$obj->process();

//秀出網頁布景標頭
head("學期成績查詢");

//顯示SFS連結選單(欲使用請拿開註解)
echo make_menu($school_menu_p);

//顯示內容
$obj->display($template_file);
//佈景結尾
foot();


//物件class
class score_ss{
	var $CONN;//adodb物件
	var $smarty;//smarty物件
	var $size=30;//每頁筆數
	var $page;//目前頁數
	var $tol;//資料總筆數
   var $IS_JHORES;
   var $Teach;//全部教師
   var $YesNo=array('0'=>'否','1'=>'是');
   var $Grade;

	//建構函式
	function score_ss($CONN,$smarty){
		$this->CONN=&$CONN;
		$this->smarty=&$smarty;
      global $IS_JHORES;
      $this->IS_JHORES=$IS_JHORES;

	}
	//初始化
	function init() {
		//($_GET['YS']=='') ? $this->YS=curr_year().curr_seme():$this->YS=strip_tags($_GET[$this->YS]);
		$this->page=($_GET['page']=='') ? 0:$_GET['page'];
		}
	//程序
	function process() {
		$this->init();
		if($_GET['act']=='del') $this->del();
		$this->Search();
		$this->all();
		$this->getTeach();//教師資料陣列 $this->TeaName
	}


	//顯示
	function display($tpl){
		$this->smarty->assign("this",$this);
		$this->smarty->display($tpl);
		//echo "<pre>";print_r($this->Course);
	}
	//擷取資料
	function all(){
		//echo $this->Search;
		//先處理搜尋條件
		$Search_syntax=$this->Search;
		if ($Search_syntax=='') return ;
		//先算總筆數
		$SQL="select sss_id  from  stud_seme_score  $Search_syntax ";
		$rs=$this->CONN->Execute($SQL) or die($SQL);
		//$arr=$rs->GetArray();
		$this->tol=$rs->rowCount();

		//處理排序依據
		$Order_syntax=' order by student_sn,seme_year_seme,ss_id ,sss_id ';

		//只取分頁資料
		$SQL="select * from  stud_seme_score  $Search_syntax  $Order_syntax  limit ".($this->page*$this->size).", {$this->size}  ";
		$rs=$this->CONN->Execute($SQL) or die($SQL);
		//echo$SQL; 
		$arr=$rs->GetArray();
		$this->all=$arr;//return $arr;
		//產生連結頁面
		$this->links=new Chi_Page($this->tol,$this->size,$this->page);
		

		/*取課程中文名稱*/
  		$SQL="select subject_id,subject_name from score_subject ";
		$rs=$this->CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
		if ($rs->RecordCount()==0) return "尚未設定任何科目資料！";
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
	
		//所有課程設定
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

/*處理搜尋*/
function Search(){
		//不搜尋,清空上次搜尋session,並回傳空值
		if ($_GET['form_act']=='noSearch') {
			//session_unregister('Search');
			$_SESSION['Search']='';
			$this->Search='';
			$URL=$_SERVER['SCRIPT_NAME'];
			Header("Location:$URL");
			return;
		}

		//繼續上次搜尋,直接回傳上次記錄
		if ($_POST['form_act']!='搜尋') {
			$this->Search=$_SESSION['Search'];
			return $this->Search;		
		}

		$SQL=array();
		/*新的搜尋處理$_POST['form_act']=='Search', 先處理字串 */
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


		/* 沒有資料時,清空上次搜尋session,並回傳空值  */
		if (count($SQL)==0){
			$_SESSION['Search']='';//session_unregister('Search');
			$this->Search='';
			return;
		}

		/* 組合新的查詢,記錄到 session,並回傳值  */
		//session_register('Search');
		$_SESSION['Search']="where ".join(' and ',$SQL).' ';  
		$this->Search=$_SESSION['Search'];
		return $this->Search ;
	}

/*擷取教師名稱*/
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

	//刪除記錄
	function del(){
		$YS=strip_tags($_GET['YS']);
		$SN=(int)$_GET['sn'];
		$sss_id =(int)$_GET['id'];
		if ($YS=='' or $YS==0) backe('參數錯誤！');
		if ($SN=='' or $SN==0) backe('參數錯誤！');
		if ($sss_id=='' or $sss_id==0) backe('參數錯誤！');
		
		$SQL="DELETE FROM `stud_seme_score` WHERE `seme_year_seme` = '{$YS}' AND `student_sn` = '{$SN}' AND `sss_id` = '{$sss_id}' LIMIT 1  ";
		$rs=$this->CONN->Execute($SQL) or die($SQL);

		$URL=$_SERVER['SCRIPT_NAME']."?page=".$this->page;
		Header("Location:$URL");
		}




//end class
} 





function backe($st="未填妥!按下後回上頁重填!") {
echo "<BR><BR><BR><CENTER><form>
	<input type='button' name='b1' value='$st' onclick=\"history.back()\" style='font-size:16pt;color:red'>
	</form></CENTER>";
	exit;
	}
