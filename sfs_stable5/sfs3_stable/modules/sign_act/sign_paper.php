<?
// $Id: sign_paper.php 8681 2015-12-25 02:59:43Z qfon $
  include "config.php";
  //登入認証
  session_start();   
  //報名單總類 ===============================
  $_GET[pid]=intval($_GET[pid]);
  $sqlstr = " select * from  sign_act_kind where  id ='$_GET[pid]' " ;

  $result =  $CONN->Execute($sqlstr) ; 
  $row = $result->FetchRow() ;
      $id = $row["id"] ;	
      $beg_date = $row["beg_date"] ;	
      $end_date = $row["end_date"] ;	
      $doc = $row["act_doc"] ;	
      $title = $row["act_name"] ;
      $act_passwd = $row["act_passwd"] ;
      $team_set = $row["team_set"] ;
      $max_team = $row["max_team"] ;
      $max_each = $row["max_each"] ;
      $member_set = $row["member_set"] ;
      $fields_set = $row["fields_set"] ;     
      $def_passwd = $row["act_passwd"] ;     
      

  //組別    
  $tmparr = split (",", $team_set) ;  
  for ($i= 1 ; $i <= count($tmparr) ; $i++) {
    if ($tmparr[$i-1]<>"") {
      	$ni = $i ;	  
  	$tmp_arr1 = split ("##",$tmparr[$i-1]) ;  //甲組名|男姓說明,乙組|女生  
  	$team_set_arr[] = $tmp_arr1 ;
    } 	  
  }

  //成員    
  $tmparr = split (",", $member_set) ;      //隊長*1,帶隊*2,隊長*1,隊員*4
  for ($i= 1 ; $i <= count($tmparr) ; $i++) {
    if ($tmparr[$i-1]<>"") {

      	$ni = $i ;	  
  	$tmp_arr1 = split ("\*",$tmparr[$i-1]) ;    
  	
  	//成員職稱放入陣列中
  	for ($j = 0 ; $j < $tmp_arr1[1] ; $j++ ) 
  	  $group_user_title[]= $tmp_arr1[0] ; 
  	/*  
  	$member_set_arr[] = $tmp_arr1 ;

  	$group_man += $member_set_arr[$i-1][1] ;
  	*/
    } 	  
  }  
  
  //欄位    
  $tmparr = split (",", $fields_set) ;      //身份証字號|10|預設值|說明,生日|6|預設值|說明
  for ($i= 1 ; $i <= count($tmparr) ; $i++) {
    if ($tmparr[$i-1]<>"") {
      	$ni = $i ;	  
  	$tmp_arr1 = split ("##",$tmparr[$i-1]) ;    
  	$fields_set_arr[] = $tmp_arr1 ;
    } 	  
  } 
  // 先將學校名稱的空白刪除
  $schoolName = preg_replace('/\s+/', '',$_SESSION['schoolname']);
  //該校的報名資料======================================================================
  $_GET[pid]=intval($_GET[pid]);
  $sqlstr = " select * from  sign_act_data where  pid ='$_GET[pid]'   and school_name ='$schoolName' order by did " ;
  $result = $CONN->Execute($sqlstr);  
  

  $mi=0 ;
  while($row =$result->FetchRow()) {
      $did_arr[] = $row["did"] ;	
      $team_id[$mi] = $row["team_id"] ;	
      $set_passwd = $row["set_passwd"] ;	
      $data = $row["data"] ;	
      
     
      
      //欄位    
      $tmparr = split (",", $data) ;      //(姓名|欄位1|欄位2,姓名|欄位1|欄位2)
      for ($i= 1 ; $i <= count($tmparr) ; $i++) {
      	    $tmp_arr1 = @split ("##",$tmparr[$i-1]) ;    
      	    $member_data_arr[$mi][] = $tmp_arr1 ;
      }    
      //$member_data_arr[$mi]['term'] = $row["team_id"] ;
      $mi++ ;
      $have_team ++ ;
  }
  
//=================================================  
$template_dir = $SFS_PATH."/".get_store_path()."/templates";
$smarty->template_dir = $template_dir;
$smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE);
$smarty->assign("module_name","報名資料表");


$smarty->assign("school_name",$_SESSION[schoolname]);

$smarty->assign("team_set_arr",$team_set_arr);

$smarty->assign("fields_set_arr",$fields_set_arr);

$smarty->assign("member_data_arr",$member_data_arr);
$smarty->assign("team_id",$team_id);
$smarty->assign("group_user_title",$group_user_title);


//$smarty->debugging = true;
$smarty->display("sign_paper.htm");

?>