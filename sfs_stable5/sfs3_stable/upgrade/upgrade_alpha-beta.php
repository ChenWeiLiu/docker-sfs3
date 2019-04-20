<?php
include "../include/config.php";
require_once "../include/sfs_core_globals.php";
include "../include/sfs_case_PLlib.php";
include "../include/sfs_case_sql.php";
include "update_function.php";
set_time_limit(180) ;
$postBtn = "開始升級";
$alpha_arr = array("0325"=>"sfs-3.0.a4-20030325","0408"=>"sfs-3.0.a4-20030408","0415"=>"sfs-3.0.a4-20030415","0424"=>"sfs-3.0.a4-20030424","0428"=>"sfs-3.0.a4-20030428");


if ($_POST[do_key] == $postBtn) {
	if($_POST[sfs_var]!='') {
		$sql_str = "./alpha/".$_POST[sfs_var].".sql";
		$do_this = false;
		switch ($_POST[sfs_var]){
			case "0325":
				$do_this = !check_table($mysql_db,$conID,"sfs_module");
			break;
			case "0408":
			case "0415":
				$do_this = !check_table($mysql_db,$conID,"grad_stud");
			break;				
			case "0424":
				$do_this = !check_table($mysql_db,$conID,"school_day");
			break;
			case "0428":
				$do_this = !check_table($mysql_db,$conID,"board");
			break;


		}
		if ($do_this) {
			echo "<hr>橫線之下如果沒有錯誤訊息,表示安裝成功!!<br>,如果有錯誤訊息,可能的原因是你已經升級或更動了資料表<br>你還是可以將上列的sql 語法加入 或參考 <a href=\"$sql_str\" target=_blank>$sql_str</a> 來更新資料庫" ;

			echo "<p><a href=\"$_SERVER[PHP_SELF]\">重新執行程式</a><hr>";
			$sql_query = read_file($sql_str);
			do_sql($sql_query,$conID);
		}
		else {
			echo "<hr> ".$alpha_arr[$_POST[sfs_var]]." ,已經升級了!!";
		}
			echo "</body></html>";
		exit;
	}
}
?>
<html>
<meta http-equiv="Content-Type" content="text/html; Charset=Big5">
<body>
<form name=myform method=post action="<?php $_SERVER[PHP_SELF] ?>">
<table width="100%" cellspacing=1 cellpadding=2 bgcolor="blue">
<table width="95%" cellspacing=1 cellpadding=2 align="center" bgcolor="#fcffc6">
<tr>
<td><h2>SFS3 alpha 升級至 Beta 版程式</h2><hr></td>
</tr>
<tr>
<td>
	<pre>
	升級說明:

	本程式提供 SFS3.0 alpha4 升級至 beta1 資料庫轉換程式,
	如果您的 sfs3 alpha 版已正式上線使用,建議執行本程式升級前
	先<font color=red size=4>備份你的資料庫</font>,本程式僅提供轉換
	alpha 版釋出時的初始的資料庫格式,如果您<font color=red size=4>已了解上述說明</font>,
	請檢查您的版本,準備升級至 SFS3.0 Beta1 !!
	</pre>

 </td>
</tr>
<tr>
<td>
選擇 alpha 的版本升級 &nbsp;&nbsp;
<?php
	$sel = new drop_select();
	$sel->s_name = "sfs_var";
	$sel->arr = $alpha_arr;
	$sel->top_option = "選擇 SFS3 alpha 版本";
	$sel->do_select();		
?>
</td>
</tr>
<tr>
<td> <input type=submit name="do_key" value="<?php echo $postBtn ?>">
</table>
</table>
</form>
</body>
</html>


<?php
function do_sql($sql_query,$conID) {
if ($sql_query != '') {
    $pieces       = array();
    PMA_splitSqlFile_2($pieces, $sql_query, PMA_MYSQL_INT_VERSION);
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
    if ($pieces_count == 1 && !empty($pieces[0]) ) {
        // sql.php will stripslash the query if get_magic_quotes_gpc
        if (get_magic_quotes_gpc() == 1) {
            $sql_query = addslashes($pieces[0]);
        } else {
            $sql_query = $pieces[0];
        }
        if (eregi('^(DROP|CREATE)[[:space:]]+(IF EXISTS[[:space:]]+)?(TABLE|DATABASE)[[:space:]]+(.+)', $sql_query)) {
            $reload = 1;
        }
       // include('./sql.php');
        exit();
    }

    // Runs multiple queries
    else  {
        for ($i = 0; $i < $pieces_count; $i++) {
            $a_sql_query = $pieces[$i];
            $result = mysql_query($a_sql_query,$conID);
            if ($result == FALSE) { // readdump failed
                $my_die = $a_sql_query;
		echo "錯誤的語法 :<br>$my_die <BR><br>";
                //break;
            }
            if (!isset($reload) && eregi('^(DROP|CREATE)[[:space:]]+(IF EXISTS[[:space:]]+)?(TABLE|DATABASE)[[:space:]]+(.+)', $a_sql_query)) {
                $reload = 1;
            }
        } // end for
    } // end else if
    unset($pieces);
} // end if

}


?>
