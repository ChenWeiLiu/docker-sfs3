<?php

// $Id: address_book.php 7706 2013-10-23 08:59:03Z smallduh $

/*引入學務系統設定檔*/
include "config.php";

$button["Excel"]="MS Office Excel 檔";
$button["Word"]="MS Office Word 檔";
$button["sxw"]="OpenOffice.org Writer 檔";

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
$test=pipa_log("班級通訊錄\r\n學年：$sel_year\r\n學期：$sel_seme\r\n班級：$class_description\r\n");		

if($_POST['Submit1']=="下載班級通訊錄"){
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
		</table>";
	echo $menu;
    $seme_year_seme=sprintf("%03d",curr_year()).curr_seme();
    $sql="select stud_id,seme_num from stud_seme where seme_class='$class_name[0]' and  seme_year_seme='$seme_year_seme' order by  seme_num";
    $rs=$CONN->Execute($sql);
    $m=0;
    echo "<table bgcolor='#000000' border=0 cellspacing=1 cellpadding=2>
			<tr bgcolor='#FAF799'>
				<td colspan='2'>$class_name[1]</td>
				<td colspan='1'>電話1</td>
				<td colspan='1'>電話2</td>
				<td colspan='1'>電話3</td>
				<td colspan='1'>住址1</td>
				<td colspan='1'>住址2</td>
			</tr>";
	while(!$rs->EOF){
        $stud_id[$m] = $rs->fields["stud_id"];
        $site_num[$m] = $rs->fields["seme_num"];
        $rs_name=$CONN->Execute("select stud_name,stud_tel_1,stud_tel_2,stud_tel_3,stud_addr_1,stud_addr_2 from stud_base where stud_id='$stud_id[$m]' and stud_study_cond =0 ");
        if ($rs_name->fields["stud_name"]) {
          $stud_name[$m] = $rs_name->fields["stud_name"];
		$stud_addr_1[$m] = $rs_name->fields["stud_addr_1"];
		$stud_addr_2[$m] = $rs_name->fields["stud_addr_2"];
		$stud_tel_1[$m] = $rs_name->fields["stud_tel_1"];
		$stud_tel_2[$m] = $rs_name->fields["stud_tel_2"];
		$stud_tel_3[$m] = $rs_name->fields["stud_tel_3"];
          echo "<tr bgcolor='#FFFFFF'>
				<td>$site_num[$m]</td>
				<td>$stud_name[$m]</td>
				<td>$stud_tel_1[$m]</td>
				<td>$stud_tel_2[$m]</td>
				<td>$stud_tel_3[$m]</td>
				<td>$stud_addr_1[$m]</td>
				<td>$stud_addr_2[$m]</td>
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
	global $CONN, $class_name;
	
	
	//轉出為excel、word	
	if ($print_key=="Excel")
		$filename =  "name.xls"; 	
	else if ($print_key=="Word")
		$filename =  "name.doc";
	header("Content-disposition: filename=$filename");
	header("Content-type: application/octetstream");
	//header("Pragma: no-cache");
					//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");

	header("Expires: 0");
	
    $seme_year_seme=sprintf("%03d",curr_year()).curr_seme();
    $sql="select stud_id,seme_num from stud_seme where seme_class='$class_name[0]' and  seme_year_seme='$seme_year_seme' order by  seme_num";
    $rs=$CONN->Execute($sql);
    $m=0;
    echo "<table  border=1 cellspacing=1 cellpadding=2 width=95%>
			<tr  >
				<td colspan='2'>$class_name[1]</td>
				<td colspan='1'>電話1</td>
				<td colspan='1'>電話2</td>
				<td colspan='1'>電話3</td>
				<td colspan='1'>住址1</td>
				<td colspan='1'>住址2</td>
			</tr>";
	while(!$rs->EOF){
        $stud_id[$m] = $rs->fields["stud_id"];
        $site_num[$m] = $rs->fields["seme_num"];
        $rs_name=$CONN->Execute("select stud_name,stud_tel_1,stud_tel_2,stud_tel_3,stud_addr_1,stud_addr_2 from stud_base where stud_id='$stud_id[$m]' and stud_study_cond =0 ");
        if ($rs_name->fields["stud_name"]) {
          $stud_name[$m] = $rs_name->fields["stud_name"];
		$stud_addr_1[$m] = $rs_name->fields["stud_addr_1"];
		$stud_addr_2[$m] = $rs_name->fields["stud_addr_2"];
		$stud_tel_1[$m] = $rs_name->fields["stud_tel_1"];
		$stud_tel_2[$m] = $rs_name->fields["stud_tel_2"];
		$stud_tel_3[$m] = $rs_name->fields["stud_tel_3"];
          echo "<tr >
				<td>$site_num[$m]</td>
				<td>$stud_name[$m]</td>
				<td>$stud_tel_1[$m]</td>
				<td>$stud_tel_2[$m]</td>
				<td>$stud_tel_3[$m]</td>
				<td>$stud_addr_1[$m]</td>
				<td>$stud_addr_2[$m]</td>
			  </tr>";
		$m++;
	}	
        $rs->MoveNext();
    }
	echo "</table>";

	exit;
}

function ooo(){
	global $CONN,$class_name;

	$oo_path = "ooo_addressbook";

	$filename="addressbook".$class_name[0].".sxw";

    //新增一個 zipfile 實例
	$ttt = new EasyZip;
	$ttt->setPath($oo_path);
	$ttt->addDir('META-INF');
	$ttt->addfile("settings.xml");
	$ttt->addfile("styles.xml");
	$ttt->addfile("meta.xml");

	//讀出 content.xml
	$data = $ttt->read_file(dirname(__FILE__)."/$oo_path/content.xml");

	//將 content.xml 的 tag 取代
    //$class_name=teacher_sn_to_class_name($_SESSION['session_tea_sn']);
    $seme_year_seme=sprintf("%03d",curr_year()).curr_seme();
    $sql="select stud_id,seme_num from stud_seme where seme_class='$class_name[0]' and  seme_year_seme='$seme_year_seme' order by seme_num";
    $rs=$CONN->Execute($sql);
    $m=0;
    while(!$rs->EOF){
        $stud_id[$m] = $rs->fields["stud_id"];
        $site_num[$m] = $rs->fields["seme_num"];
        $rs_name=$CONN->Execute("select stud_name,stud_tel_1,stud_tel_2,stud_tel_3,stud_addr_1,stud_addr_2 from stud_base where stud_id='$stud_id[$m]' and stud_study_cond =0  ");
        if ($rs_name->fields["stud_name"]) {
          $stud_name[$m] = $rs_name->fields["stud_name"];
		$stud_addr_1[$m] = $rs_name->fields["stud_addr_1"];
		$stud_addr_2[$m] = $rs_name->fields["stud_addr_2"];
		$stud_tel_1[$m] = $rs_name->fields["stud_tel_1"];
		$stud_tel_2[$m] = $rs_name->fields["stud_tel_2"];
		$stud_tel_3[$m] = $rs_name->fields["stud_tel_3"];
          $m++;
        }  
        $rs->MoveNext();
    }
	$head="
		<table:table-header-rows>
		<table:table-row>
		<table:table-cell table:style-name='course_tbl.A1' table:number-columns-spanned='2' table:value-type='string'>
		<text:p text:style-name='P1'>$class_name[1]
		</text:p>
		</table:table-cell>
		<table:covered-table-cell/>
		<table:table-cell table:style-name='course_tbl.C1' table:value-type='string'>
		<text:p text:style-name='P1'>電話1
		</text:p>
		</table:table-cell>
		<table:table-cell table:style-name='course_tbl.C1' table:value-type='string'>
		<text:p text:style-name='P1'>電話2
		</text:p>
		</table:table-cell>
		<table:table-cell table:style-name='course_tbl.C1' table:value-type='string'>
		<text:p text:style-name='P1'>電話3
		</text:p></table:table-cell>
		<table:table-cell table:style-name='course_tbl.C1' table:value-type='string'>
		<text:p text:style-name='P1'>住址1
		</text:p>
		</table:table-cell>
		<table:table-cell table:style-name='course_tbl.G1' table:value-type='string'>
		<text:p text:style-name='P1'>住址2
		</text:p>
		</table:table-cell>
		</table:table-row>
		</table:table-header-rows>";


    for($i=0;$i<count($stud_id);$i++){
        $cont.="
		<table:table-row>
		<table:table-cell table:style-name='course_tbl.A2' table:value-type='string'>
		<text:p text:style-name='P2'>$site_num[$i]
		</text:p>
		</table:table-cell>
		<table:table-cell table:style-name='course_tbl.B2' table:value-type='string'>
		<text:p text:style-name='P2'>$stud_name[$i]
		</text:p>
		</table:table-cell>
		<table:table-cell table:style-name='course_tbl.B2' table:value-type='string'>
		<text:p text:style-name='P2'>$stud_tel_1[$i]
		</text:p>
		</table:table-cell>
		<table:table-cell table:style-name='course_tbl.B2' table:value-type='string'>
		<text:p text:style-name='P2'>$stud_tel_2[$i]
		</text:p>
		</table:table-cell>
		<table:table-cell table:style-name='course_tbl.B2' table:value-type='string'>
		<text:p text:style-name='P2'>$stud_tel_3[$i]
		</text:p>
		</table:table-cell>
		<table:table-cell table:style-name='course_tbl.B2' table:value-type='string'>
		<text:p text:style-name='P2'>$stud_addr_1[$i]
		</text:p>
		</table:table-cell>
		<table:table-cell table:style-name='course_tbl.G2' table:value-type='string'>
		<text:p text:style-name='P2'>$stud_addr_2[$i]
		</text:p>
		</table:table-cell>
		</table:table-row>";
    }

    $temp_arr["head"] = $head;
	$temp_arr["cont"] = $cont;
	// change_temp 會將陣列中的 big5 轉為 UTF-8 讓 openoffice 可以讀出
	$replace_data = $ttt->change_temp($temp_arr,$data,0);

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

?>
