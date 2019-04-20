<?php
//$Id: PHP_tmp.html 5310 2009-01-10 07:57:56Z hami $
include "config.php";
include "../../include/sfs_case_studclass.php";
//認證
sfs_check();


//程式使用的Smarty樣本檔


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
// print_menu($school_menu_p);//,$obj->linkstr

$obj->display();
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
	5=>'健康與體育',6=>'藝術與人文',7=>'綜合活動');//,8=>'全部'
	var $linkstr;

	//建構函式
	function basic_chc($CONN,$smarty){
		$this->CONN=&$CONN;
		$this->smarty=&$smarty;
	}
	//初始化
	function init() {
		//過濾字串及決定GET或POST變數
		$Y=gVar('Y');$G=gVar('G');$S=gVar('S');
		
		//學年度格式 92_2,或102_1
		if (preg_match("/^[0-9]{2,3}_[1-2]$/",$Y)) $this->Y=$Y;
		
		//年級格式..1-6小學,7-9國中
		if (preg_match("/^[1-9]$/",$G)) $this->G=$G;

		//領域代碼1-7,8表示全部領域
		if (preg_match("/^[1-7]$/",$S)) $this->S=$S;

		//學年度選單
		$this->sel_year=sel_year('Y',$this->Y);
		//年級選單
		$this->sel_grade=sel_grade('G',$this->G,$_SERVER['PHP_SELF'].'?Y='.$this->Y.'&G=');
		//頁數
		// $this->page=($_GET[page]=='') ? 0:$_GET[page];
		
		//其他分頁連結參數
		$this->linkstr="Y={$this->Y}&G={$this->G}&S={$this->S}";
	
	}
		//程序
	function process() {
	   	//echo "123".$_POST['form_act'];
		//if ($_GET['act']=='update') $this->updateDate();
		if ($_POST['form_act']=='saveData') $this->save();
		if ($_POST['form_act']=='delData') $this->delData();
		if ($_POST['form_act']=='seme_csv') $this->seme_csv();
		$this->all();
	}

	//顯示
	function display(){
		$temp1 = dirname (__file__)."/templates/score_input.htm";
		//$temp2 = dirname (__file__)."/templates/score_list_all.htm";
		//($this->S == "8") ? $tpl=$temp2 : $tpl = $temp1;
		$this->smarty->assign("this",$this);
		$this->smarty->display($temp1);
	}
	//顯示
	  	// 寫入資料表
	function save(){
	  	// echo '<pre>';print_r($_POST);die();
	  	if(count($_POST['score_input'])==0) backe("！！未選擇資料！！");
	  	foreach($_POST['score_input'] as $a=>$sco_test){
		      $data=explode("_",$a);
		      $SN=$data[0];$sco_src=$data[1];
		      $end_score=ceil($sco_src);
		      $sco_test=ceil($sco_test);
		      if ($sco_test > $sco_src )  $end_score=ceil($sco_test);
		      if ($end_score >60 ) $end_score=60;

		     $SQL="UPDATE `chc_mend` SET `score_test` = '$sco_test',
		     `score_end` = '$end_score' WHERE `student_sn` = '$SN' and scope='{$this->S}' and seme='{$this->Y}' LIMIT 1 ;";
		     $rs=$this->CONN->Execute($SQL) or die($SQL);
		    //echo $SQL."<br>";
	      }
		$URL=$_SERVER['SCRIPT_NAME']."?Y=".$this->Y.'&G='.$this->G.'&S='.$this->S;
		Header("Location:$URL");
	}
	//刪除資料
	function delData(){
		//echo '<pre>';print_r($_POST);die();
		if ($this->Y=='' || $this->G=='' || $this->S=='')  backe("！！資料錯誤！！");
		if(count($_POST['st_sn'])==0) backe("！！未選擇資料！！");
		foreach ($_POST['st_sn'] as $id =>$SN){
		   if ($id=='' || $SN=='') backe("！！未選擇資料！！");
			$SQL="DELETE FROM `chc_mend` WHERE id='{$id}' and  `student_sn` = '{$SN}' 
			and scope='{$this->S}' and seme='{$this->Y}' LIMIT 1";
			$rs=$this->CONN->Execute($SQL) or die($SQL);		   
		}
		$URL=$_SERVER['SCRIPT_NAME']."?Y=".$this->Y.'&G='.$this->G.'&S='.$this->S;
		Header("Location:$URL");
	}

	//擷取資料
	function all(){
		if ($this->Y=='') return;
		if ($this->G=='') return;
		if ($this->S=='') return;
		$ys=explode("_",$this->Y);
		$YS=sprintf("%03d",$ys[0]).$ys[1];
		$sel_year=$ys[0];
		$sel_seme=$ys[1];
		$seme_year_seme=sprintf("%03d",$sel_year).$sel_seme;
		$seme_class=$this->G."%";
		$Scope=$this->S;
				
		$curr_y=curr_year();
		$sel_y=substr($this->Y,0,-2);
		$sel_g=$this->G;
		$op=$curr_y-$sel_y+$sel_g."%";
		$SQL="select a.stud_id,a.stud_name,a.stud_sex,b.seme_class,b.seme_num,b.seme_year_seme,c.*
		from stud_base a,chc_mend c
		LEFT JOIN stud_seme b on (c.student_sn=b.student_sn  
		and b.seme_year_seme='$seme_year_seme'  
		and b.seme_class like '$seme_class')
		where a.student_sn=c.student_sn		
		and c.seme='$this->Y'		
		and a.curr_class_num LIKE '$op'
		and c.scope='$Scope'
		and a.stud_study_cond=0
		order by b.seme_class,b.seme_num
		";
//echo $SQL;
		$rs=$this->CONN->Execute($SQL);
		$this->stu=$rs->GetArray();
		 foreach($this->stu as $a=>$b){ 
		   $this->stu[$a][score_src]=ceil($this->stu[$a][score_src]);
		 }
		
	}

	function seme_csv(){
		$ys=explode("_",$this->Y);
		$YS=sprintf("%03d",$ys[0]).$ys[1];
		$sel_year=$ys[0];
		$sel_seme=$ys[1];
		$seme_year_seme=sprintf("%03d",$sel_year).$sel_seme;
	   	$SQL="SELECT b.stud_name,a.scope ,a.score_src,b.stud_id,b.student_sn,c.seme_class,c.seme_class,c.seme_num
	   	FROM chc_mend a, stud_base b 
	   	left join stud_seme c on (c.student_sn=b.student_sn and c.seme_year_seme='$seme_year_seme')
	   	where  a.student_sn=b.student_sn  
	   	and a.seme='$this->Y'
	   	and b.stud_study_cond=0
	   	order by c.seme_class,c.seme_num";
	   	//echo $SQL;
	   	$rs=$this->CONN->Execute($SQL);
		$stu=$rs->GetArray();	
		$data = "班級,座號,學號,姓名,語文,數學,自然,社會,健體,藝文,綜合\r\n";
		foreach($stu as $a=>$b){   
		      $class_id =sprintf("%03d","101")."_"."1"."_".sprintf("%02d",substr($b[seme_class],0,1))."_".substr($b[seme_class],1,2);
		      $stud_score[$b[student_sn]][0]=class_id_to_full_class_name($class_id);
		      $stud_score[$b[student_sn]][1]=$b[seme_num];
		      $stud_score[$b[student_sn]][2]=$b[stud_id];
		      $stud_score[$b[student_sn]][3]=$b[stud_name];
		      for($i=4;$i<=10;$i++){
		      	if($b[scope] ==$i-3){
//			       $stud_score[$b[student_sn]][$i]="補考";//$b[score_src];
			       if (ceil($b[score_src])<60) {$stud_score[$b[student_sn]][$i]="補考";}		 
			    }else if($stud_score[$b[student_sn]][$i]!=""){
			       $stud_score[$b[student_sn]][$i]=$stud_score[$b[student_sn]][$i];
			    }else{
			       $stud_score[$b[student_sn]][$i]=""; 
			    }
	          }
		}
		 foreach($stud_score as $a=>$b){
		       $data.=join(",",$b)."\r\n";
	     }
		$filename=$_REQUEST['Y']."學期補考成績.csv";
		header("Content-disposition: attachment;filename=$filename");
		header("Content-type: text/x-csv ; Charset=Big5");
		header("Pragma: no-cache");
		header("Expires: 0");
		echo $data;
		die();

	   
	}
}


