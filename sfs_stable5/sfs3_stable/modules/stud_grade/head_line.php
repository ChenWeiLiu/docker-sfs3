<?php 
//內容 畢業生一覽表
   
   $ttt = new easyzip; 

   //前半段的一行
   $data_con1 = $ttt->read_file(dirname(__FILE__)."/$oo_path/con_c1");
   //後半段的一行
   $data_con2 = $ttt->read_file(dirname(__FILE__)."/$oo_path/con_c2");
   //頁首標題
   $data_h = $ttt->read_file(dirname(__FILE__)."/$oo_path/con_h");
   //頁尾
   $data_e = $ttt->read_file(dirname(__FILE__)."/$oo_path/con_e");

//每一列   
function content_line($is_head=0) {
  global $stud_id,$stud_name,$stud_birthday,$stud_sex,$curr_class_name,$t_year,$grade_num_stri,$stud_graduate_num ,$grad_num;
  global $oo_path,$data_con1 ,$data_con2 ;
  
  if ($stud_sex =='1') {
	$stud_sex_temp = "男";	
  }
  else {
	$stud_sex_temp = "女";
  }

  $temp = explode ("-",$stud_birthday);
  $stud_birthday = sprintf ("%s年%s月%s日", Num2CNum($temp[0]-1911),Num2CNum(intval($temp[1])),Num2CNum(intval($temp[2])));

  $stud_class = $curr_class_name ;
  
  $temp_arr["class"]=$stud_class ;
  $temp_arr["name"]=$stud_name ;
  $temp_arr["sex"]=$stud_sex_temp ;
  $temp_arr["birth"]=$stud_birthday ;
  $temp_arr["grad_id"]=$stud_graduate_num ;
  $temp_arr["num"]=$grad_num ;

    $ttt = new easyzip; 

  if($is_head==0)
     $mstr = $ttt->change_temp($temp_arr,$data_con1) ;
  else 	
     $mstr = $ttt->change_temp($temp_arr,$data_con2) ;
     
  return $mstr ; 
}


//標題
function title_line() {
   global $school_long_name,$curr_grade_school,$title_str ,$oo_path ,$data_h;
   
   $temp_arr["ttt"]=$title_str ;
   $ttt = new easyzip; 
   
   
   $mstr = $ttt->change_temp($temp_arr,$data_h) ;
   return $mstr ; 
}

//中間 空白
function blank_line() {
   $str= '</table:table><text:p text:style-name="Text body"/><text:p text:style-name="Text body"/><table:table table:name="tab2" table:style-name="tab2"><table:table-column table:style-name="tab2.A"/><table:table-column table:style-name="tab2.B"/><table:table-column table:style-name="tab2.C"/><table:table-column table:style-name="tab2.D"/><table:table-column table:style-name="tab2.E"/><table:table-column table:style-name="tab2.F"/>' ;
   return $str ;
}

//分頁
function page_break() {
   $break ='<text:p text:style-name="break_page"/>';
   return $break ;
}

//頁尾
function sign_form() {
  global $data_e ;	
  return $data_e ;

}
?>
