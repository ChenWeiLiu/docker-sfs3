<?php

// $Id: name_form2.php 7049 2012-12-26 05:16:27Z smallduh $

/*引入學務系統設定檔*/
include "../../include/config.php";
require_once "./module-cfg.php";
include_once "../../include/sfs_case_PLlib.php";

// print $UPLOAD_PATH; // 檔案上傳位置 
$class_name_arr = class_base() ; //列出全有班級
//選定班級
if($_POST['sel_year']){ $sel_year = $_POST['sel_year']; }
elseif (get_teach_class()){ $sel_year = get_teach_class();} 
else {
	end($class_name_arr);
	$sel_year = key($class_name_arr);   //不是導師就指定最後一個年段
 }

  $class_name[0]= $sel_year ;   //班級代號  ex:401
  $class_name[1]= $class_name_arr[$sel_year] ;  //四年甲班
  $assign_class[$class_name[0]] = $class_name[1] ;
		  $sel1 = new drop_select(); //選單類別
		  $sel1->s_name = "sel_year"; //選單名稱		
		  $sel1->id = $sel_year;		
		  $sel1->has_empty = false;

		  if (get_teach_class()){
			$sel1->arr = $assign_class;
			$sel1->is_submit = false;  //導師
		  }else{
			$sel1->arr = $class_name_arr; //內容陣列(六個學年)
			$sel1->is_submit = true;  //註冊組
		  }

		  $sel1->bgcolor = "#DDFFEE";
		  $sel1->font_style ="font-size: 15px;font-weight: bold";
		  $class_select = "班級: " . $sel1->get_select();

		

//使用者認證
sfs_check();
//if(empty($sel_year))$sel_year = curr_year(); //目前學年
if(empty($sel_seme))$sel_seme = curr_seme(); //目前學期
$teacher_sn=$_SESSION['session_tea_sn'];//取得登入老師的id

    $seme_year_seme=sprintf("%03d",curr_year()).curr_seme();
    $sql="select stud_id,seme_num from stud_seme where seme_class='$class_name[0]' and  seme_year_seme='$seme_year_seme' order by  seme_num";
    $rs=$CONN->Execute($sql);

    $m=0;
	while(!$rs->EOF){
	  unset($tmp) ;	
	  $stud_id = $rs->fields["stud_id"];	
          $tmp[stud_id] = $stud_id;
          $tmp[site_num] = $rs->fields["seme_num"];
	  $rs->fields["seme_num"];
          $rs_name=$CONN->Execute("select stud_name,stud_name_eng from stud_base where stud_id='$stud_id' and stud_study_cond =0 ");
    
        if ($rs_name->fields["stud_name"]) {

           $tmp[stud_name] = $rs_name->fields["stud_name"];	
           $tmp[stud_name_eng] = $rs_name->fields["stud_name_eng"];	
           $tmp['urlencode_stud_name'] = urlencode($rs_name->fields["stud_name"]);	
           $raw_data[]= $tmp ;
	}
        $rs->MoveNext();
}

//print_r($raw_data);
//print $class_name[1];
//$raw_data['class_name'] = $class_name[0];

$keep_data['class_name'] = urlencode($class_name[1]);
$system_title = '姓名譯音轉換';
    //使用樣版
    $template_dir = $SFS_PATH."/".get_store_path()."/templates";

    //$smarty->assign("template_dir",$template_dir);

    // 使用 smarty tag
    $smarty->left_delimiter="{{";
    $smarty->right_delimiter="}}";
    //$smarty->debugging = true;
    $smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE);
    
    $smarty->assign("system_title",$system_title); 
    $smarty->assign("with_stud_id",$_POST['with_stud_id']); 
    $smarty->assign("act",$act); 
    $smarty->assign("raw_data",$raw_data); 
    $smarty->assign("keep_data",$keep_data); 
    $smarty->assign("class_select",$class_select); 
    $smarty->assign("import_option",$import_option);     

    $smarty->assign("upload_path",$UPLOAD_PATH); 
      
    $smarty->assign("class_id",$class_name[0]); 
    $smarty->assign("data_class_name",$class_name[1]); 
    

    $smarty->display("$template_dir/name.tpl");

    $smarty=new Smarty;// instantiates an object $smarty of class Smarty

?>
