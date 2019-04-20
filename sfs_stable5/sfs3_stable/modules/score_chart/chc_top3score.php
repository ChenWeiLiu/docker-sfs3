<?php
//$Id: chc_seme_rank.php 8576 2015-10-27 11:13:27Z qfon $
include "chc_config.php";
//認證
sfs_check();

//引入換頁物件(學務系統用法)
include_once "chc_dropmenu.php";
//include_once "chc_seme_advance.php";
//程式使用的Smarty樣本檔
$template_file = dirname (__file__)."/templates/chc_top3score.htm";
//建立物件
$obj= new chc_top3score($CONN,$smarty);
//初始化
$obj->init();
//處理程序,有時程序內有header指令,故本程序宜於head("score_semester_91_2模組");之前
$obj->process();

//秀出網頁布景標頭
head("階段成績表現優異名單");

//顯示SFS連結選單(欲使用請拿開註解)
echo make_menu($school_menu_p);

//顯示內容
$obj->display($template_file);
//佈景結尾
foot();

//物件class
class chc_top3score{
	var $CONN;//adodb物件
	var $smarty;//smarty物件
	var $stu;//學生資料
	var $subj;//科目陣列
	var $rule;//等第
	var $TotalSco;//各段考分數
	var $kind;
	var $Allclass_id; //全年級的所有班級
	var $class_id;    //班級
	var $TestNum;     //第幾次定期(平時)考
    var $Rank;        //取到前幾名
	//建構函式
	function chc_top3score($CONN,$smarty){
		$this->CONN=&$CONN;
		$this->smarty=&$smarty;
	}
	//初始化
	function init() {}
	//程序
	function process() {
		$this->all();
	}
	//顯示
	function display($tpl){
		$ob=new drop($this->CONN);
		$this->select=&$ob->select();	
		$this->smarty->assign("this",$this);
		$this->smarty->display($tpl);
	}
	//擷取資料
	function all(){     
		$array1=array("1"=>"sco_order1","2"=>"sco_order2","3"=>"sco_order3","4"=>"diff_org_order2","5"=>"diff_org_order3");    
		$ob=new drop($this->CONN);
		$this->Allclass_id=&$ob->grade();
		$this->TestNum=$_GET['TestNum'];
		$this->Rank=$_GET['Rank'];	
      foreach($this->Allclass_id as $ary => $classid){
        //$this->class_id=$classid;
		$this_stu=$this->get_stu($classid);				 
		$this_subj=$this->get_subj("seme",$classid);	
		$this_sco=$this->get_sco($_GET[kind],$classid,$this_subj,$this_stu);
		$this_kind=$this->get_kind($_GET[kind]);
		$this->kind=$this_kind;
		foreach($this_stu as $sn => $value){
         if(isset($this_sco[$sn]) AND isset($this_stu[$sn])){			 
            $this_stu[$sn] = $this_stu[$sn]+$this_sco[$sn];
            if ($this_stu[$sn][$array1[$this->TestNum]]<=$this->Rank && $this_stu[$sn][$array1[$this->TestNum]]<>"--") {
            $this->stu[$sn]=$this_stu[$sn];
            
		    }
         }
        }
       }      
	}
	
	function get_kind($a){
	    if ($a==1)$obj_kind="定期";
		if ($a==2)$obj_kind="平時";
		if ($a==3)$obj_kind="[定期+平時]";		
		return $obj_kind;
	}	
	
	

/* 取學生陣列,取自stud_base表與stud_seme表*/
	function get_stu($classid){
		$CID=split("_",$classid);//093_1_01_01
		$year=$CID[0];
		$seme=$CID[1];
		$grade=$CID[2];//年級
		$class=$CID[3];//班級
		$CID_1=$year.$seme;
		$CID_2=sprintf("%03d",$grade.$class);
		$SQL="select a.stud_id,a.student_sn,a.stud_name,a.stud_sex,b.seme_year_seme,b.seme_class,b.seme_num,a.stud_study_cond  from stud_base a,stud_seme b where a.student_sn=b.student_sn and b.seme_year_seme='$CID_1' and b.seme_class='$CID_2' $add_sql order by b.seme_num ";
		$rs=&$this->CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
		$obj_stu=array();
		while ($rs and $ro=$rs->FetchNextObject(false)) {
			$obj_stu[$ro->student_sn] = get_object_vars($ro);
		}
		return $obj_stu;
	}

/*取科目陣列：自score_subject表中取中文名稱,自score_ss表中取該班科目  score_ss表rate表示加權  print完整  need_exam計分  enable使用  */
function get_subj($type='',$class_id) {
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
	$CID=split("_",$class_id);//093_1_01_01
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
		$ii=count($All_ss);
		for($i=0;$i<$ii;$i++){
			$key=$All_ss[$i][ss_id];//索引
			// $obj_SS[$key]=$All_ss[$i];//全部陣列,暫不用
			$obj_SS[$key][rate]=$All_ss[$i][rate];//加權
			if ($All_ss[$i]["print"]=='1') {
            $obj_SS[$key][mon_test]=$score_setup[performance_test_times];
         }else{
            $obj_SS[$key][mon_test]='0';
         }
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
	function get_sco($a,$class_id,$this_subj,$this_stu){
		if (sizeof($this_stu)==0) return;
		$ss=join(",",array_keys($this_subj));
		$stu=join(",",array_keys($this_stu));		
		$YSGC=split("_",$class_id);
		$tb="score_semester_".($YSGC[0]+0)."_".($YSGC[1]+0);
		$SQL="select score_id,class_id,student_sn,ss_id,score,test_name,test_kind,test_sort from `$tb` where  student_sn in ($stu) and  ss_id in ($ss) ";
		$rs=&$this->CONN->Execute($SQL) or die("無法查詢，語法:".$SQL."可能是課程或無學生資料");
		$All_sco=&$rs->GetArray();	
		foreach ($All_sco as $sco){
			$sn=$sco[student_sn];
			$ss_id=$sco[ss_id];
			$test_sort=$sco[test_sort];
			if ($a==1)
			{
			 if ($sco[test_kind]=='定期評量'){
             $kind='mon';
             $TotalSco[$sn][$test_sort]+=$sco[score];
             $TotalSco[$sn][TopMarks.'_'.$test_sort]+=100;  //計算該生的滿分是多少
	         }	        
			}
			if ($a==2)
			{
			 if ($sco[test_kind]=='平時成績'){
             $kind='nor';
             $TotalSco[$sn][$test_sort]+=$sco[score];
             $TotalSco[$sn][TopMarks.'_'.$test_sort]+=100;  //計算該生的滿分是多少
	         }	
			}
			if($a==3)
			{				
				$CID=split("_",$class_id);//093_1_01_01
			    $year=(int)$CID[0];				
		        $seme=$CID[1];
		        $grade=(int)$CID[2];//年級
		        $class=$CID[3];//班級				
		      ///////////////////////////////////////////////////////////////		
			  $sql="select * from score_setup where class_year='$grade' and year='$year' and semester='$seme'";
	          $rs=&$this->CONN->Execute($sql) or die("無法查詢，語法:".$sql."可能是課程或無學生資料");
			  $score_mode= $rs->fields['score_mode'];			  
			  $performance_test_times= $rs->fields['performance_test_times'];			  
	          if ($score_mode=="all" || $performance_test_times==1)
			  {
			  $test_ratio=explode("-",$rs->fields['test_ratio']);
	          $sratio=$test_ratio[0]*0.01;
	          $nratio=$test_ratio[1]*0.01;	
			  }
			  else
			  {
			    $test_rv=explode(",",$rs->fields['test_ratio']);
		        for($j=0;$j<$performance_test_times;$j++)
                {
				$jj=$j+1;
			    $rv=explode("-",$test_rv[$j]);
			    $sratioi[$jj]=$rv[0]*0.01;
			    $nratioi[$jj]=$rv[1]*0.01;	
		        }		
                $sratio=$sratioi[$test_sort];
	            $nratio=$nratioi[$test_sort];									  
			  }
				///////////////////////////////////////////////////////  			 
			 if ($sco[test_kind]=='定期評量'){ 		
               $TotalSco[$sn][$test_sort]+=$sco[score]*$sratio;             
	         }			  
			   if ($sco[test_kind]=='平時成績'){
              $TotalSco[$sn][$test_sort]+=$sco[score]*$nratio;
              //$TotalSco[$sn][TopMarks.'_'.$test_sort]+=100;  //計算該生的滿分是多少
	          }				  
			   $TotalSco[$sn][TopMarks.'_'.$test_sort]+=100;  //計算該生的滿分是多少   
			}			
         if ($sco[test_kind]=='平時成績') $kind='nor';
			if ($sco[test_kind]=='全學期') $kind='all';
			$Vsco[$sn][$ss_id][$test_sort][$kind]=$sco[score];
			//debug_msg("第".__LINE__."行 sco ", $sco);
			//debug_msg("第".__LINE__."行 TopMarks ", $TopMarks);
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
               if($diff>0){  //有進步才計算
                  $GainRate=$diff*100/$TotalSco[$sn][TopMarks.'_'.$start];  //進退步比例
                  $GainRate=sprintf("%.2f", $GainRate);
               }else{
                  $GainRate="--";
               }
               $TotalSco[$sn][diff.$start]=$diff;               
               $TotalSco[$sn][GainRate.$start]=$GainRate;
               //$TotalSco[$sn][TopMarks.$start]=$sco[$start][TopMarks];
               ${DiffOrder.$start}[$sn]=$diff;
            }
            ${EachSco.$start}[$sn]=$sco[$start];
            $start--;
         }
      }
      //debug_msg("第".__LINE__."行 TotalSco ", $TotalSco);
      $start=max($startkey);
      while($start>0){
         //各段考名次排名
         arsort(${EachSco.$start});
         //$pre=$start-1;
         $sco_order=0;  //當次排名
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
         //各段考間進退步成績名次排名
         $pre_test=$start-1;
         if ($pre_test>0) {
	      arsort(${DiffOrder.$start});
	      $diff_sco_order=0;  //當次排名
          $diff_pre_value=0;
          $diff_sco_order_add=0;
          foreach(${DiffOrder.$start} as $key => $value){  //進退步名次
             if($diff_pre_value==$value){
                $diff_sco_order_add++;               
             }else{
                $diff_sco_order=$diff_sco_order+$diff_sco_order_add;
                $diff_sco_order_add=0;
                $diff_sco_order++;
             }
            $TotalSco[$key][diff_org_order.$start]=$diff_sco_order;
            $diff_pre_value=$value;
          }
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
