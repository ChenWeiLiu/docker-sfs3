<?php
// $Id: modify.php 5310 2009-01-10 07:57:56Z hami $

include "config.php";
sfs_check();

//預設值設定

//獎助類別
$type=($_REQUEST[type]);

$act=$_REQUEST[act];

//執行動作判斷
if($act=="update"){
        grant_update($_POST[data],$_POST[sn]);
        header("location: index.php?type=$type");
}elseif($act=="del"){
        grant_del($_GET[sn]);
        header("location: index.php?type=$type");
}elseif($act=="modify"){
        $main=&grant_mainForm($_GET[sn],"modify");
}else{
        header("location: index.php?type=$type");
}


//秀出網頁
head("獎助學金");
echo $menu;
echo $main;
foot();

//主要輸入畫面
function &grant_mainForm($sn="",$mode){
        global $school_menu_p,$col_default;

        if($mode=="modify" and !empty($sn)){
                $dbData=get_grant_data($sn);
        }

        if(is_array($dbData) and sizeof($dbData)>0){
                foreach($dbData as $a=>$b){
                        $DBV[$a]=(!is_null($b))?$b:$col_default[$a];
                }
        }else{
                $DBV=$col_default;
        }

        $submit=($mode=="modify")?"update":"insert";

        //相關功能表
        $tool_bar=&make_menu($school_menu_p);

        $main="
        <table cellspacing='1' cellpadding='3' bgcolor='#C0C0C0'>
        <form action='$_SERVER[PHP_SELF]' method='post'>

        <tr bgcolor='#FFFFFF'>
        <td>學期別</td>
        <td><input type='text' name='data[year_seme]' value='$DBV[year_seme]' size='6' maxlength='6'></td>
        </tr>

        <tr bgcolor='#FFFFFF'>
        <td>學籍流水號</td>
        <td><input type='text' name='data[student_sn]' value='$DBV[student_sn]' size='10' maxlength='10'></td>
        </tr>

        <tr bgcolor='#FFFFFF'>
        <td>班級座號</td>
        <td><input type='text' name='data[class_num]' value='$DBV[class_num]' size='6' maxlength='10'></td>
        </tr>

        <tr bgcolor='#FFFFFF'>
        <td>金額</td>
        <td><input type='text' name='data[dollar]' value='$DBV[dollar]' size='10' maxlength='10'></td>
        </tr>


        </table>
        <input type='hidden' name='sn' value='$sn'>
        <input type='hidden' name='work_year_seme' value='$DBV[year_seme]'>
        <input type='hidden' name='act' value='$submit'>
        <input type='submit' value='送出'>
        </form>

        <a href='index.php?work_year_seme=$DBV[year_seme]'>回原年度紀錄列表</a>
        ";
        return $main;
}

//更新
function grant_update($data,$sn){
        global $CONN;

        $sql_update = "update grant_aid set year_seme='$data[year_seme]',student_sn='$data[student_sn]',dollar='$data[dollar]' where sn='$sn'";
        $CONN->Execute($sql_update) or user_error("更新失敗！<br>$sql_update",256);
        return $sn;
}

//刪除
function grant_del($sn=""){
        global $CONN;
        $sql_delete = "delete from grant_aid where sn=$sn";
        $CONN->Execute($sql_delete) or user_error("刪除失敗！<br>$sql_delete",256);
        return true;
}

//取得某一筆資料
function get_grant_data($sn){
        global $CONN;
        $sql_select="select year_seme,student_sn,class_num,dollar,sn from grant_aid where sn='$sn'";
        $recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
        $theData=$recordSet->FetchRow();
        return $theData;
}


?>