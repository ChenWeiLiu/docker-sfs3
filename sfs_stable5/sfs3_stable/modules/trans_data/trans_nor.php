<?php

// $Id: trans_nor.php 5310 2009-01-10 07:57:56Z hami $

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
if ($_FILES['upload_file']['size'] >0 && $file_name != "" && strstr($file_name,"XGUGN") && substr($file_name,(strpos($file_name,".")+1),3) == "DBF"){
   copy($_FILES['upload_file']['tmp_name'],$temp_path.$_FILES['upload_file']['name']);
}

if ($trans_key){
	$main=trans_nor();
} else {
	$main=view_trans_nor();
}

//秀出網頁
head("匯入日常成績");
echo $main;
foot();

function view_trans_nor(){
	global $menu_p,$temp_path,$sel_file,$CONN,$IS_JHORES;
	$toolbar=make_menu($menu_p);
	
	//說明
	$help_text="
	如果檔案未上傳，請先選擇一個檔案上傳。||
	如果檔案已上傳，則選擇要匯入的檔案。||
	上傳檔案：「\student\person\g9x\xgugn9x.dbf」。||
	檔案說明：xgugn91.dbf為91學年度，依此類推。
	";
	$help=help($help_text);

	//檔案選單
	$temp4="<select name='sel_file' onChange='jumpMenu()'>
	<option value=''>請選擇檔案";
	$fp = opendir($temp_path);
	while ( gettype($file=readdir($fp)) != boolean ){
		$temp5=($sel_file==$file)?"selected":"";
		if (is_file("$temp_path/$file") && (substr($file,0,5)=="XGUGN" || substr($file,0,5)=="Xgugn")){
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
	以上為檔案內第一位及最後一位學生資料，請先核對是否正確。||
	若學生姓名未出現，請先匯入學生資料。||
	本資料檔包含上、下學期資料，所以請務必選擇正確學期。||
	若最後一位學生資料仍為第一學期，則表示第二學期的資料尚未輸入。||
	若欄內資料為999，表示此欄未輸入資料，匯入時會自動轉成0。 
	";
	$help=help($help_text);

		//計算學年學期
		$sel_year=substr($sel_file,5,2);
		$sel_study=1;

		//顯示第一位學生資料
		$file_name=$temp_path."/".$sel_file;
		if ( !$fp = dbase_open($file_name,0) ) { 
			echo '無法開啟 $file_name\n'; 
			exit; 
		}
		$nr = dbase_numrecords($fp);
		$temp1="";
		$k=dbase_get_record($fp,1);
		$class_year=$sel_study + $IS_JHORES;

		$temp9="<select name='year_seme'>";
		$ss=array();
		$i=0;
		$sql_select = "select year,semester from score_ss where enable='1' order by year,semester";
		$recordSet=$CONN->Execute($sql_select);
		while (!$recordSet->EOF) {	
			$year = $recordSet->fields["year"];
			$semester = $recordSet->fields["semester"];
			$semester_name=($semester=='2')?"下":"上";
			$ss_temp="0".$year.$semester;
			if (!in_array($ss_temp,$ss)){
				$selected=(($sel_year==$year) && ($sel_seme==$semester))?"selected":"";
				$temp9.="<option value='$ss_temp' $selected>$sel_year"."學年度"."$semester_name"."學期";
				$ss[$i]=$ss_temp;
				$i++;		   
			}
			$recordSet->MoveNext();
		}
		$temp9.="</select><input type='text' name='sel_study' value='$sel_study' maxlength='1' size='1'>年級";

		$main.="
		<form name='form2' enctype='multipart/form-data' action='{$_SERVER['PHP_SELF']}' method='post'>
		<tr bgcolor='#FFFFFF'>
		<td class='title_sbody1' nowrap>匯入學期年級：<td colspan=2>$temp9</td>
		</tr>
		<tr bgcolor='#FFFFFF'>
		<td class='title_sbody1' nowrap colspan='3'>
		<table cellspacing='1' cellpadding='3' class='main_body'>
		<tr>
		<td class='title_sbody2' rowspan='2'>學號<br>姓名</td>
		<td class='title_sbody2' rowspan='2'>學期</td>
		<td class='title_sbody2'>日常表現加減分</td>
		<td class='title_sbody2' colspan='4'><center>團體活動加減分</center></td>
		<td class='title_sbody2' rowspan='2'>特殊加分</td>
		</tr>
		<tr>
		<td class='title_sbody2'><center>導師評分</center></td>
		<td class='title_sbody2'>班級活動</td>
		<td class='title_sbody2'>社團活動</td>
		<td class='title_sbody2'>學生自治</td>
		<td class='title_sbody2'>學校活動</td>
		</tr>";
		for ($i=0;$i<2;$i++) {
			if ($i==0) $k=dbase_get_record($fp,1);
			else $k=dbase_get_record($fp,$nr);
			$sql_select = "select * from stud_base where stud_id=$k[0]";
			$recordSet = $CONN->Execute($sql_select);
			$studname=$recordSet->fields['stud_name'];
			$main.="
				<tr>
				<td class='title_sbody2'>$k[0]<br>$studname</td>
				<td class='title_sbody2'>$k[1]</td>
				<td class='title_sbody2'>$k[8]</td>
				<td class='title_sbody2'>$k[11]</td>
				<td class='title_sbody2'>$k[12]</td>
				<td class='title_sbody2'>$k[13]</td>
				<td class='title_sbody2'>$k[14]</td>
				<td class='title_sbody2'>$k[16]</td>
				</tr>";
		}
		$main.="
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

function trans_nor(){
	global $menu_p,$temp_path,$sel_file,$year_seme,$sel_study,$CONN,$IS_JHORES;
	$toolbar=make_menu($menu_p);

	$nor_val=array("1"=>"表現優異","2"=>"表現良好","3"=>"表現尚可","4"=>"需再加油","5"=>"有待改進");
	$nor_kind=array("10"=>"1","9"=>"1","8"=>"2","7"=>"2","6"=>"3","5"=>"3","4"=>"3","3"=>"4","2"=>"4","1"=>"5","0"=>"5");
	$Create_db="CREATE TABLE  if not exists seme_score_nor (
	   seme_year_seme varchar(6) NOT NULL,
	   stud_id varchar(20) NOT NULL,
	   score1 smallint(3) NOT NULL default '0' ,
	   score2 smallint(3) NOT NULL default '0' ,
	   score3 smallint(3) NOT NULL default '0' ,
	   score4 smallint(3) NOT NULL default '0' ,
	   score5 smallint(3) NOT NULL default '0' ,
	   score6 smallint(3) NOT NULL default '0' ,
	   score7 smallint(3) NOT NULL default '0' ,
	   PRIMARY KEY  (seme_year_seme,stud_id),
	   KEY  (stud_id))";
	mysql_query($Create_db);
	$main="
	$toolbar
		<table cellspacing='1' cellpadding='3' class='main_body'>
		<tr>
		<td class='title_sbody2' rowspan='2'>學號<br>姓名</td>
		<td class='title_sbody2' rowspan='2'>學期</td>
		<td class='title_sbody2'>日常表現加減分</td>
		<td class='title_sbody2' colspan='4'><center>團體活動加減分</center></td>
		<td class='title_sbody2' rowspan='2'>特殊加分</td>
		</tr>
		<tr>
		<td class='title_sbody2'><center>導師評分</center></td>
		<td class='title_sbody2'>班級活動</td>
		<td class='title_sbody2'>社團活動</td>
		<td class='title_sbody2'>學生自治</td>
		<td class='title_sbody2'>學校活動</td>
		</tr>";
	$file_name=$temp_path."/".$sel_file;
	if ( !$fp = dbase_open($file_name,0) ) { 
		echo '無法開啟 $file_name\n'; 
		exit; 
	} 
	$nr = dbase_numrecords($fp);
	$sel_seme=substr($year_seme,3,1);
	$seme=$sel_seme;
	$i=1;
	$j=0;
	while ($j!=$seme) {
		$k=dbase_get_record($fp,$i);
		$j=$k[1];
		$i++;
	}
	$i=$i-1;
	$total_data=0;
	while (($i<=$nr)&&($seme==$sel_seme)) {
		$k=dbase_get_record($fp,$i);
		$sql_select = "select a.stud_name,b.seme_class from stud_base a,stud_seme b where a.stud_id=b.stud_id and a.stud_id='$k[0]'";
		$recordSet = $CONN->Execute($sql_select);
		$studname=$recordSet->fields['stud_name'];
		$seme_class=$recordSet->fields['seme_class'];
		$class_id="0".$ys[0]."_".$ys[1]."_0".substr($seme_class,0,1)."_".substr($seme_class,1,2);
		$k[8]=($k[8]=="999")?"0":$k[8];
		$k[11]=($k[11]=="999")?"0":$k[11];
		$k[12]=($k[12]=="999")?"0":$k[12];
		$k[13]=($k[13]=="999")?"0":$k[13];
		$k[14]=($k[14]=="999")?"0":$k[14];
		$k[16]=($k[16]=="999")?"0":$k[16];
		if ($k[16]>5) $k[16]=5;
		$main.="
		<tr>
		<td class='title_sbody2'>$k[0]<br>$studname</td>
		<td class='title_sbody2'>$k[1]</td>
		<td class='title_sbody2'>$k[8]</td>
		<td class='title_sbody2'>$k[11]</td>
		<td class='title_sbody2'>$k[12]</td>
		<td class='title_sbody2'>$k[13]</td>
		<td class='title_sbody2'>$k[14]</td>
		<td class='title_sbody2'>$k[16]</td>
		</tr>";
		$sql="select * from seme_score_nor where seme_year_seme='$year_seme' and stud_id='$k[0]'";
		$rs=$CONN->Execute($sql);
		$stud_id=$rs->fields['stud_id'];
		if (!$stud_id) {
			$sql="insert into seme_score_nor (seme_year_seme,stud_id,score1,score2,score3,score4,score5,score6,score7) values ('$year_seme','$k[0]','$k[8]','$k[11]','$k[12]','$k[13]','$k[14]','0','$k[16]')";
			$rs=$CONN->Execute($sql);
		}
		check_nor($year_seme,$k[0],1,$nor_val[$nor_kind[(integer)$k[8]+5]]);
		$l=round(($k[11]+$k[12]+$k[13]+$k[14])/4);
		check_nor($year_seme,$k[0],2,$nor_val[$nor_kind[$l+5]]);
		check_nor($year_seme,$k[0],3,$nor_val[$nor_kind[5]]);
		check_nor($year_seme,$k[0],4,$nor_val[$nor_kind[(integer)$k[16]+5]]);
		check_oth($year_seme,$k[0]);
		$i++;
		$k=dbase_get_record($fp,$i);
		$seme=$k[1];
		$total_data++;
	}
	dbase_close($fp);
	$main.="</table><br>共 $total_data 筆資料";
	return $main;
}

function check_nor($year_seme,$stud_id,$ss_id,$ss_val) {
	global $CONN;
	
	$sql_chk="select * from stud_seme_score_oth where seme_year_seme='$year_seme' and stud_id='$stud_id' and ss_id='$ss_id'";
	$rs_chk=$CONN->Execute($sql_chk);
	$chk_ss_id=$rs_chk->fields['ss_id'];
	$chk_ss_val=$rs_chk->fields['ss_val'];
       	if (($chk_ss_id!="")&&($ss_val!="")) {
     		$sql_chk="update stud_seme_score_oth set ss_val='$ss_val' where seme_year_seme='$year_seme' and stud_id='$stud_id' and ss_id='$ss_id'";
      		$rs_chk=$CONN->Execute($sql_chk);
       	} else {
       		$sql_chk="insert into stud_seme_score_oth (seme_year_seme,stud_id,ss_kind,ss_id,ss_val) values ('$year_seme','$stud_id','生活表現評量','$ss_id','$ss_val')";
       		$rs_chk=$CONN->Execute($sql_chk);
      	}
}

function check_oth($year_seme,$stud_id) {
	global $CONN;
	
	$sql_chk="select * from stud_seme_score_oth where seme_year_seme='$year_seme' and stud_id='$stud_id' and ss_id='0'";
	$rs_chk=$CONN->Execute($sql_chk);
	$chk_ss_id=$rs_chk->fields['ss_id'];
	if (!$chk_ss_id) {
       		$sql_chk="insert into stud_seme_score_oth (seme_year_seme,stud_id,ss_kind,ss_id,ss_val) values ('$year_seme','$stud_id','其他設定','0','')";
       		$rs_chk=$CONN->Execute($sql_chk);
      	}
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
