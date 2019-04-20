<?php

// $Id: address_book.php 7704 2013-10-23 08:51:29Z smallduh $

/*引入學務系統設定檔*/
include "../../include/config.php";
include "../../include/sfs_oo_zip2.php";
include_once "../../include/sfs_case_PLlib.php";

//引入函數
//include "./my_fun.php";
require_once "./module-cfg.php";

//使用者認證
sfs_check();


if(empty($sel_year))$sel_year = curr_year(); //目前學年
if(empty($sel_seme))$sel_seme = curr_seme(); //目前學期

$sel_year = $_POST['sel_year'] ;
$allyear = $_POST['allyear'] ;
if ($allyear) $chk_allyear = "checked" ;

if ($sel_year=="") $sel_year ="601" ;


$class_name_arr = class_base() ;
$class_name[0]= $sel_year ;
$class_name[1]= $class_name_arr[$sel_year] ;


$oo_path = "oooo";
$sex_arr= array(1=>"男" ,2 =>"女") ;
$print_key = $_POST[print_key];

if($_POST['Submit1']=="下載班級通訊錄"){
  if ($print_key == "sxw")   
     echo ooo();
  else 
     print_key($sel_year,$sel_seme,$print_key,$allyear) ;
}else{
	//秀出網頁
	head("全校班級名冊");

	if ($_GET['act']=="") print_menu($menu_p);
	//設定主網頁顯示區的背景顏色
	

		$sel1 = new drop_select(); //選單類別
		$sel1->s_name = "sel_year"; //選單名稱		
		$sel1->id = $sel_year;		
		$sel1->has_empty = false;
		$sel1->arr = $class_name_arr ; //內容陣列(六個學年)
		$sel1->is_submit = true;
		$sel1->bgcolor = "#DDFFEE";
		$sel1->font_style ="font-size: 15px;font-weight: bold";
        $class_select = "選擇班級:" . $sel1->get_select();
	$menu=" <html><head><meta http-equiv=\"Content-Type\" content=\"text/html; Charset=Big5\"></head><body>
		<table cellspacing=2 cellpadding=2>
			<tr>
				<td>
					<form name='form1' method='post' action='{$_SERVER['PHP_SELF']}'> 
					 $class_select  
					<input name='allyear' type='checkbox' value='1' $chk_allyear >全學年
					$import_option <input type='submit' name='Submit1' value='下載班級通訊錄'>
					</form>
				</td>
			</tr>
		</table></body></html>";
	echo $menu;
    $seme_year_seme=sprintf("%03d",curr_year()).curr_seme();
    $sql="select s.stud_id,s.seme_num from stud_seme s , stud_base b  where s.stud_id = b.stud_id and s.seme_class='$class_name[0]' and  s.seme_year_seme='$seme_year_seme'   and b.stud_study_cond =0 order by  seme_num ";
    $rs=$CONN->Execute($sql);
    
    //echo  $sql ;
    $m=0;
    echo "<html><head><meta http-equiv='Content-Type' content='text/html; charset=big5'></head><body><table bgcolor='#000000' border=0 cellspacing=1 cellpadding=2>
			<tr bgcolor='#FAF799'>
				
				<td colspan='1'>學號</td>
				<td colspan='1'>座號</td>
				<td colspan='1'>姓名</td>
				<td colspan='1'>性別</td>
				<td colspan='1'>英文姓名</td>
				<td colspan='1'>身份証</td>
				<td colspan='1'>生日</td>
				<td colspan='1'>地址</td>
				<td colspan='1'>電話</td>
				<td colspan='1'>監護人</td>
				<td colspan='1'>工作地</td>
				<td colspan='1'>職稱</td>
				<td colspan='1'>緊急電話</td>
			</tr>";
	while(!$rs->EOF){
        $stud_id[$m] = $rs->fields["stud_id"];
        $site_num[$m] = $rs->fields["seme_num"];
        $rs_name=$CONN->Execute(" select b.* , d.*  from stud_base b ,stud_domicile d  where b.stud_id='$stud_id[$m]' and b.stud_id =d.stud_id   ");
        // echo " select b.* , d.*  from stud_base b ,stud_domicile d  where b.stud_id='$stud_id[$m]' and b.stud_id =d.stud_id " ;  
        
        $stud_name[$m] = $rs_name->fields["stud_name"];
		$stud_name_eng[$m] = $rs_name->fields["stud_name_eng"];
		$stud_addr_1[$m] = $rs_name->fields["stud_addr_1"];

		$stud_tel_1[$m] = $rs_name->fields["stud_tel_1"];
		$stud_tel_2[$m] = $rs_name->fields["stud_tel_2"];

		$stud_person_id[$m] = $rs_name->fields["stud_person_id"];
		$stud_sex[$m] = $sex_arr[$rs_name->fields["stud_sex"]];
		$stud_birthday[$m] = DtoCh($rs_name->fields["stud_birthday"]);		
		
        $guardian_name[$m] =$rs_name->fields["guardian_name"]  ;
        $guardian_unit[$m] =$rs_name->fields["guardian_unit"]  ;
        $guardian_work_name[$m] =$rs_name->fields["guardian_work_name"]  ;
 
        echo "<tr bgcolor='#FFFFFF'>
                                <td>$stud_id[$m]</td>
				<td>$site_num[$m]</td>
				<td>$stud_name[$m]</td>
				<td>$stud_sex[$m]</td>
				<td>$stud_name_eng[$m]</td>
				<td>$stud_person_id[$m]</td>
				<td>$stud_birthday[$m]</td>
				<td>$stud_addr_1[$m]</td>
				<td>$stud_tel_1[$m]</td>
				<td>$guardian_name[$m]</td>
				<td>$guardian_unit[$m]</td>
				<td>$guardian_work_name[$m]</td>
				<td>$stud_tel_2[$m]</td>
				
			  </tr>";
		$m++;
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
function print_key($sel_year="",$sel_seme="",$print_key="" ,$allyear=0){
	global $CONN, $class_name ,$sex_arr;
	
	
	//轉出為excel、word	
	if ($print_key=="Excel")
		$filename =  "name.xls"; 	
	else if ($print_key=="Word")
		$filename =  "name.doc";
 
	header("Content-disposition: filename=$filename");
	header("Content-type: application/octetstream ; Charset=Big5");
	//header("Pragma: no-cache");
					//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");

	header("Expires: 0");
	
    $class_name_arr = class_base() ;    	
    $classid_search =  " = '$class_name[0]' ";
    	
    if ($allyear)  
       	$classid_search =  " like '" . substr($class_name[0],0,1) ."%'";
 
    $seme_year_seme=sprintf("%03d",curr_year()).curr_seme();
    $sql="select s.stud_id,s.seme_num ,seme_class from stud_seme s , stud_base b where s.stud_id=b.stud_id and  b.stud_study_cond =0 and s.seme_class $classid_search and  s.seme_year_seme='$seme_year_seme' order by  s.seme_class ,s.seme_num";
    $rs=$CONN->Execute($sql);
    
    //echo  $sql ;
    $m=0;
  	echo "<html><head><meta http-equiv='Content-Type' content='text/html; charset=big5'></head><body>
		<table border=1 cellspacing=1 cellpadding=2>
			<tr >
				<td colspan='1'>班級</td>
				<td colspan='1'>學號</td>
				<td colspan='1'>座號</td>
				<td colspan='1'>姓名</td>
				<td colspan='1'>性別</td>
				<td colspan='1'>英文姓名</td>
				<td colspan='1'>身份証</td>
				<td colspan='1'>生日</td>
				<td colspan='1'>地址</td>
				<td colspan='1'>電話</td>
				<td colspan='1'>監護人</td>
				<td colspan='1'>工作地</td>
				<td colspan='1'>職稱</td>
				<td colspan='1'>緊急電話</td>
			</tr>";
	while(!$rs->EOF){
	$now_classname = $class_name_arr[$rs->fields["seme_class"]] ;
        $stud_id[$m] = $rs->fields["stud_id"];
        $site_num[$m] = $rs->fields["seme_num"];
        $rs_name=$CONN->Execute("select b.* , d.*  from stud_base b ,stud_domicile d  where b.stud_id='$stud_id[$m]' and b.stud_id =d.stud_id and b.stud_study_cond =0 ");
        $stud_name[$m] = $rs_name->fields["stud_name"];
		$stud_name_eng[$m] = $rs_name->fields["stud_name_eng"];
		$stud_addr_1[$m] = $rs_name->fields["stud_addr_1"];
		//$stud_addr_2[$m] = $rs_name->fields["stud_addr_2"];
		$stud_tel_1[$m] = $rs_name->fields["stud_tel_1"];
		$stud_tel_2[$m] = $rs_name->fields["stud_tel_2"];
		//$stud_tel_3[$m] = $rs_name->fields["stud_tel_3"];
		$stud_person_id[$m] = $rs_name->fields["stud_person_id"];
		$stud_sex[$m] = $sex_arr[$rs_name->fields["stud_sex"]];
		if ($print_key == "Excel")
		   $stud_birthday[$m] = $rs_name->fields["stud_birthday"];
		else 
		   $stud_birthday[$m] = DtoCh($rs_name->fields["stud_birthday"]);		
		
	       // $d_guardian_name ="&nbsp;";

                $d_guardian_name[$m] =$rs_name->fields["guardian_name"]  ;
                $guardian_unit[$m] =$rs_name->fields["guardian_unit"]  ;
                $guardian_work_name[$m] =$rs_name->fields["guardian_work_name"]  ;
                /*
                $is_same_gua[$m] = $rs_name->fields["is_same_gua"] ;
                if ($is_same_gua==1) {
                   $d_guardian_name[$m] = $rs_name->fields["fath_name"] ; //同生父
                   $guardian_unit[$m] =$rs_name->fields2["fath_unit"]  ;
                   $guardian_work_name[$m] =$rs_name->fields["fath_work_name"]  ;           
                }   
                if ($is_same_gua==2) {
                   $d_guardian_name[$m] =$rs_name->fields["moth_name"] ; //同生母
                   $guardian_unit[$m] =$rs_name->fields["moth_unit"]  ;
                   $guardian_work_name[$m] =$rs_name->fields["moth_work_name"]  ;                          
                }   	
                */	
	echo "<tr>
                                <td>$now_classname</td>
                                <td>&nbsp;$stud_id[$m]</td>
				<td>$site_num[$m]</td>
				<td>$stud_name[$m]</td>
				<td>$stud_sex[$m]</td>
				<td>$stud_name_eng[$m]</td>
				<td>$stud_person_id[$m]</td>
				<td>$stud_birthday[$m]</td>
				<td>$stud_addr_1[$m]</td>
				<td>&nbsp;$stud_tel_1[$m]</td>
				<td>$d_guardian_name[$m]</td>
				<td>$guardian_unit[$m]</td>
				<td>$guardian_work_name[$m]</td>
				<td>&nbsp;$stud_tel_2[$m]</td>
				
			  </tr>";
		$m++;
        $rs->MoveNext();
    }
	echo "</table></body></html>";

	exit;
}	

function ooo(){
	global $CONN,$class_name ,$sel_year , $allyear ,$addr_head ,$addr_line ,$oo_path;



	$filename="addressbook".$class_name[0].".sxw";
	$break ="<text:p text:style-name=\"break_page\"/>";

    //新增一個 zipfile 實例
	$ttt = new EasyZip;
	$ttt->setpath($oo_path);
	$ttt->addDir('META-INF');
	$ttt->addfile('settings.xml');
	$ttt->addfile('styles.xml');
	$ttt->addfile('meta.xml');
	
	//讀出 content.xml
	$data = $ttt->read_file(dirname(__FILE__)."/$oo_path/content_doc.xml");
 
        
        
	//將 content.xml 的 tag 取代
        //$class_name=teacher_sn_to_class_name($_SESSION['session_tea_sn']);
         
        set_time_limit(180) ;
        
         
        if ( $allyear ) {
            $seme_year_seme=sprintf("%03d",curr_year()).curr_seme();
            $sql="select seme_class from stud_seme where seme_class like '$sel_year[0]%' and  seme_year_seme ='$seme_year_seme' group by  seme_class ";
            $rs=$CONN->Execute($sql);
            $m=0;
    
            while(!$rs->EOF){
               $class_id = $rs->fields["seme_class"];
               $cont .= all_ooo($class_id ) . $break ;
               
               $m++;
               $rs->MoveNext();           
            }
        }
        else  $cont = all_ooo($sel_year ) ;
        
        //echo $cont ;
        //exit ;

        //$temp_arr["head"] = $head;
	$temp_arr["cont"] = $cont;
	// change_temp 會將陣列中的 big5 轉為 UTF-8 讓 openoffice 可以讀出
	$replace_data = $ttt->change_temp2($temp_arr,$data);
 
	// 加入 content.xml 到zip 中
	$ttt->add_file($replace_data,"content.xml");

	//產生 zip 檔
	$sss = & $ttt->file();

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
    $sql="select s.stud_id,s.seme_num from stud_seme s , stud_base b where s.stud_id=b.stud_id and b.stud_study_cond =0 and s.seme_class='$sel_year' and  s.seme_year_seme='$seme_year_seme' order by s.seme_num";
    //echo $sql ;
    $rs=$CONN->Execute($sql);
    $m=0;

    while(!$rs->EOF){
        $stud_id[$m] = $rs->fields["stud_id"];
        $site_num[$m] = $rs->fields["seme_num"];
        /*
        $rs_name=$CONN->Execute("select stud_name,stud_tel_1,stud_tel_2,stud_tel_3,stud_addr_1,stud_addr_1 from stud_base where stud_id='$stud_id[$m]'");
        $stud_name[$m] = $rs_name->fields["stud_name"];
		$stud_addr_1[$m] = $rs_name->fields["stud_addr_1"];
		$stud_addr_2[$m] = $rs_name->fields["stud_addr_2"];
		$stud_tel_1[$m] = $rs_name->fields["stud_tel_1"];
		$stud_tel_2[$m] = $rs_name->fields["stud_tel_2"];
		$stud_tel_3[$m] = $rs_name->fields["stud_tel_3"];
	*/	
        $rs_name=$CONN->Execute("select b.* , d.*  from stud_base b ,stud_domicile d  where b.stud_id='$stud_id[$m]' and b.stud_id =d.stud_id ");
        $stud_name[$m] = $rs_name->fields["stud_name"];
		$stud_addr_1[$m] = $rs_name->fields["stud_addr_1"];
		//$stud_addr_2[$m] = $rs_name->fields["stud_addr_2"];
		$stud_tel_1[$m] = $rs_name->fields["stud_tel_1"];
		$stud_tel_2[$m] = $rs_name->fields["stud_tel_2"];
		//$stud_tel_3[$m] = $rs_name->fields["stud_tel_3"];
		$stud_person_id[$m] = $rs_name->fields["stud_person_id"];
		$stud_sex[$m] = $sex_arr[$rs_name->fields["stud_sex"]];
		$stud_birthday[$m] = DtoCh($rs_name->fields["stud_birthday"]);
		
	        //$d_guardian_name ="&nbsp;";

                $d_guardian_name[$m] =$rs_name->fields["guardian_name"]  ;
                $guardian_unit[$m] =$rs_name->fields["guardian_unit"]  ;
                $guardian_work_name[$m] =$rs_name->fields["guardian_work_name"]  ;
                
                /* 
                $fath_name = $rs_name->fields["fath_name"] ;
                if (($d_guardian_name[$m]  =="" ) and $fath_name) {
                   $d_guardian_name[$m] = $rs_name->fields["fath_name"] ; //同生父
                   $guardian_unit[$m] =$rs_name->fields2["fath_unit"]  ;
                   $guardian_work_name[$m] =$rs_name->fields["fath_work_name"]  ;           
                } 
                   
                if (($d_guardian_name[$m]  =="" ) and $fath_name) {
                   $d_guardian_name[$m] =$rs_name->fields["moth_name"] ; //同生母
                   $guardian_unit[$m] =$rs_name->fields["moth_unit"]  ;
                   $guardian_work_name[$m] =$rs_name->fields["moth_work_name"]  ;                          
                }   
                */				
        $m++;
        $rs->MoveNext();
    }
    
        //新增一個 zipfile 實例
    $ttt = new zipfile;
    
    $addr_head = $ttt->read_file(dirname(__FILE__)."/$oo_path/content_head.xml");
    $addr_line = $ttt->read_file(dirname(__FILE__)."/$oo_path/content_row.xml");
    $cont = $addr_head;

    
    for($i=0;$i<count($stud_id);$i++){
        
        $temp_arr["class"] = $class_name[1] ;
        $temp_arr["cid"] = $stud_id[$i] ;
        $temp_arr["num"] = $site_num[$i] ;
        $temp_arr["sex"] = $stud_sex[$i] ;
        $temp_arr["name"] = $stud_name[$i] ;
		$temp_arr["name_eng"] = $stud_name_eng[$i] ;
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
