<?php

// $Id: address_book2.php 8503 2015-08-28 05:36:36Z smallduh $

/*引入學務系統設定檔*/
include "../../include/config.php";
include "../../include/sfs_oo_zip2.php";
include_once "../../include/sfs_case_PLlib.php";
require_once "../../include/sfs_case_excel.php";

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

if ($sel_year=="") $sel_year ="101" ;


$class_name_arr = class_base() ;
$class_name[0]= $sel_year ;
$class_name[1]= $class_name_arr[$sel_year] ;


$oo_path = "oooo";
$sex_arr= array(1=>"男" ,2 =>"女") ;
$print_key = $_POST[print_key];

if($_POST['Submit1']=="下載班級通訊錄"){
  if ($print_key == "sxw")
     echo ooo($sel_year,$sel_seme);
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
    $class_select =  $sel1->get_select();

    $data_array = get_class_data($sel_year,$sel_seme) ;



    //使用樣版
    $template_dir = $SFS_PATH."/".get_store_path()."/templates";
    // 使用 smarty tag
    $smarty->left_delimiter="{{";
    $smarty->right_delimiter="}}";
    //$smarty->debugging = true;

    $smarty->assign("class_select",$class_select);
    $smarty->assign("import_option",$import_option);
    $smarty->assign("data_array",$data_array);


    $smarty->assign("template_dir",$template_dir);

    $smarty->display("$template_dir/address.htm");


	foot();
}

//取得資料陣列
function get_class_data($sel_year="",$sel_seme="",$print_key="" ,$allyear=0){
    global $CONN, $class_name ,$sex_arr , $class_name_arr;

    $classid_search =  " = '$sel_year' ";

    //全學年
    if ($allyear)
       	$classid_search =  " like '" . substr($sel_year,0,1) ."%'";


    $seme_year_seme=sprintf("%03d",curr_year()).curr_seme();
    $sql="select s.stud_id,s.seme_num ,seme_class from stud_seme s , stud_base b where s.stud_id=b.stud_id and  b.stud_study_cond =0 and s.seme_class $classid_search and  s.seme_year_seme='$seme_year_seme' order by  s.seme_class ,s.seme_num";
    $rs=$CONN->Execute($sql);


    while(!$rs->EOF){

	      $row_data[classname] = $class_name_arr[$rs->fields["seme_class"]] ;
        $row_data[stud_id] = $rs->fields["stud_id"];
        $row_data[site_num] = $rs->fields["seme_num"];
        $rs_name=$CONN->Execute("select b.* , d.*  from stud_base b  LEFT JOIN stud_domicile d  ON b.student_sn=d.student_sn
         where b.stud_id='$row_data[stud_id]'  and b.stud_study_cond =0 ");
        $row_data[stud_name] = $rs_name->fields["stud_name"];
		$row_data[stud_name_eng] = $rs_name->fields["stud_name_eng"];
		    $row_data[stud_addr_1] = $rs_name->fields["stud_addr_1"];
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
        $row_data[guardian_unit] =$rs_name->fields["guardian_unit"]  ;
        $row_data[guardian_work_name] =$rs_name->fields["guardian_work_name"]  ;

        $data[] = $row_data ;
        $rs->MoveNext();
    }
	//echo "<PRE>";
	//print_r($data);
	//echo "</PRE>";

  return $data ;

}


//列印文件
function print_key($sel_year="",$sel_seme="",$print_key="" ,$allyear=0){
	global $CONN, $class_name ,$sex_arr , $SFS_PATH ,$smarty ;

    $data_array = get_class_data($sel_year,$sel_seme,$print_key ,$allyear) ;

	//轉出為excel、word
	if ($print_key=="Excel") {
		
	 $x=new sfs_xls();
	 $x->setUTF8();
	 $x->filename='name.xls';
	 $x->setBorderStyle(1);
	 $x->addSheet("通訊錄");
	 $x->items[0]=array('學號','班級','座號','姓名','性別','英文姓名','身份證號','生日','地址','電話','監護人','工作地','職稱','緊急電話');

   foreach ($data_array as $k=>$v) {
     $x->items[]=array($v['stud_id'],$v['classname'],$v['site_num'],$v['stud_name'],$v['stud_sex'],$v['stud_name_eng'],$v['stud_person_id'],$v['stud_birthday'],$v['stud_addr_1'],$v['stud_tel_1'],$v['d_guardian_name'],$v['guardian_unit'],$v['guardian_work_name'],$v['stud_tel_2']);
    }

   $x->writeSheet();
	 $x->process();
	
	}	else if ($print_key=="Word") {
		$filename =  "name.doc";
  
	header("Content-disposition: filename=$filename");
	header("Content-type: application/octetstream");
	//header("Pragma: no-cache");
					//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
		header("Cache-Control: max-age=0");
		header("Pragma: public");
		header("Expires: 0");


    //使用樣版
    $template_dir = $SFS_PATH."/".get_store_path()."/templates";

    // 使用 smarty tag
    $smarty->left_delimiter="{{";
    $smarty->right_delimiter="}}";
    //$smarty->debugging = true;


    $smarty->assign("data_array",$data_array);


    $smarty->assign("template_dir",$template_dir);

    $smarty->display("$template_dir/address_exec.htm");
  }
	exit;
}

function ooo($sel_year,$sel_seme){
	global $CONN, $class_name , $allyear  ,$oo_path;



	$filename="addressbook".$class_name[0].".sxw";
	$break ="<text:p text:style-name=\"break_page\"/>";
	$ttt = new EasyZip;
	$ttt->setpath($oo_path);
	$ttt->addDir('META-INF');
	$ttt->addfile('settings.xml');
	$ttt->addfile('styles.xml');
	$ttt->addfile('meta.xml');


	//讀出 content.xml
	$data = $ttt->read_file(dirname(__FILE__)."/$oo_path/content_doc.xml");


        set_time_limit(180) ;


        if ( $allyear ) {
            $seme_year_seme=sprintf("%03d",curr_year()).curr_seme();
            $sql="select seme_class from stud_seme where seme_class like '$sel_year[0]%' and  seme_year_seme ='$seme_year_seme' group by  seme_class ";

            $rs=$CONN->Execute($sql);
            $m=0;

            while(!$rs->EOF){
               $class_id = $rs->fields["seme_class"];

               $cont .= all_ooo($class_id , $sel_seme ) . $break ;

               $m++;
               $rs->MoveNext();
            }
        }
        else  $cont = all_ooo($sel_year , $sel_seme ) ;


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



function all_ooo($class_id , $sel_seme ) {
	global $CONN,$class_name ,$oo_path ,$sex_arr;

        //新增一個 zipfile 實例
    $ttt = new EasyZip;

    $addr_head = $ttt->read_file(dirname(__FILE__)."/$oo_path/content_head.xml");
    $addr_line = $ttt->read_file(dirname(__FILE__)."/$oo_path/content_row.xml");
    $cont = $addr_head;

    unset($data_array) ;
    $data_array = get_class_data($class_id,$sel_seme) ;


    foreach (	$data_array as $k => $row_data) {

        $temp_arr["class"] = $row_data[classname] ;
        $temp_arr["cid"] = $row_data[stud_id] ;
        $temp_arr["num"] = $row_data[site_num] ;
        $temp_arr["sex"] = $row_data[stud_sex] ;
        $temp_arr["name"] = $row_data[stud_name] ;
		$temp_arr["name_eng"] = $row_data[stud_name_eng] ;
        $temp_arr["pid"] = $row_data[stud_person_id] ;
        $temp_arr["birth"] = $row_data[stud_birthday] ;
        $temp_arr["addr"] = $row_data[stud_addr_1]  ;
        $temp_arr["tel1"] = $row_data[stud_tel_1] ;
        $temp_arr["parent"] = $row_data[d_guardian_name] ;
        $temp_arr["work"] = $row_data[guardian_unit] ;
        $temp_arr["worker"] = $row_data[guardian_work_name] ;
        $temp_arr["tel2"] = $row_data[stud_tel_2i] ;

        $replace_data = $ttt->change_temp($temp_arr,$addr_line);

        $cont.= $replace_data ;
    }
    $cont.= '</table:table>' ;
    return  $cont ;

}
?>
