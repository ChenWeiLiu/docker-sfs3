<?php
//Id$

// 引入 SFS3 的函式庫
include "../../include/config.php";

// 引入您自己的 config.php 檔
require "config.php";

// 認證
//sfs_check();

// 叫用 SFS3 的版頭
//不要使用 SFS3 的版頭
//head("我的部落格");

//
// 您的程式碼由此開始
//個人首頁資料
$bh_sn=($_POST['bh_sn'])?$_POST['bh_sn']:$_GET['bh_sn'];
$bc_sn=($_POST['bc_sn'])?$_POST['bc_sn']:$_GET['bc_sn'];

$new_name=($_POST['new_name'])?$_POST['new_name']:$_GET['new_name'];
$new_feel_cont=($_POST['new_feel_cont'])?$_POST['new_feel_cont']:$_GET['new_feel_cont'];
$act=($_POST['act'])?$_POST['act']:$_GET['act'];

if(!$bh_sn) header("Location:./index.php");
if(!$bc_sn) header("Location:./my_blog.php?bh_sn=$bh_sn");

//寫入回應資料
if($act=="save_feel"){
	$sql_feel="insert into blog_feelback(bc_sn,name,feel_cont,feel_date,ip) values('$bc_sn','$new_name','$new_feel_cont',now(),'{$_SERVER['REMOTE_ADDR']}')";
	$CONN->Execute($sql_feel) or trigger_error($sql_feel,256);
	header("Location:./continue.php?bh_sn=$bh_sn&bc_sn=$bc_sn");
	exit;
}

//紀錄點閱次數
$CONN->Execute("update blog_content set freq=freq+1 where bc_sn='$bc_sn' ");

$sql="select * from blog_home where bh_sn='$bh_sn' and enable=1 ";
$rs=$CONN->Execute($sql) or trigger_error($sql,256);
$check=$rs->RecordCount( );
if(!$check) header("Location:./index.php");
$style=$rs->fields['style'];
$css=$style."/style.css";
$main=$rs->fields['main'];
$direction=nl2br($rs->fields['direction']);
$alias=$rs->fields['alias'];
$start=$rs->fields['start'];
$cover_image=$UPLOAD_URL."blog/cover/".$bh_sn.".jpg";

//文章資料
$sql2="select * from blog_content where bc_sn='$bc_sn' and  enable=1 ";
$rs2=$CONN->Execute($sql2) or trigger_error($sql2,256);
$bc_sn=$rs2->fields['bc_sn'];
$kind_sn=$rs2->fields['kind_sn'];
$sql="select kind_name from blog_kind where kind_sn='$kind_sn' and enable=1 ";
$rs3=$CONN->Execute($sql) or trigger_error($sql,256);
$kind_name=$rs3->fields['kind_name'];
$title=$rs2->fields['title'];
$content=nl2br($rs2->fields['content']);
$content2=nl2br($rs2->fields['content2']);
$dater=$rs2->fields['dater'];
//$dater=strtotime($dater[$i]);
$date=date("F d, Y, l" ,strtotime($dater[$i]));
$time=date("g:i A",strtotime($dater[$i]));
$freq=$rs2->fields['freq'];

//找出本篇文章所有回應
$sql3="select * from blog_feelback where bc_sn='$bc_sn' ";
$rs3=$CONN->Execute($sql3) or trigger_error($sql3,256);
$i=0;
$feel_list.="<tr><td><a name=\"#reback\"></a><div class='tb1'>回應</div></td></tr>";
while(!$rs3->EOF){
	$bf_sn[$i]=$rs3->fields['bf_sn'];
	$name[$i]=$rs3->fields['name'];
	$feel_cont[$i]=nl2br($rs3->fields['feel_cont']);
	$feel_date[$i]=$rs3->fields['feel_date'];
	$ip[$i]=$rs3->fields['ip'];
	$feel_list.="
		<tr><td><p>&nbsp;</p><div class='content_fnt'>$feel_cont[$i]</div></td></tr>
		<tr><td><div class='alias_fnt'>$name[$i] $feel_date[$i] from $ip[$i] </div></td></tr>
		";
	$i++;
	$rs3->MoveNext();
}

$input_feel="
	<tr><td><div class='tb1'>留下足跡</div></td></tr>
	<tr><td><div class='feel_fnt'>
	<form action='{$_SERVER['PHP_SELF']}' method='POST'>
		姓名<br><input type='text' name='new_name' size='20'><br>
		內容<br><textarea name='new_feel_cont' cols='60' rows='10'></textarea><br>
		<input type='submit' name='submit' value='送出'> <input type='reset' value='清除'>
		<input type='hidden' name='act' value='save_feel'>
		<input type='hidden' name='bh_sn' value='$bh_sn'>
		<input type='hidden' name='bc_sn' value='$bc_sn'>
	</form>
	</div></td></tr>
";



$a_list="
	<table class='content_tb'>
		<tr><td><div class='tb1'>$date</div></td></tr>
		<tr><td><div class='title_fnt'>$title</div><div class='kind_fnt'>分類：$kind_name</div></td></tr>
		<tr><td><div class='content_fnt'>$content</div><div class='content_fnt'>$content2</div></td></tr>
		<tr><td><div class='alias_fnt'>$alias $time 點閱次數$freq </div></td></tr>
		$feel_list
		$input_feel
	</table>
";

$my_blog="
<span class='button1'><a href='".$SFS_PATH_HTML."modules/sfs3_blog'>登入</a></span>
";



//找出文章分類與文章分類筆數
$sql5="select kind_sn,kind_name from blog_kind where enable=1 and bh_sn='$bh_sn' ";
$rs5=$CONN->Execute($sql5) or trigger_error($sql5,256);
$m=0;
while(!$rs5->EOF){
	$kind_sn=$rs5->fields['kind_sn'];
	$kind_name=$rs5->fields['kind_name'];
		$sql6="select count(*) from blog_content where kind_sn='$kind_sn' and enable=1";
		$rs6=$CONN->Execute($sql6) or trigger_error($sql6,256);
		$kind_num=$rs6->fields['0'];
	$kind_list.="<div class='kind_list_fnt'><font color='#CC0D0D'>&raquo;</font><a href='my_blog.php?kind_sn=$kind_sn&bh_sn=$bh_sn'>$kind_name</a> ($kind_num 篇)</div>";
	$m++;
	$rs5->MoveNext();
}

//一週內新進文章列表
//上個禮拜的此時
$lastweek =  mktime (0,0,0,date("m"),date("d")-7,  date("Y"));
$sql6="select * from blog_content where enable=1 and UNIX_TIMESTAMP(dater)>$lastweek and bh_sn='$bh_sn' order by dater DESC";
$rs6=$CONN->Execute($sql6) or trigger_error($sql6,256);
while(!$rs6->EOF){
	$nc_bc_sn=$rs6->fields['bc_sn'];
	$nc_title=$rs6->fields['title'];
	$nc_dater=date("Y/m/d" ,strtotime($rs6->fields['dater']));
	$new_content.="<div class='new_content_list_fnt'><font color='#CC0D0D'>&raquo;</font><a href='continue.php?bh_sn=$bh_sn&bc_sn=$nc_bc_sn'>$nc_title</a>$nc_dater</div>";
	$rs6->MoveNext();
}

//一週內新進回應列表
$sql9="select bc_sn from blog_content where enable=1 and bh_sn='$bh_sn' ";
$rs9=$CONN->Execute($sql9) or trigger_error($sql9,256);
while (!$rs9->EOF) {
	$bc_sn_arr[] =$rs9->fields['bc_sn'];
	$rs9->MoveNext();
}
$bc_sn_str=implode(",",$bc_sn_arr);
$sql7="select bc_sn,feel_date from blog_feelback where UNIX_TIMESTAMP(feel_date)>$lastweek and bc_sn in ($bc_sn_str) order by feel_date DESC";
$rs7=$CONN->Execute($sql7) or trigger_error($sql7,256);
while(!$rs7->EOF){
	$nf_bc_sn=$rs7->fields['bc_sn'];
	$nf_feel_date=date("Y/m/d" ,strtotime($rs7->fields['feel_date']));
	$sql8="select title from blog_content where enable=1 and bc_sn='$nf_bc_sn' ";
	$rs8=$CONN->Execute($sql8) or trigger_error($sql8,256);
	$nf_title=$rs8->fields['title'];
	$new_feel.="<div class='new_content_list_fnt'><font color='#CC0D0D'>&raquo;</font><a href='continue.php?bh_sn=$bh_sn&bc_sn=$nf_bc_sn'>RE:$nf_title</a>$nf_feel_date</div>";
	$rs7->MoveNext();
}

$main="
<html>
	<head>
		<title>我的校園Blog</title><meta http-equiv='Content-Type' content='text/html; charset=big5'>
		<link rel=stylesheet href='themes/$css' type='text/css'>
	</head>
		<body><p>
			<table class='main_tb'>
				<tr><td colspan='2'><table  class='second_tb'><tr><td class='td_second_left'><div  class='log_div'><a href='./'><img src='log4.gif' width='80' height='16' border='0'></a></div><div class='fnt2'>$main</div><div class='fnt3'>$direction</div></td><td  class='td_second_right'><a href='my_blog.php?bh_sn=$bh_sn'><img src='$cover_image' border='0'></a></td></tr></table></td></tr>
				<tr>
				<td  class='td_left'>
					$a_list
				</td>
				<td  class='td_right'>
					<p>
					<div class='fnt1'>格主：$alias (Since:$start)</div><p>
					<div class='tb2'>管理我的Blog</div><p>$my_blog<p>
					<div class='tb2'>文章分類</div><p>$kind_list<p>
					<div class='tb2'>新進文章</div><p>$new_content<p>
					<div class='tb2'>新進回應</div><p>$new_feel<p>
				</td>
				</tr>
			</table>
";
echo $main;
// SFS3 的版尾
//foot();

function cmp($a,$b) {

      if ($a == $b) return 0;
      return ($a > $b) ? -1 : 1;

   }

?>
