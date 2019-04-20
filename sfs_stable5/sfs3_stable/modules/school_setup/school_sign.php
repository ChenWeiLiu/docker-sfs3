<?php

// $Id: school_sign.php 5310 2009-01-10 07:57:56Z hami $

// 載入設定檔
include "school_base_config.php";



// 認證檢查
sfs_check();

if ($_POST[do_key]=='上傳圖檔') {
	if (!check_is_php_file($_FILES['sign_file']['name'])) {
		$alias = "title_".$_POST[teach_title_id];
		if (copy($_FILES['sign_file']['tmp_name'],$filePath.$alias)){
			echo "<html><body>
			<script LANGUAGE=\"JavaScript\">\n
			window.opener.history.go(0);\n
       			window.close();
			</script>
			</body>
			</html>";
			exit;
		}
	}
}


$query = "select teach_title_id ,title_name  from teacher_title where teach_title_id='$_GET[teach_title_id]'";

$res = $CONN->Execute($query) or trigger_error("SQL錯誤",E_USER_ERROR);
$teach_title_id = $res->fields[0];
$title_name = $res->fields[1];

?>
<html>
<meta http-equiv="Content-Type" content="text/html; Charset=Big5">
<body>
<?php
	if (is_file($filePath."/title_".$teach_title_id))
		echo "<img src=\"$UPLOAD_URL"."school/title_img/title_"."$teach_title_id\">";

?>
<form method="post" action="<?php echo $_SERVER[PHP_SELF] ?>" name="myform" encType="multipart/form-data" ?>
<table width="100%" cellpadding=4 bgcolor="#c9ded4">
  <tr bgcolor="#f7ebaf">
    <td align=center >
	<font size=3>上傳 <?php echo "$title_name" ?> 簽名章檔</font>
   </td>
  </tr>
  <tr bgcolor="#f7ebaf">
    <td>
<input type="file" name="sign_file">
<input type="hidden" name="teach_title_id" value="<?php echo $teach_title_id ?>">

<input type="submit" name="do_key" value="上傳圖檔" ?>

</td>
  </tr>
</table>

</form>

</body>
</html>


