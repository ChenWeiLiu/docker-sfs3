<?php

// $Id: sfs_case_signpost.php 6820 2012-06-22 11:22:47Z infodaes $

require_once "sfs_case_studclass.php";
require_once "sfs_case_dataarray.php";

//秀出新消息
function showPost(){
	global $CONN,$SFS_PATH_HTML;
	// 確定連線成立
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);

	//今天日期
	$today=date("Y-m-d");
	
	//辦公室種類
	$office=room_kind();
	
	//取得公告
	$sql_select="select serial,date_format(post_date,'%Y-%m-%d'),title,content,teacher_sn,FSN,image_url from new_board where work_date >= '$today' || work_date='0000-00-00 ' order by post_date desc ,  serial desc";
	
	$recordSet=$CONN->Execute($sql_select) or user_error($sql_select, 256);
	
	while(list($serial,$post_date,$title,$content,$tsn,$FSN,$image_url) =$recordSet->FetchRow()){
		$content=nl2br($content);
		$man=get_teacher_post_data($tsn);
		
		//若是該教師有班級<優先顯示班級
		if(empty($man[class_num])){
			$n=$man[post_office];
			$office_name=$office[$n]." ";
		}else{			
			$n=$man[class_num];
			$office_name=curr_class_num2_data($n)." ";
		}
		
		$post_man=(empty($man[name]))?get_teacher_name($tsn):"$man[name]";
		
		//處裡檔案
		$file=(!empty($FSN))?"<a href='".$SFS_PATH_HTML."modules/new_board/file.php?FSN=$FSN' target='_blank'>
		<img src='".$SFS_PATH_HTML."modules/new_board/images/filesave.png' width=16 height=17 border=0></a>":"";
		$url=(!empty($image_url))?"<a href='$image_url' target='_blank'>連結</a>":"";

		$post.="
		<tr bgcolor='white'>
		<td align='center'>$post_date</td>
		<td><a href='".$SFS_PATH_HTML."modules/new_board/index.php?act=view&serial=$serial'>$title</a> $file $url</td>
		<td nowarp align='right'><font color='#67518A'>$man[title_name]</font> $post_man</td>
		</tr>";
	}

	if(empty($post))$post="<tr bgcolor='#FFFFFF'><td colspan=6>目前沒有任何公告</td></tr>";

	$main="
	<img src='${SFS_PATH_HTML}images/board_logo.png' alt='' width='224' height='39' border='0'>
	<table width='96%' border='0' cellspacing='1' cellpadding='2' bgcolor='#536F46' class='small'>
	<tr bgcolor='#E2E6B7'>
	<td align='center'>日期</td>
	<td align='center'>標題</td>
	<td align='center'>發佈</td>
	</tr>
	$post
	</table>";
	return $main;
}

//秀出首頁填報資訊
function school_sign_form(){
	global $CONN,$SFS_PATH_HTML;
	// 確定連線成立
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);

	$today=date("Y-m-d");
	
	//找出填報資訊
	$sql_select="select * from form_all where enable='1' and ('$today' between of_start_date and of_dead_line)";
	$recordSet=$CONN->Execute($sql_select) or trigger_error("錯誤訊息： $sql_select", E_USER_ERROR);

    	if(empty($recordSet)) return "";

	while($f= $recordSet->FetchRow()){
		$man=get_teacher_name($f[teacher_sn]);

		$sign_ok=(check_signd_form($_SESSION[session_tea_sn],$f[ofsn]))?"<font color='blue'>已填</font>":"<font color='red'>待填</font>";

		$school_sign=(!empty($have_sign_form))?"<td colspan='2'>$have_sign_form</td>":"<td>$f[of_dead_line]</td><td>$man</td>";
		$subject=(strlen($f[of_title])>=30)?substr($f[of_title],0,30)."...":$f[of_title];
		$data1.="
		<tr bgcolor='white'>
		<td><a href='".$SFS_PATH_HTML."modules/online_form/index.php?act=sign&ofsn=$f[ofsn]'>$subject</a></td>
		$school_sign<td>$sign_ok</td>
		</tr>
		";
	}

	if(empty($data1))return ;

	$main="
	<table cellspacing='1' cellpadding=3 bgcolor='#D7D7D7' class='small'>
	<tr bgcolor='#cbe9ff'><td>填報調查主題</td><td>截止日期</td><td>發佈者</td><td>狀況</td></tr>
	$data1
	</table>
	";
	return $main;
}

//檢查某教師是否填報過了
function check_signd_form($teacher_sn=0,$ofsn=0){
	global $CONN;
	// 確定連線成立
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);

	if(empty($teacher_sn))trigger_error("無教師編號無法確定是否已經填過該調查", E_USER_ERROR);
	$sql_select="select schfi_sn,man_name,fill_time from form_fill_in where teacher_sn=$teacher_sn and ofsn=$ofsn";
	$recordSet=$CONN->Execute($sql_select) or trigger_error($sql_select, E_USER_ERROR);
	list($schfi_sn,$man_name,$fill_time)=$recordSet->FetchRow();

	if(!empty($schfi_sn)){
		return "已於 $fill_time 由 $man_name 所填報了。";
	}
	return false;
}
?>