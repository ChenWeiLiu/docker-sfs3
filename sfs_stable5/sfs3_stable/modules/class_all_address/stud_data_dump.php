<?php

// $Id: stud_data_dump.php 7704 2013-10-23 08:51:29Z smallduh $

/*引入學務系統設定檔*/
include "../../include/config.php";
include_once "../../include/sfs_case_PLlib.php";
include_once "../../include/sfs_case_dataarray.php";

//引入函數
include "./my_fun.php";
require_once "./module-cfg.php";

//使用者認證
sfs_check();


if(empty($sel_year))$sel_year = curr_year(); //目前學年
if(empty($sel_seme))$sel_seme = curr_seme(); //目前學期

$sel_year = $_POST['sel_year'] ;
$allyear = $_POST['allyear'] ;
$print_key = $_POST[print_key];


if ($sel_year=="") $sel_year ="601" ;


$class_name_arr = class_base() ;



if($_POST['Submit1'] == "下載班級資料"){
  $act = "列印" ;
  if ($allyear) {
    foreach ($class_name_arr as $class_id =>$class_name ) {
      if (substr($class_id,0,1) == substr($sel_year,0,1) )
        $data_array[$class_id] = get_class_data($class_id) ;  
    }
  }else {
    $data_array[$sel_year] = get_class_data($sel_year) ;  
  }    
}

/*  無法全校下載，可能佔記憶體過大
if($_POST[Submit1] == "全校"){
  $act = "列印" ;
set_time_limit(40) ; 
  foreach ($class_name_arr as $class_id =>$class_name ) {
    $data_array[$class_id] = &get_class_data($class_id) ;  
 
  }

}
*/


if ($act) {
  //下載
  if ($print_key=='sxw' ) {
     //使用工作表呈現
     $filename =  "data_dump.sxc";
     header("Content-disposition: attachment; filename=$filename");
     header("Content-type: application/vnd.sun.xml.calc");
     //header("Pragma: no-cache");
     				//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");

     header("Expires: 0");     
  }else {
     //不適合 word 呈現
       $filename =  "data_dump.xls";


    header("Content-disposition: filename=$filename");
	  header("Content-type: application/octetstream ; Charset=Big5");
	  //header("Pragma: no-cache");
	  				//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");

	  header("Expires: 0");    
  }  
    
}else {
    $data_array[$sel_year] = get_class_data($sel_year) ;  
}    

    $sel1 = new drop_select(); //選單類別
    $sel1->s_name = "sel_year"; //選單名稱		
    $sel1->id = $sel_year;		
    $sel1->has_empty = false;
    $sel1->arr = $class_name_arr ; //內容陣列(六個學年)
    $sel1->is_submit = true;
    $sel1->bgcolor = "#DDFFEE";
    $sel1->font_style ="font-size: 15px;font-weight: bold";
    $class_select =  $sel1->get_select();
    
    
    
    //取得學生資料欄位中文名
    $sql = " select d_field_name , d_field_cname from sys_data_field where d_table_name = 'stud_base' and d_field_cname <>'' " ;
    $rs=$CONN->Execute($sql);
    while(!$rs->EOF){
    	 $d_field_name =$rs->fields["d_field_name"]  ;
    	 $fields[$d_field_name] =$rs->fields["d_field_cname"]  ;
    	$rs->MoveNext();
    }	
    
    //取得戶口資料欄位中文名
    $sql = " select d_field_name , d_field_cname from sys_data_field where d_table_name = 'stud_domicile'  and d_field_cname <>''  " ;
    $rs=$CONN->Execute($sql);
    while(!$rs->EOF){
    	 $d_field_name =$rs->fields["d_field_name"]  ;
    	 $fields[$d_field_name] =$rs->fields["d_field_cname"]  ;
    	$rs->MoveNext();
    }	
    

    
        
    //使用樣版
    $template_dir = $SFS_PATH."/".get_store_path()."/templates";
    // 使用 smarty tag
    $smarty->left_delimiter="{{";
    $smarty->right_delimiter="}}";
    //$smarty->debugging = true;

    $smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE);
    $smarty->assign("SFS_MENU",$menu_p);     
    
    $smarty->assign("act",$act);     
    $smarty->assign("class_select",$class_select); 
    $smarty->assign("import_option",$import_option); 
    
    $smarty->assign("class_name_arr",$class_name_arr); 
    
    $smarty->assign("fields",$fields); 
    
    $smarty->assign("data_array",$data_array); 
    
    
    $smarty->assign("template_dir",$template_dir);
    
    $smarty->display("$template_dir/data_dump.htm");
    

//取得資料陣列
function get_class_data($class_id ="" ){
    global $CONN,  $class_name_arr  ;
  	
  	$sex_arr= array(1=>"男" ,2 =>"女") ;
  	
    $classid_search =  " = '$class_id' ";
    

    $seme_year_seme=sprintf("%03d",curr_year()).curr_seme();
    
    //班名
    $class_name = $class_name_arr[$class_id] ;
    
    //班級性質
    $stud_class_kind = stud_class_kind() ;
    $stud_spe_kind = stud_spe_kind() ;
    $stud_spe_class_kind = stud_spe_class_kind() ;
    $stud_preschool_status = stud_preschool_status() ;
    $blood = blood() ;
    $birth_state = birth_state() ;
    $edu_kind = edu_kind() ;        
    $guar_kind = guar_kind() ;  
    $fath_relation = fath_relation() ;  
    $is_live = is_live() ;  
    $moth_relation = moth_relation() ;  
    $stud_spe_class_id = stud_spe_class_id() ; 
    $stud_kind =stud_kind() ;
    $stud_country_kind=stud_country_kind() ;
    
/*
    $pos = strpos($class_name, "忠");
    if  ($pos === false) {
       //原班不排除忠班
       $sql="select s.stud_id,s.seme_num ,seme_class from stud_seme s , stud_base b where s.stud_id=b.stud_id and  b.stud_study_cond =0 and s.seme_class $classid_search and  s.seme_year_seme='$seme_year_seme' order by  s.seme_class ,s.seme_num";
       
    }else {
       //忠班
       $class_y = substr($class_id,0,1) ;
       $sql="select s.stud_id, b.spe_sit_num as seme_num , '$class_id' as seme_class from stud_seme s , stud_base b where s.stud_id=b.stud_id and  b.stud_study_cond =0 and  b.stud_class_kind='1' and b.stud_spe_kind='2' and s.seme_class like '$class_y%' and  s.seme_year_seme='$seme_year_seme' order by  b.spe_sit_num ";       
       //$sql = "select a.stud_id,a.stud_name , a.spe_sit_num as sit_num from stud_base a,stud_seme b where a.stud_id=b.stud_id  and a.stud_study_cond=0  and  a.stud_class_kind='1' and a.stud_spe_kind='2' and  b.seme_year_seme='$seme_year_seme' and b.seme_class like '$class_y%' order by  a.spe_sit_num";	
    }       	
*/
    $sql="select s.student_sn, s.seme_num, seme_class from stud_seme s, stud_base b where s.student_sn=b.student_sn and b.stud_study_cond =0 and s.seme_class = '$class_id' and s.seme_year_seme='$seme_year_seme' order by s.seme_class ,s.seme_num";

    $rs=$CONN->Execute($sql);


    while(!$rs->EOF){
        unset($row_data) ;    
	$student_sn = $rs->fields["student_sn"];
 
        //個人資料
        //$CONN->SetFetchMode(ADODB_FETCH_ASSOC);
        $rs_name=$CONN->Execute("select b.* ,d.* from stud_base b, stud_domicile d where b.student_sn='$student_sn' and d.student_sn=b.student_sn");
        
        $row_data = $rs_name->FetchRow() ;

        //資料轉換 
        $row_data[stud_sex]= $sex_arr[$row_data[stud_sex]] ;
        $row_data[stud_class_kind]= $stud_class_kind[$row_data[stud_class_kind]] ;
        $row_data[stud_spe_kind]= $stud_spe_kind[$row_data[stud_spe_kind]] ;
        $row_data[stud_spe_class_kind]= $stud_spe_class_kind[$row_data[stud_spe_class_kind]] ;        
        $row_data[stud_preschool_status]= $stud_preschool_status[$row_data[stud_preschool_status]] ;
        $row_data[stud_blood_type]= $blood[$row_data[stud_blood_type]] ;
        $row_data[stud_birth_place]= $birth_state[$row_data[stud_birth_place]] ;
        $row_data[fath_education]= $edu_kind[$row_data[fath_education]] ;
        $row_data[moth_education]= $edu_kind[$row_data[moth_education]] ;
        $row_data[guardian_relation]= $guar_kind[$row_data[guardian_relation]] ;
        $row_data[fath_relation]= $fath_relation[$row_data[fath_relation]] ;
        $row_data[grandfath_alive]= $is_live[$row_data[grandfath_alive]] ;
        $row_data[fath_alive]= $is_live[$row_data[fath_alive]] ;
        $row_data[moth_alive]= $is_live[$row_data[moth_alive]] ;
        $row_data[grandmoth_alive]= $is_live[$row_data[grandmoth_alive]] ;
        $row_data[moth_relation]= $moth_relation[$row_data[moth_relation]] ;
        $row_data[stud_spe_class_id]= $stud_spe_class_id[$row_data[stud_spe_class_id]] ;
        $row_data[stud_country_kind]= $stud_country_kind[$row_data[stud_country_kind]] ;
        
        $stud_kind_list=split(',' ,$row_data[stud_kind] ) ;
        $stud_kind_str ='' ;
        foreach ($stud_kind_list as $k => $v ) {
          if ($v<>'') 
              $stud_kind_str .= $stud_kind[$v] . ',' ;
        }     
        $row_data[stud_kind] = $stud_kind_str ;
        
        $data[] = $row_data ; 
        $rs->MoveNext();
    }
  return $data ;

}
?>
