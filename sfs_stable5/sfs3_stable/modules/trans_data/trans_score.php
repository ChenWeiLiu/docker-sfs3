<?php

// $Id: trans_score.php 5310 2009-01-10 07:57:56Z hami $

// 載入設定檔
include "config.php";
include "../../include/sfs_case_subjectscore.php";

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

//秀出網頁
head("匯入階段成績");

//欄位資訊
$sel_file=($_POST['sel_file'])?$_POST['sel_file']:$_GET['sel_file'];
$trans_key=$_POST['trans_key'];
$run_key=$_POST['run_key'];
$trans_sub=$_POST['trans_sub'];
$des_subject=$_POST['des_subject'];
$trans_ss=$_POST['trans_ss'];
$sel_year=$_POST['sel_year'];
$sel_seme=$_POST['sel_seme'];
$sel_study=$_POST['sel_study'];
$max_sub=$_POST['max_sub'];
$year_seme=$_POST['year_seme'];
if ($year_seme) {
	$ys=explode("_",$year_seme);
	$sel_year=$ys[0];
	$sel_seme=$ys[1];
}

//檔案上傳
$file_name=strtoupper($_FILES['upload_file']['name']);
if ($_FILES['upload_file']['size'] >0 && $file_name != "" && strstr($file_name,"YGSC") && substr($file_name,(strpos($file_name,".")+1),3) == "DBF"){
	copy($_FILES['upload_file']['tmp_name'],$temp_path.$_FILES['upload_file']['name']);
}

if ($run_key){
	$sel_study+=$IS_JHORES;
	$main=trans_now();
} else {
	if ($trans_key){
		$main=trans_score();
	} else {
		$main=view_trans_score();
	}
}	

//秀出內容
echo $main;
foot();

function view_trans_score(){
	global $menu_p,$temp_path,$sel_file,$trans_key,$trans_sub,$des_subject,$trans_ss,$sel_year,$sel_seme,$sel_study,$max_sub,$CONN,$IS_JHORES;
	$toolbar=make_menu($menu_p);
	
	//說明
	$help_text="
	如果檔案未上傳，請先選擇一個檔案上傳。||
	如果檔案已上傳，則選擇要匯入的檔案。||
	上傳檔案：「\student\stage\g9x\ygsc9xxxx.dbf」。||
	檔案說明：ygsc9121.dbf為91學年度第2學期1年級，依此類推。
	";
	$help=help($help_text);

	//檔案選單
	$temp4="<select name='sel_file' onChange='jumpMenu()'>
	<option value=''>請選擇檔案";
	$fp = opendir($temp_path);
	while ( gettype($file=readdir($fp)) != boolean ){
		$temp5=($sel_file==$file)?"selected":"";
		if (is_file("$temp_path/$file") && (substr($file,0,4)=="YGSC" || substr($file,0,4)=="Ygsc")) {
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
			若學生姓名未出現，請先匯入學生資料。||
			若科目選單沒有內容，請先設定好科目資料。||
			若欄內資料為999，表示此欄未輸入資料。||
			選取要匯入的欄位，按下匯入鍵前最好先將資料庫內資料進行備份。
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
		$nr = dbase_numrecords($fp);
		$class_year=$sel_study + $IS_JHORES;
		$sql_ratio="select test_ratio from score_setup where year='$sel_year' and semester='$sel_seme' and class_year='$class_year' and enable='1'";
		$rs_ratio=$CONN->Execute($sql_ratio);
		$r=explode("-",$rs_ratio->fields['test_ratio']);
		$j=1;
		for ($i=1; $i<=$nr; $i++){ 
			$k=dbase_get_record($fp,$i);
			if ($k[0] == $kfirst){
				//科目選單
				$temp3="<select name='des_subject[$j]'><option value='0' selected>科目選單</option>";
				$sql1="select subject_id,ss_id,scope_id from score_ss where year='$sel_year' and semester='$sel_seme' and  class_year='$class_year' and enable=1 and need_exam=1";
				$rs1=$CONN->Execute($sql1) or die($sql1);
				$l=0;
				while(!$rs1->EOF){
					$subject_id[$l] = $rs1->fields["subject_id"];
					$ss_id[$l] = $rs1->fields["ss_id"];
					$scope_id[$l] = $rs1->fields["scope_id"];
					if($subject_id[$l]=="0") $subject_id[$l] = $scope_id[$l];
					$rs2=$CONN->Execute("select subject_name from score_subject where subject_id='$subject_id[$l]'");
					$subject_name[$l] = $rs2->fields["subject_name"];
					$temp3.="<option value='$ss_id[$l]' $selected>$subject_name[$l]</option>";
					$l++;
					$rs1->MoveNext();
				}
				$temp3.="</select>";
				$avg=number_format((($k[5]+$k[6]+$k[7])*$r[0]+($k[14]+$k[15]+$k[16])*$r[1])/3/($r[0]+$r[1]),2);
				$temp1.="
					<tr>
					<td class='title_sbody1'><p align='center'><input type='checkbox' name='trans_sub[$j]' value=$k[2]></p><td class='title_sbody2'>$k[2]<td class='title_sbody1' bgcolor='#ffffff'>$k[5]<td class='title_sbody1' bgcolor='#ffffff'>$k[6]<td class='title_sbody1' bgcolor='#ffffff'>$k[7]<td class='title_sbody1' bgcolor='#ffffff'>$k[14]<td class='title_sbody1' bgcolor='#ffffff'>$k[15]<td class='title_sbody1' bgcolor='#ffffff'>$k[16]<td class='title_sbody1' bgcolor='#ffffff'>$avg<td class='title_sbody1' bgcolor='#ffffff'>$temp3</td>
					</tr>";
				$j++;
			}
		}
		dbase_close($fp);
		$max_sub=$j-1;

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
				$temp5=(($sel_year==$year) && ($sel_seme==$semester))?"selected":"";
				$temp9.="<option value='$ss_temp' $temp5>$year"."學年度"."$semester_name"."學期";
				$ss[$i]=$ss_temp;
				$i++;
			}
			$recordSet->MoveNext();		   
		}
		$temp9.="</select>";
		$sql_select = "select * from stud_base where stud_id=$kfirst";
		$recordSet = $CONN->Execute($sql_select);
		$studname=$recordSet->fields['stud_name'];
	
		//核對方框
		$temp2="<td class='title_sbody1'>選擇<td class='title_sbody2'>原科目代碼";
		for ($i=1; $i<=7; $i++){ 
			$temp2.="<td class='title_sbody1'><p align='center'><input type='checkbox' name='trans_ss[$i]'></p>";
		}
		$temp2.="<td class='title_sbody1'><p align='center'>
			<input type='hidden' name='trans_key' value='trans'>
			<input type='hidden' name='sel_file' value='$sel_file'>
			<input type='hidden' name='max_sub' value='$max_sub'>
			<input type=submit value='開始匯入'></p></td>";

		$main.="
			<form name='form2' enctype='multipart/form-data' action='{$_SERVER['PHP_SELF']}' method='post'>
			<tr bgcolor='#FFFFFF'>
			<td class='title_sbody1' nowrap>匯入學期年級：<td colspan=2>$temp9<input type='text' name='sel_study' value='$sel_study' maxlength='1' size='1'>年級</td>
			</tr>
			<tr bgcolor='#FFFFFF'>
			<td class='title_sbody1' nowrap colspan='3'>
			<table cellspacing='1' cellpadding='3' class='main_body'>
			<tr>
			<td class='title_sbody1'>學號<br>姓名<td class='title_sbody1'>$kfirst<br>$studname<td class='title_sbody1'>第一次<br>段考成績<td class='title_sbody1'>第二次<br>段考成績<td class='title_sbody1'>第三次<br>段考成績<td class='title_sbody1'>第一次<br>平時成績<td class='title_sbody1'>第二次<br>平時成績<td class='title_sbody1'>第三次<br>平時成績<td class='title_sbody1'>學期成績<td class='title_sbody1'><p align='center'>匯入後科目名稱</p></td>
			</tr>
			$temp1
			<tr>
			$temp2
			</tr>
			</table>
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

function trans_score(){
	global $menu_p,$temp_path,$sel_file,$run_key,$trans_sub,$des_subject,$trans_ss,$sel_year,$sel_seme,$sel_study,$max_sub,$CONN;

	$toolbar=make_menu($menu_p);
	
	for ($k=1;$k<=$max_sub;$k++) {
		if ($trans_sub[$k]) {
			if ($des_subject[$k]) {
				$i=$des_subject[$k];
				$sql="select scope_id,subject_id from score_ss where ss_id='$i'";
				$rs=$CONN->Execute($sql);
				$i=$rs->fields['subject_id'];
				if ($i=="0") $i=$rs->fields['scope_id'];
				$subject_name=get_subject_name($i);
			} else {
				$subject_name="<font color=#ff0000>未選科目</font>";
				$sub_err=1;
			}   
			$temp1.="<tr bgcolor='#FFFFFF'><td>$trans_sub[$k]==&gt;$subject_name<td>";	
			$l=0;
			$temp3="";
			for ($i=0;$i<=1;$i++) {
				for ($j=1;$j<=3;$j++) {
					if ($trans_ss[$j+$i*3]) {
						$temp3.="第".$j."階段";
						$temp3.=($i)?"平時成績 &nbsp;&nbsp;":"定期評量 &nbsp;&nbsp;";
					}
				}
				if ($temp3) $temp3.="<br>";
			}
			if ($trans_ss[7]) $temp3.="學期成績";
			if ($temp3) {
				$temp1.="$temp3";
			} else {
				$temp1.="<font color=#ff0000>未選擇匯入成績</font>";
				$sub_err=1;
			}
			$temp1.="</td></tr>";
		}   
	}
	if ($temp1) {
		$temp1="<table cellspacing='1' cellpadding='3' class='main_body'><tr bgcolor='#FFFFFF'><td class='title_sbody2'><p align='center'>匯入科目</p><td class='title_sbody2'><p align='center'>匯入段次</p></td></tr>".$temp1;
		$temp1.="</table>";
	} else {
		$temp1.="沒有選擇";
		$sub_err=1;
	}
	if ($sub_err) {
		$temp2="<br><input type=submit value='回上頁'>";
		$temp3="<form name='form0' onSubmit='history.go(-1);'>";
	} else {
		$temp2="";
		for ($i=1; $i<=$max_sub; $i++){
			$temp2.="<input type='hidden' name='trans_sub[$i]' value='$trans_sub[$i]'>";
			$temp2.="<input type='hidden' name='des_subject[$i]' value='$des_subject[$i]'>";
		}   
		for ($i=1; $i<=7; $i++){
			$temp2.="<input type='hidden' name='trans_ss[$i]' value='$trans_ss[$i]'>";
		}   
		$temp2.="<br><input type='hidden' name='run_key' value='run_now'>
			<input type='hidden' name='sel_file' value='$sel_file'>
			<input type='hidden' name='max_sub' value='$max_sub'>
			<input type=submit value='開始匯入資料'>";
		$temp3="<form name='form0' enctype='multipart/form-data' action='{$_SERVER['PHP_SELF']}' method='post'>";
	}

	$semester_name=($sel_seme=='2')?"下":"上";
	$semester_name.="學期";

	$help_text="
	為方便匯入工作，本程式僅將成績資料匯入，並不同時計算學期成績。||
	未來本模組將有其他程式處理學期成績。||
	為方便匯入之後續作業，成績資料匯入後，會將該科鎖定，若要更改成績，請先由教務處成績管理部份將鎖定解除。
	";
	$help=help($help_text);
	
	$main="
	$toolbar
	<table cellspacing='1' cellpadding='3' class='main_body'>
	$temp3
	<tr bgcolor='#FFFFFF'>
		<td class='title_sbody2'>匯入檔案<td>$sel_file</td>
	</tr>
	<tr bgcolor='#FFFFFF'>
		<td class='title_sbody2'>匯入學年<td>$sel_year<b></b>學年度</td>
	</tr>
	<tr bgcolor='#FFFFFF'>
		<td class='title_sbody2'>匯入學期<td>$semester_name</td>
	</tr>
	<tr bgcolor='#FFFFFF'>
		<td class='title_sbody2'>匯入年級<td>$sel_study<b></b>年級</td>
	</tr>
	<tr bgcolor='#FFFFFF'>
		<td class='title_sbody2'>匯入資料<td>$temp1</td>
	</tr>
   	<input type='hidden' name='sel_year' value='$sel_year'>
   	<input type='hidden' name='sel_seme' value='$sel_seme'>
   	<input type='hidden' name='sel_study' value='$sel_study'>
	</table>$temp2</form>
	$help";	

	return $main;
}


function trans_now(){
	global $menu_p,$temp_path,$sel_file,$run_key,$trans_sub,$des_subject,$trans_ss,$sel_year,$sel_seme,$sel_study,$max_sub,$CONN;

	$toolbar=make_menu($menu_p);
	echo "$toolbar
		<table cellspacing='1' cellpadding='3' class='main_body'>
		<tr bgcolor='#FFFFFF'>
		<td>";
	$tableName="score_semester_".$sel_year."_".$sel_seme;
	$Create_db="CREATE TABLE if not exists $tableName (
		score_id int(10) unsigned NOT NULL  auto_increment,
		class_id varchar(11) NOT NULL default '' ,
		student_sn int(10) unsigned NOT NULL default '0' ,
		ss_id smallint(5) unsigned NOT NULL default '0' ,
		score float unsigned NOT NULL default '0' ,
		test_name varchar(20) NOT NULL default '' ,
		test_kind varchar(10) NOT NULL default '定期評量' ,
		test_sort tinyint(3) unsigned NOT NULL default '0' ,
		update_time datetime NOT NULL default '0000-00-00 00:00:00' ,
		sendmit enum('0','1') NOT NULL default '1' ,
		PRIMARY KEY  (score_id))";
	mysql_query($Create_db);  
	$Create_db="CREATE TABLE if not exists $tableName2 (
		edu_adm_id int(10) unsigned NOT NULL  auto_increment,
		class_id varchar(11) NOT NULL default '' ,
		student_sn int(10) unsigned NOT NULL default '0' ,
		ss_id smallint(5) unsigned NOT NULL default '0' ,
		score float NOT NULL default '0' ,
		test_sort tinyint(3) unsigned NOT NULL default '0' ,
		update_time datetime NOT NULL default '0000-00-00 00:00:00' ,
		PRIMARY KEY  (edu_adm_id))";
	mysql_query($Create_db);  

	$today=date("Y-m-d G:i:s",mktime (date("G"),date("i"),date("s"),date("m"),date("d"),date("Y")));
	$file_name=$temp_path."/".$sel_file;
	if ( !$fp = dbase_open($file_name,0) ) { 
		echo '無法開啟 $file_name\n';
		exit;
	}
	$temp1="";
	$seme_year_seme=sprintf("%03d%d",$sel_year,$sel_seme);
	$sql = "select student_sn,stud_id,seme_class,seme_num from stud_seme where seme_year_seme='$seme_year_seme' and seme_class like '$sel_study%'";
	$rs = $CONN->Execute($sql);
	$all_sn="";
	while (!$rs->EOF) {
		$stud_id=$rs->fields['stud_id'];
		$student_sn[$stud_id]=$rs->fields['student_sn'];
		$seme_class[$stud_id]=$rs->fields['seme_class'];
		$seme_num[$stud_id]=$rs->fields['seme_num'];
		$all_sn.=$student_sn[$stud_id].",";
		$rs->MoveNext();
	}
	$all_sn=substr($all_sn,0,-1);
	$sql = "select stud_id,stud_name from stud_base where student_sn in ($all_sn)";
	$rs = $CONN->Execute($sql);
	while (!$rs->EOF) {
		$stud_id=$rs->fields['stud_id'];
		$stud_name[$stud_id]=$rs->fields['stud_name'];
		$rs->MoveNext();
	}
	$sql_ratio="select test_ratio from score_setup where year='$sel_year' and semester='$sel_seme' and class_year='$class_year' and enable='1'";
	$rs_ratio=$CONN->Execute($sql_ratio);
	$r=explode("-",$rs_ratio->fields['test_ratio']);
	$all=0;
	$nr = dbase_numrecords($fp);
	for ($i=1; $i<=$nr; $i++){ 	   
		$k=dbase_get_record($fp,$i);
		if (in_array(trim($k[2]),$trans_sub)){
			$j=1;
			$ssn=$student_sn[$k[0]];
			while ((trim($k[2])!=$trans_sub[$j]) && ($j<=$max_sub)) {$j++;}
			$class_id=sprintf("%03d_%1d_%02d_%02d",$sel_year,$sel_seme,$sel_study,$seme_num[$k[2]]);
			$ss_id=$des_subject[$j];
			for ($l=1; $l<=6; $l++){
				if ($trans_ss[$l]){
					$score=$k[$l+4+($l>3)*6];
					if ($score=="999") {$score="-100";}
					$test_name=($l<4)?"定期評量":"平時成績";
					$test_kind=$test_name;
					$test_sort=fmod(($l-1),3)+1;
					$update_time=$today;
					$sendmit='0';
					$sql_select = "select score_id,score from $tableName where class_id='$class_id' and student_sn='$student_sn' and ss_id='$ss_id' and test_name='$test_name' and test_kind='$test_kind' and test_sort='$test_sort'";
					$recordSet = $CONN->Execute($sql_select);
					$score_id=$recordSet->fields["score_id"];
					if ($score_id) {
	      		    			$old_score=$recordSet->fields["score"];
						if ($score!="-100") {
							$sql_select = "update $tableName set score='$score' where score_id='$score_id'";
							$recordSet = $CONN->Execute($sql_select);
							echo "<font color=#000088>".$k[0]."-".$stud_name[$k[0]]."</font>-".$ss_id."-第".$test_sort."次".$test_name."_".$score."--成績匯入<br>";
							$all++;
						} else {
							echo "<font color=#ff0000>".$k[0]."-".$stud_name[$k[0]]."-".$ss_id."-第".$test_sort."次".$test_name."-已有成績</font><br>";
						}
					} else {
						$sql_select = "insert into $tableName (class_id,student_sn,ss_id,score,test_name,test_kind,test_sort,update_time,sendmit) values ('$class_id','$ssn','$ss_id','$score','$test_name','$test_kind','$test_sort','$update_time','$sendmit')";
						$recordSet = $CONN->Execute($sql_select);
						$sql_select = "insert into $tableName2 (class_id,student_sn,ss_id,score,test_sort,update_time) values ('$class_id','$ssn','$ss_id','$score','$test_sort','$update_time')";
						$recordSet = $CONN->Execute($sql_select);
						echo "<font color=#000088>".$k[0]."-".$stud_name[$k[0]]."</font>-".$ss_id."-第".$test_sort."次".$test_name."_".$score."--成績匯入<br>";
						$all++;
					}
				}
			}
			if ($trans_ss[7]) {
				$ss_score=number_format((($k[5]+$k[6]+$k[7])*$r[0]+($k[14]+$k[15]+$k[16])*$r[1])/3/($r[0]+$r[1]),2);
				$sql_ss="select * from stud_seme_score where seme_year_seme='$seme_year_seme' and student_sn='$ssn' and ss_id='$ss_id'";
				$rs_ss=$CONN->Execute($sql_ss);
				if ($rs_ss->recordcount()==0) {
					$sql_insert="insert into stud_seme_score (seme_year_seme,student_sn,ss_id,ss_score,ss_score_memo,teacher_sn) values ('$seme_year_seme','$ssn','$ss_id','$ss_score','','$_SESSION[session_tea_sn]')";
					$rs_insert=$CONN->Execute($sql_insert);
					echo "<font color=#000088>".$k[0]."-".$stud_name[$k[0]]."</font>-".$ss_id."-學期成績_".$ss_score."--成績匯入<br>";
					$all++;
				} else { 
					if (doubleval($ss_score=$rs_ss->fields['ss_score'])==0) {
						$sql_update="update stud_seme_score set ss_score='$ss_score',teacher_sn='$_SESSION[session_tea_sn]' where seme_year_seme='$seme_year_seme' and student_sn='$ssn' and ss_id='$ss_id'";
						$rs_update=$CONN->Execute($sql_update);
						echo "<font color=#000088>".$k[0]."-".$stud_name[$k[0]]."</font>-".$ss_id."-學期成績_".$ss_score."--成績匯入<br>";
						$all++;
					}
				}
			}
		}
	}
	dbase_close($fp);
	echo "
		<br><font color=#000088>共 $all 筆資料匯入完成</font></td>
		</tr>
		</table>
		$help
		";
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
