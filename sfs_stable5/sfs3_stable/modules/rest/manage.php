<?php	
// $Id: index.php 5310 2009-01-10 07:57:56Z smallduh $
//取得設定檔
include_once "config.php";
//驗證是否登入
sfs_check();

//AJAX檢查 id
if ($_POST['act']=="check_id") {
  $sn=$_POST['sn'];
  $who=$_POST['who'];
  $sql="select * from rest_manage where sn!='$sn' and s_id='$who'";
    $res=$CONN->Execute($sql);
    if ($res->RecordCount()) {
        echo 1;
    } else {
        echo 0;
    }

    exit();
}

//製作選單 ( $school_menu_p陣列設定於 module-cfg.php )
$tool_bar=&make_menu($school_menu_p);
//讀取目前操作的老師有沒有管理權 , 搭配 module-cfg.php 裡的設定
$module_manager=checkid($_SERVER['SCRIPT_FILENAME'],1);

/**************** 開始秀出網頁 ******************/
//秀出 SFS3 標題
head();
//列出選單
echo $tool_bar;

if ($_POST['act']=="insert") {
    echo "<br><b>新增一筆授權</b><br>";
    api_manage_form($row,"save");
}

//儲存新增的授權
if ($_POST['act']=="save") {
    $method_post=implode(",",$_POST['api_post']);
    $method_get=implode(",",$_POST['api_get']);

    $s_id=$_POST['s_id'];
    $s_pwd=$_POST['s_pwd'];
    $allow_ip=$_POST['allow_ip'];

    $sql="insert into rest_manage (s_id,s_pwd,allow_ip,method_post,method_get) values ('$s_id','$s_pwd','$allow_ip','$method_post','$method_get')";
    $CONN->Execute($sql);
    $_POST['act']="";
}

if ($_POST['act']=="edit") {
    $sql = "select * from rest_manage where sn='" . $_POST['sn'] . "'";
    $res = $CONN->Execute($sql) or die("SQL=" . $sql);
    $row = $res->fetchRow();
    echo "<br><b>編輯流水號 #" . $row['sn'] . "授權內容</b><br>";
    api_manage_form($row, "update");
}

//儲存修改的授權
if ($_POST['act']=="update") {
    $sn=$_POST['sn'];
    if ($sn) {
        $method_post=implode(",",$_POST['api_post']);
        $method_get=implode(",",$_POST['api_get']);

        $s_id=$_POST['s_id'];
        $s_pwd=$_POST['s_pwd'];
        $allow_ip=$_POST['allow_ip'];

        $sql="update rest_manage set s_id='$s_id',s_pwd='$s_pwd',allow_ip='$allow_ip',method_post='$method_post',method_get='$method_get' where sn='$sn'";
        $CONN->Execute($sql);
    }
    $_POST['act']="";
}

//刪除
if ($_POST['act']=="drop") {
    $sql = "delete from rest_manage where sn='" . $_POST['sn'] . "'";
    $res = $CONN->Execute($sql) or die("SQL=" . $sql);
    $_POST['act']="";
}


//無任何動作, 列表
if ($_POST['act']=="") {

    $sql = "select * from rest_manage";
    $res = $CONN->Execute($sql) or die("SQL=" . $sql);

    $rows = $res->GetRows();

    ?>
    <table border="0" width="100%">
        <tr>

            <td align="right">
                <button id="insert">新增</button>
            </td>
        </tr>
    </table>
    <form name="myform" method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>">
        <input type="hidden" name="sn" value="">
        <input type="hidden" name="act" value="">
        <table border="1" style="border-collapse: collapse;border-color:#5F6F79" width="100%">
            <tr>
                <td style="background-color: #CCCCCC;font-size:10pt" width="5%" align="center">流水號</td>
                <td style="background-color: #CCCCCC" width="10%" align="center">帳號</td>
                <td style="background-color: #CCCCCC" width="10%" align="center">密碼</td>
                <td style="background-color: #CCCCCC" width="20%" align="center">IP限制</td>
                <td style="background-color: #CCCCCC" width="25%" align="center">POST權限</td>
                <td style="background-color: #CCCCCC" width="25%" align="center">GET權限</td>
                <td style="background-color: #CCCCCC" width="5%" align="center">動作</td>
            </tr>
            <?php
            foreach ($rows as $V) {
                $priv_post = explode(",", $V['method_post']);
                $priv_get = explode(",", $V['method_get']);
                ?>
                <tr>
                    <td style="background-color: #FFFFFF;font-size:10pt" align="center"><?php echo $V['sn'] ?></td>
                    <td style="background-color: #FFFFFF"><?php echo $V['s_id'] ?></td>
                    <td style="background-color: #FFFFFF"><?php echo $V['s_pwd'] ?></td>
                    <td style="background-color: #FFFFFF"><?php echo $V['allow_ip'] ?></td>
                    <td style="background-color: #FFFFFF">
                        <?php
                        if ($V['method_post']) {
                            foreach ($priv_post as $p) {
                                echo $p . "(" . $api_post[$p] . ") <br>";
                            }
                        } else {
                            echo "無";
                        }
                        ?>
                    </td>
                    <td style="background-color: #FFFFFF">
                        <?php
                        if ($V['method_get']) {
                        foreach ($priv_get as $g) {
                            echo $g . "(" . $api_get[$g] . ") <br>";
                        }
                        } else {
                            echo "無";
                        }
                        ?>
                    </td>
                    <td align="center">
                        <a id="edit_<?php echo $V['sn'] ?>" class="edit"><img src="images/edit.png"></a>
                        <a id="drop_<?php echo $V['sn'] ?>" class="drop"><img src="images/del.png"></a>
                    </td>
                </tr>
            <?php
            }
            ?>
        </table>
    </form>
<?php
} // end if
//  --程式檔尾
foot();
?>

<script>
 $(".edit").click(function(){
     var strID=$(this).attr('id').split("_");
     var sn=strID[1];

     document.myform.sn.value=sn;
     document.myform.act.value='edit';

     document.myform.submit();

 });
 $(".drop").click(function(){
     var strID=$(this).attr('id').split("_");
     var sn=strID[1];
    if (confirm("您確定要刪除權限設定「流水號 #"+sn+"」 ？")) {
        document.myform.sn.value=sn;
        document.myform.act.value='drop';

        document.myform.submit();

    } else {
        return false
    }

 });
 $("#insert").click(function(){
    document.myform.act.value='insert';
     document.myform.submit();
 });
</script>