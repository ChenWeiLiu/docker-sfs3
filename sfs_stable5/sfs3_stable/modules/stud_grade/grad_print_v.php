<?php
//$Id: grad_print_v.php 8364 2015-03-23 07:28:21Z chiming $
//載入設定檔
require("config.php") ;
include_once "../../include/sfs_oo_zip2.php";
// 認證檢查
sfs_check();

($IS_JHORES==0) ? $UP_YEAR=6:$UP_YEAR=9;//判斷國中小
$y[] = $UP_YEAR ;
$class_year_p = class_base("",$y); //班級

//-----------------------------------
$oo_path = "ooo/v_grade"  ;


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


//分頁
function page_break() {
   global $boy,$girl;

   $break ="<text:p text:style-name=\"break_page\"/>";
   return $break ;
}



//每列(每個人)的資料
function hs_print($curr_grade_school, $curr_class_name='all') {
    global $CONN , $UP_YEAR , $oo_path ,$school_short_name ;     
  
    $class_name = class_base();
        
    if ( $curr_class_name == 'all' ) $curr_class_name = $UP_YEAR ;  //整個年級

//    if (!get_magic_quotes_gpc())  $curr_grade_school = addslashes($curr_grade_school); 
   $curr_grade_school = addslashes($curr_grade_school); 
    
    $sqlstr = "select s.stud_id , s.stud_name , s.stud_addr_1  , s.stud_tel_1 , s.stud_tel_2  , s.curr_class_num ,s.stud_birthday ,s.stud_sex , 
             g.grad_sn , g.grad_word , g.grad_num ,g.new_school  ,d.guardian_name 
             from stud_base as s 
             LEFT JOIN stud_domicile as d ON s.student_sn=d.student_sn
             LEFT JOIN grad_stud as g ON s.student_sn=g.student_sn 
              
             where s.stud_study_cond in ('0','15')  and s.curr_class_num like '$curr_class_name%' and g.new_school = '$curr_grade_school' 
             order by s.curr_class_num ";
             //將s.stud_id=d.stud_id修改成 s.student_sn=d.student_sn，s.stud_id=g.stud_id修改成s.student_sn=g.student_sn避免畢業生十年學號重複問題  modify by kai,103.4.30   
      
    $result =$CONN->Execute($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256) ; 



    
    $t1 = $school_short_name.Num2CNum(curr_year()) ."學年度畢業生名冊" ;
    $t2 = "屬" . $curr_grade_school ;

    $clear_arr["tt1"]=$t1 ;
    $clear_arr["TT2"]=$t2 ;
    
    
    for ($i = 1 ; $i <=19 ; $i++) {
    	  $clear_arr["name" . $i ] = "";

        $clear_arr["sex". $i] ="" ;
        $clear_arr["tel". $i] ="" ;
        $clear_arr["birth" . $i] ="" ;
        $clear_arr["da". $i] ="" ;
        $clear_arr["add". $i] ="";
        $clear_arr["class". $i] ="" ;   
    }        
    $i = 1;
    $now_class = "" ;
    $boy = 0 ;
    $girl = 0 ;
    $temp_arr = $clear_arr ;
    
    $ttt = new easyzip; 
    $data_cont = $ttt->read_file(dirname(__FILE__)."/$oo_path/content");
    
   
    while ($row = $result->FetchRow() ) {

        $stud_inhabit_address =  $row["stud_addr_1"]  ;
        $stud_home_phone = $row["stud_tel_1"]  ;	//家中電話
        $s_offical_phone =  $row["stud_tel_2"]  ;	//工作地電話	
        
        $stud_id = $row["stud_id"];

        $stud_name = $row["stud_name"];	
        
        $stud_birthday = $row["stud_birthday"];
        $temp = explode ("-",$stud_birthday);
  			$y= $temp[0]-1911 ;
  			$m =intval($temp[1]) ;
  			$d=intval($temp[2])  ;        
  			
        $stud_sex = $row["stud_sex"];
        if ($stud_sex =='1') {
      	  $stud_sex_temp = "男";	
      	  $boy++;
        }
        else {
      	  $stud_sex_temp = "女";
      	  $girl++;
        }        
        
        $guardian_name = $row["guardian_name"] ;	  
       
        //echo "$stud_name , $guardian_name <br>" ; 
        //目前所處理的班級
        $t_class = substr($row["curr_class_num"],0,3) ;
        $now_classname= $class_name[$t_class]  ;   //班名        

        $temp_arr["name" . $i ] = $stud_name ;
        //echo "$stud_name , $guardian_name $i " . $temp_arr["name" & $i ] ." <br>" ; 
        $temp_arr["sex". $i] =$stud_sex_temp ;
        $temp_arr["tel". $i] =$stud_home_phone ;
        $temp_arr["birth" . $i] =$y. "-". $m ."-". $d  ;
        $temp_arr["da". $i] =$guardian_name ;
        $temp_arr["add". $i] =$stud_inhabit_address ;
        $temp_arr["class". $i] =$now_classname ;   	

        if ($i >= 19) {	
                //完成一頁，
                $temp_arr["boy"]= $boy ;
  							$temp_arr["girl"]= $girl ;
  							$temp_arr["tol"]= $boy + $girl ;
  							

   							$data .= $ttt->change_temp($temp_arr,$data_cont)  ;

               // $data .= page_break(); //分頁符號

                //每頁預設值
                $i = 1;
                $boy = 0 ;
                $girl = 0 ;
                unset($temp_arr) ;
                $temp_arr = $clear_arr ;
        }else 
           $i++;
        
    }
    
    if ($i>1) {
      //最後部份
      $temp_arr["boy"]= $boy ;
  	  $temp_arr["girl"]= $girl ;
  	  $temp_arr["tol"]= $boy + $girl ;
  							
  	  $data .= $ttt->change_temp($temp_arr,$data_cont)  ;
      //$data .= page_break(); //分頁符號
    }
    //echo $data ;
    return $data ; 
       
}


function do_sxw($data, $name_add ) {
    global $oo_path;        
  if ($data){
    	$ttt = new easyzip; 
    	$ttt->setPath($oo_path);
    
    	//讀出 XML 檔頭
    	$doc_head = $ttt->read_file(dirname(__FILE__)."/$oo_path/content_head");
    	
    	//讀出 XML 檔尾
    	$doc_foot = $ttt->read_file(dirname(__FILE__)."/$oo_path/content_foot");

      //結合頭尾
      $data = $doc_head . $data  . $doc_foot;
        
        // 加入 content.xml 到zip 中
    	$ttt->add_file($data,"content.xml");
            
    	//讀出 xml 檔案
    	//$data = $ttt->read_file(dirname(__FILE__)."/$oo_path/META-INF/manifest.xml");
    
    	//加入 xml 檔案到 zip 中，共有五個檔案 
    	//第一個參數為原始字串，第二個參數為 zip 檔案的目錄和名稱
	$ttt->addDir("META-INF");

	$ttt->addfile("settings.xml");

	$ttt->addfile("styles.xml");

	$ttt->addfile("meta.xml");
    
      //產生 zip 檔
      $sss = $ttt->file();
            
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
