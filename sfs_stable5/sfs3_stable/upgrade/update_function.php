<?php

//昇級函式
function showtitle($title) {
	global $iii;
	$iii=0;
	echo "<br>-------------------------------------------------<br>";
	echo "<b>$title ..... 請稍待!</b><br>";
}

//轉換住址


function change_addr($addr) {
	global $addr ;
	//縣市	
	$temp_str = split_str("縣",1);
	if ($temp_str =="") 
		$temp_str = split_str("縣",1);	
	if ($temp_str =="") 
		$temp_str = split_str("市",1);
	$res[]=$temp_str ;	
	      	
      	//鄉鎮	
	$temp_str = split_str("鄉",1);
	if ($temp_str =="") 
		$temp_str = split_str("鎮",1);
	
	if ($temp_str =="") 
		$temp_str = split_str("市",1);

	$res[]=$temp_str ;	     	
      	
	//村里	
	$temp_str = split_str("村",1);
	if ($temp_str =="") 
		$temp_str = split_str("里",1);
		
	if ($temp_str =="") 
		$temp_str = split_str("區",1);
	
	$res[]=$temp_str ;	
	
	//鄰
	$res[] = split_str("鄰");
	
	//路
	$temp_str = split_str("路",1);
	if ($temp_str =="") 
		$temp_str = split_str("街",1);
	
	$res[] = $temp_str;

      	//段
	$res[] = split_str("段");
	
      	//巷
	$res[] = split_str("巷");
	
	//弄
	$res[] = split_str("弄");
      	//之
	$res[] = split_str("之");
      	
      	//號
	$res[] = split_str("號");
	
	//樓
	$res[] = split_str("之");
		
	//樓之
	if ($addr != "")
		$temp_str = $addr;
	else
		$temp_str ="";
		
	$res[]=$temp_str ;	
      		      	
      	return $res;
}

function split_str($str,$last=0 ) {
	global $addr;
      	$temp = explode ($str, $addr);	
	if (count($temp)==1 )
		return  "" ;
      	else if ($last) {
      		$addr = substr($addr,strlen($temp[0])+strlen($str));
      		return  trim($temp[0]).$str;
      	}
      	else {
      		$addr = substr($addr,strlen($temp[0])+strlen($str));
      		return  trim($temp[0]);
      	}
      	
}

//檢查datacase 是否存在
function check_db($db_name) {
	global $Mysql_db,$conID2;
	$is_add = false;
	$result = mysql_list_dbs ($conID2);
	$num = mysql_num_rows($result);
	//sfs 現有tables
	for ($i =0 ;$i<$num;$i++)
		$sfs_db[] = mysql_tablename($result,$i);
		
	for ($i=0;$i<count($sfs_db);$i++)
		if ($sfs_db[$i] == $db_name){
			$is_add = true;
			break;
		}		
	return  $is_add;
}


//sql 字串處理
function split_sql($sql)
{
    $sql = trim($sql);
    $sql = ereg_replace("#[^\n]*\n", "", $sql);
    $buffer = array();
    $ret = array();
    $in_string = false;

    for($i=0; $i<strlen($sql)-1; $i++)
    {
         if($sql[$i] == ";" && !$in_string)
        {
            $ret[] = substr($sql, 0, $i);
            $sql = substr($sql, $i + 1);
            $i = 0;
        }

        if($in_string && ($sql[$i] == $in_string) && $buffer[0] != "\\")
        {
             $in_string = false;
        }
        elseif(!$in_string && ($sql[$i] == "\"" || $sql[$i] == "'") && (!isset($buffer[0]) || $buffer[0] != "\\"))
        {
             $in_string = $sql[$i];
        }
        if(isset($buffer[1]))
        {
            $buffer[0] = $buffer[1];
        }
        $buffer[1] = $sql[$i];
     }

    if(!empty($sql))
    {
        $ret[] = $sql;
    }

    return($ret);
}



//(基本資料表)
function list_sfs() {
	return  array ("pro_check","pro_check_stu","pro_kind","school_base","school_room","seme_class","stud_addr","stud_base","stud_behabior","stud_brother_sister","stud_domicile","stud_guid_case","stud_guid_case_list","stud_guid_case_u","stud_guidance","stud_kinfolk","stud_move","stud_psy_tests","stud_score","stud_seme","stud_tea_parent","teacher_base","teacher_connect","teacher_post","teacher_subject","teacher_title");
    		    		
}

function check_sfsbase($table_name) {
	$pp= list_sfs();
	$flag = false;
	for ($i=0;$i<count($pp); $i++)
		if ($pp[$i]==$table_name) {
			$flag = true;
			break;
		}
	return $flag;
}
//檢查資料表
function check_table($mysql_db,$conID,$table_name) {
	$is_add = false;
	$query = "show tables from $mysql_db ";
	$result = mysql_query($query,$conID) or die ($query);	
	while ($row = mysql_fetch_row($result)) {
		if($row[0]==$table_name) {
			$is_add =true;
			break;
		}
	}		
	return  $is_add;
}



//檢查欄位
function check_field($mysql_db,$conID,$table_name,$field_name) {
	
	$is_add = false;
	$result = mysql_list_fields($mysql_db,$table_name,$conID);
	$num = mysql_num_fields($result);
	for($i=0; $i< $num;$i++) 
		$temp[]=mysql_field_name($result,$i);
		
	for ($i=0;$i<count($temp);$i++) 
		if ($temp[$i] == $field_name){
			$is_add = true;
			break;
		}		
	return  $is_add;
}

function utime ()
	{
		$time = explode( " ", microtime());
		$usec = (double)$time[0];
		$sec = (double)$time[1];
		return $sec + $usec;
    }

/* 重整目錄名稱 */

function get_dir($dir="")
{
	global $root_dir,$this_path,$curr_file,$get,$ap_array;
	$curr_dir = "$root_dir$dir";
	$handle=@opendir($curr_dir);
	while ($file = readdir($handle)){
		if ($file != "." and $file != ".." and is_display_path($file)){
			chdir($curr_dir); 
			if (!is_dir($file))
				continue;
			$ap_array[] = "$dir/$file";
			get_dir("$dir/$file");
		}
	}
	@closedir($handle); 
	return $ap_array;
}



//找出不存在的設定
function check_err_set($delkey=0) {
	global $CONN,$root_dir,$SFS_PATH,$curr_dir;
 	$root_dir = $SFS_PATH;
	$curr_dir = $root_dir.$dir;
	//取得程式目錄
	$ap_array = get_dir();
	$query = "SELECT store_path,pro_kind_name ,pro_kind_id FROM pro_kind ";
	$result = $CONN->Execute($query);
  
	while (!$result->EOF){
		$flag = 0;
		if (count($ap_array)>0){
			reset($ap_array);
			while (list ($key, $val) = each ($ap_array)){
				if(substr($val,1) == $result->fields[0]){
					$flag = 1;
					break;
				}
			}
		}
		if (!$flag){ // 實體目錄和資料庫不符合
			$query= "delete from pro_kind where pro_kind_id='".$result->fields[2]."'";
			$CONN->Execute($query);
			$query= "delete from  pro_check where pro_kind_id=".$result->fields[2]."'";
			$CONN->Execute($query);
			$query= "delete from pro_check_stu where pro_kind_id=".$result->fields[2]."'";
			$CONN->Execute($query);
			$err_path[]= $result->fields[0]."--".$result->fields[1];
			$i++;
		}
		$result->MoveNext();
	}	
	return $err_path;
}

//重新設定程式
function reset_pro($begin_p) {
	global $CONN;
	$query = "select pro_kind_id,store_path from pro_kind where pro_parent ='$begin_p' ";
	$res = $CONN->Execute($query) or die($query);
	while (!$res->EOF) {
		$temp_b = $res->fields[0];
		$temp_store = $res->fields[1];
		$query2 = "update pro_kind set pro_parent='$temp_b' where store_path like '$temp_store/%'";
		$CONN->Execute($query2) or die ($query2);
		//echo $query2."<BR>";
		reset_pro($temp_b);
		$res->MoveNext();
	}
}

function PMA_splitSqlFile_2(&$ret, $sql, $release)
{
    $sql          = trim($sql);
    $sql_len      = strlen($sql);
    $char         = '';
    $string_start = '';
    $in_string    = FALSE;

    for ($i = 0; $i < $sql_len; ++$i) {
        $char = $sql[$i];

        // We are in a string, check for not escaped end of strings except for
        // backquotes that can't be escaped
        if ($in_string) {
            for (;;) {
                $i         = strpos($sql, $string_start, $i);
                // No end of string found -> add the current substring to the
                // returned array
                if (!$i) {
                    $ret[] = $sql;
                    return TRUE;
                }
                // Backquotes or no backslashes before quotes: it's indeed the
                // end of the string -> exit the loop
                else if ($string_start == '`' || $sql[$i-1] != '\\') {
                    $string_start      = '';
                    $in_string         = FALSE;
                    break;
                }
                // one or more Backslashes before the presumed end of string...
                else {
                    // ... first checks for escaped backslashes
                    $j                     = 2;
                    $escaped_backslash     = FALSE;
                    while ($i-$j > 0 && $sql[$i-$j] == '\\') {
                        $escaped_backslash = !$escaped_backslash;
                        $j++;
                    }
                    // ... if escaped backslashes: it's really the end of the
                    // string -> exit the loop
                    if ($escaped_backslash) {
                        $string_start  = '';
                        $in_string     = FALSE;
                        break;
                    }
                    // ... else loop
                    else {
                        $i++;
                    }
                } // end if...elseif...else
            } // end for
        } // end if (in string)

        // We are not in a string, first check for delimiter...
        else if ($char == ';') {
            // if delimiter found, add the parsed part to the returned array
            $ret[]      = substr($sql, 0, $i);
            $sql        = ltrim(substr($sql, min($i + 1, $sql_len)));
            $sql_len    = strlen($sql);
            if ($sql_len) {
                $i      = -1;
            } else {
                // The submited statement(s) end(s) here
                return TRUE;
            }
        } // end else if (is delimiter)

        // ... then check for start of a string,...
        else if (($char == '"') || ($char == '\'') || ($char == '`')) {
            $in_string    = TRUE;
            $string_start = $char;
        } // end else if (is start of string)

        // ... for start of a comment (and remove this comment if found)...
        else if ($char == '#'
                 || ($char == ' ' && $i > 1 && $sql[$i-2] . $sql[$i-1] == '--')) {
            // starting position of the comment depends on the comment type
            $start_of_comment = (($sql[$i] == '#') ? $i : $i-2);
            // if no "\n" exits in the remaining string, checks for "\r"
            // (Mac eol style)
            $end_of_comment   = (strpos(' ' . $sql, "\012", $i+2))
                              ? strpos(' ' . $sql, "\012", $i+2)
                              : strpos(' ' . $sql, "\015", $i+2);
            if (!$end_of_comment) {
                // no eol found after '#', add the parsed part to the returned
                // array if required and exit
                if ($start_of_comment > 0) {
                    $ret[]    = trim(substr($sql, 0, $start_of_comment));
                }
                return TRUE;
            } else {
                $sql          = substr($sql, 0, $start_of_comment)
                              . ltrim(substr($sql, $end_of_comment));
                $sql_len      = strlen($sql);
                $i--;
            } // end if...else
        } // end else if (is comment)

        // ... and finally disactivate the "/*!...*/" syntax if MySQL < 3.22.07
        else if ($release < 32270
                 && ($char == '!' && $i > 1  && $sql[$i-2] . $sql[$i-1] == '/*')) {
            $sql[$i] = ' ';
        } // end else if

        // loic1: send a fake header to bypass browser timeout
        header('Expires: 0');
    } // end for

    // add any rest to the returned array
    if (!empty($sql) && ereg('[^[:space:]]+', $sql)) {
        $ret[] = $sql;
    }

    return TRUE;
} // end of the 'PMA_splitSqlFile()' function


//更改 teacher_sn 值

function up_teacher_sn($table_name,$teach_id='teach_id',$sn_name="teacher_sn") {
	global $CONN,$conID,$mysql_db;
	if (! check_field($mysql_db,$conID,$table_name,$sn_name))
		$CONN->Execute(" ALTER TABLE $table_name ADD $sn_name SMALLINT UNSIGNED NOT NULL") or trigger_error("SQL錯誤",E_USER_ERROR);

	$query = "select teacher_sn,teach_id from teacher_base ";
	$res = $CONN->Execute($query);
	while(!$res->EOF){
		$query = "update $table_name set $sn_name=".$res->fields[0]." where $teach_id='".$res->fields[1]."'";
		$CONN->Execute($query) or trigger_error("SQL 語法錯誤<BR>$query", E_USER_ERROR);
		$res->MoveNext();
	}

}

function up_student_sn($table_name,$stud_id='stud_id',$sn_name="student_sn") {
	global $CONN,$conID,$mysql_db;
	if (! check_field($mysql_db,$conID,$table_name,$sn_name))
		$CONN->Execute(" ALTER TABLE $table_name ADD $sn_name SMALLINT UNSIGNED NOT NULL") or trigger_error("SQL錯誤",E_USER_ERROR);

	$query = "select student_sn,stud_id from stud_base ";
	$res = $CONN->Execute($query);
	while(!$res->EOF){
		$query = "update $table_name set $sn_name=".$res->fields[0]." where $stud_id='".$res->fields[1]."'";
		$CONN->Execute($query) or trigger_error("SQL 語法錯誤<BR>$query", E_USER_ERROR);
		$res->MoveNext();
	}

}


?>
