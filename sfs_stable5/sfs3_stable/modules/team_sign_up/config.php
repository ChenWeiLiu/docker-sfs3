<?php
//$Id: config.php 5310 2009-01-10 07:57:56Z hami $
//預設的引入檔，不可移除。
require_once "./module-cfg.php";
include_once "../../include/config.php";
include_once "../../include/sfs_case_PLlib.php";
//您可以自己加入引入檔

 //取得模組參數設定
$m_arr =& get_module_setup("team_sign_up");
extract($m_arr, EXTR_OVERWRITE);

$PHP_SELF = $_SERVER["PHP_SELF"] ;

function show_page_point($showpage, $totalpage) {
  $PHP_SELF = $_SERVER["PHP_SELF"] ;
              if ($showpage >1) 
                   $main =  "<a href=\"$PHP_SELF?showpage=" . ($showpage-1) . "\"><img src=\"images/prev.gif\"  alt=\"前一頁\" border=\"0\"></a> \n " ;
                 else 
                   $main =  "<img src=\"images/prev.gif\"  alt=\"已是最前頁\" border=\"0\" class=\"hide\">\n " ;
                 $main .= " | 第 $showpage 頁 | \n" ;
                 if ($showpage < $totalpage) 
                   $main .= "<a href=\"$PHP_SELF?showpage=" . ($showpage+1). "\"><img src=\"images/next.gif\"  alt=\"下一頁\" border=\"0\"></a> \n" ;
                 else   
                   $main .= "<img src=\"images/next.gif\"  alt=\"已是最後頁\" border=\"0\" class=\"hide\">\n " ;

                 
   
     $main = 
        "<table width='98%' border='0' cellspacing='0' cellpadding='0' align='center'>
          <tr>
            <td width='70%'>&nbsp;</td>
            <td width='30%'>   
              $main
            </td>\n
          </tr>
        </table>" ;
    return $main ;    
                 
}  

function Get_stud_data($class_num  , $stud ) {
     //由班級+座號及 $get_arr 陣列中取得：姓名....
     global $CONN ;

   
     
    //由座號
    if (is_numeric($stud) ) {
       $class_num_id = $class_num . sprintf("%02d" ,$stud) ;
   
       $sql="select * from  stud_base  
           where  curr_class_num = '$class_num_id'    and stud_study_cond = 0   ";
    }else {
    	$sql="select * from  stud_base  
           where  stud_name = '$stud'  and stud_study_cond = 0   ";
    	
    }	
    //座號、姓名、生日、地址、電話、家長、家長工作、工作電話

    $result = $CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256) ;
    
    
    $row = $result->FetchRow() ;
        $have_stud_data_fg = TRUE ;
	$s_addres = $row["stud_addr_1"]  ;
	$s_home_phone = $row["stud_tel_1"]  ;	//家中電話
	$s_offical_phone =stud_tel_2  ;		//工作地電話
	$stud_id = $row["stud_id"];		//學號
	$stud_name = $row["stud_name"];		//姓名
	$stud_person_id = $row["stud_person_id"]; //身份証
	$stud_sex = $row["stud_sex"];		//性別
	
	$s_birthday = $row["stud_birthday"]  ;
	
        $dd[] = $stud_name ;
        $dd[] = $stud_id ;	

    

    return $dd  ;
      	
}


function Get_stud_data2($class_num  , $now_class_id ) {
     //由班級+座號及 $get_arr 陣列中取得：姓名....
     global $CONN ;
    //預設學年
    $curr_year =  curr_year();
    //預設學期
    $curr_seme = curr_seme();

     $curr_class_year= substr($now_class_id,0,1) ;
     
    //把本班姓名放入陣列中

    $class_num_id = $class_num . sprintf("%02d" ,$now_class_id) ;

    $sql="select * from  stud_base  
           where  curr_class_num = '$class_num_id'    and stud_study_cond = 0   ";

    //座號、姓名、生日、地址、電話、家長、家長工作、工作電話

    $result = $CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256) ;
    
    
    $row = $result->FetchRow() ;
        $have_stud_data_fg = TRUE ;
	$s_addres = $row["stud_addr_1"]  ;
	$s_home_phone = $row["stud_tel_1"]  ;	//家中電話
	$s_offical_phone =stud_tel_2  ;		//工作地電話
	$stud_id = $row["stud_id"];		//學號
	$stud_name = $row["stud_name"];		//姓名
	$stud_person_id = $row["stud_person_id"]; //身份証
	$stud_sex = $row["stud_sex"];		//性別
	
	$s_birthday = $row["stud_birthday"]  ;
	/*
	//轉換民國日期
	if ( substr($s_birthday,0,4)>1911) 
	  $s_birthday = (substr($s_birthday,0,4) - 1911). substr($s_birthday,4) ;
	else 
	  $s_birthday = " " ; 
	*/
    if ($have_stud_data_fg) {	
        $sql="select *    from stud_domicile   where stud_id = '$stud_id'   ";
        $result = $CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256) ;
    
        $row = $result->FetchRow() ;           

	//家長

         $d_guardian_name = $row["guardian_name"] ;	  
         $fath_name =$row["fath_name"] ;	  
         $moth_name =$row["moth_name"] ;
         

        $stud_class_id = $now_class_id ;

    	
        //可匯出欄位選項
        //$STUD_FIELD = array("學號","生日","身份証字號","座號","性別","電話","地址","監護人","父親","母親") ;	
        $dd[] = $stud_name ;
        $dd[] = $stud_id ;
        $dd[] = $s_birthday ;
        $dd[] = $stud_person_id ;
        $dd[] = $stud_class_id ;
        $dd[] = $stud_sex ;
        $dd[] = $s_home_phone ;
        $dd[] = $s_addres ;
        $dd[] = $d_guardian_name ;
        $dd[] = $fath_name ;
        $dd[] = $moth_name ;


    
        //一併匯出
	$data_str  = @implode("," , $dd) ;

    }
    return $data_str  ;
      	
}
?>
