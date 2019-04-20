<?php
// 引入 SFS3 的函式庫
include "../../include/config.php";

// 引入您自己的 config.php 檔
require "config.php";

// 認證
sfs_check();

$bc_sn=($_POST['bc_sn'])?$_POST['bc_sn']:$_GET['bc_sn'];
if($bc_sn){
$sql="select * from blog_content where bc_sn='$bc_sn' ";
$rs=$CONN->Execute($sql) or trigger_error($sql,256);
$kind_sn=$rs->fields['kind_sn'];
$title=$rs->fields['title'];
$content=nl2br($rs->fields['content']);
$content2=nl2br($rs->fields['content2']);
$bh_sn=$rs->fields['bh_sn'];
$dater=$rs->fields['dater'];
echo "
<table cellspacing='4' align='center' bgcolor='#C9CEFF' width='100%'><tr><td></td></tr><tr><td><h3>$title</h3></td></tr>
<tr><td><table cellspacing='1' bgcolor='#FFFFFF' width='100%'><tr><td>$content<p>$content2
</td></tr></table></td></tr><tr><td><font color='#878787'>最後更新日期：$dater</font></td></tr></table>
<button onclick=\"window.close()\">關閉</button>
";
}else{
echo "
<div style=\"background-color:#FFF08B ; color:#FF0000 ; text-align:center\">請先選擇文章</div>
<button onclick=\"window.close()\">關閉</button>
";
}


?>
