<?php
//$Id: PHP_tmp.html 5310 2009-01-10 07:57:56Z hami $
include "config.php";
//認證
sfs_check();


//程式使用的Smarty樣本檔
$template_file = dirname (__file__)."/templates/score_notification.htm";

//建立物件
$obj= new basic_chc($CONN,$smarty);
//初始化
$obj->init();
//處理程序,有時程序內有header指令,故本程序宜於head之前
$obj->process();


//秀出網頁布景標頭
head("補考家長通知單");

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
    var $filePath;
    
	//建構函式
	function basic_chc($CONN,$smarty){
		$this->CONN=&$CONN;
		$this->smarty=&$smarty;
		$this->filePath = set_upload_path("/school/chc_mend");
		if (!is_dir($this->filePath)){mkdir($this->filePath);}
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
		
		//取送出
		$this->act=$_REQUEST['act'];
        $this->note=$_REQUEST['note'];
		//其他分頁連結參數
		$this->linkstr="Y={$this->Y}&G={$this->G}&S={$this->S}";
	}
	//程序
	function process() {
		$this->all();
		$this->Edit_note();
		$this->ReEdit_note();
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
		$seme_class=$this->G."%";
		
		
		$query="select a.student_sn,a.stud_id,a.stud_name,a.stud_sex,b.seme_class,b.seme_num,b.seme_year_seme,c.student_sn
		from stud_base a,stud_seme b,chc_mend c
		where a.student_sn=c.student_sn
		and c.student_sn=b.student_sn
		and b.seme_year_seme='$seme_year_seme'
		and b.seme_class like '$seme_class'
		group by c.student_sn
		order by b.seme_class,b.seme_num,c.seme
		";
		$res=$this->CONN->Execute($query);
		
		//取出班級名稱陣列
		$class_base=class_base($seme_year_seme);
		
		
		while(!$res->EOF) {
			$this->stu_data[]=array(
			"stud_id"=>$res->fields[stud_id],
			"stud_name"=>$res->fields[stud_name],
			"stud_sex"=>$res->fields[stud_sex],
			"seme_class"=>$class_base{$res->fields[seme_class]},
			"seme_num"=>$res->fields[seme_num],
			"student_sn"=>$res->fields[student_sn]
			);
			$this->students_sn .= "&students_sn[]=".$res->fields[student_sn];
			$res->MoveNext();
		}
	}
	
	function Edit_note() {
		
    if ($this->act=="send") {
	/*	
	if (!is_dir("../../data/school/chc_mend")){mkdir("../../data/school/chc_mend");}
	$fp=fopen("../../data/school/chc_mend/note.txt",'w');
	*/
	$fp=fopen($this->filePath."/note.txt",'w');
	fwrite($fp,$this->note);
	fclose($fp);
      
	}		
		}
	function ReEdit_note() {
	/*	
	 if (!is_dir("../../data/school/chc_mend")){mkdir("../../data/school/chc_mend");}	
	 if (!is_file("../../data/school/chc_mend/note.txt")) {
	*/
	 if (!is_file($this->filePath."/note.txt")) {	 
		 $newnote ="因應103.7.7府教學字弟1030218804 號函修正之「彰化縣國民中學學生成績評量要點」規定,七大學習領域有四大學習領域以上畢業總平均丙等(60)以上者,方能發給畢業證書。本校讓學生有補救機會，依上述要點第9點，針對各學習領域成績未達60分者辦理補考。關於本次補考，說明如下：\r\n一、實施日期及地點：104年3月2日(一)~6日(五)第8節依序辦理全年級(語文)(數學)(自然)(社會)(健體、藝文、綜合)領域補考。請於16:05分前說明第七點到達指定教室，作答完畢即可交卷,最晚16:50結束。\r\n二、實施對象：針對上學期各學期領域不及格者，分領域補考之。\r\n三、補考成績計算：補考及格者以 60分計，補考不及格者，該領域成績就補考成績或原成績擇優登錄。\r\n四、「104學年度彰化區免試入學超額比序項目積分對照表」之均衡學習項目，其三領域(健康與體育、藝文與人文、綜合活動)採計原始成績，不採計補考後成績。\r\n五，補考當日未能出席者，視同放棄機會，不能異議。\r\n六、本次補考試題題庫於 2月 6日(五)起公告於本校網站公佈欄，請提醒貴子弟事先準備，以期補考及格。\r\n七、本通知單請粘貼於聯絡簿，回條請於2月24日(二)前送回教務處註冊組\r\n八、貴子弟需補考領域如下：\r\n";
		//$fp=fopen("../../data/school/chc_mend/note.txt",'w');
	    $fp=fopen($this->filePath."/note.txt",'w');
	    fwrite($fp,$newnote);
	    fclose($fp);
		}	           
		
	 //$fp=fopen("../../data/school/chc_mend/note.txt",'r');
	 $fp=fopen($this->filePath."/note.txt",'r');
	 while(! feof($fp)) {
	 $this->oldnote .= fgets($fp); 	 
	 
	 }
	 $this->smarty->assign("oldnote",$this->oldnote);
	
		
	}	
		
}


