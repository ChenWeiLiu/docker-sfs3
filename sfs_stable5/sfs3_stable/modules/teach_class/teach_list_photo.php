<?php

// $Id: teach_list.php 7454 2013-08-30 01:30:19Z hami $

// 載入設定檔
include "teach_config.php";

// 認證檢查
sfs_check();

//印出檔頭
head("教師基本資料");

//職稱類別
$POST_KIND = post_kind();

//印出選單
$tool_bar=&make_menu($teach_menu_p);
//列出選單
echo $tool_bar;

//列出教職員id
// ====================================================================
?>
 <table border="0" cellspacing="0" width="100%" bordercolor="#FFFFFF" style="border-collapse:collapse">
<?php
//依職別取得資料
for ($kind=1;$kind<=count($POST_KIND);$kind++) {
 $i=0; //紀錄本類別人數
 $query="select a.teacher_sn,c.teach_id,c.name from teacher_post a,teacher_title b,teacher_base c where a.post_kind=".$kind." and a.teach_title_id=b.teach_title_id and a.teacher_sn=c.teacher_sn and c.teach_condition=0 order by b.room_id,b.rank";
 if ($kind==6) {
 	$query="select a.teacher_sn,c.teach_id,c.name from teacher_post a,teacher_title b,teacher_base c where a.post_kind=".$kind." and a.teach_title_id=b.teach_title_id and a.teacher_sn=c.teacher_sn and c.teach_condition=0 order by a.class_num";
 }
 //echo $query;
 $result=$CONN->Execute($query);
 if ($result->RecordCount()>0) {
 	?>
  <tr><td colspan="5" style="color:#800000"><b>職別：  <?php echo $POST_KIND[$kind]; ?> </b></td></tr>
 	<tr>
  		<td>
  			<table border="0">
 	<?php
  while ($row=$result->fetchRow()) {
  	?>
  	
  		
  		<?php
  			$i++;  if ($i%5==1) echo "<tr>";
				$teacher_sn=$row['teacher_sn'];
       ?>
        
        <td style="font-size:10pt" align="center">
        	<table border="1"  style="border-collapse:collapse">
        		<tr>
        			<td align="center" width="120" height="200">
        				<?php
        				 //印出照片    	
    						if (file_exists($UPLOAD_PATH."/$img_path/".$teacher_sn)&& $teacher_sn<>'') {
    							echo "<img src=\"".$UPLOAD_URL."$img_path/$teacher_sn\" width=\"$img_width\"><br>";
								} else {
									echo "<font size=2>沒有照片</font><br>";
								}
        				?>
        			</td>
        		</tr>
        		<tr>
        			<td align="center"><font color="blue"><b>
        				<?php
  				        if ($kind==6 and $row['class_num']>0) echo $row['class_num']-600;
        					echo $row['name'];
        				?>
        				</b></font>
        			</td>
        		</tr>
        	</table>
         </td>
        	<?php
      		if ($i%5==0) echo "</tr>";
 	}// end while
	?> 
		</table>
  	</td>
  </tr>
  <?php
 	
 } // end if $result->RecordCount() 
 
} // end for

?>  	

</table>

<?php 
//印出尾頭
foot();
?> 
