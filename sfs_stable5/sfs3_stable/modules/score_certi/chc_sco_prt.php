<?php


//預設的引入檔，不可移除。
include "config.php";




sfs_check();

head("成績証明通用版");
print_menu($menu_p);


// smarty的一些設定  -----------------------------------
$template_dir = $SFS_PATH."/".get_store_path()."/templates";
//  所有的樣本檔   ------------------------------------
$tpl_file=$template_dir."/elps.htm";
$smarty->left_delimiter="{{";
$smarty->right_delimiter="}}";


/////  以get方式取得 學年學期選項 ------------------
($_GET[Seme]!='') ? $Seme=$_GET[Seme]:$Seme=sprintf("%03d",curr_year()).curr_seme();

/////  以get方式取得 班級選項 ------------------
if($_GET[Sclass]!='') $Sclass=$_GET[Sclass];


/////  若有得到班級選項 則將值帶入選單中 使其 selected ------------------
($Sclass) ? $LINK=link_a($Seme,$Sclass): $LINK=link_a($Seme);

/////  設定一個陣列,將性別轉為圖檔 ------------------
$SEX=array(1=>"<img src=images/boy.gif height=25>",2=>"<img src=images/girl.gif height=25>");


/////    從SFS3內建的函式取得學籍資料代碼  -------------------
$stud_coud=study_cond();
//////  從SFS3內建的函式取學校資料函式---------------------
$sch_data=get_school_base();


///// 印出年班級下拉選單 ------------------------------------
echo "
<TABLE border=0 width=100% style='font-size:11pt;'  cellspacing=1 cellpadding=0 bgcolor=#9EBCDD>
<TR bgcolor=#9EBCDD><FORM name=p2><TD  nowrap> $LINK
&nbsp;查詢的學年度&nbsp;<INPUT TYPE='text' NAME='Seme' value='$Seme' size=6 class=ipmei>
<INPUT TYPE='submit' value='返回'>
</TD></TR></FORM></TABLE>";

///// 切開由GET傳入的(學期_年班)變數,以取得年班值 ------------------------------------
	$Class=split("_",$Sclass);

///// 當學期及年班資料都有時,即執行sql以取得該學期所有學生資料-----------------------
if ($Sclass && $Seme){
	$SQL="select a.student_sn,a.stud_id,a.stud_name,a.stud_sex,a.stud_study_cond,b.seme_num from stud_base a,stud_seme b where a.student_sn=b.student_sn and b.seme_year_seme ='$Seme' and b.seme_class='".$Class[1]."' order by b.seme_num ";
	$rs=$CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
	$All_stu=$rs->GetArray();
	}

///  若選擇學生,則進行學生資料擷取  ------------------------------------
if($_GET[st_sn]){
	$stud_data=new data_stud($_GET[st_sn]);
	$smarty->assign("stu", $stud_data);
	$Seme_arry=array();
	for ($i=0;$i<count($stud_data->class_detail);$i++){
		$Seme_arry[$i][all]=$stud_data->class_detail[$i];
		$aa=split("_",$stud_data->class_detail[$i]);
		$Seme_arry[$i][year]=$aa[0];
		$Seme_arry[$i][seme]=$aa[1];
		}
	$smarty->assign("stu_seme",$Seme_arry);
	}


// smarty 指派變數處理  -----------------------------------
$smarty->assign("stud_coud",$stud_coud);
$smarty->assign("data",$All_stu);
$smarty->assign("SEX",$SEX);
$smarty->display($tpl_file);
foot();


?>
