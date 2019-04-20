<?php
//$Id: grad_list_print.php 8008 2014-04-30 22:11:22Z yjtzeng $

//載入設定檔
require("config.php") ;
include_once "../../include/sfs_oo_zip2.php";

$oo_path = "ooo/list_all"  ;
include "head_line.php"; 

// 認證檢查
sfs_check();
($IS_JHORES==0) ? $UP_YEAR=6:$UP_YEAR=9;//判斷國中小

$class_year_p =  class_base(); //班級    
//print_r($class_year_p);
//exit;
$key =$_POST['key'];
$curr_class_name =$_POST['curr_class_name'];
$name_add = $curr_class_name ;

if  ( $key)  {

       
  $title_str = $school_long_name . Num2CNum(curr_year()) ."學年度畢業生一覽表"; 


  $sqlstr = "select s.stud_id , s.stud_name ,s.curr_class_num ,s.stud_birthday ,s.stud_sex , 
             g.grad_sn , g.grad_word , grad_num   from stud_base as s  LEFT JOIN grad_stud as g ON s.student_sn=g.student_sn 
             where s.stud_study_cond = '0'  and s.curr_class_num like '$curr_class_name%' order by s.curr_class_num ";   
  $sqlstr = "select s.stud_id , s.stud_name ,s.curr_class_num ,s.stud_birthday ,s.stud_sex , 
             g.grad_sn , g.grad_word , grad_num   from stud_base as s , grad_stud as g where  s.student_sn=g.student_sn 
             and s.stud_study_cond = '0'  and s.curr_class_num like '$curr_class_name%' order by s.curr_class_num ";   
//將s.stud_id=g.stud_id修改成s.student_sn=g.student_sn避免畢業生十年學號重複問題  modify by kai,103.4.30   

  //echo  $sqlstr ;         
  $result =$CONN->Execute($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256) ; 

  $data  .= title_line();
  $i = 1;

  while ($row = $result->FetchRow() ) {
	
	$stud_id = $row["stud_id"];	
	$stud_name = $row["stud_name"];	
	$stud_birthday = $row["stud_birthday"];
	$stud_sex = $row["stud_sex"];
	$curr_class_num = substr($row["curr_class_num"],0,3);
	
	$stud_graduate_num = $row["grad_word"] ."第" ;
	$grad_num = $row["grad_num"];	
 
        $curr_class_name  = $class_year_p[$curr_class_num] ;
        
        if ($old_class == "")  $old_class = $curr_class_name ;
    	if  ($old_class <> $curr_class_name) {  //換班級就要換頁
          $old_class = $curr_class_name ;
          $i = 26 ;
    	}

	if ($i % 13 ==0) {	
		if ($i % 26 == 0){ //換頁
			$data  .= sign_form();
			$data  .= page_break(); //分頁符號
			//每頁預設值
			$i = 1;			
			$data  .= title_line();//標題			
			$data  .= content_line();//內容	
			
		}
		else {	
			
			$data  .= blank_line();	//隔頁
			$data  .= content_line(1);//內容			
		}
	}
	else
		$data  .= content_line();	//內容

	$i++;
	
  } //while


	$data  .= sign_form();

        
	$ttt = new EasyZip; 
	$ttt->setPath($oo_path);

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
        $sss = & $ttt->file();
        
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
