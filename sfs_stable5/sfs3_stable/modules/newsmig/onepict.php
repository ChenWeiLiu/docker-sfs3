<?php
ob_start();
session_start();
include "config.php";

//這支程式是新聞瀏覽的程式, 不須做 sys_check的動作
if ($m_arr["IS_STANDALONE"]=='0'){
	//秀出網頁布景標頭
	head("新聞發佈");
}

//主要內容
$op_rdsno = $_GET["rdsno"];
$op_imgname = $_GET["imgname"];
$op_imgname = substr_replace($op_imgname,"Mi",0,2);
$op_dir_url = $htmlsavepath.$op_rdsno."/";
?>

<html>
<head>
<meta http-equiv="Content-Type" content="html/text;charset=Big5">
<title>校園新聞發佈</title>
</head>

<body>
<br>
<center>
<img border="2" src="<?php echo $op_dir_url.$op_imgname; ?>">
</center>
<br>
<?php
if ($m_arr["IS_STANDALONE"]=='0'){
	//SFS3佈景結尾
	foot();
}
?>

</body>

</html>
