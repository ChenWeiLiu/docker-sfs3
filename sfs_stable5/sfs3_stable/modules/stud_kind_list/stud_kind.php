<?php

// $Id: stud_kind.php 7711 2013-10-23 13:07:37Z smallduh $


//載入設定檔
require("config.php") ;
// --認證 session 
sfs_check();



//-----------------------------------


  if ($_POST['Submit']=="匯出EXCEL") {

             
	$filename ="score.xls" ;


	header("Content-disposition: filename=$filename");
	header("Content-type: application/octetstream");
	//header("Pragma: no-cache");
				//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");
	header("Expires: 0");

	echo show_data(0) ;
	exit;
         
  }       


head("特殊身份別學生名冊");
print_menu($menu_p);
echo "<form name='form1' method='post' >
       <div align='center'><input type='submit' name='Submit' value='匯出EXCEL'>
       </div>\n" ;
      
echo show_data() ;
echo "</form>" ;
foot() ;


function show_data($view=1 ) {
  global $CONN ;     
  $class_year_p = class_base(); //班級

  //$main = "<table width=100% BGCOLOR='#FDDDAB' ><tr><td align=center>" ;
 
    //取得各類別名稱
    $sqlstr = "select d_id , t_name from sfs_text where  t_kind='stud_kind'  " ;
    $result =$CONN->Execute($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256) ;
    while ($row = $result->FetchRow() ) {
        $d_id = $row["d_id"] ;
        $t_name = $row["t_name"] ;    
        $kind_name[$d_id] = "($d_id)$t_name"   ;
    }


    $sqlstr = "select b.* , d.*  from stud_base b ,stud_domicile d  where b.stud_study_cond = 0  and b.stud_kind <> '0' and (b.stud_kind <> ',0,') and b.stud_kind <> ''  and b.stud_id =d.stud_id order by b.stud_kind , b.curr_class_num " ;
    $result =$CONN->Execute($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256) ; 
    //echo $sqlstr ;
    
    $i =0;
    
    if ( $result->RecordCount() > 0 ){
    	//$main .= "筆數：". $result->RecordCount() .	"</td></tr>" ;
    	$main .= "<table  align=center  border=\"1\" cellspacing=\"0\" cellpadding=\"2\" bordercolorlight=\"#333354\" bordercolordark=\"#FFFFFF\" >
    	 <tr  class='title_sbody1'>
    	 <td align=center>(代號)身份別</td><td align=center>學號</td><td align=center>班級</td><td align=center>座號</td><td align=center>姓名</td><td align=center>身份證號</td>
    	    <td align=center>生日</td><td align=center>地址</td><td align=center>電話</td>
    	    <td align=center>父親</td><td align=center>母親</td><td align=center>監護人</td>
    	 </tr>";
    }
    
    while ($row = $result->FetchRow() ) {
    	$s_addres = $row["stud_addr_1"];
    	$s_home_phone = $row["stud_tel_1"];	  //家中電話
    	$s_offical_phone = $row["stud_tel_2"];  	//工作地電話
    
    	$stud_id = $row["stud_id"];
    	$stud_name = $row["stud_name"];
    	$stud_person_id = $row["stud_person_id"];
            $stud_kind = $row["stud_kind"];
    
    	$class_num_curr = $row["curr_class_num"];
    	$s_year_class = substr($class_num_curr,0,3);   //取得班級
    
    	$s_num = intval (substr($class_num_curr,-2));	//座號
    	$s_birthday = $row["stud_birthday"]  ;
    	//轉換民國日期
    	if ($view)
            $s_birthday = DtoCh($s_birthday) ; 
            
            $d_guardian_name = $row["guardian_name"] ;
            $fath_name = $row["fath_name"] ;
            $moth_name = $row["moth_name"] ;
            
            $s_kind ="" ;
    	$stud_kind_arr = split("," , $stud_kind) ;
    	foreach( $stud_kind_arr as  $tid =>$tval) {
    	  if ($tval > 0 )
    	     $s_kind .= $kind_name["$tval"]; 
    	}     
                     
    	$main .= ($i%2 == 1) ? "<tr class='nom_1' >" : "<tr class='nom_2'>";
    	$main .= "<td>$s_kind</td>
    	<td>$stud_id</td>
    	<td>$class_year_p[$s_year_class] </td>
    	<td>$s_num </td>
    	<td>$stud_name </td>
    	<td>$stud_person_id </td>
    	<td>$s_birthday </td>
    	<td>$s_addres </td>
    	<td>$s_home_phone </td>
    	<td>$fath_name </td>
    	<td>$moth_name </td>
    	<td>$d_guardian_name </td>
    	</tr>\n";
    	$i++;
    }
    
    $main .= "</table>";
    return $main ;
    
}    

?>