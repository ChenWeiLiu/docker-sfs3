<?php
// $Id: month_paper_advance.php 5310 2009-01-10 07:57:56Z hami $
// 引入 SFS3 的函式庫
//include "../../include/config.php";

// 引入您自己的 config.php 檔
@ini_set('error_reporting','E_ALL & ~E_NOTICE');
require "config.php";

// 認證
sfs_check();

	//轉換成全域變數
$act=($_POST['act'])?"{$_POST['act']}":"{$_GET['act']}";
$test_sort=($_POST['test_sort'])?"{$_POST['test_sort']}":"{$_GET['test_sort']}";
$class_num=($_POST['class_num'])?"{$_POST['class_num']}":"{$_GET['class_num']}";

//程式使用的Smarty樣本檔
$template_file = dirname (__file__)."/templates/chc_seme_advance_class.htm";


// 叫用 SFS3 的版頭
head("月考成績查詢");

// 您的程式碼由此開始
print_menu($menu_p);
$curr_year = curr_year();
$curr_seme = curr_seme();

//由teacher_sn找出他是哪一班的導師
$class_num=get_teach_class();
if($class_num){
   $class_id=sprintf("%03d",$curr_year)."_".$curr_seme."_".sprintf("%02d_%02d",substr($class_num,0,-2),substr($class_num,-2,2));
   //建立物件
   $obj= new chc_seme_advance_class($CONN,$smarty);
   //初始化
   $obj->init();

   
   $obj->process($class_id);
   //顯示內容
   $obj->display($template_file);

   // SFS3 的版尾
   foot();

}else{
   $main="<table cellspacing=1 cellpadding=6 border=0 bgcolor='#FFF829' width='80%' align='center'><tr><td align='center'><h1><img src='images/caution.png' align='middle' border=0> 警告</h1></font></td></tr><tr><td align='center' bgcolor='#FFFFFF' width='90%'>".$_SESSION['session_tea_name']."您不具導師身份！ 無法進行操作！<br>若有疑問請檢查『教師管理』的任職資料。</td></tr></table>";

	//設定主網頁顯示區的背景顏色
	$back_ground="
		<table cellspacing=1 cellpadding=6 border=0 bgcolor='#B0C0F8' width='100%'>
			<tr bgcolor='#FFFFFF'>
				<td>
					$main
				</td>
			</tr>
		</table>";
	echo $back_ground;
}

//物件class
class chc_seme_advance_class{
	var $CONN;//adodb物件
	var $smarty;//smarty物件
	var $stu;//學生資料
	var $subj;//科目陣列
	var $rule;//等第
	var $TotalSco;//各段考分數

	//建構函式
	function chc_seme_advance_class($CONN,$smarty){
		$this->CONN=&$CONN;
		$this->smarty=&$smarty;
	}
	//初始化
	function init() {}
	//程序
	function process($class_id) {
		$this->all($class_id);
	}
	//顯示
	function display($tpl){
		//$ob=new drop($this->CONN);
		//$this->select=&$ob->select();
		$this->smarty->assign("this",$this);
		$this->smarty->display($tpl);
	}
	//擷取資料
	function all($class_id){
		if ($class_id=='') return;

		$this->class_id=$class_id;
		$this->stu=$this->get_stu();

		$this->subj=$this->get_subj("seme");
		$this->sco=$this->get_sco();

		foreach($this->stu as $sn => $value){
         if(isset($this->sco[$sn]) AND isset($this->stu[$sn])){
            $this->stu[$sn] = $this->stu[$sn]+$this->sco[$sn];
         }
      }
	}

/* 取學生陣列,取自stud_base表與stud_seme表*/
	function get_stu(){
		$CID=split("_",$this->class_id);//093_1_01_01
		$year=$CID[0];
		$seme=$CID[1];
		$grade=$CID[2];//年級
		$class=$CID[3];//班級
		$CID_1=$year.$seme;
		$CID_2=sprintf("%03d",$grade.$class);
		$SQL="select 	a.stud_id,a.student_sn,a.stud_name,a.stud_sex,b.seme_year_seme,b.seme_class,b.seme_num,a.stud_study_cond  from stud_base a,stud_seme b where a.student_sn=b.student_sn and b.seme_year_seme='$CID_1' and b.seme_class='$CID_2' $add_sql order by b.seme_num ";

		$rs=&$this->CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
		$obj_stu=array();
		while ($rs and $ro=$rs->FetchNextObject(false)) {
			$obj_stu[$ro->student_sn] = get_object_vars($ro);
		}
		return $obj_stu;
	}

/*取科目陣列：自score_subject表中取中文名稱,自score_ss表中取該班科目  score_ss表rate表示加權  print完整  need_exam計分  enable使用  */
function get_subj($type='') {
	switch ($type) {
		case 'all':
		$add_sql=" ";break;
		case 'seme':
		$add_sql=" and need_exam='1' and enable='1' and  rate > 0  ";break;//有成績的
		case 'stage':
		$add_sql=" and need_exam='1'  and print='1' and enable='1' and  rate > 0  ";break;//有段考,完整
		case 'no_test':
		$add_sql=" and need_exam='1'  and print!='1' and enable='1'  and  rate > 0  ";break; //不用段考的
		default:
		$add_sql=" and enable='1'  and  rate > 0  ";break;
	}
	$CID=split("_",$this->class_id);//093_1_01_01
	$year=$CID[0];//094_2_01_04
	$seme=$CID[1];
	$grade=$CID[2];
	$class=$CID[3];
	$CID_1=sprintf("%03d",$year)."_".$seme."_".$grade."_".$class;

	$SQL="select * from score_ss where class_id='$CID_1' $add_sql order by print desc,sort,sub_sort ";
	$rs=&$this->CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);

	if ($rs->RecordCount()==0){
		$SQL="select * from score_ss where class_id='' and year='".intval($year)."' and semester='".intval($seme)."' and  class_year='".intval($grade)."' $add_sql order by print desc,sort,sub_sort ";
		$rs=&$this->CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
		$All_ss=&$rs->GetArray();
	}
	else{$All_ss=&$rs->GetArray();}

		//取科目中文名稱
		$SQL="select subject_id,subject_name from score_subject ";
		$rs=&$this->CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
		while ($rs and $ro=$rs->FetchNextObject(false)) {
			$subj_name[$ro->subject_id] = $ro->subject_name;
		}

		//取考試次數設定來自 score_setup 表
		$SQL="SELECT * FROM `score_setup` where  year='".($year+0)."' and  semester='".($seme+0)."' and class_year='".($grade+0)."' ";
		$rs=&$this->CONN->Execute($SQL) or die("無法查詢，語法:".$SQL."可能是成績尚未設定");//echo $SQL;
		$score_setup=$rs->GetRowAssoc(FALSE);

//成績等第(目前未用)
		$this->rule=$this->get_rule($score_setup[rule]);
		//整理科目陣列
		$obj_SS=array();
		for($i=0;$i<count($All_ss);$i++){
			$key=$All_ss[$i][ss_id];//索引
			// $obj_SS[$key]=$All_ss[$i];//全部陣列,暫不用
			$obj_SS[$key][rate]=$All_ss[$i][rate];//加權
			if ($All_ss[$i]["print"]=='1') {$obj_SS[$key][mon_test]=$score_setup[performance_test_times];}
			else{$obj_SS[$key][mon_test]='0'; }
//			$obj_SS[$key][mon_test]=$All_ss[$i]["print"];//是否完整
			$obj_SS[$key][sc]=$subj_name[$All_ss[$i][scope_id]];//領域名稱
			$obj_SS[$key][sb]=$subj_name[$All_ss[$i][subject_id]];//科目名稱
			($obj_SS[$key][sb]=='') ? $obj_SS[$key][sb]=$obj_SS[$key][sc]:"";
		}
	return $obj_SS;
	}

	function get_rule($rule) {
		$rule=str_replace(" ","",$rule);
		$rules = explode("\n",$rule);
		for ($i=0; $i<count($rules); $i++){
			$ary[$i]=explode("_", $rules[$i]);}
		return 	$ary;

	}
	//取所有成績
	function get_sco(){
		$ss=join(",",array_keys($this->subj));
		$stu=join(",",array_keys($this->stu));
		$YSGC=split("_",$this->class_id);
		$tb="score_semester_".($YSGC[0]+0)."_".($YSGC[1]+0);
		$SQL="select score_id,class_id,student_sn,ss_id,score,test_name,test_kind,test_sort from `$tb` where  student_sn in ($stu) and  ss_id in ($ss) ";

		$rs=&$this->CONN->Execute($SQL) or die("無法查詢，語法:".$SQL."可能是課程或無學生資料");
		$All_sco=&$rs->GetArray();

		foreach ($All_sco as $sco){
			$sn=$sco[student_sn];
			$ss_id=$sco[ss_id];
			$test_sort=$sco[test_sort];
			if ($sco[test_kind]=='定期評量'){
            $kind='mon';
            $TotalSco[$sn][$test_sort]+=$sco[score];
         }
         if ($sco[test_kind]=='平時成績') $kind='nor';
			if ($sco[test_kind]=='全學期') $kind='all';
			$Vsco[$sn][$ss_id][$test_sort][$kind]=$sco[score];
		}

      foreach ($TotalSco as $sn => $sco){
         $TotalSco[$sn][diff2]="--";
         $TotalSco[$sn][diff3]="--";
         $TotalSco[$sn][sco_order1]="--";
         $TotalSco[$sn][sco_order2]="--";
         $TotalSco[$sn][sco_order3]="--";
         $TotalSco[$sn][diff_order2]="--";
         $TotalSco[$sn][diff_order3]="--";
         $TotalSco[$sn][diff_org_order2]="--";
         $TotalSco[$sn][diff_org_order3]="--";

         $sk=array_keys($sco);
         $start=max($sk);
         $startkey[]=$start;
         while($start>0){
            $pre_test=$start-1;
            if($pre_test>0){
               $diff=$sco[$start]-$sco[$pre_test];
               $TotalSco[$sn][diff.$start]=$diff;
               ${DiffOrder.$start}[$sn]=$diff;
            }
            ${EachSco.$start}[$sn]=$sco[$start];
            $start--;
         }
      }


      $start=max($startkey);
      while($start>0){
         //各段考名次排名
         arsort(${EachSco.$start});
         //$pre=$start-1;
         $sco_order=0;     //當次排名
         $pre_value=0;
         $sco_order_add=0;
         foreach(${EachSco.$start} as $key => $value){  //進退步名次
            if($pre_value==$value){
               $sco_order_add++;
            }else{
               $sco_order=$sco_order+$sco_order_add;
               $sco_order_add=0;
               $sco_order++;
            }
            $TotalSco[$key][sco_order.$start]=$sco_order;

            $pre_value=$value;

         }

         $start--;
      }


      $start=max($startkey);

      foreach($TotalSco as $sn => $value){
         if($TotalSco[$sn][sco_order3]!="--" AND $TotalSco[$sn][sco_order2]!="--"){
            $TotalSco[$sn][diff_order3]=($TotalSco[$sn][sco_order3]-$TotalSco[$sn][sco_order2])*(-1);  //進退步名次
         }
         if($TotalSco[$sn][sco_order2]!="--" AND $TotalSco[$sn][sco_order1]!="--"){
            $TotalSco[$sn][diff_order2]=($TotalSco[$sn][sco_order2]-$TotalSco[$sn][sco_order1])*(-1);  //進退步名次
         }
      }

      return $TotalSco;

   }

	//傳回該生該科該階段成績//
	function sco($sn,$ss,$test_sort,$kind){
		$sco=ceil($this->sco[$sn][$ss][$test_sort][$kind]);
		if ($sco < 60) { return "<font color=#FF0000> $sco</font>";}
		else{	return $sco;}
	}
	//傳回該生日常成績//
	function sco_nor($sn){
		$sco=ceil($this->sco[$sn][nor]);
		if ($sco < 60) { return "<font color=#FF0000> $sco</font>";}
		else{	return $sco;}
	}


}


?>
