<?php
header('Content-type: text/html;charset=utf-8');
// $Id: reward_one.php 6735 2012-04-06 08:14:54Z smallduh $

//���o�]�w��
include_once "../../include/config.php";

sfs_check();

$now=date("Y-m-d H:i:s");

    //�p�G�w��ܤF�峹
    if ($_POST['typing_words'] and $_POST['type_id']) {

        $sql="select * from contest_typebank where id='{$_POST['type_id']}'";
        $res=$CONN->Execute($sql);
        list($id,$kind,$article,$content)=$CONN->Execute($sql)->fetchrow();
        /*
        //��g�@�_��諸�覡
        //���� , ���������O���Φr, ���� stripslashes
        if ($kind==1) {

            $O=preg_replace('/[\r\n\t]/', '', $content);
            //�`�N! �]�� ajax �u�|�� utf-8 �s�X�ǹL��, �ҥH�n�⥦��^ big5 , �^���|���S��Ÿ�, �|�Q�[��� \ �Ÿ� , �n stripslashes
            $T=preg_replace('/[\r\n\t]/', '',iconv("utf-8","big5",$_POST['typing_words']));
            //�v�g�����r��
            $words=mb_strlen($T, "big5");
            $correct=0;

            //���r
            //�Q�� iconv_substr($str,$i,1,'big5') �v�@���C�Ӧr�� ,�@�˴N�[ 1
            for ($i=0;$i<$words;$i++) {
                if (iconv_substr($T,$i,1,'big5')==iconv_substr($O,$i,1,'big5')) {
                    $correct+=1;
                }
            }
            //�Q�� iconv_substr($str,$i,1,'big5') �v�@���C�Ӧr�� ,�@�˴N�[ 1
            for ($i=0;$i<$words;$i++) {
                if (iconv_substr($T,$i,1,'big5')==iconv_substr($O,$i,1,'big5')) {
                    $correct+=1;
                }
            }

        //�^��
        } else {
            //$O=str_replace("\r\n","",$content);
            $O=preg_replace('/[\r\n\t]/', '', $content);
            //�`�N! �]�� ajax �u�|�� utf-8 �s�X�ǹL��, �ҥH�n�⥦��^ big5 , �^���|���S��Ÿ�, �|�Q�[��� \ �Ÿ� , �n stripslashes
            $T=preg_replace('/[\r\n\t]/', '',stripslashes(iconv("utf-8","big5",$_POST['typing_words'])));
            //�v�g�����r��
            $words=mb_strlen($T, "big5");
            $correct=0;
             //���r
             //�Q�� iconv_substr($str,$i,1,'big5') �v�@���C�Ӧr�� ,�@�˴N�[ 1
                for ($i=0;$i<$words;$i++) {
                        if (substr($T,$i,1)==substr($O,$i,1)) {
                            $correct+=1;
                        }
                }
        }
        */
        //�����l�峹�ର UTF-8
        $content=big5_to_utf8($content);
        //�v��ӧO���
        $O=explode("\r\n", $content);
        //�`�N! �]�� ajax �u�|�� utf-8 �s�X�ǹL��, �ҥH�n�⥦��^ big5 , �^���|���S��Ÿ�, �|�Q�[��� \ �Ÿ� , �n stripslashes
        $type_words=preg_replace('/[\r\n\t]/', '',$_POST['typing_words']);
        //�v�g�����r��
        //$words=mb_strlen($type_words, "big5");
        $words=0;
        $correct=0;

        //���r
        $T=explode('\n',stripslashes($_POST['typing_words']));
        $T_line=count($T)-1;   //�w�����
        //�Q�� iconv_substr($str,$i,1,'big5') �v�@���C�Ӧr�� ,�@�˴N�[ 1
        foreach ($T as $k=>$v) {

            //���ثe��
            if ($k==$T_line) {
                $line_words=mb_strlen($v, "utf-8");
                $words+=$line_words;
                for ($i=0;$i<$line_words;$i++) {
                    if (iconv_substr($v,$i,1,'utf-8')==iconv_substr($O[$k],$i,1,'utf-8')) {
                        $correct+=1;
                    }
                }
                //���w������
            } else {
                $line_words=mb_strlen($O[$k], "utf-8");
                $words+=$line_words;
                for ($i=0;$i<$line_words;$i++) {
                    if (iconv_substr($v,$i,1,'utf-8')==iconv_substr($O[$k],$i,1,'utf-8')) {
                        $correct+=1;
                    }
                }
            }

        }

        //���T�v
        $correct_per=round(($correct/$words)*100,2);
    }

    //�}�l���r�~�}�l�p��
    if ($words>0 and $_SESSION['timer']==0) {
        $_SESSION['timer']=1;
        if ($_POST['rec_id']>0) {
            //�g�J�}�l�ɶ�
            $sql="update contest_typerec set sttime_{$_POST['type_times']}='".date("Y-m-d H:i:s")."' where id='{$_POST['rec_id']}' and student_sn='{$_SESSION['session_tea_sn']}'";
            $CONN->Execute($sql) or die("SQL Error! SQL=".$sql);
        } else {
            $_SESSION['type_timer']=date("Y-m-d H:i:s");
        }

    }

//�w�L�ɶ�
if ($_SESSION['timer']) {
    if ($_POST['rec_id']>0) {
        $type_times=$_POST['type_times'];    //�� ? ���˴�
        $sql="select sttime_{$_POST['type_times']} from contest_typerec where id='{$_POST['rec_id']}' and student_sn='{$_SESSION['session_tea_sn']}'";
        $res=$CONN->Execute($sql);
        $sttime=$res->fields[0];
        $type_timer=strtotime(date("Y-m-d H:i:s"))-strtotime($sttime);
        $timer=599;   //��������, �ɶ� 10����
    } else {
        $type_timer=strtotime(date("Y-m-d H:i:s"))-strtotime($_SESSION['type_timer']);
        $timer=299;  //�˴��ɶ�   , �m�߬� 5 ����
    }
}



//���A
$state=($type_timer>$timer)?"2":"1";     //-1 ��ܭn����

//�t��
$speed=round($correct/($type_timer/60),2);

//�ɶ��� , �p�G�O��������, �n�O��
if ($state==2 and $_POST['rec_id']>0) {

    $sql="update contest_typerec set endtime_{$_POST['type_times']}='".date("Y-m-d H:i:s")."',correct_{$_POST['type_times']}='{$correct_per}',speed_{$_POST['type_times']}='{$correct}' where id='{$_POST['rec_id']}' and student_sn='{$_SESSION['session_tea_sn']}'";
    $CONN->Execute($sql) or die($sql);
    $sql="select * from contest_typerec where id='{$_POST['rec_id']}' and student_sn='{$_SESSION['session_tea_sn']}'";
    $res=$CONN->Execute($sql) or die($sql);
    $row=$res->fetchRow();
    //if ($_POST['type_times']==2) {
        //����`���T�r��
        if ($row['speed_1']>$row['speed_2']) {
            //�Ĥ@������
            $sql="update contest_typerec set score_correct='{$row['correct_1']}',score_speed='{$row['speed_1']}' where id='{$_POST['rec_id']}' and student_sn='{$_SESSION['session_tea_sn']}'";
            $CONN->Execute($sql);
        } elseif ($row['speed_1']<$row['speed_2']) {
            //�ĤG������
            $sql="update contest_typerec set score_correct='{$row['correct_2']}',score_speed='{$row['speed_2']}' where id='{$_POST['rec_id']}' and student_sn='{$_SESSION['session_tea_sn']}'";
            $CONN->Execute($sql);
        } else {
            //�@�˧� , �񵪹�v
            if ($row['correct_1']>$row['correct_2']) {
                $sql="update contest_typerec set score_correct='{$row['correct_1']}',score_speed='{$row['speed_1']}' where id='{$_POST['rec_id']}' and student_sn='{$_SESSION['session_tea_sn']}'";
                $CONN->Execute($sql);
            } elseif ($row['correct_1']<$row['correct_2']) {
                $sql="update contest_typerec set score_correct='{$row['correct_2']}',score_speed='{$row['speed_2']}' where id='{$_POST['rec_id']}' and student_sn='{$_SESSION['session_tea_sn']}'";
                $CONN->Execute($sql);
            } else {
                $sql="update contest_typerec set score_correct='{$row['correct_1']}',score_speed='{$row['speed_1']}' where id='{$_POST['rec_id']}' and student_sn='{$_SESSION['session_tea_sn']}'";
                $CONN->Execute($sql);
            }
        }
    //}

}

if ($_POST['ending']) { $state=-1; }
//

echo $type_timer.",".$speed.",".$correct_per.",".$correct.",".$state;
//echo $T_line.",".$words.",".$correct_per.",".$correct.",".$state;
//echo $words.",".$speed.",".$correct_per.",".$_POST['typing_words'].",".$state;
//echo $type_timer.",".$speed.",".stripslashes($_POST['typing_words']).",".$state;


//big5�� utf8
function big5_to_utf8($str){
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

    return $str;

}
?>