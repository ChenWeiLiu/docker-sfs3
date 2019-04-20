<?php


//---------------------------------------------------
// 這裡請放上程式的識別 Id，寫法： $ + Id + $
// SFS 開發小組幫您放入 SFS 的 CVS Server 時
// 會自動維護此一變數，注意! 請放在註解範圍中，如下所示：
//
//---------------------------------------------------

// $Id: config.php 9147 2017-09-19 08:07:38Z smallduh $

//---------------------------------------------------
//
// 模組系統相關的設定檔，一定要引入，所以使用 require !!!
//
//---------------------------------------------------

require_once "./module-cfg.php";
include_once "../../include/config.php";
include_once "../../include/sfs_case_PLlib.php";
include_once "module-upgrade.php";

 //取得模組參數設定
$m_arr =& get_module_setup("stud_sign");
extract($m_arr, EXTR_OVERWRITE);

$PHP_SELF = $_SERVER["PHP_SELF"] ;


//---------------------------------------------------
// 這裡請引入 SFS 學務系統的相關函式庫。
//
// 至於要引入那些呢？
//
// 1. sfs3/include/config.php 經常是需要的。
//
// 2. 其它，就視您的程式目的而定。
// 請注意!!!!! 這裡只能使用 include_once 或 include
//---------------------------------------------------


// 引入 SFS 設定檔，它會幫您載入 SFS 的核心函式庫
include_once "../../include/config.php";


//---------------------------------------------------
// 這裡請引入您自己的函式庫
//
// 沒有的話，可以略過。
// 請注意!!!!! 這裡只能使用 include_once 或 include
//---------------------------------------------------

// 待您填入


function Get_teach_name($class_id) {
	global $CONN ;
	//取得班級的級任名，<50 代表取得級任，非實習老師 
	$sql =" select name  from teacher_base b ,teacher_post p 
	          where b.teacher_sn  = p.teacher_sn  and b.teach_condition =0 
	          and class_num ='$class_id' and p.teach_title_id < 50  ";
	$result =  $CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256) ;
	//echo $query ;
	$row = $result->FetchRow() ;
	$name = $row["name"];	
	return $name ;

}	 

function Get_stud_data($class_num  , $now_class_id , $get_arr) {
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
	$s_offical_phone =stud_tel_2  ;	//工作地電話
	$stud_id = $row["stud_id"];		//學號
    $student_sn=$row['student_sn'];    //2017.09.19 by smallduh
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
        $sql="select *    from stud_domicile where student_sn = '$student_sn'   ";
        $result = $CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256) ;
    
        $row = $result->FetchRow() ;           

	//家長

         $d_guardian_name = $row["guardian_name"] ;	  
         $fath_name =$row["fath_name"] ;	  
         $moth_name =$row["moth_name"] ;
         

        $stud_class_id = $now_class_id ;
        $teacher_name= Get_teach_name($class_num) ;
    	
        //可匯出欄位選項
        //$STUD_FIELD = array("學號","生日","身份証字號","座號","性別","電話","地址","監護人","父親","母親","級任") ;	
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
        $dd[] = $teacher_name ;

    
        //一併匯出
        $data_item_arr = split (",", $get_arr);   

        $max_get = count($data_item_arr) ;
        //$data_arr[] = $stud_name ;
        for ($i = 0 ; $i < $max_get ; $i++) {
    	  $tii = $data_item_arr[$i] ;
   	  $data_arr[] = $dd[$tii] ;
       }	
       if ($max_get ) 
         $data_arr_str  = @implode("##" , $data_arr) ;
      $data[0] = $stud_name ;
      $data[1] = $data_arr_str ;
      //echo $data_arr_str ;
    }
    return $data ;
      	
}	

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


//取得舊報名資料
function get_history() {
  global $PHP_SELF ,$CONN ;
  
  $sqlstr = " select id , title  from  sign_kind  order by id DESC " ;
  $recordSet =  $CONN->Execute($sqlstr) ;  
  while ( ($recordSet) and ( !$recordSet->EOF) ){         
      	$id = $recordSet->fields["id"];
      	$act_name = nl2br($recordSet->fields["title"]);     
      	$select_str  .= "<option value='$id' >$act_name</option>\n" ;
 	$recordSet->MoveNext();    
  }      
  $select_str = "<select name='history_id' >
                 <option value='0' selected>------</option>    
                 $select_str
                 </select>" ;    
  return $select_str ;
}  
//---------------------------------------------------
// 
// 變數定義，請至：module-cfg.php
// 
//
//---------------------------------------------------


?>
