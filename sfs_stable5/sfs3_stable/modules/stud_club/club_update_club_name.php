<?php

// $Id: reward_one.php 6735 2012-04-06 08:14:54Z smallduh $

//取得設定檔
include_once "config.php";

sfs_check();


//秀出網頁
head("社團活動 - 修正學生社團成績單中的社團名稱");

$tool_bar=&make_menu($school_menu_p);

//列出選單
echo $tool_bar;

//取得目前學年度
$curr_year=curr_year();
$curr_seme=curr_seme();

//取得所有學期資料, 每年有兩個學期
$class_seme_p = get_class_seme(); //學年度	
$class_seme_p=array_reverse($class_seme_p,1);

//目前選定學期
$c_curr_seme=($_POST['c_curr_seme']!="")?$_POST['c_curr_seme']:sprintf('%03d%1d',$curr_year,$curr_seme);


$module_manager=checkid($_SERVER['SCRIPT_FILENAME'],1);
if ($module_manager!=1) {
 echo "抱歉 , 您沒有無管理權限!";
 exit();
}
//POST後之動作 ================================================================
if ($_POST['mode']=="start") {
	    $N=0;
      $query="select * from association where seme_year_seme='$c_curr_seme' and club_sn!=''";
      $res=mysql_query($query);
      while ($row=mysql_fetch_array($res)) {
      	$query="select club_name from stud_club_base where club_sn='".$row['club_sn']."'";
				$result=mysql_query($query);
				list($club_name)=mysql_fetch_row($result);
				$query="update association set association_name='".SafeAddSlashes($club_name)."' where sn='".$row['sn']."'";
				if (mysql_query($query)) {
					$N++;
				} else {
				  echo "錯誤發生了！query=$query";
				  exit();
				}	      	
      	
      } // end while
      $INFO="總共重新載入(登錄)了 $N 位學生的社團成績中的社團名稱。";
}  


     $query="select * from association where seme_year_seme='$c_curr_seme' and club_sn!=''";
     $res=mysql_query($query);
     $N=mysql_num_rows($res);
?>
<form name="myform" method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
  <input type="hidden" name="mode" value="">
 <table border="0" width="100%">
 	<tr>
 	 <td>
 	  	<select name="c_curr_seme" onchange="this.form.submit()">
			<?php
			while (list($tid,$tname)=each($class_seme_p)){
    	?>
    		<option value="<?php echo $tid;?>" <?php if ($c_curr_seme==$tid) echo "selected";?>><?php echo $tname;?></option>
   		<?php
    	} // end while
    	?>
    </select> 
 	 </td>
 	</tr>
  <tr>
    <td>
     本學期為 <?php echo substr($c_curr_seme,0,3);?>學年度第 <?php echo substr($c_curr_seme,3,1);?> 學期<br>
     社團成績資料表中, 共含有 <?php echo $N;?> 位學生資料, 其成績記錄是經由 SFS3 社團活動模組所建立.<br>
     您要重新載入(更正)學生參加的社團名稱嗎? <input type="button" value="是, 請重新載入" onclick="document.myform.mode.value='start';document.myform.submit();"><br>
     <br>
     <font color=blue>註：如果您曾經更動過社團名稱，或發現成績單中的社團名稱有亂碼，必須執行本程式進行更正。</font>
     <br>
    </td>
  </tr>
  <tr>
    <td style="color:#FF0000">
     <?php echo $INFO;?>
    </td>
  </tr>
 </table>
</form>     
