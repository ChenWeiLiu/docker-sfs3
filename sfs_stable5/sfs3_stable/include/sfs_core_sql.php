<?php

// $Id: sfs_core_sql.php 6792 2012-06-08 07:56:28Z smallduh $

beginRequest();

// $Id: sfs_core_sql.php 6792 2012-06-08 07:56:28Z smallduh $
function beginRequest(){

		//$strip=get_magic_quotes_gpc();
		if(isset($_GET))
			array_walk($_GET,'pradoEncodeData');
		if(isset($_POST))
			array_walk($_POST,'pradoEncodeData');
		if(isset($_REQUEST))
			array_walk($_REQUEST,'pradoEncodeData');
	}

function pradoEncodeData(&$data,$key=null) {
	if(is_array($data)) {
		array_walk($data,'pradoEncodeData');
	} else {
	
		if (!get_magic_quotes_gpc())
			$data=addslashes($data);
		//加入將 \' 換成空字串 2008.04.10 by brucelyc
		//	$data=strtr($data,array("\'"=>'','&'=>'&amp;','"'=>'&quot;',"'"=>'&#039;','<'=>'&lt;','>'=>'&gt;'));
	}
}

//執行 sql 語法〈尚未用到〉
function sql_execute_file($sql_query) {
	global $CONN;
	if ($sql_query != '') {
		$pieces = array();
		sfs_splitSqlFile($pieces, $sql_query, PMA_MYSQL_INT_VERSION);
		$pieces_count = count($pieces);

		if ($sql_file != 'none' && $pieces_count > 10) {
			$sql_query_cpy = $sql_query = '';
		} else {
			$sql_query_cpy = implode(";\n", $pieces) . ';';
		}

		// Only one query to run
		if ($pieces_count == 1 && !empty($pieces[0]) ) {
		// sql.php will stripslash the query if get_magic_quotes_gpc
			if (get_magic_quotes_gpc() == 1) {
				$sql_query = addslashes($pieces[0]);
			} else {
				$sql_query = $pieces[0];
			}
			$result = $CONN->Execute($sql_query) or trigger_error("版本更新失敗！ $sql_query", E_USER_ERROR);
		}
		// Runs multiple queries
		else  {
			for ($i = 0; $i < $pieces_count; $i++) {
				$a_sql_query = $pieces[$i];
				$result = $CONN->Execute($a_sql_query);
				if ($result == FALSE) { // readdump failed
					$my_die = $a_sql_query;
					echo "錯誤的語法 :$my_die ";
					//break;
				}
			} // end for
		} // end else if
		unset($pieces);
	} // end if
}


//sql 字串拆解〈尚未用到〉
function sfs_splitSqlFile(&$ret, $sql, $release){
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

    } // end for

    // add any rest to the returned array
    if (!empty($sql) && ereg('[^[:space:]]+', $sql)) {
        $ret[] = $sql;
    }

    return TRUE;
} // end of the 'PMA_splitSqlFile()' function



//?〈尚未用到〉
function mysql_to_epoch ($datestr){
  list($year,$month,$day,$hour,$minute,$second)	= split("([^0-9])",$datestr);
  return mktime($hour,$minute,$second,$month,$day,$year);
}

function mysql_date() {
  return date("Y-m-d H:i:s", time());
}

//自動判別有沒有開 magic_quota , PHP 5.4 後全部都取消, 傳回 false
function SafeAddSlashes($STR){
 if (!get_magic_quotes_gpc()) {
    if (is_array($STR)) {
       foreach ($STR as $Key => $DATA) {
	 $STR[$Key]=addslashes($DATA);
       }
    }else{
     $STR=addslashes($STR);
    }
 } //end if
 return $STR;
}

?>