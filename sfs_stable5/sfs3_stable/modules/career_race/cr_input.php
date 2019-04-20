<?php
//取得設定檔
include_once "config.php";


sfs_check();

//製作選單 ( $school_menu_p陣列設定於 module-cfg.php )
$tool_bar=&make_menu($school_menu_p);

//讀取目前操作的老師有沒有管理權
$module_manager=checkid($_SERVER['SCRIPT_FILENAME'],1);

$R_select=explode(',',$rank_select);
$N_select=explode(',',$nature_select);

//取得目前學年度
$curr_year=curr_year();
$curr_seme=curr_seme();

//目前選定學期 , 若有選定則以選定的學期作為比對學生班級座號的依據, 否則以最新學期的個資為準
$c_curr_seme=sprintf('%03d%1d',$curr_year,$curr_seme);

//取得目前所有班級
$class_array=class_base();

//預設並未選定學生
$start=0;

/** submit 後的動作 **************************************************/
//刪除單筆
if ($_POST['act']=='DeleteOne') {
	$sn=$_POST['option1'];
	$query="delete from career_race where sn='$sn'";
	mysql_query($query);
	$_POST['act']='';
}

//儲存一筆
if ($_POST['act']=='save') {
	
 	$student_sn=$_POST['student_sn'];
	$level=$_POST['level'];
	$squad=$_POST['squad'];
	$name=$_POST['r_name'];
	$rank=$_POST['rank'];
  $certificate_date=$_POST['certificate_date'];
	$sponsor=$_POST['sponsor'];
	$memo=$_POST['memo'];
	$word=strip_tags(trim($_POST['word']));
	$weight=$_POST['weight'];
	$weight_tech=$_POST['weight_tech'];
	$year=$_POST['year'];
	$nature=$_POST['nature'];
	
	$query="insert into career_race set student_sn='$student_sn',level='$level',squad='$squad',name='$name',
	rank='$rank',certificate_date='$certificate_date',sponsor='$sponsor',memo='$memo',
	word='{$word}', weight='{$weight}', weight_tech='{$weight_tech}',year='$year',nature='$nature' ,	update_sn='".$_SESSION['session_tea_sn']."'";
   		if (!mysql_query($query)) {
   		 $MSG="儲存資料失敗!";
   		  echo $query;die($MSG);
			} 
	$_POST['act']='';
}

//指定 edit 的學生
if ($_POST['act']=='update') {
  $student_sn=$_POST['student_sn'];
	$level=$_POST['level'];
	$squad=$_POST['squad'];
	$name=$_POST['r_name'];
	$rank=$_POST['rank'];
  $certificate_date=$_POST['certificate_date'];
	$sponsor=$_POST['sponsor'];
	$memo=$_POST['memo'];
	$word=strip_tags(trim($_POST['word']));
	$weight=$_POST['weight'];
	$weight_tech=$_POST['weight_tech'];
	$year=$_POST['year'];
	$nature=$_POST['nature'];
	
	$query="update career_race set level='$level',squad='$squad',name='$name',rank='$rank', certificate_date='$certificate_date',sponsor='$sponsor',memo='$memo', word='$word',weight='$weight',weight_tech='$weight_tech',year='$year',nature='$nature' where sn='".$_POST['option1']."' and student_sn='$student_sn'";
   		if (!mysql_query($query)) {
   		 $MSG="儲存資料失敗!";
   		 echo$query;die($MSG);
			}  
  $_POST['act']='';
}

//若有指定編輯
if ($_POST['act']=='edit') {
	$query="select a.student_sn,a.curr_class_num from stud_base a,career_race b where a.student_sn=b.student_sn and b.sn='".$_POST['option1']."'";
  $res=mysql_query($query);
  $row=mysql_fetch_array($res,1);
  $_POST['to_class']=substr($row['curr_class_num'],0,3);
  $_POST['to_student']=$row['student_sn'];
  //echo $query;
  
}


//取得班級所有學生 array
if (isset($_POST['to_class'])) {
	$query="select a.student_sn,a.stud_id,a.seme_class,a.seme_num,b.stud_name from stud_seme a,stud_base b where a.student_sn=b.student_sn and a.seme_year_seme='$c_curr_seme' and a.seme_class='".$_POST['to_class']."' order by a.seme_num";
	$res=mysql_query($query);
	$student_array=array();
	while ($row=mysql_fetch_array($res,1)) {
		$student_array[$row['student_sn']]=$row['seme_num']." ".$row['stud_name'];
	}
}

//利用select指定的學生 
if (isset($_POST['to_student'])) {
	$student_sn=$_POST['to_student'];
}

//

/**************** 開始秀出網頁 ******************/
//秀出 SFS3 標題
head();

//列出選單
echo $tool_bar;

//print_r($class_array);

?>
<form name="myform" method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
	<input type="hidden" name="act" value="">
	<input type="hidden" name="option1" value="<?php echo $_POST['option1'];?>">

<table border="0" width="100%" cellspacing="1" cellpadding="2" bgcolor="#CCCCCC">
<tr>
  <td  width="100%" valign="top" bgcolor="#ffffff">
	<!-- 學生資料及已登錄的記錄  -->
	<table border="0" >
		<tr>
			<td colspan="2" style="color:#800000">※請選定學生</td>
		</tr>
		<tr>
			<td>班級</td>
			<td>
				<select size="1" name="to_class" onchange="document.myform.submit()">
					<option value="">---</option>
					<?php
					 foreach ($class_array as $k=>$v) {
					 ?>
					 <option value="<?php echo $k;?>" <?php if ($k==$_POST['to_class']) echo "selected";?>><?php echo $v;?></option>
					 <?php
					 }
					?>
				</select>
			</td>
			<td>姓名</td>
			<td>
				<select size="1" name="to_student"  onchange="document.myform.submit()">
					<option value="">---</option>
					<?php
					 foreach ($student_array as $k=>$v) {
					 ?>
					 <option value="<?php echo $k;?>" <?php if ($k==$student_sn) { $start=1; echo "selected"; } ?>><?php echo $v;?></option>
					 <?php
					 }
					?>
				</select>
				<?php
				if ($start==1) {
				?>
					<input type="button" value="顯示輸入表單" onclick="race_form.style.display='block';">
				<?php
				}
				?>
			</td>
		</tr>
	</table>
	<?php
	if ($start==1) {
		//讀取該生已登錄的競賽記錄
		$race_record_all=get_race_record("","",$student_sn);
		?>
	<!-- 登錄表單  -->
	<table border="0" bgcolor="#ffffff" style="border-collapse:collapse" bordercolor="#800000">
		<tr>
			<td style="color:#800000">
				<div id="race_form" style="padding: 0px;<?php if ($_POST['act']=="") echo ";display:none";?>">
				<fieldset style="line-height: 150%; margin-top: 0; margin-bottom: 0">
				<legend><font size=2 color=#0000dd>請輸入競賽細目</font></legend>
				<?php
	 				if ($_POST['act']=='edit') {
	 					//若為點選編輯, 則將資料放入陣列
	   				foreach ($race_record_all[$_POST['option1']] as $k=>$v){
	     				$race_record[$k]=$v;
	   				} // end foreach
	 					 $act='update';
					 } else {
					 	//預設值
					 	$race_record['year']=date("Y")-1911;
						$race_record['level']=5;	
						$race_record['squad']=1;
						$race_record['weight']=1;
						$race_record['weight_tech']=1;
						$race_record['certificate_date']=date("Y-m-d");
						$act='save';
					 }
						form_race_record($race_record);
					?>
					<input type="hidden" name="student_sn" value="<?php echo $student_sn;?>">
					<input type="button" value="儲存一筆記錄" onclick="check_save('<?php echo $act;?>')">
					<?php
					if (substr($sch_id,0,2)=='13') {
					 echo "<br><font size=\"2\">※說明：屏東區的「競賽類別」及「得獎名次」無法經由模組變數自行設定。</font>";
					}
					?>
			</fieldset>
			</div>
			</td>
		</tr>
	</table>
		<font color="#800000">※該生的所有參與競賽記錄</font>
		<?php
		
		list_race_record($race_record_all,0,1);
		
	} // end if start
	?>

	</td>	
</tr>
</table>
</form>
<?php
//若為屏東
if (substr($sch_id,0,2)=='13') {
?>
<script type='text/javascript' src='select_race_option.js'></script>
<?php
  if ($start==1 and $_POST['act']=='edit') echo "<Script>SelectR_name(0);</Script>";
 }
?>
<Script language="JavaScript">
 //檢測資料是否完整
 function check_save(ACT) {
 	var ok=1;
 	if (document.myform.r_name.value=='') {
 		ok=0;
 		alert('請輸入競賽名稱');
 		document.myform.r_name.focus();
 		return false;
 	}
 	if (document.myform.rank.value=='') {
 		ok=0;
 		alert('請輸入得獎名次, 如「第1名」、「優等」....等。');
 		document.myform.rank.focus();
 		return false;
 	}
 	if (document.myform.certificate_date.value=='') {
 		ok=0;
 		alert('請輸入證書日期');
 		document.myform.certificate_date.focus();
 		return false;
 	}
 	if (document.myform.sponsor.value=='') {
 		ok=0;
 		alert('請輸入舉辦單位');
 		document.myform.sponsor.focus();
 		return false;
 	}
 	
 	if (ok==1) {
 		document.myform.act.value=ACT;
 		document.myform.submit();
 	}
 	
 }

</Script>
