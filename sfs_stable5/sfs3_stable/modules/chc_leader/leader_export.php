<?php

//$Id: chc_seme.php 5310 2009-01-10 07:57:56Z hami $

//ini_set('display_errors', '1');
//ini_set('output_buffering', '1');

include "config.php";
include "chc_func_class.php";
include_once "../../include/sfs_case_excel.php";
//引入換頁物件(學務系統用法)
include_once "../../include/sfs_oo_dropmenu.php";

//認證
sfs_check();

//顯示內容
if(isset($_POST) and count($_POST)>0){
	if($_POST[leader_excel]=='匯出EXCEL'){
		$aa=get_stu($_POST["class_id"]);
		output_excel($aa, $_POST['class_id'], 'excel');
	}
}

//秀出網頁布景標頭
head("匯出資料");
print_menu($menu_p);

$ob=new drop($CONN);
$select=$ob->select();
//顯示SFS連結選單(欲使用請拿開註解)

echo make_menu($school_menu_p);

display($select);



function output_excel($stu, $class_id, $output_type) {
	global $SFS_PATH; 

	ob_clean();
	if($output_type=='excel'){
		$data1=array();

		$iii=0;
		foreach($stu as $stu_sn=>$val){
			$seme_class=$val['seme_class'];
			$data1[$iii][]=$val['seme_num'];
			$data1[$iii][]=$val['stud_name'];
			$data1[$iii][]=$val['title'][0];
			$data1[$iii][]=$val['title'][1];
			$data1[$iii][]='';//目前無「小老師1」欄位
			$data1[$iii][]='';//目前無「小老師2」欄位
			$data1[$iii][]=$val['memo'];
			$iii++;
		}
		$filename ="leader_".$class_id.".xls";
		$myhead1=array('座號','姓名','幹部1','幹部2','小老師1','小老師2','備註');

		$x=new sfs_xls();
		$x->setUTF8();//$x->setVersion(8);
		$x->setBorderStyle(1);
		$x->filename=$filename;
		$x->setRowText($myhead1);
		$x->addSheet($seme_class);
		$x->items=$data1;
		$x->writeSheet();
		$x->process();
	}
	exit;


}



function init() {
	$YS=''; 
	if (isset($_POST['year_seme'])) $YS=$_POST['year_seme'];
	if ($YS=='' && isset($_GET['year_seme'])) $YS=$_GET['year_seme'];
	if ($YS=='') $YS=curr_year()."_".curr_seme();
	$year_seme=$YS;
	$aa=split("_",$this->year_seme);
	$year=$aa[0];
	$seme=$aa[1];
}

/* 取學生陣列,取自stud_base表與stud_seme表*/
function get_stu($class_id){
	global $CONN;

	$CID=split("_",$class_id);//093_1_01_01
	$year=$CID[0];
	$seme=$CID[1];
	$grade=$CID[2];//年級
	$class=$CID[3];//班級
	$CID_1=$year.$seme;
	$CID_2=sprintf("%03d",$grade.$class);
	$SQL="select a.stud_id,a.student_sn,a.stud_name,a.stud_sex,b.seme_year_seme,b.seme_class,b.seme_num,a.stud_study_cond  from stud_base a,stud_seme b where a.student_sn=b.student_sn and b.seme_year_seme='$CID_1' and b.seme_class='$CID_2' $add_sql order by b.seme_num ";
	$rs=$CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
	$obj_stu=array();
	while ($rs and $ro=$rs->FetchNextObject(false)) {
		$obj_stu[$ro->student_sn] = get_object_vars($ro);
	}

	$SQL="select id,student_sn,seme,kind,org_name,title,memo from chc_leader 
	where kind='0'  and seme='$CID_1' and org_name ='$CID_2'  ";
	$rs=$CONN->Execute($SQL) or die("無法查詢，語法:".$SQL);
	$All=$rs->GetArray();
	foreach ($All as $ary){
		$Sn=$ary['student_sn'];
		$obj_stu[$Sn]['title'][]=$ary['title'];
		if($ary['memo']!=''){
			$obj_stu[$Sn]['memo'][].=' '.$ary['memo'];
		}
	}
	return $obj_stu;
}


//顯示
function display($select){

	echo '<table  width="100%"  border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="#9EBCDD" style="table-layout: fixed;word-wrap:break-word;font-size:10pt">
<tr style="font-size:11pt" bgcolor="#9EBCDD"><td>
說明：<br>
1.配合「12年國教五專免試入學」作業，本程式匯出「班級幹部」資料檔。<br>
2.請開啟匯出的檔案，並複製內容，貼上到<a href="/test_sfs3/modules/career_leader/leader_paste.php" target="_blank">【生涯輔導班級幹部管理】</a>。<br>
<br>
</td></tr></table>';
echo '<table  width="100%"  border="1" align="center" cellpadding="1" cellspacing="1" style="table-layout: fixed;word-wrap:break-word;font-size:10pt">
<tr style="font-size:11pt">

<td></td></tr>';
	echo '<tr style="font-size:11pt">

<td>';
	echo '<form name="form1" method="post" action="">'.$select.'
 <br> 「班級幹部」資料檔　
<input type="submit" name="leader_excel" value="匯出EXCEL">
</form>';
	echo '</td></tr>';
	echo '</table>';

}
