<?php
header('Content-type: text/html;charset=big5');
// $Id: reward_one.php 6735 2012-04-06 08:14:54Z smallduh $

//取得設定檔
include_once "config.php";

sfs_check();

//秀出網頁
head("網路應用競賽 - 打字練習");


$tool_bar=&make_menu($school_menu_p);

//列出選單
echo $tool_bar;

//取得目前學年度
$curr_year=curr_year();
$curr_seme=curr_seme();

//目前選定學期
$c_curr_seme=sprintf('%03d%1d',$curr_year,$curr_seme);

//目前日期時間
$Now=date("Y-m-d H:i:s");

//先選擇要中打還是英打

//再選擇文章



?>
<form name="myform" method="post" action="<?php echo $_SERVER['php_self'];?>">
    <input type="hidden" name="start" value="<?= $_POST['start'] ?>">
    <div>
        <span>
            類型：<select size="1" name="kind" onchange="document.myform.start.value=0;document.myform.submit()">
                <option value="">請選擇中打或英打</option>
                <option value="1"<?php if ($_POST['kind']=="1") echo " selected";?>>中打</option>
                <option value="2"<?php if ($_POST['kind']=="2") echo " selected";?>>英打</option>
            </select>
        </span>

    <?php
    if ($_POST['kind']!='') {
        $sql="select * from contest_typebank where kind='{$_POST['kind']}' and open='1'";
        $res=$CONN->Execute($sql);
        if ($res->recordCount()==0) {
            echo "沒有文章!";
        } else {
            ?>
                <span>
                    篇名：
                    <select size="1" name="type_id" onchange="document.myform.start.value=0;document.myform.submit()">
                        <option value="">請選擇文章</option>
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
    //如果已選擇了文章
    if ($_POST['type_id']!='') {
        $sql="select * from contest_typebank where id='{$_POST['type_id']}'";
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
                字數：<?php echo $words;?> &nbsp;&nbsp; 行數：<?php echo $new_line;?>
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
                已過時間： <span id="timer">0</span> 秒， 速度：<span id="speed">0</span> 字/分， 正確率：<span id="correct"></span>，積分：<span id="score"></span> &nbsp;&nbsp;《 測試時間 300 秒 (即 5 分鐘) 》
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

    } // end if $_POST['type_id']!=''

    ?>

</form>



    <?php
        if ($_POST['type_id']!='') {
          $type_id=$_POST['type_id'];
          include_once("typingrace_check.inc");
        }
    ?>

