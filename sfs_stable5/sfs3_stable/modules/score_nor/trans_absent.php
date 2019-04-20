<?php

// $Id: trans_absent.php 6218 2010-10-14 03:34:47Z brucelyc $

// 載入設定檔
include "config.php";

// 認證檢查
sfs_check();

//欄位資訊
$sel_file=($_POST['sel_file'])?$_POST['sel_file']:$_GET['sel_file'];
$trans_key=$_POST['trans_key'];
$year_seme=$_POST['year_seme'];
$sel_study=$_POST['sel_study'];

//檔案上傳
$file_name=strtoupper($_FILES['upload_file']['name']);
if ($_FILES['upload_file']['size'] >0 && $file_name != "" && strstr($file_name,"YABS") && substr($file_name,-3,3) == "CSV"){
	copy($_FILES['upload_file']['tmp_name'],$temp_path.$file_name);
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
	要處理的檔案：「\student\absent\a9x\yabs9xxx.dbf」。||
	請先將此檔以Excel打開，然後存成CSV格式後再選擇上傳。||
	也就是上傳前先將檔案存成「YABS9xxx.CSV」。||
	檔案說明：yabs9121.dbf為91學年度第2學期1年級，依此類推。||
	<a href=YABS9211.csv>範例檔</a>。各代碼分別為，C : 曠課, V : 事假, S : 病假, M : 喪假, B : 公假。
	";
	$help=help($help_text);

	//檔案選單
	$temp4="<select name='sel_file' onChange='jumpMenu()'>
	<option value=''>請選擇檔案";
	$fp = opendir($temp_path);
	while ( gettype($file=readdir($fp)) != boolean ){
		$temp5=($sel_file==$file)?"selected":"";
		if (is_file("$temp_path/$file") && (substr($file,0,4)=="YABS" || substr($file,0,4)=="Yabs") && (substr($file,-3,3)=="csv" || substr($file,-3,3)=="CSV")){
			$temp4.="<option value='$file' $temp5>$file";
		}
	}
	closedir($fp);
	$temp4.="</select>";
	
	$main="
	$toolbar
	<table cellspacing='1' cellpadding='3' class='main_body'>
	<tr bgcolor='#FFFFFF'>
	<form name='form0' enctype='multipart/form-data' action='{$_SERVER['SCRIPT_NAME']}' method='post'>
		<td class='title_sbody1' nowrap>上傳檔案：<td><input type=file name='upload_file'></td>
		<td class='title_sbody1' nowrap><p align='center'><input type=submit name='doup_key' value='上傳'></p></td>
	</form>
	</tr>
	<tr bgcolor='#FFFFFF'>
	<form name='form1' action='{$_SERVER['SCRIPT_NAME']}' method='post'>
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
		$fp=fopen($file_name,"r");
		
		for ($i=0;$i<2;$i++){
			$k=sfs_fgetcsv($fp,2000,",");
			//只抓取匯入檔的第二列
			if ($i==1){
				$temp1="";
				$kfirst=$k[0];
				$d=explode("/",$k[2]);
				$abs_date=sprintf("%04d-%02d-%02d",$d[0],$d[1],$d[2]);
				$temp2="";
				$temp3="";
				for ($i=3;$i<=11;$i++) {
					$temp2.="<td class='title_sbody2'>".$sec[$i-3]."</td>";
					$temp3.="<td class='title_sbody1'>".$abs_kind[$k[$i]]."</td>";
				}
				$class_year=$sel_study + $IS_JHORES;
	
				$temp9="<select name='year_seme'>";
				$ss=array();
				$i=0;
				$sql_select = "select distinct seme_year_seme from stud_seme order by seme_year_seme";
				$recordSet=$CONN->Execute($sql_select);
				while (!$recordSet->EOF) {	
					$seme_year_seme = $recordSet->fields["seme_year_seme"];
					$year=intval(substr($seme_year_seme,0,3));
					$semester=substr($seme_year_seme,-1,1);
					$ss_temp=$year."_".$semester;
					if (!in_array($ss_temp,$ss)){
						$selected=(($sel_year==$year) && ($sel_seme==$semester))?"selected":"";
						$temp9.="<option value='$ss_temp' $selected>$year"."學年度第"."$semester"."學期";
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
				<form name='form2' enctype='multipart/form-data' action='{$_SERVER['SCRIPT_NAME']}' method='post'>
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
		              	<input type='hidden' name='sel_file' value='$sel_file'>
				<input type=submit value='開始匯入'>
				</td>
				</tr>
		              	</form>
				</table>
				$help
				";
			}
		}
	} else {
		$main.="
		</table>
		$help
		";
	}
	fclose($fp);
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
	$ys=explode("_",$year_seme);
	$teacher_sn=$_SESSION['session_tea_sn'];;
	$teacher_name=$_SESSION['session_tea_name'];;
	$today=date("Y-m-d G:i:s",mktime (date("G"),date("i"),date("s"),date("m"),date("d"),date("Y")));
	$seme_year_seme=sprintf("%03d",$ys[0]).$ys[1];
	$file_name=$temp_path."/".$sel_file;
	$fp=fopen($file_name,"r");
	//先取掉第一筆資料, 因為是標題
	$k=sfs_fgetcsv($fp, 2000, ",");
	while($k=sfs_fgetcsv($fp, 2000, ",")) { 
		$sql_select = "select a.stud_name,b.seme_class,b.seme_num from stud_base a,stud_seme b where a.student_sn=b.student_sn and a.stud_id='$k[0]' and b.seme_year_seme='$seme_year_seme'";
		$recordSet = $CONN->Execute($sql_select);
		$studname=$recordSet->fields['stud_name'];
		$seme_class=$recordSet->fields['seme_class'];
		$seme_num=$recordSet->fields['seme_num'];
		$d=explode("/",$k[2]);
		$abs_date=sprintf("%04d-%02d-%02d",$d[0],$d[1],$d[2]);
		$class_id=sprintf("%03d_%1d_%02d_%02d",$ys[0],$ys[1],substr($seme_class,0,1),substr($seme_class,-2,2));
		$main.="<tr><td class='title_sbody1'>".$k[0]."<br>$studname</td><td class='title_sbody1'>$abs_date<br>$class_id</td>";
		for ($j=0;$j<=8;$j++) {
			$sql_select = "select * from stud_absent where stud_id='$k[0]' and class_id='$class_id' and date='$abs_date' and section='$sec_id[$j]'";
			$recordSet = $CONN->Execute($sql_select);
			$sasn=$recordSet->fields['sasn'];
			if ($sasn=="") {
				$abskind=$abs_kind[$k[$j+3]];
				$main.="<td class='title_sbody1'>".$abskind."</td>";
				if ($abskind) {
					$sql_select = "insert into stud_absent (year,semester,class_id,stud_id,date,absent_kind,section,sign_man_sn,sign_man_name,sign_time,txt,month) values ('$ys[0]','$ys[1]','$class_id','$k[0]','$abs_date','$abskind','$sec_id[$j]','$teacher_sn','$teacher_name','$today','','$d[1]')";
					$recordSet = $CONN->Execute($sql_select);
				}
			} else
				$main.="<td bgcolor='#ffffff'><font color='#ff0000'>已有資料</font></td>";
		}
		$main.="</tr>";
	}
	$main.="</table>";
	fclose($fp);
	return $main;
}
?>

<script language="JavaScript1.2">
<!-- Begin
function jumpMenu(){
	if (document.form1.sel_file.options[document.form1.sel_file.selectedIndex].value!="") {
		location="<?php echo $_SERVER['SCRIPT_NAME']; ?>?sel_file=" + document.form1.sel_file.options[document.form1.sel_file.selectedIndex].value;
	}
}
//  End -->
</script>
