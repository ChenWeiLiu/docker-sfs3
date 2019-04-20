<?php

// $Id: sfs_case_sql.php 7771 2013-11-15 06:39:56Z smallduh $

/*
-----------------------------------------------------------------------
這二個函式修改自 phpMyAdmin 2.2.6 的 read_dump.php

版權: 本程式遵循 phpMyAdmin GPL 版權宣告.

作用: 它可以一次執行多個 sql 命令

目的: 執行 SFS3 的 db/sfs3.sql，以初始化 SFS 的各個必要的資料庫表格

修改者: OLS3 (ols3@www.tnc.edu.tw) 日期: 09/22/2002
-----------------------------------------------------------------------
*/

function run_sql(&$sql_query, $db) {

  if ($sql_query != '') {
    $pieces       = array();
    PMA_splitSqlFile($pieces, $sql_query);
    $pieces_count = count($pieces);

    // Copy of the cleaned sql statement for display purpose only (see near the
    // beginning of "db_details.php" & "tbl_properties.php")
    if ($sql_file != 'none' && $pieces_count > 10) {
         // Be nice with bandwidth...
        $sql_query_cpy = $sql_query = '';
    } else {
        $sql_query_cpy = implode(";\n", $pieces) . ';';
    }

    // Only one query to run
    if ($pieces_count == 1 && !empty($pieces[0]) && $view_bookmark == 0) {
 
        $sql_query = $pieces[0];
 
        if (eregi('^(DROP|CREATE)[[:space:]]+(IF EXISTS[[:space:]]+)?(TABLE|DATABASE)[[:space:]]+(.+)', $sql_query)) {
            $reload = 1;
        }
		if (mysql_select_db($db)) mysql_query($sql_query);
        return;
    }

    // Runs multiple queries
    else if (mysql_select_db($db)) {
        for ($i = 0; $i < $pieces_count; $i++) {
            $a_sql_query = $pieces[$i];
            $result = mysql_query($a_sql_query);
            if ($result == FALSE) { // readdump failed
                $my_die = $a_sql_query;
                break;
            }
            if (!isset($reload) && eregi('^(DROP|CREATE)[[:space:]]+(IF EXISTS[[:space:]]+)?(TABLE|DATABASE)[[:space:]]+(.+)', $a_sql_query)) {
                $reload = 1;
            }
        } // end for
    } // end else if
    unset($pieces);
  } // end if
}

function PMA_splitSqlFile(&$ret, $sql)
{
    $sql          = trim($sql);
    $sql_len      = strlen($sql);
    $char         = '';
    $string_start = '';
    $in_string    = FALSE;
    $time0        = time();

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

             // loic1: send a fake header each 30 sec. to bypass browser timeout
        $time1     = time();
        if ($time1 >= $time0 + 30) {
            $time0 = $time1;
            header('X-pmaPing: Pong');
        } // end if
    } // end for

    // add any rest to the returned array
    if (!empty($sql) && ereg('[^[:space:]]+', $sql)) {
        $ret[] = $sql;
    }

    return TRUE;
} // end of the 'PMA_splitSqlFile()' function


//傳回一個資料表的架構及資料
function table_data($table="",$mark=";#@_@",$add_drop=1,$need_insert=1){
	global $CONN,$mysql_db;
	
	//找出欄位資料
	$sql="SHOW FIELDS FROM $mysql_db.$table";
	$result = mysql_query($sql);

	while ($row = mysql_fetch_array($result)) {
		$row_Default=$row['Default'];
		$row_Field=$row['Field'];

		$not_null=($row['Null']=="YES")?"":"NOT NULL";

		$default=(is_null($row_Default) or $row_Default=="NULL")?"":"default '".$row_Default."'";

		$option[]="\n  ".$row_Field." ".$row['Type']." ".$not_null." ".$default." ".$row['Extra'];
	}

	
	//找出索引
	$sql="SHOW INDEX FROM $table";
	$result = mysql_query($sql);
	$Key_name_array=array();
	while ($key_row = mysql_fetch_array($result)) {

		$Key_name=$key_row['Key_name'];
		$Non_unique=$key_row['Non_unique'];
		$Column_name=$key_row['Column_name'];
		$Comment=$key_row['Comment'];


		if($Key_name=="PRIMARY"){
			$kindex="PRIMARY";
		}elseif($Non_unique=="0" && $Key_name!="PRIMARY"){
			$kindex="UNIQUE";
		}elseif($Comment=="FULLTEXT"){
			$kindex="FULLTEXT";
		}else{
			$kindex="KEY";
		}

		$keykind[$Key_name]=$kindex;
		$key[$Key_name][]=$Column_name;

	}

	reset($key);
	while(list($k,$v)=each($key)){
		$key_value=implode(",",$v);

		if($keykind[$k]=="PRIMARY"){
			$the_key[]="\n  PRIMARY KEY  ($key_value)";
		}elseif($keykind[$k]=="UNIQUE"){
			$the_key[]="\n  UNIQUE KEY $k ($key_value)";
		}elseif($keykind[$k]=="FULLTEXT"){
			$the_key[]="\n  FULLTEXT KEY $k ($key_value)";
		}elseif($keykind[$k]=="KEY"){
			$the_key[]="\n  KEY $k ($key_value)";
		}

	}

	if(sizeof($the_key)>0){
		reset($the_key);
		while(list($k,$v)=each($the_key)){
			array_push($option,$v);
		}
	}


	$FetchField=implode(",",$option);
	
		if($need_insert){
		//找出內容
		$sql="select * from $table";
		$result = mysql_query($sql);
		while ($row = mysql_fetch_row($result)) {
			$row_data="";
			for($i=0;$i<sizeof($row);$i++){
				$row_data[]="'".addslashes($row[$i])."'";
			}
			$insert_data=implode(",",$row_data);
			$insert.="INSERT INTO $table VALUES ($insert_data)$mark\n";
		}
	}else{
		$insert="";
	}
	
	$drop=($add_drop==1)?"DROP TABLE IF EXISTS $table$mark\n":"";
	
	$main=$drop."CREATE TABLE $table ($FetchField\n)$mark\n$insert\n";
	return $main;
}


//變更資料表名稱
function chang_dbname($dbname="",$new_dbname=""){
	global $CONN;
	if(empty($dbname) or empty($new_dbname))user_error("資料表名稱不完整。", 256);
	$str="ALTER table $dbname rename as $new_dbname";
	$CONN->Execute($str) or user_error($str, 256);
	return true;
}
?>
