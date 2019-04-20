<?php
// $Id: batchadd.php 7301 2013-06-05 14:26:52Z infodaes $

include "config.php";
sfs_check();

//獎助類別
$type=($_REQUEST[type]);

//目標身份t_id
$type_id=($_POST[type_id])?$_POST[type_id]:$_GET[type_id];
if($type_id=='') $type_id='9';

//學期別
$curr_year_seme = sprintf("%03d%d",curr_year(),curr_seme());

//秀出網頁
head("獎助學金");
echo $menu;

// 取出班級陣列
//$class_base = class_base($curr_year_seme);

//設定橫向類別選項數
$col=5;

//取得學年學期陣列
$year_seme_arr = get_class_seme();

//取得學生身份列表
$type_select="SELECT d_id,t_name FROM sfs_text WHERE t_kind='stud_kind' AND d_id>0 order by t_order_id";

$recordSet=$CONN->Execute($type_select) or user_error("讀取失敗！<br>$type_select",256);
while (list($d_id,$t_name)=$recordSet->FetchRow()) {
        if($recordSet->currentrow() % $col==1) $data.="<tr bgcolor='#FFFFFF'>";
        //設定儲存格底色
        $pos = strpos($t_name, $keyword);
		/*  撤除預設類別限制
        //if ($pos === false) $bgcolor="#FFFFFF";  else  $bgcolor="$hint_color";
        if($target_id[$type]==$d_id){
		$bgcolor="$hint_color";  $checked="checked";
        } else {
		$bgcolor="#FFFFFF";   $checked="disabled";
        }
        */
        $data.="<td bgcolor='$bgcolor'><input type='checkbox' name='sel_stud[]' value='$d_id' id='stud_sel' $checked>$t_name</td>\n";
        if($recordSet->currentrow() % $col==0  or $recordSet->EOF) $data.="</tr>";
}

//        <tr bgcolor='#FFCCFF'><td colspan=$col align='center'><BR><h2><font face='標楷體'><< 請選取要開列的學生身份類別 >></h2></td></tr>

$main="<table width='100%' cellspacing='1' cellpadding='3' bgcolor='$hint_color'>
        <form name=\"sel_stud\" method=\"post\" action=\"batchinsert.php\">
        <tr><td colspan=5><center><img border='0' src='images/pin.gif'>填報的學年(期)：$year_seme_arr[$curr_year_seme]　　　　　<a href='index.php?type=$type&work_year_seme=$curr_year_seme'><img border='0' src='images/back.gif'> 回上一頁</a></center></td></tr>
        $data
        <tr><td colspan=$col align='center'>
                <input type='hidden' name='curr_year_seme' value='$curr_year_seme'>
                <input type='hidden' name='type' value='$type'>
                　金額：<input type='text' name='dollar' size='5' value=$dollars>
                <input type='submit' value='增列勾選類別的學生' name='B1'>
        </td></tr></form>
        </table>";

echo $main;


foot();

?>