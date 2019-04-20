<?php
// 引入 SFS3 的函式庫
include "../../include/config.php";

// 引入您自己的 config.php 檔
require "config.php";

// 認證
sfs_check();

$kind_name=trim($_POST['kind_name']);
$bh_sn=($_POST['bh_sn'])?$_POST['bh_sn']:$_GET['bh_sn'];

if($_POST['s1']=="送出" && $kind_name) {
	$sql="insert into blog_kind(bh_sn,owner_id,kind_name) values('$bh_sn','{$_SESSION['session_tea_sn']}','$kind_name') ";
	$CONN->Execute($sql) or teigger_error($sql,256);
	$kind_sn=$CONN->Insert_ID();
	$url_str=$SFS_PATH_HTML.get_store_path()."/blog.php?kind_sn=$kind_sn";
	echo "<html><body>
	<script LANGUAGE=\"JavaScript\">
	window.opener.location.href=\"$url_str\";
	window.close();
	</script>
	</body>
	</html>";

}

echo "新增文章類別<p></p>
<form action='{$_SERVER['PHP_SELF']}' method='POST'>
	<input type='text' name='kind_name' size='30'>
	<input type='submit' name='s1' value='送出'>
	<input type='hidden' name='bh_sn' value='$bh_sn'>
</form>
<button onclick=\"window.close()\">關閉</button>
	";
?>
