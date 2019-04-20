<?php

// $Id: upload.php 5310 2009-01-10 07:57:56Z hami $

include "config.php";

// 不需要 register_globals
if (!ini_get('register_globals')) {
	ini_set("magic_quotes_runtime", 0);
	extract( $_POST );
	extract( $_GET );
	extract( $_SERVER );
}

if($act=="上傳"){
	$fsn=uploadfile($_FILES['userfile']['tmp_name'],$_FILES['userfile']['type'],$_FILES['userfile']['size'],$_FILES['userfile']['name'],$description,$teacher_sn,$category_sn,$col_name,$col_sn,$teacher_sn,"1");
	
	if(!empty($fsn)){
		$msg=$userfile_name." 上傳成功！";
	}
}
?>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html;CHARSET=big5">
<html>
<head>
	<title>上傳檔案</title>
</head>

<body>

<?php
if($col_name=="ofsn" && empty($description)){
	$description="此檔是線上填報的附加檔案，其對應的編號是線上填報 $col_sn 號。";
}elseif($col_name=="serial" && empty($description)){
	$description="此檔是線上公告的附加檔案，其對應的編號是線上公告 $col_sn 號。";
}
?>
<script language='JavaScript'>
function closewin(){
	opener.location.href='<?php echo $SFS_PATH_HTML;?>modules/online_form/form_admin.php';
	self.close();
}
</script>
<form action='<?php echo $_SERVER['PHP_SELF'] ?>' method='post' enctype='multipart/form-data'>
檔案：<input type='file' name='userfile' class='tinyBorder'>
<input type='hidden' name='col_name' value='<?php echo $col_name;?>'>
<input type='hidden' name='col_sn' value='<?php echo $col_sn;?>'>
<input type='hidden' name='description' value='<?php echo $description;?>'>
<input type='submit' name='act' value='上傳' class='tinyBorder'>
<input type='button' value='關閉'  class='tinyBorder' onClick='closewin()'>
</form>

<?php echo $msg;?>

</body>
</html>