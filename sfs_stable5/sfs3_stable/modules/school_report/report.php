<?php
//$Id: report.php 8891 2016-05-04 14:00:41Z chiming $
include "config.php";
//認證
sfs_check();

include_once "../../include/sfs_case_dataarray.php";
//引入換頁物件(學務系統用法)
include_once "../../include/chi_page2.php";

//程式使用的Smarty樣本檔
$template_file = dirname (__file__)."/templates/school_report_things.htm";

//建立物件
$obj= new school_report_things($CONN,$smarty);
//初始化
$obj->init();
//處理程序,有時程序內有header指令,故本程序宜於head("school_report_things模組");之前
$obj->process();

//秀出網頁布景標頭
head("校務報告匯整");

//顯示SFS連結選單(欲使用請拿開註解)
//echo make_menu($school_menu_p);

//顯示內容
$obj->display($template_file);
//佈景結尾
//取代textarea為ckeditor
echo "<script>CKEDITOR.replace('report_content',{ language: 'zh',toolbar:'simple'});</script>";
foot();


//物件class
class school_report_things{
	var $CONN;//adodb物件
	var $smarty;//smarty物件
	var $size=10;//每頁筆數
	var $page;//目前頁數
	var $tol;//資料總筆數
	//建構函式
	function school_report_things($CONN,$smarty){
		$this->CONN=&$CONN;
		$this->smarty=&$smarty;
	}
	//初始化
	function init() {
		global $SCHOOL_BASE;
		$this->smarty->assign('SCHOOL_BASE',$SCHOOL_BASE);
	}
	//程序
	function process() {
		if($_POST[form_act]=='add') $this->add();
		if($_POST[form_act]=='update') $this->update();
		if($_GET[form_act]=='del') $this->del();
		if ($_GET['act'] == 'print'){
			$this->display(dirname (__FILE__)."/templates/list_rep.tpl");
			exit;
		}
		//fix by licf 2009/08/03
		if ($_GET['act'] == 'big_print'){
			$this->display(dirname (__FILE__)."/templates/list_rep2.tpl");
			exit;

		}
	}
	//顯示
	function display($tpl){
		$this->smarty->assign('temp_path',dirname (__FILE__)."/templates/");
		$this->smarty->assign("this",$this);
		$this->smarty->display($tpl);
	}
	/**
	* 取得職稱資料
	*/
	function & get_title(){
		$query = "SELECT a.teacher_sn, b.post_kind, b.post_office,d.title_name ,b.class_num FROM teacher_base a , teacher_post b, teacher_title d WHERE a.teacher_sn = b.teacher_sn AND b.teach_title_id = d.teach_title_id ";
		$res = & $this->CONN->Execute($query) or die($query);
		$arr = array();
		while($row = $res->fetchRow()){
			$arr[$row['teacher_sn']] = $row;
		}
		return $arr;

	}


	//擷取資料
	function & get_all($year_seme,$val,$is_date=0){
		if ($is_date == 1)
			$temp = " AND open_date='$val' ";
		else
			$temp = " AND weeks='$val' ";

		$SQL="select a.*,b.name,c.teach_title_id from school_report_things a ,teacher_base b,teacher_post c, teacher_title d WHERE a.teacher_sn=b.teacher_sn AND a.year_seme='$year_seme' AND c.teacher_sn=a.teacher_sn AND c.teach_title_id=d.teach_title_id $temp  order by a.open_date desc, a.room_id ASC,d.rank";
		$rs=$this->CONN->Execute($SQL) or die($SQL);
		$arr=$rs->GetArray();
		return $arr;
	}

	//新增
	function add(){
		$week_num = $this->getCurrweek($_POST['open_date']);
		$SQL="INSERT INTO school_report_things(weeks,title,content,c_time,teacher_sn,room_id,year_seme,open_date)  values ('{$week_num}' ,'{$_POST['title']}' ,'{$_POST['report_content']}' ,now() ,'{$_SESSION['session_tea_sn']}' ,'{$_POST['room_id']}' ,'{$_POST['year_seme']}' ,'{$_POST['open_date']}')";
		$rs=$this->CONN->Execute($SQL) or die($SQL);
		//$Insert_ID= $this->CONN->Insert_ID();
		$URL=$_SERVER[PHP_SELF]."?year_seme={$_POST['year_seme']}&week_num={$_POST['week_num']}";
		Header("Location:$URL");
	}
	//更新
	function update(){
		$week_num = $this->getCurrweek($_POST['open_date']);
		$SQL="update  school_report_things set   title ='{$_POST['title']}', content ='{$_POST['report_content']}', c_time =now(), teacher_sn ='{$_SESSION['session_tea_sn']}', room_id ='{$_POST['room_id']}', open_date ='{$_POST['open_date']}' , weeks='$week_num'  where id ='{$_POST['id']}'";
		$rs=$this->CONN->Execute($SQL) or die($SQL);
		$URL=$_SERVER[PHP_SELF]."?year_seme={$_POST['year_seme']}&week_num={$_POST['week_num']}";
		Header("Location:$URL");
	}


	//刪除
	function del(){
		$SQL="Delete from  school_report_things  where  id='{$_GET['id']}' ";
		$rs=$this->CONN->Execute($SQL) or die($SQL);
		$URL=$_SERVER[PHP_SELF]."?page=".$_GET[page];
		Header("Location:$URL");
	}

	/**
	* 取得處室陣列
	*/
	function & getRoomArr(){
		return room_kind();

	}

	/**
	* 取專學年學期
	*/
	function &  get_year_seme() {
		$sel_year = curr_year();
		$sel_seme = curr_seme();
		$year_seme = sprintf("%03d%d",$sel_year,$sel_seme);
		$arr[$year_seme] = "$sel_year 學年第 $sel_seme 學期";
		$query = "SELECT DISTINCT year_seme FROM school_report_things ORDER BY year_seme DESC";
		$res = & $this->CONN->Execute($query) or trigger_error("SQL 語法錯誤: $query", E_USER_ERROR);
		while($row = $res->FetchRow()) {
			$arr[$row['year_seme']] = substr($row['year_seme'],0,3)." 學年第 ".substr($row['year_seme'],-1)."學期";
		}
		return $arr;
	}

	/**
	* 取得本周 id
	*/
	function getCurrweek($this_date='') {
		$sel_year = curr_year();
		$sel_seme = curr_seme();
		if ($this_date=='') $this_date=date("Y-m-d");
		//取得週次
		$weeks_array=get_week_arr($sel_year,$sel_seme,$this_date);
		return $weeks_array[0];
	}


	/**
	* 取得周別
	*/
	function & get_week($year_seme=''){
		if (empty($year_seme)){
			$sel_year = curr_year();
			$sel_seme = curr_seme();
		}
		else{
			$sel_year = substr($year_seme,0,3);
			$sel_seme = substr($year_seme,-1);
		}
		//週選單
		$start_day = curr_year_seme_day($sel_year,$sel_seme);
		if (!$start_day[st_start])
			return "開學日沒有設定";
		else {
			//取得週次
			$weeks_array=get_week_arr($sel_year,$sel_seme);
			while(list($k,$v)=each($weeks_array)) {
				if ($k==0) continue;
				$weeks[$k]="第".$k."週 ($v ~ ".date("Y-m-d",(strtotime("+ 6 days",strtotime($v)))).")";
			}
			return $weeks;
		}
	}

	/**
	* 取得使用者處室
	*/
	function get_user_room_id(){

		$query =  "SELECT post_office FROM teacher_post WHERE teacher_sn='{$_SESSION['session_tea_sn']}'";
		$res = $this->CONN->Execute($query);
		$row = $res->fetchRow();
		return $row['post_office'];
	}

	/**
	* 取得  fckeditor
	*/
	function & getFckeditor($fname,$value=''){
		require "../../include/fckeditor.php";
		$oFCKeditor = new FCKeditor($fname) ;
		$oFCKeditor->ToolbarSet = 'Basic';
		$oFCKeditor->Value=$value;
		return $oFCKeditor;
	}

	/**
	* 取得資料
	*/
	function & getData($id){

		$query = "SELECT * FROM school_report_things WHERE id = $id";
		$res = & $this->CONN->Execute($query) or die($query);
		return $res->fetchRow();
	}
}
?>
