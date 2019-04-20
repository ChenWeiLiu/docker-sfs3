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

//設定字元處理編碼為 UTF-8
//mb_internal_encoding("UTF-8");

//要處理的特殊字碼
$special_words=array(
    "么\\","娉\\","稞\\","擺\\","功\\","珮\\","鈾\\","黠\\","吒\\","豹\\","暝\\","孀\\",
    "吭\\","崤\\","蓋\\","髏\\","沔\\","淚\\","墦\\","躡\\","坼\\","許\\","穀\\","歿\\",
    "廄\\","閱\\","俞\\","琵\\","璞\\","枯\\","跚\\","餐\\","苒\\","愧\\","縷\\");
$correct_words=array(
    "么","娉","稞","擺","功","珮","鈾","黠","吒","豹","暝","孀",
    "吭","崤","蓋","髏","沔","淚","墦","躡","坼","許","穀","歿",
    "廄","閱","俞","琵","璞","枯","跚","餐","苒","愧","縷");


/**************** 開始秀出網頁 ******************/
//秀出 SFS3 標題
//將smarty輸出的資料先cache住
ob_start();
head();
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

    $tables_number=count($_POST['TABLES']);
    echo "您本次要處理 ".$tables_number." 個資料表 ，每次處理筆數：".$_POST['pre_count']."<br><br>";

    $start_time=date("Y-m-d H:i:s");
    $root_pass=$_POST['root_pass'];
    $utf8_db=$_POST['utf8_db'];

    if ($root_pass=='' or $utf8_db=='')  { exit(); }

    if ($mysql_db==$utf8_db)  { echo "不能原資料庫轉換! 轉換後要存入的位置不可以是 $utf8_db !"; exit(); }


    //Create connection
    $conn = new mysqli($mysql_host, 'root', $root_pass);

    // Check connection
    if ($conn->connect_error) {
          die("Connection failed: " . $conn->connect_error);
    }

    echo "Connected successfully <br><br>";

    //刪除同名 database
    if ($_POST['delete_exist_database']==1) {
        $db_sql="DROP DATABASE IF EXISTS $utf8_db";
        $conn->query($db_sql);

        $db_sql="CREATE DATABASE ".$utf8_db." DEFAULT CHARACTER SET utf8mb4 DEFAULT COLLATE utf8mb4_unicode_ci;";

        if ($conn->query($db_sql) === TRUE) {
            echo "Database created successfully! <br><br>";
        } else {
            echo "Error creating database: " . $conn->error ."<br>";
            echo $_POST['delete_exist_database'];
            $conn->close();
            exit();
        }

    }

    //把buffer 先全部送出
    ob_flush();
    flush();

/*** 開始依序處理每個 table  *****/
    foreach ($_POST['TABLES'] as $table_name) {
        $i++;
        //$table_name = $table_v[0];    //資料表名稱

        //不處理的資料表
        if ($table_name=='student_view' or $table_name=='teacher_course_view' or $table_name=='teacher_post_view') {
            echo "目前處理（" . $i . "/" . $tables_number . "）：" . $table_name . " 略過!<br>";
            continue;
        }

        //僅處理某個 table (檢測時使用 , 需取消註解並指定 table 名稱)  *****************************************************************************************//
        //if ($table_name!='msn_data') continue;

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

        //取得建立 table 的 sql
        $sql_create = "SHOW CREATE TABLE $table_name";
        $res_create = mysqli_query($conn,$sql_create) or die ($sql_create);
        $row_create = mysqli_fetch_array($res_create,MYSQL_ASSOC);
        //echo "<pre>";
        //print_r($row_create);
        //exit();

        //新的 create sql  :
        $O=explode("ENGINE=",$row_create['Create Table']);
        if ($O[0]=="") continue;
        if ($CHAR=='utf8') {
            $O[0]=str_replace("utf8_","utf8mb4_",$O[0]);
        }

        if ($table_name=='file_db' or $table_name=='health_exam_record') {
            $new_create_sql = $O[0] . " ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        } else {
            $new_create_sql = $O[0] . " ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC";
        }

        echo "目前處理（" . $i . "/" . $tables_number . "）：[ {$CHAR} ] " . $table_name . " <br>";
        //把buffer 先全部送出
        ob_flush();
        flush();

        //建立新的資料表
        if( !mysqli_select_db($conn, $utf8_db)) {
            die ("無法選擇資料庫 $utf8_db");
        }
        mysqli_query( $conn, "SET NAMES 'utf8mb4'");

        //刪除目前資料表
        $drop_table_sql="DROP TABLE IF EXISTS `$table_name`";
        mysqli_query($conn, $drop_table_sql) or die("刪除資料表".$table_name."失敗! SQL=".$drop_table_sql);


        //把建立資料表的 sql 編碼
        $new_create_sql=big5_to_utf8($new_create_sql);  //sql 要轉成 utf8

        mysqli_query($conn, $new_create_sql) or die("建立資料表".$table_name."失敗! SQL=".$new_create_sql);

        //把 sfs3 的 table 的欄位資料取出
        if( !mysqli_select_db($conn, $mysql_db)) {
            die ("無法選擇資料庫 $mysql_db");
        }
        mysqli_query( $conn, "SET NAMES '{$CHAR}'");

        //取得所有欄位 , 建立基本 insert sql 語法
        $columns=getColumn($table_name);
        $insert_sql="";
        foreach ($columns as $v) {
            $insert_sql.="`{$v}`,";
        }

        $insert_sql=substr($insert_sql,0,strlen($insert_sql)-1);
        $insert_sql="insert into `{$table_name}` ({$insert_sql}) values ";

        //取出 sfs3 這個 table 的總數
        $sql="select count(*) from `$table_name`";
        $res_count = mysqli_query($conn, $sql);
        $row_count=mysqli_fetch_array($res_count,MYSQL_NUM);
        $total=$row_count[0];
        echo "資料筆數：".$total."筆，目前進度...<span id='check_$table_name'>0</span>";
        //把buffer 先全部送出
        ob_flush();
        flush();
        if ($total>0) {
            //開始取出 sfs3 這個 table 的資料
            $pre_count=$_POST['pre_count'];
            //以下為幾個較特殊的 table ，每筆資料量可能較大，不能一次取太多
            if ($table_name=='jboard_images') {
                if ($pre_count>100) {
                    $pre_count=100;
                    echo "<br> jboard_image 資料量較大，每次處理筆數最多 100 筆<br>";
                }
            }
            if ($table_name=='stud_seme_talk' or $table_name=='jboard_p') {
                if ($pre_count>500) {
                    $pre_count=500;
                    echo "<br> stud_seme_talk 資料量較大，每次處理筆數最多 500 筆<br>";
                }
            }
            //if ($table_name=='comment') {
            //    $pre_count=1;
            //    echo "評語資料庫, 採單筆逐一轉碼, 會比較慢! <br> 目前進度.....<span id='check_comment'>0</span>";
            //}
            //分批處理每次處理 5000筆 ========================================
            $interval = floor($total / $pre_count);

            for ($int_step = 0; $int_step <= $interval; $int_step++) {
                $i_step = $int_step * $pre_count;
                $i_step_show=(($i_step+$pre_count)>$total)?$total:$i_step+$pre_count;
                //echo $i_step."<br>";
                //印出進度
                echo "<Script> $(\"#check_{$table_name}\").html({$i_step_show});</Script>";
                ob_flush();
                flush();
                //切換回 sfs3 資料庫
                if (!mysqli_select_db($conn, $mysql_db)) {
                    die ("無法選擇資料庫 $mysql_db");
                }
                mysqli_query($conn, "SET NAMES '{$CHAR}'");

                $sql = "select * from `$table_name` limit $i_step,$pre_count";
                $res_data = mysqli_query($conn, $sql);

                //切回 utf8 資料庫
                if (!mysqli_select_db($conn, $utf8_db)) {
                    die ("無法選擇資料庫 $utf8_db");
                }
                mysqli_query($conn, "SET NAMES 'utf8mb4'");
                //逐筆處理 整合所有的 value , 一次存入
                $insert_it = "";
                while ($row_data = mysqli_fetch_array($res_data, MYSQL_ASSOC)) {
                    $insert_it .= "(";
                   // $insert_big5.="(";
                    foreach ($columns as $v) {
                        //有 POST 值的才轉碼
                        if ($_POST['TRANS'][$table_name]) {
                            $insert_it .= "'" . addslashes(big5_to_utf8($row_data[$v])) . "',";
                        } else {
                            $insert_it .= "'" . $row_data[$v]. "',";
                        }
                    }
                    $insert_it = substr($insert_it, 0, strlen($insert_it) - 1);
                    $insert_it .= "),";

                } // end while

                $insert_it = substr($insert_it, 0, strlen($insert_it) - 1);

                $insert_it = $insert_sql . $insert_it;

                //處理特殊字
                $insert_it=str_replace($special_words,$correct_words,$insert_it);

                //if ($table_name=='sfs_module') echo $insert_it."<br>";

                //存入 , 如果發生錯誤, 檢查是否為 comment , 若是, 另行單筆處理
                if (!mysqli_query($conn, $insert_it)) {
                    if ($table_name == 'comment') {
                        //另行改用單筆匯入
                        echo "<br><span style='color:#FF0000'> 評語資料庫 `comment`，整批匯入發生錯誤！ </span>";
                        echo "<br> 改用單筆逐一匯入模式，此資料庫由教師個別維護，匯入的資料不保證字碼正確性! <br>";
                        $pre_count = 1;
                        echo "<br> 目前進度.....<span id='check_comment'>0</span>";
                        ob_flush();
                        flush();

                        $interval = floor($total / $pre_count);

                        for ($int_step2 = 0; $int_step2 <= $interval; $int_step2++) {
                            $i_step = $int_step2 * $pre_count;

                            //切換回 sfs3 資料庫
                            if (!mysqli_select_db($conn, $mysql_db)) {
                                die ("無法選擇資料庫 $mysql_db");
                            }
                            mysqli_query($conn, "SET NAMES '{$CHAR}'");

                            $sql = "select * from `$table_name` limit $i_step,$pre_count";
                            $res_data = mysqli_query($conn, $sql);

                            //切回 utf8 資料庫
                            if (!mysqli_select_db($conn, $utf8_db)) {
                                die ("無法選擇資料庫 $utf8_db");
                            }
                            mysqli_query($conn, "SET NAMES 'utf8mb4'");
                            //逐筆處理 整合所有的 value , 一次存入
                            $insert_it = "";
                            while ($row_data = mysqli_fetch_array($res_data, MYSQL_ASSOC)) {
                                $insert_it .= "(";
                                foreach ($columns as $v) {
                                    //有 POST 值的才轉碼
                                    if ($_POST['TRANS'][$table_name]) {
                                        $insert_it .= "'" . addslashes(big5_to_utf8($row_data[$v])) . "',";
                                    } else {
                                        $insert_it .= "'" . $row_data[$v] . "',";
                                    }
                                }
                                $insert_it = substr($insert_it, 0, strlen($insert_it) - 1);
                                $insert_it .= "),";
                            } // end while

                            $insert_it = substr($insert_it, 0, strlen($insert_it) - 1);
                            $insert_it = $insert_sql . $insert_it;

                            //處理特殊字
                            $insert_it = str_replace($special_words, $correct_words, $insert_it);

                            if (!mysqli_query($conn, $insert_it)) {
                                echo "<br> 有一筆資料無法存入，SQL=" . $insert_it . " <br>";
                                ob_flush();
                                flush();
                            } else {

                                //印出進度
                                echo "<Script> $(\"#check_comment\").html({$i_step});</Script>";
                                ob_flush();
                                flush();

                            }
                        } // end for

                        break; //脫離外層迴圈

                    } else {
                        echo "<br> <span style='color:#FF0000'>資料表 $table_name 轉檔失敗! 改採逐筆匯入檢驗單筆!!</span><br>";
                        $success_in_one=1;
                        //改用逐筆檢查
                        for ($int_step2 = $i_step; $int_step2 < $i_step_show; $int_step2++) {
                            //$i_step = $int_step * $pre_count;
                            //$i_step_show=(($i_step+$pre_count)>$total)?$total:$i_step+$pre_count;
                            //echo $i_step."<br>";
                            //印出進度
                            echo "<Script> $(\"#check_{$table_name}\").html({$int_step2});</Script>";
                            ob_flush();
                            flush();
                            //切換回 sfs3 資料庫
                            if (!mysqli_select_db($conn, $mysql_db)) {
                                die ("無法選擇資料庫 $mysql_db");
                            }
                            mysqli_query($conn, "SET NAMES '{$CHAR}'");

                            $sql = "select * from `$table_name` limit $int_step2,1";
                            $res_data = mysqli_query($conn, $sql);

                            //切回 utf8 資料庫
                            if (!mysqli_select_db($conn, $utf8_db)) {
                                die ("無法選擇資料庫 $utf8_db");
                            }
                            mysqli_query($conn, "SET NAMES 'utf8mb4'");
                            //逐筆處理 整合所有的 value , 一次存入
                            $insert_it = "";
                            while ($row_data = mysqli_fetch_array($res_data, MYSQL_ASSOC)) {
                                $insert_it .= "(";
                                // $insert_big5.="(";
                                foreach ($columns as $v) {
                                    //有 POST 值的才轉碼
                                    if ($_POST['TRANS'][$table_name]) {
                                        $insert_it .= "'" . addslashes(big5_to_utf8($row_data[$v])) . "',";
                                    } else {
                                        $insert_it .= "'" . $row_data[$v] . "',";
                                    }
                                }
                                $insert_it = substr($insert_it, 0, strlen($insert_it) - 1);
                                $insert_it .= "),";

                            } // end while

                            $insert_it = substr($insert_it, 0, strlen($insert_it) - 1);

                            $insert_it = $insert_sql . $insert_it;

                            //處理特殊字
                            $insert_it = str_replace($special_words, $correct_words, $insert_it);

                            //存入 , 如果發生錯誤, 檢查是否為 comment , 若是, 另行單筆處理
                            if (!mysqli_query($conn, $insert_it)) {
                                echo "<br> <span style='color:#FF0000'>資料表 $table_name 轉檔失敗! </span><br>SQL=" . $insert_it . "<br>";
                                echo "<br> <span style='color:#FF0000'>請在進行資料表內容維護後，再執行本功能，然後單獨處理此資料表! <br>";
                                echo "<br> 系統繼續進行下一個資料表轉檔!</span><br>";
                                ob_flush();
                                flush();
                                $success_in_one=0;

                                break;
                            }
                        } // end for
                        if ($success_in_one==0) break; //逐筆仍然有錯, 脫離外層迴圈 , 本資料表不再繼續往下處理
                    } // end if ($table_name == 'comment') else
                } // end if (!mysqli_query($conn, $insert_it))

            } // end for

            echo "...完成！<br>";
        } else {
            echo "...略過! <br>";
        }// end if total>0
    } // end foreach
    echo "<br><br>";
    echo "資料庫轉碼完畢! 有問題而略過的資料庫，請在處理原始資料維護後，進行個別勾選轉碼處理！<br>";
    echo "<br>現在進行不維護模組的設定移除!<br>";
    //處理不維護的模組設定
    //切回 utf8 資料庫
    if (!mysqli_select_db($conn, $utf8_db)) {
        die ("無法選擇資料庫 $utf8_db");
    }
    mysqli_query($conn, "SET NAMES 'utf8mb4'");
    foreach ($remove_array as $dirname=>$R) {
        $sql="delete from `sfs_module` where `dirname`='{$dirname}' and kind='模組'";
        echo $sql."<br>";
        mysqli_query($conn, $sql);
        $sql="delete from `pro_module` where `pm_id`='{$dirname}'";
        echo $sql."<br>";
        mysqli_query($conn, $sql);
    }


    echo "全數處理完畢!<br>";
    $end_time=date("Y-m-d H:i:s");
    $total_time=strtotime($end_time)-strtotime($start_time);
    echo "花費時間：".floor($total_time/60)."分".($total_time%60)."秒";
} else {
    $sql="SELECT VERSION() as version";
    $res=$CONN->Execute($sql);
    $version=$res->fields[0];
    ?>
    <form name="myform" method="post" action="<?php echo $_SERVER['PHP_SELF']?>">
        <input type="hidden" name="act" value="">
        <table border="1" style="border-collapse:collapse;background-color: #d3d599;border-color: #000a28;font-size:10pt">
            <tr>
                <td style="padding:10px">
                    系統內的 MySQL 版本：<?php echo $version;?><br><br>
                    為了讓 SFS3U 能支援更多少見的中文字，之後的版本將採用 utf8mb4 編碼。<br><br>
                    注意！您的 MySQL 版本必須 5.5.3 之後才能支援 utf8mb4 編碼！<br>
                    請確認您的 my.cnf 設定的 [mysqld] 區段裡包含以下參數內容(紅字部分)，確保資料能正確轉換為 utf8mb4 編碼。<br><br>

                    [mysqld]<br>
                    .<br>
                    .<br>
                    <span style="color:#FF0000">
                    max_allowed_packet = 100M<br>
                    innodb_file_format = Barracuda <br>
                    innodb_file_format_max = Barracuda <br>
                    innodb_file_per_table = 1 <br>
                    innodb_large_prefix
                        </span>
                    .<br>
                    .<br>
                        <br>

                    此模組功能的運作原理，是產出一個編碼為 utf8mb4 的資料庫，之後您再將此資料庫滙出／匯入到 SFS3U 系統主機內，完成系統轉換。
                    <br>
                    <br>
                </td>
            </tr>
        </table>
        <br>
        <table border="0">
            <tr>
                <td>請輸入要產出的資料庫名稱</td>
                <td><input type="text" name="utf8_db" value="sfs3_utf8mb4"></td>
            </tr>
            <tr>
                <td>請輸入 MySQL root 密碼</td>
                <td><input type="password" name="root_pass" value=""></td>
            </tr>
            <tr>
                <td>每次處理筆數</td>
                <td><input type="text" name="pre_count" value="3000"> (如果處理過程發生程式中斷問題，請減少每次處理筆數)</td>
            </tr>
            <tr>
                <td valign="top" align="right"><input type="checkbox" name="delete_exist_database" value="1"></td>
                <td valign="top">刪除並重建已存在的同名資料庫<br>(要全部清除重建或由程式自行建立一個新的資料庫則必須打勾)</td>
            </tr>
            <tr>
                <td colspan="2">
                    <table border="1" style="border-collapse:collapse;border-color:#AAAAAA">
                        <tr style="background-color: #e4ffe5">
                            <td>序號</td>
                            <td>資料表名稱</td>
                            <td>資料表編碼</td>
                            <td>重建過程是否轉碼</td>
                            <td>確認要處理此資料表？<input type="checkbox" id="check_all_table" name="check_all_table" checked onclick="check_tag()"></td>
                        </tr>

                    <?php
                    foreach ($row as $table_v) {
                        $i++;
                        $table_name = $table_v[0];    //資料表名稱
                        //取得編碼  //編碼 $row_table['Collation']
                        $sql_table = "SHOW TABLE STATUS WHERE NAME LIKE '$table_name';";
                        $result =$CONN->Execute($sql_table);
                        $row_table = $result->getrows();
                        //echo "<pre>";
                        //print_r($row_table);
                        if (isset($row_table[0]['Collation'])) {
                            $C = explode("_", $row_table[0]['Collation']);
                            $CHAR = $C[0];  //編碼
                            $if_remove=0;
                            foreach ($remove_array as $R) {
                                if (in_array($table_name,$R['database'])) {
                                    $if_remove=1;
                                    break;
                                }
                            }
                            ?>
                            <tr>
                                <td><?php echo $i; ?></td>
                                <td><?php echo $table_name; ?></td>
                                <td align="center"><?php echo $CHAR; ?></td>
                                <td align="center">
                                    <?php
                                    if ($if_remove==0) {
                                        ?>
                                        <input type="checkbox" value="<?php echo $table_name ?>"
                                               name="TRANS[<?php echo $table_name ?>]" <?php echo ($CHAR == "utf8") ? "" : "checked"; ?> >
                                        <?php
                                    } else {
                                        ?><span style="color:#FF0000">不維護的模組</span>
                                        <?php
                                    }
                                        ?>
                                </td>

                                <td align="center">
                                <?php
                                if ($if_remove==0) {
                                    ?>
                                    <input type="checkbox" value="<?php echo $table_name ?>" name="TABLES[]" checked>
                                    <?php
                                } else {
                                    ?><span style="color:#FF0000">不維護的模組</span>
                                    <?php
                                }
                                ?>
                                </td>
                            </tr>
                            <?php
                        }
                    }
                    ?>

                    </table>

                </td>
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

//清理已不維護的模組
function remove_unserv_module() {

    global $conn;





}


function getColumn($table) {
     global $conn;
     $sql = "show columns FROM `$table`";
     $res=mysqli_query($conn,$sql) or die ($sql);
        $columns=array();
     while ($row=mysqli_fetch_array($res,MYSQL_ASSOC)) {
         if (!in_array($row['Field'],$columns)) $columns[]=$row['Field'];
     }

     return $columns;
}

//big5轉 utf8
function big5_to_utf8($str){
  global $CHAR;
    $str = mb_convert_encoding($str, "UTF-8", "BIG5");

    $i=1;

    while ($i != 0){
        $pattern = '/&#\d+\;/';
        preg_match($pattern, $str, $matches);
        $i = sizeof($matches);
        if ($i !=0){
            $unicode_char = mb_convert_encoding($matches[0], 'UTF-8', 'HTML-ENTITIES');
            $str = preg_replace("/$matches[0]/",$unicode_char,$str);
        } //end if
    } //end wile

    //去除原始資料中 addslash 過多的 \
    //$str=stripslashes($str);
    //去除連續兩個 \\ 的字元
    $str=str_replace("\\\\","",$str);
    //去除資料欄位最後一碼是\
    if (substr($str,-1)=="\\") $str=substr($str,0,strlen($str)-1);

    return $str;

}
?>
<Script>

    function check_tag() {

       var status=$("#check_all_table").prop("checked");

        var i =0;
            while (i < document.myform.elements.length)  {
                if (document.myform.elements[i].name.substr(0,6)=='TABLES') {
                    document.myform.elements[i].checked=status;
                }
                i++;
            }
    }
</Script>
