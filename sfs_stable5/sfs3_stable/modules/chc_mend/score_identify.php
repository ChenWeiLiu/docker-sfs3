<?php
//$Id: PHP_tmp.html 5310 2009-01-10 07:57:56Z hami $
include "config.php";
//認證
sfs_check();


//程式使用的Smarty樣本檔
$template_file = dirname (__file__)."/templates/score_identify.htm";

//建立物件
$obj= new basic_chc($CONN,$smarty);
//初始化
$obj->init();
//處理程序,有時程序內有header指令,故本程序宜於head之前
$obj->process();


//秀出網頁布景標頭
head("補考成績證明");

//顯示SFS連結選單(欲使用請拿開註解)
echo make_menu($school_menu_p,$obj->linkstr);

//顯示內容
$obj->display($template_file);
//佈景結尾
foot();


//物件class
class basic_chc{
	var $CONN;//adodb物件
	var $smarty;//smarty物件
	var $scope=array(1=>'語文',2=>'數學',3=>'自然與生活科技',4=>'社會',5=>'健康與體育',6=>'藝術與人文',7=>'綜合活動');

	//建構函式
	function basic_chc($CONN,$smarty){
		$this->CONN=&$CONN;
		$this->smarty=&$smarty;
	}
	//初始化
	function init() {
		//過濾字串及決定GET或POST變數
		$Y=gVar('Y');$G=gVar('G');
		
		//學年度格式 92_2,或102_1
		if (preg_match("/^[0-9]{2,3}_[1-2]$/",$Y)) $this->Y=$Y;
		
		//年級格式..1-6小學,7-9國中
		if (preg_match("/^[1-9]$/",$G)) $this->G=$G;
		
		//$this->Y=strip_tags($_GET['Y']);
		//$this->G=strip_tags($_GET['G']);
		
		$this->sel_year=sel_year('Y',$this->Y);
		$this->sel_grade=sel_grade('G',$this->G,$_SERVER['PHP_SELF'].'?Y='.$this->Y.'&G=');
		$this->print_all_class_this_seme = (!empty($this->Y))?"1":"";
		$this->print_this_class_this_seme = (!empty($this->G))?"1":"";

		//其他分頁連結參數
		$this->linkstr="Y={$this->Y}&G={$this->G}&S={$this->S}";
	}
	//程序
	function process() {
		$this->all();
	}

	//顯示
	function display($tpl){
		$this->smarty->assign("this",$this);
		$this->smarty->display($tpl);
	}
	//擷取資料
	function all(){
		if ($this->Y=='') return;
		if ($this->G=='') return;
		$ys=explode("_",$this->Y);
		$sel_year=$ys[0];
		$sel_seme=$ys[1];
		$seme_year_seme=sprintf("%03d",$sel_year).$sel_seme;
		//$seme_class=$this->G."%";
		$query="select a.student_sn,b.stud_id,b.stud_name,b.stud_sex,c.seme_class,c.seme_num,c.seme_year_seme
		from (chc_mend a left join stud_base b on a.student_sn=b.student_sn)left join stud_seme c 
		on b.student_sn=c.student_sn and c.seme_year_seme='{$seme_year_seme}' 
		where a.seme='{$this->Y}' 
		group by a.student_sn
		order by c.seme_class,c.seme_num
		";
/*
		$query="select a.student_sn,a.stud_id,a.stud_name,a.stud_sex,b.seme_class,b.seme_num,b.seme_year_seme,c.student_sn
		from stud_base a,stud_seme b,chc_mend c
		where a.student_sn=c.student_sn
		and c.student_sn=b.student_sn
		and b.seme_year_seme='$seme_year_seme'
		and b.seme_class like '$seme_class'
		group by c.student_sn
		order by b.seme_class,b.seme_num,c.seme
		";
*/
		$res=$this->CONN->Execute($query);
		
		//取出班級名稱陣列
		$class_base=class_base($seme_year_seme);
		
		$sel_Y_G = substr($this->Y,0,3)-$this->G;//取指定學期指定年級學生的班級資料
		
		while(!$res->EOF) {
			$query2="select seme_year_seme,seme_class from stud_seme where student_sn ='{$res->fields[student_sn]}'";
			$rec2=$this->CONN->Execute($query2);
			list($seme_year_seme2,$seme_class2)=$rec2->FetchRow();
			//取指定年級，從某一學年和該年級的關係看出
			if(substr($seme_year_seme2,0,3)-substr($seme_class2,0,1)==$sel_Y_G){
				
				$this->stu_data[]=array(
				"stud_id"=>$res->fields[stud_id],
				"stud_name"=>trim(str_replace("　","",$res->fields[stud_name])),
				"stud_sex"=>$res->fields[stud_sex],
				"seme_class"=>$class_base{$res->fields[seme_class]},
				"seme_num"=>$res->fields[seme_num],
				"student_sn"=>$res->fields[student_sn]
				);
				$this->students_sn .= "&students_sn[]=".$res->fields[student_sn];
			}
			
			
			$res->MoveNext();
		}
	}








}


