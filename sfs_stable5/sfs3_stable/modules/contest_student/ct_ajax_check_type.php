<?php
header('Content-type: text/html;charset=utf-8');
// $Id: reward_one.php 6735 2012-04-06 08:14:54Z smallduh $

//取得設定檔
include_once "../../include/config.php";

sfs_check();

$now=date("Y-m-d H:i:s");

    //如果已選擇了文章
    if ($_POST['typing_words'] and $_POST['type_id']) {

        $sql="select * from contest_typebank where id='{$_POST['type_id']}'";
        $res=$CONN->Execute($sql);
        list($id,$kind,$article,$content)=$CONN->Execute($sql)->fetchrow();
        /*
        //整篇一起比對的方式
        //中打 , 中打全部是全形字, 不用 stripslashes
        if ($kind==1) {

            $O=preg_replace('/[\r\n\t]/', '', $content);
            //注意! 因為 ajax 只會用 utf-8 編碼傳過來, 所以要把它轉回 big5 , 英打會有特殊符號, 會被加脫位 \ 符號 , 要 stripslashes
            $T=preg_replace('/[\r\n\t]/', '',iconv("utf-8","big5",$_POST['typing_words']));
            //己經打的字數
            $words=mb_strlen($T, "big5");
            $correct=0;

            //比對字
            //利用 iconv_substr($str,$i,1,'big5') 逐一比對每個字元 ,一樣就加 1
            for ($i=0;$i<$words;$i++) {
                if (iconv_substr($T,$i,1,'big5')==iconv_substr($O,$i,1,'big5')) {
                    $correct+=1;
                }
            }
            //利用 iconv_substr($str,$i,1,'big5') 逐一比對每個字元 ,一樣就加 1
            for ($i=0;$i<$words;$i++) {
                if (iconv_substr($T,$i,1,'big5')==iconv_substr($O,$i,1,'big5')) {
                    $correct+=1;
                }
            }

        //英打
        } else {
            //$O=str_replace("\r\n","",$content);
            $O=preg_replace('/[\r\n\t]/', '', $content);
            //注意! 因為 ajax 只會用 utf-8 編碼傳過來, 所以要把它轉回 big5 , 英打會有特殊符號, 會被加脫位 \ 符號 , 要 stripslashes
            $T=preg_replace('/[\r\n\t]/', '',stripslashes(iconv("utf-8","big5",$_POST['typing_words'])));
            //己經打的字數
            $words=mb_strlen($T, "big5");
            $correct=0;
             //比對字
             //利用 iconv_substr($str,$i,1,'big5') 逐一比對每個字元 ,一樣就加 1
                for ($i=0;$i<$words;$i++) {
                        if (substr($T,$i,1)==substr($O,$i,1)) {
                            $correct+=1;
                        }
                }
        }
        */
        //先把原始文章轉為 UTF-8
        $content=big5_to_utf8($content);
        //逐行個別比對
        $O=explode("\r\n", $content);
        //注意! 因為 ajax 只會用 utf-8 編碼傳過來, 所以要把它轉回 big5 , 英打會有特殊符號, 會被加脫位 \ 符號 , 要 stripslashes
        $type_words=preg_replace('/[\r\n\t]/', '',$_POST['typing_words']);
        //己經打的字數
        //$words=mb_strlen($type_words, "big5");
        $words=0;
        $correct=0;

        //比對字
        $T=explode('\n',stripslashes($_POST['typing_words']));
        $T_line=count($T)-1;   //已打行數
        //利用 iconv_substr($str,$i,1,'big5') 逐一比對每個字元 ,一樣就加 1
        foreach ($T as $k=>$v) {

            //比對目前行
            if ($k==$T_line) {
                $line_words=mb_strlen($v, "utf-8");
                $words+=$line_words;
                for ($i=0;$i<$line_words;$i++) {
                    if (iconv_substr($v,$i,1,'utf-8')==iconv_substr($O[$k],$i,1,'utf-8')) {
                        $correct+=1;
                    }
                }
                //比對已完成行
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

        //正確率
        $correct_per=round(($correct/$words)*100,2);
    }

    //開始打字才開始計時
    if ($words>0 and $_SESSION['timer']==0) {
        $_SESSION['timer']=1;
        if ($_POST['rec_id']>0) {
            //寫入開始時間
            $sql="update contest_typerec set sttime_{$_POST['type_times']}='".date("Y-m-d H:i:s")."' where id='{$_POST['rec_id']}' and student_sn='{$_SESSION['session_tea_sn']}'";
            $CONN->Execute($sql) or die("SQL Error! SQL=".$sql);
        } else {
            $_SESSION['type_timer']=date("Y-m-d H:i:s");
        }

    }

//已過時間
if ($_SESSION['timer']) {
    if ($_POST['rec_id']>0) {
        $type_times=$_POST['type_times'];    //第 ? 次檢測
        $sql="select sttime_{$_POST['type_times']} from contest_typerec where id='{$_POST['rec_id']}' and student_sn='{$_SESSION['session_tea_sn']}'";
        $res=$CONN->Execute($sql);
        $sttime=$res->fields[0];
        $type_timer=strtotime(date("Y-m-d H:i:s"))-strtotime($sttime);
        $timer=599;   //正式比賽, 時間 10分鐘
    } else {
        $type_timer=strtotime(date("Y-m-d H:i:s"))-strtotime($_SESSION['type_timer']);
        $timer=299;  //檢測時間   , 練習為 5 分鐘
    }
}



//狀態
$state=($type_timer>$timer)?"2":"1";     //-1 表示要停止

//速度
$speed=round($correct/($type_timer/60),2);

//時間到 , 如果是正式比賽, 要記錄
if ($state==2 and $_POST['rec_id']>0) {

    $sql="update contest_typerec set endtime_{$_POST['type_times']}='".date("Y-m-d H:i:s")."',correct_{$_POST['type_times']}='{$correct_per}',speed_{$_POST['type_times']}='{$correct}' where id='{$_POST['rec_id']}' and student_sn='{$_SESSION['session_tea_sn']}'";
    $CONN->Execute($sql) or die($sql);
    $sql="select * from contest_typerec where id='{$_POST['rec_id']}' and student_sn='{$_SESSION['session_tea_sn']}'";
    $res=$CONN->Execute($sql) or die($sql);
    $row=$res->fetchRow();
    //if ($_POST['type_times']==2) {
        //比較總正確字數
        if ($row['speed_1']>$row['speed_2']) {
            //第一次較快
            $sql="update contest_typerec set score_correct='{$row['correct_1']}',score_speed='{$row['speed_1']}' where id='{$_POST['rec_id']}' and student_sn='{$_SESSION['session_tea_sn']}'";
            $CONN->Execute($sql);
        } elseif ($row['speed_1']<$row['speed_2']) {
            //第二次較快
            $sql="update contest_typerec set score_correct='{$row['correct_2']}',score_speed='{$row['speed_2']}' where id='{$_POST['rec_id']}' and student_sn='{$_SESSION['session_tea_sn']}'";
            $CONN->Execute($sql);
        } else {
            //一樣快 , 比答對率
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


//big5轉 utf8
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