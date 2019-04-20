<?php
//$Id: PHP_tmp.html 5310 2009-01-10 07:57:56Z hami $
include "config.php";
//認證
sfs_check();

//引入換頁物件(學務系統用法)
//include_once "../../include/chi_page2.php";
//include_once "../../include/sfs_case_PLlib.php";
//程式使用的Smarty樣本檔
$template_file = dirname (__file__)."/templates/teacher_chi.htm";

//建立物件
$obj= new teacher_absent_course($CONN,$smarty);
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
		$year=(int)$_GET['Y'];
		if ($year==0 || $year=='') $year=date("Y");
		if (strlen($year)==4) $this->Y=$year;		
		}
	//程序
	function process() {

		if($_POST['form_act']=='OK') $this->add();
		if($_POST['form_act']=='id_select') $this->add2();

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
		//$Year=date("Y");
		$Year_a=$this->Y-1;
		
		$SQL="select a.*,count(c_id) as Num from teacher_absent a
		left join teacher_absent_course b  
		ON a.id=b.a_id and b.teacher_sn=a.teacher_sn and b.travel='1' 
		where a.teacher_sn='{$this->SN}'  and a.abs_kind='52' 
		and (a.start_date like '{$this->Y}%' or a.start_date like '{$Year_a}%' )
		group by a.id  ";
		//and (a.start_date like '{$this->Y}%' or a.start_date like '{$Year_a}%' )
		// and a.start_date like '{$this->Y}%' 
		//and check4_sn >'0' "; //已核章
		//$SQL="select a.* from teacher_absent a
		//where a.teacher_sn='{$SN}'  and a.abs_kind='52' ";
		$SQL.=" order by a.start_date ";
		$rs=$this->CONN->Execute($SQL) or die($SQL);
		$arr=$rs->GetArray();
		$this->all=$arr;//return $arr;
		
	//產生連結頁面
	//$this->links= new Chi_Page($this->tol,$this->size,$this->page);
	}
	//分月列印畫面
	function add(){

		foreach ($_POST['pMonth'] as $pMon){ 
			$MM[]=" a.start_date like '{$pMon}%' ";}
			//值12月時,自動加入 12月。
			if (date("m")=='12') {
				$this->add12=date("Y-m");
				$MM[]=" a.start_date like '{$this->add12}%' ";
			}
		$SQL="select a.*,count(c_id) as Num from teacher_absent a
		left join teacher_absent_course b  
		ON a.id=b.a_id and  b.travel='1' 
		where a.teacher_sn='{$this->SN}'  and a.abs_kind='52' 
		and (".join(" or ",$MM) ." )  group by a.id  ";
		$SQL.=" order by a.start_date ";
		$rs=$this->CONN->Execute($SQL) or die($SQL);
		$arr=$rs->GetArray();
		$this->all=$arr;//出差單列表(含未申請領經費)
		$this->Sub=$this->getMon($arr);//取經費單據
		
		$tpl = dirname (__file__)."/templates/teacher_chi_prt.htm";
		$this->smarty->assign("this",$this);
		$this->smarty->assign("SN",$this->SN);
		$this->smarty->display($tpl);
		die();
	}
  //分筆列印畫面
	function add2(){
		//print_r($_POST);die();
		foreach ($_POST['prnID'] as $pID){ 
			$MM[]=(int)$pID;
			}
		if (count($MM)==0) backe('未選擇記錄!!');
		$SQL="select a.*,count(c_id) as Num from teacher_absent a
		left join teacher_absent_course b  
		ON a.id=b.a_id and  b.travel='1' 
		where a.teacher_sn='{$this->SN}'  and a.abs_kind='52' 
		and a.id in (".join(",",$MM)." )  group by a.id  ";
		$SQL.=" order by a.start_date ";
		$rs=$this->CONN->Execute($SQL) or die($SQL);
		$arr=$rs->GetArray();
		$this->all=$arr;//出差單列表(含未申請領經費)
		$this->Sub=$this->getMon($arr);//取經費單據
		
		$tpl = dirname (__file__)."/templates/teacher_chi_prt.htm";
		$this->smarty->assign("this",$this);
		$this->smarty->assign("SN",$this->SN);
		$this->smarty->display($tpl);
		die();
	}

	//取單據
	function getMon($arr){
		foreach($arr as $ary) {
			if ($ary['Num']==0) continue;
			$tmp[]=$ary['id'];
			}
		if (count($tmp)==0) backe('選擇的月份沒有資料');
		$A_ID=join(" , ",$tmp);
		$SQL="select b.* from teacher_absent a,teacher_absent_course b 
		where b.teacher_sn='{$this->SN}'  and b.travel='1' 
		and a.id=b.a_id	and b.a_id IN ($A_ID) ";
		$SQL.=" order by a.start_date ,b.c_id ";
		$rs=$this->CONN->Execute($SQL) or die($SQL);
		return $rs->GetArray();
	}

	//月份選擇
	function SelMonth($name){
		//$M=array();
		$str='';
		$Y=date("Y");//今年
		$Y1=date("Y")-1;//去年
		$M=date("m");
		for($i=10;$i>=1;$i--){
			if ($M <= $i) {$YY=$Y1;$MM=($M+12)-$i;}
			if ($M > $i) {$YY=$Y;$MM=$M-$i;}
			$A=$YY.'-'.sprintf("%02d",$MM);
			$Mon[$A]=$A;
			$str.="<label><input type='checkbox' name='{$name}[]' value='{$A}' />{$A}</label>&nbsp;&nbsp;\n";
			if ($i==6)$str.="<br>";
			}
		//print_r($Mon);
		return $str;
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

/*轉數字為國字*/
function CNum($num) {
	return Num2CNum($num);
}

/*轉數字為金額*/
function MoneyNum($money){
    $ar = array("零", "壹", "貳", "參", "肆", "伍", "陸", "柒", "捌", "玖") ;
    $cName = array("", "", "拾", "佰", "仟", "萬", "拾", "佰", "仟", "億", "拾", "佰", "仟");
    $conver = "";
    $cLast = "" ;
    $cZero = 0;
    $i = 0;
    for ($j = strlen($money) ; $j >=1 ; $j--){  
      $cNum = intval(substr($money, $i, 1));
      $cunit = $cName[$j]; //取出位數
      if ($cNum == 0) { //判斷取出的數字是否為0,如果是0,則記錄共有幾0
         $cZero++;
         if (strpos($cunit,"萬億") >0 && ($cLast == "")){ // '如果取出的是萬,億,則位數以萬億來補
          $cLast = $cunit ;
         }      
      }else {
        if ($cZero > 0) {// '如果取出的數字0有n個,則以零代替所有的0
          if (strpos("萬億", substr($conver, strlen($conver)-2)) >0) {
             $conver .= $cLast; //'如果最後一位不是億,萬,則最後一位補上"億萬"
          }
          $conver .=  "零" ;
          $cZero = 0;
          $cLast = "" ;
        }
         $conver = $conver.$ar[$cNum].$cunit; // '如果取出的數字沒有0,則是中文數字+單位          
      }
      $i++;
    }  
  //'判斷數字的最後一位是否為0,如果最後一位為0,則把萬億補上
     if (strpos("萬億", substr($conver, strlen($conver)-2)) >0) {
       $conver .=$cLast; // '如果最後一位不是億,萬,則最後一位補上"億萬"
    }
    return $conver;	


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
<h1 onclick='window.close();' title='按下後關閉視窗'>$value</h1><br><br>
</center></body></html>";
exit;
}
