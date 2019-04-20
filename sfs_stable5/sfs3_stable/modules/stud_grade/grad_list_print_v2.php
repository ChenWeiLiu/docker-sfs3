<?php
//$Id: grad_list_print_v.php 5310 2009-01-10 07:57:56Z hami $
//載入設定檔
require("config.php") ;
include_once "../../include/sfs_oo_zip2.php";

$oo_path = "ooo/v_view2"  ;
//include "head_line.php"; 

// 認證檢查
sfs_check();
($IS_JHORES==0) ? $UP_YEAR=6:$UP_YEAR=9;//判斷國中小

$class_year_p =  class_base(); //班級    
//print_r($class_year_p);
//exit;
$key =$_POST['key'];
$curr_class_name =$_POST['curr_class_name'];
$name_add = $curr_class_name ;

function page_break() {
   $break ='<text:p text:style-name="break_page"/>';
   return $break ;
}

if  ( $key)  {

       
  $title_str = $school_long_name . Num2CNum(curr_year()) ."學年度畢業生一覽表"; 


  //$sqlstr = "select s.stud_id , s.stud_name ,s.curr_class_num ,s.stud_birthday ,s.stud_sex , 
  //           g.grad_sn , g.grad_word , grad_num   from stud_base as s  LEFT JOIN grad_stud as g ON s.stud_id=g.stud_id 
  //           where s.stud_study_cond = '0'  and s.curr_class_num like '$curr_class_name%' order by s.curr_class_num ";   
  $sqlstr = "select s.stud_id  , s.stud_person_id, s.stud_name ,s.curr_class_num ,s.stud_birthday ,s.stud_sex , 
             g.grad_sn , g.grad_word , grad_num   from stud_base as s , grad_stud as g where s.student_sn=g.student_sn 
             and s.stud_study_cond in ('0','15')  and s.curr_class_num like '$curr_class_name%' order by s.curr_class_num ";  

  //echo  $sqlstr ;         
  $result =$CONN->Execute($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256) ; 


  
  for ($i = 1 ; $i <=19 ; $i++) {
    	  $clear_arr["class" . $i ] = "";

        $clear_arr["name". $i] ="" ;
        $clear_arr["sex". $i] ="" ;
		$clear_arr["person_id". $i] ="" ;
        $clear_arr["birth" . $i] ="" ;
        $clear_arr["num". $i] ="";
		$clear_arr["grad_id". $i] ="";
  }  
  $clear_arr["ttt"]=$title_str ; 
  $clear_arr["grad_id"] ="" ;
  
  $i = 1;
  $temp_arr = $clear_arr ;
    
  $ttt = new EasyZip; 
  $ttt->setPath($oo_path);
  $data_cont = $ttt->read_file(dirname(__FILE__)."/$oo_path/content");
  
  while ($row = $result->FetchRow() ) {
    	
    	$stud_id = $row["stud_id"];	
    	$stud_name = $row["stud_name"];	
    	$stud_birthday = $row["stud_birthday"];
      $temp = explode ("-",$stud_birthday);
      $stud_birthday = sprintf ("%s年%s月%s日", $temp[0]-1911,intval($temp[1]),intval($temp[2]));
          	
    	$stud_sex = $row["stud_sex"];
      if ($stud_sex =='1') 
    	  $stud_sex_temp = "男";	
      else 
    	   $stud_sex_temp = "女";
		   
		 $stud_person_id = $row["stud_person_id"];
 	
    	$curr_class_num = substr($row["curr_class_num"],0,3);
    	
    	$now_classname= $class_name[$t_class]  ;   //班名    
    	
    	$stud_graduate_num = $row["grad_word"] ."第" ;
    	$grad_num = $row["grad_num"];	
 
      $curr_class_name  = $class_year_p[$curr_class_num] ;


      if ($old_class == "")  $old_class = $curr_class_name ;
    	if  ($old_class <> $curr_class_name) {  //換班級就要換頁
          $old_class = $curr_class_name ;

						$data .= $ttt->change_temp($temp_arr,$data_cont)  ;
            $i = 1;
  	        $have_data_out = false ;  
		        
            unset($temp_arr) ;
            $temp_arr = $clear_arr ;
    	}    
      
      $temp_arr["class" . $i ]=$curr_class_name ;
      $temp_arr["name". $i]=$stud_name ;
      $temp_arr["sex" . $i ]=$stud_sex_temp ;
	  $temp_arr["person_id" . $i ]=$stud_person_id;
      $temp_arr["birth" . $i]=$stud_birthday ;
      $temp_arr["grad_id". $i]=$stud_graduate_num ;
      $temp_arr["num" . $i]=$grad_num ;
      $have_data_out = true ;    
        


    	if ($i % 19 == 0) {	
            //完成一頁，

						$data .= $ttt->change_temp($temp_arr,$data_cont)  ;

            //$data .= page_break(); //分頁符號

            $i = 1;
		        $have_data_out = false ;  
		        
            unset($temp_arr) ;
            $temp_arr = $clear_arr ;
    	}
    	else
    		$i++;
	
  } //while
  

     if ( $have_data_out) {
      //最後部份
					
  	  $data .= $ttt->change_temp($temp_arr,$data_cont)  ;
      //$data .= page_break(); //分頁符號
    }		

	//讀出 XML 檔頭
	$doc_head = $ttt->read_file(dirname(__FILE__)."/$oo_path/content_head");

	
	//讀出 XML 檔尾
	$doc_foot = $ttt->read_file(dirname(__FILE__)."/$oo_path/content_foot");

        
  //結合頭尾
  $data = $doc_head . $data  . $doc_foot;
        
        // 加入 content.xml 到zip 中
	$ttt->add_file($data,"content.xml");
        
	$ttt->addDir('META-INF');

	$ttt->addfile("settings.xml");
	$ttt->addfile("styles.xml");
	$ttt->addfile("meta.xml");

  //產生 zip 檔
  $sss = &$ttt->file();
        
	$df="畢業生一覽表$name_add.sxw";

	//以串流方式送出 ooo.sxw
	header("Content-disposition: attachment; filename=$df");
	//header("Content-type: application/octetstream");
	header("Content-type: application/vnd.sun.xml.writer");
	//header("Pragma: no-cache");
				//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");
	header("Expires: 0");

  	echo $sss;      
}
        
	
if (!isset($key)) {
	head() ;

	print_menu($menu_p);
        echo show_menu() ;
	foot() ;
	exit;
}

function show_menu() {
        global $UP_YEAR ,$PHP_SELF ;
        $curr_year =  curr_year() ;
        $class_year_p = class_base("",array($UP_YEAR)); //班級
        
	$main =  "<table width=100% bgcolor='#CCCCCC' >
  		<tr><td align='center'>	
  		<center><H2>畢業生一覽表列印</H2><form action='$PHP_SELF' method='post' name='pform'>
  		<table  width=50% cellspacing='0'  cellpadding='2' bordercolorlight='#333354' bordercolordark='#FFFFFF' border='1' bgcolor='#99CCCC' >
  		<tr><td align=right>選擇班級</td><td><select name='curr_class_name'>
  		<option value='$UP_YEAR'>全學年</option>\n";
	$class_temp ="";		
	foreach ( $class_year_p as $tkey => $tvalue) {
		  if ($tkey == $curr_class_name)	  
			 $class_temp .=  sprintf ("<option value='%d' selected>%s</option>\n",$tkey,$tvalue);
		   else
			 $class_temp .=   sprintf ("<option value='%d'>%s</option>\n",$tkey,$tvalue);
	}             	 
	$main .=  $class_temp ;
	
	$main .= " </select></td></tr>
	    <tr><td colspan=2 align=center><input type='submit' name='key' value='匯出 SXW'>
	    </td></tr></table></form></center>
	    </td></tr></table>" ;	    
	return $main ;        
}        


?>
