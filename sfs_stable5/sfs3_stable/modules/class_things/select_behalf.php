<?php

// $Id: select_behalf.php 7706 2013-10-23 08:59:03Z smallduh $

/*引入學務系統設定檔*/
include "config.php";

//使用者認證
sfs_check();
if(empty($sel_year))$sel_year = curr_year(); //目前學年
if(empty($sel_seme))$sel_seme = curr_seme(); //目前學期
$teacher_sn=$_SESSION['session_tea_sn'];//取得登入老師的id
//找出任教班級
$class_name=teacher_sn_to_class_name($teacher_sn);
 //取得班級家長及學生姓名
	$rs_name=$CONN->Execute("select sb.stud_id, sd.guardian_name from stud_base as sb, stud_domicile as sd where sb.student_sn=sd.student_sn and sb.curr_class_num like '$class_name[0]%' and sb.stud_study_cond =0");
	while(!$rs_name->EOF){
		$rs_name_arr[$rs_name->fields[stud_id]] = $rs_name->fields[guardian_name];
		$rs_name->MoveNext();	
	}


if($_POST['Submit1']=="下載班代表圈選表") {
  if ($_POST['print_key'] == "sxw")   
     echo ooo();
  else 
     print_key($sel_year,$sel_seme,$print_key,$many_col) ;
}else{
	//秀出網頁
	head("班級事務");

	if ($_GET['act']=="") print_menu($menu_p);
	//設定主網頁顯示區的背景顏色
	$menu="
		<table cellspacing=2 cellpadding=2>
			<tr>
				<td>
					<form name='form1' method='post' action='{$_SERVER['SCRIPT_NAME']}'>
					$import_option<input type='submit' name='Submit1' value='下載班代表圈選表'>
					</form>
				</td>
			</tr>
		</table>";
	echo $menu;
    $seme_year_seme=sprintf("%03d",curr_year()).curr_seme();
       $sql="select a.stud_id,a.seme_num,b.stud_name from stud_seme a, stud_base b where a.student_sn=b.student_sn and a.seme_class='$class_name[0]' and a.seme_year_seme='$seme_year_seme' and b.stud_study_cond =0 order by a.seme_num";
    $rs=$CONN->Execute($sql);
    //   echo $sql ;
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
		if($m%2=="0") echo "<td>&nbsp;</td>";
		if($m%2=="1") echo "</tr>";
		$m++;
        	$rs->MoveNext();
	}

	if($m%2=="1")echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>"; 
	echo "</table>";
	//結束主網頁顯示區
	echo "</td>";
	echo "</tr>";
	echo "</table>";
	//程式檔尾
	foot();
}

//列印文件
function print_key($sel_year="",$sel_seme="",$print_key="",$cols=0){
	global $CONN, $class_name ,$rs_name_arr;
	
	
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
       $sql="select a.stud_id,a.seme_num,b.stud_name from stud_seme a, stud_base b where a.student_sn=b.student_sn and a.seme_class='$class_name[0]' and  a.seme_year_seme='$seme_year_seme' and b.stud_study_cond =0 order by a.seme_num";
    $rs=$CONN->Execute($sql);
    //   echo $sql ;
    $m=0;
    echo "<table  border=1 cellspacing=1 cellpadding=2 widht = 95%>
			<tr >
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
        	if($m%2=="0") echo "<tr >";
		echo "<td></td>
			  <td>$guardian_name[$m]</td>
			  <td>$stud_name[$m]</td>";
		if($m%2=="0") echo "<td>&nbsp;</td>";
		if($m%2=="1") echo "</tr>";
		$m++;
        	$rs->MoveNext();
	}

	if($m%2=="1")echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>"; 
	echo "</table>";

	exit;
}

function ooo(){
	global $CONN,$class_name,$rs_name_arr;

	$oo_path = "ooo_behalf";

	$filename="behalf".$class_name[0].".sxw";

    //新增一個 zipfile 實例
	$ttt = new zipfile;

	//讀出 xml 檔案
	$data = $ttt->read_file(dirname(__FILE__)."/$oo_path/META-INF/manifest.xml");

	//加入 xml 檔案到 zip 中，共有五個檔案
	//第一個參數為原始字串，第二個參數為 zip 檔案的目錄和名稱
	$ttt->add_file($data,"/META-INF/manifest.xml");

	$data = $ttt->read_file(dirname(__FILE__)."/$oo_path/settings.xml");
	$ttt->add_file($data,"settings.xml");

	$data = $ttt->read_file(dirname(__FILE__)."/$oo_path/styles.xml");
	$ttt->add_file($data,"styles.xml");

	$data = $ttt->read_file(dirname(__FILE__)."/$oo_path/meta.xml");
	$ttt->add_file($data,"meta.xml");

	//讀出 content.xml
	$data = $ttt->read_file(dirname(__FILE__)."/$oo_path/content.xml");

	//將 content.xml 的 tag 取代
    //$class_name=teacher_sn_to_class_name($_SESSION['session_tea_sn']);
    $seme_year_seme=sprintf("%03d",curr_year()).curr_seme();
    $sql="select a.stud_id,a.seme_num,b.stud_name from stud_seme a, stud_base b where a.student_sn=b.student_sn and a.seme_class='$class_name[0]' and a.seme_year_seme='$seme_year_seme' and b.stud_study_cond =0 order by a.seme_num";
 
    $rs=$CONN->Execute($sql);
    $m=0;
	while(!$rs->EOF){
	        $stud_id[$m] = $rs->fields["stud_id"];
		$site_num[$m] = $rs->fields["seme_num"];
		$stud_name[$m] = $rs->fields["stud_name"];
		$guardian_name[$m] = $rs_name_arr[$rs->fields["stud_id"]];
		$m++;
		$rs->MoveNext();
    }
	$title=" ".$class_name[1]." 班代表圈選表";
	$head="
		<table:table-header-rows>
		<table:table-row>
		<table:table-cell table:style-name='course_tbl.A1' table:value-type='string'>
		<text:p text:style-name='P2'>圈選處
		</text:p>
		</table:table-cell>
		<table:table-cell table:style-name='course_tbl.B1' table:value-type='string'>
		<text:p text:style-name='P2'>家長姓名
		</text:p>
		</table:table-cell>
		<table:table-cell table:style-name='course_tbl.B1' table:value-type='string'>
		<text:p text:style-name='P2'>學生姓名
		</text:p>
		</table:table-cell>
		<table:table-cell table:style-name='course_tbl.D1' table:value-type='string'>
		<text:p text:style-name='P2'/>
		</table:table-cell>
		<table:table-cell table:style-name='course_tbl.B1' table:value-type='string'>
		<text:p text:style-name='P2'>圈選處
		</text:p>
		</table:table-cell>
		<table:table-cell table:style-name='course_tbl.B1' table:value-type='string'>
		<text:p text:style-name='P2'>家長姓名
		</text:p>
		</table:table-cell>
		<table:table-cell table:style-name='course_tbl.G1' table:value-type='string'>
		<text:p text:style-name='P2'>學生姓名
		</text:p>
		</table:table-cell>
		</table:table-row>
		</table:table-header-rows>";


    for($i=0;$i<count($stud_id);$i++){
        if($i%2=="0"){
		$cont.="<table:table-row>
		<table:table-cell table:style-name='course_tbl.A2' table:value-type='string'>
		<text:p text:style-name='P3'/>
		</table:table-cell>
		<table:table-cell table:style-name='course_tbl.B2' table:value-type='string'>
		<text:p text:style-name='P3'>$guardian_name[$i]
		</text:p>
		</table:table-cell>
		<table:table-cell table:style-name='course_tbl.B2' table:value-type='string'>
		<text:p text:style-name='P3'>$stud_name[$i]
		</text:p>
		</table:table-cell>
		<table:table-cell table:style-name='course_tbl.D2' table:value-type='string'>
		<text:p text:style-name='P3'/>
		</table:table-cell>";
		$is_not_end = TRUE ;
		}

		if($i%2=="1") {
		$cont.="
		<table:table-cell table:style-name='course_tbl.B2' table:value-type='string'>
		<text:p text:style-name='P3'/>
		</table:table-cell>
		<table:table-cell table:style-name='course_tbl.B2' table:value-type='string'>
		<text:p text:style-name='P3'>$guardian_name[$i]
		</text:p>
		</table:table-cell>
		<table:table-cell table:style-name='course_tbl.G2' table:value-type='string'>
		<text:p text:style-name='P3'>$stud_name[$i]
		</text:p>
		</table:table-cell>
		</table:table-row>";
		$is_not_end = FALSE ;
		}
    }
    if($is_not_end){
		$cont.="
		<table:table-cell table:style-name='course_tbl.B2' table:value-type='string'>
		<text:p text:style-name='P3'/>
		</table:table-cell>
		<table:table-cell table:style-name='course_tbl.B2' table:value-type='string'>
		<text:p text:style-name='P3'>
		</text:p>
		</table:table-cell>
		<table:table-cell table:style-name='course_tbl.G2' table:value-type='string'>
		<text:p text:style-name='P3'>
		</text:p>
		</table:table-cell>
		</table:table-row>";}
    
	$temp_arr["title"] = $title;
    $temp_arr["head"] = $head;
	$temp_arr["cont"] = $cont;
	// change_temp 會將陣列中的 big5 轉為 UTF-8 讓 openoffice 可以讀出
	$replace_data = $ttt->change_temp($temp_arr,$data,0);

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
?>
