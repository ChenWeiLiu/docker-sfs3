<?php

//載入設定檔
require("config.php");

// 認證檢查
sfs_check();
($IS_JHORES==0) ? $UP_YEAR=6:$UP_YEAR=9;//判斷國中小
//取得子選單
if ($_GET[smenu]=='') header("Location:$_SERVER[PHP_SELF]?smenu=grad");

// 2.判斷學年度,只取年度即可
($_GET[year_seme]=='') ? $year_seme=curr_year():$year_seme=$_GET[year_seme];
($_GET[smenu]=='') ? $smenu="grad" : $smenu=$_GET[smenu];

//取得該年度畢業班升學學校//
$temp_grade = get_grade_school_table($year_seme);
//加入key
foreach($temp_grade as $name_1){	$daa[$name_1]=$name_1;	}
$temp_grade=$daa;

head("畢業生報名輸出");//避開win保留字
print_menu($menu_p);


//子選單陣列
$menu2=array(
"grad"=>"畢業生名冊(A格式)",
"grad2"=>"畢業生名冊(B格式)",
"school"=>"升學學校名冊(逐班)",
"school2"=>"升學學校名冊(逐校)"
);


// 1.smarty物件設定
$template_dir = $SFS_PATH."/".get_store_path()."/templates/";
$smarty->left_delimiter="{{";
$smarty->right_delimiter="}}";


//css樣式
$smarty->assign("css",myheader());
//功能子選單
$smarty->assign("menu",$menu2);
//選擇的子選單
$smarty->assign("smenu",$smenu);



// 指派下拉式選擇年度
$smarty->assign("sel_year",sel_year("year_seme",$year_seme));
//指派年度
$smarty->assign("year_seme",$year_seme);



//print_r($temp_grade);


///各班列表處理
if($year_seme!='' ){
//echo "OK";
	//六年級班級資料
	$all_class_array=get_class_info1( $UP_YEAR,$year_seme);
	//班級計數
	$num=count($all_class_array);
	//班級計行
	$num_max=(ceil($num/10))*10;
	$prt_ary=array();
	//跑迴圈將不足一行的顯示資料補足,求示美觀
	for($i=0;$i<$num_max;$i++){
		if($all_class_array[$i][class_id]!='') { 
			$prt_ary[$i][class_id]=$all_class_array[$i][class_id];
			$prt_ary[$i][c_name]="<TD width=10%><LABEL><INPUT TYPE='checkbox' NAME='class_id[".$all_class_array[$i][class_id]."]' >".$all_class_array[$i][c_name]."班</LABEL></TD>\n";
		}else {
			$prt_ary[$i][class_id]="";
			$prt_ary[$i][c_name]="<TD width=10%>&nbsp;</TD>";
			}
	}

	if ($_GET[smenu]=='school')	$smarty->assign("school",$temp_grade);
	if ($_GET[smenu]=='school2')	$smarty->assign("school2",$temp_grade);	
	$smarty->assign("phpself",$_SERVER[PHP_SELF]);
	$smarty->assign("sel_class",$prt_ary);
//	$smarty->assign("click_button",$click_button);


	}//end if 

$smarty->display($template_dir."stud_grad_list.htm");

//佈景結尾
foot();





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
	global $CONN ;
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
	$str="<select name='$name' onChange=\"location.href='".$_SERVER[PHP_SELF]."?smenu=".$_GET[smenu]."&".$name."='+this.options[this.selectedIndex].value;\">\n";
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
?>