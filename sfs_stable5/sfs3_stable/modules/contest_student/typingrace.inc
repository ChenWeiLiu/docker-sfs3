<?php
include_once('login.inc');
//�ثe����ɶ�
$Now=date("Y-m-d H:i:s");
//�ɭ��e�{�}�l, �����]�b <form>�� , act�ʧ@ , option1, option2 �Ѽ�2�� return��^�e�@�Ӱʧ@=============================
?>
<form method="post" name="myform" action="<?php echo $_SERVER['PHP_SELF'];?>"  enctype="multipart/form-data">
    <input type="hidden" name="act" value="<?php echo $_POST['act'];?>">
    <input type="hidden" name="option1" value="<?php echo $_POST['option1'];?>">
    <input type="hidden" name="option2" value="<?php echo $_POST['option2'];?>">
    <input type="hidden" name="return" value="<?php echo $_POST['act'];?>">

    <?php
    //���n�J
    if ($_POST['act']=='') {
        stud_login($ACTIVE,$INFO);
    }

    //���ҳq�L, �}�l
    if ($_POST['act']=='Start') {

        //���o�v�ɳ]�w
        $TEST=get_test_setup($_POST['option1']);				//�v�ɳ]�w
        $STUD=get_student($_SESSION['session_tea_sn']);         //�ǥ͸��

        $NOWsec=NowAllSec(date("Y-m-d H:i:s"));
        $StartTest=NowAllSec($TEST['sttime']);
        $EndTest=NowAllSec($TEST['endtime']);
        //���X�v�ɸ��, �Y�٨S�}�l
        if ($NOWsec<$StartTest) {
            //�v�ɩ|���}�l�A���ܦh�[�Y�N�i�� ********************
            $LeaveTime=$StartTest-$NOWsec;
            ?>
            <table border="0" width="100%">
                <tr>
                    <td style="color:#0000FF">
                        �n�J�ǥ͡G<?php echo $STUD['class_name']." ".$STUD['seme_num']."�� ".$STUD['stud_name'];?>�A�Z�����ɶ}�l�٦�<input type="text" name="time" size="6">�A�Ъ`�N�I
                    </td>
                </tr>
            </table>
        <?php
        test_main($_POST['option1'],0);
        ?>
            <Script language="JavaScript">
                //�����ܼ�
                var ACT="Start";
                //���ɩ|���i��A���ܨ�ƭp�ɫ�}�l
                var inttestsec=<?php echo $LeaveTime;?>;

                checkLeaveTime();

            </Script>

            <?php
            //���ɶi�椤
        } elseif ($NOWsec>=$StartTest and $NOWsec<$EndTest) {
            ?>
            <input type="hidden" name="start" value="<?= $_POST['start'] ?>">
            <?php
            //���o�ǥͤ��ɱ���
            $sql="select * from contest_typerec where race_id='{$_POST['option1']}' and student_sn='{$_SESSION['session_tea_sn']}'";
            $res=$CONN->Execute($sql);
            //�O�_�w�g�i�J�L
            if ($res->recordCount()==0) {
                $sql="insert into contest_typerec (race_id,student_sn) values ('{$_POST['option1']}','{$_SESSION['session_tea_sn']}')";
                $res=$CONN->Execute($sql);
                //$rec_id=$CONN->Insert_ID(); //���o last id
                $type_id=$TEST['type_id_1'];    //�峹 id
                $type_times=1;                  //�ĴX�g
            } else {
                $row=$res->fetchRow();
                if ($row['sttime_1']=='0000-00-00 00:00:00' and $row['sttime_2']=='0000-00-00 00:00:00') {
                    $type_id=$TEST['type_id_1'];    //�峹 id
                    $type_times=1;
                    $rec_id=$row['id'];
                } elseif ($row['sttime_1']!='0000-00-00 00:00:00' and $row['sttime_2']=='0000-00-00 00:00:00') {
                    $type_id=$TEST['type_id_2'];    //�峹 id
                    $type_times=2;
                    $rec_id=$row['id'];
                } else {
                    echo "<br>�A�w�g�˴�����! <br><br>";
                    echo "�Ĥ@�����Z�@�t�סG".round($row['speed_1']/10,2)." �r/���@�A���T�v�G".$row['correct_1']."�@�A�n���G".$row['speed_1']."<br><br>";
                    echo "�ĤG�����Z�@�t�סG".round($row['speed_2']/10,2)." �r/���@�A���T�v�G".$row['correct_2']."�@�A�n���G".$row['speed_2']."<br>";
                    exit();
                }

            }


            // $TEST �����ɪ��]�w array
            // $STUd  ���ǥ͸�� array
            $sql="select * from contest_typebank where id='$type_id'";
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
                �� <?= $type_times ?> �g�峹�˴�  &nbsp;&nbsp;&nbsp;�r�ơG<?php echo $words;?> &nbsp;&nbsp; ��ơG<?php echo $new_line;?>
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
                    �w�L�ɶ��G <span id="timer">0</span> ���A �t�סG<span id="speed">0</span> �r/���A ���T�v�G<span id="correct"></span>�A�n���G<span id="score"></span> &nbsp;&nbsp;�m �˴��ɶ� 600 �� (�Y 10 ����) �n
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
                    <div style="background-color: #cdcdcd;margin-top:5px;margin-bottom: 5px">
                        ���v�ɳW�h<br><br>
                        <?= str_replace("\n","<br>",$TEST['memo']) ?>
                    </div>
                    ���˴�����
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

            include_once ('typingrace_check.inc');

        } else {
            echo "���ɮɶ��w�L!!";
            exit();
        } // end if ���ɮɶ�����

    } // end if $_POST['act']=='start'
?>


</form>




