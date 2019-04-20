<?php
//$Id: PHP_tmp.html 5310 2009-01-10 07:57:56Z hami $
//系統設定檔

// include "config.php";
/* 只引入config.php時,因帶有javascript,無法使用header函數 ,所以改為下者 */
include_once "./module-cfg.php";
include_once "../../include/config.php";
	
//認證
sfs_check();

$module_manager=checkid($_SERVER['SCRIPT_FILENAME'],1);
if ($module_manager!=1) {
	head("服務學習項目編修");	
	echo make_menu($school_menu_p);
	echo "抱歉 , 您沒有無管理權限!";
	exit();
}

//$Item=get_module_setup('stud_service');
//print_r($Item);

//引入換頁物件(學務系統用法)
include_once "../../include/chi_page2.php";
//程式使用的Smarty樣本檔
$template_file = dirname (__file__)."/templates/service_item.htm";

//建立物件
$obj= new stud_service($CONN,$smarty);
//初始化
$obj->init();
//處理程序,有時程序內有header指令,故本程序宜於head("stud_service模組");之前
$obj->process();

//秀出網頁布景標頭
head("服務學習項目編修");

//顯示SFS連結選單(欲使用請拿開註解)
echo make_menu($school_menu_p);



//顯示內容
$obj->display($template_file);
//佈景結尾

foot();
//print "<pre>";
//print_r($_SESSION);


//物件class
class stud_service{
	var $CONN;//adodb物件
	var $smarty;//smarty物件
	var $size=15;//每頁筆數
	var $page;//目前頁數
	var $tol;//資料總筆數
	var $Item;//模組參數
	var $YN=array('0'=>'否','1'=>'是');//radio用資料

	//建構函式
	function stud_service($CONN,$smarty){
		$this->CONN=&$CONN;
		$this->smarty=&$smarty;
		//取得模組參數
		$Item=get_module_setup('stud_service');
		$this->Item=explode(',',$Item['item']);
	}
	//初始化
	function init() {$this->page=($_GET[page]=='') ? 0:$_GET[page];}
	//程序
	function process() {
		//if($_POST['form_act']=='Search') $this->SearchMemo();
		if($_POST['form_act']=='add') $this->add();
		if($_POST['form_act']=='update') $this->update();
		if($_GET['form_act']=='del') $this->del();
		// 取處室名稱資料
		$this->setRoom();
		$this->all();
	}
	//顯示
	function display($tpl){
		$this->smarty->assign("this",$this);
		$this->smarty->display($tpl);
	}
	//擷取資料
	function all(){
		//--- 203-10-01 檢查使用者是否擁有最高權限
		$is_admin = false;
		
		$SQL2 = "select * from pro_check_new where (pro_kind_id = 1  and  id_sn = '{$_SESSION[session_tea_sn]}' )";
		$rs2=$this->CONN->Execute($SQL2) or die($SQL2);
		if ($rs2 and $ro2=$rs2->FetchNextObject() ) $is_admin = true;
		//搜尋內容字串
		$Search_Str=$this->SearchMemo();
		if ($Search_Str!='') {
			$this->Search_Str=$Search_Str;
			$addSQL1=" and memo like '%".$Search_Str."%' ";
			$addSQL=" and a.memo like '%".$Search_Str."%' ";
			$admSQL=" where  memo like '%".$Search_Str."%' ";
			}
		$SQL="select sn from stud_service where input_sn='{$_SESSION[session_tea_sn]}' $addSQL1 ";
		if ($is_admin) {
			$SQL="select sn from stud_service $admSQL ";		
		}
		$rs=$this->CONN->Execute($SQL) or die($SQL);
		$this->tol=$rs->RecordCount();
		
		$SQL="select a.*,count(b.sn) as btol from stud_service a ,stud_service_detail b 
		where a.sn=b.item_sn group by  a.sn order by a.sn desc  limit ".($this->page*$this->size).", {$this->size}  ";
		//--- 2013-10-01 修改成 依服務的時間反序排列
		$SQL="select a.*,count(b.sn) as btol from stud_service a ,stud_service_detail b 
		where (a.input_sn='{$_SESSION[session_tea_sn]}') and a.sn=b.item_sn  $addSQL  group by  a.sn order by a.service_date desc  limit ".($this->page*$this->size).", {$this->size}  ";

		if ($is_admin ) {
			$SQL="select a.*,count(b.sn) as btol from stud_service a ,stud_service_detail b 
			where a.sn=b.item_sn $addSQL group by  a.sn order by a.service_date desc  limit ".($this->page*$this->size).", {$this->size}  ";
		}
		//--- 2013-10-01 ----------------------------------------------------------------------------------------
		$rs=$this->CONN->Execute($SQL) or die($SQL);
		$arr=$rs->GetArray();
		$this->all=$arr;//return $arr;
	//產生連結頁面
		$this->links= new Chi_Page($this->tol,$this->size,$this->page);
	}

	//更新
	function update(){
		//變數過濾
		$fields=array('page','sn','year_seme','service_date','department','sponsor','item','memo','confirm');
    	foreach ($fields as $FF){
			//if ($_POST[A]=='') continue ;
			$tmp=filter_var($_POST[$FF], FILTER_SANITIZE_STRING);
			$$FF=strip_tags(trim($tmp));
		}

		$update_time=date("Y-m-d H:i:s"); 
		$SQL="update  stud_service set   year_seme ='{$year_seme}', service_date ='{$service_date}',
		department ='{$department}', item ='{$item}', memo ='{$memo}', sponsor ='{$sponsor}', confirm ='{$confirm}',
		update_time ='$update_time'  where sn ='{$sn}' ";
		$rs=$this->CONN->Execute($SQL) or die($SQL);
		$URL=$_SERVER['SCRIPT_NAME']."?page=".$page;
		Header("Location:".$URL);
	}
	//刪除
	function del(){
		$sn=(int)$_GET['sn'];$page=(int)$_GET['page'];
		$SQL="select sn from  stud_service_detail where  item_sn ='$sn' ";
		$rs=$this->CONN->Execute($SQL) or die($SQL);
		$tol=$rs->RecordCount();
		if ($tol > 0) $this->backe('!!尚有登錄學生不能刪除!!');

		$SQL="Delete from  stud_service  where  sn='{$sn}' ";
		$rs=$this->CONN->Execute($SQL) or die($SQL);
		$URL=$_SERVER['SCRIPT_NAME']."?page=".$page;
		Header("Location:".$URL);
	}
//取得處室名稱-代號,及教師姓名-代號
	function setRoom(){
		$SQL="select room_id,room_name from  school_room  where  enable='1' order by  room_id ";
		$rs=$this->CONN->Execute($SQL) or die($SQL);
		$arr=$rs->GetArray();
		foreach ($arr as $ary){$A[$ary['room_id']]=$ary['room_name'];}
		$this->Room=$A;//return $arr;

		$SQL="select  teacher_sn,`name` from  teacher_base order by  abs(name) ";
		$rs=$this->CONN->Execute($SQL) or die($SQL);
		$arr=$rs->GetArray();
		foreach ($arr as $ary){$B[$ary['teacher_sn']]=$ary['name'];}
		$this->Tea=$B;//return $arr;
	}

function backe($value= "BACK"){
	echo  "<head><meta http-equiv='Content-Type' content='text/html; charset=big5'></head><br><br><br><br><CENTER><form><input type=button value='".$value."' onclick=\"history.back()\" style='font-size:16pt;color:red;'></form><BR></CENTER>";
	exit;
}
function SearchMemo(){
	if (isset($_POST['nSearch'])) {unset($_SESSION['Search_Str']);return ;}
	if (isset($_POST['Search'])) {
		$str=strip_tags(trim($_POST['Search']));
		if (strlen($str)>=2 && strlen($str)<=30) {
			$_SESSION['Search_Str']=$str;
		}
	}
	return $_SESSION['Search_Str'];
}

}
