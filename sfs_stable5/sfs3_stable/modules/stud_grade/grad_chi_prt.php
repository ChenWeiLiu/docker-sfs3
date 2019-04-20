<?php

if ($_POST[class_id]=='') die();
if ($_POST[smenu]=='') die();


//print_r($_POST);die();
//載入設定
require("config.php") ;
include_once "../../include/sfs_case_dataarray.php";
// 認證檢查
sfs_check();
($IS_JHORES==0) ? $UP_YEAR=6:$UP_YEAR=9;//判斷國中小
//////  smarty的設定---------------------
$template_dir = $SFS_PATH."/".get_store_path()."/templates/";
$smarty->left_delimiter="{{";
$smarty->right_delimiter="}}";
$SEX=array(1=>"男",2=>"女");
//////  從SFS3內建的函式取學校資料函式---------------------
$sch_data=get_school_base();
$smarty->assign("school_name",$sch_data[sch_cname]);
//男女生
$smarty->assign("SEX",$SEX);

//取學校班級名稱陣列使用sfs_case_dataarray.php內的class_base函式
$class_ary=class_base(sprintf("%03d",$_POST[year_seme])."2");
//print_r($class_ary);
$smarty->assign("class_base",$class_ary);
//學年度year_seme僅有年例如93,本處將學年度送入smarty
$smarty->assign("year_seme",Num2CNum($_POST[year_seme]));
//換頁符號
$break_page="<P STYLE='page-break-before: always;'>";
//顯示標頭檔
$smarty->display($template_dir."stud_grad_head.htm");
//換頁符號
$break_page="<P STYLE='page-break-before: always;'>";
//頁數
$prn_page = 0;
//全部頁數
$all_class=count($_POST[class_id])-1;

////------處理國中名冊輸出段落-----------///////
if ($_POST[class_id]=='all' && $_POST[smenu]=='school2'){
	//直接指定該學期第2學期,組合後格式為0932,主要於stud_seme查詢
	$seme_year_seme=sprintf("%03d",$_POST[year_seme])."2";
	//$_POST[sel_school]學校名稱,
	$SQL="select a.stud_id,a.student_sn,a.stud_name,a.stud_sex,a.stud_birthday,
	    	a.stud_person_id,a.stud_addr_1,a.stud_tel_1, b.seme_class,c.grad_word,
	    	c.grad_num,c.new_school,d.guardian_name from stud_base as a,stud_seme b,
	    	grad_stud c,stud_domicile as d where   
	    	b.seme_year_seme='$seme_year_seme' and  b.student_sn=a.student_sn 
	    	and b.stud_id=d.stud_id and b.stud_id=c.stud_id 
	    	and  c.stud_grad_year='$_POST[year_seme]' and c.new_school='$_POST[sel_school]' 
	    	order by b.seme_class,b.seme_num ";
//	    	echo $SQL;
   $rs =$CONN->Execute($SQL) or user_error("讀取失敗！<br>$SQL",256) ; 
   while ($rs and $ro=$rs->FetchNextObject(false)) {
   	$ro->stud_sex=$SEX[$ro->stud_sex];
      $bir=split("-",$ro->stud_birthday);
      $obj_stu[$ro->student_sn] = get_object_vars($ro);
      $obj_stu[$ro->student_sn][birth]=Num2CNum(($bir[0]-1911))."年".Num2CNum($bir[1]+0)."月".Num2CNum($bir[2]+0)."日";
      }
   $smarty->assign("grad_school2",$obj_stu);
	$smarty->assign("NewSchool",$_POST[sel_school]);
//	$grad_class=Num2CNum($P_ary[2]+0)."年".Num2CNum($P_ary[3]+0)."班";
//	$smarty->assign("grad_class",$grad_class);//班級名稱
	$smarty->display($template_dir."stud_grad.htm");
//print_r($obj_stu);
	//直接結束
	die();
	}
else{
///---------------處理依班級輸出的段落-------------------///
///$_POST[class_id] 格式為 class_id[093_2_06_01]
foreach ($_POST[class_id] as $class_id =>$NULLnull) {
	$P_ary=split("_",$class_id);
	$curr_class_name=($P_ary[2]+0).sprintf("%02d",$P_ary[3]);
//	echo $curr_class_name;
	$seme_year_seme=sprintf("%03d",$P_ary[0]).sprintf("%d",$P_ary[1]);
	$year_name=$P_ary[0]+0;
	$smarty->assign('break_page',($prn_page > 0 ? $break_page:''));
	$smarty->assign('now_page',($prn_page+1)."(共".$all_class."頁)");
	switch ($_POST[smenu]){
		case "grad":
			$SQL = "select s.student_sn,s.stud_id ,b.seme_class,b.seme_num, s.stud_name ,s.curr_class_num ,s.stud_birthday ,s.stud_sex , 
             g.grad_sn , g.grad_word , grad_num   from stud_base as s,stud_seme b,grad_stud  g where  (s.stud_id=g.stud_id and g.stud_grad_year='$year_name') and s.student_sn=b.student_sn and b.seme_class='$curr_class_name' and b.seme_year_seme='$seme_year_seme' and b.stud_id=g.stud_id order by b.seme_num ";
             $rs =$CONN->Execute($SQL) or user_error("讀取失敗！<br>$SQL",256) ; 
             while ($rs and $ro=$rs->FetchNextObject(false)) {
             	$ro->stud_sex=$SEX[$ro->stud_sex];
             	$bir=split("-",$ro->stud_birthday);
             	$obj_stu[$ro->student_sn] = get_object_vars($ro);
             	$obj_stu[$ro->student_sn][birth]=Num2CNum(($bir[0]-1911))."年".Num2CNum($bir[1]+0)."月".Num2CNum($bir[2]+0)."日";
             	}
             		$smarty->assign("grad_stu",$obj_stu);
      		break;
      case "grad2":
	    	$SQL="select 
	    	a.stud_id,a.student_sn,a.stud_name,a.stud_sex,a.stud_birthday,
	    	 a.stud_person_id,a.stud_addr_1,a.stud_tel_1,b.seme_class,c.grad_word,c.grad_num,d.guardian_name from stud_base as a,stud_seme b, grad_stud c,stud_domicile as d where  b.seme_class='$curr_class_name' and b.seme_year_seme='$seme_year_seme' and  b.student_sn=a.student_sn and b.stud_id=d.stud_id
	    	and b.stud_id=c.stud_id and  c.stud_grad_year='$year_name' order by b.seme_num ";
	    	$rs =$CONN->Execute($SQL) or user_error("讀取失敗！<br>$SQL",256) ; 
	    	while ($rs and $ro=$rs->FetchNextObject(false)) {
	    		$ro->stud_sex=$SEX[$ro->stud_sex];
	    		$bir=split("-",$ro->stud_birthday);
            $obj_stu[$ro->student_sn] = get_object_vars($ro);
             }
      		
			  	$smarty->assign("grad_stu2",$obj_stu);
      		break;
      case "school":
      $SQL="select 
	    	a.stud_id,a.student_sn,a.stud_name,a.stud_sex,a.stud_birthday,
	    	 a.stud_person_id,a.stud_addr_1,a.stud_tel_1,b.seme_class,c.grad_word,c.grad_num,c.new_school,d.guardian_name from stud_base as a,stud_seme b, grad_stud c,stud_domicile as d where  b.seme_class='$curr_class_name' and b.seme_year_seme='$seme_year_seme' and  b.student_sn=a.student_sn and b.stud_id=d.stud_id
	    	and b.stud_id=c.stud_id and  c.stud_grad_year='$year_name' and c.new_school='$_POST[new_sch]' order by b.seme_num ";
	    	$rs =$CONN->Execute($SQL) or user_error("讀取失敗！<br>$SQL",256) ; 
	    	while ($rs and $ro=$rs->FetchNextObject(false)) {
	    		$ro->stud_sex=$SEX[$ro->stud_sex];
	    		$bir=split("-",$ro->stud_birthday);
            $obj_stu[$ro->student_sn] = get_object_vars($ro);
             }
      		
			  	$smarty->assign("grad_school",$obj_stu);
			  	$smarty->assign("NewSchool",$_POST[new_sch]);
      		break;            		
     		default:break;
             	
             		
	}//end switch
	$grad_class=$class_ary[($P_ary[2].$P_ary[3])];
	$smarty->assign("grad_class",$grad_class);//班級名稱
	$smarty->display($template_dir."stud_grad.htm");
	$prn_page++;
	unset($obj_stu);
	unset($P_ary);
}
}
?>