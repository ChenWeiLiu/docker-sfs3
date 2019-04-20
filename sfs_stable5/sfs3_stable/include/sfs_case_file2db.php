<?php

// $Id: sfs_case_file2db.php 5391 2009-02-10 11:42:51Z hami $
// 取代 dbfile_function.php

//上傳檔案到檔案資料庫中
function uploadfile($userfile,$userfile_type,$userfile_size,$userfile_name,$description,$eduer_unit_sn,$category_sn,$col_name,$col_sn,$unit_sn,$enable){
	global $CONN,$tmp_path;

	// 確定連線成立
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);

	if(!empty($userfile)){
		$mode=($_SERVER['HTTP_HOST'] == "localhost")?"rb":"r";
		$data = Base64_Encode(addslashes(fread(fopen($tmp_path.'/'.basename($userfile), $mode), filesize($userfile))));
		$str="INSERT INTO file_db (eduer_unit_sn,filename,main_data,description,type,size,date,category_sn,col_name,col_sn,unit_sn,enable) VALUES ('$eduer_unit_sn','$userfile_name','$data','$description','$userfile_type','$userfile_size',now(),'$category_sn','$col_name','$col_sn','$unit_sn','$enable')";
		$CONN->Execute($str) or trigger_error($str, E_USER_ERROR);
		$fsn=mysql_insert_id();
	}
	return $fsn;
}


//取得資料庫中的檔案
//注意! 本函式已改為 return by reference
function &getFormFile($col_name,$col_sn){
	global $FILE_DB,$FILE_TBL,$CONN;
	// 確定連線成立
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);

	$str="select FSN,filename,size,date,enable from file_db where col_name='$col_name' and col_sn=$col_sn order by date desc";
	$recordSet=$CONN->Execute($str) or trigger_error($str, E_USER_ERROR);

	while(list($FSN,$filename,$size,$date,$enable)=$recordSet->FetchRow()){
		$fname=explode(".",$filename);
		$n=sizeof($fname)-1;
		$pic=file_pic($fname[$n]);
		$size=chkFileSize($size);
		$f.="<tr bgcolor='#FFFFFF'><td><input type='hidden' name='FSN[]' value='$FSN'><img src='images/$pic' hspace='4' border='0'><a name='ff_$i' id='ff_$i' href='file.php?FSN=$FSN' target='_balnk'>$filename</a></td><td>( <font color='#800000'>$size</font> )</td><td>$date</td></tr>";
	}
	$main="
	<table cellspacing='0' cellpadding='2' bgcolor='#C0C0C0'>$f</table>
	";
	return $main;
}

//轉換檔案大小
function chkFileSize($size=""){
	if($size >= 1048576){
		$fSize=round($size/1048576,$p)." MB";
	}elseif($size >= 1024){
		$fSize=round($size/1024,$p)." KB";
	}else{
		$fSize=$size." byte";
	}
	return $fSize;
}


//判別格式圖檔
function file_pic($file){
	switch($file){
		case "doc":
		return "ex_doc.gif";
		break;
		case "pdf":
		return "ex_pdf.gif";
		break;
		case "htm":
		return "ex_htm.gif";
		break;
		case "html":
		return "ex_htm.gif";
		break;
		case "gif":
		return "ex_gif.gif";
		break;
		case "txt":
		return "ex_txt.gif";
		break;
		case "jpg":
		return "ex_jpg.gif";
		break;
		case "xls":
		return "ex_xls.gif";
		break;
		case "zip":
		return "ex_zip.gif";
		break;
		case "rtf":
		return "ex_doc.gif";
		break;
		case "ppt":
		return "ex_ppt.gif";
		break;
		case "exe":
		return "ex_exe.gif";
		break;
		case "png":
		return "ex_png.gif";
		break;
		case "wmv":
		return "ex_wmv.gif";
		break;
		case "avi":
		return "ex_avi.gif";
		break;
	}
	return "ex_none.gif";
}

?>
