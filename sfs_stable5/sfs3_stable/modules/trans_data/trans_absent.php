<?php

// $Id: trans_absent.php 5310 2009-01-10 07:57:56Z hami $

// 載入設定檔
include "config.php";

// 認證檢查
sfs_check();

/*
if (!ini_get('register_globals')) {
	ini_set("magic_quotes_runtime", 0);
	extract( $_POST );
	extract( $_GET );
	extract( $_SERVER );
}
*/

//欄位資訊
$sel_file=$_GET['sel_file'];
$trans_key=$_POST['trans_key'];
$year_seme=$_POST['year_seme'];
$sel_study=$_POST['sel_study'];

//檔案上傳
$file_name=strtoupper($_FILES['upload_file']['name']);
if ($_FILES['upload_file']['size'] >0 && $file_name != "" && strstr($file_name,"YABS") && substr($file_name,(strpos($file_name,".")+1),3) == "DBF"){
	copy($_FILES['upload_file']['tmp_name'],$temp_path.$_FILES['upload_file']['name']);
}

if ($trans_key){
	$main=trans_absent();
} else {
	$main=view_trans_absent();
}

//秀出網頁
head("匯入出缺席記綠");
echo $main;
foot();

function view_trans_absent(){
	global $menu_p,$temp_path,$sel_file,$sec,$abs_kind,$CONN,$IS_JHORES;
	$toolbar=make_menu($menu_p);
	
	//說明
	$help_text="
	如果檔案未上傳，請先選擇一個檔案上傳。||
	如果檔案已上傳，則選擇要匯入的檔案。||
	上傳檔案：「\student\absent\a9x\yabs9xxx.dbf」。||
	檔案說明：yabs9121.dbf為91學年度第2學期1年級，依此類推。
	";
	$help=help($help_text);

	//檔案選單
	$temp4="<select name='sel_file' onChange='jumpMenu()'>
	<option value=''>請選擇檔案";
	$fp = opendir($temp_path);
	while ( gettype($file=readdir($fp)) != boolean ){
		$temp5=($sel_file==$file)?"selected":"";
		if (is_file("$temp_path/$file") && (substr($file,0,4)=="YABS" || substr($file,0,4)=="Yabs")){
			$temp4.="<option value='$file' $temp5>$file";
		}
	}
	closedir($fp);
	$temp4.="</select>";
	
	$main="
	$toolbar
	<table cellspacing='1' cellpadding='3' class='main_body'>
	<tr bgcolor='#FFFFFF'>
	<form name='form0' enctype='multipart/form-data' action='{$_SERVER['PHP_SELF']}' method='post'>
		<td class='title_sbody1' nowrap>上傳檔案：<td><input type=file name='upload_file'></td>
		<td class='title_sbody1' nowrap><p align='center'><input type=submit name='doup_key' value='上傳'></p></td>
	</form>
	</tr>
	<tr bgcolor='#FFFFFF'>
	<form name='form1' action='{$_SERVER['PHP_SELF']}' method='post'>
		<td class='title_sbody1' nowrap>伺服器內存檔案：<td colspan=2>$temp4</td>
	</form>
	</tr>";

	if ($sel_file){
	   //說明
	$help_text="
	以上為檔案內第一位學生資料，請先核對是否正確。||
	若學生姓名未出現，請先匯入學生資料。
	";
		$help=help($help_text);

		//計算學年學期
		$sel_year=substr($sel_file,4,2);
		$sel_seme=substr($sel_file,6,1);
		$sel_study=substr($sel_file,7,1);

		//顯示第一位學生資料
		$file_name=$temp_path."/".$sel_file;
		if ( !$fp = dbase_open($file_name,0) ) {
			echo '無法開啟 $file_name\n';
			exit;
		}
		$nr = dbase_numrecords($fp);
		$temp1="";
		$k=dbase_get_record($fp,1);
		$kfirst=$k[0];
		$abs_date=substr($k[2],0,4)."-".substr($k[2],4,2)."-".substr($k[2],6,2);
		$temp2="";
		$temp3="";
		for ($i=3;$i<=11;$i++) {
			$temp2.="<td class='title_sbody2'>".$sec[$i-3]."</td>";
			$temp3.="<td class='title_sbody1'>".$abs_kind[$k[$i]]."</td>";
		}
		$class_year=$sel_study + $IS_JHORES;
		dbase_close($fp);

		$temp9="<select name='year_seme'>";
		$ss=array();
		$i=0;
		$sql_select = "select year,semester from score_ss where enable='1' order by year,semester";
		$recordSet=$CONN->Execute($sql_select);
		while (!$recordSet->EOF) {	
			$year = $recordSet->fields["year"];
			$semester = $recordSet->fields["semester"];
			$semester_name=($semester=='2')?"下":"上";
			$ss_temp=$year."_".$semester;
			if (!in_array($ss_temp,$ss)){
				$selected=(($sel_year==$year) && ($sel_seme==$semester))?"selected":"";
				$temp9.="<option value='$ss_temp' $selected>$sel_year"."學年度"."$semester_name"."學期";
				$ss[$i]=$ss_temp;
				$i++;		   
			}
			$recordSet->MoveNext();		   
		}
		$temp9.="</select>";
		$sql_select = "select * from stud_base where stud_id=$kfirst";
		$recordSet = $CONN->Execute($sql_select);
		$studname=$recordSet->fields['stud_name'];
		$temp3="<td class='title_sbody1'>$kfirst<br>$studname</td><td class='title_sbody1'>".$abs_date."</td>".$temp3;

		$main.="
		<form name='form2' enctype='multipart/form-data' action='{$_SERVER['PHP_SELF']}' method='post'>
		<tr bgcolor='#FFFFFF'>
		<td class='title_sbody1' nowrap>匯入學期年級：<td colspan=2>$temp9<input type='text' name='sel_study' value='$sel_study' maxlength='1' size='1'>年級</td>
		</tr>
		<tr bgcolor='#FFFFFF'>
		<td class='title_sbody1' nowrap colspan='3'><p align='left'>
		<table cellspacing='1' cellpadding='3' class='main_body'>
		<tr>
		<td class='title_sbody2'><p align='center'>學號<br>姓名</p><td class='title_sbody2'><p align='center'>日　　期</p>".$temp2."
		</tr>
		<tr>
		$temp3
		</tr>
		</table>
              	<input type='hidden' name='trans_key' value='trans'>
		<input type=submit value='開始匯入'>
		</td>
		</tr>
              	</form>
		</table>
		$help
		";
	} else {
		$main.="
		</table>
		$help
		";
	}
	return $main;
}

function trans_absent(){
	global $menu_p,$temp_path,$sel_file,$year_seme,$sel_study,$sec,$sec_id,$abs_kind,$CONN,$IS_JHORES;
	$toolbar=make_menu($menu_p);

	$title_temp="<td class='title_sbody2'>學號<br>姓名<td class='title_sbody2'>日期";
	for ($i=0;$i<=8;$i++) $title_temp.="<td class='title_sbody2'>".$sec[$i]."</td>";

	$main="
	$toolbar
	<table cellspacing='1' cellpadding='3' class='main_body'>
	<tr>
	$title_temp
	</tr>
	";
	$file_name=$temp_path."/".$sel_file;
	if ( !$fp = dbase_open($file_name,0) ) { 
		echo '無法開啟 $file_name\n'; 
		exit; 
	} 
	$ys=explode("_",$year_seme);
	$teacher_sn=$_SESSION['session_tea_sn'];;
	$teacher_name=$_SESSION['session_tea_name'];;
	$today=date("Y-m-d G:i:s",mktime (date("G"),date("i"),date("s"),date("m"),date("d"),date("Y")));
	$nr = dbase_numrecords($fp);
	for ($i=1; $i<=$nr; $i++){ 
		$k=dbase_get_record($fp,$i);
		$sql_select = "select a.stud_name,b.seme_class,b.seme_num from stud_base a,stud_seme b where a.stud_id=b.stud_id and a.stud_id='$k[0]'";
		$recordSet = $CONN->Execute($sql_select);
		$studname=$recordSet->fields['stud_name'];
		$seme_class=$recordSet->fields['seme_class'];
		$seme_num=$recordSet->fields['seme_num'];
		$abs_date=substr($k[2],0,4)."-".substr($k[2],4,2)."-".substr($k[2],6,2);
		$class_id=sprintf("%03d_%1d_%02d_%02d",$sel_year,$sel_seme,substr($seme_class,0,1),$seme_num);
		$main.="<tr><td class='title_sbody1'>".$k[0]."<br>$studname</td><td class='title_sbody1'>$abs_date<br>$class_id</td>";
		for ($j=0;$j<=8;$j++) {
			$sql_select = "select sasn from stud_absent where stud_id='$k[0]' and class_id='$class_id' and date='$abs_date' and section='$sec_id[$j]'";
			$recordSet = $CONN->Execute($sql_select);
			$sasn=$recordSet->fields['sasn'];
			if ($sasn=="") {
				$abskind=$abs_kind[$k[$j+3]];
				$main.="<td class='title_sbody1'>".$abskind."</td>";
				if ($abskind) {
					$sql_select = "insert into stud_absent (year,semester,class_id,stud_id,date,absent_kind,section,sign_man_sn,sign_man_name,sign_time,txt) values ('$ys[0]','$ys[1]','$class_id','$k[0]','$abs_date','$abskind','$sec_id[$j]','$teacher_sn','$teacher_name','$today','')";
					$recordSet = $CONN->Execute($sql_select);
				}
			} else
				$main.="<td bgcolor='#ffffff'><font color='#ff0000'>已有資料</font></td>";
		}
		$main.="</tr>";
	}
	dbase_close($fp);
	$main.="</table>";
	return $main;
}
?>

<script language="JavaScript1.2">
<!-- Begin
function jumpMenu(){
	if (document.form1.sel_file.options[document.form1.sel_file.selectedIndex].value!="") {
		location="<?php echo $_SERVER['PHP_SELF']; ?>?sel_file=" + document.form1.sel_file.options[document.form1.sel_file.selectedIndex].value;
	}
}
//  End -->
</script>
