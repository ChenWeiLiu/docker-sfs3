<?php 
// $Id: ustep4.php 5310 2009-01-10 07:57:56Z hami $
	if ( !$conID_list = @mysql_connect ("$session_mysql_host","$session_mysql_user","$session_mysql_password")) {
		echo "未受認證進入!!";
		exit;
	}
	
?>
<a name="this_step4">
<table border="0" >
  <tr>
    <td width="100%">
<p><b>轉檔成功!!</b></p>
<p>sfs2.0 資料規格符合 "教育公布國小學籍規格 1.0 版 參考規範"</p>
<p>新的系統架構，將 所有上傳檔案統一置放在 <font color=red> <?php echo $UPLOAD_PATH ?></font>&nbsp; 
目錄中，方便程式撰寫及資料維護，
(參考 <?php echo $SFS_PATH ?>/include/config.php 的設定)<BR>你需手動搬移(複製) 
原有的上傳檔案目錄，包括校務公告欄(附件檔)、數位相本(圖檔)、學生作業(作業檔)、文件資料庫(文件檔)。</p>

<p>操作步驟：</p>
<p>更改上傳目錄權限：</p>
<p><span style="background-color: #CCCCFF"><pre>chmod 777 <?php echo $UPLOAD_PATH ?> </pre></span></p>
<p>複製檔案：</p>
<p>例： 校務佈告欄：</p>
<?php
	$new_path_html = $SFS_PATH_HTML;
	include "$session_sfs_path/include/config.php";	
	echo "<p><span style=\"background-color: #CCCCFF\"><pre>";
	echo "mkdir $UPLOAD_PATH"."board \n\n";
	echo "cp $path/school/board/updata/*  $UPLOAD_PATH"."board </pre></span></p>";
	echo "<p>(檔案實際目錄參考 $SFS_PATH/school/board/board_config.php 中設定 )</p>";
?>
<p>現在，你可以測試 sfs2.0  <?php echo "<a href=\"$new_path_html\">$new_path_html</a>" ?></p>

</td>
  </tr>
</table>
