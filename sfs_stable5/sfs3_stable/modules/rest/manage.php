<?php	
// $Id: index.php 5310 2009-01-10 07:57:56Z smallduh $
//���o�]�w��
include_once "config.php";
//���ҬO�_�n�J
sfs_check();

//AJAX�ˬd id
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

//�s�@��� ( $school_menu_p�}�C�]�w�� module-cfg.php )
$tool_bar=&make_menu($school_menu_p);
//Ū���ثe�ާ@���Ѯv���S���޲z�v , �f�t module-cfg.php �̪��]�w
$module_manager=checkid($_SERVER['SCRIPT_FILENAME'],1);

/**************** �}�l�q�X���� ******************/
//�q�X SFS3 ���D
head();
//�C�X���
echo $tool_bar;

if ($_POST['act']=="insert") {
    echo "<br><b>�s�W�@�����v</b><br>";
    api_manage_form($row,"save");
}

//�x�s�s�W�����v
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
    echo "<br><b>�s��y���� #" . $row['sn'] . "���v���e</b><br>";
    api_manage_form($row, "update");
}

//�x�s�ק諸���v
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

//�R��
if ($_POST['act']=="drop") {
    $sql = "delete from rest_manage where sn='" . $_POST['sn'] . "'";
    $res = $CONN->Execute($sql) or die("SQL=" . $sql);
    $_POST['act']="";
}


//�L����ʧ@, �C��
if ($_POST['act']=="") {

    $sql = "select * from rest_manage";
    $res = $CONN->Execute($sql) or die("SQL=" . $sql);

    $rows = $res->GetRows();

    ?>
    <table border="0" width="100%">
        <tr>

            <td align="right">
                <button id="insert">�s�W</button>
            </td>
        </tr>
    </table>
    <form name="myform" method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>">
        <input type="hidden" name="sn" value="">
        <input type="hidden" name="act" value="">
        <table border="1" style="border-collapse: collapse;border-color:#5F6F79" width="100%">
            <tr>
                <td style="background-color: #CCCCCC;font-size:10pt" width="5%" align="center">�y����</td>
                <td style="background-color: #CCCCCC" width="10%" align="center">�b��</td>
                <td style="background-color: #CCCCCC" width="10%" align="center">�K�X</td>
                <td style="background-color: #CCCCCC" width="20%" align="center">IP����</td>
                <td style="background-color: #CCCCCC" width="25%" align="center">POST�v��</td>
                <td style="background-color: #CCCCCC" width="25%" align="center">GET�v��</td>
                <td style="background-color: #CCCCCC" width="5%" align="center">�ʧ@</td>
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
                            echo "�L";
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
                            echo "�L";
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
//  --�{���ɧ�
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
    if (confirm("�z�T�w�n�R���v���]�w�u�y���� #"+sn+"�v �H")) {
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