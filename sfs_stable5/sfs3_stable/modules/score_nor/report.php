<?php
// $Id: report.php 8066 2014-06-15 09:59:06Z chiming $
require_once("config.php");
include_once "../../include/sfs_case_dataarray.php";

//認證
sfs_check();

if ($_POST[act]=='OK') {
	//以班級陣列取出學生
	if (is_array($_POST[class_id]) ){
		$sn_ary=get_stsn($_POST[class_id]);
	}

	//以學號取出學生
	if ($_POST[list_stud_id]){
		$list_stud_id=$_POST[list_stud_id];
		if (ereg('-',$list_stud_id)==true){
			$aa=split('-',$list_stud_id);//切開字串
			$SQL="select stud_id,student_sn from stud_base where stud_id between '".$aa[0]."' and '".$aa[1]."' order by stud_id";
			//--- 2013-09-25 修正 增加是否為在學生的判斷
			$SQL="select stud_id,student_sn from stud_base where (stud_study_cond>=0  and stud_study_cond<=2) and stud_id between '".$aa[0]."' and '".$aa[1]."' order by stud_id";
			//--- 2013-09-27 修正非在學生的取得
		  if ($_POST[stud_cond]==='OUT'){
		    $SQL="select stud_id,student_sn from stud_base where (stud_study_cond>2) and stud_id between '".$aa[0]."' and '".$aa[1]."' order by stud_id";
		  }
			$rs=$CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
			$All_ss=$rs->GetArray();
			foreach($All_ss as $ss){$sn_ary[]=$ss[student_sn];}
		}else{
			//--- 2013-09-25 修正 增加是否為在學生的判斷
			$SQL="select stud_id,student_sn from stud_base where (stud_study_cond>=0  and stud_study_cond<=2) and stud_id ='$list_stud_id' order by stud_id";
			//--- 2013-09-27 修正非在學生的取得
		  if ($_POST[stud_cond]==='OUT'){
		     $SQL="select stud_id,student_sn from stud_base where (stud_study_cond>2) and stud_id ='$list_stud_id' order by stud_id";
		  }

			$rs=$CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
			$All_ss=$rs->GetArray();
			foreach($All_ss as $ss){$sn_ary[]=$ss[student_sn];}
		}
	}

	$break_page="<P STYLE='page-break-before: always;'>";
	$prn_page = 0;
	$smarty->display("prn_head.tpl");
	$smarty->assign('sex_kind',array("1"=>"男","2"=>"女"));
	$smarty->assign('guar_kind',guardian_relation());
	$smarty->assign('move_kind',study_cond());
	$smarty->assign('reward_arr',array("1"=>"嘉獎一次","2"=>"嘉獎二次","3"=>"小功一次","4"=>"小功二次","5"=>"大功一次","6"=>"大功二次","7"=>"大功三次","-1"=>"警告一次","-2"=>"警告二次","-3"=>"小過一次","-4"=>"小過二次","-5"=>"大過一次","-6"=>"大過二次","-7"=>"大過三次"));

  //簽章檔
  //校長
  if (is_file($UPLOAD_PATH."school/title_img/title_1")){
 		$title_img_1 = "http://".$_SERVER["SERVER_ADDR"].$UPLOAD_URL."school/title_img/title_1";
 		$smarty->assign('title_img_1',$title_img_1);
 	}
  //學務主任
  if (is_file($UPLOAD_PATH."school/title_img/title_3")){
 		$title_img_3 = "http://".$_SERVER["SERVER_ADDR"].$UPLOAD_URL."school/title_img/title_3";
 	  $smarty->assign('title_img_3',$title_img_3);
 	}

	foreach($sn_ary as $student_sn) {	
		
		$student_data=new data_student($student_sn);
		$prn_page++;
		if ($IS_JHORES==0) 
			$seme_width=7;
		else
			$seme_width=15;
		$smarty->assign('break_page',($prn_page>1?$break_page:''));
		$smarty->assign('school_name',$school_long_name);
		$smarty->assign('seme_width',$seme_width);
		$smarty->assign('base',$student_data->base);//基本資料
		$smarty->assign('move_data',$student_data->move);			//所有異動記錄
		$smarty->assign('seme_ary',$student_data->seme);
		$smarty->assign('abs_data',$student_data->abs);
		$smarty->assign('rew_data',$student_data->rew);
		$smarty->assign('rew_record',$student_data->rew_record);
		$smarty->assign('seme_arr2',$student_data->seme_arr2);
		$smarty->assign('club',$student_data->club);					//社團
		$smarty->assign('service',$student_data->service);		//服務學習
		
		$smarty->assign('room_sign',$_POST['room_sign']);
		unset($student_data);
		$smarty->display("prn_nor_record.tpl");
		
	}
} else {

// 判斷學年度
	($_GET[year_seme]=='') ? $year_seme=curr_year()."_".curr_seme():$year_seme=$_GET[year_seme];

// 指派下拉式選擇學期
	$smarty->assign("sel_year",sel_year('year_seme',$year_seme));

// 指派下拉式選擇年級
	$url=$_SERVER[PHP_SELF]."?type=".$_REQUEST[type]."&year_seme=".$year_seme."&grade=";
	$smarty->assign("sel_grade",sel_grade('grade',$_GET[grade],$url));
	$smarty->assign("phpself",$_SERVER[PHP_SELF]);
	$smarty->assign("school_name",$sch_data[sch_cname]);

// 若有選擇班級  指派班級選擇區 ,判斷是否傳值  再列出各班以供選擇 
	if($year_seme!='' && $_GET[grade]!='' ){
		$all_class_array=get_class_info1($_GET[grade],$year_seme);
		$num=count($all_class_array);
		$num_max=(ceil($num/10))*10;
		$prt_ary=array();
		for($i=0;$i<$num_max;$i++){
			if($all_class_array[$i][class_id]!='') { 
				$prt_ary[$i][class_id]=$all_class_array[$i][class_id];
				$prt_ary[$i][c_name]="<TD width=10%><LABEL><INPUT TYPE='checkbox' NAME='class_id[".$all_class_array[$i][class_id]."]' >".$all_class_array[$i][c_name]."班</LABEL></TD>\n";
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
	$form_type[intval($_REQUEST[type])]="checked";
	$smarty->assign("type",$form_type);
	$smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE); 
	$smarty->assign("SFS_PATH_HTML",$SFS_PATH_HTML); 
	$smarty->assign("module_name","綜合表現記錄表"); 
	$smarty->assign("SFS_MENU",$menu_p); 
	$smarty->display("score_nor_report.tpl");
}

//佈景結尾
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

##################  學期下拉式選單函式  ##########################
function sel_year($name,$select_t='') {
	global $CONN,$_REQUEST ;
	$SQL = "select * from school_day where  day<=now() and day_kind='start' order by day desc ";
	$rs=$CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
	while(!$rs->EOF){
	$ro = $rs->FetchNextObject(false);
	// 亦可$ro=$rs->FetchNextObj()，但是僅用於新版本的adodb，目前的SFS3不適用
	$year_seme=$ro->year."_".$ro->seme;
	$obj_stu[$year_seme]=$ro->year."學年度第".$ro->seme."學期";
	}
	$str="<select name='$name' onChange=\"location.href='".$_SERVER[PHP_SELF]."?type=".$_REQUEST[type]."&".$name."='+this.options[this.selectedIndex].value;\">\n";
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

##################取得學生流水號函式##########################
function get_stsn($class_id){
	global $CONN;

	$st_sn=array();
	foreach($class_id as $key=>$data){
		$class_ids=split("_",$key);
		$seme=$class_ids[0].$class_ids[1];
		$the_class=($class_ids[2]+0).$class_ids[3];
		$SQL="select student_sn from stud_seme where seme_year_seme='$seme' and seme_class='$the_class' order by seme_num";
		$rs = $CONN->Execute($SQL);
		$the_sn=$rs->GetArray();
		for ($i=0;$i<$rs->RecordCount();$i++){
			array_push($st_sn,$the_sn[$i][student_sn]);
		}
	}
	return $st_sn;
}
?>
