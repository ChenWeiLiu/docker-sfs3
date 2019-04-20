<?php

// $Id: index.php 8829 2016-02-26 02:06:44Z hsiao $

include "config.php";

sfs_check();

// 不需要 register_globals
if (!ini_get('register_globals')) {
	ini_set("magic_quotes_runtime", 0);
	extract( $_POST );
	extract( $_GET );
	extract( $_SERVER );
}

//執行動作判斷
if($act=="addpost"){
	addpost($post,$mode,$postSerial);
	header("location:index.php");
}elseif($act=="delPost"){
	delPost($postSerial);
	header("location:index.php");
}elseif($act=="mantain"){
	$main=&MantainMsgForm();
}elseif($act=="postMsgForm"){
	$main=&postMsgForm($mode,$postSerial);
}elseif($act=="發佈公告" or $act=="add"){
	$main=&postMsgForm("add");
}elseif($act=="view" and !empty($serial)){
	$main=&viewPost($serial);
}else{
	$main=&main_showPost();
}


//秀出網頁
head("校園佈告欄");
echo $main;
foot();



/*******************************************************/
//發布消息的函數
/*******************************************************/
//秀出新消息
function &main_showPost(){
	global $today,$CONN,$POSTUPDIR,$UPLOAD_URL,$SFS_PATH_HTML,$school_menu_p;
	$tool_bar=&make_menu($school_menu_p);
	$post=showPost();
	$main="
	$tool_bar
	$post";
	return $main;
}

//發佈消息的表格
function &postMsgForm($mode="add",$postSerial="",$toSomeOne="all"){
	global $school_menu_p,$today,$teacher_sn,$CONN;
	
	$tool_bar=&make_menu($school_menu_p);
	
	if($mode=="modify" && !empty($postSerial)){
		$sql_select="select work_date,title,content,teacher_sn ,FSN,image_url from new_board where serial=$postSerial";
		$recordSet=$CONN->Execute($sql_select);
		list($post_date,$title,$content,$teacher_sn,$FSN,$image_url) = $recordSet->FetchRow();
		$d=explode("-",$post_date);
	}
	
	$dY=($mode=="modify")?$d[0]:date("Y");
	$dM=($mode=="modify")?$d[1]:date("m");
	$dD=($mode=="modify")?$d[2]:date("d");
	
	$main="
	$tool_bar
	<table  border='0' cellspacing='1' cellpadding='0'  bgcolor='#E2E6B7'>
	<tr bgcolor='#FFFFFF'><td>
		<table cellspacing='0' cellpadding='4'>
		<form action='{$_SERVER['PHP_SELF']}' method='post' enctype='multipart/form-data'>
		<tr><td align='right' bgcolor='#E2E6B7'><img src='images/fileopen.png' width=18 height=18 border=0></td><td>公告標題</td><td>
		<input type='text' name='post[title]' value='$title' class='tinyBorder' style='width: 400px;'>
		</td></tr>

		<tr><td align='right' bgcolor='#E2E6B7'><img src='images/edit.png' width=16 height=16 border=0></td><td>公告內容</td><td>
		<textarea cols='80' rows='8' name='post[content]' style='width: 400px;' class='tinyBorder'>$content</textarea>
		</td></tr>

		<tr><td align='right' bgcolor='#E2E6B7'><img src='images/history.png' width=16 height=16 border=0></td><td>公告期限</td><td>
		西元 <input type='text' name='post[y]' value='$dY' size='4' class='tinyBorder'> 年 <input type='Text' name='post[m]' size='2' class='tinyBorder' value='$dM'> 月 <input type='Text' name='post[d]' value='$dD' size='2' class='tinyBorder'> 日
		</td></tr>

		<tr><td align='right' bgcolor='#E2E6B7'><img src='images/filesave.png' width=16 height=17 border=0></td><td>相關附件</td><td>
		<input type='file' name='file' style='width: 400px;' class='tinyBorder'><br>
		</td></tr>

		<tr><td align='right' bgcolor='#E2E6B7'><img src='images/filefind.png' width=16 height=16 border=0></td><td>相關連結</td><td>
		<input type='text' name='post[url]' value='$image_url' class='tinyBorder' style='width: 400px;'>
		</td></tr></table>
	</td></tr></table>
	<input type='hidden' name='mode' value='$mode'>
		<input type='hidden' name='postSerial' value='$postSerial'>
		<input type='hidden' name='post[teacher_sn]' value='$teacher_sn'>
		<input type='hidden' name='act' value='addpost'>
		<p align='center'><input type='Submit' value='下一步' class='tinyBorder'></p>
		</form>
	";
	return $main;
}

//加入公告
function addpost($post="",$mode="add",$postSerial=""){
	global $POSTUPDIR,$CONN,$teacher_sn;
	//檢查標題、內容
	if(empty($post[title]))trigger_error("沒有輸入標題。",E_USER_ERROR);
	if(empty($post[content]))trigger_error("沒有輸入內容。",E_USER_ERROR);
	if(!get_magic_quotes_gpc()){
		$post[title]=addslashes($post[title]);
		$post[content]=addslashes($post[content]);
	}
	//檢查有效日期
	if(checkdate($post[m],$post[d],$post[y])){
		$post[dieline]=$post[y]."-".$post[m]."-".$post[d];
	}else{
		$post[dieline]=date("m-d-Y",mktime (0,0,0,(date("m")+3),date("d"),date("Y")));
	}
	
	//加入
	if($mode=="add"){
		$sql_insert="insert into new_board (title,content,teacher_sn,post_date,work_date,image_url) values ('$post[title]','$post[content]','$post[teacher_sn]',now(),'$post[dieline]','$post[url]')";
	}elseif($mode=="modify"){
		$sql_insert="update new_board set title='$post[title]',content='$post[content]',post_date=now(),work_date='$post[dieline]',image_url='$post[url]' where serial='$postSerial'";
	}

	$CONN->Execute($sql_insert) or trigger_error("SQL語法錯誤： $sql_insert", E_USER_ERROR);

	if($mode=="add"){
		$insert_id=mysql_insert_id();
	}elseif($mode=="modify"){
		$insert_id=$postSerial;
	}
	
	//檢查上傳圖檔
	if(!empty($_FILES['file']['tmp_name']) and !empty($insert_id)){
		$fsn=uploadfile($_FILES['file']['tmp_name'],$_FILES['file']['type'],$_FILES['file']['size'],$_FILES['file']['name'],"學校公告附件",$teacher_sn,"","serial",$insert_id,$teacher_sn,1);
	}
	
	if(!empty($fsn)){
		$sql_update="update new_board set FSN='$fsn' where serial='$insert_id'";
		$CONN->Execute($sql_update) or trigger_error("SQL語法錯誤： $sql_update", E_USER_ERROR);
	}
	return $insert_id;
}


//維護新消息的列表
function &MantainMsgForm(){
	global $today,$CONN,$teacher_sn,$school_menu_p;
	
	$tool_bar=&make_menu($school_menu_p);

	$sql_select="select serial,date_format(post_date,'%Y-%m-%d'),title,content,FSN,image_url from new_board where teacher_sn=$teacher_sn order by post_date desc , serial desc";
	$recordSet=$CONN->Execute($sql_select);

	while(list($serial,$post_date,$title,$content,$FSN,$image_url) = $recordSet->FetchRow()){
	$content=nl2br($content);

	$post.="<tr><td width='25'>
	<img src='images/d.gif' width=25 height=16 border=0>
	</td><td>
	<FONT class=redword>《".$post_date."》</FONT><font color='Navy'>".$title."</FONT>
	</td><td>
	<a href='{$_SERVER['PHP_SELF']}?act=postMsgForm&postSerial=$serial&mode=modify'>
	<img src='images/txt.png' width=16 height=16 border=0 hspace=4 align='middle'>修改</a>
	</td><td>
	<a href='{$_SERVER['PHP_SELF']}?act=delPost&postSerial=$serial'>
	<img src='images/remove.png' width=16 height=16 border=0 hspace=4 align='middle'>刪除</a>
	</td></tr>
	<tr bgcolor='white'><td></td><td colspan=4>
	<font color='#2C2E30' class='line2'>$content</font>
	</td></tr>";
	}
	$postTXT=(empty($post))?"<tr><td width='25'>
	<img src='image/d.gif' width='25' height='16' border='0'>
	</td><td bgcolor='White' background='image/post_bg.gif'>
	<FONT class=redword>《".$today."》</FONT><font color='Navy'>您沒有發佈過任何消息。</FONT>
	</td></tr>":$post;

	$main="
	$tool_bar
	<table  border='0' cellspacing='1' cellpadding='2'  bgcolor='#E2E6B7' class='small'>
	$postTXT
	</table>";
	return $main;
}

//刪除新消息
function delPost($postSerial=""){
	global $CONN,$teacher_sn;
	$sql_select="select FSN from new_board where serial='$postSerial'";
	$recordSet=$CONN->Execute($sql_select) or trigger_error("SQL語法錯誤： $sql_select", E_USER_ERROR);
	list($FSN) = $recordSet->FetchRow();
	
	if($FSN){
		$del_file=$POSTUPDIR."/".$image;
		@unlink($del_file);
	}
	
	$sql_delete="delete from new_board where serial='$postSerial' and teacher_sn=$teacher_sn";
	if($CONN->Execute($sql_delete)){
		$msg="成功的刪掉了 $postSerial 這篇公告。";
	}
}

//秀出新消息
function &viewPost($serial=""){
	global $today,$CONN,$POSTUPDIR,$UPLOAD_URL,$SFS_PATH_HTML,$school_menu_p;
	$tool_bar=&make_menu($school_menu_p);

	//辦公室種類
	$office=room_kind();
	
	$sql_select="select work_date,title,content,teacher_sn,post_date,FSN,image_url from new_board where serial=$serial";
	$recordSet=$CONN->Execute($sql_select);
	list($work_date,$title,$content,$teacher_sn,$post_date,$FSN,$image_url) = $recordSet->FetchRow();
	
	//$content = ereg_replace("((http[s]?)|(ftp)|(telnet)|(gopher))+://[^<>[:space:]]+[[:alnum:]/]","\\0", $content); 
	$content = ereg_replace("[[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/]","<a href=\"\\0\">\\0</a>", $content);
	$content=nl2br($content);
 
	
	$man=get_teacher_post_data($teacher_sn);
	//若是該教師有班級<優先顯示班級
		if(empty($man[class_num])){
			$n=$man[post_office];
			$office_name=$office[$n]." ";
		}else{			
			$n=$man[class_num];
			$office_name=curr_class_num2_data($n)." ";
		}
		
		$post_man=(empty($man[name]))?get_teacher_name($tsn):"$man[name]";
	
	$url=(!empty($image_url))?"<a href='$image_url' target='_blank'>$image_url</a>":"";
	$file=(!empty($FSN))?"<a href='".$SFS_PATH_HTML."modules/new_board/file.php?FSN=$FSN' target='_blank'><img src='".$SFS_PATH_HTML."modules/new_board/images/filesave.png' width=16 height=17 border=0></a>":"";

	$show_url=(empty($url))?"":"相關連結： $url";
	$show_file=(empty($file))?"":"相關附件： $file";

	$main="
	$tool_bar
	<table  width='96%' border='0' cellspacing='1' cellpadding='10'  bgcolor='#828687' class='small'>
	<tr bgcolor='#FFFFFF'><td>
	<div align='center'>
	<font size=5 color='#224180'>$title</font>
	<p>發佈者： <font color='red'>$office_name</font> <font color='darkGreen'>$post_man</font> ，公告期限： <font color='darkMagenta'>$work_date</font> </p>
	</div>
	<hr align='center' size=1 width='90%' noshade=0>
		<table width='90%' align='center'>
   		<tr><td style='line-height: 1.5;'>$content</td></tr>
 		</table>
	<hr align='center' size=1 width='90%' noshade=0>
	<p align='center'>
	$show_url $show_file 發佈時間： $post_date
	</p>
	<br>
	</td></tr>
	</table>	
	";
	return $main;
}

?>
