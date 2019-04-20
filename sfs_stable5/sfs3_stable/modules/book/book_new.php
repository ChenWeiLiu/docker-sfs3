<?php
// $Id: book_new.php 8905 2016-06-03 06:31:36Z infodaes $
// --系統設定檔
include "book_config.php";

//檢查管理IP
if (!$un_limit_ip) {
    $is_man = 0;
    for ($mi = 0; $mi < count($man_ip); $mi++) {
        if (check_home_ip($man_ip[$mi])) {
            $is_man = 1;
            break;
        }
    }

    if (!$is_man)
        header("Location: err.php");
}
// --認證 session
//session_start();
//session_register("session_log_id");
if (!checkid(substr($PHP_SELF, 1))) {
    $go_back = 1; //回到自已的認證畫面
    include "header.php";
    include "$rlogin";
    include "footer.php";
    exit;
}

//================================= ISBN =======================================
if ($_POST['key1'] == "確定輸入") {
    $BN = trim($_REQUEST['BN']);
    $PAGELINE = $_REQUEST['PAGELINE'];
    $serialBN = ereg_replace("-", "", $BN);
    $fp = fopen("http://192.83.186.170/search*cht/i$BN/i$serialBN/-6,0,0,B/marc&FF=i$serialBN&1,1,", "r");
    if (!$fp) {
        echo "<center><font size='30' color='red'>查無此ISBN，請重新輸入</font></center>";
    } else {
        while (!feof($fp)) {
            $buffer = fgetss($fp, 4096);

            //$buffer  = iconv("UTF-8","BIG5",$buffer);

            if (substr("$buffer", 0, 3) == "200") {
                $buffer = substr("$buffer", 6, strlen($buffer) - 6);
                $data = explode("|", $buffer);
                $name = trim($data[0]); //初始書名
                for ($i = 1; $i <= count($data) - 1; $i++) {
                    if (substr("$data[$i]", 0, 1) == "f")
                        $author = iconv("UTF-8//IGNORE", "Big5", substr($data[$i], 1, strlen($data[$i]) - 1)); //作者
                    elseif (substr("$data[$i]", 0, 1) == "g")
                        $transer = iconv("UTF-8//IGNORE", "Big5", substr($data[$i], 1, strlen($data[$i]) - 1)); //譯者
                    else
                        $name .= substr($data[$i], 1, strlen($data[$i]) - 1);     //書名
                }
                $name = iconv("UTF-8//IGNORE", "Big5", $name);
            }
            elseif (substr("$buffer", 0, 3) == "010") {
                $cost = strstr($buffer, 'd'); //金額
                if (substr($cost, 1, 1) == "N") {//判斷金額的前置字元是否為NT$
                    $cost = substr($cost, 4, -1);
                } else {
                    $cost = substr($cost, 7, -4); //若非NT$則為新台幣或人民幣
                }
                $buffer = substr("$buffer", 6, strlen($buffer) - 6);
                $data = explode("|", $buffer);
                $isbn_code = ltrim($data[0]);   //ISBN
                $bind = iconv("UTF-8//IGNORE", "Big5", $data[1]);          //精裝本或平裝未
                if (strstr("$bind", '精')) {
                    $book_bind = "精裝";
                } else {
                    $book_bind = "平裝";
                }
            } elseif (substr("$buffer", 0, 3) == "210") {
                $buffer = strstr($buffer, ' ');
                $data = explode("|", $buffer);
                for ($i = 1; $i <= count($data) - 1; $i++) {
                    if (substr("$data[$i]", 0, 1) == "c")
                        $pub_person = iconv("UTF-8//IGNORE", "Big5", substr("$data[$i]", 1, strlen($data[$i]) - 1)); //出版商
                    if (substr("$data[$i]", 0, 1) == "d")
                        $pub_year = iconv("UTF-8//IGNORE", "Big5", substr("$data[$i]", 1, strlen($data[$i]) - 1));  //出版年
                }
            }

            elseif (substr("$buffer", 0, 3) == "681") {
                $buffer = strstr($buffer, ' ');
                $data = explode("|", $buffer);
                $book_class = trim($data[0]);   //分類號
            } elseif (substr("$buffer", 0, 3) == "805") {
                $buffer = strstr($buffer, ' ');
                $data = explode("|", $buffer);
            }
        }
    }
    $book_name = $name;
    $book_author = $author;
    $book_maker = $pub_person;
    $book_myear = $pub_year;
    $book_price = $cost;
    $book_isbn = $isbn_code;
}
//===============================================================================


$isPosted = "";
$myMsg = "";
if ($_POST['key'] == "確定新增") {
    $mysqliconn = get_mysqli_conn();
    if (preg_match("/\./", $_POST['book_id']))
        $user_defned = 0;
    else
        $user_defned = 1;  // 自訂圖書號

    for ($i = 0; $i < $_POST[howmany]; $i++) {
        if ($user_defned) { // 自訂圖書號
            $dd = intval('1' . $_POST['book_id']) + $i;
            $dd = substr($dd, 1);
        } else {
            //檢查是否超過一萬本

            /*
              $sql_select = "select max(book_id)as mm from book where bookch1_id ='$_POST[bookch1_id]' AND length(book_id)>8";
              $result = mysql_query ($sql_select,$conID) or die($sql_select);
              $row = mysql_fetch_array($result);
             */

///mysqli
            $sql_select = "select max(book_id)as mm from book where bookch1_id =? AND length(book_id)>8";
            $stmt = "";
            $stmt = $mysqliconn->prepare($sql_select);
            $stmt->bind_param('s', $_POST[bookch1_id]);
            $stmt->execute();
            $stmt->bind_result($row[0]);
            $stmt->fetch();
            $stmt->close();

            if ($row[0]) {
                $is_over_10000 = 1;
            } else {
                $is_over_10000 = 0;

                $sql_select = "select max(book_id)as mm from book where bookch1_id =?";
                $stmt = "";
                $stmt = $mysqliconn->prepare($sql_select);
                $stmt->bind_param('s', $_POST['bookch1_id']);
                $stmt->execute();
                $stmt->bind_result($book_id);
                $stmt->fetch();
                $stmt->close();
                /*
                  $sql_select = "select max(book_id)as mm from book where bookch1_id ='{$_POST['bookch1_id']}'";
                  $result = mysql_query ($sql_select,$conID) or die($sql_select);
                  $row = mysql_fetch_array($result);
                 */
            }
            //$book_id = $row["mm"];
            $bb = explode(".", $book_id);
            if ($is_over_10000)
                $book_id = $bb[0] . "." . substr(intval($bb[1] + 100001), 1, 5);
            else
                $book_id = $bb[0] . "." . substr(intval($bb[1] + 10001), 1, 4);
            $dd = "";
            if ($book_id == ".0001")
                $dd = $_POST[bookch1_id] . $book_id;
            else
                $dd = $book_id;
        }
        /*
          $sql_insert = "insert into book (bookch1_id,book_id,book_name,book_author,book_maker,book_myear,book_bind,book_price,book_content,book_isborrow,book_isbn,book_buy_date)values ('$_POST[bookch1_id]','$dd','$_POST[book_name]','$_POST[book_author]','$_POST[book_maker]','$_POST[book_myear]','$_POST[book_bind]','$_POST[book_price]','$_POST[book_content]','$_POST[book_isborrow]','$_POST[book_isbn]','$_POST[book_buy_date]')";
          mysql_query($sql_insert,$conID) or die ($sql_insert);
         */
///mysqli
		$dd=trim($dd);
        $sql_insert = "insert into book (bookch1_id,book_id,book_name,book_author,book_maker,book_myear,book_bind,book_price,book_content,book_isborrow,book_isbn,book_buy_date)values (?,'$dd',?,?,?,?,?,?,?,?,?,?)";
        $stmt = "";
        $stmt = $mysqliconn->prepare($sql_insert);
        $stmt->bind_param('sssssssssss', check_mysqli_param($_POST[bookch1_id]), check_mysqli_param($_POST[book_name]), check_mysqli_param($_POST[book_author]), check_mysqli_param($_POST[book_maker]), check_mysqli_param($_POST[book_myear]), check_mysqli_param($_POST[book_bind]), check_mysqli_param($_POST[book_price]), check_mysqli_param($_POST[book_content]), check_mysqli_param($_POST[book_isborrow]), check_mysqli_param($_POST[book_isbn]), check_mysqli_param($_POST[book_buy_date]));
        $stmt->execute();
        $stmt->close();
///mysqli	


        /*
          $query = "update  bookch1 set tolnum = tolnum + 1 where bookch1_id = '$_POST[bookch1_id]'";
          mysql_query($query,$conID) or die($query);
         */
        //mysqli
        $query = "update  bookch1 set tolnum = tolnum + 1 where bookch1_id = ?";
        $stmt = "";
        $stmt = $mysqliconn->prepare($query);
        $stmt->bind_param('s', $_POST[bookch1_id]);
        $stmt->execute();
        $stmt->close();
        //mysqli

        $myMsg = $myMsg . $_POST[book_name] . "，書籍編號：" . $dd . "已新增完成！\\n";
    }
    $isPosted = "yes";
}
//else
//{

if (!empty($book_class)) {
    $bookch1_id = substr($book_class, 0, 1) . "00"; //以國家圖書館分類號優先
} elseif (!empty($_POST[bookch1_id])) {
    $bookch1_id = $_POST[bookch1_id]; //等於表單之分類號
} else {
//if($bookch1_id == "")
    $bookch1_id = "000"; //預設值：總類
}

//  if($bookch1_id == "")
//	  $bookch1_id= "000";
//  $sql_select = "select max(book_id)as mm from book where bookch1_id ='$_POST[bookch1_id]'";
//先檢查有沒有超過一萬本

$sql_select = "select max(book_id)as mm from book where bookch1_id ='$bookch1_id' AND length(book_id)>8";
$result = mysql_query($sql_select, $conID) or die($sql_select);
$row = mysql_fetch_array($result);

if ($row[0]) {
    $is_over_10000 = "本分類存書本數超過一萬本，編碼增加為5碼！";
} else {
    $sql_select = "select max(book_id)as mm from book where bookch1_id ='$bookch1_id'";
    $result = mysql_query($sql_select, $conID) or die($sql_select);
    $row = mysql_fetch_array($result);
}

$book_id = $row["mm"];
$bb = explode(".", $book_id);

//  $book_id= $bb[0].".".substr(intval($bb[1]+10001),1,4);
if ($is_over_10000)
    $book_id = $bookch1_id . "." . substr(intval($bb[1] + 100001), 1, 5);
else
    $book_id = $bookch1_id . "." . substr(intval($bb[1] + 10001), 1, 4);
//}
include "header.php";
?>

<center>
    <form name=bookform method="post" action ="<?php echo $PHP_SELF ?>">
        <table>


            <tr>
                <td height="30" colspan="4" bgcolor="#66ff99">
                    <div align="center">輸入ISBN 條碼
                        <input name=routine type=hidden value=holding>
                        <input type=hidden name="PAGELINE" value=10>
                        <input type="text" name="BN" size="20">
                        <input type="submit" name="key1" value="確定輸入">
                    </div>

                </td>
            </tr>


            <caption><font size=4><B>新 增 圖 書</b></font></caption>
            <tr>
                <td align="right" valign="top">中國圖書分類號</td>
                <td>
                    <select name=bookch1_id onChange="this.form.submit()">
                        <?php
                        $query = "select * from bookch1 order by bookch1_id ";
                        $result = mysql_query($query, $conID);
                        while ($row = mysql_fetch_array($result)) {
                            //if (substr($row["bookch1_id"],0,1) == substr($book_class, 0, 1))
                            //echo sprintf("<option value=\"%s\" selected>%s %s",$row["bookch1_id"],$row["bookch1_id"],$row["bookch1_name"]);
                            if ($row["bookch1_id"] == $bookch1_id)
                                echo sprintf("<option value=\"%s\" selected>%s %s", $row["bookch1_id"], $row["bookch1_id"], $row["bookch1_name"]);
                            else
                                echo sprintf("<option value=\"%s\">%s %s", $row["bookch1_id"], $row["bookch1_id"], $row["bookch1_name"]);
                        }
                        ?>
                    </select>
                    <input type="text" size="9" maxlength="9" name="book_no" value="<?php echo $book_sid1 ?>">
                </td>
            </tr>
            <body  onload="setfocus()">
                <script language="JavaScript"><!--
                function setfocus() {
                        //document.bookform.key.value='';
                        //document.bookform.book_isbn.focus();
                        //document.isbn_form.BN.focus();
                        document.bookform.BN.focus();

                        return;
                    }

                    function doSubmit() {
                        if (document.bookform.book_name.value == "")
                        {
                            alert("請輸入書名！");
                            document.bookform.book_name.focus();
                        } else if (document.bookform.howmany.value == "")
                        {
                            alert("請輸入冊數！");
                            document.bookform.howmany.focus();
                        } else if (isNaN(document.bookform.howmany.value))
                        {
                            alert("冊數輸入錯誤，請重新輸入！");
                            document.bookform.howmany.focus();
                        } else
                        {
                            document.bookform.key.value = '確定新增';
                            document.bookform.submit();
                        }
                    }
                    function openwindow(url_str) {
                        urls = url_str + "?ISBN=" + document.bookform.book_isbn.value;
                        win = window.open(urls, "new", "toolbar=no,location=no,directories=no,status=no,scrollbars=no,resizable=no,copyhistory=no,width=450,height=320");
                        win.creator = self;
                    }

                    // -->
                </script>
            <tr>
                <td align="right" valign="top">圖書編號</td>
                <td><input type="text" size="9" maxlength="9" name="book_id" value="<?php echo $book_id; ?>"><?php echo "<font color=red size=2>$is_over_10000</font>"; ?></td>
            </tr>


            <tr>
                <td align="right" valign="top">書名</td>
                <td><input type="text" size="40" maxlength="40" name="book_name" value="<?php echo $book_name; ?>"></td>
            </tr>


            <tr>
                <td align="right" valign="top">作者</td>
                <td><input type="text" size="20" maxlength="20" name="book_author" value="<?php echo $book_author; ?>"></td>
            </tr>


            <tr>
                <td align="right" valign="top">出版商</td>
                <td><input type="text" size="20" maxlength="20" name="book_maker" value="<?php echo $book_maker; ?>"></td>
            </tr>


            <tr>
                <td align="right" valign="top">出版日期</td>
                <td><input type="text" size="10" maxlength="10" name="book_myear" value="<?php echo $book_myear; ?>"> (格式：2000-8-1)</td>
            </tr>

            <tr>
                <td align="right" valign="top">購買日期</td>
                <td><input type="text" size="10" maxlength="10" name="book_buy_date"> (格式：2000-8-1)</td>
            </tr>

            <tr>
                <td align="right" valign="top">裝訂</td>
                <td>

                    <input type=radio name=book_bind  <?php
                    if ($book_bind == "精裝") {
                        echo "checked";
                    }
                    ?> value="精裝">精裝
                    &nbsp;<input type=radio name=book_bind  <?php
                    if ($book_bind == "平裝") {
                        echo "checked";
                    }
                    ?> checked value="平裝">平裝
                </td>
            </tr>

            <tr>
                <td align="right" valign="top">定價</td>
                <td><input type="text" size="11" maxlength="11"   name="book_price" value="<?php echo $book_price; ?>">元</td>
            </tr>


            <tr>
                <td align="right" valign="top">ISBN</td>
                <td><input type="text" size="13" maxlength="13" name="book_isbn" value="<?php echo $book_isbn; ?>"></td>
            </tr>

            <tr>
                <td align="right" valign="top">內容簡介</td>
                <td><input type="text" size="40" maxlength="40" name="book_content"></td>
            </tr>


            <tr>
                <td align="right" valign="top">是否外借</td>
                <td>

                    <input type=radio name=book_isborrow checked value="0">是
                    &nbsp;<input type=radio name=book_isborrow value="1">否

                </td>
            </tr>
            <tr>
                <td align="right" valign="top">共有幾冊</td>
                <td><input type="text" size="2" maxlength="2" name="howmany" value="1">
                </td>
            </tr>

            <tr>
                <td colspan=2 align=center><hr size=1>
                    <input type=button name='aa' value="確定新增" onClick="doSubmit();">
                    <input type=hidden name='key' value="">
                    <input type=button value="圖書資料匯入" OnClick="openwindow('import_from_html.php')"></td>
            </tr>
        </table>

    </form>
</center>

<?php
if ($isPosted == "yes") {
    ?>
    <script language="javascript">
        alert("<?php echo $myMsg ?>");
    </script>
    <?php
}
include "footer.php";
?>
