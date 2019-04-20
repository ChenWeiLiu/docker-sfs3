<?php

// $Id: select_behalf.php 7704 2013-10-23 08:51:29Z smallduh $

/*引入學務系統設定檔*/
include "../../include/config.php";
include "../../include/sfs_oo_zip2.php";
include_once "../../include/sfs_case_PLlib.php";
//引入函數
include "./my_fun.php";
require_once "./module-cfg.php";

$oo_path = "ooo_behalf";

//使用者認證
sfs_check();
if(empty($sel_year))$sel_year = curr_year(); //目前學年
if(empty($sel_seme))$sel_seme = curr_seme(); //目前學期

$class_name_arr = class_base() ;

$sel_year = $_POST['sel_year'] ;
$allyear = $_POST['allyear'] ;
if ($allyear) $chk_allyear = "checked" ;

if ($sel_year=="") $sel_year ="601" ;

	$rs_name=$CONN->Execute("select sb.stud_id, sd.guardian_name from stud_base as sb  LEFT JOIN stud_domicile as sd
	ON sb.student_sn=sd.student_sn
	where  sb.stud_study_cond =0 and sb.curr_class_num like '$sel_year%' ");
	while(!$rs_name->EOF){
		$rs_name_arr[$rs_name->fields[stud_id]] = $rs_name->fields[guardian_name];
		$rs_name->MoveNext();
	}


if($_POST['Submit1']=="班級圈選表") echo ooo();
elseif ($_POST['Submit1']=="全校圈選表") {
   $all_school_fg = true  ;
   echo ooo();
}else{
	//秀出網頁
	head("家長委員名冊");

	$class_name[0]= $sel_year ;
    $class_name[1]= $class_name_arr[$sel_year] ;

		$sel1 = new drop_select(); //選單類別
		$sel1->s_name = "sel_year"; //選單名稱
		$sel1->id = $sel_year;
		$sel1->has_empty = false;
		$sel1->arr = $class_name_arr ; //內容陣列(六個學年)
		$sel1->is_submit = true;
		$sel1->bgcolor = "#DDFFEE";
		$sel1->font_style ="font-size: 15px;font-weight: bold";
        $class_select = "選擇班級:" . $sel1->get_select();

	if ($_GET['act']=="") print_menu($menu_p);
	//設定主網頁顯示區的背景顏色
	$menu="
		<table cellspacing=2 cellpadding=2>
			<tr>
				<td>
					<form name='form1' method='post' action='{$_SERVER['PHP_SELF']}'>
					$class_select <input name='allyear' type='checkbox' value='1' $chk_allyear >全學年
					<input type='submit' name='Submit1' value='班級圈選表'>
					<input type='submit' name='Submit1' value='全校圈選表'>
					</form>
				</td>
			</tr>
		</table>";
	echo $menu;



    $seme_year_seme=sprintf("%03d",curr_year()).curr_seme();
       $sql="select a.stud_id,a.seme_num,b.stud_name from stud_seme a, stud_base b where a.student_sn=b.student_sn and a.seme_class='$class_name[0]' and  a.seme_year_seme='$seme_year_seme' and b.stud_study_cond =0 order by  a.seme_num";
    $rs=$CONN->Execute($sql);
    $m=0;
    echo "<table bgcolor='#000000' border=0 cellspacing=1 cellpadding=2>
			<tr bgcolor='#FAF799'>
				<td>圈選處</td>
				<td>家長姓名</td>
				<td>學生姓名</td>
				<td></td>
				<td>圈選處</td>
				<td>家長姓名</td>
				<td>學生姓名</td>
			</tr>";

	while(!$rs->EOF){
		$stud_id[$m] = $rs->fields["stud_id"];
		$site_num[$m] = $rs->fields["seme_num"];
		$stud_name[$m] = $rs->fields["stud_name"];
		$guardian_name[$m] = $rs_name_arr[$rs->fields["stud_id"]];
        	if($m%2=="0") echo "<tr bgcolor='#FFFFFF'>";
		echo "<td></td>
			  <td>$guardian_name[$m]</td>
			  <td>$stud_name[$m]</td>";
		if($m%2=="0") echo "<td></td>";
		if($m%2=="1") echo "</tr>";
		$m++;
        	$rs->MoveNext();
	}
	if($m%2=="1") echo "<td></td><td></td><td></td></tr>";
	echo "</table>";
	//結束主網頁顯示區
	echo "</td>";
	echo "</tr>";
	echo "</table>";
	//程式檔尾
	foot();
}




function ooo(){
	global $CONN,$class_name,$rs_name_arr , $oo_path , $sel_year , $allyear ,$all_school_fg ;

    $break ="<text:p text:style-name=\"break_page\"/>";
	$filename="behalf.sxw";

    	$ttt = new EasyZip;
	$ttt->setpath($oo_path);
	$ttt->addDir('META-INF');
	$ttt->addfile('settings.xml');
	$ttt->addfile('styles.xml');
	$ttt->addfile('meta.xml');


	//讀出 content.xml
	$data = $ttt->read_file(dirname(__FILE__)."/$oo_path/content_head.xml");

	$data .= $ttt->read_file(dirname(__FILE__)."/$oo_path/content_doc_beg.xml");

    set_time_limit(180) ;
    if ($all_school_fg) {
       //全校
            $seme_year_seme=sprintf("%03d",curr_year()).curr_seme();
            $sql="select seme_class from stud_seme where   seme_year_seme ='$seme_year_seme' group by  seme_class ";
            $rs=$CONN->Execute($sql);
            $m=0;

            while(!$rs->EOF){
               $class_id = $rs->fields["seme_class"];
               $data .= all_ooo($class_id ) . $break ;

               $m++;
               $rs->MoveNext();
            }
    }elseif ( $allyear ) {
       //全學年
            $seme_year_seme=sprintf("%03d",curr_year()).curr_seme();
            $sql="select seme_class from stud_seme where seme_class like '$sel_year[0]%' and  seme_year_seme ='$seme_year_seme' group by  seme_class ";
            $rs=$CONN->Execute($sql);
            $m=0;

            while(!$rs->EOF){
               $class_id = $rs->fields["seme_class"];
               $data .= all_ooo($class_id ) . $break ;

               $m++;
               $rs->MoveNext();
            }
    }else  $data .= all_ooo($sel_year ) ;

	$data .= $ttt->read_file(dirname(__FILE__)."/$oo_path/content_foot.xml");





	// 加入 content.xml 到zip 中
	$ttt->add_file($data,"content.xml");

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
	global $CONN, $oo_path ,$school_short_name ;

    $class_name_arr = class_base() ;
    $class_name[0]= $sel_year ;
    $class_name[1]= $class_name_arr[$sel_year] ;

	//取得姓名
 	$rs_name=$CONN->Execute("select sb.stud_id, sd.guardian_name from stud_base sb, stud_domicile sd where sb.student_sn=sd.student_sn  and sb.stud_study_cond =0 and sb.curr_class_num like '$sel_year%' ");
	while(!$rs_name->EOF){
		$rs_name_arr[$rs_name->fields[stud_id]] = $rs_name->fields[guardian_name];
		$rs_name->MoveNext();
	}

    $seme_year_seme=sprintf("%03d",curr_year()).curr_seme();
    $sql="select a.stud_id,a.seme_num,b.stud_name from stud_seme a, stud_base b  where a.student_sn=b.student_sn and a.seme_class='$class_name[0]' and  a.seme_year_seme='$seme_year_seme'  and b.stud_study_cond =0 order by  a.seme_num";
    $rs=$CONN->Execute($sql);
    $m=0;
    //echo $sql ;


    while(!$rs->EOF){
 	    $stud_id[$m] = $rs->fields["stud_id"];
		$site_num[$m] = $rs->fields["seme_num"];
		$stud_name[$m] = $rs->fields["stud_name"];
		$guardian_name[$m] = $rs_name_arr[$rs->fields["stud_id"]];
		$m++;
		$rs->MoveNext();
    }

        //新增一個 zipfile 實例
    $ttt = new EasyZip;

    $row_head = $ttt->read_file(dirname(__FILE__)."/$oo_path/content_row_head.xml");
    $row_line = $ttt->read_file(dirname(__FILE__)."/$oo_path/content_row.xml");
    $row_end = $ttt->read_file(dirname(__FILE__)."/$oo_path/content_row_foot.xml");


    $temp_arr["school_name"] = $school_short_name;
    $temp_arr["class_name"] = $class_name[1] ;
    $replace_data = $ttt->change_temp($temp_arr,$row_head);
    $cont = $replace_data;

    for($i=0;$i<count($stud_id);$i++){

        $temp_arr["parent1"] = $guardian_name[$i];
        $temp_arr["student1"] = $stud_name[$i] ;
        $i++ ;
        $temp_arr["parent2"] = $guardian_name[$i] ;
        $temp_arr["student2"] = $stud_name[$i];

        $replace_data = $ttt->change_temp($temp_arr,$row_line);

        $cont.= $replace_data ;
    }

    $cont.= $row_end ;
    return  $cont ;

}

?>
