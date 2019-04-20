<?php
//$Id: PHP_tmp.html 5310 2009-01-10 07:57:56Z hami $
include "config.php";
//認證
sfs_check();


//程式使用的Smarty樣本檔


//建立物件
$obj= new basic_chc($CONN,$smarty);
//初始化
//$obj->init();
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
	5=>'健康與體育',6=>'藝術與人文',7=>'綜合活動',8=>'全部領域');
	var $scope2=array(1=>'語文',2=>'數學',3=>'自然與生活科技',4=>'社會',
	5=>'健康與體育',6=>'藝術與人文',7=>'綜合活動');
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
		if (preg_match("/^[1-8]$/",$S)) $this->S=$S;
		
		//學年度選單
		$this->sel_year=sel_year('Y',$this->Y);
		//年級選單
		$this->sel_grade=sel_grade('G',$this->G,$_SERVER['PHP_SELF'].'?Y='.$this->Y.'&G=');
		//頁數
		//$this->page=($_GET[page]=='') ? 0:$_GET[page];
		//其他分頁連結參數
		$this->linkstr="Y={$this->Y}&G={$this->G}&S={$this->S}";
	
	}
		//程序
	function process() {
		//if ($_GET['act']=='update') $this->updateDate();
		$this->init();
		$this->all();
	}

	//顯示
	function display(){
		$temp1 = dirname (__file__)."/templates/score_list.htm";
		$temp2 = dirname (__file__)."/templates/score_list_all.htm";
		($this->S == "8") ? $tpl=$temp2 : $tpl = $temp1;
		$this->smarty->assign("this",$this);
		$this->smarty->display($tpl);
	}
	//擷取資料
	function all(){
		if ($this->Y=='') return;
		if ($this->G=='') return;
		if ($this->S=='') return;
		$ys=explode("_",$this->Y);
		$sel_year=$ys[0];
		$sel_seme=$ys[1];
		$seme_year_seme=sprintf("%03d",$sel_year).$sel_seme;
		$seme_class=$this->G."%";
		$Scope=$this->S;
		$SQL2="and c.scope='$Scope'";
		($Scope!="8") ? $ADD_SQL=$SQL2:$ADD_SQL='';
		/*$query="select a.stud_id,a.stud_name,a.stud_sex,b.seme_class,b.seme_num,b.seme_year_seme,c.*
		from stud_base a,stud_seme b,chc_mend c
		where a.student_sn=c.student_sn
		and c.student_sn=b.student_sn
		and c.seme='$this->Y'
		and b.seme_year_seme='$seme_year_seme'
		and b.seme_class like '$seme_class'
		$ADD_SQL
		order by b.seme_class,b.seme_num
		";
		*/
		//增加判斷是否為今年再籍學生，如此轉學生也會顯現
		//本學年度-選擇學年+選擇年級=目前年級，才被選取。
		//但是如此一來又會產生畢業生無法被選取的另一個問題
		$curr_y=curr_year();
		$sel_y=substr($this->Y,0,-2);
		$sel_g=$this->G;
		$op=$curr_y-$sel_y+$sel_g."%";
		
		$query="select a.stud_id,a.stud_name,a.stud_sex,
		b.seme_class,b.seme_num,b.seme_year_seme,c.* 
		from stud_base a,chc_mend c left join stud_seme b 
		on (c.student_sn=b.student_sn  
		and b.seme_year_seme='$seme_year_seme'  
		and b.seme_class like '$seme_class' )
		where a.student_sn=c.student_sn 
		and c.seme='$this->Y' 
		and a.stud_study_cond=0
		and a.curr_class_num LIKE '$op' 
		$ADD_SQL
		order by b.seme_class,b.seme_num ";
		//echo $query;
		$res=$this->CONN->Execute($query);
		$ALL=$res->GetArray();
		if ($Scope=="8") {
			$New=array();
			foreach ($ALL as $ary){
				$sn=$ary['student_sn'];
				$ss=$ary['scope'];
				$New['A'][$sn]=$ary;
				$New['B'][$sn][$ss]=$ary;
				//CSV 用$all_student_sn
				$all_student_sn[]=$sn;
				//CSV 結束			
				}
			$this->stu_data=$New;			
		//CSV 開始            
        $all_student_sn_unique=array_unique($all_student_sn);
		if ($_REQUEST['op']=="CSV") {
		$student_sex = array(1=>"男",2=>"女");
	    $this->stu_data=$New;	 
		//$CSV_data 為CSV輸出檔案
		$CSV_data = "學號,班級,座號,姓名,性別,語文原始,語文補考,語文採計,數學原始,數學補考,數學採計,自然原始,自然補考,自然採計,社會原始,社會補考,社會採計,健體原始,健體補考,健體採計,藝術原始,藝術補考,藝術採計,綜合原始,綜合補考,綜合採計\r\n";
		foreach ($all_student_sn_unique as $value_sn) {	
		      $CSV_data .="{$this->stu_data['A'][$value_sn]['stud_id']},{$this->stu_data['A'][$value_sn]['seme_class']},{$this->stu_data['A'][$value_sn]['seme_num']},{$this->stu_data['A'][$value_sn]['stud_name']},{$student_sex[$this->stu_data['A'][$value_sn]['stud_sex']]},{$this->stu_data['B'][$value_sn][1][score_src]},{$this->stu_data['B'][$value_sn][1][score_test]},{$this->stu_data['B'][$value_sn][1][score_end]},{$this->stu_data['B'][$value_sn][2][score_src]},{$this->stu_data['B'][$value_sn][2][score_test]},{$this->stu_data['B'][$value_sn][2][score_end]},{$this->stu_data['B'][$value_sn][3][score_src]},{$this->stu_data['B'][$value_sn][3][score_test]},{$this->stu_data['B'][$value_sn][3][score_end]},{$this->stu_data['B'][$value_sn][4][score_src]},{$this->stu_data['B'][$value_sn][4][score_test]},{$this->stu_data['B'][$value_sn][4][score_end]},{$this->stu_data['B'][$value_sn][5][score_src]},{$this->stu_data['B'][$value_sn][5][score_test]},{$this->stu_data['B'][$value_sn][5][score_end]},{$this->stu_data['B'][$value_sn][6][score_src]},{$this->stu_data['B'][$value_sn][6][score_test]},{$this->stu_data['B'][$value_sn][6][score_end]},{$this->stu_data['B'][$value_sn][7][score_src]},{$this->stu_data['B'][$value_sn][7][score_test]},{$this->stu_data['B'][$value_sn][7][score_end]}\r\n";  
		}	      	
		$CSV_filename = $ys[0]."學年".$ys[1]."學期".$this->G."年級補考成績列表.csv";
		header("Content-disposition: attachment;filename=$CSV_filename");
		header("Content-type: text/x-csv ; Charset=Big5");
		header("Progma: no-cache");
		header("Expires: 0");
		echo $CSV_data;
		die();
	     }
	     //CSV 結束
			
			}
		else{
			$this->stu_data=$ALL;
			}
		



		
	}

	




}


