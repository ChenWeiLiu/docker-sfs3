<?php
//$Id: grad_print2.php 8011 2014-04-30 22:11:30Z yjtzeng $
//載入設定檔
require("config.php") ;
include_once "../../include/sfs_oo_zip2.php";
// 認證檢查
sfs_check();

($IS_JHORES==0) ? $UP_YEAR=6:$UP_YEAR=9;//判斷國中小
$y[] = $UP_YEAR ;
$class_year_p = class_base("",$y); //班級

//-----------------------------------
$oo_path = "ooo/list_all2"  ;
include "head_line2.php";

$temp_grade = get_grade_school_table();

$key = $_POST['key'];
$curr_class_name = $_POST['curr_class_name'];
$curr_grade_school = $_POST['curr_grade_school'];


switch ( $key)  {
    case "依學校匯出" :    
        
        foreach ($temp_grade as $tkey => $curr_grade_school) 
           $data .= hs_print($curr_grade_school);    
        do_sxw($data) ;   
        
         break;
    case "依班級匯出" :    

        if ($curr_class_name == $UP_YEAR ) {
          foreach ($class_year_p as $ckey => $c_name)  	
            foreach ($temp_grade as $tkey => $curr_grade_school) 
                $data .=  hs_print($curr_grade_school, $ckey);  	
        }else{ 	
            foreach ($temp_grade as $tkey => $curr_grade_school) 
                $data .=  hs_print($curr_grade_school, $curr_class_name);
        }  
        do_sxw($data ,  $curr_class_name ) ;     
         break;
    case "匯出 sxw" :    
         if ($curr_grade_school == "all" )

            foreach ($temp_grade as $tkey => $curr_grade_school) 
                $data .=  hs_print($curr_grade_school, $curr_class_name );                  
         else        
             $data .= hs_print($curr_grade_school, $curr_class_name) ;
         do_sxw($data ,  $curr_class_name) ; 
         break;                  
}


if (!isset($key)) {
   $main = show_menu() ;
   head() ;  
   print_menu($menu_p);
   echo $main ;
   foot() ;
}   

//主選單
function show_menu() { 
        global $UP_YEAR ,$PHP_SELF,$class_year_p ;
        
        $curr_year =  curr_year() ;

	$main =  "<table width=100%  bgcolor='#CCCCCC' >
  		<tr><td align='center'>	 
	<H2>$curr_year 學年度畢業生名冊列印</H2>
	<form action='$PHP_SELF' method='post' name='pform'> 
	<table width=50%  cellspacing='0' cellpadding='2' bordercolorlight='#333354' bordercolordark='#FFFFFF' border='1' bgcolor='#99CCCC' > 
	<tr ><td colspan=2 align=center>全部升學學校<br><input type='submit' name='key' value='依學校匯出'> &nbsp; &nbsp; <input type='submit' name='key' value='依班級匯出'></td></tr>
	<tr ><td align=right>升學學校</td><td><select name='curr_grade_school'>
	<option value= 'all' selected >全部學校</option> \n";
	$temp_grade =  get_grade_school_table(); 
	
	foreach( $temp_grade as $tkey => $tvalue) {
		if ($tvalue == $curr_grade_school)
			$main .=   sprintf ("<option value='%s' selected>%s</option>\n",$tvalue,$tvalue);
		else
			$main .=  sprintf ("<option value='%s'>%s</option>\n",$tvalue,$tvalue);
	}

	$main .= "</select></td></tr> \n
	     <tr ><td align=right>選擇班級</td><td><select name='curr_class_name'>
	     <option value='$UP_YEAR'>全學年</option> ";
        foreach ( $class_year_p as $tkey => $tvalue) {
		  if ($tkey == $curr_class_name)	  
			 $main .=  sprintf ("<option value='%02d' selected>%s</option>\n",$tkey,$tvalue);
		   else
			 $main .=   sprintf ("<option value='%02d'>%s</option>\n",$tkey,$tvalue);
	}             	 
          

	$main .= " </select></td></tr>
	         <tr ><td colspan=2 align=center><input type='submit' name='key' value='匯出 sxw'></td></tr>
	         </table></form>
	         </td></tr></table>" ;
        return $main ;
}





//每列(每個人)的資料
function hs_print($curr_grade_school, $curr_class_name='all') {
    global $CONN , $UP_YEAR ;     
    global $stud_id,$stud_name,$stud_birthday,$stud_sex,$stud_inhabit_address,$guardian_name,$stud_home_phone,$boy,$girl , $now_classname;

    $class_name = class_base();
        
    if ( $curr_class_name == 'all' ) $curr_class_name = $UP_YEAR ;  //整個年級

 if (!get_magic_quotes_gpc())  $curr_grade_school = addslashes($curr_grade_school); 
    /*
    $sqlstr = "select s.stud_id , s.stud_name , s.stud_addr_1  , s.stud_tel_1 , s.stud_tel_2  , s.curr_class_num ,s.stud_birthday ,s.stud_sex , 
             g.grad_sn , g.grad_word , g.grad_num ,g.new_school  ,d.guardian_name 
             from stud_base as s 
             LEFT JOIN stud_domicile as d ON s.stud_id=d.stud_id
             LEFT JOIN grad_stud as g ON s.stud_id=g.stud_id 
             where s.stud_study_cond = '0'  and s.curr_class_num like '$curr_class_name%' and g.new_school = '$curr_grade_school' 
             order by s.curr_class_num "; */
    
    $sqlstr = "select s.stud_id , s.stud_name , s.stud_addr_1  , s.stud_tel_1 , s.stud_tel_2  , s.curr_class_num ,s.stud_birthday ,s.stud_sex , 
             g.grad_sn , g.grad_word , g.grad_num ,g.new_school  ,d.guardian_name 
             from stud_base as s 
             LEFT JOIN stud_domicile as d ON s.student_sn=d.student_sn
             LEFT JOIN grad_stud as g ON s.student_sn=g.student_sn 
             where s.stud_study_cond = '0'  and s.curr_class_num like '$curr_class_name%' and g.new_school = '$curr_grade_school' 
             order by s.curr_class_num ";   
    //搜尋條件將s.stud_id=g.stud_id修改成 s.student_sn=g.student_sn，避免畢業生十年學號重複問題  modify by kai,103.4.30
    //echo  $sqlstr ;         
    $result =$CONN->Execute($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256) ; 


    $data .= title_line();
    
    $i = 1;
    $now_class = "" ;
    $boy = 0 ;
    $girl = 0 ;


    while ($row = $result->FetchRow() ) {

        $stud_inhabit_address =  $row["stud_addr_1"]  ;
        $stud_home_phone = $row["stud_tel_1"]  ;	//家中電話
        $s_offical_phone =  $row["stud_tel_2"]  ;	//工作地電話	
        
        $stud_id = $row["stud_id"];

        $stud_name = $row["stud_name"];	
        $stud_birthday = $row["stud_birthday"];
        $stud_sex = $row["stud_sex"];
        $guardian_name = $row["guardian_name"] ;	  
   	

        //目前所處理的班級
        $t_class = substr($row["curr_class_num"],0,3) ;
        //$t_class = $row["curr_class_num"] ;
        $now_classname= $class_name[$t_class]  ;   //班名
//    	print_r($class_name);
//	echo $t_class;
//	exit; 
        if ($i % 23 ==0) {	

                $data .= tol_sex(); //計算男女人數
                $data .= page_break(); //分頁符號
                //每頁預設值
                $i = 1;
                $boy = 0 ;
                $girl = 0 ;
                
                $data .= title_line();//標題			
                $data .= content_line();//內容	

        }
        else
             $data .= content_line();	//內容

        
        $i++;
        
    }

      $data .= tol_sex();
      $data .= page_break(); //分頁符號
    if ($i>1) 
       return $data ; 
}


function do_sxw($data, $name_add ) {
    global $oo_path;        
    if ($data){
	$ttt = new EasyZip; 
	$ttt->setPath($oo_path);

        //$data_utf =iconv("Big5","UTF-8",$data);


	//讀出 XML 檔頭
	$doc_head = $ttt->read_file(dirname(__FILE__)."/$oo_path/content_head");
	//$doc_head =iconv("Big5","UTF-8",$doc_head);
	
	//讀出 XML 檔尾
	$doc_foot = $ttt->read_file(dirname(__FILE__)."/$oo_path/content_foot");
	//$doc_foot =iconv("Big5","UTF-8",$doc_foot);
        
        //結合頭尾
        $data = $doc_head . $data  . $doc_foot;
        
        // 加入 content.xml 到zip 中
	$ttt->add_file($data,"content.xml");
        
	//讀出 xml 檔案

	//加入 xml 檔案到 zip 中，共有五個檔案 
	//第一個參數為原始字串，第二個參數為 zip 檔案的目錄和名稱
	$ttt->addDir("META-INF");

	$ttt->addfile("settings.xml");
	$ttt->addfile("styles.xml");
	$ttt->addfile("meta.xml");

        //產生 zip 檔
        $sss = & $ttt->file();
        
	$df="畢業生名冊$name_add.sxw";

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
}
  
?>
