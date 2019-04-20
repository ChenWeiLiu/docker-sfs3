<?php             
$m_id=intval($m_id);                                                                                                          
$query = "select  * from unit_c  where b_id='$m_id'  and b_days > 0 ";
$result = mysql_query($query);
$row= mysql_fetch_array($result);
$b_id = $row['b_id'];
$bk_id = $row['bk_id'];
$b_open_date = $row['b_open_date'];
$b_days = $row['b_days'];
$b_unit = $row['b_unit'];
$b_title = $row['b_title'];
$b_name = $row['b_name'];
$b_sub = $row['b_sub'];
$b_con = $row['b_con'];
$b_hints = $row['b_hints'];
$b_upload = $row['b_upload'];
$b_own_id = $row['b_own_id'];
$b_url = $row['b_url'];
$b_post_time = $row['b_post_time'];
$b_edit_time = $row['b_edit_time'];
$b_edit_con = $row['b_edit_con'];
$b_is_intranet = $row['b_is_intranet'];
$teacher_sn = $row['teacher_sn'];
$b_kind = $row['b_kind'];
if($b_id==""){
	$main="<table align='center' border='1' cellPadding='3' cellSpacing='0' width='100%'><tr bgColor='#f1f5cd'><td ><font size=5 face=標楷體>
                $note_s[$entry]
	</font></td></tr></table>";

}else{ 
	 //只有教材內容才可增加細目
	if($entry=='b' or $entry=='d'){ 
		if($b_kind == $entry ){
			if($_SESSION[session_who]=="教師" or $entry=='d')
				$add="<input type='submit'  value='新增'>";
		}else{
			$add="<a href='etoe.php?unit=$unit&entry=$entry&m_id=$bk_id' >回上頁</a>";
		}
	}
	$main="<form  method='post' action=board_r.php>";
	$main.="<table align='center' border='1' cellPadding='3' cellSpacing='0' width='100%'><tr bgColor='#f1f5cd'><td >
		<a href=javascript:fullwin('full.php?b_id=$b_id')><img  src='images/full.gif'  border='0' alt='全螢幕'></a>
 		<a href=javascript:fullwin('read.php?b_id=$b_id')><img  src='images/read.gif'  border='0' alt='閱讀'></a>	
		<font size=5 face=標楷體> $b_sub </font> $add </td></tr>
		<input type='hidden' name='u_id' value= $b_id >
		<input type='hidden' name='entry' value=$entry>
		<input type='hidden' name='unit' value=$unit>
		<input type='hidden' name='b_kind' value=$b_kind>
		<input type='hidden' name='s_title' value='$b_sub' >
		<tr bgColor='#ffffff'><td ><p style='line-height: 150%'>" ;
	$main.= nl2br($b_con) ."</td></tr>";

	if($b_url != ''){
		if (eregi('http://',$b_url)) 
			$b_url ="<a href= $b_url target=window>相關網址</a>";			
        	
              if (eregi('ftp://',$b_url)) 
			$b_url ="<a href=$b_url target=window>相關網址</a>";  
  
		$main.="<tr bgColor='#ffffff'><td > $b_url </td></tr>";
	}
	if($b_upload != ''){ 
		if (substr($b_upload,-3)=='jpg' or substr($b_upload,-3)=='JPG'or substr($b_upload,-3)=='gif' or substr($b_upload,-3)=='png'){		
			$main.="<tr><td  ><img  src='" . $download_path  .$b_id. "_" .$b_upload . "'></td></tr>";
		}elseif(substr($b_upload,-3)=='wav' or substr($b_upload,-3)=='WAV'or substr($b_upload,-3)=='mp3' or substr($b_upload,-3)=='MP3' or substr($b_upload,-3)=='mid' or substr($b_upload,-3)=='MID'){		
			$talk= $b_id. "_" .$b_upload;
			$main .= "<tr><td><a href=javascript:Play('$talk');><img  border=0 src='images/speak.gif'  width=22 height=18 align=middle ></a></td></tr>" ;

		}else{
			$main.="<tr bgColor='$bgcolor'>
			<td  >檔案下載：<a href=". $download_path . $b_id."_" .$b_upload. " >$b_upload</a></td>
				</tr>";
		}
	}

	if($teacher_sn ==$_SESSION[session_tea_sn] || checkid($_SERVER[SCRIPT_FILENAME],1)){
			$b_text= "　<a href='board_edit_c.php?b_id=$b_id&unit=$unit&o_bk_id=$u_id&entry=$entry' >修改</a>　　<a href='board_delete_c.php?b_id=$b_id&unit=$unit&entry=$entry'>刪除</a>" ;
	}


$edit='<font color=red>';
if($b_edit_time != ''){
	if($b_edit_id !=$b_own_id) 
		$edit.=$b_edit_id;
 	$edit.="於 $b_edit_time 第  $b_edit_con 次修正";	   
}
$edit.='</font>';	
$main.="<tr bgColor='#ffffff' >
		<td align=right>
			  <font size=2> $b_text  　　$b_title $b_name 於 $b_post_time 發布　$edit </font>
		</td>
	</tr>
</table>";
//細目標題
if($entry=='b' or $entry=='d'){  //教材內容及互動討論才有細目
	$sqlstr = "select * from unit_c where  bk_id='$b_id'  and b_days > 0 and b_kind='' order by  b_open_date desc " ;	
	$result =$CONN->Execute($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256) ;	
	$main.="<table align='center'  border='1' cellpadding='3' cellspacing='0' width='100%'  >";
	while ($row = $result->FetchRow() ) {    		
    		$b_sub = $row["b_sub"] ;   
    		$b_id = $row["b_id"] ;  
		$b_title = $row['b_title'];
		$b_name = $row['b_name'];
		$b_post_time = $row['b_post_time'];

		$main.="<tr bgcolor='#D0FFB9'>
			<td width='60%'><a href=etoe.php?entry=$entry&unit=$unit&m_id=$b_id > $b_sub </a></td>
			<td width='40%'><font size=2>  $b_title $b_name 於 $b_post_time 發布</font></td></tr>";
	}
	$main.="</table>"; 

}
}
?>
