<?php

// $Id: config.php 8764 2016-01-13 13:08:50Z qfon $
include_once "../../include/config.php";

require_once "./module-cfg.php";
require_once "./module-upgrade.php";





//取得模組參數設定
$m_arr = get_module_setup("sign_act");
extract($m_arr, EXTR_OVERWRITE);

$PHP_SELF = $_SERVER["PHP_SELF"] ;

//---------------------------------------------------
// 這裡請引入您自己的函式庫
//
// 沒有的話，可以略過。
// 請注意!!!!! 這裡只能使用 include_once 或 include
//---------------------------------------------------

function check_mysqli_param($param){
	if (!isset($param))$param="";
	return $param;
}

//取得所在目錄的校名文字檔(school_name.txt)
function get_school_name($school_name ,$school_list) {
  global $SCHOOL_NAME_LIST ;
  //有無 限定校名  
  //echo $SCHOOL_NAME_LIST  ;
  if (!$school_list) {
    $nowdir =dirname( $_SERVER[SCRIPT_FILENAME] );             //所在目錄，以根目錄為準
    //$data = file($nowdir ."/school_name.txt") ;     

    $data = file($nowdir ."/$SCHOOL_NAME_LIST") ;     

  }else 
    $data = split("\n" ,$school_list) ; 

  for ($i = 0 ; $i < count($data) ; $i++) {
      if (trim($data[$i])<>"") {  
      	        $data_line = trim($data[$i]) ;
      	 if ($data_line == $school_name) 
      	    $selstr .= "<option value='$data_line' selected >$data_line</option>\n" ;
      	 else    
            $selstr .= "<option value='$data_line' >$data_line</option>\n" ;
      }   
  }
  return $selstr ;        
        
}    

//取得舊報名資料
function get_history() {
  global $PHP_SELF ,$CONN ;
  
  $sqlstr = " select id , act_name  from  sign_act_kind  order by id DESC " ;
  $recordSet =  $CONN->Execute($sqlstr) ;  
  while ( ($recordSet) and ( !$recordSet->EOF) ){         
      	$id = $recordSet->fields["id"];
      	$act_name = nl2br($recordSet->fields["act_name"]);     
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
