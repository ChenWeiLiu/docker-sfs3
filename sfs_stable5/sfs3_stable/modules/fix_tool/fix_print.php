<?php
//$Id: fix_print.php 5310 2009-01-10 07:57:56Z hami $
require_once("config.php");
require_once("chi_fun.php");
//使用者認證
sfs_check();

if ($_POST[act]=='OK'){
	if ($_POST[year_seme]=='' || $_POST[grade]=='' ||  $_POST[url_class_id]=='') backe("無法執行");
	$SQL = "update score_ss set scope_id='$_POST[scope]',
	subject_id='$_POST[subject]',class_id='$_POST[class_id]',
	class_year='$_POST[class_year]',enable='$_POST[enable]',need_exam='$_POST[need_exam]',rate='$_POST[rate]',
	sort='$_POST[sort]',sub_sort='$_POST[sub_sort]',print='".$_POST["print"]."',link_ss='$_POST[link_ss]'  where ss_id='$_POST[ss_id]'";
	$rs=$CONN->Execute($SQL) or die("無法執行，語法:".$SQL);
	$URL=$_SERVER[PHP_SELF]."?year_seme=".$_POST[year_seme]."&grade=".$_POST[grade]."&class_id=".$_POST[url_class_id];
	header("Location:$URL");
}





head("課程修正");
print_menu($school_menu_p);

##################陣列列示函式2##########################
// 1.smarty物件
	$template_dir = $SFS_PATH."/".get_store_path()."/templates/";
	$smarty->left_delimiter="{{";
	$smarty->right_delimiter="}}";

// 2.判斷學年度
	($_GET[year_seme]=='') ? $year_seme=curr_year()."_".curr_seme():$year_seme=$_GET[year_seme];

// 3.指派下拉式選擇學期
	$smarty->assign("sel_year",sel_year('year_seme',$year_seme));//學年度選單

// 4.指派下拉式選擇年級
	$url=$_SERVER[PHP_SELF]."?year_seme=".$year_seme."&grade=";
	$smarty->assign("sel_grade",sel_grade('grade',$_GET[grade],$url));//年級選單

// 5.若有選擇班級  指派班級選擇區 ,判斷是否傳值  再列出各班以供選擇 
if($year_seme!='' && $_GET[grade]!='' ){
	$all_class_array=get_class_info1($_GET[grade],$year_seme);
	$num=count($all_class_array);
	$num_max=(ceil($num/10))*10;
	$prt_ary=array();
	for($i=0;$i<$num_max;$i++){
		if($all_class_array[$i][class_id]!='') { 
			$class_word=($all_class_array[$i][c_name]=="全年級") ? "":"班";
			$prt_ary[$i][class_id]=$all_class_array[$i][class_id];
			$bgcolor=($_GET[class_id]==$all_class_array[$i][class_id]) ? "bgcolor=#FFEBD6":"";
			$prt_ary[$i][c_name]="<TD width=10% $bgcolor><LABEL><INPUT TYPE='checkbox' NAME='class_id[]' ";
			$prt_ary[$i][c_name].=" value='".$all_class_array[$i][class_id]."' ";
			$prt_ary[$i][c_name].="onclick='jamp(this.value);' >";
			$prt_ary[$i][c_name].=$all_class_array[$i][c_name].$class_word."</LABEL></TD>\n";
			//".$all_class_array[$i][class_id]."]'
		}else {
			$prt_ary[$i][class_id]="";
			$prt_ary[$i][c_name]="<TD width=10%>&nbsp;</TD>";
			}
	}

	$smarty->assign("sel_class",$prt_ary);
	}//end if 
	else {
	$smarty->assign("sel_class","<CENTER>使用方式：先選學期，再選年級！</CENTER>");
	}

##################列示課程代碼##########################
if ($_GET[class_id]!=''){
	$smarty->assign("PHP_SELF",$_SERVER[PHP_SELF]);
	$ss_ary=get_subj2($_GET[class_id]);//取得課程資料score_ss
	$scope_name=get_subj3("scope");//取領域名
	$subj_name=get_subj3("subject");//取科目名

	$smarty->assign("ss_ary",$ss_ary);//送入課程資料score_ss
	$smarty->assign("scope",$scope_name);//送入領域名
	$smarty->assign("subj",$subj_name);//送入科目名
	$smarty->assign("myheader",myheader());//送入CSS
	}

$smarty->display("$template_dir/fix_print.htm");


foot();

?>
