<?php
//$Id: chi_dom.php 5310 2009-01-10 07:57:56Z hami $
require_once("stud_reg_config.php");

//使用者認證
sfs_check();


//c_curr_class=095_1_04_05&c_curr_seme=0951

$obj=new stu_photo();
$obj->CONN=&$CONN;
$obj->smarty=&$smarty;
$obj->IS_JHORES=&$IS_JHORES;
//$obj->smarty->assign('modify_flag',$modify_flag);
$obj->process();

head("整班編修--戶口資料");
$linkstr = 'c_curr_class='.$_GET[c_curr_class].'&c_curr_seme='.$_GET[c_curr_seme];
print_menu($menu_p,$linkstr);
$obj->display();

foot();



class stu_photo{
   var $CONN;//ADO
   var $smarty;
   var $IS_JHORES;//國中小判斷參數
   var $year;//年
   var $seme;//學期
   var $Y_name='c_curr_seme';//下拉式選單學期的奱數名稱
   var $S_name='c_curr_class';//下拉式選單班級的奱數名稱
   var $YS_ary;//學期陣列
   var $YC_ary;//年班陣列
   var $year_seme;//下拉式選單學期的奱數值95_1
   var $Sel_class;//下拉式選單班級的奱數值095_1_04_02
  
   function process() {
   	//if ($_POST){echo "<pre>";print_r($_POST);die();}
   	$this->init();
   	$this->Option=&$this->myData();//程式資料
   	if ($_POST[form_act]=='update') $this->update();
   	$this->YS_ary=$this->sel_year();//學期陣列
   	$this->YC_ary=$this->grade();//年級班級陣列

   	if ($this->Sel_class!='') $this->get_stu($this->Sel_class);
   	//$this->display();

   	$stud_coud=study_cond();//學籍資料代碼
   	foreach ($stud_coud as $tk=>$tv){$stud_coud2[$tk]=$tk.'-'.$tv;}
   	$this->Cond[A]=$stud_coud;
   	$this->Cond[B]=$stud_coud2;
   	
   	
   }

function init() {
	($_GET[$this->Y_name]=='') ? $this->year_seme=$_POST[$this->Y_name]:$this->year_seme=$_GET[$this->Y_name];
	if ($this->year_seme=='') $this->year_seme=sprintf("%04d",curr_year().curr_seme());
	($_GET[$this->S_name]=='') ? $this->Sel_class=$_POST[$this->S_name]:$this->Sel_class=$_GET[$this->S_name];
   $this->year=substr($this->year_seme,0,3);
   $this->seme=substr($this->year_seme,-1);

}
function display(){
	if ($this->tpl=='') $this->tpl=dirname(__file__)."/templates/chi_dom.htm";
		$this->smarty->assign("this",$this);
		$this->smarty->display($this->tpl);
}

function update() {
	//echo "<pre>";print_r($_POST);print_r($_FILES);die();
	//決定修改欄位
	foreach ($this->Option[txt] as $key=>$null){
		if (count($_POST[$key]) > 0) $AA[]=$key;
		unset($key);
	}
	if (count($AA)==0) return ;
	//整理為SQL語法並執行
	foreach ($_POST[$AA[0]] as $SN =>$null){
		$SQL="update  stud_domicile set ";
		foreach ($AA as $key){$JJ[]=$key."='".$_POST[$key][$SN]."'";}
		$SQL.=join(",",$JJ)." where student_sn='".$SN."'";//echo $SQL."<br>";
		$rs=$this->CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
		unset($SQL);unset($JJ);
	} 
	$URL=$_SERVER[PHP_SELF]."?".$this->Y_name."=".$_POST[$this->Y_name]."&".$this->S_name."=".$_POST[$this->S_name];
	Header("Location:$URL");
}


function sel_year() {
	$SQL = "select * from school_day where  day<=now() and day_kind='start' order by day desc ";
	$rs=$this->CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
	while(!$rs->EOF){
	$ro = $rs->FetchNextObject(false);
	// 亦可$ro=$rs->FetchNextObj()，但是僅用於新版本的adodb，目前的SFS3不適用
	$tmp_y=sprintf("%04d",$ro->year.$ro->seme);
	$tmp[$tmp_y]=$ro->year."學年度第".$ro->seme."學期";
	}
	return $tmp;
	}

function grade() {
    //名稱,起始值,結束值,選擇值
    ($this->IS_JHORES==6) ? $grade=array(7=>"一年",8=>"二年",9=>"三年"):$grade=array(1=>"一年",2=>"二年",3=>"三年",4=>"四年",5=>"五年",6=>"六年");
    $SQL="select class_id,c_year,c_name,teacher_1 from  school_class where year='".$this->year."' and semester='".$this->seme."' and enable=1  order by class_id  ";
    $rs=&$this->CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
    if ($rs->RecordCount()==0) return"尚未設定班級資料！";
    $All=$rs->GetArray();

    foreach($All as $ary) {
    	$tmp[$ary[class_id]]=$grade[$ary[c_year]].$ary[c_name]."班 (".$ary[teacher_1].")";
		}
    return $tmp;
} 


function get_stu($class_id,$type='') {
	//echo $class_id;//094_1_01_05
	$tmp=split('_',$class_id);
	$seme=$tmp[0].$tmp[1];
	
	$cla=($tmp[2]+0).$tmp[3];
	$SQL="select a.stud_id,a.student_sn,a.stud_name,a.stud_sex,a.stud_study_cond ,
	b.seme_year_seme,b.seme_class,
	b.seme_num,a.stud_study_year,c.*
	from stud_base a,stud_seme b , stud_domicile c
	where a.student_sn=b.student_sn and
	 a.student_sn=c.student_sn and  
	b.seme_year_seme='$seme' and b.seme_class='$cla'  order by b.seme_num ";
	$rs=$this->CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
	$this->stu=$rs->GetArray();

}

	function Radio($name,$sn) {
			$str=$name.'['.$sn.']';
		return $str;
	}

	function myData(){
		//主要的欄位及中文名稱對映
		$Option[txt]=array(
		'fath_name'=>'父:姓名','fath_birthyear'=>'父:出生年','fath_alive'=>'父:存歿\',
		'fath_relation'=>'父:關係','fath_p_id'=>'父:身分證號','fath_education'=>'父:教育程度',
		'fath_occupation'=>'父:職業','fath_unit'=>'父:服務單位','fath_work_name'=>'父:職稱',
		'fath_phone'=>'父:電話(公)','fath_home_phone'=>'父:電話(宅)','fath_hand_phone'=>'父:行動',
		'fath_email'=>'父:電子郵件',
		'moth_name'=>'母:姓名','moth_birthyear'=>'母:出生年','moth_alive'=>'母:存歿\',
		'moth_relation'=>'母:關係','moth_p_id'=>'母:身分證號','moth_education'=>'母:教育程度',
		'moth_occupation'=>'母:職業','moth_unit'=>'母:服務單位','moth_work_name'=>'母:職稱',
		'moth_phone'=>'母:電話(公)','moth_home_phone'=>'母:電話(宅)','moth_hand_phone'=>'母:行動',
		'moth_email'=>'母:電子郵件',
		'guardian_name'=>'監:姓名','guardian_phone'=>'監:電話','guardian_address'=>'監:地址',
		'guardian_relation'=>'監:關係','guardian_p_id'=>'監:身分證號','guardian_unit'=>'監:服務單位',
		'guardian_work_name'=>'監:職稱','guardian_hand_phone'=>'監:行動','guardian_email'=>'監:電子郵件',
		'grandfath_name'=>'祖父姓名','grandfath_alive'=>'祖父存歿\',
		'grandmoth_name'=>'祖母姓名','grandmoth_alive'=>'祖母存歿\');

		//欄位類型設定
		$Option[type]=array(
		'fath_name'=>'text','fath_birthyear'=>'text','fath_alive'=>'radio',
		'fath_relation'=>'selectbox','fath_p_id'=>'text','fath_education'=>'selectbox',
		'fath_occupation'=>'text','fath_unit'=>'text','fath_work_name'=>'text',
		'fath_phone'=>'text','fath_home_phone'=>'text','fath_hand_phone'=>'text',
		'fath_email'=>'text',
		'moth_name'=>'text','moth_birthyear'=>'text','moth_alive'=>'radio',
		'moth_relation'=>'selectbox','moth_p_id'=>'text','moth_education'=>'selectbox',
		'moth_occupation'=>'text','moth_unit'=>'text','moth_work_name'=>'text',
		'moth_phone'=>'text','moth_home_phone'=>'text','moth_hand_phone'=>'text',
		'moth_email'=>'text',
		'guardian_name'=>'text','guardian_phone'=>'text','guardian_address'=>'text',
		'guardian_relation'=>'selectbox','guardian_p_id'=>'text','guardian_unit'=>'text',
		'guardian_work_name'=>'text','guardian_hand_phone'=>'text','guardian_email'=>'text',
		'grandfath_name'=>'text','grandfath_alive'=>'radio',
		'grandmoth_name'=>'text','grandmoth_alive'=>'radio');

		//文字欄位大小
		$Option[long]=array(
		'fath_name'=>'12','fath_birthyear'=>'6',
		'fath_p_id'=>'12','fath_occupation'=>'12','fath_unit'=>'12','fath_work_name'=>'12',
		'fath_phone'=>'12','fath_home_phone'=>'12','fath_hand_phone'=>'12',
		'fath_email'=>'20',
		'moth_name'=>'12','moth_birthyear'=>'6',
		'moth_p_id'=>'12','moth_occupation'=>'12','moth_unit'=>'12','moth_work_name'=>'12',
		'moth_phone'=>'12','moth_home_phone'=>'12','moth_hand_phone'=>'12',
		'moth_email'=>'20',
		'guardian_name'=>'12','guardian_phone'=>'12','guardian_address'=>'30',
		'guardian_p_id'=>'12','guardian_unit'=>'12',
		'guardian_work_name'=>'12','guardian_hand_phone'=>'12','guardian_email'=>'20',
		'grandfath_name'=>'12','grandmoth_name'=>'12');
		
   	$GR=array(1=>'父子',2=>'父女',3=>'母子',4=>'母女',5=>'祖孫',6=>'兄弟',7=>'兄妹',8=>'姐弟',9=>'姊妹',10=>'伯叔姑姪甥',11=>'其他');
   	$MR=array(1=>'生母',2=>'養母',3=>'繼母');
   	$FR=array(1=>'生父',2=>'養父',3=>'繼父');
   	$Live=array(1=>'存',2=>'歿\');
   	$Edu=array(1=>'博士',2=>'碩士',3=>'大學',4=>'專科',5=>'高中',6=>'國中',7=>'國小畢業',8=>'國小肄業',9=>'識字(未就學)',10=>'不識字');
 
		//下拉式選單及RadioCheck選項資料設定
		$Option[ary]=array(
		'fath_alive'=>$Live,
		'fath_relation'=>$FR,
		'fath_education'=>$Edu,
		'moth_alive'=>$Live,
		'moth_relation'=>$MR,
		'moth_education'=>$Edu,
		'guardian_relation'=>$GR,
		'grandfath_alive'=>$Live,
		'grandmoth_alive'=>$Live);
		return $Option;
	}

}//end class 





