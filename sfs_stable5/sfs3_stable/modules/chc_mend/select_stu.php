<?php
//$Id: PHP_tmp.html 5310 2009-01-10 07:57:56Z hami $
include "config.php";

//認證
sfs_check();


//程式使用的Smarty樣本檔
$template_file = dirname (__file__)."/templates/select_stu.htm";

//建立物件
$obj= new basic_chc($CONN,$smarty);
//初始化
$obj->init();
//處理程序,有時程序內有header指令,故本程序宜於head("12basic_chc模組");之前
$obj->process();


//秀出網頁布景標頭
head("補考成績管理");

//顯示SFS連結選單(欲使用請拿開註解)

echo make_menu($school_menu_p,$obj->linkstr);//
//顯示內容
$obj->display($template_file);
//佈景結尾
foot();


//物件class
class basic_chc{
	var $CONN;//adodb物件
	var $smarty;//smarty物件
	var $size=10;//每頁筆數
	var $page;//目前頁數
	var $tol;//資料總筆數
	var $scope=array(1=>'語文',2=>'數學',3=>'自然與生活科技',4=>'社會',
	5=>'健康與體育',6=>'藝術與人文',7=>'綜合活動');
	var $linkstr;//連結傳遞

	//建構函式
	function basic_chc($CONN,$smarty){
		$this->CONN=&$CONN;
		$this->smarty=&$smarty;
	}
	//初始化
	function init() {
		if (preg_match("/^[0-9]{2,3}_[1-2]$/",$_GET['Y'])) $this->Y=strip_tags($_GET['Y']);
		if (preg_match("/^[1-9]$/",$_GET['G'])) $this->G=strip_tags($_GET['G']);
		if (preg_match("/^[1-7]$/",$_GET['S'])) $this->S=strip_tags($_GET['S']);
		$this->Scope_name=$this->scope[$this->S];
		$this->sel_year=sel_year('Y',$this->Y);
		$this->sel_grade=sel_grade('G',$this->G,$_SERVER['PHP_SELF'].'?Y='.$this->Y.'&G=');
		$this->linkstr="Y={$this->Y}&G={$this->G}&S={$this->S}";
	}
	//程序
	function process() {
	   
		if ($_POST['form_act']=='saveData') $this->save();
		$this->all();
	}
	//顯示
	function display($tpl){
		$this->smarty->assign("this",$this);
		$this->smarty->display($tpl);
	}
	//顯示
	function save(){
	  	// 寫入資料表
	  	if(count($_POST['sel'])==0) backe("！！未選擇資料！！");
		//print_r($_POST['sel']);
		foreach($_POST['sel'] as $a=>$b){
		$sel=explode("_n_",$b);
		$datetime = date ("Y-m-d H:i:s"); 
		$SQL="INSERT INTO `chc_mend` 
		(`student_sn`,`seme`,`scope`,`score_src`,`cr_time`) 
		VALUES ('".$sel[0]."','".$_POST['Y']."','".$_POST['S']."','".$sel[1]."','$datetime');";
		$rs=$this->CONN->Execute($SQL);// or die($SQL);
		//echo $SQL."<br>";
		echo "";
		}
		$URL=$_SERVER['SCRIPT_NAME']."?Y=".$_POST['Y'].'&G='.$_POST['G'].'&S='.$_POST['S'];
		//echo $URL;
		Header("Location:$URL");
	}
	
	//擷取資料
	function all(){
	  	if ($this->Y=='') return;
		if ($this->G=='') return;
		if ($this->S=='') return;
		$ys=explode("_",$this->Y);
		$YS=sprintf("%03d",$ys[0]).$ys[1];
		$now_YS=curr_year().curr_seme();
		$N=$this->scope[$this->S];		
		$Scope=" and link_ss like '$N%' ";
		//取出該學年該領域之序號與比重
		$SQL="SELECT a.ss_id, a.scope_id, a.subject_id, a.rate, a.link_ss, b.subject_name
		FROM score_ss a
		LEFT JOIN score_subject b ON ( 
		IF (
		a.subject_id =0, a.scope_id, a.subject_id
		) = b.subject_id ) 
		WHERE a.year =  '$ys[0]' 
		AND a.semester =  '$ys[1]' 
		AND a.class_year =  '$this->G' 
		AND a.enable =  '1' 
		AND a.need_exam = '1' 
		$Scope 
		ORDER BY a.ss_id";
		//echo $SQL;
		$rs=$this->CONN->Execute($SQL) or die($SQL);
		$All=$rs->GetArray();
		//echo "<pre>";
		//print_r($All);
		foreach ($All as $ary){
			$ss_id[]=$ary[ss_id];
			$ss_rate[$ary[ss_id]]=$ary[rate];
			$this->link_ss[$ary[ss_id]]=$ary[subject_name]."*".$ary[rate];
		}
		//print_r($this->link_ss)."<br>";
		$str="'".join("','",$ss_id)."'";
		$SQL="SELECT a.student_sn,a.ss_id , group_concat(a.ss_id, '-', a.ss_score) as score, MIN( a.ss_score ) AS s, b.stud_name, c.seme_class, c.seme_num, b.stud_id 
		FROM stud_seme_score a, stud_base b 
		LEFT JOIN stud_seme c ON ( c.student_sn = b.student_sn AND c.seme_year_seme = '$YS' ) 
		WHERE a.seme_year_seme = '$YS' 
		AND a.ss_id IN ( $str) 
		AND a.student_sn = b.student_sn 
		AND b.stud_study_cond = '0' 
		GROUP BY a.student_sn 
		HAVING s <60 
		ORDER BY c.seme_class, c.seme_num";
		$rs=$this->CONN->Execute($SQL) or die($SQL);
		//echo $SQL."<br>";
		$All=$rs->GetArray();
		//echo "<pre>";print_r($All);
		foreach ($All as $ary){
		$stu_sn[$ary['student_sn']]['student_sn']=$ary['student_sn'];
		$stu_sn[$ary['student_sn']]['seme_class']=$ary['seme_class'];
		$stu_sn[$ary['student_sn']]['stud_name']=$ary['stud_name'];
		$stu_sn[$ary['student_sn']]['seme_num']=$ary['seme_num'];
		$stu_sn[$ary['student_sn']]['stud_id']=$ary['stud_id'];
		$stu_sn[$ary['student_sn']][$ary['ss_id']]=$ary['ss_score'];
		$sn=$ary['student_sn'];
		$avarage=0;
		$rate=0;
		$score1=explode(",",$ary['score']);
		//解碼各科成績
		foreach($score1 as $b){
		$score2=explode("-",$b);
		$stu_sn[$ary['student_sn']][$score2[0]]=$score2[1];
		$avarage=$avarage+$ss_rate[$score2[0]]*$score2[1];
		$rate+=$ss_rate[$score2[0]];
	           	}	
	           	$stu_sn[$ary['student_sn']]['average']=ceil($avarage/$rate);
		}
		$this->all_ary=$stu_sn;
		//print_r($stu_sn);
	}	


}


