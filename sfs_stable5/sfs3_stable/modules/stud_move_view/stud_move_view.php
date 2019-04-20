<?php

// $Id: stud_move_view.php 8336 2015-03-04 02:04:08Z smallduh $

// 載入設定檔
include "stud_move_config.php";
// 認證檢查
sfs_check();

$m_arr = get_sfs_module_set();
extract($m_arr, EXTR_OVERWRITE);

// 不需要 register_globals
if (!ini_get('register_globals')) {
	ini_set("magic_quotes_runtime", 0);
	extract( $_POST );
	extract( $_GET );
	extract( $_SERVER );
}

	$sel_year = curr_year(); //選擇學年
	$sel_seme = curr_seme(); //選擇學期
	$curr_seme = curr_year().curr_seme(); //現在學年學期
	
$today = date("Y-m-d") ;

//未指定日期，取得前一月
if (!$beg_date) {
	 $beg_date =GetMonthAdd( $today ,-1) ;
	 list($ty,$tm,$td) = split('[/-]' , $beg_date) ;
	 $beg_date= "$ty-$tm-01" ;
}	
if (!$end_date) {
	 $end_date = $today  ;
}	

if ($Submit=='匯出轉入學生資料') {
	 $filename = "newstud.xls";
	 header("Content-disposition: attachment; filename=$filename");
	 header("Content-type: application/octetstream");
	 //header("Pragma: no-cache");
				//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");
	 header("Expires: 0");	  
	 
	echo "<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; Charset=Big5\"></head><body><table border=1>\n";
	echo "<tr><td>代號</td><td>姓名</td><td>性別</td><td>入學年</td><td>班級</td><td>座號</td><td>生日(西元)</td><td>身份證字號</td><td>父親姓名</td><td>母親姓名</td><td>郵遞區號</td><td>電話</td><td>住址</td><td>緊急聯方式</td><td>地址備註，匯入前要刪除此欄位</td></tr>\n";
	
  $query = "select a.* from stud_move m, stud_base a  where a.student_sn=m.student_sn and m.move_kind in (2,3,4) and m.move_date>='$beg_date' and m.move_date<='$end_date'  and a.stud_study_cond in (0,5) order by a.curr_class_num ";
	//echo  $query ; 
	$result = $CONN->Execute($query)or die($query);
	$zip_arr = get_addr_zip_arr() ;
	
	while (!$result->EOF) {
		$stud_id = $result->fields[stud_id];
		//$s_addres = $result->fields[stud_addr_1];
		$s_home_phone = $result->fields[stud_tel_1];
		$s_offical_phone = $result->fields[stud_tel_2];
		$stud_sex = $result->fields[stud_sex];
		$stud_name = $result->fields[stud_name];
		$curr_class_num = $result->fields[curr_class_num];
		$stud_birthday = $result->fields[stud_birthday];
		$stud_person_id = $result->fields[stud_person_id];
		$addr_zip = $result->fields[addr_zip];
		//取得 郵遞區號

		if ($addr_zip == '') {
			if ( $result->fields[stud_addr_a] <>'') {
		     $addr_ab = $result->fields[stud_addr_a] . $result->fields[stud_addr_b];  	
		     $addr_zip= $zip_arr[$addr_ab] ;
		  } 
    }

		$addr = change_addr(addslashes($result->fields[stud_addr_1]),1);
		$s_addres = "";
		for ($i=2;$i<=12;$i++) $s_addres .= $addr[$i];
		
		$addr_all = $result->fields[stud_addr_1];

		$query2 = "select fath_name,moth_name from stud_domicile where stud_id ='$stud_id'";
		$result2 = $CONN->Execute($query2)or die ($query2) ;
		$fath_name = $result2->fields[fath_name];
		$moth_name = $result2->fields[moth_name];

		echo sprintf("<tr><td>=T(\"%s\")</td><td>%s</td><td>%d</td><td>%s</td>",$stud_id,$stud_name,$stud_sex,substr($stud_id,0,2));
		
		echo sprintf("<td>%d</td><td>%d</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td>",substr($curr_class_num,1,2),substr($curr_class_num,-2),$stud_birthday,$stud_person_id,$fath_name,$moth_name,$addr_zip); 

		echo sprintf("<td>=T(\"%s\")</td><td>%s</td><td>=T(\"%s\")</td><td>%s</td>",$s_home_phone,stripslashes($s_addres),$s_offical_phone,stripslashes($addr_all)); 


		echo"</tr>\n";
		$result->MoveNext();

	}
	echo "</table></body></html>";

	 exit ;
	 
}	
//取得資料---------------------------------------------------------------------------
    $class_list_p = class_base($curr_seme);
		$query = "select a.*,b.stud_name , b.curr_class_num from stud_move a ,stud_base b where a.student_sn=b.student_sn and a.move_date>='$beg_date' and a.move_date<='$end_date'  order by a.move_date desc,a.stud_id desc ";
		$result = $CONN->Execute($query) or die ($query);
		while(!$result->EOF) {
	    $move_kind= $result->fields["move_kind"];
			$stud_name = $result->fields["stud_name"];
			$move_date = $result->fields["move_date"];
			$stud_id = $result->fields["stud_id"];
			$curr_class_num = $result->fields["curr_class_num"];
			
			unset($tmp_array) ;
			$tmp_array[stud_id]= $stud_id ;
			$tmp_array[stud_name]= $stud_name ;
			$tmp_array[move_date]= $move_date ;
			$tmp_array[class_num]= substr($curr_class_num,0,3) ;
			//$tmp_array[class_num]=$class_list_p[substr($curr_class_num,0,3)] ; 
			$tmp_array[class_seat_num]= substr($curr_class_num,3,2) ;       
			//轉入
			if (in_array( $move_kind ,array(2,3,4))) {
				$in_array[] =$tmp_array ; 
		  }	
			//調出	
			if (in_array( $move_kind ,array(6,7,8,11,12))) {
				$out_array[] =$tmp_array ; 

		  } 

			$result->moveNext();
		}
		
//---------------------------------------------------------------------------
head("轉入調出名冊查看");
print_menu($menu_p);
//echo $beg_date ;

$template_dir = $SFS_PATH."/".get_store_path()."/templates";
// 使用 smarty tag
$smarty->left_delimiter="{{";
$smarty->right_delimiter="}}";
//$smarty->debugging = true;


$smarty->assign("beg_date",$beg_date);
$smarty->assign("end_date",$end_date);
$smarty->assign("arr_in",$in_array); //調入
$smarty->assign("arr_out",$out_array);//轉出


$smarty->assign("template_dir",$template_dir);

$smarty->display("$template_dir/main.htm");

foot();

?>