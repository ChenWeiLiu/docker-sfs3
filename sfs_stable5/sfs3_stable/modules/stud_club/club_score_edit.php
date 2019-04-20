<?php

// $Id: reward_one.php 6735 2012-04-06 08:14:54Z smallduh $

//取得設定檔
include_once "config.php";

sfs_check();

if ($_SESSION['session_who'] != "教師") {
	echo "很抱歉！本功能模組為教師專用！";
	exit();
}

//取得目前學年度
$curr_year=curr_year();
$curr_seme=curr_seme();

//目前選定學期
$c_curr_seme=($_POST['c_curr_seme']=='')?sprintf('%03d%1d',$curr_year,$curr_seme):$_POST['c_curr_seme'];

//目前選定的班級
$c_curr_class=$_POST['c_curr_class'];

//取得選定年度的所有班級陣列
$select_class=class_base($c_curr_seme);

//取得學年學期陣列
$select_seme=get_class_seme();
$select_seme=array_reverse($select_seme,1); //將陣列反轉, 使較近年度排前面

//POST 資料處理 刪除
if ($_POST['act']=='del') {
 //開始刪除
 $i=0;
 foreach ($_POST['sn'] as $sn) {
  $sql="delete from association where sn='$sn'";
  $res=$CONN->Execute($sql) or die ('SQL 錯誤! query='.$sql);
  $i++;
 } // end foreach
 $INFO="已成功刪除".$i."筆資料!";
} // end if del

//POST 資料處理 刪除
if ($_POST['act']=='save') {
 //開始刪除
 foreach ($_POST['association_name'] as $sn=>$v) {
 	if ($v) {
   	$association_name=$v;
   	$score=$_POST['score'][$sn];
   	$stud_post=$_POST['stud_post'][$sn];
   	$description=$_POST['description'][$sn];
   	
   	$sql="update association set association_name='$association_name',score='$score',stud_post='$stud_post',description='$description' where sn='$sn'";
  	$res=$CONN->Execute($sql) or die ('SQL 錯誤! query='.$sql);
 	
 	} 	
 } // end foreach
 $INFO="於".date("Y-m-d H:i:s")."進行儲存!";
} // end if save



//取得學期社團設定
//$SETUP=get_club_setup($c_curr_seme);

//秀出網頁
head("社團活動 - 成績資料庫異動");
//列出選單
$tool_bar=&make_menu($school_menu_p);
echo $tool_bar;

//檢驗是否有管理權
$module_manager=checkid($_SERVER['SCRIPT_FILENAME'],1);
if (!$module_manager) {
  echo "抱歉! 您沒有管理權!";
  exit();
}
?>
<form method="post" name="myform" action="<?php echo $_SERVER['PHP_SELF'];?>">
	<input type="hidden" name="act">
	<!--學年學期選單 -->
	<select name="c_curr_seme" size="1" onchange="document.myform.c_curr_class.value='';document.myform.submit()">
		<?php 
		foreach ($select_seme as $k=>$v) {
			?>
			<option value="<?php echo $k;?>"<?php if ($k==$c_curr_seme) echo " selected";?>><?php echo $v;?></option>
			<?php
		}
		?>	
	</select>
	<!--班級選單 -->
	<select name="c_curr_class" size="1" onchange="document.myform.submit()">
		<option value="" style="color:#FF00FF">請選擇班級</option>
		<?php 
		foreach ($select_class as $k=>$v) {
			?>
			<option value="<?php echo $k;?>"<?php if ($k==$c_curr_class) echo " selected";?>><?php echo $v;?></option>
			<?php
		}
		?>	
	</select>
	<input type="radio" name="list_mode" value="0" <?php if ($_POST['list_mode']==0) echo " checked";?> onclick="document.myform.submit()">僅呈現手動匯入的資料
	<input type="radio" name="list_mode" value="1" <?php if ($_POST['list_mode']==1) echo " checked";?> onclick="document.myform.submit()">呈現所有資料
	<!--列出班級社團列表 -->
	<?php
	 if ($_POST['c_curr_class']) {
	 	?>
	 	<table border="2" style="border-collapse:collapse" bordercolor="#111111" cellpadding="3">
	 		<tr bgcolor="#CCFFFF">
	 			<td width="10" align="center"><input type="checkbox" value="1" name="check_all" onclick="check_copy('check_all','sn')"></td>
	 			<td width="40" align="center">座號</td>
	 			<td width="100" align="center">姓名</td>
	 			<td align="center">參加社團</td>
	 			<td width="40" align="center">成績</td>
	 			<td align="center">擔任幹部</td>
	 			<td align="center">指導老師評語</td>
	 			<td align="center">更新時間</td>
	 			<td align="center">說明</td>
	 		</tr>
	 	<?php
	 	//取得該班學生所有資料
	 	$sql="select a.*,b.seme_num,c.stud_name from association a,stud_seme b,stud_base c where a.seme_year_seme=b.seme_year_seme and a.student_sn=b.student_sn and b.student_sn=c.student_sn and b.seme_class='$c_curr_class' and a.seme_year_seme='$c_curr_seme' order by b.seme_num";
	 	$res=$CONN->Execute($sql) or die('SQL Error! query='.$sql);
	 	while ($row=$res->FetchRow()) {
	 		$row['club_sn']=trim($row['club_sn']);
	 		if ($row['club_sn']) {
	 			if ($_POST['list_mode']) {
			?>
			<tr style="color:#888888">
	 			<td align="center">●</td>
	 			<td align="center"><?php echo $row['seme_num'];?></td>
	 			<td align="center"><?php echo $row['stud_name'];?></td>
	 			<td><?php echo $row['association_name'];?></td>
	 			<td align="center"><?php echo $row['score'];?></td>
	 			<td align="center"><?php echo $row['stud_post'];?></td>
	 			<td><?php echo $row['description'];?></td>
	 			<td style="font-size:10pt"><?php echo $row['update_time'];?></td>
	 			<td style="font-size:10pt;color:#FFAAAA"><i>校內社團</i></td>
			</tr>
	 		<?php	
	 			}
	 		} else {
			?>
			<tr bgcolor="#FFFFCC">
	 			<td align="center"><input type="checkbox" name="sn[<?php echo $row['sn'];?>]" value="<?php echo $row['sn'];?>"></td>
	 			<td align="center" style="color:#0000FF"><?php echo $row['seme_num'];?></td>
	 			<td align="center" style="color:#0000FF"><b><?php echo $row['stud_name'];?></b></td>
	 			<td><input type="text" name="association_name[<?php echo $row['sn'];?>]" value="<?php echo $row['association_name'];?>"></td>
	 			<td><input type="text" name="score[<?php echo $row['sn'];?>]" value="<?php echo $row['score'];?>" size="5"></td>
	 			<td><input type="text" name="stud_post[<?php echo $row['sn'];?>]" value="<?php echo $row['stud_post'];?>" size="20"></td>
	 			<td><input type="text" name="description[<?php echo $row['sn'];?>]" value="<?php echo $row['description'];?>"></td>
	 			<td align="center" style="color:#0000FF"><?php echo $row['update_time'];?></td>
	 			<td style="font-size:10pt;color:#6666FF"><i>匯入資料</i></td>
			</tr>
	 		<?php	
	 		
	 		} // end if 	
	 	
	 	} // end while
	 	
		?>	 	
	 	</table>
	 	<input type="button" value="刪除勾選的資料" onclick="if (confirm('您確定要:\n刪除勾選的資料?')) { document.myform.act.value='del';document.myform.submit(); }">
	 	<input type="button" value="儲存資料" onclick="document.myform.act.value='save';document.myform.submit()">
	 	<font color="red" size=2><i><?php echo $INFO;?></i></font>
	 
	 <?php
	 }// end if ($_POST['c_curr_class']) 
	 
	?>
	

</form>
	 <br>
	 <font color="#800000">
	  ※說明：<br>
	  1.本程式功能主要用於檢視歷年各班學生的社團活動資料，並針對資料直接手動修改及刪除。<br>
	  2.顯示的內容僅止於學生在學期間的資料，若為外校轉學生，其非在學期間的外校資料無法呈現，請利用「轉學生補登他校資料」功能編輯。
	 </font> 
	  

