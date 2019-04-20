<?php
// $Id: score_sort.php 2015-10-17 22:12:01Z qfon $

include "config.php";
sfs_check();

$sel_year=$_REQUEST['sel_year'];
$sel_seme=$_REQUEST['sel_seme'];
$class_id=$_REQUEST['class_id'];
$ss_id=$_POST['ss_id'];
$test_sort=$_REQUEST['test_sort'];
$test_kind=$_REQUEST['test_kind'];
$rate=$_REQUEST['rate'];
$student_sn=$_REQUEST['student_sn'];
$c_name=$_REQUEST['c_name'];
$subject1=$_REQUEST['subject1'];

//秀出網頁
if (empty($friendly_print) && empty($save_csv) && empty($excel)) head("補救教學名單");
//列出橫向的連結選單模組
if (empty($friendly_print) && empty($save_csv) && empty($excel)) print_menu($menu_p);
if (empty($friendly_print) && empty($save_csv) && empty($excel)) echo "<table border=0 cellspacing=0 cellpadding=2 width=100% bgcolor=#cccccc><tr><td>";

?>
<table>


<tr><td>

執行需要花費一些時間，是否確定要執行?<p>
<form action="getchart.php" method="post" target="my_iframe">
  <input type="hidden" name="student_sn" value="<?php echo $student_sn;?>" />
  <input type="hidden" name="sel_year" value="<?php echo $sel_year;?>" />
  <input type="hidden" name="sel_seme" value="<?php echo $sel_seme;?>" />
  <input type="hidden" name="class_id" value="<?php echo $class_id;?>" />
  <input type="hidden" name="ss_id" value="<?php echo $ss_id;?>" />
  <input type="hidden" name="test_sort" value="<?php echo $test_sort;?>" />
  <input type="hidden" name="test_kind" value="<?php echo $test_kind;?>" />
  <input type="hidden" name="rate" value="<?php echo $rate;?>" />
  <?php
  
   if (isset($_POST['subject1'])) 
   {
         $arrb=$_POST['subject1'];
  		  for($i=0;$i<count($arrb);$i++)
		  {
  
  ?>
  
  <input type="hidden" name="subject1[]" value="<?php echo $arrb[$i];?>" />
  
  
  <?php
           }
  }
  ?>
  <input type="submit" name="submit" value="確定" >
 
 
</form>
</td></tr>

<tr><td>
<IFRAME src="getchart.php" name="my_iframe" width="900" height="800" marginwidth=0 marginheight=0 hspace=0 vspace=0 frameborder=0 scrolling=true></IFRAME>
</td></tr>

</table>


<?

if (empty($friendly_print) && empty($save_csv) && empty($excel)) echo "</td></tr></table></form></tr></table>";

//程式檔尾
if (empty($friendly_print) && empty($save_csv) && empty($excel)) foot();


?>