<?php
//$Id: chc_check.php 6346 2011-02-24 03:29:16Z infodaes $
require_once("./chc_config.php");

//認證

sfs_check();


//秀出網頁布景標頭
myheader();
head("成績繳交檢查");
print_menu($school_menu_p);
##################陣列列示函式2##########################
// 1.smarty物件
$template_dir = $SFS_PATH."/".get_store_path()."/templates/";
$smarty->left_delimiter="{{";
$smarty->right_delimiter="}}";

// 2.判斷學年度
	($_GET[year_seme]=='') ? $year_seme=curr_year()."_".curr_seme():$year_seme=$_GET[year_seme];

// 3.指派下拉式選擇學期
	$smarty->assign("sel_year",sel_year('year_seme',$year_seme));

// 4.指派下拉式選擇年級
	$url=$_SERVER[PHP_SELF]."?year_seme=".$year_seme."&grade=";
	$smarty->assign("sel_grade",sel_grade('grade',$_GET[grade],$url));
	$smarty->assign("phpself",$_SERVER[PHP_SELF]);
	$smarty->assign("input_txt",$input_txt);
	$smarty->assign("add_memo_file",$add_memo_file);
	$smarty->assign("school_name",$sch_data[sch_cname]);

// 5.若有選擇班級  指派班級選擇區 ,判斷是否傳值  再列出各班以供選擇 
if($year_seme!='' && $_GET[grade]!='' ){
	$all_class_array=get_class_info1($_GET[grade],$year_seme);
	$num=count($all_class_array);
	$num_max=(ceil($num/10))*10;
	$prt_ary=array();
	for($i=0;$i<$num_max;$i++){
		if($all_class_array[$i][class_id]!='') { 
			$prt_ary[$i][class_id]=$all_class_array[$i][class_id];
			$prt_ary[$i][c_name]="<TD width=10%><LABEL><INPUT TYPE='checkbox' NAME='class_id[".$all_class_array[$i][class_id]."]'  onclick=\"location.href='$_SERVER[PHP_SELF]?class_id=".$all_class_array[$i][class_id]."&year_seme=$year_seme&grade=$_GET[grade]'\">".$all_class_array[$i][c_name]."班</LABEL></TD>\n";//year_seme=93_2&grade=4
		}else {
			$prt_ary[$i][class_id]="";
			$prt_ary[$i][c_name]="<TD width=10%>&nbsp;</TD>";
			}
	}

	$smarty->assign("sel_class",$prt_ary);
	$smarty->assign("click_button",$click_button);


	}//end if 
else {
	$smarty->assign("sel_class","<CENTER>使用方式：先選學期，再選年級！</CENTER>");
}
$smarty->display($template_dir."chc_check.htm");

if ( $_GET[class_id] ){
	$class_id=$_GET[class_id];
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
				//$stu[$sn][$sco[ss_id]][score]=ceil($sco[ss_score]);
				$stu[$sn][$sco[ss_id]][score]=$sco[ss_score];
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
				//$stu[$sn][nor][score]=ceil($nor[ss_score]);
				$stu[$sn][nor][score]=$nor[ss_score];
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
</style><?
}

##################  學期下拉式選單函式  ##########################
function sel_year($name,$select_t='') {
	global $CONN ;
	$SQL = "select * from school_day where  day<=now() and day_kind='start' order by day desc ";
	$rs=$CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
	while(!$rs->EOF){
	$ro = $rs->FetchNextObject(false);
	// 亦可$ro=$rs->FetchNextObj()，但是僅用於新版本的adodb，目前的SFS3不適用
	$year_seme=$ro->year."_".$ro->seme;
	$obj_stu[$year_seme]=$ro->year."學年度第".$ro->seme."學期";
	}
	$str="<select name='$name' onChange=\"location.href='".$_SERVER[PHP_SELF]."?".$name."='+this.options[this.selectedIndex].value;\">\n";
		//$str.="<option value=''>-未選擇-</option>\n";
	foreach($obj_stu as $key=>$val) {
		($key==$select_t) ? $bb=' selected':$bb='';
		$str.= "<option value='$key' $bb>$val</option>\n";
		}
	$str.="</select>";
	return $str;
	}


##################陣列列示函式2##########################
function sel_grade($name,$select_t='',$url='') {
	//名稱,起始值,結束值,選擇值
	global $IS_JHORES;
($IS_JHORES==6) ? $all_grade=array(7=>"一年級",8=>"二年級",9=>"三年級"):$all_grade=array(1=>"一年級",2=>"二年級",3=>"三年級",4=>"四年級",5=>"五年級",6=>"六年級");

$str="<select name='$name' onChange=\"location.href='".$url."'+this.options[this.selectedIndex].value;\">\n";
$str.= "<option value=''>-未選擇-</option>\n";
foreach($all_grade as $key=>$val) {
 ($key==$select_t) ? $bb=' selected':$bb='';
	$str.= "<option value='$key' $bb>$val</option>\n";
	}

$str.="</select>";
return $str;
 }
###########################################################
##  傳入年級,學年度,學期 預設值為all表示將傳出所有年級與班級
##  傳出以  class_id  為索引的陣列  
function get_class_info1($grade='all',$year_seme='') {
	global $CONN ;
if ($year_seme=='') {
	$curr_year=curr_year(); $curr_seme=curr_seme();}
else {
	$CID=split("_",$year_seme);//093_1
	$curr_year=$CID[0]; $curr_seme=$CID[1];}
	($grade=='all') ? $ADD_SQL='':$ADD_SQL=" and c_year='$grade'  ";
	$SQL="select class_id,c_name,teacher_1 from  school_class where year='$curr_year' and semester='$curr_seme' and enable=1  $ADD_SQL order by class_id  ";
	$rs=$CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
	if ($rs->RecordCount()==0) return"尚未設定班級資料！";
	$obj_stu=$rs->GetArray();
	return $obj_stu;
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









?>