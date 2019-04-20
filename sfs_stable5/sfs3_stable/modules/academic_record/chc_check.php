<?php
//$Id: chc_check.php 8132 2014-09-23 07:59:30Z smallduh $
include_once "../../include/config.php";
require_once "./module-cfg.php";

//認證

sfs_check();


//秀出網頁布景標頭
myheader();
head("成績繳交檢查");
print_menu($school_menu_p);

$class_num=get_teach_class();
if ($class_num=='') {
	echo "<H2><CENTER>您非該班導師！</CENTER></H2>";
	foot();
	die();
	}else {
	$grade=substr($class_num,0,1);
	$class=substr($class_num,1,2);
	$year_seme=sprintf("%03d",curr_year())."_".curr_seme();
	$class_id=$year_seme."_".sprintf("%02d",$grade)."_".sprintf("%02d",$class);
	}

##################陣列列示函式2##########################
// 1.smarty物件
$template_dir = $SFS_PATH."/".get_store_path()."/templates/";
$smarty->left_delimiter="{{";
$smarty->right_delimiter="}}";



if ( $class_id ){
	$seme=split("_",$class_id);
	$class_name=($seme[0]+0)."學年 第".$seme[1]."學期&nbsp;".($seme[2]+0)."年".($seme[3]+0)."班";
	$seme=sprintf("%03d",$seme[0]).$seme[1];
	$sn_ary=get_stsn($class_id);
	$sn_ary2=join(',',array_keys($sn_ary));//僅取key值即student_sn
	foreach ($sn_ary as $sn =>$data){$stud_id[]=$data[stud_id];}
	$stud_id=join(',',$stud_id);
	$ss_id=get_subj($class_id);
//	$ss_id2=join(',',array_keys($ss_id));//僅取key值即student_sn
//	$ss_k=array_keys($ss_id);
//	$ss_id2=$ss_k
	$SQL = "select * from stud_seme_score   where   seme_year_seme='$seme' and  student_sn in ($sn_ary2)  ";
	$rs=$CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
	$All_ss=$rs->GetArray();
	$SQL = "select * from stud_seme_score_oth  where   seme_year_seme='$seme' and  stud_id in ($stud_id)  ";
	$rs=$CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
	$All_oth=$rs->GetArray();
	$SQL = "select * from stud_seme_score_nor  where   seme_year_seme='$seme' and  student_sn in ($sn_ary2)  ";
	$rs=$CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
	$All_nor=$rs->GetArray();

	foreach ($sn_ary as $sn =>$data){
		$stu[$sn]=$data;
		$stud_id=$data[stud_id];
		foreach ($All_ss as $sco){
			if ($sco[student_sn]==$sn ){
				$stu[$sn][$sco[ss_id]][score]=ceil($sco[ss_score]);
				$stu[$sn][$sco[ss_id]][memo]=$sco[ss_score_memo];
			}
		}
		foreach ($All_oth as $oth){
			//各科的努力程度
			if ($oth[stud_id]==$stud_id && $oth[ss_kind]=='努力程度'){
				$stu[$sn][$oth[ss_id]][ss_val]=$oth[ss_val];
			}
			//日常成績的努力程度
			if ($oth[stud_id]==$stud_id && $oth[ss_kind]=='生活表現評量'){
				$na="ss_val_".$oth[ss_id];
				$stu[$sn][nor][$na]=$oth[ss_val];
			}
		}
		foreach ($All_nor as $nor){
			if ($nor[student_sn]==$sn ) {
				$stu[$sn][nor][score]=ceil($nor[ss_score]);
				$stu[$sn][nor][memo]=$nor[ss_score_memo];
			}
		}
	}
	$width=ceil(600/(COUNT($ss_id)+2))-1;
	$smarty->assign('width', $width);
	$smarty->assign('stu', $stu);
	$smarty->assign('ss_id', $ss_id);
	$smarty->assign('class_name', $class_name);
	$smarty->display($template_dir."chc_check_view.htm");
}

// echo"<pre>";
//print_r($stu);
//print_r($sn_ary);
//   	  student_sn   	  ss_id   	  ss_score   	  ss_score_memo

//佈景結尾
foot();
#####################   CSS  ###########################
function myheader(){
?>
<style type="text/css">
body{background-color:#f9f9f9;font-size:12pt}
.ip12{border-style: solid; border-width: 0px; background-color:#E6ECF0; font-size:12pt;}
.ipmei{border-style: solid; border-width: 0px; background-color:#E6ECF0; font-size:14pt;}
.ipme2{border-style: solid; border-width: 0px; background-color:#E6ECF0; font-size:14pt;color:red;font-family:標楷體 新細明體;}
.ip2{border-style: solid; border-width: 0px; background-color:#E6ECF0; font-size:11pt;color:red;font-family:新細明體 標楷體;}
.ip3{border-style: solid; border-width: 0px; background-color:#E6ECF0; font-size:12pt;color:blue;font-family:新細明體 標楷體;}
.bu1{border-style: groove;border-width:1px: groove;background-color:#CCCCFF;font-size:12px;Padding-left:0 px;Padding-right:0 px;}
.bub{background-color:#FFCCCC;font-size:14pt;}
.bur2{border-style: groove;border-width:1px: groove;background-color:#FFCCCC;font-size:12px;Padding-left:0 px;Padding-right:0 px;}
.f8{font-size:9pt;color:blue;}
.f9{font-size:9 pt;}
</style><?php
}


function get_stsn($class_id){
		global $CONN;
	$st_sn=array();
	//--foreach($class_id as $key=>$data){
	$key = $class_id;
	$class_ids=split("_",$key);
	$seme=$class_ids[0].$class_ids[1];
	$the_class=($class_ids[2]+0).$class_ids[3];
	$SQL="select a.stud_id,a.stud_name,a.stud_sex,a.student_sn,b.seme_num  from stud_base  a,stud_seme b where  b.seme_year_seme ='$seme' and b.seme_class='$the_class' and a.student_sn=b.student_sn order by seme_num ";
	$rs = $CONN->Execute($SQL);
//	$the_sn=$rs->GetArray();
	while(!$rs->EOF){
		$ro = $rs->FetchNextObject(false);
		$sn=$ro->student_sn;
		$stu[$sn]=get_object_vars($ro);
	}

return $stu;
}


########- get_subj 取得該班所有在籍學生以學生流水號為key 的函式 --------#################
########-      get_subj  取得該班所有SS_ID科目名稱函式 --------#################
##   $type=all全部,seme有學期成績,stage須段考,no_test不須段考
##    計分need_exam  完整print  加權
function get_subj($class_id,$type='') {
global $CONN ;
	switch ($type) {
		case 'all':
		$add_sql=" ";break;
		case 'seme':
		$add_sql=" and need_exam='1' and enable='1' ";break;//有成績的
		case 'stage':
		$add_sql=" and need_exam='1'  and print='1' and enable='1' ";break;//有段考,完整
		case 'no_test':
		$add_sql=" and need_exam='1'  and print!='1' and enable='1' ";break; //不用段考的
		default:
		$add_sql=" and enable='1' ";break;
	} 
	$CID=split("_",$class_id);//093_1_01_01
	$year=$CID[0];
	$seme=$CID[1];
	$grade=$CID[2];
	$class=$CID[3];
	$CID_1=$year."_".$seme."_".$grade."_".$class;

	$SQL="select * from score_ss where class_id='$CID_1' $add_sql  and  rate > 0  order by sort,sub_sort ";
	$rs=$CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);

	if ($rs->RecordCount()==0){
		$SQL="select * from score_ss where class_id='' and year='".intval($year)."' and semester='".intval($seme)."' and  class_year='".intval($grade)."' $add_sql order by sort,sub_sort ";
		$rs=$CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
		$All_ss=$rs->GetArray();
	}
	else{$All_ss=$rs->GetArray();}

	$subj_name=initArray("subject_id,subject_name","select * from score_subject ");
	$obj_SS=array();

	for($i=0;$i<count($All_ss);$i++){
		$key=$All_ss[$i][ss_id];//索引
		// $obj_SS[$key]=$All_ss[$i];//全部陣列,暫不用
		$obj_SS[$key][rate]=$All_ss[$i][rate];//加權
		$obj_SS[$key][sc]=$subj_name[$All_ss[$i][scope_id]];//領域名稱
		$obj_SS[$key][sb]=$subj_name[$All_ss[$i][subject_id]];//科目名稱
		($obj_SS[$key][sb]=='') ? $obj_SS[$key][sb]=$obj_SS[$key][sc]:"";
	}
	//die("無法查詢，語法:".$SQL);
	return $obj_SS;
}

##################  基本工具 initArray轉化資料為索引與帶值函式 #######################
## 選取資料的欄A為索引,欄B為值,欄A須是唯一
## 使用時 傳入 $F1為字串==>subject_id,subject_name
## 使用時 傳入 $SQL為資料庫語法
##################  基本工具 initArray轉化資料為索引與帶值函式 #######################

function initArray($F1,$SQL){
	global $CONN ;
	$col=split(",",$F1);
	$key_field=$col[0];
	$value_field=$col[1];

	$rs = $CONN->Execute($SQL) or die($SQL);
	$sch_all = array();
	if (!$rs) {
		Return $CONN->ErrorMsg(); 
	} else {
		while (!$rs->EOF) {
		$sch_all[$rs->fields[$key_field]]=$rs->fields[$value_field]; 
		$rs->MoveNext(); // 移至下一筆記錄
		}
	}
	Return $sch_all;
}


?>
