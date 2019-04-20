<?php
//$Id: PHP_tmp.html 5310 2009-01-10 07:57:56Z hami $
include "config.php";
//認證
sfs_check();

//引入換頁物件(學務系統用法)
include_once "../../include/chi_page2.php";
//程式使用的Smarty樣本檔
$template_file = dirname (__file__)."/reservation.htm";

//建立物件
$obj= new course_room($CONN,$smarty);
//初始化
$obj->init();
//處理程序,有時程序內有header指令,故本程序宜於head("course_room模組");之前
$obj->process();
$obj->weekN7=$weekN7;
//秀出網頁布景標頭
head("專科教室預約");

//顯示SFS連結選單(欲使用請拿開註解)
echo make_menu($school_menu_p);
//顯示內容
$obj->display($template_file);
//佈景結尾
foot();


//物件class
class course_room{
	var $CONN;//adodb物件
	var $smarty;//smarty物件
	var $size=20;//每頁筆數
	var $page;//目前頁數
	var $tol;//資料總筆數
	var $weekN7;//資料總筆數
	var $sections=array('0'=>'早修','1'=>'第1節',
	'2'=>'第2節','3'=>'第3節','4'=>'第4節','100'=>'午修',
	'5'=>'第5節','6'=>'第6節','7'=>'第7節'	);
	var $wk=array('0'=>'週日','1'=>'星期一','2'=>'星期二',
	'3'=>'星期三','4'=>'星期四','5'=>'星期五','6'=>'星期六',);

	//建構函式
	function course_room($CONN,$smarty){
		$this->CONN=&$CONN;
		$this->smarty=&$smarty;
	}
	//初始化
	function init() {
		$this->page=($_GET[page]=='') ? 0:$_GET[page];
		$this->gYM();
		}
	//程序
	function process() {
		$this->init();
		if($_POST[form_act]=='add') $this->add();
		if($_GET[form_act]=='del') $this->del();
		$this->all();
	}



	//顯示
	function display($tpl){
		$this->smarty->assign("this",$this);
		$this->smarty->display($tpl);
	}
	//擷取資料
	function all(){
		$SQL="select crsn from course_room ";
		$rs=$this->CONN->Execute($SQL) or die($SQL);
		$this->tol=$rs->RecordCount();
		$SQL="select * from course_room  order by date desc,sector  limit ".($this->page*$this->size).", {$this->size}  ";
		$rs=$this->CONN->Execute($SQL) or die($SQL);
		$arr=$rs->GetArray();
		$this->all=$arr;//return $arr;
	//產生連結頁面
		$this->links= new Chi_Page($this->tol,$this->size,$this->page);
	}
	//新增
	function add(){
		if (!isset($_POST['sector'])) backe('未選擇節次！');
		if (count($_POST['day'])==0) backe('未選日期！');
		if (empty($_POST['room'])) backe('未選教室/地點！');
		if (empty($_POST['class_kind'])) backe('未選使用單位/處室！');
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
	//刪除預約
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
			case 0:	$j_title='早修';	break;
			case 100:$j_title='午休';break;
			default: $j_title='第'.$SN.'節';break;
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
		//那個月的總天數
		$days = cal_days_in_month(CAL_GREGORIAN,$M,$Y);  
		//該月的第1天，是週？
		$date=$Y.'-'.$M.'-01';
		$WK=date("w",strtotime($date));//該月的第1天，是星期幾？
		$Mx=$days+$WK;//總共須要的格子數
		$Mx2=(ceil($Mx/7))*7;//每列7格，全部的格子數
		//目前所在月份嗎？
		if ($Ya==$Y && $Ma==$M) $Today=date("j");
		
		for ($i=0;$i<$Mx2;$i++){
			$A[$i]['W']=$i%7;//取餘數，就是星期幾？
			if ($A[$i]['W']==0) $A[$i]['color']='D';//週日顏色不同
			if ($i==$WK) $D='1';//第一排迴圈時，順便帶日期$D進去，
			if ($i>=$WK && $i<$Mx){ //再來就是日期$D加1就好
			$A[$i]['d']=$Y.'-'.$M.'-'.$D; //組合成日期格式2016-11-2
			$A[$i]['D']=$D;//第幾天
			if ($Today==$D) $A[$i]['Td']='Y';//是不是今天
			$D++;//日期$D加1
			}	
		}
    return $A;
	}

	//決定選擇月份，上一月，下一月等參數函式,by 村仔 105.11.07
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

	//決定選擇場地函式
	function gPlace() {
		$SQL = "select room_name from spec_classroom where enable='1'";
		$rs=$this->CONN->Execute($SQL) or die($SQL);
		$arr=$rs->GetArray();
		foreach ($arr as $ary){$K=$ary['room_name'];$A[$K]=$K;}
		$this->room=$A;
		return $A;
		}
	//決定是否過期了
	function chkToday($day) {
		$d=explode('-',$day);
		$d0=$d[0]+0;$d1=$d[1]+0;$d2=$d[2]+0;
		if ($d0 > date("Y")) return 'Y';
		if (date("Y")==$d0 && $d1>date("n")) return 'Y';
		if (date("Y")==$d0 && $d1==date("n") && $d[2]>=date("j")) return 'Y';
		}

function gPerson(){
	///$teacher_sn=$_SESSION['session_tea_sn'];
	//取得任教班級代號
	$class_num=get_teach_class();
	//教師姓名
	$A['name']=get_teacher_name($_SESSION['session_tea_sn']);
	//班級列表
	$A['class_ary']=class_base();
	if ($class_num!=''){
		$A['me_class']=$class_num;
		$A['me_class_name']=$A['class_ary'][$class_num];
	}
	//echo $class_num;print_r($class_arr);

    //處室列表
	$SQL = "select room_id,room_name from school_room where enable='1'";
	$rs=$this->CONN->Execute($SQL) or die($SQL);
	$arr=$rs->GetArray();
	foreach ($arr as $ary){$K=$ary['room_name'];$B[$K]=$K;}
	$A['office']=$B;
	return $A;
	}
}


// 結尾符號可略
// 


//----- 回上頁函式 -----//
function backe($value= "BACK"){
	$str="<html><head>\n<meta http-equiv=\"CONTENT-TYPE\" content=\"text/html; charset=big5\">\n<title>：：錯誤訊息：：</title>\n<body><CENTER><br><br><br>\n<H2 style='color:red;'>".$value."</H2><B onclick=\"history.back()\" >[[按下後返回]]</B>\n</CENTER></body>\n</html>";
	echo $str;
	exit;
}
