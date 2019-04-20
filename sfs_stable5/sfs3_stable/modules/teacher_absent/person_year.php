<?php
//$Id: PHP_tmp.html 5310 2009-01-10 07:57:56Z hami $
include "config.php";
//認證
sfs_check();

//引入換頁物件(學務系統用法)
//include_once "../../include/chi_page2.php";
//include_once "../../include/sfs_case_PLlib.php";
//程式使用的Smarty樣本檔
$template_file = dirname (__file__)."/templates/person_year.htm";

//建立物件
$obj= new teacher_absent_course($CONN,$smarty);
$obj->UPLOAD_URL=$UPLOAD_URL;
//初始化
$obj->init();
//處理程序,有時程序內有header指令,故本程序宜於head("teacher_absent_course模組");之前
$obj->process();

//秀出網頁布景標頭
head("差旅費列印");

//顯示SFS連結選單(欲使用請拿開註解)
echo make_menu($school_menu_p);

//顯示內容
$obj->display($template_file);
//佈景結尾
foot();


//物件class
class teacher_absent_course{
	var $CONN;//adodb物件
	var $smarty;//smarty物件
	var $size=10;//每頁筆數
	var $page;//目前頁數
	var $tol;//資料總筆數
	var $SN;//教師代碼

	//建構函式
	function teacher_absent_course($CONN,$smarty){
		$this->CONN=&$CONN;
		$this->smarty=&$smarty;
		
	}
	//初始化
	function init() {
		$this->SN=(int)$_SESSION['session_tea_sn'];//教師
		//$this->SN='300';//測試用
		$this->Sch=get_school_base();//學校資料
		$this->getTeach();//教師資料
		}
	//程序
	function process() {
		if (isset($_GET['Y']) && preg_match("/[0-9]{4}/",$_GET['Y'])){
			$this->Y=(int)$_GET['Y'];
			}
		else{$this->Y=date("Y");}

		//上傳附件
		if (isset($_POST['form_act']) &&$_POST['form_act']=='add_file') $this->add_file();
		
		//取得代碼與假別陣列
		$this->ABS=tea_abs_kind();
		$this->all();
	}
	//顯示
	function display($tpl){
		$this->smarty->assign("this",$this);
		$this->smarty->assign("SN",$this->SN);
		$this->smarty->display($tpl);
	}
	//擷取資料
	function all(){
		$SQL="select a.*,count(c_id) as Num from teacher_absent a
		left join teacher_absent_course b  
		ON a.id=b.a_id and b.teacher_sn=a.teacher_sn and b.travel='1' 
		where a.teacher_sn='{$this->SN}'  and  a.start_date like '{$this->Y}%' 
		group by a.id  ";
		//and check4_sn >'0' "; //已核章
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

		
	//產生連結頁面
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
	if (count($arr)!=1) backe('無相關資料！');//無資料
	$ary=$arr[0];
	if ($ary['check4_sn']!='0') backe('人事己經核章！不能再變更！');
	if ($this->chkDay($ary['start_date'])!='Y') backe('超過上傳期限！');//超過上傳期限
	if ($_FILES['ufile']['error']!=0) backe('檔案上傳錯誤！');
	if ($_FILES['ufile']['size']==0) backe('檔案上傳錯誤！');
	if (check_is_php_file($_FILES['ufile']['name'])) backe('不可上傳PHP檔案！');
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



//目前日期,修改期限,返回空值或Y
function chkDay($day,$mx=20) {
	$old = strtotime($day);
	$now = mktime();
	//幾天之前
	$diff=floor(($now-$old)/(60*60*24));
	//if ($mx>$diff) echo '未到期'.$diff;
	if ($mx > $diff) return 'Y';	
}



/*擷取教師名冊*/
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
// 結尾符號可略


function backe($value= "BACK"){
	echo "<html><head>
<meta http-equiv='content-type' content='text/html; charset=Big5'>
<title>！！錯誤訊息！！</title>
<META NAME='ROBOTS' CONTENT='NOARCHIVE'>
<META NAME='ROBOTS' CONTENT='NOINDEX, NOFOLLOW'>
<META HTTP-EQUIV='Pargma' CONTENT='no-cache'>
<center style='margin-top: 120px'>
<b style='color:red'>！！錯誤訊息！！</b><br>
<h1 onclick='window.history.back();' title='按下後返回'>$value</h1><br><br>
</center></body></html>";
exit;
}
