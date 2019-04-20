<?php

// $Id: address_book2.php 8508 2015-09-01 13:48:07Z brucelyc $

/*引入學務系統設定檔*/
include "config.php";

//使用者認證
sfs_check();
if(empty($sel_year))$sel_year = curr_year(); //目前學年
if(empty($sel_seme))$sel_seme = curr_seme(); //目前學期
$teacher_sn=$_SESSION['session_tea_sn'];//取得登入老師的id
//找出任教班級
$class_name=teacher_sn_to_class_name($teacher_sn);

//個資記錄
//只要進入就記錄
$class_description=implode(",",$class_name);
$test=pipa_log("通訊錄2\r\n學年：$sel_year\r\n學期：$sel_seme\r\n班級：$class_description\r\n");		

$menu_head = "<html>
	<head>
	<meta http-equiv=\"Content-Type\" content=\"text/html; Charset=Big5\">
	</head>
	<body>";
	
$menu_foot = "</body></html>";

$oo_path = "ooo_addr2";
$sex_arr= array(1=>"男" ,2 =>"女") ;
if($_POST['Submit1']=="下載班級通訊錄") {
  if ($_POST['print_key'] == "sxw")   
     echo ooo();
  else 
     print_key($sel_year,$sel_seme,$_POST['print_key'],$many_col) ;
}else{
	//秀出網頁
	head("班級事務");

	if ($_GET['act']=="") print_menu($menu_p);
	//設定主網頁顯示區的背景顏色
	
	$menu="
		<table cellspacing=2 cellpadding=2>
			<tr>
				<td>
					<form name='form1' method='post' action='{$_SERVER['PHP_SELF']}'> 
					$import_option<input type='submit' name='Submit1' value='下載班級通訊錄'>
					</form>
				</td>
			</tr>
		</table>
		";
	echo $menu;
    $seme_year_seme=sprintf("%03d",curr_year()).curr_seme();
    $sql="select stud_id,seme_num from stud_seme where seme_class='$class_name[0]' and  seme_year_seme='$seme_year_seme' order by  seme_num";
    $rs=$CONN->Execute($sql);
    
   // echo  $sql ;
    $m=0;
    echo "<table bgcolor='#000000' border=0 cellspacing=1 cellpadding=2>
			<tr bgcolor='#FAF799'>
				
				<td colspan='1'>學號</td>
				<td colspan='1'>座號</td>
				<td colspan='1'>姓名</td>
				<td colspan='1'>性別</td>
				<td colspan='1'>身份証</td>
				<td colspan='1'>生日</td>
				<td colspan='1'>連絡地址</td>
				<td colspan='1'>電話</td>
				<td colspan='1'>監護人</td>
				<td colspan='1'>工作地</td>
				<td colspan='1'>職稱</td>
				<td colspan='1'>緊急電話</td>
			</tr>";
	while(!$rs->EOF){
        $stud_id = $rs->fields["stud_id"];
        $site_num = $rs->fields["seme_num"];
        $rs_name=$CONN->Execute("select b.* , d.*  from stud_base b ,stud_domicile d  where b.stud_id='$stud_id' and b.student_sn=d.student_sn and b.stud_study_cond =0 ");
        if ($rs_name->fields["stud_name"]) {
        $stud_name = $rs_name->fields["stud_name"];
		$stud_addr_1 = $rs_name->fields["stud_addr_1"];
		$stud_addr_2 = $rs_name->fields["stud_addr_2"];
		$stud_tel_1 = $rs_name->fields["stud_tel_1"];
		$stud_tel_2 = $rs_name->fields["stud_tel_2"];
		//$stud_tel_3 = $rs_name->fields["stud_tel_3"];
		$stud_person_id = $rs_name->fields["stud_person_id"];
		$stud_sex = $sex_arr[$rs_name->fields["stud_sex"]];
		$stud_birthday = DtoCh($rs_name->fields["stud_birthday"]);		
		
	       // $d_guardian_name ="&nbsp;";

                $d_guardian_name =$rs_name->fields["guardian_name"]  ;
                $guardian_unit =$rs_name->fields["guardian_unit"]  ;
                $guardian_work_name =$rs_name->fields["guardian_work_name"]  ;

        echo "<tr bgcolor='#FFFFFF'>
                                <td>$stud_id</td>
				<td>$site_num</td>
				<td>$stud_name</td>
				<td>$stud_sex</td>
				<td>$stud_person_id</td>
				<td>$stud_birthday</td>
				<td>$stud_addr_2</td>
				<td>$stud_tel_1</td>
				<td>$d_guardian_name</td>
				<td>$guardian_unit</td>
				<td>$guardian_work_name</td>
				<td>$stud_tel_2</td>
				
			  </tr>";
		$m++;
		}
        $rs->MoveNext();
    }
	echo "</table>";
	//結束主網頁顯示區
	echo "</td>";
	echo "</tr>";
	echo "</table>";
	//程式檔尾
	foot();
}

//列印文件
function print_key($sel_year="",$sel_seme="",$print_key="",$cols=""){
	global $CONN, $class_name ,$menu_head,$menu_foot,$sex_arr;
	
	
	//轉出為excel、word	
	if ($print_key=="Excel")
		$filename =  "name.xls"; 	
	else if ($print_key=="Word")
		$filename =  "name.doc";
	header("Content-disposition: attachment; filename=$filename");
	header("Content-type: application/octetstream");
	//header("Pragma: no-cache");
					//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");

	header("Expires: 0");
	
    $seme_year_seme=sprintf("%03d",curr_year()).curr_seme();
    $sql="select stud_id,seme_num from stud_seme where seme_class='$class_name[0]' and  seme_year_seme='$seme_year_seme' order by  seme_num";
    $rs=$CONN->Execute($sql);
    
   // echo  $sql ;
    $m=0;
    echo "$menu_head <table  border=1 cellspacing=1 cellpadding=2 width=95%>
			<tr >
				
				<td colspan='1'>學號</td>
				<td colspan='1'>座號</td>
				<td colspan='1'>姓名</td>
				<td colspan='1'>性別</td>
				<td colspan='1'>身份証</td>
				<td colspan='1'>生日</td>
				<td colspan='1'>連絡地址</td>
				<td colspan='1'>電話</td>
				<td colspan='1'>監護人</td>
				<td colspan='1'>工作地</td>
				<td colspan='1'>職稱</td>
				<td colspan='1'>緊急電話</td>
			</tr>";
	while(!$rs->EOF){
        $stud_id = $rs->fields["stud_id"];
        $site_num = $rs->fields["seme_num"];
        $rs_name=$CONN->Execute("select b.* , d.*  from stud_base b ,stud_domicile d  where b.stud_id='$stud_id' and b.student_sn =d.student_sn and b.stud_study_cond =0  ");
	 
      if ($rs_name->fields["stud_name"]) { 
        $stud_name = $rs_name->fields["stud_name"];
		$stud_addr_1 = $rs_name->fields["stud_addr_1"];
		$stud_addr_2 = $rs_name->fields["stud_addr_2"];
		$stud_tel_1 = $rs_name->fields["stud_tel_1"];
		$stud_tel_2 = $rs_name->fields["stud_tel_2"];
		//$stud_tel_3 = $rs_name->fields["stud_tel_3"];
		$stud_person_id = $rs_name->fields["stud_person_id"];
		$stud_sex = $sex_arr[$rs_name->fields["stud_sex"]];
		$stud_birthday = DtoCh($rs_name->fields["stud_birthday"]);		
	       // $d_guardian_name ="&nbsp;";

                $d_guardian_name =$rs_name->fields["guardian_name"]  ;
                $guardian_unit =$rs_name->fields["guardian_unit"]  ;
                $guardian_work_name =$rs_name->fields["guardian_work_name"]  ;

        echo "<tr >
                                <td>$stud_id</td>
				<td>$site_num</td>
				<td>$stud_name</td>
				<td>$stud_sex</td>
				<td>$stud_person_id</td>
				<td>&nbsp; $stud_birthday</td>
				<td>$stud_addr_2</td>
				<td>$stud_tel_1</td>
				<td>$d_guardian_name</td>
				<td>$guardian_unit</td>
				<td>$guardian_work_name</td>
				<td>$stud_tel_2</td>
				
			  </tr>";
		$m++;
	   }	
        $rs->MoveNext();
    }
	echo "</table> $menu_foot";
	
	exit;
}	

function ooo(){
	global $CONN,$class_name ,$sel_year , $allyear ,$addr_head ,$addr_line ,$oo_path;



	$filename="addressbook".$class_name[0].".sxw";
	$break ="<text:p text:style-name=\"break_page\"/>";

    //新增一個 zipfile 實例
	$ttt = new EasyZip;
	$ttt->setPath($oo_path);
	$ttt->addDir('META-INF');
	$ttt->addfile("settings.xml");
	$ttt->addfile("styles.xml");
	$ttt->addfile("meta.xml");

	//讀出 content.xml
	$data = $ttt->read_file(dirname(__FILE__)."/$oo_path/content_doc.xml");
 
        
        
	//將 content.xml 的 tag 取代
        //$class_name=teacher_sn_to_class_name($_SESSION['session_tea_sn']);
         
        set_time_limit(180) ;
        
         
         $cont = all_ooo($class_name[0]) ;
        
        //echo $cont ;
        //exit ;

        //$temp_arr["head"] = $head;
	$temp_arr["cont"] = $cont;
	// change_temp 會將陣列中的 big5 轉為 UTF-8 讓 openoffice 可以讀出
	$replace_data = $ttt->change_temp2($temp_arr,$data);
 
	// 加入 content.xml 到zip 中
	$ttt->add_file($replace_data,"content.xml");

	//產生 zip 檔
	$sss = $ttt->file();

	//以串流方式送出 ooo.sxw
	header("Content-disposition: attachment; filename=$filename");
	header("Content-type: application/vnd.sun.xml.writer");
	//header("Pragma: no-cache");
					//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");

	header("Expires: 0");

	echo $sss;

	exit;
	return;
}



function all_ooo($sel_year  ) {
	global $CONN,$class_name ,$oo_path ,$sex_arr;        
	
    $class_name_arr = class_base() ;
    $class_name[0]= $sel_year ;
    $class_name[1]= $class_name_arr[$sel_year] ;	
	
    $seme_year_seme=sprintf("%03d",curr_year()).curr_seme();
    $sql="select stud_id,seme_num from stud_seme where seme_class='$sel_year' and  seme_year_seme='$seme_year_seme' order by seme_num";
    $rs=$CONN->Execute($sql);
    $m=0;

    while(!$rs->EOF){
        $stud_id = $rs->fields["stud_id"];
        $site_num[$m]  = $rs->fields["seme_num"];
        /*
        $rs_name=$CONN->Execute("select stud_name,stud_tel_1,stud_tel_2,stud_tel_3,stud_addr_1,stud_addr_1 from stud_base where stud_id='$stud_id'");
        $stud_name = $rs_name->fields["stud_name"];
		$stud_addr_1 = $rs_name->fields["stud_addr_1"];
		$stud_addr_2 = $rs_name->fields["stud_addr_2"];
		$stud_tel_1 = $rs_name->fields["stud_tel_1"];
		$stud_tel_2 = $rs_name->fields["stud_tel_2"];
		$stud_tel_3 = $rs_name->fields["stud_tel_3"];
	*/	
        $rs_name=$CONN->Execute("select b.* , d.*  from stud_base b ,stud_domicile d  where b.stud_id='$stud_id' and b.student_sn =d.student_sn and b.stud_study_cond =0  ");

     if ($rs_name->fields["stud_name"]) {   
     	  $stud_id_arr[$m] = $rs_name->fields["stud_id"];
        $stud_name[$m] = $rs_name->fields["stud_name"];
    		$stud_addr_1[$m] = $rs_name->fields["stud_addr_1"];
    		//$stud_addr_2 = $rs_name->fields["stud_addr_2"];
    		$stud_tel_1[$m] = $rs_name->fields["stud_tel_1"];
    		$stud_tel_2[$m] = $rs_name->fields["stud_tel_2"];
    		//$stud_tel_3 = $rs_name->fields["stud_tel_3"];
    		$stud_person_id[$m] = $rs_name->fields["stud_person_id"];
    		$stud_sex[$m] = $sex_arr[$rs_name->fields["stud_sex"]];
    		$stud_birthday[$m] = DtoCh($rs_name->fields["stud_birthday"]);
		
	        //$d_guardian_name ="&nbsp;";

        $d_guardian_name[$m] =$rs_name->fields["guardian_name"]  ;
        $guardian_unit[$m] =$rs_name->fields["guardian_unit"]  ;
        $guardian_work_name[$m] =$rs_name->fields["guardian_work_name"]  ;
                
		
        $m++;
      }  
        $rs->MoveNext();
    }
    
        //新增一個 zipfile 實例
    $ttt = new EasyZip;
    
    $addr_head = $ttt->read_file(dirname(__FILE__)."/$oo_path/content_head.xml");
    $addr_line = $ttt->read_file(dirname(__FILE__)."/$oo_path/content_row.xml");
    $cont = $addr_head;

    
    for($i=0;$i<count($stud_id_arr);$i++){
        
        $temp_arr["class"] = $class_name[1] ;
        $temp_arr["cid"] = $stud_id_arr[$i] ;
        $temp_arr["num"] = $site_num[$i] ;
        $temp_arr["sex"] = $stud_sex[$i] ;
        $temp_arr["name"] = $stud_name[$i] ;
        $temp_arr["pid"] = $stud_person_id[$i] ;
        $temp_arr["birth"] = $stud_birthday[$i] ;
        $temp_arr["addr"] = $stud_addr_1[$i]  ;
        $temp_arr["tel1"] = $stud_tel_1[$i] ;
        $temp_arr["parent"] = $d_guardian_name[$i] ;
        $temp_arr["work"] = $guardian_unit[$i] ;
        $temp_arr["worker"] = $guardian_work_name[$i] ;
        $temp_arr["tel2"] = $stud_tel_2[$i] ;
        
        $replace_data = $ttt->change_temp($temp_arr,$addr_line); 
        
        $cont.= $replace_data ;
    }
    $cont.= '</table:table>' ;	
    return  $cont ;
    
}
?>
