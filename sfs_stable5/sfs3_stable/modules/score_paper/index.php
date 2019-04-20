<?php
// $Id: index.php 7700 2013-10-23 08:09:06Z smallduh $

include "config.php";
sfs_check();
$OS=PHP_OS;
//主選單設定
$school_menu_p=(empty($school_menu_p))?array():$school_menu_p;

//預設值設定
$col_default=array("enable"=>"1;2");


$act=$_REQUEST[act];

//執行動作判斷
if($act=="insert"){
        $msg=score_paper_add($_POST[data]);
        header("location: $_SERVER[PHP_SELF]?act=listAll&msg=$msg");
}elseif($act=="update"){
        score_paper_update($_POST[data],$_POST[sp_sn]);
        header("location: $_SERVER[PHP_SELF]?act=listAll");
}elseif($act=="del"){
        score_paper_del($_GET[sp_sn]);
        header("location: $_SERVER[PHP_SELF]?act=listAll");
}elseif($act=="modify"){
        $main=&score_paper_mainForm($_GET[sp_sn],"modify");
        $main.="<p>".score_paper_listAll($_GET[msg]);
}else{
        $main=&score_paper_mainForm($_POST[sp_sn]);
        $main.="<p>".score_paper_listAll($_GET[msg]);
}


//秀出網頁
head("自訂成績單");

echo $main;
foot();

//主要輸入畫面
function &score_paper_mainForm($sp_sn="",$mode){
        global $school_menu_p,$col_default;

        if($mode=="modify" and !empty($sp_sn)){
                $dbData=get_score_paper_data($sp_sn);
        }

        if(is_array($dbData) and sizeof($dbData)>0){
                foreach($dbData as $a=>$b){
                        $DBV[$a]=(!is_null($b))?$b:$col_default[$a];
                }
        }else{
                $DBV=$col_default;
        }

        $submit=($mode=="modify")?"update":"insert";

        //說明
        $readme=readme();

        //相關功能表
        $tool_bar=&make_menu($school_menu_p);

        $main="
        $tool_bar

        <table cellspacing='1' cellpadding='4' bgcolor='#C0C0C0' class='small'>
        <form action='$_SERVER[PHP_SELF]' method='post' ENCTYPE='multipart/form-data'>

        <input type='hidden' name='data[sp_sn]' value='$DBV[sp_sn]'>

        <tr bgcolor='#FFFFFF'>
        <td>檔名：<input type='file' name='userfile' value='$DBV[file_name]' size='20'>
        <br>名稱：<input type='text' name='data[sp_name]' value='$DBV[sp_name]' size='20' maxlength='255'><br>啟用：<input type='radio' name='data[enable]' value='1' checked>使用<input type='radio' name='data[enable]' value='2' >停用
        </td><td rowspan=3 valign=top>$readme</td></tr>

        <tr bgcolor='#D6DFFF'><td>請簡單描述此成績單</td></tr>
        <tr bgcolor='#00659C'><td align='center'>
        <textarea name='data[descriptive]' cols='40' rows='5' style='width:100%'>$DBV[descriptive]</textarea>
        <br>
        <input type='hidden' name='sp_sn' value='$sp_sn'>
        <input type='hidden' name='act' value='$submit'>
        <input type='submit' value='上傳' class='b1'></td>
        </tr>

        </table>
        </form>
        ";
        return $main;
}

//readme
function readme(){
        $main="<ol style='line-height:2'>
        <li>若欲自製成績單，請使用 OpenOffice.org 的 Writer 來建立新檔案，假設存檔為 test.sxw。
        <li>請參考<a href='mark.php'>可用標籤</a>，並將需要的欄位放入 test.sxw 中，例如 {學生姓名} 到時候下載時就會自動變成學生的姓名，您可以參考這個範例：<a href='demo.sxw'>成績單範例(下載後請將副檔名改為.sxw)</a>。
        <li>做好後，就可以到左邊上傳，系統會將您的成績單樣版儲存起來。
        <li>最後，就可以到<a href='make.php'>成績單製作</a>去下載成績單。
        <li>更詳細說明請看<a href='faq.php'>成績單製作問題集</a>。
        </ol>
        ";
        return $main;
}

//新增
function score_paper_add($data){
        global $CONN;

        $sql_insert = "insert into score_paper (sp_sn,file_name,sp_name,descriptive,enable) values ('$data[sp_sn]','".$_FILES['userfile']['name']."','$data[sp_name]','$data[descriptive]','$data[enable]')";
        $CONN->Execute($sql_insert) or user_error("新增失敗！<br>$sql_insert",256);
        $sp_sn=mysql_insert_id();
        $msg=unzip($sp_sn);
        return $msg;
}

//更新
function score_paper_update($data,$sp_sn){
        global $CONN;
        $file=(!empty($_FILES['userfile']['name']))?"file_name='".$_FILES['userfile']['name']."',":"";

        $sql_update = "update score_paper set $file sp_name='$data[sp_name]',descriptive='$data[descriptive]',enable='$data[enable]'  where sp_sn='$sp_sn'";
        $CONN->Execute($sql_update) or user_error("更新失敗！<br>$sql_update",256);
        return $sp_sn;
}

//刪除
function score_paper_del($sp_sn=""){
        global $CONN,$UPLOAD_PATH;

        //Openofiice的路徑
        $dir=$UPLOAD_PATH."score_paper/".$sp_sn;

        //刪除目錄所有檔案
        deldir($dir);
        $sql_delete = "delete from score_paper where sp_sn='$sp_sn'";
        $CONN->Execute($sql_delete) or user_error("刪除失敗！<br>$sql_delete",256);

        return true;
}



//列出所有
function &score_paper_listAll($msg=""){
        global $CONN,$SFS_PATH_HTML,$UPLOAD_URL;

        $sql_select="select sp_sn,file_name,sp_name,descriptive,enable from score_paper";
        $recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
        while (list($sp_sn,$file_name,$sp_name,$descriptive,$enable)=$recordSet->FetchRow()) {
                //Openofiice的路徑
                $df=$UPLOAD_URL."score_paper/".$sp_sn."/".$sp_sn.".sxw";

                $data.="<tr bgcolor='#FFFFFF'><td>$sp_sn</td><td><a href='$df'>$file_name</a></td><td>$sp_name</td><td>$descriptive</td><td>$enable</td><td nowrap><a href='$_SERVER[PHP_SELF]?act=modify&sp_sn=$sp_sn'>修改</a> | <a href='$_SERVER[PHP_SELF]?act=del&sp_sn=$sp_sn'>刪除</a></td></tr>";
        }
        $main="
        <table cellspacing='1' cellpadding='3' bgcolor='#C0C0C0' class='small'>
        <tr bgcolor='#E6E9F9'><td>流水號</td><td>檔名</td><td>名稱</td><td>描述</td><td>啟用</td><td>功能</td></tr>
        $data
        </table>
        $msg";
        return $main;
}



//取得某一筆資料
function get_score_paper_data($sp_sn){
        global $CONN;
        $sql_select="select sp_sn,file_name,sp_name,descriptive,enable from score_paper where sp_sn='$sp_sn'";
        $recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
        $theData=$recordSet->FetchRow();
        return $theData;
}


function unzip($sp_sn=0){
        global $SFS_PATH,$UPLOAD_PATH,$OS;
        if(empty($sp_sn))return;

        $is_win=ereg('win', strtolower($_SERVER['SERVER_SOFTWARE']))?true:false;
        $zipfile=($is_win)?"UNZIP32.EXE":"/usr/bin/unzip";
        
        $zipfile=($OS=="FreeBSD")?"/usr/local/bin/unzip":$zipfile;


        $arg1=($is_win)?"START /min cmd /c ":"";
        $arg2=($is_win)?"-d":"-d";

        if($_FILES['userfile']['type'] == "application/vnd.sun.xml.writer"){
                $filename=$sp_sn.".sxw";
        }elseif(strtolower(substr($_FILES['userfile']['name'],-3))=="sxw"){
                $filename=$sp_sn.".sxw";
        }else{
                die("格式不正確");
        }

        if (!is_dir($UPLOAD_PATH)) {
                die("上傳目錄 $UPLOAD_PATH 不存在！");
        }


        //統一上傳目錄
        $upath=$UPLOAD_PATH."score_paper";
        if (!is_dir($upath)) {
                mkdir($upath) or die($upath."建立失敗！");
        }

        //上傳目的地
        $todir=$upath."/".$sp_sn."/";
        if (!is_dir($todir)) {
                mkdir($todir) or die($todir."目的目錄建立失敗！");
        }

        $the_file=$todir.$filename;

        copy($_FILES['userfile']['tmp_name'],$the_file);
        unlink($_FILES['userfile']['tmp_name']);

        if (!file_exists($zipfile)) {
       	       echo $_SERVER['PHP_OS'];
                die($zipfile."不存在！");
        }elseif(!file_exists($the_file)) {
                die($the_file."不存在！");
        }

        $cmd=$arg1." ".$zipfile." ".$the_file." ".$arg2." ".$todir;
        if(exec($cmd,$output,$rv)){
                //unlink($the_file);
                return;
        }else{
                $msg=$cmd."已執行。<br>";
                foreach($output as $v){
                        $msg.=$v."<br>";
                }
                return $msg;
        }
}
?>