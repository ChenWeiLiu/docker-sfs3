<?php

//$Id: fix_score_batch.php 6798 2012-06-21 04:52:15Z infodaes $

require_once("config.php");

//使用者認證

sfs_check();



head("課程成績轉正");

print_menu($school_menu_p);



$year_seme=$_POST[year_seme];

$study_grade=$_POST[study_grade];

$items_0=$_POST[items_0];

$items_1=$_POST[items_1];



if($year_seme==''){

	$study_year = curr_year(); //目前學年

	$study_seme = curr_seme(); //目前學期

	$year_seme = sprintf("%03d%d",$study_year,$study_seme);

} else {

	$study_year=substr($year_seme,0,3);

	$study_seme=substr($year_seme,-1);

}



$table_name="score_semester_".($study_year+0)."_$study_seme";



if($_POST['GoBTN']=='確定') {

	if($items_0 and $items_1){

		//不進行科目檢查

		//替換階段成績科目代號

		$sql="UPDATE $table_name SET ss_id=$items_1 WHERE ss_id=$items_0";

		$res=$CONN->Execute($sql) or user_error("轉接課程的 階段 成績資料失敗！<br>$sql",256);

		

		//替換學期成績

		$sql="UPDATE stud_seme_score SET ss_id=$items_1 WHERE ss_id=$items_0 and seme_year_seme='$year_seme'";

		$res=$CONN->Execute($sql) or user_error("轉接課程的 學期 成績資料失敗！<br>$sql",256);



		//替換努力程度

		$sql="UPDATE stud_seme_score_oth SET ss_id=$items_1 WHERE ss_id=$items_0 and seme_year_seme='$year_seme'";

		$res=$CONN->Execute($sql) or user_error("轉接課程的努力程度成績資料失敗！<br>$sql",256);

				

		//替換課表

		$sql="UPDATE score_course SET ss_id=$items_1 WHERE ss_id=$items_0 and year='".intval($study_year)."' and semester='$study_seme'";

		$res=$CONN->Execute($sql) or user_error("轉接課程的 學期 課表資料失敗！<br>$sql",256);

		

	} else {

		echo "<script language=\"Javascript\"> alert (\"未選定好來源與目的課程, 無法進行轉接！\")</script>";

	}

}

//班級課程改為年級課程
$release_ssid=$_POST['release_ssid'];
if($release_ssid) {
	$sql="UPDATE score_ss SET class_id='' WHERE ss_id='$release_ssid'";
	$res=$CONN->Execute($sql) or user_error("班級課程改為年級課程失敗！<br>$sql",256);
}

if($_POST['GoBTN']=='刪除選取') {

	if($_POST[kill_id]){

		foreach($_POST[kill_id] as $ss_id) $ss_id_list.="$ss_id,";

		$ss_id_list=substr($ss_id_list,0,-1);

		$sql="DELETE FROM score_ss WHERE ss_id IN ($ss_id_list)";

		$res=$CONN->Execute($sql) or user_error("刪除課程資料失敗！ => $ss_id_list<br>$sql",256);

	}

}



//echo "$year_seme<BR>$study_year<BR>$study_seme<BR>$study_grade<BR>";



$warning="<font size=2><li>本程式旨在協助使用者能批次將某課程成績轉換成另一個課程成績。
<li>使用的時機係在期中因\"誤刪\"了已經輸入成績的課程，欲將原先輸入的成績快速銜接至新設定的課程，以避免得重新大量輸入或得進入系統資料庫DEBUG的困擾!!
<li>此程式設計的用意，不在解決\"經常性\"的課程設定錯誤。每學期的期初課程設定前，請能詳悉學校課程規劃與SFS的特性再進行設定!!
<li>本程式不做任何錯誤擔保，使用前有疑慮，請先做好資料庫備份!
<li><font color='red'>2012/6/21 新增DBLCLICK班級課程代號可撤除班級課程設定為年級課程功能(不可回復，請謹慎使用！)</font></li>
<li>程式作者：<a href='mailto:infodaes@seed.net.tw'>台中縣infodaes</a></font>";


//取得反轉後的學期列表陣列

$semesters=get_class_seme();


//製作學年下拉選單

$semesters_menu="<select name='year_seme' onchange='this.form.submit();'>";

foreach($semesters as $key=>$value){

	$selected=($key==$year_seme)?'selected':'';

	$semesters_menu.="<option value='$key' $selected>$value</option>";

}

$semesters_menu.="</select>";



//取得已設定課程的年級

$sql="SELECT DISTINCT class_year FROM score_ss WHERE year=$study_year AND semester=$study_seme ORDER BY class_year";

$res=$CONN->Execute($sql) or user_error("讀取已設定課程的年級列表失敗！<br>$sql",256);



$study_grade_menu="<select name='study_grade' onchange='this.form.submit();'><option></option>";

while(!$res->EOF) {

	

	$selected=($study_grade==$res->fields['class_year'])?'selected':'';

	$study_grade_menu.="<option value='".$res->fields['class_year']."' $selected>".$res->fields['class_year']."年級</option>";

	$res->MoveNext();

	}

$study_grade_menu.="</select>";



if($study_grade){

	//取得課程中文名稱對照

	$sql="SELECT subject_id,subject_name FROM score_subject";

	$res=$CONN->Execute($sql) or user_error("讀取課程名稱失敗！<br>$sql",256);

	$ss_name=array();

	while(!$res->EOF) {

		$subject_id=$res->fields['subject_id'];

		$ss_name[$subject_id]=$res->fields['subject_name'];

		$res->MoveNext();

	}

	

	//取得年級課程與已輸入的成績紀錄

	$sql="SELECT ss_id,subject_id,enable,need_exam,class_id,rate,link_ss FROM score_ss WHERE year='$study_year' AND semester='$study_seme' AND class_year='$study_grade' ORDER BY class_id,sort,sub_sort";

	$res=$CONN->Execute($sql) or user_error("讀取課程設定資料失敗！<br>$sql",256);

	$ss=array();

	while(!$res->EOF) {

		$ss_id=$res->fields['ss_id'];

		$subject_id=$res->fields['subject_id'];

		$enabled=$res->fields['enable'];



		$ss[$enabled][$ss_id]['subject_id']=$subject_id;

		$ss[$enabled][$ss_id]['ss_name']=$ss_name[$subject_id];

		$ss[$enabled][$ss_id]['enable']=$res->fields['enable'];

		$ss[$enabled][$ss_id]['need_exam']=$res->fields['need_exam'];

		$ss[$enabled][$ss_id]['class_id']=$res->fields['class_id'];

		$ss[$enabled][$ss_id]['rate']=$res->fields['rate'];

		$ss[$enabled][$ss_id]['link_ss']=$res->fields['link_ss'];

		

		$res->MoveNext();

	}

//echo "$sql<BR>";

	//取得已輸入成績的資料筆數  資料表格式為 score_semester_94_1  比對欄位class_id格式為094_1_01_X

	

	$sql="SELECT ss_id,count(*) as records FROM $table_name WHERE class_id like '".$study_year."_".$study_seme."_".sprintf("%02d",$study_grade)."_%' GROUP BY ss_id";

	$res=$CONN->Execute($sql) or user_error("讀取成績統計資料失敗！<br>$sql",256);

	$ss_records=array();

	while(!$res->EOF) {

		$ss_id=$res->fields['ss_id'];

		$ss_records[$ss_id]=$res->fields['records'];

		$res->MoveNext();

	}



//echo "$sql<BR>";

	//將課程資料轉換為要顯示的資料

	foreach($ss as $key=>$value){

		$target=sprintf("items_%01d",$key);

		$$target="<table width='100%' style='font-size:10pt;' align='left' border='1' cellpadding='1' cellspacing='0' style='border-collapse: collapse' bordercolor='#CCCCCC' id='".($key+2)."'>";

		$$target.="<tr align='center' bgcolor='#CCCCCC'><td>編號</td><td>名稱</td><td>九年一貫對應</td><td>計分</td><td>加權</td><td>成績數</td><td>班級</td></tr>";

		foreach($value as $ss_id=>$data){

			//判斷是否可以選擇

			$kill_id='';

			switch($key) {

			case 0:

				if(!$ss_records[$ss_id]) {

					$enabled='disabled';

					$kill_id="<input type='checkbox' name='kill_id[]' value='$ss_id'>";

				} else $enabled='';

				break;

			case 1:

				$enabled=$ss_records[$ss_id]?'disabled':'';

				if($multi_connectable) $enabled='';

				break;

			}

			if(!$data['need_exam']) $enabled='disabled';



			//測試專用

			//$enabled='';
			$ss_radio="<input type='radio' name='".$target."' value='$ss_id' onclick='this.form.selection_$key.value=$ss_id' $enabled>";
			$release=$data['class_id']?'onMouseOver="this.style.cursor=\'hand\';" ondblclick="if(confirm(\'真的要撤除 #'.$ss_id.'-'.$data['ss_name'].' 的班級課程設定('.$data['class_id'].')，轉為年級課程？\')) { document.myform.release_ssid.value=\''.$ss_id.'\'; document.myform.submit(); }"':'';
			$$target.="<tr align='center'>
				<td>$ss_radio".$ss_id."</td>
				<td>".$data['ss_name']."</td>
				<td>".$data['link_ss']."</td>
				<td>".$data['need_exam']."</td>
				<td>".$data['rate']."</td>
				<td>".$ss_records[$ss_id]."$kill_id</td>
				<td $release>".$data['class_id']."</td>
				</tr>";
		}
		$$target.="</tr></table>";
	}
	//echo "<PRE>";
	//print_r($items_0);
	//print_r($items_1);
	//echo "</PRE>";
}

$main="<table align=left border='2' cellpadding='5' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111' id='AutoNumber1'>";
$main.="<form name='myform' method='post' action='$_SERVER[PHP_SELF]'><input type='hidden' name='release_ssid' value=''>$semesters_menu $study_grade_menu";
$main.="<TR BGCOLOR='#FFCCCC'><TD align='center'>停用的課程</TD><TD align='center'>啟用的課程</TD><TD align='center'>! 注意 !</TD></TR>";
$main.="<TR><TD valign='top'>$items_0</TD><TD valign='top'>$items_1</TD><TD width='200' valign='top'>$warning</TD></TR>";
echo $main."<tr align='center'><td><input type='submit' name='GoBTN' value='刪除選取' onclick='return confirm(\"真的要自資料庫刪除？\")'></td><td colspan=2>您的選擇：停用課程id:<input type='text' name='selection_0' size='5' disabled>的成績轉接至id:<input type='text' name='selection_1' size='5' disabled><input type='submit' name='GoBTN' value='確定' onclick='return confirm(\"真的要進行轉換？\")'></td></table></form>";

foot();
?>

