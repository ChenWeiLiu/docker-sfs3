<?php

// $Id: dlsxw.php 7712 2013-10-23 13:31:11Z smallduh $

/*引入學務系統設定檔*/
include "../../include/config.php";
include "../../include/sfs_oo_zip2.php";
if($_GET['stud_study_year']) $stud_study_year=$_GET['stud_study_year'];
else $stud_study_year=$_POST['stud_study_year'];
if($_GET['class_year']) $class_year=$_GET['class_year'];
else $class_year=$_POST['class_year'];
if($_GET['class_sort']) $class_sort=$_GET['class_sort'];
else $class_sort=$_POST['class_sort'];

//使用者認證
sfs_check();

echo ooo();


function ooo(){
	global $CONN,$stud_study_year,$class_year,$class_sort;

	$oo_path = "ooo_newstud";

	$filename="newstud".$stud_study_year.$class_year.$class_sort.".sxw";

    //新增一個 zipfile 實例
	$ttt = new EasyZip;
	$ttt->setPath($oo_path);

	//讀出 xml 檔案
	$data = $ttt->addDir("META-INF");

	//加入 xml 檔案到 zip 中，共有五個檔案
	//第一個參數為原始字串，第二個參數為 zip 檔案的目錄和名稱

	$ttt->addFile("settings.xml");
	$ttt->addFile("styles.xml");
	$ttt->addFile("meta.xml");

	//讀出 content.xml
	$data = $ttt->read_file(dirname(__FILE__)."/$oo_path/content.xml");

	//將 content.xml 的 tag 取代

    $sql="select * from new_stud where stud_study_year='$stud_study_year' and  class_year='$class_year' and class_sort='$class_sort' order by class_site";
    $rs=$CONN->Execute($sql) or die($sql);
    $i=0;
    while(!$rs->EOF){
		$stud_id[$i]= $rs->fields['stud_id'];
		$stud_name[$i]= $rs->fields['stud_name'];		
		$class_site[$i]= $rs->fields['class_site'];
		$stud_sex[$i]= $rs->fields['stud_sex'];	
		$stud_sex_name[$i]=($stud_sex[$i]=="1")?"男":"女";		
		$stud_address[$i]= $rs->fields['stud_address'];	
		$stud_tel_1[$i]= $rs->fields['stud_tel_1'];
        $i++;
        $rs->MoveNext();
    }
	
	$title=$stud_study_year."學年度".$class_year."年".$class_sort."班新生名冊";
	
	$head="
		<table:table-header-rows>
		<table:table-row>
		<table:table-cell table:style-name='course_tbl.A1' table:value-type='string'>
		<text:p text:style-name='P2'>
		姓名</text:p>
		</table:table-cell>
		<table:table-cell table:style-name='course_tbl.B1' table:value-type='string'>
		<text:p text:style-name='Table Heading'>
		<text:span text:style-name='T2'>
		學號</text:span>
		</text:p>
		</table:table-cell>
		<table:table-cell table:style-name='course_tbl.B1' table:value-type='string'>
		<text:p text:style-name='P2'>
		座號</text:p>
		</table:table-cell>
		<table:table-cell table:style-name='course_tbl.B1' table:value-type='string'>
		<text:p text:style-name='P2'>
		性別</text:p>
		</table:table-cell>
		<table:table-cell table:style-name='course_tbl.B1' table:value-type='string'>
		<text:p text:style-name='P2'>
		住址</text:p>
		</table:table-cell>
		<table:table-cell table:style-name='course_tbl.F1' table:value-type='string'>
		<text:p text:style-name='P2'>
		電話</text:p>
		</table:table-cell>
		</table:table-row>
		</table:table-header-rows>		
	";

    for($i=0;$i<count($stud_id);$i++){
        $cont.="
		<table:table-row>
		<table:table-cell table:style-name='course_tbl.A2' table:value-type='string'>
		<text:p text:style-name='P3'>
		$stud_name[$i]</text:p>
		</table:table-cell>
		<table:table-cell table:style-name='course_tbl.B2' table:value-type='string'>
		<text:p text:style-name='P3'>
		$stud_id[$i]</text:p>
		</table:table-cell>
		<table:table-cell table:style-name='course_tbl.B2' table:value-type='string'>
		<text:p text:style-name='P3'>
		$class_site[$i]</text:p>
		</table:table-cell>
		<table:table-cell table:style-name='course_tbl.B2' table:value-type='string'>
		<text:p text:style-name='P3'>
		$stud_sex_name[$i]</text:p>
		</table:table-cell>
		<table:table-cell table:style-name='course_tbl.B2' table:value-type='string'>
		<text:p text:style-name='P3'>
		$stud_address[$i]</text:p>
		</table:table-cell>
		<table:table-cell table:style-name='course_tbl.F2' table:value-type='string'>
		<text:p text:style-name='P3'>
		$stud_tel_1[$i]</text:p>
		</table:table-cell>
		</table:table-row>";
    }
	
	$temp_arr["title"]=$title;
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
