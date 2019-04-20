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
//head("展示部落格");
$act=($_POST['act'])?$_POST['act']:$_GET['act'];
$nowpage=$_POST['nowpage'];
$search_str=($_POST['search_str'])?$_POST['search_str']:$_GET['search_str'];
$search_str=trim($search_str);
//
// 您的程式碼由此開始
if($act=="search_ourblog" and $search_str!=""){
	//搜尋結果
	$sql2="select * from blog_home where enable=1 and (main like '%$search_str%' or direction like '%$search_str%' or alias like '%$search_str%')  ";

	//設定每頁顯示幾筆紀錄
	$pen=10;
	$PA=fpage($pen,$sql2);
	$sql2=$PA[0];

	$rs2=$CONN->Execute($sql2) or trigger_error($sql2,256);
	$i=0;
	while(!$rs2->EOF){
		$bh_sn[$i]=$rs2->fields['bh_sn'];
			//找出每人發佈的文章點閱數
			$sql3="select freq from blog_content where bh_sn='{$bh_sn[$i]}' ";
			$rs3=$CONN->Execute($sql3) or trigger_error($sql3,256);
			$content_mount[$bh_sn[$i]]=$content_mount[$bh_sn[$i]]+$rs3->RecordCount( );
			$j=0;
			$freq[$bh_sn[$i]]=0;
			while(!$rs3->EOF){
				$freq[$bh_sn[$i]]=$freq[$bh_sn[$i]]+$rs3->fields['freq'];
				$j++;
				$rs3->MoveNext();
			}
		//$style[$i]=$rs->fields['style'];
		$main[$bh_sn[$i]]=$rs2->fields['main'];
		$direction[$bh_sn[$i]]=nl2br($rs2->fields['direction']);
		$alias[$bh_sn[$i]]=$rs2->fields['alias'];
		$start[$bh_sn[$i]]=$rs2->fields['start'];
		$i++;
		$rs2->MoveNext();
	}
	foreach($freq as $key => $val){
	//echo $key." => ".$val."<br>";
	$hot_list.="
		<table style=\"border-style:solid ; border-width:1px ; background:#E7E7E7 ; width:96% ;\" align='center'>
		<tr valign='top'>
		<td rowspan='3' width='202' align='center'><a href='".$SFS_PATH_HTML."modules/our_blog/my_blog.php?bh_sn=$key'><img src='".$UPLOAD_URL."blog/cover/".$key.".jpg' alt='沒有封面圖片' border='0'></a></td>
		<td height='20%'>".$main[$key]." <font color='#828282'>文章篇數：".$content_mount[$key]."</font></td>
		</tr>
		<tr valign='top'><td>".$direction[$key]."</td></tr>
		<tr valign='bottom'><td><font color='#828282'>格主：".$alias[$key]." 建立日期：".$start[$key]." 點閱次數：".$val."</font></td></tr>
		</table><br>
		";
	}

	//登入
	$my_blog="
	<span class='like_button'><a href='".$SFS_PATH_HTML."modules/sfs3_blog'>登入</a></span>
	";

	//以搜尋個人的網誌主題和描述為主
	$search="
	<form action='{$_SERVER[PHP_SELF]}' method='POST'>
	<input type='text' name='search_str' size='12'> <input type='submit' name='' value='查詢'>
	<input type='hidden' name='act' value='search_blog'>
	</form>
	";

	//SFS3-Blog列表
	$sql="select * from blog_home where enable=1";
	$rs=$CONN->Execute($sql) or trigger_error($sql,256);
	$i=0;
	$bh_sn_arr=array();
	while(!$rs->EOF){
		$bh_sn[$i]=$rs->fields['bh_sn'];
		array_push($bh_sn_arr, $bh_sn[$i]);
		$main[$bh_sn[$i]]=$rs->fields['main'];
		$alias[$bh_sn[$i]]=$rs->fields['alias'];
		$i++;
		$rs->MoveNext();
	}
	foreach($bh_sn_arr as $val3){

		$all_list.="<div class='norlist'><font color='#CC0D0D'>&raquo;</font><a href='".$SFS_PATH_HTML."modules/our_blog/my_blog.php?bh_sn=$val3'>$main[$val3]~$alias[$val3]</a></div>";

	}
	$main="
	<html>
		<head>
			<title>我們的校園Blog</title><meta http-equiv='Content-Type' content='text/html; charset=big5'>
			<style>
				<!--
					.square {color:#FFFFFF; background-color:#089E0B ; font-weight=800 ;  margin-left:10px ; line-height:20px; text-align:center; max-width:25%  ;  word-break:break-all  ; border:thin solid yellow;}
					.square2 {color:#FFFFFF; background-color:#089E0B ; font-weight=800 ;  margin-left:10px ; line-height:20px; text-align:center; max-width:80%  ;  word-break:break-all  ; border:thin solid yellow;}
					.like_button	{font-size: small;	background-color: lightGray;	border-width: 2px;	border-color: #efefef #696969 #696969 #efefef;	border-style: solid;	padding: 2px; cursor:help; }
					.norlist {color:#211BC7;  margin-left:38px ; line-height:140%; text-align:left; wordbreak:keep-all ; text-indent:-10px ; text-decoration:none;}

				-->
			</style>
		</head>
			<body bgcolor='#26622A'><p>
				<table width='90%'  bgcolor='#FFFFFF' align='center' cellspacing='1'>
					<tr><td colspan='2' bgcolor='#A2A9E3'><a href='./'><img src='log4.gif' border='0'></a><div style=\"text-align:right ;margin-bottom:10px ; margin-right:10px\">我們的校園部落格</div></td></tr>
					<tr>
					<td bgcolor='#CFD4FF' width='74%' align='left' valign='top'>
						<div class='square'>搜尋結果</div><p>$hot_list<p>
					</td>
					<td bgcolor='#A2A9E3' width='26%' align='center' valign='top'>
						$PA[1]
						<div class='square2'>管理我的Blog</div><p>$my_blog<p>
						<div class='square2'>網誌查詢</div><p>$search<p>
						<div class='square2'>SFS3-Blog列表</div><p>$all_list<p>
					</td>
					</tr>
				</table>
	";
	echo $main;

}else{
$sql="select * from blog_home where enable=1";
$rs=$CONN->Execute($sql) or trigger_error($sql,256);
$count=$rs->RecordCount( );
if($count>5) $count=5;
$i=0;
$bh_sn_arr=array();
while(!$rs->EOF){
	$bh_sn[$i]=$rs->fields['bh_sn'];
	array_push($bh_sn_arr, $bh_sn[$i]);
		//找出每人發佈的文章點閱數
		$sql2="select freq from blog_content where bh_sn='{$bh_sn[$i]}' ";
		$rs2=$CONN->Execute($sql2) or trigger_error($sql2,256);
		$content_mount[$bh_sn[$i]]=$content_mount[$bh_sn[$i]]+$rs2->RecordCount( );
		$j=0;
		$freq[$bh_sn[$i]]=0;
		while(!$rs2->EOF){
			$freq[$bh_sn[$i]]=$freq[$bh_sn[$i]]+$rs2->fields['freq'];
			$j++;
			$rs2->MoveNext();
		}
	//$style[$i]=$rs->fields['style'];
	$main[$bh_sn[$i]]=$rs->fields['main'];
	$direction[$bh_sn[$i]]=nl2br($rs->fields['direction']);
	$alias[$bh_sn[$i]]=$rs->fields['alias'];
	$start[$bh_sn[$i]]=$rs->fields['start'];
	$i++;
	$rs->MoveNext();
}
//熱門******************************************************
uasort($freq, cmp);
foreach($freq as $key => $val){
//echo $key." => ".$val."<br>";
$hot_list.="
	<table style=\"border-style:solid ; border-width:1px ; background:#E7E7E7 ; width:96% ;\" align='center'>
	<tr valign='top'>
	<td rowspan='3' width='202' align='center'><a href='".$SFS_PATH_HTML."modules/our_blog/my_blog.php?bh_sn=$key'><img src='".$UPLOAD_URL."blog/cover/".$key.".jpg' alt='沒有封面圖片' border='0'></a></td>
	<td height='20%'>".$main[$key]." <font color='#828282'>文章篇數：".$content_mount[$key]."</font></td>
	</tr>
	<tr valign='top'><td>".$direction[$key]."</td></tr>
	<tr valign='bottom'><td><font color='#828282'>格主：".$alias[$key]." 建立日期：".$start[$key]." 點閱次數：".$val."</font></td></tr>
	</table><br>
	";
}
//**********************************************************

//隨機******************************************************
srand ((double) microtime() * 10000000);
$rand5arr=array();
$rand5arr = array_rand ($bh_sn_arr, $count);
foreach($rand5arr as $key => $val){
$random_list.="
	<table style=\"border-style:solid ; border-width:1px ; background:#E7E7E7 ; width:96% ;\" align='center'>
	<tr valign='top'>
	<tr>
	<td rowspan='3' width='202' align='center'><a href='".$SFS_PATH_HTML."modules/our_blog/my_blog.php?bh_sn={$bh_sn_arr[$val]}'><img src='".$UPLOAD_URL."blog/cover/".$bh_sn_arr[$val].".jpg' alt='沒有封面圖片' border='0'></a></td>
	<td height='20%'>".$main[$bh_sn_arr[$val]]." <font color='#828282'>文章篇數：".$content_mount[$bh_sn_arr[$val]]."</td>
	</tr>
	<tr valign='top'><td>".$direction[$bh_sn_arr[$val]]."</td></tr>
	<tr valign='bottom'><td><font color='#828282'>格主：".$alias[$bh_sn_arr[$val]]." 建立日期：".$start[$bh_sn_arr[$val]]." 點閱次數：".$freq[$bh_sn_arr[$val]]."</font></td></tr>
	</table><br>
	";
}
//************************************************************

//新進文章******************************************************
$sql_new="select * from blog_content where enable=1 order by dater DESC LIMIT 0,5";
$rs_new=$CONN->Execute($sql_new) or trigger_error($sql_new,256);
$i=0;
while(!$rs_new->EOF){
	$bc_sn[$i]=$rs_new->fields['bc_sn'];
	$bh_sn[$i]=$rs_new->fields['bh_sn'];
	//取得回應次數
		$sql4="select count(*) from blog_feelback where bc_sn='{$bc_sn[$i]}' ";
		$rs4=$CONN->Execute($sql4) or trigger_error($sql4,256);
		$feel_time[$i]=$rs4->fields['0'];
	$kind_sn[$i]=$rs_new->fields['kind_sn'];
	$sql="select kind_name from blog_kind where kind_sn='{$kind_sn[$i]}' and enable=1 ";
	$rs3=$CONN->Execute($sql) or trigger_error($sql,256);
	$kind_name[$i]=$rs3->fields['kind_name'];
	$title[$i]=$rs_new->fields['title'];
	$content[$i]=nl2br($rs_new->fields['content']);
	$dater[$i]=$rs_new->fields['dater'];
	//echo $dater[$i];
	//$dater[$i]=strtotime($dater[$i]);
	$date[$i]=date("F d, Y, l" ,strtotime($dater[$i]));
	$time[$i]=date("g:i A",strtotime($dater[$i]));
	$freq[$i]=$rs_new->fields['freq'];
	$new_list.="
		<table class='content_tb'>
			<tr><td>{$date[$i]}</td></tr>
			<tr><td>標題：{$title[$i]}分類：{$kind_name[$i]}</td></tr>
			<tr><td>{$content[$i]}<a href='continue.php?bh_sn={$bh_sn[$i]}&bc_sn={$bc_sn[$i]}'>完整閱讀...</a></td></tr>
			<tr><td><font color='#828282'>{$alias[$bh_sn[$i]]} {$time[$i]} 點閱次數{$freq[$i]} <a href='continue.php?bh_sn={$bh_sn[$i]}&bc_sn={$bc_sn[$i]}#reback'>回應</a>{$feel_time[$i]}</font></td></tr>
		</table>
	";
	$i++;
	$rs_new->MoveNext();
}

//************************************************************

$my_blog="
<span class='like_button'><a href='".$SFS_PATH_HTML."modules/sfs3_blog'>登入</a></span>
";

//以搜尋個人的網誌主題和描述為主
$search="
<form action='{$_SERVER[PHP_SELF]}' method='POST'>
<input type='text' name='search_str' size='12'> <input type='submit' name='' value='查詢'>
<input type='hidden' name='act' value='search_ourblog'>
</form>
";

foreach($bh_sn_arr as $val3){

	$all_list.="<div class='norlist'><font color='#CC0D0D'>&raquo;</font><a href='".$SFS_PATH_HTML."modules/our_blog/my_blog.php?bh_sn=$val3'>$main[$val3]~$alias[$val3]</a></div>";

}


$main="
<html>
	<head>
		<title>我們的校園Blog</title><meta http-equiv='Content-Type' content='text/html; charset=big5'>
		<style>
			<!--
				.square {color:#FFFFFF; background-color:#089E0B ; font-weight=800 ;  margin-left:10px ; line-height:20px; text-align:center; max-width:25%  ;  word-break:break-all  ; border:thin solid yellow;}
				.square2 {color:#FFFFFF; background-color:#089E0B ; font-weight=800 ;  margin-left:10px ; line-height:20px; text-align:center; max-width:80%  ;  word-break:break-all  ; border:thin solid yellow;}
				.like_button	{font-size: small;	background-color: lightGray;	border-width: 2px;	border-color: #efefef #696969 #696969 #efefef;	border-style: solid;	padding: 2px; cursor:help; }
				.norlist {color:#211BC7;  margin-left:38px ; line-height:140%; text-align:left; wordbreak:keep-all ; text-indent:-10px ; text-decoration:none;}
				.content_tb{
					background-color: #DDDDDD;
					margin: 2% 2% 2% 2%;
					text-align:left;
					width:96%;
					border-spacing:5px;
					border-collapse:0px;
					border:thin solid black;
				}
			-->
		</style>
	</head>
		<body bgcolor='#26622A'><p>
			<table width='90%'  bgcolor='#FFFFFF' align='center' cellspacing='1'>
				<tr><td colspan='2' bgcolor='#A2A9E3'><a href='./'><img src='log4.gif' border='0'></a><div style=\"text-align:right ;margin-bottom:10px ; margin-right:10px\">我們的校園部落格</div></td></tr>
				<tr>
				<td bgcolor='#CFD4FF' width='74%' align='left' valign='top'>
					<div class='square'>熱門網誌</div><p>$hot_list<p>
					<div class='square'>隨機網誌</div><p>$random_list<p>
					<div class='square'>新進文章</div><p>$new_list<p>
				</td>
				<td bgcolor='#A2A9E3' width='26%' align='center' valign='top'>
					<div class='square2'>管理我的Blog</div><p>$my_blog<p>
					<div class='square2'>網誌查詢</div><p>$search<p>
					<div class='square2'>SFS3-Blog列表</div><p>$all_list<p>
				</td>
				</tr>
			</table>
";
echo $main;
// SFS3 的版尾
//foot();
}


function cmp($a,$b) {

      if ($a == $b) return 0;
      return ($a > $b) ? -1 : 1;

   }

function fpage($pen,$sql){
    global $nowpage,$search_str;
    //總共有幾頁
    $rs1 = @mysql_query($sql) or trigger_error($sql,256);
    $kk = mysql_num_rows($rs1) ;
    $d=$kk%$pen;
    if($d==0) $pagenum=$kk/$pen;
    else $pagenum=ceil($kk/$pen);

    //目前位於第幾頁
    if($nowpage=="") $nowpage=1;
    $start=($nowpage-1)*$pen;

    $limit_str=" limit $start , $pen";
    $end_sql=$sql.$limit_str;
    $pageArr[0]=$end_sql;

    $menu.="<form action='{$_SERVER['PHP_SELF']}' method='POST'><select name='nowpage' onchange='this.form.submit()'>\n";
    for($i=1;$i<=$pagenum;$i++){
        if($i==$nowpage) $selected[$i]="selected";
        $menu.="<option value='$i' $selected[$i]>第 $i 頁</option>\n";
    }
    $menu.="
	</select>
	<input type='hidden' name='act' value='search_ourblog'>
	<input type='hidden' name='search_str' value='$search_str'>
	</form>\n";
    $pageArr[1]=$menu;

    //傳回包含limit的sql語法還有分頁棒
    return $pageArr;
}



?>
