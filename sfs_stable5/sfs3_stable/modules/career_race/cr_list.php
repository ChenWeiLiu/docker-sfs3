<?php
//取得設定檔
include_once "config.php";

sfs_check();

//取得系統中所有學期資料, 每一學年有二個學期
$class_seme_p = get_class_seme(); 

//目前選定學期 , 若有選定則以選定的學期作為比對學生班級座號的依據, 否則以最新學期的個資為準
$c_curr_seme=$_POST['c_curr_seme'];

 //計算該學期的日期區間
 $year=sprintf("%d",substr($c_curr_seme,0,3));
 $seme=substr($c_curr_seme,-1);
 //起始日
 $sql="select day from school_day where year='$year' and seme='$seme' and day_kind='start'";
 /* 原始 php 的 MySQL 函式
 $res=mysql_query($sql);
 list($st_date)=mysql_fetch_row($res);
 */
 /* ADODB 寫法*/
 $res=$CONN->Execute($sql) or die("SQL錯誤:$sql");
 $st_date=$res->fields[0];
 
 //結束日
 $sql="select day from school_day where year='$year' and seme='$seme' and day_kind='end'";
 /* 原始 php 的 MySQL 函式
 $res=mysql_query($sql);
 list($end_date)=mysql_fetch_row($res);
 */
  /* ADODB 寫法*/
 $res=$CONN->Execute($sql) or die("SQL錯誤:$sql");
 $end_date=$res->fields[0];



//製作選單 ( $school_menu_p陣列設定於 module-cfg.php )
$tool_bar=&make_menu($school_menu_p);

//讀取目前操作的老師有沒有管理權
$module_manager=checkid($_SERVER['SCRIPT_FILENAME'],1);

/** submit 後的動作 **************************************************/
//刪除單筆
if ($_POST['act']=='DeleteOne') {
	$sn=$_POST['option1'];
	$query="delete from career_race where sn='$sn'";
	//mysql_query($query);
	 $res=$CONN->Execute($query) or die("SQL錯誤:$query");
	$_POST['act']='limit_date';
}

/**************** 開始秀出網頁 ******************/
//秀出 SFS3 標題
head();

//列出選單
echo $tool_bar;

?>
<form name="myform" method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
	<input type="hidden" name="act" value="">
	<input type="hidden" name="option1" value="<?php echo $_POST['option1'];?>">
	
<table border="0" width="100%" cellspacing="1" cellpadding="2" bgcolor="#CCCCCC">
<tr>
  <td  width="100%" valign="top" bgcolor="#ffffff">
<!--依學期 -->
   <table border="0" width="100%">
     <tr>
      <td style="color:#800000">
      	<u><b>※依證書日期所屬學期</b></u>
				<select name="c_curr_seme" onchange="this.form.act.value='limit_date';this.form.submit()">
					<option value="">---</option>
					<?php
					foreach ($class_seme_p as $tid=>$tname) {
    			?>
    				<option style="color:#FF00FF" value="<?php echo $tid;?>" <?php if ($c_curr_seme==$tid) echo "selected";?>><?php echo $tname;?></option>
   				<?php
    			} // end while
    			?>
    		</select>
    		<?php
    		 if ($_POST['act']=='limit_date') {
    		?>
      	<font size=2>日期：<?php echo $st_date;?>~<?php echo $end_date;?></font>
      	<?php } ?>
      	</td>
     </tr>
   </table>
</td>
<!--依班級座號 -->
<td  width="100%" valign="top" bgcolor="#ffffff">
	
</td>   
</table>
<!-- 開始秀出資料 -->
<?php
if ($_POST['act']=='limit_date') {
 if ($c_curr_seme!="") $race_record=get_race_record($c_curr_seme,"","");
 list_race_record($race_record,0,1,'cr_input.php');
}
?>



</form>


