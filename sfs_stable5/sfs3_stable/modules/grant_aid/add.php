<?php
// $Id: add.php 5310 2009-01-10 07:57:56Z hami $

include "config.php";
sfs_check();

//獎助類別
//$type=($_REQUEST[type]);

//目標身份
$type=($_REQUEST[type]);
$type_id=$target_id[$type];

//學期別
$curr_year_seme = sprintf("%03d%d",curr_year(),curr_seme());

//秀出網頁
head("獎助學金");
echo $menu;

// 取出班級陣列
$class_base = class_base($curr_year_seme);


//取得學年學期陣列
$year_seme_arr = get_class_seme();

//取得學生身份列表
$type_select="SELECT d_id,t_name FROM sfs_text WHERE t_kind='stud_kind' AND d_id>0 order by t_order_id";

$recordSet=$CONN->Execute($type_select) or user_error("讀取失敗！<br>$type_select",256);
while (list($d_id,$t_name)=$recordSet->FetchRow()) {
        if ($type_id==$d_id)
                $typedata.="<option value='$d_id' selected>$t_name</option>";
        else
                $typedata.="<option value='$d_id'>$t_name</option>";
}

//取得本類別學生身份資料

$sql_select="select left(curr_class_num,length(curr_class_num)-2) as class_id,right(curr_class_num,2) as stud_seat,student_sn,stud_id,stud_name,stud_person_id from stud_base where (stud_kind like '%,$type_id,%') and (stud_study_cond=0) order by curr_class_num";
$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
//print_r($recordSet->FetchRow());

while (list($class_id,$stud_seat,$student_sn,$stud_id,$stud_name,$stud_person_id)=$recordSet->FetchRow()) {
$data.="<tr bgcolor='#FFFFFF'><td>$class_base[$class_id]</td><td>$stud_seat</td><td>$student_sn</td><td>$stud_id</td><td>$stud_name</td><td>$stud_person_id</td><td nowrap><input type='checkbox' name='sel_stud[]' value='$student_sn,$class_id$stud_seat' id='stud_sel'></td></tr>";
}

$main="
        <table width='96%' cellspacing='1' cellpadding='3' bgcolor='$hint_color'><form name=\"sel_stud_kind\" method=\"post\" action=\"$_SERVER[PHP_SELF]\"><tr><td><img border='0' src='images/pin.gif'>填報的學年(期)：$year_seme_arr[$curr_year_seme]　　　　　　　　<img border='0' src='images/pin.gif'>學生身份選項：<select name='type_id' onchange='this.form.submit()' disabled>$typedata</select>　代碼值：$type_id</td></tr></form></table>
        <table width='96%' cellspacing='1' cellpadding='3' bgcolor='#C0C0F0'>
        <form name=\"sel_stud\" method=\"post\" action=\"insert.php\">
        <tr><td>班級</td><td>座號</td><td>學籍編號</td><td>學號</td><td>姓名</td><td>身分證字號</td><td><input type='checkbox' name='all_stud' onClick='CheckAll();'>選取</td></tr>
        $data
        <tr><td colspan=5>
                <input type='hidden' name='curr_year_seme' value='$curr_year_seme'>
                <input type='hidden' name='type' value='$type'>
                　金額：<input type='text' name='dollar' size='5' value=$dollars>
                <input type='submit' value='增列勾選的學生' name='B1'>
                <input type='reset' value='重新設定' name='clear'></td>
                <td colspan=2><a href='index.php?type=$type&work_year_seme=$curr_year_seme'><img border='0' src='images/back.gif'> 回上一頁</a>
        </td></tr>
        </form></table>";
?>
<script language="JavaScript">

        function CheckAll(){
        for (var i=0;i<document.sel_stud.elements.length;i++){
                var e = document.sel_stud.elements[i];
                if (e.id == 'stud_sel') e.checked = !e.checked;
        }}
</script>
<?
echo $main;


foot();

?>