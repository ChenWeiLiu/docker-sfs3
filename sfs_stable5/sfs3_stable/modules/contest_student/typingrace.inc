<?php
include_once('login.inc');
//目前日期時間
$Now=date("Y-m-d H:i:s");
//界面呈現開始, 全部包在 <form>裡 , act動作 , option1, option2 參數2個 return返回前一個動作=============================
?>
<form method="post" name="myform" action="<?php echo $_SERVER['PHP_SELF'];?>"  enctype="multipart/form-data">
    <input type="hidden" name="act" value="<?php echo $_POST['act'];?>">
    <input type="hidden" name="option1" value="<?php echo $_POST['option1'];?>">
    <input type="hidden" name="option2" value="<?php echo $_POST['option2'];?>">
    <input type="hidden" name="return" value="<?php echo $_POST['act'];?>">

    <?php
    //未登入
    if ($_POST['act']=='') {
        stud_login($ACTIVE,$INFO);
    }

    //驗證通過, 開始
    if ($_POST['act']=='Start') {

        //取得競賽設定
        $TEST=get_test_setup($_POST['option1']);				//競賽設定
        $STUD=get_student($_SESSION['session_tea_sn']);         //學生資料

        $NOWsec=NowAllSec(date("Y-m-d H:i:s"));
        $StartTest=NowAllSec($TEST['sttime']);
        $EndTest=NowAllSec($TEST['endtime']);
        //取出競賽資料, 若還沒開始
        if ($NOWsec<$StartTest) {
            //競賽尚未開始，提示多久即將進行 ********************
            $LeaveTime=$StartTest-$NOWsec;
            ?>
            <table border="0" width="100%">
                <tr>
                    <td style="color:#0000FF">
                        登入學生：<?php echo $STUD['class_name']." ".$STUD['seme_num']."號 ".$STUD['stud_name'];?>，距離比賽開始還有<input type="text" name="time" size="6">，請注意！
                    </td>
                </tr>
            </table>
        <?php
        test_main($_POST['option1'],0);
        ?>
            <Script language="JavaScript">
                //全域變數
                var ACT="Start";
                //比賽尚未進行，提示到數計時後開始
                var inttestsec=<?php echo $LeaveTime;?>;

                checkLeaveTime();

            </Script>

            <?php
            //比賽進行中
        } elseif ($NOWsec>=$StartTest and $NOWsec<$EndTest) {
            ?>
            <input type="hidden" name="start" value="<?= $_POST['start'] ?>">
            <?php
            //取得學生比賽情形
            $sql="select * from contest_typerec where race_id='{$_POST['option1']}' and student_sn='{$_SESSION['session_tea_sn']}'";
            $res=$CONN->Execute($sql);
            //是否已經進入過
            if ($res->recordCount()==0) {
                $sql="insert into contest_typerec (race_id,student_sn) values ('{$_POST['option1']}','{$_SESSION['session_tea_sn']}')";
                $res=$CONN->Execute($sql);
                //$rec_id=$CONN->Insert_ID(); //取得 last id
                $type_id=$TEST['type_id_1'];    //文章 id
                $type_times=1;                  //第幾篇
            } else {
                $row=$res->fetchRow();
                if ($row['sttime_1']=='0000-00-00 00:00:00' and $row['sttime_2']=='0000-00-00 00:00:00') {
                    $type_id=$TEST['type_id_1'];    //文章 id
                    $type_times=1;
                    $rec_id=$row['id'];
                } elseif ($row['sttime_1']!='0000-00-00 00:00:00' and $row['sttime_2']=='0000-00-00 00:00:00') {
                    $type_id=$TEST['type_id_2'];    //文章 id
                    $type_times=2;
                    $rec_id=$row['id'];
                } else {
                    echo "<br>你已經檢測完畢! <br><br>";
                    echo "第一次成績　速度：".round($row['speed_1']/10,2)." 字/分　，正確率：".$row['correct_1']."　，積分：".$row['speed_1']."<br><br>";
                    echo "第二次成績　速度：".round($row['speed_2']/10,2)." 字/分　，正確率：".$row['correct_2']."　，積分：".$row['speed_2']."<br>";
                    exit();
                }

            }


            // $TEST 為比賽的設定 array
            // $STUd  為學生資料 array
            $sql="select * from contest_typebank where id='$type_id'";
            $res=$CONN->Execute($sql);
            list($id,$kind,$article,$content)=$CONN->Execute($sql)->fetchrow();

            //要打的字
            $data=$content;
            $L=explode("\r\n",$data);
            $words=0;
            foreach ($L as $line) {
                $words+=mb_strlen($line, "big5");  //每行字數加起來
            }
            $new_line=count($L);  //行數

            $window_height=($new_line<13)?$new_line*18+6:222;
            //把要打的字重新組合, 給 javascript 使用
            //$TT=implode("\\n",$L);

            ?>
            <div>
            <span style="text-align: right">
                第 <?= $type_times ?> 篇文章檢測  &nbsp;&nbsp;&nbsp;字數：<?php echo $words;?> &nbsp;&nbsp; 行數：<?php echo $new_line;?>
            </span>
            </div>
            <div style="padding-top:3px;padding-bottom:3px;line-height:22px;font-family:新細明體;border-style: solid;font-size:14pt;height:<?php echo $window_height;?>;overflow: auto" id="SHOW2">
                <?php //= str_replace("\n","<BR>",$data); ?>
            </div>

            <?php

            if ($_POST['start']==1) {
                $_SESSION['type_timer']=date("Y-m-d H:i:s");
                ?>
                <div>
                    已過時間： <span id="timer">0</span> 秒， 速度：<span id="speed">0</span> 字/分， 正確率：<span id="correct"></span>，積分：<span id="score"></span> &nbsp;&nbsp;《 檢測時間 600 秒 (即 10 分鐘) 》
                </div>
                <div style="border-style: solid;border-color: #cccccc;">
                    <textarea style="font-family:新細明體;font-size:14pt;width:100%;height: <?php echo $window_height;?>px" name="typetest" id="typetest"></textarea>
                </div>
                <?php
            } else {
              $_SESSION['timer']=0;
                ?>
                <div style="margin-top: 5px">
                    <input type="button" value="按下我開始打字，打第１個字就開始計時" onclick="document.myform.start.value='1';document.myform.submit()">
                </div>
                <div style="margin-top: 10px">
                    <div style="background-color: #cdcdcd;margin-top:5px;margin-bottom: 5px">
                        ◎競賽規則<br><br>
                        <?= str_replace("\n","<br>",$TEST['memo']) ?>
                    </div>
                    ◎檢測說明
                    <ol>
                        <li>關於標題符號及空白符號，中打請一律用全形字；英打則一律用半形字。</li>
                        <li>遇到斷行時，請自行按 [Enter] 鍵換行。沒有正確換行，會影響正確率。</li>
                        <li><span style="color:#FF0000">注意！正確率的計算，是依序逐字比對，如果有跳字或跳行，後面全部算錯！</span></li>
                        <li>速度的計算是用正確字數/時間。</li>
                        <li>積分是指鍵入的所有正確字元數。</li>
                    </ol>
                </div>
                <?php
            }

            include_once ('typingrace_check.inc');

        } else {
            echo "比賽時間已過!!";
            exit();
        } // end if 比賽時間檢驗

    } // end if $_POST['act']=='start'
?>


</form>





