<?php
// $Id: bookcode_new.php 8785 2016-01-19 09:12:41Z qfon $

include "book_config.php";
// 不需要 register_globals
if (!ini_get('register_globals')) {
    ini_set("magic_quotes_runtime", 0);
    extract($_POST);
    extract($_GET);
    extract($_SERVER);
}


if ($key == "產生圖書條碼(無分類號)") {
    $len = strlen($b_num);
    $temp = 1;
    for ($i = 1; $i <= $len; $i++)
        $temp = $temp * 10;
    settype($b_num, double);
    echo "<html><body><table border=0 cellPadding=2 cellSpacing=5 ><tr>";
    for ($i = 0; $i < $code_num; $i++) {
        //$core = substr($bookch1_id,0,3).".".substr(($temp+$b_num),1,$len);
        $core = substr(($temp + $b_num), 1, $len);
        $topname = $lib_name . "---" . substr($bookch1_id, 3, strlen($bookch1_id) - 3);
        echo "<td align=center nowrap><font face='$code_font' size='$code_title_size'>$topname</font><BR>";
        barcode($core);
        echo "<br><font face='$code_font' size='$code_num_size'>$core</font></td>\n";
        if ($i % $barcore_cols == $barcore_cols - 1)
            echo"</tr><tr>";
        $b_num++;
    }
    echo "</tr></table>";
    echo "</body></html>";
    exit;
}

include "header.php";
$code_p = "$PHP_SELF";
$query = "select * from bookch1  order by bookch1_id";
$result = mysql_query($query, $conID);
//分類號選項
$i = 0;
$tt = "";
while ($row = mysql_fetch_array($result)) {
    if ($i > 0) {
        $tt .= sprintf(" <option value=\"%s\" >%s%s</option>", $row["bookch1_id"] . $row["bookch1_name"], $row["bookch1_id"], $row["bookch1_name"]);
    } else {
        $tt .= sprintf(" <option value=\"%s\" selected>%s%s</option>", $row["bookch1_id"] . $row["bookch1_name"], $row["bookch1_id"], $row["bookch1_name"]);
    }
    $i++;
}
?>
<script language="JavaScript">
<!-- Hide
    function checknum(checktext)
    {
        if (parseInt(checktext.value) == -1)
        {
            checktext.value = "";
            return;
        }
        if (checktext.value == "NaN") {
            checktext.value = ""
            return;
        }
    }
    function checkok()
    {
        var OK = true
        if (document.spost.b_num.value == "")
        {
            OK = false;
        }
        if (OK == false) {
            alert("起始號不可空白！請再詳填！")
        }
        return OK
    }

    function checkok2()
    {
        var OK = true
        if (document.spost2.s_no.value == "")
        {
            OK = false;
        }
        if (OK == false) {
            alert("學號不可空白！請再詳填！")
        }
        return OK
    }


    function checkok3()
    {
        var OK = true
        if (document.spost3.s_no.value == "")
        {
            OK = false;
        }
        if (OK == false) {
            alert("教師代號不可空白！請再詳填！")
        }
        return OK
    }
//-->
</script>

<form method="post" name="spost" action="<?php echo $code_p ?>"  onSubmit="return checkok()">
    <table border=1 width=90% align=center bgcolor=#ffaa00>
        <caption><font size=+2>圖書條碼列印</font></caption>
        <tr><td bgcolor="#8080FF" width=10% align=center><strong>分類號</strong></td>
            <td bgcolor="#8080FF" width=15% align=center><strong>列印起始號</strong></td>
            <td bgcolor="#8080FF" width=15% align=center><strong>列印條碼數</strong></td>
            <td bgcolor="#8080FF" width=15% align=center><strong>標題大小</strong></td>
            <td bgcolor="#8080FF" width=15% align=center><strong>條碼顯示行數</strong></td>
		    <td bgcolor="#8080FF" width=15% align=center><strong>字型</strong></td>
	        <td bgcolor="#8080FF" width=15% align=center><strong>流水號大小</strong></td>
	
		</tr><td>
            <select name="bookch1_id" size="1" >  
                <?php echo $tt ?> 
            </select></td>　　
        <td align=center><input type=text name="b_num" size=20 onBlur="checknum(this)"></td>
        <td align=center>
            <select name="code_num" size="5" >
                <option value=1 selected> 1個</option>
                <?php
                for ($i = 2; $i <= 40; $i++)
                    echo sprintf("<option value=%d >%2d個</option>", $i, $i);
                ?>
            </select></td>
		  <td align=center>
            <select name="code_title_size" size="5" >
                <option value=1 selected> 2</option>
                <?php
                for ($i = 1; $i <= 8; $i++)
                    echo sprintf("<option value=%d >%2d</option>", $i, $i);
                ?>
            </select></td>	
		<td align=center>
            <select name="barcore_cols" size="5" >
                <option value=1 selected> 4</option>
                <?php
                for ($i = 1; $i <= 10; $i++)
                    echo sprintf("<option value=%d >%2d</option>", $i, $i);
                ?>
            </select></td>		
		  <td align=center>
            <select name="code_font" size="5" >
                <option value="新細明體" selected>新細明體</option>
				<option value="標楷體">標楷體</option>
            </select></td>				
		  <td align=center>
            <select name="code_num_size" size="5" >
                <option value=1 selected> 2</option>
                <?php
                for ($i = 1; $i <= 8; $i++)
                    echo sprintf("<option value=%d >%2d</option>", $i, $i);
                ?>
            </select></td>			
			
			
        </tr>
        <tr><td  colspan="3" align=center><input type=submit name=key value="產生圖書條碼(無分類號)"></td></tr>
    </table>
</form>
</center>
<?php
include "footer.php";
?>
