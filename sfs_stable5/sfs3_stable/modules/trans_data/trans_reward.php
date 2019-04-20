<?php

// $Id: trans_reward.php 5310 2009-01-10 07:57:56Z hami $

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
$year=$_POST['year'];
$sel_study=$_POST['sel_study'];

//檔案上傳
$file_name=strtoupper($_FILES['upload_file']['name']);
if ($_FILES['upload_file']['size'] >0 && $file_name != "" && strstr($file_name,"XDESRT") && substr($file_name,(strpos($file_name,".")+1),3) == "DBF"){
   copy($_FILES['upload_file']['tmp_name'],$temp_path.$_FILES['upload_file']['name']);
}

if ($trans_key){
	$main=trans_reward();
} else {
	$main=view_trans_reward();
}

//秀出網頁
head("匯入獎懲記錄");
echo $main;
foot();

function view_trans_reward(){
	global $menu_p,$temp_path,$sel_file,$c_times,$reward_kind,$CONN,$IS_JHORES;
	$toolbar=make_menu($menu_p);
	
	//說明
	$help_text="
	如果檔案未上傳，請先選擇一個檔案上傳。||
	如果檔案已上傳，則選擇要匯入的檔案。||
	上傳檔案：「\student\person\p9x\xdesrt9x.dbf」。||
	檔案說明：xdesrt91.dbf為91學年度，依此類推。
	";
	$help=help($help_text);

	//檔案選單
	$temp4="<select name='sel_file' onChange='jumpMenu()'>
	<option value=''>請選擇檔案";
	$fp = opendir($temp_path);
	while ( gettype($file=readdir($fp)) != boolean ){
		$temp5=($sel_file==$file)?"selected":"";
		if (is_file("$temp_path/$file") && (substr($file,0,6)=="XDESRT" || substr($file,0,6)=="Xdesrt")){
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
	<td class='title_sbody1' nowrap><input type=submit name='doup_key' value='上傳'></td>
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
		$sel_year=substr($sel_file,6,2);
		$sel_study='1';
	   
		//顯示第一位學生資料
		$file_name=$temp_path."/".$sel_file;
		if ( !$fp = dbase_open($file_name,0) ) { 
       			echo 'Cannot open $file_name\n'; 
       			exit; 
	   	}
		$nr = dbase_numrecords($fp);
		$temp1="";
		$k=dbase_get_record($fp,1);
		$kfirst=$k[0];
		$reward_date=substr($k[1],0,4)."-".substr($k[1],4,2)."-".substr($k[1],6,2);
		$class_year=$sel_study + $IS_JHORES;
		for ($i=5;$i<=7;$i++) {
			if ($k[$i]>0) $rkind=$i; 
	   	}
	   	$rekind=$reward_kind[$k[3]*3+$rkind-4].$c_times[$k[$rkind]]."次";

		$temp9="<select name='year'>";
		$ss=array();
		$i=0;
		$sql_select = "select distinct year from score_ss where enable='1' order by year";
		$recordSet=$CONN->Execute($sql_select);
		while (!$recordSet->EOF) {	
			$year = $recordSet->fields["year"];
			$ss_temp=$year;
			if (!in_array($ss_temp,$ss)){
				$selected=($sel_year==$year)?"selected":"";
				$temp9.="<option value='$ss_temp' $selected>$sel_year"."學年度";
				$ss[$i]=$ss_temp;
				$i++;		   
			}
			$recordSet->MoveNext();		   
	   	}
		$temp9.="</select><input type='text' name='sel_study' value='$sel_study' maxlength='1' size='1'>年級";
		$sql_select = "select * from stud_base where stud_id=$kfirst";
		$recordSet = $CONN->Execute($sql_select);
		$studname=$recordSet->fields['stud_name'];

		$main.="
		<form name='form2' enctype='multipart/form-data' action='{$_SERVER['PHP_SELF']}' method='post'>
		<tr bgcolor='#FFFFFF'>
		<td class='title_sbody1' nowrap>匯入學年度年級：<td colspan=2>$temp9</td>
		</tr>
		<tr bgcolor='#FFFFFF'>
		<td class='title_sbody1' nowrap colspan='3'>
		<table cellspacing='1' cellpadding='3' class='main_body'>
		<tr>
		<td class='title_sbody2'>學號<br>姓名<td class='title_sbody2'>日期<td class='title_sbody2'>獎懲<td class='title_sbody2'>事由<td class='title_sbody2'>依據</td>
		</tr>
		<tr>
		<td class='title_sbody1'>$kfirst<br>$studname<td class='title_sbody1'>$reward_date<td class='title_sbody1'>$rekind<td class='title_sbody1'>$k[4]<td class='title_sbody1'>$k[8]</td>
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
		dbase_close($fp);
	} else {
		$main.="
		</table>
		$help
		";
	}
	return $main;
}

function trans_reward(){
	global $menu_p,$temp_path,$sel_file,$year,$sel_study,$c_times,$reward_kind,$CONN,$IS_JHORES;
	$toolbar=make_menu($menu_p);

	$title_temp="<td class='title_sbody2'>學號<br>姓名<td class='title_sbody2'>日期<td class='title_sbody2'>獎懲<td class='title_sbody2'>事由<td class='title_sbody2'>依據<td class='title_sbody2'>匯入狀況</td>";

	$main="
	$toolbar
	<table cellspacing='1' cellpadding='3' class='main_body'>
	<tr>
	$title_temp
	</tr>
	";
	$file_name=$temp_path."/".$sel_file;
	if ( !$fp = dbase_open($file_name,0) ) { 
		echo 'Cannot open $file_name\n'; 
		exit; 
	} 
	$update_id=$_SESSION['session_tea_name'];;
	if ($_SERVER['HTTP_X_FORWARDED_FOR']){ 
		$update_ip=$_SERVER['HTTP_X_FORWARDED_FOR']; 
	} else { 
		$update_ip=$_SERVER['REMOTE_ADDR']; 
	}
	$nr = dbase_numrecords($fp);
	for ($i=1; $i<=$nr; $i++){ 
		$k=dbase_get_record($fp,$i);
		$sql_select = "select stud_name from stud_base where stud_id='$k[0]'";
		$recordSet = $CONN->Execute($sql_select);
		$studname=$recordSet->fields['stud_name'];
		$reward_date=substr($k[1],0,4)."-".substr($k[1],4,2)."-".substr($k[1],6,2);
		$move_year_seme=$year.$k[2];
		$reward_reason=$k[4];
		$reward_base=$k[8];
		$rkind=($k[3]==0)?(-1):1;
		$reward_div=2-$k[3];
		for ($j=5;$j<=7;$j++) 
			if ($k[$j]>0) {
				$re=$j; 
				$rkind=($k[$j]+($j-5)*2)*$rkind;
			}
		$rekind=$reward_kind[$k[3]*3+$re-4].$c_times[$k[$re]]."次";
		$main.="<tr><td class='title_sbody1'>".$k[0]."<br>$studname</td><td class='title_sbody1'>$reward_date<td class='title_sbody1'>$rekind<td class='title_sbody1'>$k[4]<td class='title_sbody1'>$k[8]";
		$sql_select = "select reward_id from reward where stud_id='$k[0]' and reward_kind='$rkind' and reward_reason='$k[4]' and move_date='$reward_date'";
		$recordSet = $CONN->Execute($sql_select);
		$reid=$recordSet->fields['reward_id'];
		if ($reid=="") {
			$sql_select = "insert into reward (reward_div,stud_id,reward_kind,move_year_seme,move_date,reward_reason,reward_base,update_id,update_ip,reward_sub) values ('$reward_div','$k[0]','$rkind','$move_year_seme','$reward_date','$k[4]','$k[8]','$update_id','$update_ip','1')";
			$recordSet = $CONN->Execute($sql_select);
			$main.="<td class='title_sbody1'>匯入成功</td>";
		} else
			$main.="<td bgcolor='#ffffff'><font color='#ff0000'>已有資料</font></td>";
	}
	$main.="</tr>";
	dbase_close($fp);
	$main.="</table>";
	$main.="<br><font color='#ff0000'>匯入資料時不允許同一日有相同事由的獎懲，若非錯誤，請再自行輸入。</font>";
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
