<?php

// $Id: name_form2.php 7694 2013-10-23 08:03:46Z smallduh $

/*引入學務系統設定檔*/
include "../../include/config.php";
require_once "./module-cfg.php";
include_once "../../include/sfs_case_PLlib.php";
//引入函數
//include "./my_fun.php";
$sex_ch= array(1=>"男" ,2=>"女") ;

$sel_year = $_POST['sel_year'] ;		
if ($sel_year=="") $sel_year ="601" ;
$class_name_arr = class_base() ;
$class_name[0]= $sel_year ;
$class_name[1]= $class_name_arr[$sel_year] ;

$allyear = $_POST['allyear'] ;

		$sel1 = new drop_select(); //選單類別
		$sel1->s_name = "sel_year"; //選單名稱		
		$sel1->id = $sel_year;		
		$sel1->has_empty = false;
		$sel1->arr = $class_name_arr ; //內容陣列(六個學年)
		$sel1->is_submit = true;
		$sel1->bgcolor = "#DDFFEE";
		$sel1->font_style ="font-size: 15px;font-weight: bold";
		$class_select = "班級:" . $sel1->get_select();

		

//使用者認證
sfs_check();
if(empty($sel_year))$sel_year = curr_year(); //目前學年
if(empty($sel_seme))$sel_seme = curr_seme(); //目前學期
$teacher_sn=$_SESSION['session_tea_sn'];//取得登入老師的id

$many_col=$_POST['many_col'];
$print_key = $_POST[print_key];

if($_POST['Submit1']=="下載班級名條") {
	$act = $print_key ;
}	

    $seme_year_seme=sprintf("%03d",curr_year()).curr_seme();
    $sql="select stud_id,seme_num from stud_seme where seme_class='$class_name[0]' and  seme_year_seme='$seme_year_seme' order by  seme_num";
    $rs=$CONN->Execute($sql);

    $m=0;
	while(!$rs->EOF){
	  unset($tmp) ;	
	  $stud_id = $rs->fields["stud_id"];	
          $tmp[stud_id] = $stud_id;
          $tmp[site_num] = $rs->fields["seme_num"];
          $rs_name=$CONN->Execute("select stud_name , stud_sex  from stud_base where stud_id='$stud_id' and stud_study_cond =0 ");
    
        if ($rs_name->fields["stud_name"]) {

           $tmp[stud_name] = $rs_name->fields["stud_name"];	
           $tmp[stud_sex] = $sex_ch[$rs_name->fields["stud_sex"]];	
           $data_array[]= $tmp ;
	}
        $rs->MoveNext();
    }

  if ($act and ($act<>'html')) {
    $filename =  $class_name[0]. "." . $act; 	
    if ($act == "sxc") {
       header("Content-disposition: attachment; filename=$filename");
	     header("Content-type: application/vnd.sun.xml.calc");    	
	  }else {
	     header("Content-disposition: filename=$filename");
	     header("Content-type: application/octetstream");	  
	  }   
    //header("Pragma: no-cache");
				//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");

	  header("Expires: 0"); 
  }
  
  
    //使用樣版
    $template_dir = $SFS_PATH."/".get_store_path()."/templates";

    // 使用 smarty tag
    $smarty->left_delimiter="{{";
    $smarty->right_delimiter="}}";
    //$smarty->debugging = true;
    
    $smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE);
    
    $smarty->assign("with_stud_id",$_POST['with_stud_id']); 
    $smarty->assign("act",$act); 
    $smarty->assign("data_array",$data_array); 
    $smarty->assign("class_select",$class_select); 
    $smarty->assign("import_option",$import_option);     

    $smarty->assign("many_col",$_POST[many_col]); 
      
    $smarty->assign("class_id",$class_name[0]); 
    $smarty->assign("data_class_name",$class_name[1]); 
    
    $smarty->assign("template_dir",$template_dir);
    
    $smarty->display("$template_dir/name.htm");
    

?>
