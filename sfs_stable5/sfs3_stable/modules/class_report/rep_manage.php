<?php
include_once('config.php');

sfs_check();

//製作選單 ( $school_menu_p陣列設定於 module-cfg.php )
$tool_bar=&make_menu($school_menu_p);

//讀取目前操作的老師有沒有管理權
$module_manager=checkid($_SERVER['SCRIPT_FILENAME'],1);

//目前學期
$c_curr_seme=sprintf("%03d%d",curr_year(),curr_seme());
//目前選定學期
$the_seme=($_POST['the_seme']=="")?$c_curr_seme:$_POST['the_seme'];

//目前選定頁數
$the_page=($_POST['option2']>=1)?$_POST['option2']:1;


//取回變數
$M_SETUP=get_module_setup('class_report');
$PAGES=$M_SETUP['pages']; //每頁條列幾筆

//秀出 SFS3 標題
head();

//列出選單
echo $tool_bar;

//post 後的動作
//表單預設值
if ($_POST['act']=='') {
	$REP['seme_year_seme']=$c_curr_seme;
	$REP['open_input']=0;				//開放小老師登錄
	$REP['open_read']=0;				//開放瀏覽
	$REP['open_classmates']=0;	//開放瀏覽全班
	$REP['rep_sum']=1;					//自動求總分
	$REP['rep_avg']=1;					//自動求平均
	$REP['rep_rank']=0;					//自動算排名
}
//select 的班級變了
if ($_POST["change_class"]) {
	foreach ($_POST as $k=>$v) {
		$REP[$k]=$v;
	}
}
//新增一張成績單
if ($_POST['act']=='insert') {
	foreach ($_POST as $k=>$v) {
		${$k}=$v;
	} //end foreach
	$sql="insert into `class_report_setup` set seme_year_seme='$seme_year_seme',seme_class='$seme_class',title='$title',teacher_sn='{$_SESSION['session_tea_sn']}',student_sn='$student_sn',open_input='$open_input',open_read='$open_read',rep_classmates='$rep_classmates',rep_sum='$rep_sum',rep_avg='$rep_avg',rep_rank='$rep_rank',update_sn='{$_SESSION['session_tea_sn']}'";
	$res=$CONN->Execute($sql) or die("SQL錯誤:$sql");
	$_POST['act']="";
} // end if

//修改成績單設定
if ($_POST['act']=='edit') {
	$sql="select * from `class_report_setup` where sn='{$_POST['option1']}'";
	$res=$CONN->Execute($sql) or die("SQL錯誤:$sql");
	$REP=$res->FetchRow();
}

//解鎖成績單設定
if ($_POST['act']=='unlock') {
	$sql="update `class_report_setup` set locked='0' where sn='{$_POST['option1']}'";
	$res=$CONN->Execute($sql) or die("SQL錯誤:$sql");
  $_POST['act']='';
	$_POST['option1']='';
}

if ($_POST['act']=='update') {
	foreach ($_POST as $k=>$v) {
		${$k}=$v;
	} //end foreach
	$sql="update `class_report_setup` set seme_year_seme='$seme_year_seme',seme_class='$seme_class',title='$title',teacher_sn='{$_SESSION['session_tea_sn']}',student_sn='$student_sn',open_input='$open_input',open_read='$open_read',rep_classmates='$rep_classmates',rep_sum='$rep_sum',rep_avg='$rep_avg',rep_rank='$rep_rank',update_sn='{$_SESSION['session_tea_sn']}' where sn='{$_POST['option1']}'";
	$res=$CONN->Execute($sql) or die("SQL錯誤:$sql");
	$_POST['act']='';
	$_POST['option1']='';
} // end if

//刪除成績單
if ($_POST['act']=='DeleteOne') {
	//取得本份成績單的所有考試	
  $TESTS=get_report_test_all($_POST['option1']); 
	//刪除所有學生成績
	foreach ($TESTS as $test_setup) {
		$sql="delete from class_report_score where test_sn='{$test_setup['sn']}'";
		$res=$CONN->Execute($sql) or die("SQL錯誤:$sql");
	}
	//刪除成績單的考試設定
		$sql="delete from class_report_test where report_sn='{$_POST['option1']}'";
		$res=$CONN->Execute($sql) or die("SQL錯誤:$sql");
	//刪除成績單設定	
		$sql="delete from class_report_setup where sn='{$_POST['option1']}'";
		$res=$CONN->Execute($sql) or die("SQL錯誤:$sql");

	$_POST['act']="";
	$_POST['option1']="";
}



//檢驗身分, 並取出可讀取的成績單
switch ($_SESSION['session_who']) {
	//如果是老師, 取得所有學期
	case '教師':
		$select_seme = get_class_seme(); //學年度
		//取得目前學期的所有可讀取的成績單
		$select_report=get_report("list",$the_seme,"",$the_page);
	break;

	//如果是學生, 取得就學學期
	case '學生':
		echo "此為教師專用功能!";
		exit();
	break;
} // end switch


$REP['seme_year_seme']=$the_seme;

$act=($_POST['act']=='')?'insert':'update';

?>
<form method="post" name="myform" action="<?php echo $_SERVER['php_self'];?>">
	<input type="hidden" name="act" value="">
	<input type="hidden" name="change_class" value="">
	<input type="hidden" name="option1" value="<?php echo $_POST['option1'];?>">
	<input type="hidden" name="option2" value="<?php echo $_POST['option2'];?>">
	<select size="1" name="the_seme" onchange="document.myform.submit()">
		<?php
		foreach ($select_seme as $k=>$v) {
		?>
			<option value="<?php echo $k;?>"<?php if ($the_seme==$k) echo " selected";?>><?php echo $v;?></option>
		<?php
		}
		?>
	</select>
	<input type="button" value="顯示成績單表單" onclick="form_report.style.display='block'">
	
		<table border="0" bgcolor="#ffffff" style="border-collapse:collapse" bordercolor="#800000">
		<tr>
			<td style="color:#800000">
				<div id="form_report" style="padding: 0px;display:<?php echo ($_POST['act']=="edit" or $_POST['change_class'])?"block":"none"; ?>">
				<fieldset style="line-height: 150%; margin-top: 0; margin-bottom: 0">
				<legend><font size=2 color=#0000dd>請輸入成績單細目</font></legend>
				<?php form_report($REP);?>
					<input type="button" value="送出資料" onclick="check_save('<?php echo $act;?>')">
					<input type="button" value="隱藏表單" onclick="form_report.style.display='none'">
			</fieldset>
			</div>
			</td>
		</tr>
	</table>

	
	<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse;' bordercolor='#111111'>
		<tr bgcolor="#663300" style="color:#FFFFFF">
			<td align="center" width="30" style="font-size:9pt">操作</td>
			<td align="center" width="200">成績單名稱</td>
			<td align="center" width="80">教師</td>
			<td align="center" width="60">班級</td>
			<td align="center" width="60">成績數</td>
			<td align="center" width="80" style="font-size:9pt">科長/小老師</td>
			<td align="center" width="60" style="font-size:10pt">開放登錄</td>
			<td align="center" width="60" style="font-size:10pt">開放查詢</td>
			<td align="center" width="60" style="font-size:10pt">成績樣式</td>
			<td align="center" width="60" style="font-size:10pt">統計總分</td>
			<td align="center" width="60" style="font-size:10pt">計算平均</td>
			<td align="center" width="60" style="font-size:10pt">提供排名</td>
		</tr>
		<?php
		foreach ($select_report as $k=>$v) {
		?>
		<tr<?php if ($v['sn']==$_POST['option1']) echo " bgcolor='#FFFF00'";?>>
			<td align="center" style="color:#888888">
				<?php if ($v['locked']==0) { ?>
				<img src="images/edit.png" style="cursor:hand" title="編輯" onclick="document.myform.act.value='edit';document.myform.option1.value='<?php echo $v['sn'];?>';document.myform.submit();">
  			<img src="images/del.png"  style="cursor:hand" title="刪除" onclick="if (confirm('您確定要刪除嗎?\n成績單:<?php echo  '('.$v['seme_class_cname'].')'.$v['title'];?>')) { document.myform.act.value='DeleteOne'; document.myform.option1.value='<?php echo $v['sn'];?>'; document.myform.submit(); } ">
			<?php } else { 
					echo "<font size=\"2\"><i>鎖定</i></font>"; 
					if ($M_SETUP['unlock_self']) {
						?>
						 <img src="../score_manage/images/key.png" title="解鎖" style="cursor:hand" onclick="confirm_unlockk('unlock',<?php echo $v['sn'];?>)">
						<?php
					}
			}?>
			</td>
			<td><?php echo $v['title'];?></td>
			<td align="center"><?php echo get_teacher_name($v['teacher_sn']);?></td>
			<td align="center"><?php echo $v['seme_class_cname'];?></td>
			<td align="center"><?php echo $v['test_num'];?></td>
			<td align="center"><?php echo ($v['student_sn']>0)?student_sn_to_stud_name($v['student_sn']):"";?></td>
			<td align="center"><?php echo ($v['open_input'])?"是":"否";?></td>
			<td align="center"><?php echo ($v['open_read'])?"是":"否";?></td>
			<td align="center"><?php echo ($v['rep_classmates'])?"全班":"個人";?></td>
			<td align="center"><?php echo ($v['rep_sum'])?"是":"否";?></td>
			<td align="center"><?php echo ($v['rep_avg'])?"是":"否";?></td>
			<td align="center"><?php echo ($v['rep_rank'])?"是":"否";?></td>
		</tr>
		<?php
		}
		?>	
	</table>
	<table>
		<tr>
			<td>頁次 :<?php select_pages($the_page);?></td>
		</tr>
	</table>
	
</form>
<Script language="JavaScript">
 //檢測資料是否完整
 function check_save(ACT) {
 	var ok=1;
 	if (document.myform.title.value=='') {
 		ok=0;
 		alert('請輸入成績單名稱');
 		document.myform.title.focus();
 		return false;
 	}
 	if (document.myform.seme_class.value=='') {
 		ok=0;
 		alert('請選擇班級');
 		document.myform.rank.focus();
 		return false;
 	}
 	
 	if (ok==1) {
 		document.myform.act.value=ACT;
 		document.myform.submit();
 	}
 	
 }

//確認解鎖
 function confirm_unlockk(ACT,SN) {
  C=confirm("本成績單所結算的平均分數已被匯出至學期階段成績, \n 為了保存原始完整成績來源, 因而系統暫時鎖定, \n 您確定要解鎖? \n(如果您先前已匯至成績管理的[平常成績]做為一次平常成績,\n 解鎖若是為了要重匯, \n則請記得事先將已匯入的該次平常成績刪除!)");
  if (C) {
  	document.myform.act.value=ACT;
  	document.myform.option1.value=SN;
 		document.myform.submit(); 	
  } else {
   return false;
  }
  
 }

</Script>


