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
$c_curr_seme=sprintf('%03d%1d',$curr_year,$curr_seme);

//取得學期社團設定
$SETUP=get_club_setup($c_curr_seme);

//目前選定年級，100指未指定
$c_curr_class=$_POST['c_curr_class'];

//取得任教班級代號
$class_num = get_teach_class();

if ($_POST['mode']=="") {
//秀出網頁
head("社團活動 - 列印班級名單");
//列出選單
$tool_bar=&make_menu($school_menu_p);
echo $tool_bar;

}

//檢驗是否有管理權
$module_manager=checkid($_SERVER['SCRIPT_FILENAME'],1);

//若具導師身分, 直接將班級資料帶入 如:101_1_07_02
if ($class_num and $_POST['c_curr_class']=="") $_POST['c_curr_class']=sprintf("%03d_%1d_%02d_%02d",$curr_year,$curr_seme,substr($class_num,0,1),substr($class_num,1,2));
?>
<form name="myform" method="post" action="<?php echo $_SERVER['PHP_SELF'];?>" target="">
	<input type="hidden" name="mode" value="">
	<input type="hidden" name="club_sn" value="">

<?php
//若有管理權, 可檢查每一個班
if ($module_manager==1 and $_POST['mode']=="") {
?>
<table border="0" width="800">
	<tr>
	  <!--左列視窗, 學期社團列表 -->
	  <td width="160" valign="top" style="color:#FF00FF;font-size:10pt">
	  	<?php
    $s_y = substr($c_curr_seme,0,3);
    $s_s = substr($c_curr_seme,-1);
    
    //做出年級與班級選單
     $tmp=&get_class_select($s_y,$s_s,"","c_curr_class","document.myform.target=\"\";this.form.mode.value=\"\";this.form.submit",$c_curr_class); 
	//$year_seme=sprintf('%03d%d',$s_y,$s_s);
	//$class_array=class_base($c_curr_seme);
	//print_r($class_array);
	 
	 echo $tmp;
	 
	  	?>
	  </td>
	</tr>
</table>
<?php
}

	  //顯示某班級名單 ================================================================
	  
	  if ($_POST['c_curr_class']!="") {
	  	
		 print_class_student($c_curr_seme,$_POST['c_curr_class'],$SETUP['show_score'],$SETUP['show_feedback']);
	  	
	  }
	  

if ($class_num==0 and $module_manager!=1) {
	
echo "抱歉, 您不具導師身分!";
exit();
	 
}// end if class_num
		?>
</form>
	  
