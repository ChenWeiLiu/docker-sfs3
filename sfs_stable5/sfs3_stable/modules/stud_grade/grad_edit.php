<?php
// $Id: grad_edit.php 8032 2014-05-15 08:27:19Z chiming $
//載入設定檔
require("config.php");
// 認證檢查
sfs_check();
#############--------SQL處理區 ----------######################## 
//----新增單筆--------------------///
if ($_POST[act]=='ADD_ONE'){
	if ($_POST[one_stud_id]=='') backe("請輸入學號");
	$seme_year_seme=sprintf("%03d",$_POST[Syear])."2";
	$SQL="select a.stud_id,a.student_sn,b.seme_class from stud_base a ,stud_seme b where  	a.stud_id='$_POST[one_stud_id]' and a.student_sn=b.student_sn and b.seme_year_seme='$seme_year_seme' 	 ";
	$rs=$CONN->Execute($SQL) or die("無法查詢，語法:".$SQL); 
	if ($rs->RecordCount()==0) backe("Ｘ俺查不到該生資料！");
	$obj_stu=$rs->GetArray();
	$obj_stu=$obj_stu[0];
	$class_year=substr($obj_stu[seme_class],0,1);
	$class_sort=substr($obj_stu[seme_class],1,2)+0;
	//103.05.15 fix 加入SN
	$SQL="insert into grad_stud (stud_grad_year,class_year,class_sort,stud_id,grad_date,grad_word,grad_num,new_school,student_sn) values 	('$_POST[Syear]','$class_year','$class_sort','$_POST[one_stud_id]','$_POST[one_date]','$_POST[one_word]','$_POST[one_num]','$_POST[one_school]','{$obj_stu['student_sn']}')";
	$rs=$CONN->Execute($SQL) or die("無法查詢，語法:".$SQL); 
	$URL=$_SERVER[PHP_SELF]."?Page=".$_POST[Page]."&Syear=".$_POST[Syear];
	header("Location:$URL");
}
//----自動依學號填入畢業者--------------------///
if ($_POST[act]=='UP_stud_id'){
	if (strlen($_POST[auto_stid_id]) > 1)  backe("位數過多");
	$SQL="select stud_id from grad_stud where  stud_grad_year ='$_POST[Syear]' and grad_kind='1' order by stud_id ";
	$rs=$CONN->Execute($SQL) or die("無法查詢，語法:".$SQL); 
	if ($rs->RecordCount()==0) backe("Ｘ俺查不到該生資料！");
	$stu=$rs->GetArray();
	$nformat="%0".$_POST[auto_stid_id]."d";
	for ($i=0;$i<count($stu);$i++){
		$new_nu=sprintf($nformat,$i+1);
		$SQL="update  grad_stud set  grad_num ='$new_nu' where stud_id='".$stu[$i][stud_id]."' and  stud_grad_year ='$_POST[Syear]' ";
		$rs=$CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
		}
	$URL=$_SERVER[PHP_SELF]."?Page=".$_POST[Page]."&Syear=".$_POST[Syear];
	header("Location:$URL");
}
//----證書字號與學號一致--------------------///
if ($_POST[act]=='UP_by_stud_id'){
	//echo "<pre>";print_r($_POST);die();
	if ($_POST[Syear]=='')  backe("未傳入學年度！");
	$SQL="update  grad_stud set  grad_num = stud_id  where  stud_grad_year ='$_POST[Syear]' ";
	$rs=$CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
	$URL=$_SERVER[PHP_SELF]."?Page=".$_POST[Page]."&Syear=".$_POST[Syear];
	header("Location:$URL");
}


//----自動依學號填入修業者--------------------///
if ($_POST[act]=='UP_kind_2'){
	if (strlen($_POST[auto_stid_id]) > 1)  backe("位數過多");
	if ($_POST[word_k2]=='')  backe("請輸入修業字");
	$SQL="select stud_id from grad_stud where  stud_grad_year ='$_POST[Syear]' and grad_kind='2' order by stud_id ";
	$rs=$CONN->Execute($SQL) or die("無法查詢，語法:".$SQL); 
	if ($rs->RecordCount()==0) backe("Ｘ查無學生資料！");
	$stu=$rs->GetArray();
	$nformat="%0".$_POST[auto_stid_id]."d";
	for ($i=0;$i<count($stu);$i++){
		$new_nu=sprintf($nformat,$i+1);
		$SQL="update  grad_stud set  grad_num ='$new_nu', grad_word='$_POST[word_k2]'  where stud_id='".$stu[$i][stud_id]."' and  stud_grad_year ='$_POST[Syear]' ";
		$rs=$CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
		}
	$URL=$_SERVER[PHP_SELF]."?Page=".$_POST[Page]."&Syear=".$_POST[Syear];
	header("Location:$URL");
}

//---修改部分---------------------///
if ($_POST[act]=='UP_start'){
	if ($_POST[grad_stud]=='') backe("未選學生");
	if ($_POST[start_grad_num]=='' && $_POST[start_new_school]=='') backe("填寫不完整");
	if ($_POST[start_grad_num]!='' )	$A=1;
	if ( $_POST[start_new_school]!='')	$B=2;
		$C=$A+$B;
		$format=strlen($_POST[start_grad_num]);
		$f1='%0'.$format.'d';
		$start=$_POST[start_grad_num]+0;
		foreach ($_POST[grad_stud] as $sn =>$NULL ){
			$grad_num=sprintf("$f1",$start);
			switch ($C){
			case 1:
				$SQL="update grad_stud set grad_num ='$grad_num' where grad_sn='$sn' ";				
				break;		
			case 2:
				$SQL="update grad_stud set new_school='$_POST[start_new_school]' where grad_sn='$sn' ";
				break;
			case 3:
				$SQL="update grad_stud set  grad_num ='$grad_num', new_school='$_POST[start_new_school]' where grad_sn='$sn' ";				
				break;			
			default:break;			
			}
			$rs=$CONN->Execute($SQL) or die("無法查詢，語法:".$SQL); 						
			$start++;
			}
	$URL=$_SERVER[PHP_SELF]."?Page=".$_POST[Page]."&Syear=".$_POST[Syear];
	header("Location:$URL");
}

//--------更新單筆或全部---------------------//
if ($_POST[act]=='UP_OK'){
	if($_POST[Syear]=='0') die();
	$SQL="update grad_stud set stud_grad_year='$_POST[stud_grad_year]',grad_kind ='$_POST[grad_kind]',grad_date ='$_POST[grad_date]' ,grad_word='$_POST[grad_word]', grad_num='$_POST[grad_num]', new_school='$_POST[new_school]'
	 where grad_sn='$_POST[grad_sn]' ";
	$rs=$CONN->Execute($SQL) or die("無法查詢，語法:".$SQL); 
	if ($_POST[word_all]=='yes' || $_POST[date_all]=='yes'){
		$SQL="update grad_stud set ";
		($_POST[word_all]=='yes') ? $SQL1=" grad_word='$_POST[grad_word]' ":$SQL1='' ;
		($_POST[date_all]=='yes') ? $SQL2=" grad_date='$_POST[grad_date]' ":$SQL2='';
		if ($_POST[word_all]=='yes' && $_POST[date_all]=='yes') {
			$SQL3=$SQL1." , ".$SQL2;}
		else {
			$SQL3=$SQL1.$SQL2;
		}
		$SQL.=$SQL3." where stud_grad_year='$_POST[Syear]' and  grad_kind='1' ";
		$rs=$CONN->Execute($SQL) or die("無法查詢，語法:".$SQL); 
	}
	if ($_POST[kind_all]=='yes'&& $_POST[Syear]==$_POST[stud_grad_year] ){
		$SQL="update grad_stud set grad_kind ='$_POST[grad_kind]' where stud_grad_year='$_POST[Syear]' ";
		$rs=$CONN->Execute($SQL) or die("無法查詢，語法:".$SQL); 
	}
	$URL=$_SERVER[PHP_SELF]."?Page=".$_POST[Page]."&Syear=".$_POST[Syear];
	header("Location:$URL");
}

//------刪除單筆---------------------//
if ($_POST[act]=='DEL_OK'){
	if($_POST[Syear]=='0') die();
	$SQL="delete from  grad_stud where grad_sn='$_POST[grad_sn]' ";
	$rs=$CONN->Execute($SQL) or die("無法刪除，語法:".$SQL); 
	$URL=$_SERVER[PHP_SELF]."?Page=".$_POST[Page]."&Syear=".$_POST[Syear];
	header("Location:$URL");
}

//------刪除全年度---------------------//
if ($_POST[act]=='DEL_ALL_OK'){
	if($_POST[Syear]=='0') die();
	$SQL="delete from  grad_stud where stud_grad_year ='$_POST[Syear]' ";
	$rs=$CONN->Execute($SQL) or die("無法刪除，語法:".$SQL); 
	$URL=$_SERVER[PHP_SELF];
	header("Location:$URL");
}

#############--------SQL處理區結束 ----------########################
//每頁顯示筆數
$page_size=30;

head("畢業生報名輸出");
print_menu($menu_p);
// 1.smarty物件設定
$template_dir = $SFS_PATH."/".get_store_path()."/templates/";
$smarty->left_delimiter="{{";
$smarty->right_delimiter="}}";

//指派成績的連結
$view_course_url=$SFS_PATH_HTML."/modules/stud_report/chc_prn_score.php?list_stud_id=";
$smarty->assign("view",$view_course_url);
//css樣式
$smarty->assign("css",myheader());
// 指派下拉式選擇年度
($_GET[Syear]=='') ? $Syear=curr_year():$Syear=$_GET[Syear];
//頁數
($_GET[Page]=='') ? $Page=0:$Page=$_GET[Page];
$smarty->assign("sel_year",sel_year("Syear",$Syear));
//取到選擇的下學期
$seme_year_seme=sprintf("%03d",$Syear)."2";

// echo "<pre>";print_r(count_grad());
$tol_ary=count_grad();
$total=  ceil($tol_ary[$Syear]/$page_size);//總頁數
$page_link='頁:';
for ($i=0;$i<$total;$i++){
($i==$Page) ? $page_link.="<U><b>".($i+1)."</b></U>&nbsp;":$page_link.="<a href='$_SERVER[PHP_SELF]?Page=$i&Syear=$Syear'>".($i+1)."</a>&nbsp;";
}
//送入分頁連結
$smarty->assign("page_link",$page_link);
//送入目前頁數
$smarty->assign("Page",$Page);
//送入選取年度
$smarty->assign("Syear",$Syear);
//送入PHP_SELF
$smarty->assign("phpself",$_SERVER[PHP_SELF]);


//男女生
$SEX=array(1=>"<font color=#0000FF>男</font>",2=>"<font color=#FF0000>女</font>");
$smarty->assign("SEX",$SEX);
$Gkind=array("1"=>"畢業","2"=>"修業","3"=>"留級");
$smarty->assign("Gkind",$Gkind);//
$Gkind1=array("1"=>"畢業","2"=>"<font color=#0000ff>修業</font>","3"=>"<font color=#ff0000>留級</font>");
$smarty->assign("Gkind1",$Gkind1);//


///輸出資料整理 PS:本表不須studend_sn
$SQL="select a.*,b.stud_name,b.student_sn,b.stud_sex,b.stud_birthday,c.seme_class,c.seme_num from  grad_stud a,stud_base b ,stud_seme c  where a.stud_grad_year='$Syear' and a.stud_id=b.stud_id and a.stud_id=c.stud_id  and  c.seme_year_seme='$seme_year_seme' and b.student_sn=c.student_sn  order by  c.seme_class,c.seme_num  limit   ".$Page*$page_size." ,$page_size ";
/*
//本段語法專為額外處理用
$SQL="select a.*,b.stud_name,b.stud_sex,b.stud_birthday,c.seme_num,MAX(c.seme_class) as seme_class from  grad_stud a,stud_base b
, stud_seme c  where a.stud_grad_year='$Syear' and a.stud_id=b.stud_id and a.stud_id=c.stud_id 
GROUP BY a.stud_id order by   c.seme_class,c.seme_num  limit   ".$Page*$page_size." ,$page_size ";
*/ 
$rs =$CONN->Execute($SQL) or user_error("讀取失敗！<br>$SQL",256) ;
$obj_stu=$rs->GetArray();//echo $SQL;//echo "<pre>";print_r($obj_stu);
//轉換生日顯示
for($i=0;$i<count($obj_stu);$i++){
	$bir=split("-",$obj_stu[$i][stud_birthday]);
	$obj_stu[$i][birth]=($bir[0]-1911)."-".$bir[1]."-".$bir[2];
	}
//送入顯示的學生資料陣列
$smarty->assign("stu",$obj_stu);

//取學校班級名稱陣列使用sfs_case_dataarray.php內的class_base函式
$class_ary=class_base(sprintf("%03d",$Syear)."2");
$smarty->assign("class_base",$class_ary);




//顯示畫面
$smarty->display($template_dir."stud_grad_edit.htm");

//佈景結尾
foot();

##################計算畢業生數量函式##########################
function count_grad(){
	global $CONN ;
	$SQL="select stud_grad_year,count(*) as tol from  grad_stud group by stud_grad_year ";
	$rs =$CONN->Execute($SQL) or user_error("讀取失敗！<br>$SQL",256) ; 
   while ($rs and $ro=$rs->FetchNextObject(false)) {
		$obj_stu[$ro->stud_grad_year] = $ro->tol;}
	return $obj_stu;

}



##################年級下拉式選單##########################
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
//($_GET[Page]=='') ? $Page=0:$Page=$_GET[Page];	
if ($year_seme=='') $year_seme=curr_year();
//	$CID=split("_",$year_seme);//093_1
//	$curr_year=$CID[0]; $curr_seme=$CID[1];
	$curr_year=$year_seme;
	($grade=='all') ? $ADD_SQL='':$ADD_SQL=" and c_year='$grade'  ";
//	$SQL="select class_id,c_name,teacher_1 from  school_class where year='$curr_year' and semester='$curr_seme' and enable=1  $ADD_SQL order by class_id  ";
	$SQL="select class_id,c_name,teacher_1 from  school_class where year='$curr_year' and semester='2' and enable=1  $ADD_SQL order by class_id  ";
	$rs=$CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
	if ($rs->RecordCount()==0) return"尚未設定班級資料！";
	$obj_stu=$rs->GetArray();
	return $obj_stu;

}

##################  學期下拉式選單函式  ##########################
function sel_year($name,$select_t='') {
	global $CONN ,$Page;
	$SQL = "select * from school_day where  day<=now() and day_kind='start' and seme='2' order by day desc ";
	$rs=$CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
	while(!$rs->EOF){
	$ro = $rs->FetchNextObject(false);
	// 亦可$ro=$rs->FetchNextObj()，但是僅用於新版本的adodb，目前的SFS3不適用
//	$year_seme=$ro->year."_".$ro->seme;
	$year_seme=$ro->year;
	$obj_stu[$year_seme]=$ro->year."學年度畢業生";
	}
//	print_r($obj_stu);
	$str="<select name='$name' onChange=\"location.href='".$_SERVER[PHP_SELF]."?Page=".$Page."&".$name."='+this.options[this.selectedIndex].value;\">\n";
		//$str.="<option value=''>-未選擇-</option>\n";
	foreach($obj_stu as $key=>$val) {
		($key==$select_t) ? $bb=' selected':$bb='';
		$str.= "<option value='$key' $bb>$val</option>\n";
		}
	$str.="</select>";
	return $str;
	}

#####################   CSS  ###########################
function myheader(){
$str=<<<EOD
<style type="text/css">
.ip12{border-style: solid; border-width: 0px; background-color:#E6ECF0; font-size:12pt;}
.ipmei{border-style: solid; border-width: 0px; background-color:#E6ECF0; font-size:14pt;}
.ipme2{border-style: solid; border-width: 0px; background-color:#E6ECF0; font-size:14pt;color:red;font-family:標楷體 新細明體;}
.ip2{border-style: solid; border-width: 0px; background-color:#E6ECF0; font-size:11pt;color:red;font-family:新細明體 標楷體;}
.ip3{border-style: solid; border-width: 0px; background-color:#E6ECF0; font-size:12pt;color:blue;font-family:新細明體 標楷體;}
.bu1{border-style: groove;border-width:1px: groove;background-color:#CCCCFF;font-size:12px;Padding-left:0 px;Padding-right:0 px;}
.bub{background-color:#FFCCCC;font-size:14pt;}
.bur1{border-style: groove;border-width:1px: groove;background-color:#FFA500;font-size:11px;Padding-left:0 px;Padding-right:0 px;}
.bur2{border-style: groove;border-width:1px: groove;background-color:#FFCCCC;font-size:12px;Padding-left:0 px;Padding-right:0 px;}
.f8{font-size:9pt;color:blue;}
.f9{font-size:9 pt;}
.me{text-decoration:none;color:#009900;font-size:9 pt}
A:link { color: blue }
A:visited { color:blue}
</style>

EOD;

return $str;
}
function backe($st="未填妥!按下後回上頁重填!") {
echo "<BR><BR><BR><CENTER><form>
	<input type='button' name='b1' value='".$st."' onclick=\"history.back()\" style='font-size:16pt;color:red'>
	</form></CENTER>";
	exit;
	}

