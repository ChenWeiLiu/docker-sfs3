<?php

// $Id: reward_one.php 6735 2012-04-06 08:14:54Z smallduh $

//���o�]�w��
include_once "config.php";

sfs_check();

//�q�X����
head("���������v�� - �޲z���r���ɥ��D�w");

?>
<script type="text/javascript" src="./include/tr_functions.js"></script>

<?php
$tool_bar=&make_menu($school_menu_p);

//�C�X���
echo $tool_bar;

//���o�ثe�Ǧ~��
$curr_year=curr_year();
$curr_seme=curr_seme();

//�ثe��w�Ǵ�
$c_curr_seme=sprintf('%03d%1d',$curr_year,$curr_seme);

//�ثe����ɶ�, �Ω���������Ĵ���
$Now=date("Y-m-d H:i:s");

if (!$MANAGER) {
 echo "<font color=red>��p! �A�S���޲z�v��, �t�θT��A�~��ާ@���\��!!!</font>";
 exit();
}

//POST �e�X��,�D�{���ާ@�}�l 
//�s�W�@��
if ($_POST['act']=='insert') {

    $kind = $_POST['kind'];           //���� 1���� 2�^��
    $article = $_POST['article'];     //�峹���D
    $content = $_POST['content'];     //����
    $open=$_POST['open'];
    //�s�J
    $query = "insert into contest_typebank (kind,article,content,open) values ('$kind','$article','$content','$open')";
    $res=$CONN->Execute($query) or die("Error! SQL=".$query);
    $kind=$article=$content="";
}  //end if insert

//��s�@��
if ($_POST['act']=='update') {

    $kind = $_POST['kind'];           //���� 1���� 2�^��
    $article = $_POST['article'];     //�峹���D
    $content = $_POST['content'];     //����
    $open=$_POST['open'];
    //�s�J
    $query = "update contest_typebank set kind='$kind',article='$article',content='$content',open='$open' where id='{$_POST['id']}'";
    $res=$CONN->Execute($query) or die("Error! SQL=".$query);

    $kind=$article=$content="";
}  //end if insert

//�R���@��
if ($_POST['act']=='delete') {
    $query="delete from contest_typebank where id='{$_POST['opt']}'";
    $res=$CONN->Execute($query) or die("Error! SQL=".$query);
}

//�s��@��
if ($_POST['act']=='edit') {
    $query="select * from contest_typebank where id='{$_POST['opt']}'";
    list($id,$kind,$article,$content,$open)=$CONN->Execute($query)->fetchrow();
}
//�ɭ��e�{�}�l, �����]�b <form>�� , act�ʧ@ , option1, option2 �Ѽ�2�� , return�O�U��^����
?>
<form method="post" name="myform" action="<?php echo $_SERVER['PHP_SELF'];?>" enctype="multipart/form-data">
 <input type="hidden" name="act" value="<?php echo (($id>0)?"update":"insert");?>">
 <input type="hidden" name="opt" value="">
 <input type="hidden" name="id" value="<?php echo $id;?>">
 <div>
        <span>�峹���O�G</span>
        <span>
            <select name="kind" size="1">
                <option value="1"<?php if ($kind==1) echo "selected";?>>����</option>
                <option value="2"<?php if ($kind==2) echo "selected";?>>�^��</option>
            </select>
        </span>
 </div>
 <div style="margin-top: 5px;margin-bottom: 5px">
     <span>�峹���D�G</span>
     <span><input type="text" name="article" value="<?= $article; ?>" style="width:90%"></span>
 </div>
 <div>
     <span>�峹���e�G</span>
     <span>
         <input type="radio" name="open" value="0"<?php if ($open==0) echo " checked";?>>���}��m��
         <input type="radio" name="open" value="1"<?php if ($open==1) echo " checked";?>>�}��m��
     </span>
     <div>
         <textarea name="content" style="width:100%;height:250px"><?= $content ?></textarea>
     </div>
 </div>
 <div>
     <span><input type="button" value="<?php echo (($id>0)?"��s���e":"�s�W�@��");?>" onclick="confirm_save()"></span>
 </div>
 <div style="margin-top: 10px">
   <ol>
       <li>�����峹�A��ĳ�Ҧ��Ÿ����ĥΥ��Φr�C</li>
       <li>�^���峹�A�C�@��e���o���ťղŸ��C</li>
       <li>.�ȥ��b�C�@�檺�A����׳B���U [enter] �䴫��A�C</li>
   </ol>
 </div>
</form>
<?php
$sql="select * from contest_typebank order by kind,id";
$res=$CONN->Execute($sql);
if ($res->recordCount()==0) {
    echo "�ثe��Ʈw���L���L���r���D�w!";
} else {
    ?>
    <table border="1" style="width:100%;border-collapse:collapse;border-style: solid;border-width: thin">
        <thead>
            <tr style="background-color: #8CCCCA">
                <td>�Ǹ�</td>
                <td>����</td>
                <td>���D</td>
                <td>�r��</td>
                <td>���</td>
                <td>�ާ@</td>
            </tr>
        </thead>
        <tbody>
    <?php
    $row=$res->getRows();
    $i=0;
    foreach ($row as $R) {
      $i++;
        $kind=($R['kind']==1)?"����":"�^��";
        $L=explode("\r\n",$R['content']);
        $words=0;
        foreach ($L as $line) {
            $words+=mb_strlen($line, "big5");  //�C��r�ƥ[�_��
        }
        $new_line=count($L);  //���
        ?>
        <tr>
            <td><?= $i ?></td>
            <td><?= $kind ?></td>
            <td><?= $R['article'] ?></td>
            <td><?= $words ?></td>
            <td><?= $new_line ?></td>
            <td>
                <input type="button" value="�s��" onclick="document.myform.opt.value='<?= $R['id'];?>';document.myform.act.value='edit';document.myform.submit()">
                <input type="button" value="�R��" onclick="confirm_delete('<?= $R['id'];?>')">
            </td>
        </tr>
        <?php
    }
    ?>
        </tbody>
    </table>
    <?php
}

?>

<Script>
    function confirm_delete(id) {

        var ok=confirm('�z�T�{�n�R��?');

        if (ok) {
            document.myform.opt.value=id;
            document.myform.act.value='delete';
            document.myform.submit();
        }

    }

    function confirm_save() {

        if (document.myform.article.value!='' && document.myform.content.value!='') {
            document.myform.submit();
        } else {
            alert ('�峹�g�W�M���e��������J�@�I');
        }

    }
</Script>
