<?php
// $Id: board.php 9092 2017-06-14 09:04:08Z tuheng $
// --系統設定檔
include "board_config.php";
// session 認證
session_start();

$teach_id = $_SESSION[session_log_id];

$bk_id = $_REQUEST['bk_id'];

//mysqli
$mysqliconn = get_mysqli_conn("board_kind");



if (!board_checkid($bk_id)) {

    $go_back = 1; //回到自已的認證畫面
    if ($is_standalone)
        include "header.php";
    else
        head("校務佈告欄");

    include $SFS_PATH . "/rlogin.php";
    if ($is_standalone)
        include "footer.php";
    else
        foot();
    exit;
}
//-----------------------------------

$query = "select  a.post_office , b.title_name ,c.name from teacher_post a ,teacher_title b ,teacher_base c where a.teacher_sn = c.teacher_sn and  a.teach_title_id =b.teach_title_id  and a.teacher_sn='$_SESSION[session_tea_sn]' ";
$result = $CONN->Execute($query) or die($query);

$row = $result->fetchRow();
$b_name = addslashes($row["name"]); //張貼人姓名
$b_unit = addslashes($_POST['board_name']); //所在處室
$b_title = addslashes($row["title_name"]); //職稱
///mysqli
$query = "select board_name,board_date,board_k_id,board_last_date,board_is_upload,board_is_public,board_admin from board_kind ";
$query .= "where bk_id =? ";
$stmt = "";
$stmt = $mysqliconn->prepare($query);
$stmt->bind_param('s', $bk_id);
$stmt->execute();
$stmt->bind_result($board_name, $board_date, $board_k_id, $board_last_date, $board_is_upload, $board_is_public, $board_admin);
$stmt->fetch();
$stmt->close();
///mysqli
/*
  $query = "select * from board_kind ";
  $query .= "where bk_id ='$bk_id' ";
  $result= $CONN->Execute($query) or die ($query);
  $row = $result->fetchRow();
  $board_name = $row["board_name"];
  $board_date = $row["board_date"];
  $board_k_id = $row["board_k_id"];
  $board_last_date = $row["board_last_date"];
  $board_is_upload = $row["board_is_upload"];
  $board_is_public = $row["board_is_public"];
  $board_admin = $row["board_admin"];
 */

if ($_POST['key'] == "確定公告") {
    $b_post_time = mysql_date();
    $b_upload_name = $_FILES['b_upload']['name'];

    /*
      $sql_insert = "insert into board_p(bk_id,b_open_date,b_days,b_unit,b_title,b_name," .
      "b_sub,b_con,b_upload,b_url,b_own_id,b_post_time,b_is_intranet,teacher_sn,b_is_sign,b_is_marquee)values " .
      "('{$_POST['bk_id']}','{$_POST['b_open_date']}','{$_POST['b_days']}'," .
      "'$b_unit','$b_title','$b_name','{$_POST['b_sub']}','{$_POST['b_con']}'," .
      "'$b_upload_name','{$_POST['b_url']}','{$_SESSION['session_log_id']}',now()," .
      "'{$_POST['b_is_intranet']}','{$_SESSION['session_tea_sn']}','{$_POST['b_is_sign']}','{$_POST['b_is_marquee']}')";

      $CONN->Execute($sql_insert) or die ($sql_insert);
      //echo $sql_insert;
      $b_id = $CONN->Insert_ID();
     */

//mysqli	

    $sql_insert = "insert into board_p(bk_id,b_open_date,b_days,b_unit,b_title,b_name," .
            "b_sub,b_con,b_upload,b_url,b_own_id,b_post_time,b_is_intranet,teacher_sn,b_is_sign,b_is_marquee)values " .
            "(?,?,?," .
            "'$b_unit','$b_title','$b_name',?,?," .
            "'$b_upload_name',?,'{$_SESSION['session_log_id']}',now()," .
            "?,'{$_SESSION['session_tea_sn']}',?,?)";
    $stmt = "";
    $stmt = $mysqliconn->prepare($sql_insert);
    $stmt->bind_param('sssssssss', check_mysqli_param($_POST['bk_id']), check_mysqli_param($_POST['b_open_date']), check_mysqli_param($_POST['b_days']), check_mysqli_param($_POST['b_sub']), check_mysqli_param($_POST['b_con']), check_mysqli_param($_POST['b_url']), check_mysqli_param($_POST['b_is_intranet']), check_mysqli_param($_POST['b_is_sign']), check_mysqli_param($_POST['b_is_marquee']));
    $stmt->execute();
    $b_id = mysqli_stmt_insert_id($stmt);
    $stmt->close();
///mysqli		


    $fileCount = count($_FILES);
    if ($fileCount > 0) {
        //上傳檔案
        $file_path = "$USR_DESTINATION/$b_id";
        if (!is_dir($file_path))
            mkdir($file_path, 0700);
        $tt = time();
        for ($i = 1; $i <= $fileCount; $i++) {
            if ($_FILES["resourceFile_$i"]['name'] == '')
                continue;
            if (!check_is_php_file($_FILES["resourceFile_$i"]['name'])) {
                $org_filename = $_FILES["resourceFile_$i"]['name'];
                //檢驗副檔名
                $expand_name = explode(".", $org_filename);
                $nn = count($expand_name) - 1;  //取最後一個當附檔名
                $ATTR = strtolower($expand_name[$nn]); //轉小寫副檔名
                $new_filename = $tt . "_" . $i . "-" . date("Y_m_d") . "." . $ATTR;
                //copy($_FILES["resourceFile_$i"]['tmp_name'],$file_path."/".$tt.'_'.$i.'-'.$_FILES["resourceFile_$i"]['name']);
                copy($_FILES["resourceFile_$i"]['tmp_name'], $file_path . "/" . $new_filename);
                //儲存副檔資訊
                $query = "insert into board_files (b_id,org_filename,new_filename) values ('$b_id','$org_filename','$new_filename')";
                $CONN->Execute($query) or die($query);
            }
        }
    }


    if (!$error_flag) {
        Header("Location: board_view.php?bk_id=$bk_id");
    }
}

//是否有獨立的界面
if ($is_standalone)
    include "header.php";
else
    head("校務佈告欄");

$b_open_date = date("Y-m-j");

//產生白名單字串
if($file_filter==0){
    $white_file="";
    $multi_form_js="forms.js";
}else{
    $ext_temp=explode(',',$file_ext_list);
    $white_file="/(\.".$ext_temp[0];
    $i=1;
    while($i<count($ext_temp)){
         $white_file.="|\.".$ext_temp[$i];
         $i++;
    }
    $white_file.=")$/i";
    $multi_form_js="forms2.js";
}
?>
<Script src="../../include/ckeditor/ckeditor.js"></Script>

<script language="JavaScript">
    function checkok()
    {
        var OK = true
        if (document.eform.b_sub.value == "")
        {
            OK = false;
        }
        if (OK == false)
        {
            alert("標題不可空白")
        }
        return OK
    }
    function filecheck(elm) {
        var fileInput = document.getElementById(elm.id);
        var filePath = fileInput.value;
        var allowedExtensions = <?php echo $white_file;?>;
        if (!allowedExtensions.exec(filePath)) {
            alert('您上傳的檔案格式不支援，請選擇其他檔案');
            fileInput.value = '';
            return false;
        }
    }

//-->
</script>

<script type="text/javascript" src="<?php echo $SFS_PATH_HTML."javascripts/".$multi_form_js ?>"></script>

<form enctype="multipart/form-data" name=eform method="post" action="<?php echo $PHP_SELF ?>"
      onSubmit="return checkok()">
    <center>
<?php echo "<b>$board_name 公告欄</b>"; ?>
<?php
//顯示錯誤訊息
if ($error_flag)
    echo "<h3><font color=red>錯誤 !! 不可上傳 php 程式檔!!</font></h3>";
?>
        <table	border="1" bgcolor="#CCFFFF" bordercolor="#9999FF">
            <tr>
                <td align="right" valign="top">公告人</td>
                <td><?php echo "$b_unit $b_name"; ?></td>
            </tr>
            <tr>
                <td align="right" valign="top">公告日期</td>
                <td><input type="text" size="12" maxlength="12" name="b_open_date" value="<?php echo $b_open_date
?>"></td>
            </tr>

            <tr>
                <td align="right" valign="top">內部文件</td>
                <td><input type="checkbox"  name="b_is_intranet" value="1"> 本訊息只對本校公布</td>
            </tr>
<?php
if ($enable_is_sign == '1') {
    ?>
                <tr>
                    <td align="right" valign="top">啟用簽收</td>
                    <td><input type="checkbox"  name="b_is_sign" value="1" > 本校教職員須簽收公告</td>
                </tr>

    <?php
}
?>
            <tr>
                <td align="right" valign="top">跑馬燈</td>
                <td><input type="checkbox"  name="b_is_marquee" value="1" > 將本公告置於跑馬燈</td>
            </tr>
            <tr>
                <td align="right" valign="top">公告天數</td>
                <td><select name="b_days">
<?php
while (list ($key, $val) = each($days)) {
    if ($key == 7)
        echo "<option value=\"$key\" selected >$val";
    else
        echo "<option value=\"$key\" >$val";
}
?>
                    </select>
                </td>
            </tr>


            <tr>
                <td align="right" valign="top">標題</td>
                <td><input type="text" size="80" maxlength="100" name="b_sub" value="<?php echo $b_sub ?>"></td>
            </tr>


            <tr>
                <td align="right" valign="top" nowrap="true">公布內容</td>

<?php
/*
  if ($enable_is_html<>''){
  echo '<td width="95%" >';
  require "../../include/fckeditor.php";
  $oFCKeditor = new FCKeditor('b_con') ;
  $oFCKeditor->Height = 300;
  $oFCKeditor->ToolbarSet = $enable_is_html;
  $oFCKeditor->Value=$b_con;
  $oFCKeditor->Create();
  }
  else{
 */
?>
                <td width="95%">	<textarea name="b_con" cols=52 rows=5 wrap=virtual><?php echo $b_con ?></textarea>
                <?php
                //}
                ?>
                </td>
            </tr>

            <tr>
                <td align="right" valign="top">相關網址</td>
                <td><input type="text" name="b_url" size=50 value="<?php echo $b_url ?>"></td>
            </tr>

<?php
if ($board_is_upload) {
    ?>
                <tr>
                    <TD vAlign=top align=right><p>附件</p>
                        <a href="javascript:addElementToForm('fileFields','file','resourceFile','')" class='b1'>增加附件</a>
                    </td>
                    <td>
                        <div class="field" id="fileFields">
                            <input type="file" id="resourceFile_1" name="resourceFile_1"<?php if($file_filter == 1) echo " onchange=\"return filecheck(this)\""; ?>/>
                            <br />
                            <div id="marker" style="clear:none;"></div>
                        </div>
                    </td>
                </tr>
<?php } ?>
            <input type="hidden" name="bk_id" value="<?php echo $bk_id ?>">
            <input type="hidden" name="board_name" value="<?php echo $board_name ?>">
            <tr>
                <td  align=center colspan=2 ><input type="submit" name="key" value="確定公告"></form>
                    <form action="board_view.php"><INPUT TYPE="submit" VALUE="回佈告欄" ></td>
                        </tr>
                        </table>
                    </form>
                    </center>

<?php
//若啟用 html 編輯器
if ($enable_is_html <> '') {
    echo "<script>CKEDITOR.replace('b_con',{ language: 'zh'});</script>";
}

if ($is_standalone)
    include "footer.php";
else
    foot();
?>
