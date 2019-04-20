<?
//$Id: signView.php 7712 2013-10-23 13:31:11Z smallduh $
include "config.php";

sfs_check();

$SCRIPT_FILENAME = $_SERVER['SCRIPT_FILENAME'] ;
if ( !checkid($SCRIPT_FILENAME,1)){
    head("報名表單設計") ;
    print_menu($school_menu_p);
    echo "無管理者權限！<br>請進入 系統管理 / 模組權限管理 修改 stud_sign 模組授權。" ;
    foot();
    exit ;
    
    //Header("Location: signList.php"); 
}

   //列出各才藝班資料 
    $sqlstr =" select *  from stud_team_kind   where mid = '$_GET[id]'  " ;
	//echo $sqlstr ;
    $result = $CONN->Execute($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256) ; 
    $i = 1 ;
    while (  $row = $result->FetchRow() ) {
      $kid = $row["id"] ;
      $mid = $row["mid"] ;
      $class_kind = $row["class_kind"] ;
      $stud_max = $row["stud_max"] ;
      $stud_ps = $row["stud_back"] ;
      $class_max = $row["class_max"] ;
      $week_set = $row["week_set"] ;
      $year_set = $row["year_set"] ;     
      $cost = $row["cost"] ;   
      $main .= Get_kind_stud_List($kid,$class_kind,$stud_max,$stud_ps,$cost)  ;
    }
    
	//以串流方式送出 data.csv
	header("Content-disposition: attachment; filename=data.csv");
	header("Content-type: text/x-csv");
	//header("Pragma: no-cache");
				//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");
	header("Expires: 0");
  echo "班別,費用,編號,班級,姓名,電話,緊急電話,行動電話,報名時間,是否正取\n" ;
  
  echo $main   ;


function Get_kind_stud_List($kid,$class_kind,$stud_max,$stud_ps,$cost) {      
    global $CONN  ;
    $class_base_p = class_base();
    $sqlstr = " select *  from stud_team_sign where kid ='$kid'  order by sid " ;

    $result = $CONN->Execute($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256) ;
    $i = 1 ;
    while (  $row = $result->FetchRow() ) {
        $class_id = $row["class_id"] ;	
        $class_name = $class_base_p[$class_id] ;	
        $stud_name = $row["stud_name"] ;
        $stud_id = $row["stud_id"] ;
        $sign_time  = $row["sign_time"] ;    	
        if ($i>$stud_max)
           $bk= "備取" ;
        else 
           $bk= "正取" ;   
        $phon = Get_stud_phon($stud_id);      
        $main .= "$class_kind, $cost ,$i,$class_name,$stud_name,$phon,=T(\"$sign_time\"),$bk\n" ;
        $i++ ;  
    }

    return $main ;         
}    


function Get_stud_phon($stud_id) {
     //由班級+座號及 $get_arr 陣列中取得：姓名....
     global $CONN   ;



    $sql="select * from  stud_base  
           where  stud_id  = '$stud_id'   and stud_study_cond = 0   ";

    //座號、姓名、生日、地址、電話、家長、家長工作、工作電話

    $result = $CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256) ;
    
    
    $row = $result->FetchRow() ;

	$s_home_phone = $row["stud_tel_1"]  ;	//家中電話
	$s_offical_phone =$row["stud_tel_2"]  ;	//工作地電話
	$s_cell_phone =$row["stud_tel_3"]  ;	//工作地電話
	$data_str  = "=T(\"$s_home_phone\"),=T(\"$s_offical_phone\"),=T(\"$s_cell_phone\")" ;

    
    return $data_str  ;
      	
}		
?>