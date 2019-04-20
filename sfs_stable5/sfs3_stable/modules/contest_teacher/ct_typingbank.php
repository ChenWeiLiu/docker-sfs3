<?php

// $Id: reward_one.php 6735 2012-04-06 08:14:54Z smallduh $

//取得設定檔
include_once "config.php";

sfs_check();

//秀出網頁
head("網路應用競賽 - 管理打字比賽用題庫");

?>
<script type="text/javascript" src="./include/tr_functions.js"></script>

<?php
$tool_bar=&make_menu($school_menu_p);

//列出選單
echo $tool_bar;

//取得目前學年度
$curr_year=curr_year();
$curr_seme=curr_seme();

//目前選定學期
$c_curr_seme=sprintf('%03d%1d',$curr_year,$curr_seme);

//目前日期時間, 用於比對消息有效期限
$Now=date("Y-m-d H:i:s");

if (!$MANAGER) {
 echo "<font color=red>抱歉! 你沒有管理權限, 系統禁止你繼續操作本功能!!!</font>";
 exit();
}

//POST 送出後,主程式操作開始 
//新增一筆
if ($_POST['act']=='insert') {

    $kind = $_POST['kind'];           //類型 1中打 2英打
    $article = $_POST['article'];     //文章標題
    $content = $_POST['content'];     //本文
    $open=$_POST['open'];
    //存入
    $query = "insert into contest_typebank (kind,article,content,open) values ('$kind','$article','$content','$open')";
    $res=$CONN->Execute($query) or die("Error! SQL=".$query);
    $kind=$article=$content="";
}  //end if insert

//更新一筆
if ($_POST['act']=='update') {

    $kind = $_POST['kind'];           //類型 1中打 2英打
    $article = $_POST['article'];     //文章標題
    $content = $_POST['content'];     //本文
    $open=$_POST['open'];
    //存入
    $query = "update contest_typebank set kind='$kind',article='$article',content='$content',open='$open' where id='{$_POST['id']}'";
    $res=$CONN->Execute($query) or die("Error! SQL=".$query);

    $kind=$article=$content="";
}  //end if insert

//刪除一筆
if ($_POST['act']=='delete') {
    $query="delete from contest_typebank where id='{$_POST['opt']}'";
    $res=$CONN->Execute($query) or die("Error! SQL=".$query);
}

//編輯一筆
if ($_POST['act']=='edit') {
    $query="select * from contest_typebank where id='{$_POST['opt']}'";
    list($id,$kind,$article,$content,$open)=$CONN->Execute($query)->fetchrow();
}
//界面呈現開始, 全部包在 <form>裡 , act動作 , option1, option2 參數2個 , return記下返回頁面
?>
<form method="post" name="myform" action="<?php echo $_SERVER['PHP_SELF'];?>" enctype="multipart/form-data">
 <input type="hidden" name="act" value="<?php echo (($id>0)?"update":"insert");?>">
 <input type="hidden" name="opt" value="">
 <input type="hidden" name="id" value="<?php echo $id;?>">
 <div>
        <span>文章類別：</span>
        <span>
            <select name="kind" size="1">
                <option value="1"<?php if ($kind==1) echo "selected";?>>中打</option>
                <option value="2"<?php if ($kind==2) echo "selected";?>>英打</option>
            </select>
        </span>
 </div>
 <div style="margin-top: 5px;margin-bottom: 5px">
     <span>文章標題：</span>
     <span><input type="text" name="article" value="<?= $article; ?>" style="width:90%"></span>
 </div>
 <div>
     <span>文章內容：</span>
     <span>
         <input type="radio" name="open" value="0"<?php if ($open==0) echo " checked";?>>不開放練習
         <input type="radio" name="open" value="1"<?php if ($open==1) echo " checked";?>>開放練習
     </span>
     <div>
         <textarea name="content" style="width:100%;height:250px"><?= $content ?></textarea>
     </div>
 </div>
 <div>
     <span><input type="button" value="<?php echo (($id>0)?"更新內容":"新增一筆");?>" onclick="confirm_save()"></span>
 </div>
 <div style="margin-top: 10px">
   <ol>
       <li>中打文章，建議所有符號都採用全形字。</li>
       <li>英打文章，每一行前不得有空白符號。</li>
       <li>.務必在每一行的適當長度處按下 [enter] 鍵換行，。</li>
   </ol>
 </div>
</form>
<?php
$sql="select * from contest_typebank order by kind,id";
$res=$CONN->Execute($sql);
if ($res->recordCount()==0) {
    echo "目前資料庫中無任無打字用題庫!";
} else {
    ?>
    <table border="1" style="width:100%;border-collapse:collapse;border-style: solid;border-width: thin">
        <thead>
            <tr style="background-color: #8CCCCA">
                <td>序號</td>
                <td>類型</td>
                <td>標題</td>
                <td>字數</td>
                <td>行數</td>
                <td>操作</td>
            </tr>
        </thead>
        <tbody>
    <?php
    $row=$res->getRows();
    $i=0;
    foreach ($row as $R) {
      $i++;
        $kind=($R['kind']==1)?"中打":"英打";
        $L=explode("\r\n",$R['content']);
        $words=0;
        foreach ($L as $line) {
            $words+=mb_strlen($line, "big5");  //每行字數加起來
        }
        $new_line=count($L);  //行數
        ?>
        <tr>
            <td><?= $i ?></td>
            <td><?= $kind ?></td>
            <td><?= $R['article'] ?></td>
            <td><?= $words ?></td>
            <td><?= $new_line ?></td>
            <td>
                <input type="button" value="編輯" onclick="document.myform.opt.value='<?= $R['id'];?>';document.myform.act.value='edit';document.myform.submit()">
                <input type="button" value="刪除" onclick="confirm_delete('<?= $R['id'];?>')">
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

        var ok=confirm('您確認要刪除?');

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
            alert ('文章篇名和內容都必須輸入哦！');
        }

    }
</Script>
