<?php
//$Id:  $
include "config.php";
//認證
sfs_check();

//引入換頁物件(學務系統用法)
include_once "../../include/chi_page2.php";
//程式使用的Smarty樣本檔
$template_file = dirname (__file__)."/templates/fix_print2.htm";

//建立物件
$obj= new score_ss($CONN,$smarty);
//初始化
$obj->init();
//處理程序,有時程序內有header指令,故本程序宜於head("score_ss模組");之前
$obj->process();

//秀出網頁布景標頭
head("課程分析");

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
	var $size=10;//每頁筆數
	var $page;//目前頁數
	var $tol;//資料總筆數
   var $IS_JHORES;
	var $year;
   var $seme;
   var $YS='year_seme';//下拉式選單學期的奱數名稱
   var $year_seme;//下拉式選單班級的奱數值
   var $Sclass='class_id';//下拉式選單班級的奱數名稱
   var $grade_name='Grade';//下拉式選單年級的奱數名稱
   var $Grade;

	//建構函式
	function score_ss($CONN,$smarty){
		$this->CONN=&$CONN;
		$this->smarty=&$smarty;
      global $IS_JHORES;
      $this->IS_JHORES=$IS_JHORES;
      ($_GET[$this->YS]=='') ? $this->year_seme=curr_year()."_".curr_seme():$this->year_seme=$_GET[$this->YS];
      if ($_GET[$this->grade_name]!='') $this->Grade=(int)$_GET[$this->grade_name];
      $aa=split("_",$this->year_seme);
      $this->year=$aa[0];
      $this->seme=$aa[1];
	}
	//初始化
	function init() {$this->page=($_GET[page]=='') ? 0:$_GET[page];}
	//程序
	function process() {
		//http://localhost/sfs3/modules/fix_tool/fix_print2.php?year_seme=98_2&Grade=7
		$this->all();
	}
	//顯示
	function display($tpl){
		$this->smarty->assign("this",$this);
		$this->smarty->display($tpl);
		//echo "<pre>";print_r($this->Course);
	}
	//擷取資料
	function all(){
		$SQL="select year,semester,count(*) as tol from score_ss 
		group by year,semester ";
		$rs=$this->CONN->Execute($SQL) or die($SQL);
		$this->aTol=$rs->GetArray();

		if ($this->year=='') return ;
		if ($this->seme=='') return ;
		if ($this->Grade=='') return ;
		//所有課程設定
		$SQL="select * from score_ss where year='{$this->year}' 	and  semester='{$this->seme}' and class_year='{$this->Grade}'  ";
		$rs=$this->CONN->Execute($SQL) or die($SQL);
		$arr=$rs->GetArray();
		$this->all=$arr;//return $arr;
		$this->tol=count($arr);
		/*取班級*/
    	$SQL="select class_id,c_year,c_name,c_sort ,teacher_1 from  school_class where year='{$this->year}'
    	 and semester='{$this->seme}' and c_year='{$this->Grade}' and enable=1  order by c_sort, class_id  ";
	    $rs=&$this->CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
	    if ($rs->RecordCount()==0) return"尚未設定班級資料！";
	    $this->aGrade=$rs->GetArray();
		/*取課程中文名稱*/
  		$SQL="select subject_id,subject_name from score_subject ";
		$rs=$this->CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
		if ($rs->RecordCount()==0) return "尚未設定任何科目資料！";
		$obj=$rs->GetArray();
		foreach($obj as $ary){
			$id=$ary[subject_id];
			$this->Subj[$id]=$ary[subject_name];
		}
		$this->ScoTol();
		$this->Course=$this->Course();
		//echo "<pre>";print_r($this->Course);
		$this->SsidToName=$this->SsidToName();
	}
	function SsidToName(){
		foreach ($this->all as $ary){
			$id=$ary[ss_id];
			$scope=$ary[scope_id];
			$subject=$ary[subject_id];
			$AA[$id]=$this->Subj[$scope].':'.$this->Subj[$subject];
		}	
	
	return $AA;
	
	}





	//新增
	function class_ss($classid=''){
		if ($this->all=='' || $this->seme=='' || $this->Grade=='') return ;
		$AA='';
		foreach ($this->all as $ary){
		 	$ss_id=$ary[ss_id];
			if ($ary[class_id]==$classid && $classid!='') $AA[$ss_id]=$ary;
			if ($classid=='' && $ary[class_id]=='') $AA[$ss_id]=$ary;
		}
		return $AA;
	}

	//取所有成績統計 by ss_id
	function ScoTol(){
		
		$TB='score_semester_'.$this->year.'_'.$this->seme;
  		$SQL="SELECT class_id ,ss_id,count(*)  as  stol  FROM  {$TB}  group  by class_id,ss_id ";
		$rs=$this->CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
		if ($rs->RecordCount()==0) return "尚未設定任何科目資料！";
		$obj=$rs->GetArray();
		foreach ($obj as $ary){
			$cla=$ary[class_id];
			$ssid=$ary[ss_id];		
			$this->ScoTol[$cla][$ssid]=$ary[stol];
			$this->ScoTol2[$ssid]=$this->ScoTol2[$ssid]+$ary[stol];
		}
	
	
	}
function gScoTol($cla,$id){
	if ($cla=='') return $this->ScoTol2[$id];
	return $this->ScoTol[$cla][$id];
}

function Course(){
 		$SQL="SELECT class_id ,ss_id,count(*)  as  stol  FROM  score_course 	where year ='{$this->year}' and  semester='{$this->seme}' 	and class_year='{$this->Grade}' 	 group  by class_id,ss_id ";
		$rs=$this->CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
		$all=$rs->GetArray();
		foreach ($all as $ary){
			$cla=$ary[class_id];
			$ss_id=$ary[ss_id];
			$AA[$cla][$ss_id]=$ary[stol];
		}
		return  $AA;
}
  
function gCourse($cla){
//print_r($this->Course[$cla]);
	return $this->Course[$cla];
}

##################  學期下拉式選單函式  ##########################
function select($show_class=1) {
    $SQL = "select * from school_day where  day<=now() and day_kind='start' order by day desc ";
    $rs=&$this->CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
    while(!$rs->EOF){
        $ro = $rs->FetchNextObject(false);
        // 亦可$ro=$rs->FetchNextObj()，但是僅用於新版本的adodb，目前的SFS3不適用
        $year_seme=$ro->year."_".$ro->seme;
        $obj_stu[$year_seme]=$ro->year."學年度第".$ro->seme."學期";
    }
    $str="<select name='".$this->YS."' onChange=\"location.href='".$_SERVER[PHP_SELF]."?".$this->YS."='+this.options[this.selectedIndex].value;\">\n";
        //$str.="<option value=''>-未選擇-</option>\n";
    foreach($obj_stu as $key=>$val) {
        ($key==$this->year_seme) ? $bb=' selected':$bb='';
        $str.= "<option value='$key' $bb>$val</option>\n";
        }
    $str.="</select>";
    ($show_class==1) ? $str.=$this->grade():$str.=$this->only_grade();
    return $str;
}
##################陣列列示函式2##########################
function grade() {
    //名稱,起始值,結束值,選擇值
    $url="?".$this->YS."=". $this->year_seme."&".$this->Sclass."=";
    ($this->IS_JHORES==6) ? $grade=array(7=>"一年",8=>"二年",9=>"三年"):$grade=array(1=>"一年",2=>"二年",3=>"三年",4=>"四年",5=>"五年",6=>"六年");

    $SQL="select class_id,c_year,c_name,teacher_1 from  school_class where year='".$this->year."' and semester='".$this->seme."' and enable=1  order by class_id  ";
    $rs=&$this->CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
    if ($rs->RecordCount()==0) return"尚未設定班級資料！";
    $All=$rs->GetArray();
    $str="<select name='".$this->Sclass."' onChange=\"location.href='".$url."'+this.options[this.selectedIndex].value;\">\n";
    $str.= "<option value=''>-未選擇-</option>\n";
    foreach($All as $ary) {
        ($ary[class_id]==$_GET[$this->Sclass]) ? $bb=' selected':$bb='';
        $str.= "<option value='".$ary[class_id]."' $bb>".$grade[$ary[c_year]].$ary[c_name]."班 (".$ary[teacher_1].")</option>\n";
        }
    $str.="</select>";
    return $str;
    }
    
##################陣列列示函式2##########################
function only_grade() {
    //名稱,起始值,結束值,選擇值
    $url="?".$this->YS."=". $this->year_seme."&".$this->grade_name."=";
    ($this->IS_JHORES==6) ? $grade=array(7=>"一年",8=>"二年",9=>"三年"):$grade=array(1=>"一年",2=>"二年",3=>"三年",4=>"四年",5=>"五年",6=>"六年");

    $str="<select name='".$this->grade_name."' onChange=\"location.href='".$url."'+this.options[this.selectedIndex].value;\">\n";
    $str.= "<option value=''>-未選擇-</option>\n";
    foreach($grade as $Key=>$ary) {
        ($Key==$_GET[$this->grade_name]) ? $bb=' selected':$bb='';
        $str.= "<option value='".$Key."' $bb>".$grade[$Key]."級</option>\n";
        }
    $str.="</select>";
    return $str;
    }

} 