<?php

// 引入 SFS3 的函式庫
include "../../include/config.php";

// 引入您自己的 config.php 檔
require "config.php";

// 認證
sfs_check();

//管理者驗證
if(!is_blog_admin()) header("Location:./");

//轉換成全域變數
$act=($_POST['act'])?"{$_POST['act']}":"{$_GET['act']}";
$size_arr=$_POST['size_arr'];
$many_arr=$_POST['many_arr'];
//$act=($_POST['act'])?"{$_POST['act']}":"{$_GET['act']}";

if($act=="save"){//儲存設定
	foreach ($size_arr as $ts => $sz){
		$sz=intval(trim($sz));
		$many_arr[$ts]=intval(trim($many_arr[$ts]));
		$sql="update blog_quota set size='$sz' , many='{$many_arr[$ts]}' where teacher_sn='$ts'  ";
		$CONN->Execute($sql) or trigger_error($sql,256);
	}
}


// 叫用 SFS3 的版頭
head("校園部落格檔案控管");
//網路硬碟的管理介面
//您的程式碼由此開始
//預設是每人容量20MB，數量200個

$sql="select teach_id, name , teacher_sn from teacher_base where teach_condition=0 ";
$rs=$CONN->Execute($sql) or trigger_error($sql,256);
$i=0;
while(!$rs->EOF){
	$teach_id[$i]=$rs->fields['teach_id'];
	$name[$i]=$rs->fields['name'];
	$teacher_sn[$i]=$rs->fields['teacher_sn'];
	$sql2="select * from blog_quota where teacher_sn='{$teacher_sn[$i]}' and enable=1";
	$rs2=$CONN->Execute($sql2) or trigger_error($sql2,256);

	//給預設值
	if(!$rs2->fields['size'] && !$rs2->fields['many']) $CONN->Execute("insert into blog_quota (teacher_sn,size,many,enable) values('$teacher_sn[$i]','20','200','1')");

	$size[$i]=$rs2->fields['size'];
	if(!$size[$i]) $size[$i]=20;
	$many[$i]=$rs2->fields['many'];
	if(!$many[$i]) $many[$i]=200;
	$main.="<tr bgcolor='#FFFFFF'><td> $teach_id[$i] </td><td> $name[$i] </td><td align='center'> <input type='text' name='size_arr[$teacher_sn[$i]]' value='{$size[$i]}' size='6'></td><td align='center'><input type='text' name='many_arr[$teacher_sn[$i]]' value='{$many[$i]}' size='6'></td></tr>\n";
	$i++;
	$rs->MoveNext();
}

$main="<table bgcolor='#24A20B' cellspacing='1'>\n
				<form action='{$_SERVER['PHP_SELF']}' method='POST'>\n
				<tr bgcolor='#FFFFFF'><td colspan='4'><input type='submit' value='儲存'></td></tr>\n
				<tr bgcolor='#FFFFFF'><td>代號</td><td>姓名</td><td>最大空間(MB)</td><td>最多檔案數</td></tr>\n
				$main
				<input type='hidden' name='act' value='save'>\n
				<tr bgcolor='#FFFFFF'><td colspan='4'><input type='submit' value='儲存'></td></tr>\n
				</form>\n
			</table>\n";
echo $main;
// SFS3 的版尾
foot();

?>