<?php 

   $ttt = new easyzip; 
   $data_c = $ttt->read_file(dirname(__FILE__)."/$oo_path/con_c");
   $data_h = $ttt->read_file(dirname(__FILE__)."/$oo_path/con_h");
   $data_e = $ttt->read_file(dirname(__FILE__)."/$oo_path/con_e");
//內容
function content_line($is_head=0) {
  global $stud_id,$stud_name,$stud_birthday,$stud_sex,$stud_inhabit_address,$guardian_name,$stud_home_phone,$boy,$girl , $now_classname;
  global $oo_path, $data_c;
  if ($stud_sex =='1') {
	$stud_sex_temp = "男";	
	$boy++;
  }
  else {
	$stud_sex_temp = "女";
	$girl++;
  }

  $temp = explode ("-",$stud_birthday);
  $y= $temp[0]-1911 ;
  $m =intval($temp[1]) ;
  $d=intval($temp[2])  ;
  
  //$stud_birthday = sprintf ('<text:span text:style-name="T2">%d</text:span>年<text:span text:style-name="T2">%d</text:span>月<text:span text:style-name="T2">%d</text:span>日', $temp[0]-1911,$temp[1],$temp[2]);
  
  $temp_arr["name"] = $stud_name ;
  $temp_arr["sex"] =$stud_sex_temp ;
  $temp_arr["tel"] =$stud_home_phone ;
  $temp_arr["y"] =$y ;
  $temp_arr["m"] =$m ;
  $temp_arr["d"] =$d ;
  $temp_arr["da"] =$guardian_name ;
  $temp_arr["add"] =$stud_inhabit_address ;
  $temp_arr["class"] =$now_classname ;
  	
   $ttt = new easyzip; 

   
   $mstr = $ttt->change_temp($temp_arr,$data_c)  ;

  return $mstr ;
}


//標題
function title_line() {
  global $school_long_name,$curr_grade_school ,$oo_path ,$data_h;
  
  $ttt = new easyzip; 
  
  $t1 = $school_long_name.Num2CNum(curr_year()) ."學年度畢業生名冊" ;
  $t2 = "屬" . $curr_grade_school ;

   $temp_arr["tt1"]=$t1 ;
   $temp_arr["TT2"]=$t2 ;
   $temp_arr["S1"]="姓名" ;
   $temp_arr["S2"]="性別" ;
   $temp_arr["S3"]="電話" ;
   $temp_arr["S4"]="生日" ;
   $temp_arr["S5"]="家長" ;
   $temp_arr["S6"]="地址" ;
   $temp_arr["S7"]="備註" ;
   
   
   
   $mstr = $ttt->change_temp($temp_arr,$data_h) ;
   return $mstr ; 
  
}

//中間 空白
function blank_line() {

}

//分頁
function page_break() {
   global $boy,$girl;

   $break ="<text:p text:style-name=\"break_page\"/>";
   return $break ;
}

function tol_sex() {
  global $boy,$girl ,$oo_path ,$data_e;
  $temp_arr["boy"]= $boy ;
  $temp_arr["girl"]= $girl ;
  $temp_arr["tol"]= $boy + $girl ;
  $ttt = new easyzip; 
   
   
   $mstr = $ttt->change_temp($temp_arr,$data_e)  ;

  return $mstr ;
}


?>