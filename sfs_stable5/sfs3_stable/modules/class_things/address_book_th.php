<?php

// $Id: address_book_th.php 6848 2012-08-01 01:55:54Z hsiao $

/*引入學務系統設定檔*/
include "config.php";
include_once "../../include/config.php";


//使用者認證
sfs_check();


if(empty($sel_year))$sel_year = curr_year(); //目前學年
if(empty($sel_seme))$sel_seme = curr_seme(); //目前學期

$teacher_sn=$_SESSION['session_tea_sn'];//取得登入老師的id
//找出任教班級
$class_name=teacher_sn_to_class_name($teacher_sn);

$sex_arr= array(1=>"男" ,2 =>"女") ;


//個資記錄
//只要進入就記錄
$class_description=implode(",",$class_name);
$test=pipa_log("使用教師手冊名單功能(內容含通訊錄)\r\n學年：$sel_year\r\n學期：$sel_seme\r\n班級：$class_description\r\n");		


if($_POST['Submit1']=="下載班級通訊錄"){
  print_key($class_name) ;
}else{
	//秀出網頁
	head("教師手冊名單");

	if ($_GET['act']=="") print_menu($menu_p);
	//設定主網頁顯示區的背景顏色

    
    //$class_name = $class_name_arr[$sel_year] ;
    //$data_class_name = $class_name_arr[$sel_year] ;  
    $data_array = get_class_data($class_name ,$sel_seme) ;
    
        
    //使用樣版
    $template_dir = $SFS_PATH."/".get_store_path()."/templates";
    // 使用 smarty tag
    $smarty->left_delimiter="{{";
    $smarty->right_delimiter="}}";
    //$smarty->debugging = true;
    

    $smarty->assign("data_array",$data_array); 
    $smarty->assign("class_name",$class_name[1] ); 
    
    $smarty->assign("guardian_relation",guardian_relation()); 
    
    $smarty->assign("template_dir",$template_dir);
    
    $smarty->display("$template_dir/address_th.htm");
    

	foot();
}

//取得資料陣列
function get_class_data($class_name    ){
    global $CONN, $sex_arr ;


    $seme_year_seme=sprintf("%03d",curr_year()).curr_seme();


       $sql="select s.stud_id,s.seme_num ,seme_class from stud_seme s , stud_base b where s.stud_id=b.stud_id and  b.stud_study_cond =0 and s.seme_class = '$class_name[0]' and  s.seme_year_seme='$seme_year_seme' order by  s.seme_class ,s.seme_num";

    
    $rs=$CONN->Execute($sql);


    while(!$rs->EOF){
        unset($row_data) ;
        $row_data[stud_id] = $rs->fields["stud_id"];
        $row_data[site_num] = $rs->fields["seme_num"];
        
        $rs_name=$CONN->Execute("select b.* , d.*  from stud_base b ,stud_domicile d  where b.stud_id='$row_data[stud_id]' and b.student_sn =d.student_sn and b.stud_study_cond =0 ");
        $row_data[stud_name] = $rs_name->fields["stud_name"];
		    //$row_data[stud_addr_1] = $rs_name->fields["stud_addr_1"];
		    $s_addres = addslashes($rs_name->fields["stud_addr_1"]);
		    $addr = change_addr($s_addres,1);
		    for ($i=1;$i<=12;$i++) 
		        $row_data[stud_addr_1] .= $addr[$i];
		        
		    $row_data[stud_addr_2] = $rs_name->fields["stud_addr_2"];
		    $row_data[stud_tel_1] = $rs_name->fields["stud_tel_1"];
		    $row_data[stud_tel_2] = $rs_name->fields["stud_tel_2"];
		    $row_data[stud_tel_3] = $rs_name->fields["stud_tel_3"];
		    $row_data[stud_person_id] = $rs_name->fields["stud_person_id"];
		    $row_data[stud_sex] = $sex_arr[$rs_name->fields["stud_sex"]];
		    if ($print_key == "Excel")
		       $row_data[stud_birthday] = $rs_name->fields["stud_birthday"];
		    else 
		       $row_data[stud_birthday] = DtoCh($rs_name->fields["stud_birthday"]);		
		
        $row_data[d_guardian_name] =$rs_name->fields["guardian_name"]  ;
        
        $row_data[guardian_relation] =$rs_name->fields["guardian_relation"]  ;
        
        $row_data[guardian_work_name] =$rs_name->fields["guardian_work_name"]  ;
        
        $data[] = $row_data ;
        $rs->MoveNext();
    }
    
  return $data ;

}	


//列印文件
function print_key($class_name  ){
	global $CONN, $sex_arr , $SFS_PATH ,$smarty   ;
	

    $data_array = get_class_data($class_name ) ;

    
    //使用樣版
    $template_dir = $SFS_PATH."/".get_store_path()."/templates";

    // 使用 smarty tag
    $smarty->left_delimiter="{{";
    $smarty->right_delimiter="}}";
    //$smarty->debugging = true;
    
    
    $smarty->assign("data_array",$data_array); 
    $smarty->assign("guardian_relation",guardian_relation());     
    $smarty->assign("data_class_name",$class_name[1]); 
    
    $smarty->assign("template_dir",$template_dir);
    
    $smarty->display("$template_dir/address_th_exec.htm");

	exit;
}	




function change_addr($addr,$mode=0) {
	//縣市
	$temp_str = split_str($addr,"縣",1);
	if ($temp_str[0] =="")
		$temp_str = split_str($addr,"市",1);
	$res[]=$temp_str[0] ;
	$addr=$temp_str[1];

      	//鄉鎮	
	$temp_str = split_str($addr,"鄉",1);
	if ($temp_str[0] =="")
		$temp_str = split_str($addr,"鎮",1);

	if ($temp_str[0] =="")
		$temp_str = split_str($addr,"市",1);
	
	if ($temp_str[0] =="")
		$temp_str = split_str($addr,"區",1);

	$res[]=$temp_str[0] ;
	$addr=$temp_str[1];

	//村里
	$temp_str = split_str($addr,"村",1);
	if ($temp_str[0] =="")
		$temp_str = split_str($addr,"里",1);

	$res[]=$temp_str[0] ;
	$addr=$temp_str[1];

	//鄰
	$temp_str = split_str($addr,"鄰",$mode);
	$res[]=$temp_str[0] ;
	$addr=$temp_str[1];

	//路
	$temp_str = split_str($addr,"路",1);
	if ($temp_str[0] =="")
		$temp_str = split_str($addr,"街",1);
	
	$res[] = $temp_str[0];
	$addr=$temp_str[1];

      	//段
	$temp_str = split_str($addr,"段",$mode);
	$res[]=$temp_str[0] ;
	$addr=$temp_str[1];

      	//巷
	$temp_str = split_str($addr,"巷",$mode);
	$res[]=$temp_str[0] ;
	$addr=$temp_str[1];

	//弄
	$temp_str = split_str($addr,"弄",$mode);
	$res[]=$temp_str[0] ;
	$addr=$temp_str[1];

	//號
	$temp_str = split_str($addr,"號",$mode);
	$temp_arr = explode("-",$temp_str);
	if (sizeof($temp_arr)>1){
		$res[]=$temp_arr[0];
		$res[]=$temp_arr[1];
	}else {
		$res[]=$temp_str[0];
		$res[]="";
	}
	$addr=$temp_str[1];
	
	//樓
	$temp_str = split_str($addr,"樓",$mode);
	$res[]=$temp_str[0] ;
	$addr=$temp_str[1];

	//樓之
	if ($addr != "") {
		if ($mode)
			$temp_str = $addr;
		else
			$temp_str = substr(chop($addr),2);
	} else
		$temp_str ="";
		
	$res[]=$temp_str ;
      	return $res;
}

function split_str($addr,$str,$last=0) {
      	$temp = explode ($str, $addr);
	if (count($temp)<2 ){
		$t[0]="";
		$t[1]=$addr;
	}else{
		$t[0]=(!empty($last))?$temp[0].$str:$temp[0];
		$t[1]=$temp[1];
	}
	return $t;
}
?>
