<?php	
// $Id: index.php 5310 2009-01-10 07:57:56Z smallduh $
//取得設定檔
include_once "config.php";
set_time_limit(0);
//驗證是否登入
sfs_check(); 
//製作選單 ( $school_menu_p陣列設定於 module-cfg.php )
$tool_bar=&make_menu($school_menu_p);
//讀取目前操作的老師有沒有管理權 , 搭配 module-cfg.php 裡的設定
$module_manager=checkid($_SERVER['SCRIPT_FILENAME'],1);

/**************** 開始秀出網頁 ******************/
//秀出 SFS3 標題
//將smarty輸出的資料先cache住
ob_start();
head(mb_convert_encoding("轉換前後資料庫比對","BIG-5","UTF-8"));
//列出選單
echo $tool_bar;
$header_menu=ob_get_contents();
ob_end_clean();
$header_menu=mb_convert_encoding($header_menu,"UTF-8","BIG-5");
header("Content-Type:text/html; charset=utf-8");
echo $header_menu;

$sql="show tables";
$res=$CONN->Execute($sql) or die("Error! sql=".$sql);
$row=$res->getRows();


$tables_all_number=count($row);    //資料表總數


echo "資料表總數：".$tables_all_number."<br><br>";


if ($_POST['act']=='start') {

    $root_pass=$_POST['root_pass'];
    $utf8_db=$_POST['utf8_db'];
    $utf8_db_encode=$_POST['utf8_db_encode'];

    if ($root_pass=='' or $utf8_db=='')  exit();


    //Create connection
    $conn = new mysqli($mysql_host, 'root', $root_pass);

    // Check connection
    if ($conn->connect_error) {
          die("資料庫連線錯誤! " . $conn->connect_error);
    }


/*** 開始依序處理每個 table  *****/
    $table_row= "
     <table border=1 style='border-collapse:collapse;border-color:#cccccc'>
     <tr style='background-color: #000f40;color:#FFFFFF'>
        <td align='center' style='padding:10px'>序號</td>
        <td align='center' style='padding:10px'>資料表名稱</td>
        <td align='center' style='padding:10px'>轉換前資料庫 $mysql_db 的資料數 </td>
        <td align='center' style='padding:10px'>轉換後產出資料庫 $utf8_db 的資料數 </td>
     </tr>
    ";
    foreach ($row as $table_v) {
        $i++;
        $table_name = $table_v[0];    //資料表名稱
        $table_row_h="
            <td>$i</td>
            <td>$table_name</td>
        ";

        //不處理的資料表
        if ($table_name=='student_view' or $table_name=='teacher_course_view' or $table_name=='teacher_post_view') {
            continue;
        }


        //切換回 sfs3 資料庫
        if( !mysqli_select_db($conn, $mysql_db)) {
            die ("無法選擇資料庫 $mysql_db");
        }

        //mysqli_query( $conn, "SET NAMES 'latin1'");

        //取得編碼  //編碼 $row_table['Collation']
        $sql_table = "SHOW TABLE STATUS WHERE NAME LIKE '$table_name';";
        $result = mysqli_query($conn, $sql_table);
        $row_table = mysqli_fetch_array($result,MYSQL_ASSOC);
        //echo "<pre>";
        //print_r($row_table);
        $C=explode("_",$row_table['Collation']);
        $CHAR=$C[0];  //編碼

        mysqli_query( $conn, "SET NAMES '{$CHAR}'");
        //取出 sfs3 這個 table 的資料總數
        $sql="select count(*) from `$table_name`";
        $res_count = mysqli_query($conn, $sql);
        $row_count=mysqli_fetch_array($res_count,MYSQL_NUM);
        $total=$row_count[0];

        //切換回 UTF-8 資料庫
        if( !mysqli_select_db($conn, $utf8_db)) {
            die ("無法選擇資料庫 $utf8_db");
        }
        mysqli_query( $conn, "SET NAMES '$utf8_db_encode'");

        //取出 sfs3 這個 table 的資料總數
        $sql="select count(*) from `$table_name`";
        $if_remove=0;
        foreach ($remove_array as $R) {
            if (in_array($table_name,$R['database'])) {
                $if_remove=1;
                break;
            }
        }
        if ($if_remove) {
            $total_utf8="<span style='color:#666666'>不維護的模組資料表，未進行轉換!</span>";
            $bg="#CCFFCC";
        } else {
            if ($res_count = mysqli_query($conn, $sql)) {
                $row_count=mysqli_fetch_array($res_count,MYSQL_NUM);
                $total_utf8=$row_count[0];
                if ($total==$total_utf8) {
                    $bg="#FFFFFF";
                } else {
                    $bg="#CCFFFF";
                }
            } else {
                $total_utf8="<span style='color:#FF0000'>無此資料表，可能轉換未成功!</span>";
                $bg="#FFCCCC";
            }

        }

        $table_row.="
          <tr style='background-color: $bg'>
                $table_row_h
              <td align='center'>$total</td>
              <td align='center'>$total_utf8</td>
          </tr>
        ";

    } // end foreach

    $table_row.="</table>";

    echo $table_row;


} else {
    ?>
    <form name="myform" method="post" action="<?php echo $_SERVER['PHP_SELF']?>">
        <input type="hidden" name="act" value="">
        <table border="0">
            <tr>
                <td>請輸入要比對的 UTF-8 資料庫名稱</td>
                <td><input type="text" name="utf8_db" value="sfs3_utf8"></td>
            </tr>
            <tr>
                <td>選擇 UTF-8 資料庫編碼</td>
                <td>
                    <select size="1" name="utf8_db_encode">
                        <option value="utf8mb4">utf8mb4</option>
                        <option value="utf8">utf8</option>
                    </select>

            </tr>
            <tr>
                <td>請輸入 MySQL root 密碼</td>
                <td><input type="password" name="root_pass" value=""></td>
            </tr>

            <tr>
                <td><input type="button" value="開始執行" onclick="document.myform.act.value='start';document.myform.submit()"></td>
                <td></td>
            </tr>

        </table>

    </form>
    <?php
}

 ?>

 
<?php
//  --程式檔尾
foot();


