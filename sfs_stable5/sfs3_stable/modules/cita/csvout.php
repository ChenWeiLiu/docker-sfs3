<?php

// $Id: view.php 6761 2012-05-09 08:27:22Z infodaes $


include "config.php";
// 不需要 register_globals
if (!ini_get('register_globals')) {
	ini_set("magic_quotes_runtime", 0);
	extract( $_POST );
	extract( $_GET );
	extract( $_SERVER );
}

sfs_check();

    $class_base_p = class_base();            
    $sqlstr =" select * from cita_kind  where id = '$id'" ;
    $result = $CONN->Execute($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256) ; 
    $row = $result->FetchRow() ;          
      $doc = $id.'-'.$grada[$row["grada"]].'-'.$row["doc"];     
      $kind_set = $row["kind_set"] ;       

	$csv_data="成績,班級,座號,學號,姓名,>指導者,報送日期\r\n";              

    //班上報名資料
     $sqlstr =" select * from cita_data  where (kind = '$id'  and order_pos>-1) order by class_id,order_pos,num" ;

	//echo $query ;
    $result = $CONN->Execute($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256) ; 
    while ($row = $result->FetchRow() ) {
        $did = $row["id"] ;	
        $item = $row["item"] ;
        $order_pos = $row["order_pos"]+1 ;
        $stud_name = $row["stud_name"] ;
		$guidance_name = $row["guidance_name"] ;
		$stud_num = $row["num"] ;
        $data_get = $row["data_get"] ;
        $data_input = $row["data_input"] ;   
     	
	   $up_date = $row["up_date"] ;      
		if($date=='no')   $up_date="";
        $class_id = $row["class_id"] ;              
        $stud_id = $row["stud_id"] ;  
		$class_name=class_id_to_full_class_name($class_id);
        $temp = explode("_",$class_id);
        $seme_year_seme = $temp[0].$temp[1];
        $cti_class_id = sprintf("%d%02d",$temp[2],$temp[3]);
        $num = $row["cc"] ;
        $csv_data.="$data_get,$class_name,$stud_num,$stud_id,$stud_name,$guidance_name,$up_date\r\n";
   }
	header("Content-disposition: attachment; filename=$doc");
	header("Content-type: text/x-csv");
	//header("Pragma: no-cache");
					//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");

	header("Expires: 0");   
   echo $csv_data;    
?>
