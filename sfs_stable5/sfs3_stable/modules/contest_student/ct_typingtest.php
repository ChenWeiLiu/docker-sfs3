<?php
header('Content-type: text/html;charset=big5');
// $Id: reward_one.php 6735 2012-04-06 08:14:54Z smallduh $

//���o�]�w��
include_once "config.php";

sfs_check();

//�q�X����
head("���������v�� - ���r�m��");


$tool_bar=&make_menu($school_menu_p);

//�C�X���
echo $tool_bar;

//���o�ثe�Ǧ~��
$curr_year=curr_year();
$curr_seme=curr_seme();

//�ثe��w�Ǵ�
$c_curr_seme=sprintf('%03d%1d',$curr_year,$curr_seme);

//�ثe����ɶ�
$Now=date("Y-m-d H:i:s");

//����ܭn�����٬O�^��

//�A��ܤ峹



?>
<form name="myform" method="post" action="<?php echo $_SERVER['php_self'];?>">
    <input type="hidden" name="start" value="<?= $_POST['start'] ?>">
    <div>
        <span>
            �����G<select size="1" name="kind" onchange="document.myform.start.value=0;document.myform.submit()">
                <option value="">�п�ܤ����έ^��</option>
                <option value="1"<?php if ($_POST['kind']=="1") echo " selected";?>>����</option>
                <option value="2"<?php if ($_POST['kind']=="2") echo " selected";?>>�^��</option>
            </select>
        </span>

    <?php
    if ($_POST['kind']!='') {
        $sql="select * from contest_typebank where kind='{$_POST['kind']}' and open='1'";
        $res=$CONN->Execute($sql);
        if ($res->recordCount()==0) {
            echo "�S���峹!";
        } else {
            ?>
                <span>
                    �g�W�G
                    <select size="1" name="type_id" onchange="document.myform.start.value=0;document.myform.submit()">
                        <option value="">�п�ܤ峹</option>
                        <?php
                        while ($row=$res->fetchRow()) {
                            ?>
                            <option value="<?= $row['id'] ?>"<?php if ($_POST['type_id']==$row['id']) echo " selected";?>><?= $row['article'] ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </span>
            <?php
        }
    }
    ?>
    </div>
        <?php
    //�p�G�w��ܤF�峹
    if ($_POST['type_id']!='') {
        $sql="select * from contest_typebank where id='{$_POST['type_id']}'";
        $res=$CONN->Execute($sql);
        list($id,$kind,$article,$content)=$CONN->Execute($sql)->fetchrow();

        //�n�����r
        $data=$content;
        $L=explode("\r\n",$data);
        $words=0;
        foreach ($L as $line) {
            $words+=mb_strlen($line, "big5");  //�C��r�ƥ[�_��
        }
        $new_line=count($L);  //���

        $window_height=($new_line<13)?$new_line*18+6:222;
        //��n�����r���s�զX, �� javascript �ϥ�
        //$TT=implode("\\n",$L);

        ?>
        <div>
            <span style="text-align: right">
                �r�ơG<?php echo $words;?> &nbsp;&nbsp; ��ơG<?php echo $new_line;?>
            </span>
        </div>
        <div style="padding-top:3px;padding-bottom:3px;line-height:22px;font-family:�s�ө���;border-style: solid;font-size:14pt;height:<?php echo $window_height;?>;overflow: auto" id="SHOW2">
            <?php //= str_replace("\n","<BR>",$data); ?>
        </div>

        <?php

        if ($_POST['start']==1) {
            $_SESSION['type_timer']=date("Y-m-d H:i:s");
            ?>
            <div>
                �w�L�ɶ��G <span id="timer">0</span> ��A �t�סG<span id="speed">0</span> �r/���A ���T�v�G<span id="correct"></span>�A�n���G<span id="score"></span> &nbsp;&nbsp;�m ���ծɶ� 300 �� (�Y 5 ����) �n
            </div>
            <div style="border-style: solid;border-color: #cccccc;">
                <textarea style="font-family:�s�ө���;font-size:14pt;width:100%;height: <?php echo $window_height;?>px" name="typetest" id="typetest"></textarea>
            </div>
            <?php
        } else {
            $_SESSION['timer']=0;
            ?>
            <div style="margin-top: 5px">
                <input type="button" value="���U�ڶ}�l���r�A���Ģ��Ӧr�N�}�l�p��" onclick="document.myform.start.value='1';document.myform.submit()">
            </div>
            <div style="margin-top: 10px">
                <ol>
                    <li>������D�Ÿ��ΪťղŸ��A�����Ф@�ߥΥ��Φr�F�^���h�@�ߥΥb�Φr�C</li>
                    <li>�J���_��ɡA�Цۦ�� [Enter] �䴫��C�S�����T����A�|�v�T���T�v�C</li>
                    <li><span style="color:#FF0000">�`�N�I���T�v���p��A�O�̧ǳv�r���A�p�G�����r�θ���A�᭱��������I</span></li>
                    <li>�t�ת��p��O�Υ��T�r��/�ɶ��C</li>
                    <li>�n���O����J���Ҧ����T�r���ơC</li>
                </ol>
            </div>
            <?php
        }

    } // end if $_POST['type_id']!=''

    ?>

</form>



    <?php
        if ($_POST['type_id']!='') {
          $type_id=$_POST['type_id'];
          include_once("typingrace_check.inc");
        }
    ?>

