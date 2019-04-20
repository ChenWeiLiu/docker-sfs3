<?php

// $Id: reward_one.php 6735 2012-04-06 08:14:54Z smallduh $

//取得設定檔
include_once "config.php";

sfs_check();


//秀出網頁
head("社團活動 - 列印社團名單");

$tool_bar=&make_menu($school_menu_p);

//列出選單
echo $tool_bar;

if ($_SESSION['session_who'] != "教師") {
	echo "很抱歉！本功能模組為教師專用！";
	exit();
}

//取得目前學年度
$curr_year=curr_year();
$curr_seme=curr_seme();

//目前選定學期
$c_curr_seme=sprintf('%03d%1d',$curr_year,$curr_seme);

//目前選定年級，100指未指定
//$c_curr_class=($_POST['c_curr_class']!="")?$_POST['c_curr_class']:"100";
 $c_curr_class=$_POST['c_curr_class'];

?>
<form name="myform" method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
	<input type="hidden" name="mode" value="">
	<input type="hidden" name="club_sn" value="">
<table border="0" width="800">
	<tr>
	  <!--左列視窗, 學期社團列表 -->
	  <td width="160" valign="top" style="color:#FF00FF;font-size:10pt">
	  	<select name='c_curr_class' onchange="document.myform.submit()">
	  		<option value="" style="color:#FF00FF">請選擇社團年級別</option>
	  	<?php
			    $class_year_array=get_class_year_array(sprintf('%d',substr($c_curr_seme,0,3)),sprintf('%d',substr($c_curr_seme,-1)));
                foreach ($class_year_array as $K=>$class_year_name) {
                	?>
                	<option value="<?php echo $K;?>" style="color:#0000FF;font-size:10pt" <?php if ($c_curr_class==$K) echo "selected";?>><?php echo $school_kind_name[$K];?>級(<?php echo get_club_num($c_curr_seme,$K);?>)</option>
                	<?php
                }	
			?>
									<option value="100" style="color:#0000FF;font-size:10pt" <?php if ($c_curr_class=='100') echo "selected";?>>跨年級(<?php echo get_club_num($c_curr_seme,100);?>)</option>
		</select>
			<?php
			if ($c_curr_class) {
	  	//傳入參數 1001 , 1002 等, 年度學期
	  	list_club_select($c_curr_seme,$c_curr_class);
	  	}
	  	?>
	  </td>
	  <!--左列視窗結尾 -->
	  <!--右列視窗, 主畫面 -->
		<td width="640" valign="top">			
		<?php
	  //顯示某社團名單 ================================================================
	  if ($_POST['mode']=="list" and $_POST['club_sn']) {
	   echo "<input type='button' value='友善列印' onclick='print_name()'><br>";	
		 print_name_list($c_curr_seme,$_POST['club_sn']);
	  	
	  }
		?>
	  </td>
	  <!--右列視窗結尾 -->
	</tr>
</table>
</form>
