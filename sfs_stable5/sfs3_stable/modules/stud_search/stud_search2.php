<?php
// $Id: stud_search2.php 6848 2012-08-01 01:55:54Z hsiao $

  //載入設定檔
  require("config.php") ;
 
  // 認證檢查
  sfs_check();

  head("搜尋");
  //選單
  print_menu($menu_p);

  //座號、姓名、生日、地址、電話、家長、家長工作、工作電話
  
  $Submit =$_POST['Submit'];
  $searchname =$_POST['searchname'];
  $search_id =$_POST['search_id'];
  $search_p_id =$_POST['search_p_id'];
  $search_f_name =$_POST['search_f_name'];
  $search_phon =$_POST['search_phon'];
  $search_town =$_POST['search_town'];
  $search_village =$_POST['search_village'];
  $search_pschool =$_POST['search_pschool'];
  $pschool =($IS_JHORES==0)?"stud_preschool_name":"stud_mschool_name";
  $sql_select = "select s.stud_id, s.student_sn, s.stud_name, s.stud_person_id , s.stud_birthday ,s.stud_tel_1 ,
					  s.stud_study_cond , s.curr_class_num , s.$pschool ,
					  d.guardian_name ,d.fath_name ,d.moth_name 
					  from stud_base as s  LEFT JOIN stud_domicile as d ON s.student_sn=d.student_sn"; 			  
        
  if ($Submit =="送出") {
	
	//個資記錄
	$test=pipa_log("學生搜尋條件\r\n：姓名：$searchname\r\n學號：$search_id\r\n身份證字號：$search_p_id\r\n
	家長或監護人姓名：$search_f_name\r\n電話：$search_phon\r\n地址：$search_town\r\n入學學校：$search_pschool\r\n在學學生：$pschool\r\n");		
	
    if (trim($searchname)<>""){	
        //學生姓名
   	    $searchname= trim($searchname) ;
   	    //echo $searchname ;
        $searchname = addslashes($searchname);

        $sqlstr = " and s.stud_name like '%".($searchname)."%'"  ;

        //echo $sql_select ;
    }     
    elseif (trim($search_id)<>""){	
        //學號
   	    $search_id= trim($search_id) ;
        $sqlstr = " and   s.stud_id like '%$search_id%' "  ;

    }
  elseif (trim($search_p_id)<>""){	
        //身分證號
   	    $search_id= trim(strtolower($search_p_id) );
        $sqlstr = " and    lower(s.stud_person_id ) like '%$search_p_id%' "  ;

    }   
    elseif (trim($search_f_name)<>"") {
        //家長姓名
   	    $search_f_name= trim($search_f_name) ;
        $search_f_name = addslashes($search_f_name);

   	    $sqlstr = " and ( d.guardian_name  like '%" .$search_f_name."%' 
   	                 or  d.fath_name   like '%" .$search_f_name."%' 
   	                or  d.moth_name    like '%" .$search_f_name."%' ) "  ;

        
    }	 
    elseif (trim($search_phon)<>"") {
        //家中電話
      
   	    $search_phon= trim($search_phon) ;
   	    $sqlstr = " and ( s.stud_tel_1   ='$search_phon' 
   	            or  s.stud_tel_2   ='$search_phon'  
   	            or  s.stud_tel_3  ='$search_phon' ) "  ;

    }	     
    elseif (trim($search_town)<>"") {
        //地址
        $search_town = trim($search_town) ;
        $search_town = addslashes($search_town);
        if ($search_town)  
           $sqlstr =  " and  ( s.stud_addr_1   like '%$search_town%' or s.stud_addr_2   like '%$search_town%' ) " ;

    }
    elseif ($search_pschool) {
        //入學前學校
        $search_pschool = trim($search_pschool) ;
        $search_pschool = addslashes($search_pschool);
        if ($search_pschool)  
           $sqlstr =  " and s.$pschool like '%$search_pschool%'" ;

    }
    

  }	
   if ($sqlstr) {
   	$sqlstr = substr($sqlstr ,4) ; //去除 and
   	//在學生===================================================
    $sql_select_in = " $sql_select  where $sqlstr  and  stud_study_cond = 0   order by  s.curr_class_num ";

    $data_array_in = get_stud_data($sql_select_in) ;
    $have_in = 1 ;

    //非在學生===================================================  
    if ($_POST[chk_view_old] == 1) {
    	 $sql_select_out = " $sql_select  where $sqlstr  and  stud_study_cond <> 0   order by s.stud_id DESC ";
    	 $data_array_out = get_stud_data($sql_select_out) ;
    	 $have_out = 1 ;
    }	
   }

//使用樣版
$template_dir = $SFS_PATH."/".get_store_path()."/templates";
// 使用 smarty tag
$smarty->left_delimiter="{{";
$smarty->right_delimiter="}}";
//$smarty->debugging = true;


$smarty->assign("data_array_in",$data_array_in); 
$smarty->assign("data_array_out",$data_array_out); 
//是否有資料
$smarty->assign("have_in",$have_in);
$smarty->assign("have_out",$have_out); 

$smarty->assign("template_dir",$template_dir);

//是否為模組管理員
$smarty->assign("is_man",checkid($_SERVER['SCRIPT_FILENAME'],1));

$smarty->display("$template_dir/search.htm");

foot();





function get_stud_data($sql_select)  {
	global $CONN ; 
	$class_year_p = class_base(); //班級
	$c_curr_seme = sprintf ("%03s%s",curr_year(),curr_seme()); //現在學年學期
       $result = $CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256) ; 

       while ($row =  $result->FetchRow() ) {
       	  unset($tmp_row) ;
       	  $student_sn = $row["student_sn"]; 
       	  $stud_id = $row["stud_id"]; 
        	if(checkid($_SERVER['SCRIPT_FILENAME'],1)) {
				$tmp_row[stud_id] = "<a href=\"stu_list.php?student_sn=$student_sn\">$stud_id</a>" ; } else $tmp_row[stud_id] = "$stud_id" ;
        	$tmp_row[stud_name] = $row["stud_name"];
        	$tmp_row[stud_person_id] = $row["stud_person_id"];
        	$tmp_row[stud_study_cond]  = $row["stud_study_cond"] ;
        	$tmp_row[s_birthday] = DtoCh($row["stud_birthday"]) ; 
        	$tmp_row[s_home_phone]=$row["stud_tel_1"];	  //家中電話
        	$class_num_curr = $row["curr_class_num"];		//目前班級、座號

        	$classid = intval(substr($class_num_curr,0,3));	//取得班級	
        	$tmp_row[s_classname] = $class_year_p[$classid] ;
        	$tmp_row[s_num] = intval (substr($class_num_curr,-2));	//座號
        	if ($tmp_row[stud_study_cond] == 0) {
             $c_curr_class = sprintf("%03s_%s_%02s_%02s",curr_year(),curr_seme(),substr($class_num_curr,0,1),substr($class_num_curr,1,2));
             $tmp_row[link] = "<a href=\"../stud_base/stud_base.php?student_sn=$student_sn&stud_id=$stud_id&c_curr_class=$c_curr_class&c_curr_seme=$c_curr_seme\">基本資料</a>" ;
          }else 
             $tmp_row[link] ="(非在學)" ;
                  	  	
       	
        	$tmp_row[d_guardian_name] =$row["guardian_name"]  ;
        	$tmp_row[fath_name] =$row["fath_name"]  ;
        	$tmp_row[moth_name] =$row["moth_name"]  ;
          $data_array[] =  $tmp_row ;
          	
       }   	
  return $data_array ;       
       
}	
?>
