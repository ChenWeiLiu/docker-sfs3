<?php

// $Id: trans_reward.php 6218 2010-10-14 03:34:47Z brucelyc $

// 載入設定檔
include "config.php";

// 認證檢查
sfs_check();

//欄位資訊
$sel_file=($_POST['sel_file'])?$_POST['sel_file']:$_GET['sel_file'];
$trans_key=$_POST['trans_key'];
$ys=$_POST['year_seme'];
$trans_seme=$_POST['seme'];
if ($ys=='all') {
	while (list($k,$v)=each($trans_seme)) {
		$tseme[$k]=$v;
		$rseme[$v]=$k;
	}
} else {
	$yss=explode("_",$ys);
	$yys=$yss[0].$yss[1];
	$tseme[$yys]=$trans_seme[$yys];
	$rseme[$trans_seme[$yys]]=$yys;
}


//檔案上傳
$file_name=strtoupper($_FILES['upload_file']['name']);
if ($_FILES['upload_file']['size'] >0 && $file_name != "" && strstr($file_name,"XDESRT") && substr($file_name,(strpos($file_name,".")+1),3) == "CSV"){
	copy($_FILES['upload_file']['tmp_name'],$temp_path.$file_name);
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
	要處理的檔案：「\student\person\p9x\xdesrt9x.dbf」。||
	請先將此檔以Excel打開，然後存成CSV格式後再選擇上傳。||
	也就是上傳前先將檔案存成「XDESRT9x.CSV」。||
	檔案說明：xdesrt91.dbf為91學年度入學學生的各學期資料，依此類推。
	";
	$help=help($help_text);

	//檔案選單
	$temp4="<select name='sel_file' onChange='jumpMenu()'>
	<option value=''>請選擇檔案";
	$fp = opendir($temp_path);
	while ( gettype($file=readdir($fp)) != boolean ){
		$temp5=($sel_file==$file)?"selected":"";
		if (is_file("$temp_path/$file") && (substr($file,0,6)=="XDESRT" || substr($file,0,6)=="Xdesrt") && (substr($file,-3,3)=="csv" || substr($file,-3,3)=="CSV")){
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
	<td class='title_sbody1' nowrap><input type=submit name='doup_key' value='上傳'></td>
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
	以上為檔案內各學期第一筆學生資料，請先核對是否正確。||
	若學生姓名未出現，請先匯入學生資料。
	";
		$help=help($help_text);

		//計算學年學期
		$sel_year=substr($sel_file,6,2);
		$sel_study='1';
	   
		//顯示第一位學生資料
		$file_name=$temp_path."/".$sel_file;
		$fp=fopen($file_name,"r");

		$h=0;
		$seme=1;
		while ($k=sfs_fgetcsv($fp, 2000, ",")) {
			if ($h>0) {
				if ($os!=$k[2]) {
					$stud_id[$seme]=$k[0];
					$reward_reason[$seme]=addslashes($k[4]);
					$reward_base[$seme]=addslashes($k[8]);
					$dd=explode("/",$k[1]);
					$reward_date[$seme]=$dd[0]."-".$dd[1]."-".$dd[2];
					for ($i=5;$i<=7;$i++) if ($k[$i]>0) $rkind=$i;
					$rekind[$seme]=$reward_kind[$k[3]*3+$rkind-4].$c_times[$k[$rkind]]."次";
					$rs=$CONN->Execute("select stud_name,stud_study_year,curr_class_num from stud_base where stud_id='$k[0]'");
					$stud_name[$seme]=addslashes($rs->fields['stud_name']);
					$study_year=$rs->fields['stud_study_year'];
					$seme++;
				}
				$os=$k[2];
			}
			$h++;
		}
		$temp9="<select name='year_seme'>";
		while (list($seme,$v)=each($reward_date)) {	
			$year=$study_year+floor(($seme-1)/2);
			$se=($seme-1)%2+1;
			$ys=$year."_".$se;
			$selected=($year_seme==$ys)?"selected":"";
			$seme_data[$seme]=$year."學年度第".$se."學期";
			$temp9.="<option value='$ys' $selected>".$seme_data[$seme];
			$trans_seme.="<input type='hidden' name='seme[".$year.$se."]' value='".$seme."'>";
		}
		if (count($seme_data)>0) $temp9.="<option value='all'>所有學期";
		$temp9.="</select>";
		$main.="
			<form name='form2' enctype='multipart/form-data' action='{$_SERVER['SCRIPT_NAME']}' method='post'>
			<tr bgcolor='#FFFFFF'>
			<td class='title_sbody1' nowrap>匯入學年度：<td colspan=2>$temp9</td>
			</tr>
			<tr bgcolor='#FFFFFF'>
			<td class='title_sbody1' nowrap colspan='3'>
			<table cellspacing='1' cellpadding='3' class='main_body'>
			<tr class='title_sbody2'>
			<td align='center'>學期<td align='center'>學號<br>姓名<td align='center'>日期<td align='center'>獎懲<td align='left'>事由<td align='left'>依據</td>
			</tr>";
		while (list($seme,$v)=each($stud_id)) {
			$main.="
				<tr class='title_sbody1'>
				<td bgcolor='#ffffff'>".$seme_data[$seme]."<td bgcolor='#ffffff' align='center'>".$stud_id[$seme]."<br>".stripslashes($stud_name[$seme])."<td bgcolor='#ffffff' align='left'>".$reward_date[$seme]."<td bgcolor='#ffffff' align='left'>".$rekind[$seme]."<td bgcolor='#ffffff' align='left'>".stripslashes($reward_reason[$seme])."<td bgcolor='#ffffff' align='left'>".stripslashes($reward_base[$seme])."</td>
				</tr>";
		}
		$main.="
			</table>
		       	<input type='hidden' name='trans_key' value='trans'>
	        	<input type='hidden' name='sel_file' value='$sel_file'>
	        	$trans_seme
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

function trans_reward(){
	global $menu_p,$temp_path,$sel_file,$c_times,$reward_kind,$tseme,$rseme,$CONN,$IS_JHORES;
	$toolbar=make_menu($menu_p);

	$sql_select = "select stud_name,student_sn,stud_id from stud_base order by student_sn";
	$recordSet = $CONN->Execute($sql_select);
	while (!$recordSet->EOF) {
		$id=$recordSet->fields['stud_id'];
		$student_sn[$id]=$recordSet->fields['student_sn'];
		$studname[$id]=addslashes($recordSet->fields['stud_name']);
		$recordSet->MoveNext();
	}
	$title_temp="<td class='title_sbody2'>學號<br>姓名<td class='title_sbody2'>日期<td class='title_sbody2'>獎懲<td class='title_sbody2'>事由<td class='title_sbody2'>依據<td class='title_sbody2'>匯入狀況</td>";

	$main="
	$toolbar
	<table cellspacing='1' cellpadding='3' class='main_body'>
	<tr>
	$title_temp
	</tr>
	";
	$file_name=$temp_path."/".$sel_file;
	$fp=fopen($file_name,"r");
	//先取掉第一筆資料, 因為是標題
	$k=sfs_fgetcsv($fp, 2000, ",");
	$update_id=$_SESSION['session_tea_name'];;
	if ($_SERVER['HTTP_X_FORWARDED_FOR']){ 
		$update_ip=$_SERVER['HTTP_X_FORWARDED_FOR']; 
	} else { 
		$update_ip=$_SERVER['REMOTE_ADDR']; 
	}
	$k=sfs_fgetcsv($fp, 2000, ",");
	while($k=sfs_fgetcsv($fp, 2000, ",")) {
		if (in_array($k[2],$tseme)) {
			$d=explode("/",$k[1]);
			$reward_date=sprintf("%04d-%02d-%02d",$d[0],$d[1],$d[2]);
			$year=$d[0]-1911+floor(($seme-1)/2);
			$reward_year_seme=$rseme[$k[2]];
			$reward_reason=addslashes($k[4]);
			$reward_base=addslashes($k[8]);
			$rkind=($k[3]==0)?(-1):1;
			$reward_div=2-$k[3];
			for ($j=5;$j<=7;$j++) 
				if ($k[$j]>0) {
					$re=$j; 
					$rkind=($k[$j]+($j-5)*2)*$rkind;
				}
			$rekind=$reward_kind[$k[3]*3+$re-4].$c_times[$k[$re]]."次";
			$main.="<tr class='title_sbody1'><td align='center'>".$k[0]."<br>".stripslashes($studname[$k[0]])."</td><td align='left'>$reward_date<td align='left'>$rekind<td align='left'>$k[4]<td align='left'>".stripslashes($k[8]);
			$sn=$student_sn[$k[0]];
			$k[4]=addslashes($k[4]);
			$k[8]=addslashes($k[8]);
			$sql_select = "select reward_id from reward where stud_id='$k[0]' and reward_kind='$rkind' and reward_reason='$k[4]' and reward_date='$reward_date'";
			$recordSet = $CONN->Execute($sql_select);
			$reid=$recordSet->fields['reward_id'];
			if ($reid=="") {
				$sql_select = "insert into reward (reward_div,stud_id,reward_kind,reward_year_seme,reward_date,reward_reason,reward_base,update_id,update_ip,reward_sub,student_sn) values ('$reward_div','$k[0]','$rkind','$reward_year_seme','$reward_date','$k[4]','$k[8]','$update_id','$update_ip','1','$sn')";
				$recordSet = $CONN->Execute($sql_select);
				$main.="<td align='left'>匯入成功</td>";
			} else
				$main.="<td bgcolor='#ffffff' align='left'><font color='#ff0000'>已有資料</font></td>";
		}
	}
	$main.="</tr>";
	$main.="</table>";
	$help_text="匯入資料時不允許同一日有相同事由的獎懲，若非錯誤，請再自行輸入。||請記得<a href='cal.php'>重新統計日常成績</a>。";
	$main.=help($help_text);
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
