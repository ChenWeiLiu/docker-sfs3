<?php
//$Id:  $
include "config.php";
//認證
sfs_check();

//引入換頁物件(學務系統用法)
// include_once "../../include/chi_page2.php";
//程式使用的Smarty樣本檔
$template_file = dirname (__file__)."/templates/fix_score_course.htm";

//建立物件
$obj= new score_ss($CONN,$smarty);
//初始化
$obj->init();
//處理程序,有時程序內有header指令,故本程序宜於head("score_ss模組");之前
$obj->process();

//秀出網頁布景標頭
head("課表資料編修");

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
   var $YS='YS';//下拉式選單學期的奱數名稱
   var $year_seme;//下拉式選單班級的奱數值
   var $Sclass='class_id';//下拉式選單班級的奱數名稱
   var $grade_name='Grade';//下拉式選單年級的奱數名稱
   var $Teach;//全部教師
   var $YesNo=array('0'=>'否','1'=>'是');
   var $Grade;

	//建構函式
	function score_ss($CONN,$smarty){
		$this->CONN=&$CONN;
		$this->smarty=&$smarty;
      global $IS_JHORES;
      $this->IS_JHORES=$IS_JHORES;
      ($_GET[$this->YS]=='') ? $this->year_seme=curr_year()."_".curr_seme():$this->year_seme=$_GET[$this->YS];
      if ($_GET['Tsn']!='') $this->Tsn=(int)$_GET['Tsn'];
      $aa=split("_",$this->year_seme);
      $this->year=$aa[0];
      $this->seme=$aa[1];
	}
	//初始化
	function init() {$this->page=($_GET[page]=='') ? 0:$_GET[page];}
	//程序
	function process() {
		//http://localhost/sfs3/modules/fix_tool/fix_print2.php?year_seme=98_2&Grade=7
		// fix_score_course.php?YS=103_1&Tsn=52
		if($_GET['act']=='del') $this->del();
		if($_GET['act']=='delall') $this->delall();
		if($_POST['form_act']=='update') $this->update();
		$this->all();
		$this->Teach=$this->getTeach();//教師資料陣列

	}
	//更新課表
	function update(){
		//print_r($_POST);die();
		$class_year=(int)$_POST['class_year'];
		$class_name =(int)$_POST['class_name'];
		$day=(int)$_POST['day'];
		$sector=(int)$_POST['sector'];
		$ss_id=(int)$_POST['ss_id'];
		$cooperate_sn=(int)$_POST['cooperate_sn'];
		$room=strip_tags($_POST['room']);
		$c_kind=(int)$_POST['c_kind'];
		$course_id=(int)$_POST['course_id'];
		$teacher_sn=(int)$_POST['teacher_sn'];
		$YS=strip_tags($_POST[$this->YS]);
		if ($YS=='') return ;
		if ($course_id=='' || $course_id=='0') return ;
		if ($teacher_sn=='' || $teacher_sn=='0') return ;
		if ($ss_id=='' || $ss_id=='0') return ;
		$SQL = "update score_course set teacher_sn='{$teacher_sn}',cooperate_sn='{$cooperate_sn}',
		class_year='{$class_year}',class_name='{$class_name}',day='{$day}',sector='{$sector}',
		ss_id='{$ss_id}',room='{$room}',c_kind='{$c_kind}' where course_id = '{$course_id}'";
		//die($SQL);
		$rs=$this->CONN->Execute($SQL) or die($SQL);
		$URL=$_SERVER['PHP_SELF']."?YS=".$YS."&Tsn=".$teacher_sn;
		Header("Location:$URL");
		
	}

	//顯示
	function display($tpl){
		$this->smarty->assign("this",$this);
		$this->smarty->display($tpl);
		//echo "<pre>";print_r($this->Course);
	}
	//擷取資料
	function all(){

		if ($this->year=='') return ;
		if ($this->seme=='') return ;
		if ($this->Tsn=='') return ;
		//所有課程設定
		$SQL="select * from  score_course  where year='{$this->year}' 	and  semester='{$this->seme}' and teacher_sn='{$this->Tsn}' order by class_year,class_name,day,sector 	  ";
		$rs=$this->CONN->Execute($SQL) or die($SQL);
		$arr=$rs->GetArray();
		$this->all=$arr;//return $arr;
		$this->tol=count($arr);
		//return ;
		
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
	}
	function SsidToName(){
		//所有課程設定
		$SQL="select * from score_ss where year='{$this->year}' 	and  semester='{$this->seme}' and enable='1'  order by class_year ";
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

	function delall(){
		if ($this->Tsn=='' || $this->Tsn==0) return ;
		//所有課程設定
		$SQL="delete  from  score_course  where year='{$this->year}' and  semester='{$this->seme}'  and   	teacher_sn='{$this->Tsn}' ";
		$rs=$this->CONN->Execute($SQL) or die($SQL);
//echo $SQL;
		$URL=$_SERVER[PHP_SELF]."?YS=".$this->year_seme;
		Header("Location:$URL");
	}
	function del(){
		$id=(int)$_GET['id'];
		if ($id==0 || $id=='') die("無法查詢，語法:".$SQL);
		//所有課程設定
		$SQL="delete  from  score_course  where year='{$this->year}' and  semester='{$this->seme}'  and course_id='$id' ";
		$rs=$this->CONN->Execute($SQL) or die($SQL);
//echo $SQL;
		$URL=$_SERVER[PHP_SELF]."?YS=".$this->year_seme."&Tsn=".$_GET['Tsn'];
		Header("Location:$URL");
	}



##################  學期下拉式選單函式  ##########################
function select($show_class=1) {
    $SQL = "select * from school_day where  day<=now() and day_kind='start' order by day desc ";
    $rs=$this->CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
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
    return $str;
}

/*擷取教師名冊及任教節數*/
function getTeach() {
		if ($this->year=='') return ;
		if ($this->seme=='') return ;
		$SQL="select  a.teacher_sn,a.name,a.sex ,count(b.course_id) as tol from teacher_base a, score_course b where a.teacher_sn=b.teacher_sn and b.year='{$this->year}'  and b.semester='{$this->seme}' group by b.teacher_sn order by  hex(left(a.name,2)),a.sex desc  ";
		$rs=$this->CONN->Execute($SQL) or die($SQL);
		$arr=$rs->GetArray();	
		foreach($arr as $ary) {
		$K=$ary['teacher_sn'];
		$T[$K]=$ary;
		$this->TeaName[$K]=$ary['name']."($K)";
		}
	return $T;
	//print_r($this->Teach);
}
/*回傳單一教師姓名*/
function getTeaOne($sn) {
	return $this->Teach[$sn]['name'];
}

##################  學期下拉式選單函式  ##########################
function select_tea($select) {
		if ($this->year=='') return ;
		if ($this->seme=='') return ;
		//if ($this->Teach=='') return ;

		//echo "<pre>";print_r($arr);
		$str="<select name='".$this->YS."' onChange=\"location.href='".$_SERVER[PHP_SELF]."?YS=".$this->year_seme."&Tsn='+this.options[this.selectedIndex].value;\">\n";
		$str.="<option value=''>-未選擇-</option>\n";

	foreach($this->Teach as $key=>$ary) {
		//$key=$ary['teacher_sn'];
		//$T[$key]=$ary['name'];
		if ($ary['sex']=='1') {$SS=' class=blue ';$val=$ary['name']."(".$ary['tol'].")";}
		if ($ary['sex']=='2') {$SS=' class=red ';$val=$ary['name']."(".$ary['tol'].")";}
		($key==$this->Tsn) ? $bb=' selected':$bb='';
		$str.= "<option value='$key' $bb $SS>$val - $key </option>\n";
        }
    $str.="</select>";

    return $str;
}






} 
